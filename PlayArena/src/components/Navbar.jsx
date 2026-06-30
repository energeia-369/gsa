import React from "react";
import { Link, useLocation, useNavigate } from "react-router-dom";
import { useCart } from "../context/CartContext";
import "../styles/Navbar.css";

function Navbar() {
  const { getCartCount } = useCart();
  const location = useLocation();
  const navigate = useNavigate();
  const [isMenuOpen, setIsMenuOpen] = React.useState(false);
  
  const token = localStorage.getItem("token");

  const handleNavClick = (sectionId) => {
    closeMenu();
    if (location.pathname === "/") {
      const element = document.getElementById(sectionId);
      if (element) {
        element.scrollIntoView({ behavior: "smooth" });
      }
    } else {
      navigate("/", { state: { scrollTo: sectionId } });
    }
  };

  const handleLogout = () => {
    localStorage.removeItem("token");
    localStorage.removeItem("userEmail");
    localStorage.removeItem("userRole");
    alert("Logged out successfully");
    closeMenu();
    navigate("/login");
  };

  const isActive = (path) => {
    return location.pathname === path ? "active" : "";
  };

  const toggleMenu = () => {
    setIsMenuOpen(!isMenuOpen);
  };

  const closeMenu = () => {
    setIsMenuOpen(false);
  };

  return (
    <nav className="navbar">
      <div className="nav-container">
        <div className="nav-brand">
          <Link to="/" onClick={() => { closeMenu(); handleNavClick("home"); }} style={{ display: "flex", alignItems: "center" }}>
            <img src="/logo.png" alt="GSA Logo" className="nav-logo" />
          </Link>
        </div>
        
        <div className={`nav-menu ${isMenuOpen ? "active" : ""}`}>
          <div className="nav-links">
            <button className="nav-link-btn" onClick={() => handleNavClick("home")}>HOME</button>
            <button className="nav-link-btn" onClick={() => handleNavClick("about-us")}>ABOUT US</button>
            
            {/* EVENTS DROPDOWN */}
            <div className="nav-dropdown">
              <span className="nav-dropdown-trigger">EVENTS <span className="dropdown-arrow">▼</span></span>
              <div className="nav-dropdown-menu">
                <button className="nav-dropdown-item" onClick={() => handleNavClick("flagship-events")}>Flagship Tournaments</button>
                <button className="nav-dropdown-item" onClick={() => handleNavClick("active-tournaments")}>Active Tournaments</button>
                <Link to="/event-registration" className="nav-dropdown-item" onClick={closeMenu}>Register Event</Link>
              </div>
            </div>

            <button className="nav-link-btn" onClick={() => handleNavClick("destinations")}>DESTINATIONS</button>
            <button className="nav-link-btn" onClick={() => handleNavClick("membership")}>MEMBERSHIP</button>
            
            {/* NXL CREDITS DROPDOWN */}
            <div className="nav-dropdown">
              <span className="nav-dropdown-trigger">NXL CREDITS <span className="dropdown-arrow">▼</span></span>
              <div className="nav-dropdown-menu">
                <Link to="/wallet" className="nav-dropdown-item" onClick={closeMenu}>My Wallet</Link>
                <Link to="/credits" className="nav-dropdown-item" onClick={closeMenu}>My Credits</Link>
              </div>
            </div>

            <button className="nav-link-btn" onClick={() => handleNavClick("partners")}>PARTNERS</button>
            <button className="nav-link-btn" onClick={() => handleNavClick("gallery")}>GALLERY</button>
            <button className="nav-link-btn" onClick={() => handleNavClick("blog")}>BLOG</button>
            <Link to="/contact-us" className={isActive("/contact-us")} onClick={closeMenu}>CONTACT US</Link>
          </div>
          
          <div className="nav-actions">
            <Link to={localStorage.getItem("userRole") === "ADMIN" ? "/admin-dashboard" : "/user-dashboard"} className="nav-icon" title="Dashboard" onClick={closeMenu}>
              👤
            </Link>
            <Link to="/cart" className="nav-icon cart-link" title="Cart" onClick={closeMenu}>
              🛒
              {getCartCount() > 0 && (
                <span className="cart-count">{getCartCount()}</span>
              )}
            </Link>
            {token ? (
              <button 
                onClick={handleLogout} 
                className="login-btn"
                style={{ 
                  border: "none", 
                  cursor: "pointer",
                  display: "inline-block",
                  textAlign: "center"
                }}
              >
                Logout
              </button>
            ) : (
              <Link to="/login" className="login-btn" onClick={closeMenu}>
                Login
              </Link>
            )}
          </div>
        </div>
        
        <div className="mobile-menu-btn" onClick={toggleMenu}>
          <span>{isMenuOpen ? "✕" : "☰"}</span>
        </div>
      </div>
    </nav>
  );
}

export default Navbar;