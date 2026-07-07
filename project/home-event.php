<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config/Database.php';
$db = (new Database())->getConnection();

// Build a map of Country -> Slug/Link for dynamic destination pages
$dynamicLinksMap = [];
$stmtEv = $db->query("SELECT slug, title FROM events");
while($row = $stmtEv->fetch(PDO::FETCH_ASSOC)){
    $eventCountry = str_replace(['GSA ', ' 2026', ' Edition'], '', $row['title']);
    $dynamicLinksMap[strtoupper(trim($eventCountry))] = 'event-details.php?slug=' . $row['slug'];
}

$stmtCarousel = $db->query("SELECT country, state, slug, btn_url FROM home_carousel_events WHERE status='published' AND show_in_overseas=1");
while($row = $stmtCarousel->fetch(PDO::FETCH_ASSOC)){
    $loc = !empty($row['country']) ? strtoupper(trim($row['country'])) : strtoupper(trim($row['state']));
    $link = !empty($row['btn_url']) ? $row['btn_url'] : 'home-event.php?slug=' . $row['slug'];
    $dynamicLinksMap[$loc] = $link;
}

$stmtGsa = $db->query("SELECT country, slug FROM gsa_carousel_events WHERE status='published'");
while($row = $stmtGsa->fetch(PDO::FETCH_ASSOC)){
    if(!empty($row['country'])) {
        $dynamicLinksMap[strtoupper(trim($row['country']))] = 'event-details.php?slug=' . $row['slug'];
    }
}

// Fetch Event Details
$slug = $_GET['slug'] ?? '';
if (empty($slug)) {
    echo "<script>window.location.href='index.php';</script>";
    exit();
}

$stmtEvent = $db->prepare("SELECT * FROM home_carousel_events WHERE slug = ?");
$stmtEvent->execute([$slug]);
$event = $stmtEvent->fetch();

if (!$event) {
    echo "<script>alert('Event not found.'); window.location.href='index.php';</script>";
    exit();
}

$isAdmin = (isset($_SESSION['userRole']) && $_SESSION['userRole'] === 'ADMIN');
$isDraft = ($event['status'] === 'draft');

if ($isDraft) {
    echo "<script>
        if (localStorage.getItem('userRole') !== 'ADMIN') {
            alert('This event is not yet live.');
            window.location.href = 'index.php';
        }
    </script>";
}
$isCompleted = ($event['status'] === 'completed');


$stmt = $db->query("SELECT setting_key, setting_value FROM system_settings WHERE setting_key LIKE 'sponsor_%'");
$sponsorSettings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

$pricingStmt = $db->query("SELECT setting_value FROM system_settings WHERE setting_key = 'exhibitor_pricing'");
$pricingRow = $pricingStmt->fetch(PDO::FETCH_ASSOC);
$exhibitorPricing = $pricingRow ? json_decode($pricingRow['setting_value'], true) : [];
$punePricing = $exhibitorPricing['pune'] ?? [
    'standard' => ['size' => '3m x 3m', 'price' => '30000'],
    'premium' => ['size' => '6m x 3m', 'price' => '60000'],
    'corner' => ['size' => '6m x 6m', 'price' => '90000'],
    'pavilion' => ['size' => 'Custom', 'price' => '2,00,000+']
];

$pageTitle = htmlspecialchars($event['title'] ?? 'Event') . " | PlayArena";
$disableAdminTheme = true; // Prevents the global admin theme from breaking this page's custom dark/light theme!
include 'includes/header.php';
include 'includes/navbar.php';
?>

<!-- Custom CSS for this page - External file -->
<link rel="stylesheet" href="assets/css/gsa-pune-2026.css?v=3">

<script>
    document.body.classList.add('gsa-pune-page');
</script>

<!-- Floating Particles Background -->
<div class="particles-container">
    <div class="particle" style="--delay: 0s; --size: 8px; --left: 10%; --duration: 15s;"></div>
    <div class="particle" style="--delay: 2s; --size: 12px; --left: 20%; --duration: 18s;"></div>
    <div class="particle" style="--delay: 4s; --size: 6px; --left: 35%; --duration: 12s;"></div>
    <div class="particle" style="--delay: 1s; --size: 10px; --left: 50%; --duration: 20s;"></div>
    <div class="particle" style="--delay: 3s; --size: 7px; --left: 65%; --duration: 16s;"></div>
    <div class="particle" style="--delay: 5s; --size: 14px; --left: 80%; --duration: 14s;"></div>
    <div class="particle" style="--delay: 2.5s; --size: 9px; --left: 90%; --duration: 19s;"></div>
    <div class="particle" style="--delay: 4.5s; --size: 11px; --left: 45%; --duration: 17s;"></div>
</div>



<!-- =================================
     SECTION 1 â€” HERO BANNER
================================= -->
<?php $bgUrl = !empty($event['banner']) ? "background-image: url('" . str_replace("'", "\\'", $event['banner']) . "') !important;" : ""; ?>
<section class="gsa-hero" style="<?= $bgUrl ?>">
    <div class="hero-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>
    <div class="container">
        <div class="gsa-hero-content">
            <div style="display: block; width: 100%;">
                <div class="gsa-badge animate-badge">
                    <i class="fas fa-trophy"></i> 
                    <span class="badge-text"><?= htmlspecialchars(!empty($event['badge_text']) ? $event['badge_text'] : 'GSA Championship Series') ?></span>
                    <span class="badge-pulse"></span>
                </div>
            </div>
            <h1 class="animate-title">
                <?= htmlspecialchars($event['title'] ?? 'Event Name') ?>
                <span class="title-underline"></span>
            </h1>
            <h3 class="gsa-hero-subtitle animate-subtitle">
                <span class="typed-text"><?= htmlspecialchars($event['description'] ?? '9 Days Sports Championship Festival') ?></span>
            </h3>
            
            <div class="gsa-event-details animate-details">
                <div class="gsa-detail-item">
                    <div class="gsa-detail-icon"><i class="far fa-calendar-alt"></i></div>
                    <div class="gsa-detail-text">
                        <p>Event Date</p>
                        <h4><?= htmlspecialchars($event['event_date'] ?? 'Upcoming') ?></h4>
                    </div>
                </div>
                <div class="gsa-detail-item">
                    <div class="gsa-detail-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div class="gsa-detail-text">
                          <p>Location</p>
                          <h4>
                              <?php if (!empty($event['location'])): ?>
                                  <?= nl2br(htmlspecialchars($event['location'])) ?>
                              <?php else: ?>
                                  Shree Shiv Chhatrapati Sports Complex,<br>Balewadi Stadium, Pune
                              <?php endif; ?>
                          </h4>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-3 mt-4 flex-wrap justify-content-center animate-buttons">
                <?php if ($isCompleted): ?>
                    <button class="btn-outline-gold" style="opacity: 0.7; cursor: not-allowed; padding: 10px 24px; font-weight: bold; border-radius: 4px;" disabled>
                        <i class="fas fa-check-circle"></i> Event Completed
                    </button>
                <?php else: ?>
                    <a href="event-registration.php?event=<?= urlencode($event['slug']) ?>" class="btn-gold pulse-btn">
                        <i class="fas fa-ticket-alt"></i> Register Now
                        <span class="btn-ripple"></span>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Hero Countdown -->
            <div class="mt-7 flex gap-2 sm:gap-3 hero-countdown" style="display: flex; gap: 8px; justify-content: space-between; margin-top: 2rem;">
              <div class="rounded-2xl border border-beige-200/80 bg-white/75 px-2 py-4 text-center shadow-[0_10px_22px_rgba(139,90,43,0.06)]" style="flex: 1; min-width: 0; border: 1px solid rgba(229, 219, 184, 0.8); background: rgba(255,255,255,0.75); padding: 16px 8px; text-align: center; border-radius: 16px; box-shadow: 0 10px 22px rgba(139,90,43,0.06); backdrop-filter: blur(8px);">
                <div class="text-2xl sm:text-3xl font-black text-beige-900" id="days" style="font-size: 1.8rem; font-weight: 900; color: #3A342B;">00</div>
                <div class="mt-1 text-[0.6rem] sm:text-[0.7rem] font-semibold uppercase tracking-[0.16em] text-beige-600" style="margin-top: 4px; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.16em; color: #8A7A5F;">Days</div>
              </div>
              <div class="rounded-2xl border border-beige-200/80 bg-white/75 px-2 py-4 text-center shadow-[0_10px_22px_rgba(139,90,43,0.06)]" style="flex: 1; min-width: 0; border: 1px solid rgba(229, 219, 184, 0.8); background: rgba(255,255,255,0.75); padding: 16px 8px; text-align: center; border-radius: 16px; box-shadow: 0 10px 22px rgba(139,90,43,0.06); backdrop-filter: blur(8px);">
                <div class="text-2xl sm:text-3xl font-black text-beige-900" id="hours" style="font-size: 1.8rem; font-weight: 900; color: #3A342B;">00</div>
                <div class="mt-1 text-[0.6rem] sm:text-[0.7rem] font-semibold uppercase tracking-[0.16em] text-beige-600" style="margin-top: 4px; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.16em; color: #8A7A5F;">Hours</div>
              </div>
              <div class="rounded-2xl border border-beige-200/80 bg-white/75 px-2 py-4 text-center shadow-[0_10px_22px_rgba(139,90,43,0.06)]" style="flex: 1; min-width: 0; border: 1px solid rgba(229, 219, 184, 0.8); background: rgba(255,255,255,0.75); padding: 16px 8px; text-align: center; border-radius: 16px; box-shadow: 0 10px 22px rgba(139,90,43,0.06); backdrop-filter: blur(8px);">
                <div class="text-2xl sm:text-3xl font-black text-beige-900" id="minutes" style="font-size: 1.8rem; font-weight: 900; color: #3A342B;">00</div>
                <div class="mt-1 text-[0.6rem] sm:text-[0.7rem] font-semibold uppercase tracking-[0.16em] text-beige-600" style="margin-top: 4px; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.16em; color: #8A7A5F;">Mins</div>
              </div>
              <div class="rounded-2xl border border-beige-200/80 bg-white/75 px-2 py-4 text-center shadow-[0_10px_22px_rgba(139,90,43,0.06)]" style="flex: 1; min-width: 0; border: 1px solid rgba(229, 219, 184, 0.8); background: rgba(255,255,255,0.75); padding: 16px 8px; text-align: center; border-radius: 16px; box-shadow: 0 10px 22px rgba(139,90,43,0.06); backdrop-filter: blur(8px);">
                <div class="text-2xl sm:text-3xl font-black text-beige-900" id="seconds" style="font-size: 1.8rem; font-weight: 900; color: #3A342B;">00</div>
                <div class="mt-1 text-[0.6rem] sm:text-[0.7rem] font-semibold uppercase tracking-[0.16em] text-beige-600" style="margin-top: 4px; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.16em; color: #8A7A5F;">Secs</div>
              </div>
            </div>
        </div>
    </div>
</section>

<!-- =================================
     SECTION 2 â€” EVENT OVERVIEW
================================= -->
<?php
// Calculate dynamic stats
$sports = [];
if (!empty($event['sports_data'])) {
    $sports = json_decode($event['sports_data'], true) ?? [];
}
$sportsCount = count($sports);

$daysFestival = 0;
if (!empty($event['event_date']) && !empty($event['end_date'])) {
    try {
        $st = new DateTime($event['event_date']);
        $ed = new DateTime($event['end_date']);
        $daysFestival = $st->diff($ed)->days + 1;
    } catch (Exception $e) {}
}
if ($daysFestival <= 0) $daysFestival = 9; // fallback

$totalPrize = 0;
foreach ($sports as $s) {
    if (!empty($s['prize'])) {
        $val = str_replace([',', ' '], '', $s['prize']);
        if (is_numeric($val)) {
            $totalPrize += (float)$val;
        }
    }
}
$prizeText = '10L+';
$prizeNumber = 10;
$prizeSuffix = 'L+';
if ($totalPrize > 0) {
    if ($totalPrize >= 10000000) {
        $prizeNumber = floor($totalPrize / 10000000);
        $prizeSuffix = 'Cr+';
    } elseif ($totalPrize >= 100000) {
        $prizeNumber = floor($totalPrize / 100000);
        $prizeSuffix = 'L+';
    } elseif ($totalPrize >= 1000) {
        $prizeNumber = floor($totalPrize / 1000);
        $prizeSuffix = 'K+';
    } else {
        $prizeNumber = $totalPrize;
        $prizeSuffix = '+';
    }
}
$participantsCount = $sportsCount * 250;
if ($participantsCount < 1000) $participantsCount = 1000;
?>
<section class="overview-section">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-3 col-6">
                <div class="stat-card glass-card stat-animate">
                    <div class="stat-icon"><i class="fas fa-medal"></i></div>
                    <div class="stat-number counter" data-target="<?= $sportsCount ?>">0</div>
                    <div class="stat-label">Sports<br>Championships</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card glass-card stat-animate">
                    <div class="stat-icon"><i class="fas fa-calendar-day"></i></div>
                    <div class="stat-number counter" data-target="<?= $daysFestival ?>">0</div>
                    <div class="stat-label">Days<br>Festival</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card glass-card stat-animate">
                    <div class="stat-icon"><i class="fas fa-rupee-sign"></i></div>
                    <div class="stat-number"><span class="counter" data-target="<?= $prizeNumber ?>">0</span><?= $prizeSuffix ?></div>
                    <div class="stat-label">Prize<br>Pool</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card glass-card stat-animate">
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-number"><span class="counter" data-target="<?= $participantsCount ?>">0</span>+</div>
                    <div class="stat-label">Participants<br>Expected</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- =================================
     SECTION 2.5 â€” FESTIVAL LOCATIONS
================================= -->
<?php 
$locations = [];
if (!empty($event['locations_data'])) {
    $locations = json_decode($event['locations_data'], true) ?? [];
}
if (count($locations) > 0): 
?>
<section class="locations-section" style="padding: 60px 0; background: transparent;">
    <div class="container">
        <div class="section-title">
            <h2 style="color:#c5a85c; text-align:center; font-family: 'Cinzel', serif; margin-bottom: 30px; letter-spacing: 2px;">FESTIVAL LOCATIONS</h2>
        </div>
        <div class="locations-wrapper grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" style="max-width: 1200px; margin: 0 auto; padding: 0 15px;">
            <?php foreach ($locations as $index => $loc): ?>
            <div class="location-card transition-all duration-300 hover:-translate-y-2" style="position: relative; padding: 30px; min-height: 380px; border: 1px solid rgba(197, 168, 92, 0.4); border-radius: 12px; overflow: hidden; display: flex; flex-direction: column; justify-content: flex-end; box-shadow: 0 10px 30px rgba(0,0,0,0.5); <?= !empty($loc['bg']) ? 'background: linear-gradient(to top, rgba(0,0,0,0.95) 0%, rgba(0,0,0,0.6) 50%, rgba(0,0,0,0.1) 100%), url(\'' . htmlspecialchars(str_replace("'", "\'", $loc['bg'])) . '\') center/cover no-repeat;' : 'background: #111;' ?>">
                <div style="position: relative; z-index: 2;">
                    <h3 style="color: #f5d87a !important; -webkit-text-fill-color: #f5d87a !important; font-family: 'Cinzel', serif; font-size: 26px; margin-bottom: 5px; text-shadow: 0 2px 4px rgba(0,0,0,0.8);"><?= htmlspecialchars($loc['name']) ?></h3>
                    <h4 style="color: #fff !important; -webkit-text-fill-color: #fff !important; font-size: 15px; font-weight: bold; letter-spacing: 1px; margin-bottom: 15px; text-shadow: 0 1px 2px rgba(0,0,0,0.8);"><?= htmlspecialchars($loc['subtitle']) ?></h4>
                    <ul style="color: #ddd !important; list-style: none; padding-left: 0; font-size: 14px; margin-bottom: 0;">
                        <?php 
                        $items = array_filter(array_map('trim', explode("\n", $loc['items'])));
                        foreach ($items as $item) {
                            echo '<li style="margin-bottom: 8px; text-shadow: 0 1px 2px rgba(0,0,0,0.8); color: #ddd !important;"><span style="color:#c5a85c !important; margin-right:6px;">&bull;</span> ' . htmlspecialchars($item) . '</li>';
                        }
                        ?>
                    </ul>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- =================================
     SECTION 3 â€” SPORTS CATEGORIES
================================= -->
<?php 
$sports = [];
if (!empty($event['sports_data'])) {
    $sports = json_decode($event['sports_data'], true) ?? [];
}
if (count($sports) > 0): 
?>
<section class="sports-section">
    <div class="container">
        <div class="section-title">
            <h2>Sports <span>Categories</span></h2>
            <div class="title-line"></div>
            <p class="section-subtitle">Choose your sport and compete for glory</p>
        </div>

        <div class="row g-4">
            <?php foreach ($sports as $index => $s): ?>
            <?php $cardClass = 'sport-card-' . (($index % 3) + 1); ?>
            <div class="col-md-4 col-6">
                <div class="glass-card sport-card <?= $cardClass ?>">
                    <div class="sport-img-wrap">
                        <?php if (!empty($s['prize'])): ?>
                        <div class="sport-prize-badge">
                            <i class="fas fa-trophy"></i> <?= (isset($s['prize_currency']) && $s['prize_currency'] === 'USD') ? '$' : 'â‚¹' ?><?= htmlspecialchars($s['prize']) ?>
                        </div>
                        <?php endif; ?>
                        <div class="sport-image-bg">
                            <?php if (!empty($s['image'])): ?>
                                <img src="<?= htmlspecialchars($s['image']) ?>" alt="<?= htmlspecialchars($s['title']) ?>" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.8;">
                            <?php else: ?>
                                <i class="fas <?= htmlspecialchars($s['icon']) ?> sport-icon"></i>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($s['badge'])): ?>
                        <div class="sport-overlay">
                            <span class="sport-tag"><?= htmlspecialchars($s['badge']) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <h3><?= htmlspecialchars($s['title']) ?></h3>
                    
                    <?php if (!empty($s['categories'])): ?>
                    <div class="sport-categories">
                        <?php 
                        $cats = array_map('trim', explode(',', $s['categories']));
                        foreach ($cats as $cat) {
                            $icon = stripos($cat, 'double') !== false || stripos($cat, 'pair') !== false || stripos($cat, 'corporate') !== false ? 'fa-users' : 'fa-user';
                            echo '<span class="cat-tag"><i class="fas ' . $icon . '"></i> ' . htmlspecialchars($cat) . '</span>';
                        }
                        ?>
                    </div>
                    <?php endif; ?>
                    
                    <div class="sport-fees">
                        <?php if (!empty($s['price_individual'])): ?>
                        <div class="fee-item">
                            <span><i class="fas fa-user"></i> Individual</span>
                            <span><?= (isset($s['currency']) && $s['currency'] === 'USD') ? '$' : 'â‚¹' ?><?= htmlspecialchars($s['price_individual']) ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($s['price_pair'])): ?>
                        <div class="fee-item">
                            <span><i class="fas fa-users"></i> Pair</span>
                            <span><?= (isset($s['currency']) && $s['currency'] === 'USD') ? '$' : 'â‚¹' ?><?= htmlspecialchars($s['price_pair']) ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($s['price_team'])): ?>
                        <div class="fee-item">
                            <span><i class="fas fa-users"></i> Team</span>
                            <span><?= (isset($s['currency']) && $s['currency'] === 'USD') ? '$' : 'â‚¹' ?><?= htmlspecialchars($s['price_team']) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php if ($isCompleted): ?>
                        <button class="btn-outline-gold text-center w-100" style="opacity: 0.7; cursor: not-allowed; padding: 8px 16px; border: 1px solid #c5a85c; background: transparent; color: #c5a85c; border-radius: 4px; font-weight: bold;" disabled>
                            <i class="fas fa-check-circle"></i> Event Completed
                        </button>
                    <?php else: ?>
                        <a href="<?= !empty($event['slug']) ? 'event-registration.php?event=' . urlencode($event['slug']) . '&sport=' . urlencode($s['title']) : '#' ?>" class="btn-outline-gold text-center w-100">
                            <i class="fas fa-arrow-right"></i> Register
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- =================================
     SECTION 4 â€” EVENT SCHEDULE
================================= -->
  <?php 
  $schedules = [];
  if (!empty($event['schedule_data'])) {
      $schedules = json_decode($event['schedule_data'], true) ?? [];
  }
  if (count($schedules) > 0): 
  ?>
  <section class="schedule-section">
      <div class="container">
          <div class="section-title">
              <h2>Event <span>Schedule</span></h2>
              <div class="title-line"></div>
              <p class="section-subtitle">Thrilling sports action</p>
          </div>
  
          <div class="timeline">
              <?php foreach ($schedules as $s): ?>
              <div class="timeline-item">
                  <div class="timeline-dot">
                      <span class="dot-inner"></span>
                  </div>
                  <div class="timeline-content">
                      <?php if (!empty($s['day'])): ?>
                      <div class="timeline-day">
                          <i class="fas fa-calendar-day"></i> <?= htmlspecialchars($s['day']) ?>
                      </div>
                      <?php endif; ?>
                      
                      <?php if (!empty($s['title'])): ?>
                      <h3 class="timeline-event"><?= htmlspecialchars($s['title']) ?></h3>
                      <?php endif; ?>
                      
                      <?php if (!empty($s['description'])): ?>
                      <p class="text-muted mt-2 mb-0"><?= nl2br(htmlspecialchars($s['description'])) ?></p>
                      <?php endif; ?>
                      
                      <?php if (!empty($s['time'])): ?>
                      <div class="timeline-time"><i class="far fa-clock"></i> <?= htmlspecialchars($s['time']) ?></div>
                      <?php endif; ?>
                  </div>
              </div>
              <?php endforeach; ?>
          </div>
      </div>
  </section>
  <?php endif; ?>

<!-- =================================
     SECTION 5 â€” FACILITIES
================================= -->
<section class="facilities-section">
    <div class="container">
        <div class="section-title">
            <h2>Premium <span>Facilities</span></h2>
            <div class="title-line"></div>
            <p class="section-subtitle">World-class amenities for an unforgettable experience</p>
        </div>

        <div class="row g-4">

            <div class="col-md-4 col-6">
                <a href="media-hub.php" style="text-decoration: none; display: block; color: inherit;">
                    <div class="facility-card facility-card-2">
                        <div class="facility-icon"><i class="fas fa-video"></i></div>
                        <h3 class="facility-title">Live Streaming</h3>
                        <p class="facility-desc">HD live streaming on multiple platforms worldwide</p>
                    </div>
                </a>
            </div>
            <div class="col-md-4 col-6">
                <a href="medical-support.php" style="text-decoration: none; display: block; color: inherit;">
                    <div class="facility-card facility-card-3">
                        <div class="facility-icon"><i class="fas fa-stethoscope"></i></div>
                        <h3 class="facility-title">Medical Support</h3>
                        <p class="facility-desc">24/7 medical team with ambulance and first-aid services</p>
                    </div>
                </a>
            </div>

            <div class="col-md-4 col-6">
                <a href="gallery.php" style="text-decoration: none; display: block; color: inherit;">
                    <div class="facility-card facility-card-6">
                        <div class="facility-icon"><i class="fas fa-camera"></i></div>
                        <h3 class="facility-title">Media Coverage</h3>
                        <p class="facility-desc">Extensive media coverage with professional photographers</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- =================================
     SECTION 6 â€” SPONSORSHIP
================================= -->
<?php
$isAdmin = (isset($_SESSION['userRole']) && $_SESSION['userRole'] === 'ADMIN');
?>
<section class="sponsors-section">
    <div class="container">
        <div class="section-title">
            <h2>Sponsorship <span>Opportunities</span></h2>
            <div class="title-line"></div>
            <p class="section-subtitle">Partner with us for global brand visibility</p>
        </div>

        <link rel="stylesheet" href="assets/css/gallery.css?v=3">
        <div class="gallery-section" style="width: 100%; margin: 0; padding-bottom: 20px;">
            <?php 
            $eventSponsors = [];
            if (!empty($event['sponsors_data'])) {
                $eventSponsors = json_decode($event['sponsors_data'], true) ?? [];
            }
            
            // Fetch database-driven sponsors for this specific event or global
            $dbSponsors = [];
            try {
                $evtTitle = $event['title'] ?? '';
                $spStmt = $db->prepare("SELECT * FROM admin_sponsors WHERE status = 'active' AND (event_name = ? OR event_name = '' OR event_name IS NULL) ORDER BY FIELD(tier, 'Title', 'Platinum', 'Gold', 'Silver', 'Bronze'), created_at DESC");
                $spStmt->execute([$evtTitle]);
                $dbSponsors = $spStmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e) {}

            // Format db sponsors to match the format of eventSponsors
            foreach ($dbSponsors as $ds) {
                $eventSponsors[] = [
                    'name' => $ds['company_name'],
                    'website' => $ds['website_url'],
                    'img' => $ds['thumbnail']
                ];
            }

            $hasSponsors = count($eventSponsors) > 0;
            
            if ($hasSponsors):
                foreach ($eventSponsors as $sponsor): 
                    $name = $sponsor['name'] ?? "";
                    $website = $sponsor['website'] ?? "";
                    $img = $sponsor['img'] ?? "";
            ?>
            <div class="gallery-card" style="cursor: default;">
                <img src="<?= htmlspecialchars($img ?: 'assets/images/placeholder.png') ?>" alt="<?= htmlspecialchars($name ?: 'Sponsor') ?>" style="object-fit: contain; padding: 20px; background: #fff;">
                <div class="gallery-content" style="<?= empty($name) && empty($website) ? 'display: none;' : '' ?>">
                    <?php if (!empty($name)): ?>
                        <h3><?= htmlspecialchars($name) ?></h3>
                    <?php endif; ?>
                    <?php if ($website): ?>
                        <a href="<?= htmlspecialchars($website) ?>" target="_blank" style="color: #c5a85c; font-size: 0.85rem; text-decoration: none; margin-top: 5px; display: inline-block;">Visit Website <i class="fas fa-external-link-alt"></i></a>
                    <?php endif; ?>
                </div>
            </div>
            <?php 
                endforeach; 
            else:
                echo '<p style="text-align: center; color: #9aa0b4; grid-column: 1 / -1;">New sponsors will be announced soon.</p>';
            endif;
            ?>
        </div>

        <div class="text-center mt-5">
            <a href="sponsors.php" class="btn-gold sponsor-cta">
                <i class="fas fa-handshake"></i> Become A Sponsor
            </a>
        </div>
    </div>
</section>

<!-- =================================
     SECTION 7 â€” EXHIBITOR
================================= -->
<section class="exhibitor-section">
    <div class="container">
        <div class="section-title">
            <h2>Exhibitor <span>Opportunities</span></h2>
            <div class="title-line"></div>
            <p class="section-subtitle">Showcase your brand to thousands of attendees</p>
        </div>

        <div class="row g-4">
            <?php 
            $exhibitors = [];
            if (!empty($event['exhibitor_data'])) {
                $exhibitors = json_decode($event['exhibitor_data'], true) ?? [];
            }
            
            if (count($exhibitors) > 0): 
                $i = 1;
                foreach ($exhibitors as $exh): 
            ?>
                <div class="col-md-3 col-6">
                    <div class="glass-card stall-card stall-card-<?= $i ?>">
                        <?php if(!empty($exh['badge'])): ?>
                            <div class="stall-popular"><?= htmlspecialchars($exh['badge']) ?></div>
                        <?php endif; ?>
                        <div class="stall-icon"><i class="fas <?= htmlspecialchars($exh['icon']) ?> <?= ($exh['badge']=='HOT' || stripos($exh['title'], 'Premium')!==false) ? 'text-gold' : '' ?>"></i></div>
                        <h4 class="<?= ($exh['badge']=='HOT' || stripos($exh['title'], 'Premium')!==false) ? 'text-gold' : '' ?>"><?= htmlspecialchars($exh['title']) ?></h4>
                        <p class="text-muted mt-3 mb-0"><?= htmlspecialchars($exh['size']) ?> <?= htmlspecialchars($exh['desc']) ?></p>
                        <div class="stall-price <?= ($exh['badge']=='HOT' || stripos($exh['title'], 'Premium')!==false) ? 'text-gold' : '' ?>"><?= (isset($exh['currency']) && $exh['currency'] === 'USD') ? '$' : 'â‚¹' ?><?= is_numeric($exh['price']) ? number_format($exh['price']) : htmlspecialchars($exh['price']) ?></div>
                        <a href="exhibitor.php" class="btn-<?= ($exh['badge']=='HOT' || stripos($exh['title'], 'Premium')!==false) ? 'gold' : 'outline-gold' ?> btn-sm mt-3">Book Now</a>
                    </div>
                </div>
            <?php 
                $i++;
                if($i > 4) $i = 1; // loop classes 1-4
                endforeach; 
            else: 
                // Fallback to legacy structure
            ?>
                <div class="col-md-3 col-6">
                    <div class="glass-card stall-card stall-card-1">
                        <div class="stall-icon"><i class="fas fa-store"></i></div>
                        <h4>Standard Stall</h4>
                        <p class="text-muted mt-3 mb-0"><?= htmlspecialchars($punePricing['standard']['size'] ?? '3m x 3m') ?> Booth space in general area</p>
                        <div class="stall-price">â‚¹<?= is_numeric($punePricing['standard']['price'] ?? '') ? number_format($punePricing['standard']['price']) : htmlspecialchars($punePricing['standard']['price'] ?? '30,000') ?></div>
                        <a href="exhibitor.php" class="btn-outline-gold btn-sm mt-3">Book Now</a>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="glass-card stall-card stall-card-2">
                        <div class="stall-popular">HOT</div>
                        <div class="stall-icon"><i class="fas fa-store-alt text-gold"></i></div>
                        <h4 class="text-gold">Premium Stall</h4>
                        <p class="text-muted mt-3 mb-0"><?= htmlspecialchars($punePricing['premium']['size'] ?? '6m x 3m') ?> Booth space in high footfall area</p>
                        <div class="stall-price text-gold">â‚¹<?= is_numeric($punePricing['premium']['price'] ?? '') ? number_format($punePricing['premium']['price']) : htmlspecialchars($punePricing['premium']['price'] ?? '60,000') ?></div>
                        <a href="exhibitor.php" class="btn-gold btn-sm mt-3">Book Now</a>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="glass-card stall-card stall-card-3">
                        <div class="stall-icon"><i class="fas fa-city"></i></div>
                        <h4>Corner Premium</h4>
                        <p class="text-muted mt-3 mb-0"><?= htmlspecialchars($punePricing['corner']['size'] ?? '6m x 6m') ?> Two-side open booth for better visibility</p>
                        <div class="stall-price">â‚¹<?= is_numeric($punePricing['corner']['price'] ?? '') ? number_format($punePricing['corner']['price']) : htmlspecialchars($punePricing['corner']['price'] ?? '90,000') ?></div>
                        <a href="exhibitor.php" class="btn-outline-gold btn-sm mt-3">Book Now</a>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="glass-card stall-card stall-card-4">
                        <div class="stall-icon"><i class="fas fa-building"></i></div>
                        <h4>Pavilion Partner</h4>
                        <p class="text-muted mt-3 mb-0"><?= htmlspecialchars($punePricing['pavilion']['size'] ?? 'Custom') ?> Large space buildout</p>
                        <div class="stall-price">â‚¹<?= is_numeric($punePricing['pavilion']['price'] ?? '') ? number_format($punePricing['pavilion']['price']) : htmlspecialchars($punePricing['pavilion']['price'] ?? '2,00,000+') ?></div>
                        <a href="exhibitor.php" class="btn-outline-gold btn-sm mt-3">Book Now</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="text-center mt-5">
            <a href="exhibitor.php" class="btn-gold exhibitor-cta">
                <i class="fas fa-store"></i> Book Stall
            </a>
        </div>
    </div>
</section>

<!-- =================================
     SECTION 8 â€” NXL CREDITS
================================= -->
<section class="nxl-section">
    <div class="container">
        <div class="nxl-card">
            <div class="nxl-icon">
                <i class="fab fa-bitcoin"></i>
                <div class="nxl-pulse"></div>
            </div>
            <div class="nxl-content">
                <h3>Powered by <span class="text-gold">NXL Credits</span></h3>
                <p>NXL Credits are the official utility credits of the GSA ecosystem and can be used for registrations, merchandise, food courts, exhibitor services, and partner services.</p>
                <div class="nxl-features">
                    <span><i class="fas fa-check-circle text-gold"></i> Registration</span>
                    <span><i class="fas fa-check-circle text-gold"></i> Merchandise</span>
                    <span><i class="fas fa-check-circle text-gold"></i> Food Court</span>
                    <span><i class="fas fa-check-circle text-gold"></i> Exhibitor Services</span>
                </div>
                <a href="credits.php" class="btn-gold mt-3">
                    <i class="fas fa-coins"></i> Learn More about NXL
                </a>
            </div>
        </div>
    </div>
</section>

<!-- =================================
     SECTION 9 â€” GALA DINNER
================================= -->
<?php if (!empty($event['gala_venue'])): ?>
<section class="gala-section">
    <div class="container">
        <div class="gala-content">
            <div class="gala-badge">Exclusive Event</div>
            <h2>
                <?php if (!empty($event['gala_title'])): ?>
                    <?= htmlspecialchars($event['gala_title']) ?>
                <?php else: ?>
                    Award Ceremony <br>& Gala Dinner
                <?php endif; ?>
            </h2>
            
            <div class="gala-details">
                <div class="gsa-detail-item">
                    <div class="gsa-detail-icon"><i class="fas fa-hotel"></i></div>
                    <div class="gsa-detail-text">
                        <p>Venue</p>
                        <h4><?= htmlspecialchars($event['gala_venue']) ?></h4>
                    </div>
                </div>
                
                <div class="gsa-detail-item">
                    <div class="gsa-detail-icon"><i class="far fa-calendar-check"></i></div>
                    <div class="gsa-detail-text">
                        <p>Date</p>
                        <h4><?= htmlspecialchars($event['gala_date'] ?? '') ?></h4>
                    </div>
                </div>
                
                <div class="gsa-detail-item">
                    <div class="gsa-detail-icon"><i class="fas fa-clock"></i></div>
                    <div class="gsa-detail-text">
                        <p>Time</p>
                        <h4><?= htmlspecialchars($event['gala_time'] ?? '') ?></h4>
                    </div>
                </div>
            </div>
            
            <?php if (!empty($event['gala_description'])): ?>
            <p class="gala-description"><?= nl2br(htmlspecialchars($event['gala_description'])) ?></p>
            <?php endif; ?>
            
            <a href="award-registration.php?event=<?= urlencode($event['slug']) ?>" class="btn-gold gala-btn">
                <i class="fas fa-ticket-alt"></i> Book Gala Pass
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- =================================
     SECTION 10 â€” REGISTRATION CTA
================================= -->
<section class="cta-section" id="register">
    <div class="container">
        <div class="cta-content">
            <h2>Register Today & Be A <span>Champion</span></h2>
            <p class="cta-text">Join the biggest sports championship in Pune 2026</p>
            <div class="cta-buttons">
                <a href="event-registration.php?type=player" class="btn-gold cta-btn-primary">
                    <i class="fas fa-user"></i> Register as Player
                </a>
            </div>
        </div>
    </div>
</section>

<!-- =================================
     SECTION 11 â€” GLOBAL CHAPTERS (CAROUSEL)
================================= -->
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
      <span class="chevron-icon">â—€</span>
    </button>
    
    <div 
      class="destinations-slider" 
      id="destinations-slider"
    >
      <!-- Slides will repeat for infinite scroll effect in JavaScript -->
    </div>

    <button class="slider-control-btn next" onclick="scrollDestinations('right')">
      <span class="chevron-icon">â–¶</span>
    </button>
  </div>
</section>

<!-- Countdown Timer Script -->
<script>
    // Pass the event's start date from the database to the countdown script
    window.targetEventDate = "<?= htmlspecialchars($event['event_date'] ?? '') ?>";
    window.timerStartDate = "<?= htmlspecialchars($event['timer_start_date'] ?? '') ?>";
    window.pageLoadTime = new Date().getTime();
</script>
<script src="assets/js/countdown.js?v=<?= time() ?>"></script>

<script>

// Counter Animation
document.addEventListener('DOMContentLoaded', function() {
    const counters = document.querySelectorAll('.counter');
    const speed = 200;

    counters.forEach(counter => {
        const updateCounter = () => {
            const target = parseInt(counter.getAttribute('data-target'));
            const current = parseInt(counter.innerText.replace(/,/g, ''));
            const increment = Math.ceil(target / speed);

            if (current < target) {
                counter.innerText = Math.min(current + increment, target);
                setTimeout(updateCounter, 20);
            }
        };

        // Intersection Observer for animation trigger
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    updateCounter();
                    observer.unobserve(entry.target);
                }
            });
        });

        observer.observe(counter);
    });
});

function editSponsorImage(key) {
    const newUrl = prompt("Enter the new Image URL for this sponsor:");
    if (newUrl !== null) {
        const payload = {};
        payload[key] = newUrl;
        
        fetch('api/index.php/settings', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert("Sponsor image updated successfully! Reloading...");
                window.location.reload();
            } else {
                alert("Failed to update: " + data.message);
            }
        })
        .catch(err => {
            console.error(err);
            alert("An error occurred while updating the sponsor image.");
        });
    }
}

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

let homeDestFilter = 'international';

function setHomeDestFilter(type) {
    homeDestFilter = type;
    document.getElementById("btnHomeIntl").classList.toggle('active', type === 'international');
    document.getElementById("btnHomeNatl").classList.toggle('active', type === 'national');
    
    document.getElementById("destinations-title").textContent = type === 'international' ? 'Overseas Events' : 'Indian States Events';
    
    renderDestinations();
}

function renderDestinations() {
    const slider = document.getElementById("destinations-slider");
    if (!slider) return;

    let customDest = window.apiDestinations || [];

    const allDefaults = [...defaultDestinations, ...defaultNationalDestinations];
    let mergedAll = allDefaults.map(d => {
        const customOverride = customDest.find(c => c.id === d.id);
        return customOverride ? customOverride : d;
    });
    
    const purelyNew = customDest.filter(c => !allDefaults.some(d => d.id === c.id));
    
    let finalDest = [...mergedAll, ...purelyNew].filter(d => !d.deleted);
    
    finalDest = finalDest.filter(d => {
        const type = d.type || (d.id > 100 ? 'national' : 'international');
        return type === homeDestFilter;
    });
    
    const displayList = [...finalDest, ...finalDest, ...finalDest, ...finalDest]; // duplicate for infinite scroll effect
    
    const dynamicLinks = <?php echo json_encode($dynamicLinksMap); ?>;
    slider.innerHTML = displayList.map((dest, idx) => {
        let locKey = (dest.country || '').toUpperCase();
        if(!locKey && dest.region) locKey = dest.region.toUpperCase();
        
        let defaultLink = dynamicLinks[locKey] ? dynamicLinks[locKey] : `destination-detail.php?id=${dest.id}`;
        let targetLink = dest.link && dest.link !== "#" ? dest.link : defaultLink;
        let targetAttr = '';
        if (locKey === "PUNE") {
            targetLink = "gsa-pune-2026.php";
        }
        return `
          <a href="${targetLink}" ${targetAttr} class="destination-card" onclick="${targetLink === '#' ? 'event.preventDefault()' : ''}">
            <div class="destination-image-box">
              <img src="${dest.image}" alt="${dest.country}" class="destination-image" />
              <div class="destination-flag-overlay">${dest.country}</div>
            </div>
            <div class="destination-body" style="display: flex; flex-direction: column; align-items: center; text-align: center; gap: 1rem; flex: 1; justify-content: space-between;">
              <p style="color: #9aa0b4; font-size: 0.85rem; line-height: 1.4; margin: 0;">Where Sports, Tourism & Entertainment come together on a global stage.</p>
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

let autoScrollAnimation;
let isHovering = false;

function startAutoScroll() {
    const slider = document.getElementById("destinations-slider");
    if (!slider) return;

    function step() {
        if (!isHovering && slider.style.scrollBehavior !== "smooth") {
            slider.scrollLeft += 1;
            // Reset to beginning if scrolled to end of the cloned list
            if (slider.scrollLeft >= (slider.scrollWidth - slider.clientWidth) - 5) {
                slider.scrollLeft = 0;
            }
        }
        autoScrollAnimation = requestAnimationFrame(step);
    }
    autoScrollAnimation = requestAnimationFrame(step);

    slider.addEventListener("mouseenter", () => isHovering = true);
    slider.addEventListener("mouseleave", () => isHovering = false);
    
    // Also handle touch events for mobile
    slider.addEventListener("touchstart", () => isHovering = true, {passive: true});
    slider.addEventListener("touchend", () => isHovering = false);
}

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
    startAutoScroll();
});
</script>

<?php include 'includes/footer.php'; ?>





