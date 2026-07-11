<?php
require_once __DIR__ . '/../config/Database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['rating']) || !isset($input['review_text'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// Get name and role from the frontend JS, falling back to defaults if missing
$name = isset($input['name']) && !empty(trim($input['name'])) ? trim($input['name']) : "Guest User";

$roleRaw = isset($input['role']) ? trim($input['role']) : "User";
// Format the role to look nicer
if (strtolower($roleRaw) === 'admin') {
    $role = 'Platform Admin';
} elseif (strtolower($roleRaw) === 'merchant') {
    $role = 'Merchant';
} elseif (strtolower($roleRaw) === 'user' || $roleRaw === 'Community Member') {
    $role = 'User';
} else {
    $role = ucfirst(strtolower($roleRaw));
}

try {
    $db = Database::getConnection();
    
    $stmt = $db->prepare("INSERT INTO client_reviews (name, role, rating, review_text) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $role, $input['rating'], $input['review_text']]);
    
    echo json_encode(['success' => true, 'message' => 'Review submitted successfully']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
