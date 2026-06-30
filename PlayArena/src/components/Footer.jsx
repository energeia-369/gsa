import { useState } from "react";
import { Link } from "react-router-dom";
import axios from "axios";
import "../styles/Footer.css";

function Footer() {
  const currentYear = new Date().getFullYear();
  const [email, setEmail] = useState("");
  const [loading, setLoading] = useState(false);

  const handleSubscribe = async (e) => {
    e.preventDefault();
    if (!email.trim()) return;

    try {
      setLoading(true);
      const res = await axios.post("http://localhost:8080/api/newsletter/subscribe", { email: email });
      alert(res.data.message || "Subscribed successfully!");
      setEmail("");
    } catch (err) {
      console.error("Newsletter error", err);
      alert("Failed to subscribe. Please try again.");
    } finally {
      setLoading(false);
    }
  };

  return (
    <footer className="footer-premium">
      <div className="footer-top-grid">
        {/* Brand Column */}
        <div className="footer-brand-col">
          <h2>🏆 GLOBAL SPORTS ARENA</h2>
          <p>
            One Ecosystem. Infinite Possibilities. The leading championship platform for sports tournament bookings and authentic merchandise.
          </p>
          
          <form className="newsletter-form" onSubmit={handleSubscribe}>
            <input
              type="email"
              placeholder="Your email address"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              style={{
                background: "rgba(22, 24, 38, 0.9)",
                border: "1px solid rgba(197, 168, 92, 0.3)",
                padding: "10px 15px",
                borderRadius: "8px",
                color: "#fff",
                outline: "none",
                fontSize: "0.85rem",
                width: "70%",
                boxSizing: "border-box"
              }}
              required
            />
            <button
              type="submit"
              disabled={loading}
              style={{
                background: "linear-gradient(135deg, #c5a85c 0%, #8c7237 100%)",
                color: "#0b0c10",
                border: "none",
                padding: "10px 15px",
                borderRadius: "8px",
                fontWeight: "700",
                cursor: "pointer",
                fontSize: "0.85rem"
              }}
            >
              {loading ? "..." : "Subscribe"}
            </button>
          </form>

          <div className="footer-socials" style={{ marginTop: "1.5rem" }}>
            <a href="#">📘</a>
            <a href="#">📷</a>
            <a href="#">🐦</a>
            <a href="#">🎥</a>
            <a href="#">💼</a>
          </div>
        </div>

        {/* Company Links */}
        <div className="footer-links-col">
          <h4>Company</h4>
          <ul>
            <li><Link to="/about-event">About Us</Link></li>
            <li><Link to="/">Our Pillars</Link></li>
            <li><Link to="/faq">Careers</Link></li>
            <li><Link to="/contact-us">Press & Media</Link></li>
          </ul>
        </div>

        {/* Events Links */}
        <div className="footer-links-col">
          <h4>Events</h4>
          <ul>
            <li><Link to="/event-registration">Nexus Elite</Link></li>
            <li><Link to="/event-registration">Maytriya Meet</Link></li>
            <li><Link to="/event-registration">GSA League</Link></li>
            <li><Link to="/sports-categories">All Events</Link></li>
          </ul>
        </div>

        {/* Destinations Links */}
        <div className="footer-links-col">
          <h4>Destinations</h4>
          <ul>
            <li><Link to="/">Mumbai</Link></li>
            <li><Link to="/">Delhi</Link></li>
            <li><Link to="/">Bangalore</Link></li>
            <li><Link to="/">All Cities</Link></li>
          </ul>
        </div>

        {/* Membership Links */}
        <div className="footer-links-col">
          <h4>Membership</h4>
          <ul>
            <li><Link to="/">Membership Plans</Link></li>
            <li><Link to="/register">Member Benefits</Link></li>
            <li><Link to="/register">How to Join</Link></li>
          </ul>
        </div>

        {/* NXL Credits Links */}
        <div className="footer-links-col">
          <h4>NXL Credits</h4>
          <ul>
            <li><Link to="/credits">About NXL Credits</Link></li>
            <li><Link to="/credits">How It Works</Link></li>
            <li><Link to="/credits">Earn Credits</Link></li>
            <li><Link to="/credits">Redeem Credits</Link></li>
          </ul>
        </div>
      </div>

      <div className="footer-bottom-strip">
        <p>&copy; {currentYear} GLOBAL SPORTS ARENA. All Rights Reserved.</p>
        
        <div className="footer-bottom-info">
          <span>
            <strong className="footer-bottom-icon">✉️</strong> info@globalsportsarena.com
          </span>
          <span>
            <strong className="footer-bottom-icon">📞</strong> +91 12345 67890
          </span>
        </div>
      </div>
    </footer>
  );
}

export default Footer;