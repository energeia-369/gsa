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
      <div class="admin-badge" style="background: linear-gradient(135deg, #c5a85c 0%, #8c7237 100%); color: #0b0c10; fontWeight: bold; display: inline-block; padding: 4px 12px; border-radius: 4px; margin-bottom: 10px;">
        ⚙️ Administrative Core
      </div>
      <h1>System Operations</h1>
      <p>Real-time tournament CRUD controls, NXL ledger wallets adjustments, and synchronized orders listings in MySQL</p>
    </div>
  </div>

  <!-- Loading Overlay -->
  <div id="adminGlobalLoading" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(11,12,16,0.8); z-index: 9999; justify-content: center; align-items: center; color: #c5a85c; font-size: 1.5rem; font-weight: bold;">
    ? Processing request...
  </div>

  <!-- Dynamic KPI Stats Grid -->
  <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 15px; margin-top: 30px;">
    <div class="stat-card" style="background: #12131c; border: 1px solid rgba(197, 168, 92, 0.15); padding: 20px; border-radius: 12px; display: flex; align-items: center; gap: 15px;">
      <div class="stat-icon" style="font-size: 2rem;">📈</div>
      <div class="stat-info">
        <h3 style="font-size: 0.9rem; color: #9aa0b4; margin: 0 0 5px 0;">Live Total Sales</h3>
        <p class="stat-value" id="statTotalSales" style="color: #22c55e; font-size: 1.5rem; font-weight: bold; margin: 0;">?0</p>
        <span class="stat-change" style="font-size: 0.75rem; color: #9aa0b4;">Synchronized DB</span>
      </div>
    </div>

    <div class="stat-card" style="background: #12131c; border: 1px solid rgba(197, 168, 92, 0.15); padding: 20px; border-radius: 12px; display: flex; align-items: center; gap: 15px;">
      <div class="stat-icon" style="font-size: 2rem;">📈</div>
      <div class="stat-info">
        <h3 style="font-size: 0.9rem; color: #9aa0b4; margin: 0 0 5px 0;">Total NXL Issued</h3>
        <p class="stat-value" id="statTotalNxl" style="color: #c5a85c; font-size: 1.5rem; font-weight: bold; margin: 0;">0 Coins</p>
        <span class="stat-change" style="font-size: 0.75rem; color: #9aa0b4;">Loyalty Ledger</span>
      </div>
    </div>

    <div class="stat-card" style="background: #12131c; border: 1px solid rgba(197, 168, 92, 0.15); padding: 20px; border-radius: 12px; display: flex; align-items: center; gap: 15px;">
      <div class="stat-icon" style="font-size: 2rem;">📈</div>
      <div class="stat-info">
        <h3 style="font-size: 0.9rem; color: #9aa0b4; margin: 0 0 5px 0;">Active Customers</h3>
        <p class="stat-value" id="statActiveCustomers" style="font-size: 1.5rem; font-weight: bold; margin: 0;">0 Users</p>
        <span class="stat-change" style="font-size: 0.75rem; color: #9aa0b4;">Logged Profile</span>
      </div>
    </div>

    <div class="stat-card" style="background: #12131c; border: 1px solid rgba(197, 168, 92, 0.15); padding: 20px; border-radius: 12px; display: flex; align-items: center; gap: 15px;">
      <div class="stat-icon" style="font-size: 2rem;">📈</div>
      <div class="stat-info">
        <h3 style="font-size: 0.9rem; color: #9aa0b4; margin: 0 0 5px 0;">Active Merchants</h3>
        <p class="stat-value" id="statMerchants" style="color: #38bdf8; font-size: 1.5rem; font-weight: bold; margin: 0;">0 Merchants</p>
        <span class="stat-change" style="font-size: 0.75rem; color: #9aa0b4;">Registered Partners</span>
      </div>
    </div>
  </div>

  <div class="admin-content" style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 30px; margin-top: 40px;">
    
    <div style='display:flex; flex-direction:column; gap:30px;'>
      <!-- Security Settings -->
      <div class="admin-card" style="background: #12131c; border: 1px solid rgba(220, 53, 69, 0.4); border-radius: 20px; padding: 25px; margin-bottom: 30px;">
        <h2 style="color: #dc3545; margin: 0 0 20px 0;">🔒 Security Settings</h2>
        
        <div style="display: grid; gap: 15px;">
          <div>
            <label style="display: block; font-size: 0.85rem; color: #9aa0b4; margin-bottom: 5px;">Event Deletion Password</label>
            <div style="display: flex; gap: 10px;">
              <input type="password" id="deleteEventPassword" placeholder="Enter new password" style="flex: 1; padding: 10px; border: 1px solid rgba(220, 53, 69, 0.4); border-radius: 8px; background: #0b0c10; color: #fff; box-sizing: border-box;" required>
              <button type="button" onclick="updateDeletePassword()" style="background: #dc3545; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: bold; cursor: pointer;">Update</button>
            </div>
            <p style="font-size: 0.8rem; color: #666; margin-top: 5px;">This password is required when deleting an event from the Event Editor.</p>
          </div>
          <div>
            <label style="display: block; font-size: 0.85rem; color: #9aa0b4; margin-bottom: 5px;">Pillars Section Title</label>
            <div style="display: flex; gap: 10px;">
              <input type="text" id="pillarsTitle" placeholder="e.g. Our Five Pillars" style="flex: 1; padding: 10px; border: 1px solid rgba(197, 168, 92, 0.4); border-radius: 8px; background: #0b0c10; color: #fff; box-sizing: border-box;" required>
              <button type="button" onclick="updatePillarsTitle()" style="background: #c5a85c; color: #12131c; border: none; padding: 10px 20px; border-radius: 8px; font-weight: bold; cursor: pointer;">Update Title</button>
            </div>
            <p style="font-size: 0.8rem; color: #666; margin-top: 5px;">This will instantly update the title of the 'Our Five Pillars' section on the homepage.</p>
          </div>
          <div>
            <label style="display: block; font-size: 0.85rem; color: #9aa0b4; margin-bottom: 5px;">NXL Cashback Percentage (e.g., 0.05 for 5%)</label>
            <div style="display: flex; gap: 10px;">
              <input type="number" id="nxlCashbackPercentage" step="0.01" min="0" max="1" placeholder="0.05" style="flex: 1; padding: 10px; border: 1px solid rgba(197, 168, 92, 0.4); border-radius: 8px; background: #0b0c10; color: #fff; box-sizing: border-box;" required>
              <button type="button" onclick="updateNxlCashback()" style="background: #c5a85c; color: #12131c; border: none; padding: 10px 20px; border-radius: 8px; font-weight: bold; cursor: pointer;">Update %</button>
            </div>
            <p style="font-size: 0.8rem; color: #666; margin-top: 5px;">Sets the percentage of cashback (in NXL credits) users earn on eligible purchases.</p>
          </div>
          </div>
          <div>
            <label style="display: block; font-size: 0.85rem; color: #9aa0b4; margin-bottom: 5px;">New User Registration NXL Bonus</label>
            <div style="display: flex; gap: 10px;">
              <input type="number" id="signupNxlBonus" min="0" placeholder="25" style="flex: 1; padding: 10px; border: 1px solid rgba(197, 168, 92, 0.4); border-radius: 8px; background: #0b0c10; color: #fff; box-sizing: border-box;" required>
              <button type="button" onclick="updateSignupNxlBonus()" style="background: #c5a85c; color: #12131c; border: none; padding: 10px 20px; border-radius: 8px; font-weight: bold; cursor: pointer;">Update Bonus</button>
            </div>
            <p style="font-size: 0.8rem; color: #666; margin-top: 5px;">Amount of NXL credits given to new users upon registration.</p>
          </div>
        </div>
      </div>

      <!-- Membership Plans Editor -->
      <div class="admin-card" style="background: #12131c; border: 1px solid rgba(197, 168, 92, 0.4); border-radius: 20px; padding: 25px; margin-bottom: 30px;">
        <h2 style="color: #c5a85c; margin: 0 0 20px 0;">? Membership Plans</h2>
        <div id="membershipPlansContainer" style="display: grid; gap: 20px;">
          <p style="color: #9aa0b4;">Loading plans...</p>
        </div>
        <button type="button" onclick="saveMembershipPlans()" style="margin-top: 20px; background: #c5a85c; color: #12131c; border: none; padding: 12px 20px; border-radius: 8px; font-weight: bold; cursor: pointer; width: 100%;">Save All Plans</button>
      </div>
      <script>
      function updateDeletePassword() {
          const pwd = document.getElementById('deleteEventPassword').value;
          if (!pwd) {
              alert("Password cannot be empty.");
              return;
          }
          fetch('api/index.php/settings', {
              method: 'PUT',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ delete_event_password: pwd })
          })
          .then(res => res.json())
          .then(data => {
              if (data.success) {
                  alert("Delete Event Password updated successfully!");
                  document.getElementById('deleteEventPassword').value = '';
              } else {
                  alert("Failed to update password.");
              }
          })
          .catch(err => {
              console.error(err);
              alert("An error occurred while updating the password.");
          });
      }

      function updatePillarsTitle() {
          const title = document.getElementById('pillarsTitle').value;
          if (!title) {
              alert("Title cannot be empty.");
              return;
          }
          fetch('api/index.php/settings', {
              method: 'PUT',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ pillars_title: title })
          })
          .then(res => res.json())
          .then(data => {
              if (data.success) {
                  alert("Pillars Title updated successfully!");
                  document.getElementById('pillarsTitle').value = '';
              } else {
                  alert("Failed to update title.");
              }
          })
          .catch(err => {
              console.error(err);
              alert("An error occurred while updating the title.");
          });
      }

      function updateNxlCashback() {
          const val = document.getElementById('nxlCashbackPercentage').value;
          if (val === '') {
              alert("Percentage cannot be empty.");
              return;
          }
          fetch('api/index.php/settings', {
              method: 'PUT',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ nxl_cashback_percentage: val })
          })
          .then(res => res.json())
          .then(data => {
              if (data.success) {
                  alert("NXL Cashback Percentage updated successfully!");
              } else {
                  alert("Failed to update percentage.");
              }
          })
          .catch(err => {
              console.error(err);
              alert("An error occurred while updating the percentage.");
          });
      }

      function updateSignupNxlBonus() {
          const val = document.getElementById('signupNxlBonus').value;
          if (val === '') {
              alert("Bonus cannot be empty.");
              return;
          }
          fetch('api/index.php/settings', {
              method: 'PUT',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ signup_nxl_bonus: val })
          })
          .then(res => res.json())
          .then(data => {
              if (data.success) {
                  alert("Signup NXL Bonus updated successfully!");
              } else {
                  alert("Failed to update bonus.");
              }
          })
          .catch(err => {
              console.error(err);
              alert("An error occurred while updating the bonus.");
          });
      }
      
      // Load current settings when the page loads
      document.addEventListener('DOMContentLoaded', () => {
          fetch('api/index.php/settings')
          .then(res => res.json())
          .then(data => {
              if (data.success) {
                  if (data.pillars_title && document.getElementById('pillarsTitle')) {
                      document.getElementById('pillarsTitle').value = data.pillars_title;
                  }
                  if (data.nxl_cashback_percentage !== undefined && document.getElementById('nxlCashbackPercentage')) {
                      document.getElementById('nxlCashbackPercentage').value = data.nxl_cashback_percentage;
                  }
                  if (data.signup_nxl_bonus !== undefined && document.getElementById('signupNxlBonus')) {
                      document.getElementById('signupNxlBonus').value = data.signup_nxl_bonus;
                  }
                  if (data.membership_plans) {
                      renderMembershipPlans(data.membership_plans);
                  }
              }
          })
          .catch(console.error);
      });

      let membershipPlansData = {};

      function renderMembershipPlans(plans) {
          membershipPlansData = plans;
          const container = document.getElementById('membershipPlansContainer');
          container.innerHTML = '';
          
          Object.keys(plans).forEach(key => {
              const plan = plans[key];
              const card = document.createElement('div');
              card.style.cssText = 'background: rgba(255,255,255,0.05); padding: 15px; border-radius: 10px; border: 1px solid rgba(197, 168, 92, 0.2);';
              
              const titleHtml = `<h3 style="color: #c5a85c; margin-top: 0;">${plan.name} (${key})</h3>`;
              
              const priceHtml = `
                  <div style="margin-bottom: 10px;">
                      <label style="display: block; font-size: 0.85rem; color: #9aa0b4; margin-bottom: 5px;">Price (?)</label>
                      <input type="number" id="plan_${key}_price" value="${plan.price}" style="width: 100%; padding: 8px; border: 1px solid rgba(197, 168, 92, 0.4); border-radius: 5px; background: #0b0c10; color: #fff;">
                  </div>
              `;
              
              const cashbackHtml = `
                  <div style="margin-bottom: 10px;">
                      <label style="display: block; font-size: 0.85rem; color: #9aa0b4; margin-bottom: 5px;">Cashback / Discount % (e.g., 0.05 for 5%)</label>
                      <input type="number" step="0.01" id="plan_${key}_cashback" value="${plan.cashback_percent}" style="width: 100%; padding: 8px; border: 1px solid rgba(197, 168, 92, 0.4); border-radius: 5px; background: #0b0c10; color: #fff;">
                  </div>
              `;
              
              const perksHtml = `
                  <div style="margin-bottom: 10px;">
                      <label style="display: block; font-size: 0.85rem; color: #9aa0b4; margin-bottom: 5px;">Perks (One per line)</label>
                      <textarea id="plan_${key}_perks" rows="5" style="width: 100%; padding: 8px; border: 1px solid rgba(197, 168, 92, 0.4); border-radius: 5px; background: #0b0c10; color: #fff;">${plan.perks.join('\\n')}</textarea>
                  </div>
              `;
              
              card.innerHTML = titleHtml + priceHtml + cashbackHtml + perksHtml;
              container.appendChild(card);
          });
      }

      function saveMembershipPlans() {
          const keys = Object.keys(membershipPlansData);
          const updatedPlans = {};
          
          keys.forEach(key => {
              updatedPlans[key] = {
                  name: membershipPlansData[key].name,
                  price: parseFloat(document.getElementById(`plan_${key}_price`).value) || 0,
                  cashback_percent: parseFloat(document.getElementById(`plan_${key}_cashback`).value) || 0,
                  perks: document.getElementById(`plan_${key}_perks`).value.split('\\n').filter(p => p.trim() !== '')
              };
          });

          fetch('api/index.php/settings', {
              method: 'PUT',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ membership_plans: updatedPlans })
          })
          .then(res => res.json())
          .then(data => {
              if (data.success) {
                  alert("Membership plans updated successfully!");
              } else {
                  alert("Failed to update membership plans.");
              }
          })
          .catch(err => {
              console.error(err);
              alert("An error occurred while updating membership plans.");
          });
      }

      </script>


<!-- Manage E.V.A. Chatbot -->
      <div class="admin-card" style="background: #12131c; border: 1px solid rgba(197, 168, 92, 0.2); border-radius: 20px; padding: 25px; margin-bottom: 30px;">
        <h2 style="color: #c5a85c; margin: 0 0 20px 0;">🤖 E.V.A. Chatbot FAQs</h2>
        <div style="margin-bottom: 20px;">
          <form id="chatbotForm">
            <input type="hidden" id="chatbotEditId" value="">
            <input type="text" id="chatbotQuestion" placeholder="Question (e.g. How do I book a venue?)" required style="width: 100%; padding: 10px; margin-bottom: 10px; border-radius: 5px; border: 1px solid rgba(197, 168, 92, 0.3); background: rgba(11, 12, 16, 0.8); color: #fff;">
            <textarea id="chatbotAnswer" placeholder="E.V.A.'s Answer..." required rows="3" style="width: 100%; padding: 10px; margin-bottom: 10px; border-radius: 5px; border: 1px solid rgba(197, 168, 92, 0.3); background: rgba(11, 12, 16, 0.8); color: #fff;"></textarea>
            <div style="display: flex; gap: 10px;">
              <button type="submit" id="chatbotSubmitBtn" style="background: linear-gradient(135deg, #c5a85c 0%, #8c7237 100%); color: #0b0c10; border: none; padding: 8px 15px; border-radius: 5px; font-weight: bold; cursor: pointer;">Add FAQ</button>
              <button type="button" id="chatbotCancelBtn" style="display: none; background: rgba(255,255,255,0.1); color: #fff; border: 1px solid rgba(255,255,255,0.2); padding: 8px 15px; border-radius: 5px; cursor: pointer;" onclick="cancelEditChatbotFaq()">Cancel</button>
            </div>
          </form>
        </div>
        <div id="chatbotListContainer" style="display: grid; gap: 12px; max-height: 300px; overflow-y: auto;">
          <p style="color: #9aa0b4; text-align: center;">Loading FAQs...</p>
        </div>
      </div>
      <script>
      let currentFaqs = [];

      function loadChatbotFaqs() {
          fetch('api/index.php/chatbot/faqs')
          .then(res => res.json())
          .then(data => {
              const container = document.getElementById('chatbotListContainer');
              if (data.success && data.data.length > 0) {
                  currentFaqs = data.data;
                  container.innerHTML = '';
                  data.data.forEach(faq => {
                      const div = document.createElement('div');
                      div.style.cssText = 'background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); padding: 15px; border-radius: 10px; display: flex; justify-content: space-between; align-items: start;';
                      
                      const textDiv = document.createElement('div');
                      textDiv.style.flex = '1';
                      textDiv.innerHTML = `
                          <strong style="color: #c5a85c; display: block; margin-bottom: 5px;">${faq.question}</strong>
                          <span style="color: #9aa0b4; font-size: 0.9rem;">${faq.answer}</span>
                      `;

                      const actionDiv = document.createElement('div');
                      actionDiv.style.display = 'flex';
                      actionDiv.style.gap = '10px';
                      
                      const editBtn = document.createElement('button');
                      editBtn.innerHTML = '✏️';
                      editBtn.style.cssText = 'background: none; border: none; cursor: pointer; filter: grayscale(1); transition: 0.3s;';
                      editBtn.onmouseover = () => editBtn.style.filter = 'grayscale(0)';
                      editBtn.onmouseout = () => editBtn.style.filter = 'grayscale(1)';
                      editBtn.onclick = () => editChatbotFaq(faq.id);

                      const deleteBtn = document.createElement('button');
                      deleteBtn.innerHTML = '🗑️';
                      deleteBtn.style.cssText = 'background: none; border: none; cursor: pointer; filter: grayscale(1); transition: 0.3s;';
                      deleteBtn.onmouseover = () => deleteBtn.style.filter = 'grayscale(0)';
                      deleteBtn.onmouseout = () => deleteBtn.style.filter = 'grayscale(1)';
                      deleteBtn.onclick = () => deleteChatbotFaq(faq.id);

                      actionDiv.appendChild(editBtn);
                      actionDiv.appendChild(deleteBtn);

                      div.appendChild(textDiv);
                      div.appendChild(actionDiv);
                      container.appendChild(div);
                  });
              } else {
                  container.innerHTML = '<p style="color: #9aa0b4; text-align: center;">No FAQs found.</p>';
              }
          })
          .catch(err => {
              console.error(err);
              document.getElementById('chatbotListContainer').innerHTML = '<p style="color: #dc3545; text-align: center;">Error loading FAQs.</p>';
          });
      }

      function editChatbotFaq(id) {
          const faq = currentFaqs.find(f => f.id == id);
          if (faq) {
              document.getElementById('chatbotEditId').value = faq.id;
              document.getElementById('chatbotQuestion').value = faq.question;
              document.getElementById('chatbotAnswer').value = faq.answer;
              document.getElementById('chatbotSubmitBtn').textContent = 'Update FAQ';
              document.getElementById('chatbotCancelBtn').style.display = 'block';
          }
      }

      function cancelEditChatbotFaq() {
          document.getElementById('chatbotEditId').value = '';
          document.getElementById('chatbotQuestion').value = '';
          document.getElementById('chatbotAnswer').value = '';
          document.getElementById('chatbotSubmitBtn').textContent = 'Add FAQ';
          document.getElementById('chatbotCancelBtn').style.display = 'none';
      }

      function deleteChatbotFaq(id) {
          if (!confirm("Are you sure you want to delete this FAQ?")) return;
          
          fetch('api/index.php/chatbot/faqs/' + id, {
              method: 'DELETE'
          })
          .then(res => res.json())
          .then(data => {
              if (data.success) {
                  loadChatbotFaqs();
              } else {
                  alert(data.message || "Error deleting FAQ");
              }
          })
          .catch(err => console.error(err));
      }

      document.getElementById('chatbotForm').addEventListener('submit', function(e) {
          e.preventDefault();
          const id = document.getElementById('chatbotEditId').value;
          const question = document.getElementById('chatbotQuestion').value;
          const answer = document.getElementById('chatbotAnswer').value;
          
          const payload = { question, answer };
          const method = id ? 'PUT' : 'POST';
          const url = id ? 'api/index.php/chatbot/faqs/' + id : 'api/index.php/chatbot/faqs';
          
          document.getElementById('chatbotSubmitBtn').disabled = true;

          fetch(url, {
              method: method,
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify(payload)
          })
          .then(res => res.json())
          .then(data => {
              document.getElementById('chatbotSubmitBtn').disabled = false;
              if (data.success) {
                  cancelEditChatbotFaq();
                  loadChatbotFaqs();
              } else {
                  alert(data.message || "Error saving FAQ");
              }
          })
          .catch(err => {
              console.error(err);
              document.getElementById('chatbotSubmitBtn').disabled = false;
              alert("Server error");
          });
      });

      async function loadNewsletterSubscribers() {
          const container = document.getElementById("subscribersListContainer");
          try {
              const res = await fetch("api/index.php/newsletter-subscribers");
              const data = await res.json();
              if (data.success && data.data) {
                  const subs = data.data;
                  if (subs.length === 0) {
                      container.innerHTML = `<p style="color: #9aa0b4; text-align: center;">No newsletter subscribers found.</p>`;
                      return;
                  }
                  
                  let html = '';
                  subs.forEach(sub => {
                      html += `
                      <div style="background: rgba(255,255,255,0.05); padding: 12px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1); display:flex; justify-content:space-between; align-items:center;">
                          <div>
                              <div style="color: #fff; font-weight:600; font-size:0.9rem;">${sub.email}</div>
                              <div style="color: #9aa0b4; font-size:0.75rem;">Subscribed: ${new Date(sub.date).toLocaleDateString()}</div>
                          </div>
                          <div>
                              <span style="background: ${sub.status === 'ACTIVE' ? 'rgba(74, 222, 128, 0.2)' : 'rgba(239, 68, 68, 0.2)'}; color: ${sub.status === 'ACTIVE' ? '#4ade80' : '#ef4444'}; padding: 4px 8px; border-radius: 12px; font-size: 0.7rem; font-weight: bold;">${sub.status}</span>
                          </div>
                      </div>
                      `;
                  });
                  container.innerHTML = html;
              } else {
                  container.innerHTML = `<p style="color: #ef4444; text-align: center;">Failed to load subscribers.</p>`;
              }
          } catch (err) {
              console.error(err);
              container.innerHTML = `<p style="color: #ef4444; text-align: center;">Error loading subscribers.</p>`;
          }
      }

      async function deleteReview(id) {
          if (!confirm("Are you sure you want to delete this review?")) return;
          try {
              const res = await fetch(`api/index.php/page-reviews/${id}`, { method: 'DELETE' });
              const data = await res.json();
              if (data.success) {
                  loadPageReviews();
              } else {
                  alert("Failed to delete review: " + data.message);
              }
          } catch (err) {
              console.error(err);
              alert("Error deleting review.");
          }
      }

      async function loadPageReviews() {
          const container = document.getElementById("reviewsAdminContainer");
          try {
              const res = await fetch("api/index.php/page-reviews");
              const reviews = await res.json();
              
              if (Array.isArray(reviews)) {
                  if (reviews.length === 0) {
                      container.innerHTML = `<p style="color: #9aa0b4; text-align: center;">No reviews found.</p>`;
                      return;
                  }
                  
                  let html = '';
                  reviews.forEach(review => {
                      let stars = '';
                      for(let i=0; i<5; i++) {
                          stars += i < review.rating ? '<i class="fa-solid fa-star" style="color:#c5a85c;"></i>' : '<i class="fa-regular fa-star" style="color:#c5a85c;"></i>';
                      }
                      
                      html += `
                      <div style="background: rgba(255,255,255,0.05); padding: 15px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1); position: relative;">
                          <div style="display:flex; justify-content:space-between; margin-bottom: 8px;">
                              <div>
                                  <div style="color: #fff; font-weight:600; font-size:1rem;">${review.author} <span style="background: rgba(197, 168, 92, 0.2); color: #c5a85c; font-size:0.7rem; padding: 2px 6px; border-radius:10px; margin-left: 8px;">${review.role}</span></div>
                                  <div style="font-size:0.8rem; margin-top:4px;">${stars}</div>
                              </div>
                              <button onclick="deleteReview(${review.id})" style="background: rgba(239, 68, 68, 0.2); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444; width: 30px; height: 30px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s;" title="Delete Review">
                                  <i class="fa-solid fa-trash"></i>
                              </button>
                          </div>
                          <div style="color: #9aa0b4; font-size:0.9rem; line-height:1.4; margin-bottom: 8px;">"${review.comment}"</div>
                          <div style="color: #666; font-size:0.75rem;">${new Date(review.created_at).toLocaleString()}</div>
                      </div>
                      `;
                  });
                  container.innerHTML = html;
              } else {
                  container.innerHTML = `<p style="color: #ef4444; text-align: center;">Failed to load reviews.</p>`;
              }
          } catch (err) {
              console.error(err);
              container.innerHTML = `<p style="color: #ef4444; text-align: center;">Error loading reviews.</p>`;
          }
      }

      document.addEventListener('DOMContentLoaded', () => {
          loadChatbotFaqs();
          loadNewsletterSubscribers();
          loadPageReviews();
      });
      </script>

      
<!-- Newsletter Subscribers -->
      <div class="admin-card" style="background: #12131c; border: 1px solid rgba(197, 168, 92, 0.2); border-radius: 20px; padding: 25px;">
        <h2 style="color: #c5a85c; margin: 0 0 20px 0;">📰 Newsletter Subscribers</h2>
        <div id="subscribersListContainer" style="display: grid; gap: 8px; max-height: 250px; overflow-y: auto;">
          <p style="color: #9aa0b4; text-align: center;">Loading subscribers...</p>
        </div>
      </div>

      
<!-- Manage Page Reviews -->
      <div class="admin-card" style="background: #12131c; border: 1px solid rgba(197, 168, 92, 0.2); border-radius: 20px; padding: 25px; margin-top: 30px;">
        <h2 style="color: #c5a85c; margin: 0 0 20px 0;">? Manage Page Reviews</h2>
        <div id="reviewsAdminContainer" style="display: grid; gap: 12px; max-height: 350px; overflow-y: auto;">
          <p style="color: #9aa0b4; text-align: center;">Loading reviews...</p>
        </div>
      </div>
    </div>

  </div>

  
</div>
</div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
