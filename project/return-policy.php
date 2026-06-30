<?php
$pageTitle = "GLOBAL SPORTS ARENA | Return Policy";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<link rel="stylesheet" href="assets/css/SupportPages.css">

<div class="support-page">
  <!-- Hero Section -->
  <div class="policy-hero">
    <div class="policy-hero-overlay"></div>
    <div class="policy-hero-content">
      <div class="policy-badge">
        🔄 Hassle-Free Returns
      </div>
      <h1>
        Return <span class="highlight">Policy</span>
      </h1>
      <p>
        Your satisfaction is our priority. Read our simple and transparent return guidelines.
      </p>
    </div>
  </div>

  <!-- Main Content -->
  <div class="policy-container">
    <!-- Products Section -->
    <div class="policy-card product-card">
      <div class="card-icon">📦</div>
      <div class="card-badge">Products</div>
      <h2>Product Returns</h2>
      <div class="policy-details">
        <div class="policy-point">
          <span class="point-icon">✅</span>
          <div>
            <strong>7 Days Return Window</strong>
            <p>Products can be returned within 7 days of delivery</p>
          </div>
        </div>
        <div class="policy-point">
          <span class="point-icon">🎯</span>
          <div>
            <strong>Condition Requirements</strong>
            <p>Items must be unused, undamaged, and in original packaging</p>
          </div>
        </div>
        <div class="policy-point">
          <span class="point-icon">🚚</span>
          <div>
            <strong>Free Pickup</strong>
            <p>Free return pickup available for eligible products</p>
          </div>
        </div>
        <div class="policy-point">
          <span class="point-icon">💵</span>
          <div>
            <strong>Full Refund</strong>
            <p>Refund processed within 5-7 business days after verification</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Events Section -->
    <div class="policy-card events-card">
      <div class="card-icon">🎫</div>
      <div class="card-badge danger">Events</div>
      <h2>Event Tickets</h2>
      <div class="policy-details">
        <div class="policy-point">
          <span class="point-icon">⚠️</span>
          <div>
            <strong>Non-Refundable</strong>
            <p>Event tickets cannot be returned after successful booking</p>
          </div>
        </div>
        <div class="policy-point">
          <span class="point-icon">🔄</span>
          <div>
            <strong>Event Cancellation</strong>
            <p>If an event is canceled, full refund will be processed automatically</p>
          </div>
        </div>
        <div class="policy-point">
          <span class="point-icon">📅</span>
          <div>
            <strong>Rescheduling</strong>
            <p>Ticket rescheduling allowed up to 48 hours before the event</p>
          </div>
        </div>
        <div class="policy-point">
          <span class="point-icon">💺</span>
          <div>
            <strong>Seat Upgrades</strong>
            <p>Upgrade your seats anytime with price difference payment</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Process Steps -->
    <div class="process-section">
      <h2>
        Return <span class="highlight">Process</span>
      </h2>
      <div class="process-steps">
        <div class="step">
          <div class="step-number">1</div>
          <div class="step-icon">📞</div>
          <h4>Request Return</h4>
          <p>Contact support or initiate return from your orders page</p>
        </div>
        <div class="step-arrow">→</div>
        <div class="step">
          <div class="step-number">2</div>
          <div class="step-icon">✅</div>
          <h4>Get Approval</h4>
          <p>Receive confirmation and return instructions</p>
        </div>
        <div class="step-arrow">→</div>
        <div class="step">
          <div class="step-number">3</div>
          <div class="step-icon">📦</div>
          <h4>Ship Product</h4>
          <p>Pack item and ship using provided label</p>
        </div>
        <div class="step-arrow">→</div>
        <div class="step">
          <div class="step-number">4</div>
          <div class="step-icon">💰</div>
          <h4>Get Refund</h4>
          <p>Refund issued after quality check</p>
        </div>
      </div>
    </div>

    <!-- FAQ Accordion -->
    <div class="faq-section">
      <h2>
        Frequently Asked <span class="highlight">Questions</span>
      </h2>
      <div class="faq-grid">
        <div class="faq-item">
          <div class="faq-question">
            <span>❓</span>
            <h4>How long does refund take?</h4>
          </div>
          <p>Refunds are processed within 5-7 business days after we receive and verify the returned product.</p>
        </div>
        <div class="faq-item">
          <div class="faq-question">
            <span>❓</span>
            <h4>Who pays for return shipping?</h4>
          </div>
          <p>Return shipping is free for defective products. For other returns, shipping charges may apply.</p>
        </div>
        <div class="faq-item">
          <div class="faq-question">
            <span>❓</span>
            <h4>Can I exchange a product?</h4>
          </div>
          <p>Yes, exchanges are available for size/color variations within 7 days of delivery.</p>
        </div>
        <div class="faq-item">
          <div class="faq-question">
            <span>❓</span>
            <h4>What if I received a damaged product?</h4>
          </div>
          <p>Contact us immediately with photos. We'll arrange free pickup and replacement.</p>
        </div>
      </div>
    </div>

    <!-- Contact CTA -->
    <div class="contact-cta">
      <div class="contact-cta-icon">💬</div>
      <h3>Still have questions?</h3>
      <p>Our support team is here to help you 24/7</p>
      <div class="contact-buttons">
        <button class="contact-btn" onclick="window.location.href='contact.php'">
          Contact Support →
        </button>
        <button class="chat-btn" onclick="window.location.href='faq.php'">
          View FAQs
        </button>
      </div>
    </div>
  </div>
</div>



<?php require_once __DIR__ . '/includes/footer.php'; ?>
