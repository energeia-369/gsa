<?php
$pageTitle = "GLOBAL SPORTS ARENA | Visitor Pass Registration";
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$successMsg = "";
$errorMsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $db = Database::getConnection();
        
        // Fetch the event end_date from tournaments
        $eventName = $_POST['event'] ?? '';
        $eventDate = null;
        if ($eventName) {
            $evtStmt = $db->prepare("SELECT end_date FROM tournaments WHERE name = ? LIMIT 1");
            $evtStmt->execute([$eventName]);
            $evtRow = $evtStmt->fetch(PDO::FETCH_ASSOC);
            if ($evtRow && $evtRow['end_date']) {
                $eventDate = $evtRow['end_date'];
            }
        }
        
        $stmt = $db->prepare("INSERT INTO visitor_passes (full_name, email, phone, country, city, company, designation, event, event_date, razorpay_payment_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['full_name'] ?? '',
            $_POST['email'] ?? '',
            $_POST['phone'] ?? '',
            $_POST['country'] ?? '',
            $_POST['city'] ?? '',
            $_POST['company'] ?? '',
            $_POST['designation'] ?? '',
            $eventName,
            $eventDate,
            $_POST['razorpay_payment_id'] ?? null
        ]);
        $insertId = $db->lastInsertId();
        $successMsg = "Visitor pass registration successful! We look forward to seeing you.";
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        $qrUrl = $protocol . $_SERVER['HTTP_HOST'] . "/Mithraa_E_Project/project/verify-pass.php?type=visitor&id=" . $insertId;

    } catch (Exception $e) {
        $errorMsg = "An error occurred. Please try again.";
    }
}

// Fetch dynamic tournaments for the dropdown
try {
    $db = Database::getConnection();
    $tStmt = $db->query("SELECT name, sport FROM tournaments ORDER BY id DESC");
    $tournamentsList = $tStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch visitor pass fee
    $fStmt = $db->query("SELECT setting_value FROM system_settings WHERE setting_key = 'visitor_pass_fee'");
    $feeRow = $fStmt->fetch(PDO::FETCH_ASSOC);
    $visitorFee = $feeRow ? floatval($feeRow['setting_value']) : 0;
} catch (Exception $e) {
    $tournamentsList = [];
    $visitorFee = 0;
}
?>

<link rel="stylesheet" href="assets/css/visitor-pass.css?v=2">

<div class="page-wrapper visitor-page">
    <div class="form-container w-full max-w-2xl mx-auto px-4">
        <div class="form-header">
            <h1>Visitor Pass Registration</h1>
            <p>Register as a visitor and access global events, exhibitions, keynote sessions, and networking opportunities.</p>
        </div>
        
        <?php if ($successMsg): ?>
            <div style="background: rgba(46, 125, 50, 0.2); border: 1px solid #2e7d32; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
                <div style="margin-bottom: 15px; font-weight: bold;"><?php echo $successMsg; ?></div>
                <?php if (isset($qrUrl)): ?>
                    <p style="font-size: 0.9rem; margin-bottom: 10px;">Please save this QR Code for event check-in:</p>
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?php echo urlencode($qrUrl); ?>" alt="QR Code" style="background: #fff; padding: 10px; border-radius: 8px;">
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($errorMsg): ?>
            <div style="background: rgba(198, 40, 40, 0.2); border: 1px solid #c62828; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
                <?php echo $errorMsg; ?>
            </div>
        <?php endif; ?>
        
        <form id="visitorForm" action="visitor-pass.php" method="POST">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" class="form-control" name="full_name" placeholder="Enter your full name" required>
            </div>
            
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" class="form-control" name="email" placeholder="Enter your email" required>
            </div>
            
            <div class="form-group">
                <label>Phone Number</label>
                <input type="tel" class="form-control" name="phone" placeholder="Enter your phone number" required>
            </div>
            
            <div class="form-group">
                <label>Country</label>
                <input type="text" class="form-control" name="country" placeholder="Enter your country" required>
            </div>
            
            <div class="form-group">
                <label>City</label>
                <input type="text" class="form-control" name="city" placeholder="Enter your city" required>
            </div>
            
            <div class="form-group">
                <label>Company / Organization (Optional)</label>
                <input type="text" class="form-control" name="company" placeholder="Enter your company name">
            </div>
            
            <div class="form-group">
                <label>Designation (Optional)</label>
                <input type="text" class="form-control" name="designation" placeholder="Enter your designation">
            </div>
            
            <div class="form-group">
                <label>Select Event</label>
                <select class="form-control" name="event" required>
                    <option value="" disabled selected>-- Select an Event --</option>
                    <?php foreach ($tournamentsList as $t): ?>
                        <option value="<?php echo htmlspecialchars($t['name']); ?>">
                            <?php echo htmlspecialchars($t['name']) . ($t['sport'] ? ' (' . htmlspecialchars($t['sport']) . ')' : ''); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <?php if ($visitorFee > 0): ?>
            <div class="form-group">
                <label>Registration Fee</label>
                <input type="text" class="form-control" value="₹<?php echo number_format($visitorFee); ?>" style="background: rgba(197, 168, 92, 0.1); color: #c5a85c; font-weight: bold; border-color: rgba(197, 168, 92, 0.3);" disabled>
            </div>
            <?php endif; ?>
            
            <button type="submit" id="submitVisitorBtn" class="submit-btn">
                <?php echo $visitorFee > 0 ? "Register & Pay ₹" . number_format($visitorFee) . " →" : "Register for Visitor Pass"; ?>
            </button>
        </form>
    </div>
</div>


<script>
const visitorFee = <?php echo $visitorFee; ?>;

document.getElementById("visitorForm").addEventListener("submit", async function(e) {
    if (visitorFee > 0) {
        e.preventDefault();
        
        const form = this;
        const submitBtn = document.getElementById("submitVisitorBtn");
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.innerHTML = "Processing...";
        submitBtn.disabled = true;

        try {
            // 1. Fetch Razorpay Order ID from backend
            const amountInPaise = visitorFee * 100;
            const orderRes = await fetch("api/index.php/public-payment/create-razorpay-order", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ amount: visitorFee })
            });

            if (!orderRes.ok) {
                const errData = await orderRes.json();
                throw new Error(errData.error || "Failed to create order");
            }
            const orderData = await orderRes.json();

            // 2. Initialize Razorpay
            var options = {
                key: "<?php echo defined('RAZORPAY_KEY_ID') ? RAZORPAY_KEY_ID : 'rzp_test_YourKeyIdHere'; ?>", // Fallback if RAZORPAY_KEY_ID isn't defined here
                amount: amountInPaise,
                currency: "INR",
                name: "ENERGEIA'S Global Ventures",
                description: "Visitor Pass Registration",
                order_id: orderData.id,
                handler: function (response) {
                    // Inject payment ID and submit
                    const paymentId = response.razorpay_payment_id;
                    const hiddenInput = document.createElement("input");
                    hiddenInput.type = "hidden";
                    hiddenInput.name = "razorpay_payment_id";
                    hiddenInput.value = paymentId;
                    form.appendChild(hiddenInput);
                    
                    form.submit();
                },
                theme: { color: "#c5a85c" }
            };

            if (!window.Razorpay) {
                throw new Error("Razorpay SDK could not be loaded. Please check your internet connection.");
            }
            const rzp = new window.Razorpay(options);
            
            rzp.on('payment.failed', function (response){
                alert("Payment Failed: " + response.error.description);
                submitBtn.innerHTML = originalBtnText;
                submitBtn.disabled = false;
            });

            rzp.open();
        } catch (error) {
            console.error(error);
            alert("Error initializing payment: " + error.message);
            submitBtn.innerHTML = originalBtnText;
            submitBtn.disabled = false;
        }
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
