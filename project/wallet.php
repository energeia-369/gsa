<?php
require_once __DIR__ . '/config/Config.php';
$pageTitle = "GLOBAL SPORTS ARENA | My NXL Wallet";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<link rel="stylesheet" href="assets/css/Wallet.css?v=2">

<style>
/* ── Recharge Modal: Dark Theme ── */
body:not(.light-theme) #rechargeModal .modal-content {
    background: #12131c !important;
    border-color: rgba(197,168,92,0.4) !important;
    color: #f5f6fa !important;
}
body:not(.light-theme) #rechargeModal h2 {
    color: #c5a85c !important;
}
body:not(.light-theme) #rechargeModal .modal-close {
    color: #f5f6fa !important;
}
body:not(.light-theme) #rechargeModal .preset-btn {
    background: rgba(197,168,92,0.12) !important;
    border-color: rgba(197,168,92,0.4) !important;
    color: #f5f6fa !important;
}
body:not(.light-theme) #rechargeModal label,
body:not(.light-theme) #rechargeModal .method-option {
    color: #d0d0d0 !important;
}
body:not(.light-theme) #rechargeModal #rechargeAmountInput {
    background: rgba(255,255,255,0.06) !important;
    color: #f5f6fa !important;
    border-color: rgba(197,168,92,0.3) !important;
}
body:not(.light-theme) #rechargeModal #rechargeAmountInput::placeholder {
    color: #888 !important;
}
body:not(.light-theme) #rechargeModal .cancel-btn {
    color: #f5f6fa !important;
    border-color: rgba(197,168,92,0.4) !important;
}
body:not(.light-theme) #rechargeModal .modal-header {
    border-bottom-color: rgba(197,168,92,0.2) !important;
}
body:not(.light-theme) #rechargeModal .modal-footer {
    border-top-color: rgba(197,168,92,0.2) !important;
}

/* ── Recharge Modal: Light Theme (keep as is) ── */
body.light-theme #rechargeModal .modal-content {
    background: #ffffff !important;
    color: #1a1a1a !important;
}
body.light-theme #rechargeModal label,
body.light-theme #rechargeModal .method-option {
    color: #333 !important;
}

/* --- Mobile Fixes --- */
@media (max-width: 640px) {
    .wallet-container {
        padding: 15px !important;
        margin-top: 20px !important;
        width: 100% !important;
        box-sizing: border-box !important;
    }
    .balance-card {
        padding: 20px !important;
    }
    .balance-amount {
        font-size: 2.5rem !important;
        flex-wrap: wrap;
    }
    .transactions-section {
        padding: 15px !important;
    }
    .transaction-item {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 10px !important;
    }
    .transaction-item .details {
        width: 100%;
        margin-right: 0 !important;
    }
    .transaction-item .amount {
        align-self: flex-end;
    }
}
</style>

<div class="wallet-page" style="background: #0b0c10; color: #f5f6fa; min-height: 100vh;" class="px-4 py-10">
  <div class="wallet-container max-w-3xl mx-auto px-4" style="border: 1px solid rgba(197,168,92,0.15); border-radius: 24px; background: #12131c; padding: 24px;">
    <!-- Header Section -->
    <div class="wallet-header" style="text-align: center; margin-bottom: 30px;">
      <div class="wallet-icon" style="color: #c5a85c; font-size: 3rem; margin-bottom: 10px;">👛</div>
      <h1 style="color: #c5a85c; font-weight: 800; font-size: 2.2rem; margin: 0 0 10px 0;">My NXL Wallet</h1>
      <p style="color: #9aa0b4; margin: 0;">Manage your dynamic loyalty funds and track credit logs in MySQL</p>
    </div>

    <!-- Balance Card -->
    <div class="balance-card" style="position: relative; overflow: hidden; background: linear-gradient(135deg, #1e202c 0%, #0b0c10 100%); border: 1px solid #c5a85c; padding: 30px; border-radius: 16px; text-align: center;">
      <div class="balance-glow" style="position: absolute; top: -50%; left: -50%; width: 200%; height: 200%; background: radial-gradient(circle, rgba(197, 168, 92, 0.15) 0%, transparent 60%); pointer-events: none;"></div>
      <div class="balance-content" style="position: relative; z-index: 1;">
        <span class="balance-label" style="color: #9aa0b4; letter-spacing: 1px; text-transform: uppercase; font-size: 0.85rem; font-weight: 600;">Dynamic NXL Balance</span>
        <div class="balance-amount" style="font-size: 3.5rem; font-weight: 800; margin: 15px 0; display: flex; justify-content: center; align-items: center; gap: 10px;">
          <span class="currency" style="color: #c5a85c;">💎</span>
          <span class="amount" id="walletBalanceVal" style="color: #c5a85c;">0</span>
        </div>
        <p class="balance-subtext" style="color: #9aa0b4; font-size: 0.9rem; margin-bottom: 25px;">Ready to redeem dynamically at sports checkout</p>
        <button class="recharge-btn" onclick="openRechargeModal()" style="background: linear-gradient(135deg, #c5a85c 0%, #8c7237 100%); color: #0b0c10; font-weight: bold; border: none; padding: 14px 28px; border-radius: 8px; font-size: 1rem; cursor: pointer; transition: all 0.3s;">
          <span>+</span> Add Money & Earn NXL
        </button>
      </div>
    </div>

    <!-- Transaction History -->
    <!-- Pending NXL Requests Section -->
    <div class="pending-requests-section" id="pendingRequestsContainer" style="display: none; margin-top: 40px; background: rgba(197, 168, 92, 0.05); border: 1px solid rgba(197, 168, 92, 0.4); border-radius: 16px; padding: 25px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);">
      <h2 style="color: #c5a85c; margin: 0 0 15px 0; font-size: 1.5rem;">⚠️ Pending Payment Requests</h2>
      <div id="pendingRequestsList" style="display: grid; gap: 15px;"></div>
    </div>

    <div class="transactions-section" style="margin-top: 40px; background: #12131c; border: 1px solid rgba(197, 168, 92, 0.2); border-radius: 20px; padding: 30px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);">
      <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(197,168,92,0.15); padding-bottom: 10px;">
        <h2 style="color: #c5a85c; margin: 0; font-size: 1.5rem;">Credit History</h2>
        <span class="transaction-count" id="transactionCountLabel" style="color: #9aa0b4; font-size: 0.9rem;">0 entries recorded</span>
      </div>
      
      <div class="transactions-list" id="walletTransactionsContainer" style="margin-top: 20px; display: grid; gap: 10px;">
        <p style="text-align: center; color: #c5a85c;">Refreshing logs from database...</p>
      </div>
    </div>
  </div>

  <!-- Recharge Modal -->
  <div class="modal-overlay" id="rechargeModal" style="display: none; background: rgba(0,0,0,0.85); position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 99999; justify-content: center; align-items: center;">
    <div class="modal-content" style="background: #ffffff; border: 2px solid rgba(197,168,92,0.5); color: #1a1a1a; max-width: 500px; width: 90%; max-height: 90vh; overflow-y: auto; border-radius: 16px; padding: 25px; box-sizing: border-box; position: relative; box-shadow: 0 20px 60px rgba(0,0,0,0.5);">
      <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(197,168,92,0.3); padding-bottom: 10px; margin-bottom: 20px;">
        <h2 style="color: #c5a85c; margin: 0; font-size: 1.5rem;">Recharge Wallet &amp; Earn NXL</h2>
        <button class="modal-close" onclick="closeRechargeModal()" style="background: none; border: none; color: #1a1a1a; font-size: 1.5rem; cursor: pointer;">✕</button>
      </div>
      
      <div class="modal-body">
        <div class="amount-presets flex flex-wrap gap-2 mb-5">
          <button class="preset-btn" onclick="setPresetAmount(1000)" style="flex: 1; padding: 10px; background: rgba(197,168,92,0.12); border: 1.5px solid rgba(197,168,92,0.5); border-radius: 8px; color: #1a1a1a; font-weight: bold; cursor: pointer; transition: all 0.2s;">₹1,000</button>
          <button class="preset-btn" onclick="setPresetAmount(2000)" style="flex: 1; padding: 10px; background: rgba(197,168,92,0.12); border: 1.5px solid rgba(197,168,92,0.5); border-radius: 8px; color: #1a1a1a; font-weight: bold; cursor: pointer; transition: all 0.2s;">₹2,000</button>
          <button class="preset-btn" onclick="setPresetAmount(5000)" style="flex: 1; padding: 10px; background: rgba(197,168,92,0.12); border: 1.5px solid rgba(197,168,92,0.5); border-radius: 8px; color: #1a1a1a; font-weight: bold; cursor: pointer; transition: all 0.2s;">₹5,000</button>
        </div>
        
        <div class="input-group" style="margin: 20px 0;">
          <label style="display: block; margin-bottom: 8px; font-size: 0.9rem; color: #555;">Enter Amount (₹)</label>
          <input
            type="number"
            id="rechargeAmountInput"
            placeholder="Enter custom amount"
            min="1"
            style="width: 100%; padding: 12px; border: 1.5px solid rgba(197,168,92,0.4); border-radius: 8px; background: #f9f5ec; color: #1a1a1a; outline: none; font-size: 1rem; box-sizing: border-box;"
            oninput="calculateWalletGST()"
          />
          <div id="walletGstDisplay" style="display:none; font-size: 0.9rem; color: #9aa0b4; margin-top: 8px;">+ 18% GST: ₹<span id="walletGstAmount">0</span> | Total Payable: ₹<span id="walletTotalAmount">0</span></div>
          <span style="font-size: 0.8rem; color: #c5a85c; marginTop: 6px; display: block;">
            💡 Conversion: 100rs gives 105 NXL credits (e.g. ₹1000 recharge gives 1050 NXL credits)
          </span>
        </div>
        
        <div class="payment-methods">
          <label style="display: block; margin-bottom: 8px; font-size: 0.9rem; color: #555;">Payment Method</label>
          <div class="method-options" style="display: flex; gap: 15px;">
            <label class="method-option" style="display: flex; align-items: center; gap: 8px; cursor: pointer; color: #1a1a1a;">
              <input
                type="radio"
                name="paymentMethod"
                value="razorpay"
                checked
              />
              <span>🔒 Razorpay Secure</span>
            </label>
          </div>
        </div>
      </div>
      
      <div class="modal-footer" style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 25px; border-top: 1px solid rgba(197,168,92,0.15); padding-top: 15px;">
        <button class="cancel-btn" onclick="closeRechargeModal()" style="background: rgba(197,168,92,0.1); border: 1.5px solid rgba(197,168,92,0.5); color: #1a1a1a; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 600;">Cancel</button>
        <button class="confirm-btn" onclick="triggerRazorpayRecharge()" style="background: linear-gradient(135deg, #c5a85c 0%, #8c7237 100%); color: #0b0c10; font-weight: bold; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer;">
          Proceed to Pay
        </button>
      </div>
    </div>
  </div>
</div>

<script>
const userEmail = localStorage.getItem("userEmail");

document.addEventListener("DOMContentLoaded", function() {
    if (!userEmail) {
        alert("Please login to access your wallet.");
        window.location.href = "login.php";
        return;
    }
    fetchWalletDetails();
    fetchPendingRequests();
});

async function fetchPendingRequests() {
    try {
        const email = localStorage.getItem("userEmail");
        if (!email) return;
        const res = await fetch(`api/index.php/user/nxl-requests?email=${encodeURIComponent(email)}`);
        const data = await res.json();
        
        const container = document.getElementById('pendingRequestsContainer');
        const list = document.getElementById('pendingRequestsList');
        
        if (data.success && data.requests && data.requests.length > 0) {
            container.style.display = 'block';
            list.innerHTML = data.requests.map(req => `
                <div style="background: rgba(0,0,0,0.4); border: 1px solid rgba(197,168,92,0.2); border-radius: 12px; padding: 15px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                    <div>
                        <div style="font-weight: bold; color: #fff; font-size: 1.1rem;">Merchant Request: ${req.merchant_name}</div>
                        <div style="color: #9aa0b4; font-size: 0.9rem; margin-top: 4px;">Requested: ${new Date(req.created_at).toLocaleString()}</div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <div style="font-size: 1.2rem; font-weight: bold; color: #ffca28;">${req.amount} NXL</div>
                        <div style="display: flex; gap: 10px;">
                            <button onclick="approveNxlRequest(${req.id})" style="background: linear-gradient(135deg, #2e7d32 0%, #1b5e20 100%); border: none; padding: 8px 16px; color: #fff; border-radius: 6px; cursor: pointer; font-weight: bold;">Approve</button>
                            <button onclick="rejectNxlRequest(${req.id})" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); padding: 8px 16px; color: #fff; border-radius: 6px; cursor: pointer; font-weight: bold;">Reject</button>
                        </div>
                    </div>
                </div>
            `).join('');
        } else {
            container.style.display = 'none';
            list.innerHTML = '';
        }
    } catch (e) {
        console.error("Error fetching pending requests:", e);
    }
}

async function approveNxlRequest(id) {
    if (!confirm("Are you sure you want to approve this payment?")) return;
    try {
        const email = localStorage.getItem("userEmail");
        const res = await fetch('api/index.php/user/approve-nxl-request', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ request_id: id, email: email })
        });
        const data = await res.json();
        alert(data.message);
        if (data.success) {
            fetchPendingRequests();
            fetchWalletDetails();
        }
    } catch (e) {
        alert("Error approving request.");
    }
}

async function rejectNxlRequest(id) {
    if (!confirm("Are you sure you want to reject this request?")) return;
    try {
        const email = localStorage.getItem("userEmail");
        const res = await fetch('api/index.php/user/reject-nxl-request', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ request_id: id, email: email })
        });
        const data = await res.json();
        alert(data.message);
        if (data.success) fetchPendingRequests();
    } catch (e) {
        alert("Error rejecting request.");
    }
}

async function fetchWalletDetails() {
    const container = document.getElementById("walletTransactionsContainer");
    try {
        // 1. Fetch balance
        const balanceRes = await fetch(`api/index.php/wallet/balance?email=${encodeURIComponent(userEmail)}`);
        const balanceData = await balanceRes.json();
        const currentBalance = balanceData.nxlCredits || 0;
        
        document.getElementById("walletBalanceVal").textContent = currentBalance.toLocaleString();
        localStorage.setItem("nxlCoins", currentBalance);

        // 2. Fetch transaction logs
        const logsRes = await fetch(`api/index.php/wallet/transactions?email=${encodeURIComponent(userEmail)}`);
        const logs = await logsRes.json();

        document.getElementById("transactionCountLabel").textContent = `${logs.length} entries recorded`;

        if (logs.length === 0) {
            container.innerHTML = `
                <div class="empty-state" style="text-align: center; padding: 40px;">
                  <span style="font-size: 3rem;">📭</span>
                  <p style="color: #9aa0b4; margin-top: 10px;">No transaction history found in database.</p>
                </div>
            `;
        } else {
            container.innerHTML = logs.map(tx => {
                const isCredit = tx.type === "EARNED" || tx.type === "ADMIN_ADD" || tx.type.toLowerCase().includes("recharge") || tx.type.toLowerCase().includes("add") || tx.type.toLowerCase().includes("credit");
                
                return `
                    <div 
                      class="transaction-item ${isCredit ? 'credit' : 'debit'}"
                      style="
                        background: rgba(22, 24, 38, 0.5);
                        border: 1px solid rgba(197, 168, 92, 0.1);
                        border-radius: 12px;
                        padding: 15px;
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                      "
                    >
                      <div>
                        <div class="transaction-description" style="font-weight: 600; font-size: 0.95rem; color: #fff;">
                          ${tx.description}
                        </div>
                        <div class="transaction-date" style="font-size: 0.8rem; color: #9aa0b4; margin-top: 4px;">
                          ${new Date(tx.date || tx.created_at).toLocaleString()} • Ref: ${tx.refId || tx.reference_id || 'N/A'}
                        </div>
                      </div>
                      <div 
                        class="transaction-amount" 
                        style=" 
                          font-weight: bold; 
                          font-size: 1.1rem;
                          color: ${isCredit ? '#22c55e' : '#ef4444'};
                        "
                      >
                        ${isCredit ? "+" : "-"} ${Math.abs(tx.amount)} NXL
                      </div>
                    </div>
                `;
            }).join('');
        }
    } catch(err) {
        console.error(err);
        container.innerHTML = `<p style="text-align: center; color: #ef4444;">Failed to sync wallet data with database.</p>`;
    }
}

function openRechargeModal() {
    document.getElementById("rechargeModal").style.display = "flex";
}

function closeRechargeModal() {
    document.getElementById("rechargeModal").style.display = "none";
}

function setPresetAmount(amt) {
    document.getElementById("rechargeAmountInput").value = amt;
    calculateWalletGST();
}

let rechargeTotalAmount = 0;

function calculateWalletGST() {
    const amountVal = document.getElementById("rechargeAmountInput").value;
    const amount = parseFloat(amountVal);
    const gstDisplay = document.getElementById("walletGstDisplay");
    
    if (!isNaN(amount) && amount > 0) {
        const gst = Math.floor(amount * 0.18);
        rechargeTotalAmount = amount + gst;
        document.getElementById("walletGstAmount").textContent = gst.toLocaleString('en-IN');
        document.getElementById("walletTotalAmount").textContent = rechargeTotalAmount.toLocaleString('en-IN');
        gstDisplay.style.display = "block";
    } else {
        rechargeTotalAmount = 0;
        gstDisplay.style.display = "none";
    }
}

async function triggerRazorpayRecharge() {
    const amountVal = document.getElementById("rechargeAmountInput").value;
    const amount = parseFloat(amountVal);
    
    if (rechargeTotalAmount <= 0) {
        alert("Please enter a valid amount");
        return;
    }

    if (!window.Razorpay) {
        alert("Razorpay SDK not loaded. Please verify your internet connection.");
        return;
    }

    // 1. Fetch secure Razorpay Order ID from backend
    let orderId = null;
    try {
        const orderRes = await fetch("api/index.php/orders/create-razorpay-order", {
            method: "POST",
            headers: { 
                "Content-Type": "application/json",
                "Authorization": "Bearer " + localStorage.getItem("token")
            },
            body: JSON.stringify({ amount: rechargeTotalAmount })
        });
        const orderData = await orderRes.json();
        
        if (orderData.id) {
            orderId = orderData.id;
        } else {
            console.error("Order generation failed", orderData);
            alert("System error initializing payment. Please try again.");
            return;
        }
    } catch(err) {
        console.error("Network error fetching order ID", err);
        alert("Network error connecting to payment gateway.");
        return;
    }

    // 2. Initialize Razorpay checkout
    const options = {
        key: "<?php echo RAZORPAY_KEY_ID; ?>",
        amount: Math.round(rechargeTotalAmount * 100), // convert to paise
        currency: "INR",
        name: "ENERGEIA'S Global Ventures",
        description: `Add Money & Earn NXL (Recharge ₹${amount})`,
        image: "https://cdn-icons-png.flaticon.com/512/857/857455.png",
        order_id: orderId,
        handler: async function (response) {
            try {
                const payload = {
                    email: userEmail,
                    amount: amount,
                    paymentId: response.razorpay_payment_id
                };

                const res = await fetch("api/index.php/wallet/recharge", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Authorization": "Bearer " + localStorage.getItem("token")
                    },
                    body: JSON.stringify(payload)
                });
                
                const data = await res.json();
                alert(`₹${amount} recharged successfully! You earned ${data.creditsEarned || 0} NXL Credits.`);
                
                closeRechargeModal();
                document.getElementById("rechargeAmountInput").value = "";
                
                await fetchWalletDetails();
            } catch (err) {
                console.error("Recharge sync failed", err);
                alert("Payment completed, but failed to sync credits to database.");
            }
        },
        prefill: {
            email: userEmail,
        },
        theme: {
            color: "#c5a85c",
        }
    };

    const rzp = new window.Razorpay(options);
    rzp.open();
}
</script>



<?php require_once __DIR__ . '/includes/footer.php'; ?>
