<?php
$pageTitle = "GLOBAL SPORTS ARENA | Dashboard";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<link rel="stylesheet" href="assets/css/UserDashboard.css?v=4">

<style>
@keyframes flash-expired {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.55; transform: scale(1.05); }
}
.pass-expired-badge {
    display: inline-block;
    background: #c62828;
    color: #fff;
    font-size: 0.72rem;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 20px;
    letter-spacing: 1px;
    animation: flash-expired 1.2s ease-in-out infinite;
    box-shadow: 0 0 8px rgba(198,40,40,0.5);
}


/* --- Mobile Fixes for Dashboard --- */
@media (max-width: 640px) {
  .dashboard-grid {
    grid-template-columns: 1fr !important;
    gap: 1.5rem !important;
    padding: 10px !important;
    width: 100% !important;
    box-sizing: border-box !important;
    overflow-x: hidden !important;
  }
  .dashboard-card {
    padding: 1.5rem !important;
    width: 100% !important;
    box-sizing: border-box !important;
    min-width: 0 !important;
    flex-direction: column !important;
    align-items: center !important;
    text-align: center !important;
    gap: 15px !important;
  }
  .dashboard-card .card-content {
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    gap: 8px !important;
  }
  .dashboard-card .card-content > div {
    min-width: 0 !important;
    width: 100% !important;
    justify-content: center !important;
  }
  .dashboard-card button {
    width: 100% !important;
    margin-top: 10px !important;
  }
}
</style>

<div class="dashboard-page" id="dashboardPage" style="display: none;">
  <div class="dashboard-hero">
    <div class="dashboard-overlay"></div>
    <div class="dashboard-hero-content">
      <div class="welcome-badge">Welcome Back!</div>
      <h1>User Dashboard</h1>
      <p>Manage your account, track orders, and view upcoming events</p>
    </div>
  </div>

  <div class="profile-card flex flex-col md:flex-row justify-between items-center max-w-7xl mx-auto p-6 gap-4 bg-[#12131c] rounded-2xl border border-[rgba(197,168,92,0.2)] mt-[-40px] relative z-10 shadow-lg mb-8">
    <div class="profile-left flex flex-col sm:flex-row items-center gap-4 text-center sm:text-left">
      <div class="profile-avatar" id="profileAvatarContainer" style="overflow: hidden; display: flex; justify-content: center; align-items: center;">
          <img id="profilePicImg" src="" alt="Profile" style="width: 100%; height: 100%; object-fit: cover; display: none;" />
          <span id="avatarLetter">U</span>
        </div>
      <div class="profile-details">
        <h2 id="profileName" style="margin: 0; font-size: 1.5rem;">Loading...</h2>
        <p id="profileEmail"></p>
        <span id="membershipInfo">Member since 2026</span>
      </div>
    </div>
    <div style="display: flex; gap: 15px; align-items: center;">
      <div style="position: relative;">
        <button id="notifBtn" style="background: rgba(184, 156, 98, 0.1); border: 1px solid #B89C62; color: #B89C62; border-radius: 50%; width: 40px; height: 40px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; transition: all 0.3s ease;">
          🔔
        </button>
        <span id="notifBadge" style="position: absolute; top: -5px; right: -5px; background: #ef4444; color: white; border-radius: 50%; width: 18px; height: 18px; font-size: 0.7rem; display: flex; align-items: center; justify-content: center; display: none;">0</span>
        
        <!-- Dropdown panel -->
        <div id="notifPanel" style="display: none; position: absolute; top: 50px; right: 0; width: 550px; background: #FFFFFF; border: 1px solid #B89C62; border-radius: 12px; box-shadow: 0 10px 30px rgba(138, 122, 95, 0.15); z-index: 100; overflow: hidden;">
          <div style="padding: 15px; border-bottom: 1px solid rgba(189, 168, 131, 0.2); background: #F9F6F0; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; color: #B89C62; font-size: 1rem;">Notifications</h3>
            <button onclick="clearNotifications(event)" style="background: none; border: none; color: #7A7061; font-size: 0.8rem; cursor: pointer; text-decoration: underline; padding: 0;">Clear All</button>
          </div>
          <div style="display: flex; background: #FFFFFF; border-bottom: 1px solid rgba(189, 168, 131, 0.1);">
            <button class="notif-tab active" data-type="all" onclick="filterNotifications('all', event)" style="flex: 1; padding: 10px; background: transparent; border: none; border-bottom: 2px solid #B89C62; color: #B89C62; cursor: pointer; font-size: 0.85rem; transition: all 0.2s;">All</button>
            <button class="notif-tab" data-type="matches" onclick="filterNotifications('matches', event)" style="flex: 1; padding: 10px; background: transparent; border: none; border-bottom: 2px solid transparent; color: #7A7061; cursor: pointer; font-size: 0.85rem; transition: all 0.2s;">Matches</button>
            <button class="notif-tab" data-type="nxl" onclick="filterNotifications('nxl', event)" style="flex: 1; padding: 10px; background: transparent; border: none; border-bottom: 2px solid transparent; color: #7A7061; cursor: pointer; font-size: 0.85rem; transition: all 0.2s;">NXL Credits</button>
          </div>
          <div id="notifList" style="max-height: 400px; overflow-y: auto;">
            <div style="padding: 15px; text-align: center; color: #7A7061;">Loading...</div>
          </div>
        </div>
      </div>
      
      <button class="edit-profile-btn" onclick="openEditProfile()">
        Edit Profile →
      </button>
    </div>
  </div>

  <div class="dashboard-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 max-w-7xl mx-auto px-4">
    <div class="dashboard-card">
      <div class="card-icon">💎</div>
      <div class="card-content">
        <h3>Wallet Balance</h3>
        <h2 id="walletBalanceLabel">0 Credits</h2>
        <p>Recharge wallet and manage your balance</p>
      </div>
      <button class="card-btn" onclick="window.location.href='wallet.php'">Wallet</button>
    </div>

    <div class="dashboard-card">
      <div class="card-icon">💎</div>
      <div class="card-content">
        <h3>NXL Credits</h3>
        <h2 id="creditsLabel">0</h2>
        <p>Redeem credits for rewards and offers</p>
      </div>
      <button class="card-btn" onclick="window.location.href='credits.php'">Credits</button>
    </div>

    <div class="dashboard-card">
      <div class="card-icon">📦</div>
      <div class="card-content">
        <h3>Total Orders</h3>
        <h2 id="totalOrdersLabel">0</h2>
        <p>View your sports product orders</p>
      </div>
      <button class="card-btn" onclick="window.location.href='orders.php'">Orders</button>
    </div>

    <div class="dashboard-card">
      <div class="card-icon">🏆</div>
      <div class="card-content">
        <h3>Events Joined</h3>
        <h2 id="eventsJoinedLabel">0</h2>
        <p>Browse and register for tournaments</p>
      </div>
      <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-top: 4px;">
        <button class="card-btn" onclick="window.location.href='event-registration.php'" style="margin-top:0;">Browse Events</button>
      </div>
    </div>

    <div class="dashboard-card" style="grid-column: 1 / -1; flex-wrap: wrap;">
      <div class="card-icon">🎟️</div>
      <div class="card-content" style="display: flex; align-items: center; gap: 25px; flex-wrap: wrap; flex: 1;">
        <div style="min-width: 250px;">
          <h3>Passes QR Code</h3>
          <h2 style="margin: 4px 0;">View Passes</h2>
          <p style="margin: 0;">View your registered event entry passes</p>
        </div>
        <div style="display: flex; gap: 10px; flex-wrap: wrap; flex: 1;">
            <button class="card-btn" onclick="viewMyPasses('visitor')" style="margin-top:0; background: linear-gradient(135deg, #2e7d32 0%, #1b5e20 100%); color: #fff; border: 1px solid #4caf50;">See Visitor pass</button>
            <button class="card-btn" onclick="viewMyPasses('exhibitor')" style="margin-top:0; background: linear-gradient(135deg, #0288d1 0%, #01579b 100%); color: #fff; border: 1px solid #03a9f4;">See Exhibitor pass</button>
            <button class="card-btn" id="btnEventsQrPass" onclick="viewEventQrPasses()" style="margin-top:0; background: linear-gradient(135deg, #7b1fa2 0%, #4a0072 100%); color: #fff; border: 1px solid #ab47bc;">🏅 Events QR Pass</button>
            <button class="card-btn" id="btnAwardPasses" onclick="viewAwardPasses()" style="margin-top:0; background: linear-gradient(135deg, #c5a85c 0%, #8c6010 100%); color: #fff; border: 1px solid #c5a85c;">🏆 Gala Pass</button>
        </div>
      </div>
      
      <div id="inlinePassesContainer" style="display: none; width: 100%; flex-basis: 100%; margin-top: 25px; padding-top: 25px; border-top: 1px dashed rgba(189, 168, 131, 0.4); grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
          <!-- QR codes will be injected here -->
      </div>
      <div id="inlineEventPassesContainer" style="display: none; width: 100%; flex-basis: 100%; margin-top: 25px; padding-top: 25px; border-top: 1px dashed rgba(189, 168, 131, 0.4); grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
          <!-- Event QR passes will be injected here -->
      </div>
      <div id="inlineAwardPassesContainer" style="display: none; width: 100%; flex-basis: 100%; margin-top: 25px; padding-top: 25px; border-top: 1px dashed rgba(189, 168, 131, 0.4); grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
          <!-- Award Gala passes will be injected here -->
      </div>
    </div>
  </div>

  <!-- Exhibitor Applications Section -->
  <div class="dashboard-grid grid grid-cols-1 max-w-7xl mx-auto px-4 mt-6">
    <div class="dashboard-card" style="grid-column: 1 / -1; flex-wrap: wrap;">
      <div class="card-icon">🏢</div>
      <div class="card-content" style="display: flex; align-items: center; gap: 25px; flex-wrap: wrap; flex: 1;">
        <div style="min-width: 250px;">
          <h3>My Exhibitor Applications</h3>
          <p style="margin: 0;">Track the status of your event booth applications</p>
        </div>
        <div style="display: flex; gap: 10px; flex-wrap: wrap; flex: 1;">
            <button class="card-btn" id="btnViewExhibitorApps" onclick="toggleExhibitorApps()" style="margin-top:0; background: linear-gradient(135deg, #10b981 0%, #047857 100%); color: #fff; border: 1px solid #059669;">View Applications</button>
        </div>
      </div>
      <div id="exhibitorAppsContainer" style="display: none; width: 100%; flex-basis: 100%; margin-top: 25px; padding-top: 25px; border-top: 1px dashed rgba(189, 168, 131, 0.4); grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
          <!-- Apps will be injected here -->
      </div>
    </div>
  </div>

  <!-- Gift Cards Section -->
  <div class="dashboard-grid grid grid-cols-1 max-w-7xl mx-auto px-4 mt-6">
    <div class="dashboard-card" style="grid-column: 1 / -1;">
      <div class="card-icon">🎁</div>
      <div class="card-content">
        <h3>My Gift Cards</h3>
        <h2 id="giftCardsLabel">View Cards</h2>
        <p>Purchase, send, and redeem sports experience gift cards</p>
      </div>
      <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-top: 4px; width: 100%;">
        <button class="card-btn" onclick="window.location.href='gift-cards.php'" style="margin-top:0; background: linear-gradient(135deg, #c9a34a 0%, #8c6010 100%); color: #fff; border: 1px solid #c9a34a;">Buy Gift Card</button>
        <button class="card-btn" onclick="window.location.href='gift-card-redeem.php'" style="margin-top:0; background: linear-gradient(135deg, #2e7d32 0%, #1b5e20 100%); color: #fff; border: 1px solid #4caf50;">Redeem Gift Card</button>
        <button class="card-btn" id="btnViewMyGiftCards" onclick="toggleMyGiftCards()" style="margin-top:0;">View My Cards</button>
      </div>
      <div id="myGiftCardsContainer" style="display: none; width: 100%; flex-basis: 100%; margin-top: 15px; padding-top: 15px; border-top: 1px dashed rgba(189,168,131,0.4);">
        Loading your gift cards...
      </div>
    </div>
  </div>

  <div style="display: flex; justify-content: center; padding-bottom: 50px;">
    <button class="edit-profile-btn" onclick="handleLogout()">Logout</button>
  </div>
</div>

<!-- Edit Profile Modal -->
<div id="editProfileModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.4); z-index: 1000; justify-content: center; align-items: center;">
  <div style="background: #FFFFFF; border: 1px solid #B89C62; border-radius: 12px; width: 400px; padding: 25px; box-shadow: 0 10px 40px rgba(138,122,95,0.2);">
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(189, 168, 131, 0.2); padding-bottom: 15px; margin-bottom: 20px;">
      <h3 style="margin: 0; color: #B89C62;">Edit Profile</h3>
      <button onclick="closeEditProfile()" style="background: none; border: none; color: #7A7061; font-size: 1.2rem; cursor: pointer;">✕</button>
    </div>
    
    <form id="editProfileForm" onsubmit="submitEditProfile(event)">
      <div style="margin-bottom: 15px;">
        <label style="display: block; color: #5C5446; margin-bottom: 5px; font-size: 0.9rem;">Profile Picture</label>
        <input type="file" id="editProfilePic" accept="image/*" style="width: 100%; padding: 10px; background: #F9F6F0; border: 1px solid rgba(189,168,131,0.3); border-radius: 6px; color: #3A342B;">
      </div>
      
      <div style="margin-bottom: 15px;">
        <label style="display: block; color: #5C5446; margin-bottom: 5px; font-size: 0.9rem;">Full Name</label>
        <input type="text" id="editFullName" required style="width: 100%; padding: 10px; background: #F9F6F0; border: 1px solid rgba(189,168,131,0.3); border-radius: 6px; color: #3A342B;">
      </div>
      
      <div style="margin-bottom: 25px;">
        <label style="display: block; color: #5C5446; margin-bottom: 5px; font-size: 0.9rem;">Phone Number</label>
        <input type="tel" id="editPhone" style="width: 100%; padding: 10px; background: #F9F6F0; border: 1px solid rgba(189,168,131,0.3); border-radius: 6px; color: #3A342B;">
      </div>
      
      <button type="submit" id="editSubmitBtn" style="width: 100%; padding: 12px; background: #B89C62; color: #FFFFFF; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">Save Changes</button>
    </form>
  </div>
</div>

<!-- QR Pass Modal -->
<div id="qrPassModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.4); z-index: 1000; justify-content: center; align-items: center;">
  <div style="background: #FFFFFF; border: 1px solid #B89C62; border-radius: 16px; width: 340px; padding: 30px; box-shadow: 0 10px 40px rgba(138,122,95,0.2); text-align: center; transition: background 0.3s;">
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(189, 168, 131, 0.2); padding-bottom: 15px; margin-bottom: 20px;">
      <h3 style="margin: 0; color: #B89C62; font-size: 1.1rem;">🎟️ Event Entry Pass</h3>
      <button onclick="closeQrPassModal()" style="background: none; border: none; color: #7A7061; font-size: 1.2rem; cursor: pointer; line-height: 1;">✕</button>
    </div>
    <div id="passesContainer" style="max-height: 450px; overflow-y: auto; padding-right: 5px;">
      <div style="background: #F9F6F0; padding: 20px; border-radius: 10px; border: 1px dashed rgba(189, 168, 131, 0.4);">
          <img id="dashboardQrImg" src="" alt="Entry Pass QR" style="width: 200px; height: 200px; border: 2px solid #B89C62; border-radius: 10px; padding: 5px; background: white;" />
          <p style="color: #B89C62; font-weight: bold; font-size: 0.85rem; margin-top: 12px; letter-spacing: 2px;">SCAN AT COMPLEX</p>
          <p id="dashboardQrUser" style="color: #3A342B; font-size: 0.85rem; margin-top: 5px;"></p>
      </div>
    </div>
  </div>
</div>

<!-- Membership Expired Modal -->
<div id="membershipExpiredModal" class="modal-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.4); z-index:9999; justify-content:center; align-items:center; backdrop-filter:blur(4px);">
  <div class="modal-content" style="background:#FFFFFF; border:1px solid #B89C62; border-radius:12px; max-width:400px; width:90%; padding:30px; text-align:center; box-shadow:0 10px 40px rgba(138,122,95,0.2);">
    <div style="font-size:3rem; margin-bottom:15px;">⚠️</div>
    <h2 style="color:#B89C62; margin:0 0 15px 0; font-size:1.5rem;">Membership Expired</h2>
    <p style="color:#7A7061; font-size:0.95rem; margin-bottom:25px; line-height:1.5;">
      Your Premium Membership has officially expired. Want to renew the membership again?
    </p>
    <div style="margin-bottom:20px; font-size:0.85rem; color:#7A7061; display:flex; align-items:center; justify-content:center; gap:8px;">
      <input type="checkbox" id="dontRemindExpiry" style="accent-color:#B89C62; cursor:pointer; width:16px; height:16px;">
      <label for="dontRemindExpiry" style="cursor:pointer; user-select:none;">Don't remind me again</label>
    </div>
    <div style="display:flex; gap:10px; justify-content:center;">
      <button onclick="dismissExpiryModal()" style="padding:10px 20px; border-radius:6px; background:transparent; border:1px solid #B89C62; color:#B89C62; cursor:pointer; font-weight:bold; transition:all 0.3s;" onmouseover="this.style.background='rgba(184, 156, 98, 0.1)'" onmouseout="this.style.background='transparent'">Remind Me Later</button>
      <button onclick="window.location.href='index.php?scrollTo=membership'" style="padding:10px 20px; border-radius:6px; background:#B89C62; border:none; color:#FFFFFF; font-weight:bold; cursor:pointer; transition:all 0.3s;" onmouseover="this.style.background='#8A7A5F'" onmouseout="this.style.background='#B89C62'">Upgrade Now</button>
    </div>
  </div>
</div>

<script>
function dismissExpiryModal() {
    if (document.getElementById('dontRemindExpiry').checked) {
        localStorage.setItem('expiryModalDismissed', 'true');
    }
    document.getElementById('membershipExpiredModal').style.display='none';
}
</script>

<div class="dashboard-loading" id="dashboardLoading">Loading Dashboard...</div>

<script>
async function loadDashboard() {
    const userEmail = localStorage.getItem("userEmail");
    const role = localStorage.getItem("userRole");

    if (!userEmail) {
        window.location.href = "login.php";
        return;
    }

    if (role === "ADMIN") {
        window.location.href = "admin-dashboard.php";
        return;
    }

    if (role === "MERCHANT") {
        window.location.href = "merchant.php";
        return;
    }

    document.getElementById("dashboardLoading").style.display = "block";

    try {
        const res = await fetch(`api/index.php/user/profile?email=${encodeURIComponent(userEmail)}`);
        const data = await res.json();
        
        if (data.email) {
            renderDashboardData(data);
        } else {
            throw new Error("Profile not found");
        }
    } catch(e) {
        console.warn("Profile fetch failed, using local storage fallback", e);
        
        // Fallbacks
        const nxlCoins = Number(localStorage.getItem("nxlCoins")) || 50;
        const eventsJoined = Number(localStorage.getItem("eventsJoined")) || 0;
        
        let totalOrders = 0;
        try {
            const orderKey = `orders_${userEmail}`;
            const orders = JSON.parse(localStorage.getItem(orderKey)) || [];
            totalOrders = orders.length;
        } catch(err) {}

        const namePart = userEmail.split("@")[0];
        const fullName = namePart.charAt(0).toUpperCase() + namePart.slice(1);

        renderDashboardData({
            fullName: fullName,
            email: userEmail,
            walletBalance: 0,
            credits: nxlCoins,
            totalOrders: totalOrders,
            eventsJoined: eventsJoined
        });
    }
}

function renderDashboardData(user) {
    document.getElementById("dashboardLoading").style.display = "none";
    document.getElementById("dashboardPage").style.display = "block";

    // Avatar setup
    const avatarLetter = document.getElementById("avatarLetter");
    const profilePicImg = document.getElementById("profilePicImg");
    
    if (user.profile_pic) {
        avatarLetter.style.display = "none";
        profilePicImg.style.display = "block";
        profilePicImg.src = user.profile_pic;
    } else {
        avatarLetter.style.display = "block";
        profilePicImg.style.display = "none";
        avatarLetter.textContent = user.fullName ? user.fullName.charAt(0).toUpperCase() : 'U';
    }

    document.getElementById("profileName").textContent = user.fullName;
    document.getElementById("profileEmail").textContent = user.email;
    
    // Membership logic
    const oldMembership = document.getElementById('membershipInfo');
    if (oldMembership) oldMembership.remove();

    let membershipHtml = `<span id="membershipInfo" style="display:block; margin-top:5px; font-size:0.9rem; color:#B89C62; background: rgba(184, 156, 98, 0.1); padding: 5px 10px; border-radius: 6px; display: inline-block;">`;
    if (user.membershipTier && user.membershipTier !== 'none') {
        membershipHtml += `🏆 <span style="font-weight:bold;">${user.membershipTier.toUpperCase()} MEMBER</span>`;
        if (user.membershipExpiry) {
            membershipHtml += `<br><small style="color:#7A7061;">Expires in: <strong id="membershipCountdown"></strong></small>`;
            startMembershipCountdown(user.membershipExpiry);
        }
    } else {
        membershipHtml += `Basic Member - <a href="index.php?scrollTo=membership" style="color:#B89C62; text-decoration:underline;">Upgrade to Premium</a>`;
    }
    membershipHtml += `</span>`;
    document.querySelector('.profile-details').insertAdjacentHTML('beforeend', membershipHtml);
    
    // Store globally for editing
    window.currentUserData = user;
    document.getElementById("walletBalanceLabel").textContent = Number(user.walletBalance || 0).toLocaleString() + " Credits";
    
    // Check if membership just expired and show popup
    if (user.isExpiredMember && !sessionStorage.getItem('expiryModalShown') && !localStorage.getItem('expiryModalDismissed')) {
        document.getElementById("membershipExpiredModal").style.display = "flex";
        // Also update local storage so the navbar badge updates
        localStorage.setItem("userMembership", "none");
        sessionStorage.setItem("expiryModalShown", "true");
    }
    
    document.getElementById("creditsLabel").textContent = user.credits || 0;
    document.getElementById("totalOrdersLabel").textContent = user.totalOrders || 0;
    document.getElementById("eventsJoinedLabel").textContent = user.eventsJoined || 0;
    
    // Fetch notifications
    loadNotifications(user.email);
}

document.addEventListener("DOMContentLoaded", loadDashboard);

function startMembershipCountdown(expiryDateStr) {
    const countDownDate = new Date(expiryDateStr.replace(/-/g, '/')).getTime();
    
    if (window.membershipInterval) clearInterval(window.membershipInterval);
    
    const updateCountdown = () => {
        const now = new Date().getTime();
        const distance = countDownDate - now;
        
        const countdownEl = document.getElementById('membershipCountdown');
        if (!countdownEl) return;
        
        if (distance < 0) {
            clearInterval(window.membershipInterval);
            countdownEl.innerHTML = "<span style='color:#ef4444'>EXPIRED</span>";
            // Show modal immediately when it expires live
            const modal = document.getElementById("membershipExpiredModal");
            if (modal) modal.style.display = "flex";
            
            // Revert UI to Basic
            setTimeout(() => {
                const membershipInfo = document.getElementById("membershipInfo");
                if (membershipInfo) {
                    membershipInfo.innerHTML = `Basic Member - <a href="index.php?scrollTo=membership" style="color:#B89C62; text-decoration:underline;">Upgrade to Premium</a>`;
                }
            }, 100);
            
            return;
        }
        
        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        countdownEl.innerHTML = days + "d " + hours + "h " + minutes + "m " + seconds + "s ";
    };
    
    updateCountdown();
    window.membershipInterval = setInterval(updateCountdown, 1000);
}

async function loadNotifications(email) {
    try {
        const res = await fetch(`api/index.php/user/notifications?email=${email}`);
        const result = await res.json();
        const list = document.getElementById('notifList');
        const badge = document.getElementById('notifBadge');
        
        if (result.success && result.data && result.data.length > 0) {
            window.currentMaxNotifTimestamp = Math.max(...result.data.map(n => n.timestamp * 1000));
            
            const clearedTime = parseInt(localStorage.getItem('clearedNotifTime_' + email) || '0');
            window.currentVisibleNotifications = result.data.filter(n => (n.timestamp * 1000) > clearedTime);
            
            if (window.currentVisibleNotifications.length > 0) {
                const lastSeen = parseInt(localStorage.getItem('lastSeenNotifTime_' + email) || '0');
                const unreadCount = window.currentVisibleNotifications.filter(n => (n.timestamp * 1000) > lastSeen).length;
                
                if (unreadCount > 0) {
                    badge.style.display = 'flex';
                    badge.textContent = unreadCount > 9 ? '9+' : unreadCount;
                } else {
                    badge.style.display = 'none';
                }
                
                renderNotificationList('all');
            } else {
                badge.style.display = 'none';
                list.innerHTML = '<div style="padding: 15px; text-align: center; color: #9aa0b4; font-size: 0.9rem;">No new notifications</div>';
            }
        } else {
            window.currentVisibleNotifications = [];
            badge.style.display = 'none';
            list.innerHTML = '<div style="padding: 15px; text-align: center; color: #9aa0b4; font-size: 0.9rem;">No new notifications</div>';
        }
    } catch(e) {
        console.error("Failed to load notifications", e);
    }
}

function filterNotifications(type, e) {
    if (e) e.stopPropagation();
    
    // Update active tab styles
    document.querySelectorAll('.notif-tab').forEach(tab => {
        tab.style.borderBottomColor = 'transparent';
        tab.style.color = '#9aa0b4';
    });
    const clickedTab = document.querySelector(`.notif-tab[data-type="${type}"]`);
    if (clickedTab) {
        clickedTab.style.borderBottomColor = '#c5a85c';
        clickedTab.style.color = '#c5a85c';
    }
    
    renderNotificationList(type);
}

function renderNotificationList(filterType = 'all') {
    const list = document.getElementById('notifList');
    if (!window.currentVisibleNotifications || window.currentVisibleNotifications.length === 0) {
        list.innerHTML = '<div style="padding: 15px; text-align: center; color: #9aa0b4; font-size: 0.9rem;">No new notifications</div>';
        return;
    }
    
    let filtered = window.currentVisibleNotifications;
    if (filterType === 'matches') {
        filtered = filtered.filter(n => n.type === 'NEW_TOURNAMENT');
    } else if (filterType === 'nxl') {
        filtered = filtered.filter(n => n.type === 'NXL_TRANSACTION');
    }
    
    if (filtered.length > 0) {
        list.innerHTML = filtered.map(n => `
            <div style="padding: 15px; border-bottom: 1px solid rgba(197, 168, 92, 0.1); cursor: default; transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                <strong style="color: ${n.type === 'NEW_TOURNAMENT' ? '#3b82f6' : (n.title.includes('Deducted') ? '#ef4444' : '#22c55e')}; display: block; font-size: 0.9rem; margin-bottom: 5px;">${n.title}</strong>
                <p style="color: #9aa0b4; font-size: 0.85rem; margin: 0; line-height: 1.4;">${n.message}</p>
                <small style="color: #4a4d62; font-size: 0.7rem; display: block; margin-top: 5px;">${new Date(n.date).toLocaleString()}</small>
            </div>
        `).join('');
    } else {
        list.innerHTML = '<div style="padding: 15px; text-align: center; color: #9aa0b4; font-size: 0.9rem;">No notifications in this category</div>';
    }
}

function clearNotifications(e) {
    if (e) e.stopPropagation();
    const email = localStorage.getItem("userEmail");
    
    // Set cleared time to the highest timestamp seen so far
    const clearTime = window.currentMaxNotifTimestamp || Date.now();
    localStorage.setItem('clearedNotifTime_' + email, clearTime);
    window.currentVisibleNotifications = [];
    
    // Clear list visually immediately
    renderNotificationList();
    document.getElementById('notifBadge').style.display = 'none';
    
    // Try to hide the global nav badge if it exists on the page
    const navBadge = document.getElementById('navNotifBadge');
    if (navBadge) navBadge.style.display = 'none';
}

document.getElementById('notifBtn').addEventListener('click', function(e) {
    e.stopPropagation();
    const panel = document.getElementById('notifPanel');
    panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
    
    // Clear badge when opened
    if (panel.style.display === 'block') {
        const userEmail = localStorage.getItem("userEmail");
        const seenTime = window.currentMaxNotifTimestamp ? window.currentMaxNotifTimestamp : Date.now();
        localStorage.setItem('lastSeenNotifTime_' + userEmail, seenTime);
        document.getElementById('notifBadge').style.display = 'none';
        
        // Hide global nav badge as well if present
        const navBadge = document.getElementById('navNotifBadge');
        if (navBadge) navBadge.style.display = 'none';
    }
});

document.addEventListener('click', function(e) {
    const panel = document.getElementById('notifPanel');
    if (panel && e.target.closest('#notifPanel') === null && e.target.closest('#notifBtn') === null) {
        panel.style.display = 'none';
    }
});

function openEditProfile() {
    if (!window.currentUserData) return;
    
    document.getElementById('editFullName').value = window.currentUserData.fullName || '';
    document.getElementById('editPhone').value = window.currentUserData.phone_number || '';
    document.getElementById('editProfilePic').value = ''; // Clear file input
    
    document.getElementById('editProfileModal').style.display = 'flex';
}

function closeEditProfile() {
    document.getElementById('editProfileModal').style.display = 'none';
}

async function submitEditProfile(e) {
    e.preventDefault();
    const btn = document.getElementById('editSubmitBtn');
    btn.textContent = 'Saving...';
    btn.disabled = true;
    
    const email = localStorage.getItem("userEmail");
    const fullName = document.getElementById('editFullName').value;
    const phone = document.getElementById('editPhone').value;
    const fileInput = document.getElementById('editProfilePic');
    
    const formData = new FormData();
    formData.append('email', email);
    formData.append('full_name', fullName);
    formData.append('phone', phone);
    
    if (fileInput.files && fileInput.files[0]) {
        formData.append('profile_pic', fileInput.files[0]);
    }
    
    try {
        const res = await fetch('api/index.php/user/profile', {
            method: 'POST',
            body: formData
        });
        
        const result = await res.json();
        if (result.success) {
            // Update local data
            window.currentUserData.fullName = fullName;
            window.currentUserData.phone_number = phone;
            if (result.profile_pic) {
                window.currentUserData.profile_pic = result.profile_pic;
            }
            
            // Re-render
            renderDashboardData(window.currentUserData);
            closeEditProfile();
        } else {
            alert(result.message || 'Failed to update profile');
        }
    } catch(err) {
        console.error("Profile update error:", err);
        alert('An error occurred while updating the profile.');
    } finally {
        btn.textContent = 'Save Changes';
        btn.disabled = false;
    }
}

async function viewMyPasses(passType) {
    const userEmail = localStorage.getItem("userEmail");
    if (!userEmail) {
        alert("You must be logged in to view your passes.");
        return;
    }

    try {
        const res = await fetch(`api/index.php/user/all_passes?email=${encodeURIComponent(userEmail)}`);
        const result = await res.json();
        const inlineContainer = document.getElementById("inlinePassesContainer");
        
        if (result.success && result.passes && result.passes.length > 0) {
            inlineContainer.innerHTML = ""; // Clear existing
            
            // Filter by requested pass type
            const filteredPasses = result.passes.filter(p => p.type === passType);
            
            if (filteredPasses.length === 0) {
                alert(`No registered ${passType} passes found for this email.`);
                return;
            }
            
            filteredPasses.forEach((pass, index) => {
                const typeStr = pass.type === 'visitor' ? 'Visitor Pass' : 'Exhibitor Pass';
                const isExpired = pass.status === 'expired';
                const qrImageUrl = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(pass.qrUrl)}`;
                const passName = pass.pass_name || "User";
                const eventDate = pass.event_date ? new Date(pass.event_date).toLocaleDateString('en-US', {year:'numeric',month:'short',day:'numeric'}) : 'N/A';
                const cardBg = isExpired ? 'rgba(198,40,40,0.08)' : 'rgba(46,125,50,0.13)';
                const cardBorder = isExpired ? '#c62828' : '#2e7d32';
                const statusBadge = isExpired
                    ? `<span class="pass-expired-badge">⚠️ EXPIRED</span>`
                    : `<span style="display:inline-block;background:#2e7d32;color:#fff;font-size:0.72rem;font-weight:700;padding:3px 10px;border-radius:20px;letter-spacing:1px;">✓ ACTIVE</span>`;
                const qrFilter = isExpired ? 'filter: grayscale(100%) opacity(0.5);' : '';
                
                const passHtml = `
                    <div style="background:${cardBg}; border: 2px solid ${cardBorder}; padding: 18px; border-radius: 12px; text-align: center; position: relative;">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                            <span style="font-weight:bold; color:#1b5e20; font-size:0.9rem;">${typeStr}</span>
                            ${statusBadge}
                        </div>
                        <p style="font-size:0.8rem; color:#555; margin-bottom:8px;">📅 Event: <strong>${pass.event || 'N/A'}</strong></p>
                        <p style="font-size:0.8rem; color:#555; margin-bottom:12px;">🗓 Event Date: <strong>${eventDate}</strong></p>
                        <img src="${qrImageUrl}" alt="Entry Pass QR" style="width:150px; height:150px; background:#fff; padding:10px; border-radius:8px; margin:0 auto; ${qrFilter}" />
                        <p style="color:#1b5e20; font-weight:bold; font-size:0.9rem; margin-top:12px;">${passName}</p>
                        <p style="color:#777; font-size:0.75rem; margin-top:4px;">Registered: ${new Date(pass.created_at).toLocaleDateString()}</p>
                    </div>
                `;
                inlineContainer.insertAdjacentHTML('beforeend', passHtml);
            });
            
            // Toggle display or force open if switching types
            if (inlineContainer.style.display === 'none' || inlineContainer.dataset.lastType !== passType) {
                inlineContainer.style.display = 'grid';
            } else {
                inlineContainer.style.display = 'none';
            }
            inlineContainer.dataset.lastType = passType;
        } else {
            alert(`No registered passes found for this email. Please register for an event first.`);
        }
    } catch (e) {
        console.error(e);
        alert("Error fetching passes.");
    }
}

function closeQrPassModal() {
    document.getElementById('qrPassModal').style.display = 'none';
}

async function viewEventQrPasses() {
    const userEmail = localStorage.getItem("userEmail");
    if (!userEmail) {
        alert("You must be logged in to view your event passes.");
        return;
    }

    const btn = document.getElementById('btnEventsQrPass');
    const inlineContainer = document.getElementById('inlineEventPassesContainer');

    // Toggle off if already open
    if (inlineContainer.style.display !== 'none') {
        inlineContainer.style.display = 'none';
        if (btn) btn.textContent = '🏅 Events QR Pass';
        return;
    }

    if (btn) btn.textContent = '⏳ Loading...';

    try {
        const res = await fetch(`api/index.php/user/event_passes?email=${encodeURIComponent(userEmail)}`);
        const result = await res.json();

        inlineContainer.innerHTML = '';

        if (result.success && result.passes && result.passes.length > 0) {
            const sportEmoji = { 'cricket': '🏏', 'football': '⚽', 'basketball': '🏀', 'badminton': '🏸', 'tennis': '🎾', 'volleyball': '🏐', 'kabaddi': '🤼', 'chess': '♟️' };

            result.passes.forEach(pass => {
                const isExpired = pass.status === 'expired';
                const isPending = (pass.payment_status || '').toLowerCase() !== 'paid';
                const sport = (pass.sport || 'event').toLowerCase();
                const emoji = sportEmoji[sport] || '🏅';
                const teamName = pass.team_name || 'N/A';
                const captainName = pass.captain_name || 'N/A';
                const eventDate = pass.event_date ? new Date(pass.event_date).toLocaleDateString('en-US', {year:'numeric', month:'short', day:'numeric'}) : 'TBD';
                const regType = pass.registration_type || 'N/A';
                const category = pass.team_category || 'N/A';

                let cardBg, cardBorder, badgeHtml;
                if (isPending) {
                    cardBg = 'rgba(245,124,0,0.08)';
                    cardBorder = '#f57c00';
                    badgeHtml = `<span style="display:inline-block;background:#f57c00;color:#fff;font-size:0.72rem;font-weight:700;padding:3px 10px;border-radius:20px;letter-spacing:1px;">⏳ PENDING PAYMENT</span>`;
                } else if (isExpired) {
                    cardBg = 'rgba(198,40,40,0.08)';
                    cardBorder = '#c62828';
                    badgeHtml = `<span class="pass-expired-badge">⚠️ EXPIRED</span>`;
                } else {
                    cardBg = 'rgba(123,31,162,0.08)';
                    cardBorder = '#7b1fa2';
                    badgeHtml = `<span style="display:inline-block;background:#7b1fa2;color:#fff;font-size:0.72rem;font-weight:700;padding:3px 10px;border-radius:20px;letter-spacing:1px;">✓ REGISTERED</span>`;
                }

                const qrFilter = (isExpired || isPending) ? 'filter: grayscale(100%) opacity(0.5);' : '';
                const qrUrl = pass.qrUrl || '';
                const qrImageUrl = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(qrUrl)}`;

                const passHtml = `
                    <div style="background:${cardBg}; border: 2px solid ${cardBorder}; padding: 18px; border-radius: 12px; text-align: center; position: relative;">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                            <span style="font-weight:bold; color:${cardBorder}; font-size:1.1rem;">${emoji} ${sport.charAt(0).toUpperCase() + sport.slice(1)}</span>
                            ${badgeHtml}
                        </div>
                        <p style="font-size:0.82rem; color:#555; margin-bottom:4px;">👥 Team: <strong>${teamName}</strong></p>
                        <p style="font-size:0.82rem; color:#555; margin-bottom:4px;">👤 Captain: <strong>${captainName}</strong></p>
                        <p style="font-size:0.82rem; color:#555; margin-bottom:4px;">🏷 Type: <strong>${regType}</strong> | Category: <strong>${category}</strong></p>
                        <p style="font-size:0.82rem; color:#555; margin-bottom:12px;">📅 Event Date: <strong>${eventDate}</strong></p>
                        <img src="${qrImageUrl}" alt="Event QR Pass" style="width:150px; height:150px; background:#fff; padding:10px; border-radius:8px; margin:0 auto; ${qrFilter}" />
                        <p style="color:${cardBorder}; font-weight:bold; font-size:0.85rem; margin-top:10px; letter-spacing:1px;">SCAN AT VENUE ENTRY</p>
                    </div>
                `;
                inlineContainer.insertAdjacentHTML('beforeend', passHtml);
            });

            inlineContainer.style.display = 'grid';
            if (btn) btn.textContent = '🏅 Events QR Pass ▲';
        } else {
            inlineContainer.innerHTML = `<div style="grid-column:1/-1; text-align:center; padding:25px; color:#777; background:rgba(123,31,162,0.05); border:1px dashed rgba(123,31,162,0.3); border-radius:10px;">
                <p style="font-size:1.5rem; margin-bottom:8px;">🏅</p>
                <p style="font-size:0.95rem;">No event registrations found.</p>
                <a href="event-registration.php" style="color:#7b1fa2; font-weight:bold; text-decoration:underline;">Register for an event →</a>
            </div>`;
            inlineContainer.style.display = 'grid';
            if (btn) btn.textContent = '🏅 Events QR Pass ▲';
        }
    } catch (e) {
        console.error(e);
        alert('Error fetching event passes. Please try again.');
        if (btn) btn.textContent = '🏅 Events QR Pass';
    }
}

async function viewAwardPasses() {
    const userEmail = localStorage.getItem("userEmail");
    if (!userEmail) {
        alert("You must be logged in to view your Gala passes.");
        return;
    }

    const btn = document.getElementById('btnAwardPasses');
    const inlineContainer = document.getElementById('inlineAwardPassesContainer');

    if (inlineContainer.style.display !== 'none') {
        inlineContainer.style.display = 'none';
        if (btn) btn.textContent = '🏆 Gala Pass';
        return;
    }

    if (btn) btn.textContent = '⏳ Loading...';

    try {
        const res = await fetch(`api/index.php/user/award_passes?email=${encodeURIComponent(userEmail)}`);
        const result = await res.json();

        inlineContainer.innerHTML = '';

        if (result.success && result.passes && result.passes.length > 0) {
            result.passes.forEach(pass => {
                const isPending = (pass.payment_status || '').toLowerCase() !== 'paid';
                const regType = pass.pass_type || 'Gala Pass';
                const name = pass.full_name || 'N/A';
                
                let cardBg, cardBorder, badgeHtml;
                if (isPending) {
                    cardBg = 'rgba(245,124,0,0.08)';
                    cardBorder = '#f57c00';
                    badgeHtml = `<span style="display:inline-block;background:#f57c00;color:#fff;font-size:0.72rem;font-weight:700;padding:3px 10px;border-radius:20px;letter-spacing:1px;">⏳ PENDING PAYMENT</span>`;
                } else {
                    cardBg = 'rgba(197, 168, 92, 0.08)';
                    cardBorder = '#c5a85c';
                    badgeHtml = `<span style="display:inline-block;background:#c5a85c;color:#fff;font-size:0.72rem;font-weight:700;padding:3px 10px;border-radius:20px;letter-spacing:1px;">✓ RESERVED</span>`;
                }

                const passHtml = `
                    <div style="background:${cardBg}; border: 2px solid ${cardBorder}; padding: 18px; border-radius: 12px; text-align: center; position: relative;">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                            <span style="font-weight:bold; color:${cardBorder}; font-size:1.1rem;">🏆 Gala Dinner</span>
                            ${badgeHtml}
                        </div>
                        <p style="font-size:0.82rem; color:#555; margin-bottom:4px;">👤 Name: <strong>${name}</strong></p>
                        <p style="font-size:0.82rem; color:#555; margin-bottom:12px;">🎟 Type: <strong>${regType}</strong></p>
                        <button onclick="window.location.href='${pass.ticketUrl}'" style="width: 100%; padding: 10px; background: ${cardBorder}; color: #fff; font-weight: bold; border: none; border-radius: 6px; cursor: pointer;">
                            View e-Ticket
                        </button>
                    </div>
                `;
                inlineContainer.insertAdjacentHTML('beforeend', passHtml);
            });

            inlineContainer.style.display = 'grid';
            if (btn) btn.textContent = '🏆 Gala Pass ▲';
        } else {
            inlineContainer.innerHTML = `<div style="grid-column:1/-1; text-align:center; padding:25px; color:#777; background:rgba(197, 168, 92, 0.05); border:1px dashed rgba(197, 168, 92, 0.3); border-radius:10px;">
                <p style="font-size:1.5rem; margin-bottom:8px;">🏆</p>
                <p style="font-size:0.95rem;">No Gala registrations found.</p>
                <a href="award-registration.php" style="color:#c5a85c; font-weight:bold; text-decoration:underline;">Register for Gala Dinner →</a>
            </div>`;
            inlineContainer.style.display = 'grid';
            if (btn) btn.textContent = '🏆 Gala Pass ▲';
        }
    } catch (e) {
        console.error(e);
        alert('Error fetching Gala passes. Please try again.');
        if (btn) btn.textContent = '🏆 Gala Pass';
    }
}

async function toggleMyGiftCards() {
    const container = document.getElementById('myGiftCardsContainer');
    if (container.style.display !== 'none') {
        container.style.display = 'none';
        return;
    }
    const email = localStorage.getItem('userEmail');
    if (!email) {
        container.innerHTML = '<p style="color:#ef4444;">Please log in to view your gift cards.</p>';
        container.style.display = 'block';
        return;
    }
    container.innerHTML = 'Loading...';
    container.style.display = 'block';
    try {
        const res = await fetch(`api/index.php/giftcards/user?email=${encodeURIComponent(email)}`);
        const data = await res.json();
        if (data.success && data.data.length > 0) {
            const statusColor = { 'unredeemed': '#c9a34a', 'redeemed': '#10b981', 'expired': '#ef4444', 'partial': '#3b82f6' };
            container.innerHTML = `
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px,1fr)); gap:15px;">
                    ${data.data.map(gc => `
                        <div style="background: rgba(201,163,74,0.08); border:1px solid rgba(201,163,74,0.4); border-radius:12px; padding:20px;">
                            <div style="font-size:1.5rem; margin-bottom:8px;">🎁</div>
                            <div style="font-family:monospace; font-size:0.85rem; color:#c9a34a; margin-bottom:8px;">${gc.gift_code}</div>
                            <div style="font-size:0.9rem; margin-bottom:4px;"><strong>To:</strong> ${gc.recipient_name}</div>
                            <div style="font-size:0.9rem; margin-bottom:4px;"><strong>Amount:</strong> ₹${gc.amount}</div>
                            <div style="font-size:0.9rem; margin-bottom:8px;"><strong>Expires:</strong> ${gc.expiry_date}</div>
                            <span style="padding:3px 10px; border-radius:20px; font-size:0.75rem; background:${statusColor[gc.redeem_status] || '#888'}22; color:${statusColor[gc.redeem_status] || '#888'}; border:1px solid ${statusColor[gc.redeem_status] || '#888'};">
                                ${gc.redeem_status.toUpperCase()}
                            </span>
                        </div>
                    `).join('')}
                </div>`;
        } else {
            container.innerHTML = '<p style="text-align:center; opacity:0.7;">No gift cards found. <a href="gift-cards.php" style="color:#c9a34a; text-decoration:underline;">Buy one now!</a></p>';
        }
    } catch(e) {
        container.innerHTML = '<p style="color:#ef4444;">Error loading gift cards.</p>';
    }
}
async function toggleExhibitorApps() {
    const container = document.getElementById('exhibitorAppsContainer');
    const btn = document.getElementById('btnViewExhibitorApps');
    
    if (container.style.display !== 'none') {
        container.style.display = 'none';
        if (btn) btn.textContent = 'View Applications';
        return;
    }
    
    const userId = localStorage.getItem('userId');
    if (!userId) {
        container.innerHTML = '<p style="color:#ef4444;">Please log in to view your applications.</p>';
        container.style.display = 'block';
        return;
    }
    
    if (btn) btn.textContent = '⏳ Loading...';
    
    try {
        const res = await fetch(`api/index.php/user-exhibitor-applications?user_id=${userId}`);
        const result = await res.json();
        
        container.innerHTML = '';
        
        if (result.success && result.data && result.data.length > 0) {
            result.data.forEach(app => {
                let statusBadge = '';
                let actionBtn = '';
                
                if (app.approval_status === 'pending') {
                    statusBadge = '<span style="background: #f59e0b; color: #fff; padding: 3px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: bold;">Pending Review</span>';
                } else if (app.approval_status === 'approved') {
                    if (app.razorpay_payment_id) {
                        statusBadge = '<span style="background: #10b981; color: #fff; padding: 3px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: bold;">Approved & Paid</span>';
                        actionBtn = `<button onclick="window.location.href='verify-pass.php?type=exhibitor&id=${app.id}'" style="width: 100%; padding: 10px; margin-top: 10px; background: #c5a85c; color: #000; font-weight: bold; border: none; border-radius: 6px; cursor: pointer;">View QR Pass</button>`;
                    } else {
                        statusBadge = '<span style="background: #10b981; color: #fff; padding: 3px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: bold;">Approved</span>';
                        actionBtn = `<button id="payExhibitorBtn_${app.id}" onclick="payExhibitorFee(${app.id}, ${app.fee_amount})" style="width: 100%; padding: 10px; margin-top: 10px; background: #3b82f6; color: #fff; font-weight: bold; border: none; border-radius: 6px; cursor: pointer;">Pay Now ₹${parseFloat(app.fee_amount).toLocaleString('en-IN')}</button>`;
                    }
                } else if (app.approval_status === 'rejected') {
                    statusBadge = '<span style="background: #ef4444; color: #fff; padding: 3px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: bold;">Rejected</span>';
                    actionBtn = '<div style="margin-top:10px; font-size:0.85rem; color:#ef4444; text-align:center;">Sorry, Not Selected For The Exhibition List. Try Next Time.</div>';
                }

                const appHtml = `
                    <div style="background: rgba(197, 168, 92, 0.05); border: 1px solid rgba(197, 168, 92, 0.2); padding: 18px; border-radius: 12px; position: relative;">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                            <span style="font-weight:bold; color:#c5a85c; font-size:1.1rem;">${app.company_name}</span>
                            ${statusBadge}
                        </div>
                        <p style="font-size:0.85rem; color:#f5f6fa; margin-bottom:4px;">🎫 Event: <strong>${app.event}</strong></p>
                        <p style="font-size:0.85rem; color:#9aa0b4; margin-bottom:4px;">🏢 Booth: ${app.booth}</p>
                        <p style="font-size:0.85rem; color:#9aa0b4; margin-bottom:12px;">📅 Applied on: ${new Date(app.created_at).toLocaleDateString()}</p>
                        ${actionBtn}
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', appHtml);
            });
            container.style.display = 'grid';
            if (btn) btn.textContent = 'View Applications ▲';
        } else {
            container.innerHTML = `<div style="grid-column:1/-1; text-align:center; padding:25px; color:#777; background:rgba(197, 168, 92, 0.05); border:1px dashed rgba(197, 168, 92, 0.3); border-radius:10px;">
                <p style="font-size:1.5rem; margin-bottom:8px;">🏢</p>
                <p style="font-size:0.95rem;">No exhibitor applications found.</p>
                <a href="exhibitor.php" style="color:#c5a85c; font-weight:bold; text-decoration:underline;">Apply to be an Exhibitor →</a>
            </div>`;
            container.style.display = 'block';
            if (btn) btn.textContent = 'View Applications ▲';
        }
    } catch (e) {
        console.error(e);
        alert('Error fetching exhibitor applications.');
        if (btn) btn.textContent = 'View Applications';
    }
}

async function payExhibitorFee(applicationId, feeAmount) {
    const btn = document.getElementById(`payExhibitorBtn_${applicationId}`);
    if (btn) {
        btn.disabled = true;
        btn.textContent = 'Processing...';
    }
    
    try {
        const amountInPaise = feeAmount * 100;
        const orderRes = await fetch("api/index.php/public-payment/create-razorpay-order", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ amount: amountInPaise })
        });

        if (!orderRes.ok) {
            const errData = await orderRes.json();
            throw new Error(errData.error || "Failed to create order");
        }
        const orderData = await orderRes.json();

        var options = {
            key: "<?php echo RAZORPAY_KEY_ID; ?>",
            amount: amountInPaise,
            currency: "INR",
            name: "GLOBAL SPORTS ARENA",
            description: "Exhibitor Booth Payment",
            order_id: orderData.id,
            handler: async function (response) {
                const paymentId = response.razorpay_payment_id;
                
                // Update payment status via API
                const saveRes = await fetch("api/index.php/exhibitors/update-payment", {
                    method: "PUT",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ id: applicationId, razorpay_payment_id: paymentId })
                });
                const saveResult = await saveRes.json();
                if (saveResult.success) {
                    alert("Payment successful! Your QR pass is ready.");
                    toggleExhibitorApps(); // reload applications
                } else {
                    alert("Payment recorded, but there was an issue saving it. Please contact support.");
                }
            },
            theme: { color: "#c5a85c" }
        };

        if (!window.Razorpay) {
            throw new Error("Razorpay SDK could not be loaded. Please check your internet connection.");
        }
        const rzp = new window.Razorpay(options);
        
        rzp.on('payment.failed', function (response){
            alert("Payment Failed: " + response.error.description);
            if (btn) {
                btn.disabled = false;
                btn.textContent = `Pay Now ₹${parseFloat(feeAmount).toLocaleString('en-IN')}`;
            }
        });

        rzp.open();
    } catch (err) {
        console.error(err);
        alert(err.message);
        if (btn) {
            btn.disabled = false;
            btn.textContent = `Pay Now ₹${parseFloat(feeAmount).toLocaleString('en-IN')}`;
        }
    }
}
</script>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

