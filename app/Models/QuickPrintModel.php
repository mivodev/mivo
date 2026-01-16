<?php

namespace App\Models;

use App\Core\Database;

class QuickPrintModel {

    public function getAllBySession($sessionName) {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT * FROM quick_prints WHERE session_name = ?", [$sessionName]);
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT * FROM quick_prints WHERE id = ?", [$id]);
        return $stmt->fetch();
    }

    public function add($data) {
        $db = Database::getInstance();
        $sql = "INSERT INTO quick_prints (session_name, name, server, profile, prefix, char_length, price, time_limit, data_limit, comment, color) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        return $db->query($sql, [
            $data['session_name'],
            $data['name'],
            $data['server'],
            $data['profile'],
            $data['prefix'] ?? '',
            $data['char_length'] ?? 4,
            $data['price'] ?? 0,
            $data['time_limit'] ?? '',
            $data['data_limit'] ?? '',
            $data['comment'] ?? '',
            $data['color'] ?? 'bg-blue-500'
        ]);
    }

    public function update($id, $data) {
        $db = Database::getInstance();
        $sql = "UPDATE quick_prints SET name=?, server=?, profile=?, prefix=?, char_length=?, price=?, time_limit=?, data_limit=?, comment=?, color=?, updated_at=CURRENT_TIMESTAMP WHERE id=?";
        
        return $db->query($sql, [
            $data['name'],
            $data['server'],
            $data['profile'],
            $data['prefix'] ?? '',
            $data['char_length'] ?? 4,
            $data['price'] ?? 0,
            $data['time_limit'] ?? '',
            $data['data_limit'] ?? '',
            $data['comment'] ?? '',
            $data['color'] ?? 'bg-blue-500',
            $id
        ]);
    }

    public function delete($id) {
        $db = Database::getInstance();
        return $db->query("DELETE FROM quick_prints WHERE id = ?", [$id]);
    }
}
