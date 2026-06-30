<?php
$pageTitle = "EKONAMIA - Finance, Investment & Economic Development";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<link rel="stylesheet" href="assets/css/pillars.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<div class="pillar-page ekonamia-page">
  <div class="pillar-hero">
    <h1>EKONAMIA</h1>
    <p class="subtitle">Finance, Investment & Economic Development</p>
  </div>

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

  <!-- Creative Pillar Cards Showcase -->
  <div class="carousel-container">
    <button class="carousel-btn prev-btn" id="pillarPrev" aria-label="Previous">
      <i class="fas fa-chevron-left"></i>
    </button>
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

  <section class="pillar-section">
    <h2>About</h2>
    <p style="color:#7A7061; line-height: 1.6; font-size: 1.1rem;">
      Ekonamia aims to empower the global economy by connecting visionary entrepreneurs with global investors. We facilitate cross-border trade, foster fintech innovations, and support robust economic development initiatives worldwide.
    </p>
  </section>

  <div class="pillar-grid" style="margin-bottom: 40px;">
    <section class="pillar-section" style="margin-bottom: 0;">
      <h2>Mission</h2>
      <p style="color:#7A7061; line-height: 1.6;">
        To democratize access to global investments and leverage financial technology to create inclusive economic growth for communities around the world.
      </p>
    </section>
    
    <section class="pillar-section" style="margin-bottom: 0;">
      <h2>Vision</h2>
      <p style="color:#7A7061; line-height: 1.6;">
        A borderless financial ecosystem where innovation in fintech and strategic investments drive unprecedented economic stability and prosperity.
      </p>
    </section>
  </div>

  <section class="pillar-section">
    <h2>Key Sectors & Ideal Participants</h2>
    <div class="pillar-grid">
      <div class="pillar-card-box">
        <h3>Key Sectors</h3>
        <ul style="color:#7A7061; line-height:1.8; margin-left: 20px;">
          <li>Fintech & Blockchain</li>
          <li>Global Trade & Logistics</li>
          <li>Venture Capital & Private Equity</li>
          <li>SME Development & Microfinance</li>
        </ul>
      </div>
      <div class="pillar-card-box">
        <h3>Ideal Participants</h3>
        <ul style="color:#7A7061; line-height:1.8; margin-left: 20px;">
          <li>Angel Investors & VCs</li>
          <li>Fintech Entrepreneurs</li>
          <li>Economic Policy Advisors</li>
          <li>Global Trade Facilitators</li>
        </ul>
      </div>
    </div>
  </section>

  <section class="pillar-section">
    <h2>Special Activities</h2>
    <p style="color:#9aa0b4; line-height: 1.6; margin-bottom: 20px;">
      Features exclusive pitch sessions for startups, one-on-one investor matchmaking, and masterclasses on emerging blockchain and decentralized finance (DeFi) trends.
    </p>
  </section>

  
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
        }, 30);

        // Pause on hover
        showcase.addEventListener('mouseenter', () => { isPillarPaused = true; });
        showcase.addEventListener('mouseleave', () => { isPillarPaused = false; });
      }
    });
  </script>
</div>



<?php require_once __DIR__ . '/includes/footer.php'; ?>
