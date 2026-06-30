<?php
$pageTitle = "ENERGEIA | About Event";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
require_once __DIR__ . '/config/Database.php';

$team_profiles = [];
try {
    $db = Database::getConnection();
    $stmt = $db->query("SELECT * FROM team_profiles WHERE status = 'active' ORDER BY display_order ASC, id ASC");
    $team_profiles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Silently ignore if table doesn't exist or other db error on frontend
}
?>

<link rel="stylesheet" href="assets/css/AboutEvent.css?v=5">
<link rel="stylesheet" href="assets/css/team-profiles.css?v=1">

<div class="about-page">
  <section class="about-hero">
    <div class="about-hero-content">
      <h1>Welcome to ENERGEIA</h1>
      <p>Sports Event & E-Commerce Platform</p>

      <div class="about-buttons">
        <a href="sports-categories.php" class="about-primary-btn" style="display:inline-block; text-align:center;">
          Explore Now
        </a>
        <a href="products.php" class="about-secondary-btn" style="display:inline-block; text-align:center;">
          Shop Now
        </a>
      </div>
    </div>
  </section>

  <section class="about-info">
    <h2>About ENERGEIA</h2>
    <p>
      ENERGEIA is a complete sports event and e-commerce platform where
      users can register for tournaments, purchase sports products, earn NXL
      credits, redeem cashback rewards, and generate QR-based event passes.
    </p>
  </section>

  <section class="about-features">
    <div class="section-title">
      <span>What We Offer</span>
      <h2>Everything for Sports Lovers</h2>
    </div>

    <div class="feature-grid">
      <div class="about-feature-card" onclick="window.location.href='sports-categories.php'" style="cursor:pointer;">
        <div class="about-feature-icon">🏆</div>
        <h3>Tournament Registration</h3>
        <p>Register for indoor and outdoor sports tournaments with simple online booking.</p>
      </div>
      <div class="about-feature-card" onclick="window.location.href='products.php'" style="cursor:pointer;">
        <div class="about-feature-icon">🛒</div>
        <h3>Sports Store</h3>
        <p>Shop sports shoes, jerseys, rackets, footballs, gym accessories, and more.</p>
      </div>
      <div class="about-feature-card" onclick="window.location.href='credits.php'" style="cursor:pointer;">
        <div class="about-feature-icon">💎</div>
        <h3>NXL Credits</h3>
        <p>Earn reward credits on purchases and redeem them during checkout.</p>
      </div>
      <div class="about-feature-card" onclick="window.location.href='visitor-pass.php'" style="cursor:pointer;">
        <div class="about-feature-icon">📱</div>
        <h3>QR Pass</h3>
        <p>Generate QR-based event passes for smooth entry and verification.</p>
      </div>
    </div>
  </section>

  <section class="how-section">
    <div class="section-title">
      <span>Simple Process</span>
      <h2>How It Works</h2>
    </div>

    <div class="steps-grid">
      <div class="step-card">
        <div class="step-number">01</div>
        <div class="step-icon">📝</div>
        <h3>Register</h3>
        <p>Create your ENERGEIA account.</p>
      </div>
      <div class="step-card">
        <div class="step-number">02</div>
        <div class="step-icon">🏅</div>
        <h3>Choose</h3>
        <p>Select tournament or product.</p>
      </div>
      <div class="step-card">
        <div class="step-number">03</div>
        <div class="step-icon">💳</div>
        <h3>Pay</h3>
        <p>Complete secure online payment.</p>
      </div>
      <div class="step-card">
        <div class="step-number">04</div>
        <div class="step-icon">🎉</div>
        <h3>Enjoy</h3>
        <p>Get QR pass or product delivery.</p>
      </div>
    </div>
  </section>

  <section class="nxl-section">
    <div>
      <span class="nxl-tag">NXL Rewards</span>
      <h2>Earn & Redeem NXL Credits</h2>
      <p>
        Earn NXL credits on every eligible purchase and tournament
        registration. Use credits to reduce checkout price and enjoy special
        cashback rewards.
      </p>

      <div class="nxl-boxes">
        <div onclick="window.location.href='credits.php'">
          <h3>₹1000</h3>
          <p>50 NXL Credits</p>
        </div>
        <div onclick="window.location.href='credits.php'">
          <h3>₹2000</h3>
          <p>100 NXL Credits</p>
        </div>
      </div>

      <a href="credits.php" class="nxl-cta-btn" style="display:inline-block; text-align:center;">
        Go to Credits & Rewards Store Store →
      </a>
    </div>
  </section>



  <section class="about-cta">
    <h2>Ready to Start Your Journey?</h2>
    <p>Join ENERGEIA and experience sports, shopping, and rewards.</p>

    <div class="about-buttons">
      <a href="register.php" class="about-primary-btn" style="display:inline-block; text-align:center;">
        Register Now
      </a>
      <a href="sports-categories.php" class="about-secondary-btn" style="display:inline-block; text-align:center;">
        Browse Events
      </a>
    </div>
  </section>

  <?php if (!empty($team_profiles)): ?>
  <section class="team-section">
    <div class="team-section-title">
      <h2><span>Team</span> Profiles</h2>
      <div class="underline"></div>
    </div>
    
    <div class="team-grid">
      <?php foreach ($team_profiles as $member): ?>
      <div class="team-card">
        <div class="team-img-wrapper">
          <img src="<?php echo htmlspecialchars($member['image'] ?? 'assets/images/placeholder.jpg'); ?>" alt="<?php echo htmlspecialchars($member['name']); ?>">
        </div>
        <h3><?php echo htmlspecialchars($member['name']); ?></h3>
        <?php if (!empty($member['qualification'])): ?>
        <span class="team-qualification">| <?php echo htmlspecialchars($member['qualification']); ?></span>
        <?php endif; ?>
        <div class="team-role"><?php echo htmlspecialchars($member['role']); ?></div>
        <p class="team-desc"><?php echo htmlspecialchars($member['description']); ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>

</div>



<?php require_once __DIR__ . '/includes/footer.php'; ?>
