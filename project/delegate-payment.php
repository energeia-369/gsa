<?php
$pageTitle = "Delegate Payment";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
require_once __DIR__ . '/config/Database.php';

$delegate_id = $_GET['id'] ?? '';
if (empty($delegate_id)) {
    echo "<div style='text-align:center; padding: 5rem;'><h2>Invalid Delegate ID.</h2></div>";
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$db = Database::getConnection();
$stmt = $db->prepare("SELECT * FROM delegates WHERE delegate_id = ?");
$stmt->execute([$delegate_id]);
$delegate = $stmt->fetch();

if (!$delegate) {
    echo "<div style='text-align:center; padding: 5rem;'><h2>Delegate not found.</h2></div>";
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['razorpay_payment_id'])) {
    $paymentId = $_POST['razorpay_payment_id'];
    $orderId = $_POST['razorpay_order_id'] ?? '';
    $signature = $_POST['razorpay_signature'] ?? '';

    $generatedSignature = hash_hmac('sha256', $orderId . '|' . $paymentId, RAZORPAY_KEY_SECRET);
    if ($generatedSignature !== $signature) {
        die("Invalid payment signature.");
    }
    
    if ($delegate['payment_status'] !== 'Paid') {
        $stmt = $db->prepare("UPDATE delegates SET payment_status = 'Paid', razorpay_payment_id = ? WHERE delegate_id = ?");
        $stmt->execute([$paymentId, $delegate_id]);
        
        // Calculate NXL Credits (5% of fee)
        $stmtFee = $db->query("SELECT setting_value FROM delegate_settings WHERE setting_key = 'registration_fee'");
        $feeAmt = $stmtFee->fetchColumn() ?: '150.00';
        
        if (!empty($delegate['event_id'])) {
            $evtStmt = $db->prepare("SELECT delegate_fee FROM home_carousel_events WHERE id = ?");
            $evtStmt->execute([$delegate['event_id']]);
            $evt = $evtStmt->fetch();
            if ($evt && !empty($evt['delegate_fee']) && $evt['delegate_fee'] > 0) {
                $feeAmt = $evt['delegate_fee'];
            }
        }
        $nxlCredits = round($feeAmt * 0.05);

        if ($nxlCredits > 0) {
            // Find user by email
            $userStmt = $db->prepare("SELECT id FROM users WHERE email = ?");
            $userStmt->execute([$delegate['email']]);
            $user = $userStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                $userId = $user['id'];
                
                // Add credits to users table
                $db->prepare("UPDATE users SET credits = credits + ?, wallet_balance = wallet_balance + ? WHERE id = ?")
                   ->execute([$nxlCredits, $nxlCredits, $userId]);
                   
                // Add credits to nxl_wallets table
                $walletStmt = $db->prepare("SELECT id FROM nxl_wallets WHERE user_id = ?");
                $walletStmt->execute([$userId]);
                if ($walletStmt->fetch()) {
                    $db->prepare("UPDATE nxl_wallets SET balance = balance + ? WHERE user_id = ?")
                       ->execute([$nxlCredits, $userId]);
                } else {
                    $db->prepare("INSERT INTO nxl_wallets (user_id, balance) VALUES (?, ?)")
                       ->execute([$userId, $nxlCredits]);
                }
                
                // Log transaction
                $db->prepare("INSERT INTO nxl_transactions (user_id, type, amount, description, ref_id) VALUES (?, 'EARNED', ?, 'Delegate Registration Cashback', ?)")
                   ->execute([$userId, $nxlCredits, $paymentId]);
            }
        }
    }
    
    // Redirect to success
    echo "<script>window.location.href='delegate-success.php?id=" . $delegate_id . "';</script>";
    exit;
}

// Fetch global fallback settings first
$stmt = $db->query("SELECT setting_value FROM delegate_settings WHERE setting_key = 'registration_fee'");
$fee = $stmt->fetchColumn() ?: '150.00';

$stmt = $db->query("SELECT setting_value FROM delegate_settings WHERE setting_key = 'currency'");
$currency = $stmt->fetchColumn() ?: 'USD';

$eventTitle = "";

// If delegate selected a specific event, override fee with event-specific fee (if set)
if (!empty($delegate['event_id'])) {
    $evtStmt = $db->prepare("SELECT title, delegate_fee, delegate_currency FROM home_carousel_events WHERE id = ?");
    $evtStmt->execute([$delegate['event_id']]);
    $evt = $evtStmt->fetch();
    if ($evt) {
        $eventTitle = $evt['title'];
        if (!empty($evt['delegate_fee']) && $evt['delegate_fee'] > 0) {
            $fee = $evt['delegate_fee'];
            $currency = $evt['delegate_currency'] ?: $currency;
        }
    }
}

// Auto-convert foreign currency to INR based on today's rate
$inrAmount = $fee;
if (strtoupper($currency) !== 'INR') {
    $api_url = "https://open.er-api.com/v6/latest/" . strtoupper($currency);
    $response = @file_get_contents($api_url);
    if ($response !== false) {
        $data = json_decode($response, true);
        if (isset($data['rates']['INR'])) {
            $inrAmount = $fee * $data['rates']['INR'];
        } else {
            $inrAmount = $fee * 83.5; // static fallback
        }
    } else {
        $inrAmount = $fee * 83.5; // static fallback
    }
}
$inrAmount = ceil($inrAmount);

?>

<link rel="stylesheet" href="assets/css/delegate.css?v=1">

<section class="delegate-section" style="background-color: transparent; min-height: 70vh; display: flex; align-items: center; justify-content: center;">
    <div class="delegate-form-container mx-auto" style="max-width: 600px; text-align: center;">
        <h2 class="section-title" style="margin-bottom: 1rem;">Complete Your Payment</h2>
        <p style="color: #666; margin-bottom: <?= $eventTitle ? '0.5rem' : '2rem' ?>;">Delegate: <strong><?php echo htmlspecialchars($delegate['full_name']); ?></strong> (<?php echo htmlspecialchars($delegate_id); ?>)</p>
        <?php if ($eventTitle): ?>
            <p style="color: #c5a85c; font-weight: bold; margin-bottom: 2rem;">Event: <?php echo htmlspecialchars($eventTitle); ?></p>
        <?php endif; ?>
        
        <div style="background: #f9f9fa; padding: 2rem; border-radius: 8px; margin-bottom: 2rem; border: 1px solid #ddd;">
            <h3 style="font-size: 1.5rem; color: #12131c; margin-bottom: 1rem;">Registration Fee</h3>
            <div style="font-size: 2.5rem; font-weight: 800; color: #c5a85c;">
                <?php echo htmlspecialchars($currency . ' ' . $fee); ?>
            </div>
            <?php if (strtoupper($currency) !== 'INR'): ?>
                <div style="font-size: 1rem; color: #777; margin-top: 5px;">
                    approx. ₹<?php echo number_format($inrAmount); ?> (Today's Rate)
                </div>
            <?php endif; ?>
        </div>

        <form method="POST" id="delegatePaymentForm">
            <button type="submit" id="payBtn" class="delegate-btn" style="width: 100%; font-size: 1.2rem; padding: 1rem;">
                <i class="fa-solid fa-credit-card"></i> Pay Now
            </button>
        </form>


        <script>
        document.getElementById("delegatePaymentForm").addEventListener("submit", async function(e) {
            e.preventDefault();
            
            const form = this;
            const submitBtn = document.getElementById("payBtn");
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.innerHTML = "Processing...";
            submitBtn.disabled = true;

            try {
                // Fetch Razorpay Order ID
                const amountInInr = <?php echo $inrAmount; ?>;
                const amountInPaise = amountInInr * 100;
                const orderRes = await fetch("api/index.php/public-payment/create-razorpay-order", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ amount: amountInInr })
                });

                if (!orderRes.ok) {
                    const errText = await orderRes.text();
                    throw new Error("Failed to create order: " + errText);
                }
                const orderData = await orderRes.json();

                // Initialize Razorpay
                var options = {
                    key: "<?php echo defined('RAZORPAY_KEY_ID') ? RAZORPAY_KEY_ID : 'rzp_test_YourKeyIdHere'; ?>",
                    amount: amountInPaise,
                    currency: "INR",
                    name: "ENERGEIA'S Global Ventures",
                    description: "Delegate Registration Payment",
                    order_id: orderData.id,
                    prefill: {
                        name: "<?php echo addslashes(htmlspecialchars($delegate['full_name'])); ?>" || "Guest",
                        email: "<?php echo addslashes(htmlspecialchars($delegate['email'])); ?>" || "guest@example.com",
                        contact: "<?php echo addslashes(htmlspecialchars($delegate['phone'])); ?>" || "9999999999"
                    },
                    handler: function (response) {
                        const hiddenInput = document.createElement("input");
                        hiddenInput.type = "hidden";
                        hiddenInput.name = "razorpay_payment_id";
                        hiddenInput.value = response.razorpay_payment_id;
                        form.appendChild(hiddenInput);
                        
                        const hiddenOrderId = document.createElement("input");
                        hiddenOrderId.type = "hidden";
                        hiddenOrderId.name = "razorpay_order_id";
                        hiddenOrderId.value = response.razorpay_order_id;
                        form.appendChild(hiddenOrderId);
                        
                        const hiddenSignature = document.createElement("input");
                        hiddenSignature.type = "hidden";
                        hiddenSignature.name = "razorpay_signature";
                        hiddenSignature.value = response.razorpay_signature;
                        form.appendChild(hiddenSignature);
                        
                        form.submit();
                    },
                    theme: { color: "#c5a85c" }
                };

                const rzp = new window.Razorpay(options);
                rzp.on('payment.failed', function (response){
                    console.error("Razorpay Error Code:", response.error.code);
                    console.error("Razorpay Error Description:", response.error.description);
                    console.error("Razorpay Error Source:", response.error.source);
                    console.error("Razorpay Error Step:", response.error.step);
                    console.error("Razorpay Error Reason:", response.error.reason);
                    console.error("Razorpay Error Metadata Order ID:", response.error.metadata.order_id);
                    console.error("Razorpay Error Metadata Payment ID:", response.error.metadata.payment_id);
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
        });
        </script>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
