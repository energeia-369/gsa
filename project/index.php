<?php
$pageTitle = "GLOBAL SPORTS ARENA | Home";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
require_once __DIR__ . '/models/Tournament.php';

// Fetch tournaments from database
$tournamentModel = new Tournament();
$dbTournaments = [];
try {
    $dbTournaments = $tournamentModel->findAll();
} catch (Exception $e) {
    error_log("Failed to load tournaments: " . $e->getMessage());
}

require_once __DIR__ . '/config/Database.php';
// Fetch dynamic home event cards
$dynamicCards = [];
$dynamicBlogs = [];
$homeEventCards = [];
try {
    $dbConn = Database::getConnection();
    
    // Fetch NEW dynamic home carousel events (Exclude those marked for Overseas/Indian States carousel)
    $newCarouselStmt = $dbConn->query("SELECT * FROM home_carousel_events WHERE status = 'published' AND show_on_home = 1 AND show_in_overseas = 0 ORDER BY display_order ASC");
    $dynamicHomeCarousel = $newCarouselStmt->fetchAll(PDO::FETCH_ASSOC);

    // Legacy fallback queries
    $cardsStmt = $dbConn->query("SELECT * FROM home_event_cards WHERE status = 'active' ORDER BY id ASC");
    $dbCarouselCards = $cardsStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch custom full-width image banners from home_carousel_events
    $stmtEventCards = $dbConn->query("SELECT * FROM home_carousel_events WHERE status = 'published' AND show_home_banner = 1 ORDER BY display_order ASC, id DESC");
    $homeEventCards = $stmtEventCards->fetchAll(PDO::FETCH_ASSOC);

    
    $dynamicCards = [];
    foreach ($dbCarouselCards as $c) {
        $isDynamic = $c['dynamic_page_enabled'] ?? 0;
        $btnLink = $c['link'] ?? $c['button_link'] ?? '#';
        $slug = $c['slug'] ?? '';
        $link = ($isDynamic == 1 && $slug !== '') ? 'home-event-details.php?slug=' . $slug : $btnLink;
        
        $countryStr = $c['country'] ?? '';
        if(isset($c['country_or_state'])) $countryStr = $c['country_or_state'];
        $type = (strtolower(trim($countryStr)) === 'india' || in_array(strtolower(trim($countryStr)), ['maharashtra', 'karnataka', 'tamil nadu', 'delhi', 'goa', 'kerala', 'rajasthan', 'gujarat', 'pune', 'mumbai', 'bangalore'])) ? 'national' : 'international';

        $dynamicCards[] = [
            'id' => (int)$c['id'],
            'event_title' => $c['title'] ?? '', // For the custom banners
            'image' => $c['thumbnail'] ?? '',
            'country' => strtoupper($countryStr),
            'city' => $c['location'] ?? '',
            'date' => $c['event_date'] ?? '',
            'link' => $link,
            'type' => $type,
            'country_or_state' => $countryStr // Legacy JS compatibility
        ];
    }
    
    // Fetch NEW overseas events from home_carousel_events
    $overseasStmt = $dbConn->query("SELECT * FROM home_carousel_events WHERE status = 'published' AND show_in_overseas = 1 ORDER BY display_order ASC");
    $newOverseasCards = $overseasStmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($newOverseasCards as $c) {
        $slug = $c['slug'] ?? '';
        $btnLink = $c['btn_url'] ?? '';
        $link = (!empty($btnLink)) ? $btnLink : 'home/events/' . $slug;
        
        $countryStr = $c['country'] ?? '';
        if (empty($countryStr)) $countryStr = $c['state'] ?? '';
        
        $type = (strtolower(trim($countryStr)) === 'india' || in_array(strtolower(trim($countryStr)), ['maharashtra', 'karnataka', 'tamil nadu', 'delhi', 'goa', 'kerala', 'rajasthan', 'gujarat', 'pune'])) ? 'national' : 'international';

        $dynamicCards[] = [
            'id' => (int)$c['id'] + 1000, // offset id to avoid conflicts
            'event_title' => $c['title'] ?? '', 
            'image' => $c['carousel_img'] ?: $c['hero_banner'] ?: '',
            'country' => strtoupper($countryStr),
            'city' => $c['state'] ?? '',
            'date' => $c['event_date'] ?? '',
            'link' => $link,
            'type' => $type,
            'country_or_state' => $countryStr // Legacy JS compatibility
        ];
    }

    // Fetch dynamic blogs (3x3 grid = up to 9 blogs)
    $stmtBlogs = $dbConn->query("SELECT * FROM blogs WHERE status = 'active' ORDER BY id DESC LIMIT 9");
    $dynamicBlogs = $stmtBlogs->fetchAll(PDO::FETCH_ASSOC);

    // Fetch admin partners for carousel
    $dbPartners = [];
    try {
        $partnerTable = $dbConn->query("SHOW TABLES LIKE 'admin_partners'");
        if ($partnerTable->rowCount() > 0) {
            $pStmt = $dbConn->query("SELECT * FROM admin_partners ORDER BY created_at ASC");
            $dbPartners = $pStmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (Exception $e) { /* table may not exist yet */ }

    // Fetch dynamic GSA Tournaments
    $gsaStmt = $dbConn->query("SELECT * FROM home_carousel_events WHERE status = 'published' AND show_on_gsa = 1 ORDER BY display_order ASC LIMIT 6");
    $dbTournaments = $gsaStmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    error_log("Failed to load data: " . $e->getMessage());
}


// Default events fallback if DB has no tournaments
$defaultEvents = [
    [
        "id" => 1,
        "name" => "Champions League Finals",
        "sport" => "Soccer",
        "date" => "May 15, " . date('Y') . " | 7:00 PM",
        "venue" => "National Stadium, Mumbai",
        "registration_fee" => 999,
        "badge" => "Limited Seats"
    ],
    [
        "id" => 2,
        "name" => "Basketball Pro League",
        "sport" => "Basketball",
        "date" => "June 5, " . date('Y') . " | 6:30 PM",
        "venue" => "Indoor Arena, Delhi",
        "registration_fee" => 799,
        "badge" => "Early Bird"
    ],
    [
        "id" => 3,
        "name" => "Tennis Grand Slam",
        "sport" => "Tennis",
        "date" => "July 20, " . date('Y') . " | 4:00 PM",
        "venue" => "Tennis Complex, Bangalore",
        "registration_fee" => 1499,
        "badge" => "Trending"
    ]
];

$displayedTournaments = !empty($dbTournaments) ? $dbTournaments : $defaultEvents;
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link rel="stylesheet" href="assets/css/Home.css?v=7">
<link rel="stylesheet" href="assets/css/Navbar.css?v=2">
<link rel="stylesheet" href="assets/css/pillars.css">

<div class="home-page" id="home">
  <section class="premium-hero">
    <div class="hero-content">
      
    <h1>ENERGEIA'S</h1>
    <h2>
        Global Ventures
      </h2>
      <p class="hero-desc">
        Global Events. Premium Experiences.<br>Meaningful Connections.
      </p>

      <div class="hero-action-buttons flex flex-col sm:flex-row gap-4 sm:gap-6 w-full max-w-lg mb-12">
        <a href="sports-categories.php" class="btn-premium-gold w-full sm:w-auto text-center justify-center">
          🏆 Book Tournaments
        </a>
        <a href="products.php" class="btn-premium-outline w-full sm:w-auto text-center justify-center">
          🛒 Sports Store
        </a>
      </div>

      <!-- Golden Metrics / Counters Bar Inline -->
      <div class="hero-metrics-bar-inline grid grid-cols-2 sm:flex sm:flex-row gap-6 sm:gap-8 justify-start items-center">
        <div class="metric-item">
          <span class="metric-val">4</span>
          <span class="metric-lbl">PILLARS</span>
        </div>
        <div class="metric-item border-l sm:border-l-0 sm:border-r border-[rgba(197,168,92,0.4)] px-4">
          <span class="metric-val">10+</span>
          <span class="metric-lbl">COUNTRIES</span>
        </div>
        <div class="metric-item">
          <span class="metric-val">25+</span>
          <span class="metric-lbl">EVENTS</span>
        </div>
        <div class="metric-item border-l sm:border-l-0 border-[rgba(197,168,92,0.4)] px-4 sm:px-0">
          <span class="metric-val">∞</span>
          <span class="metric-lbl">POSSIBILITIES</span>
        </div>
      </div>
    </div>
  </section>

  <!-- DYNAMIC HOME CAROUSEL -->
  <?php if (!empty($dynamicHomeCarousel)): ?>
  <section class="dynamic-carousel-section" style="padding: 40px 0; background-color: var(--bg-primary);">
    <div class="section-premium-title">
      <h2>FEATURED EVENTS</h2>
    </div>
    
    <div class="destinations-slider-container">
      <button class="slider-control-btn prev" onclick="scrollDynamicCarousel('left')">
        <span class="chevron-icon"><i class="fas fa-chevron-left"></i></span>
      </button>
      
      <div class="destinations-slider" id="dynamic-carousel-slider" style="display: flex; gap: 20px; overflow-x: auto; scroll-behavior: smooth; padding: 20px;">
        <?php foreach ($dynamicHomeCarousel as $evt): ?>
            <?php 
                $catLower = strtolower($evt['category'] ?? '');
                $themeClass = '';
                if ($catLower === 'nexus') $themeClass = 'theme-card-nexus';
                elseif ($catLower === 'maytriya') $themeClass = 'theme-card-maytriya';
                elseif ($catLower === 'gsa') $themeClass = 'theme-card-gsa';
                else $themeClass = 'theme-card-default';
            ?>
            <div class="creative-card <?= $themeClass ?>" style="min-width: 300px; max-width: 350px; background-image: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.8)), url('<?= htmlspecialchars($evt['carousel_img'] ?: $evt['hero_banner']) ?>'); background-size: cover; background-position: center; border-radius: 15px; padding: 20px; color: white; display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <?php if ($evt['category']): ?>
                        <span style="background: rgba(197, 168, 92, 0.9); color: #000; padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: bold;"><?= htmlspecialchars(strtoupper($evt['category'])) ?></span>
                    <?php endif; ?>
                    <h3 style="margin-top: 15px; font-size: 1.5rem; text-transform: uppercase;"><?= htmlspecialchars($evt['title']) ?></h3>
                    <?php if ($evt['country'] || $evt['state']): ?>
                        <p style="color: #c5a85c; font-size: 0.9rem; margin-bottom: 10px;">📍 <?= htmlspecialchars(implode(', ', array_filter([$evt['state'], $evt['country']]))) ?></p>
                    <?php endif; ?>
                    <p style="font-size: 0.9rem; opacity: 0.9; line-height: 1.4;"><?= htmlspecialchars($evt['short_desc']) ?></p>
                </div>
                <div style="margin-top: 20px;">
                    <a href="<?= htmlspecialchars($evt['btn_url'] ?: 'home/events/' . $evt['slug']) ?>" class="btn-premium-gold" style="display: inline-block; padding: 8px 15px; font-size: 0.9rem; border-radius: 5px; text-decoration: none;">
                        <?= htmlspecialchars($evt['btn_text'] ?: 'Explore') ?>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
      </div>

      <button class="slider-control-btn next" onclick="scrollDynamicCarousel('right')">
        <span class="chevron-icon"><i class="fas fa-chevron-right"></i></span>
      </button>
    </div>
  </section>

  <script>
    function scrollDynamicCarousel(direction) {
        const slider = document.getElementById('dynamic-carousel-slider');
        const scrollAmount = 320;
        if (direction === 'left') {
            slider.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
        } else {
            slider.scrollBy({ left: scrollAmount, behavior: 'smooth' });
        }
    }
  </script>

  <?php else: ?>
  <!-- 2. OUR FLAGSHIP EVENTS (STATIC FALLBACK) -->
  <section class="flagship-events-section" id="flagship-events">
    <div class="section-premium-title flagship-title">
      <h2>OUR FLAGSHIP EVENTS</h2>
    </div>

    <div class="premium-events-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      <!-- Event 1 -->
      <a href="event-info.php?event=elite" class="flagship-card">
        <div class="flagship-card-header" style="background-color: #08102b">
          <div class="flagship-header-badge">
            <span>N</span>
          </div>
          <div class="flagship-header-text">
            <h4>NEXUS ELITE</h4>
            <p>BUSINESS SUMMIT</p>
          </div>
        </div>
        <div class="flagship-card-image-box">
          <img src="assets/images/nexas card.png" alt="Nexus Elite" class="flagship-card-img theme-card-dark" />
          <img src="assets/images/nexas card light.png" alt="Nexus Elite" class="flagship-card-img theme-card-light" style="display: none;" />
          <div class="flagship-profiles-row">
            
          </div>
        </div>
        <div class="flagship-card-body-box">
          <p class="flagship-card-desc">A premium business summit connecting leaders, startups, investors & innovators.</p>
          <div class="flagship-know-more-btn">KNOW MORE</div>
        </div>
        <div class="flagship-card-footer">
          <div class="flagship-footer-icon-box">
            <span class="flagship-footer-icon">🏢</span>
          </div>
          <div class="flagship-footer-text">
            <h5>Nexus Elite</h5>
            <p>Business Summit</p>
          </div>
        </div>
      </a>

      <!-- Event 2 -->
      <a href="event-info.php?event=maytriya" class="flagship-card">
        <div class="flagship-card-header" style="background-color: #2a1704">
          <div class="flagship-header-badge">
            <span>M</span>
          </div>
          <div class="flagship-header-text">
            <h4>MAYTRIYA CONNECT</h4>
            <p>LEADERSHIP & FRANCHISE MEET</p>
          </div>
        </div>
        <div class="flagship-card-image-box">
          <img src="assets/images/maytriya card dark.png" alt="Maytriya Connect" class="flagship-card-img theme-card-dark" />
          <img src="assets/images/maytriya light card.png" alt="Maytriya Connect" class="flagship-card-img theme-card-light" style="display: none;" />
        </div>
        <div class="flagship-card-body-box">
          <p class="flagship-card-desc">Curated meet for investors, franchise brands, entrepreneurs & business leaders.</p>
          <div class="flagship-know-more-btn">KNOW MORE</div>
        </div>
        <div class="flagship-card-footer">
          <div class="flagship-footer-icon-box">
            <span class="flagship-footer-icon">🤝</span>
          </div>
          <div class="flagship-footer-text">
            <h5>Maytriya Connect</h5>
            <p>Leadership & Franchise Meet</p>
          </div>
        </div>
      </a>

      <!-- Event 3 -->
      <a href="event-info.php?event=gsa" class="flagship-card">
        <div class="flagship-card-header" style="background-color: #1d042f">
          <div class="flagship-header-badge">
            <span>GSA</span>
          </div>
          <div class="flagship-header-text">
            <h4>GSA</h4>
            <p>GLOBAL SPORTS ARENA</p>
          </div>
        </div>
        <div class="flagship-card-image-box">
          <img src="assets/images/gsa card.png" alt="GSA" class="flagship-card-img theme-card-dark" />
          <img src="assets/images/gsa card light.png" alt="GSA" class="flagship-card-img theme-card-light" style="display: none;" />
        </div>
        <div class="flagship-card-body-box">
          <p class="flagship-card-desc">Where Sports, Tourism & Entertainment come together on a global stage.</p>
          <div class="flagship-know-more-btn">KNOW MORE</div>
        </div>
        <div class="flagship-card-footer">
          <div class="flagship-footer-icon-box">
            <span class="flagship-footer-icon">🏃</span>
          </div>
          <div class="flagship-footer-text">
            <h5>GSA</h5>
            <p>Global Sports Arena</p>
          </div>
        </div>
      </a>
    </div>
  </section>

  <!-- Dynamic Home Event Cards (Admin Managed) -->
  <?php foreach ($homeEventCards as $card): ?>
  <section class="custom-image-banner-section" style="padding: 0 5% 60px; background-color: var(--bg-primary); text-align: center;">
    <a href="<?php echo htmlspecialchars(!empty($card['btn_url']) ? $card['btn_url'] : 'home/events/' . $card['slug']); ?>" style="display: block; max-width: 800px; margin: 0 auto; transition: transform 0.3s ease;" onmouseover="this.style.transform='scale(1.015)';" onmouseout="this.style.transform='scale(1)';">
      <img src="<?php echo htmlspecialchars($card['home_banner_img'] ?: $card['carousel_img'] ?: $card['hero_banner']); ?>" alt="<?php echo htmlspecialchars($card['title']); ?>" class="theme-card-dark" style="width: 100%; height: auto; display: block; border-radius: 30px;" />
      <!-- Assuming we use the same image for light mode for user uploads unless specified otherwise -->
      <img src="<?php echo htmlspecialchars($card['home_banner_img'] ?: $card['carousel_img'] ?: $card['hero_banner']); ?>" alt="<?php echo htmlspecialchars($card['title']); ?>" class="theme-card-light" style="width: 100%; height: auto; display: none; border-radius: 30px;" />
    </a>
  </section>
  <?php endforeach; ?>

  <!-- 3. GLOBAL EVENT DESTINATIONS (CAROUSEL) -->
  <section class="destinations-section" id="destinations">
    <div class="section-premium-title" style="display: flex; flex-direction: column; align-items: center;">
      <span class="title-tagline">Global Chapters</span>
      <h2 id="destinations-title">Overseas Events</h2>
      <div style="display: flex; gap: 10px; margin-top: 15px;">
        <button id="btnHomeIntl" class="dest-filter-btn active" onclick="setHomeDestFilter('international')">International</button>
        <button id="btnHomeNatl" class="dest-filter-btn" onclick="setHomeDestFilter('national')">Indian States</button>
      </div>
      <div class="title-separator" style="margin-top: 20px;"></div>
    </div>

    <div class="destinations-slider-container">
      <button class="slider-control-btn prev" onclick="scrollDestinations('left')">
        <span class="chevron-icon"><i class="fas fa-chevron-left"></i></span>
      </button>
      
      <div 
        class="destinations-slider" 
        id="destinations-slider"
      >
        <!-- Slides will repeat for infinite scroll effect in JavaScript -->
      </div>

      <button class="slider-control-btn next" onclick="scrollDestinations('right')">
        <span class="chevron-icon"><i class="fas fa-chevron-right"></i></span>
      </button>
    </div>
    </div>
  </section>
  <?php endif; // End of Dynamic/Static Switch ?>

  <!-- 4. NXL CREDITS & WALLET WORKFLOW -->
  <section class="nxl-wallet-section">
    <div class="nxl-banner-card flex flex-col lg:flex-row gap-6 p-6 md:p-10">
      <div class="nxl-banner-glow"></div>
      
      <div class="nxl-banner-content">
        <h2>NXL Credits & Digital Wallet</h2>
        <p>
          Earn NXL reward credits automatically on every single tournament registration, sports equipment order, or community referral. Redeem coins instantly at checkout to pay for bookings, or unlock premium loyalty tiers!
        </p>

        <!-- Visual Workflow Steps -->
        <div class="nxl-steps-flow grid grid-cols-2 md:grid-cols-4 gap-4 my-8">
          <div class="nxl-flow-connector hidden md:block"></div>
          
          <div class="nxl-flow-step" onclick="window.location.href='wallet.php'" style="cursor: pointer; transition: transform 0.3s;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
            <div class="nxl-flow-icon-circle"><i class="fas fa-wallet" style="color: #8b6508; text-shadow: 1px 1px 0px rgba(255,255,255,0.7), -1px -1px 0px rgba(0,0,0,0.2);"></i></div>
            <span>Create Wallet</span>
          </div>
          
          <div class="nxl-flow-step" onclick="window.location.href='event-registration.php'" style="cursor: pointer; transition: transform 0.3s;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
            <div class="nxl-flow-icon-circle"><i class="fas fa-gem" style="color: #8b6508; text-shadow: 1px 1px 0px rgba(255,255,255,0.7), -1px -1px 0px rgba(0,0,0,0.2);"></i></div>
            <span>Earn Credits</span>
          </div>
          
          <div class="nxl-flow-step" onclick="window.location.href='products.php'" style="cursor: pointer; transition: transform 0.3s;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
            <div class="nxl-flow-icon-circle"><i class="fas fa-shopping-cart" style="color: #8b6508; text-shadow: 1px 1px 0px rgba(255,255,255,0.7), -1px -1px 0px rgba(0,0,0,0.2);"></i></div>
            <span>Use Credits</span>
          </div>
          
          <div class="nxl-flow-step" onclick="window.location.href='credits.php'" style="cursor: pointer; transition: transform 0.3s;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
            <div class="nxl-flow-icon-circle"><i class="fas fa-gift" style="color: #8b6508; text-shadow: 1px 1px 0px rgba(255,255,255,0.7), -1px -1px 0px rgba(0,0,0,0.2);"></i></div>
            <span>Redeem Rewards</span>
          </div>
        </div>

        <a href="wallet.php" class="btn-premium-gold" style="display:inline-block; text-align:center;">
          💎 Open NXL Wallet
        </a>
      </div>

      <div class="nxl-banner-visual">
        <span class="nxl-coin-sub" style="margin-bottom: 25px; display: block;">Redeem Loyalty Discount</span>
        <div class="nxl-coin-large" style="width: 100px; height: 100px; border-radius: 50%; background: radial-gradient(circle, #fcd036 30%, #dfa019 80%, #b8860b 100%); border: 3px solid #b8860b; box-shadow: inset 0 0 0 4px #ffe76e, inset 0 0 6px 4px rgba(184,134,11,0.5), 0 8px 15px rgba(0,0,0,0.15); display: flex; align-items: center; justify-content: center; font-size: 32px; font-weight: 900; letter-spacing: 2px; color: #8b6508; text-shadow: 1px 1px 0px rgba(255,255,255,0.7), -1px -1px 0px rgba(0,0,0,0.2); margin: 0 auto 1rem auto;">NXL</div>
        <span class="nxl-coin-label">1 Rupee = 1 NXL Credits</span>
        <span class="nxl-coin-sub" style="display: block; font-size: 0.85rem; letter-spacing: 1px; color: #555; margin-top: -5px; margin-bottom: 5px; text-transform: uppercase;">Only in Store Purchase</span>
      </div>
    </div>
  </section>

  <!-- 5. MEMBERSHIP PACKAGES -->
  <section class="membership-section" id="membership">
    <div class="section-premium-title">
      <span class="title-tagline">Exclusive Perks</span>
      <h2>Membership Plans</h2>
      <div class="title-separator"></div>
    </div>

    <div class="membership-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-7xl mx-auto px-4">
<?php
$membershipPlansJson = Settings::get('membership_plans', '{}');
$membershipPlans = json_decode($membershipPlansJson, true) ?: [];

$tierMetadata = [
    'standard' => ['icon' => '🥉', 'class' => ''],
    'premium' => ['icon' => '🥈', 'class' => ' premium-tier'],
    'elite' => ['icon' => '👑', 'class' => '']
];

foreach ($tierMetadata as $tierKey => $meta) {
    if (isset($membershipPlans[$tierKey])) {
        $plan = $membershipPlans[$tierKey];
        $priceFormatted = number_format($plan['price']);
        
        echo '<div class="membership-card' . $meta['class'] . '">';
        echo '<span class="tier-icon">' . $meta['icon'] . '</span>';
        echo '<h3>' . htmlspecialchars($plan['name']) . '</h3>';
        echo '<div class="tier-price">₹' . $priceFormatted . ' <span>/ Year</span></div>';
        echo '<ul class="tier-benefits">';
        foreach ($plan['perks'] as $perk) {
            echo '<li><span class="benefit-bullet">✓</span> ' . htmlspecialchars($perk) . '</li>';
        }
        echo '</ul>';
        echo '<a href="membership-checkout.php?plan=' . $tierKey . '" class="btn-membership-join" style="text-decoration: none; display: inline-block; text-align: center; box-sizing: border-box;">';
        echo 'Join ' . ucfirst($tierKey);
        echo '</a>';
        echo '</div>';
    }
}
?>
    </div>
  </section>

<?php 
  require_once __DIR__ . '/config/Settings.php'; 
  $pillarsTitle = Settings::get('pillars_title', 'Our Five Pillars'); 
?>
  <!-- 7. OUR FIVE PILLARS -->
  <section class="pillars-section" id="about-us">
    <div class="section-premium-title">
      <span class="title-tagline">Core Values</span>
      <h2><?= htmlspecialchars($pillarsTitle) ?></h2>
      <div class="title-separator"></div>
    </div>

    <div class="carousel-container relative w-full max-w-7xl mx-auto px-4">
      <button class="carousel-btn prev-btn" id="pillarPrev" aria-label="Previous">
        <i class="fas fa-chevron-left"></i>
      </button>

      <?php
      require_once __DIR__ . '/config/Settings.php';
      $defaultPillars = [
        [
            "id" => "energeia",
            "title" => "ENERGEIA",
            "icon" => "fas fa-leaf",
            "tags" => ["Energy", "Sustainability", "EV", "Climate Tech"],
            "description" => "Building a sustainable future through clean energy, EV innovation and climate action.",
            "link" => "energeia.php"
        ],
        [
            "id" => "ekonamia",
            "title" => "EKONAMIA",
            "icon" => "fas fa-chart-line",
            "tags" => ["Economy", "Fintech", "Investment", "Trade"],
            "description" => "Empowering global economy through finance, investment, trade and business growth.",
            "link" => "ekonamia.php"
        ],
        [
            "id" => "exploria",
            "title" => "EXPLORIA",
            "icon" => "fas fa-globe-americas",
            "tags" => ["Tourism", "Destinations", "Fintech", "Tech Showcase"],
            "description" => "Exploring the world through tourism, destinations and technology showcases.",
            "link" => "exploria.php"
        ],
        [
            "id" => "evexia",
            "title" => "EVEXIA",
            "icon" => "fas fa-heartbeat",
            "tags" => ["Wellness", "Hospitality", "Lifestyle", "Experiences"],
            "description" => "Enhancing life through wellness, hospitality, lifestyle and memorable experiences.",
            "link" => "evexia.php"
        ],
        [
            "id" => "metroxia",
            "title" => "METROXIA",
            "icon" => "fas fa-city",
            "tags" => ["Urban", "Infrastructure", "Smart City", "Real Estate"],
            "description" => "Building smart, sustainable, and modern urban infrastructure for the future.",
            "link" => "metroxia.php"
        ]
      ];
      $pillars = Settings::get('pillars', $defaultPillars);
      ?>

      <div class="pillar-showcase" id="pillarShowcase">
        <?php foreach ($pillars as $p): 
          $targetLink = !empty($p['link']) ? $p['link'] : $p['id'] . '.php';
          $themeClass = in_array($p['id'], ['energeia', 'ekonamia', 'exploria', 'evexia', 'metroxia']) ? $p['id'] : 'metroxia';
        ?>
          <a href="<?php echo htmlspecialchars($targetLink); ?>" class="creative-card <?php echo htmlspecialchars($themeClass); ?>"<?php if (!empty($p['image'])): ?> style="background-image: linear-gradient(rgba(0,0,0,0.45), rgba(0,0,0,0.65)), url('<?php echo htmlspecialchars($p['image']); ?>');"<?php endif; ?>>
            <div class="card-icon">
              <?php if (!empty($p['logo'])): ?>
                <img src="<?php echo htmlspecialchars($p['logo']); ?>" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
              <?php else: ?>
                <i class="<?php echo htmlspecialchars($p['icon']); ?>"></i>
              <?php endif; ?>
            </div>
            <div class="card-title"><?php echo htmlspecialchars($p['title']); ?></div>
            <div class="card-tags">
              <?php if (!empty($p['tags'])): ?>
                <?php foreach ($p['tags'] as $tag): ?>
                  <span class="card-badge"><?php echo htmlspecialchars($tag); ?></span>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
            <div class="card-description"><?php echo htmlspecialchars($p['description']); ?></div>
          </a>
        <?php endforeach; ?>
      </div>
      
      <button class="carousel-btn next-btn" id="pillarNext" aria-label="Next">
        <i class="fas fa-chevron-right"></i>
      </button>
    </div>

    <script>
      document.addEventListener('DOMContentLoaded', () => {
        const showcase = document.getElementById('pillarShowcase');
        const prevBtn = document.getElementById('pillarPrev');
        const nextBtn = document.getElementById('pillarNext');
        let isPillarPaused = false;

        if (showcase && prevBtn && nextBtn) {
          const scrollAmount = 320; // card width + gap

          // 1. Clone cards for infinite loop effect
          const originalCards = Array.from(showcase.children);
          originalCards.forEach(card => {
            const clone = card.cloneNode(true);
            showcase.appendChild(clone);
          });
          
          // Disable CSS smooth scroll to handle it via JS properly
          showcase.style.scrollBehavior = 'auto';

          // Manual scroll buttons
          prevBtn.addEventListener('click', () => {
            if (showcase.scrollLeft === 0) {
              showcase.scrollLeft = showcase.scrollWidth / 2;
            }
            showcase.style.scrollBehavior = 'smooth';
            showcase.scrollLeft -= scrollAmount;
            setTimeout(() => { showcase.style.scrollBehavior = 'auto'; }, 500);
          });
          
          nextBtn.addEventListener('click', () => {
            showcase.style.scrollBehavior = 'smooth';
            showcase.scrollLeft += scrollAmount;
            setTimeout(() => { showcase.style.scrollBehavior = 'auto'; }, 500);
          });

          // Continuous smooth auto-scroll (marquee effect)
          setInterval(() => {
            if (!isPillarPaused) {
              const halfWidth = showcase.scrollWidth / 2;
              if (halfWidth > 0) {
                if (showcase.scrollLeft >= halfWidth - 1) {
                  showcase.scrollLeft -= halfWidth; // Instantly jump back seamlessly
                } else {
                  showcase.scrollLeft += 1; // Pan by 1 pixel
                }
              }
            }
          }, 20); // 50fps smooth panning

          // Pause on hover or touch
          showcase.addEventListener('mouseenter', () => { isPillarPaused = true; });
          showcase.addEventListener('mouseleave', () => { isPillarPaused = false; });
          showcase.addEventListener('touchstart', () => { isPillarPaused = true; });
          showcase.addEventListener('touchend', () => { isPillarPaused = false; });
        }
      });
    </script>
  </section>

  <!-- 8. OUR PARTNERS -->
  <section class="partners-section" id="partners">
    <div class="section-premium-title">
      <span class="title-tagline">Trusted Networks</span>
      <h2>Our Partners</h2>
      <div class="title-separator"></div>
    </div>

    <div class="partners-slider-container">
      <button class="slider-control-btn prev" onclick="scrollPartners('left')">
        <span class="chevron-icon"><i class="fas fa-chevron-left"></i></span>
      </button>
      
      <div 
        class="partners-slider" 
        id="partners-slider"
      >
        <!-- Partner slides repeated in JS -->
      </div>

      <button class="slider-control-btn next" onclick="scrollPartners('right')">
        <span class="chevron-icon"><i class="fas fa-chevron-right"></i></span>
      </button>
    </div>
  </section>

  <!-- 11. BLOG SECTION -->
  <section class="blog-section" id="blog" style="padding: 4rem 5%; background: #0b0c10; border-top: 1px solid rgba(197, 168, 92, 0.15)">
    <div class="section-premium-title">
      <span class="title-tagline">Insights & News</span>
      <h2>📰 Sports Blog</h2>
      <div class="title-separator"></div>
    </div>
    <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 25px; margin-top: 30px;">
      <?php if (!empty($dynamicBlogs)): ?>
        <?php foreach ($dynamicBlogs as $blog): ?>
          <div class="blog-post-card" style="flex: 1 1 320px; max-width: 380px; box-sizing: border-box;" onclick="window.location.href='<?php echo htmlspecialchars($blog['link'] ?? '#'); ?>'">
            <div class="blog-card-img-wrap">
              <img src="<?php echo htmlspecialchars($blog['image']); ?>" alt="<?php echo htmlspecialchars($blog['title']); ?>" class="blog-card-img" />
            </div>
            <span style="color: #c5a85c; font-size: 0.8rem; font-weight: bold;">⚡ <?php echo htmlspecialchars($blog['category']); ?> • <?php echo htmlspecialchars($blog['date_published']); ?></span>
            <h3 style="margin: 10px 0; color: #f5f6fa"><?php echo htmlspecialchars($blog['title']); ?></h3>
            <p style="color: #9aa0b4; font-size: 0.9rem; line-height: 1.5"><?php echo htmlspecialchars($blog['excerpt']); ?></p>
            <a href="<?php echo htmlspecialchars($blog['link'] ?? '#'); ?>" class="read-more-link" style="text-decoration: none;">Read Article →</a>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p style="color: #9aa0b4; grid-column: 1 / -1; text-align: center;">New insights and articles coming soon.</p>
      <?php endif; ?>
    </div>
  </section>

  <!-- 9. DYNAMIC TOURNAMENTS REGISTER SECTION -->
  <section class="events-section" id="active-tournaments" style="background: #0b0c10; border-top: 1px solid rgba(197, 168, 92, 0.15)">
    <div class="section-premium-title">
      <span class="title-tagline">Reserve Your Slot</span>
      <h2>📅 Active Sports Tournaments</h2>
      <div class="title-separator"></div>
    </div>

    <div class="events-grid">
      <?php foreach ($displayedTournaments as $tournament): ?>
        <?php
          // Handle DB column names vs default array keys
          $tId = $tournament['id'];
          $tName = $tournament['title'] ?? $tournament['name'] ?? $tournament['tournament_name'] ?? 'Tournament';
          $tSport = $tournament['category'] ?? $tournament['sport'] ?? $tournament['sport_category'] ?? 'Sports';
          $tVenue = !empty($tournament['location']) ? $tournament['location'] : (!empty($tournament['state']) ? $tournament['state'] : (!empty($tournament['gala_venue']) ? $tournament['gala_venue'] : 'Arena'));
          $tDate = !empty($tournament['event_date']) ? $tournament['event_date'] : (!empty($tournament['date']) ? $tournament['date'] : 'Scheduled');
          
          $tFee = 0;
          if (!empty($tournament['sports_data'])) {
              $sportsArr = json_decode($tournament['sports_data'], true);
              if (is_array($sportsArr)) {
                  foreach ($sportsArr as $s) {
                      if (!empty($s['prize'])) {
                          $val = str_replace([',', ' '], '', $s['prize']);
                          if (is_numeric($val)) $tFee += (float)$val;
                      }
                  }
              }
          }
          if ($tFee == 0) {
              $tFeeStr = !empty($tournament['delegate_fee']) ? $tournament['delegate_fee'] : 'TBA';
          } else {
              $tFeeStr = '₹' . number_format($tFee);
          }
          
          $tBadge = !empty($tournament['badge_text']) ? $tournament['badge_text'] : ($tournament['badge'] ?? (($tournament['status'] ?? '') === 'published' ? 'Live Event' : 'Upcoming'));
          $slug = $tournament['slug'] ?? '';
          $targetUrl = !empty($slug) ? "home/events/" . urlencode($slug) : "register.php";
        ?>
        <div class="event-card" onclick="window.location.href='<?= htmlspecialchars($targetUrl) ?>'" style="background: rgba(22, 24, 38, 0.95); cursor: pointer;">
          <div class="event-image">🏆</div>
          <div class="event-badge" style="text-transform: uppercase;"><?php echo htmlspecialchars($tBadge); ?></div>
          <h3><?php echo htmlspecialchars($tName); ?></h3>
          
          <div class="event-details">
            <p class="event-date">
              <span class="event-icon">📅</span>
              <?php echo htmlspecialchars($tDate); ?>
            </p>
            <p class="event-location">
              <span class="event-icon">📍</span>
              <?php echo htmlspecialchars($tVenue); ?>
            </p>
            <p class="event-price" style="color: #c5a85c; font-weight: bold;">
              <span class="event-icon">💰</span>
              Pool: <?php echo htmlspecialchars($tFeeStr); ?>
            </p>
          </div>

          <button class="book-btn">
            View Details <span>→</span>
          </button>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
</div>

<script>
  // Destinations array
  const defaultDestinations = [
    { id: 2, country: 'SINGAPORE', city: 'Marina Bay', region: 'Central Core', image: 'https://images.unsplash.com/photo-1525625293386-3f8f99389edd?w=800&q=80', link: '#', date: 'Oct 2026', type: 'international' },
    { id: 4, country: 'DUBAI', city: 'Downtown', region: 'UAE', image: 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=800&q=80', link: '#', date: 'Nov 2026', type: 'international' },
    { id: 13, country: 'LONDON', city: 'Wembley', region: 'UK', image: 'https://images.unsplash.com/photo-1529655683826-aba9b3e77383?w=800&q=80', link: '#', date: 'Dec 2026', type: 'international' },
    { id: 7, country: 'NEW YORK', city: 'Manhattan', region: 'USA', image: 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTfwahV5yF4az000LrH5UPZhBV23NNv9LTd7penHXR4ew&s=10', link: '#', date: 'Jan 2027', type: 'international' },
    { id: 20, country: 'PARIS', city: 'Stade de France', region: 'France', image: 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRPMDDK1BX_jKbra9wFsdY-d_MVNa7qrdH0i4enN_7fUg&s=10', link: '#', date: 'Feb 2027', type: 'international' },
    { id: 21, country: 'TOKYO', city: 'Shinjuku', region: 'Japan', image: 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=800&q=80', link: '#', date: 'Mar 2027', type: 'international' },
    { id: 22, country: 'SYDNEY', city: 'Opera House', region: 'Australia', image: 'https://images.unsplash.com/photo-1506973035872-a4ec16b8e8d9?w=800&q=80', link: '#', date: 'Apr 2027', type: 'international' },
    { id: 23, country: 'ROME', city: 'Colosseum', region: 'Italy', image: 'https://images.unsplash.com/photo-1552832230-c0197dd311b5?w=800&q=80', link: '#', date: 'May 2027', type: 'international' },
    { id: 24, country: 'BARCELONA', city: 'Camp Nou', region: 'Spain', image: 'https://images.unsplash.com/photo-1583422409516-2895a77efded?w=800&q=80', link: '#', date: 'Jun 2027', type: 'international' },
    { id: 25, country: 'AMSTERDAM', city: 'Dam Square', region: 'Netherlands', image: 'https://images.unsplash.com/photo-1512470876302-972faa2aa9a4?w=800&q=80', link: '#', date: 'Jul 2027', type: 'international' },
    { id: 26, country: 'BERLIN', city: 'Mitte', region: 'Germany', image: 'https://images.unsplash.com/photo-1560969184-10fe8719e047?w=800&q=80', link: '#', date: 'Aug 2027', type: 'international' },
    { id: 27, country: 'CAPE TOWN', city: 'Table Mountain', region: 'South Africa', image: 'https://images.unsplash.com/photo-1580060839134-75a5edca2e99?w=800&q=80', link: '#', date: 'Sep 2027', type: 'international' },
    { id: 28, country: 'RIO DE JANEIRO', city: 'Copacabana', region: 'Brazil', image: 'https://images.unsplash.com/photo-1483729558449-99ef09a8c325?w=800&q=80', link: '#', date: 'Oct 2027', type: 'international' }
  ];
  
  const dynamicCardsFromDB = <?php echo json_encode($dynamicCards); ?>;
  
  // Update links for existing default destinations AND push new ones from DB
  const defaultNationalDestinations = [
    { id: 101, country: 'MUMBAI', city: 'Bandra', region: 'Maharashtra', image: 'https://images.unsplash.com/photo-1529253355930-ddbe423a2ac7?w=800&q=80', link: '#', date: 'Oct 2026', type: 'national' },
    { id: 103, country: 'DELHI', city: 'CP', region: 'Delhi', image: 'https://images.unsplash.com/photo-1587474260584-136574528ed5?w=800&q=80', link: '#', date: 'Nov 2026', type: 'national' },
    { id: 102, country: 'BANGALORE', city: 'Whitefield', region: 'Karnataka', image: 'https://images.unsplash.com/photo-1596176530529-78163a4f7af2?w=800&q=80', link: '#', date: 'Dec 2026', type: 'national' },
    { id: 104, country: 'GOA', city: 'Panaji', region: 'Goa', image: 'https://images.unsplash.com/photo-1512343879784-a960bf40e7f2?w=800&q=80', link: '#', date: 'Jan 2027', type: 'national' },
    { id: 105, country: 'KERALA', city: 'Kochi', region: 'Kerala', image: 'https://images.unsplash.com/photo-1602216056096-3b40cc0c9944?w=800&q=80', link: '#', date: 'Feb 2027', type: 'national' },
    { id: 106, country: 'RAJASTHAN', city: 'Jaipur', region: 'Rajasthan', image: 'https://images.unsplash.com/photo-1477587458883-47145ed94245?w=800&q=80', link: '#', date: 'Mar 2027', type: 'national' },
    { id: 107, country: 'GUJARAT', city: 'Ahmedabad', region: 'Gujarat', image: 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a5/Rani_ki_vav_02.jpg/960px-Rani_ki_vav_02.jpg', link: '#', date: 'Apr 2027', type: 'national' },
    { id: 108, country: 'TAMIL NADU', city: 'Chennai', region: 'Tamil Nadu', image: 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/f2/Mamallapuram_view.jpg/960px-Mamallapuram_view.jpg', link: '#', date: 'May 2027', type: 'national' },
    { id: 109, country: 'PUNJAB', city: 'Amritsar', region: 'Punjab', image: 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d7/Golden_Temple_India.jpg/800px-Golden_Temple_India.jpg', link: '#', date: 'Jun 2027', type: 'national' },
    { id: 110, country: 'WEST BENGAL', city: 'Kolkata', region: 'West Bengal', image: 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e5/Howrah_bridge_betwixt_Lights.jpg/960px-Howrah_bridge_betwixt_Lights.jpg', link: '#', date: 'Jul 2027', type: 'national' },
    { id: 111, country: 'TELANGANA', city: 'Hyderabad', region: 'Telangana', image: 'https://upload.wikimedia.org/wikipedia/commons/f/f7/A_typical_charminar_evening.jpg', link: '#', date: 'Aug 2027', type: 'national' },
    { id: 112, country: 'MADHYA PRADESH', city: 'Indore', region: 'Madhya Pradesh', image: 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/68/India-5749_-_Visvanatha_Temple_-_Flickr_-_archer10_%28Dennis%29.jpg/960px-India-5749_-_Visvanatha_Temple_-_Flickr_-_archer10_%28Dennis%29.jpg', link: '#', date: 'Sep 2027', type: 'national' },
    { id: 113, country: 'UTTARAKHAND', city: 'Dehradun', region: 'Uttarakhand', image: 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/ce/Nanda_Devi_-_Hidden_Summit%2C_Uttarakhand_India_2013.jpg/960px-Nanda_Devi_-_Hidden_Summit%2C_Uttarakhand_India_2013.jpg', link: '#', date: 'Oct 2027', type: 'national' },
    { id: 114, country: 'HIMACHAL PRADESH', city: 'Shimla', region: 'Himachal Pradesh', image: 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/80/Kullu_Valley_near_Manali%2C_Himachal_Pradesh%2C_India.jpg/960px-Kullu_Valley_near_Manali%2C_Himachal_Pradesh%2C_India.jpg', link: '#', date: 'Nov 2027', type: 'national' }
  ];

  dynamicCardsFromDB.forEach(card => {
      if (!card.country_or_state) return;
      const targetCountry = card.country_or_state.toUpperCase();
      
      if (card.type === 'international') {
          const match = defaultDestinations.find(dest => dest.country === targetCountry || dest.region.toUpperCase() === targetCountry);
          if (match) {
              if (card.link) match.link = card.link;
              if (card.image) match.image = card.image;
              if (card.event_title) match.city = card.event_title;
          } else {
              defaultDestinations.push({
                  id: 'dyn_intl_' + card.id,
                  country: targetCountry,
                  city: card.city || targetCountry,
                  region: targetCountry,
                  image: card.image || '',
                  link: card.link || '#',
                  date: card.date || '',
                  type: 'international'
              });
          }
      } else {
          const match = defaultNationalDestinations.find(dest => dest.country === targetCountry || dest.region.toUpperCase() === targetCountry);
          if (match) {
              if (card.link) match.link = card.link;
              if (card.image) match.image = card.image;
              if (card.event_title) match.city = card.event_title;
          } else {
              defaultNationalDestinations.push({
                  id: 'dyn_nat_' + card.id,
                  country: targetCountry,
                  city: card.city || targetCountry,
                  region: targetCountry,
                  image: card.image || '',
                  link: card.link || '#',
                  date: card.date || '',
                  type: 'national'
              });
          }
      }
  });

// Partners array — always driven by DB (seeded with defaults on first visit)
<?php
$emojiMap = [
    'TATA GROUP'  => '👔',
    'INFOSYS'     => '💻',
    'HDFC BANK'   => '🏦',
    'GOOGLE'      => '🔍',
    'BOOKMYSHOW'  => '🎟️',
    'DECATHLON'   => '👟',
    'KRAFTON'     => '🎮',
];
if (!empty($dbPartners)) {
    $jsPartners = array_map(function($p) use ($emojiMap) {
        $icon = $p['logo_url'] ?: ($emojiMap[strtoupper(trim($p['name']))] ?? '🤝');
        return [
            'id'   => (int)$p['id'],
            'name' => strtoupper($p['name']),
            'icon' => $icon,
            'link' => $p['website_url'] ?: '#',
        ];
    }, $dbPartners);
} else {
    $jsPartners = [
        ['id'=>1,'name'=>'TATA GROUP','icon'=>'👔','link'=>'https://www.tata.com'],
        ['id'=>2,'name'=>'INFOSYS','icon'=>'💻','link'=>'https://www.infosys.com'],
        ['id'=>3,'name'=>'HDFC BANK','icon'=>'🏦','link'=>'https://www.hdfcbank.com'],
        ['id'=>4,'name'=>'GOOGLE','icon'=>'🔍','link'=>'https://www.google.com'],
        ['id'=>5,'name'=>'BOOKMYSHOW','icon'=>'🎟️','link'=>'https://in.bookmyshow.com'],
        ['id'=>6,'name'=>'DECATHLON','icon'=>'👟','link'=>'https://www.decathlon.in'],
        ['id'=>7,'name'=>'KRAFTON','icon'=>'🎮','link'=>'#'],
    ];
}
?>
const partnersList = <?= json_encode($jsPartners) ?>;


let isPaused = false;
let isPartnersPaused = false;

let homeDestFilter = 'international';

function setHomeDestFilter(type) {
    homeDestFilter = type;
    document.getElementById("btnHomeIntl").classList.toggle('active', type === 'international');
    document.getElementById("btnHomeNatl").classList.toggle('active', type === 'national');
    
    document.getElementById("destinations-title").textContent = type === 'international' ? 'Overseas Events' : 'Indian States Events';
    
    renderDestinations();
}

// Render destinations list
function renderDestinations() {
    const slider = document.getElementById("destinations-slider");
    if (!slider) return;

    let finalDest = [...defaultDestinations, ...defaultNationalDestinations];
    
    // Also include destinations from API (custom_destinations table)
    if (window.apiDestinations && window.apiDestinations.length > 0) {
        window.apiDestinations.forEach(apiDest => {
            if (!apiDest.deleted) {
                // Ensure no duplicates by ID
                if (!finalDest.some(d => d.id == apiDest.id)) {
                    finalDest.push(apiDest);
                }
            }
        });
    }

    // Filter by 'national' or 'international' depending on current tab
    finalDest = finalDest.filter(d => {
        let type = d.type || (d.country.toUpperCase() === 'INDIA' ? 'national' : 'international');
        type = type.toLowerCase();
        
        // Normalize legacy types
        if (type === 'sports') type = 'international';
        if (type === 'overseas' || type === 'international event' || type === 'overseas event') type = 'international';
        if (type === 'state' || type === 'indian state event') type = 'national';
        
        return type === homeDestFilter;
    });
    
    if (finalDest.length === 0) {
        slider.innerHTML = `<p style="color: #9aa0b4; padding: 20px;">No events found for this category.</p>`;
        return;
    }
    
    const originalCount = finalDest.length;
    // Duplicate 4 times to ensure enough width for 4K/Ultrawide screens to loop seamlessly
    const displayList = [...finalDest, ...finalDest, ...finalDest, ...finalDest];
    
    slider.innerHTML = displayList.map((dest, idx) => {
        let targetLink = dest.link && dest.link !== "#" ? dest.link : `destination-detail.php?id=${dest.id}`;
        let targetAttr = '';

        return `
          <a href="${targetLink}" ${targetAttr} class="destination-card" onclick="${targetLink === '#' ? 'event.preventDefault()' : ''}">
            <div class="destination-image-box">
              <img src="${dest.image}" alt="${dest.country}" class="destination-image" />
              <div class="destination-flag-overlay">${dest.country}</div>
            </div>
            <div class="destination-body" style="display: flex; flex-direction: column; align-items: center; text-align: center; gap: 1rem; justify-content: space-between;">
              <p style="color: var(--text-secondary); font-size: 0.85rem; line-height: 1.4; margin: 0;">Where Sports, Tourism & Entertainment come together on a global stage.</p>
              <button class="nexus-btn" style="pointer-events: none; padding: 0.6rem 1.2rem; font-size: 0.75rem;">KNOW MORE</button>
              <div style="width: 100%; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 1rem; display: flex; flex-direction: column; gap: 0.5rem; text-align: left;">
                <div class="destination-detail-row">
                  <span class="destination-icon">📅</span> ${dest.date}
                </div>
                <div class="destination-detail-row">
                  <span class="destination-icon">📍</span> ${dest.city}
                </div>
                <div class="destination-detail-row">
                  <span class="destination-icon">📍</span> ${dest.region}
                </div>
              </div>
            </div>
          </a>
        `;
    }).join('');
}

// Render partners
function renderPartners() {
    const slider = document.getElementById("partners-slider");
    if (!slider) return;

    let customPart = [];
    try {
        customPart = JSON.parse(localStorage.getItem("globalsportsarena_custom_partners") || "[]");
    } catch(e) {}

    let mergedAll = partnersList.map(p => {
        const customOverride = customPart.find(c => c.id === p.id);
        return customOverride ? customOverride : p;
    });
    const purelyNew = customPart.filter(c => !partnersList.some(p => p.id === c.id));
    
    let finalPartners = [...mergedAll, ...purelyNew].filter(p => !p.deleted);

    const displayList = [...finalPartners, ...finalPartners];
    slider.innerHTML = displayList.map((partner, idx) => {
        return `
          <a href="${partner.link}" target="_blank" class="partner-card" onclick="${partner.link === '#' ? 'event.preventDefault()' : ''}">
            <span class="partner-card-icon" style="display: flex; justify-content: center; align-items: center; width: 100%; height: 100%;">
                ${(partner.icon.startsWith('data:image') || partner.icon.startsWith('http') || partner.icon.includes('/')) ? `<img src="${partner.icon}" style="max-width: 100%; max-height: 100%; object-fit: contain;">` : partner.icon}
            </span>
            <span class="partner-card-name">${partner.name}</span>
          </a>
        `;
    }).join('');
}

function scrollDestinations(direction) {
    const slider = document.getElementById("destinations-slider");
    if (slider) {
        slider.style.scrollBehavior = "smooth";
        const scrollAmount = 300;
        slider.scrollLeft += direction === "left" ? -scrollAmount : scrollAmount;
        setTimeout(() => {
            if (slider) slider.style.scrollBehavior = "auto";
        }, 500);
    }
}

function scrollPartners(direction) {
    const slider = document.getElementById("partners-slider");
    if (slider) {
        slider.style.scrollBehavior = "smooth";
        const scrollAmount = 250;
        slider.scrollLeft += direction === "left" ? -scrollAmount : scrollAmount;
        setTimeout(() => {
            if (slider) slider.style.scrollBehavior = "auto";
        }, 500);
    }
}

function handleJoinMembership(tierName, price) {
    const isLoggedIn = !!localStorage.getItem("token");
    if (!isLoggedIn) {
        window.location.href = "register.php";
    } else {
        const membershipOrder = {
            price: price,
            discountAmount: 0,
            nxlCoinsUsed: 0,
            deliveryFee: 0,
            total: price,
            nxlCoinsEarned: Math.round(price * 0.10), // 10% cashback in NXL Credits
            items: [{
                id: "membership-" + tierName.toLowerCase().replace(" ", "-"),
                name: tierName + " Subscription (1 Year)",
                price: price,
                quantity: 1
            }]
        };
        // Save to localStorage temporarily to retrieve on checkout
        localStorage.setItem("gsa_membership_order", JSON.stringify(membershipOrder));
        window.location.href = "checkout.php?type=membership";
    }
}

// Load destinations from API on page load
window.apiDestinations = [];
document.addEventListener("DOMContentLoaded", function() {
    fetch('api/index.php/destinations')
        .then(res => res.json())
        .then(data => {
            if (Array.isArray(data)) {
                window.apiDestinations = data;
            }
            renderDestinations();
        })
        .catch(err => {
            console.error("Failed to load custom destinations", err);
            renderDestinations();
        });
        
    setHomeDestFilter('international');
    renderPartners();

    // Auto scroll destinations
    const destSlider = document.getElementById("destinations-slider");
    if (destSlider) {
        destSlider.addEventListener("mouseenter", () => { isPaused = true; });
        destSlider.addEventListener("mouseleave", () => { isPaused = false; });
        
        setInterval(() => {
            if (!isPaused) {
                const segmentWidth = destSlider.scrollWidth / 4;
                if (segmentWidth > 0) {
                    if (destSlider.scrollLeft >= segmentWidth - 1) {
                        destSlider.scrollLeft -= segmentWidth;
                    } else {
                        destSlider.scrollLeft += 1;
                    }
                }
            }
        }, 20);
    }

    // Auto scroll partners
    const partSlider = document.getElementById("partners-slider");
    if (partSlider) {
        partSlider.addEventListener("mouseenter", () => { isPartnersPaused = true; });
        partSlider.addEventListener("mouseleave", () => { isPartnersPaused = false; });
        
        setInterval(() => {
            if (!isPartnersPaused) {
                const halfWidth = partSlider.scrollWidth / 2;
                if (halfWidth > 0) {
                    if (partSlider.scrollLeft >= halfWidth - 1) {
                        partSlider.scrollLeft -= halfWidth;
                    } else {
                        partSlider.scrollLeft += 1;
                    }
                }
            }
        }, 20);
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
