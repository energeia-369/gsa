<?php
$pageTitle = "Registration Successful";
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

// Ensure they paid
if ($delegate['payment_status'] !== 'Paid') {
    echo "<script>window.location.href='delegate-payment.php?id=" . $delegate_id . "';</script>";
    exit;
}

// Generate verification URL for the QR code
$verifyUrl = "http://" . $_SERVER['HTTP_HOST'] . "/Mithraa_E_Project/project/delegate-details.php?id=" . $delegate_id;
$qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($verifyUrl);
?>

<link rel="stylesheet" href="assets/css/delegate.css?v=1">

<section class="delegate-section" style="background-color: transparent; min-height: 70vh;">
    <div class="delegate-form-container mx-auto" style="max-width: 700px; text-align: center; padding: 4rem 2rem;">
        <div style="color: #4ade80; font-size: 4rem; margin-bottom: 1rem;">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <h2 class="section-title" style="margin-bottom: 1rem; color: #12131c;">Registration Successful!</h2>
        <p style="color: #666; font-size: 1.1rem; margin-bottom: 2rem;">
            Thank you, <strong><?php echo htmlspecialchars($delegate['full_name']); ?></strong>. Your delegate registration has been received and payment is successful.
        </p>

        <div style="background: #fff; padding: 2rem; border-radius: 8px; border: 1px solid #ddd; margin-bottom: 2rem; text-align: left;">
            <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center;">
                <div>
                    <p style="margin-bottom: 0.5rem; color: #666;">Delegate ID / Registration No.</p>
                    <h3 style="font-size: 2rem; color: #c5a85c; margin-bottom: 1rem; letter-spacing: 2px;">
                        <?php echo htmlspecialchars($delegate_id); ?>
                    </h3>
                    <p style="margin-bottom: 0.5rem; color: #333;"><strong>Email:</strong> <?php echo htmlspecialchars($delegate['email']); ?></p>
                    <p style="margin-bottom: 0.5rem; color: #333;"><strong>Type:</strong> <?php echo htmlspecialchars($delegate['delegate_type']); ?></p>
                    <p style="margin-bottom: 0.5rem; color: #333;"><strong>Status:</strong> <span style="color: #f59e0b; font-weight: bold;"><?php echo htmlspecialchars($delegate['registration_status']); ?></span> (Awaiting Approval)</p>
                </div>
                <div style="text-align: center; margin-top: 1rem;">
                    <img src="<?php echo $qrUrl; ?>" alt="QR Code" style="border: 4px solid #fff; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-radius: 8px; margin-bottom: 0.5rem;">
                    <p style="font-size: 0.8rem; color: #999;">Scan to Verify</p>
                </div>
            </div>
        </div>

        <div style="display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
            <button onclick="window.print()" class="delegate-btn"><i class="fa-solid fa-print"></i> Print Receipt</button>
            <a href="index.php" class="delegate-btn delegate-btn-outline">Return to Home</a>
        </div>
    </div>
</section>

<!-- Print Styles -->
<style>
@media print {
    body * {
        visibility: hidden;
    }
    .delegate-form-container, .delegate-form-container * {
        visibility: visible;
    }
    .delegate-form-container {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        box-shadow: none;
        padding: 0;
    }
    .delegate-btn {
        display: none !important;
    }
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
