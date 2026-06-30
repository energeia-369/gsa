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
try {
    $dbConn = Database::getConnection();
    
    // Fetch dynamic event cards from the new isolated table
    $cardsStmt = $dbConn->query("SELECT * FROM home_carousel_events WHERE status = 'active' ORDER BY id ASC");
    $dbCarouselCards = $cardsStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch custom full-width image banners from home_event_cards
    $stmtEventCards = $dbConn->query("SELECT * FROM home_event_cards WHERE status = 'active' ORDER BY id DESC");
    $homeEventCards = $stmtEventCards->fetchAll(PDO::FETCH_ASSOC);

    
    $dynamicCards = [];
    foreach ($dbCarouselCards as $c) {
        $link = ($c['dynamic_page_enabled'] == 1) ? 'home-event.php?slug=' . $c['slug'] : $c['button_link'];
        
        $type = (strtolower(trim($c['country'])) === 'india' || in_array(strtolower(trim($c['country'])), ['maharashtra', 'karnataka', 'tamil nadu', 'delhi', 'goa', 'kerala', 'rajasthan', 'gujarat', 'pune'])) ? 'national' : 'international';

        $dynamicCards[] = [
            'id' => (int)$c['id'],
            'event_title' => $c['title'], // For the custom banners
            'image' => $c['thumbnail'],
            'country' => strtoupper($c['country']),
            'city' => $c['location'],
            'date' => $c['event_date'],
            'link' => $link,
            'type' => $type,
            'country_or_state' => $c['country'] // Legacy JS compatibility
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
<link rel="stylesheet" href="assets/css/Home.css?v=14">
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

  <!-- 2. OUR FLAGSHIP EVENTS -->
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
            <h4>MAYTRIYA MEET</h4>
            <p>LEADERSHIP & FRANCHISE SUMMIT</p>
          </div>
        </div>
        <div class="flagship-card-image-box">
          <img src="assets/images/maytriya card dark.png" alt="Maytriya Meet" class="flagship-card-img theme-card-dark" />
          <img src="assets/images/maytriya light card.png" alt="Maytriya Meet" class="flagship-card-img theme-card-light" style="display: none;" />
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
            <h5>Maytriya Meet</h5>
            <p>Leadership & Franchise Summit</p>
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
    <a href="<?php echo htmlspecialchars(!empty($card['link']) ? $card['link'] : '#'); ?>" style="display: block; max-width: 800px; margin: 0 auto; transition: transform 0.3s ease;" onmouseover="this.style.transform='scale(1.015)';" onmouseout="this.style.transform='scale(1)';">
      <img src="<?php echo htmlspecialchars($card['image']); ?>" alt="<?php echo htmlspecialchars($card['event_title']); ?>" class="theme-card-dark" style="width: 100%; height: auto; display: block; border-radius: 30px;" />
      <!-- Assuming we use the same image for light mode for user uploads unless specified otherwise -->
      <img src="<?php echo htmlspecialchars($card['image']); ?>" alt="<?php echo htmlspecialchars($card['event_title']); ?>" class="theme-card-light" style="width: 100%; height: auto; display: none; border-radius: 30px;" />
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
        <span class="chevron-icon">◀</span>
      </button>
      
      <div 
        class="destinations-slider" 
        id="destinations-slider"
      >
        <!-- Slides will repeat for infinite scroll effect in JavaScript -->
      </div>

      <button class="slider-control-btn next" onclick="scrollDestinations('right')">
        <span class="chevron-icon">▶</span>
      </button>
    </div>
  </section>

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
            <div class="nxl-flow-icon-circle">👛</div>
            <span>Create Wallet</span>
          </div>
          
          <div class="nxl-flow-step" onclick="window.location.href='event-registration.php'" style="cursor: pointer; transition: transform 0.3s;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
            <div class="nxl-flow-icon-circle">💎</div>
            <span>Earn Credits</span>
          </div>
          
          <div class="nxl-flow-step" onclick="window.location.href='products.php'" style="cursor: pointer; transition: transform 0.3s;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
            <div class="nxl-flow-icon-circle">🛒</div>
            <span>Use Credits</span>
          </div>
          
          <div class="nxl-flow-step" onclick="window.location.href='credits.php'" style="cursor: pointer; transition: transform 0.3s;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
            <div class="nxl-flow-icon-circle">🎁</div>
            <span>Redeem Rewards</span>
          </div>
        </div>

        <a href="wallet.php" class="btn-premium-gold" style="display:inline-block; text-align:center;">
          💎 Open NXL Wallet
        </a>
      </div>

      <div class="nxl-banner-visual">
        <span class="nxl-coin-sub" style="margin-bottom: 5px; display: block;">Redeem Loyalty Discount</span>
        <span class="nxl-coin-large">💎</span>
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
        <span class="chevron-icon">◀</span>
      </button>
      
      <div 
        class="partners-slider" 
        id="partners-slider"
      >
        <!-- Partner slides repeated in JS -->
      </div>

      <button class="slider-control-btn next" onclick="scrollPartners('right')">
        <span class="chevron-icon">▶</span>
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
          $tName = $tournament['name'] ?? $tournament['tournament_name'] ?? 'Tournament';
          $tSport = $tournament['sport'] ?? $tournament['sport_name'] ?? 'Sports';
          $tVenue = $tournament['venue'] ?? $tournament['location'] ?? 'Arena';
          $tDate = $tournament['date'] ?? $tournament['event_date'] ?? 'Scheduled';
          $tFee = $tournament['registration_fee'] ?? $tournament['entry_fee'] ?? 0;
          $tBadge = $tournament['badge'] ?? 'Live Pool';
        ?>
        <div class="event-card" onclick="window.location.href='event-registration.php'" style="background: rgba(22, 24, 38, 0.95); cursor: pointer;">
          <div class="event-image">🏆</div>
          <div class="event-badge"><?php echo htmlspecialchars($tBadge); ?></div>
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
              Fee: ₹<?php echo htmlspecialchars($tFee); ?>
            </p>
          </div>

          <button class="book-btn">
            Book Tournament Now <span>→</span>
          </button>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
</div>

<script>
// Destinations array
const defaultDestinations = [
    { id: 1, country: "INDIA", image: "https://images.unsplash.com/photo-1564507592333-c60657eea523?w=500&auto=format&fit=crop&q=60", date: "24-26 July 2026", city: "Pune / Mumbai", region: "India", link: "gsa-pune-2026.php" },
    { id: 2, country: "SINGAPORE", image: "https://images.unsplash.com/photo-1525625293386-3f8f99389edd?w=500&auto=format&fit=crop&q=60", date: "18-20 Sept 2026", city: "Singapore", region: "Singapore", link: "#" },
    { id: 3, country: "SWITZERLAND", image: "https://images.unsplash.com/photo-1506744038136-46273834b3fb?w=500&auto=format&fit=crop&q=60", date: "May - Sep", city: "Zurich", region: "Switzerland", link: "#" },
    { id: 4, country: "UAE", image: "https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=500&auto=format&fit=crop&q=60", date: "23-25 Oct 2026", city: "Dubai / Abu Dhabi", region: "UAE", link: "#" },
    { id: 5, country: "THAILAND", image: "https://images.unsplash.com/photo-1508009603885-50cf7c579365?w=500&auto=format&fit=crop&q=60", date: "18-20 Dec 2026", city: "Phuket / Bangkok", region: "Thailand", link: "#" },
    { id: 6, country: "USA - LAS VEGAS", image: "https://images.unsplash.com/photo-1501183007986-d0d080b147f9?w=500&auto=format&fit=crop&q=60", date: "23-25 July 2026", city: "Las Vegas", region: "USA", link: "#" },
    { id: 7, country: "USA - NEW YORK", image: "https://images.unsplash.com/photo-1526304640581-d334cdbbf45e?w=500&auto=format&fit=crop&q=60", date: "23-25 July 2026", city: "New York", region: "USA", link: "#" },
    { id: 8, country: "MALAYSIA", image: "https://images.unsplash.com/photo-1596422846543-75c6fc197f07?w=500&auto=format&fit=crop&q=60", date: "20-22 Nov 2026", city: "Kuala Lumpur", region: "Malaysia", link: "#" },
    { id: 9, country: "INDONESIA", image: "https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=500&auto=format&fit=crop&q=60", date: "22-24 Jan 2026", city: "Bali / Jakarta", region: "Indonesia", link: "#" },
    { id: 10, country: "VIETNAM", image: "https://images.unsplash.com/photo-1528127269322-539801943592?w=500&auto=format&fit=crop&q=60", date: "19-21 Feb 2026", city: "Ho Chi Minh", region: "Vietnam", link: "#" },
    { id: 11, country: "AUSTRALIA", image: "https://images.unsplash.com/photo-1523482580672-f109ba8cb9be?w=500&auto=format&fit=crop&q=60", date: "19-21 March 2026", city: "Sydney", region: "Australia", link: "#" },
    { id: 12, country: "GERMANY", image: "https://images.unsplash.com/photo-1467269204594-9661b134dd2b?w=500&auto=format&fit=crop&q=60", date: "23-25 April 2026", city: "Berlin", region: "Germany", link: "#" },
    { id: 13, country: "UNITED KINGDOM", image: "https://images.unsplash.com/photo-1505761671935-60b3a7427bad?w=500&auto=format&fit=crop&q=60", date: "21-23 May 2026", city: "London", region: "UK", link: "#" },
    { id: 14, country: "CANADA", image: "https://images.unsplash.com/photo-1503614472-8c93d56e92ce?w=500&auto=format&fit=crop&q=60", date: "18-20 June 2026", city: "Toronto", region: "Canada", link: "#" }
];

const dynamicCardsFromDB = <?php echo json_encode($dynamicCards); ?>;

// Update the links in defaultDestinations if an admin set a custom link
defaultDestinations.forEach(dest => {
    const match = dynamicCardsFromDB.find(card => card.country_or_state.toUpperCase() === dest.country);
    if (match && match.link) {
        dest.link = match.link;
    }
});

const defaultNationalDestinations = [
    { id: 108, country: "TAMIL NADU", image: "https://images.unsplash.com/photo-1582510003544-4d00b7f74220?w=500&auto=format&fit=crop&q=60", date: "July - Aug 2026", city: "Coimbatore", region: "Tamil Nadu", type: "national", link: "#" },
    { id: 109, country: "PUNE", image: "https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=500&auto=format&fit=crop&q=60", date: "Oct 2026", city: "Pune", region: "Pune", type: "national", link: "gsa-pune-2026.php" },
    { id: 101, country: "MAHARASHTRA", image: "https://images.unsplash.com/photo-1570168007204-dfb528c6958f?w=500&auto=format&fit=crop&q=60", date: "10-12 Aug 2026", city: "Mumbai / Pune", region: "India", type: "national", link: "gsa-pune-2026.php" },
    { id: 102, country: "KARNATAKA", image: "https://images.unsplash.com/photo-1596176530529-78163a4f7af2?w=500&auto=format&fit=crop&q=60", date: "15-17 Sept 2026", city: "Bangalore", region: "India", type: "national", link: "#" },
    { id: 103, country: "DELHI", image: "https://images.unsplash.com/photo-1587474260584-136574528ed5?w=500&auto=format&fit=crop&q=60", date: "05-07 Oct 2026", city: "New Delhi", region: "India", type: "national", link: "#" },
    { id: 104, country: "GOA", image: "https://images.unsplash.com/photo-1512343879784-a960bf40e7f2?w=500&auto=format&fit=crop&q=60", date: "20-22 Nov 2026", city: "Panaji", region: "India", type: "national", link: "#" },
    { id: 105, country: "KERALA", image: "https://images.unsplash.com/photo-1602216056096-3b40cc0c9944?w=500&auto=format&fit=crop&q=60", date: "12-14 Dec 2026", city: "Kochi", region: "India", type: "national", link: "#" },
    { id: 106, country: "RAJASTHAN", image: "https://images.unsplash.com/photo-1477587458883-47145ed94245?w=500&auto=format&fit=crop&q=60", date: "15-17 Jan 2026", city: "Jaipur", region: "India", type: "national", link: "#" },
    { id: 107, country: "GUJARAT", image: "https://images.unsplash.com/photo-1605130284535-11dd9eedc58a?w=500&auto=format&fit=crop&q=60", date: "10-12 Feb 2026", city: "Ahmedabad", region: "India", type: "national", link: "#" }
];

// Update the links in defaultNationalDestinations if an admin set a custom link
defaultNationalDestinations.forEach(dest => {
    const match = dynamicCardsFromDB.find(card => card.country_or_state.toUpperCase() === dest.country);
    if (match && match.link) {
        dest.link = match.link;
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

    // Start with default hardcoded cards
    let customDest = dynamicCardsFromDB || [];
    const allDefaults = [...defaultDestinations, ...defaultNationalDestinations];
    
    // Only update the LINK if a custom card matches the country, keep the visual data static!
    let finalDest = allDefaults.map(d => {
        const customOverride = customDest.find(c => c.country.toUpperCase() === d.country.toUpperCase());
        if (customOverride && customOverride.link && customOverride.link !== '#') {
            d.link = customOverride.link;
        }
        return d;
    });
    
    // Append any completely new countries that admin added
    const purelyNew = customDest.filter(c => !allDefaults.some(d => d.country.toUpperCase() === c.country.toUpperCase()));
    finalDest = [...finalDest, ...purelyNew];

    // Filter by 'national' or 'international' depending on current tab
    finalDest = finalDest.filter(d => {
        const type = d.type || (d.country.toUpperCase() === 'INDIA' ? 'national' : 'international');
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
            <div class="destination-body">
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
