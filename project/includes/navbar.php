<nav class="navbar" style="position: sticky; top: 0; z-index: 1000;">

  <!-- NEW BACKGROUND LOGO GIF -->
  <img src="assets/images/logo_original_colors.gif?v=2" id="underNavLogo" alt="" style="position: absolute; z-index: -1; pointer-events: none; border-radius: 50%; width: 52px; height: 52px; transform: scale(1.45); mix-blend-mode: multiply; transform-origin: center center;">

  <!-- ✨ THE PILL ✨ -->
  <div class="nav-container" id="mainNavContainer" style="background: transparent !important; border: none !important; box-shadow: none !important;">
  
    <!-- ISOLATED BACKGROUND LAYER FOR MASKING -->
    <div id="navMaskedBackground" style="position: absolute; inset: 0; background: linear-gradient(180deg, #1a1b2e 0%, #12131c 50%, #0e0f18 100%); border: 1.5px solid #c5a85c; border-radius: inherit; box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.06), inset 0 -1px 0 rgba(0, 0, 0, 0.3), 0 4px 28px rgba(0, 0, 0, 0.55), 0 0 0 1px rgba(197, 168, 92, 0.08); z-index: -1; transition: mask-image 0.2s ease;">
       <!-- Recreate the star-texture overlay so it also gets masked -->
       <div class="purple-glow" style="position: absolute; inset: 0; border-radius: inherit; background-image: radial-gradient(ellipse at 70% 50%, rgba(120, 80, 220, 0.12) 0%, transparent 60%), radial-gradient(ellipse at 30% 50%, rgba(60, 20, 140, 0.1) 0%, transparent 60%); pointer-events: none;"></div>
    </div>

    <!-- Logo -->
    <a href="index.php" class="nav-brand" onclick="closeMenu()" style="position: relative; margin-left: 15px; margin-right: 30px;">
      <img src="assets/images/logo 2.png" class="nav-logo" id="dummyNavLogo" alt="Logo" style="opacity: 0; height: 52px !important; width: 52px !important; object-fit: contain;">
    </a>

    <!-- Menu (links + actions) -->
    <div class="nav-menu" id="navMenu">

      <!-- ── Navigation Links ── -->
      <div class="nav-links">

        <!-- HOME -->
        <a href="index.php" class="nav-link-btn" data-page="index" onclick="closeMenu()">HOME</a>
        <a href="about-event.php" class="nav-link-btn" data-page="about-event" onclick="closeMenu()">ABOUT US</a>

        <?php
        require_once __DIR__ . '/../config/Settings.php';
        $customLinkText = Settings::get('custom_link_text', '');
        $customLinkUrl = Settings::get('custom_link_url', '');
        if (!empty($customLinkText) && !empty($customLinkUrl)):
        ?>
            <a href="<?php echo htmlspecialchars($customLinkUrl); ?>" class="nav-link-btn" target="_blank"><?php echo htmlspecialchars(strtoupper($customLinkText)); ?></a>
        <?php endif; ?>

        <!-- EVENTS dropdown -->
        <div class="nav-dropdown">
          <span class="nav-dropdown-trigger" data-page="events">EVENTS <span class="dropdown-arrow">▼</span></span>
          <div class="nav-dropdown-menu">
            <button class="nav-dropdown-item" onclick="handleNavClick('flagship-events')">Flagship Tournaments</button>
            <button class="nav-dropdown-item" onclick="handleNavClick('active-tournaments')">Active Tournaments</button>
            <a href="event-registration.php" class="nav-dropdown-item" data-page="event-registration" onclick="closeMenu()">Register Event</a>
            <a href="delegate-registration.php" class="nav-dropdown-item" data-page="delegate-registration" onclick="closeMenu()">Register as Delegate</a>
            <a href="visitor-pass.php" class="nav-dropdown-item" data-page="visitor-pass" onclick="closeMenu()">Visitor Pass</a>
            <a href="exhibitor.php" class="nav-dropdown-item" data-page="exhibitor" onclick="closeMenu()">Exhibitor Registration</a>
            <a href="gallery.php" class="nav-dropdown-item" data-page="gallery" onclick="closeMenu()">Photo Gallery</a>
            <a href="media-hub.php" class="nav-dropdown-item" data-page="media-hub" onclick="closeMenu()">Events Highlights &amp; Media</a>
          </div>
        </div>

        <button class="nav-link-btn" data-page="destinations" onclick="handleNavClick('destinations')">DESTINATIONS</button>
        <button class="nav-link-btn" data-page="membership" onclick="handleNavClick('membership')">MEMBERSHIP</button>

        <!-- NXL CREDITS dropdown -->
        <div class="nav-dropdown">
          <span class="nav-dropdown-trigger" data-page="credits">NXL CREDITS <span class="dropdown-arrow">▼</span></span>
          <div class="nav-dropdown-menu">
            <a href="wallet.php" class="nav-dropdown-item" data-page="wallet" onclick="closeMenu()">My Wallet</a>
            <a href="credits.php" class="nav-dropdown-item" data-page="credits" onclick="closeMenu()">My Credits</a>
          </div>
        </div>

        <!-- ••• more dropdown -->
        <div class="nav-dropdown" onclick="this.classList.toggle('open')">
          <span class="nav-dropdown-trigger" style="font-size:1rem;letter-spacing:3px;">•••</span>
          <div class="nav-dropdown-menu" style="min-width:160px; text-align:center;">
            <button class="nav-dropdown-item" onclick="handleNavClick('partners')">Partners</button>
            <a href="sponsors.php" class="nav-dropdown-item" data-page="sponsors" onclick="closeMenu()">Business</a>
            <a href="gift-cards.php" class="nav-dropdown-item" data-page="gift-cards" onclick="closeMenu()">🎁 Gift Cards</a>
            <button class="nav-dropdown-item" onclick="handleNavClick('blog')">Blog</button>
            <a href="contact.php" class="nav-dropdown-item" data-page="contact" id="contactLink" onclick="closeMenu()">Contact Us</a>
          </div>
        </div>

        <!-- SPORTS STORE — boxed inside navbar -->
        <a href="products.php" class="nav-store-btn" data-page="products" onclick="closeMenu()">
          <i class="fa-solid fa-cart-shopping"></i>
          SPORTS STORE
        </a>

      </div><!-- /nav-links -->

      <!-- ── Right side: icons + auth ── -->
      <div class="nav-actions">

        <!-- Theme toggle moon/sun icon button -->
        <button class="nav-icon" onclick="toggleTheme()" title="Toggle Theme">
          <i class="fa-solid fa-moon" id="themeIcon"></i>
        </button>

        <!-- Profile icon button — white user silhouette -->
        <a href="user-dashboard.php" id="dashboardLink" class="nav-icon" title="Dashboard" onclick="closeMenu()" style="position:relative;">
          <i class="fa-solid fa-user"></i>
          <span id="navNotifBadge" style="position:absolute;top:-5px;right:-5px;background:#ef4444;color:#fff;border-radius:50%;width:16px;height:16px;font-size:0.6rem;display:none;align-items:center;justify-content:center;border:1.5px solid #0b0c10;">0</span>
        </a>

        <!-- Cart icon button with badge -->
        <a href="cart.php" class="nav-icon cart-link" title="Cart" onclick="closeMenu()" style="position:relative;">
          <i class="fa-solid fa-cart-shopping"></i>
          <span class="cart-count" id="cartCountElement" style="display:none;">0</span>
        </a>

        <!-- Auth: greeting + logout / login -->
        <span id="authButtonContainer">
          <a href="login.php" class="login-btn" onclick="closeMenu()">Login</a>
        </span>

      </div><!-- /nav-actions -->

    </div><!-- /nav-menu -->

    <!-- Hamburger (mobile) -->
    <div class="mobile-menu-btn" onclick="toggleMenu()">
      <span id="menuIcon">☰</span>
    </div>

  </div><!-- /nav-container (pill) -->


</nav>

<script>
/* ── Mobile menu ── */
function toggleMenu() {
    const navMenu = document.getElementById("navMenu");
    const menuIcon = document.getElementById("menuIcon");
    navMenu.classList.toggle("active");
    menuIcon.textContent = navMenu.classList.contains("active") ? "✕" : "☰";
}
function closeMenu() {
    document.getElementById("navMenu").classList.remove("active");
    document.getElementById("menuIcon").textContent = "☰";
}

/* ── Section scroll / redirect ── */
function handleNavClick(sectionId) {
    closeMenu();
    const isHome = window.location.pathname.endsWith("index.php")
                || window.location.pathname.endsWith("/")
                || window.location.pathname === "";
    if (isHome) {
        const el = document.getElementById(sectionId);
        if (el) el.scrollIntoView({ behavior: "smooth" });
    } else {
        window.location.href = "index.php?scrollTo=" + sectionId;
    }
}

/* ── Auth rendering ── */
function updateNavbarAuth() {
    const token       = localStorage.getItem("token");
    const userRole    = localStorage.getItem("userRole");
    const userName    = localStorage.getItem("userName") || localStorage.getItem("userEmail") || "User";
    const container   = document.getElementById("authButtonContainer");
    const dashLink    = document.getElementById("dashboardLink");

    if (token) {
        dashLink.href = (userRole === "ADMIN") ? "admin-dashboard.php" : "user-dashboard.php";

        const membership = localStorage.getItem("userMembership");
        let nameStyle = "";
        if (membership && membership.toLowerCase() !== "none") {
            nameStyle = "color: #60a5fa !important; text-shadow: 0 0 10px rgba(96, 165, 250, 0.6) !important; font-weight: 800 !important;";
        }
        const roleHtml = (userRole === "ADMIN")
            ? `<span style="font-size:0.68rem;background:rgba(197,168,92,0.15);padding:2px 6px;border-radius:4px;margin-left:4px;color:#f5d87a;font-weight:700;letter-spacing:0.04em;">ADMIN</span>`
            : "";

        container.style.display = 'flex';
        container.style.alignItems = 'center';
        container.style.justifyContent = 'center';
        container.style.flexWrap = 'wrap';
        container.style.gap = '15px';
        
        container.innerHTML =
            `<div style="display: flex; flex-direction: column; align-items: center; justify-content: center; line-height: 1.2;">
                <span class="nav-greeting" style="display: flex; align-items: center; gap: 6px;">Hi, <span style="${nameStyle}">${userName}</span>${roleHtml}</span>
                <span id="adminNxlDisplay"></span>
            </div>` +
            `<button onclick="handleLogout()" class="login-btn">Logout</button>`;
            
        if (userRole === "ADMIN") {
            const email = localStorage.getItem('userEmail') || '';
            fetch(`api/index.php/wallet/balance?email=${encodeURIComponent(email)}`, {
                headers: { "Authorization": "Bearer " + token }
            })
            .then(res => res.json())
            .then(data => {
                if (data.nxlCredits !== undefined && document.getElementById('adminNxlDisplay')) {
                    document.getElementById('adminNxlDisplay').innerHTML = `<span style="font-weight: 800; font-size: 0.85rem; display: flex; align-items: center; justify-content: center; gap: 4px; color: inherit; letter-spacing: 0.5px; margin-top: 3px;">💎 ${data.nxlCredits.toLocaleString()}</span>`;
                }
            })
            .catch(err => console.error("Error fetching admin NXL", err));
        }
    } else {
        dashLink.href = "login.php";
        container.innerHTML = `<a href="login.php" class="login-btn" onclick="closeMenu()">Login</a>`;
    }
}

function handleLogout() {
    ["token","userEmail","userRole","userName","userMembership"].forEach(k => localStorage.removeItem(k));
    fetch('api/index.php/auth/logout', { method: 'POST' }).catch(() => {});
    closeMenu();
    window.location.href = "login.php";
}

/* ── Cart count ── */
function updateCartCount() {
    if (!window.Cart) return;
    const count = window.Cart.getCartCount();
    const el    = document.getElementById("cartCountElement");
    if (!el) return;
    el.style.display = count > 0 ? "flex" : "none";
    el.textContent   = count;
}
window.addEventListener('cartUpdate', updateCartCount);

/* ── Notifications badge ── */
async function updateNavNotifications() {
    const email = localStorage.getItem("userEmail");
    const role  = localStorage.getItem("userRole");
    if (!email || (role && role.toUpperCase() !== "USER")) return;
    try {
        const res    = await fetch(`api/index.php/user/notifications?email=${email}`);
        const result = await res.json();
        const badge  = document.getElementById('navNotifBadge');
        if (!badge) return;
        if (result.success && result.data && result.data.length > 0) {
            const cleared  = parseInt(localStorage.getItem('clearedNotifTime_' + email) || '0');
            const visible  = result.data.filter(n => (n.timestamp * 1000) > cleared);
            const lastSeen = parseInt(localStorage.getItem('lastSeenNotifTime_' + email) || '0');
            const unread   = visible.filter(n => (n.timestamp * 1000) > lastSeen).length;
            badge.style.display = unread > 0 ? 'flex' : 'none';
            badge.textContent   = unread > 9 ? '9+' : unread;
        } else {
            badge.style.display = 'none';
        }
    } catch(e) { console.error("Nav notifications error", e); }
}

/* ── DOMContentLoaded ── */
document.addEventListener("DOMContentLoaded", function () {
    updateNavbarAuth();
    updateCartCount();

    /* ── Active nav highlight ── */
    (function setActiveNav() {
        const path = window.location.pathname;
        const page = path.split('/').pop().replace('.php','') || 'index';

        // Map: page filename (no .php) → data-page value on the trigger
        const pageMap = {
            'index'              : 'index',
            ''                   : 'index',
            'about-event'        : 'about-event',
            'event-registration' : 'events',
            'visitor-pass'       : 'events',
            'exhibitor'          : 'events',
            'gallery'            : 'events',
            'media-hub'          : 'events',
            'products'           : 'products',
            'wallet'             : 'credits',
            'credits'            : 'credits',
            'sponsors'           : 'sponsors',
            'contact'            : 'contact',
            'gift-cards'         : 'gift-cards',
            'gift-card-details'  : 'gift-cards',
            'gift-card-checkout' : 'gift-cards',
            'gift-card-success'  : 'gift-cards',
            'gift-card-redeem'   : 'gift-cards',
        };

        const activeKey = pageMap[page] || page;

        // Apply .nav-active to matching element
        document.querySelectorAll('[data-page]').forEach(el => {
            if (el.getAttribute('data-page') === activeKey) {
                el.classList.add('nav-active');
            }
        });
    })();

    const params   = new URLSearchParams(window.location.search);
    const scrollTo = params.get('scrollTo');
    if (scrollTo) {
        setTimeout(() => {
            const el = document.getElementById(scrollTo);
            if (el) el.scrollIntoView({ behavior: "smooth" });
        }, 300);
    }

    updateNavNotifications();
});

// Dynamic Circular Cutout for Logo
window.addEventListener('DOMContentLoaded', () => {
    const navContainer = document.getElementById('mainNavContainer');
    const maskBg = document.getElementById('navMaskedBackground');
    const dummyLogo = document.getElementById('dummyNavLogo');
    const realLogo = document.getElementById('underNavLogo');
    
    function updateLogoHole() {
        if (!dummyLogo || !navContainer || !realLogo || !maskBg) return;
        const rect = dummyLogo.getBoundingClientRect();
        const navRect = navContainer.getBoundingClientRect();
        
        // Hide if not visible
        if (rect.width === 0) return;

        // Mask center relative to nav-container
        const centerX = rect.left - navRect.left + rect.width / 2;
        const centerY = rect.top - navRect.top + rect.height / 2;
        const holeRadius = (rect.height / 2) + 6; // 6px gap for hollow space
        
        // Apply the mask cutout ONLY to the background layer, so dropdowns don't get clipped
        maskBg.style.webkitMaskImage = `radial-gradient(circle ${holeRadius}px at ${centerX}px ${centerY}px, transparent ${holeRadius}px, black ${holeRadius + 1}px)`;
        maskBg.style.maskImage = `radial-gradient(circle ${holeRadius}px at ${centerX}px ${centerY}px, transparent ${holeRadius}px, black ${holeRadius + 1}px)`;
        
        // Position the GIF exactly behind it, accounting for navbar padding
        const navMain = document.querySelector('nav.navbar');
        const navMainRect = navMain.getBoundingClientRect();
        const navStyle = window.getComputedStyle(navMain);
        const paddingTop = parseFloat(navStyle.paddingTop) || 0;

        let pTop = 0;
        if (window.innerWidth <= 768) {
            dummyLogo.style.setProperty('height', '52px', 'important');
            dummyLogo.style.setProperty('width', '52px', 'important');
            realLogo.style.transform = 'scale(1.2)';
            pTop = paddingTop; // Fixes vertical shift on mobile
        } else {
            dummyLogo.style.setProperty('height', '52px', 'important');
            dummyLogo.style.setProperty('width', '52px', 'important');
            realLogo.style.transform = 'scale(1.45)';
            pTop = 0; // Original PC behavior
        }

        realLogo.style.left = (rect.left - navMainRect.left) + 'px';
        realLogo.style.top = (rect.top - navMainRect.top - pTop) + 'px';
        realLogo.style.width = rect.width + 'px';
        realLogo.style.height = rect.height + 'px';
    }
    
    window.addEventListener('resize', updateLogoHole);
    window.addEventListener('scroll', updateLogoHole);
    setTimeout(updateLogoHole, 100);
    setTimeout(updateLogoHole, 500);
});
</script>

<!-- Global Chatbot Widget -->
<?php require_once __DIR__ . '/chatbot-widget.php'; ?>
