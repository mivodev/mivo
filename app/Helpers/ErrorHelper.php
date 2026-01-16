<?php

namespace App\Helpers;

class ErrorHelper {
    
    public static function show($code = 404, $message = 'Page Not Found', $description = null) {
        http_response_code($code);
        
        // Provide default descriptions for common codes
        if ($description === null) {
            switch ($code) {
                case 403:
                    $description = "You do not have permission to access this resource.";
                    break;
                case 500:
                    $description = "Something went wrong on our end. Please try again later.";
                    break;
                case 503:
                    $description = "Service Unavailable. The server is currently unable to handle the request due to maintenance or overload.";
                    break;
                case 404:
                default:
                    $description = "The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.";
                    break;
            }
        }
        
        // Variables extracted in view
        $errorCode = $code;
        $errorMessage = $message;
        $errorDescription = $description;
        
        // Ensure strictly NO output before this if keeping clean, but we are in view mode.
        // Clean buffer if active to remove partial content
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        require ROOT . '/app/Views/errors/default.php';
        exit;
    }

    public static function showException($exception) {
        http_response_code(500);
        
        // Clean output buffer to ensure clean error page
        if (ob_get_level()) {
            ob_end_clean();
        }

        require ROOT . '/app/Views/errors/development.php';
        exit;
    }
}
