<?php
require_once __DIR__ . '/../config/Database.php';

class Category {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function findAll() {
        $stmt = $this->db->query("SELECT * FROM sports_categories");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM sports_categories WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($name, $icon, $description, $isFeatured) {
        $stmt = $this->db->prepare("INSERT INTO sports_categories (name, icon, description, is_featured) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$name, $icon, $description, $isFeatured ? 1 : 0]);
    }

    public function update($id, $name, $icon, $description, $isFeatured) {
        $stmt = $this->db->prepare("UPDATE sports_categories SET name = ?, icon = ?, description = ?, is_featured = ? WHERE id = ?");
        return $stmt->execute([$name, $icon, $description, $isFeatured ? 1 : 0, $id]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM sports_categories WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
