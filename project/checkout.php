<?php
$pageTitle = "GLOBAL SPORTS ARENA | Secure Checkout";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<link rel="stylesheet" href="assets/css/Checkout.css">

<div class="checkout-page flex items-center justify-center min-h-screen px-4" style="color: #f8fafc">
  <div class="checkout-box" style="max-width: 600px; width: 90%; background: #12131c; padding: 30px; border-radius: 24px; border: 1px solid rgba(197, 168, 92, 0.25); box-shadow: 0 15px 35px rgba(0,0,0,0.5)">
    <h1 style="text-align: center; font-size: 2rem; margin-bottom: 20px; color: #c5a85c; font-weight: 800; letter-spacing: 1px">
      💳 Secure Checkout
    </h1>
    
    <!-- Order Details List -->
    <div style="background: rgba(22, 24, 38, 0.5); padding: 15px; border-radius: 16px; border: 1px solid rgba(255,255,255,0.05); margin-bottom: 20px">
      <h3 style="border-bottom: 1px solid rgba(197,168,92,0.2); padding-bottom: 8px; margin-bottom: 12px; color: #f8fafc; font-weight: 700">Order Summary</h3>
      <div id="checkoutItemsContainer" style="max-height: 150px; overflow-y: auto; margin-bottom: 15px">
        <!-- Rendered dynamically -->
      </div>

      <div style="display: flex; justify-content: space-between; fontSize: 0.9rem; color: #9aa0b4; margin-bottom: 6px">
        <span>Subtotal</span>
        <span id="checkoutSubtotal">₹0</span>
      </div>

      <div style="display: none; justify-content: space-between; fontSize: 0.9rem; color: #ef4444; margin-bottom: 6px" id="checkoutCouponRow">
        <span>Coupon Discount</span>
        <span id="checkoutCouponDiscount">- ₹0</span>
      </div>

      <div style="display: none; justify-content: space-between; fontSize: 0.9rem; color: #ef4444; margin-bottom: 6px" id="checkoutNxlRow">
        <span id="checkoutNxlUsedText">NXL Credits Used (0 Coins)</span>
        <span id="checkoutNxlDiscount">- ₹0</span>
      </div>

      <div style="display: flex; justify-content: space-between; fontSize: 0.9rem; color: #9aa0b4; margin-bottom: 6px">
        <span>GST (18%)</span>
        <span id="checkoutGstAmount">₹0</span>
      </div>

      <div style="display: flex; justify-content: space-between; fontSize: 0.9rem; color: #9aa0b4; margin-bottom: 6px">
        <span>Delivery Fee</span>
        <span>FREE</span>
      </div>

      <div style="display: flex; justify-content: space-between; fontSize: 1.2rem; font-weight: 800; color: #c5a85c; border-top: 1px solid rgba(197,168,92,0.2); padding-top: 8px; margin-top: 8px">
        <span>Total Payable</span>
        <span id="checkoutTotalPayable">₹0</span>
      </div>
    </div>

    <!-- NXL Credits Earning Badge -->
    <div style="background: rgba(197, 168, 92, 0.08); border: 1px dashed #c5a85c; padding: 12px; border-radius: 12px; text-align: center; margin-bottom: 20px; font-size: 0.9rem; color: #f5f6fa">
      💎 You will earn <strong style="color: #c5a85c; font-size: 1rem" id="checkoutEarnedCredits">0 NXL Credits</strong> on this purchase!
    </div>

    <!-- Shipping Form -->
    <div style="margin-bottom: 20px">
      <div id="shippingAddressContainer">
        <label style="display: block; font-size: 0.85rem; color: #9aa0b4; margin-bottom: 5px">Delivery Address *</label>
        <input
          type="text"
          id="checkoutAddressInput"
          placeholder="Enter your shipping address"
          style="width: 100%; padding: 10px; border: 1px solid rgba(197, 168, 92, 0.2); border-radius: 8px; background: rgba(22, 24, 38, 0.8); color: #f5f6fa; box-sizing: border-box; outline: none; margin-bottom: 12px"
        />
      </div>

      <label style="display: block; font-size: 0.85rem; color: #9aa0b4; margin-bottom: 5px">Phone Number *</label>
      <input
        type="tel"
        id="checkoutPhoneInput"
        placeholder="Enter your phone number"
        style="width: 100%; padding: 10px; border: 1px solid rgba(197, 168, 92, 0.2); border-radius: 8px; background: rgba(22, 24, 38, 0.8); color: #f5f6fa; box-sizing: border-box; outline: none"
      />
    </div>

    <!-- Payment button -->
    <button 
      id="checkoutPayButton"
      onclick="handleCheckoutPayment()"
      style="
        width: 100%; 
        padding: 12px; 
        background: #475569; 
        color: #94a3b8; 
        border: none; 
        border-radius: 8px; 
        font-size: 1.1rem; 
        font-weight: bold; 
        cursor: not-allowed;
        transition: background 0.3s ease;
      "
      disabled
    >
      Fill Shipping Details to Pay
    </button>

    <button 
      onclick="window.location.href='cart.php'" 
      id="checkoutCancelButton"
      style=" 
        width: 100%; 
        padding: 10px; 
        background: transparent; 
        color: #9aa0b4; 
        border: 1px solid rgba(197, 168, 92, 0.2); 
        border-radius: 8px; 
        font-size: 0.95rem; 
        margin-top: 12px;
        cursor: pointer;
      "
    >
      ← Cancel and Return to Cart
    </button>
  </div>
</div>

<!-- Congratulations Modal for 0 Rs Checkout -->
<div id="zeroAmountCongratsModal" class="congrats-modal-overlay" style="display: none;">
  <div id="congratsModalContent" class="congrats-modal-content">
    <div class="congrats-icon">🎉</div>
    <h2 class="congrats-title">Congratulations!</h2>
    <p class="congrats-text">You have successfully used your NXL Credits to cover the entire cost. Your total payable amount is exactly <strong class="congrats-amount">₹0.00</strong>.</p>
    <button onclick="closeCongratsModal()" class="congrats-claim-btn">Claim Free Order →</button>
  </div>
</div>

<style>
  @keyframes bounce {
    0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
    40% { transform: translateY(-15px); }
    60% { transform: translateY(-7px); }
  }
</style>

<script>
function closeCongratsModal() {
    const modal = document.getElementById('zeroAmountCongratsModal');
    const content = document.getElementById('congratsModalContent');
    modal.style.opacity = '0';
    content.style.transform = 'scale(0.9)';
    setTimeout(() => { modal.style.display = 'none'; }, 400);
}
let activeOrder = null;
let isDigital = false;
let walletBalance = 0;

function loadCheckoutDetails() {
    const urlParams = new URLSearchParams(window.location.search);
    const type = urlParams.get('type');
    
    // Enforce login
    if (!localStorage.getItem("token")) {
        alert("Please login first to place an order!");
        window.location.href = "login.php";
        return;
    }

    // Load active order depending on type
    if (type === "membership") {
        activeOrder = JSON.parse(localStorage.getItem("gsa_membership_order"));
    } else {
        activeOrder = JSON.parse(localStorage.getItem("gsa_active_order"));
    }

    if (!activeOrder) {
        alert("No active order details found. Returning to cart.");
        window.location.href = "cart.php";
        return;
    }

    isDigital = activeOrder.items && activeOrder.items.every(item => 
        item.id && (
            item.id.toString().startsWith("membership-") || 
            item.id.toString().startsWith("event-") || 
            item.id.toString().startsWith("ticket-")
        )
    );

    if (isDigital) {
        // Auto fill address and hide it
        document.getElementById("checkoutAddressInput").value = "Digital Delivery (Email Upgrade)";
        document.getElementById("shippingAddressContainer").style.display = "none";
    }

    // Prefill phone if available
    const savedPhone = localStorage.getItem("userContact") || "";
    if (savedPhone) {
        document.getElementById("checkoutPhoneInput").value = savedPhone;
    }

    // Render summary list
    const container = document.getElementById("checkoutItemsContainer");
    container.innerHTML = activeOrder.items.map(item => `
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; font-size: 0.9rem;">
        <span style="color: #9aa0b4">${item.name} <strong style="color: #c5a85c">x${item.quantity}</strong></span>
        <span style="font-weight: 600">₹${(item.price * item.quantity).toLocaleString()}</span>
      </div>
    `).join('');

    document.getElementById("checkoutSubtotal").textContent = `₹${activeOrder.price.toLocaleString()}`;

    if (activeOrder.discountAmount > 0) {
        document.getElementById("checkoutCouponRow").style.display = "flex";
        document.getElementById("checkoutCouponDiscount").textContent = `- ₹${activeOrder.discountAmount.toLocaleString()}`;
    }

    if (activeOrder.nxlCoinsUsed > 0) {
        document.getElementById("checkoutNxlRow").style.display = "flex";
        document.getElementById("checkoutNxlUsedText").textContent = `NXL Credits Used (${activeOrder.nxlCoinsUsed} Coins)`;
        document.getElementById("checkoutNxlDiscount").textContent = `- ₹${(activeOrder.nxlCoinDiscount || activeOrder.nxlCoinsUsed).toLocaleString()}`;
    }

    if (activeOrder.gstAmount !== undefined) {
        document.getElementById("checkoutGstAmount").textContent = `₹${activeOrder.gstAmount.toLocaleString()}`;
    } else {
        const subAfterDisc = activeOrder.price - (activeOrder.discountAmount || 0) - (activeOrder.nxlCoinDiscount || 0);
        const calcGst = Math.floor(subAfterDisc * 0.18);
        document.getElementById("checkoutGstAmount").textContent = `₹${calcGst.toLocaleString()}`;
    }

    document.getElementById("checkoutTotalPayable").textContent = `₹${activeOrder.total.toLocaleString()}`;
    document.getElementById("checkoutEarnedCredits").textContent = `${activeOrder.nxlCoinsEarned} NXL Credits`;

    // Fetch user wallet balance
    const userEmail = localStorage.getItem("userEmail");
    fetch(`api/index.php/wallet/balance?email=${encodeURIComponent(userEmail)}`)
        .then(res => res.json())
        .then(data => {
            walletBalance = data.nxlCredits || 0;
        });

    setupInputListeners();

    if (activeOrder.total <= 0) {
        const modal = document.getElementById('zeroAmountCongratsModal');
        modal.style.display = 'flex';
        setTimeout(() => {
            modal.style.opacity = '1';
            document.getElementById('congratsModalContent').style.transform = 'scale(1)';
        }, 50);
    }
}

function setupInputListeners() {
    const addressInput = document.getElementById("checkoutAddressInput");
    const phoneInput = document.getElementById("checkoutPhoneInput");
    const payBtn = document.getElementById("checkoutPayButton");

    function validate() {
        const address = addressInput.value.trim();
        const phone = phoneInput.value.trim();

        if (address && phone) {
            payBtn.disabled = false;
            payBtn.style.background = "linear-gradient(135deg, #c5a85c 0%, #8c7237 100%)";
            payBtn.style.color = "#0b0c10";
            payBtn.style.cursor = "pointer";
            if (activeOrder.total <= 0) {
                payBtn.textContent = "Place Free Order →";
            } else {
                payBtn.textContent = `Pay ₹${activeOrder.total.toLocaleString()} via Razorpay`;
            }
        } else {
            payBtn.disabled = true;
            payBtn.style.background = "#475569";
            payBtn.style.color = "#94a3b8";
            payBtn.style.cursor = "not-allowed";
            payBtn.textContent = isDigital ? "Enter Phone to Pay" : "Fill Shipping Details to Pay";
        }
    }

    addressInput.addEventListener("input", validate);
    phoneInput.addEventListener("input", validate);
    validate(); // Initial run
}

async function handleCheckoutPayment() {
    const address = document.getElementById("checkoutAddressInput").value.trim();
    const phone = document.getElementById("checkoutPhoneInput").value.trim();
    const payBtn = document.getElementById("checkoutPayButton");
    const userEmail = localStorage.getItem("userEmail") || "guest@globalsportsarena.com";
    const userName = localStorage.getItem("userName") || "Player";

    payBtn.disabled = true;
    payBtn.textContent = "Processing Transaction...";

    const orderPayload = {
        total: activeOrder.total,
        subtotal: activeOrder.price || activeOrder.total,
        discountAmount: activeOrder.discountAmount || 0,
        membershipDiscountAmount: activeOrder.membershipDiscountAmount || 0,
        gstAmount: activeOrder.gstAmount || 0,
        paymentStatus: activeOrder.total <= 0 ? "FREE" : "PENDING",
        shippingAddress: address,
        customerPhone: phone,
        nxlCoinsEarned: activeOrder.nxlCoinsEarned || 0,
        nxlCoinsUsed: activeOrder.nxlCoinsUsed || 0,
        items: JSON.stringify(activeOrder.items),
        email: userEmail,
        paymentMethod: "CARD",
        paymentId: "FREE-ORDER-" + Date.now()
    };

    // Bypass Razorpay entirely if the order amount is zero
    if (activeOrder.total <= 0) {
        try {
            const res = await fetch("api/index.php/orders/place", {
                method: "POST",
                headers: { 
                    "Content-Type": "application/json",
                    "Authorization": "Bearer " + localStorage.getItem("token")
                },
                body: JSON.stringify(orderPayload)
            });
            const data = await res.json();
            
            if (window.Cart) window.Cart.clearCart(true); // Clear
            alert(`Order confirmed successfully! You earned ${activeOrder.nxlCoinsEarned} NXL Credits.`);
            
            localStorage.setItem("gsa_last_order", JSON.stringify(data));
            window.location.href = "payment-success.php";
        } catch (err) {
            console.error("Order placement failed", err);
            alert("Failed to complete order. Please try again.");
            payBtn.disabled = false;
        }
        return;
    }

    if (!window.Razorpay) {
        alert("Razorpay SDK not loaded. Check script headers.");
        payBtn.disabled = false;
        return;
    }
    
    // Fetch Razorpay order ID from backend
    let rzpOrderId = null;
    try {
        const orderRes = await fetch("api/index.php/orders/create-razorpay-order", {
            method: "POST",
            headers: { 
                "Content-Type": "application/json",
                "Authorization": "Bearer " + localStorage.getItem("token")
            },
            body: JSON.stringify({ amount: Math.round(activeOrder.total * 100) })
        });
        const orderData = await orderRes.json();
        
        if (orderData.error) {
            alert("Payment Gateway Error: " + orderData.error.description || orderData.error);
            payBtn.disabled = false;
            payBtn.textContent = isDigital ? "Enter Phone to Pay" : "Fill Shipping Details to Pay";
            return;
        }
        if (orderData.id) {
            rzpOrderId = orderData.id;
        }
    } catch (err) {
        console.error("Failed to generate Razorpay order", err);
        alert("Failed to initialize payment gateway. Please check your API keys or network.");
        payBtn.disabled = false;
        payBtn.textContent = isDigital ? "Enter Phone to Pay" : "Fill Shipping Details to Pay";
        return;
    }

    const options = {
        key: "<?php echo RAZORPAY_KEY_ID; ?>",
        amount: Math.round(activeOrder.total * 100), // in paise
        order_id: rzpOrderId,
        currency: "INR",
        name: "GLOBAL SPORTS ARENA",
        description: "Sports Tournaments & Gear",
        image: "assets/logo.png",
        handler: async function (response) {
            const paymentId = response.razorpay_payment_id;
            
            const finalPayload = {
                ...orderPayload,
                paymentStatus: "PAID",
                paymentId: paymentId,
                paymentMethod: "RAZORPAY"
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
                
                // Sync new coin balance in localStorage
                const newBalance = walletBalance - (activeOrder.nxlCoinsUsed || 0) + (activeOrder.nxlCoinsEarned || 0);
                localStorage.setItem("nxlCoins", newBalance);
                
                const urlParams = new URLSearchParams(window.location.search);
                const type = urlParams.get("type");
                if (type === "membership") {
                    const tier = activeOrder.id.replace("membership-", "");
                    localStorage.setItem("userMembership", tier);
                }
                
                alert(`Payment successful! You earned ${activeOrder.nxlCoinsEarned} NXL Credits.`);
                localStorage.setItem("gsa_last_order", JSON.stringify(data));
                window.location.href = "payment-success.php";
            } catch (err) {
                console.error("Failed to sync paid order", err);
                alert("Payment received, but database sync failed. Please contact support.");
            }
        },
        prefill: {
            name: userName,
            email: userEmail,
            contact: phone,
        },
        notes: {
            address: address,
            orderId: activeOrder.id
        },
        theme: {
            color: "#c5a85c",
        },
        modal: {
            ondismiss: function () {
                payBtn.disabled = false;
                setupInputListeners();
            }
        }
    };

    const razorpay = new window.Razorpay(options);
    razorpay.open();
}

document.addEventListener("DOMContentLoaded", function() {
    loadCheckoutDetails();
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
