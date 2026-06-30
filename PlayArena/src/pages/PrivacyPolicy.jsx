// PrivacyPolicy.jsx

import "../styles/SupportPages.css";
import { useNavigate } from "react-router-dom";

function PrivacyPolicy() {
  const navigate = useNavigate();

  return (
    <div className="support-page">
      {/* Hero Section */}
      <div className="privacy-hero">
        <div className="privacy-hero-overlay"></div>
        <div className="privacy-hero-content">
          <div className="privacy-badge">
            🔒 Your Privacy Matters
          </div>
          <h1>
            Privacy <span className="highlight">Policy</span>
          </h1>
          <p>
            We are committed to protecting your personal information and being transparent about how we use it
          </p>
          <div className="last-updated">
            🛡️ Last Updated: January 1, 2026 | Version 2.0
          </div>
        </div>
      </div>

      {/* Main Content */}
      <div className="privacy-container">
        {/* Trust Badge */}
        <div className="trust-badge">
          <div className="trust-item">
            <span>🔐</span>
            <p>SSL Encrypted</p>
          </div>
          <div className="trust-item">
            <span>🛡️</span>
            <p>GDPR Compliant</p>
          </div>
          <div className="trust-item">
            <span>✅</span>
            <p>PCI Certified</p>
          </div>
          <div className="trust-item">
            <span>📋</span>
            <p>ISO 27001</p>
          </div>
        </div>

        {/* Information We Collect */}
        <div className="privacy-card info-collect">
          <div className="card-header">
            <div className="card-icon">📋</div>
            <h2>Information We Collect</h2>
          </div>
          <div className="info-grid">
            <div className="info-item">
              <div className="info-icon">👤</div>
              <h4>Personal Information</h4>
              <ul>
                <li>Full Name</li>
                <li>Email Address</li>
                <li>Phone Number</li>
                <li>Date of Birth</li>
              </ul>
            </div>
            <div className="info-item">
              <div className="info-icon">📦</div>
              <h4>Order Information</h4>
              <ul>
                <li>Event Bookings</li>
                <li>Product Purchases</li>
                <li>Payment Status</li>
                <li>Delivery Address</li>
              </ul>
            </div>
            <div className="info-item">
              <div className="info-icon">💻</div>
              <h4>Technical Data</h4>
              <ul>
                <li>IP Address</li>
                <li>Browser Type</li>
                <li>Device Information</li>
                <li>Usage Analytics</li>
              </ul>
            </div>
            <div className="info-item">
              <div className="info-icon">💳</div>
              <h4>Payment Details</h4>
              <ul>
                <li>Transaction ID</li>
                <li>Payment Method</li>
                <li>Billing Address</li>
                <li>Invoice History</li>
              </ul>
            </div>
          </div>
        </div>

        {/* How We Use Your Info */}
        <div className="privacy-card use-info">
          <div className="card-header">
            <div className="card-icon">🎯</div>
            <h2>How We Use Your Information</h2>
          </div>
          <div className="uses-grid">
            <div className="use-item">
              <div className="use-number">01</div>
              <h4>Process Transactions</h4>
              <p>Complete your event bookings and product purchases securely</p>
            </div>
            <div className="use-item">
              <div className="use-number">02</div>
              <h4>Send Notifications</h4>
              <p>Order confirmations, event reminders, and delivery updates</p>
            </div>
            <div className="use-item">
              <div className="use-number">03</div>
              <h4>Improve Services</h4>
              <p>Analyze usage patterns to enhance your experience</p>
            </div>
            <div className="use-item">
              <div className="use-number">04</div>
              <h4>Customer Support</h4>
              <p>Resolve queries and provide personalized assistance</p>
            </div>
            <div className="use-item">
              <div className="use-number">05</div>
              <h4>Legal Compliance</h4>
              <p>Fulfill regulatory requirements and prevent fraud</p>
            </div>
            <div className="use-item">
              <div className="use-number">06</div>
              <h4>Send Offers</h4>
              <p>Share exclusive deals and personalized recommendations</p>
            </div>
          </div>
        </div>

        {/* Data Sharing */}
        <div className="privacy-card sharing">
          <div className="card-header">
            <div className="card-icon">🤝</div>
            <h2>Information Sharing</h2>
          </div>
          <div className="sharing-content">
            <div className="sharing-note positive">
              <span>✅</span>
              <div>
                <strong>We DO NOT Share:</strong>
                <p>Your personal information is never sold, rented, or traded with third parties for marketing purposes without your explicit consent.</p>
              </div>
            </div>
            <div className="sharing-note neutral">
              <span>🔄</span>
              <div>
                <strong>We MAY Share With:</strong>
                <p>Trusted service providers who assist in operations (payment gateways, delivery partners) - they follow strict confidentiality agreements.</p>
              </div>
            </div>
            <div className="sharing-note legal">
              <span>⚖️</span>
              <div>
                <strong>Legal Requirements:</strong>
                <p>We may disclose information if required by law, court order, or to protect our rights and safety.</p>
              </div>
            </div>
          </div>
        </div>

        {/* Data Security */}
        <div className="privacy-card security">
          <div className="card-header">
            <div className="card-icon">🔒</div>
            <h2>Data Security Measures</h2>
          </div>
          <div className="security-grid">
            <div className="security-item">
              <span>🔐</span>
              <div>
                <h4>End-to-End Encryption</h4>
                <p>All data transmitted is encrypted using TLS 1.3 protocol</p>
              </div>
            </div>
            <div className="security-item">
              <span>🛡️</span>
              <div>
                <h4>Firewall Protection</h4>
                <p>Advanced firewalls to prevent unauthorized access</p>
              </div>
            </div>
            <div className="security-item">
              <span>📊</span>
              <div>
                <h4>Regular Audits</h4>
                <p>Security audits conducted monthly by third-party experts</p>
              </div>
            </div>
            <div className="security-item">
              <span>💾</span>
              <div>
                <h4>Secure Storage</h4>
                <p>Data stored in PCI-compliant, encrypted databases</p>
              </div>
            </div>
          </div>
        </div>

        {/* Your Rights */}
        <div className="privacy-card rights">
          <div className="card-header">
            <div className="card-icon">👤</div>
            <h2>Your Privacy Rights</h2>
          </div>
          <div className="rights-grid">
            <div className="right-item">
              <div className="right-icon">👁️</div>
              <h4>Right to Access</h4>
              <p>Request a copy of your personal data we hold</p>
            </div>
            <div className="right-item">
              <div className="right-icon">✏️</div>
              <h4>Right to Rectify</h4>
              <p>Correct inaccurate or incomplete information</p>
            </div>
            <div className="right-item">
              <div className="right-icon">🗑️</div>
              <h4>Right to Delete</h4>
              <p>Request deletion of your personal data</p>
            </div>
            <div className="right-item">
              <div className="right-icon">🚫</div>
              <h4>Right to Opt-Out</h4>
              <p>Unsubscribe from marketing communications</p>
            </div>
            <div className="right-item">
              <div className="right-icon">📦</div>
              <h4>Data Portability</h4>
              <p>Receive your data in a machine-readable format</p>
            </div>
            <div className="right-item">
              <div className="right-icon">🛑</div>
              <h4>Right to Restrict</h4>
              <p>Limit how we process your information</p>
            </div>
          </div>
        </div>

        {/* Cookies & Tracking */}
        <div className="privacy-card cookies">
          <div className="card-header">
            <div className="card-icon">🍪</div>
            <h2>Cookies & Tracking Technologies</h2>
          </div>
          <div className="cookies-content">
            <p>
              We use cookies and similar technologies to enhance your browsing experience, 
              analyze site traffic, and personalize content. You can manage cookie preferences 
              through your browser settings.
            </p>
            <div className="cookie-types">
              <div className="cookie-type">
                <strong>Essential Cookies</strong>
                <span>Required for basic site functionality</span>
              </div>
              <div className="cookie-type">
                <strong>Analytics Cookies</strong>
                <span>Help us understand user behavior</span>
              </div>
              <div className="cookie-type">
                <strong>Preference Cookies</strong>
                <span>Remember your settings and preferences</span>
              </div>
              <div className="cookie-type">
                <strong>Marketing Cookies</strong>
                <span>Used for relevant advertisements (opt-out available)</span>
              </div>
            </div>
          </div>
        </div>

        {/* Children's Privacy */}
        <div className="privacy-card children">
          <div className="card-header">
            <div className="card-icon">👶</div>
            <h2>Children's Privacy</h2>
          </div>
          <div className="children-content">
            <p>
              GLOBAL SPORTS ARENA does not knowingly collect personal information from children under 13 years of age. 
              If you believe a child has provided us with personal data, please contact us immediately.
            </p>
          </div>
        </div>

        {/* Policy Updates */}
        <div className="privacy-card updates">
          <div className="card-header">
            <div className="card-icon">📢</div>
            <h2>Policy Updates</h2>
          </div>
          <div className="updates-content">
            <p>
              We may update this Privacy Policy periodically to reflect changes in our practices or legal requirements. 
              We will notify you of any material changes by posting the new policy on this page and updating the "Last Updated" date.
            </p>
            <div className="update-notice">
              <span>💡</span>
              <p>Sign up for our newsletter to receive privacy policy update notifications</p>
            </div>
          </div>
        </div>

        {/* Contact Section */}
        <div className="privacy-contact">
          <div className="contact-icon">📧</div>
          <h3>Have Questions About Your Privacy?</h3>
          <p>
            Our Data Protection Officer is here to help address your privacy concerns and exercise your data rights.
          </p>
          <div className="contact-buttons">
            <button className="contact-btn" onClick={() => navigate("/contact-us")}>
              Contact Privacy Team →
            </button>
            <button className="request-btn" onClick={() => navigate("/contact-us")}>
              Submit Data Request
            </button>
          </div>
          <div className="contact-info">
            <p>📞 +91 12345 67890</p>
            <p>✉️ privacy@globalsportsarena.com</p>
            <p>📍 Data Protection Office, Mumbai, India</p>
          </div>
        </div>

        {/* Consent Banner */}
        <div className="consent-note">
          <p>
            By using GLOBAL SPORTS ARENA, you consent to the collection and use of your information as described in this Privacy Policy.
          </p>
        </div>
      </div>
    </div>
  );
}

export default PrivacyPolicy;