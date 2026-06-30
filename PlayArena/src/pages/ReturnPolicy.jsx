// ReturnPolicy.jsx

import "../styles/SupportPages.css";
import { useNavigate } from "react-router-dom";

function ReturnPolicy() {
  const navigate = useNavigate();

  return (
    <div className="support-page">
      {/* Hero Section */}
      <div className="policy-hero">
        <div className="policy-hero-overlay"></div>
        <div className="policy-hero-content">
          <div className="policy-badge">
            🔄 Hassle-Free Returns
          </div>
          <h1>
            Return <span className="highlight">Policy</span>
          </h1>
          <p>
            Your satisfaction is our priority. Read our simple and transparent return guidelines.
          </p>
        </div>
      </div>

      {/* Main Content */}
      <div className="policy-container">
        {/* Products Section */}
        <div className="policy-card product-card">
          <div className="card-icon">📦</div>
          <div className="card-badge">Products</div>
          <h2>Product Returns</h2>
          <div className="policy-details">
            <div className="policy-point">
              <span className="point-icon">✅</span>
              <div>
                <strong>7 Days Return Window</strong>
                <p>Products can be returned within 7 days of delivery</p>
              </div>
            </div>
            <div className="policy-point">
              <span className="point-icon">🎯</span>
              <div>
                <strong>Condition Requirements</strong>
                <p>Items must be unused, undamaged, and in original packaging</p>
              </div>
            </div>
            <div className="policy-point">
              <span className="point-icon">🚚</span>
              <div>
                <strong>Free Pickup</strong>
                <p>Free return pickup available for eligible products</p>
              </div>
            </div>
            <div className="policy-point">
              <span className="point-icon">💵</span>
              <div>
                <strong>Full Refund</strong>
                <p>Refund processed within 5-7 business days after verification</p>
              </div>
            </div>
          </div>
        </div>

        {/* Events Section */}
        <div className="policy-card events-card">
          <div className="card-icon">🎫</div>
          <div className="card-badge danger">Events</div>
          <h2>Event Tickets</h2>
          <div className="policy-details">
            <div className="policy-point">
              <span className="point-icon">⚠️</span>
              <div>
                <strong>Non-Refundable</strong>
                <p>Event tickets cannot be returned after successful booking</p>
              </div>
            </div>
            <div className="policy-point">
              <span className="point-icon">🔄</span>
              <div>
                <strong>Event Cancellation</strong>
                <p>If an event is canceled, full refund will be processed automatically</p>
              </div>
            </div>
            <div className="policy-point">
              <span className="point-icon">📅</span>
              <div>
                <strong>Rescheduling</strong>
                <p>Ticket rescheduling allowed up to 48 hours before the event</p>
              </div>
            </div>
            <div className="policy-point">
              <span className="point-icon">💺</span>
              <div>
                <strong>Seat Upgrades</strong>
                <p>Upgrade your seats anytime with price difference payment</p>
              </div>
            </div>
          </div>
        </div>

        {/* Process Steps */}
        <div className="process-section">
          <h2>
            Return <span className="highlight">Process</span>
          </h2>
          <div className="process-steps">
            <div className="step">
              <div className="step-number">1</div>
              <div className="step-icon">📞</div>
              <h4>Request Return</h4>
              <p>Contact support or initiate return from your orders page</p>
            </div>
            <div className="step-arrow">→</div>
            <div className="step">
              <div className="step-number">2</div>
              <div className="step-icon">✅</div>
              <h4>Get Approval</h4>
              <p>Receive confirmation and return instructions</p>
            </div>
            <div className="step-arrow">→</div>
            <div className="step">
              <div className="step-number">3</div>
              <div className="step-icon">📦</div>
              <h4>Ship Product</h4>
              <p>Pack item and ship using provided label</p>
            </div>
            <div className="step-arrow">→</div>
            <div className="step">
              <div className="step-number">4</div>
              <div className="step-icon">💰</div>
              <h4>Get Refund</h4>
              <p>Refund issued after quality check</p>
            </div>
          </div>
        </div>

        {/* FAQ Accordion */}
        <div className="faq-section">
          <h2>
            Frequently Asked <span className="highlight">Questions</span>
          </h2>
          <div className="faq-grid">
            <div className="faq-item">
              <div className="faq-question">
                <span>❓</span>
                <h4>How long does refund take?</h4>
              </div>
              <p>Refunds are processed within 5-7 business days after we receive and verify the returned product.</p>
            </div>
            <div className="faq-item">
              <div className="faq-question">
                <span>❓</span>
                <h4>Who pays for return shipping?</h4>
              </div>
              <p>Return shipping is free for defective products. For other returns, shipping charges may apply.</p>
            </div>
            <div className="faq-item">
              <div className="faq-question">
                <span>❓</span>
                <h4>Can I exchange a product?</h4>
              </div>
              <p>Yes, exchanges are available for size/color variations within 7 days of delivery.</p>
            </div>
            <div className="faq-item">
              <div className="faq-question">
                <span>❓</span>
                <h4>What if I received a damaged product?</h4>
              </div>
              <p>Contact us immediately with photos. We'll arrange free pickup and replacement.</p>
            </div>
          </div>
        </div>

        {/* Contact CTA */}
        <div className="contact-cta">
          <div className="contact-cta-icon">💬</div>
          <h3>Still have questions?</h3>
          <p>Our support team is here to help you 24/7</p>
          <div className="contact-buttons">
            <button className="contact-btn" onClick={() => navigate("/contact-us")}>
              Contact Support →
            </button>
            <button className="chat-btn" onClick={() => navigate("/faq")}>
              View FAQs
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}

export default ReturnPolicy;