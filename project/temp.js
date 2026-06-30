
let allUsers = [];
let rewardHistory = [];
let customDestinations = [];
let currentDestFilter = 'international';

const defaultDestinations = [
    { id: 1, country: "INDIA", image: "https://images.unsplash.com/photo-1564507592333-c60657eea523?w=500&auto=format&fit=crop&q=60", date: "24-26 July 2026", city: "Pune / Mumbai", region: "India" },
    { id: 2, country: "SINGAPORE", image: "https://images.unsplash.com/photo-1525625293386-3f8f99389edd?w=500&auto=format&fit=crop&q=60", date: "18-20 Sept 2026", city: "Singapore", region: "Singapore" },
    { id: 3, country: "SWITZERLAND", image: "https://images.unsplash.com/photo-1506744038136-46273834b3fb?w=500&auto=format&fit=crop&q=60", date: "May - Sep", city: "Zurich", region: "Switzerland" },
    { id: 4, country: "UAE", image: "https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=500&auto=format&fit=crop&q=60", date: "23-25 Oct 2026", city: "Dubai / Abu Dhabi", region: "UAE" },
    { id: 5, country: "THAILAND", image: "https://images.unsplash.com/photo-1508009603885-50cf7c579365?w=500&auto=format&fit=crop&q=60", date: "18-20 Dec 2026", city: "Phuket / Bangkok", region: "Thailand" },
    { id: 6, country: "USA - LAS VEGAS", image: "https://images.unsplash.com/photo-1501183007986-d0d080b147f9?w=500&auto=format&fit=crop&q=60", date: "23-25 July 2027", city: "Las Vegas", region: "USA" },
    { id: 7, country: "USA - NEW YORK", image: "https://images.unsplash.com/photo-1526304640581-d334cdbbf45e?w=500&auto=format&fit=crop&q=60", date: "23-25 July 2027", city: "New York", region: "USA" },
    { id: 8, country: "MALAYSIA", image: "https://images.unsplash.com/photo-1596422846543-75c6fc197f07?w=500&auto=format&fit=crop&q=60", date: "20-22 Nov 2026", city: "Kuala Lumpur", region: "Malaysia" },
    { id: 9, country: "INDONESIA", image: "https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=500&auto=format&fit=crop&q=60", date: "22-24 Jan 2027", city: "Bali / Jakarta", region: "Indonesia" },
    { id: 10, country: "VIETNAM", image: "https://images.unsplash.com/photo-1528127269322-539801943592?w=500&auto=format&fit=crop&q=60", date: "19-21 Feb 2027", city: "Ho Chi Minh", region: "Vietnam" },
    { id: 11, country: "AUSTRALIA", image: "https://images.unsplash.com/photo-1523482580672-f109ba8cb9be?w=500&auto=format&fit=crop&q=60", date: "19-21 March 2027", city: "Sydney", region: "Australia" },
    { id: 12, country: "GERMANY", image: "https://images.unsplash.com/photo-1467269204594-9661b134dd2b?w=500&auto=format&fit=crop&q=60", date: "23-25 April 2027", city: "Berlin", region: "Germany" },
    { id: 13, country: "UNITED KINGDOM", image: "https://images.unsplash.com/photo-1505761671935-60b3a7427bad?w=500&auto=format&fit=crop&q=60", date: "21-23 May 2027", city: "London", region: "UK" },
    { id: 14, country: "CANADA", image: "https://images.unsplash.com/photo-1503614472-8c93d56e92ce?w=500&auto=format&fit=crop&q=60", date: "18-20 June 2027", city: "Toronto", region: "Canada" }
];

const defaultNationalDestinations = [
    { id: 101, country: "MAHARASHTRA", image: "https://images.unsplash.com/photo-1570168007204-dfb528c6958f?w=500&auto=format&fit=crop&q=60", date: "10-12 Aug 2026", city: "Mumbai / Pune", region: "India", type: "national" },
    { id: 102, country: "KARNATAKA", image: "https://images.unsplash.com/photo-1596176530529-78163a4f7af2?w=500&auto=format&fit=crop&q=60", date: "15-17 Sept 2026", city: "Bangalore", region: "India", type: "national" },
    { id: 103, country: "DELHI", image: "https://images.unsplash.com/photo-1587474260584-136574528ed5?w=500&auto=format&fit=crop&q=60", date: "05-07 Oct 2026", city: "New Delhi", region: "India", type: "national" },
    { id: 104, country: "GOA", image: "https://images.unsplash.com/photo-1512343879784-a960bf40e7f2?w=500&auto=format&fit=crop&q=60", date: "20-22 Nov 2026", city: "Panaji", region: "India", type: "national" },
    { id: 105, country: "KERALA", image: "https://images.unsplash.com/photo-1602216056096-3b40cc0c9944?w=500&auto=format&fit=crop&q=60", date: "12-14 Dec 2026", city: "Kochi", region: "India", type: "national" },
    { id: 106, country: "RAJASTHAN", image: "https://images.unsplash.com/photo-1477587458883-47145ed94245?w=500&auto=format&fit=crop&q=60", date: "15-17 Jan 2027", city: "Jaipur", region: "India", type: "national" },
    { id: 107, country: "GUJARAT", image: "https://images.unsplash.com/photo-1605130284535-11dd9eedc58a?w=500&auto=format&fit=crop&q=60", date: "10-12 Feb 2027", city: "Ahmedabad", region: "India", type: "national" }
];

document.addEventListener("DOMContentLoaded", function() {
    const token = localStorage.getItem("token");
    const role = localStorage.getItem("userRole");

    if (!token || role !== "ADMIN") {
        alert("Access Denied: Admin login required!");
        window.location.href = "login.php";
        return;
    }

    loadDashboardData();

    // Setup Event CRUD Form Submission
    const tourForm = document.getElementById("tournamentForm");
    if (tourForm) tourForm.addEventListener("submit", handleSaveTournament);

    // Setup forms
    const wf = document.getElementById("walletAdjustForm");
    if (wf) wf.addEventListener("submit", handleAdjustWallet);

    const mtf = document.getElementById("merchantTopupForm");
    if (mtf) mtf.addEventListener("submit", handleMerchantTopup);

    // Setup Reward Form Submission
    const rf = document.getElementById("rewardForm");
    if (rf) rf.addEventListener("submit", handleSendReward);

    // Init Gift Card Management panel
    loadAdminGiftCards();



    // Setup User Edit Form Submission
    const uef = document.getElementById("userEditForm");
    if (uef) uef.addEventListener("submit", handleSaveUser);
    
    const bcue = document.getElementById("btnCancelUserEdit");
    if (bcue) bcue.addEventListener("click", function() {
        document.getElementById("userEditSection").style.display = "none";
    });

    // Setup Custom Destination Form Submission
    const adf = document.getElementById("addDestinationForm");
    if (adf) adf.addEventListener("submit", handleAddDestination);

    loadCustomDestinations();
    
    // Setup Custom Partner Form Submission
    document.getElementById("addPartnerForm").addEventListener("submit", handleAddPartner);
    loadPartners();

    const partnerLogoFile = document.getElementById("partnerLogoFile");
    const partnerLogoPreview = document.getElementById("partnerLogoPreview");
    const partnerPreviewImg = document.getElementById("partnerPreviewImg");
    if (partnerLogoFile) {
        partnerLogoFile.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                if (!validTypes.includes(file.type)) {
                    alert("Invalid file format. Please upload JPG, JPEG, PNG, or WEBP.");
                    this.value = "";
                    partnerLogoPreview.style.display = "none";
                    return;
                }
                const reader = new FileReader();
                reader.onload = function(evt) {
                    partnerPreviewImg.src = evt.target.result;
                    partnerLogoPreview.style.display = "block";
                }
                reader.readAsDataURL(file);
            } else {
                partnerLogoPreview.style.display = "none";
            }
        });

        const btnClearPartnerFile = document.getElementById('btnClearPartnerFile');
        if (btnClearPartnerFile) {
            btnClearPartnerFile.addEventListener('click', () => {
                partnerLogoFile.value = "";
                partnerLogoPreview.style.display = "none";
            });
        }
    }
    
    // Media Hub form submission and load
    document.getElementById("mediaHubForm").addEventListener("submit", handleAddMedia);
    loadMediaAdmin();
});

// Load Custom Destinations from API
async function loadCustomDestinations() {
    try {
        const response = await fetch('api/index.php/destinations');
        const data = await response.json();
        if (Array.isArray(data)) {
            customDestinations = data;
        } else {
            customDestinations = [];
        }
    } catch(e) {
        console.error("Failed to load destinations from DB", e);
        customDestinations = [];
    }
    renderDestinations();
}

function setDestFilter(type) {
    currentDestFilter = type;
    document.getElementById("btnFilterInternational").style.background = type === 'international' ? 'rgba(197,168,92,0.2)' : 'rgba(255,255,255,0.05)';
    document.getElementById("btnFilterInternational").style.color = type === 'international' ? '#c5a85c' : '#9aa0b4';
    document.getElementById("btnFilterInternational").style.border = type === 'international' ? '1px solid #c5a85c' : '1px solid rgba(255,255,255,0.15)';
    
    document.getElementById("btnFilterNational").style.background = type === 'national' ? 'rgba(197,168,92,0.2)' : 'rgba(255,255,255,0.05)';
    document.getElementById("btnFilterNational").style.color = type === 'national' ? '#c5a85c' : '#9aa0b4';
    document.getElementById("btnFilterNational").style.border = type === 'national' ? '1px solid #c5a85c' : '1px solid rgba(255,255,255,0.15)';
    
    renderDestinations();
}

function getMergedDestinations() {
    const allDefaults = [...defaultDestinations, ...defaultNationalDestinations];
    let mergedAll = allDefaults.map(d => {
        const customOverride = customDestinations.find(c => c.id === d.id);
        return customOverride ? customOverride : d;
    });
    const purelyNew = customDestinations.filter(c => !allDefaults.some(d => d.id === c.id));
    return [...mergedAll, ...purelyNew].filter(d => !d.deleted);
}

function renderDestinations() {
    const container = document.getElementById("destinationsContainer");
    if (!container) return;

    const mergedDestinations = getMergedDestinations();
    const filteredDestinations = mergedDestinations.filter(d => {
        const type = d.type || (d.id > 100 ? 'national' : 'international');
        return type === currentDestFilter;
    });

    if (filteredDestinations.length === 0) {
        container.innerHTML = `
            <div style="text-align: center; padding: 40px 20px; background: rgba(255,255,255,0.02); border-radius: 12px; border: 1px dashed rgba(197,168,92,0.2);">
              <div style="font-size: 2rem; margin-bottom: 10px;">🌐</div>
              <p style="color: #9aa0b4; margin: 0; font-size: 0.85rem;">No custom ${currentDestFilter} destinations added yet.<br>Use the form to add new places.</p>
            </div>
        `;
        return;
    }

    container.innerHTML = filteredDestinations.map(dest => `
        <div style="background: #0b0c10; border: 1px solid rgba(197,168,92,0.15); border-radius: 12px; padding: 12px 14px; display: flex; gap: 12px; align-items: center;">
          <img src="${dest.image}" alt="${dest.country}" style="width: 55px; height: 55px; object-fit: cover; border-radius: 8px; flex-shrink: 0;" onerror="this.style.display='none';">
          <div style="flex: 1; min-width: 0;">
            <div style="font-weight: 700; color: #f5f6fa; font-size: 0.9rem;">${dest.country.toUpperCase()} <span style="font-size: 0.7rem; color: #c5a85c;">(${dest.type || 'international'})</span></div>
            <div style="color: #9aa0b4; font-size: 0.78rem; margin-top: 2px;">📅 ${dest.date}</div>
            <div style="color: #9aa0b4; font-size: 0.78rem;">📍 ${dest.city}</div>
          </div>
          <div style="display: flex; gap: 8px; flex-shrink: 0;">
            <button onclick="editDestination(${dest.id})" style="background: rgba(197,168,92,0.12); border: 1px solid #c5a85c; color: #c5a85c; padding: 6px 10px; border-radius: 6px; font-size: 0.78rem; cursor: pointer;">✏️ Edit</button>
            <button onclick="handleDeleteDestination(${dest.id})" style="background: rgba(220,38,38,0.12); border: 1px solid #dc2626; color: #f87171; padding: 6px 10px; border-radius: 6px; font-size: 0.78rem; cursor: pointer;">🗑️ Remove</button>
          </div>
        </div>
    `).join('');
}

function handleAddDestination(e) {
    e.preventDefault();
    const editId = document.getElementById("editDestId").value;
    const country = document.getElementById("destCountry").value.trim().toUpperCase();
    const image = document.getElementById("destImageUrl").value.trim();
    const date = document.getElementById("destDate").value.trim();
    const city = document.getElementById("destCity").value.trim();
    const type = document.getElementById("destType").value;
    const region = document.getElementById("destRegion").value.trim() || city;
    const link = document.getElementById("destLink").value.trim() || `events.php?country=${country.toLowerCase()}`;

    if (!country || !image || !date || !city) return;

    let targetId = Date.now();
    if (editId) {
        targetId = parseInt(editId, 10) || editId;
    }
    
    const newDest = {
        id: targetId,
        country: country,
        image: image,
        date: date,
        city: city,
        region: region,
        type: type,
        link: link
    };

    fetch('api/index.php/destinations', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(newDest)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(`✅ "${country}" ${editId ? 'updated' : 'added'} successfully to the ${type === 'national' ? 'National' : 'International'} Event Destinations carousel!`);
            loadCustomDestinations(); // Reload from DB
        } else {
            alert('Failed to save destination: ' + data.message);
        }
    })
    .catch(err => {
        console.error(err);
        alert('Error saving destination to database.');
    });

    cancelDestEdit();
    setDestFilter(type); // switch to the added/edited type
}

function editDestination(id) {
    const mergedDestinations = getMergedDestinations();
    const dest = mergedDestinations.find(d => d.id === id);
    if (!dest) return;
    
    document.getElementById("editDestId").value = dest.id;
    document.getElementById("destCountry").value = dest.country;
    document.getElementById("destImageUrl").value = dest.image;
    document.getElementById("destDate").value = dest.date;
    document.getElementById("destCity").value = dest.city;
    document.getElementById("destRegion").value = dest.region || dest.city;
    document.getElementById("destType").value = dest.type || (dest.id > 100 ? 'national' : 'international');
    document.getElementById("destLink").value = dest.link && dest.link !== "#" ? dest.link : "";
    
    document.getElementById("btnSaveDest").textContent = "Update Destination";
    document.getElementById("btnCancelDestEdit").style.display = "inline-block";
    
    // Scroll up to form
    document.getElementById("addDestinationForm").scrollIntoView({ behavior: 'smooth' });
}

function cancelDestEdit() {
    document.getElementById("editDestId").value = "";
    document.getElementById("addDestinationForm").reset();
    document.getElementById("btnSaveDest").textContent = "🌍 Add to Home Carousel";
    document.getElementById("btnCancelDestEdit").style.display = "none";
}

function handleDeleteDestination(id) {
    if (!confirm("Are you sure you want to remove this destination?")) return;

    const parsedId = parseInt(id, 10) || id;
    
    fetch(`api/index.php/destinations/${parsedId}`, {
        method: 'DELETE'
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            loadCustomDestinations(); // Reload from DB
        } else {
            alert('Failed to delete destination.');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Error deleting destination from database.');
    });
}

// --- PARTNERS LOGIC ---
const defaultPartners = [
    { id: 1, name: "TATA GROUP", icon: "👔", link: "https://www.tata.com" },
    { id: 2, name: "INFOSYS", icon: "💻", link: "https://www.infosys.com" },
    { id: 3, name: "HDFC BANK", icon: "🏦", link: "https://www.hdfcbank.com" },
    { id: 4, name: "GOOGLE", icon: "🔍", link: "https://www.google.com" },
    { id: 5, name: "BOOKMYSHOW", icon: "🎟️", link: "https://in.bookmyshow.com" },
    { id: 6, name: "DECATHLON", icon: "👟", link: "https://www.decathlon.in" }
];

let customPartners = [];

function loadPartners() {
    try {
        customPartners = JSON.parse(localStorage.getItem("globalsportsarena_custom_partners") || "[]");
    } catch(e) {
        customPartners = [];
    }
    renderPartnersList();
}

function getMergedPartners() {
    let mergedAll = defaultPartners.map(p => {
        const customOverride = customPartners.find(c => c.id === p.id);
        return customOverride ? customOverride : p;
    });
    const purelyNew = customPartners.filter(c => !defaultPartners.some(p => p.id === c.id));
    return [...mergedAll, ...purelyNew].filter(p => !p.deleted);
}

function renderPartnersList() {
    const container = document.getElementById("partnersContainer");
    if (!container) return;

    const mergedPartners = getMergedPartners();

    if (mergedPartners.length === 0) {
        container.innerHTML = `
            <div style="text-align: center; padding: 40px 20px; background: rgba(255,255,255,0.02); border-radius: 12px; border: 1px dashed rgba(197,168,92,0.2);">
              <div style="font-size: 2rem; margin-bottom: 10px;">🤝</div>
              <p style="color: #9aa0b4; margin: 0; font-size: 0.85rem;">No partners available.<br>Use the form to add new partners.</p>
            </div>
        `;
        return;
    }

    container.innerHTML = mergedPartners.map(partner => `
        <div style="background: #0b0c10; border: 1px solid rgba(197,168,92,0.15); border-radius: 12px; padding: 12px 14px; display: flex; gap: 12px; align-items: center;">
          <div style="font-size: 2rem; flex-shrink: 0; width: 40px; text-align: center; display: flex; justify-content: center; align-items: center;">
             ${(partner.icon.startsWith('data:image') || partner.icon.startsWith('http') || partner.icon.includes('/')) ? `<img src="${partner.icon}" style="max-width: 100%; max-height: 40px; object-fit: contain;">` : partner.icon}
          </div>
          <div style="flex: 1; min-width: 0;">
            <div style="font-weight: 700; color: #f5f6fa; font-size: 0.9rem;">${partner.name.toUpperCase()}</div>
            <div style="color: #9aa0b4; font-size: 0.78rem; margin-top: 2px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">🔗 ${partner.link}</div>
          </div>
          <div style="display: flex; flex-direction: column; gap: 5px; flex-shrink: 0;">
              <button onclick="editPartner(${partner.id})" style="background: rgba(197,168,92,0.15); border: 1px solid #c5a85c; color: #c5a85c; padding: 4px 10px; border-radius: 4px; cursor: pointer; font-size: 0.75rem;">✏️ Edit</button>
              <button onclick="handleDeletePartner(${partner.id})" style="background: rgba(220,38,38,0.12); border: 1px solid #dc2626; color: #f87171; padding: 4px 10px; border-radius: 4px; cursor: pointer; font-size: 0.75rem;">🗑️ Remove</button>
          </div>
        </div>
    `).join('');
}

function editPartner(id) {
    const allP = getMergedPartners();
    const partner = allP.find(p => p.id === id);
    if (!partner) return;

    document.getElementById("editPartnerId").value = partner.id;
    document.getElementById("partnerName").value = partner.name;
    document.getElementById("partnerUrl").value = partner.link;

    if (partner.icon.startsWith('data:image') || partner.icon.startsWith('http') || partner.icon.includes('/')) {
        document.getElementById("partnerIcon").value = partner.icon; // We can put the long base64 here so it's not lost
        document.getElementById("partnerPreviewImg").src = partner.icon;
        document.getElementById("partnerLogoPreview").style.display = "block";
    } else {
        document.getElementById("partnerIcon").value = partner.icon;
        document.getElementById("partnerLogoPreview").style.display = "none";
    }

    document.getElementById("btnSavePartner").innerHTML = "💾 Save Changes";
    document.getElementById("btnCancelPartnerEdit").style.display = "block";
    
    document.getElementById("addPartnerForm").scrollIntoView({ behavior: "smooth" });
}

function cancelPartnerEdit() {
    document.getElementById("editPartnerId").value = "";
    document.getElementById("addPartnerForm").reset();
    document.getElementById("btnSavePartner").innerHTML = "🤝 Add to Partners Carousel";
    document.getElementById("btnCancelPartnerEdit").style.display = "none";
    if (document.getElementById("partnerLogoFile")) {
        document.getElementById("partnerLogoFile").value = "";
        document.getElementById("partnerLogoPreview").style.display = "none";
    }
}

function handleAddPartner(e) {
    e.preventDefault();
    const editId = document.getElementById("editPartnerId").value;
    const name = document.getElementById("partnerName").value.trim().toUpperCase();
    let icon = document.getElementById("partnerIcon").value.trim();
    const link = document.getElementById("partnerUrl").value.trim();
    const fileInput = document.getElementById("partnerLogoFile");
    const file = fileInput ? fileInput.files[0] : null;

    if (!name || (!icon && !file) || !link) {
        alert("Please provide a Name, Link, and either a Logo URL or an Uploaded Image.");
        return;
    }

    if (file) {
        const reader = new FileReader();
        reader.onload = function(evt) {
            savePartner(editId, name, evt.target.result, link);
        };
        reader.readAsDataURL(file);
    } else {
        savePartner(editId, name, icon, link);
    }
}

function savePartner(editId, name, icon, link) {
    if (editId) {
        const id = parseInt(editId);
        const existingIdx = customPartners.findIndex(c => c.id === id);
        if (existingIdx !== -1) {
            customPartners[existingIdx] = { id, name, icon, link, deleted: false };
        } else {
            customPartners.push({ id, name, icon, link, deleted: false });
        }
        alert(`✅ Partner updated successfully!`);
    } else {
        const newPartner = {
            id: Date.now(),
            name: name,
            icon: icon,
            link: link,
            deleted: false
        };
        customPartners.push(newPartner);
        alert(`✅ "${name}" added to the Partners carousel!`);
    }

    localStorage.setItem("globalsportsarena_custom_partners", JSON.stringify(customPartners));
    window.dispatchEvent(new StorageEvent("storage", { key: "globalsportsarena_custom_partners" }));

    cancelPartnerEdit();
    renderPartnersList();
}

function handleDeletePartner(id) {
    if (!window.confirm("Remove this partner from the Home page carousel?")) return;
    
    const existingIdx = customPartners.findIndex(c => c.id === id);
    if (existingIdx !== -1) {
        customPartners[existingIdx].deleted = true;
    } else {
        customPartners.push({ id: id, deleted: true });
    }

    localStorage.setItem("globalsportsarena_custom_partners", JSON.stringify(customPartners));
    window.dispatchEvent(new StorageEvent("storage", { key: "globalsportsarena_custom_partners" }));
    
    renderPartnersList();
}

function resetDefaultPartners() {
    if (!window.confirm("Restore all default partners and remove custom ones?")) return;
    customPartners = [];
    localStorage.setItem("globalsportsarena_custom_partners", JSON.stringify(customPartners));
    window.dispatchEvent(new StorageEvent("storage", { key: "globalsportsarena_custom_partners" }));
    renderPartnersList();
}

// Fetch all lists from backend
async function loadDashboardData() {
    showLoading(true);
    try {
        const token = localStorage.getItem("token");

        // 1. Fetch Users
        const usersRes = await fetch("api/index.php/user/all", {
            headers: { "Authorization": "Bearer " + token }
        });
        allUsers = await usersRes.json();
        // renderUsers(); // Moved to admin-records.php

        // 2. Fetch Tournaments
        const tourRes = await fetch("api/index.php/tournaments");
        const tournaments = await tourRes.json();
        renderTournaments(tournaments);

        // 3. Fetch Enquiries
        // Moved to admin-contact-enquiries.php

        // 4. Fetch Newsletter Subscribers
        const subRes = await fetch("api/index.php/newsletter/subscribers", {
            headers: { "Authorization": "Bearer " + token }
        });
        const subscribers = await subRes.json();
        renderSubscribers(subscribers);

        // Fetch Business Inquiries
        // Moved to admin-records.php

        // Fetch System Settings
        const settingsRes = await fetch("api/index.php/settings");
        const settingsData = await settingsRes.json();
        if (settingsData && settingsData.data) {
            if (settingsData.data.event_fee !== undefined) {
                document.getElementById("settingEventFee").value = settingsData.data.event_fee;
            }
            if (settingsData.data.visitor_pass_fee !== undefined) {
                document.getElementById("settingVisitorPassFee").value = settingsData.data.visitor_pass_fee;
            } else {
                document.getElementById("settingVisitorPassFee").value = "0"; // Default
            }
            if (settingsData.data.exhibitor_fee !== undefined) {
                document.getElementById("settingExhibitorFee").value = settingsData.data.exhibitor_fee;
            } else {
                document.getElementById("settingExhibitorFee").value = "0"; // Default
            }
            if (settingsData.data.exhibitor_pricing) {
                try {
                    window.exhibitorPricingData = JSON.parse(settingsData.data.exhibitor_pricing);
                } catch(e) {
                    window.exhibitorPricingData = {};
                }
            } else {
                window.exhibitorPricingData = {};
            }
        }

        // Fetch Team Registrations
        // Moved to admin-records.php

        // Fetch Visitor Passes
        // Moved to admin-records.php

        // Fetch Exhibitors
        // Moved to admin-records.php

        // 5. Fetch Orders
        const ordersRes = await fetch("api/index.php/orders/all", {
            headers: { "Authorization": "Bearer " + token }
        });
        const orders = await ordersRes.json();
        renderOrders(orders);

        // Calculate KPI Stats
        const totalSales = orders.reduce((sum, o) => sum + Number(o.total_amount || 0), 0);
        const totalCoinsIssued = orders.reduce((sum, o) => sum + Number(o.nxl_coins_earned || 0), 0);
        const uniqueUsers = [...new Set(orders.map(o => o.user_id))];

        document.getElementById("statTotalSales").textContent = "₹" + totalSales.toLocaleString();
        document.getElementById("statTotalNxl").textContent = totalCoinsIssued + " Coins";
        document.getElementById("statActiveCustomers").textContent = uniqueUsers.length + " Users";
        const activeMerchantsCount = allUsers.filter(u => u.role === "MERCHANT").length;
        document.getElementById("statMerchants").textContent = activeMerchantsCount + (activeMerchantsCount === 1 ? " Merchant" : " Merchants");
        
        // Fetch Media Hub
        loadMediaAdmin();
        
        // Fetch Chatbot FAQs
        loadChatbotFaqs();

        // Fetch Reviews
        loadAdminReviews();

        // Fetch Gallery
        loadGalleryAdmin();

    } catch (err) {
        console.error("Dashboard Load Error:", err);
    } finally {
        showLoading(false);
    }
}

function showLoading(show) {
    document.getElementById("adminGlobalLoading").style.display = show ? "flex" : "none";
}

// Users rendering logic
function renderUsers() {
    const customersContainer = document.getElementById("customersContainer");
    const merchantsContainer = document.getElementById("merchantsContainer");
    const adminsContainer = document.getElementById("adminsContainer");

    const customers = allUsers.filter(u => u.role !== "ADMIN" && u.role !== "MERCHANT");
    const merchants = allUsers.filter(u => u.role === "MERCHANT");
    const admins = allUsers.filter(u => u.role === "ADMIN");

    if (customers.length === 0) {
        customersContainer.innerHTML = `<p style="color: #9aa0b4; text-align: center;">No customer accounts registered yet.</p>`;
    } else {
        customersContainer.innerHTML = customers.map(u => `
            <div style="background: #0b0c10; border: 1px solid rgba(197,168,92,0.1); padding: 15px; border-radius: 12px; display: flex; justify-content: space-between; align-items: center;">
              <div>
                <h4 style="margin: 0 0 4px 0; color: #f5f6fa;">${u.full_name || u.fullName || 'User'} <span style="color: #c5a85c; font-size: 0.8rem;">(ID: ${u.id})</span></h4>
                <div style="font-size: 0.8rem; color: #9aa0b4;">📧 ${u.email}</div>
                <div style="font-size: 0.8rem; color: #9aa0b4; margin-top: 2px;">📞 Phone: ${u.phone_number || u.phoneNumber || "N/A"}</div>
              </div>
              <div style="display: flex; gap: 8px;">
                <button onclick="handleEditUserClick(${u.id})" style="background: rgba(197,168,92,0.1); border: 1px solid #c5a85c; color: #c5a85c; padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; cursor: pointer;">✏️ Edit</button>
                <button onclick="handleDeleteUser(${u.id}, 'USER')" style="background: rgba(220,38,38,0.15); border: 1px solid #dc2626; color: #f87171; padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; cursor: pointer;">🗑️ Delete</button>
              </div>
            </div>
        `).join('');
    }

    if (merchants.length === 0) {
        merchantsContainer.innerHTML = `<p style="color: #9aa0b4; text-align: center;">No merchant accounts registered yet.</p>`;
    } else {
        merchantsContainer.innerHTML = merchants.map(u => `
            <div style="background: #0b0c10; border: 1px solid rgba(197,168,92,0.1); padding: 15px; border-radius: 12px; display: flex; justify-content: space-between; align-items: center;">
              <div>
                <h4 style="margin: 0 0 4px 0; color: #f5f6fa;">${u.full_name || u.fullName || 'Merchant'} <span style="color: #c5a85c; font-size: 0.8rem;">(ID: ${u.id})</span></h4>
                <div style="font-size: 0.8rem; color: #9aa0b4;">📧 ${u.email}</div>
                <div style="font-size: 0.8rem; color: #9aa0b4; margin-top: 2px;">📞 Phone: ${u.phone_number || u.phoneNumber || "N/A"}</div>
              </div>
              <div style="display: flex; gap: 8px;">
                <button onclick="handleEditUserClick(${u.id})" style="background: rgba(197,168,92,0.1); border: 1px solid #c5a85c; color: #c5a85c; padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; cursor: pointer;">✏️ Edit</button>
                <button onclick="handleDeleteUser(${u.id}, 'MERCHANT')" style="background: rgba(220,38,38,0.15); border: 1px solid #dc2626; color: #f87171; padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; cursor: pointer;">🗑️ Delete</button>
              </div>
            </div>
        `).join('');
    }

    if (admins.length === 0) {
        adminsContainer.innerHTML = `<p style="color: #9aa0b4; text-align: center;">No administrative accounts found.</p>`;
    } else {
        adminsContainer.innerHTML = admins.map(u => `
            <div style="background: #0b0c10; border: 1px solid rgba(197,168,92,0.1); padding: 15px; border-radius: 12px; display: flex; justify-content: space-between; align-items: center;">
              <div>
                <h4 style="margin: 0 0 4px 0; color: #f5f6fa;">${u.full_name || u.fullName || 'Admin'} <span style="color: #c5a85c; font-size: 0.8rem;">(ID: ${u.id})</span></h4>
                <div style="font-size: 0.8rem; color: #9aa0b4;">📧 ${u.email}</div>
                <div style="font-size: 0.8rem; color: #9aa0b4; margin-top: 2px;">📞 Phone: ${u.phone_number || u.phoneNumber || "N/A"}</div>
              </div>
              <div style="display: flex; gap: 8px;">
                <button onclick="handleEditUserClick(${u.id})" style="background: rgba(197,168,92,0.1); border: 1px solid #c5a85c; color: #c5a85c; padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; cursor: pointer;">✏️ Edit</button>
                <button onclick="handleDeleteUser(${u.id}, 'ADMIN')" style="background: rgba(220,38,38,0.15); border: 1px solid #dc2626; color: #f87171; padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; cursor: pointer;">🗑️ Delete</button>
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
    document.getElementById("editUserFullName").value = user.fullName;
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
        await loadDashboardData();
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
        await loadDashboardData();
    } catch(err) {
        console.error(err);
        alert("Failed to delete account.");
    } finally {
        showLoading(false);
    }
}

// Tournaments rendering & management
function renderTournaments(tournaments) {
    const container = document.getElementById("tournamentsListContainer");
    if (tournaments.length === 0) {
        container.innerHTML = `<p style="color: #9aa0b4; text-align: center;">No active tournament pools in the database.</p>`;
        return;
    }

    container.innerHTML = tournaments.map(t => `
        <div style="background: #0b0c10; border: 1px solid rgba(197,168,92,0.1); padding: 15px; border-radius: 12px; display: flex; justify-content: space-between; align-items: center;">
          <div>
            <h4 style="margin: 0 0 5px 0; color: #f5f6fa;">${t.name} <span style="color: #c5a85c; font-size: 0.8rem;">(#POOL-${t.id})</span></h4>
            <div style="font-size: 0.8rem; color: #9aa0b4;">
              🏸 Sport: <strong>${t.sport}</strong> • 📍 ${t.venue} • 💰 Fee: ₹${t.registrationFee || t.registration_fee}
            </div>
            <div style="font-size: 0.8rem; color: #9aa0b4; margin-top: 2px;">
              👥 Team slots: ${t.currentTeams || t.current_teams} / ${t.maxTeams || t.max_teams} slots registered
            </div>
          </div>

          <div style="display: flex; gap: 8px;">
            <button onclick="handleEditTournamentClick(${JSON.stringify(t).replace(/"/g, '&quot;')})" style="background: rgba(197,168,92,0.1); border: 1px solid #c5a85c; color: #c5a85c; padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; cursor: pointer;">✏️ Edit</button>
            <button onclick="handleDeleteTournament(${t.id})" style="background: rgba(220,38,38,0.15); border: 1px solid #dc2626; color: #f87171; padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; cursor: pointer;">🗑️ Delete</button>
          </div>
        </div>
    `).join('');
}

function handleEditTournamentClick(t) {
    document.getElementById("eventFormTitle").textContent = "✏️ Edit Active Tournament";
    document.getElementById("btnSaveTournament").textContent = "Save Tournament Updates";
    document.getElementById("btnCancelEditTournament").style.display = "inline-block";

    document.getElementById("editEventId").value = t.id;
    document.getElementById("eventName").value = t.name;
    document.getElementById("eventSport").value = t.sport;
    document.getElementById("eventDate").value = t.date;
    document.getElementById("eventVenue").value = t.venue;
    document.getElementById("eventFee").value = t.registrationFee || t.registration_fee;
    document.getElementById("eventMaxTeams").value = t.maxTeams || t.max_teams;
    document.getElementById("eventCurrTeams").value = t.currentTeams || t.current_teams;

    document.querySelector(".event-form-section").scrollIntoView({ behavior: "smooth" });
}

function resetTournamentForm() {
    document.getElementById("eventFormTitle").textContent = "➕ Create Sports Tournament";
    document.getElementById("btnSaveTournament").textContent = "Publish Tournament Live";
    document.getElementById("btnCancelEditTournament").style.display = "none";

    document.getElementById("editEventId").value = "";
    document.getElementById("tournamentForm").reset();
    document.getElementById("eventMaxTeams").value = "16";
    document.getElementById("eventCurrTeams").value = "0";
}

async function handleSaveTournament(e) {
    e.preventDefault();
    const id = document.getElementById("editEventId").value;
    const token = localStorage.getItem("token");

    const payload = {
        name: document.getElementById("eventName").value,
        sport: document.getElementById("eventSport").value,
        date: document.getElementById("eventDate").value || "TBD",
        venue: document.getElementById("eventVenue").value,
        registrationFee: parseFloat(document.getElementById("eventFee").value),
        maxTeams: parseInt(document.getElementById("eventMaxTeams").value),
        currentTeams: parseInt(document.getElementById("eventCurrTeams").value)
    };

    showLoading(true);
    try {
        let res, url, method;
        if (id) {
            url = `api/index.php/tournaments/${id}`;
            method = "PUT";
        } else {
            url = "api/index.php/tournaments";
            method = "POST";
        }

        res = await fetch(url, {
            method: method,
            headers: {
                "Content-Type": "application/json",
                "Authorization": "Bearer " + token
            },
            body: JSON.stringify(payload)
        });

        alert(id ? "Tournament updated successfully!" : "New Tournament created successfully!");
        resetTournamentForm();
        await loadDashboardData();
    } catch(err) {
        console.error(err);
        alert("Error saving tournament.");
    } finally {
        showLoading(false);
    }
}

async function handleDeleteTournament(id) {
    if (!window.confirm("Are you sure you want to delete this tournament permanently?")) return;

    const token = localStorage.getItem("token");
    showLoading(true);
    try {
        await fetch(`api/index.php/tournaments/${id}`, {
            method: "DELETE",
            headers: { "Authorization": "Bearer " + token }
        });
        alert("Tournament deleted successfully.");
        await loadDashboardData();
    } catch(err) {
        console.error(err);
        alert("Failed to delete tournament.");
    } finally {
        showLoading(false);
    }
}

// Render Orders
function renderOrders(orders) {
    const tbody = document.getElementById("ordersTableBody");
    if (orders.length === 0) {
        tbody.innerHTML = `<tr><td colspan="5" style="text-align: center; padding: 20px; color: #9aa0b4;">No customer orders found in the database.</td></tr>`;
        return;
    }

    tbody.innerHTML = orders.map(o => {
        let itemsList = [];
        try {
            itemsList = o.items_json ? JSON.parse(o.items_json) : [];
        } catch(e) {}
        const itemsSummary = itemsList.length > 0 ? itemsList.map(i => i.name).join(", ") : "Sports Product Order";

        return `
            <tr style="border-bottom: 1px solid rgba(255,255,255,0.03);">
              <td style="padding: 12px 10px; font-weight: bold;">#ORD-${o.id}</td>
              <td style="padding: 12px 10px; color: #9aa0b4;"><strong>${itemsSummary}</strong></td>
              <td style="padding: 12px 10px; color: #22c55e; font-weight: bold;">₹${o.total_amount || o.totalAmount}</td>
              <td style="padding: 12px 10px; color: #c5a85c;">💎 +${o.nxl_coins_earned || 0} / -${o.nxl_coins_used || 0}</td>
              <td style="padding: 12px 10px;">
                <select
                  onchange="handleOrderStatusChange(${o.id}, this.value)"
                  style="background: #0b0c10; color: #c5a85c; border: 1px solid rgba(197,168,92,0.25); padding: 4px 8px; border-radius: 6px; cursor: pointer;"
                >
                  <option value="pending" ${o.order_status === 'pending' ? 'selected' : ''}>Pending</option>
                  <option value="payment_received" ${o.order_status === 'payment_received' ? 'selected' : ''}>Payment Received</option>
                  <option value="confirmed" ${o.order_status === 'confirmed' ? 'selected' : ''}>Confirmed</option>
                  <option value="shipped" ${o.order_status === 'shipped' ? 'selected' : ''}>Shipped</option>
                  <option value="delivered" ${o.order_status === 'delivered' ? 'selected' : ''}>Delivered</option>
                  <option value="cancelled" ${o.order_status === 'cancelled' ? 'selected' : ''}>Cancelled</option>
                </select>
              </td>
            </tr>
        `;
    }).join('');
}

document.getElementById("systemSettingsForm").addEventListener("submit", async function(e) {
    e.preventDefault();
    const btn = e.target.querySelector("button[type='submit']");
    const originalText = btn.innerHTML;
    btn.innerHTML = "Saving...";
    
    const fee = document.getElementById("settingEventFee").value;
    const vFee = document.getElementById("settingVisitorPassFee").value;
    const eFee = document.getElementById("settingExhibitorFee").value;
    
    try {
        const token = localStorage.getItem("token");
        const res = await fetch("api/index.php/settings", {
            method: "PUT",
            headers: {
                "Content-Type": "application/json",
                "Authorization": "Bearer " + token
            },
            body: JSON.stringify({ event_fee: fee, visitor_pass_fee: vFee, exhibitor_fee: eFee })
        });
        
        const data = await res.json();
        if (data.success) {
            alert("Settings updated successfully!");
        } else {
            alert(data.message || "Failed to update settings");
        }
    } catch(err) {
        console.error(err);
        alert("Error updating settings");
    } finally {
        btn.innerHTML = originalText;
    }
});

async function handleOrderStatusChange(orderId, newStatus) {
    const token = localStorage.getItem("token");
    showLoading(true);
    try {
        const res = await fetch(`api/index.php/orders/${orderId}/status`, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json",
                "Authorization": "Bearer " + token
            },
            body: JSON.stringify({ status: newStatus })
        });
        alert(`Order #ORD-${orderId} successfully updated to ${newStatus.toUpperCase()}`);
        await loadDashboardData();
    } catch(err) {
        console.error(err);
        alert("Failed to update status. Server error.");
    } finally {
        showLoading(false);
    }
}


// Render Team Registrations
function renderTeamRegistrations(registrations) {
    const container = document.getElementById("teamRegistrationsListContainer");
    if (!registrations || registrations.length === 0) {
        container.innerHTML = `<p style="color: #9aa0b4; text-align: center;">No team registrations found.</p>`;
        return;
    }

    container.innerHTML = registrations.map(r => `
        <div style="background: #0b0c10; border: 1px solid rgba(197,168,92,0.15); padding: 12px; border-radius: 10px; font-size: 0.85rem;">
          <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
            <strong style="color: #c5a85c;">Team: ${r.team_name}</strong>
            <span style="color: #9aa0b4; font-size: 0.75rem;">${new Date(r.created_at || Date.now()).toLocaleDateString()}</span>
          </div>
          <div style="color: #f5f6fa; font-size: 0.8rem; margin-bottom: 3px;">🏆 Sport: ${r.sport} (${r.team_category}) | Members: ${r.team_members}</div>
          <div style="color: #9aa0b4; font-size: 0.8rem;">👤 Captain: ${r.captain_name} | 📞 ${r.captain_contact} | 📧 ${r.email}</div>
          <div style="color: #9aa0b4; font-size: 0.8rem; margin-top: 3px;">💰 Fee: ₹${r.registration_fee} | Status: <span style="color: ${r.payment_status === 'FREE' || r.payment_status === 'PAID' ? '#22c55e' : '#eab308'}; font-weight: bold;">${r.payment_status}</span></div>
          ${r.notes ? `<div style="color: #9aa0b4; font-size: 0.8rem; margin-top: 5px; font-style: italic;">📝 Notes: ${r.notes}</div>` : ''}
        </div>
    `).join('');
}

// Render Visitor Passes
function renderVisitorPasses(passes) {
    const container = document.getElementById("visitorPassesListContainer");
    if (!passes || passes.length === 0) {
        container.innerHTML = `<p style="color: #9aa0b4; text-align: center;">No visitor passes found.</p>`;
        return;
    }

    container.innerHTML = passes.map(p => `
        <div style="background: #0b0c10; border: 1px solid rgba(197,168,92,0.15); padding: 12px; border-radius: 10px; font-size: 0.85rem;">
          <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
            <strong style="color: #c5a85c;">${p.full_name}</strong>
            <span style="color: #9aa0b4; font-size: 0.75rem;">${new Date(p.created_at || Date.now()).toLocaleDateString()}</span>
          </div>
          <div style="color: #f5f6fa; font-size: 0.8rem; margin-bottom: 3px;">🎫 Event: ${p.event}</div>
          <div style="color: #9aa0b4; font-size: 0.8rem;">📧 ${p.email} | 📞 ${p.phone}</div>
          <div style="color: #9aa0b4; font-size: 0.8rem;">🏢 ${p.company} (${p.designation}) | 📍 ${p.city}, ${p.country}</div>
        </div>
    `).join('');
}

// Render Exhibitors
function renderExhibitors(exhibitors) {
    const container = document.getElementById("exhibitorsListContainer");
    if (!exhibitors || exhibitors.length === 0) {
        container.innerHTML = `<p style="color: #9aa0b4; text-align: center;">No exhibitor registrations found.</p>`;
        return;
    }

    container.innerHTML = exhibitors.map(e => `
        <div style="background: #0b0c10; border: 1px solid rgba(197,168,92,0.15); padding: 12px; border-radius: 10px; font-size: 0.85rem;">
          <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
            <strong style="color: #c5a85c;">${e.company_name}</strong>
            <span style="color: #9aa0b4; font-size: 0.75rem;">${new Date(e.created_at || Date.now()).toLocaleDateString()}</span>
          </div>
          <div style="color: #f5f6fa; font-size: 0.8rem; margin-bottom: 3px;">🎫 Event: ${e.event} | Booth: ${e.booth} (${e.reps} reps)</div>
          ${e.custom_build_details ? `<div style="color: #c5a85c; font-size: 0.8rem; margin-bottom: 3px; font-style: italic;">🛠️ Custom Build: ${e.custom_build_details}</div>` : ''}
          <div style="color: #9aa0b4; font-size: 0.8rem;">👤 Contact: ${e.contact_person} | 📧 ${e.email} | 📞 ${e.phone}</div>
          <div style="color: #9aa0b4; font-size: 0.8rem;">🌍 Industry: ${e.industry} | 📍 ${e.city}, ${e.country}</div>
          ${e.website ? `<div style="margin-top: 5px;"><a href="${e.website}" target="_blank" style="color: #38bdf8; text-decoration: none;">🔗 ${e.website}</a></div>` : ''}
        </div>
    `).join('');
}


// Render Business Inquiries
function renderBusinessInquiries(inquiries) {
    const container = document.getElementById("businessInquiriesListContainer");
    if (!inquiries || inquiries.length === 0) {
        container.innerHTML = `<p style="color: #9aa0b4; text-align: center;">No business inquiries in database.</p>`;
        return;
    }

    container.innerHTML = inquiries.map(b => `
        <div style="background: #0b0c10; border: 1px solid rgba(197,168,92,0.15); padding: 12px; border-radius: 10px; font-size: 0.85rem;">
          <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
            <strong style="color: #c5a85c;">${b.company_name}</strong>
            <span style="color: #9aa0b4; font-size: 0.75rem;">${new Date(b.created_at || Date.now()).toLocaleDateString()}</span>
          </div>
          ${b.company_url ? `<div style="margin-bottom: 5px;"><a href="${b.company_url}" target="_blank" style="color: #38bdf8; text-decoration: none;">🔗 ${b.company_url}</a></div>` : ''}
          <div style="color: #f5f6fa; font-size: 0.8rem; margin-bottom: 3px;">👤 ${b.contact_person}</div>
          <div style="color: #9aa0b4; font-size: 0.8rem; margin-bottom: 5px;">📧 ${b.email} | 📞 ${b.phone_number}</div>
          <p style="margin: 0; color: #c5a85c; line-height: 1.4; border-top: 1px dashed rgba(255,255,255,0.1); padding-top: 5px; margin-top: 5px;"><strong>${b.partnership_type}</strong></p>
          ${b.message ? `<p style="margin: 5px 0 0 0; color: #9aa0b4; line-height: 1.4;">${b.message}</p>` : ''}
        </div>
    `).join('');
}

// Render Subscribers
function renderSubscribers(subscribers) {
    const container = document.getElementById("subscribersListContainer");
    if (subscribers.length === 0) {
        container.innerHTML = `<p style="color: #9aa0b4; text-align: center;">No newsletter subscribers in database.</p>`;
        return;
    }

    container.innerHTML = subscribers.map(s => `
        <div style="background: #0b0c10; border: 1px solid rgba(255,255,255,0.02); padding: 10px 15px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem;">
          <span>${s.email}</span>
          <span style="color: #22c55e; font-weight: bold; font-size: 0.75rem;">active</span>
        </div>
    `).join('');
}

// Add Gallery Photo Logic
const galleryForm = document.getElementById('galleryForm');
const galleryImageFile = document.getElementById('galleryImageFile');
const previewImg = document.getElementById('previewImg');
const galleryImagePreview = document.getElementById('galleryImagePreview');

if (galleryImageFile) {
    galleryImageFile.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Validate file type
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            if (!validTypes.includes(file.type)) {
                alert("Invalid file format. Please upload JPG, JPEG, PNG, or WEBP.");
                this.value = "";
                galleryImagePreview.style.display = "none";
                return;
            }
            // Validate file size (5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert("File size exceeds 5MB limit.");
                this.value = "";
                galleryImagePreview.style.display = "none";
                return;
            }
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                galleryImagePreview.style.display = "block";
            }
            reader.readAsDataURL(file);
        } else {
            galleryImagePreview.style.display = "none";
        }
    });

    const btnClearGalleryFile = document.getElementById('btnClearGalleryFile');
    if (btnClearGalleryFile) {
        btnClearGalleryFile.addEventListener('click', () => {
            galleryImageFile.value = "";
            galleryImagePreview.style.display = "none";
        });
    }
}

if (galleryForm) {
    galleryForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const imageUrl = document.getElementById('galleryImageUrl').value.trim();
        const fileInput = document.getElementById('galleryImageFile');
        const file = fileInput ? fileInput.files[0] : null;
        
        const category = document.getElementById('galleryCategory').value;
        const title = document.getElementById('galleryTitle').value.trim();
        const subtitle = document.getElementById('gallerySubtitle').value.trim();

        if (!imageUrl && !file) {
            alert("Please provide either a Photo URL or Upload an Image.");
            return;
        }
        if (!title) {
            alert("Title is required.");
            return;
        }

        try {
            showLoading(true);
            const token = localStorage.getItem("token");
            const editId = document.getElementById("editGalleryId").value;
            
            let fetchOptions = {
                method: editId ? "POST" : "POST", // POST is used for both with multipart formData, route checks
                headers: {
                    "Authorization": "Bearer " + token
                }
            };

            const apiUrl = editId ? `api/index.php/gallery/${editId}` : "api/index.php/gallery/add";

            if (file) {
                const formData = new FormData();
                formData.append('galleryImage', file);
                if (imageUrl) formData.append('image_url', imageUrl); // Fallback URL if desired, though file overrides
                formData.append('category', category);
                formData.append('title', title);
                formData.append('subtitle', subtitle);
                fetchOptions.body = formData;
            } else {
                fetchOptions.headers["Content-Type"] = "application/json";
                fetchOptions.body = JSON.stringify({ image_url: imageUrl, category, title, subtitle });
            }

            const res = await fetch(apiUrl, fetchOptions);

            const data = await res.json();
            if (data.success) {
                alert(data.message || "Gallery photo saved successfully!");
                cancelGalleryEdit();
                loadGalleryAdmin();
            } else {
                alert(data.message || "Failed to save gallery photo.");
            }
        } catch (error) {
            console.error(error);
            alert("An error occurred while saving the photo.");
        } finally {
            showLoading(false);
        }
    });
}

function cancelGalleryEdit() {
    document.getElementById("editGalleryId").value = "";
    document.getElementById("galleryForm").reset();
    document.getElementById("btnSaveGallery").textContent = "Add to Gallery";
    document.getElementById("btnCancelGalleryEdit").style.display = "none";
    const preview = document.getElementById('galleryImagePreview');
    if (preview) preview.style.display = "none";
}

let allGalleryItems = [];

async function loadGalleryAdmin() {
    try {
        const res = await fetch('api/index.php/gallery/items');
        const data = await res.json();
        
        if (!data.success) throw new Error(data.message);
        
        allGalleryItems = data.data;
        const tbody = document.getElementById('galleryTableBody');
        if (data.data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" style="text-align: center; padding: 20px; color: #9aa0b4;">No gallery photos added yet.</td></tr>';
            return;
        }
        
        tbody.innerHTML = data.data.map(item => {
            return `
            <div style="background: rgba(255,255,255,0.02); border: 1px solid rgba(197,168,92,0.15); border-radius: 12px; padding: 12px; display: flex; gap: 15px; align-items: center;">
                <img src="${item.image_url}" style="width: 70px; height: 50px; object-fit: cover; border-radius: 6px; flex-shrink: 0;">
                <div style="flex: 1; min-width: 0;">
                    <strong style="color: #f5f6fa; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-size: 0.9rem;">${item.title}</strong>
                    <span style="font-size: 0.75rem; color: #9aa0b4; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${item.subtitle}</span>
                </div>
                <div style="display: flex; flex-direction: column; gap: 5px; flex-shrink: 0;">
                    <button type="button" onclick="editGalleryPhoto(${item.id})" style="background: rgba(197,168,92,0.15); border: 1px solid #c5a85c; color: #c5a85c; padding: 4px 10px; border-radius: 4px; cursor: pointer; font-size: 0.75rem;">✏️ Edit</button>
                    <button type="button" onclick="deleteGalleryPhoto(${item.id})" style="background: rgba(220,38,38,0.12); border: 1px solid #dc2626; color: #f87171; padding: 4px 10px; border-radius: 4px; cursor: pointer; font-size: 0.75rem;">🗑️ Remove</button>
                </div>
            </div>
            `;
        }).join('');
    } catch (e) {
        console.error(e);
        document.getElementById('galleryTableBody').innerHTML = '<tr><td colspan="4" style="text-align: center; color: #ef4444;">Failed to load gallery photos.</td></tr>';
    }
}

function editGalleryPhoto(id) {
    const item = allGalleryItems.find(i => i.id == id);
    if (!item) return;

    document.getElementById("editGalleryId").value = item.id;
    document.getElementById("galleryTitle").value = item.title;
    document.getElementById("gallerySubtitle").value = item.subtitle;
    document.getElementById("galleryCategory").value = item.category;
    document.getElementById("galleryImageUrl").value = item.image_url.startsWith('uploads') ? '' : item.image_url;
    
    if (item.image_url) {
        document.getElementById('previewImg').src = item.image_url;
        document.getElementById('galleryImagePreview').style.display = "block";
    }

    document.getElementById("btnSaveGallery").textContent = "💾 Save Changes";
    document.getElementById("btnCancelGalleryEdit").style.display = "block";
    document.getElementById("galleryForm").scrollIntoView({ behavior: 'smooth' });
}

async function deleteGalleryPhoto(id) {
    if (!confirm("Are you sure you want to delete this gallery photo?")) return;

    try {
        const token = localStorage.getItem("token");
        const res = await fetch(`api/index.php/gallery/${id}`, {
            method: "DELETE",
            headers: {
                "Authorization": "Bearer " + token
            }
        });
        const data = await res.json();
        
        if (data.success) {
            alert("Gallery photo deleted!");
            loadGalleryAdmin();
        } else {
            alert(data.message || "Failed to delete.");
        }
    } catch (error) {
        console.error(error);
        alert("An error occurred.");
    }
}

// Sports Categories Functions
let allCategories = [];

async function loadCategoriesAdmin() {
    try {
        const res = await fetch('api/index.php/categories/all');
        const data = await res.json();
        const container = document.getElementById('categoriesListContainer');
        const eventSportDropdown = document.getElementById('eventSport');
        
        if (data.success && data.data && data.data.length > 0) {
            allCategories = data.data;
            container.innerHTML = data.data.map(cat => `
                <div style="background: rgba(255,255,255,0.02); border: 1px solid rgba(197,168,92,0.15); border-radius: 8px; padding: 10px; display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 1.5rem;">${cat.icon}</span>
                        <div>
                            <strong style="color: #c5a85c;">${cat.name} ${cat.is_featured == 1 ? '<span style="font-size:0.7rem; background:#c5a85c; color:#000; padding:2px 5px; border-radius:4px; margin-left:5px;">Featured</span>' : ''}</strong>
                            <p style="margin: 2px 0 0 0; font-size: 0.75rem; color: #9aa0b4;">${cat.description || 'No description'}</p>
                        </div>
                    </div>
                    <div>
                        <button onclick="editCategory(${cat.id})" style="background: rgba(197,168,92,0.15); border: 1px solid #c5a85c; color: #c5a85c; padding: 4px 10px; border-radius: 4px; cursor: pointer; font-size: 0.75rem; margin-right: 5px;">Edit</button>
                        <button onclick="deleteCategory(${cat.id})" style="background: rgba(220,38,38,0.12); border: 1px solid #dc2626; color: #f87171; padding: 4px 10px; border-radius: 4px; cursor: pointer; font-size: 0.75rem;">Delete</button>
                    </div>
                </div>
            `).join('');
            
            if (eventSportDropdown) {
                eventSportDropdown.innerHTML = data.data.map(cat => `<option value="${cat.name}">${cat.name}</option>`).join('');
            }
        } else {
            container.innerHTML = '<p style="color: #9aa0b4; text-align: center;">No categories found.</p>';
            if (eventSportDropdown) eventSportDropdown.innerHTML = '<option value="">No categories available</option>';
        }
    } catch (e) {
        console.error("Failed to load categories:", e);
    }
}

function editCategory(id) {
    const cat = allCategories.find(c => c.id == id);
    if (!cat) return;
    
    document.getElementById('editCategoryId').value = cat.id;
    document.getElementById('catName').value = cat.name;
    document.getElementById('catIcon').value = cat.icon;
    document.getElementById('catDesc').value = cat.description;
    document.getElementById('catFeatured').checked = cat.is_featured == 1;
    
    document.getElementById('btnSaveCategory').textContent = "Save Changes";
    document.getElementById('btnCancelEditCategory').style.display = "inline-block";
    document.getElementById('categoryFormTitle').textContent = "✏️ Edit Category";
}

function cancelEditCategory() {
    document.getElementById('categoryForm').reset();
    document.getElementById('editCategoryId').value = "";
    document.getElementById('btnSaveCategory').textContent = "Add Category";
    document.getElementById('btnCancelEditCategory').style.display = "none";
    document.getElementById('categoryFormTitle').textContent = "🏷️ Manage Sports Categories";
}

document.getElementById('categoryForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const id = document.getElementById('editCategoryId').value;
    const data = {
        name: document.getElementById('catName').value,
        icon: document.getElementById('catIcon').value,
        description: document.getElementById('catDesc').value,
        is_featured: document.getElementById('catFeatured').checked
    };
    
    const url = id ? `api/index.php/categories/${id}` : `api/index.php/categories/create`;
    const method = id ? 'PUT' : 'POST';
    
    try {
        const res = await fetch(url, {
            method: method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const result = await res.json();
        if (result.success) {
            cancelEditCategory();
            loadCategoriesAdmin();
        } else {
            alert(result.message || "Failed to save category");
        }
    } catch (e) {
        console.error(e);
        alert("An error occurred");
    }
});

async function deleteCategory(id) {
    if (!confirm("Are you sure you want to delete this category?")) return;
    try {
        const res = await fetch(`api/index.php/categories/${id}`, { method: 'DELETE' });
        const result = await res.json();
        if (result.success) {
            loadCategoriesAdmin();
        } else {
            alert(result.message || "Failed to delete");
        }
    } catch (e) {
        console.error(e);
        alert("An error occurred");
    }
}

document.getElementById('btnCancelEditCategory').addEventListener('click', cancelEditCategory);

// Media Hub Functions
let allMediaItems = [];

async function loadMediaAdmin() {
    try {
        const res = await fetch('api/index.php/media/admin/items');
        const data = await res.json();
        
        if (!data.success) throw new Error(data.message);
        
        allMediaItems = data.data;
        
        const tbody = document.getElementById('mediaTableBody');
        if (data.data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 20px; color: #9aa0b4;">No media added yet.</td></tr>';
            return;
        }
        
        tbody.innerHTML = data.data.map(item => {
            const visibilityStr = item.visibility == 1 ? '<span style="color: #22c55e;">Visible</span>' : '<span style="color: #ef4444;">Hidden</span>';
            const toggleAction = item.visibility == 1 ? 0 : 1;
            const toggleLabel = item.visibility == 1 ? 'Hide' : 'Show';
            
            return `
            <tr style="border-bottom: 1px solid rgba(197,168,92,0.15);">
                <td style="padding: 10px;">
                    <img src="${item.thumbnail_url || 'assets/images/placeholder.jpg'}" style="width: 80px; height: 50px; object-fit: cover; border-radius: 4px;">
                </td>
                <td style="padding: 10px;">
                    <strong style="color: #f5f6fa;">${item.title}</strong><br>
                    <span style="font-size: 0.75rem; color: #9aa0b4;">${item.date_time || ''}</span>
                </td>
                <td style="padding: 10px; font-size: 0.75rem; color: #9aa0b4;">
                    ${item.category}<br>
                    ${item.tournament_name || ''}
                </td>
                <td style="padding: 10px;">${visibilityStr}</td>
                <td style="padding: 10px;">
                    <button onclick="editMedia(${item.id})" style="background: rgba(197,168,92,0.15); border: 1px solid #c5a85c; color: #c5a85c; padding: 4px 8px; border-radius: 4px; cursor: pointer; font-size: 0.75rem; margin-right: 5px;">Edit</button>
                    <button onclick="toggleMediaVisibility(${item.id}, ${toggleAction})" style="background: #1e293b; border: 1px solid rgba(197,168,92,0.3); color: #fff; padding: 4px 8px; border-radius: 4px; cursor: pointer; font-size: 0.75rem; margin-right: 5px;">${toggleLabel}</button>
                    <button onclick="deleteMedia(${item.id})" style="background: rgba(220,38,38,0.12); border: 1px solid #dc2626; color: #f87171; padding: 4px 8px; border-radius: 4px; cursor: pointer; font-size: 0.75rem;">Delete</button>
                </td>
            </tr>
            `;
        }).join('');
    } catch (e) {
        console.error(e);
        document.getElementById('mediaTableBody').innerHTML = '<tr><td colspan="5" style="text-align: center; color: #ef4444;">Failed to load media</td></tr>';
    }
}

async function handleAddMedia(e) {
    e.preventDefault();
    showLoading(true);
    
    const formData = new FormData();
    formData.append('title', document.getElementById('mediaTitle').value.trim());
    formData.append('category', document.getElementById('mediaCategory').value);
    formData.append('video_link', document.getElementById('mediaLink').value.trim());
    formData.append('tournament_name', document.getElementById('mediaTournament').value.trim());
    formData.append('stadium', document.getElementById('mediaStadium').value.trim());
    formData.append('duration', document.getElementById('mediaDuration').value.trim());
    formData.append('views', document.getElementById('mediaViews').value.trim());
    formData.append('status', document.getElementById('mediaStatus').value);
    formData.append('date_time', document.getElementById('mediaDateTime').value.trim());
    formData.append('short_description', document.getElementById('mediaDesc').value.trim());
    
    const fileInput = document.getElementById('mediaThumbnail');
    if (fileInput.files.length > 0) {
        formData.append('mediaThumbnail', fileInput.files[0]);
    }

    const editId = document.getElementById('editMediaId').value;
    const url = editId ? `api/index.php/media/${editId}` : 'api/index.php/media/add';

    try {
        const token = localStorage.getItem('token');
        const res = await fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'Authorization': 'Bearer ' + token
            }
        });
        const data = await res.json();
        
        if (data.success) {
            alert(editId ? 'Media updated successfully!' : 'Media added successfully!');
            cancelEditMedia();
            loadMediaAdmin();
        } else {
            alert('Error: ' + data.message);
        }
    } catch (e) {
        console.error(e);
        alert('An error occurred while adding media.');
    } finally {
        showLoading(false);
    }
}

async function toggleMediaVisibility(id, visibility) {
    try {
        const token = localStorage.getItem('token');
        const res = await fetch(`api/index.php/media/${id}/visibility`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + token
            },
            body: JSON.stringify({ visibility })
        });
        const data = await res.json();
        if (data.success) {
            loadMediaAdmin();
        } else {
            alert('Error: ' + data.message);
        }
    } catch(e) {
        console.error(e);
        alert('Failed to toggle visibility');
    }
}

function editMedia(id) {
    const item = allMediaItems.find(i => i.id == id);
    if (!item) return;

    document.getElementById('editMediaId').value = item.id;
    document.getElementById('mediaTitle').value = item.title;
    document.getElementById('mediaCategory').value = item.category;
    document.getElementById('mediaLink').value = item.video_link;
    document.getElementById('mediaTournament').value = item.tournament_name || '';
    document.getElementById('mediaStadium').value = item.stadium || '';
    document.getElementById('mediaDuration').value = item.duration || '';
    document.getElementById('mediaViews').value = item.views || '';
    document.getElementById('mediaStatus').value = item.status || 'Published';
    document.getElementById('mediaDateTime').value = item.date_time || '';
    document.getElementById('mediaDesc').value = item.short_description || '';
    
    // Thumbnail cannot be preset in file input, so make it not required
    document.getElementById('mediaThumbnail').required = false;

    document.getElementById('btnSaveMedia').textContent = "💾 Save Changes";
    document.getElementById('btnCancelEditMedia').style.display = "inline-block";
    document.getElementById('mediaHubForm').scrollIntoView({ behavior: 'smooth' });
}

function cancelEditMedia() {
    document.getElementById('editMediaId').value = "";
    document.getElementById('mediaHubForm').reset();
    document.getElementById('mediaThumbnail').required = true;
    document.getElementById('btnSaveMedia').textContent = "➕ Add to Media Hub";
    document.getElementById('btnCancelEditMedia').style.display = "none";
}

async function deleteMedia(id) {
    if (!confirm('Are you sure you want to delete this video?')) return;
    
    try {
        const token = localStorage.getItem('token');
        const res = await fetch(`api/index.php/media/${id}`, {
            method: 'DELETE',
            headers: {
                'Authorization': 'Bearer ' + token
            }
        });
        const data = await res.json();
        if (data.success) {
            loadMediaAdmin();
        } else {
            alert('Error: ' + data.message);
        }
    } catch(e) {
        console.error(e);
        alert('Failed to delete media');
    }
}

// ==========================
// CHATBOT FAQS LOGIC
// ==========================
async function loadChatbotFaqs() {
    try {
        const res = await fetch("api/index.php/chatbot-faqs");
        const data = await res.json();
        const container = document.getElementById("chatbotListContainer");
        
        if (!data.success || !data.data || data.data.length === 0) {
            container.innerHTML = "<p>No questions found.</p>";
            return;
        }
        
        let html = `
        <table class="data-table" style="width: 100%; text-align: left; border-collapse: collapse;">
          <thead>
            <tr>
              <th style="padding: 10px; border-bottom: 1px solid rgba(197, 168, 92, 0.2); width: 5%;">ID</th>
              <th style="padding: 10px; border-bottom: 1px solid rgba(197, 168, 92, 0.2); width: 35%;">Question</th>
              <th style="padding: 10px; border-bottom: 1px solid rgba(197, 168, 92, 0.2); width: 40%;">Answer</th>
              <th style="padding: 10px; border-bottom: 1px solid rgba(197, 168, 92, 0.2); width: 20%; text-align: center;">Action</th>
            </tr>
          </thead>
          <tbody>
        `;
        
        data.data.forEach(q => {
            html += `
            <tr>
              <td style="padding: 10px; border-bottom: 1px solid rgba(255,255,255,0.05);">#${q.id}</td>
              <td style="padding: 10px; border-bottom: 1px solid rgba(255,255,255,0.05); color: #c5a85c;">${q.question}</td>
              <td style="padding: 10px; border-bottom: 1px solid rgba(255,255,255,0.05); color: #9aa0b4;">${q.answer.length > 40 ? q.answer.substring(0, 40) + '...' : q.answer}</td>
              <td style="padding: 10px; border-bottom: 1px solid rgba(255,255,255,0.05); text-align: center; white-space: nowrap;">
                <button class="status-btn active" style="background: rgba(197,168,92,0.2); color: #c5a85c; border: 1px solid rgba(197,168,92,0.3); margin-right: 5px;" onclick='editChatbotFaq(${q.id}, ${JSON.stringify(q.question)}, ${JSON.stringify(q.answer)})'>Edit</button>
                <button class="status-btn active" style="background: rgba(220,38,38,0.2); color: #ef4444; border: 1px solid rgba(220,38,38,0.3);" onclick="deleteChatbotFaq(${q.id})">Delete</button>
              </td>
            </tr>
            `;
        });
        
        html += `</tbody></table>`;
        container.innerHTML = html;
        
    } catch(e) {
        console.error("Error loading chatbot faqs:", e);
    }
}

async function deleteChatbotFaq(id) {
    if (!confirm("Delete this FAQ?")) return;
    try {
        const res = await fetch(`api/index.php/chatbot-faqs/${id}`, { method: "DELETE" });
        const data = await res.json();
        if (data.success) {
            loadChatbotFaqs();
        } else {
            alert(data.message);
        }
    } catch(e) {
        console.error(e);
    }
}

function editChatbotFaq(id, question, answer) {
    document.getElementById("chatbotEditId").value = id;
    document.getElementById("chatbotQuestion").value = question;
    document.getElementById("chatbotAnswer").value = answer;
    document.getElementById("chatbotSubmitBtn").textContent = "Update FAQ";
    document.getElementById("chatbotCancelBtn").style.display = "inline-block";
    
    // Scroll to form
    document.getElementById("chatbotForm").scrollIntoView({ behavior: 'smooth' });
}

function cancelEditChatbotFaq() {
    document.getElementById("chatbotForm").reset();
    document.getElementById("chatbotEditId").value = "";
    document.getElementById("chatbotSubmitBtn").textContent = "Add FAQ";
    document.getElementById("chatbotCancelBtn").style.display = "none";
}

document.addEventListener("DOMContentLoaded", function() {
    // Other setup logic ...
    
    // Chatbot Form Submit
    const chatbotForm = document.getElementById("chatbotForm");
    if (chatbotForm) {
        chatbotForm.addEventListener("submit", async function(e) {
            e.preventDefault();
            const editId = document.getElementById("chatbotEditId").value;
            const payload = {
                question: document.getElementById("chatbotQuestion").value,
                answer: document.getElementById("chatbotAnswer").value
            };
            
            showLoading(true);
            try {
                let url = "api/index.php/chatbot-faqs";
                let method = "POST";
                
                if (editId) {
                    url = `api/index.php/chatbot-faqs/${editId}`;
                    method = "PUT";
                }
                
                const res = await fetch(url, {
                    method: method,
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (data.success) {
                    cancelEditChatbotFaq();
                    loadChatbotFaqs();
                } else {
                    alert(data.message);
                }
            } catch(e) {
                console.error(e);
            } finally {
                showLoading(false);
            }
        });
    }
});

// ==========================
// PRODUCTS LOGIC
// ==========================
let allProducts = [];

async function loadProductsAdmin() {
    try {
        const res = await fetch("api/index.php/products");
        const data = await res.json();
        const tbody = document.getElementById('productsTableBody');
        
        const items = Array.isArray(data) ? data : (data.data || []);
        allProducts = items;
        
        if (items.length === 0) {
            tbody.innerHTML = '<p style="text-align: center; color: #9aa0b4;">No products added yet.</p>';
            return;
        }
        
        tbody.innerHTML = items.map(item => `
            <div style="background: rgba(255,255,255,0.02); border: 1px solid rgba(197,168,92,0.15); border-radius: 12px; padding: 12px; display: flex; gap: 15px; align-items: center;">
                <img src="${item.image_url || 'https://images.unsplash.com/photo-1517649763962-0c623066013b?w=400&h=400&fit=crop'}" style="width: 70px; height: 50px; object-fit: cover; border-radius: 6px; flex-shrink: 0;" onerror="this.src='https://images.unsplash.com/photo-1517649763962-0c623066013b?w=400&h=400&fit=crop'">
                <div style="flex: 1; min-width: 0;">
                    <strong style="color: #f5f6fa; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-size: 0.9rem;">${item.name}</strong>
                    <span style="font-size: 0.75rem; color: #22c55e; display: block;">₹${item.price} | Stock: ${item.stock}</span>
                </div>
                <div style="display: flex; flex-direction: column; gap: 5px; flex-shrink: 0;">
                    <button type="button" onclick="editProduct(${item.id})" style="background: rgba(197,168,92,0.15); border: 1px solid #c5a85c; color: #c5a85c; padding: 4px 10px; border-radius: 4px; cursor: pointer; font-size: 0.75rem;">✏️ Edit</button>
                    <button type="button" onclick="deleteProduct(${item.id})" style="background: rgba(220,38,38,0.12); border: 1px solid #dc2626; color: #f87171; padding: 4px 10px; border-radius: 4px; cursor: pointer; font-size: 0.75rem;">🗑️ Remove</button>
                </div>
            </div>
        `).join('');
    } catch (e) {
        console.error(e);
        document.getElementById('productsTableBody').innerHTML = '<p style="text-align: center; color: #ef4444;">Failed to load products.</p>';
    }
}

function editProduct(id) {
    const item = allProducts.find(i => i.id == id);
    if (!item) return;

    document.getElementById("editProductId").value = item.id;
    document.getElementById("productName").value = item.name;
    document.getElementById("productCategory").value = item.category;
    document.getElementById("productPrice").value = item.price;
    document.getElementById("productStock").value = item.stock;
    document.getElementById("productDescription").value = item.description || "";
    document.getElementById("productColors").value = item.colors || "";
    document.getElementById("productImageUrl").value = item.image_url && item.image_url.startsWith('uploads') ? '' : (item.image_url || '');
    
    const preview = document.getElementById('productImagePreview');
    const previewImg = document.getElementById('prodPreviewImg');
    if (item.image_url) {
        previewImg.src = item.image_url;
        preview.style.display = "block";
    } else {
        preview.style.display = "none";
    }

    document.getElementById("btnSaveProduct").textContent = "💾 Save Changes";
    document.getElementById("btnCancelProductEdit").style.display = "block";
    document.getElementById("productForm").scrollIntoView({ behavior: 'smooth' });
}

function cancelProductEdit() {
    document.getElementById("editProductId").value = "";
    document.getElementById("productForm").reset();
    document.getElementById("btnSaveProduct").textContent = "Add Product";
    document.getElementById("btnCancelProductEdit").style.display = "none";
    document.getElementById("productImagePreview").style.display = "none";
}

async function deleteProduct(id) {
    if (!confirm("Are you sure you want to delete this product?")) return;

    try {
        const token = localStorage.getItem("token");
        const res = await fetch(`api/index.php/products/${id}`, {
            method: "DELETE",
            headers: {
                "Authorization": "Bearer " + token
            }
        });
        
        if (res.ok) {
            alert("Product deleted successfully!");
            loadProductsAdmin();
        } else {
            alert("Failed to delete product.");
        }
    } catch (error) {
        console.error(error);
        alert("An error occurred.");
    }
}

document.addEventListener("DOMContentLoaded", function() {
    loadCategoriesAdmin();
    loadProductsAdmin();

    const productImageFile = document.getElementById('productImageFile');
    const prodPreviewImg = document.getElementById('prodPreviewImg');
    const productImagePreview = document.getElementById('productImagePreview');
    const btnClearProductFile = document.getElementById('btnClearProductFile');

    if (productImageFile) {
        productImageFile.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                if (!validTypes.includes(file.type)) {
                    alert("Invalid file format. Please upload JPG, JPEG, PNG, or WEBP.");
                    this.value = "";
                    productImagePreview.style.display = "none";
                    return;
                }
                const reader = new FileReader();
                reader.onload = function(e) {
                    prodPreviewImg.src = e.target.result;
                    productImagePreview.style.display = "block";
                }
                reader.readAsDataURL(file);
            } else {
                productImagePreview.style.display = "none";
            }
        });
    }

    if (btnClearProductFile) {
        btnClearProductFile.addEventListener('click', () => {
            productImageFile.value = "";
            productImagePreview.style.display = "none";
        });
    }

    const productForm = document.getElementById('productForm');
    if (productForm) {
        productForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const editId = document.getElementById('editProductId').value;
            const name = document.getElementById('productName').value.trim();
            const category = document.getElementById('productCategory').value;
            const price = document.getElementById('productPrice').value;
            const stock = document.getElementById('productStock').value;
            const description = document.getElementById('productDescription').value.trim();
            const colors = document.getElementById('productColors').value.trim();
            const imageUrl = document.getElementById('productImageUrl').value.trim();
            
            const fileInput = document.getElementById('productImageFile');
            const file = fileInput ? fileInput.files[0] : null;

            try {
                // Using a globally defined function if available or just default logic
                if (typeof showLoading === 'function') showLoading(true);
                const token = localStorage.getItem("token");
                const apiUrl = editId ? `api/index.php/products/${editId}` : "api/index.php/products";
                
                let fetchOptions = {
                    method: "POST", // POST is handled as create/update in API index
                    headers: {
                        "Authorization": "Bearer " + token
                    }
                };

                if (file) {
                    const formData = new FormData();
                    formData.append('productImage', file);
                    formData.append('name', name);
                    formData.append('category', category);
                    formData.append('price', price);
                    formData.append('stock', stock);
                    formData.append('description', description);
                    if (colors) formData.append('colors', colors);
                    if (imageUrl) formData.append('image_url', imageUrl);
                    fetchOptions.body = formData;
                } else {
                    fetchOptions.headers["Content-Type"] = "application/json";
                    fetchOptions.body = JSON.stringify({ 
                        name, category, price, stock, description, image_url: imageUrl, colors 
                    });
                }

                const res = await fetch(apiUrl, fetchOptions);
                
                if (res.ok) {
                    alert("Product saved successfully!");
                    cancelProductEdit();
                    loadProductsAdmin();
                } else {
                    alert("Failed to save product.");
                }
            } catch (error) {
                console.error(error);
                alert("An error occurred while saving the product.");
            } finally {
                if (typeof showLoading === 'function') showLoading(false);
            }
        });
    }
});

// ----------------------------------------
// MANAGE REVIEWS LOGIC
// ----------------------------------------
async function loadAdminReviews() {
    try {
        const res = await fetch("api/index.php/reviews");
        const data = await res.json();
        const container = document.getElementById("reviewsAdminContainer");

        if (Array.isArray(data) && data.length > 0) {
            container.innerHTML = data.map(r => `
                <div style="background: #0b0c10; border: 1px solid rgba(197,168,92,0.15); padding: 15px; border-radius: 12px; display: flex; justify-content: space-between; align-items: flex-start; gap: 15px;">
                    <div style="flex: 1;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                            <strong style="color: #f5f6fa;">${r.author} <span style="color: #c5a85c; font-size: 0.8em;">(${r.role})</span></strong>
                            <span style="color: #c5a85c;">${'⭐'.repeat(r.rating)}</span>
                        </div>
                        <p style="color: #9aa0b4; margin: 0 0 10px 0; font-size: 0.9rem; font-style: italic;">"${r.comment}"</p>
                        <small style="color: #555;">${new Date(r.created_at).toLocaleDateString()}</small>
                    </div>
                    <button onclick="deleteReview(${r.id})" style="background: rgba(220,38,38,0.15); border: 1px solid #dc2626; color: #f87171; padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; cursor: pointer; white-space: nowrap;">🗑️ Delete</button>
                </div>
            `).join('');
        } else {
            container.innerHTML = '<p style="color: #9aa0b4; text-align: center;">No reviews found.</p>';
        }
    } catch (err) {
        console.error("Error loading admin reviews:", err);
    }
}

async function deleteReview(id) {
    if (!confirm("Are you sure you want to delete this review permanently?")) return;
    
    try {
        if (typeof showLoading === 'function') showLoading(true);
        const res = await fetch(`api/index.php/reviews/${id}`, {
            method: "DELETE"
        });
        const data = await res.json();
        if (typeof showLoading === 'function') showLoading(false);
        
        if (data.success) {
            loadAdminReviews();
        } else {
            alert("Error: " + data.message);
        }
    } catch (err) {
        if (typeof showLoading === 'function') showLoading(false);
        console.error(err);
        alert("Failed to delete review.");
    }
}

// ----------------------------------------
// MANAGE SPONSORSHIP OPPORTUNITIES LOGIC
// ----------------------------------------

let sponsorData = {}; // key => { name, url, website }

async function loadSponsors() {
    try {
        const res = await fetch("api/index.php/settings");
        const response = await res.json();
        sponsorData = {};
        if (response && response.success && response.data) {
            const s = response.data;
            for (let i = 1; i <= 9; i++) {
                const key = 'sponsor_' + i;
                if (s[key]) {
                    sponsorData[key] = {
                        url: s[key],
                        name: s[key + '_name'] || 'Sponsor ' + i,
                        website: s[key + '_website'] || ''
                    };
                }
            }
        }
    } catch (err) {
        console.error("Error loading sponsors:", err);
    }
    renderSponsorCards();
}

function renderSponsorCards() {
    const container = document.getElementById('sponsorCardsContainer');
    if (!container) return;

    const keys = Object.keys(sponsorData);
    if (keys.length === 0) {
        container.innerHTML = '<p style="text-align: center; color: #9aa0b4;">No sponsors added yet.</p>';
        return;
    }

    container.innerHTML = keys.map(key => {
        const sp = sponsorData[key];
        const slot = key.replace('sponsor_', 'Sponsor ');
        return `
        <div style="display: flex; align-items: center; gap: 12px; background: #0b0c10; border: 1px solid rgba(197,168,92,0.15); border-radius: 10px; padding: 10px 14px;">
            <img src="${sp.url}" alt="${sp.name}" style="width: 80px; height: 50px; object-fit: contain; background: #fff; border-radius: 6px; padding: 4px; flex-shrink: 0;" onerror="this.style.display='none'"/>
            <div style="flex: 1; min-width: 0;">
                <div style="color: #f5f6fa; font-weight: 600; font-size: 0.9rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${sp.name}</div>
                <div style="color: #c5a85c; font-size: 0.75rem;">${slot}</div>
                ${sp.website ? `<div style="color: #9aa0b4; font-size: 0.72rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${sp.website}</div>` : ''}
            </div>
            <div style="display: flex; gap: 6px; flex-shrink: 0;">
                <button onclick="editSponsor('${key}')" style="background: rgba(197,168,92,0.15); border: 1px solid rgba(197,168,92,0.3); color: #c5a85c; padding: 5px 10px; border-radius: 6px; cursor: pointer; font-size: 0.78rem;">✏️ Edit</button>
                <button onclick="deleteSponsor('${key}')" style="background: rgba(220,38,38,0.1); border: 1px solid rgba(220,38,38,0.3); color: #f87171; padding: 5px 10px; border-radius: 6px; cursor: pointer; font-size: 0.78rem;">🗑️</button>
            </div>
        </div>`;
    }).join('');
}

function editSponsor(key) {
    const sp = sponsorData[key];
    if (!sp) return;
    document.getElementById('editSponsorKey').value = key;
    document.getElementById('sponsorLogoUrl').value = sp.url || '';
    document.getElementById('sponsorName').value = sp.name || '';
    document.getElementById('sponsorWebsite').value = sp.website || '';
    document.getElementById('sponsorSlot').value = key;
    document.getElementById('btnSaveSponsor').textContent = 'Update Sponsor';
    document.getElementById('btnCancelSponsorEdit').style.display = 'block';
    document.getElementById('sponsorForm').scrollIntoView({ behavior: 'smooth' });
}

function cancelSponsorEdit() {
    document.getElementById('editSponsorKey').value = '';
    document.getElementById('sponsorLogoUrl').value = '';
    document.getElementById('sponsorLogoFile').value = '';
    document.getElementById('sponsorName').value = '';
    document.getElementById('sponsorWebsite').value = '';
    document.getElementById('sponsorLogoPreview').style.display = 'none';
    document.getElementById('btnSaveSponsor').textContent = 'Save Sponsor';
    document.getElementById('btnCancelSponsorEdit').style.display = 'none';
}

async function deleteSponsor(key) {
    if (!confirm('Remove this sponsor?')) return;
    const formData = new FormData();
    formData.append(key, '');
    try {
        const res = await fetch("api/index.php/settings/sponsors", { method: "POST", body: formData });
        const data = await res.json();
        if (data.success) {
            delete sponsorData[key];
            renderSponsorCards();
        } else {
            alert("Failed to remove sponsor: " + data.message);
        }
    } catch (err) {
        alert("Network error removing sponsor.");
    }
}

document.addEventListener("DOMContentLoaded", () => {
    loadSponsors();

    // File preview for sponsor logo
    const sponsorFile = document.getElementById('sponsorLogoFile');
    if (sponsorFile) {
        sponsorFile.addEventListener('change', () => {
            const file = sponsorFile.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = e => {
                    document.getElementById('sponsorPreviewImg').src = e.target.result;
                    document.getElementById('sponsorLogoPreview').style.display = 'block';
                    document.getElementById('sponsorLogoUrl').value = '';
                };
                reader.readAsDataURL(file);
            }
        });
    }

    const sf = document.getElementById("sponsorForm");
    if (sf) {
        sf.addEventListener("submit", async (e) => {
            e.preventDefault();
            const key = document.getElementById('editSponsorKey').value || document.getElementById('sponsorSlot').value;
            const logoUrl = document.getElementById('sponsorLogoUrl').value.trim();
            const logoFile = document.getElementById('sponsorLogoFile').files[0];
            const name = document.getElementById('sponsorName').value.trim();
            const website = document.getElementById('sponsorWebsite').value.trim();

            if (!name) { alert("Please enter the sponsor name."); return; }
            if (!logoUrl && !logoFile) { alert("Please provide a logo URL or upload a file."); return; }

            const btn = document.getElementById('btnSaveSponsor');
            btn.disabled = true;
            btn.textContent = 'Saving...';

            const formData = new FormData();
            if (logoFile) {
                formData.append(key, logoFile);
            } else {
                formData.append(key, logoUrl);
            }
            formData.append(key + '_name', name);
            formData.append(key + '_website', website);

            try {
                const res = await fetch("api/index.php/settings/sponsors", { method: "POST", body: formData });
                const data = await res.json();
                if (data.success) {
                    alert("✅ Sponsor saved successfully!");
                    cancelSponsorEdit();
                    loadSponsors();
                } else {
                    alert("❌ Error: " + data.message);
                }
            } catch (err) {
                alert("Network error saving sponsor.");
            }
            btn.disabled = false;
            btn.textContent = 'Save Sponsor';
        });
    }
});



// ─── ADMIN GIFT CARD FUNCTIONS ────────────────────────────────────────────
function adminGcTab(tab) {
    ['list','orders','redemptions'].forEach(t => {
        document.getElementById(`gcPanel${t.charAt(0).toUpperCase()+t.slice(1)}`).style.display = (t === tab) ? 'block' : 'none';
        const btn = document.getElementById(`gcTab${t.charAt(0).toUpperCase()+t.slice(1)}`);
        if(btn) {
            btn.style.background = (t === tab) ? '#c5a85c' : 'transparent';
            btn.style.color = (t === tab) ? '#000' : '#c5a85c';
            btn.style.fontWeight = (t === tab) ? 'bold' : 'normal';
        }
    });
}

async function loadAdminGiftCards() {
    // Load active gift cards
    try {
        const res = await fetch('api/index.php/giftcards');
        const data = await res.json();
        const listEl = document.getElementById('gcCardsList');
        if(data.success && data.data.length > 0) {
            listEl.innerHTML = data.data.map(card => `
                <div style="background: rgba(197,168,92,0.08); border:1px solid rgba(197,168,92,0.3); border-radius:10px; padding:15px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
                    <div>
                        <div style="font-weight:bold; color:#c5a85c;">🎁 ${card.name}</div>
                        <div style="font-size:0.85rem; color:#9aa0b4;">₹${card.price} &bull; ${card.validity_days} days validity</div>
                    </div>
                    <div style="display:flex; gap:8px;">
                        <span style="padding:4px 10px; border-radius:20px; font-size:0.75rem; background:rgba(16,185,129,0.15); color:#10b981; border:1px solid #10b981;">${card.status.toUpperCase()}</span>
                        <button onclick="toggleGiftCardStatus(${card.id}, '${card.status}')" style="padding:5px 12px; border-radius:6px; border:1px solid rgba(197,168,92,0.4); background:transparent; color:#c5a85c; cursor:pointer; font-size:0.8rem;">Toggle Status</button>
                    </div>
                </div>
            `).join('');
        } else {
            listEl.innerHTML = '<p style="color:#9aa0b4; text-align:center;">No gift cards found.</p>';
        }
    } catch(e) {
        document.getElementById('gcCardsList').innerHTML = '<p style="color:#ef4444;">Error loading gift cards.</p>';
    }

    // Load orders
    try {
        const res2 = await fetch('api/index.php/giftcards/user?email=all');
        const data2 = await res2.json();
        const ordersEl = document.getElementById('gcOrdersList');
        // Use direct DB query via PHP — let's use a special endpoint
    } catch(e) {}

    // Load from direct DB query via PHP
    loadAdminGiftCardOrders();
}

async function loadAdminGiftCardOrders() {
    try {
        const res = await fetch('api/index.php/giftcards?admin=orders');
        // Fallback: show message
        document.getElementById('gcOrdersList').innerHTML = `
            <p style="color:#9aa0b4; text-align:center;">
                To view all orders, <a href="gift-cards.php" style="color:#c5a85c;">visit Gift Cards page</a>.
            </p>
            <div id="gcOrdersDirectTable">Loading...</div>`;
        
        const r = await fetch('api/index.php/giftcards/admin-orders');
        const d = await r.json();
        if(d.success && d.data) {
            document.getElementById('gcOrdersDirectTable').innerHTML = d.data.map(o => `
                <div style="background:rgba(197,168,92,0.06); border:1px solid rgba(197,168,92,0.25); border-radius:8px; padding:12px; margin-top:8px;">
                    <div style="font-family:monospace; color:#c5a85c; font-size:0.85rem;">${o.gift_code}</div>
                    <div style="font-size:0.85rem; margin-top:4px;">To: ${o.recipient_name} (${o.recipient_email})</div>
                    <div style="font-size:0.85rem;">Amount: ₹${o.amount} &bull; Status: ${o.redeem_status}</div>
                </div>`).join('');
        }
    } catch(e) {
        document.getElementById('gcOrdersList').innerHTML = '<p style="color:#9aa0b4; text-align:center;">Gift card orders shown on purchase.</p>';
    }
    document.getElementById('gcRedemptionsList').innerHTML = '<p style="color:#9aa0b4; text-align:center;">Redemptions are logged when users redeem their codes.</p>';
}

async function toggleGiftCardStatus(id, currentStatus) {
    const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
    if(!confirm(`Set gift card status to ${newStatus}?`)) return;
    try {
        const res = await fetch('api/index.php/giftcards/toggle-status', {
            method: 'POST',
            headers: {'Content-Type':'application/json'},
            body: JSON.stringify({id, status: newStatus})
        });
        const data = await res.json();
        if(data.success) {
            loadAdminGiftCards();
        } else {
            alert(data.message || 'Failed to update status.');
        }
    } catch(e) {
        alert('Network error.');
    }
}
// ─────────────────────────────────────────────────────────────────────────

