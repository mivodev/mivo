<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Config;
use App\Core\Middleware;
use App\Helpers\FormatHelper;

class SettingsController extends Controller {
    
    public function __construct() {
        // Auth handled by Router Middleware
    }

    public function system() {
        // Systems Settings Tab (Admin, Global, Backup)
        $settingModel = new \App\Models\Setting();
        $settings = $settingModel->getAll();
        
        $username = $_SESSION['username'] ?? 'admin';

        return $this->view('settings/systems', [
            'settings' => $settings,
            'username' => $username
        ]);
    }

    public function routers() {
        // Routers List Tab
        $configModel = new Config();
        $routers = $configModel->getAllSessions();
        return $this->view('settings/index', ['routers' => $routers]);
    }

    // ... (Existing Store methods) ...
    public function store() {
        // Sanitize Session Name (Duplicate Frontend Logic)
        $rawSess = $_POST['sessname'] ?? '';
        $sessName = preg_replace('/[^a-z0-9-]/', '', strtolower(str_replace(' ', '-', $rawSess)));
        
        $data = [
            'session_name' => $sessName, 
            'ip_address' => $_POST['ipmik'], 
            'username' => $_POST['usermik'], 
            'password' => $_POST['passmik'], 
            'hotspot_name' => $_POST['hotspotname'], 
            'dns_name' => $_POST['dnsname'], 
            'currency' => $_POST['currency'], 
            'reload_interval' => $_POST['areload'], 
            'interface' => $_POST['iface'], 
            'description' => 'Added via Remake',
            'quick_access' => isset($_POST['quick_access']) ? 1 : 0
        ];

        $configModel = new Config();
        try {
            $configModel->addSession($data);
            
            $redirect = '/settings/routers';
            if (isset($_POST['action']) && $_POST['action'] === 'connect') {
                $redirect = '/' . urlencode($data['session_name']) . '/dashboard';
            }
            
            \App\Helpers\FlashHelper::set('success', 'toasts.router_added', 'toasts.router_added_desc', ['name' => $data['session_name']], true);
            header("Location: $redirect");
        } catch (\Exception $e) {
            echo "Error adding session: " . $e->getMessage();
        }
    }

    // Update Admin Password
    public function updateAdmin() {
        $newPassword = $_POST['admin_password'] ?? '';
        
        if (!empty($newPassword)) {
            $db = \App\Core\Database::getInstance();
            $hash = password_hash($newPassword, PASSWORD_DEFAULT);
            // Assuming we are updating the default 'admin' user or the currently logged in user
            // Original Mikhmon usually has one main user. Let's update 'admin' for now.
            $db->query("UPDATE users SET password = ? WHERE username = 'admin'", [$hash]);
            \App\Helpers\FlashHelper::set('success', 'toasts.password_updated', 'toasts.password_updated_desc', [], true);
        }
        
        header('Location: /settings/system');
    }

    // Update Global Settings
    public function updateGlobal() {
        $settingModel = new \App\Models\Setting();
        
        if (isset($_POST['quick_print_mode'])) {
            $settingModel->set('quick_print_mode', $_POST['quick_print_mode']);
            \App\Helpers\FlashHelper::set('success', 'toasts.settings_saved', 'toasts.settings_saved_desc', [], true);
        }
        
        header('Location: /settings/system');
    }




    public function update() {
        $id = $_POST['id'];
        
        // Sanitize Session Name
        $rawSess = $_POST['sessname'] ?? '';
        $sessName = preg_replace('/[^a-z0-9-]/', '', strtolower(str_replace(' ', '-', $rawSess)));

        $data = [
            'session_name' => $sessName,
            'ip_address' => $_POST['ipmik'],
            'username' => $_POST['usermik'],
            'password' => $_POST['passmik'], // Can be empty if not changing
            'hotspot_name' => $_POST['hotspotname'],
            'dns_name' => $_POST['dnsname'],
            'currency' => $_POST['currency'],
            'reload_interval' => $_POST['areload'],
            'interface' => $_POST['iface'],
            'description' => 'Updated via Remake',
            'quick_access' => isset($_POST['quick_access']) ? 1 : 0
        ];

        $configModel = new Config();
        try {
            $configModel->updateSession($id, $data);
            
            $redirect = '/settings/routers';
            if (isset($_POST['action']) && $_POST['action'] === 'connect') {
                $redirect = '/' . urlencode($data['session_name']) . '/dashboard';
            }

            \App\Helpers\FlashHelper::set('success', 'toasts.router_updated', 'toasts.router_updated_desc', ['name' => $data['session_name']], true);
            header("Location: $redirect");
        } catch (\Exception $e) {
            echo "Error updating session: " . $e->getMessage();
        }
    }

    public function delete() {
        $id = $_POST['id'];
        $configModel = new Config();
        $configModel->deleteSession($id);
        \App\Helpers\FlashHelper::set('success', 'toasts.router_deleted', 'toasts.router_deleted_desc', [], true);
        header('Location: /settings/routers');
    }

    public function backup() {
        $backupName = 'mivo_backup_' . date('d-m-Y') . '.mivo';
        $json = [];
        
        // Backup Settings
        $settingModel = new \App\Models\Setting();
        $settings = $settingModel->getAll();
        $json['settings'] = $settings;

        // Backup Sessions
        $configModel = new Config();
        $sessions = $configModel->getAllSessions();
        
        // Decrypt passwords for portability
        foreach ($sessions as &$session) {
            if (!empty($session['password'])) {
                $session['password'] = \App\Helpers\EncryptionHelper::decrypt($session['password']);
            }
        }
        $json['sessions'] = $sessions;

        // Backup Voucher Templates
        $templateModel = new \App\Models\VoucherTemplateModel();
        $json['voucher_templates'] = $templateModel->getAll();

        // Backup Logos
        $logoModel = new \App\Models\Logo();
        $logos = $logoModel->getAll();
        foreach ($logos as &$logo) {
            $filePath = ROOT . '/public' . $logo['path'];
            if (file_exists($filePath)) {
                $logo['data'] = base64_encode(file_get_contents($filePath));
            }
        }
        $json['logos'] = $logos;

        // Encode
        $jsonString = json_encode($json, JSON_PRETTY_PRINT);
        
        // Encrypt the entire file content for security
        // Decrypted data inside (like passwords) remain plaintext relative to the JSON structure
        // ensuring portability if decrypted successfully.
        $content = \App\Helpers\EncryptionHelper::encrypt($jsonString);
        
        // Force Download
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.basename($backupName));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . strlen($content));
        ob_clean();
        flush();
        echo $content;
        exit;
    }

    public function restore() {
        if (!isset($_FILES['backup_file']) || $_FILES['backup_file']['error'] !== UPLOAD_ERR_OK) {
             \App\Helpers\FlashHelper::set('error', 'toasts.restore_failed', 'toasts.no_file_selected', [], true);
             header('Location: /settings/system');
             exit;
        }

        $file = $_FILES['backup_file'];
        $filename = $file['name'];
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $mime = $file['type'];

        // Validate Extension & MIME
        $allowedExtensions = ['mivo'];
        $allowedMimes = ['application/octet-stream', 'text/plain']; // text/plain fallback for some OS/Browsers

        if (!in_array($extension, $allowedExtensions) || (!empty($mime) && !in_array($mime, $allowedMimes))) {
            \App\Helpers\FlashHelper::set('error', 'toasts.restore_failed', 'toasts.invalid_file_type_mivo', [], true);
            header('Location: /settings/system');
            exit;
        }
        
        $rawValue = file_get_contents($file['tmp_name']);
        if (empty($rawValue)) {
            \App\Helpers\FlashHelper::set('error', 'toasts.restore_failed', 'toasts.file_empty', [], true);
            header('Location: /settings/system');
            exit;
        }
        
        // Attempt to decrypt. If file is old (JSON plaintext), decrypt() returns it as-is.
        $content = \App\Helpers\EncryptionHelper::decrypt($rawValue);
        
        $json = json_decode($content, true);

        if (!$json || (!isset($json['settings']) && !isset($json['sessions']))) {
            \App\Helpers\FlashHelper::set('error', 'toasts.restore_failed', 'toasts.file_corrupted', [], true);
            header('Location: /settings/system');
            exit;
        }

        // Restore Settings
        if (isset($json['settings'])) {
            $settingModel = new \App\Models\Setting();
            // Assuming we check if data exists
            // We might need to iterate and update
            foreach ($json['settings'] as $key => $val) {
                $settingModel->set($key, $val);
            }
        }

        // Restore Sessions
        if (isset($json['sessions'])) {
            $configModel = new Config();
            foreach ($json['sessions'] as $session) {
                unset($session['id']); // Let system generate new ID
                try {
                    $configModel->addSession($session);
                } catch (\Exception $e) {
                    error_log("Failed to restore session: " . ($session['session_name'] ?? 'unknown'));
                }
            }
        }

        // Restore Voucher Templates
        if (isset($json['voucher_templates'])) {
            $templateModel = new \App\Models\VoucherTemplateModel();
            foreach ($json['voucher_templates'] as $tmpl) {
                // Check if template exists by name and session
                $db = \App\Core\Database::getInstance();
                $existing = $db->query("SELECT id FROM voucher_templates WHERE name = ? AND session_name = ?", [$tmpl['name'], $tmpl['session_name']])->fetch();
                
                if ($existing) {
                    $templateModel->update($existing['id'], $tmpl);
                } else {
                    $templateModel->add($tmpl);
                }
            }
        }

        // Restore Logos
        if (isset($json['logos'])) {
            $logoModel = new \App\Models\Logo();
            $uploadDir = ROOT . '/public/uploads/logos/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            foreach ($json['logos'] as $logo) {
                if (empty($logo['data'])) continue;

                // Decode data
                $binaryData = base64_decode($logo['data']);
                if (!$binaryData) continue;

                // Determine filename (try to keep original ID/name or generate new)
                $extension = $logo['type'] ?? 'png';
                $filename = $logo['id'] . '.' . $extension;
                $targetPath = $uploadDir . $filename;

                // Save file
                if (file_put_contents($targetPath, $binaryData)) {
                    // Update DB
                    $db = \App\Core\Database::getInstance();
                    $db->query("INSERT INTO logos (id, name, path, type, size) VALUES (:id, :name, :path, :type, :size)
                                ON CONFLICT(id) DO UPDATE SET name=excluded.name, path=excluded.path, type=excluded.type, size=excluded.size", [
                        'id' => $logo['id'],
                        'name' => $logo['name'],
                        'path' => '/uploads/logos/' . $filename,
                        'type' => $extension,
                        'size' => $logo['size']
                    ]);
                }
            }
        }
        
        \App\Helpers\FlashHelper::set('success', 'toasts.restore_success', 'toasts.restore_success_desc', [], true);
        header('Location: /settings/system');
    }

    // --- Logo Management ---

    public function logos() {
        $logoModel = new \App\Models\Logo(); // Fully qualified to avoid import issues for now or add import
        $logoModel->syncFiles(); // Ensure FS and DB are in sync
        $logos = $logoModel->getAll();

        // Format size for display (since DB stores raw bytes or maybe we want helper there)
        // Actually model stored bytes, we format in View or here.
        // Let's format here for consistency with previous view.
        foreach ($logos as &$logo) {
            $logo['formatted_size'] = FormatHelper::formatBytes($logo['size']);
        }

        return $this->view('settings/logos', ['logos' => $logos]);
    }

    public function uploadLogo() {
        if (!isset($_FILES['logo_file']) || $_FILES['logo_file']['error'] !== UPLOAD_ERR_OK) {
             \App\Helpers\FlashHelper::set('error', 'toasts.upload_failed', 'toasts.no_file_selected', [], true);
             header('Location: /settings/logos');
             exit;
        }

        $logoModel = new \App\Models\Logo();
        try {
            $result = $logoModel->add($_FILES['logo_file']);
            if ($result) {
                \App\Helpers\FlashHelper::set('success', 'toasts.logo_uploaded', 'toasts.logo_uploaded_desc', [], true);
            } else {
                 \App\Helpers\FlashHelper::set('error', 'toasts.upload_failed', 'Generic upload error', [], true);
            }
        } catch (\Exception $e) {
            \App\Helpers\FlashHelper::set('error', 'toasts.upload_failed', $e->getMessage(), [], true);
        }

        header('Location: /settings/logos');
    }

    public function deleteLogo() {
        $id = $_POST['id']; // Changed from filename to id
        
        $logoModel = new \App\Models\Logo();
        $logoModel->delete($id);

        \App\Helpers\FlashHelper::set('success', 'toasts.logo_deleted', 'toasts.logo_deleted_desc', [], true);
        header('Location: /settings/logos');
    }

    // --- API CORS Management ---

    public function apiCors() {
        $db = \App\Core\Database::getInstance();
        $rules = $db->query("SELECT * FROM api_cors ORDER BY created_at DESC")->fetchAll();
        
        // Decode JSON methods and headers for view
        foreach ($rules as &$rule) {
            $rule['methods_arr'] = json_decode($rule['methods'], true) ?: [];
            $rule['headers_arr'] = json_decode($rule['headers'], true) ?: [];
        }

        return $this->view('settings/api_cors', ['rules' => $rules]);
    }

    public function storeApiCors() {
        $origin = $_POST['origin'] ?? '';
        $methods = isset($_POST['methods']) ? json_encode($_POST['methods']) : '["GET","POST"]';
        $headers = isset($_POST['headers']) ? json_encode(array_map('trim', explode(',', $_POST['headers']))) : '["*"]';
        $maxAge = (int)($_POST['max_age'] ?? 3600);

        if (!empty($origin)) {
            $db = \App\Core\Database::getInstance();
            $db->query("INSERT INTO api_cors (origin, methods, headers, max_age) VALUES (?, ?, ?, ?)", [
                $origin, $methods, $headers, $maxAge
            ]);
            \App\Helpers\FlashHelper::set('success', 'toasts.cors_rule_added', 'toasts.cors_rule_added_desc', ['origin' => $origin], true);
        }

        header('Location: /settings/api-cors');
    }

    public function updateApiCors() {
        $id = $_POST['id'] ?? null;
        $origin = $_POST['origin'] ?? '';
        $methods = isset($_POST['methods']) ? json_encode($_POST['methods']) : '["GET","POST"]';
        $headers = isset($_POST['headers']) ? json_encode(array_map('trim', explode(',', $_POST['headers']))) : '["*"]';
        $maxAge = (int)($_POST['max_age'] ?? 3600);

        if ($id && !empty($origin)) {
            $db = \App\Core\Database::getInstance();
            $db->query("UPDATE api_cors SET origin = ?, methods = ?, headers = ?, max_age = ? WHERE id = ?", [
                $origin, $methods, $headers, $maxAge, $id
            ]);
            \App\Helpers\FlashHelper::set('success', 'toasts.cors_rule_updated', 'toasts.cors_rule_updated_desc', ['origin' => $origin], true);
        }

        header('Location: /settings/api-cors');
    }

    public function deleteApiCors() {
        $id = $_POST['id'] ?? null;
        if ($id) {
            $db = \App\Core\Database::getInstance();
            $db->query("DELETE FROM api_cors WHERE id = ?", [$id]);
            \App\Helpers\FlashHelper::set('success', 'toasts.cors_rule_deleted', 'toasts.cors_rule_deleted_desc', [], true);
        }
        header('Location: /settings/api-cors');
    }
}
