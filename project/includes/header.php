<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/Config.php';

// Dynamically set title and meta defaults
$pageTitle = $pageTitle ?? 'GLOBAL SPORTS ARENA';
$pageDescription = $pageDescription ?? 'One Ecosystem. Infinite Possibilities. The leading championship platform for sports tournament bookings and authentic merchandise.';

$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <base href="<?php echo htmlspecialchars($basePath); ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo htmlspecialchars($pageDescription); ?>">
    <link rel="icon" type="image/svg+xml" href="assets/favicon.svg">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Inter:wght@100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- External scripts -->
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Global and Main CSS -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Inter:wght@100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap');
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            background-color: #0b0c10 !important;
            color: #f5f6fa !important;
            font-family: 'Inter', sans-serif !important;
            overflow-x: clip;
        }
        h1, h2, h3, h4, h5, h6, .brand-font {
            font-family: 'Playfair Display', serif;
        }
        .accent-font {
            font-family: 'Cormorant Garamond', serif;
            font-style: italic;
        }
        a {
            text-decoration: none;
            color: inherit;
        }
        button {
            cursor: pointer;
            font-family: 'Inter', sans-serif;
        }
        /* Premium Scrollbar Customization */
        ::-webkit-scrollbar {
            width: 10px;
        }
        ::-webkit-scrollbar-track {
            background: #0b0c10;
        }
        ::-webkit-scrollbar-thumb {
            background: #c5a85c;
            border-radius: 10px;
            border: 2px solid #0b0c10;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #a68948;
        }

        /* Light Theme Overrides */
        body.light-theme {
            --bg-primary: #f5f5dc;
            --bg-secondary: #eae1c9;
            --bg-card: rgba(255, 255, 255, 0.7);
            --text-primary: #1a1a1a;
            --text-secondary: #4a4a4a;
            --text-dark: #000000;
        }

        body.light-theme .main-layout,
        body.light-theme .home-page,
        body.light-theme .destinations-section,
        body.light-theme .flagship-events-section,
        body.light-theme .nxl-wallet-section,
        body.light-theme .membership-section,
        body.light-theme .gsa-detail-page,
        body.light-theme .gsa-about-section,
        body.light-theme .gsa-features-section,
        body.light-theme .gsa-events-section,
        body.light-theme .events-section,
        body.light-theme .gsa-gallery-section,
        body.light-theme .gallery-section,
        body.light-theme .gallery-hero,
        body.light-theme .filter-section,
        body.light-theme .blog-section,
        body.light-theme .pillars-section,
        body.light-theme .pillar-page,
        body.light-theme .partners-section,
        body.light-theme .custom-image-banner-section,
        body.light-theme .gsa-booking-section,
        body.light-theme .gsa-reviews-section,
        body.light-theme .gsa-location-section,
        body.light-theme .nexus-section,
        body.light-theme .about-page,
        body.light-theme .about-hero,
        body.light-theme .login-page,
        body.light-theme .register-page,
        body.light-theme .support-page,
        body.light-theme .visitor-page,
        body.light-theme .exhibitor-page,
        body.light-theme .media-page,
        body.light-theme .wallet-page,
        body.light-theme .credits-page,
        body.light-theme .sponsors-page,
        body.light-theme .dashboard-page,
        body.light-theme .cart-page,
        body.light-theme .admin-dashboard,
        body.light-theme .products-page,
        body.light-theme .sports-page,
        body.light-theme .dest-detail-page,
        body.light-theme .gc-container {
            background-color: #f5f5dc !important;
            color: #1a1a1a !important;
            background: #f5f5dc !important;
        }

        body.light-theme .premium-hero {
            background-image: linear-gradient(rgba(255, 255, 255, 0.3), rgba(255, 255, 255, 0.4)), url('assets/images/home background light.png') !important;
            background-size: cover !important;
            background-position: center right !important;
            background-color: transparent !important;
        }

        /* Hide the gold glow blob in light mode - it creates a visible smudge */
        body.light-theme .hero-glow-blob {
            display: none !important;
        }

        body.light-theme.gsa-pune-page .gsa-hero {
            background-image: url('assets/images/pune Background.png') !important;
        }

        body.light-theme .gsa-detail-page .gsa-hero {
            background-image: url('assets/images/gsa background light.png') !important;
        }

        body.light-theme .navbar {
            background: transparent !important;
            filter: none !important;
        }

        body.light-theme .nav-container,
        body.light-theme #navMaskedBackground,
        body.light-theme .nav-dropdown-menu {
            background: linear-gradient(135deg, #eae1c9 0%, #ddd0b0 100%) !important;
            border-color: rgba(140, 100, 30, 0.55) !important;
        }

        /* Completely hide the dark mode purple gradients in light theme */
        body.light-theme .nav-container::before,
        body.light-theme .purple-glow {
            display: none !important;
        }

        body.light-theme .nav-sports-store-tab {
            background: linear-gradient(135deg, #eae1c9 0%, #ddd0b0 100%) !important;
            border-color: rgba(140, 100, 30, 0.55) !important;
            color: #8c6010 !important;
        }

        body.light-theme .nav-icon {
            background: rgba(197, 168, 92, 0.18) !important;
            border-color: rgba(140, 100, 30, 0.4) !important;
        }

        body.light-theme .footer-premium {
            background-color: #eae1c9 !important;
            border-color: #d1c5a9 !important;
        }

        body.light-theme .theme-card-dark {
            display: none !important;
        }

        body.light-theme .theme-card-light {
            display: block !important;
            width: 100% !important;
            margin: 0 auto;
        }

        body.light-theme .nav-logo {
            mix-blend-mode: normal !important;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2)) !important;
        }

        body.light-theme h1, 
        body.light-theme h2, 
        body.light-theme h3, 
        body.light-theme h4, 
        body.light-theme p, 
        body.light-theme span:not(.badge):not(.tag):not(.metric-val):not(.sport-tag):not(.gold-highlight):not(.card-badge),
        body.light-theme a,
        body.light-theme .nav-links a,
        body.light-theme .nav-link-btn,
        body.light-theme .nav-dropdown-trigger,
        body.light-theme .nav-dropdown-item {
            color: #1a1a1a !important;
            -webkit-text-fill-color: #1a1a1a !important;
            text-shadow: none !important;
        }

        /* Keep text bright on image overlays */
        body.light-theme .gallery-overlay h4 {
            color: #c5a85c !important;
            -webkit-text-fill-color: #c5a85c !important;
        }
        body.light-theme .gallery-overlay p {
            color: #f5f6fa !important;
            -webkit-text-fill-color: #f5f6fa !important;
        }

        /* Specific Navbar text sizing */
        body.light-theme .nav-links a,
        body.light-theme .nav-link-btn,
        body.light-theme .nav-dropdown-trigger,
        body.light-theme .nav-dropdown-item {
            font-weight: 700 !important;
        }

        /* Better contrast for metrics bar */
        body.light-theme .hero-metrics-bar-inline {
            background: rgba(255, 255, 255, 0.6) !important;
            border: 1px solid rgba(0, 0, 0, 0.1) !important;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05) !important;
        }
        
        body.light-theme .metric-val {
            color: #8c6010 !important;
            -webkit-text-fill-color: #8c6010 !important;
        }

        body.light-theme .gold-highlight {
            color: #8c6010 !important;
            -webkit-text-fill-color: #8c6010 !important;
            background: none !important;
            -webkit-background-clip: border-box !important;
        }

        body.light-theme .accent-gold {
            color: #8c6010 !important;
            -webkit-text-fill-color: #8c6010 !important;
            background: none !important;
        }

        body.light-theme .card-glass,
        body.light-theme .nexus-card,
        body.light-theme .destination-card,
        body.light-theme .feature-card,
        body.light-theme .stat-card,
        body.light-theme .dashboard-card,
        body.light-theme .flagship-card,
        body.light-theme .membership-card,
        body.light-theme .nxl-banner-card,
        body.light-theme .blog-post-card,
        body.light-theme .review-card,
        body.light-theme .partner-card,
        body.light-theme .pillar-card-box {
            background: rgba(255, 255, 255, 0.7) !important;
            border: 1px solid rgba(197, 168, 92, 0.6) !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08) !important;
        }

        body.light-theme .premium-hero h1,
        body.light-theme .premium-hero h2,
        body.light-theme .premium-hero p,
        body.light-theme .pillar-hero h1,
        body.light-theme .pillar-hero h2,
        body.light-theme .pillar-hero p,
        body.light-theme .pillar-hero .subtitle {
            color: #1a1a1a !important;
            -webkit-text-fill-color: #1a1a1a !important;
            background: none !important;
            -webkit-background-clip: border-box !important;
            background-clip: border-box !important;
            text-shadow: none !important;
            opacity: 1 !important;
            visibility: visible !important;
            transform: none !important;
        }

        body.light-theme .premium-hero h2 span.gold-highlight {
            color: #8c6010 !important;
            -webkit-text-fill-color: #8c6010 !important;
            background: none !important;
            -webkit-background-clip: border-box !important;
            background-clip: border-box !important;
            animation: none !important;
        }

        body.light-theme .flagship-card-desc,
        body.light-theme .event-theme,
        body.light-theme .pillar-section p,
        body.light-theme .pillar-card-box h3,
        body.light-theme .creative-card .card-description {
            color: #1a1a1a !important;
            -webkit-text-fill-color: #1a1a1a !important;
            background: transparent !important;
            -webkit-background-clip: border-box !important;
            text-shadow: none !important;
            opacity: 1 !important;
            visibility: visible !important;
            transform: none !important;
        }

        body.light-theme .creative-card .card-title {
            color: #ffffff !important;
            -webkit-text-fill-color: #ffffff !important;
            text-shadow: 0 2px 8px rgba(0,0,0,0.8) !important;
        }
        
        body.light-theme .creative-card .card-badge {
            color: #ffffff !important;
            -webkit-text-fill-color: #ffffff !important;
            text-shadow: 0 1px 4px rgba(0,0,0,0.8) !important;
        }

        /* Hero description border for light mode */
        body.light-theme .hero-desc {
            border-left-color: #c5a85c !important;
            color: #333333 !important;
            -webkit-text-fill-color: #333333 !important;
        }

        /* Metric labels in hero */
        body.light-theme .hero-metrics-bar-inline .metric-lbl {
            color: #333333 !important;
            -webkit-text-fill-color: #333333 !important;
        }

        body.light-theme .hero-action-buttons,
        body.light-theme .hero-action-buttons a {
            opacity: 1 !important;
            visibility: visible !important;
            transform: none !important;
        }

        /* Pillar creative cards */
        body.light-theme .creative-card::after {
            background: linear-gradient(180deg, rgba(245, 245, 220, 0) 0%, rgba(245, 245, 220, 0) 40%, rgba(245, 245, 220, 1) 100%) !important;
        }
        
        body.light-theme .creative-card {
            border-color: rgba(197, 168, 92, 0.5) !important;
        }

        body.light-theme .creative-card.energeia {
            background-image: url("https://images.unsplash.com/photo-1466611653911-95081537e5b7?w=500&auto=format&fit=crop&q=60") !important;
        }
        body.light-theme .creative-card.ekonamia {
            background-image: url("https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=500&auto=format&fit=crop&q=60") !important;
        }
        body.light-theme .creative-card.exploria {
            background-image: url("https://images.unsplash.com/photo-1436491865332-7a61a109cc05?w=500&auto=format&fit=crop&q=60") !important;
        }
        body.light-theme .creative-card.evexia {
            background-image: url("https://images.unsplash.com/photo-1545205597-3d9d02c29597?w=500&auto=format&fit=crop&q=60") !important;
        }

        /* Fix creative card icon colors */
        body.light-theme .creative-card.energeia .card-icon,
        body.light-theme .creative-card.energeia .card-icon i {
            color: #f7931e !important;
            -webkit-text-fill-color: #f7931e !important;
        }
        body.light-theme .creative-card.ekonamia .card-icon,
        body.light-theme .creative-card.ekonamia .card-icon i {
            color: #6fb6ff !important;
            -webkit-text-fill-color: #6fb6ff !important;
        }
        body.light-theme .creative-card.exploria .card-icon,
        body.light-theme .creative-card.exploria .card-icon i {
            color: #6fb6ff !important;
            -webkit-text-fill-color: #6fb6ff !important;
        }
        body.light-theme .creative-card.evexia .card-icon,
        body.light-theme .creative-card.evexia .card-icon i {
            color: #ff8ab4 !important;
            -webkit-text-fill-color: #ff8ab4 !important;
        }

        /* Fix Hero Action Buttons */
        body.light-theme .btn-premium-gold {
            background: linear-gradient(135deg, #c5a85c 0%, #8c7237 100%) !important;
            color: #0b0c10 !important;
            -webkit-text-fill-color: #0b0c10 !important;
            border: none !important;
        }
        body.light-theme .btn-premium-outline {
            background: transparent !important;
            color: #1a1a1a !important;
            -webkit-text-fill-color: #1a1a1a !important;
            border: 2px solid #c5a85c !important;
        }

        /* Newsletter and Footer Icons */
        body.light-theme .newsletter-form input {
            background-color: #ffffff !important;
            border: 1px solid #c5a85c !important;
            color: #1a1a1a !important;
        }
        
        body.light-theme .social-links a,
        body.light-theme .footer-socials a,
        body.light-theme .footer-socials a i {
            background-color: #d1c5a9 !important;
            color: #1a1a1a !important;
            border: none !important;
        }
        
        body.light-theme .social-links a:hover,
        body.light-theme .footer-socials a:hover,
        body.light-theme .footer-socials a:hover i {
            background-color: #c5a85c !important;
            color: #ffffff !important;
        }

        /* Fix Event Cards */
        body.light-theme .event-card {
            background: rgba(255, 255, 255, 0.95) !important;
            border-color: rgba(197, 168, 92, 0.5) !important;
            position: relative !important;
            overflow: hidden !important;
        }
        body.light-theme .event-card:hover {
            background: #ffffff !important;
            border-color: #c5a85c !important;
            box-shadow: 0 10px 30px rgba(197, 168, 92, 0.2) !important;
        }
        body.light-theme .event-card h3,
        body.light-theme .event-card p,
        body.light-theme .event-card span,
        body.light-theme .event-card .event-details,
        body.light-theme .event-card .event-badge,
        body.light-theme .event-card .book-btn {
            position: relative !important;
            z-index: 2 !important;
            color: #1a1a1a !important;
            -webkit-text-fill-color: #1a1a1a !important;
        }
        body.light-theme .event-card::before {
            content: "";
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(to bottom, rgba(250, 246, 235, 0.1) 0%, rgba(250, 246, 235, 0.7) 40%, rgba(250, 246, 235, 1) 80%) !important;
            z-index: 0 !important;
            border-radius: 20px;
        }
        body.light-theme .event-card .event-icon {
            color: #8c6010 !important;
            -webkit-text-fill-color: #8c6010 !important;
        }
        body.light-theme .event-card .event-price {
            color: #8c6010 !important;
            -webkit-text-fill-color: #8c6010 !important;
            font-weight: bold !important;
        }
        body.light-theme .event-card .event-badge {
            background: rgba(197, 168, 92, 0.15) !important;
            border: 1px solid #8c6010 !important;
            color: #8c6010 !important;
            -webkit-text-fill-color: #8c6010 !important;
        }
        body.light-theme .event-card .book-btn {
            background: linear-gradient(135deg, #c5a85c 0%, #8c7237 100%) !important;
            color: #0b0c10 !important;
            -webkit-text-fill-color: #0b0c10 !important;
        }

        /* Fix Visitor Pass Button */
        body.light-theme .booking-btn.secondary {
            border: 2px solid #1a1a1a !important;
            color: #1a1a1a !important;
            background: transparent !important;
        }
        body.light-theme .booking-btn.secondary:hover {
            background: rgba(0,0,0,0.05) !important;
            transform: translateY(-3px);
        }

        /* Fix Footer CTA Overlay and Image */
        body.light-theme .gsa-footer-cta {
            background-image: url("https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=1200") !important;
        }
        body.light-theme .cta-overlay {
            background: rgba(245, 245, 220, 0.75) !important;
        }

        body.light-theme .flagship-card-header,
        body.light-theme .flagship-card-footer,
        body.light-theme .nexus-card-header,
        body.light-theme .nexus-card-footer {
            background: rgba(234, 225, 201, 0.9) !important;
        }
        
        /* Floating Theme Button */
        .theme-toggle-btn {
            position: fixed;
            top: 100px; /* Below navbar */
            right: 20px;
            z-index: 1000;
            background: #c5a85c;
            color: #0b0c10;
            border: none;
            border-radius: 50%;
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            transition: all 0.3s ease;
        }
        .theme-toggle-btn:hover {
            transform: scale(1.1);
            background: #a68948;
        }
    </style>

    <!-- =====================================================
         GLOBAL RESPONSIVE CSS — Applied Across All Pages
         ===================================================== -->
    <style>
        /* --- Global overflow prevention --- */
        *, *::before, *::after { box-sizing: border-box; }
        img, video, canvas, svg { max-width: 100%; height: auto; display: block; }

        /* --- Tables: always scrollable on mobile --- */
        table { width: 100%; border-collapse: collapse; }
        .table-responsive, .overflow-x-auto { overflow-x: auto; -webkit-overflow-scrolling: touch; }

        /* --- Forms: inputs always full width --- */
        input[type="text"], input[type="email"], input[type="password"],
        input[type="tel"], input[type="number"], input[type="url"],
        input[type="date"], input[type="search"], select, textarea {
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
        }

        /* --- Responsive hero typography --- */
        @media (max-width: 768px) {
            .premium-hero h1 { font-size: 2rem !important; }
            .premium-hero h2 { font-size: 1.5rem !important; }
            .hero-desc { font-size: 1rem !important; }
            .hero-content { padding: 0 1rem; }
        }

        /* --- Footer grid: responsive columns --- */
        .footer-top-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 2rem;
            padding: 3rem 1.5rem;
        }

        @media (max-width: 640px) {
            .footer-top-grid {
                grid-template-columns: 1fr 1fr;
            }
            .footer-brand-col {
                grid-column: 1 / -1;
            }
        }

        /* --- Admin Navbar: ensure sidebar works on mobile --- */
        .admin-navbar-sidebar {
            max-width: 100%;
            overflow-x: hidden;
        }

        /* --- Cards: ensure they don't overflow --- */
        .flagship-card, .dashboard-card, .membership-card, .product-card, .destination-card {
            min-width: 0;
            max-width: 100%;
        }

        /* --- Modals: always fit on screen --- */
        .modal-overlay {
            padding: 1rem;
        }
        .modal-content {
            width: 100% !important;
            max-width: 540px !important;
            margin: 0 auto;
        }

        /* --- Generic page container fix --- */
        .main-layout { overflow-x: clip; }

        @media (max-width: 640px) {
            /* Make buttons stack full width in action groups */
            .hero-action-buttons a,
            .hero-action-buttons button {
                width: 100%;
                text-align: center;
                justify-content: center;
            }

            /* NXL flow steps: 2 columns on mobile */
            .nxl-steps-flow {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 1rem;
            }

            /* Membership cards: single column */
            .membership-grid {
                grid-template-columns: 1fr !important;
            }
        }

        @media (max-width: 480px) {
            /* Very small screens: headings shrink */
            h1 { font-size: 1.75rem !important; }
            h2 { font-size: 1.4rem !important; }
            h3 { font-size: 1.15rem !important; }

            .section-premium-title h2 { font-size: 1.6rem !important; }
        }
    </style>

    <link rel="stylesheet" href="assets/css/MainLayout.css">
    <link rel="stylesheet" href="assets/css/Navbar.css?v=<?= time() ?>">
    <link rel="stylesheet" href="assets/css/Footer.css">
    
    <!-- Include cart utility -->
    <script src="assets/js/cart.js"></script>
</head>
<body class="overflow-x-clip w-full">
<script>
    // Apply theme immediately to prevent flashing. Default is LIGHT mode.
    if (localStorage.getItem('userTheme') !== 'dark') {
        document.body.classList.add('light-theme');
    } else {
        document.body.classList.add('dark-theme');
    }
</script>
<?php
$isAdminTheme = (isset($_SESSION['userRole']) && $_SESSION['userRole'] === 'ADMIN');
if (isset($disableAdminTheme) && $disableAdminTheme === true) {
    $isAdminTheme = false;
}
?>
<div class="main-layout <?php echo $isAdminTheme ? 'admin-theme' : ''; ?>">

<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>


<script>
    function toggleTheme() {
        const body = document.body;
        const icon = document.getElementById('themeIcon');
        if (body.classList.contains('light-theme')) {
            // Switching to DARK mode
            body.classList.remove('light-theme');
            body.classList.add('dark-theme');
            icon.classList.remove('fa-moon');
            icon.classList.add('fa-sun');
            localStorage.setItem('userTheme', 'dark');
        } else {
            // Switching to LIGHT mode
            body.classList.add('light-theme');
            body.classList.remove('dark-theme');
            icon.classList.remove('fa-sun');
            icon.classList.add('fa-moon');
            localStorage.setItem('userTheme', 'light');
        }
    }

    // Apply saved theme icon on load
    document.addEventListener('DOMContentLoaded', function() {
        const icon = document.getElementById('themeIcon');
        if (localStorage.getItem('userTheme') !== 'dark') {
            if (icon) {
                icon.classList.remove('fa-sun');
                icon.classList.add('fa-moon');
            }
        } else {
            if (icon) {
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
            }
        }
    });
</script>
