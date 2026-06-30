<?php
$pageTitle = "GLOBAL SPORTS ARENA | Dynamic Pillar Cards Manager";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<link rel="stylesheet" href="assets/css/AdminDashboard.css?v=7">

<style>
  /* Base styles for pillar list item (dark theme default) */
  .pillar-item {
      background: rgba(11, 12, 16, 0.6);
      border: 1px solid rgba(197, 168, 92, 0.15);
      border-radius: 12px;
      padding: 15px;
      display: flex;
      align-items: flex-start;
      gap: 15px;
      justify-content: space-between;
      transition: all 0.3s ease;
  }
  .pillar-item:hover {
      border-color: rgba(197, 168, 92, 0.4);
      background: rgba(11, 12, 16, 0.8);
  }
  .pillar-item-title {
      margin: 0;
      color: #fff;
      font-size: 1rem;
      display: flex;
      align-items: center;
      gap: 8px;
  }
  .pillar-item-desc {
      margin: 4px 0 0 0;
      color: #9aa0b4;
      font-size: 0.8rem;
      line-height: 1.4;
  }
  .pillar-item-tags {
      margin-top: 6px;
      font-size: 0.75rem;
      color: #666;
  }
  .pillar-item-link {
      margin-top: 4px;
      font-size: 0.75rem;
      color: #c5a85c;
  }

  /* Light theme overrides */
  body.light-theme .pillar-item {
      background: #fafaf5 !important;
      border-color: rgba(197, 168, 92, 0.3) !important;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
  }
  body.light-theme .pillar-item:hover {
      background: #ffffff !important;
      border-color: #8c6010 !important;
      box-shadow: 0 6px 18px rgba(197, 168, 92, 0.15);
  }
  body.light-theme .pillar-item-title {
      color: #1a1a1a !important;
  }
  body.light-theme .pillar-item-desc {
      color: #4a4a4a !important;
  }
  body.light-theme .pillar-item-tags {
      color: #777777 !important;
  }
  body.light-theme .pillar-item-link {
      color: #8c6010 !important;
  }
  
  body.light-theme .admin-header {
      border-color: rgba(197, 168, 92, 0.3) !important;
  }
  body.light-theme .admin-header p {
      color: #4a4a4a !important;
  }
</style>

<div class="admin-dashboard px-4 sm:px-8 py-10" style="min-height: 100vh;">
  <?php require_once __DIR__ . '/includes/admin_navbar.php'; ?>
  
  <!-- Header -->
  <div class="admin-header" style="border-bottom: 1px solid rgba(197, 168, 92, 0.2); padding-bottom: 20px; margin-bottom: 30px;">
    <div class="header-left">
      <div class="admin-badge" style="background: linear-gradient(135deg, #c5a85c 0%, #8c7237 100%); color: #0b0c10; font-weight: bold; display: inline-block; padding: 4px 12px; border-radius: 4px; margin-bottom: 10px;">
        🏛️ Core Pillars CRUD
      </div>
      <h1>Pillar Cards & Redirects Manager</h1>
      <p>Add new custom pillars, update tags/descriptions/icons, configure redirection links, or delete cards entirely.</p>
    </div>
  </div>

  <div class="admin-content" style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 30px;">
    
    <!-- Left Column: Current Cards List -->
    <div>
      <div class="admin-card" style="background: #12131c; border: 1px solid rgba(197, 168, 92, 0.2); border-radius: 20px; padding: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
        <h2 style="color: #c5a85c; margin: 0 0 20px 0; display: flex; align-items: center; justify-content: space-between;">
          <span><i class="fa-solid fa-list"></i> Active Pillar Cards</span>
          <button onclick="resetPillars()" style="background: transparent; color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.4); padding: 6px 12px; border-radius: 6px; font-size: 0.85rem; font-weight: bold; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='rgba(239,68,68,0.1)'" onmouseout="this.style.background='transparent'">
            <i class="fa-solid fa-arrow-rotate-left"></i> Reset Defaults
          </button>
        </h2>

        <div id="pillarsListContainer" style="display: grid; gap: 15px;">
          <p style="color: #9aa0b4; text-align: center; padding: 20px;">Loading cards...</p>
        </div>
      </div>
    </div>

    <!-- Right Column: Add / Edit Form -->
    <div>
      <!-- Form Card -->
      <div class="admin-card" id="formCard" style="background: #12131c; border: 1px solid rgba(197, 168, 92, 0.2); border-radius: 20px; padding: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); position: sticky; top: 100px;">
        <h2 id="formTitle" style="color: #c5a85c; margin: 0 0 20px 0;">
          <i class="fa-solid fa-square-plus"></i> Add New Pillar Card
        </h2>
        
        <form id="pillarForm" onsubmit="handleFormSubmit(event)">
          <input type="hidden" id="editCardId" value="">
          
          <div style="display: grid; gap: 15px; margin-bottom: 20px;">
            <div>
              <label for="pillarTitle" style="display: block; font-size: 0.85rem; color: #9aa0b4; margin-bottom: 5px; font-weight: 500;">Pillar Title</label>
              <input type="text" id="pillarTitle" placeholder="e.g. METROXIA" style="width: 100%; padding: 10px; border: 1px solid rgba(197, 168, 92, 0.3); border-radius: 6px; background: #0b0c10; color: #fff; box-sizing: border-box;" required>
            </div>

            <div>
              <label for="pillarIcon" style="display: block; font-size: 0.85rem; color: #9aa0b4; margin-bottom: 5px; font-weight: 500;">Icon Class (FontAwesome)</label>
              <input type="text" id="pillarIcon" placeholder="e.g. fas fa-city" style="width: 100%; padding: 10px; border: 1px solid rgba(197, 168, 92, 0.3); border-radius: 6px; background: #0b0c10; color: #fff; box-sizing: border-box;" required>
              <p style="font-size: 0.75rem; color: #666; margin-top: 3px;">Examples: fas fa-leaf, fas fa-chart-line, fas fa-city, fas fa-heartbeat. Falls back to this icon if no custom logo is uploaded.</p>
            </div>

            <div>
              <label for="pillarLogo" style="display: block; font-size: 0.85rem; color: #9aa0b4; margin-bottom: 5px; font-weight: 500;">Card Logo Image URL (Optional)</label>
              <input type="text" id="pillarLogo" placeholder="e.g. uploads/my_logo.png" style="width: 100%; padding: 10px; border: 1px solid rgba(197, 168, 92, 0.3); border-radius: 6px; background: #0b0c10; color: #fff; box-sizing: border-box;">
              
              <div style="margin-top: 10px; border-top: 1px dashed rgba(197,168,92,0.15); padding-top: 10px;">
                <label for="pillarLogoFile" style="display: block; font-size: 0.8rem; color: #9aa0b4; margin-bottom: 5px;">Or Upload Logo from Device</label>
                <input type="file" id="pillarLogoFile" accept="image/*" style="width: 100%; color: #fff;" onchange="uploadLogoFile(this)">
                <span id="logoUploadStatus" style="font-size: 0.75rem; color: #666; margin-top: 3px; display: block;">Choose a logo image file to upload.</span>
              </div>
            </div>

            <div>
              <label for="pillarTags" style="display: block; font-size: 0.85rem; color: #9aa0b4; margin-bottom: 5px; font-weight: 500;">Tags (Comma-separated)</label>
              <input type="text" id="pillarTags" placeholder="e.g. Urban, Infrastructure, Smart City" style="width: 100%; padding: 10px; border: 1px solid rgba(197, 168, 92, 0.3); border-radius: 6px; background: #0b0c10; color: #fff; box-sizing: border-box;" required>
            </div>

            <div>
              <label for="pillarDescription" style="display: block; font-size: 0.85rem; color: #9aa0b4; margin-bottom: 5px; font-weight: 500;">Description</label>
              <textarea id="pillarDescription" placeholder="Brief description of this pillar's focus..." rows="3" style="width: 100%; padding: 10px; border: 1px solid rgba(197, 168, 92, 0.3); border-radius: 6px; background: #0b0c10; color: #fff; box-sizing: border-box; font-family: inherit;" required></textarea>
            </div>

            <div>
              <label for="pillarLink" style="display: block; font-size: 0.85rem; color: #9aa0b4; margin-bottom: 5px; font-weight: 500;">Custom URL Link (Optional)</label>
              <input type="text" id="pillarLink" placeholder="Leave empty to use static/default page" style="width: 100%; padding: 10px; border: 1px solid rgba(197, 168, 92, 0.3); border-radius: 6px; background: #0b0c10; color: #fff; box-sizing: border-box;">
              <p style="font-size: 0.75rem; color: #666; margin-top: 3px;">If left empty, defaults to local template page (e.g. title.php)</p>
            </div>

            <div>
              <label for="pillarImage" style="display: block; font-size: 0.85rem; color: #9aa0b4; margin-bottom: 5px; font-weight: 500;">Card Background Image URL</label>
              <input type="text" id="pillarImage" placeholder="e.g. https://images.unsplash.com/photo-..." style="width: 100%; padding: 10px; border: 1px solid rgba(197, 168, 92, 0.3); border-radius: 6px; background: #0b0c10; color: #fff; box-sizing: border-box;" required>
              
              <div style="margin-top: 10px; border-top: 1px dashed rgba(197,168,92,0.15); padding-top: 10px;">
                <label for="pillarImageFile" style="display: block; font-size: 0.8rem; color: #9aa0b4; margin-bottom: 5px;">Or Upload from Device</label>
                <input type="file" id="pillarImageFile" accept="image/*" style="width: 100%; color: #fff;" onchange="uploadImageFile(this)">
                <span id="uploadStatus" style="font-size: 0.75rem; color: #666; margin-top: 3px; display: block;">Choose a local image file to upload.</span>
              </div>
            </div>
          </div>

          <div style="display: flex; gap: 10px;">
            <button type="submit" id="submitBtn" style="background: linear-gradient(135deg, #c5a85c 0%, #8c7237 100%); color: #0b0c10; border: none; padding: 10px 20px; border-radius: 6px; font-weight: bold; cursor: pointer; display: flex; align-items: center; gap: 8px;">
              <i class="fa-solid fa-save"></i> Save Card
            </button>
            <button type="button" id="cancelBtn" onclick="cancelEdit()" style="display: none; background: rgba(255,255,255,0.1); color: #fff; border: 1px solid rgba(255,255,255,0.2); padding: 10px 20px; border-radius: 6px; cursor: pointer;">
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>

  </div>
</div>

<script>
let pillars = [];

// Load pillars on startup
document.addEventListener('DOMContentLoaded', loadPillars);

function loadPillars() {
    fetch('api/index.php/settings')
    .then(res => res.json())
    .then(data => {
        if (data.success && data.pillars) {
            pillars = data.pillars;
            renderPillarsList();
        }
    })
    .catch(err => {
        console.error("Error loading settings:", err);
    });
}

function renderPillarsList() {
    const container = document.getElementById('pillarsListContainer');
    if (!pillars.length) {
        container.innerHTML = `<p style="color:#9aa0b4; text-align:center; padding: 20px;">No cards configured.</p>`;
        return;
    }

    container.innerHTML = pillars.map((card, idx) => {
        const defaultLink = card.id + '.php';
        const displayLink = card.link && card.link.trim() !== '' ? card.link : defaultLink + ' (Static Default)';
        const displayImage = card.image || 'None';
        
        return `
            <div class="pillar-item">
                <div style="display: flex; gap: 12px; align-items: center; width: 100%;">
                    ${card.logo ? `
                    <div style="width: 40px; height: 40px; border-radius: 50%; background-image: url('${card.logo}'); background-size: cover; background-position: center; border: 1px solid #c5a85c; flex-shrink: 0;"></div>
                    ` : card.image ? `
                    <div style="width: 70px; height: 90px; border-radius: 6px; background-image: url('${card.image}'); background-size: cover; background-position: center; border: 1px solid rgba(197, 168, 92, 0.3); flex-shrink: 0;"></div>
                    ` : `
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: #12131c; border: 1px solid #c5a85c; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; color: #c5a85c; flex-shrink: 0;">
                        <i class="${card.icon || 'fas fa-square'}"></i>
                    </div>
                    `}
                    <div style="flex-grow: 1; min-width: 0;">
                        <h4 class="pillar-item-title">
                            ${card.title} 
                            <span style="font-size: 0.75rem; background: rgba(197, 168, 92, 0.15); color: #c5a85c; padding: 2px 6px; border-radius: 4px;">${card.id}</span>
                        </h4>
                        <p class="pillar-item-desc" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${card.description}</p>
                        <div class="pillar-item-tags" style="display: flex; flex-wrap: wrap; gap: 5px;">
                            <strong>Tags:</strong> ${card.tags ? card.tags.join(', ') : 'None'}
                        </div>
                        <div class="pillar-item-link" style="overflow-wrap: break-word;">
                            <strong>Link:</strong> ${displayLink}
                        </div>
                    </div>
                </div>
                <div style="display: flex; gap: 8px; flex-shrink: 0;">
                    <button onclick="editCard(${idx})" style="background: rgba(197, 168, 92, 0.1); border: 1px solid rgba(197, 168, 92, 0.3); color: #c5a85c; padding: 6px 10px; border-radius: 6px; font-size: 0.8rem; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#c5a85c'; this.style.color='#12131c';" onmouseout="this.style.background='rgba(197,168,92,0.1)'; this.style.color='#c5a85c';">
                        <i class="fa-solid fa-pen"></i> Edit
                    </button>
                    <button onclick="deleteCard(${idx})" style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444; padding: 6px 10px; border-radius: 6px; font-size: 0.8rem; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#ef4444'; this.style.color='#fff';" onmouseout="this.style.background='rgba(239,68,68,0.1)'; this.style.color='#ef4444';">
                        <i class="fa-solid fa-trash-can"></i> Delete
                    </button>
                </div>
            </div>
        `;
    }).join('');
}

function handleFormSubmit(e) {
    e.preventDefault();
    const editIdxStr = document.getElementById('editCardId').value;
    const title = document.getElementById('pillarTitle').value.trim();
    const icon = document.getElementById('pillarIcon').value.trim();
    const logo = document.getElementById('pillarLogo').value.trim();
    const tagsStr = document.getElementById('pillarTags').value.trim();
    const description = document.getElementById('pillarDescription').value.trim();
    const link = document.getElementById('pillarLink').value.trim();
    const image = document.getElementById('pillarImage').value.trim();
    
    // Generate unique slug id from title
    const id = title.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
    const tags = tagsStr.split(',').map(t => t.trim()).filter(t => t.length > 0);

    const cardData = { id, title, icon, logo, tags, description, link, image };

    if (editIdxStr !== '') {
        // Edit mode
        const idx = parseInt(editIdxStr);
        pillars[idx] = cardData;
    } else {
        // Add mode
        // Check for duplicates
        if (pillars.some(p => p.id === id)) {
            alert("A pillar card with this title already exists!");
            return;
        }
        pillars.push(cardData);
    }

    savePillarsSettings();
}

function editCard(idx) {
    const card = pillars[idx];
    document.getElementById('editCardId').value = idx;
    document.getElementById('pillarTitle').value = card.title;
    document.getElementById('pillarIcon').value = card.icon;
    document.getElementById('pillarLogo').value = card.logo || '';
    document.getElementById('pillarTags').value = card.tags ? card.tags.join(', ') : '';
    document.getElementById('pillarDescription').value = card.description;
    document.getElementById('pillarLink').value = card.link || '';
    document.getElementById('pillarImage').value = card.image || '';
    
    document.getElementById('formTitle').innerHTML = `<i class="fa-solid fa-pen-to-square"></i> Edit Card: ${card.title}`;
    document.getElementById('submitBtn').innerHTML = `<i class="fa-solid fa-save"></i> Save Changes`;
    document.getElementById('cancelBtn').style.display = 'block';
}

function cancelEdit() {
    document.getElementById('editCardId').value = '';
    document.getElementById('pillarForm').reset();
    document.getElementById('formTitle').innerHTML = `<i class="fa-solid fa-square-plus"></i> Add New Pillar Card`;
    document.getElementById('submitBtn').innerHTML = `<i class="fa-solid fa-save"></i> Save Card`;
    document.getElementById('cancelBtn').style.display = 'none';
    document.getElementById('uploadStatus').textContent = 'Choose a local image file to upload.';
    document.getElementById('uploadStatus').style.color = '#666';
    document.getElementById('logoUploadStatus').textContent = 'Choose a logo image file to upload.';
    document.getElementById('logoUploadStatus').style.color = '#666';
}

function uploadImageFile(input) {
    if (!input.files || !input.files[0]) return;
    
    const file = input.files[0];
    const statusSpan = document.getElementById('uploadStatus');
    statusSpan.textContent = 'Uploading: ' + file.name + '...';
    statusSpan.style.color = '#c5a85c';
    
    const formData = new FormData();
    formData.append('file', file);
    
    fetch('api/index.php/upload', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success && data.url) {
            document.getElementById('pillarImage').value = data.url;
            statusSpan.textContent = '? Uploaded successfully!';
            statusSpan.style.color = '#22c55e';
        } else {
            statusSpan.textContent = '? Upload failed: ' + (data.message || 'Unknown error');
            statusSpan.style.color = '#ef4444';
        }
    })
    .catch(err => {
        console.error("Upload error:", err);
        statusSpan.textContent = '? Upload failed due to network error.';
        statusSpan.style.color = '#ef4444';
    });
}

function uploadLogoFile(input) {
    if (!input.files || !input.files[0]) return;
    
    const file = input.files[0];
    const statusSpan = document.getElementById('logoUploadStatus');
    statusSpan.textContent = 'Uploading: ' + file.name + '...';
    statusSpan.style.color = '#c5a85c';
    
    const formData = new FormData();
    formData.append('file', file);
    
    fetch('api/index.php/upload', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success && data.url) {
            document.getElementById('pillarLogo').value = data.url;
            statusSpan.textContent = '? Uploaded successfully!';
            statusSpan.style.color = '#22c55e';
        } else {
            statusSpan.textContent = '? Upload failed: ' + (data.message || 'Unknown error');
            statusSpan.style.color = '#ef4444';
        }
    })
    .catch(err => {
        console.error("Upload error:", err);
        statusSpan.textContent = '? Upload failed due to network error.';
        statusSpan.style.color = '#ef4444';
    });
}

function deleteCard(idx) {
    if (confirm(`Are you sure you want to delete card "${pillars[idx].title}"?`)) {
        pillars.splice(idx, 1);
        savePillarsSettings();
    }
}

function savePillarsSettings() {
    fetch('api/index.php/settings', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ pillars })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            cancelEdit();
            loadPillars();
            alert("Settings updated successfully!");
        } else {
            alert("Failed to update settings.");
        }
    })
    .catch(err => {
        console.error("Save error:", err);
        alert("An error occurred while saving.");
    });
}

function resetPillars() {
    if (confirm("Reset to default 5 pillar cards?")) {
        fetch('api/index.php/settings', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ pillars: null }) // Setting to null clears it, trigger default on next load
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                cancelEdit();
                loadPillars();
                alert("Pillars reset to defaults!");
            } else {
                alert("Failed to reset.");
            }
        })
        .catch(err => {
            console.error(err);
            alert("An error occurred while resetting.");
        });
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
