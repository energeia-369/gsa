<?php
$pageTitle = "GLOBAL SPORTS ARENA | Account Management";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
<link rel="stylesheet" href="assets/css/AdminDashboard.css?v=7">
<div class="admin-dashboard px-4 sm:px-8 py-10" style="background: #0b0c10; color: #f5f6fa; min-height: 100vh;">
  <?php require_once __DIR__ . '/includes/admin_navbar.php'; ?>
  
  <div class="admin-header" style="border-bottom: 1px solid rgba(197, 168, 92, 0.2); padding-bottom: 20px;">
    <h1>👥 Account Management</h1>
    <p>Manage all Customer Accounts, Merchant Partners, and Administrative Staff.</p>
  </div>

  <div id="adminGlobalLoading" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(11,12,16,0.8); z-index: 9999; justify-content: center; align-items: center; color: #c5a85c; font-size: 1.5rem; font-weight: bold;">
    ? Loading records...
  </div>

  <div class="admin-content" style="display: block; margin-top: 40px;">

    <!-- Edit User Form -->
    <div id="userEditSection" class="admin-card" style="display: none; background: #12131c; border: 2px solid #c5a85c; border-radius: 20px; padding: 25px; margin-bottom: 30px;">
      <h2 id="userEditTitle" style="color: #c5a85c; margin: 0 0 20px 0;">✏️ Edit Account Profile</h2>
      <form id="userEditForm" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
        <input type="hidden" id="editUserId" value="">
        <div>
          <label style="display: block; font-size: 0.85rem; color: #9aa0b4; margin-bottom: 5px;">Full Name *</label>
          <input 
            type="text" 
            id="editUserFullName" 
            style="width: 100%; padding: 10px; border: 1px solid rgba(197,168,92,0.2); border-radius: 8px; background: #0b0c10; color: #fff; box-sizing: border-box;"
            required
          />
        </div>
        <div>
          <label style="display: block; font-size: 0.85rem; color: #9aa0b4; margin-bottom: 5px;">Email *</label>
          <input 
            type="email" 
            id="editUserEmail" 
            style="width: 100%; padding: 10px; border: 1px solid rgba(197,168,92,0.2); border-radius: 8px; background: #0b0c10; color: #fff; box-sizing: border-box;"
            required
          />
        </div>
        <div>
          <label style="display: block; font-size: 0.85rem; color: #9aa0b4; margin-bottom: 5px;">Phone Number</label>
          <input 
            type="text" 
            id="editUserPhone" 
            style="width: 100%; padding: 10px; border: 1px solid rgba(197,168,92,0.2); border-radius: 8px; background: #0b0c10; color: #fff; box-sizing: border-box;"
          />
        </div>
        <div>
          <label style="display: block; font-size: 0.85rem; color: #9aa0b4; margin-bottom: 5px;">Account Role</label>
          <select 
            id="editUserRole" 
            style="width: 100%; padding: 10px; border: 1px solid rgba(197,168,92,0.2); border-radius: 8px; background: #0b0c10; color: #fff; box-sizing: border-box;"
          >
            <option value="USER">USER (Customer)</option>
            <option value="MERCHANT">MERCHANT (Partner)</option>
            <option value="ADMIN">ADMIN (Staff)</option>
          </select>
        </div>
        <div style="grid-column: span 4; display: flex; gap: 10px; margin-top: 10px;">
          <button 
            type="submit" 
            style="background: linear-gradient(135deg, #c5a85c 0%, #8c7237 100%); color: #0b0c10; border: none; padding: 12px 25px; border-radius: 8px; font-weight: bold; cursor: pointer;"
          >
            Save Profile Updates
          </button>
          <button 
            type="button" 
            id="btnCancelUserEdit"
            style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.15); color: #fff; padding: 12px 20px; border-radius: 8px; cursor: pointer;"
          >
            Cancel
          </button>
        </div>
      </form>
    </div>

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
      
      /* Make sure sections act properly in the grid */
      .account-section {
        display: block;
        transition: opacity 0.3s ease;
      }
    </style>

    <!-- Centered Pill Navbar -->
    <div style="display: flex; justify-content: center; gap: 15px; margin-bottom: 40px; flex-wrap: wrap;">
      <button class="account-tab active" onclick="filterAccounts('customer', this)">Customer Accounts</button>
      <button class="account-tab" onclick="filterAccounts('merchant', this)">Merchant Accounts</button>
      <button class="account-tab" onclick="filterAccounts('admin', this)">Administrative Staff</button>
    </div>

    <div class="accounts-grid" id="accountsGridContainer" style="display: grid; grid-template-columns: 1fr; gap: 30px; width: 100%;">
      
      <!-- User List Panel -->
      <div id="sectionCustomer" class="account-section">
        <h2 style="color: #c5a85c; margin: 0 0 10px 0;">👤 Customer Accounts</h2>
        <p style="color: #9aa0b4; font-size: 0.8rem; margin: 0 0 20px 0;">Edit profile information or remove customer accounts</p>

        <div id="customersContainer" style="display: grid; gap: 15px;">
          <p style="color: #9aa0b4; text-align: center;">Loading customers...</p>
        </div>
      </div>

      <!-- Merchant List Panel -->
      <div id="sectionMerchant" class="account-section" style="display: none;">
        <h3 style="color: #c5a85c; margin: 0 0 10px 0; font-size: 1.2rem;">🏪 Merchant Accounts</h3>
        <p style="color: #9aa0b4; font-size: 0.85rem; margin-top: 0;">Edit profile information or remove merchant partners</p>

        <div id="merchantsContainer" style="display: grid; gap: 15px; margin-top: 20px;">
          <p style="color: #9aa0b4; text-align: center;">Loading...</p>
        </div>
      </div>

      <!-- Admin Staff Panel -->
      <div id="sectionAdmin" class="account-section" style="display: none;">
        <h3 style="color: #38bdf8; margin: 0 0 10px 0; font-size: 1.2rem;">🛡️ Administrative Staff</h3>
        <p style="color: #9aa0b4; font-size: 0.85rem; margin-top: 0;">Edit administrative privileges or remove moderators</p>

        <div id="adminsContainer" style="display: grid; gap: 15px; margin-top: 20px;">
          <p style="color: #9aa0b4; text-align: center;">Loading...</p>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
let allUsers = [];

function createAccountCard(user) {
    return `
        <div style="background: #12131c; border: 1px solid rgba(197,168,92,0.15); padding: 20px; border-radius: 12px; font-size: 0.9rem; display: flex; flex-direction: column; justify-content: space-between; height: 100%; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
          <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                <strong style="color: #f5f6fa; font-size: 1.1rem; text-transform: uppercase; letter-spacing: 1px;">${user.username || user.first_name || 'User'} <span style="color: #c5a85c; font-size: 0.8rem;">(ID: ${user.id})</span></strong>
            </div>
            <div style="color: #9aa0b4; margin-bottom: 5px;">✉️ ${user.email}</div>
            <div style="color: #9aa0b4; margin-bottom: 12px;">📞 Phone: ${user.phone || 'N/A'}</div>
          </div>
          <div style="display: flex; gap: 10px; border-top: 1px dashed rgba(255,255,255,0.1); padding-top: 15px; margin-top: auto;">
              <button onclick="alert('Edit user ${user.id} functionality coming soon.')" style="flex: 1; padding: 8px; background: transparent; border: 1px solid #c5a85c; color: #c5a85c; border-radius: 6px; cursor: pointer; transition: all 0.3s ease; font-weight: bold;" onmouseover="this.style.background='rgba(197,168,92,0.1)'" onmouseout="this.style.background='transparent'">
                  ✏️ Edit
              </button>
              <button onclick="alert('Delete user ${user.id} functionality coming soon.')" style="flex: 1; padding: 8px; background: transparent; border: 1px solid #ef4444; color: #ef4444; border-radius: 6px; cursor: pointer; transition: all 0.3s ease; font-weight: bold;" onmouseover="this.style.background='rgba(239,68,68,0.1)'" onmouseout="this.style.background='transparent'">
                  🗑️ Delete
              </button>
          </div>
        </div>
    `;
}

document.addEventListener("DOMContentLoaded", function() {
    const token = localStorage.getItem("token");
    const role = localStorage.getItem("userRole");

    if (!token || role !== "ADMIN") {
        alert("Access Denied: Admin login required!");
        window.location.href = "login.php";
        return;
    }

    loadData();

    // Setup User Edit Form Submission
    document.getElementById("userEditForm").addEventListener("submit", handleSaveUser);
    document.getElementById("btnCancelUserEdit").addEventListener("click", function() {
        document.getElementById("userEditSection").style.display = "none";
    });
});

async function loadData() {
    showLoading(true);
    try {
        const token = localStorage.getItem("token");
        const res = await fetch("api/index.php/user/all", {
            headers: { "Authorization": "Bearer " + token }
        });
        allUsers = await res.json();
        renderUsers();
    } catch (err) {
        console.error("Load Error:", err);
    } finally {
        showLoading(false);
    }
}

function showLoading(show) {
    document.getElementById("adminGlobalLoading").style.display = show ? "flex" : "none";
}

function renderUsers() {
    const customersContainer = document.getElementById("customersContainer");
    const merchantsContainer = document.getElementById("merchantsContainer");
    const adminsContainer = document.getElementById("adminsContainer");

    const customers = allUsers.filter(u => u.role !== "ADMIN" && u.role !== "MERCHANT");
    const premiumCustomers = customers.filter(u => u.membership_tier && u.membership_tier !== 'none' && u.membership_tier !== 'standard');
    const regularCustomers = customers.filter(u => !u.membership_tier || u.membership_tier === 'none' || u.membership_tier === 'standard');
    
    const merchants = allUsers.filter(u => u.role === "MERCHANT");
    const admins = allUsers.filter(u => u.role === "ADMIN");

    if (customers.length === 0) {
        customersContainer.innerHTML = `<p style="color: #9aa0b4; text-align: center;">No customer accounts registered yet.</p>`;
    } else {
        // Prepare premium HTML
        const premiumHtml = premiumCustomers.map(u => {
            const cardBg = 'linear-gradient(135deg, #f5d87a 0%, #c5a85c 100%) !important';
            const textColor = '#0b0c10 !important';
            const subTextColor = '#333 !important';
            const idColor = '#444 !important';
            
            const badgeBg = '#ffffff !important';
            const badgeText = '#0b0c10 !important';
            const badgeHtml = `<span style="background: ${badgeBg}; color: ${badgeText}; font-size: 0.75rem; font-weight: 900; padding: 3px 8px; border-radius: 6px; margin-left: 8px; text-transform: uppercase; box-shadow: 0 2px 4px rgba(0,0,0,0.1); border: 1px solid rgba(197, 168, 92, 0.5);">${u.membership_tier}</span>`;
            
            const borderStyle = 'none !important';
            const shadowStyle = 'box-shadow: 0 4px 15px rgba(197, 168, 92, 0.4) !important;';
            
            const btnBg = 'rgba(255, 255, 255, 0.4) !important';
            const btnBorder = '#0b0c10 !important';
            const btnColor = '#0b0c10 !important';

            return `
            <div style="background: ${cardBg}; border: ${borderStyle}; ${shadowStyle} padding: 15px; border-radius: 12px; display: flex; flex-direction: column; gap: 10px;">
              <div>
                <h4 style="margin: 0 0 4px 0; color: ${textColor};">${u.full_name || u.fullName || 'User'} ${badgeHtml} <span style="color: ${idColor}; font-size: 0.8rem;">(ID: ${u.id})</span></h4>
                <div style="font-size: 0.8rem; color: ${subTextColor};">✉️ ${u.email}</div>
                <div style="font-size: 0.8rem; color: ${subTextColor}; margin-top: 2px;">📞 Phone: ${u.phone_number || u.phoneNumber || "N/A"}</div>
              </div>
              <div style="display: flex; gap: 8px;">
                <button onclick="handleEditUserClick(${u.id})" style="flex: 1; background: ${btnBg}; border: 1px solid ${btnBorder}; color: ${btnColor}; padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; cursor: pointer; font-weight: bold;">✏️ Edit</button>
                <button onclick="handleDeleteUser(${u.id}, 'USER')" style="flex: 1; background: rgba(220,38,38,0.15); border: 1px solid #dc2626; color: #dc2626; padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; cursor: pointer; font-weight: bold;">🗑️ Delete</button>
              </div>
            </div>
            `;
        }).join('');

        // Prepare normal HTML
        const normalHtml = regularCustomers.map(u => {
            const borderStyle = '1px solid rgba(197,168,92,0.1)';
            return `
            <div class="regular-user-card" style="background: #0b0c10; border: ${borderStyle}; padding: 15px; border-radius: 12px; display: flex; flex-direction: column; gap: 10px;">
              <div>
                <h4 style="margin: 0 0 4px 0; color: #f5f6fa;">${u.full_name || u.fullName || 'User'} <span style="color: #c5a85c; font-size: 0.8rem;">(ID: ${u.id})</span></h4>
                <div style="font-size: 0.8rem; color: #9aa0b4;">✉️ ${u.email}</div>
                <div style="font-size: 0.8rem; color: #9aa0b4; margin-top: 2px;">📞 Phone: ${u.phone_number || u.phoneNumber || "N/A"}</div>
              </div>
              <div style="display: flex; gap: 8px;">
                <button onclick="handleEditUserClick(${u.id})" style="flex: 1; background: rgba(197,168,92,0.1); border: 1px solid #c5a85c; color: #c5a85c; padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; cursor: pointer;">✏️ Edit</button>
                <button onclick="handleDeleteUser(${u.id}, 'USER')" style="flex: 1; background: rgba(220,38,38,0.15); border: 1px solid #dc2626; color: #f87171; padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; cursor: pointer;">🗑️ Delete</button>
              </div>
            </div>
            `;
        }).join('');

        customersContainer.style.display = 'grid';
        customersContainer.style.gridTemplateColumns = '40% 40%';
        customersContainer.style.justifyContent = 'center';
        customersContainer.style.gap = '5%';

        customersContainer.innerHTML = `
            <style>
              body.light-theme .regular-user-card {
                  background: #f5f5dc !important;
                  border-color: rgba(197, 168, 92, 0.3) !important;
              }
            </style>
            <div style="display: flex; flex-direction: column; gap: 15px; background: transparent !important; border: none !important; box-shadow: none !important;">
                <h3 style="color: #f5d87a; margin: 0 0 5px 0; font-size: 1.1rem; border-bottom: 1px dashed rgba(245,216,122,0.3); padding-bottom: 8px; text-transform: uppercase;">💎 Premium Users</h3>
                ${premiumHtml || '<p style="color: #9aa0b4; text-align: center; margin-top: 20px;">No premium users.</p>'}
            </div>
            <div style="display: flex; flex-direction: column; gap: 15px; background: transparent !important; border: none !important; box-shadow: none !important;">
                <h3 style="color: #c5a85c; margin: 0 0 5px 0; font-size: 1.1rem; border-bottom: 1px dashed rgba(197,168,92,0.3); padding-bottom: 8px; text-transform: uppercase;">👤 Regular Users</h3>
                ${normalHtml || '<p style="color: #9aa0b4; text-align: center; margin-top: 20px;">No regular users.</p>'}
            </div>
        `;
    }

    if (merchants.length === 0) {
        merchantsContainer.innerHTML = `<p style="color: #9aa0b4; text-align: center;">No merchant accounts registered yet.</p>`;
    } else {
        const premiumMerchants = merchants.filter(u => u.membership_tier && u.membership_tier !== 'none' && u.membership_tier !== 'standard');
        const regularMerchants = merchants.filter(u => !u.membership_tier || u.membership_tier === 'none' || u.membership_tier === 'standard');
        
        const premiumMerchantsHtml = premiumMerchants.map(u => {
            const cardBg = 'linear-gradient(135deg, #f5d87a 0%, #c5a85c 100%) !important';
            const textColor = '#0b0c10 !important';
            const subTextColor = '#333 !important';
            const idColor = '#444 !important';
            
            const badgeBg = '#ffffff !important';
            const badgeText = '#0b0c10 !important';
            const badgeHtml = `<span style="background: ${badgeBg}; color: ${badgeText}; font-size: 0.75rem; font-weight: 900; padding: 3px 8px; border-radius: 6px; margin-left: 8px; text-transform: uppercase; box-shadow: 0 2px 4px rgba(0,0,0,0.1); border: 1px solid rgba(197, 168, 92, 0.5);">${u.membership_tier}</span>`;
            
            const shadowStyle = 'box-shadow: 0 4px 15px rgba(197, 168, 92, 0.4) !important;';
            const btnBg = 'rgba(255, 255, 255, 0.4) !important';
            
            return `
            <div style="background: ${cardBg}; border: none !important; ${shadowStyle} padding: 15px; border-radius: 12px; display: flex; flex-direction: column; gap: 10px;">
              <div>
                <h4 style="margin: 0 0 4px 0; color: ${textColor};">${u.full_name || u.fullName || 'Merchant'} ${badgeHtml} <span style="color: ${idColor}; font-size: 0.8rem;">(ID: ${u.id})</span></h4>
                <div style="font-size: 0.8rem; color: ${subTextColor};">✉️ ${u.email}</div>
                <div style="font-size: 0.8rem; color: ${subTextColor}; margin-top: 2px;">📞 Phone: ${u.phone_number || u.phoneNumber || "N/A"}</div>
              </div>
              <div style="display: flex; gap: 8px;">
                <button onclick="handleEditUserClick(${u.id})" style="flex: 1; background: ${btnBg}; border: 1px solid #0b0c10; color: #0b0c10; padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; cursor: pointer; font-weight: bold;">✏️ Edit</button>
                <button onclick="handleDeleteUser(${u.id}, 'MERCHANT')" style="flex: 1; background: rgba(220,38,38,0.15); border: 1px solid #dc2626; color: #dc2626; padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; cursor: pointer; font-weight: bold;">🗑️ Delete</button>
              </div>
            </div>
            `;
        }).join('');

        const regularMerchantsHtml = regularMerchants.map(u => {
            return `
            <div class="regular-merchant-card" style="background: #0b0c10; border: 1px solid rgba(168,85,247,0.15); padding: 15px; border-radius: 12px; display: flex; flex-direction: column; gap: 10px;">
              <div>
                <h4 style="margin: 0 0 4px 0; color: #f5f6fa;">${u.full_name || u.fullName || 'Merchant'} <span style="color: #a855f7; font-size: 0.8rem;">(ID: ${u.id})</span></h4>
                <div style="font-size: 0.8rem; color: #9aa0b4;">✉️ ${u.email}</div>
                <div style="font-size: 0.8rem; color: #9aa0b4; margin-top: 2px;">📞 Phone: ${u.phone_number || u.phoneNumber || "N/A"}</div>
              </div>
              <div style="display: flex; gap: 8px;">
                <button onclick="handleEditUserClick(${u.id})" style="flex: 1; background: rgba(168,85,247,0.1); border: 1px solid #a855f7; color: #a855f7; padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; cursor: pointer;">✏️ Edit</button>
                <button onclick="handleDeleteUser(${u.id}, 'MERCHANT')" style="flex: 1; background: rgba(220,38,38,0.15); border: 1px solid #dc2626; color: #f87171; padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; cursor: pointer;">🗑️ Delete</button>
              </div>
            </div>
            `;
        }).join('');
        
        merchantsContainer.style.display = 'grid';
        merchantsContainer.style.gridTemplateColumns = '40% 40%';
        merchantsContainer.style.justifyContent = 'center';
        merchantsContainer.style.gap = '5%';

        merchantsContainer.innerHTML = `
            <style>
              body.light-theme .regular-merchant-card {
                  background: #f5f5dc !important;
                  border-color: rgba(168, 85, 247, 0.3) !important;
              }
            </style>
            <div style="display: flex; flex-direction: column; gap: 15px; background: transparent !important; border: none !important; box-shadow: none !important;">
                <h3 style="color: #f5d87a; margin: 0 0 5px 0; font-size: 1.1rem; border-bottom: 1px dashed rgba(245,216,122,0.3); padding-bottom: 8px; text-transform: uppercase;">💎 Premium Merchants</h3>
                ${premiumMerchantsHtml || '<p style="color: #9aa0b4; text-align: center; margin-top: 20px;">No premium merchants.</p>'}
            </div>
            <div style="display: flex; flex-direction: column; gap: 15px; background: transparent !important; border: none !important; box-shadow: none !important;">
                <h3 style="color: #a855f7; margin: 0 0 5px 0; font-size: 1.1rem; border-bottom: 1px dashed rgba(168,85,247,0.3); padding-bottom: 8px; text-transform: uppercase;">🏪 Regular Merchants</h3>
                ${regularMerchantsHtml || '<p style="color: #9aa0b4; text-align: center; margin-top: 20px;">No regular merchants.</p>'}
            </div>
        `;
    }

    if (admins.length === 0) {
        adminsContainer.innerHTML = `<p style="color: #9aa0b4; text-align: center;">No administrative accounts found.</p>`;
    } else {
        adminsContainer.innerHTML = admins.map(u => `
            <div style="background: #0b0c10; border: 1px solid rgba(56,189,248,0.15); padding: 15px; border-radius: 12px; display: flex; flex-direction: column; gap: 10px;">
              <div>
                <h4 style="margin: 0 0 4px 0; color: #f5f6fa;">${u.full_name || u.fullName || 'Admin'} <span style="color: #38bdf8; font-size: 0.8rem;">(ID: ${u.id})</span></h4>
                <div style="font-size: 0.8rem; color: #9aa0b4;">✉️ ${u.email}</div>
                <div style="font-size: 0.8rem; color: #9aa0b4; margin-top: 2px;">📞 Phone: ${u.phone_number || u.phoneNumber || "N/A"}</div>
              </div>
              <div style="display: flex; gap: 8px;">
                <button onclick="handleEditUserClick(${u.id})" style="flex: 1; background: rgba(56,189,248,0.1); border: 1px solid #38bdf8; color: #38bdf8; padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; cursor: pointer;">✏️ Edit</button>
                <button onclick="handleDeleteUser(${u.id}, 'ADMIN')" style="flex: 1; background: rgba(220,38,38,0.15); border: 1px solid #dc2626; color: #f87171; padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; cursor: pointer;">🗑️ Delete</button>
              </div>
            </div>
        `).join('');
    }
}

function handleEditUserClick(userId) {
    const user = allUsers.find(u => u.id === userId);
    if (!user) return;

    document.getElementById("userEditSection").style.display = "block";
    document.getElementById("editUserId").value = user.id;
    document.getElementById("editUserFullName").value = user.fullName || user.full_name;
    document.getElementById("editUserEmail").value = user.email;
    document.getElementById("editUserPhone").value = user.phone_number || user.phoneNumber || "";
    document.getElementById("editUserRole").value = user.role || "USER";

    document.getElementById("userEditSection").scrollIntoView({ behavior: "smooth" });
}

async function handleSaveUser(e) {
    e.preventDefault();
    const token = localStorage.getItem("token");
    const userId = document.getElementById("editUserId").value;
    const payload = {
        fullName: document.getElementById("editUserFullName").value,
        email: document.getElementById("editUserEmail").value,
        phoneNumber: document.getElementById("editUserPhone").value,
        role: document.getElementById("editUserRole").value
    };

    showLoading(true);
    try {
        const res = await fetch(`api/index.php/user/${userId}`, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json",
                "Authorization": "Bearer " + token
            },
            body: JSON.stringify(payload)
        });
        const data = await res.json();
        alert("User profile successfully updated!");
        document.getElementById("userEditSection").style.display = "none";
        await loadData();
    } catch(err) {
        console.error(err);
        alert("Failed to update user profile.");
    } finally {
        showLoading(false);
    }
}

async function handleDeleteUser(userId, role) {
    const confirmMsg = `Are you sure you want to delete this ${role.toLowerCase()} account permanently? This action cannot be undone.`;
    if (!window.confirm(confirmMsg)) return;

    const token = localStorage.getItem("token");
    showLoading(true);
    try {
        await fetch(`api/index.php/user/${userId}`, {
            method: "DELETE",
            headers: { "Authorization": "Bearer " + token }
        });
        alert(`${role} account deleted successfully.`);
        await loadData();
    } catch(err) {
        console.error(err);
        alert("Failed to delete account.");
    } finally {
        showLoading(false);
    }
}
function filterAccounts(type, btnElement) {
    // Update active button styling
    const buttons = document.querySelectorAll('.account-tab');
    buttons.forEach(btn => btn.classList.remove('active'));
    if (btnElement) {
        btnElement.classList.add('active');
    }

    // Get sections
    const secCustomer = document.getElementById('sectionCustomer');
    const secMerchant = document.getElementById('sectionMerchant');
    const secAdmin = document.getElementById('sectionAdmin');
    const gridContainer = document.getElementById('accountsGridContainer');

    // Show/Hide logic
    if (type === 'customer') {
        secCustomer.style.display = 'block';
        secMerchant.style.display = 'none';
        secAdmin.style.display = 'none';
        gridContainer.style.gridTemplateColumns = '1fr';
    } else if (type === 'merchant') {
        secCustomer.style.display = 'none';
        secMerchant.style.display = 'block';
        secAdmin.style.display = 'none';
        gridContainer.style.gridTemplateColumns = '1fr';
    } else if (type === 'admin') {
        secCustomer.style.display = 'none';
        secMerchant.style.display = 'none';
        secAdmin.style.display = 'block';
        gridContainer.style.gridTemplateColumns = '1fr';
    }
}
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
