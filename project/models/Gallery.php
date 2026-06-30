<?php
require_once __DIR__ . '/../config/Database.php';

class Gallery {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
        $this->initTable();
    }

    private function initTable() {
        $query = "CREATE TABLE IF NOT EXISTS gallery_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            subtitle VARCHAR(255) NOT NULL,
            image_url TEXT NOT NULL,
            category VARCHAR(50) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $this->db->exec($query);
        
        // Check if empty, insert some default mock data for the UI
        $count = $this->db->query("SELECT COUNT(*) FROM gallery_items")->fetchColumn();
        if ($count == 0) {
            $defaults = [
                ['Cricket Tournament', 'Pune • 2026', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSj9TSuPwFk1Rq-Sd-u-vtfemMrXXqtTyncwA&s', 'cricket'],
                ['Football Match', 'Mumbai • 2026', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSoLudvDhpvy0lFm42r2Th35vX4wF9q2EbFQA&s', 'football'],
                ['Award Ceremony', 'Winners Celebration', 'https://static01.nyt.com/images/2016/12/13/sports/13ronaldo/13ronaldo-articleLarge.jpg?quality=75&auto=webp&disable=upscale', 'winners'],
                ['Basketball Event', 'Elite Sports Arena', 'https://thekhaitanschool.org/wp-content/uploads/2023/12/3-1.jpg', 'basketball']
            ];
            $stmt = $this->db->prepare("INSERT INTO gallery_items (title, subtitle, image_url, category) VALUES (?, ?, ?, ?)");
            foreach ($defaults as $d) {
                $stmt->execute($d);
            }
        }
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM gallery_items ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($title, $subtitle, $image_url, $category) {
        $stmt = $this->db->prepare("INSERT INTO gallery_items (title, subtitle, image_url, category) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$title, $subtitle, $image_url, $category]);
    }

    public function update($id, $title, $subtitle, $image_url, $category) {
        $stmt = $this->db->prepare("UPDATE gallery_items SET title=?, subtitle=?, image_url=?, category=? WHERE id=?");
        return $stmt->execute([$title, $subtitle, $image_url, $category, $id]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM gallery_items WHERE id=?");
        return $stmt->execute([$id]);
    }
}
