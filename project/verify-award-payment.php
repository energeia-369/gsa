<?php
session_start();
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/config/Config.php';

$db = Database::getConnection();

// Get data
$razorpayPaymentId = $_POST['razorpay_payment_id'] ?? '';
$razorpayOrderId   = $_POST['razorpay_order_id'] ?? '';
$razorpaySignature = $_POST['razorpay_signature'] ?? '';
$regData           = $_SESSION['temp_award_registration'] ?? null;

if (!$regData || !$razorpayPaymentId) {
    header("Location: award-registration.php?error=invalid");
    exit;
}

// Verify Razorpay Signature
if ($razorpayPaymentId === 'free_checkout') {
    $paymentStatus = 'Paid';
} else {
    $generatedSig = hash_hmac('sha256', $razorpayOrderId . '|' . $razorpayPaymentId, RAZORPAY_KEY_SECRET);
    $paymentStatus = hash_equals($generatedSig, $razorpaySignature) ? 'Paid' : 'Failed';
}

// Generate unique IDs
function generateRegistrationNo($db) {
    do {
        $no = 'GSA-AWARD-2026-' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
        $stmt = $db->prepare("SELECT id FROM award_registrations WHERE registration_no = ?");
        $stmt->execute([$no]);
    } while ($stmt->fetch());
    return $no;
}
function generatePassNo($db) {
    do {
        $no = 'GSA-PASS-' . strtoupper(substr(md5(uniqid()), 0, 10));
        $stmt = $db->prepare("SELECT id FROM award_registrations WHERE pass_no = ?");
        $stmt->execute([$no]);
    } while ($stmt->fetch());
    return $no;
}

$registrationNo = generateRegistrationNo($db);
$passNo         = generatePassNo($db);

// Generate QR Code only on successful payment
$qrPath = '';
if ($paymentStatus === 'Paid') {
    $qrDir = __DIR__ . '/assets/uploads/qr_codes/';
    if (!is_dir($qrDir)) mkdir($qrDir, 0755, true);
    
    $verifyUrl  = 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/Mithraa_E_Project/project/verify-award-pass.php?pass=' . urlencode($passNo);
    $qrFile     = $qrDir . $passNo . '.png';
    $qrLibPath  = __DIR__ . '/includes/phpqrcode/phpqrcode-master/phpqrcode.php';
    
    if (file_exists($qrLibPath) && function_exists('imagecreate')) {
        require_once $qrLibPath;
        QRcode::png($verifyUrl, $qrFile, QR_ECLEVEL_H, 8);
        $qrPath = 'assets/uploads/qr_codes/' . $passNo . '.png';
    }
}

// Insert into DB
$stmt = $db->prepare("INSERT INTO award_registrations 
    (user_id, registration_no, pass_no, full_name, email, mobile, gender, dob, age, city, state, country, pincode, occupation, company_name,
     emergency_contact, emergency_phone, id_proof_type, id_proof_file, pass_type, food_type, accommodation_required, transport_required,
     special_assistance, medical_info, food_allergies, remarks, base_amount, gst_amount, discount_amount, coupon_code, nxl_redeemed, final_amount,
     payment_status, razorpay_order_id, razorpay_payment_id, razorpay_signature, qr_code, entry_status, created_at)
    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?, NOW())");

$stmt->execute([
    $regData['user_id'] ?? null,
    $registrationNo, $passNo,
    $regData['full_name'], $regData['email'], $regData['mobile'], $regData['gender'],
    $regData['dob'], $regData['age'], $regData['city'], $regData['state'],
    $regData['country'], $regData['pincode'], $regData['occupation'], $regData['company_name'],
    $regData['emergency_contact'], $regData['emergency_phone'],
    $regData['id_proof_type'], $regData['id_proof_file'], $regData['pass_type'],
    $regData['food_type'], $regData['accommodation_required'], $regData['transport_required'],
    $regData['special_assistance'], $regData['medical_info'], $regData['food_allergies'], $regData['remarks'],
    $regData['base_amount'], $regData['gst_amount'], $regData['discount_amount'], $regData['coupon_code'] ?? null, $regData['nxl_redeemed'] ?? 0, $regData['final_amount'],
    $paymentStatus, $razorpayOrderId, $razorpayPaymentId, $razorpaySignature, $qrPath, 'Not Checked In'
]);

$registrationId = $db->lastInsertId();

// Handle NXL Credits Deduction and Earning for users
if ($paymentStatus === 'Paid' && !empty($regData['user_id'])) {
    $userId = $regData['user_id'];
    
    // Deduct redeemed NXL credits (and add to master admin)
    if (($regData['nxl_redeemed'] ?? 0) > 0) {
        $deductStmt = $db->prepare("UPDATE users SET credits = credits - ? WHERE id = ?");
        $deductStmt->execute([$regData['nxl_redeemed'], $userId]);
        
        $db->prepare("UPDATE users SET credits = credits + ?, wallet_balance = wallet_balance + ? WHERE id = 1")
           ->execute([$regData['nxl_redeemed'], $regData['nxl_redeemed']]);
        $db->prepare("UPDATE nxl_wallets SET balance = balance + ? WHERE user_id = 1")
           ->execute([$regData['nxl_redeemed']]);
    }
    
    // Calculate and add earned NXL credits (5% of discounted price for premium users)
    // We need to fetch membership tier first
    $userStmt = $db->prepare("SELECT membership_tier FROM users WHERE id = ?");
    $userStmt->execute([$userId]);
    $uRow = $userStmt->fetch(PDO::FETCH_ASSOC);
    if ($uRow && $uRow['membership_tier'] !== 'none') {
        $cashbackRate = (float)Settings::get('nxl_cashback_percentage', 0.05);
        $earnedNxlCoins = floor(($regData['base_amount'] - $regData['discount_amount']) * $cashbackRate);
        if ($earnedNxlCoins > 0) {
            $addStmt = $db->prepare("UPDATE users SET credits = credits + ? WHERE id = ?");
            $addStmt->execute([$earnedNxlCoins, $userId]);
            
            // Deduct earned NXL credits from master admin
            $db->prepare("UPDATE users SET credits = credits - ?, wallet_balance = wallet_balance - ? WHERE id = 1")
               ->execute([$earnedNxlCoins, $earnedNxlCoins]);
            $db->prepare("UPDATE nxl_wallets SET balance = balance - ? WHERE user_id = 1")
               ->execute([$earnedNxlCoins]);
        }
    }
}

// Clear session temp data
unset($_SESSION['temp_award_registration']);

// Send confirmation email (basic)
if ($paymentStatus === 'Paid') {
    $to      = $regData['email'];
    $subject = "🎉 GSA Award Ceremony - Your Gala Pass is Confirmed!";
    $body    = "Dear {$regData['full_name']},\n\nCongratulations! Your Gala Pass has been successfully booked.\n\nRegistration No: $registrationNo\nPass No: $passNo\nPass Type: {$regData['pass_type']}\nAmount Paid: ₹{$regData['final_amount']}\n\nVenue: The Orchid Hotel Pune\nDate: 13 October 2026\nTime: 7:00 PM – 11:00 PM\n\nPlease carry your QR Pass for hotel entry.\n\nDress Code: Formal / Black Tie\nContact: support@globalsportsarena.in\n\nWarm Regards,\nGlobal Sports Arena Team";
    $headers = "From: noreply@globalsportsarena.in";
    @mail($to, $subject, $body, $headers);
}

// Log entry
$logStmt = $db->prepare("INSERT INTO award_entry_logs (registration_id, pass_no, scan_status, remarks) VALUES (?,?,?,?)");
$logStmt->execute([$registrationId, $passNo, 'Registered', 'Registration completed - Payment: ' . $paymentStatus]);

// Redirect based on status
if ($paymentStatus === 'Paid') {
    header("Location: award-success.php?reg=" . urlencode($registrationNo));
} else {
    header("Location: award-registration.php?error=payment_failed&order=" . $razorpayOrderId);
}
exit;
