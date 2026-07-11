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
    
    // Fetch approved client reviews, newest first
    $stmt2 = $db->query("SELECT * FROM client_reviews WHERE status = 'approved' ORDER BY created_at DESC LIMIT 36");
    $client_reviews = $stmt2->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Silently ignore if tables don't exist
    $client_reviews = [];
}
?>

<link rel="stylesheet" href="assets/css/AboutEvent.css?v=5">
<link rel="stylesheet" href="assets/css/team-profiles.css?v=1">

<div class="about-page">
  <section class="about-hero">
    <div class="about-hero-content">
      <h1>Welcome to ENERGEIA</h1>
      <p class="hero-desc">Nexus Business Summit, Maytriya Franchise Meet,<br> Sports Event & E-Commerce Platform</p>

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

  <!-- Reviews / Testimonials Section -->
  <?php if (!empty($client_reviews)): ?>
  <section id="reviews" class="reviews-section" style="padding: 5rem 5%; background-color: rgba(197, 168, 92, 0.03); border-top: 1px solid rgba(197, 168, 92, 0.1);">
    <div style="text-align: center; margin-bottom: 3.5rem;">
      <h2 style="font-size: 2.2rem; font-family: 'Playfair Display', serif; color: #1a1a1a; margin-bottom: 1rem;"><span>Client</span> Reviews</h2>
      <div style="width: 60px; height: 3px; background: #c5a85c; margin: 0 auto;"></div>
      <p style="color: #666; margin-top: 1rem; font-size: 0.95rem;">Hear what our community has to say about their ENERGEIA experience.</p>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(6, 1fr); gap: 1.5rem; max-width: 95%; margin: 0 auto; overflow-x: auto; padding-bottom: 2rem;">
      
      <?php foreach ($client_reviews as $index => $review): ?>
      <div style="background: #fff; padding: 1.8rem; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); position: relative; border: 1px solid rgba(197, 168, 92, 0.2); transition: transform 0.3s ease; cursor: pointer; <?php if($index % 2 !== 0) echo 'transform: translateY(-8px);'; ?>"
           onclick="openReadReviewModal('<?php echo htmlspecialchars(addslashes($review['name'])); ?>', '<?php echo htmlspecialchars(addslashes($review['role'])); ?>', <?php echo $review['rating']; ?>, '<?php echo htmlspecialchars(addslashes($review['review_text'])); ?>')">
        <div style="font-size: 2rem; color: rgba(197, 168, 92, 0.15); position: absolute; top: 1rem; right: 1.5rem; font-family: serif;">"</div>
        <div style="display: flex; gap: 4px; color: #f59e0b; margin-bottom: 0.8rem; font-size: 0.85rem;">
          <?php for($i = 1; $i <= 5; $i++): ?>
            <?php if($i <= $review['rating']): ?>
              <i class="fa-solid fa-star"></i>
            <?php else: ?>
              <i class="fa-regular fa-star" style="color: #d4c8b2;"></i>
            <?php endif; ?>
          <?php endfor; ?>
        </div>
        <p style="color: #4a4a4a; line-height: 1.6; font-size: 0.85rem; font-style: italic; margin-bottom: 1.2rem; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis;">
          "<?php echo htmlspecialchars($review['review_text']); ?>"
        </p>
        <div style="display: flex; align-items: center; gap: 0.8rem;">
          <div style="width: 38px; height: 38px; border-radius: 50%; background: #c5a85c; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: bold; font-size: 1rem;">
            <?php echo strtoupper(substr(htmlspecialchars($review['name']), 0, 1)); ?>
          </div>
          <div>
            <h4 style="margin: 0; color: #1a1a1a; font-size: 0.9rem;"><?php echo htmlspecialchars($review['name']); ?></h4>
            <span style="font-size: 0.75rem; color: #888;"><?php echo htmlspecialchars($review['role']); ?></span>
          </div>
        </div>
      </div>
      <?php endforeach; ?>

    </div>
  </section>

  <!-- Read Review Modal -->
  <div id="readReviewModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(5px);">
    <div style="background: #ffffff; padding: 2.5rem; border-radius: 12px; width: 90%; max-width: 550px; position: relative; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
      <button onclick="closeReadReviewModal()" style="position: absolute; top: 1rem; right: 1.5rem; background: transparent; border: none; font-size: 1.5rem; cursor: pointer; color: #666;">&times;</button>
      
      <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem; border-bottom: 1px solid #eee; padding-bottom: 1rem;">
        <div id="modalReviewerInitial" style="width: 50px; height: 50px; border-radius: 50%; background: #c5a85c; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: bold; font-size: 1.4rem;">
          A
        </div>
        <div>
          <h4 id="modalReviewerName" style="margin: 0; color: #1a1a1a; font-size: 1.2rem; font-family: 'Playfair Display', serif;">Name</h4>
          <span id="modalReviewerRole" style="font-size: 0.85rem; color: #888;">Role</span>
        </div>
      </div>
      
      <div id="modalReviewStars" style="display: flex; gap: 4px; color: #f59e0b; margin-bottom: 1.2rem; font-size: 1.1rem;">
        <!-- Stars injected by JS -->
      </div>
      
      <p id="modalReviewText" style="color: #4a4a4a; line-height: 1.8; font-size: 1rem; font-style: italic; white-space: pre-wrap;">
        <!-- Text injected by JS -->
      </p>
    </div>
  </div>

  <script>
    function openReadReviewModal(name, role, rating, text) {
      document.getElementById('modalReviewerName').innerText = name;
      document.getElementById('modalReviewerRole').innerText = role;
      document.getElementById('modalReviewerInitial').innerText = name.charAt(0).toUpperCase();
      document.getElementById('modalReviewText').innerText = '"' + text + '"';
      
      let starsHtml = '';
      for(let i=1; i<=5; i++) {
        if(i <= rating) {
          starsHtml += '<i class="fa-solid fa-star"></i>';
        } else {
          starsHtml += '<i class="fa-regular fa-star" style="color: #d4c8b2;"></i>';
        }
      }
      document.getElementById('modalReviewStars').innerHTML = starsHtml;
      
      document.getElementById('readReviewModal').style.display = 'flex';
    }
    
    function closeReadReviewModal() {
      document.getElementById('readReviewModal').style.display = 'none';
    }
  </script>
  <?php endif; ?>

</div>


<?php require_once __DIR__ . '/includes/footer.php'; ?>
