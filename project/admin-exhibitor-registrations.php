<?php
$pageTitle = "GLOBAL SPORTS ARENA | Exhibitor Registrations";
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$db = Database::getConnection();
$pStmt = $db->query("SELECT setting_value FROM system_settings WHERE setting_key = 'exhibitor_pricing'");
$pricingRow = $pStmt->fetch(PDO::FETCH_ASSOC);
$exhibitorPricingJSON = $pricingRow ? $pricingRow['setting_value'] : '{}';
?>
<link rel="stylesheet" href="assets/css/AdminDashboard.css?v=7">
<div class="admin-dashboard px-4 sm:px-8 py-10" style="background: #0b0c10; color: #f5f6fa; min-height: 100vh;">
  <?php require_once __DIR__ . '/includes/admin_navbar.php'; ?>
  
  <div class="admin-header" style="border-bottom: 1px solid rgba(197, 168, 92, 0.2); padding-bottom: 20px;">
    <h1>🏢 Exhibitor Registrations</h1>
    <p>Review applications from businesses registering booths for the event.</p>
  </div>

  <div id="adminGlobalLoading" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(11,12,16,0.8); z-index: 9999; justify-content: center; align-items: center; color: #c5a85c; font-size: 1.5rem; font-weight: bold;">
    ⏳ Loading records...
  </div>

  <div class="admin-content" style="display: block; margin-top: 30px;">
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
          <h2 style="color: #c5a85c; margin: 0;">Live Exhibitor Passes Feed</h2>
          <button onclick="document.getElementById('locationPricingContainer').style.display = document.getElementById('locationPricingContainer').style.display === 'none' ? 'block' : 'none'" class="w-full sm:w-auto" style="background: linear-gradient(135deg, #c5a85c 0%, #8c7237 100%); color: #0b0c10; border: none; padding: 10px 20px; border-radius: 8px; font-weight: bold; cursor: pointer;">➕ Add Location-Based Exhibitor Price</button>
      </div>

      <div id="locationPricingContainer" style="display: none; margin-bottom: 30px;">
        <div class="admin-card" style="background: linear-gradient(135deg, #12131c 0%, #0b0f1e 100%); border: 1px solid rgba(197, 168, 92, 0.35); border-radius: 20px; padding: 30px; position: relative; overflow: hidden;">
          <h2 style="color: #c5a85c; margin: 0 0 6px 0; font-size: 1.2rem;">🏙️ Location-Based Exhibitor Pricing</h2>
          <p style="color: #9aa0b4; font-size: 0.82rem; margin: 0 0 24px 0;">Manage the booth sizes and prices based on the event's city.</p>

          <form id="locationPricingForm" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div style="grid-column: span 2;">
              <label style="display: block; font-size: 0.8rem; color: #c5a85c; margin-bottom: 5px; font-weight: 600;">Location (City/Country)</label>
              <input type="text" id="pricingLocation" placeholder="Enter city or country name..." style="width: 100%; padding: 10px 12px; border: 1px solid rgba(197,168,92,0.25); border-radius: 8px; background: #0b0c10; color: #fff; margin-bottom: 8px;" required onblur="loadLocationPricing(this.value)" />
            </div>

            <div>
              <label style="display: block; font-size: 0.8rem; color: #c5a85c; margin-bottom: 5px; font-weight: 600;">Standard Stall Size</label>
              <input type="text" id="pricing_std_size" placeholder="3m x 3m" style="width: 100%; padding: 10px 12px; border: 1px solid rgba(197,168,92,0.25); border-radius: 8px; background: #0b0c10; color: #fff; margin-bottom: 8px;" />
            </div>
            <div>
              <label style="display: block; font-size: 0.8rem; color: #c5a85c; margin-bottom: 5px; font-weight: 600;">Standard Stall Price (₹)</label>
              <input type="number" id="pricing_std_price" placeholder="30000" style="width: 100%; padding: 10px 12px; border: 1px solid rgba(197,168,92,0.25); border-radius: 8px; background: #0b0c10; color: #fff; margin-bottom: 8px;" />
            </div>

            <div>
              <label style="display: block; font-size: 0.8rem; color: #c5a85c; margin-bottom: 5px; font-weight: 600;">Premium Stall Size</label>
              <input type="text" id="pricing_prm_size" placeholder="6m x 3m" style="width: 100%; padding: 10px 12px; border: 1px solid rgba(197,168,92,0.25); border-radius: 8px; background: #0b0c10; color: #fff; margin-bottom: 8px;" />
            </div>
            <div>
              <label style="display: block; font-size: 0.8rem; color: #c5a85c; margin-bottom: 5px; font-weight: 600;">Premium Stall Price (₹)</label>
              <input type="number" id="pricing_prm_price" placeholder="60000" style="width: 100%; padding: 10px 12px; border: 1px solid rgba(197,168,92,0.25); border-radius: 8px; background: #0b0c10; color: #fff; margin-bottom: 8px;" />
            </div>

            <div>
              <label style="display: block; font-size: 0.8rem; color: #c5a85c; margin-bottom: 5px; font-weight: 600;">Corner Premium Size</label>
              <input type="text" id="pricing_crn_size" placeholder="6m x 6m" style="width: 100%; padding: 10px 12px; border: 1px solid rgba(197,168,92,0.25); border-radius: 8px; background: #0b0c10; color: #fff; margin-bottom: 8px;" />
            </div>
            <div>
              <label style="display: block; font-size: 0.8rem; color: #c5a85c; margin-bottom: 5px; font-weight: 600;">Corner Premium Price (₹)</label>
              <input type="number" id="pricing_crn_price" placeholder="90000" style="width: 100%; padding: 10px 12px; border: 1px solid rgba(197,168,92,0.25); border-radius: 8px; background: #0b0c10; color: #fff; margin-bottom: 8px;" />
            </div>

            <div>
              <label style="display: block; font-size: 0.8rem; color: #c5a85c; margin-bottom: 5px; font-weight: 600;">Pavilion Partner Size</label>
              <input type="text" id="pricing_pvl_size" placeholder="Custom" style="width: 100%; padding: 10px 12px; border: 1px solid rgba(197,168,92,0.25); border-radius: 8px; background: #0b0c10; color: #fff; margin-bottom: 8px;" />
            </div>
            <div>
              <label style="display: block; font-size: 0.8rem; color: #c5a85c; margin-bottom: 5px; font-weight: 600;">Pavilion Partner Price (string)</label>
              <input type="text" id="pricing_pvl_price" placeholder="2,00,000+" style="width: 100%; padding: 10px 12px; border: 1px solid rgba(197,168,92,0.25); border-radius: 8px; background: #0b0c10; color: #fff; margin-bottom: 8px;" />
            </div>
            
            <div style="grid-column: span 2; margin-top: 10px;">
              <button type="submit" id="btnSaveLocationPricing" style="background: linear-gradient(135deg, #c5a85c 0%, #8c7237 100%); color: #0b0c10; border: none; padding: 12px; border-radius: 10px; font-weight: 800; font-size: 0.95rem; cursor: pointer; letter-spacing: 0.4px;">
                💾 Save Location Pricing
              </button>
            </div>
          </form>

          <div style="margin-top: 25px; border-top: 1px solid rgba(197, 168, 92, 0.2); padding-top: 20px;">
            <h3 style="color: #c5a85c; font-size: 1rem; margin-bottom: 10px;">Configured Locations</h3>
            <div id="configuredCitiesList" style="display: flex; flex-wrap: wrap; gap: 10px;">
              <span style="color: #9aa0b4; font-size: 0.85rem;">Loading...</span>
            </div>
          </div>
        </div>
      </div>
      <div id="exhibitorRegistrationsListContainer" class="grid grid-cols-1 lg:grid-cols-2 gap-5 w-full">
        <p style="color: #9aa0b4; text-align: center; grid-column: 1 / -1;">Loading exhibitor registrations...</p>
      </div>
  </div>
</div>

<script>
window.exhibitorPricingData = <?php echo $exhibitorPricingJSON; ?>;

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
        const res = await fetch("api/index.php/exhibitors", {
            headers: { "Authorization": "Bearer " + token }
        });
        const data = await res.json();
        renderExhibitors(data);
    } catch (err) {
        console.error("Load Error:", err);
    } finally {
        showLoading(false);
    }
}

function showLoading(show) {
    document.getElementById("adminGlobalLoading").style.display = show ? "flex" : "none";
}

function renderExhibitors(registrations) {
    const container = document.getElementById("exhibitorRegistrationsListContainer");
    if (!registrations || registrations.length === 0) {
        container.innerHTML = `<p style="color: #9aa0b4; text-align: center; padding: 40px; grid-column: 1 / -1;">No exhibitor registrations found.</p>`;
        return;
    }

    container.innerHTML = registrations.map(e => {
        let statusBadge = '';
        let actionBtns = '';
        if (e.approval_status === 'pending') {
            statusBadge = '<span style="background: #f59e0b; color: #fff; padding: 3px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: bold;">Pending Review</span>';
            actionBtns = `
                <div style="margin-top: 15px; display: flex; gap: 10px;">
                    <button onclick="updateExhibitorStatus(${e.id}, 'approved')" style="background: #10b981; color: white; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-weight: bold;">Approve</button>
                    <button onclick="updateExhibitorStatus(${e.id}, 'rejected')" style="background: #ef4444; color: white; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-weight: bold;">Reject</button>
                </div>
            `;
        } else if (e.approval_status === 'approved') {
            statusBadge = '<span style="background: #10b981; color: #fff; padding: 3px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: bold;">Approved</span>';
            if (e.razorpay_payment_id) {
                statusBadge += ' <span style="background: #3b82f6; color: #fff; padding: 3px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: bold;">Paid</span>';
            } else {
                statusBadge += ' <span style="background: #6b7280; color: #fff; padding: 3px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: bold;">Unpaid</span>';
            }
        } else if (e.approval_status === 'rejected') {
            statusBadge = '<span style="background: #ef4444; color: #fff; padding: 3px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: bold;">Rejected</span>';
        }
        
        return `
        <div style="background: #12131c; border: 1px solid rgba(197,168,92,0.15); padding: 25px; border-radius: 16px; font-size: 0.95rem; display: flex; flex-direction: column; justify-content: space-between; height: 100%; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
          <div>
              <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                <strong style="color: #c5a85c; font-size: 1.25rem;">🏢 ${e.company_name}</strong>
                <span style="color: #9aa0b4; font-size: 0.85rem;">${new Date(e.created_at || Date.now()).toLocaleDateString()}</span>
              </div>
              <div style="margin-bottom: 8px;">${statusBadge}</div>
              <div style="color: #f5f6fa; margin-bottom: 8px;">🎫 Event: <strong>${e.event}</strong> | Booth: ${e.booth} (${e.reps} reps)</div>
              ${e.fee_amount ? `<div style="color: #f5f6fa; margin-bottom: 8px;">💰 Fee: ₹${parseFloat(e.fee_amount).toLocaleString('en-IN')}</div>` : ''}
              ${e.custom_build_details ? `<div style="color: #c5a85c; font-size: 0.85rem; margin-top: 10px; font-style: italic; background: rgba(197,168,92,0.05); padding: 12px; border-radius: 8px;">🛠️ Custom Build: ${e.custom_build_details}</div>` : ''}
              <div style="color: #9aa0b4; margin-bottom: 10px;">👤 Contact: ${e.contact_person} | 📧 ${e.email} | 📞 ${e.phone}</div>
          </div>
          <div style="color: #9aa0b4; margin-top: 15px; padding-top: 12px; border-top: 1px dashed rgba(255,255,255,0.1);">
            🌍 Industry: ${e.industry} | 📍 ${e.city}, ${e.country}
            ${e.website ? `<div style="margin-top: 8px;"><a href="${e.website}" target="_blank" style="color: #38bdf8; text-decoration: none; font-weight: bold;">🔗 Visit Website</a></div>` : ''}
            ${actionBtns}
          </div>
        </div>
        `;
    }).join('');
}

async function updateExhibitorStatus(id, status) {
    if (!confirm(`Are you sure you want to mark this application as ${status}?`)) return;
    
    showLoading(true);
    try {
        const token = localStorage.getItem("token");
        const res = await fetch("api/index.php/exhibitors/update-status", {
            method: "PUT",
            headers: {
                "Authorization": "Bearer " + token,
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ id, status })
        });
        const result = await res.json();
        if (result.success) {
            loadData(); // reload list
        } else {
            alert(result.message || "Failed to update status");
        }
    } catch (err) {
        console.error("Error updating status:", err);
        alert("An error occurred");
    } finally {
        showLoading(false);
    }
}

// ----------------------------------------
// Location-Based EXHIBITOR PRICING LOGIC
// ----------------------------------------
function renderConfiguredCities() {
    const listDiv = document.getElementById('configuredCitiesList');
    if (!listDiv) return;
    
    const data = window.exhibitorPricingData || {};
    const cities = Object.keys(data);
    
    if (cities.length === 0) {
        listDiv.innerHTML = '<span style="color: #9aa0b4; font-size: 0.85rem;">No cities configured yet.</span>';
        return;
    }
    
    let html = '';
    cities.forEach(city => {
        const c = city.charAt(0).toUpperCase() + city.slice(1);
        html += `
        <span style="display: inline-flex; align-items: center; gap: 8px; background: rgba(197, 168, 92, 0.1); border: 1px solid rgba(197, 168, 92, 0.3); color: #c5a85c; padding: 5px 12px; border-radius: 15px; font-size: 0.85rem;">
            ${c}
            <i class="fas fa-edit" style="cursor: pointer; padding: 2px;" onclick="document.getElementById('pricingLocation').value='${city}'; loadLocationPricing('${city}'); document.getElementById('locationPricingContainer').style.display='block';" title="Edit"></i>
            <i class="fas fa-trash-alt" style="cursor: pointer; padding: 2px; color: #ff4d4d;" onclick="deleteLocationPricing('${city}')" title="Delete"></i>
        </span>`;
    });
    listDiv.innerHTML = html;
}

async function deleteLocationPricing(city) {
    if (!confirm("Are you sure you want to delete the pricing for " + city + "?")) return;
    
    const data = window.exhibitorPricingData || {};
    delete data[city];
    window.exhibitorPricingData = data;
    
    try {
        const token = localStorage.getItem("globalsportsarena_token") || localStorage.getItem("token");
        const res = await fetch("api/index.php/settings", {
            method: "PUT",
            headers: {
                "Content-Type": "application/json",
                "Authorization": "Bearer " + token
            },
            body: JSON.stringify({ exhibitor_pricing: data })
        });
        const result = await res.json();
        if (result.success) {
            renderConfiguredCities();
        } else {
            alert(result.message || "Failed to delete location pricing");
        }
    } catch(err) {
        console.error(err);
        alert("Error deleting location pricing");
    }
}

// Call initially
setTimeout(renderConfiguredCities, 1000); // Give it a sec to load settings

function loadLocationPricing(city) {
    if (!city) return;
    const c = city.trim().toLowerCase();
    const data = window.exhibitorPricingData || {};
    
    if (data[c]) {
        document.getElementById('pricing_std_size').value = data[c].standard.size || '';
        document.getElementById('pricing_std_price').value = data[c].standard.price || '';
        document.getElementById('pricing_prm_size').value = data[c].premium.size || '';
        document.getElementById('pricing_prm_price').value = data[c].premium.price || '';
        document.getElementById('pricing_crn_size').value = data[c].corner.size || '';
        document.getElementById('pricing_crn_price').value = data[c].corner.price || '';
        document.getElementById('pricing_pvl_size').value = data[c].pavilion.size || '';
        document.getElementById('pricing_pvl_price').value = data[c].pavilion.price || '';
    } else {
        document.getElementById('pricing_std_size').value = '';
        document.getElementById('pricing_std_price').value = '';
        document.getElementById('pricing_prm_size').value = '';
        document.getElementById('pricing_prm_price').value = '';
        document.getElementById('pricing_crn_size').value = '';
        document.getElementById('pricing_crn_price').value = '';
        document.getElementById('pricing_pvl_size').value = '';
        document.getElementById('pricing_pvl_price').value = '';
    }
}

document.getElementById('locationPricingForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const city = document.getElementById('pricingLocation').value.trim().toLowerCase();
    if (!city) return;

    const data = window.exhibitorPricingData || {};
    data[city] = {
        standard: {
            size: document.getElementById('pricing_std_size').value.trim(),
            price: document.getElementById('pricing_std_price').value.trim()
        },
        premium: {
            size: document.getElementById('pricing_prm_size').value.trim(),
            price: document.getElementById('pricing_prm_price').value.trim()
        },
        corner: {
            size: document.getElementById('pricing_crn_size').value.trim(),
            price: document.getElementById('pricing_crn_price').value.trim()
        },
        pavilion: {
            size: document.getElementById('pricing_pvl_size').value.trim(),
            price: document.getElementById('pricing_pvl_price').value.trim()
        }
    };

    window.exhibitorPricingData = data;

    const btn = e.target.querySelector("button[type='submit']");
    const originalText = btn.innerHTML;
    btn.innerHTML = "Saving...";

    try {
        const token = localStorage.getItem("globalsportsarena_token") || localStorage.getItem("token");
        const res = await fetch("api/index.php/settings", {
            method: "PUT",
            headers: {
                "Content-Type": "application/json",
                "Authorization": "Bearer " + token
            },
            body: JSON.stringify({
                exhibitor_pricing: data
            })
        });
        const result = await res.json();
        if (result.success) {
            alert("Location Pricing saved successfully!");
            renderConfiguredCities();
        } else {
            alert(result.message || "Failed to save Location Pricing");
        }
    } catch(err) {
        console.error(err);
        alert("Error saving Location Pricing");
    } finally {
        btn.innerHTML = originalText;
    }
});
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
