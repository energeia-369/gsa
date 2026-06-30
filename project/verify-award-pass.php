<?php
$pageTitle = "Verify Award Pass | GSA";
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/includes/header.php';

$db = Database::getConnection();
$passNo = $_GET['pass'] ?? '';
$action = $_POST['action'] ?? '';
$postPassNo = $_POST['pass_no'] ?? '';

$result = null;
$message = '';
$messageType = '';

// Handle check-in action
if ($action === 'checkin' && $postPassNo) {
    $stmt = $db->prepare("SELECT * FROM award_registrations WHERE pass_no = ?");
    $stmt->execute([$postPassNo]);
    $reg = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($reg && $reg['payment_status'] === 'Paid' && $reg['entry_status'] === 'Not Checked In') {
        $upd = $db->prepare("UPDATE award_registrations SET entry_status='Checked In', checked_in_at=NOW() WHERE pass_no=?");
        $upd->execute([$postPassNo]);

        $logStmt = $db->prepare("INSERT INTO award_entry_logs (registration_id, pass_no, scan_status, remarks) VALUES (?,?,?,?)");
        $logStmt->execute([$reg['id'], $postPassNo, 'Checked In', 'Checked in via security scan']);

        $message = "✅ Entry Marked! " . htmlspecialchars($reg['full_name']) . " is now Checked In.";
        $messageType = 'success';
        $passNo = $postPassNo;
    }
}

// Load pass data
if ($passNo) {
    $stmt = $db->prepare("SELECT * FROM award_registrations WHERE pass_no = ?");
    $stmt->execute([$passNo]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<style>
.verify-page { min-height: 100vh; background: #0b0c10; color: #f5f6fa; padding: 80px 20px; font-family: 'Outfit', sans-serif; }
body.light-theme .verify-page { background: #f4eee1; color: #1a1a1a; }

.verify-card { max-width: 600px; margin: 0 auto; background: #12131c; border-radius: 16px; border: 1px solid rgba(197,168,92,0.3); padding: 35px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
body.light-theme .verify-card { background: #fff; border-color: rgba(197,168,92,0.4); box-shadow: 0 10px 30px rgba(197,168,92,0.15); }

.verify-card h2 { color: #c5a85c; margin: 0 0 25px; text-align: center; font-size: 1.6rem; }
body.light-theme .verify-card h2 { color: #8c6010; }

.status-badge { padding: 20px; border-radius: 10px; text-align: center; margin-bottom: 25px; font-size: 1.5rem; font-weight: bold; }
.status-badge.valid { background: rgba(34,197,94,0.15); border: 2px solid #22c55e; color: #22c55e; }
.status-badge.already { background: rgba(251,191,36,0.15); border: 2px solid #fbbf24; color: #fbbf24; }
.status-badge.invalid { background: rgba(220,38,38,0.15); border: 2px solid #dc2626; color: #dc2626; }

.verify-row { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid rgba(197,168,92,0.1); font-size: 0.9rem; }
.verify-label { color: #9aa0b4; }
body.light-theme .verify-label { color: #4a4a4a; }
.verify-value { font-weight: 700; }

.checkin-btn { width: 100%; margin-top: 25px; padding: 15px; background: linear-gradient(135deg, #22c55e, #16a34a); color: white; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: bold; cursor: pointer; }

.search-form input { width: 100%; padding: 12px 16px; background: #0b0c10; border: 1px solid rgba(197,168,92,0.3); border-radius: 8px; color: #fff; font-size: 1rem; margin-bottom: 12px; box-sizing: border-box; }
body.light-theme .search-form input { background: #fdfbf7; color: #1a1a1a; border-color: #d1c5a9; }
.search-form button { width: 100%; padding: 12px; background: linear-gradient(135deg, #c5a85c, #8c7237); color: #0b0c10; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; font-size: 1rem; }
</style>

<div class="verify-page flex items-center justify-center min-h-screen px-4">
    <div class="verify-card">
        <h2>🔐 Hotel Entry Verification</h2>

        <?php if ($message): ?>
            <div class="status-badge <?php echo $messageType === 'success' ? 'valid' : 'invalid'; ?>"><?php echo $message; ?></div>
        <?php endif; ?>

        <?php if ($result): ?>
            <?php if ($result['payment_status'] !== 'Paid'): ?>
                <div class="status-badge invalid">❌ INVALID PASS — Payment Not Completed</div>
            <?php elseif ($result['entry_status'] === 'Checked In'): ?>
                <div class="status-badge already">⚠️ ALREADY CHECKED IN at <?php echo htmlspecialchars($result['checked_in_at']); ?></div>
            <?php else: ?>
                <div class="status-badge valid">✅ VERIFIED — ENTRY GRANTED</div>
            <?php endif; ?>

            <div class="verify-row"><span class="verify-label">Name</span><span class="verify-value"><?php echo htmlspecialchars($result['full_name']); ?></span></div>
            <div class="verify-row"><span class="verify-label">Pass No.</span><span class="verify-value" style="color:#c5a85c;font-family:monospace;"><?php echo htmlspecialchars($result['pass_no']); ?></span></div>
            <div class="verify-row"><span class="verify-label">Registration No.</span><span class="verify-value"><?php echo htmlspecialchars($result['registration_no']); ?></span></div>
            <div class="verify-row"><span class="verify-label">Pass Type</span><span class="verify-value"><?php echo htmlspecialchars($result['pass_type']); ?></span></div>
            <div class="verify-row"><span class="verify-label">Payment</span><span class="verify-value" style="color:<?php echo $result['payment_status']==='Paid'?'#22c55e':'#dc2626'; ?>"><?php echo htmlspecialchars($result['payment_status']); ?></span></div>
            <div class="verify-row"><span class="verify-label">Entry Status</span><span class="verify-value"><?php echo htmlspecialchars($result['entry_status']); ?></span></div>

            <?php if ($result['payment_status'] === 'Paid' && $result['entry_status'] === 'Not Checked In'): ?>
            <form method="POST">
                <input type="hidden" name="action" value="checkin">
                <input type="hidden" name="pass_no" value="<?php echo htmlspecialchars($result['pass_no']); ?>">
                <button type="submit" class="checkin-btn">✅ Mark as Checked In</button>
            </form>
            <?php endif; ?>

        <?php elseif ($passNo): ?>
            <div class="status-badge invalid">❌ INVALID PASS — Not Found in Database</div>
        <?php else: ?>
            <div class="search-form">
                <p style="color: #9aa0b4; margin-bottom: 15px; text-align: center;">Enter Pass Number or scan QR Code to verify.</p>
                <form method="GET">
                    <input type="text" name="pass" placeholder="e.g. GSA-PASS-XXXXXXXXXX" required>
                    <button type="submit">🔍 Verify Pass</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
