<?php
require_once __DIR__ . '/../config/Database.php';

class Product {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // Get ALL products (for public store page)
    public function findAll() {
        $stmt = $this->db->query("SELECT * FROM products");
        return $stmt->fetchAll();
    }

    // Get products belonging to a specific merchant only
    public function findByMerchantId($merchantId) {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE merchant_id = ?");
        $stmt->execute([$merchantId]);
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Create a product linked to a merchant
    public function create($name, $category, $price, $description, $imageUrl, $stock, $colors = null, $merchantId = null, $sizes = null) {
        $stmt = $this->db->prepare(
            "INSERT INTO products (merchant_id, name, category, price, description, image_url, stock, colors, sizes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$merchantId, $name, $category, $price, $description, $imageUrl, $stock, $colors, $sizes]);
        return $this->findById($this->db->lastInsertId());
    }

    public function update($id, $name, $category, $price, $description, $imageUrl, $stock, $colors = null, $sizes = null) {
        $stmt = $this->db->prepare(
            "UPDATE products SET name = ?, category = ?, price = ?, description = ?, image_url = ?, stock = ?, colors = ?, sizes = ? WHERE id = ?"
        );
        $stmt->execute([$name, $category, $price, $description, $imageUrl, $stock, $colors, $sizes, $id]);
        return $this->findById($id);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>
