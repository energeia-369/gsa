<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'merchant') {
    header("Location: login.php");
    exit;
}
$pageTitle = "GLOBAL SPORTS ARENA | Merchant Panel";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<link rel="stylesheet" href="assets/css/Merchant.css?v=2">

<style>
/* Edit Profile Modal */
#editMerchantModal {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.45);
    z-index: 2000;
    justify-content: center;
    align-items: center;
}
.edit-merchant-box {
    background: #fff;
    border: 1px solid #B89C62;
    border-radius: 16px;
    width: 420px;
    max-width: 95vw;
    padding: 32px;
    box-shadow: 0 16px 50px rgba(138,122,95,0.25);
    position: relative;
    animation: slideUpModal 0.3s ease;
}
@keyframes slideUpModal {
    from { opacity: 0; transform: translateY(30px); }
    to   { opacity: 1; transform: translateY(0); }
}
.edit-merchant-box h3 {
    margin: 0 0 20px;
    color: #B89C62;
    font-size: 1.15rem;
    border-bottom: 1px solid rgba(184,156,98,0.2);
    padding-bottom: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.edit-merchant-box label {
    display: block;
    font-size: 0.82rem;
    color: #7A7061;
    font-weight: 600;
    margin-bottom: 5px;
    letter-spacing: 0.5px;
}
.edit-merchant-box input {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid rgba(184,156,98,0.4);
    border-radius: 8px;
    font-size: 0.92rem;
    color: #3A342B;
    background: #FDFAF5;
    margin-bottom: 14px;
    box-sizing: border-box;
    transition: border-color 0.2s;
}
.edit-merchant-box input:focus {
    outline: none;
    border-color: #B89C62;
}
.edit-merchant-box input:disabled {
    background: #f0ece4;
    color: #999;
    cursor: not-allowed;
}
.edit-merchant-divider {
    font-size: 0.78rem;
    color: #aaa;
    margin: 8px 0 14px;
    text-align: center;
}
.btn-save-profile {
    width: 100%;
    padding: 12px;
    background: linear-gradient(135deg, #B89C62, #8a7245);
    color: #fff;
    border: none;
    border-radius: 10px;
    font-size: 0.95rem;
    font-weight: 700;
    cursor: pointer;
    transition: opacity 0.2s, transform 0.1s;
    margin-top: 4px;
}
.btn-save-profile:hover { opacity: 0.9; transform: translateY(-1px); }
.btn-save-profile:active { transform: translateY(0); }
.close-edit-modal {
    position: absolute;
    top: 16px; right: 18px;
    background: none; border: none;
    font-size: 1.3rem; color: #999;
    cursor: pointer; line-height: 1;
}
.close-edit-modal:hover { color: #c62828; }
.profile-toast {
    display: none;
    position: fixed;
    bottom: 30px; left: 50%;
    transform: translateX(-50%);
    background: #2e7d32;
    color: #fff;
    padding: 12px 28px;
    border-radius: 30px;
    font-weight: 600;
    font-size: 0.9rem;
    z-index: 9999;
    box-shadow: 0 4px 20px rgba(46,125,50,0.3);
    animation: fadeInToast 0.3s ease;
}
@keyframes fadeInToast { from { opacity:0; bottom:10px; } to { opacity:1; bottom:30px; } }

/* Registration Card */
.merchant-reg-card {
    background: var(--bg-card);
    border: 1px solid var(--border-glass);
    border-radius: 16px;
    padding: 28px 32px;
    margin-top: 20px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}
.merchant-reg-card-title {
    margin: 0 0 4px;
    font-size: 1.05rem;
    color: var(--text-primary);
    font-weight: 700;
}
.merchant-reg-card-desc {
    margin: 0 0 20px;
    font-size: 0.85rem;
    color: var(--text-secondary);
}
.merchant-reg-btns {
    display: flex;
    gap: 14px;
    flex-wrap: wrap;
}
.merchant-reg-btn {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 11px 22px;
    border-radius: 30px;
    font-size: 0.88rem;
    font-weight: 700;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: transform 0.18s, box-shadow 0.18s, opacity 0.18s;
    letter-spacing: 0.3px;
    font-family: inherit;
}
.merchant-reg-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.12);
    opacity: 0.92;
}
.merchant-reg-btn--event {
    background: linear-gradient(135deg, #B89C62, #8a7245);
    color: #fff;
    box-shadow: 0 3px 10px rgba(184,156,98,0.35);
}
.merchant-reg-btn--visitor {
    background: linear-gradient(135deg, #2e7d32, #1b5e20);
    color: #fff;
    box-shadow: 0 3px 10px rgba(46,125,50,0.3);
}
.merchant-reg-btn--exhibitor {
    background: linear-gradient(135deg, #0288d1, #01579b);
    color: #fff;
    box-shadow: 0 3px 10px rgba(2,136,209,0.3);
}
.merchant-reg-btn--delegate {
    background: linear-gradient(135deg, #7b1fa2, #4a148c);
    color: #fff;
    box-shadow: 0 3px 10px rgba(123,31,162,0.3);
}
</style>

<div class="merchant-page">
  <div class="merchant-container w-full max-w-7xl mx-auto px-4">
    <div class="merchant-header-card" style="background: var(--bg-card); border: 1px solid var(--border-glass); padding: 2rem; border-radius: 12px; margin-top: 20px; margin-bottom: 2rem; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
      <div class="admin-badge" style="background: linear-gradient(135deg, #c5a85c 0%, #8c7237 100%); color: #0b0c10; font-weight: bold; display: inline-block; padding: 4px 12px; border-radius: 4px; margin-bottom: 10px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px;">
        🛍️ Merchant Portal
      </div>
      <h1 class="merchant-title" style="margin-top: 0; font-size: 2.2rem; font-family: 'Playfair Display', serif; color: var(--text-primary);">
        Welcome to Merchant Dashboard, <?php echo htmlspecialchars($_SESSION['merchant_name']); ?>!
      </h1>
      <p class="merchant-subtitle" style="margin-bottom: 0; color: var(--text-secondary); margin-top: 0.5rem;">
        Manage your store, track sales, and interact with the sports community.
      </p>
    </div>
    
    <!-- Quick Actions -->
    <div class="merchant-actions">
      <a href="merchant-inventory.php" class="merchant-action-card">
        <h3 class="merchant-action-title">📦 Manage Inventory</h3>
        <p class="merchant-action-desc">Add or edit your products</p>
      </a>
      <a href="#" class="merchant-action-card" onclick="openReceiveNxlModal(event)">
        <h3 class="merchant-action-title">💎 Receive NXL Payment</h3>
        <p class="merchant-action-desc">Charge NXL from a user's account</p>
      </a>
      <a href="#" class="merchant-action-card" onclick="openEditProfile(event)">
        <h3 class="merchant-action-title">✏️ Edit Profile</h3>
        <p class="merchant-action-desc">Update your name, phone &amp; password</p>
      </a>
    </div>

    <!-- Full-Width Passes & Registrations Card -->
    <div class="merchant-reg-card">
      <h3 class="merchant-reg-card-title">🎟️ Event Passes &amp; Registrations</h3>
      <p class="merchant-reg-card-desc">Register for events or view your QR entry passes below</p>
      <div class="merchant-reg-btns">
        <button onclick="viewMerchantEventPasses()" class="merchant-reg-btn merchant-reg-btn--event">🏆 Event QR Pass</button>
        <button onclick="viewMerchantPasses('delegate')" class="merchant-reg-btn merchant-reg-btn--delegate">🎖️ Delegate Pass QR</button>
        <button onclick="viewMerchantPasses('visitor')" class="merchant-reg-btn merchant-reg-btn--visitor">🎫 Visitor Pass QR</button>
        <button onclick="viewMerchantPasses('exhibitor')" class="merchant-reg-btn merchant-reg-btn--exhibitor">🏢 Exhibitor Pass QR</button>
      </div>
      <!-- Inline Event QR container -->
      <div id="merchantEventPassesContainer" style="display:none; margin-top:20px; padding-top:20px; border-top:1px dashed rgba(184,156,98,0.4); grid-template-columns: repeat(auto-fill, minmax(260px,1fr)); gap:16px;"></div>
      <!-- Inline Visitor/Exhibitor QR container -->
      <div id="merchantPassesContainer" style="display:none; margin-top:20px; padding-top:20px; border-top:1px dashed rgba(184,156,98,0.4); grid-template-columns: repeat(auto-fill, minmax(260px,1fr)); gap:16px;"></div>
    </div>

    <!-- Exhibitor Applications Card -->
    <div class="merchant-reg-card" style="display: flex; flex-direction: column;">
      <div style="display: flex; align-items: center; gap: 25px; flex-wrap: wrap;">
          <div style="font-size: 3rem; background: rgba(197, 168, 92, 0.1); padding: 15px; border-radius: 12px; margin-right: 15px;">🏢</div>
          <div style="flex: 1; min-width: 250px;">
            <h3 class="merchant-reg-card-title" style="font-size: 1.3rem;">My Exhibitor Applications</h3>
            <p class="merchant-reg-card-desc" style="margin: 0; font-size: 0.95rem;">Track the status of your event booth applications</p>
          </div>
          <div>
            <button class="merchant-reg-btn" id="btnViewExhibitorApps" onclick="toggleExhibitorApps()" style="background: linear-gradient(135deg, #10b981 0%, #047857 100%); color: #fff; border: 1px solid #059669;">View Applications</button>
          </div>
      </div>
      <div id="exhibitorAppsContainer" style="display: none; width: 100%; margin-top: 25px; padding-top: 25px; border-top: 1px dashed rgba(184,156,98,0.4); grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
      </div>
    </div>

    <!-- Analytics Section -->
    <div class="merchant-analytics">
      <h3 class="merchant-analytics-title">📊 Sales Overview</h3>
      <p class="merchant-analytics-content">(Data will be populated here)</p>
    </div>
  </div>
</div>




<!-- Edit Profile Modal -->
<div id="editMerchantModal">
  <div class="edit-merchant-box">
    <button class="close-edit-modal" onclick="closeEditProfile()">✕</button>
    <h3>✏️ Edit Profile</h3>

    <label>Full Name</label>
    <input type="text" id="editMerchantName" placeholder="Your name" />

    <label>Email Address</label>
    <input type="email" id="editMerchantEmail" disabled />

    <label>Phone Number</label>
    <input type="tel" id="editMerchantPhone" placeholder="Phone number" />

    <div class="edit-merchant-divider">— Change Password (optional) —</div>

    <label>New Password</label>
    <input type="password" id="editMerchantPassword" placeholder="Leave blank to keep current" />

    <label>Confirm New Password</label>
    <input type="password" id="editMerchantPasswordConfirm" placeholder="Repeat new password" />

    <button class="btn-save-profile" onclick="saveMerchantProfile()">💾 Save Changes</button>
  </div>
</div>

<!-- Success Toast -->
<div class="profile-toast" id="profileToast">✅ Profile updated successfully!</div>

<!-- Delegate Approved Modal -->
<div id="delegateApprovedModal" class="modal-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.4); z-index:9999; justify-content:center; align-items:center; backdrop-filter:blur(4px);">
  <div class="modal-content" style="background:#FFFFFF; border:1px solid #c5a85c; border-radius:12px; max-width:400px; width:90%; padding:30px; text-align:center; box-shadow:0 10px 40px rgba(197,168,92,0.2); position:relative;">
    <button onclick="document.getElementById('delegateApprovedModal').style.display='none'" style="position:absolute; top:10px; right:15px; background:none; border:none; font-size:1.5rem; cursor:pointer; color:#999;">&times;</button>
    <div style="font-size:3rem; margin-bottom:15px; color: #22c55e;">🎉</div>
    <h2 style="color:#c5a85c; margin:0 0 15px 0; font-size:1.5rem;">Congratulations!</h2>
    <p style="color:#333; font-size:1.1rem; font-weight: bold; margin-bottom:10px; line-height:1.5;">
      Delegate Registration Approved
    </p>
    <p style="color:#7A7061; font-size:0.95rem; margin-bottom:25px; line-height:1.5;">
      Your application has been reviewed and approved by the admin. Please proceed to payment to finalize your registration.
    </p>
    <div style="display:flex; justify-content:center;">
      <a id="delegatePaymentBtn" href="#" style="padding:12px 25px; border-radius:6px; background:#B89C62; border:none; color:#FFFFFF; font-weight:bold; cursor:pointer; text-decoration: none; transition:all 0.3s;" onmouseover="this.style.background='#8A7A5F'" onmouseout="this.style.background='#B89C62'">Proceed to Payment</a>
    </div>
  </div>
</div>

<!-- Delegate Rejected Modal -->
<div id="delegateRejectedModal" class="modal-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.4); z-index:9999; justify-content:center; align-items:center; backdrop-filter:blur(4px);">
  <div class="modal-content" style="background:#FFFFFF; border:1px solid #ef4444; border-radius:12px; max-width:400px; width:90%; padding:30px; text-align:center; box-shadow:0 10px 40px rgba(239,68,68,0.2); position:relative;">
    <button onclick="document.getElementById('delegateRejectedModal').style.display='none'" style="position:absolute; top:10px; right:15px; background:none; border:none; font-size:1.5rem; cursor:pointer; color:#999;">&times;</button>
    <div style="font-size:3rem; margin-bottom:15px; color: #ef4444;">❌</div>
    <h2 style="color:#ef4444; margin:0 0 15px 0; font-size:1.5rem;">Registration Unsuccessful</h2>
    <p style="color:#7A7061; font-size:0.95rem; margin-bottom:25px; line-height:1.5;">
      We regret to inform you that your registration for delegates was unsuccessful.
    </p>
    <div style="display:flex; justify-content:center;">
      <button onclick="dismissDelegateRejectedModal()" style="padding:10px 20px; border-radius:6px; background:transparent; border:1px solid #ef4444; color:#ef4444; cursor:pointer; font-weight:bold; transition:all 0.3s;">Close</button>
    </div>
  </div>
</div>

<script>
const MERCHANT_EMAIL = "<?php echo addslashes($_SESSION['merchant_email'] ?? ''); ?>";
const MERCHANT_NAME  = "<?php echo addslashes($_SESSION['merchant_name'] ?? ''); ?>";

document.addEventListener('DOMContentLoaded', () => {
    if (MERCHANT_EMAIL) {
        checkMerchantDelegateStatus(MERCHANT_EMAIL);
    }
});

async function checkMerchantDelegateStatus(email) {
    try {
        const res = await fetch(`api/index.php/user/delegate_passes?email=${encodeURIComponent(email)}`);
        const result = await res.json();
        
        if (result.success && result.passes && result.passes.length > 0) {
            for (const pass of result.passes) {
                if (pass.registration_status === 'Rejected') {
                    if (!sessionStorage.getItem('delegateRejectedShown_' + pass.delegate_id)) {
                        document.getElementById('delegateRejectedModal').style.display = 'flex';
                        window.currentRejectedDelegateId = pass.delegate_id;
                        return;
                    }
                }
                if (pass.registration_status === 'Approved' && pass.payment_status !== 'Paid') {
                    document.getElementById('delegatePaymentBtn').href = `delegate-payment.php?id=${pass.delegate_id}`;
                    document.getElementById('delegateApprovedModal').style.display = 'flex';
                    return;
                }
            }
        }
    } catch (e) {
        console.error("Failed to check delegate status", e);
    }
}

function dismissDelegateRejectedModal() {
    if (window.currentRejectedDelegateId) {
        sessionStorage.setItem('delegateRejectedShown_' + window.currentRejectedDelegateId, 'true');
    }
    document.getElementById('delegateRejectedModal').style.display = 'none';
}

/* -------- Edit Profile -------- */
function openEditProfile(e) {
    if (e) e.preventDefault();
    document.getElementById('editMerchantEmail').value = MERCHANT_EMAIL;
    document.getElementById('editMerchantName').value  = MERCHANT_NAME;
    // fetch fresh phone from DB
    fetch(`api/index.php/merchant/profile?email=${encodeURIComponent(MERCHANT_EMAIL)}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('editMerchantName').value  = data.merchant.merchant_name;
                document.getElementById('editMerchantPhone').value = data.merchant.phone;
            }
        });
    document.getElementById('editMerchantModal').style.display = 'flex';
}

function closeEditProfile() {
    document.getElementById('editMerchantModal').style.display = 'none';
    document.getElementById('editMerchantPassword').value = '';
    document.getElementById('editMerchantPasswordConfirm').value = '';
}

async function saveMerchantProfile() {
    const name  = document.getElementById('editMerchantName').value.trim();
    const phone = document.getElementById('editMerchantPhone').value.trim();
    const pwd   = document.getElementById('editMerchantPassword').value;
    const pwd2  = document.getElementById('editMerchantPasswordConfirm').value;

    if (!name || !phone) { alert('Name and phone are required.'); return; }
    if (pwd && pwd !== pwd2) { alert('Passwords do not match.'); return; }
    if (pwd && pwd.length < 6) { alert('Password must be at least 6 characters.'); return; }

    const btn = document.querySelector('.btn-save-profile');
    btn.textContent = 'Saving…';
    btn.disabled = true;

    try {
        const payload = { email: MERCHANT_EMAIL, merchant_name: name, phone: phone };
        if (pwd) payload.new_password = pwd;

        const res = await fetch('api/index.php/merchant/update-profile', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const data = await res.json();

        if (data.success) {
            closeEditProfile();
            // show toast
            const toast = document.getElementById('profileToast');
            toast.style.display = 'block';
            setTimeout(() => { toast.style.display = 'none'; }, 3000);
            // update greeting on page
            document.querySelector('.merchant-title').textContent =
                `Welcome to Merchant Dashboard, ${data.merchant_name}!`;
        } else {
            alert(data.message || 'Update failed. Please try again.');
        }
    } catch(err) {
        console.error(err);
        alert('Error saving profile.');
    } finally {
        btn.textContent = '💾 Save Changes';
        btn.disabled = false;
    }
}

// close modal on backdrop click
document.getElementById('editMerchantModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditProfile();
});

/* -------- Inline Event QR viewer -------- */
async function viewMerchantEventPasses() {
    if (!MERCHANT_EMAIL) { alert("You must be logged in."); return; }
    try {
        const res = await fetch(`api/index.php/user/event_passes?email=${encodeURIComponent(MERCHANT_EMAIL)}`);
        const result = await res.json();
        const container = document.getElementById('merchantEventPassesContainer');
        const passContainer = document.getElementById('merchantPassesContainer');

        if (result.success && result.passes && result.passes.length > 0) {
            container.innerHTML = '';
            // Hide the other container
            passContainer.style.display = 'none';

            result.passes.forEach(pass => {
                const isExpired  = pass.status === 'expired';
                const qrUrl      = `https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=${encodeURIComponent(pass.qrUrl)}`;
                const cardBg     = isExpired ? 'rgba(198,40,40,0.08)' : 'rgba(184,156,98,0.1)';
                const cardBorder = isExpired ? '#c62828' : '#B89C62';
                const badge      = isExpired
                    ? `<span style="background:#c62828;color:#fff;font-size:0.7rem;font-weight:700;padding:3px 9px;border-radius:20px;animation:flashBadge 1.2s ease infinite;">⚠️ EXPIRED</span>`
                    : `<span style="background:#2e7d32;color:#fff;font-size:0.7rem;font-weight:700;padding:3px 9px;border-radius:20px;">✓ ACTIVE</span>`;
                const qrFilter   = isExpired ? 'filter:grayscale(1) opacity(0.5);' : '';
                const evtDate    = pass.event_date
                    ? new Date(pass.event_date).toLocaleDateString('en-US',{year:'numeric',month:'short',day:'numeric'})
                    : 'N/A';

                container.insertAdjacentHTML('beforeend', `
                    <div style="background:${cardBg};border:2px solid ${cardBorder};padding:18px;border-radius:12px;text-align:center;">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                            <span style="font-weight:700;color:#8a7245;font-size:0.88rem;">🏆 Event Pass</span>
                            ${badge}
                        </div>
                        <p style="font-size:0.8rem;color:#555;margin:4px 0;">🏅 Sport: <strong>${pass.sport||'N/A'}</strong></p>
                        <p style="font-size:0.8rem;color:#555;margin:4px 0;">👥 Team: <strong>${pass.team_name||'N/A'}</strong></p>
                        <p style="font-size:0.8rem;color:#555;margin:4px 0 12px;">🗓 Date: <strong>${evtDate}</strong></p>
                        <img src="${qrUrl}" alt="Event QR" style="width:160px;height:160px;background:#fff;padding:8px;border-radius:8px;${qrFilter}" />
                        <p style="color:#8a7245;font-weight:700;font-size:0.88rem;margin-top:10px;">${pass.captain_name}</p>
                        <p style="color:#888;font-size:0.74rem;">${pass.registration_type||''} • ${pass.team_category||''}</p>
                        <p style="color:#777;font-size:0.72rem;margin-top:4px;">Registration #${pass.id}</p>
                    </div>
                `);
            });

            if (container.style.display === 'none' || container.dataset.shown !== '1') {
                container.style.display = 'grid';
                container.dataset.shown = '1';
            } else {
                container.style.display = 'none';
                container.dataset.shown = '0';
            }
        } else {
            alert('No event registrations found. Please register for an event first.');
        }
    } catch (err) {
        console.error(err);
        alert('Error fetching event passes.');
    }
}

/* -------- Inline Pass QR viewer -------- */
async function viewMerchantPasses(passType) {
    if (!MERCHANT_EMAIL) { alert("You must be logged in to view your passes."); return; }

    try {
        const res = await fetch(`api/index.php/user/all_passes?email=${encodeURIComponent(MERCHANT_EMAIL)}`);
        const result = await res.json();
        const container = document.getElementById('merchantPassesContainer');

        if (result.success && result.passes && result.passes.length > 0) {
            const filtered = result.passes.filter(p => p.type === passType);
            if (filtered.length === 0) {
                alert(`No ${passType} passes found for your account. Please register first.`);
                return;
            }

            container.innerHTML = '';
            filtered.forEach(pass => {
                const typeStr    = pass.type === 'visitor' ? 'Visitor Pass' : (pass.type === 'delegate' ? 'Delegate Pass' : 'Exhibitor Pass');
                const isExpired  = pass.status === 'expired';
                const qrUrl      = `https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=${encodeURIComponent(pass.qrUrl)}`;
                const passName   = pass.pass_name || MERCHANT_NAME;
                const cardBg     = isExpired ? 'rgba(198,40,40,0.08)' : 'rgba(46,125,50,0.12)';
                const cardBorder = isExpired ? '#c62828' : '#2e7d32';
                const badge      = isExpired
                    ? `<span style="background:#c62828;color:#fff;font-size:0.7rem;font-weight:700;padding:3px 9px;border-radius:20px;animation:flashBadge 1.2s ease infinite;">⚠️ EXPIRED</span>`
                    : `<span style="background:#2e7d32;color:#fff;font-size:0.7rem;font-weight:700;padding:3px 9px;border-radius:20px;">✓ ACTIVE</span>`;
                const qrFilter   = isExpired ? 'filter:grayscale(1) opacity(0.5);' : '';
                const evtDate    = pass.event_date
                    ? new Date(pass.event_date).toLocaleDateString('en-US',{year:'numeric',month:'short',day:'numeric'})
                    : 'N/A';

                container.insertAdjacentHTML('beforeend', `
                    <div style="background:${cardBg};border:2px solid ${cardBorder};padding:18px;border-radius:12px;text-align:center;">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                            <span style="font-weight:700;color:#1b5e20;font-size:0.88rem;">${typeStr}</span>
                            ${badge}
                        </div>
                        <p style="font-size:0.8rem;color:#555;margin:4px 0;">📅 Event: <strong>${pass.event||'N/A'}</strong></p>
                        <p style="font-size:0.8rem;color:#555;margin:4px 0 12px;">🗓 Date: <strong>${evtDate}</strong></p>
                        <img src="${qrUrl}" alt="QR Code" style="width:155px;height:155px;background:#fff;padding:8px;border-radius:8px;${qrFilter}" />
                        <p style="color:#1b5e20;font-weight:700;font-size:0.88rem;margin-top:10px;">${passName}</p>
                        <p style="color:#777;font-size:0.74rem;margin-top:3px;">Registered: ${new Date(pass.created_at).toLocaleDateString()}</p>
                    </div>
                `);
            });

            // Toggle or switch pass type
            if (container.style.display === 'none' || container.dataset.lastType !== passType) {
                container.style.display = 'grid';
            } else {
                container.style.display = 'none';
            }
            container.dataset.lastType = passType;
        } else {
            alert('No registered passes found. Please register for an event first.');
        }
    } catch (err) {
        console.error(err);
        alert('Error fetching passes.');
    }
}
</script>

<style>
@keyframes flashBadge {
    0%,100% { opacity:1; }
    50% { opacity:0.45; }
}
</style>

<!-- Receive NXL Modal -->
<div id="receiveNxlModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:2000; justify-content:center; align-items:center;">
  <div class="edit-merchant-box">
    <button class="close-edit-modal" onclick="closeReceiveNxlModal()">✕</button>
    <h3>💎 Receive NXL Payment</h3>
    
    <label>User Identifier (Email or Phone)</label>
    <div style="position:relative;">
      <input type="text" id="receiveNxlUser" placeholder="Enter user's email or phone" autocomplete="off" />
      <div id="receiveNxlDropdown" style="display:none; position:absolute; top:100%; left:0; width:100%; max-height:200px; overflow-y:auto; background:#fff; border:1px solid rgba(184,156,98,0.4); border-radius:8px; z-index:10; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
      </div>
    </div>

    <label>NXL Amount to Charge</label>
    <input type="number" id="receiveNxlAmount" placeholder="e.g. 50" min="1" step="1" />

    <button class="btn-save-profile" onclick="processNxlPayment()">Charge NXL</button>
  </div>
</div>

<script>
// Receive NXL Payment Modal Logic
let searchTimeout = null;

document.addEventListener('DOMContentLoaded', () => {
    const userInput = document.getElementById('receiveNxlUser');
    const dropdown = document.getElementById('receiveNxlDropdown');

    userInput.addEventListener('input', (e) => {
        const query = e.target.value.trim();
        clearTimeout(searchTimeout);
        
        if (query.length < 2) {
            dropdown.style.display = 'none';
            return;
        }

        searchTimeout = setTimeout(async () => {
            try {
                const res = await fetch(`api/index.php/merchant/search-users?q=${encodeURIComponent(query)}`);
                const data = await res.json();
                
                if (data.success && data.users.length > 0) {
                    dropdown.innerHTML = '';
                    data.users.forEach(user => {
                        const item = document.createElement('div');
                        item.style.padding = '8px 12px';
                        item.style.cursor = 'pointer';
                        item.style.borderBottom = '1px solid #f0f0f0';
                        item.style.fontSize = '0.9rem';
                        item.innerHTML = `<strong>${user.name}</strong><br/><span style="color:#666; font-size:0.8rem;">${user.email} | ${user.phone}</span>`;
                        
                        item.addEventListener('mouseover', () => item.style.background = '#f9f6f0');
                        item.addEventListener('mouseout', () => item.style.background = '#fff');
                        
                        item.addEventListener('click', () => {
                            userInput.value = user.email;
                            dropdown.style.display = 'none';
                        });
                        dropdown.appendChild(item);
                    });
                    dropdown.style.display = 'block';
                } else {
                    dropdown.style.display = 'none';
                }
            } catch (err) {
                console.error("Search error", err);
            }
        }, 300);
    });

    // Close dropdown if clicked outside
    document.addEventListener('click', (e) => {
        if (e.target !== userInput && !dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });
});

function openReceiveNxlModal(e) {
    if(e) e.preventDefault();
    document.getElementById('receiveNxlUser').value = '';
    document.getElementById('receiveNxlAmount').value = '';
    document.getElementById('receiveNxlDropdown').style.display = 'none';
    document.getElementById('receiveNxlModal').style.display = 'flex';
}

function closeReceiveNxlModal() {
    document.getElementById('receiveNxlModal').style.display = 'none';
}

async function processNxlPayment() {
    const userIdentifier = document.getElementById('receiveNxlUser').value.trim();
    const amount = document.getElementById('receiveNxlAmount').value.trim();

    if (!userIdentifier || !amount) {
        alert("Please fill in both the user identifier and NXL amount.");
        return;
    }

    try {
        const res = await fetch('api/index.php/merchant/receive-nxl', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ userIdentifier, amount: parseInt(amount, 10) })
        });
        const data = await res.json();
        
        if (data.success) {
            alert("✅ " + data.message);
            closeReceiveNxlModal();
        } else {
            alert(data.message || "Failed to process NXL payment.");
        }
    } catch (err) {
        console.error(err);
        alert("JS Error: " + err.message);
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
    
    const email = MERCHANT_EMAIL;
    if (!email) {
        container.innerHTML = '<p style="color:#ef4444;">Please log in to view your applications.</p>';
        container.style.display = 'block';
        return;
    }
    
    if (btn) btn.textContent = '⏳ Loading...';
    
    try {
        const res = await fetch(`api/index.php/user-exhibitor-applications?email=${encodeURIComponent(email)}`);
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
                        <p style="font-size:0.85rem; color:#333; margin-bottom:4px;">🎫 Event: <strong>${app.event}</strong></p>
                        <p style="font-size:0.85rem; color:#555; margin-bottom:4px;">🏢 Booth: ${app.booth}</p>
                        <p style="font-size:0.85rem; color:#555; margin-bottom:12px;">📅 Applied on: ${new Date(app.created_at).toLocaleDateString()}</p>
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
        container.innerHTML = '<p style="color:#ef4444;">Error loading applications.</p>';
        container.style.display = 'block';
        if (btn) btn.textContent = 'View Applications';
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>