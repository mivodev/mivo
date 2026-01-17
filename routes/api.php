<?php

// API Routes
// These routes do not use the session in the URL prefix by default, 
// but might require session/id in the POST body for authentication context.

// Apply Global CORS to all API routes
$router->group(['middleware' => 'cors'], function($router) {

    $router->post('/api/router/interfaces', [App\Controllers\ApiController::class, 'getInterfaces']);

    // Public Status API (No Auth Check in Controller)
    $router->post('/api/status/check', [App\Controllers\PublicStatusController::class, 'check']);

    // Voucher Check (Code/Username in URL) - Support GET (Status Page) and POST (Login Page Check)
    $router->post('/api/voucher/check/{code}', [App\Controllers\PublicStatusController::class, 'check']);
    $router->get('/api/voucher/check/{code}', [App\Controllers\PublicStatusController::class, 'check']);

});
