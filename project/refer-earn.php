<?php
$pageTitle = "GLOBAL SPORTS ARENA | Share & Earn";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<link rel="stylesheet" href="assets/css/Credits.css?v=2">

<div class="credits-page overflow-x-hidden">
  <div class="credits-hero" style="background: linear-gradient(135deg, #12131c 0%, #1a1b2e 100%);">
    <div class="credits-hero-content max-w-4xl mx-auto px-4 text-center py-16">
      <div class="hero-icon">🤝</div>
      <h1>Share & <span class="highlight">Earn</span></h1>
      <p>Invite your friends to Global Sports Arena and earn rewards together!</p>
    </div>
  </div>

  <div class="earn-section" style="max-width: 800px; margin: 40px auto; text-align: center;">
    <div class="section-header">
      <h2>Your Unique Referral Link</h2>
      <p>Share this link with your friends to earn 100 NXL Credits for each successful registration.</p>
    </div>
    
    <div style="background: #12131c; padding: 30px; border-radius: 16px; border: 1px dashed #c5a85c; margin-top: 20px;">
      <input type="text" id="referralLink" readonly 
             style="width: 80%; padding: 15px; border-radius: 8px; border: 1px solid rgba(197, 168, 92, 0.3); background: rgba(0,0,0,0.2); color: #c5a85c; font-size: 1.1rem; text-align: center; margin-bottom: 20px;" 
             value="Loading...">
      
      <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
        <button onclick="copyLink()" class="earn-btn" style="padding: 12px 25px; font-size: 1.1rem; min-width: 150px;">
          📋 Copy Link
        </button>
        <button onclick="shareWhatsApp()" class="earn-btn" style="background: #25D366; color: #1a1a1a; border: none; padding: 12px 25px; font-size: 1.1rem; min-width: 150px;">
          📱 WhatsApp
        </button>
      </div>
    </div>

    <div class="referral-dashboard" style="background: #12131c; border: 1px solid rgba(197, 168, 92, 0.4); border-radius: 16px; padding: 40px; margin-top: 40px;">
      <h3 style="color: #c5a85c; margin-bottom: 30px; font-size: 1.5rem; text-transform: uppercase; letter-spacing: 1px;">🎁 Your Referral Dashboard</h3>
      
      <div style="display: flex; justify-content: space-around; flex-wrap: wrap; gap: 20px;">
        <div style="text-align: center; flex: 1; min-width: 150px;">
          <h2 style="font-size: 3rem; color: #f5f6fa; margin: 0;">0</h2>
          <p style="color: #9aa0b4; font-size: 1.1rem; margin-top: 5px;">Links Shared</p>
        </div>
        <div style="text-align: center; flex: 1; min-width: 150px;">
          <h2 style="font-size: 3rem; color: #f5f6fa; margin: 0;">0</h2>
          <p style="color: #9aa0b4; font-size: 1.1rem; margin-top: 5px;">Friends Joined</p>
        </div>
        <div style="text-align: center; flex: 1; min-width: 150px;">
          <h2 style="font-size: 3rem; color: #c5a85c; margin: 0;">0</h2>
          <p style="color: #9aa0b4; font-size: 1.1rem; margin-top: 5px;">NXL Credits Earned</p>
        </div>
      </div>

      <div style="margin-top: 40px; text-align: center;">
        <button class="earn-btn" style="background: linear-gradient(135deg, #c5a85c 0%, #8c7237 100%); color: #0b0c10; padding: 15px 40px; font-size: 1.2rem; border: none; border-radius: 8px; font-weight: bold; opacity: 0.6; cursor: not-allowed;" onclick="alert('No bonus available to claim yet. Keep referring friends!')">
          Claim Bonus
        </button>
        <p style="color: #9aa0b4; font-size: 0.95rem; margin-top: 15px;">Earn 100 NXL Credits for each friend that joins using your link!</p>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const email = localStorage.getItem("userEmail");
    if(!email) {
        alert("Please login first to access Share & Earn");
        window.location.href = "login.php";
        return;
    }
    
    const refCode = btoa(email).substring(0, 8).toUpperCase();
    const link = window.location.origin + window.location.pathname.replace('refer-earn.php', 'register.php') + "?ref=" + refCode;
    document.getElementById("referralLink").value = link;
});

function copyLink() {
    const linkInput = document.getElementById("referralLink");
    linkInput.select();
    linkInput.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(linkInput.value);
    alert("Referral link copied to clipboard!");
}

function shareWhatsApp() {
    const link = document.getElementById("referralLink").value;
    const text = `Join Global Sports Arena using my referral link and get 100 NXL Credits instantly! ${link}`;
    window.open(`https://api.whatsapp.com/send?text=${encodeURIComponent(text)}`, '_blank');
}
</script>

<style>
/* Light theme overrides for this page */
body.light-theme .credits-hero {
  background: linear-gradient(135deg, #eae1c9 0%, #f5f5dc 100%) !important;
}
body.light-theme .earn-section > div[style*="background: #12131c"],
body.light-theme .referral-dashboard {
  background: #ffffff !important;
  border-color: rgba(197, 168, 92, 0.4) !important;
}
body.light-theme .referral-dashboard h2 {
  color: #1a1a1a !important;
}
body.light-theme .referral-dashboard p {
  color: #4a4a4a !important;
}
body.light-theme #referralLink {
  background: #f5f5dc !important;
  color: #1a1a1a !important;
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
