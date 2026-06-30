<?php
require_once 'config/Database.php';

// Accept POST from JS redirect form
$gift_code = htmlspecialchars($_POST['gift_code'] ?? '');
$recipient_name = htmlspecialchars($_POST['recipient_name'] ?? '');
$amount = $_POST['amount'] ?? 0;
$expiry_date = htmlspecialchars($_POST['expiry_date'] ?? date('Y-m-d', strtotime('+365 days')));

if (empty($gift_code)) {
    header("Location: gift-cards.php");
    exit;
}

$pageTitle = "Gift Card Purchased - Global Sports Arena";
$pageDescription = "Your gift card purchase was successful!";

require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<link rel="stylesheet" href="assets/css/gift-cards.css">
<script src="https://cdn.jsdelivr.net/npm/qrcodejs/qrcode.min.js"></script>

<main class="gc-container">
    <div class="gc-form-container w-full max-w-2xl mx-auto px-4 text-center">
        <!-- Success Banner -->
        <div style="margin-bottom: 30px;">
            <div style="font-size:4rem; margin-bottom:10px;">🎉</div>
            <h1 style="color: var(--gc-gold); font-size: 2rem; margin-bottom:10px;">Gift Card Purchased Successfully!</h1>
            <p style="opacity:0.85;">Your gift card has been generated. Share the code with the recipient to redeem it.</p>
        </div>

        <!-- Code Box -->
        <div class="gc-code-box" id="giftCodeDisplay">
            <?php echo $gift_code; ?>
        </div>

        <!-- Details Card -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; text-align: left;">
            <div style="background: var(--gc-hover-bg); border: 1px solid var(--gc-border); padding: 20px; border-radius: 12px;">
                <div style="color: var(--gc-gold); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px;">Recipient</div>
                <div style="font-size: 1.1rem; font-weight: bold;"><?php echo $recipient_name; ?></div>
            </div>
            <div style="background: var(--gc-hover-bg); border: 1px solid var(--gc-border); padding: 20px; border-radius: 12px;">
                <div style="color: var(--gc-gold); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px;">Amount</div>
                <div style="font-size: 1.1rem; font-weight: bold;">₹<?php echo number_format($amount); ?></div>
            </div>
            <div style="background: var(--gc-hover-bg); border: 1px solid var(--gc-border); padding: 20px; border-radius: 12px;">
                <div style="color: var(--gc-gold); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px;">Valid Till</div>
                <div style="font-size: 1.1rem; font-weight: bold;"><?php echo date('d M Y', strtotime($expiry_date)); ?></div>
            </div>
        </div>

        <!-- QR Code -->
        <div style="margin-bottom: 30px;">
            <h4 style="margin-bottom: 15px; color: var(--gc-gold);">Redemption QR Code</h4>
            <div class="gc-qr-box" id="qrCodeContainer"></div>
            <p style="font-size: 0.8rem; opacity: 0.7; margin-top: 8px;">Scan to redeem at gift-card-redeem page</p>
        </div>

        <!-- Action Buttons -->
        <div style="display:flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
            <button class="gc-btn gc-btn-solid" onclick="downloadGiftCard()">⬇ Download Gift Card</button>
            <button class="gc-btn" onclick="shareGiftCard()">📤 Share Gift Card</button>
            <a href="index.php" class="gc-btn">🏠 Go Home</a>
            <a href="gift-card-redeem.php" class="gc-btn" style="border-color:#10b981; color:#10b981;">🎁 Redeem Now</a>
        </div>
    </div>
</main>

<script>
const giftCode = "<?php echo $gift_code; ?>";
const recipientName = "<?php echo $recipient_name; ?>";
const amount = "₹<?php echo number_format($amount); ?>";
const expiryDate = "<?php echo date('d M Y', strtotime($expiry_date)); ?>";

// Generate QR Code
const qrContainer = document.getElementById("qrCodeContainer");
new QRCode(qrContainer, {
    text: giftCode,
    width: 150,
    height: 150,
    colorDark: "#000000",
    colorLight: "#ffffff",
    correctLevel: QRCode.CorrectLevel.H
});

function downloadGiftCard() {
    const content = `
GLOBAL SPORTS ARENA - GIFT CARD
================================
Code: ${giftCode}
Recipient: ${recipientName}
Amount: ${amount}
Valid Till: ${expiryDate}

To Redeem: Visit gift-card-redeem.php on our website
================================
Thank you for choosing GSA!
    `.trim();

    const blob = new Blob([content], {type: 'text/plain'});
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `GSA-GiftCard-${giftCode}.txt`;
    a.click();
    URL.revokeObjectURL(url);
}

function shareGiftCard() {
    const text = `🎁 I sent you a GSA Gift Card!\n\nCode: ${giftCode}\nAmount: ${amount}\nValid Till: ${expiryDate}\n\nRedeem at: ${window.location.origin}/gift-card-redeem.php`;
    if(navigator.share) {
        navigator.share({ title: 'GSA Gift Card', text });
    } else {
        navigator.clipboard.writeText(text).then(() => {
            alert('Gift card details copied to clipboard!');
        });
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>
