<?php

namespace App\Core;

class Router {
    protected $routes = [];
    protected $currentGroupMiddleware = [];
    protected $lastRouteKey = null;

    protected $middlewareAliases = [
        'auth' => \App\Middleware\AuthMiddleware::class,
        'cors' => \App\Middleware\CorsMiddleware::class,
        'router.valid' => \App\Middleware\RouterCheckMiddleware::class,
    ];

    /**
     * Add a GET route
     */
    public function get($path, $callback) {
        return $this->addRoute('GET', $path, $callback);
    }

    /**
     * Add a POST route
     */
    public function post($path, $callback) {
        return $this->addRoute('POST', $path, $callback);
    }

    /**
     * Add route to collection and return $this for chaining
     */
    protected function addRoute($method, $path, $callback) {
        $path = $this->normalizePath($path);
        
        $this->routes[$method][$path] = [
            'callback' => $callback,
            'middleware' => $this->currentGroupMiddleware // Inherit group middleware
        ];
        
        $this->lastRouteKey = ['method' => $method, 'path' => $path];
        
        return $this;
    }

    /**
     * Attach middleware to the last defined route
     */
    public function middleware($names) {
        if (!$this->lastRouteKey) return $this;

        $method = $this->lastRouteKey['method'];
        $path = $this->lastRouteKey['path'];

        $middlewares = is_array($names) ? $names : [$names];
        
        // Merge with existing middleware (from groups)
        $this->routes[$method][$path]['middleware'] = array_merge(
            $this->routes[$method][$path]['middleware'],
            $middlewares
        );

        return $this;
    }

    /**
     * Define a route group with shared attributes (middleware, prefix, etc.)
     */
    public function group($attributes, callable $callback) {
        $previousGroupMiddleware = $this->currentGroupMiddleware;

        if (isset($attributes['middleware'])) {
            $newMiddleware = is_array($attributes['middleware']) 
                ? $attributes['middleware'] 
                : [$attributes['middleware']];
                
            $this->currentGroupMiddleware = array_merge(
                $this->currentGroupMiddleware, 
                $newMiddleware
            );
        }

        // Execute the callback with $this router instance
        $callback($this);

        // Restore previous state
        $this->currentGroupMiddleware = $previousGroupMiddleware;
    }

    protected function normalizePath($path) {
        return '/' . trim($path, '/');
    }

    public function dispatch($uri, $method) {
        $path = parse_url($uri, PHP_URL_PATH);
        
        // Handle subdirectory
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);
        if (strpos($path, $scriptName) === 0) {
            $path = substr($path, strlen($scriptName));
        }
        $path = $this->normalizePath($path);

        // Global Install Check
        $dbPath = ROOT . '/app/Database/database.sqlite';
        if (!file_exists($dbPath)) {
            if ($path !== '/install' && strpos($path, '/assets/') !== 0) {
                header('Location: /install');
                exit;
            }
        }

        // 1. Try Exact Match
        if (isset($this->routes[$method][$path])) {
            return $this->runRoute($this->routes[$method][$path], []);
        }

        // 2. Try Dynamic Routes (Regex)
        foreach ($this->routes[$method] as $route => $config) {
            // e.g. /dashboard/{session} -> #^/dashboard/([^/]+)$#
            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $route);
            $pattern = "#^" . $pattern . "$#";

            if (preg_match($pattern, $path, $matches)) {
                array_shift($matches); // Remove full match
                $matches = array_map('urldecode', $matches);
                return $this->runRoute($config, $matches);
            }
        }

        \App\Helpers\ErrorHelper::show(404);
    }

    protected function runRoute($routeConfig, $params) {
        $callback = $routeConfig['callback'];
        $middlewares = $routeConfig['middleware'];
        
        // Pipeline Runner
        $pipeline = array_reduce(
            array_reverse($middlewares),
            function ($nextStack, $middlewareName) {
                return function ($request) use ($nextStack, $middlewareName) {
                    // Resolve Middleware Class
                    $class = $this->middlewareAliases[$middlewareName] ?? $middlewareName;
                    
                    if (!class_exists($class)) {
                        throw new \Exception("Middleware class '$class' not found.");
                    }
                    
                    $instance = new $class();
                    return $instance->handle($request, $nextStack);
                };
            },
            function ($request) use ($callback, $params) {
                // Final destination: The Controller
                return $this->invokeCallback($callback, $params);
            }
        );

        // Start the pipeline with the current request (mock object or just null/path)
        return $pipeline($_SERVER);
    }

    protected function invokeCallback($callback, $params = []) {
        if (is_array($callback)) {
            $controller = new $callback[0]();
            $method = $callback[1];
            return call_user_func_array([$controller, $method], $params);
        }
        return call_user_func_array($callback, $params);
    }
}
