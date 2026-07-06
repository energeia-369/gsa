<?php
// REST API Router / Front Controller
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Global Exception handler
set_exception_handler(function($e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
    exit();
});

require_once __DIR__ . '/../config/Config.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/UserController.php';
require_once __DIR__ . '/../controllers/ProductController.php';
require_once __DIR__ . '/../controllers/TournamentController.php';
require_once __DIR__ . '/../controllers/WalletController.php';
require_once __DIR__ . '/../controllers/EventRegistrationController.php';
require_once __DIR__ . '/../controllers/DelegateController.php';
require_once __DIR__ . '/../controllers/OrderController.php';
require_once __DIR__ . '/../controllers/ContactController.php';
require_once __DIR__ . '/../controllers/NewsletterController.php';
require_once __DIR__ . '/../controllers/BusinessInquiryController.php';
require_once __DIR__ . '/../controllers/MediaController.php';
require_once __DIR__ . '/../controllers/CategoryController.php';
require_once __DIR__ . '/../controllers/DestinationController.php';
require_once __DIR__ . '/../controllers/GiftCardController.php';
require_once __DIR__ . '/../controllers/ChatbotController.php';
require_once __DIR__ . '/../controllers/ReviewController.php';

// Parse URL path relative to api/index.php
// We can support calls to api/index.php/some/route or using url rewriting /api/some/route
$requestUri = $_SERVER['REQUEST_URI'];
$scriptName = $_SERVER['SCRIPT_NAME'];

// Remove script directory to find the virtual route path
$route = '/';
if (strpos($requestUri, $scriptName) === 0) {
    $route = substr($requestUri, strlen($scriptName));
} else {
    // If url rewriting is active and script name is not in URI
    $scriptDir = dirname($scriptName);
    $scriptDir = ($scriptDir === '\\' || $scriptDir === '/') ? '' : $scriptDir;
    $route = substr($requestUri, strlen($scriptDir));
}

// Clean up query string if present
if (($pos = strpos($route, '?')) !== false) {
    $route = substr($route, 0, $pos);
}
$route = '/' . trim($route, '/');

$method = $_SERVER['REQUEST_METHOD'];
$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? null;
$jsonData = json_decode(file_get_contents('php://input'), true) ?? [];

// Helper to check match with placeholder wildcard (e.g. /products/{id})
function matchRoute($pattern, $route, &$params = []) {
    $patternParts = explode('/', trim($pattern, '/'));
    $routeParts = explode('/', trim($route, '/'));

    if (count($patternParts) !== count($routeParts)) {
        return false;
    }

    foreach ($patternParts as $index => $part) {
        if (strpos($part, '{') === 0 && strpos($part, '}') === strlen($part) - 1) {
            $paramName = substr($part, 1, -1);
            $params[$paramName] = $routeParts[$index];
        } elseif ($part !== $routeParts[$index]) {
            return false;
        }
    }
    return true;
}

// Route mapping
$matched = false;
$response = null;

// Auth routes
if ($route === '/auth/register' && $method === 'POST') {
    $auth = new AuthController();
    $response = $auth->register($jsonData);
    $matched = true;
} elseif ($route === '/auth/register-send-otp' && $method === 'POST') {
    $auth = new AuthController();
    $response = $auth->registerSendOtp($jsonData);
    $matched = true;
} elseif ($route === '/auth/login' && $method === 'POST') {
    $auth = new AuthController();
    $response = $auth->login($jsonData);
    $matched = true;
} elseif ($route === '/auth/send-otp' && $method === 'POST') {
    $auth = new AuthController();
    $response = $auth->sendOtp($jsonData);
    $matched = true;
} elseif ($route === '/auth/verify-otp' && $method === 'POST') {
    $authCtrl = new AuthController();
    $data = json_decode(file_get_contents('php://input'), true);
    $response = $authCtrl->verifyOtp($data);
    $matched = true;
} elseif ($route === '/auth/register-merchant' && $method === 'POST') {
    $auth = new AuthController();
    $response = $auth->registerMerchant($jsonData);
    $matched = true;
} elseif ($route === '/auth/login-merchant' && $method === 'POST') {
    $auth = new AuthController();
    $response = $auth->loginMerchant($jsonData);
    $matched = true;
} elseif ($route === '/merchant/profile' && $method === 'GET') {
    $email = $_GET['email'] ?? '';
    if (!$email) { header("HTTP/1.1 400 Bad Request"); $response = ["success" => false, "message" => "Email required"]; }
    else {
        require_once __DIR__ . '/../config/Database.php';
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id, merchant_name, email, phone FROM merchants WHERE email = ?");
        $stmt->execute([$email]);
        $merchant = $stmt->fetch(PDO::FETCH_ASSOC);
        $response = $merchant ? ["success" => true, "merchant" => $merchant] : ["success" => false, "message" => "Not found"];
    }
    $matched = true;
} elseif ($route === '/merchant/update-profile' && $method === 'POST') {
    $email = $jsonData['email'] ?? '';
    $name  = trim($jsonData['merchant_name'] ?? '');
    $phone = trim($jsonData['phone'] ?? '');
    $newPassword = trim($jsonData['new_password'] ?? '');
    if (!$email || !$name || !$phone) { header("HTTP/1.1 400 Bad Request"); $response = ["success" => false, "message" => "Name, phone and email are required"]; }
    else {
        require_once __DIR__ . '/../config/Database.php';
        $db = Database::getConnection();
        if ($newPassword) {
            $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE merchants SET merchant_name=?, phone=?, password=? WHERE email=?");
            $stmt->execute([$name, $phone, $hashed, $email]);
        } else {
            $stmt = $db->prepare("UPDATE merchants SET merchant_name=?, phone=? WHERE email=?");
            $stmt->execute([$name, $phone, $email]);
        }
        // Update session values too via a flag
        $response = ["success" => true, "message" => "Profile updated successfully", "merchant_name" => $name, "phone" => $phone];
    }
    $matched = true;
} elseif ($route === '/merchant/receive-nxl' && $method === 'POST') {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'merchant') {
        header("HTTP/1.1 401 Unauthorized");
        $response = ["success" => false, "message" => "Unauthorized access."];
    } else {
        $identifier = trim($jsonData['userIdentifier'] ?? '');
        $amount = (int)($jsonData['amount'] ?? 0);
        
        if (empty($identifier) || $amount <= 0) {
            header("HTTP/1.1 400 Bad Request");
            $response = ["success" => false, "message" => "Valid user identifier and amount are required."];
        } else {
            require_once __DIR__ . '/../config/Database.php';
            try {
                $db = Database::getConnection();
                
                // Lookup user by email or phone
                $stmt = $db->prepare("SELECT id, credits, full_name FROM users WHERE email = ? OR phone_number = ? LIMIT 1");
                $stmt->execute([$identifier, $identifier]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$user) {
                    $response = ["success" => false, "message" => "User not found with the provided identifier."];
                } elseif ($user['credits'] < $amount) {
                    $response = ["success" => false, "message" => "User does not have enough NXL. Current balance: " . $user['credits']];
                } else {
                    // Deduct NXL from user (update all redundant columns)
                    $updateUserStmt = $db->prepare("UPDATE users SET credits = credits - ?, wallet_balance = wallet_balance - ? WHERE id = ?");
                    $updateUserStmt->execute([$amount, $amount, $user['id']]);
                    $updateWalletStmt = $db->prepare("UPDATE nxl_wallets SET balance = balance - ? WHERE user_id = ?");
                    $updateWalletStmt->execute([$amount, $user['id']]);
                    
                    // Add NXL to merchant's specific table
                    $merchantId = $_SESSION['merchant_id'] ?? 0;
                    $merchantUpdateStmt = $db->prepare("UPDATE merchants SET nxl_balance = nxl_balance + ? WHERE id = ?");
                    $merchantUpdateStmt->execute([$amount, $merchantId]);
                    
                    // Also add NXL to merchant's user wallet (so it shows up in wallet.php)
                    $mQuery = $db->prepare("SELECT email FROM merchants WHERE id = ?");
                    $mQuery->execute([$merchantId]);
                    $mRow = $mQuery->fetch(PDO::FETCH_ASSOC);
                    if ($mRow && !empty($mRow['email'])) {
                        $mEmail = $mRow['email'];
                        $mUserStmt = $db->prepare("SELECT id FROM users WHERE email = ?");
                        $mUserStmt->execute([$mEmail]);
                        $mUser = $mUserStmt->fetch(PDO::FETCH_ASSOC);
                        if ($mUser) {
                            $mUserId = $mUser['id'];
                            $db->prepare("UPDATE users SET credits = credits + ?, wallet_balance = wallet_balance + ? WHERE id = ?")->execute([$amount, $amount, $mUserId]);
                            $db->prepare("UPDATE nxl_wallets SET balance = balance + ? WHERE user_id = ?")->execute([$amount, $mUserId]);
                        }
                    }
                    
                    // Log transaction
                    $merchantName = $_SESSION['merchant_name'] ?? 'Merchant';
                    $description = "Paid to Merchant: " . $merchantName;
                    $logStmt = $db->prepare("INSERT INTO nxl_transactions (user_id, type, amount, description, ref_id) VALUES (?, 'Spent', ?, ?, 'merchant_payment')");
                    $logStmt->execute([$user['id'], $amount, $description]);
                    
                    $response = [
                        "success" => true,
                        "message" => "Successfully charged $amount NXL from {$user['full_name']}."
                    ];
                }
            } catch (Exception $e) {
                error_log("Receive NXL Error: " . $e->getMessage());
                $response = ["success" => false, "message" => "DB Error: " . $e->getMessage()];
            }
        }
    }
    $matched = true;
} elseif ($route === '/merchant/search-users' && $method === 'GET') {
    session_start();
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'merchant') {
        header("HTTP/1.1 401 Unauthorized");
        $response = ["success" => false, "message" => "Unauthorized access."];
    } else {
        $q = trim($_GET['q'] ?? '');
        if (strlen($q) < 2) {
            $response = ["success" => true, "users" => []];
        } else {
            require_once __DIR__ . '/../config/Database.php';
            try {
                $db = Database::getConnection();
                $likeQ = "%{$q}%";
                $stmt = $db->prepare("SELECT email, phone_number, full_name FROM users WHERE email LIKE ? OR phone_number LIKE ? LIMIT 10");
                $stmt->execute([$likeQ, $likeQ]);
                $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                $results = [];
                foreach ($users as $u) {
                    $results[] = [
                        'email' => $u['email'],
                        'phone' => $u['phone_number'],
                        'name' => $u['full_name']
                    ];
                }
                $response = ["success" => true, "users" => $results];
            } catch (Exception $e) {
                error_log("Search Users Error: " . $e->getMessage());
                $response = ["success" => false, "message" => "DB Error: " . $e->getMessage()];
            }
        }
    }
    $matched = true;
}

// Profile Routes
elseif ($route === '/user/profile' && $method === 'GET') {
    $userCtrl = new UserController();
    $email = $_GET['email'] ?? '';
    $response = $userCtrl->getUserProfile($email);
    $matched = true;
} elseif ($route === '/user/profile' && $method === 'POST') {
    $userCtrl = new UserController();
    $response = $userCtrl->updateProfile();
    $matched = true;
} elseif ($route === '/user/all' && $method === 'GET') {
    $userCtrl = new UserController();
    $response = $userCtrl->getAllUsers();
    $matched = true;
} elseif (matchRoute('/user/{id}', $route, $params) && $method === 'PUT') {
    $userCtrl = new UserController();
    $response = $userCtrl->updateUser($params['id'], $jsonData);
    $matched = true;
} elseif (matchRoute('/user/{id}', $route, $params) && $method === 'DELETE') {
    $userCtrl = new UserController();
    $response = $userCtrl->deleteUser($params['id']);
    $matched = true;
} elseif ($route === '/user/transactions' && $method === 'GET') {
    $userCtrl = new UserController();
    $email = $_GET['email'] ?? '';
    $response = $userCtrl->getUserTransactions($email);
    $matched = true;
} elseif ($route === '/user/notifications' && $method === 'GET') {
    $userCtrl = new UserController();
    $email = $_GET['email'] ?? '';
    $response = $userCtrl->getNotifications($email);
    $matched = true;
} elseif ($route === '/user/credits/redeem' && $method === 'POST') {
    $userCtrl = new UserController();
    $response = $userCtrl->redeemCredits($jsonData);
    $matched = true;
} elseif ($route === '/user/credits/issue' && $method === 'POST') {
    $userCtrl = new UserController();
    $response = $userCtrl->issueCoins($jsonData);
    $matched = true;
} elseif ($route === '/user/update-membership' && $method === 'POST') {
    $userCtrl = new UserController();
    $response = $userCtrl->updateMembership($jsonData);
    $matched = true;
} elseif ($route === '/user/latest_pass' && $method === 'GET') {
    $userCtrl = new UserController();
    $email = $_GET['email'] ?? '';
    $response = $userCtrl->getLatestPass($email);
    $matched = true;
} elseif ($route === '/user/all_passes' && $method === 'GET') {
    $userCtrl = new UserController();
    $email = $_GET['email'] ?? '';
    $response = $userCtrl->getAllPasses($email);
    $matched = true;
} elseif ($route === '/user/event_passes' && $method === 'GET') {
    $email = $_GET['email'] ?? '';
    if (!$email) { header("HTTP/1.1 400 Bad Request"); $response = ["success" => false, "message" => "Email required"]; }
    else {
        require_once __DIR__ . '/../config/Database.php';
        $db = Database::getConnection();
        // Auto-expire event passes
        $db->exec("UPDATE event_registrations SET status='expired' WHERE event_date IS NOT NULL AND event_date < CURDATE() AND status='active'");
        $stmt = $db->prepare("SELECT id, team_name, captain_name, sport, registration_type, team_category, payment_status, status, event_date FROM event_registrations WHERE email = ? ORDER BY id DESC");
        $stmt->execute([$email]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        foreach ($rows as &$r) {
            $r['qrUrl'] = $protocol . $_SERVER['HTTP_HOST'] . "/Mithraa_E_Project/project/verify-pass.php?type=event&id=" . $r['id'];
        }
        $response = ["success" => true, "passes" => $rows];
    }
    $matched = true;
} elseif ($route === '/user/award_passes' && $method === 'GET') {
    $email = $_GET['email'] ?? '';
    if (!$email) { header("HTTP/1.1 400 Bad Request"); $response = ["success" => false, "message" => "Email required"]; }
    else {
        require_once __DIR__ . '/../config/Database.php';
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id, registration_no, pass_no, full_name, pass_type, payment_status, created_at FROM award_registrations WHERE email = ? ORDER BY id DESC");
        $stmt->execute([$email]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        foreach ($rows as &$r) {
            $r['qrUrl'] = $protocol . $_SERVER['HTTP_HOST'] . "/Mithraa_E_Project/project/verify-award-pass.php?pass=" . urlencode($r['pass_no']);
            $r['ticketUrl'] = "award-ticket.php?reg=" . urlencode($r['registration_no']);
        }
        $response = ["success" => true, "passes" => $rows];
    }
    $matched = true;
}

// File upload API
elseif ($route === '/upload' && $method === 'POST') {
    if (!isset($_FILES['file'])) {
        $response = ["success" => false, "message" => "No file uploaded"];
    } else {
        $file = $_FILES['file'];
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        if (!in_array(strtolower($ext), $allowed)) {
            $response = ["success" => false, "message" => "Invalid file type"];
        } else {
            $uploadDir = __DIR__ . '/../uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $filename = uniqid('pillar_') . '.' . $ext;
            $targetPath = $uploadDir . $filename;
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                $response = ["success" => true, "url" => "uploads/" . $filename];
            } else {
                $response = ["success" => false, "message" => "Failed to move uploaded file"];
            }
        }
    }
    $matched = true;
}

// Settings API
elseif ($route === '/settings' && $method === 'PUT') {
    require_once __DIR__ . '/../config/Settings.php';
    if (isset($jsonData['delete_event_password'])) {
        Settings::set('delete_event_password', $jsonData['delete_event_password']);
    }
    if (isset($jsonData['pillars_title'])) {
        Settings::set('pillars_title', $jsonData['pillars_title']);
    }
    if (isset($jsonData['custom_link_text'])) {
        Settings::set('custom_link_text', $jsonData['custom_link_text']);
    }
    if (isset($jsonData['custom_link_url'])) {
        Settings::set('custom_link_url', $jsonData['custom_link_url']);
    }
    if (isset($jsonData['pillars'])) {
        Settings::set('pillars', $jsonData['pillars']);
    }
    if (isset($jsonData['nxl_cashback_percentage'])) {
        Settings::set('nxl_cashback_percentage', (float)$jsonData['nxl_cashback_percentage']);
    }
    if (isset($jsonData['signup_nxl_bonus'])) {
        Settings::set('signup_nxl_bonus', (int)$jsonData['signup_nxl_bonus']);
    }
    if (isset($jsonData['membership_plans'])) {
        Settings::set('membership_plans', json_encode($jsonData['membership_plans']));
    }
    $response = ["success" => true, "message" => "Settings updated"];
    $matched = true;
}
elseif ($route === '/settings' && $method === 'GET') {
    require_once __DIR__ . '/../config/Settings.php';
    $defaultPillars = [
        [
            "id" => "energeia",
            "title" => "ENERGEIA",
            "icon" => "fas fa-leaf",
            "tags" => ["Energy", "Sustainability", "EV", "Climate Tech"],
            "description" => "Building a sustainable future through clean energy, EV innovation and climate action.",
            "link" => "energeia.php",
            "image" => "https://images.unsplash.com/photo-1466611653911-95081537e5b7?w=500"
        ],
        [
            "id" => "ekonamia",
            "title" => "EKONAMIA",
            "icon" => "fas fa-chart-line",
            "tags" => ["Economy", "Fintech", "Investment", "Trade"],
            "description" => "Empowering global economy through finance, investment, trade and business growth.",
            "link" => "ekonamia.php",
            "image" => "https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=500"
        ],
        [
            "id" => "exploria",
            "title" => "EXPLORIA",
            "icon" => "fas fa-globe-americas",
            "tags" => ["Tourism", "Destinations", "Fintech", "Tech Showcase"],
            "description" => "Exploring the world through tourism, destinations and technology showcases.",
            "link" => "exploria.php",
            "image" => "https://images.unsplash.com/photo-1436491865332-7a61a109cc05?w=500"
        ],
        [
            "id" => "evexia",
            "title" => "EVEXIA",
            "icon" => "fas fa-heartbeat",
            "tags" => ["Wellness", "Hospitality", "Lifestyle", "Experiences"],
            "description" => "Enhancing life through wellness, hospitality, lifestyle and memorable experiences.",
            "link" => "evexia.php",
            "image" => "https://images.unsplash.com/photo-1545205597-3d9d02c29597?w=500"
        ],
        [
            "id" => "metroxia",
            "title" => "METROXIA",
            "icon" => "fas fa-city",
            "tags" => ["Urban", "Infrastructure", "Smart City", "Real Estate"],
            "description" => "Building smart, sustainable, and modern urban infrastructure for the future.",
            "link" => "metroxia.php",
            "image" => "https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=500"
        ]
    ];
    $response = [
        "success" => true, 
        "delete_event_password" => Settings::get('delete_event_password', 'admin'),
        "pillars_title" => Settings::get('pillars_title', 'Our Five Pillars'),
        "custom_link_text" => Settings::get('custom_link_text', ''),
        "custom_link_url" => Settings::get('custom_link_url', ''),
        "nxl_cashback_percentage" => Settings::get('nxl_cashback_percentage', 0.05),
        "signup_nxl_bonus" => Settings::get('signup_nxl_bonus', 25),
        "pillars" => Settings::get('pillars', $defaultPillars),
        "membership_plans" => json_decode(Settings::get('membership_plans', '{}'), true)
    ];
    $matched = true;
}

// Product routes
elseif ($route === '/products' && $method === 'GET') {
    $prodCtrl = new ProductController();
    $merchantEmail = $_GET['merchantEmail'] ?? null;
    if ($merchantEmail) {
        $response = $prodCtrl->getProductsByMerchantEmail($merchantEmail);
    } else {
        $response = $prodCtrl->getAllProducts();
    }
    $matched = true;
} elseif ($route === '/products' && $method === 'POST') {
    $prodCtrl = new ProductController();
    $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
    if (strpos($contentType, 'multipart/form-data') !== false) {
        $response = $prodCtrl->addProduct($_POST, $_FILES);
    } else {
        $response = $prodCtrl->addProduct($jsonData);
    }
    $matched = true;
} elseif (matchRoute('/products/{id}', $route, $params) && $method === 'GET') {
    $prodCtrl = new ProductController();
    $response = $prodCtrl->getProductById($params['id']);
    $matched = true;
} elseif (matchRoute('/products/{id}', $route, $params) && $method === 'PUT') {
    $prodCtrl = new ProductController();
    $response = $prodCtrl->updateProduct($params['id'], $jsonData);
    $matched = true;
} elseif (matchRoute('/products/{id}', $route, $params) && $method === 'POST') {
    $prodCtrl = new ProductController();
    $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
    if (strpos($contentType, 'multipart/form-data') !== false) {
        $response = $prodCtrl->updateProduct($params['id'], $_POST, $_FILES);
    } else {
        $response = $prodCtrl->updateProduct($params['id'], $jsonData);
    }
    $matched = true;
} elseif (matchRoute('/products/{id}', $route, $params) && $method === 'DELETE') {
    $prodCtrl = new ProductController();
    $response = $prodCtrl->deleteProduct($params['id']);
    $matched = true;
}

// Gallery routes
elseif ($route === '/gallery/items' && $method === 'GET') {
    require_once __DIR__ . '/../controllers/GalleryController.php';
    $galCtrl = new GalleryController();
    $response = $galCtrl->getAllItems();
    $matched = true;
} elseif ($route === '/gallery/add' && $method === 'POST') {
    require_once __DIR__ . '/../controllers/GalleryController.php';
    $galCtrl = new GalleryController();
    
    $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
    if (strpos($contentType, 'multipart/form-data') !== false) {
        $response = $galCtrl->addItem($_POST, $_FILES);
    } else {
        $response = $galCtrl->addItem($jsonData);
    }
    $matched = true;
} elseif (matchRoute('/gallery/{id}', $route, $params) && $method === 'PUT') {
    require_once __DIR__ . '/../controllers/GalleryController.php';
    $galCtrl = new GalleryController();
    $response = $galCtrl->updateItem($params['id'], $jsonData); // Currently assumes json update for edits
    $matched = true;
} elseif (matchRoute('/gallery/{id}', $route, $params) && $method === 'POST') {
    // POST to /gallery/{id} used for multipart form data updates
    require_once __DIR__ . '/../controllers/GalleryController.php';
    $galCtrl = new GalleryController();
    $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
    if (strpos($contentType, 'multipart/form-data') !== false) {
        $response = $galCtrl->updateItem($params['id'], $_POST, $_FILES);
    } else {
        $response = $galCtrl->updateItem($params['id'], $jsonData);
    }
    $matched = true;
} elseif (matchRoute('/gallery/{id}', $route, $params) && $method === 'DELETE') {
    require_once __DIR__ . '/../controllers/GalleryController.php';
    $galCtrl = new GalleryController();
    $response = $galCtrl->deleteItem($params['id']);
    $matched = true;
}

// Media routes
elseif ($route === '/media/items' && $method === 'GET') {
    $mediaCtrl = new MediaController();
    $response = $mediaCtrl->getAllVisible();
    $matched = true;
} elseif ($route === '/media/admin/items' && $method === 'GET') {
    $mediaCtrl = new MediaController();
    $response = $mediaCtrl->getAllAdmin();
    $matched = true;
} elseif ($route === '/media/add' && $method === 'POST') {
    $mediaCtrl = new MediaController();
    
    $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
    if (strpos($contentType, 'multipart/form-data') !== false) {
        $response = $mediaCtrl->addMedia($_POST, $_FILES);
    } else {
        $response = $mediaCtrl->addMedia($jsonData);
    }
    $matched = true;
} elseif (matchRoute('/media/{id}', $route, $params) && $method === 'POST') {
    $mediaCtrl = new MediaController();
    $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
    if (strpos($contentType, 'multipart/form-data') !== false) {
        $response = $mediaCtrl->updateMedia($params['id'], $_POST, $_FILES);
    } else {
        $response = $mediaCtrl->updateMedia($params['id'], $jsonData);
    }
    $matched = true;
} elseif (matchRoute('/media/{id}', $route, $params) && $method === 'DELETE') {
    $mediaCtrl = new MediaController();
    $response = $mediaCtrl->deleteMedia($params['id']);
    $matched = true;
} elseif (matchRoute('/media/{id}/visibility', $route, $params) && $method === 'PUT') {
    $mediaCtrl = new MediaController();
    $response = $mediaCtrl->toggleVisibility($params['id'], $jsonData['visibility'] ?? 1);
    $matched = true;
}

// Category routes
elseif ($route === '/categories/all' && $method === 'GET') {
    $catCtrl = new CategoryController();
    $response = $catCtrl->getAllCategories();
    $matched = true;
} elseif ($route === '/categories/create' && $method === 'POST') {
    $catCtrl = new CategoryController();
    $response = $catCtrl->createCategory($jsonData);
    $matched = true;
} elseif (strpos($route, '/destinations') === 0) {
    require_once __DIR__ . '/../controllers/DestinationController.php';
    $destCtrl = new DestinationController();
    
    // Parse the path to get ID for DELETE
    $pathParts = explode('/', trim($route, '/'));
    $destCtrl->handleRequest($method, $pathParts);
    $matched = true;
} elseif (strpos($route, '/reviews') === 0) {
    require_once __DIR__ . '/../controllers/ReviewController.php';
    $reviewCtrl = new ReviewController();
    
    // Parse the path to get ID for DELETE
    $pathParts = explode('/', trim($route, '/'));
    
    // Handle Reviews API
    $reviewCtrl->handleRequest($method, $pathParts);
    $matched = true;
} elseif (matchRoute('/categories/{id}', $route, $params) && $method === 'PUT') {
    $catCtrl = new CategoryController();
    $response = $catCtrl->updateCategory($params['id'], $jsonData);
    $matched = true;
} elseif (matchRoute('/categories/{id}', $route, $params) && $method === 'DELETE') {
    $catCtrl = new CategoryController();
    $response = $catCtrl->deleteCategory($params['id']);
    $matched = true;
}

// Chatbot routes
elseif ($route === '/chatbot/faqs' && $method === 'GET') {
    $chatCtrl = new ChatbotController();
    $response = $chatCtrl->getAllFaqs();
    $matched = true;
} elseif ($route === '/chatbot/faqs' && $method === 'POST') {
    $chatCtrl = new ChatbotController();
    $response = $chatCtrl->createFaq($jsonData);
    $matched = true;
} elseif (matchRoute('/chatbot/faqs/{id}', $route, $params) && $method === 'PUT') {
    $chatCtrl = new ChatbotController();
    $response = $chatCtrl->updateFaq($params['id'], $jsonData);
    $matched = true;
} elseif (matchRoute('/chatbot/faqs/{id}', $route, $params) && $method === 'DELETE') {
    $chatCtrl = new ChatbotController();
    $response = $chatCtrl->deleteFaq($params['id']);
    $matched = true;
}

// Tournament routes
elseif ($route === '/tournaments' && $method === 'GET') {
    $tourCtrl = new TournamentController();
    $response = $tourCtrl->getAllTournaments();
    $matched = true;
} elseif ($route === '/tournaments' && $method === 'POST') {
    $tourCtrl = new TournamentController();
    $response = $tourCtrl->createTournament($jsonData);
    $matched = true;
} elseif (matchRoute('/tournaments/{id}', $route, $params) && $method === 'GET') {
    $tourCtrl = new TournamentController();
    $response = $tourCtrl->getTournamentById($params['id']);
    $matched = true;
} elseif (matchRoute('/tournaments/{id}', $route, $params) && $method === 'PUT') {
    $tourCtrl = new TournamentController();
    $response = $tourCtrl->updateTournament($params['id'], $jsonData);
    $matched = true;
} elseif (matchRoute('/tournaments/{id}', $route, $params) && $method === 'DELETE') {
    $tourCtrl = new TournamentController();
    $response = $tourCtrl->deleteTournament($params['id']);
    $matched = true;
}

// Passes & Exhibitors routes
elseif ($route === '/visitor-passes' && $method === 'GET') {
    $db = (new Database())->getConnection();
    $stmt = $db->query("SELECT * FROM visitor_passes ORDER BY created_at DESC");
    $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $matched = true;
} elseif ($route === '/exhibitors' && $method === 'GET') {
    $db = (new Database())->getConnection();
    $stmt = $db->query("SELECT * FROM exhibitors ORDER BY created_at DESC");
    $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $matched = true;
}

// Wallet routes
elseif ($route === '/wallet/balance' && $method === 'GET') {
    $walletCtrl = new WalletController();
    $email = $_GET['email'] ?? '';
    $response = $walletCtrl->getWalletBalance($email, $authHeader);
    $matched = true;
} elseif ($route === '/wallet/transactions' && $method === 'GET') {
    $walletCtrl = new WalletController();
    $email = $_GET['email'] ?? '';
    $response = $walletCtrl->getWalletTransactions($email, $authHeader);
    $matched = true;
} elseif ($route === '/wallet/recharge' && $method === 'POST') {
    $walletCtrl = new WalletController();
    $response = $walletCtrl->rechargeWallet($jsonData);
    $matched = true;
} elseif ($route === '/wallet/admin/adjust' && $method === 'POST') {
    $walletCtrl = new WalletController();
    $response = $walletCtrl->adminAdjustWallet($jsonData);
    $matched = true;
} elseif ($route === '/wallet/admin/transactions' && $method === 'GET') {
    $walletCtrl = new WalletController();
    $response = $walletCtrl->getAllTransactions();
    $matched = true;
}

// Event Registrations
elseif ($route === '/event-registrations' && $method === 'POST') {
    $eventCtrl = new EventRegistrationController();
    $response = $eventCtrl->registerEvent($jsonData);
    $matched = true;
} elseif ($route === '/event-registrations' && $method === 'GET') {
    $eventCtrl = new EventRegistrationController();
    $response = $eventCtrl->getAllRegistrations();
    $matched = true;
}
// Public Orders
elseif ($route === '/public-payment/create-razorpay-order' && $method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $amount = isset($data['amount']) ? intval($data['amount']) : 0;
    
    if ($amount <= 0) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid amount"]);
        exit();
    }
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.razorpay.com/v1/orders');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'amount' => $amount * 100, // convert INR to paise
        'currency' => 'INR',
        'receipt' => 'rcptid_public_' . time()
    ]));
    curl_setopt($ch, CURLOPT_USERPWD, RAZORPAY_KEY_ID . ':' . RAZORPAY_KEY_SECRET);
    $headers = array('Content-Type: application/json');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $result = curl_exec($ch);
    if ($result === false) {
        $error = curl_error($ch);
        curl_close($ch);
        http_response_code(500);
        echo json_encode(["error" => "Backend cURL Error: " . $error]);
        exit();
    }
    
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    http_response_code($httpCode);
    echo $result;
    exit();
}

// Orders
elseif ($route === '/orders/create-razorpay-order' && $method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $amount = isset($data['amount']) ? intval($data['amount']) : 0;
    
    if ($amount <= 0) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid amount"]);
        exit();
    }
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.razorpay.com/v1/orders');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'amount' => $amount * 100, // convert INR to paise
        'currency' => 'INR',
        'receipt' => 'rcptid_' . time()
    ]));
    curl_setopt($ch, CURLOPT_USERPWD, RAZORPAY_KEY_ID . ':' . RAZORPAY_KEY_SECRET);
    $headers = array();
    $headers[] = 'Content-Type: application/json';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Fix for XAMPP localhost SSL issues
    
    $result = curl_exec($ch);
    if ($result === false) {
        $error = curl_error($ch);
        curl_close($ch);
        http_response_code(500);
        echo json_encode(["error" => "Backend cURL Error: " . $error]);
        exit();
    }
    
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    http_response_code($httpCode);
    echo $result;
    exit();

} elseif ($route === '/orders/place' && $method === 'POST') {
    $orderCtrl = new OrderController();
    $response = $orderCtrl->placeOrder($jsonData, $authHeader);
    $matched = true;
} elseif ($route === '/orders/my-orders' && $method === 'GET') {
    $orderCtrl = new OrderController();
    $email = $_GET['email'] ?? null;
    $response = $orderCtrl->getOrdersByUserEmail($email, $authHeader);
    $matched = true;
} elseif ($route === '/orders/all' && $method === 'GET') {
    $orderCtrl = new OrderController();
    $response = $orderCtrl->getAllOrders();
    $matched = true;
} elseif (matchRoute('/orders/{id}/status', $route, $params) && $method === 'PUT') {
    $orderCtrl = new OrderController();
    $response = $orderCtrl->updateOrderStatus($params['id'], $jsonData['status'] ?? '');
    $matched = true;
}

// Contact
elseif ($route === '/contact' && $method === 'POST') {
    $contactCtrl = new ContactController();
    $response = $contactCtrl->saveMessage($jsonData);
    $matched = true;
} elseif ($route === '/contact' && $method === 'GET') {
    $contactCtrl = new ContactController();
    $response = $contactCtrl->getAllMessages();
    $matched = true;
}

// Business Inquiries
elseif ($route === '/business-inquiries' && $method === 'GET') {
    $bizCtrl = new BusinessInquiryController();
    $response = $bizCtrl->getAllInquiries();
    $matched = true;
}

// Newsletter
elseif ($route === '/newsletter/subscribe' && $method === 'POST') {
    $newsCtrl = new NewsletterController();
    $response = $newsCtrl->subscribe($jsonData);
    $matched = true;
} elseif ($route === '/newsletter/subscribers' && $method === 'GET') {
    $newsCtrl = new NewsletterController();
    $response = $newsCtrl->getSubscribers();
    $matched = true;
} elseif ($route === '/exhibitors' && $method === 'GET') {
    $db = (new Database())->getConnection();
    $stmt = $db->query("SELECT * FROM exhibitors ORDER BY created_at DESC");
    $exhibitors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $response = [
        "success" => true,
        "data" => $exhibitors
    ];
    $matched = true;
} elseif ($route === '/exhibitors/update-status' && $method === 'PUT') {
    $db = (new Database())->getConnection();
    $id = $jsonData['id'] ?? null;
    $status = $jsonData['status'] ?? null;
    if ($id && $status && in_array($status, ['pending', 'approved', 'rejected'])) {
        $stmt = $db->prepare("UPDATE exhibitors SET approval_status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);
        $response = ["success" => true, "message" => "Status updated successfully"];
    } else {
        $response = ["success" => false, "message" => "Invalid ID or status"];
    }
    $matched = true;
} elseif ($route === '/exhibitors/update-payment' && $method === 'PUT') {
    $db = (new Database())->getConnection();
    $id = $jsonData['id'] ?? null;
    $paymentId = $jsonData['razorpay_payment_id'] ?? null;
    if ($id && $paymentId) {
        $stmt = $db->prepare("UPDATE exhibitors SET razorpay_payment_id = ? WHERE id = ?");
        $stmt->execute([$paymentId, $id]);
        $response = ["success" => true, "message" => "Payment saved"];
    } else {
        $response = ["success" => false, "message" => "Invalid data"];
    }
    $matched = true;
} elseif ($route === '/user-exhibitor-applications' && $method === 'GET') {
    $db = (new Database())->getConnection();
    $userId = $_GET['user_id'] ?? null;
    if ($userId) {
        $stmt = $db->prepare("SELECT * FROM exhibitors WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$userId]);
        $apps = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $response = ["success" => true, "data" => $apps];
    } else {
        $response = ["success" => false, "message" => "User ID required"];
    }
    $matched = true;
} elseif ($route === '/booth-options' && $method === 'GET') {
    $db = (new Database())->getConnection();
    $stmt = $db->query("SELECT * FROM booth_options ORDER BY id ASC");
    $options = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $response = [
        "success" => true,
        "data" => $options
    ];
    $matched = true;
} elseif ($route === '/booth-options' && $method === 'POST') {
    $db = (new Database())->getConnection();
    $name = $jsonData['name'] ?? '';
    $price = isset($jsonData['price']) ? floatval($jsonData['price']) : 0;
    
    if ($name) {
        $stmt = $db->prepare("INSERT INTO booth_options (name, price) VALUES (?, ?)");
        $stmt->execute([$name, $price]);
        $response = ["success" => true, "message" => "Booth option added"];
    } else {
        $response = ["success" => false, "message" => "Name is required"];
    }
    $matched = true;
} elseif (matchRoute('/booth-options/{id}', $route, $params) && $method === 'DELETE') {
    $db = (new Database())->getConnection();
    $stmt = $db->prepare("DELETE FROM booth_options WHERE id = ?");
    $stmt->execute([$params['id']]);
    $response = ["success" => true, "message" => "Booth option deleted"];
    $matched = true;
} elseif ($route === '/chatbot-faqs' && $method === 'GET') {
    $db = (new Database())->getConnection();
    $stmt = $db->query("SELECT * FROM chatbot_faqs ORDER BY id ASC");
    $faqs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $response = ["success" => true, "data" => $faqs];
    $matched = true;
} elseif ($route === '/chatbot-faqs' && $method === 'POST') {
    $db = (new Database())->getConnection();
    $question = $jsonData['question'] ?? '';
    $answer = $jsonData['answer'] ?? '';
    
    if ($question && $answer) {
        $stmt = $db->prepare("INSERT INTO chatbot_faqs (question, answer) VALUES (?, ?)");
        $stmt->execute([$question, $answer]);
        $response = ["success" => true, "message" => "FAQ added successfully"];
    } else {
        $response = ["success" => false, "message" => "Question and answer required"];
    }
    $matched = true;
} elseif (matchRoute('/chatbot-faqs/{id}', $route, $params) && $method === 'PUT') {
    $db = (new Database())->getConnection();
    $question = $jsonData['question'] ?? '';
    $answer = $jsonData['answer'] ?? '';
    
    if ($question && $answer) {
        $stmt = $db->prepare("UPDATE chatbot_faqs SET question = ?, answer = ? WHERE id = ?");
        $stmt->execute([$question, $answer, $params['id']]);
        $response = ["success" => true, "message" => "FAQ updated successfully"];
    } else {
        $response = ["success" => false, "message" => "Question and answer required"];
    }
    $matched = true;
} elseif (matchRoute('/chatbot-faqs/{id}', $route, $params) && $method === 'DELETE') {
    $db = (new Database())->getConnection();
    $stmt = $db->prepare("DELETE FROM chatbot_faqs WHERE id = ?");
    $stmt->execute([$params['id']]);
    $response = ["success" => true, "message" => "FAQ deleted successfully"];
    $matched = true;

} elseif ($route === '/settings' && $method === 'GET') {
    $db = (new Database())->getConnection();
    $stmt = $db->query("SELECT setting_key, setting_value FROM system_settings");
    $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    $response = [
        "success" => true,
        "data" => $settings
    ];
    $matched = true;
} elseif ($route === '/settings/sponsors' && $method === 'POST') {
    if (empty($_POST) && empty($_FILES) && $_SERVER['CONTENT_LENGTH'] > 0) {
        $max = ini_get('post_max_size');
        echo json_encode(["success" => false, "message" => "Total payload is too large. It exceeds the PHP post_max_size of $max. Please do not paste massive base64 image strings, use the 'Choose File' button instead!"]);
        exit();
    }

    $db = (new Database())->getConnection();
    $allowedSponsorKeys = [];
    for ($i = 1; $i <= 9; $i++) {
        $allowedSponsorKeys[] = 'sponsor_' . $i;
    }
    
    $uploadDir = __DIR__ . '/../assets/img/sponsors/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    foreach ($allowedSponsorKeys as $key) {
        $val = null;
        
        // If file was uploaded
        if (isset($_FILES[$key])) {
            if ($_FILES[$key]['error'] === UPLOAD_ERR_OK) {
                $tmpName = $_FILES[$key]['tmp_name'];
                $fileName = time() . '_' . basename($_FILES[$key]['name']);
                $targetFile = $uploadDir . $fileName;
                
                if (move_uploaded_file($tmpName, $targetFile)) {
                    $val = 'assets/img/sponsors/' . $fileName;
                } else {
                    $response = ["success" => false, "message" => "Failed to move uploaded file for $key. Please check folder permissions."];
                    echo json_encode($response);
                    exit();
                }
            } else if ($_FILES[$key]['error'] !== UPLOAD_ERR_NO_FILE) {
                // If there was an upload error other than NO_FILE
                $errCode = $_FILES[$key]['error'];
                $errMsg = "Unknown upload error";
                if ($errCode == UPLOAD_ERR_INI_SIZE) $errMsg = "File is larger than upload_max_filesize directive in php.ini.";
                if ($errCode == UPLOAD_ERR_NO_TMP_DIR) $errMsg = "Missing a temporary folder in XAMPP.";
                if ($errCode == UPLOAD_ERR_CANT_WRITE) $errMsg = "Failed to write file to disk.";
                
                $response = ["success" => false, "message" => "Upload error for $key: $errMsg (Code: $errCode)"];
                echo json_encode($response);
                exit();
            }
        } 
        // Or if it was sent as text
        elseif (isset($_POST[$key])) {
            $val = $_POST[$key];
        }
        
        if ($val !== null) {
            if ($val === '') {
                // Delete if empty (handles the delete button and clearing)
                $db->prepare("DELETE FROM system_settings WHERE setting_key IN (?, ?, ?)")->execute([$key, $key . '_name', $key . '_website']);
            } else {
                // Insert or Update the main logo
                $stmt = $db->prepare("SELECT setting_value FROM system_settings WHERE setting_key = ?");
                $stmt->execute([$key]);
                if ($stmt->fetch()) {
                    $update = $db->prepare("UPDATE system_settings SET setting_value = ? WHERE setting_key = ?");
                    $update->execute([$val, $key]);
                } else {
                    $insert = $db->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES (?, ?)");
                    $insert->execute([$key, $val]);
                }
                
                // Process Name and Website
                foreach (['_name', '_website'] as $suffix) {
                    $fieldKey = $key . $suffix;
                    if (isset($_POST[$fieldKey])) {
                        $fieldVal = $_POST[$fieldKey];
                        $stmt = $db->prepare("SELECT setting_value FROM system_settings WHERE setting_key = ?");
                        $stmt->execute([$fieldKey]);
                        if ($stmt->fetch()) {
                            $update = $db->prepare("UPDATE system_settings SET setting_value = ? WHERE setting_key = ?");
                            $update->execute([$fieldVal, $fieldKey]);
                        } else {
                            $insert = $db->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES (?, ?)");
                            $insert->execute([$fieldKey, $fieldVal]);
                        }
                    }
                }
            }
        }
    }
    
    $response = ["success" => true, "message" => "Sponsorships updated successfully."];
    $matched = true;
} elseif ($route === '/settings' && $method === 'PUT') {
    $db = (new Database())->getConnection();
    
    if (isset($jsonData['event_fee'])) {
        $fee = floatval($jsonData['event_fee']);
        $stmt = $db->prepare("UPDATE system_settings SET setting_value = ? WHERE setting_key = 'event_fee'");
        $stmt->execute([$fee]);
    }
    
    if (isset($jsonData['visitor_pass_fee'])) {
        $vFee = floatval($jsonData['visitor_pass_fee']);
        $stmt = $db->prepare("SELECT setting_value FROM system_settings WHERE setting_key = 'visitor_pass_fee'");
        $stmt->execute();
        if ($stmt->fetch()) {
            $update = $db->prepare("UPDATE system_settings SET setting_value = ? WHERE setting_key = 'visitor_pass_fee'");
            $update->execute([$vFee]);
        } else {
            $insert = $db->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES ('visitor_pass_fee', ?)");
            $insert->execute([$vFee]);
        }
    }

    if (isset($jsonData['exhibitor_fee'])) {
        $eFee = floatval($jsonData['exhibitor_fee']);
        $stmt = $db->prepare("SELECT setting_value FROM system_settings WHERE setting_key = 'exhibitor_fee'");
        $stmt->execute();
        if ($stmt->fetch()) {
            $update = $db->prepare("UPDATE system_settings SET setting_value = ? WHERE setting_key = 'exhibitor_fee'");
            $update->execute([$eFee]);
        } else {
            $insert = $db->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES ('exhibitor_fee', ?)");
            $insert->execute([$eFee]);
        }
    }
    if (isset($jsonData['exhibitor_pricing'])) {
        $ePricing = json_encode($jsonData['exhibitor_pricing']);
        $stmt = $db->prepare("SELECT setting_value FROM system_settings WHERE setting_key = 'exhibitor_pricing'");
        $stmt->execute();
        if ($stmt->fetch()) {
            $update = $db->prepare("UPDATE system_settings SET setting_value = ? WHERE setting_key = 'exhibitor_pricing'");
            $update->execute([$ePricing]);
        } else {
            $insert = $db->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES ('exhibitor_pricing', ?)");
            $insert->execute([$ePricing]);
        }
    }

    $allowedSponsorKeys = ['sponsor_title', 'sponsor_powered', 'sponsor_platinum', 'sponsor_gold', 'sponsor_silver', 'sponsor_associate'];
    foreach ($allowedSponsorKeys as $key) {
        if (isset($jsonData[$key])) {
            $val = $jsonData[$key];
            $stmt = $db->prepare("SELECT setting_value FROM system_settings WHERE setting_key = ?");
            $stmt->execute([$key]);
            if ($stmt->fetch()) {
                $update = $db->prepare("UPDATE system_settings SET setting_value = ? WHERE setting_key = ?");
                $update->execute([$val, $key]);
            } else {
                $insert = $db->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES (?, ?)");
                $insert->execute([$key, $val]);
            }
        }
    }

    if (isset($jsonData['delete_event_password'])) {
        $val = $jsonData['delete_event_password'];
        $stmt = $db->prepare("SELECT setting_value FROM system_settings WHERE setting_key = 'delete_event_password'");
        $stmt->execute();
        if ($stmt->fetch()) {
            $update = $db->prepare("UPDATE system_settings SET setting_value = ? WHERE setting_key = 'delete_event_password'");
            $update->execute([$val]);
        } else {
            $insert = $db->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES ('delete_event_password', ?)");
            $insert->execute([$val]);
        }
    }

    $response = [
        "success" => true,
        "message" => "Settings updated successfully"
    ];
    $matched = true;
}

// ---------------------------------------------------------
// GIFT CARDS ROUTES
// ---------------------------------------------------------
elseif (strpos($route, '/giftcards') === 0) {
    $gcController = new GiftCardController();
    $subRoute = strtok(substr($route, strlen('/giftcards')), '?');

    if ($method === 'GET') {
        if ($subRoute === '/user') {
            $email = $_GET['email'] ?? '';
            echo json_encode($gcController->getUserGiftCards($email));
        } elseif ($subRoute === '/admin-orders') {
            // Admin: get all orders
            $db2 = (new Database())->getConnection();
            $stmt = $db2->query("SELECT gco.*, gc.name as card_name FROM gift_card_orders gco JOIN gift_cards gc ON gco.gift_card_id = gc.id ORDER BY gco.id DESC LIMIT 50");
            echo json_encode(["success" => true, "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        } elseif (preg_match('/^\/(\d+)$/', $subRoute, $matches)) {
            echo json_encode($gcController->getById($matches[1]));
        } else {
            echo json_encode($gcController->getAllActive());
        }
    } elseif ($method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) $data = $_POST;

        if ($subRoute === '/checkout') {
            $data['payment_status'] = 'completed';
            echo json_encode($gcController->checkout($data));
        } elseif ($subRoute === '/redeem') {
            $code  = trim($data['code'] ?? '');
            $email = trim($data['email'] ?? '');
            if (!$code || !$email) {
                http_response_code(400);
                echo json_encode(["success" => false, "message" => "Code and email required."]);
            } else {
                echo json_encode($gcController->redeem($code, $email));
            }
        } elseif ($subRoute === '/toggle-status') {
            $id     = intval($data['id'] ?? 0);
            $status = ($data['status'] === 'active') ? 'active' : 'inactive';
            $db2    = (new Database())->getConnection();
            $stmt   = $db2->prepare("UPDATE gift_cards SET status = ? WHERE id = ?");
            if ($stmt->execute([$status, $id])) {
                echo json_encode(["success" => true]);
            } else {
                echo json_encode(["success" => false, "message" => "Failed."]);
            }
        } else {
            http_response_code(404);
            echo json_encode(["success" => false, "message" => "Route not found."]);
        }
    } else {
        http_response_code(405);
        echo json_encode(["success" => false, "message" => "Method not allowed."]);
    }
    $matched = true;

} elseif ($route === '/newsletter-subscribers' && $method === 'GET') {
    $newsletterCtrl = new NewsletterController();
    $response = [
        "success" => true,
        "data" => $newsletterCtrl->getSubscribers()
    ];
    $matched = true;

} elseif (strpos($route, '/page-reviews') === 0) {
    $ctrl = new ReviewController();
    $pathParts = explode('/', trim($route, '/'));
    $ctrl->handleRequest($method, $pathParts);
    $matched = true;

} elseif ($route === '/admin/stats' && $method === 'GET') {
    $db = (new Database())->getConnection();
    
    // Total Sales
    $salesStmt = $db->query("SELECT SUM(total_amount) as total FROM orders WHERE payment_status = 'paid'");
    $sales = $salesStmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    
    // Total NXL Issued (total of all user credits + merchant nxl_balance)
    $nxlUserStmt = $db->query("SELECT SUM(credits) as total FROM users WHERE role = 'USER'");
    $nxlMerchantStmt = $db->query("SELECT SUM(nxl_balance) as total FROM merchants");
    $nxlTotal = ($nxlUserStmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0) + ($nxlMerchantStmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);
    
    // Active Customers
    $customersStmt = $db->query("SELECT COUNT(*) as count FROM users WHERE role = 'USER'");
    $customers = $customersStmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
    
    // Active Merchants
    $merchantsStmt = $db->query("SELECT COUNT(*) as count FROM merchants");
    $merchants = $merchantsStmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
    
    $response = [
        "success" => true,
        "sales" => $sales,
        "nxl" => $nxlTotal,
        "customers" => $customers,
        "merchants" => $merchants
    ];
    $matched = true;
}

// Delegate Registration
if ($method === 'POST' && $route === 'delegate/register') {
    $response = DelegateController::registerDelegate($_POST, $_FILES);
    $matched = true;
}

// Output response

if (!$matched) {
    http_response_code(404);
    echo json_encode([
        "success" => false,
        "message" => "API Route not found: [$method] $route"
    ]);
} else if ($response !== null) {
    echo json_encode($response);
}
