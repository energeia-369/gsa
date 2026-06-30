<?php
$pageTitle = "Sponsorship & Business | GSA";
require_once __DIR__ . '/config/Database.php';

$successMessage = "";
$errorMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_inquiry'])) {
    $companyName = $_POST['company_name'] ?? '';
    $contactPerson = $_POST['contact_person'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone_number'] ?? '';
    $companyUrl = $_POST['company_url'] ?? '';

    try {
        $partnershipType = $_POST['partnership_type'] ?? '';
        $message = $_POST['message'] ?? '';
        
        $db = (new Database())->getConnection();
        $stmt = $db->prepare("INSERT INTO business_inquiries (company_name, company_url, contact_person, email, phone_number, partnership_type, message) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$companyName, $companyUrl, $contactPerson, $email, $phone, $partnershipType, $message]);
        $successMessage = "Inquiry submitted successfully! Our team will contact you shortly.";
    } catch (Exception $e) {
        $errorMessage = "Failed to submit inquiry. Please try again later.";
    }
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<link rel="stylesheet" href="assets/css/Sponsors.css?v=2">

<div class="sponsors-page">
  <!-- HERO SECTION -->
  <section class="sponsors-hero">
    <p class="tagline">💼 BUSINESS & SPONSORSHIPS</p>
    <h1>Partner with <span>Excellence</span></h1>
    <p class="hero-desc">
      Elevate your brand by aligning with the Global Sports Arena. Reach millions of sports enthusiasts, athletes, and professionals worldwide through strategic partnerships.
    </p>
  </section>

  <!-- STATS SECTION -->
  <section class="stats-section">
    <div class="stat-box">
      <span class="stat-val">5M+</span>
      <span class="stat-lbl">Global Audience</span>
    </div>
    <div class="stat-box">
      <span class="stat-val">200+</span>
      <span class="stat-lbl">Live Events/Yr</span>
    </div>
    <div class="stat-box">
      <span class="stat-val">50+</span>
      <span class="stat-lbl">Brand Partners</span>
    </div>
    <div class="stat-box">
      <span class="stat-val">300%</span>
      <span class="stat-lbl">Avg ROI</span>
    </div>
  </section>

  <!-- PACKAGES SECTION -->
  <section class="packages-section">
    <div class="section-title">
      <h2>Sponsorship Packages</h2>
    </div>
    <div class="packages-grid">
      
      <!-- Package 1 -->
      <div class="package-card">
        <div class="pkg-icon">🥈</div>
        <h3>Become Partner</h3>
        <ul class="pkg-benefits">
          <li>Digital branding on GSA App</li>
          <li>Logo placement on local event banners</li>
          <li>5 VIP event passes per year</li>
          <li>Mentions in monthly newsletters</li>
        </ul>
        <a href="#inquiry" class="pkg-btn">Inquire Now</a>
      </div>

      <!-- Package 2 -->
      <div class="package-card gold-tier">
        <div class="pkg-icon">👑</div>
        <h3>Title Sponsor</h3>
        <ul class="pkg-benefits">
          <li>Naming rights for major tournaments</li>
          <li>Prime TV & Digital broadcasting slots</li>
          <li>50 Elite Lounge event passes</li>
          <li>Exclusive keynote opportunities</li>
          <li>Player jersey branding</li>
        </ul>
        <a href="#inquiry" class="pkg-btn">Inquire Now</a>
      </div>

      <!-- Package 3 -->
      <div class="package-card">
        <div class="pkg-icon">🤝</div>
        <h3>Official Supplier</h3>
        <ul class="pkg-benefits">
          <li>Exclusive product supply rights</li>
          <li>Retail stall spaces at physical events</li>
          <li>Integrated e-commerce promotions</li>
          <li>Co-branded merchandise options</li>
        </ul>
        <a href="#inquiry" class="pkg-btn">Inquire Now</a>
      </div>

    </div>
  </section>

  <!-- INQUIRY FORM -->
  <section class="inquiry-section" id="inquiry">
    <div class="inquiry-form-card">
      <h2>Business Inquiry</h2>
      <?php if (!empty($successMessage)): ?>
        <div style="color: #4CAF50; margin-bottom: 1.5rem; text-align: center; font-weight: bold; background: rgba(76, 175, 80, 0.1); padding: 10px; border-radius: 8px; border: 1px solid #4CAF50;"><?php echo $successMessage; ?></div>
      <?php endif; ?>
      <?php if (!empty($errorMessage)): ?>
        <div style="color: #f44336; margin-bottom: 1.5rem; text-align: center; font-weight: bold; background: rgba(244, 67, 54, 0.1); padding: 10px; border-radius: 8px; border: 1px solid #f44336;"><?php echo $errorMessage; ?></div>
      <?php endif; ?>

      <form action="sponsors.php#inquiry" method="POST">
        <input type="hidden" name="submit_inquiry" value="1">
        <div class="form-group">
          <input type="text" name="company_name" placeholder="Company Name" required>
        </div>
        <div class="form-group">
          <input type="url" name="company_url" placeholder="Company Website URL" required>
        </div>
        <div class="form-group">
          <input type="text" name="contact_person" placeholder="Contact Person" required>
        </div>
        <div class="form-group">
          <input type="email" name="email" placeholder="Business Email" required>
        </div>
        <div class="form-group">
          <input type="text" name="phone_number" placeholder="Phone Number" required>
        </div>
        <div class="form-group">
          <select name="partnership_type" required style="width: 100%; padding: 12px; border: 1px solid rgba(197,168,92,0.3); border-radius: 8px; background: rgba(11,12,16,0.8); color: #fff; font-size: 0.95rem; box-sizing: border-box; cursor: pointer;">
            <option value="" disabled selected>-- Select Partnership Option --</option>
            <option value="Become Partner">Become Partner</option>
            <option value="Title Sponsor">Title Sponsor</option>
            <option value="Official Supplier">Official Supplier</option>
          </select>
        </div>
        <div class="form-group">
          <textarea name="message" rows="4" placeholder="How would you like to partner with us? (Optional Details)"></textarea>
        </div>
        <button type="submit" class="submit-btn">Submit Proposal</button>
      </form>
    </div>
  </section>
</div>



<?php require_once __DIR__ . '/includes/footer.php'; ?>
