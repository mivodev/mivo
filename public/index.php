<?php

use App\Core\Router;

// Start Output Buffering
ob_start();

// Define Root Path
define('ROOT', dirname(__DIR__));

// Handle Static Files for PHP Built-in Server
if (php_sapi_name() === 'cli-server') {
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

// Start Session
session_start();

// Manual require for the Autoloader class since it can't autoload itself
require_once ROOT . '/app/Core/Autoloader.php';
\App\Core\Autoloader::register();

// Load Environment Variables
\App\Core\Env::load(ROOT . '/.env');

// Initialize Router
$router = new Router();


// Global Error Handling for Dev Mode
if (\App\Config\SiteConfig::IS_DEV) {
    // Catch Fatal Errors (Shutdown)
    register_shutdown_function(function() {
        $error = error_get_last();
        if ($error && ($error['type'] === E_ERROR || $error['type'] === E_PARSE || $error['type'] === E_CORE_ERROR || $error['type'] === E_COMPILE_ERROR)) {
            // Convert to exception format for our helper
            $e = new ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']);
            \App\Helpers\ErrorHelper::showException($e);
        }
    });

    // Catch Uncaught Exceptions
    set_exception_handler(function($e) {
        \App\Helpers\ErrorHelper::showException($e);
    });
}

// Define Routes
require_once ROOT . '/routes/web.php';
require_once ROOT . '/routes/api.php';

// Dispatch
// Dispatch
try {
    $router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
} catch (Exception $e) {
    if (\App\Config\SiteConfig::IS_DEV) {
        \App\Helpers\ErrorHelper::showException($e);
    } else {
        \App\Helpers\ErrorHelper::show(500, 'Internal Server Error', $e->getMessage());
    }
} catch (Error $e) {
    if (\App\Config\SiteConfig::IS_DEV) {
        \App\Helpers\ErrorHelper::showException($e);
    } else {
        \App\Helpers\ErrorHelper::show(500, 'System Error', $e->getMessage());
    }
}
