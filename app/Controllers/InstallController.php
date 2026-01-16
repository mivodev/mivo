<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Migrations;
use App\Config\SiteConfig;

class InstallController extends Controller {

    public function index() {
        // Check if already installed
        if ($this->isInstalled()) {
            header('Location: /login');
            exit;
        }
        
        return $this->view('install');
    }

    public function process() {
        if ($this->isInstalled()) {
            header('Location: /login');
            exit;
        }

        $username = $_POST['username'] ?? 'admin';
        $password = $_POST['password'] ?? 'admin';
        
        try {
            // 1. Run Migrations
            Migrations::up();
            
            // 2. Generate Key if default
            if (SiteConfig::getSecretKey() === 'mikhmonv3remake_secret_key_32bytes') {
                $this->generateKey();
            }
            
            // 3. Create Admin
            $db = Database::getInstance();
            $hash = password_hash($password, PASSWORD_DEFAULT);
            
            // Check if user exists (edge case where key was default but user existed)
            $check = $db->query("SELECT id FROM users WHERE username = ?", [$username])->fetch();
            if (!$check) {
                $db->query("INSERT INTO users (username, password, created_at) VALUES (?, ?, ?)", [
                    $username, $hash, date('Y-m-d H:i:s')
                ]);
            } else {
                 $db->query("UPDATE users SET password = ? WHERE username = ?", [$hash, $username]);
            }
            
            // Success
            \App\Helpers\FlashHelper::set('success', 'Installation Complete', 'System has been successfully installed. Please login.');
            header('Location: /login');
            exit;
            
        } catch (\Exception $e) {
            \App\Helpers\FlashHelper::set('error', 'Installation Failed', $e->getMessage());
            header('Location: /install');
            exit;
        }
    }

    private function isInstalled() {
        // Check if .env exists and APP_KEY is set to something other than the default/example
        $envPath = ROOT . '/.env';
        if (!file_exists($envPath)) {
            // Check if SiteConfig has a manual override (legacy)
            return SiteConfig::getSecretKey() !== 'mikhmonv3remake_secret_key_32bytes';
        }
        
        $key = getenv('APP_KEY');
        $keyChanged = ($key && $key !== 'mikhmonv3remake_secret_key_32bytes');
        
        try {
            $db = Database::getInstance();
            $count = $db->query("SELECT count(*) as c FROM users")->fetchColumn();
            $hasUser = ($count > 0);
        } catch (\Exception $e) {
            $hasUser = false;
        }

        return $keyChanged && $hasUser;
    }
    
    private function generateKey() {
        $key = bin2hex(random_bytes(16));
        $envPath = ROOT . '/.env';
        $examplePath = ROOT . '/.env.example';
        
        if (!file_exists($envPath)) {
            if (file_exists($examplePath)) {
                copy($examplePath, $envPath);
            } else {
                return; // Cannot generate without source
            }
        }
        
        $content = file_get_contents($envPath);
        
        if (strpos($content, 'APP_KEY=') !== false) {
             $newContent = preg_replace(
                "/APP_KEY=.*/",
                "APP_KEY=$key",
                $content
            );
        } else {
            $newContent = $content . "\nAPP_KEY=$key";
        }
        
        file_put_contents($envPath, $newContent);
        
        // Refresh env in current session so next steps use it
        putenv("APP_KEY=$key");
        $_ENV['APP_KEY'] = $key;
    }
}
