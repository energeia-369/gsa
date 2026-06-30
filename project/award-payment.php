<?php
session_start();
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/config/Config.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['pass_type'])) {
    header("Location: award-registration.php");
    exit;
}

// Handle File Upload
$idProofFile = '';
if (isset($_FILES['id_proof_file']) && $_FILES['id_proof_file']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . '/assets/uploads/id_proofs/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    $ext = pathinfo($_FILES['id_proof_file']['name'], PATHINFO_EXTENSION);
    $filename = time() . '_' . uniqid() . '.' . $ext;
    if (move_uploaded_file($_FILES['id_proof_file']['tmp_name'], $uploadDir . $filename)) {
        $idProofFile = 'assets/uploads/id_proofs/' . $filename;
    }
}

// Calculate pricing securely
$baseAmount = floatval($_POST['base_amount']);
$couponCode = trim($_POST['coupon_code'] ?? '');
$userEmail = trim($_POST['user_id'] ?? '');

// 1. Fetch User Data (using email since site uses localstorage)
require_once __DIR__ . '/models/User.php';
$userModel = new User();
$userData = null;

if (!empty($userEmail)) {
    // We need to fetch by email. Let's do it manually if model doesn't have it.
    $db = Database::getConnection();
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$userEmail]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);
}

$membershipTier = $userData ? ($userData['membership_tier'] ?? 'none') : 'none';
$nxlCoins = $userData ? ($userData['credits'] ?? 0) : 0;

$productPrice = $baseAmount;
$couponDiscount = 0;

// 2. Coupon Discount
if ($couponCode === 'GLOBAL10') {
    $couponDiscount = floor($productPrice * 0.10);
} elseif ($couponCode === 'NXL100') {
    $couponDiscount = 100;
} else {
    $couponCode = '';
}
$productPrice -= $couponDiscount;

// 3. Premium Discount
$premiumDiscountPercent = 0;
if ($membershipTier === 'standard') $premiumDiscountPercent = 0.05;
elseif ($membershipTier === 'premium') $premiumDiscountPercent = 0.10;
elseif ($membershipTier === 'elite') $premiumDiscountPercent = 0.15;

$premiumDiscountAmount = 0;
if ($premiumDiscountPercent > 0) {
    $premiumDiscountAmount = $productPrice * $premiumDiscountPercent;
    $productPrice -= $premiumDiscountAmount;
}

if ($productPrice < 0) $productPrice = 0;

$totalDiscount = $couponDiscount + $premiumDiscountAmount;

// 4. GST
$gstAmount = $productPrice * 0.18;
$amountAfterGst = $productPrice + $gstAmount;

// 5. NXL Credits Redemption
$redeemedNxl = 0;
// We trust the frontend on how much to redeem, BUT we cap it to max available and max order value
$requestedRedeem = intval($_POST['nxl_redeemed'] ?? 0);
if ($requestedRedeem > 0 && $nxlCoins > 0) {
    $redeemedNxl = min($requestedRedeem, $nxlCoins, floor($amountAfterGst));
}

$finalAmount = $amountAfterGst - $redeemedNxl;
$amountInPaise = intval(round($finalAmount * 100));

// Store form data in session
$_SESSION['temp_award_registration'] = [
    'user_id' => $userData ? $userData['id'] : null,
    'full_name' => $_POST['full_name'],
    'email' => $_POST['email'],
    'mobile' => $_POST['mobile'],
    'gender' => $_POST['gender'],
    'dob' => $_POST['dob'],
    'age' => $_POST['age'],
    'city' => $_POST['city'],
    'state' => $_POST['state'],
    'country' => $_POST['country'],
    'pincode' => $_POST['pincode'],
    'occupation' => $_POST['occupation'],
    'company_name' => $_POST['company_name'] ?? '',
    'emergency_contact' => $_POST['emergency_contact'],
    'emergency_phone' => $_POST['emergency_phone'],
    'id_proof_type' => $_POST['id_proof_type'],
    'id_proof_file' => $idProofFile,
    'pass_type' => $_POST['pass_type'],
    'food_type' => $_POST['food_type'],
    'accommodation_required' => $_POST['accommodation_required'],
    'transport_required' => $_POST['transport_required'],
    'special_assistance' => $_POST['special_assistance'],
    'medical_info' => $_POST['medical_info'] ?? '',
    'food_allergies' => $_POST['food_allergies'] ?? '',
    'remarks' => $_POST['remarks'] ?? '',
    'base_amount' => $baseAmount,
    'gst_amount' => $gstAmount,
    'discount_amount' => $totalDiscount,
    'coupon_code' => $couponCode,
    'nxl_redeemed' => $redeemedNxl,
    'final_amount' => $finalAmount
];

if ($amountInPaise <= 0) {
    // Bypass Razorpay completely for free checkouts
    $razorpayOrderId = "free_order_" . time();
    ?>
    <form id="freeCheckoutForm" action="verify-award-payment.php" method="POST">
        <input type="hidden" name="razorpay_payment_id" value="free_checkout">
        <input type="hidden" name="razorpay_order_id" value="<?php echo $razorpayOrderId; ?>">
        <input type="hidden" name="razorpay_signature" value="free_checkout_signature">
    </form>
    <script>
        document.getElementById('freeCheckoutForm').submit();
    </script>
    <?php
    exit;
}

// Generate Razorpay Order locally using cURL
$ch = curl_init('https://api.razorpay.com/v1/orders');
curl_setopt($ch, CURLOPT_USERPWD, RAZORPAY_KEY_ID . ':' . RAZORPAY_KEY_SECRET);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'amount' => $amountInPaise,
    'currency' => 'INR',
    'receipt' => 'rcpt_' . time()
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$response = curl_exec($ch);
curl_close($ch);

$orderData = json_decode($response, true);
$razorpayOrderId = $orderData['id'] ?? null;

if (!$razorpayOrderId) {
    die("Failed to generate Razorpay Order. Please try again.");
}

$pageTitle = "Complete Payment | Award Ceremony";
require_once __DIR__ . '/includes/header.php';
?>

<div class="flex items-center justify-center min-h-screen px-4" style="background: #0b0c10; color: #fff;">
    <div style="background: #12131c; padding: 40px; border-radius: 12px; border: 1px solid rgba(197,168,92,0.3); text-align: center; max-width: 500px; width: 100%; box-sizing: border-box;">
        <h2 style="color: #c5a85c; margin-bottom: 20px;">Complete Your Payment</h2>
        <p style="color: #9aa0b4; margin-bottom: 30px;">You are about to pay <strong>₹<?php echo number_format($finalAmount, 2); ?></strong> for the <strong><?php echo htmlspecialchars($_POST['pass_type']); ?></strong>.</p>
        
        <form id="razorpayForm" action="verify-award-payment.php" method="POST">
            <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
            <input type="hidden" name="razorpay_order_id" id="razorpay_order_id" value="<?php echo $razorpayOrderId; ?>">
            <input type="hidden" name="razorpay_signature" id="razorpay_signature">
        </form>

        <button id="rzp-button1" style="background: linear-gradient(135deg, #c5a85c 0%, #8c7237 100%); color: #0b0c10; border: none; padding: 15px 30px; font-size: 1.2rem; font-weight: bold; border-radius: 8px; cursor: pointer; width: 100%;">
            Pay ₹<?php echo number_format($finalAmount, 2); ?>
        </button>
    </div>
</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
var options = {
    "key": "<?php echo RAZORPAY_KEY_ID; ?>",
    "amount": "<?php echo $amountInPaise; ?>",
    "currency": "INR",
    "name": "GLOBAL SPORTS ARENA",
    "description": "Award Ceremony & Gala Dinner Registration",
    "image": "assets/images/logo.png",
    "order_id": "<?php echo $razorpayOrderId; ?>",
    "handler": function (response){
        document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
        document.getElementById('razorpay_signature').value = response.razorpay_signature;
        document.getElementById('razorpayForm').submit();
    },
    "prefill": {
        "name": "<?php echo htmlspecialchars($_POST['full_name']); ?>",
        "email": "<?php echo htmlspecialchars($_POST['email']); ?>",
        "contact": "<?php echo htmlspecialchars($_POST['mobile']); ?>"
    },
    "theme": {
        "color": "#c5a85c"
    }
};
var rzp1 = new Razorpay(options);
document.getElementById('rzp-button1').onclick = function(e){
    rzp1.open();
    e.preventDefault();
}

// Auto-open Razorpay
window.onload = function() {
    rzp1.open();
};
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
