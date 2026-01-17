<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\VoucherTemplateModel;
use App\Core\Middleware;

class VoucherTemplateController extends Controller {

    public function __construct() {
        Middleware::auth();
    }

    public function index() {
        $templateModel = new VoucherTemplateModel();
        $templates = $templateModel->getAll();

        $data = [
            'templates' => $templates
        ];
        return $this->view('settings/voucher_templates/index', $data);
    }

    public function preview($id) {
        $content = '';
        if ($id === 'default') {
            $content = \App\Helpers\TemplateHelper::getDefaultTemplate();
        } else {
            $templateModel = new VoucherTemplateModel();
            $tpl = $templateModel->getById($id);
            if ($tpl) {
                $content = $tpl['content'];
            }
        }
        
        echo \App\Helpers\TemplateHelper::getPreviewPage($content);
    }

    public function add() {
        $logoModel = new \App\Models\Logo();
        $logos = $logoModel->getAll();
        $logoMap = [];
        foreach ($logos as $l) {
            $logoMap[$l['id']] = $l['path'];
        }

        $data = [
            'logoMap' => $logoMap
        ];
        return $this->view('settings/voucher_templates/add', $data); 
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        
        $name = $_POST['name'] ?? 'Untitled';
        $content = $_POST['content'] ?? '';
        
        // Session context could be 'global' or specific. For now, let's treat settings templates as global or assign to 'global' session name if column exists.
        // My migration made 'session_name' NOT NULL.
        // I will use 'global' for templates created in Settings.
        
        $data = [
            'router_id' => 0, // Global templates
            'session_name' => 'global',
            'name' => $name,
            'content' => $content
        ];

        $templateModel = new VoucherTemplateModel();
        $templateModel->add($data);

        \App\Helpers\FlashHelper::set('success', 'toasts.template_created', 'toasts.template_created_desc', ['name' => $name], true);
        header("Location: /settings/voucher-templates");
        exit;
    }

    public function edit($id) {
        $templateModel = new VoucherTemplateModel();
        $template = $templateModel->getById($id);

        if (!$template) {
             header("Location: /settings/voucher-templates");
             exit;
        }

        $logoModel = new \App\Models\Logo();
        $logos = $logoModel->getAll();
        $logoMap = [];
        foreach ($logos as $l) {
            $logoMap[$l['id']] = $l['path'];
        }

        $data = [
            'template' => $template,
            'logoMap' => $logoMap
        ];
        return $this->view('settings/voucher_templates/edit', $data);
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $id = $_POST['id'] ?? '';
        $name = $_POST['name'] ?? '';
        $content = $_POST['content'] ?? '';

        $data = [
            'name' => $name,
            'content' => $content
        ];

        $templateModel = new VoucherTemplateModel();
        $templateModel->update($id, $data);

        \App\Helpers\FlashHelper::set('success', 'toasts.template_updated', 'toasts.template_updated_desc', ['name' => $name], true);
        header("Location: /settings/voucher-templates");
        exit;
    }

    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        $id = $_POST['id'] ?? '';

        $templateModel = new VoucherTemplateModel();
        $templateModel->delete($id);

        \App\Helpers\FlashHelper::set('success', 'toasts.template_deleted', 'toasts.template_deleted_desc', [], true);
        header("Location: /settings/voucher-templates");
        exit;
    }
}
