<?php

namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller {
    public function __construct() {
        \App\Core\Middleware::auth();
    }

    public function index() {
        // Fetch real router sessions from Config model
        $config = new \App\Models\Config();
        $routers = $config->getAllSessions();

        $data = [
            'routers' => $routers
        ];

        $this->view('home', $data);
    }

    public function designSystem() {
        $data = ['title' => 'MIVO - Design System'];
        $this->view('design_system', $data);
    }

    public function testAlert() {
        \App\Helpers\FlashHelper::set('success', 'toasts.test_alert', 'toasts.test_alert_desc', [], true);
        header("Location: /");
        exit;
    }
}
