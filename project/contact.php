<?php
$pageTitle = "GLOBAL SPORTS ARENA | Get In Touch";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<link rel="stylesheet" href="assets/css/SupportPages.css?v=2">

<div class="support-page">
  <!-- Hero Section -->
  <div class="contact-hero">
    <div class="contact-hero-overlay"></div>
    <div class="contact-hero-content">
      <div class="contact-badge">
        📞 We'd Love to Hear From You
      </div>
      <h1>
        Get in <span class="highlight">Touch</span>
      </h1>
      <p>
        Have questions, feedback, or need assistance? Our team is here to help you 24/7
      </p>
    </div>
  </div>

  <!-- Main Content -->
  <div class="contact-container max-w-7xl mx-auto px-4 py-12">
    <!-- Contact Cards -->
    <div class="contact-cards grid grid-cols-1 sm:grid-cols-2 gap-6 mb-12 max-w-3xl mx-auto">
      <div class="contact-card">
        <div class="contact-card-icon">📧</div>
        <h3>Email Us</h3>
        <p>Get a response within 24 hours</p>
        <a href="mailto:support@globalsportsarena.com">support@energia369.com</a>
      </div>



      <div class="contact-card">
        <div class="contact-card-icon">💬</div>
        <h3>E.V.A. Chat-bot</h3>
        <p>Available 24/7 for quick queries</p>
        <button class="chat-now-btn" onclick="window.location.href='chatbot.php'">
          Start Chat →
        </button>
      </div>
    </div>

    <!-- Contact Form & Map Section -->
    <div class="contact-form-section max-w-2xl mx-auto">
      <div class="form-container">
        <div class="form-header">
          <div class="form-icon">✉️</div>
          <h2>Send Us a <span class="highlight">Message</span></h2>
          <p>We'll get back to you as soon as possible</p>
        </div>

        <div class="success-message" id="contactSuccessMessage" style="display: none;">
          ✅ Thank you for reaching out! We'll respond within 24 hours.
        </div>

        <form id="contactForm" class="contact-form">
          <div class="form-group">
            <label>Your Name *</label>
            <div class="input-icon">
              <span>👤</span>
              <input
                type="text"
                id="contactName"
                placeholder="John Doe"
                required
              />
            </div>
          </div>

          <div class="form-group">
            <label>Email Address *</label>
            <div class="input-icon">
              <span>📧</span>
              <input
                type="email"
                id="contactEmail"
                placeholder="john@example.com"
                required
              />
            </div>
          </div>

          <div class="form-group">
            <label>Subject *</label>
            <div class="input-icon">
              <span>📝</span>
              <input
                type="text"
                id="contactSubject"
                placeholder="How can we help you?"
                required
              />
            </div>
          </div>

          <div class="form-group">
            <label>Message *</label>
            <div class="input-icon textarea-icon">
              <span>💬</span>
              <textarea
                id="contactMessage"
                placeholder="Tell us more about your query..."
                rows="5"
                required
              ></textarea>
            </div>
          </div>

          <button type="submit" class="submit-btn">
            Send Message <span>→</span>
          </button>
        </form>

        <div class="form-footer">
          <p>📱 You can also reach us on WhatsApp at +91 98765 43210</p>
        </div>
      </div>

      <div class="map-container">
        <div class="map-card">
          <h3>📍 Our Location</h3>
          <div class="map-placeholder">
            <div class="map-icon">🗺️</div>
            <p>123 Sports Complex,</p>
            <p>Near City Center Mall,</p>
            <p>Pune, Maharashtra - 411001</p>
            <button class="directions-btn" onclick="window.open('https://maps.google.com', '_blank')">
              Get Directions →
            </button>
          </div>
        </div>

        <div class="business-hours">
          <h3>⏰ Business Hours</h3>
          <div class="hours-list">
            <div class="hour-item">
              <span>Monday - Friday</span>
              <span>10:00 AM - 7:00 PM</span>
            </div>
            <div class="hour-item">
              <span>Saturday</span>
              <span>10:00 AM - 5:00 PM</span>
            </div>
            <div class="hour-item">
              <span>Sunday</span>
              <span>Closed</span>
            </div>
            <div class="hour-item support">
              <span>📞 Support Helpline</span>
              <span>24/7 Available</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- FAQ Section -->
    <div class="contact-faq">
      <h2>Frequently Asked <span class="highlight">Questions</span></h2>
      <div class="faq-grid">
        <div class="faq-item">
          <div class="faq-question">
            <span>❓</span>
            <h4>How quickly do you respond to emails?</h4>
          </div>
          <p>We typically respond within 24 hours on business days.</p>
        </div>
        <div class="faq-item">
          <div class="faq-question">
            <span>❓</span>
            <h4>Do you have a physical store?</h4>
          </div>
          <p>Yes, we have our headquarters in Pune. Visit us during business hours!</p>
        </div>
        <div class="faq-item">
          <div class="faq-question">
            <span>❓</span>
            <h4>Can I cancel my booking through phone?</h4>
          </div>
          <p>Yes, our support team can help you with cancellations via phone.</p>
        </div>
        <div class="faq-item">
          <div class="faq-question">
            <span>❓</span>
            <h4>Do you offer bulk booking discounts?</h4>
          </div>
          <p>Yes, contact our sales team for group booking discounts.</p>
        </div>
      </div>
    </div>

    <!-- Social Connect -->
    <div class="social-connect">
      <div class="social-header">
        <span><i class="fa-solid fa-globe"></i></span>
        <h3>Connect With Us on Social Media</h3>
      </div>
      <div class="social-links-large">
        <a href="#" class="social-link facebook"><i class="fa-brands fa-facebook-f" style="font-size: 1.5rem;"></i></a>
        <a href="#" class="social-link instagram"><i class="fa-brands fa-instagram" style="font-size: 1.5rem;"></i></a>
        <a href="#" class="social-link twitter"><i class="fa-brands fa-twitter" style="font-size: 1.5rem;"></i></a>
        <a href="#" class="social-link youtube"><i class="fa-brands fa-youtube" style="font-size: 1.5rem;"></i></a>
      </div>
      <p class="social-tag">Follow us for updates, offers, and event announcements!</p>
    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById("contactForm");
    const successMsg = document.getElementById("contactSuccessMessage");

    form.addEventListener("submit", async function(e) {
        e.preventDefault();

        const payload = {
            name: document.getElementById("contactName").value,
            email: document.getElementById("contactEmail").value,
            subject: document.getElementById("contactSubject").value,
            message: document.getElementById("contactMessage").value
        };

        try {
            const res = await fetch("api/index.php/contact", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(payload)
            });

            successMsg.style.display = "block";
            form.reset();
            
            setTimeout(() => {
                successMsg.style.display = "none";
            }, 3000);
        } catch(err) {
            console.error("Contact form error:", err);
        }
    });
});
</script>



<?php require_once __DIR__ . '/includes/footer.php'; ?>
