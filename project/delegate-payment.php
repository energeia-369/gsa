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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay'])) {
    // Process mock payment
    $stmt = $db->prepare("UPDATE delegates SET payment_status = 'Paid' WHERE delegate_id = ?");
    $stmt->execute([$delegate_id]);
    
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
    $evtStmt = $db->prepare("SELECT title, delegate_fee, delegate_currency FROM events WHERE id = ?");
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
        </div>

        <form method="POST">
            <button type="submit" name="pay" class="delegate-btn" style="width: 100%; font-size: 1.2rem; padding: 1rem;">
                <i class="fa-solid fa-credit-card"></i> Pay Now (Simulated)
            </button>
        </form>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
