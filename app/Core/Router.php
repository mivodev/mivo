<?php

namespace App\Core;

class Router {
    protected $routes = [];

    public function get($path, $callback) {
        $this->routes['GET'][$path] = $callback;
    }

    public function post($path, $callback) {
        $this->routes['POST'][$path] = $callback;
    }

    public function dispatch($uri, $method) {
        $path = parse_url($uri, PHP_URL_PATH);
        
        // Handle subdirectory
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);
        if (strpos($path, $scriptName) === 0) {
            $path = substr($path, strlen($scriptName));
        }
        $path = '/' . trim($path, '/');

        // Global Install Check: Redirect if database is missing
        $dbPath = ROOT . '/app/Database/database.sqlite';
        if (!file_exists($dbPath)) {
            // Whitelist /install route and assets to prevent infinite loop
            if ($path !== '/install' && strpos($path, '/assets/') !== 0) {
                header('Location: /install');
                exit;
            }
        }

        // Check exact match first
        if (isset($this->routes[$method][$path])) {
            $callback = $this->routes[$method][$path];
            return $this->invokeCallback($callback);
        }

        // Check dynamic routes
        foreach ($this->routes[$method] as $route => $callback) {
            // Convert route syntax to regex
            // e.g. /dashboard/{session} -> #^/dashboard/([^/]+)$#
            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $route);
            $pattern = "#^" . $pattern . "$#";

            if (preg_match($pattern, $path, $matches)) {
                array_shift($matches); // Remove full match
                $matches = array_map('urldecode', $matches);
                return $this->invokeCallback($callback, $matches);
            }
        }

        \App\Helpers\ErrorHelper::show(404);
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
