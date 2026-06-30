<?php
$pageTitle = "GLOBAL SPORTS ARENA | Payment Successful";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<link rel="stylesheet" href="assets/css/PaymentSuccess.css?v=2">

<div class="success-page overflow-x-hidden" style="background: #0b0c10; font-family: Outfit, sans-serif">
  <div class="bg-animation">
    <div class="circle circle-1" style="background: rgba(197, 168, 92, 0.05)"></div>
    <div class="circle circle-2" style="background: rgba(197, 168, 92, 0.03)"></div>
  </div>

  <div class="success-box w-full max-w-xl mx-auto px-4" style="background: #12131c; border: 1px solid rgba(197, 168, 92, 0.25); box-shadow: 0 15px 35px rgba(0,0,0,0.6); padding: 40px; border-radius: 24px; max-width: 100%; width: 90%; margin: 40px auto; text-align: center;">
    <div class="success-animation">
      <div class="checkmark-circle" style="border-color: #c5a85c">
        <div class="checkmark draw" style="border-right: 3px solid #c5a85c; border-top: 3px solid #c5a85c"></div>
      </div>
    </div>

    <h1 class="success-title" style="color: #c5a85c; font-weight: 800; fontSize: 2.2rem; margin-top: 20px;">Payment Successful! 🎉</h1>

    <p class="success-message" style="color: #9aa0b4; margin-top: 10px;">
      Your order and event registration have been successfully synchronized with the database.
    </p>

    <!-- E-Ticket Display -->
    <div class="ticket-display" style="background: #0b0c10; border: 1px dashed rgba(197, 168, 92, 0.3); border-radius: 16px; padding: 20px; margin: 20px 0; text-align: left; display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 20px; alignItems: center">
      <div>
        <h4 class="ticket-header" style="color: #c5a85c; margin: 0 0 10px 0; fontSize: 1.1rem; border-bottom: 1px solid rgba(197,168,92,0.15); padding-bottom: 6px">💎 Membership Activation</h4>
        <div class="ticket-row" style="font-size: 0.85rem; color: #9aa0b4; margin-bottom: 8px">
          <strong>Order/Booking ID:</strong> <span class="ticket-val" style="color: #f5f6fa" id="successOrderId">#ORD-N/A</span>
        </div>
        <div class="ticket-row" style="font-size: 0.85rem; color: #9aa0b4; margin-bottom: 8px">
          <strong>Date & Time:</strong> <span class="ticket-val" style="color: #f5f6fa" id="successDateLabel">N/A</span>
        </div>
        <div class="ticket-row" style="font-size: 0.85rem; color: #9aa0b4; margin-bottom: 8px">
          <strong>Amount Synchronized:</strong> <span class="ticket-val-green" style="color: #22c55e; font-weight: bold" id="successAmountLabel">₹0</span>
        </div>
        <div class="ticket-row" style="font-size: 0.85rem; color: #9aa0b4">
          <strong>Loyalty Earned:</strong> <span class="ticket-val-gold" style="color: #c5a85c; font-weight: bold" id="successCreditsLabel">💎 0 NXL Credits</span>
        </div>
      </div>


    </div>

    <div class="order-details" style="background: rgba(22,24,38,0.5); border: 1px solid rgba(255,255,255,0.03); padding: 15px; border-radius: 12px; margin-bottom: 20px; text-align: left;">
      <div class="detail-row" style="display: flex; justify-content: space-between; margin: 4px 0; fontSize: 0.85rem">
        <span class="detail-label" style="color: #9aa0b4">Transaction ID:</span>
        <span class="detail-value" style="color: #f5f6fa; font-weight: bold" id="successTxId">TXN-N/A</span>
      </div>

      <div class="detail-row" style="display: flex; justify-content: space-between; margin: 4px 0; fontSize: 0.85rem">
        <span class="detail-label" style="color: #9aa0b4">Shipping Address:</span>
        <span class="detail-value" style="color: #f5f6fa" id="successAddressLabel">N/A</span>
      </div>
    </div>

    <div class="button-group" style="display: flex; gap: 10px; justify-content: center">
      <button class="home-btn" onclick="window.location.href='index.php'" style="background: linear-gradient(135deg, #c5a85c 0%, #8c7237 100%); color: #0b0c10; border: none; padding: 10px 20px; border-radius: 8px; font-weight: bold; cursor: pointer">
        🏠 Go to Homepage
      </button>

      <button class="orders-btn" onclick="window.location.href='user-dashboard.php'" style="background: transparent; color: #c5a85c; border: 1px solid #c5a85c; padding: 10px 20px; border-radius: 8px; font-weight: bold; cursor: pointer">
        📋 View My Dashboard
      </button>
    </div>

    <div style="margin-top: 20px" id="redirectContainer">
      <p class="redirect-info" style="color: #9aa0b4; font-size: 0.85rem; display: inline-block; margin-right: 10px" id="countdownText">
        Redirecting to homepage in 15 seconds...
      </p>
      <button 
        onclick="cancelRedirect()"
        style="background: rgba(220, 38, 38, 0.2); border: 1px solid #dc2626; color: #ef4444; padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; cursor: pointer"
      >
        Cancel Auto-Redirect
      </button>
    </div>
    <p style="color: #22c55e; font-size: 0.85rem; margin-top: 20px; display: none;" id="cancelAlertText">
      ✓ Auto-redirect cancelled. You can now save your QR ticket receipt pass.
    </p>
  </div>
</div>

<script>
let countdown = 15;
let redirectTimer = null;
let stopRedirect = false;

function loadOrderDetails() {
    let order = JSON.parse(localStorage.getItem("gsa_last_order"));
    if (!order) {
        order = {
            id: "12345",
            total: 0,
            total_amount: 0,
            orderDate: new Date().toLocaleDateString(),
            nxlCoinsEarned: 0,
            shippingAddress: "Digital Delivery (Email Upgrade)",
            customerPhone: "+91 99999 88888"
        };
    }

    const orderId = order.id || order.orderId || "12345";
    const total = order.total !== undefined ? order.total : (order.total_amount || 0);
    const orderDate = order.orderDate || order.order_date || new Date().toLocaleDateString();
    const coinsEarned = order.nxlCoinsEarned || order.nxl_coins_earned || 0;
    const address = order.shippingAddress || order.shipping_address || "Digital Delivery";
    const phone = order.customerPhone || order.customer_phone || "+91 99999 88888";

    document.getElementById("successOrderId").textContent = "#ORD-" + orderId;
    document.getElementById("successDateLabel").textContent = orderDate;
    document.getElementById("successAmountLabel").textContent = "₹" + Number(total).toLocaleString();
    document.getElementById("successCreditsLabel").textContent = "💎 " + coinsEarned + " NXL Credits";
    document.getElementById("successTxId").textContent = "TXN-" + orderId + "-" + Math.floor(Math.random() * 10000);
    document.getElementById("successAddressLabel").textContent = address;

    // Check order type to adjust UI
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('type') === 'membership') {
        const plan = urlParams.get('plan') || 'standard';
        localStorage.setItem("userMembership", plan);

        // Update UI for membership
        const successMsg = document.querySelector('.success-message');
        if (successMsg) successMsg.textContent = "Your premium membership has been successfully activated and synchronized with your account.";
        
        const passTitle = document.querySelector('h4');
        if (passTitle) passTitle.innerHTML = "💎 Membership Activation";
        
        // Sync with backend API
        const userEmail = localStorage.getItem("userEmail");
        if (userEmail) {
            fetch("api/index.php/user/update-membership", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ email: userEmail, plan: plan, earnedCredits: coinsEarned })
            }).then(res => res.json()).then(data => {
                console.log("Membership synced:", data);
            }).catch(e => console.error("Sync error:", e));
        }
    } else if (order.type === 'product' || order.items || order.items_json) {
        // Update UI for physical products
        const successMsg = document.querySelector('.success-message');
        if (successMsg) successMsg.textContent = "Your order has been successfully placed and will be shipped to your address shortly.";
        
        const passTitle = document.querySelector('h4');
        if (passTitle) passTitle.innerHTML = "📦 Order Confirmation";
    } else {
        // Assume Event Registration
        const successMsg = document.querySelector('.success-message');
        if (successMsg) successMsg.textContent = "Your event registration has been successfully confirmed. Please save this QR code pass to enter the match ground.";
        
        const passTitle = document.querySelector('.ticket-header');
        if (passTitle) passTitle.innerHTML = "🎟️ Event Entry Pass";
    }

    // Start auto redirect timer
    startTimer();
}

function startTimer() {
    redirectTimer = setInterval(() => {
        if (stopRedirect) return;
        countdown--;
        document.getElementById("countdownText").textContent = `Redirecting to homepage in ${countdown} seconds...`;
        
        if (countdown <= 0) {
            clearInterval(redirectTimer);
            window.location.href = "index.php";
        }
    }, 1000);
}

function cancelRedirect() {
    stopRedirect = true;
    clearInterval(redirectTimer);
    document.getElementById("redirectContainer").style.display = "none";
    document.getElementById("cancelAlertText").style.display = "block";
}

document.addEventListener("DOMContentLoaded", loadOrderDetails);
</script>



<?php require_once __DIR__ . '/includes/footer.php'; ?>
