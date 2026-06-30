<?php
$pageTitle = "GLOBAL SPORTS ARENA | My Credits";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<link rel="stylesheet" href="assets/css/Credits.css?v=2">

<div class="credits-page">
  <!-- Hero Banner -->
  <div class="credits-hero">
    <div class="credits-hero-overlay"></div>
    <div class="credits-hero-content">
      <div class="hero-icon">💰</div>
      <h1>My <span class="highlight">Credits</span></h1>
      <p>Your loyalty rewards and earned credits at one place</p>
    </div>
  </div>

  <!-- Credits Summary Cards -->
  <div class="credits-summary">
    <div class="summary-card credits-card">
      <div class="card-icon">💎</div>
      <div class="card-content">
        <h3 id="availableCredits">1,250</h3>
        <p>Available Credits</p>
        <div class="card-progress">
          <div class="progress-bar" id="creditsProgressBar" style="width: 50%;"></div>
        </div>
        <span class="card-label">1 Credit = ₹1 Value</span>
      </div>
    </div>

    <div class="summary-card points-card">
      <div class="card-icon">🛍️</div>
      <div class="card-content">
        <h3 id="totalOrders">0</h3>
        <p>Total Orders</p>
        <span class="card-label">Shop more to earn credits!</span>
      </div>
    </div>

    <div class="summary-card tier-card">
      <div class="card-icon">🏆</div>
      <div class="card-content">
        <h3 id="memberTier">Silver Tier</h3>
        <p>Membership Level</p>
        <div class="card-progress">
          <div class="progress-bar" id="tierProgressBar" style="width: 50%;"></div>
        </div>
        <span class="card-label" id="tierProgressLabel">1250/2500 to Gold</span>
      </div>
    </div>
  </div>

  <!-- Next Milestone -->
  <div class="next-milestone">
    <div class="milestone-content">
      <span class="milestone-icon">🎯</span>
      <div class="milestone-info">
        <h4 id="nextMilestoneName">Next Milestone: Free Event Ticket</h4>
        <p id="nextMilestoneRemaining">Need 250 more credits to unlock</p>
      </div>
      <div class="milestone-progress">
        <div class="progress-circle">
          <svg viewBox="0 0 36 36">
            <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" 
                  fill="none" stroke="#E0E0E0" stroke-width="3"/>
            <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" 
                  fill="none" stroke="#D4AF37" stroke-width="3"
                  id="milestoneCirclePath"
                  stroke-dasharray="50, 100"/>
          </svg>
          <span id="milestonePercentText">50%</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Rewards Store -->
  <div class="rewards-section">
    <div class="section-header">
      <h2>🎁 Rewards Store</h2>
      <p>Redeem your credits for exciting rewards</p>
    </div>
    <div class="rewards-grid" id="rewardsGrid">
      <!-- Rewards rendered dynamically via JS -->
    </div>
  </div>

  <!-- Ways to Earn -->
  <div class="earn-section">
    <div class="section-header">
      <h2>💪 Ways to Earn Credits</h2>
      <p>Complete these actions to earn more credits</p>
    </div>
    <div class="earn-grid">
      <div class="earn-card">
        <div class="earn-icon">🎫</div>
        <h4>Book Events</h4>
        <p>Earn 5% back in credits</p>
        <button onclick="window.location.href='sports-categories.php'" class="earn-btn">Book Now</button>
      </div>
      <div class="earn-card">
        <div class="earn-icon">🛍️</div>
        <h4>Shop Products</h4>
        <p>Earn 3% back in credits</p>
        <button onclick="window.location.href='products.php'" class="earn-btn">Shop Now</button>
      </div>
      <div class="earn-card">
        <div class="earn-icon">👥</div>
        <h4>Refer Friends</h4>
        <p>Earn 100 credits per referral</p>
        <button class="earn-btn" onclick="alert('Referral program details coming soon!')">Invite Now</button>
      </div>
      <div class="earn-card">
        <div class="earn-icon">⭐</div>
        <h4>Write Reviews</h4>
        <p>Earn 25 credits per review</p>
        <button class="earn-btn" onclick="alert('Reviews module coming soon!')">Write Review</button>
      </div>
    </div>
  </div>

  <!-- Transaction History -->
  <div class="transactions-section">
    <div class="section-header">
      <h2>📜 Transaction History</h2>
      <p>Track your credits and points activity</p>
    </div>
    <div class="transactions-list" id="transactionsList">
      <!-- Transactions rendered dynamically via JS -->
    </div>
  </div>

  <!-- Quick Actions -->
  <div class="quick-actions">
    <button class="action-btn" onclick="window.location.href='sports-categories.php'">
      🎟️ Book Events
    </button>
    <button class="action-btn" onclick="window.location.href='products.php'">
      🛒 Shop Now
    </button>
    <button class="action-btn" onclick="window.location.href='refer-earn.php'">
      📱 Share & Earn
    </button>
  </div>

  <!-- Redeem Modal -->
  <div class="modal-overlay" id="redeemModal" style="display: none;">
    <div class="modal-content">
      <div class="modal-header">
        <span class="modal-icon" id="modalIcon">🎫</span>
        <h3>Confirm Redemption</h3>
        <button class="modal-close" onclick="closeRedeemModal()">×</button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to redeem:</p>
        <h4 id="modalRewardName">10% Discount Voucher</h4>
        <div class="modal-cost" style="justify-content: center;">
          <span id="modalCostCredits">Cost: 100 Credits</span>
        </div>
        <div class="modal-warning">
          ⚠️ This action cannot be undone
        </div>
      </div>
      <div class="modal-footer">
        <button class="cancel-btn" onclick="closeRedeemModal()">Cancel</button>
        <button class="confirm-btn" onclick="confirmRedeem()">Confirm Redemption</button>
      </div>
    </div>
  </div>
</div>

<script>
let credits = 0;
let totalOrders = 0;
let selectedReward = null;
let userEmail = localStorage.getItem("userEmail");
let transactions = [];

const rewards = [
    { id: 1, name: "10% Discount Voucher", creditCost: 100, icon: "🎫", type: "discount" },
    { id: 2, name: "Free Event Ticket", creditCost: 250, icon: "🎟️", type: "ticket" },
    { id: 3, name: "Premium Sports Gear", creditCost: 500, icon: "🏅", type: "gear" },
    { id: 4, name: "VIP Experience Pass", creditCost: 750, icon: "👑", type: "vip" },
    { id: 5, name: "Signed Merchandise", creditCost: 1000, icon: "✍️", type: "merch" },
];

document.addEventListener("DOMContentLoaded", async function() {
    if (!userEmail) {
        alert("Please login to view your credits");
        window.location.href = "login.php";
        return;
    }
    await fetchData();
});

async function fetchData() {
    try {
        const res = await fetch(`api/index.php/user/profile?email=${encodeURIComponent(userEmail)}`);
        const data = await res.json();
        if (data && data.email) {
            credits = Number(data.credits) || 0;
            totalOrders = Number(data.total_orders) || 0;
            localStorage.setItem("nxlCoins", credits); 
        }

        const txRes = await fetch(`api/index.php/user/transactions?email=${encodeURIComponent(userEmail)}`);
        const txData = await txRes.json();
        if (txData && txData.success && Array.isArray(txData.data)) {
            transactions = txData.data.map(t => ({
                id: t.id,
                type: t.type,
                description: t.description,
                amount: Number(t.amount),
                date: t.date.split(' ')[0],
                icon: t.amount > 0 ? "✅" : "🎁"
            }));
        }
    } catch(err) {
        console.error("Error fetching data", err);
    }
    updateUI();
}

function updateUI() {
    document.getElementById("availableCredits").textContent = credits.toLocaleString();
    document.getElementById("totalOrders").textContent = totalOrders.toLocaleString();

    const cap = 2500;
    const progressPercent = Math.min(100, (credits / cap) * 100);
    document.getElementById("creditsProgressBar").style.width = progressPercent + "%";

    const nextReward = rewards.find(r => r.creditCost > credits) || rewards[rewards.length - 1];
    const prevReward = rewards.filter(r => r.creditCost <= credits).pop() || { creditCost: 0 };
    
    let progress = 0;
    if (nextReward.creditCost !== prevReward.creditCost) {
        progress = ((credits - prevReward.creditCost) / (nextReward.creditCost - prevReward.creditCost)) * 100;
    } else {
        progress = 100;
    }
    const cleanProgress = Math.min(100, Math.max(0, Math.round(progress)));

    document.getElementById("nextMilestoneName").textContent = `Next Milestone: ${nextReward.name}`;
    if (credits >= nextReward.creditCost && nextReward === rewards[rewards.length - 1]) {
        document.getElementById("nextMilestoneRemaining").textContent = `All milestones unlocked!`;
    } else {
        document.getElementById("nextMilestoneRemaining").textContent = `Need ${nextReward.creditCost - credits} more credits to unlock`;
    }
    
    document.getElementById("milestoneCirclePath").setAttribute("stroke-dasharray", `${cleanProgress}, 100`);
    document.getElementById("milestonePercentText").textContent = cleanProgress + "%";

    let tier = "Bronze Tier";
    if (credits >= 2500) {
        tier = "Gold Tier";
        document.getElementById("tierProgressLabel").textContent = `${credits} Credits Earned!`;
        document.getElementById("tierProgressBar").style.width = "100%";
    } else if (credits >= 1000) {
        tier = "Silver Tier";
        document.getElementById("tierProgressLabel").textContent = `${credits}/2500 to Gold`;
        document.getElementById("tierProgressBar").style.width = ((credits - 1000) / 1500 * 100) + "%";
    } else {
        document.getElementById("tierProgressLabel").textContent = `${credits}/1000 to Silver`;
        document.getElementById("tierProgressBar").style.width = (credits / 1000 * 100) + "%";
    }
    document.getElementById("memberTier").textContent = tier;

    renderRewardsStore();
    renderTransactions();
}

function renderRewardsStore() {
    const grid = document.getElementById("rewardsGrid");
    grid.innerHTML = rewards.map(reward => {
        const isAvailable = credits >= reward.creditCost;
        return `
            <div class="reward-card ${isAvailable ? 'available' : 'locked'}">
              <div class="reward-icon">${reward.icon}</div>
              <h3>${reward.name}</h3>
              <div class="reward-cost" style="justify-content: center;">
                <span class="cost-credits">${reward.creditCost} Credits</span>
              </div>
              <button 
                class="redeem-btn ${isAvailable ? '' : 'disabled'}"
                onclick="handleRedeem(${reward.id})"
                ${isAvailable ? '' : 'disabled'}
              >
                ${isAvailable ? 'Redeem Now' : `Need ${reward.creditCost - credits} more`}
              </button>
              ${reward.type === 'vip' ? '<div class="reward-badge">Exclusive</div>' : ''}
              ${reward.type === 'merch' ? '<div class="reward-badge limited">Limited</div>' : ''}
            </div>
        `;
    }).join('');
}

function renderTransactions() {
    const container = document.getElementById("transactionsList");
    if (transactions.length === 0) {
        container.innerHTML = `<div style="padding: 2rem; text-align: center; color: #9aa0b4;">No transactions found. Earn credits to see history here!</div>`;
        return;
    }
    container.innerHTML = transactions.map(tx => `
        <div class="transaction-item">
          <div class="transaction-icon">${tx.icon}</div>
          <div class="transaction-details">
            <div class="transaction-header">
              <span class="transaction-type">${tx.type}</span>
              <span class="transaction-date">${tx.date}</span>
            </div>
            <p class="transaction-desc">${tx.description}</p>
          </div>
          <div class="transaction-amount ${tx.amount > 0 ? 'positive' : 'negative'}">
            ${tx.amount > 0 ? '+' : ''}${tx.amount} Credits
          </div>
        </div>
    `).join('');
}

function handleRedeem(rewardId) {
    const reward = rewards.find(r => r.id === rewardId);
    if (!reward) return;

    if (credits >= reward.creditCost) {
        selectedReward = reward;
        document.getElementById("modalIcon").textContent = reward.icon;
        document.getElementById("modalRewardName").textContent = reward.name;
        document.getElementById("modalCostCredits").textContent = `Cost: ${reward.creditCost} Credits`;
        document.getElementById("redeemModal").style.display = "flex";
    } else {
        alert(`Insufficient credits! You need ${reward.creditCost - credits} more credits.`);
    }
}

function closeRedeemModal() {
    document.getElementById("redeemModal").style.display = "none";
    selectedReward = null;
}

async function confirmRedeem() {
    if (!selectedReward) return;

    try {
        const reqData = {
            email: userEmail,
            rewardCost: selectedReward.creditCost,
            rewardName: selectedReward.name
        };
        const res = await fetch("api/index.php/user/credits/redeem", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(reqData)
        });
        const data = await res.json();
        
        if (data.success) {
            alert(`Successfully redeemed ${selectedReward.name}!`);
            closeRedeemModal();
            await fetchData(); // refresh data
        } else {
            alert("Error: " + (data.message || "Failed to redeem reward."));
        }
    } catch(err) {
        console.error("Redeem error:", err);
        alert("A network error occurred.");
    }
}
</script>



<?php require_once __DIR__ . '/includes/footer.php'; ?>
