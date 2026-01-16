<?php

namespace App\Models;

use App\Core\Database;

class VoucherTemplateModel {

    public function getAll() {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT * FROM voucher_templates");
        return $stmt->fetchAll();
    }
    
    public function getBySession($sessionName) {
        // Templates can be global or session specific, but allow session filtering
        $db = Database::getInstance();
        $stmt = $db->query("SELECT * FROM voucher_templates WHERE session_name = ? OR session_name = 'global'", [$sessionName]);
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT * FROM voucher_templates WHERE id = ?", [$id]);
        return $stmt->fetch();
    }

    public function add($data) {
        $db = Database::getInstance();
        $sql = "INSERT INTO voucher_templates (session_name, name, content) VALUES (?, ?, ?)";
        return $db->query($sql, [
            $data['session_name'],
            $data['name'],
            $data['content']
        ]);
    }

    public function update($id, $data) {
        $db = Database::getInstance();
        $sql = "UPDATE voucher_templates SET name=?, content=?, updated_at=CURRENT_TIMESTAMP WHERE id=?";
        return $db->query($sql, [
            $data['name'],
            $data['content'],
            $id
        ]);
    }

    public function delete($id) {
        $db = Database::getInstance();
        return $db->query("DELETE FROM voucher_templates WHERE id = ?", [$id]);
    }
}
