<?php
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/User.php';

class ProductController {
    private $productModel;
    private $userModel;

    public function __construct() {
        $this->productModel = new Product();
        $this->userModel = new User();
    }

    public function getAllProducts() {
        $products = $this->productModel->findAll();
        return $this->enrichImages($products);
    }

    // Returns only products belonging to the given merchant email
    public function getProductsByMerchantEmail($email) {
        $user = $this->userModel->findByEmail($email);
        if (!$user) {
            header("HTTP/1.1 404 Not Found");
            return ["error" => "Merchant not found"];
        }
        $products = $this->productModel->findByMerchantId($user['id']);
        return $this->enrichImages($products);
    }

    private function enrichImages($products) {
        foreach ($products as &$dbProd) {
            $img = $dbProd['image_url'];
            if (empty($img) || (strpos($img, 'http') !== 0 && strpos($img, 'uploads/') !== 0 && strpos($img, 'data:image') !== 0)) {
                if (stripos($dbProd['name'], 'shoes') !== false) {
                    $img = "https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&h=400&fit=crop";
                } elseif (stripos($dbProd['name'], 'jersey') !== false) {
                    $img = "https://images.unsplash.com/photo-1517466787929-bc90951d0974?w=400&h=400&fit=crop";
                } elseif (stripos($dbProd['name'], 'racket') !== false) {
                    $img = "https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?w=400&h=400&fit=crop";
                } elseif (stripos($dbProd['name'], 'ball') !== false || stripos($dbProd['name'], 'football') !== false) {
                    $img = "https://images.unsplash.com/photo-1614632537190-23e4146777db?w=400&h=400&fit=crop";
                } elseif (stripos($dbProd['name'], 'gloves') !== false) {
                    $img = "https://images.unsplash.com/photo-1581009146145-b5ef050c2e1e?w=400&h=400&fit=crop";
                } else {
                    $img = "https://images.unsplash.com/photo-1602143407151-7111542de6e8?w=400&h=400&fit=crop";
                }
                $dbProd['image_url'] = $img;
            }
        }
        return $products;
    }

    public function getProductById($id) {
        $product = $this->productModel->findById($id);
        if (!$product) {
            header("HTTP/1.1 404 Not Found");
            return ["error" => "Product not found"];
        }
        return $product;
    }

    public function addProduct($data, $files = []) {
        $name        = $data['name']        ?? '';
        $category    = $data['category']    ?? '';
        $price       = floatval($data['price']    ?? 0);
        $description = $data['description'] ?? '';
        $imageUrl    = $data['imageUrl']    ?? ($data['image_url'] ?? '');
        $stock       = intval($data['stock']      ?? 0);
        $colors      = $data['colors']      ?? null;
        $sizes       = $data['sizes']       ?? null;
        $merchantEmail = $data['merchantEmail'] ?? null;

        // Resolve merchant user ID from email
        $merchantId = null;
        if ($merchantEmail) {
            $user = $this->userModel->findByEmail($merchantEmail);
            if ($user) {
                $merchantId = $user['id'];
            }
        }

        if (isset($files['productImage']) && $files['productImage']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            $file = $files['productImage'];
            if (in_array($file['type'], $allowedTypes)) {
                $uploadDir = __DIR__ . '/../uploads/products/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = uniqid('prod_') . '.' . $extension;
                $destination = $uploadDir . $filename;
                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    $imageUrl = 'uploads/products/' . $filename;
                }
            }
        }

        return $this->productModel->create($name, $category, $price, $description, $imageUrl, $stock, $colors, $merchantId, $sizes);
    }

    public function updateProduct($id, $data, $files = []) {
        $name = $data['name'] ?? '';
        $category = $data['category'] ?? '';
        $price = floatval($data['price'] ?? 0);
        $description = $data['description'] ?? '';
        $imageUrl = $data['imageUrl'] ?? ($data['image_url'] ?? '');
        $stock = intval($data['stock'] ?? 0);
        $colors = $data['colors'] ?? null;
        $sizes = $data['sizes'] ?? null;

        if (isset($files['productImage']) && $files['productImage']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            $file = $files['productImage'];
            if (in_array($file['type'], $allowedTypes)) {
                $uploadDir = __DIR__ . '/../uploads/products/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = uniqid('prod_') . '.' . $extension;
                $destination = $uploadDir . $filename;
                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    $imageUrl = 'uploads/products/' . $filename;
                }
            }
        }

        return $this->productModel->update($id, $name, $category, $price, $description, $imageUrl, $stock, $colors, $sizes);
    }

    public function deleteProduct($id) {
        $this->productModel->delete($id);
        return "Product deleted successfully";
    }
}
