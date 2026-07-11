<?php
$pageTitle = "GLOBAL SPORTS ARENA | Your Cart";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
require_once __DIR__ . '/config/Settings.php';
$nxlCashbackPercentage = Settings::get('nxl_cashback_percentage', 0.05);
$membershipPlansJson = Settings::get('membership_plans', '{}');
?>

<link rel="stylesheet" href="assets/css/Cart.css?v=3">

<div class="cart-page">
  <div class="cart-header">
    <div class="cart-header-content max-w-7xl mx-auto px-4 py-8">
      <h1 class="text-3xl md:text-4xl font-black">🛒 Your Cart</h1>
      <p id="cartItemsCountLabel" class="text-gray-400 mt-2">0 items in your cart</p>
    </div>
  </div>

  <div class="cart-container flex flex-col lg:flex-row gap-8 max-w-7xl mx-auto px-4 py-8">
    <div class="cart-items-section flex-1" id="cartItemsSection">
      <!-- Dynamic cart items list rendered via JS -->
    </div>

    <!-- Summary section -->
    <div class="order-summary w-full lg:w-96 flex-shrink-0" id="orderSummarySection" style="display: none;">
      <h2>Order Summary</h2>
      <div class="summary-details">
        <div class="summary-row">
          <span id="itemsSubtotalCount">Subtotal (0 items)</span>
          <span id="subtotalPriceSpan">₹0</span>
        </div>

        <!-- Membership Plan Discount -->
        <div class="summary-row membership-option" id="membershipOptionContainer" style="display: none; flex-direction: column; gap: 8px; margin-top: 10px; margin-bottom: 5px;">
          <label style="font-size: 0.9rem; color: #c5a85c; font-weight: 600;">Active Membership</label>
          <div id="membershipDisplay" style="background: rgba(197, 168, 92, 0.1); color: #c5a85c; padding: 10px; border-radius: 8px; border: 1px dashed rgba(197, 168, 92, 0.3); text-align: center; font-weight: bold;">
            None
          </div>
        </div>

        <div class="summary-row savings" id="membershipDiscountRow" style="display: none;">
          <span id="membershipDiscountLabel">Membership Discount</span>
          <span class="savings-amount" id="membershipDiscountSpan">- ₹0</span>
        </div>

        <div class="summary-row savings" id="couponDiscountRow" style="display: none;">
          <span>Coupon Discount</span>
          <span class="savings-amount" id="couponDiscountSpan">- ₹0</span>
        </div>

        <div class="summary-row" id="priceAfterMembershipRow" style="display: none;">
          <span>Price After Membership</span>
          <span id="priceAfterMembershipSpan">₹0</span>
        </div>

        <div class="summary-row">
          <span>GST (18%)</span>
          <span id="gstAmountSpan">₹0</span>
        </div>

        <div class="summary-row">
          <span>Amount After GST</span>
          <span id="amountAfterGstSpan">₹0</span>
        </div>

        <!-- NXL Credits Section (all users) -->
        <div class="nxl-coins-section" id="nxlCoinsSection" style="display: flex; flex-direction: column; gap: 12px; align-items: stretch; padding: 18px; background: rgba(197, 168, 92, 0.04); border: 1px dashed rgba(197, 168, 92, 0.25); border-radius: 12px; margin-top: 10px;">
          <div class="coins-info" style="display: flex; justify-content: space-between; align-items: center">
            <div>
              <span style="display: block; font-size: 0.85rem; color: #9aa0b4; margin-bottom: 2px">Available NXL Credits</span>
              <strong style="font-size: 1.25rem; color: #f8fafc; font-weight: 700" id="nxlBalanceLabel">0 Credits</strong>
            </div>
            <div id="coinsRedeemingLabel" style="text-align: right; display: none;">
              <span style="display: block; font-size: 0.8rem; color: #c5a85c">Redeeming</span>
              <strong style="font-size: 1.1rem; color: #c5a85c" id="coinsRedeemedText">0 Credits</strong>
            </div>
          </div>

          <div style="display: flex; flex-direction: column; gap: 8px; margin-top: 4px">
            <span style="font-size: 0.82rem; color: #c5a85c; font-weight: 600; letter-spacing: 0.5px">Redemption Options (1 Credit = ₹1)</span>
            <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;" id="redemptionOptionsGroup">
              <!-- Buttons rendered via JS based on coins available -->
            </div>
          </div>
        </div>

        <div class="summary-row savings" id="nxlDiscountRow" style="display: none;">
          <span>NXL Coin Redemption</span>
          <span class="savings-amount" id="nxlDiscountSpan">- ₹0</span>
        </div>

        <div class="summary-row total">
          <span>Final Amount Payable</span>
          <span id="totalAmountSpan">₹0</span>
        </div>
      </div>

      <!-- NXL Credits earning info (premium users only — shown via JS) -->
      <div class="nxl-credits" id="nxlEarnSection" style="display: none;">
        <div class="nxl-icon">💎</div>
        <div class="nxl-info">
          <span>You'll earn</span>
          <strong id="earnedCreditsLabel">0 NXL Credits</strong>
          <span>on this purchase</span>
        </div>
      </div>

      <button class="checkout-btn" onclick="proceedToCheckout()">
        Proceed to Checkout →
      </button>

    </div>
  </div>

  <footer class="cart-footer">
    <p>© 2026 GLOBAL SPORTS ARENA. All rights reserved.</p>
  </footer>
</div>

<script>
const membershipPlans = <?php echo $membershipPlansJson ?: '{}'; ?>;
let nxlCashbackPercentage = <?php echo json_encode((float)$nxlCashbackPercentage); ?>;
let discountCode = "";
let discountAmount = 0;
let nxlCoins = 0;
let redeemedCoins = 0;
let isPremiumUser = false;
let membershipDiscountAmount = 0;
let membershipPlan = (localStorage.getItem("userMembership") || "none").toLowerCase().trim();

if (membershipPlan !== "none" && membershipPlans[membershipPlan]) {
    // Only use membership percent for discount, not for NXL cashback earning
    // nxlCashbackPercentage remains the global setting
}

function checkMembership() {
    const container = document.getElementById("membershipOptionContainer");
    const display = document.getElementById("membershipDisplay");
    if (membershipPlan !== "none" && membershipPlans[membershipPlan]) {
        container.style.display = "flex";
        let discountPercent = membershipPlans[membershipPlan].cashback_percent * 100;
        display.textContent = `${membershipPlan.charAt(0).toUpperCase() + membershipPlan.slice(1)} Member (${discountPercent}% Off)`;
    } else {
        container.style.display = "none";
    }
}

function renderCart() {
    const itemsSection = document.getElementById("cartItemsSection");
    const countLabel = document.getElementById("cartItemsCountLabel");
    const summarySection = document.getElementById("orderSummarySection");

    if (!window.Cart) return;
    const items = window.Cart.getItems();
    const count = window.Cart.getCartCount();

    countLabel.textContent = `${count} item${count !== 1 ? 's' : ''} in your cart`;

    if (items.length === 0) {
        summarySection.style.display = "none";
        itemsSection.innerHTML = `
          <div class="empty-cart">
            <div class="empty-cart-icon">🛒</div>
            <h3>Your cart is empty</h3>
            <p>Looks like you haven't added any items yet</p>
            <button class="continue-shopping-btn" onclick="window.location.href='products.php'">
              Continue Shopping →
            </button>
          </div>
        `;
        return;
    }

    summarySection.style.display = "block";
    let itemsHtml = `
      <div class="cart-header-actions">
        <div class="cart-items-header">
          <span>Product</span>
          <span>Price</span>
          <span>Quantity</span>
          <span>Total</span>
          <span></span>
        </div>
        <button onclick="window.Cart.clearCart(); renderCart();" class="clear-cart-btn">
          Clear All
        </button>
      </div>
    `;

    items.forEach(item => {
        const itemSavings = (item.originalPrice && item.originalPrice > item.price) 
            ? (item.originalPrice - item.price) * item.quantity 
            : 0;

        itemsHtml += `
          <div class="cart-item">
            <div class="item-info">
              <div class="item-image">
                <img src="${item.image}" alt="${item.name}" onerror="this.src='placeholder.png';" />
              </div>
              <div class="item-details">
                <h3>${item.name}</h3>
                <p class="item-category">${item.category}</p>
                ${itemSavings > 0 ? `<span class="item-savings">Save ₹${itemSavings.toLocaleString()}</span>` : ''}
              </div>
            </div>

            <div class="item-price">
              <span class="current-price">₹${Number(item.price).toLocaleString()}</span>
              ${item.originalPrice && item.originalPrice > item.price ? `<span class="original-price">₹${Number(item.originalPrice).toLocaleString()}</span>` : ''}
            </div>

            <div class="item-quantity">
              <button class="qty-btn minus" onclick="updateItemQty(${item.id}, ${item.quantity - 1})">−</button>
              <span class="qty-value">${item.quantity}</span>
              <button class="qty-btn plus" onclick="updateItemQty(${item.id}, ${item.quantity + 1})">+</button>
            </div>

            <div class="item-total">
              ₹${(Number(item.price) * item.quantity).toLocaleString()}
            </div>

            <button class="remove-btn" onclick="window.Cart.removeFromCart(${item.id}); renderCart();">
              <span>🗑️</span>
            </button>
          </div>
        `;
    });

    itemsSection.innerHTML = itemsHtml;
    calculateSummary();
}

function updateItemQty(id, qty) {
    if (!window.Cart) return;
    window.Cart.updateQuantity(id, qty);
    renderCart();
}

function calculateSummary() {
    if (!window.Cart) return;
    const items = window.Cart.getItems();
    let subtotal = window.Cart.getCartTotal();
    const count = window.Cart.getCartCount();

    if (discountCode === "GLOBAL10") {
        discountAmount = subtotal * 0.1;
    } else if (discountCode === "NXL100") {
        discountAmount = 100;
    } else {
        discountAmount = 0;
    }

    let productPrice = subtotal - discountAmount;
    if (productPrice < 0) productPrice = 0;

    document.getElementById("itemsSubtotalCount").textContent = `Subtotal (${count} items)`;
    document.getElementById("subtotalPriceSpan").textContent = `₹${subtotal.toFixed(2)}`;

    if (discountAmount > 0) {
        document.getElementById("couponDiscountRow").style.display = "flex";
        document.getElementById("couponDiscountSpan").textContent = `- ₹${discountAmount.toFixed(2)}`;
    } else {
        document.getElementById("couponDiscountRow").style.display = "none";
    }

    let discountPercent = 0;
    if (membershipPlan !== "none" && membershipPlans[membershipPlan]) {
        discountPercent = membershipPlans[membershipPlan].cashback_percent;
    }

    if (discountPercent > 0) {
        membershipDiscountAmount = parseFloat((productPrice * discountPercent).toFixed(2));
        document.getElementById("membershipDiscountRow").style.display = "flex";
        document.getElementById("membershipDiscountLabel").textContent = `Premium Discount (${discountPercent * 100}%)`;
        document.getElementById("membershipDiscountSpan").textContent = `- ₹${membershipDiscountAmount.toFixed(2)}`;
    } else {
        membershipDiscountAmount = 0;
        document.getElementById("membershipDiscountRow").style.display = "none";
    }

    let priceAfterMembership = productPrice - membershipDiscountAmount;
    
    // Hide intermediate rows as requested for cleaner UI
    document.getElementById("priceAfterMembershipRow").style.display = "none";

    let gstAmount = parseFloat((priceAfterMembership * 0.18).toFixed(2));
    document.getElementById("gstAmountSpan").textContent = `+ ₹${gstAmount.toFixed(2)}`;

    let amountAfterGST = parseFloat((priceAfterMembership + gstAmount).toFixed(2));
    
    // Hide intermediate rows
    document.getElementById("amountAfterGstSpan").parentElement.style.display = "none";

    let earnedNxlCoins = 0;
    if (isPremiumUser) {
        earnedNxlCoins = parseFloat((priceAfterMembership * nxlCashbackPercentage).toFixed(2));
    }
    document.getElementById("earnedCreditsLabel").textContent = `${earnedNxlCoins} NXL Credits`;

    if (redeemedCoins > nxlCoins) redeemedCoins = nxlCoins;
    if (redeemedCoins > amountAfterGST) redeemedCoins = parseFloat(amountAfterGST.toFixed(2));

    if (redeemedCoins > 0) {
        document.getElementById("coinsRedeemingLabel").style.display = "block";
        document.getElementById("coinsRedeemedText").textContent = `${redeemedCoins} Credits`;
        document.getElementById("nxlDiscountRow").style.display = "flex";
        document.getElementById("nxlDiscountSpan").textContent = `- ₹${redeemedCoins.toFixed(2)}`;
    } else {
        document.getElementById("coinsRedeemingLabel").style.display = "none";
        document.getElementById("nxlDiscountRow").style.display = "none";
    }

    let finalAmount = parseFloat((amountAfterGST - redeemedCoins).toFixed(2));
    document.getElementById("totalAmountSpan").textContent = `₹${finalAmount.toFixed(2)}`;

    renderRedemptionOptions(amountAfterGST);
}

function applyCouponCode() {
    if (!window.Cart) return;
    const subtotal = window.Cart.getCartTotal();
    const code = document.getElementById("discountCodeInput").value.trim().toUpperCase();
    const msg = document.getElementById("discountMessage");

    if (code === "") {
        discountAmount = 0;
        discountCode = "";
        msg.style.display = "block";
        msg.textContent = "Please enter discount code";
        msg.style.color = "#ff6b6b";
        calculateSummary();
        return;
    }

    if (code === "GLOBAL10") {
        discountAmount = Math.floor(subtotal * 0.1);
        discountCode = code;
        msg.style.display = "block";
        msg.textContent = `GLOBAL10 applied! You saved ₹${discountAmount}`;
        msg.style.color = "#22c55e";
    } else if (code === "NXL100") {
        discountAmount = 100;
        discountCode = code;
        msg.style.display = "block";
        msg.textContent = "NXL100 applied! You saved ₹100";
        msg.style.color = "#22c55e";
    } else {
        discountAmount = 0;
        discountCode = "";
        msg.style.display = "block";
        msg.textContent = "Invalid discount code";
        msg.style.color = "#ff6b6b";
    }
    calculateSummary();
}

async function fetchWalletBalance() {
    const userEmail = localStorage.getItem("userEmail");
    if (!userEmail) return;

    try {
        const res = await fetch(`api/index.php/wallet/balance?email=${encodeURIComponent(userEmail)}`);
        const data = await res.json();
        if (data.nxlCredits !== undefined) {
            nxlCoins = data.nxlCredits;
            document.getElementById("nxlBalanceLabel").textContent = `${nxlCoins} Credits`;
            calculateSummary();
        }
    } catch(e) {
        console.error("Wallet loading failed", e);
    }
}

function renderRedemptionOptions(maxAmountAllowed = Infinity) {
    const group = document.getElementById("redemptionOptionsGroup");
    if (!group) return;

    if (nxlCoins < 100) {
        group.innerHTML = `<span style="font-size: 0.8rem; color: #9aa0b4">You need at least 100 NXL Credits to redeem.</span>`;
        return;
    }

    const maxRedeemable = Math.min(nxlCoins, maxAmountAllowed);

    group.innerHTML = `
      <div style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center; margin-bottom: 8px;">
        <input 
          type="number" 
          id="customCreditInput" 
          placeholder="Min 100" 
          min="100" 
          step="0.01"
          max="${maxRedeemable}"
          value="${redeemedCoins > 0 ? redeemedCoins : ''}"
          style="padding: 8px 12px; border-radius: 8px; border: 1px solid rgba(197, 168, 92, 0.4); background: rgba(22, 24, 38, 0.8); color: #f5f6fa; outline: none; width: 120px;"
        />
        <button 
          type="button"
          onclick="applyCustomCredits(${maxRedeemable})"
          style="padding: 8px 16px; border-radius: 8px; background: #c5a85c; color: #0b0c10; font-weight: bold; border: none; cursor: pointer;"
        >
          Apply
        </button>
        ${redeemedCoins > 0 ? `
          <button
            type="button"
            onclick="selectCoinDiscount(0, false)"
            style="padding: 8px 16px; background: transparent; border: 1px solid #ef4444; color: #ef4444; border-radius: 8px; font-weight: 600; cursor: pointer;"
          >
            Clear
          </button>
        ` : ''}
      </div>
      <div style="font-size: 0.8rem; color: #9aa0b4;">
        You can redeem up to ${maxRedeemable} credits on this order.
      </div>
    `;
}

window.applyCustomCredits = function(maxRedeemable) {
    const input = document.getElementById('customCreditInput');
    let val = parseFloat(input.value);
    
    if (isNaN(val) || val < 100) {
        alert("Minimum redemption is 100 credits.");
        return;
    }
    if (val > maxRedeemable) {
        alert("You can only redeem up to " + maxRedeemable + " credits on this order.");
        val = maxRedeemable;
        input.value = val;
    }
    
    selectCoinDiscount(val, false);
}

function selectCoinDiscount(coins, isSelected) {
    if (isSelected) {
        redeemedCoins = 0;
    } else {
        redeemedCoins = coins;
    }
    calculateSummary();
}

async function proceedToCheckout() {
    if (!window.Cart) return;
    const items = window.Cart.getItems();
    const count = window.Cart.getCartCount();
    const subtotal = window.Cart.getCartTotal();

    if (items.length === 0) {
        alert("Your cart is empty");
        return;
    }

    if (!localStorage.getItem("token")) {
        alert("Please login first to proceed to checkout!");
        window.location.href = "login.php";
        return;
    }

    const btn = document.querySelector('.checkout-btn');
    const originalText = btn.innerHTML;
    btn.innerHTML = "Processing...";
    btn.disabled = true;

    let productPrice = subtotal - discountAmount;
    if (productPrice < 0) productPrice = 0;
    
    let discountPercent = 0;
    if (membershipPlan === "standard") discountPercent = 0.05;
    else if (membershipPlan === "premium") discountPercent = 0.10;
    else if (membershipPlan === "elite") discountPercent = 0.15;
    
    let membershipDiscountAmount = parseFloat((productPrice * discountPercent).toFixed(2));
    let priceAfterMembership = productPrice - membershipDiscountAmount;
    let gstAmount = parseFloat((priceAfterMembership * 0.18).toFixed(2));
    let amountAfterGST = parseFloat((priceAfterMembership + gstAmount).toFixed(2));
    let finalAmount = parseFloat((amountAfterGST - redeemedCoins).toFixed(2));
    let nxlCreditsEarned = parseFloat((priceAfterMembership * nxlCashbackPercentage).toFixed(2));

    const newOrder = {
        id: "ORD-" + Date.now(),
        total: finalAmount,
        price: subtotal,
        discountAmount: discountAmount,
        membershipDiscountAmount: membershipDiscountAmount,
        gstAmount: gstAmount,
        nxlCoinsUsed: redeemedCoins,
        nxlCoinsEarned: nxlCreditsEarned,
        type: "product",
        title: items.map(item => item.name).join(", "),
        image: "🛒",
        brand: "GLOBAL SPORTS ARENA",
        quantity: count,
        status: "pending",
        orderDate: new Date().toLocaleDateString(),
        trackingId: "TRK-" + Math.floor(100000 + Math.random() * 900000),
        estimatedDelivery: "5-7 working days",
        items: items,
        discountCode: membershipPlan !== "none" ? (discountCode ? discountCode + ", " + membershipPlan.toUpperCase() : membershipPlan.toUpperCase()) : discountCode,
        deliveryFee: 0,
    };

    try {
        const orderRes = await fetch("api/index.php/orders/create-razorpay-order", {
            method: "POST",
            headers: { 
                "Content-Type": "application/json",
                "Authorization": "Bearer " + localStorage.getItem("token")
            },
            body: JSON.stringify({ amount: finalAmount })
        });
        const orderData = await orderRes.json();
        
        if (orderData.error) {
            alert("Payment Gateway Error: " + (orderData.error.description || orderData.error));
            btn.innerHTML = originalText;
            btn.disabled = false;
            return;
        }

        const options = {
            key: "<?php echo defined('RAZORPAY_KEY_ID') ? RAZORPAY_KEY_ID : ''; ?>",
            amount: Math.round(finalAmount * 100),
            order_id: orderData.id,
            currency: "INR",
            name: "GLOBAL SPORTS ARENA",
            description: "Shopping Cart Checkout",
            prefill: {
                name: localStorage.getItem("user_name") || "Guest User",
                email: localStorage.getItem("user_email") || "guest@example.com",
                contact: "9999999999"
            },
            handler: async function (response) {
                const finalPayload = {
                    ...newOrder,
                    paymentStatus: "PAID",
                    paymentId: response.razorpay_payment_id,
                    razorpay_order_id: response.razorpay_order_id,
                    razorpay_signature: response.razorpay_signature,
                    paymentMethod: "RAZORPAY",
                    user_email: localStorage.getItem("user_email"),
                    shippingInfo: {
                        fullName: localStorage.getItem("user_name") || "No Name",
                        email: localStorage.getItem("user_email") || "",
                        phone: "No Phone",
                        address: "Direct Cart Checkout",
                        city: "N/A",
                        state: "N/A",
                        zipCode: "N/A"
                    }
                };

                try {
                    const res = await fetch("api/index.php/orders/place", {
                        method: "POST",
                        headers: { 
                            "Content-Type": "application/json",
                            "Authorization": "Bearer " + localStorage.getItem("token")
                        },
                        body: JSON.stringify(finalPayload)
                    });
                    const data = await res.json();
                    
                    if (window.Cart) window.Cart.clearCart(true);
                    
                    let walletBalance = parseFloat(localStorage.getItem("nxlCoins") || 0);
                    const newBalance = walletBalance - (newOrder.nxlCoinsUsed || 0) + (newOrder.nxlCoinsEarned || 0);
                    localStorage.setItem("nxlCoins", newBalance);
                    
                    alert(`Payment successful! You earned ${newOrder.nxlCoinsEarned} NXL Credits.`);
                    localStorage.setItem("gsa_last_order", JSON.stringify(data));
                    window.location.href = "payment-success.php";
                } catch (err) {
                    console.error("Failed to sync paid order", err);
                    alert("Payment received, but database sync failed.");
                }
            },
            theme: { color: "#c5a85c" }
        };
        
        const rzp = new window.Razorpay(options);
        rzp.on('payment.failed', function (response){
            let errorDetails = "Razorpay Rejected the Payment!\n\n";
            errorDetails += "Reason: " + response.error.reason + "\n";
            errorDetails += "Description: " + response.error.description + "\n";
            errorDetails += "Code: " + response.error.code + "\n";
            errorDetails += "Source: " + response.error.source + "\n\n";
            errorDetails += "If you are using AdBlock Plus, it blocks Razorpay's Test Bank Simulator. Please disable it.";
            alert(errorDetails);
            
            console.error("Razorpay Error Details:", response.error);
            
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
        rzp.open();
    } catch (err) {
        console.error("Failed to generate Razorpay order", err);
        alert("Failed to initialize payment gateway.");
        btn.innerHTML = originalText;
        btn.disabled = false;
    }
}

document.addEventListener("DOMContentLoaded", function() {
    renderCart();
    fetchWalletBalance();
    checkMembership();
    renderCart();

    // NXL redeem section is visible for ALL users
    // Only the "You'll earn" (5%) section is shown for premium members only
    const membership = (localStorage.getItem("userMembership") || "none").toLowerCase().trim();
    const hasMembership = membership !== "none" && membership !== "";
    if (hasMembership) {
        const earnSection = document.getElementById("nxlEarnSection");
        if (earnSection) earnSection.style.display = "flex";
        isPremiumUser = true;
    }
});
</script>



<?php require_once __DIR__ . '/includes/footer.php'; ?>
