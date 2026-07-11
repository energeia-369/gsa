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

  <style>
      .sticky-th {
          position: sticky;
          top: 0;
          background: #12131c;
          z-index: 10;
      }
      body.light-theme .sticky-th {
          background: #f5f5dc;
      }
  </style>
  <div class="admin-content" style="grid-template-columns: 1fr !important; gap: 2rem; margin-top: 2.5rem;">
    
    <!-- NXL Ledger Activity Panel -->
    <div class="dashboard-card" style="background: #12131c; border: 1px solid rgba(197, 168, 92, 0.15); border-radius: 12px; padding: 25px;">
        <div style="display: flex; flex-wrap: wrap; gap: 15px; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <div>
                <h2 style="color: #c5a85c; margin-top: 0; font-size: 1.4rem; margin-bottom: 5px;">Recent NXL Ledger Activity</h2>
                <p style="color: var(--text-muted, #9aa0b4); font-size: 0.9rem; margin: 0;">Live tracking of NXL credits credited and debited across all users.</p>
            </div>
            <input type="text" id="searchNxl" placeholder="Search ledger..." style="padding: 8px 12px; border-radius: 5px; border: 1px solid rgba(197, 168, 92, 0.4); background: #1a1a24; color: #fff; width: 250px;" onkeyup="filterTable('searchNxl', 'nxlLedgerTableBody')">
        </div>
        
        <div style="overflow-x: auto; max-height: 400px; overflow-y: auto;">
            <table style="width: 100%; border-collapse: collapse; color: var(--text-primary, #f5f6fa); text-align: left; white-space: nowrap;">
                <thead>
                    <tr style="border-bottom: 1px solid rgba(197, 168, 92, 0.3);">
                        <th class="sticky-th" style="padding: 12px; color: var(--accent-gold, #c5a85c);">Date</th>
                        <th class="sticky-th" style="padding: 12px; color: var(--accent-gold, #c5a85c);">User</th>
                        <th class="sticky-th" style="padding: 12px; color: var(--accent-gold, #c5a85c);">Action</th>
                        <th class="sticky-th" style="padding: 12px; color: var(--accent-gold, #c5a85c);">Amount</th>
                        <th class="sticky-th" style="padding: 12px; color: var(--accent-gold, #c5a85c);">Description</th>
                    </tr>
                </thead>
                <tbody id="nxlLedgerTableBody">
                    <tr><td colspan="5" style="text-align: center; padding: 20px; color: var(--text-muted, #9aa0b4);">Loading ledger data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Recent Delegates Panel -->
    <div class="dashboard-card" style="background: #12131c; border: 1px solid rgba(197, 168, 92, 0.15); border-radius: 12px; padding: 25px;">
        <div style="display: flex; flex-wrap: wrap; gap: 15px; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <div>
                <h2 style="color: #c5a85c; margin-top: 0; font-size: 1.4rem; margin-bottom: 5px;">Recent Delegate Registrations</h2>
                <p style="color: #9aa0b4; font-size: 0.9rem; margin: 0;">Live tracking of delegate registrations from the Delegate Registration Form.</p>
            </div>
            <div style="display: flex; gap: 15px; align-items: center;">
                <input type="text" id="searchDelegates" placeholder="Search delegates..." style="padding: 8px 12px; border-radius: 5px; border: 1px solid rgba(197, 168, 92, 0.4); background: #1a1a24; color: #fff; width: 250px;" onkeyup="filterTable('searchDelegates', 'delegateTableBody')">
                <a href="admin-delegates.php" style="color: #c5a85c; text-decoration: none; font-size: 0.9rem; font-weight: bold; white-space: nowrap;">View All →</a>
            </div>
        </div>
        
        <div style="overflow-x: auto; max-height: 400px; overflow-y: auto;">
            <table style="width: 100%; border-collapse: collapse; color: var(--text-primary, #f5f6fa); text-align: left; white-space: nowrap;">
                <thead>
                    <tr style="border-bottom: 1px solid rgba(197, 168, 92, 0.3);">
                        <th class="sticky-th" style="padding: 12px; color: var(--accent-gold, #c5a85c);">Date</th>
                        <th class="sticky-th" style="padding: 12px; color: var(--accent-gold, #c5a85c);">Name / Email</th>
                        <th class="sticky-th" style="padding: 12px; color: var(--accent-gold, #c5a85c);">Organization</th>
                        <th class="sticky-th" style="padding: 12px; color: var(--accent-gold, #c5a85c);">Country</th>
                        <th class="sticky-th" style="padding: 12px; color: var(--accent-gold, #c5a85c);">Status</th>
                    </tr>
                </thead>
                <tbody id="delegateTableBody">
                    <?php
                    require_once __DIR__ . '/config/Database.php';
                    try {
                        $db = Database::getConnection();
                        $stmt = $db->query("SELECT * FROM delegates ORDER BY id DESC LIMIT 5");
                        $recentDelegates = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        if (empty($recentDelegates)) {
                            echo '<tr><td colspan="5" style="text-align: center; padding: 20px; color: var(--text-muted, #9aa0b4);">No delegates registered yet.</td></tr>';
                        } else {
                            foreach ($recentDelegates as $del) {
                                $date = date('M d, Y H:i', strtotime($del['created_at']));
                                $statusColor = $del['registration_status'] === 'Approved' ? '#22c55e' : ($del['registration_status'] === 'Rejected' ? '#ef4444' : '#f59e0b');
                                echo '<tr style="border-bottom: 1px solid rgba(197, 168, 92, 0.15);">';
                                echo '<td style="padding: 12px; font-size: 0.85rem; color: var(--text-muted, #9aa0b4);">' . htmlspecialchars($date) . '</td>';
                                echo '<td style="padding: 12px;"><div style="font-weight: 600;">' . htmlspecialchars($del['full_name']) . '</div><div style="font-size: 0.8rem; color: var(--text-muted, #9aa0b4);">' . htmlspecialchars($del['email']) . '</div></td>';
                                echo '<td style="padding: 12px; font-size: 0.85rem; color: var(--text-primary, #f5f6fa);">' . htmlspecialchars($del['organization']) . '</td>';
                                echo '<td style="padding: 12px; font-size: 0.85rem; color: var(--text-primary, #f5f6fa);">' . htmlspecialchars($del['country']) . '</td>';
                                echo '<td style="padding: 12px; font-weight: bold; color: ' . $statusColor . ';">' . htmlspecialchars($del['registration_status']) . '</td>';
                                echo '</tr>';
                            }
                        }
                    } catch (Exception $e) {
                        echo '<tr><td colspan="5" style="text-align: center; padding: 20px; color: #ef4444;">Error loading delegates: ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
                    }
                    ?>
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
                tbody.innerHTML = `<tr><td colspan="5" style="text-align: center; padding: 20px; color: var(--text-muted, #9aa0b4);">No transactions found.</td></tr>`;
                return;
            }
            
            data.transactions.forEach(t => {
                const date = new Date(t.date).toLocaleString();
                const amountColor = (t.type.includes('SUB') || t.type === 'SPEND' || t.amount < 0) ? '#ef4444' : '#22c55e';
                const sign = (t.type.includes('SUB') || t.type === 'SPEND' || t.amount < 0) ? '-' : '+';
                
                tbody.innerHTML += `
                    <tr style="border-bottom: 1px solid rgba(197, 168, 92, 0.15);">
                        <td style="padding: 12px; font-size: 0.85rem; color: var(--text-muted, #9aa0b4);">${date}</td>
                        <td style="padding: 12px;">
                            <div style="font-weight: 600; color: var(--text-primary, #f5f6fa);">${t.name}</div>
                            <div style="font-size: 0.8rem; color: var(--text-muted, #9aa0b4);">${t.email}</div>
                        </td>
                        <td style="padding: 12px; font-size: 0.85rem; color: var(--text-primary, #f5f6fa);">${t.type}</td>
                        <td style="padding: 12px; font-weight: bold; color: ${amountColor};">${sign}${Math.abs(t.amount)} NXL</td>
                        <td style="padding: 12px; font-size: 0.85rem; color: var(--text-muted, #9aa0b4);">${t.description}</td>
                    </tr>
                `;
            });
        }
    } catch (err) {
        console.error("Error fetching ledger", err);
    }
}

function filterTable(inputId, tbodyId) {
    const input = document.getElementById(inputId);
    const filter = input.value.toLowerCase();
    const tbody = document.getElementById(tbodyId);
    if (!tbody) return;
    const tr = tbody.getElementsByTagName("tr");

    for (let i = 0; i < tr.length; i++) {
        // Skip rows that span multiple columns (like "No transactions found")
        if (tr[i].getElementsByTagName("td").length === 1) continue;
        
        const textContent = tr[i].textContent || tr[i].innerText;
        if (textContent.toLowerCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
        } else {
            tr[i].style.display = "none";
        }
    }
}
</script>
</div>
</div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
