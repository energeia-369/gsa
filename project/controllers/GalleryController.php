<?php
require_once __DIR__ . '/../models/Gallery.php';

class GalleryController {
    private $galleryModel;

    public function __construct() {
        $this->galleryModel = new Gallery();
    }

    public function getAllItems() {
        try {
            $items = $this->galleryModel->getAll();
            return ["success" => true, "data" => $items];
        } catch (Exception $e) {
            http_response_code(500);
            return ["success" => false, "message" => "Failed to fetch gallery items"];
        }
    }

    public function addItem($data, $files = []) {
        $title = $data['title'] ?? '';
        $subtitle = $data['subtitle'] ?? '';
        $image_url = $data['image_url'] ?? '';
        $category = $data['category'] ?? 'all';

        if (isset($files['galleryImage']) && $files['galleryImage']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            $file = $files['galleryImage'];
            
            if (!in_array($file['type'], $allowedTypes)) {
                http_response_code(400);
                return ["success" => false, "message" => "Invalid file type. Only JPG, JPEG, PNG, and WEBP are allowed."];
            }
            
            if ($file['size'] > 5 * 1024 * 1024) {
                http_response_code(400);
                return ["success" => false, "message" => "File size exceeds 5MB limit."];
            }
            
            $uploadDir = __DIR__ . '/../uploads/gallery/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid('gallery_') . '.' . $extension;
            $destination = $uploadDir . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $image_url = 'uploads/gallery/' . $filename;
            } else {
                http_response_code(500);
                return ["success" => false, "message" => "Failed to upload image."];
            }
        }

        if (empty($title) || empty($image_url)) {
            http_response_code(400);
            return ["success" => false, "message" => "Title and Image URL (or uploaded file) are required"];
        }

        try {
            $this->galleryModel->create($title, $subtitle, $image_url, $category);
            return ["success" => true, "message" => "Gallery item added successfully"];
        } catch (Exception $e) {
            http_response_code(500);
            return ["success" => false, "message" => "Failed to add gallery item"];
        }
    }

    public function updateItem($id, $data, $files = []) {
        $title = $data['title'] ?? '';
        $subtitle = $data['subtitle'] ?? '';
        $image_url = $data['image_url'] ?? '';
        $category = $data['category'] ?? 'all';

        if (isset($files['galleryImage']) && $files['galleryImage']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            $file = $files['galleryImage'];
            
            if (!in_array($file['type'], $allowedTypes)) {
                http_response_code(400);
                return ["success" => false, "message" => "Invalid file type."];
            }
            
            $uploadDir = __DIR__ . '/../uploads/gallery/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid('gallery_') . '.' . $extension;
            $destination = $uploadDir . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $image_url = 'uploads/gallery/' . $filename;
            }
        }

        if (empty($title) || empty($image_url)) {
            http_response_code(400);
            return ["success" => false, "message" => "Title and Image URL are required"];
        }

        try {
            $this->galleryModel->update($id, $title, $subtitle, $image_url, $category);
            return ["success" => true, "message" => "Gallery item updated successfully"];
        } catch (Exception $e) {
            http_response_code(500);
            return ["success" => false, "message" => "Failed to update gallery item"];
        }
    }

    public function deleteItem($id) {
        try {
            $this->galleryModel->delete($id);
            return ["success" => true, "message" => "Gallery item deleted successfully"];
        } catch (Exception $e) {
            http_response_code(500);
            return ["success" => false, "message" => "Failed to delete gallery item"];
        }
    }
}
