<?php
$pageTitle = "GLOBAL SPORTS ARENA | Contact Enquiries";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
<link rel="stylesheet" href="assets/css/AdminDashboard.css?v=7">
<div class="admin-dashboard px-4 sm:px-8 py-10" style="background: #0b0c10; color: #f5f6fa; min-height: 100vh;">
  <?php require_once __DIR__ . '/includes/admin_navbar.php'; ?>
  
  <div class="admin-header" style="border-bottom: 1px solid rgba(197, 168, 92, 0.2); padding-bottom: 20px; max-width: 800px; margin: 0 auto;">
    <h1>✉️ Contact Form Enquiries</h1>
    <p>View all messages submitted through the contact form.</p>
  </div>

  <!-- Loading Overlay -->
  <div id="adminGlobalLoading" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(11,12,16,0.8); z-index: 9999; justify-content: center; align-items: center; color: #c5a85c; font-size: 1.5rem; font-weight: bold;">
    ? Loading enquiries...
  </div>

  <div class="admin-content" style="display: block; margin-top: 40px; max-width: 800px; margin-left: auto; margin-right: auto;">
    <div class="admin-card" style="background: #12131c; border: 1px solid rgba(197, 168, 92, 0.2); border-radius: 20px; padding: 35px; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
        <h2 style="color: #c5a85c; margin: 0 0 20px 0;">✉️ Contact Form Enquiries</h2>
        <div id="enquiriesListContainer" style="display: grid; gap: 15px;">
          <p style="color: #9aa0b4; text-align: center;">Loading enquiries...</p>
        </div>
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

    loadEnquiries();
});

function showLoading(show) {
    const loader = document.getElementById("adminGlobalLoading");
    if (loader) loader.style.display = show ? "flex" : "none";
}

async function loadEnquiries() {
    showLoading(true);
    try {
        const token = localStorage.getItem("token");
        const contactRes = await fetch("api/index.php/contact", {
            headers: { "Authorization": "Bearer " + token }
        });
        const enquiries = await contactRes.json();
        renderEnquiries(enquiries);
    } catch (err) {
        console.error("Failed to load enquiries:", err);
        document.getElementById("enquiriesListContainer").innerHTML = `<p style="color: #ef4444; text-align: center;">Error loading enquiries.</p>`;
    } finally {
        showLoading(false);
    }
}

function renderEnquiries(enquiries) {
    const container = document.getElementById("enquiriesListContainer");
    if (enquiries.length === 0) {
        container.innerHTML = `<p style="color: #9aa0b4; text-align: center;">No contact form enquiries in database.</p>`;
        return;
    }

    container.innerHTML = enquiries.map(e => `
        <div style="background: rgba(11,12,16,0.6); padding: 20px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05);">
          <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
            <strong style="color: #f5f6fa; font-size: 1.1rem; text-transform: uppercase;">${e.name}</strong>
            <span style="font-size: 0.9rem; color: #9aa0b4;">${e.date}</span>
          </div>
          <div style="font-size: 0.95rem; color: #c5a85c; margin-bottom: 12px;">?? ${e.email} | Sub: ${e.subject}</div>
          <div style="font-size: 0.95rem; color: #9aa0b4; line-height: 1.5;">${e.message}</div>
        </div>
    `).join('');
}
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
