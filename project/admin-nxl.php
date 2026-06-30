<?php
$pageTitle = "GLOBAL SPORTS ARENA | NXL Management";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
<link rel="stylesheet" href="assets/css/AdminDashboard.css?v=7">
<div class="admin-dashboard px-4 sm:px-8 py-10" style="background: #0b0c10; color: #f5f6fa; min-height: 100vh;">
  <?php require_once __DIR__ . '/includes/admin_navbar.php'; ?>
  
  <div class="admin-header" style="border-bottom: 1px solid rgba(197, 168, 92, 0.2); padding-bottom: 20px;">
    <h1>💎 NXL Management</h1>
    <p>Manage NXL wallets, process top-ups, and send rewards.</p>
  </div>

  <!-- Loading Overlay -->
  <div id="adminGlobalLoading" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(11,12,16,0.8); z-index: 9999; justify-content: center; align-items: center; color: #c5a85c; font-size: 1.5rem; font-weight: bold;">
    ? Processing request...
  </div>

  <div class="admin-content" style="display: block; margin-top: 40px;">
    <style>
      .account-tab {
        background: transparent;
        border: 1px solid #c5a85c;
        color: #f5f6fa;
        padding: 10px 25px;
        border-radius: 50px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
      }
      .account-tab:hover {
        background: rgba(197, 168, 92, 0.1);
      }
      .account-tab.active {
        background: #c5a85c;
        color: #0b0c10;
        border-color: #c5a85c;
      }
      .account-section {
        display: block;
        transition: opacity 0.3s ease;
      }
    </style>

    <!-- Centered Pill Navbar -->
    <div style="display: flex; justify-content: center; gap: 15px; margin-bottom: 40px; flex-wrap: wrap;">
      <button class="account-tab active" onclick="filterNxl('adjustment', this)">NXL Wallet Adjustment</button>
      <button class="account-tab" onclick="filterNxl('reward', this)">Send NXL Reward to User</button>
    </div>

    <!-- Sections -->
    <div id="sectionAdjustment" class="account-section" style="max-width: 600px; margin: 0 auto;">
      <div style="background: #12131c; border: 1px solid rgba(197,168,92,0.15); padding: 35px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
        <h2 style="color: #c5a85c; margin: 0 0 10px 0; font-size: 1.5rem;">💸 NXL Wallet Adjustment</h2>
        <p style="color: #9aa0b4; font-size: 0.9rem; margin: 0 0 30px 0;">Manually adjust loyalty balances for specific registered users in MySQL</p>

        <form id="walletAdjustForm" style="display: flex; flex-direction: column; gap: 20px;">
          <div>
            <label style="display: block; font-size: 0.9rem; color: #9aa0b4; margin-bottom: 8px;">User Email Address *</label>
            <input type="email" id="adjustEmail" placeholder="user@globalsportsarena.com" style="width: 100%; padding: 12px 15px; border: 1px solid rgba(197,168,92,0.2); border-radius: 8px; background: #0b0c10; color: #fff; box-sizing: border-box;" required />
          </div>
          <div>
            <label style="display: block; font-size: 0.9rem; color: #9aa0b4; margin-bottom: 8px;">Coins Amount *</label>
            <input type="number" id="adjustAmount" placeholder="50" style="width: 100%; padding: 12px 15px; border: 1px solid rgba(197,168,92,0.2); border-radius: 8px; background: #0b0c10; color: #fff; box-sizing: border-box;" required />
          </div>
          <div>
            <label style="display: block; font-size: 0.9rem; color: #9aa0b4; margin-bottom: 12px;">Select Action *</label>
            <div style="display: flex; gap: 25px;">
              <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; color: #22c55e; font-weight: 500;">
                <input type="radio" name="adjustAction" value="ADD" checked />
                ? Credit / Add Coins
              </label>
              <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; color: #ef4444; font-weight: 500;">
                <input type="radio" name="adjustAction" value="SUBTRACT" />
                ? Debit / Deduct Coins
              </label>
            </div>
          </div>
          <button type="submit" style="background: linear-gradient(135deg, #c5a85c 0%, #8c7237 100%); color: #0b0c10; border: none; padding: 15px; border-radius: 8px; font-weight: bold; cursor: pointer; font-size: 1rem; margin-top: 10px;">
            Process Adjustment
          </button>
        </form>
      </div>
    </div>


    <div id="sectionReward" class="account-section" style="display: none; max-width: 600px; margin: 0 auto;">
      <div style="background: #12131c; border: 1px solid rgba(197,168,92,0.15); padding: 35px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
        <h2 style="color: #c5a85c; margin: 0 0 10px 0; font-size: 1.5rem;">🎁 Send NXL Reward to User</h2>
        <p style="color: #9aa0b4; font-size: 0.9rem; margin: 0 0 30px 0; line-height: 1.5;">Personally reward a user with NXL credits � for winning, participation, loyalty, or promotions.</p>

        <form id="rewardForm" style="display: flex; flex-direction: column; gap: 20px;">
          <div>
            <label style="display: block; font-size: 0.85rem; color: #c5a85c; margin-bottom: 8px; font-weight: bold;">STEP 1 � Select Recipient User</label>
            <select id="rewardUserId" style="width: 100%; padding: 12px 15px; border: 1px solid rgba(197,168,92,0.2); border-radius: 8px; background: #0b0c10; color: #fff; box-sizing: border-box; cursor: pointer;" required>
              <option value="">� Choose a registered user �</option>
            </select>
          </div>
          <div>
            <label style="display: block; font-size: 0.85rem; color: #c5a85c; margin-bottom: 8px; font-weight: bold;">STEP 2 � Enter Custom NXL Reward Amount ??</label>
            <input type="number" id="rewardAmount" min="1" placeholder="Enter custom amount (e.g. 750)" style="width: 100%; padding: 12px 15px; border: 1px solid rgba(197,168,92,0.2); border-radius: 8px; background: #0b0c10; color: #fff; box-sizing: border-box;" required />
          </div>
          <div>
            <label style="display: block; font-size: 0.85rem; color: #c5a85c; margin-bottom: 8px; font-weight: bold;">STEP 3 � Reward Category</label>
            <select id="rewardReason" style="width: 100%; padding: 12px 15px; border: 1px solid rgba(197,168,92,0.2); border-radius: 8px; background: #0b0c10; color: #fff; box-sizing: border-box; cursor: pointer;" required>
              <option value="LOYALTY_REWARD">🎁 Loyalty Reward Bonus</option>
              <option value="WIN_BONUS">🏆 Tournament Win Bonus</option>
              <option value="REFERRAL_BONUS">🤝 Referral Bonus Credit</option>
              <option value="PARTICIPATION_BONUS">🎟️ Event Participation Bonus</option>
              <option value="SPECIAL_GIFT">✨ Special Gift from GLOBAL SPORTS ARENA</option>
              <option value="PROMOTIONAL_CREDIT">📢 Promotional Credit</option>
            </select>
          </div>
          <div>
            <label style="display: block; font-size: 0.85rem; color: #c5a85c; margin-bottom: 8px; font-weight: bold;">STEP 4 � Personal Message (Optional)</label>
            <textarea id="rewardMessage" placeholder="e.g. Congratulations on winning the Cricket Pro League finals! 🎉" rows="4" style="width: 100%; padding: 12px 15px; border: 1px solid rgba(197,168,92,0.2); border-radius: 8px; background: #0b0c10; color: #fff; box-sizing: border-box; resize: vertical; font-family: monospace;"></textarea>
          </div>
          <button type="submit" style="background: linear-gradient(135deg, #c5a85c 0%, #8c7237 100%); color: #0b0c10; border: none; padding: 15px; border-radius: 8px; font-weight: bold; cursor: pointer; font-size: 1rem; margin-top: 10px;">
            🚀 Send NXL Reward Now
          </button>
        </form>

        <!-- Reward Dispatch History -->
        <div id="rewardHistorySection" style="display: none; margin-top: 30px;">
          <h3 style="color: #c5a85c; margin: 0 0 14px 0; font-size: 1.1rem; border-top: 1px solid rgba(197,168,92,0.15); padding-top: 18px;">
            📜 Reward Dispatch History (This Session)
          </h3>
          <div id="rewardHistoryList" style="display: grid; gap: 10px;"></div>
        </div>

      </div>
    </div>
  </div>
</div>

<script>
let allUsers = [];
let rewardHistory = [];

document.addEventListener("DOMContentLoaded", function() {
    const token = localStorage.getItem("token");
    const role = localStorage.getItem("userRole");

    if (!token || role !== "ADMIN") {
        alert("Access Denied: Admin login required!");
        window.location.href = "login.php";
        return;
    }

    loadDashboardData();

    // Setup forms
    const wf = document.getElementById("walletAdjustForm");
    if (wf) wf.addEventListener("submit", handleAdjustWallet);

    // Setup Reward Form Submission
    const rf = document.getElementById("rewardForm");
    if (rf) rf.addEventListener("submit", handleSendReward);
});

function showLoading(show) {
    const loader = document.getElementById("adminGlobalLoading");
    if (loader) loader.style.display = show ? "flex" : "none";
}

async function loadDashboardData() {
    showLoading(true);
    try {
        const token = localStorage.getItem("token");

        // Fetch Users
        const usersRes = await fetch("api/index.php/user/all", {
            headers: { "Authorization": "Bearer " + token }
        });
        allUsers = await usersRes.json();
        
        populateRewardUserDropdown();

    } catch (err) {
        console.error("Dashboard Load Error:", err);
    } finally {
        showLoading(false);
    }
}

function populateRewardUserDropdown() {
    const dropdown = document.getElementById("rewardUserId");
    if (!dropdown) return;
    
    // Keep placeholder
    dropdown.innerHTML = '<option value="">� Choose a registered user �</option>';
    
    allUsers.filter(u => u.role !== "ADMIN").forEach(u => {
        const option = document.createElement("option");
        option.value = u.id;
        option.textContent = `${u.full_name || u.fullName || 'User'} (${u.email})`;
        dropdown.appendChild(option);
    });
}

// Adjust Wallet Form handler
async function handleAdjustWallet(e) {
    e.preventDefault();
    const token = localStorage.getItem("token");
    const email = document.getElementById("adjustEmail").value;
    const amount = parseInt(document.getElementById("adjustAmount").value);
    const action = document.querySelector('input[name="adjustAction"]:checked').value;

    showLoading(true);
    try {
        const res = await fetch("api/index.php/wallet/admin/adjust", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Authorization": "Bearer " + token
            },
            body: JSON.stringify({ email, amount, action })
        });
        const data = await res.json();
        if (data.error) throw new Error(data.error);

        alert(`Success! Successfully processed adjustment of ${amount} NXL credits for ${email}.`);
        document.getElementById("walletAdjustForm").reset();
    } catch(err) {
        console.error(err);
        alert("Failed to process adjustment. Please verify user email address is valid.");
    } finally {
        showLoading(false);
    }
}

// Send NXL Reward Form handler
async function handleSendReward(e) {
    e.preventDefault();
    const token = localStorage.getItem("token");
    const userId = document.getElementById("rewardUserId").value;
    const amount = parseInt(document.getElementById("rewardAmount").value);
    const reason = document.getElementById("rewardReason").value;
    const message = document.getElementById("rewardMessage").value;

    const user = allUsers.find(u => String(u.id) === userId);
    if (!user) {
        alert("Please select a valid user.");
        return;
    }

    const reasonLabels = {
        LOYALTY_REWARD: "Loyalty Reward Bonus",
        WIN_BONUS: "Tournament Win Bonus",
        REFERRAL_BONUS: "Referral Bonus Credit",
        PARTICIPATION_BONUS: "Event Participation Bonus",
        SPECIAL_GIFT: "Special Gift from GLOBAL SPORTS ARENA",
        PROMOTIONAL_CREDIT: "Promotional Credit",
    };

    showLoading(true);
    try {
        const res = await fetch("api/index.php/wallet/admin/adjust", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Authorization": "Bearer " + token
            },
            body: JSON.stringify({ email: user.email, amount: amount, action: "ADD" })
        });
        const data = await res.json();
        if (data.error) throw new Error(data.error);

        // Add to history
        const newEntry = {
            id: Date.now(),
            userName: user.fullName || user.full_name || 'User',
            email: user.email,
            amount: amount,
            reason: reasonLabels[reason] || reason,
            message: message,
            sentAt: new Date().toLocaleString()
        };
        rewardHistory.unshift(newEntry);
        renderRewardHistory();

        alert(`✅ Success! ${amount} NXL credits sent to ${user.email} as "${reasonLabels[reason]}"`);
        document.getElementById("rewardForm").reset();
        
    } catch(err) {
        console.error(err);
        alert("Failed to send reward.");
    } finally {
        showLoading(false);
    }
}

function renderRewardHistory() {
    const container = document.getElementById("rewardHistoryList");
    const section = document.getElementById("rewardHistorySection");
    
    if (rewardHistory.length === 0) {
        section.style.display = "none";
        return;
    }

    section.style.display = "block";
    container.innerHTML = rewardHistory.map(entry => `
        <div style="background: #0b0c10; border: 1px solid rgba(34,197,94,0.2); border-left: 3px solid #22c55e; border-radius: 10px; padding: 12px 15px; font-size: 0.82rem;">
          <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
              <div style="font-weight: 700; color: #f5f6fa; margin-bottom: 3px;">${entry.userName}</div>
              <div style="color: #9aa0b4;">✉️ ${entry.email}</div>
              <div style="color: #9aa0b4; margin-top: 3px;">📌 ${entry.reason}</div>
              ${entry.message ? `<div style="color: #9aa0b4; margin-top: 3px; font-style: italic;">💬 "${entry.message}"</div>` : ''}
            </div>
            <div style="text-align: right; flex-shrink: 0; margin-left: 12px;">
              <div style="color: #22c55e; font-weight: 800; font-size: 1.1rem;">+${entry.amount} 💎</div>
              <div style="color: #9aa0b4; font-size: 0.75rem; margin-top: 4px;">${entry.sentAt}</div>
            </div>
          </div>
        </div>
    `).join('');
}

function filterNxl(type, btnElement) {
    // Update active button styling
    const buttons = document.querySelectorAll('.account-tab');
    buttons.forEach(btn => btn.classList.remove('active'));
    if (btnElement) {
        btnElement.classList.add('active');
    }

    // Hide all
    document.getElementById('sectionAdjustment').style.display = 'none';
    document.getElementById('sectionReward').style.display = 'none';

    // Show specific
    if (type === 'adjustment') {
        document.getElementById('sectionAdjustment').style.display = 'block';
    } else if (type === 'reward') {
        document.getElementById('sectionReward').style.display = 'block';
    }
}
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
