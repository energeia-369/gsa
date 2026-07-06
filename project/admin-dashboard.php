<?php
$pageTitle = "GLOBAL SPORTS ARENA | System Operations";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<link rel="stylesheet" href="assets/css/AdminDashboard.css?v=7">

<div class="admin-dashboard px-4 sm:px-8 py-10" style="background: #0b0c10; color: #f5f6fa; min-height: 100vh;">
  <?php require_once __DIR__ . '/includes/admin_navbar.php'; ?>
  <!-- Header -->
  <div class="admin-header" style="border-bottom: 1px solid rgba(197, 168, 92, 0.2); padding-bottom: 20px;">
    <div class="header-left">
      <div class="admin-badge" style="background: linear-gradient(135deg, #c5a85c 0%, #8c7237 100%); color: #0b0c10; font-weight: bold; display: inline-block; padding: 4px 12px; border-radius: 4px; margin-bottom: 10px;">
        🛡️ Administrative Core
      </div>
      <h1>System Operations</h1>
      <p>Real-time tournament CRUD controls, NXL ledger wallets adjustments, and synchronized orders listings in MySQL</p>
    </div>
  </div>

  <!-- Loading Overlay -->
  <div id="adminGlobalLoading" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(11,12,16,0.8); z-index: 9999; justify-content: center; align-items: center; color: #c5a85c; font-size: 1.5rem; font-weight: bold;">
    ⏳ Processing request...
  </div>

  <!-- Dynamic KPI Stats Grid -->
  <div class="stats-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-8">
    <div class="stat-card" style="background: #12131c; border: 1px solid rgba(197, 168, 92, 0.15); padding: 20px; border-radius: 12px; display: flex; align-items: center; gap: 15px;">
      <div class="stat-icon" style="font-size: 2rem;">💰</div>
      <div class="stat-info">
        <h3 style="font-size: 0.9rem; color: #9aa0b4; margin: 0 0 5px 0;">Live Total Sales</h3>
        <p class="stat-value" id="statTotalSales" style="color: #22c55e; font-size: 1.5rem; font-weight: bold; margin: 0;">₹0</p>
        <span class="stat-change" style="font-size: 0.75rem; color: #9aa0b4;">Synchronized DB</span>
      </div>
    </div>

    <div class="stat-card" style="background: #12131c; border: 1px solid rgba(197, 168, 92, 0.15); padding: 20px; border-radius: 12px; display: flex; align-items: center; gap: 15px;">
      <div class="stat-icon" style="font-size: 2rem;">💎</div>
      <div class="stat-info">
        <h3 style="font-size: 0.9rem; color: #9aa0b4; margin: 0 0 5px 0;">Total NXL Issued</h3>
        <p class="stat-value" id="statTotalNxl" style="color: #c5a85c; font-size: 1.5rem; font-weight: bold; margin: 0;">0 Coins</p>
        <span class="stat-change" style="font-size: 0.75rem; color: #9aa0b4;">Loyalty Ledger</span>
      </div>
    </div>

    <div class="stat-card" style="background: #12131c; border: 1px solid rgba(197, 168, 92, 0.15); padding: 20px; border-radius: 12px; display: flex; align-items: center; gap: 15px;">
      <div class="stat-icon" style="font-size: 2rem;">👥</div>
      <div class="stat-info">
        <h3 style="font-size: 0.9rem; color: #9aa0b4; margin: 0 0 5px 0;">Active Customers</h3>
        <p class="stat-value" id="statActiveCustomers" style="font-size: 1.5rem; font-weight: bold; margin: 0;">0 Users</p>
        <span class="stat-change" style="font-size: 0.75rem; color: #9aa0b4;">Logged Profile</span>
      </div>
    </div>

    <div class="stat-card" style="background: #12131c; border: 1px solid rgba(197, 168, 92, 0.15); padding: 20px; border-radius: 12px; display: flex; align-items: center; gap: 15px;">
      <div class="stat-icon" style="font-size: 2rem;">💼</div>
      <div class="stat-info">
        <h3 style="font-size: 0.9rem; color: #9aa0b4; margin: 0 0 5px 0;">Active Merchants</h3>
        <p class="stat-value" id="statMerchants" style="color: #38bdf8; font-size: 1.5rem; font-weight: bold; margin: 0;">0 Merchants</p>
        <span class="stat-change" style="font-size: 0.75rem; color: #9aa0b4;">Registered Partners</span>
      </div>
    </div>
    
    <div class="stat-card" style="background: #12131c; border: 1px solid rgba(197, 168, 92, 0.15); padding: 20px; border-radius: 12px; display: flex; align-items: center; gap: 15px; cursor: pointer;" onclick="window.location.href='admin-delegates.php'">
      <div class="stat-icon" style="font-size: 2rem;">🏅</div>
      <div class="stat-info">
        <h3 style="font-size: 0.9rem; color: #9aa0b4; margin: 0 0 5px 0;">Delegate Registrations</h3>
        <p class="stat-value" id="statDelegates" style="color: #c5a85c; font-size: 1.5rem; font-weight: bold; margin: 0;">0 Delegates</p>
        <span class="stat-change" style="font-size: 0.75rem; color: #9aa0b4;">Manage Delegates →</span>
      </div>
    </div>
  </div>

  <div class="admin-content grid grid-cols-1 gap-8 mt-10">
    
    <!-- NXL Ledger Activity Panel -->
    <div class="dashboard-card" style="background: #12131c; border: 1px solid rgba(197, 168, 92, 0.15); border-radius: 12px; padding: 25px;">
        <h2 style="color: #c5a85c; margin-top: 0; font-size: 1.4rem;">Recent NXL Ledger Activity</h2>
        <p style="color: #9aa0b4; font-size: 0.9rem; margin-bottom: 20px;">Live tracking of NXL credits credited and debited across all users.</p>
        
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; color: #f5f6fa; text-align: left;">
                <thead>
                    <tr style="border-bottom: 1px solid rgba(197, 168, 92, 0.3);">
                        <th style="padding: 12px; color: #c5a85c;">Date</th>
                        <th style="padding: 12px; color: #c5a85c;">User</th>
                        <th style="padding: 12px; color: #c5a85c;">Action</th>
                        <th style="padding: 12px; color: #c5a85c;">Amount</th>
                        <th style="padding: 12px; color: #c5a85c;">Description</th>
                    </tr>
                </thead>
                <tbody id="nxlLedgerTableBody">
                    <tr><td colspan="5" style="text-align: center; padding: 20px; color: #9aa0b4;">Loading ledger data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
    
  </div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    fetchAdminNxlLedger();
});

async function fetchAdminNxlLedger() {
    try {
        const res = await fetch("api/index.php/wallet/admin/transactions");
        const data = await res.json();
        const tbody = document.getElementById("nxlLedgerTableBody");
        
        if (data.success && data.transactions) {
            tbody.innerHTML = "";
            if (data.transactions.length === 0) {
                tbody.innerHTML = `<tr><td colspan="5" style="text-align: center; padding: 20px; color: #9aa0b4;">No transactions found.</td></tr>`;
                return;
            }
            
            data.transactions.forEach(t => {
                const date = new Date(t.date).toLocaleString();
                const amountColor = (t.type.includes('SUB') || t.type === 'SPEND' || t.amount < 0) ? '#ef4444' : '#22c55e';
                const sign = (t.type.includes('SUB') || t.type === 'SPEND' || t.amount < 0) ? '-' : '+';
                
                tbody.innerHTML += `
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <td style="padding: 12px; font-size: 0.85rem; color: #9aa0b4;">${date}</td>
                        <td style="padding: 12px;">
                            <div style="font-weight: 600;">${t.name}</div>
                            <div style="font-size: 0.8rem; color: #9aa0b4;">${t.email}</div>
                        </td>
                        <td style="padding: 12px; font-size: 0.85rem;">${t.type}</td>
                        <td style="padding: 12px; font-weight: bold; color: ${amountColor};">${sign}${Math.abs(t.amount)} NXL</td>
                        <td style="padding: 12px; font-size: 0.85rem; color: #9aa0b4;">${t.description}</td>
                    </tr>
                `;
            });
        }
    } catch (err) {
        console.error("Error fetching ledger", err);
    }
}
</script>
</div>
</div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
