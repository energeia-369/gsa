<?php
require_once __DIR__ . '/../models/Category.php';

class CategoryController {
    private $categoryModel;

    public function __construct() {
        $this->categoryModel = new Category();
    }

    public function getAllCategories() {
        $categories = $this->categoryModel->findAll();
        return ["success" => true, "data" => $categories];
    }

    public function createCategory($data) {
        $name = $data['name'] ?? '';
        $icon = $data['icon'] ?? '';
        $description = $data['description'] ?? '';
        $isFeatured = isset($data['is_featured']) ? (bool)$data['is_featured'] : false;

        if (empty($name) || empty($icon)) {
            header("HTTP/1.1 400 Bad Request");
            return ["success" => false, "message" => "Name and Icon are required"];
        }

        $result = $this->categoryModel->create($name, $icon, $description, $isFeatured);
        if ($result) {
            return ["success" => true, "message" => "Category created successfully"];
        } else {
            header("HTTP/1.1 500 Internal Server Error");
            return ["success" => false, "message" => "Failed to create category"];
        }
    }

    public function updateCategory($id, $data) {
        $name = $data['name'] ?? '';
        $icon = $data['icon'] ?? '';
        $description = $data['description'] ?? '';
        $isFeatured = isset($data['is_featured']) ? (bool)$data['is_featured'] : false;

        if (empty($name) || empty($icon)) {
            header("HTTP/1.1 400 Bad Request");
            return ["success" => false, "message" => "Name and Icon are required"];
        }

        $result = $this->categoryModel->update($id, $name, $icon, $description, $isFeatured);
        if ($result) {
            return ["success" => true, "message" => "Category updated successfully"];
        } else {
            header("HTTP/1.1 500 Internal Server Error");
            return ["success" => false, "message" => "Failed to update category"];
        }
    }

    public function deleteCategory($id) {
        $result = $this->categoryModel->delete($id);
        if ($result) {
            return ["success" => true, "message" => "Category deleted successfully"];
        } else {
            header("HTTP/1.1 500 Internal Server Error");
            return ["success" => false, "message" => "Failed to delete category"];
        }
    }
}
