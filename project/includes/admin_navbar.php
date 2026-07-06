<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<style>
    /* Reset main admin container to accommodate the sidebar */
    .admin-dashboard {
        padding-left: calc(250px + 5%) !important;
    }
    
    /* Shift the global footer content so it is not hidden behind the fixed sidebar */
    .footer-premium {
        padding-left: 250px !important;
    }

    .admin-sidebar {
        position: fixed;
        top: 80px; /* Assuming header height */
        left: 0;
        width: 250px;
        height: calc(100vh - 80px);
        background: #12131c;
        border-right: 1px solid rgba(197, 168, 92, 0.15);
        overflow-y: auto;
        z-index: 1000;
        padding: 10px 0 20px 0;
        box-shadow: 4px 0 20px rgba(0,0,0,0.3);
        transition: transform 0.3s ease;
    }
    
    .admin-sidebar.closed {
        transform: translateX(-250px);
    }

    /* Scrollbar styling for sidebar */
    .admin-sidebar::-webkit-scrollbar {
        width: 6px;
    }
    .admin-sidebar::-webkit-scrollbar-track {
        background: rgba(0,0,0,0.1);
    }
    .admin-sidebar::-webkit-scrollbar-thumb {
        background: rgba(197, 168, 92, 0.3);
        border-radius: 10px;
    }

    .admin-sidebar-search {
        padding: 0 20px 20px 20px;
    }
    .admin-sidebar-search input {
        width: 100%;
        padding: 10px 15px;
        border-radius: 6px;
        border: 1px solid rgba(197, 168, 92, 0.3);
        background: rgba(11,12,16,0.6);
        color: #f5f6fa;
        font-size: 0.85rem;
        transition: all 0.3s ease;
    }
    .admin-sidebar-search input:focus {
        outline: none;
        border-color: #c5a85c;
        background: rgba(11,12,16,0.9);
    }

    .admin-sidebar-section {
        margin-bottom: 5px;
    }

    .admin-sidebar-title {
        padding: 10px 20px;
        font-size: 0.8rem;
        color: #9aa0b4;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: color 0.3s ease;
    }
    .admin-sidebar-title:hover {
        color: #c5a85c;
    }
    .admin-sidebar-title span {
        font-size: 0.7rem;
        transition: transform 0.3s ease;
    }
    .admin-sidebar-section.collapsed .admin-sidebar-title span {
        transform: rotate(-90deg);
    }
    .admin-sidebar-section.collapsed .admin-sidebar-nav {
        display: none;
    }

    .admin-sidebar-nav {
        display: flex;
        flex-direction: column;
        padding-bottom: 10px;
    }

    .admin-sidebar-item {
        padding: 10px 20px 10px 40px;
        color: #9aa0b4;
        text-decoration: none;
        font-size: 0.85rem;
        transition: all 0.2s ease;
        border-left: 3px solid transparent;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .admin-sidebar-item:hover {
        color: #f5f6fa;
        background: rgba(255,255,255,0.03);
    }
    .admin-sidebar-item.active {
        color: #c5a85c;
        background: rgba(197, 168, 92, 0.05);
        border-left: 3px solid #c5a85c;
        font-weight: bold;
    }

    /* Hide the old sub-navbar if it still exists via caching */
    .admin-sub-navbar { display: none !important; }

    /* Media query for mobile */
    @media (max-width: 768px) {
        .admin-sidebar {
            width: 250px;
            height: calc(100vh - 80px);
            position: fixed;
            top: 80px;
            left: -250px;
            border-right: 1px solid rgba(197, 168, 92, 0.15);
            border-bottom: none;
            transition: left 0.3s ease;
        }
        .admin-sidebar:not(.closed) {
            left: 0;
        }
        .admin-dashboard {
            padding-left: 5% !important;
        }
        .footer-premium {
            padding-left: 0 !important;
        }
    }

    /* Light Theme Overrides for Admin Sidebar */
    body.light-theme .admin-sidebar {
        background: #ffffff !important;
        border-color: rgba(197, 168, 92, 0.3) !important;
    }
    body.light-theme .admin-sidebar-search input {
        background: #f5f5dc !important;
        color: #1a1a1a !important;
        border-color: #d1c5a9 !important;
    }
    body.light-theme .admin-sidebar-title {
        color: #8c6010 !important;
    }
    body.light-theme .admin-sidebar-item {
        color: #4a4a4a !important;
    }
    body.light-theme .admin-sidebar-item:hover {
        color: #1a1a1a !important;
        background: rgba(197, 168, 92, 0.05) !important;
    }
    body.light-theme .admin-sidebar-item.active {
        color: #8c6010 !important;
        background: rgba(197, 168, 92, 0.1) !important;
    }
    body.light-theme #reopenSidebarBtn {
        background: #ffffff !important;
        color: #8c6010 !important;
        border-color: rgba(197, 168, 92, 0.3) !important;
    }
</style>

<button id="reopenSidebarBtn" onclick="toggleMainSidebar()" style="display: none; position: fixed; top: 100px; left: 0; z-index: 999; background: #12131c; color: #c5a85c; border: 1px solid rgba(197,168,92,0.3); border-left: none; padding: 10px 15px; border-radius: 0 5px 5px 0; cursor: pointer; font-size: 1.2rem; box-shadow: 4px 0 10px rgba(0,0,0,0.5);">
    ▶
</button>

<div class="admin-sidebar" id="adminSidebar">
    <div style="display: flex; justify-content: flex-end; padding: 0 20px 10px;">
        <button onclick="toggleMainSidebar()" style="background: none; border: none; color: #9aa0b4; cursor: pointer; font-size: 1.2rem; transition: color 0.2s;" onmouseover="this.style.color='#c5a85c'" onmouseout="this.style.color='#9aa0b4'">✖</button>
    </div>
    <div class="admin-sidebar-search">
        <input type="text" id="sidebarJumpTo" placeholder="JUMP TO..." onkeyup="filterSidebar()">
    </div>
    
    <div class="admin-sidebar-section">
        <div class="admin-sidebar-nav">
            <a href="admin-dashboard.php" class="admin-sidebar-item <?= $current_page == 'admin-dashboard.php' ? 'active' : '' ?>" data-name="dashboard">
                Dashboard
            </a>
        </div>
    </div>

    <div class="admin-sidebar-section">
        <div class="admin-sidebar-title" onclick="toggleSidebarSection(this)">
            Registrations & Delegates <span>▼</span>
        </div>
        <div class="admin-sidebar-nav">
            <a href="admin-delegates.php" class="admin-sidebar-item <?= $current_page == 'admin-delegates.php' ? 'active' : '' ?>" data-name="delegate management" style="color: #c5a85c;">
                🏅 Delegate Management
            </a>
            <a href="admin-delegate-settings.php" class="admin-sidebar-item <?= $current_page == 'admin-delegate-settings.php' ? 'active' : '' ?>" data-name="delegate settings" style="color: #c5a85c; padding-left: 30px; font-size: 0.9em;">
                ⚙️ Settings
            </a>
            <a href="admin-team-registrations.php" class="admin-sidebar-item <?= $current_page == 'admin-team-registrations.php' ? 'active' : '' ?>" data-name="team registrations">
                Team Registrations
            </a>
            <a href="admin-exhibitor-registrations.php" class="admin-sidebar-item <?= $current_page == 'admin-exhibitor-registrations.php' ? 'active' : '' ?>" data-name="exhibitor registrations">
                Exhibitor Registrations
            </a>
        </div>
    </div>

    <div class="admin-sidebar-section">
        <div class="admin-sidebar-title" onclick="toggleSidebarSection(this)">
            Passes & Inquiries <span>▼</span>
        </div>
        <div class="admin-sidebar-nav">
            <a href="admin-visitor-passes.php" class="admin-sidebar-item <?= $current_page == 'admin-visitor-passes.php' ? 'active' : '' ?>" data-name="visitor passes">
                Visitor Passes
            </a>
            <a href="admin-business-inquiries.php" class="admin-sidebar-item <?= $current_page == 'admin-business-inquiries.php' ? 'active' : '' ?>" data-name="business inquiries">
                Business Inquiries
            </a>
            <a href="admin-contact-enquiries.php" class="admin-sidebar-item <?= $current_page == 'admin-contact-enquiries.php' ? 'active' : '' ?>" data-name="contact form enquiries">
                Contact Enquiries
            </a>
            <a href="award-registrations.php" class="admin-sidebar-item <?= $current_page == 'award-registrations.php' ? 'active' : '' ?>" data-name="award gala registrations" style="color: #c5a85c;">
                🏆 Award Gala Registrations
            </a>
        </div>
    </div>

    <div class="admin-sidebar-section">
        <div class="admin-sidebar-title" onclick="toggleSidebarSection(this)">
            Home Management <span>▼</span>
        </div>
        <div class="admin-sidebar-nav">
            <a href="admin-home-carousel.php" class="admin-sidebar-item <?= $current_page == 'admin-home-carousel.php' ? 'active' : '' ?>" data-name="carousel events">
                🏠 Carousel Events
            </a>
        </div>
    </div>

    <div class="admin-sidebar-section">
        <div class="admin-sidebar-title" onclick="toggleSidebarSection(this)">
            Management (Legacy) <span>▼</span>
        </div>
        <div class="admin-sidebar-nav">

            <a href="admin-gift-cards.php" class="admin-sidebar-item <?= $current_page == 'admin-gift-cards.php' ? 'active' : '' ?>" data-name="gift card management">
                🎁 Gift Card Management
            </a>
            <a href="admin-account-management.php" class="admin-sidebar-item <?= $current_page == 'admin-account-management.php' ? 'active' : '' ?>" data-name="account management">
                Account Management
            </a>
            <a href="admin-nxl.php" class="admin-sidebar-item <?= $current_page == 'admin-nxl.php' ? 'active' : '' ?>" data-name="nxl management">
                NXL Management
            </a>

            <a href="admin-blogs.php" class="admin-sidebar-item <?= $current_page == 'admin-blogs.php' ? 'active' : '' ?>" data-name="sports blog">
                Sports Blog
            </a>
            <a href="admin-team-profiles.php" class="admin-sidebar-item <?= $current_page == 'admin-team-profiles.php' ? 'active' : '' ?>" data-name="team profiles">
                Team Profiles
            </a>
        </div>
    </div>

    <!-- NEW SECTION: Site Content & Operations -->
    <div class="admin-sidebar-section">
        <div class="admin-sidebar-title" onclick="toggleSidebarSection(this)">
            Site Content & Operations <span>▼</span>
        </div>
        <div class="admin-sidebar-nav">
            <a href="admin-tournaments.php" class="admin-sidebar-item <?= $current_page == 'admin-tournaments.php' ? 'active' : '' ?>" data-name="tournaments">
                🏆 Tournaments & Categories
            </a>
            <a href="admin-store-operations.php" class="admin-sidebar-item <?= $current_page == 'admin-store-operations.php' ? 'active' : '' ?>" data-name="store operations">
                🛒 Store & Orders
            </a>
            <a href="admin-media-gallery.php" class="admin-sidebar-item <?= $current_page == 'admin-media-gallery.php' ? 'active' : '' ?>" data-name="media gallery">
                📸 Media & Gallery
            </a>
            <a href="admin-partners-sponsors.php" class="admin-sidebar-item <?= $current_page == 'admin-partners-sponsors.php' ? 'active' : '' ?>" data-name="partners sponsors">
                🤝 Partners & Sponsors
            </a>
            <a href="admin-site-settings.php" class="admin-sidebar-item <?= $current_page == 'admin-site-settings.php' ? 'active' : '' ?>" data-name="site settings">
                ⚙️ Site Settings & Chatbot
            </a>
            <a href="admin-navbar-link.php" class="admin-sidebar-item <?= $current_page == 'admin-navbar-link.php' ? 'active' : '' ?>" data-name="admin navbar link settings">
                🔗 Navbar Custom Link
            </a>
        </div>
    </div>
</div>

<script>
function toggleSidebarSection(element) {
    const section = element.closest('.admin-sidebar-section');
    section.classList.toggle('collapsed');
}

function filterSidebar() {
    const input = document.getElementById('sidebarJumpTo').value.toLowerCase();
    const items = document.querySelectorAll('.admin-sidebar-item');
    
    items.forEach(item => {
        const name = item.getAttribute('data-name');
        if (name && name.includes(input)) {
            item.style.display = 'flex';
            // Also ensure the parent section is visible and expanded
            const section = item.closest('.admin-sidebar-section');
            if(section) {
                section.style.display = 'block';
                section.classList.remove('collapsed');
            }
        } else {
            item.style.display = 'none';
        }
    });

    // Hide sections that have no visible items
    if(input.length > 0) {
        document.querySelectorAll('.admin-sidebar-section').forEach(section => {
            const visibleItems = section.querySelectorAll('.admin-sidebar-item[style="display: flex;"]');
            const hasTitle = section.querySelector('.admin-sidebar-title');
            if (visibleItems.length === 0 && hasTitle) {
                section.style.display = 'none';
            }
        });
    } else {
        // Reset
        document.querySelectorAll('.admin-sidebar-section').forEach(section => {
            section.style.display = 'block';
        });
        items.forEach(item => {
            item.style.display = 'flex';
        });
    }
}
function toggleMainSidebar() {
    const sidebar = document.getElementById('adminSidebar');
    const dashboard = document.querySelector('.admin-dashboard');
    const footer = document.querySelector('.footer-premium');
    const reopenBtn = document.getElementById('reopenSidebarBtn');
    
    if (sidebar.classList.contains('closed')) {
        sidebar.classList.remove('closed');
        if (window.innerWidth > 768) {
            if (dashboard) dashboard.style.setProperty('padding-left', 'calc(250px + 5%)', 'important');
            if (footer) footer.style.setProperty('padding-left', '250px', 'important');
        }
        reopenBtn.style.display = 'none';
    } else {
        sidebar.classList.add('closed');
        if (window.innerWidth > 768) {
            if (dashboard) dashboard.style.setProperty('padding-left', '5%', 'important');
            if (footer) footer.style.setProperty('padding-left', '0', 'important');
        }
        reopenBtn.style.display = 'block';
    }
}

// Setup initial mobile state
document.addEventListener("DOMContentLoaded", function() {
    if (window.innerWidth <= 768) {
        document.getElementById('adminSidebar').classList.add('closed');
        document.getElementById('reopenSidebarBtn').style.display = 'block';
    }
});

// Dynamic Admin Stats
document.addEventListener("DOMContentLoaded", async function() {
    try {
        const res = await fetch('api/index.php/admin/stats');
        const data = await res.json();
        
        if (data.success) {
            const elSales = document.getElementById('statTotalSales');
            const elNxl = document.getElementById('statTotalNxl');
            const elCustomers = document.getElementById('statActiveCustomers');
            const elMerchants = document.getElementById('statMerchants');
            
            if (elSales) elSales.textContent = '₹' + parseFloat(data.sales).toLocaleString();
            if (elNxl) elNxl.textContent = parseInt(data.nxl).toLocaleString() + ' Coins';
            if (elCustomers) elCustomers.textContent = data.customers + ' Users';
            if (elMerchants) elMerchants.textContent = data.merchants + ' Merchants';
        }
    } catch (err) {
        console.error("Error fetching admin stats:", err);
    }
});
</script>
