// TermsConditions.jsx

import "../styles/SupportPages.css";
import { useNavigate } from "react-router-dom";

function TermsConditions() {
  const navigate = useNavigate();

  return (
    <div className="support-page">
      {/* Hero Section */}
      <div className="terms-hero">
        <div className="terms-hero-overlay"></div>
        <div className="terms-hero-content">
          <div className="terms-badge">
            ⚖️ Legal Agreement
          </div>
          <h1>
            Terms & <span className="highlight">Conditions</span>
          </h1>
          <p>
            Please read these terms carefully before using GLOBAL SPORTS ARENA platform
          </p>
          <div className="last-updated">
            📅 Last Updated: January 1, 2026
          </div>
        </div>
      </div>

      {/* Main Content */}
      <div className="terms-container">
        {/* Acceptance Section */}
        <div className="terms-card acceptance-card">
          <div className="card-icon">✅</div>
          <h2>Acceptance of Terms</h2>
          <p>
            By accessing or using GLOBAL SPORTS ARENA website, mobile application, or any of our services, 
            you agree to be bound by these Terms & Conditions. If you do not agree with any part 
            of these terms, please do not use our platform.
          </p>
        </div>

        {/* Quick Navigation */}
        <div className="quick-nav">
          <h3>📑 Quick Navigation</h3>
          <div className="nav-links-group">
            <a href="#platform-rules">Platform Rules</a>
            <a href="#payment-policies">Payment Policies</a>
            <a href="#booking-policies">Booking Policies</a>
            <a href="#user-guidelines">User Guidelines</a>
            <a href="#availability">Availability</a>
            <a href="#liability">Liability</a>
          </div>
        </div>

        {/* Platform Rules */}
        <div id="platform-rules" className="terms-card">
          <div className="card-header">
            <div className="card-icon">📋</div>
            <h2>Platform Rules</h2>
          </div>
          <div className="terms-content">
            <ul>
              <li>You must be at least 18 years old to register an account</li>
              <li>Provide accurate and complete registration information</li>
              <li>Maintain the confidentiality of your account credentials</li>
              <li>Notify us immediately of any unauthorized account access</li>
              <li>Do not impersonate any other person or entity</li>
              <li>Prohibited from using automated bots or scripts on our platform</li>
              <li>Respect intellectual property rights of GLOBAL SPORTS ARENA and third parties</li>
              <li>Do not engage in any fraudulent or unlawful activities</li>
            </ul>
          </div>
        </div>

        {/* Payment Policies */}
        <div id="payment-policies" className="terms-card">
          <div className="card-header">
            <div className="card-icon">💳</div>
            <h2>Payment Policies</h2>
          </div>
          <div className="terms-content">
            <div className="policy-grid">
              <div className="policy-item">
                <span className="policy-icon">💵</span>
                <div>
                  <h4>Secure Transactions</h4>
                  <p>All payments are processed through encrypted and secure payment gateways</p>
                </div>
              </div>
              <div className="policy-item">
                <span className="policy-icon">🔄</span>
                <div>
                  <h4>Multiple Payment Options</h4>
                  <p>Credit/Debit cards, UPI, NetBanking, Digital wallets, and EMI options available</p>
                </div>
              </div>
              <div className="policy-item">
                <span className="policy-icon">💰</span>
                <div>
                  <h4>Pricing Accuracy</h4>
                  <p>We strive for accurate pricing, but errors may occur. We reserve right to correct any errors</p>
                </div>
              </div>
              <div className="policy-item">
                <span className="policy-icon">🏦</span>
                <div>
                  <h4>Currency</h4>
                  <p>All transactions are processed in Indian Rupees (INR)</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        {/* Booking Policies */}
        <div id="booking-policies" className="terms-card">
          <div className="card-header">
            <div className="card-icon">🎟️</div>
            <h2>Booking Policies</h2>
          </div>
          <div className="terms-content">
            <div className="booking-rules">
              <div className="rule">
                <div className="rule-icon">📅</div>
                <div className="rule-text">
                  <h4>Booking Confirmation</h4>
                  <p>Bookings are confirmed only after successful payment and receipt of confirmation email/SMS</p>
                </div>
              </div>
              <div className="rule">
                <div className="rule-icon">⚠️</div>
                <div className="rule-text">
                  <h4>Cancellation Policy</h4>
                  <p>Event tickets cannot be canceled after booking. Product orders can be canceled within 24 hours</p>
                </div>
              </div>
              <div className="rule">
                <div className="rule-icon">🔄</div>
                <div className="rule-text">
                  <h4>Rescheduling</h4>
                  <p>Ticket rescheduling allowed up to 48 hours before event (subject to availability)</p>
                </div>
              </div>
              <div className="rule">
                <div className="rule-icon">👥</div>
                <div className="rule-text">
                  <h4>Group Bookings</h4>
                  <p>Group discounts available for 10+ tickets. Contact support for bulk booking</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        {/* User Guidelines */}
        <div id="user-guidelines" className="terms-card">
          <div className="card-header">
            <div className="card-icon">👤</div>
            <h2>User Guidelines</h2>
          </div>
          <div className="terms-content">
            <div className="guidelines-grid">
              <div className="guideline positive">
                <span>👍</span>
                <h4>Do's</h4>
                <ul>
                  <li>Respect other users and event attendees</li>
                  <li>Follow venue rules and safety guidelines</li>
                  <li>Report any issues to support team</li>
                  <li>Keep your account information updated</li>
                </ul>
              </div>
              <div className="guideline negative">
                <span>👎</span>
                <h4>Don'ts</h4>
                <ul>
                  <li>Share your account credentials</li>
                  <li>Resell tickets at inflated prices</li>
                  <li>Use offensive or abusive language</li>
                  <li>Attempt to hack or disrupt platform services</li>
                </ul>
              </div>
            </div>
          </div>
        </div>

        {/* Availability */}
        <div id="availability" className="terms-card">
          <div className="card-header">
            <div className="card-icon">📊</div>
            <h2>Availability & Modifications</h2>
          </div>
          <div className="terms-content">
            <div className="availability-content">
              <p>
                Event bookings and product purchases are subject to availability. 
                We reserve the right to modify, suspend, or discontinue any aspect 
                of our services at any time without prior notice.
              </p>
              <div className="availability-note">
                <span>ℹ️</span>
                <p>In case of event cancellation by organizers, full refund will be processed automatically</p>
              </div>
            </div>
          </div>
        </div>

        {/* Liability */}
        <div id="liability" className="terms-card">
          <div className="card-header">
            <div className="card-icon">⚖️</div>
            <h2>Limitation of Liability</h2>
          </div>
          <div className="terms-content">
            <div className="liability-content">
              <p>
                GLOBAL SPORTS ARENA shall not be liable for any indirect, incidental, special, consequential, 
                or punitive damages arising from your use of our platform. Our total liability 
                shall not exceed the amount paid by you for the specific product or service.
              </p>
              <div className="liability-grid">
                <div className="liability-item">
                  <strong>Not Liable For:</strong>
                  <ul>
                    <li>Technical glitches beyond our control</li>
                    <li>Third-party conduct at events</li>
                    <li>Force majeure events</li>
                    <li>User-generated content issues</li>
                  </ul>
                </div>
                <div className="liability-item">
                  <strong>Coverage Includes:</strong>
                  <ul>
                    <li>Secure payment processing</li>
                    <li>Authentic product guarantee</li>
                    <li>Valid event tickets</li>
                    <li>Customer support assistance</li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>

        {/* Governing Law */}
        <div className="terms-card">
          <div className="card-header">
            <div className="card-icon">🏛️</div>
            <h2>Governing Law</h2>
          </div>
          <div className="terms-content">
            <p>
              These terms shall be governed by and construed in accordance with the laws of India. 
              Any disputes arising shall be subject to the exclusive jurisdiction of courts in Mumbai, India.
            </p>
          </div>
        </div>

        {/* Contact & Agreement */}
        <div className="agreement-section">
          <div className="agreement-icon">📧</div>
          <h3>Questions About Terms?</h3>
          <p>
            If you have any questions regarding these Terms & Conditions, please contact our legal team.
          </p>
          <div className="agreement-buttons">
            <button className="contact-btn" onClick={() => navigate("/contact-us")}>
              Contact Support →
            </button>
            <button className="legal-btn" onClick={() => navigate("/privacy-policy")}>
              Privacy Policy
            </button>
          </div>
          <div className="agreement-note">
            <p>By continuing to use GLOBAL SPORTS ARENA, you acknowledge that you have read and agree to these Terms & Conditions.</p>
          </div>
        </div>
      </div>
    </div>
  );
}

export default TermsConditions;