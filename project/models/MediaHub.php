<?php
require_once __DIR__ . '/../config/Database.php';

class MediaHub {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAllAdmin() {
        $stmt = $this->db->query("SELECT * FROM media_hub ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllVisible() {
        $stmt = $this->db->query("SELECT * FROM media_hub WHERE visibility = 1 ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO media_hub (title, category, video_link, thumbnail, tournament_name, stadium, duration, views, status, date_time, short_description) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            $data['title'],
            $data['category'],
            $data['video_link'],
            $data['thumbnail'],
            $data['tournament_name'] ?? '',
            $data['stadium'] ?? '',
            $data['duration'] ?? '',
            $data['views'] ?? '0',
            $data['status'] ?? 'Published',
            $data['date_time'] ?? '',
            $data['short_description'] ?? ''
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM media_hub WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function update($id, $data) {
        if (!empty($data['thumbnail'])) {
            $query = "UPDATE media_hub SET title = ?, category = ?, video_link = ?, thumbnail = ?, tournament_name = ?, stadium = ?, duration = ?, views = ?, status = ?, date_time = ?, short_description = ? WHERE id = ?";
            $params = [
                $data['title'],
                $data['category'],
                $data['video_link'],
                $data['thumbnail'],
                $data['tournament_name'] ?? '',
                $data['stadium'] ?? '',
                $data['duration'] ?? '',
                $data['views'] ?? '0',
                $data['status'] ?? 'Published',
                $data['date_time'] ?? '',
                $data['short_description'] ?? '',
                $id
            ];
        } else {
            $query = "UPDATE media_hub SET title = ?, category = ?, video_link = ?, tournament_name = ?, stadium = ?, duration = ?, views = ?, status = ?, date_time = ?, short_description = ? WHERE id = ?";
            $params = [
                $data['title'],
                $data['category'],
                $data['video_link'],
                $data['tournament_name'] ?? '',
                $data['stadium'] ?? '',
                $data['duration'] ?? '',
                $data['views'] ?? '0',
                $data['status'] ?? 'Published',
                $data['date_time'] ?? '',
                $data['short_description'] ?? '',
                $id
            ];
        }
        $stmt = $this->db->prepare($query);
        return $stmt->execute($params);
    }

    public function toggleVisibility($id, $visibility) {
        $stmt = $this->db->prepare("UPDATE media_hub SET visibility = ? WHERE id = ?");
        return $stmt->execute([$visibility, $id]);
    }
}
