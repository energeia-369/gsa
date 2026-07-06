<?php
$pageTitle = "Gala Pass | GSA Award Ceremony";
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/includes/header.php';

$db = Database::getConnection();
$regNo = $_GET['reg'] ?? '';
if (!$regNo) { echo "Invalid request."; exit; }

$stmt = $db->prepare("SELECT * FROM award_registrations WHERE registration_no = ?");
$stmt->execute([$regNo]);
$reg = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$reg) { echo "Pass not found."; exit; }

$autoPrint = isset($_GET['print']) && $_GET['print'] == '1';
?>
<link rel="stylesheet" href="assets/css/award-ticket.css?v=1">
<style>
body { background: #f4eee1; margin: 0; font-family: 'Inter', sans-serif; }
body.light-theme { background: #f4eee1; }
</style>

<div class="ticket-container w-full max-w-4xl mx-auto px-4 py-10">
    <div class="award-ticket">
        <!-- LEFT SIDE -->
        <div class="ticket-left">
            <div class="ticket-header">
                <div>
                    <div style="font-size: 0.7rem; color: #888; text-transform: uppercase; letter-spacing: 2px;">Global Sports Arena</div>
                    <h2>Award Ceremony</h2>
                    <div style="font-size: 0.8rem; color: #888;">& Gala Dinner 2026</div>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 0.7rem; color: #888; text-transform: uppercase; margin-bottom: 4px;">Pass Type</div>
                    <div style="font-weight: 800; color: #8c6010; font-size: 1rem;"><?php echo htmlspecialchars($reg['pass_type']); ?></div>
                </div>
            </div>

            <div class="ticket-body">
                <div class="ticket-field" style="grid-column: span 2;">
                    <div class="ticket-label">Attendee Name</div>
                    <div class="ticket-value large"><?php echo htmlspecialchars($reg['full_name']); ?></div>
                </div>
                <div class="ticket-field">
                    <div class="ticket-label">Registration No.</div>
                    <div class="ticket-value" style="font-family: monospace; font-size: 0.85rem;"><?php echo htmlspecialchars($reg['registration_no']); ?></div>
                </div>
                <div class="ticket-field">
                    <div class="ticket-label">Payment Status</div>
                    <div class="ticket-value" style="color: #22c55e;"><?php echo htmlspecialchars($reg['payment_status']); ?></div>
                </div>
                <div class="ticket-field">
                    <div class="ticket-label">Venue</div>
                    <div class="ticket-value">The Orchid Hotel Pune</div>
                </div>
                <div class="ticket-field">
                    <div class="ticket-label">Date</div>
                    <div class="ticket-value">13 October 2026</div>
                </div>
                <div class="ticket-field">
                    <div class="ticket-label">Time</div>
                    <div class="ticket-value">7:00 PM – 11:00 PM</div>
                </div>
                <div class="ticket-field">
                    <div class="ticket-label">Food Preference</div>
                    <div class="ticket-value"><?php echo htmlspecialchars($reg['food_type']); ?></div>
                </div>
            </div>

            <!-- Barcode visual (CSS only) -->
            <div style="margin-top: 25px; display: flex; gap: 2px; height: 50px; align-items: flex-end; overflow: hidden;">
                <?php
                $chars = str_split($reg['pass_no']);
                foreach ($chars as $i => $c) {
                    $h = (ord($c) % 5 + 2) * 6;
                    echo "<div style='background:#1a1a1a;width:3px;height:{$h}px;border-radius:1px;'></div>";
                }
                ?>
            </div>
            <div style="font-size: 0.65rem; font-family: monospace; color: #888; margin-top: 4px; letter-spacing: 2px;">
                <?php echo htmlspecialchars($reg['pass_no']); ?>
            </div>
        </div>

        <!-- RIGHT SIDE -->
        <div class="ticket-right">
            <?php if ($reg['qr_code'] && file_exists(__DIR__ . '/' . $reg['qr_code'])): ?>
                <img class="ticket-qr" src="<?php echo htmlspecialchars($reg['qr_code']); ?>" alt="QR Code">
            <?php else: 
                // Fallback to QR API if local generation failed (e.g. GD missing)
                $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
                $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
                $verifyUrl = $protocol . $host . '/Mithraa_E_Project/project/verify-award-pass.php?pass=' . urlencode($reg['pass_no']);
                $qrApiUrl = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($verifyUrl);
            ?>
                <img class="ticket-qr" src="<?php echo htmlspecialchars($qrApiUrl); ?>" alt="QR Code">
            <?php endif; ?>
            <div class="ticket-pass-no"><?php echo htmlspecialchars($reg['pass_no']); ?></div>
            <div class="ticket-status-badge">✓ VALID</div>
            <div style="margin-top: 20px; font-size: 0.7rem; color: #888; line-height: 1.6;">
                Scan QR at<br>hotel entrance<br>for verification
            </div>
        </div>
    </div>

    <div class="ticket-actions">
        <button onclick="window.print()" class="btn-download">🖨️ Print Pass</button>
        <a href="award-success.php?reg=<?php echo urlencode($regNo); ?>" class="btn-download" style="background: transparent; color: #8c6010; border: 2px solid #c5a85c;">← Back</a>
    </div>
</div>

<?php if ($autoPrint): ?>
<script>window.onload = function() { window.print(); };</script>
<?php endif; ?>
