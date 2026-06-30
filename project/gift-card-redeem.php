<?php
$pageTitle = "Redeem Gift Card - Global Sports Arena";
$pageDescription = "Redeem your GSA Gift Card and enjoy sports experiences.";
require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<link rel="stylesheet" href="assets/css/gift-cards.css">

<main class="gc-container">
    <div style="max-width: 600px; margin: 0 auto; text-align: center;">
        <div style="font-size: 3rem; margin-bottom: 15px;">🎁</div>
        <h1 style="color: var(--gc-gold); margin-bottom: 10px;">Redeem Your Gift Card</h1>
        <p style="opacity: 0.85; margin-bottom: 40px;">Enter your gift card code below to add the balance to your wallet and start enjoying sports experiences.</p>
    </div>

    <div class="gc-form-container w-full max-w-xl mx-auto px-4" style="max-width: 100%;">
        <form id="gc_redeem_form" onsubmit="handleGiftCardRedeem(event)">
            <div class="gc-form-group">
                <label class="gc-form-label" for="gc_redeem_code">Gift Card Code</label>
                <input 
                    type="text" 
                    id="gc_redeem_code" 
                    name="code"
                    class="gc-form-input" 
                    placeholder="GSA-GIFT-2026-XXXXXXXX"
                    required
                    style="font-size: 1.1rem; letter-spacing: 1px; text-align: center;"
                >
            </div>

            <button type="submit" id="btn_redeem" class="gc-btn gc-btn-solid" style="width: 100%; padding: 15px; font-size: 1.1rem;">
                Redeem Gift Card
            </button>
        </form>

        <div id="redeem_result" style="margin-top: 20px;"></div>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid var(--gc-border); font-size: 0.85rem; opacity: 0.7;">
            <h4 style="margin-bottom: 10px;">How to Redeem:</h4>
            <ul style="list-style: none; padding: 0; text-align: left; line-height: 2;">
                <li>✅ Enter your 22-character gift card code</li>
                <li>✅ Make sure you are logged in for balance to be added</li>
                <li>✅ Balance will be added to your NXL Wallet instantly</li>
                <li>✅ Each gift card can be redeemed only once</li>
            </ul>
        </div>
    </div>

    <div style="text-align: center; margin-top: 30px;">
        <a href="gift-cards.php" style="color: var(--gc-gold); text-decoration: underline;">Buy a Gift Card instead</a>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
<script src="assets/js/gift-cards.js"></script>
