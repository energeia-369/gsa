<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'merchant') {
    header("Location: login.php");
    exit;
}
$pageTitle = "GLOBAL SPORTS ARENA | Issue NXL Credits";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<link rel="stylesheet" href="assets/css/Merchant.css">

<style>
/* Additional styles for the issue coins page */
.issue-coins-page {
    min-height: 80vh;
    background: linear-gradient(135deg, #0b0c10 0%, #1a1c2e 100%);
    padding: 40px 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.issue-coins-container {
    max-width: 550px;
    width: 100%;
    margin: 0 auto;
}

.issue-coins-card {
    background: rgba(22, 24, 38, 0.9);
    border: 1px solid rgba(197, 168, 92, 0.3);
    border-radius: 20px;
    padding: 40px;
    backdrop-filter: blur(10px);
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
}

.issue-coins-header {
    text-align: center;
    margin-bottom: 35px;
}

.issue-coins-icon {
    font-size: 4rem;
    margin-bottom: 10px;
    display: block;
}

.issue-coins-title {
    color: #c5a85c;
    font-size: 2rem;
    font-weight: 700;
    letter-spacing: 1px;
    margin-bottom: 8px;
}

.issue-coins-subtitle {
    color: #9aa0b4;
    font-size: 1rem;
    line-height: 1.6;
}

.form-group {
    margin-bottom: 25px;
}

.form-label {
    display: block;
    color: #c5a85c;
    font-weight: 500;
    margin-bottom: 8px;
    font-size: 0.95rem;
    letter-spacing: 0.5px;
}

.form-label i {
    margin-right: 8px;
}

.merchant-input {
    width: 100%;
    padding: 14px 18px;
    background: rgba(11, 12, 16, 0.6);
    border: 2px solid rgba(197, 168, 92, 0.2);
    border-radius: 12px;
    color: #f5f6fa;
    font-size: 1rem;
    transition: all 0.3s ease;
    outline: none;
}

.merchant-input:focus {
    border-color: #c5a85c;
    background: rgba(11, 12, 16, 0.8);
    box-shadow: 0 0 20px rgba(197, 168, 92, 0.1);
}

.merchant-input::placeholder {
    color: #4a4d62;
}

.merchant-input::-webkit-outer-spin-button,
.merchant-input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

.merchant-input[type="number"] {
    -moz-appearance: textfield;
}

.btn-issue {
    width: 100%;
    padding: 16px;
    background: linear-gradient(135deg, #c5a85c 0%, #a8893e 100%);
    color: #0b0c10;
    border: none;
    border-radius: 12px;
    font-size: 1.1rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    letter-spacing: 1px;
    text-transform: uppercase;
    margin-top: 10px;
}

.btn-issue:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(197, 168, 92, 0.3);
}

.btn-issue:active {
    transform: translateY(0);
}

.btn-issue i {
    margin-left: 10px;
}

.merchant-info-box {
    background: rgba(197, 168, 92, 0.05);
    border: 1px solid rgba(197, 168, 92, 0.1);
    border-radius: 12px;
    padding: 15px 20px;
    margin-top: 25px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.merchant-info-box i {
    color: #c5a85c;
    font-size: 1.5rem;
}

.merchant-info-text {
    color: #9aa0b4;
    font-size: 0.9rem;
    line-height: 1.5;
}

.merchant-info-text strong {
    color: #f5f6fa;
}

.balance-display {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    background: rgba(197, 168, 92, 0.05);
    border-radius: 12px;
    border: 1px solid rgba(197, 168, 92, 0.1);
    margin-bottom: 25px;
}

.balance-label {
    color: #9aa0b4;
    font-size: 0.95rem;
}

.balance-amount {
    color: #c5a85c;
    font-size: 1.3rem;
    font-weight: 700;
}

.balance-amount i {
    margin-right: 8px;
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .issue-coins-card {
        padding: 25px;
    }
    
    .issue-coins-title {
        font-size: 1.5rem;
    }
    
    .balance-display {
        flex-direction: column;
        gap: 5px;
        text-align: center;
    }
}
</style>

<div class="issue-coins-page">
    <div class="issue-coins-container w-full max-w-xl mx-auto px-4">
        <div class="issue-coins-card">
            <div class="issue-coins-header">
                <span class="issue-coins-icon">💎</span>
                <h1 class="issue-coins-title">Issue NXL Credits</h1>
                <p class="issue-coins-subtitle">
                    Reward your loyal customers with blockchain-verified NXL Credits
                </p>
            </div>

            <!-- Balance Display -->
            <div class="balance-display">
                <span class="balance-label">
                    <i class="fas fa-wallet"></i> Available Balance
                </span>
                <span class="balance-amount" id="merchantBalanceUI">
                    <i class="fas fa-coins"></i> Loading...
                </span>
            </div>

            <form id="issueCoinsForm">
                <div class="form-group" style="position: relative;">
                    <label class="form-label" for="customerEmail">
                        <i class="fas fa-envelope"></i> Customer Email
                    </label>
                    <input 
                        type="email" 
                        id="customerEmail" 
                        placeholder="customer@example.com" 
                        class="merchant-input"
                        autocomplete="off"
                        required
                    >
                    <div id="customDropdown" style="display: none; position: absolute; top: 100%; left: 0; right: 0; background: #12131c; border: 1px solid #c5a85c; border-radius: 8px; margin-top: 5px; max-height: 200px; overflow-y: auto; z-index: 100; box-shadow: 0 10px 30px rgba(0,0,0,0.5);"></div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="coinAmount">
                        <i class="fas fa-coins"></i> Coins to Issue
                    </label>
                    <input 
                        type="number" 
                        id="coinAmount" 
                        placeholder="Enter amount (e.g. 100)" 
                        class="merchant-input"
                        min="1"
                        required
                    >
                </div>

                <div class="form-group" style="margin-bottom: 10px;">
                    <label class="form-label" for="issueReason">
                        <i class="fas fa-pencil-alt"></i> Reason (Optional)
                    </label>
                    <input 
                        type="text" 
                        id="issueReason" 
                        placeholder="e.g. Tournament winner, Loyalty reward" 
                        class="merchant-input"
                    >
                </div>

                <button type="submit" class="btn-issue">
                    <i class="fas fa-gem"></i> Issue Coins <i class="fas fa-arrow-right"></i>
                </button>
            </form>

            <!-- Merchant Info -->
            <div class="merchant-info-box">
                <i class="fas fa-info-circle"></i>
                <div class="merchant-info-text">
                    <strong>Merchant ID:</strong> <?php echo htmlspecialchars($_SESSION['merchant_id'] ?? 'N/A'); ?> &bull;
                    <strong>Store:</strong> <?php echo htmlspecialchars($_SESSION['merchant_name'] ?? 'Global Sports'); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let allUsers = [];

document.addEventListener('DOMContentLoaded', async function() {
    // 1. Check merchant auth
    const token = localStorage.getItem("token");
    const role = localStorage.getItem("userRole");
    const merchantEmail = localStorage.getItem("userEmail");
    
    if (!token || role !== "MERCHANT") {
        window.location.href = "login.php";
        return;
    }
    
    // 2. Fetch Merchant Balance
    try {
        const balanceRes = await fetch(`api/index.php/wallet/balance?email=${encodeURIComponent(merchantEmail)}`);
        const balanceData = await balanceRes.json();
        const balUI = document.getElementById("merchantBalanceUI");
        if (balanceData.nxlCredits !== undefined) {
            balUI.innerHTML = `<i class="fas fa-coins"></i> ${balanceData.nxlCredits.toLocaleString()} NXL`;
        } else {
            balUI.innerHTML = `<i class="fas fa-coins"></i> 0 NXL`;
        }
    } catch(e) {
        console.error("Failed to load merchant balance", e);
        document.getElementById("merchantBalanceUI").innerHTML = `<i class="fas fa-coins"></i> Error`;
    }

    // 3. Fetch users for dropdown
    try {
        const res = await fetch('api/index.php/user/all');
        const data = await res.json();
        if (Array.isArray(data)) {
            // Only show regular customers, exclude admins and merchants
            allUsers = data.filter(u => !u.role || u.role.toUpperCase() === 'USER');
        }
    } catch (e) {
        console.error("Failed to load users for autocomplete", e);
    }
});

const customerEmailInput = document.getElementById('customerEmail');
const customDropdown = document.getElementById('customDropdown');

function renderDropdown() {
    const val = customerEmailInput.value.toLowerCase().trim();
    customDropdown.innerHTML = '';
    
    const matches = val ? allUsers.filter(u => 
        (u.full_name && u.full_name.toLowerCase().includes(val)) || 
        (u.email && u.email.toLowerCase().includes(val))
    ) : allUsers;
    
    if (matches.length > 0) {
        matches.forEach(u => {
            const item = document.createElement('div');
            item.style.cssText = 'padding: 10px 15px; color: #f5f6fa; cursor: pointer; border-bottom: 1px solid rgba(197, 168, 92, 0.1); transition: background 0.2s;';
            item.innerHTML = `<strong style="color:#c5a85c">${u.full_name || 'User'}</strong><br><small style="color:#9aa0b4">${u.email}</small>`;
            
            // Hover effect
            item.addEventListener('mouseenter', () => item.style.background = 'rgba(197, 168, 92, 0.1)');
            item.addEventListener('mouseleave', () => item.style.background = 'transparent');
            
            // Click to select
            item.addEventListener('click', (e) => {
                e.stopPropagation(); // prevent hiding immediately
                customerEmailInput.value = u.email;
                customDropdown.style.display = 'none';
            });
            
            customDropdown.appendChild(item);
        });
        customDropdown.style.display = 'block';
    } else {
        customDropdown.style.display = 'none';
    }
}

customerEmailInput.addEventListener('input', renderDropdown);
customerEmailInput.addEventListener('focus', renderDropdown);

// Hide dropdown when clicking outside
document.addEventListener('click', function(e) {
    if (e.target !== customerEmailInput && e.target !== customDropdown) {
        customDropdown.style.display = 'none';
    }
});

document.getElementById('issueCoinsForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const email = document.getElementById('customerEmail').value;
    const amount = document.getElementById('coinAmount').value;
    const reason = document.getElementById('issueReason').value || 'No reason provided';
    
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = 'ISSUING...';
    submitBtn.disabled = true;

    try {
        const merchantEmail = localStorage.getItem("userEmail");
        const res = await fetch('api/index.php/user/credits/issue', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email, amount, reason, merchantEmail })
        });
        const data = await res.json();
        
        if (data.success) {
            // Update merchant UI balance
            const balUI = document.getElementById("merchantBalanceUI");
            const currentBalText = balUI.innerText.replace(/[^0-9]/g, '');
            if (currentBalText) {
                const newBal = parseInt(currentBalText) - parseInt(amount);
                balUI.innerHTML = `<i class="fas fa-coins"></i> ${newBal.toLocaleString()} NXL`;
            }
            // Show a stylish confirmation
            const modal = document.createElement('div');
            modal.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.8);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 9999;
                backdrop-filter: blur(5px);
            `;
            
            modal.innerHTML = `
                <div style="
                    background: rgba(22, 24, 38, 0.95);
                    border: 2px solid #c5a85c;
                    border-radius: 20px;
                    padding: 40px;
                    max-width: 400px;
                    text-align: center;
                    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.8);
                ">
                    <div style="font-size: 3rem; margin-bottom: 10px;">✅</div>
                    <h2 style="color: #c5a85c; margin-bottom: 10px;">Coins Issued!</h2>
                    <p style="color: #9aa0b4; margin-bottom: 5px;">
                        <strong style="color: #f5f6fa;">${amount}</strong> NXL Credits sent to
                    </p>
                    <p style="color: #c5a85c; font-weight: 600; margin-bottom: 15px;">
                        ${email}
                    </p>
                    <p style="color: #4a4d62; font-size: 0.9rem; margin-bottom: 20px;">
                        Reason: ${reason}
                    </p>
                    <button id="closeModalBtn" style="
                        background: #c5a85c;
                        color: #0b0c10;
                        border: none;
                        padding: 12px 30px;
                        border-radius: 10px;
                        font-weight: 700;
                        cursor: pointer;
                        transition: all 0.3s ease;
                    " onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                        Great!
                    </button>
                </div>
            `;
            
            document.body.appendChild(modal);
            
            document.getElementById('closeModalBtn').addEventListener('click', function() {
                modal.remove();
            });
            
            // Close modal on outside click
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.remove();
                }
            });
            
            document.getElementById('issueCoinsForm').reset();
        } else {
            alert('Failed to issue coins: ' + (data.message || 'Unknown error'));
        }
    } catch (err) {
        console.error(err);
        alert('An error occurred. Please try again.');
    } finally {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>