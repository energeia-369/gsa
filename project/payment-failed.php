<?php
$pageTitle = "GLOBAL SPORTS ARENA | Payment Failed";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<link rel="stylesheet" href="assets/css/PaymentFailed.css">

<div class="failed-page">
  <div class="failed-box w-full max-w-md mx-auto px-4">
    <h1>Payment Failed ❌</h1>
    <p>Please try again or use another payment method.</p>
    <div style="margin-top: 25px;">
      <a href="cart.php" class="btn-premium-gold" style="display: inline-block; padding: 10px 20px; border-radius: 8px; font-weight: bold; background: #c5a85c; color: #0b0c10; text-decoration: none;">
        Return to Cart 🛒
      </a>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
