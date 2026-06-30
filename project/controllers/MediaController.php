<?php
require_once __DIR__ . '/../models/MediaHub.php';

class MediaController {
    private $mediaModel;

    public function __construct() {
        $this->mediaModel = new MediaHub();
    }

    public function getAllVisible() {
        try {
            $items = $this->mediaModel->getAllVisible();
            return ["success" => true, "data" => $items];
        } catch (Exception $e) {
            http_response_code(500);
            return ["success" => false, "message" => "Failed to fetch media items"];
        }
    }

    public function getAllAdmin() {
        try {
            $items = $this->mediaModel->getAllAdmin();
            return ["success" => true, "data" => $items];
        } catch (Exception $e) {
            http_response_code(500);
            return ["success" => false, "message" => "Failed to fetch admin media items"];
        }
    }

    public function addMedia($data, $files = []) {
        $thumbnail_url = $data['thumbnail_url'] ?? '';

        if (isset($files['mediaThumbnail']) && $files['mediaThumbnail']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            $file = $files['mediaThumbnail'];
            
            if (!in_array($file['type'], $allowedTypes)) {
                http_response_code(400);
                return ["success" => false, "message" => "Invalid file type. Only JPG, JPEG, PNG, and WEBP are allowed."];
            }
            
            if ($file['size'] > 5 * 1024 * 1024) {
                http_response_code(400);
                return ["success" => false, "message" => "File size exceeds 5MB limit."];
            }
            
            $uploadDir = __DIR__ . '/../uploads/media/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid('media_') . '.' . $extension;
            $destination = $uploadDir . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $thumbnail_url = 'uploads/media/' . $filename;
            } else {
                http_response_code(500);
                return ["success" => false, "message" => "Failed to upload image."];
            }
        }

        if (empty($data['title']) || empty($data['video_link']) || empty($thumbnail_url)) {
            http_response_code(400);
            return ["success" => false, "message" => "Title, Video Link, and Thumbnail are required"];
        }

        $data['thumbnail'] = $thumbnail_url;

        try {
            $this->mediaModel->create($data);
            return ["success" => true, "message" => "Media item added successfully"];
        } catch (Exception $e) {
            http_response_code(500);
            return ["success" => false, "message" => "Failed to add media item: " . $e->getMessage()];
        }
    }

    public function updateMedia($id, $data, $files = []) {
        $thumbnail_url = '';

        if (isset($files['mediaThumbnail']) && $files['mediaThumbnail']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            $file = $files['mediaThumbnail'];
            
            if (!in_array($file['type'], $allowedTypes)) {
                http_response_code(400);
                return ["success" => false, "message" => "Invalid file type. Only JPG, JPEG, PNG, and WEBP are allowed."];
            }
            
            if ($file['size'] > 5 * 1024 * 1024) {
                http_response_code(400);
                return ["success" => false, "message" => "File size exceeds 5MB limit."];
            }
            
            $uploadDir = __DIR__ . '/../uploads/media/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid('media_') . '.' . $extension;
            $destination = $uploadDir . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $thumbnail_url = 'uploads/media/' . $filename;
            } else {
                http_response_code(500);
                return ["success" => false, "message" => "Failed to upload image."];
            }
        }

        if (empty($data['title']) || empty($data['video_link'])) {
            http_response_code(400);
            return ["success" => false, "message" => "Title and Video Link are required"];
        }

        if (!empty($thumbnail_url)) {
            $data['thumbnail'] = $thumbnail_url;
        }

        try {
            $this->mediaModel->update($id, $data);
            return ["success" => true, "message" => "Media item updated successfully"];
        } catch (Exception $e) {
            http_response_code(500);
            return ["success" => false, "message" => "Failed to update media item: " . $e->getMessage()];
        }
    }

    public function deleteMedia($id) {
        try {
            $this->mediaModel->delete($id);
            return ["success" => true, "message" => "Media item deleted successfully"];
        } catch (Exception $e) {
            http_response_code(500);
            return ["success" => false, "message" => "Failed to delete media item"];
        }
    }

    public function toggleVisibility($id, $visibility) {
        try {
            $this->mediaModel->toggleVisibility($id, $visibility);
            return ["success" => true, "message" => "Visibility updated successfully"];
        } catch (Exception $e) {
            http_response_code(500);
            return ["success" => false, "message" => "Failed to update visibility"];
        }
    }
}
