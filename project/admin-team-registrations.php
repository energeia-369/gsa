<?php
$pageTitle = "GLOBAL SPORTS ARENA | Team Registrations";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
<link rel="stylesheet" href="assets/css/AdminDashboard.css?v=7">
<div class="admin-dashboard px-4 sm:px-8 py-10" style="background: #0b0c10; color: #f5f6fa; min-height: 100vh;">
  <?php require_once __DIR__ . '/includes/admin_navbar.php'; ?>
  
  <div class="admin-header" style="border-bottom: 1px solid rgba(197, 168, 92, 0.2); padding-bottom: 20px;">
    <h1>🏆 Team Registrations</h1>
    <p>Manage all sports team tournament sign-ups and view payment statuses.</p>
  </div>

  <div id="adminGlobalLoading" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(11,12,16,0.8); z-index: 9999; justify-content: center; align-items: center; color: #c5a85c; font-size: 1.5rem; font-weight: bold;">
    ? Loading records...
  </div>

  <div class="admin-content" style="display: block; margin-top: 30px;">
      <h2 style="color: #c5a85c; margin: 0 0 20px 0;">Live Registrations Feed</h2>
      <div id="teamRegistrationsListContainer" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; width: 100%;">
        <p style="color: #9aa0b4; text-align: center; grid-column: 1 / -1;">Loading team registrations...</p>
      </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const token = localStorage.getItem("token");
    const role = localStorage.getItem("userRole");

    if (!token || role !== "ADMIN") {
        alert("Access Denied: Admin login required!");
        window.location.href = "login.php";
        return;
    }
    loadData();
});

async function loadData() {
    showLoading(true);
    try {
        const token = localStorage.getItem("token");
        const res = await fetch("api/index.php/event-registrations", {
            headers: { "Authorization": "Bearer " + token }
        });
        const data = await res.json();
        renderTeamRegistrations(data.data || data);
    } catch (err) {
        console.error("Load Error:", err);
    } finally {
        showLoading(false);
    }
}

function showLoading(show) {
    document.getElementById("adminGlobalLoading").style.display = show ? "flex" : "none";
}

function renderTeamRegistrations(registrations) {
    const container = document.getElementById("teamRegistrationsListContainer");
    if (!registrations || registrations.length === 0) {
        container.innerHTML = `<p style="color: #9aa0b4; text-align: center; padding: 40px;">No team registrations found in the database.</p>`;
        return;
    }

    container.innerHTML = registrations.map(r => `
        <div style="background: #12131c; border: 1px solid rgba(197,168,92,0.15); padding: 25px; border-radius: 16px; font-size: 0.95rem; display: flex; flex-direction: column; justify-content: space-between; height: 100%; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
          <div>
              <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                <strong style="color: #c5a85c; font-size: 1.25rem;">Team: ${r.team_name}</strong>
                <span style="color: #9aa0b4; font-size: 0.85rem;">${new Date(r.created_at || Date.now()).toLocaleDateString()}</span>
              </div>
              <div style="color: #f5f6fa; margin-bottom: 8px;">⚽ Sport: <strong>${r.sport}</strong> (${r.team_category}) | Members: ${r.team_members}</div>
              <div style="color: #9aa0b4; margin-bottom: 10px;">👤 Captain: ${r.captain_name} | 📞 ${r.captain_contact} | ✉️ ${r.email}</div>
              ${r.notes ? `<div style="color: #c5a85c; font-size: 0.85rem; margin-top: 10px; font-style: italic; background: rgba(197,168,92,0.05); padding: 12px; border-radius: 8px;">📝 Notes: ${r.notes}</div>` : ''}
          </div>
          <div style="color: #9aa0b4; margin-top: 15px; padding-top: 12px; border-top: 1px dashed rgba(255,255,255,0.1); display: flex; justify-content: space-between; align-items: center;">
            <span>💰 Fee: ₹${r.registration_fee}</span>
            <span>Status: <span style="color: ${r.payment_status === 'FREE' || r.payment_status === 'PAID' ? '#22c55e' : '#eab308'}; font-weight: bold; background: rgba(255,255,255,0.05); padding: 4px 10px; border-radius: 6px;">${r.payment_status}</span></span>
          </div>
        </div>
    `).join('');
}
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
