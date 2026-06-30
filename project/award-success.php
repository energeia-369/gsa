<?php
session_start();
$pageTitle = "Booking Confirmed | GSA Award Ceremony";
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$db = Database::getConnection();
$regNo = $_GET['reg'] ?? '';

if (!$regNo) { header("Location: award-registration.php"); exit; }

$stmt = $db->prepare("SELECT * FROM award_registrations WHERE registration_no = ?");
$stmt->execute([$regNo]);
$reg = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reg) { header("Location: award-registration.php"); exit; }
?>
<link rel="stylesheet" href="assets/css/award-registration.css?v=1">
<style>
.success-page { min-height: 100vh; background: #0b0c10; color: #f5f6fa; padding: 60px 20px; font-family: 'Outfit', sans-serif; }
body.light-theme .success-page { background: linear-gradient(135deg, #fdfbf7 0%, #f4eee1 100%); color: #1a1a1a; }

.success-anim { text-align: center; margin-bottom: 40px; }
.success-circle {
    width: 120px; height: 120px; background: rgba(34,197,94,0.15);
    border: 4px solid #22c55e; border-radius: 50%; margin: 0 auto 20px;
    display: flex; align-items: center; justify-content: center;
    font-size: 4rem; animation: popIn 0.6s ease;
}
@keyframes popIn { 0% { transform: scale(0); opacity: 0; } 80% { transform: scale(1.1); } 100% { transform: scale(1); opacity: 1; } }

.success-page h1 { font-size: 2.5rem; font-weight: 800; color: #c5a85c; margin-bottom: 10px; }
body.light-theme .success-page h1 { color: #8c6010; }
.success-page .subtitle { font-size: 1.1rem; color: #9aa0b4; margin-bottom: 40px; }
body.light-theme .success-page .subtitle { color: #4a4a4a; }

.reg-card {
    max-width: 700px; margin: 0 auto;
    background: #12131c; border: 1px solid rgba(197,168,92,0.3);
    border-radius: 16px; padding: 35px; box-shadow: 0 10px 30px rgba(0,0,0,0.5);
}
body.light-theme .reg-card { background: #fff; border: 1px solid rgba(197,168,92,0.4); box-shadow: 0 10px 30px rgba(197,168,92,0.15); }

.reg-row { display: flex; justify-content: space-between; align-items: center; padding: 14px 0; border-bottom: 1px solid rgba(197,168,92,0.1); }
.reg-label { color: #9aa0b4; font-size: 0.9rem; }
body.light-theme .reg-label { color: #4a4a4a; }
.reg-value { font-weight: 700; color: #f5f6fa; }
body.light-theme .reg-value { color: #1a1a1a; }
.reg-value.highlight { color: #c5a85c; font-size: 1.1rem; }
body.light-theme .reg-value.highlight { color: #8c6010; }

.qr-block { text-align: center; margin: 30px 0; }
.qr-block img { max-width: 180px; background: #fff; padding: 10px; border-radius: 10px; }

.action-buttons { display: flex; flex-wrap: wrap; gap: 12px; justify-content: center; margin-top: 30px; }
.btn-primary { padding: 12px 24px; background: linear-gradient(135deg, #c5a85c 0%, #8c7237 100%); color: #0b0c10; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; text-decoration: none; font-size: 0.95rem; display: inline-flex; align-items: center; gap: 8px; }
.btn-outline { padding: 12px 24px; background: transparent; color: #c5a85c; border: 2px solid #c5a85c; border-radius: 8px; font-weight: bold; cursor: pointer; text-decoration: none; font-size: 0.95rem; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s; }
body.light-theme .btn-outline { color: #8c6010; border-color: #8c6010; }
.btn-outline:hover { background: rgba(197,168,92,0.1); }
</style>

<div class="success-page flex flex-col items-center justify-center min-h-screen px-4">
    <div class="success-anim">
        <div class="success-circle">✓</div>
        <h1>Congratulations!</h1>
        <p class="subtitle">Your Gala Pass has been successfully booked. We look forward to seeing you!</p>
    </div>

    <div class="reg-card">
        <div class="reg-row"><span class="reg-label">Registration Number</span><span class="reg-value highlight"><?php echo htmlspecialchars($reg['registration_no']); ?></span></div>
        <div class="reg-row"><span class="reg-label">Pass Number</span><span class="reg-value highlight"><?php echo htmlspecialchars($reg['pass_no']); ?></span></div>
        <div class="reg-row"><span class="reg-label">Name</span><span class="reg-value"><?php echo htmlspecialchars($reg['full_name']); ?></span></div>
        <div class="reg-row"><span class="reg-label">Email</span><span class="reg-value"><?php echo htmlspecialchars($reg['email']); ?></span></div>
        <div class="reg-row"><span class="reg-label">Pass Type</span><span class="reg-value"><?php echo htmlspecialchars($reg['pass_type']); ?></span></div>
        <div class="reg-row"><span class="reg-label">Amount Paid</span><span class="reg-value" style="color: #22c55e;">₹<?php echo number_format($reg['final_amount'], 2); ?></span></div>
        <div class="reg-row"><span class="reg-label">Payment Status</span><span class="reg-value" style="color: #22c55e;">✅ <?php echo $reg['payment_status']; ?></span></div>
        <div class="reg-row"><span class="reg-label">Venue</span><span class="reg-value">The Orchid Hotel Pune</span></div>
        <div class="reg-row"><span class="reg-label">Date</span><span class="reg-value">13 October 2026</span></div>
        <div class="reg-row"><span class="reg-label">Time</span><span class="reg-value">7:00 PM – 11:00 PM</span></div>

        <?php if ($reg['qr_code']): ?>
        <div class="qr-block">
            <p style="color: #9aa0b4; margin-bottom: 12px;">Scan this QR at the venue entrance</p>
            <img src="<?php echo htmlspecialchars($reg['qr_code']); ?>" alt="Entry QR Code">
        </div>
        <?php endif; ?>

        <div class="action-buttons">
            <a href="award-ticket.php?reg=<?php echo urlencode($reg['registration_no']); ?>" class="btn-primary" target="_blank">🎟️ View & Print Pass</a>
            <a href="award-ticket.php?reg=<?php echo urlencode($reg['registration_no']); ?>&print=1" class="btn-outline" target="_blank">🖨️ Print Pass</a>
            <a href="index.php" class="btn-outline">🏠 Go Home</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
