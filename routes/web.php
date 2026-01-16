<?php

// Auth Routes
$router->get('/install', [App\Controllers\InstallController::class, 'index']);
$router->post('/install', [App\Controllers\InstallController::class, 'process']);

$router->get('/login', [App\Controllers\AuthController::class, 'showLogin']);
$router->post('/login', [App\Controllers\AuthController::class, 'login']);
$router->get('/logout', [App\Controllers\AuthController::class, 'logout']);

// Home
$router->get('/', [App\Controllers\HomeController::class, 'index']);

// Design System / Components
$router->get('/design-system', [App\Controllers\HomeController::class, 'designSystem']);

// Temporary Test Route
$router->get('/test-alert', [App\Controllers\HomeController::class, 'testAlert']);

// Public Status Check
$router->get('/{session}/status', [App\Controllers\PublicStatusController::class, 'index']);

// Routers Settings and Systems Settings Routers
$router->get('/settings', [App\Controllers\SettingsController::class, 'routers']); // Default to Routers
$router->get('/settings/system', [App\Controllers\SettingsController::class, 'system']); // Renamed General
$router->get('/settings/routers', [App\Controllers\SettingsController::class, 'routers']);
$router->get('/settings/add', [App\Controllers\SettingsController::class, 'add']);
$router->post('/settings/store', [App\Controllers\SettingsController::class, 'store']);
$router->get('/settings/edit/{id}', [App\Controllers\SettingsController::class, 'edit']);
$router->post('/settings/update', [App\Controllers\SettingsController::class, 'update']);
$router->post('/settings/delete', [App\Controllers\SettingsController::class, 'delete']);
$router->post('/settings/admin/update', [App\Controllers\SettingsController::class, 'updateAdmin']);
$router->post('/settings/global/update', [App\Controllers\SettingsController::class, 'updateGlobal']);
$router->get('/settings/backup', [App\Controllers\SettingsController::class, 'backup']);
$router->post('/settings/restore', [App\Controllers\SettingsController::class, 'restore']);

// Settings - Templates Routes
$router->get('/settings/templates', [App\Controllers\TemplateController::class, 'index']);
$router->get('/settings/templates/preview/{id}', [App\Controllers\TemplateController::class, 'preview']);
$router->get('/settings/templates/add', [App\Controllers\TemplateController::class, 'add']);
$router->post('/settings/templates/store', [App\Controllers\TemplateController::class, 'store']);
$router->get('/settings/templates/edit/{id}', [App\Controllers\TemplateController::class, 'edit']);
$router->post('/settings/templates/update', [App\Controllers\TemplateController::class, 'update']);
$router->post('/settings/templates/delete', [App\Controllers\TemplateController::class, 'delete']);

// Logo Management Routes
$router->get('/settings/logos', [App\Controllers\SettingsController::class, 'logos']);
$router->post('/settings/logos/upload', [App\Controllers\SettingsController::class, 'uploadLogo']);
$router->post('/settings/logos/delete', [App\Controllers\SettingsController::class, 'deleteLogo']);

// API CORS Routes
$router->get('/settings/api-cors', [App\Controllers\SettingsController::class, 'apiCors']);
$router->post('/settings/api-cors/store', [App\Controllers\SettingsController::class, 'storeApiCors']);
$router->post('/settings/api-cors/update', [App\Controllers\SettingsController::class, 'updateApiCors']);
$router->post('/settings/api-cors/delete', [App\Controllers\SettingsController::class, 'deleteApiCors']);


// Hotspot - Profiles
$router->get('/{session}/hotspot/profiles', [App\Controllers\ProfileController::class, 'index']);
$router->get('/{session}/hotspot/profile/add', [App\Controllers\ProfileController::class, 'add']);
$router->post('/{session}/hotspot/profile/store', [App\Controllers\ProfileController::class, 'store']);
$router->post('/{session}/hotspot/profile/delete', [App\Controllers\ProfileController::class, 'delete']);
$router->get('/{session}/hotspot/profile/edit/{id}', [App\Controllers\ProfileController::class, 'edit']);
$router->post('/{session}/hotspot/profile/update', [App\Controllers\ProfileController::class, 'update']);

// Hotspot - Users
$router->get('/{session}/hotspot/users', [App\Controllers\HotspotController::class, 'index']);
$router->get('/{session}/hotspot/add', [App\Controllers\HotspotController::class, 'add']);
$router->post('/{session}/hotspot/store', [App\Controllers\HotspotController::class, 'store']);
$router->post('/{session}/hotspot/delete', [App\Controllers\HotspotController::class, 'delete']);
$router->get('/{session}/hotspot/user/edit/{id}', [App\Controllers\HotspotController::class, 'edit']);
$router->post('/{session}/hotspot/update', [App\Controllers\HotspotController::class, 'update']);
$router->get('/{session}/hotspot/print-batch', [App\Controllers\HotspotController::class, 'printBatchActions']);
$router->get('/{session}/hotspot/print/([a-zA-Z0-9*]+)', [App\Controllers\HotspotController::class, 'printUser']); // Handle Microtik IDs often having *

// Hotspot - Active & Hosts (New)
$router->get('/{session}/hotspot/active', [App\Controllers\HotspotController::class, 'active']);
$router->post('/{session}/hotspot/active/remove', [App\Controllers\HotspotController::class, 'removeActive']);
$router->get('/{session}/hotspot/hosts', [App\Controllers\HotspotController::class, 'hosts']);
$router->get('/{session}/hotspot/bindings', [App\Controllers\HotspotController::class, 'bindings']);
$router->post('/{session}/hotspot/bindings/store', [App\Controllers\HotspotController::class, 'storeBinding']);
$router->post('/{session}/hotspot/bindings/remove', [App\Controllers\HotspotController::class, 'removeBinding']);
$router->get('/{session}/hotspot/walled-garden', [App\Controllers\HotspotController::class, 'walledGarden']);
$router->post('/{session}/hotspot/walled-garden/store', [App\Controllers\HotspotController::class, 'storeWalledGarden']);
$router->post('/{session}/hotspot/walled-garden/remove', [App\Controllers\HotspotController::class, 'removeWalledGarden']);

// Hotspot - Generate
$router->get('/{session}/hotspot/generate', [App\Controllers\GeneratorController::class, 'index']);
$router->post('/{session}/hotspot/generate/process', [App\Controllers\GeneratorController::class, 'process']);

// Dashboard
$router->get('/{session}/dashboard', [App\Controllers\DashboardController::class, 'index']);

// Traffic Monitor (API)
$router->get('/{session}/traffic/monitor', [App\Controllers\TrafficController::class, 'monitor']);
$router->get('/{session}/traffic/interfaces', [App\Controllers\TrafficController::class, 'getInterfaces']);

// Reports
$router->get('/{session}/reports/selling', [App\Controllers\ReportController::class, 'index']);
$router->get('/{session}/reports/resume', [App\Controllers\ReportController::class, 'resume']);
$router->get('/{session}/reports/user-log', [App\Controllers\LogController::class, 'index']);

// System Tools
$router->post('/{session}/system/reboot', [App\Controllers\SystemController::class, 'reboot']);
$router->post('/{session}/system/shutdown', [App\Controllers\SystemController::class, 'shutdown']);
$router->get('/{session}/system/scheduler', [App\Controllers\SchedulerController::class, 'index']);
$router->post('/{session}/system/scheduler/store', [App\Controllers\SchedulerController::class, 'store']);
$router->post('/{session}/system/scheduler/update', [App\Controllers\SchedulerController::class, 'update']);
$router->post('/{session}/system/scheduler/delete', [App\Controllers\SchedulerController::class, 'delete']);

// Network & Cookies
$router->get('/{session}/network/dhcp', [App\Controllers\DhcpController::class, 'index']);
$router->get('/{session}/hotspot/cookies', [App\Controllers\HotspotController::class, 'cookies']);
$router->post('/{session}/hotspot/cookies/remove', [App\Controllers\HotspotController::class, 'removeCookie']);

// Quick Print Routes
$router->get('/{session}/quick-print', [App\Controllers\QuickPrintController::class, 'index']);
$router->get('/{session}/quick-print/manage', [App\Controllers\QuickPrintController::class, 'manage']);
$router->post('/{session}/quick-print/store', [App\Controllers\QuickPrintController::class, 'store']);
$router->post('/{session}/quick-print/delete', [App\Controllers\QuickPrintController::class, 'delete']);
$router->get('/{session}/quick-print/print/([a-zA-Z0-9_-]+)', [App\Controllers\QuickPrintController::class, 'printPacket']);


// API Routes
// API Routes (Moved to routes/api.php)
