import "../styles/SupportPages.css";
import { useState } from "react";
import axios from "axios";

function ContactUs() {
  const [formData, setFormData] = useState({
    name: "",
    email: "",
    subject: "",
    message: ""
  });
  const [isSubmitted, setIsSubmitted] = useState(false);

  const handleChange = (e) => {
    setFormData({
      ...formData,
      [e.target.name]: e.target.value
    });
  };

const handleSubmit = async (e) => {
  e.preventDefault();

  try {

    await axios.post(
      "http://localhost:8080/api/contact",
      formData
    );

    setIsSubmitted(true);

    setTimeout(() => setIsSubmitted(false), 3000);

    setFormData({
      name: "",
      email: "",
      subject: "",
      message: ""
    });

  } catch (error) {
    console.log("Contact form error:", error);
  }
};

  return (
    <div className="support-page">
      {/* Hero Section */}
      <div className="contact-hero">
        <div className="contact-hero-overlay"></div>
        <div className="contact-hero-content">
          <div className="contact-badge">
            📞 We'd Love to Hear From You
          </div>
          <h1>
            Get in <span className="highlight">Touch</span>
          </h1>
          <p>
            Have questions, feedback, or need assistance? Our team is here to help you 24/7
          </p>
        </div>
      </div>

      {/* Main Content */}
      <div className="contact-container">
        {/* Contact Cards */}
        <div className="contact-cards">
          <div className="contact-card">
            <div className="contact-card-icon">📧</div>
            <h3>Email Us</h3>
            <p>Get a response within 24 hours</p>
            <a href="mailto:support@globalsportsarena.com">support@globalsportsarena.com</a>
            <a href="mailto:careers@globalsportsarena.com">careers@globalsportsarena.com</a>
          </div>

          <div className="contact-card">
            <div className="contact-card-icon">📞</div>
            <h3>Call Us</h3>
            <p>Mon-Sat, 10AM - 7PM</p>
            <a href="tel:+919876543210">+91 98765 43210</a>
            <a href="tel:+918765432109">+91 87654 32109</a>
          </div>

          <div className="contact-card">
            <div className="contact-card-icon">📍</div>
            <h3>Visit Us</h3>
            <p>Come say hello at our office</p>
            <span>Pune, Maharashtra, India</span>
            <span>📍 123 Sports Complex, Pune</span>
          </div>

          <div className="contact-card">
            <div className="contact-card-icon">💬</div>
            <h3>Live Chat</h3>
            <p>Available 24/7 for quick queries</p>
            <button className="chat-now-btn" onClick={() => alert("Live chat support coming soon!")}>
              Start Chat →
            </button>
          </div>
        </div>

        {/* Contact Form & Map Section */}
        <div className="contact-form-section">
          <div className="form-container">
            <div className="form-header">
              <div className="form-icon">✉️</div>
              <h2>Send Us a <span className="highlight">Message</span></h2>
              <p>We'll get back to you as soon as possible</p>
            </div>

            {isSubmitted && (
              <div className="success-message">
                ✅ Thank you for reaching out! We'll respond within 24 hours.
              </div>
            )}

            <form onSubmit={handleSubmit} className="contact-form">
              <div className="form-group">
                <label>Your Name *</label>
                <div className="input-icon">
                  <span>👤</span>
                  <input
                    type="text"
                    name="name"
                    placeholder="John Doe"
                    value={formData.name}
                    onChange={handleChange}
                    required
                  />
                </div>
              </div>

              <div className="form-group">
                <label>Email Address *</label>
                <div className="input-icon">
                  <span>📧</span>
                  <input
                    type="email"
                    name="email"
                    placeholder="john@example.com"
                    value={formData.email}
                    onChange={handleChange}
                    required
                  />
                </div>
              </div>

              <div className="form-group">
                <label>Subject *</label>
                <div className="input-icon">
                  <span>📝</span>
                  <input
                    type="text"
                    name="subject"
                    placeholder="How can we help you?"
                    value={formData.subject}
                    onChange={handleChange}
                    required
                  />
                </div>
              </div>

              <div className="form-group">
                <label>Message *</label>
                <div className="input-icon textarea-icon">
                  <span>💬</span>
                  <textarea
                    name="message"
                    placeholder="Tell us more about your query..."
                    rows="5"
                    value={formData.message}
                    onChange={handleChange}
                    required
                  ></textarea>
                </div>
              </div>

              <button type="submit" className="submit-btn">
                Send Message <span>→</span>
              </button>
            </form>

            <div className="form-footer">
              <p>📱 You can also reach us on WhatsApp at +91 98765 43210</p>
            </div>
          </div>

          <div className="map-container">
            <div className="map-card">
              <h3>📍 Our Location</h3>
              <div className="map-placeholder">
                <div className="map-icon">🗺️</div>
                <p>123 Sports Complex,</p>
                <p>Near City Center Mall,</p>
                <p>Pune, Maharashtra - 411001</p>
                <button className="directions-btn" onClick={() => window.open("https://maps.google.com", "_blank")}>
                  Get Directions →
                </button>
              </div>
            </div>

            <div className="business-hours">
              <h3>⏰ Business Hours</h3>
              <div className="hours-list">
                <div className="hour-item">
                  <span>Monday - Friday</span>
                  <span>10:00 AM - 7:00 PM</span>
                </div>
                <div className="hour-item">
                  <span>Saturday</span>
                  <span>10:00 AM - 5:00 PM</span>
                </div>
                <div className="hour-item">
                  <span>Sunday</span>
                  <span>Closed</span>
                </div>
                <div className="hour-item support">
                  <span>📞 Support Helpline</span>
                  <span>24/7 Available</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        {/* FAQ Section */}
        <div className="contact-faq">
          <h2>Frequently Asked <span className="highlight">Questions</span></h2>
          <div className="faq-grid">
            <div className="faq-item">
              <div className="faq-question">
                <span>❓</span>
                <h4>How quickly do you respond to emails?</h4>
              </div>
              <p>We typically respond within 24 hours on business days.</p>
            </div>
            <div className="faq-item">
              <div className="faq-question">
                <span>❓</span>
                <h4>Do you have a physical store?</h4>
              </div>
              <p>Yes, we have our headquarters in Pune. Visit us during business hours!</p>
            </div>
            <div className="faq-item">
              <div className="faq-question">
                <span>❓</span>
                <h4>Can I cancel my booking through phone?</h4>
              </div>
              <p>Yes, our support team can help you with cancellations via phone.</p>
            </div>
            <div className="faq-item">
              <div className="faq-question">
                <span>❓</span>
                <h4>Do you offer bulk booking discounts?</h4>
              </div>
              <p>Yes, contact our sales team for group booking discounts.</p>
            </div>
          </div>
        </div>

        {/* Social Connect */}
        <div className="social-connect">
          <div className="social-header">
            <span>🌐</span>
            <h3>Connect With Us on Social Media</h3>
          </div>
          <div className="social-links-large">
            <a href="#" className="social-link facebook">📘 Facebook</a>
            <a href="#" className="social-link instagram">📸 Instagram</a>
            <a href="#" className="social-link twitter">🐦 Twitter</a>
            <a href="#" className="social-link linkedin">💼 LinkedIn</a>
            <a href="#" className="social-link youtube">🎥 YouTube</a>
          </div>
          <p className="social-tag">Follow us for updates, offers, and event announcements!</p>
        </div>
      </div>
    </div>
  );
}

export default ContactUs;