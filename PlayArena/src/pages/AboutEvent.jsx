import { useNavigate } from "react-router-dom";
import "../styles/AboutEvent.css";

function AboutEvent() {
  const navigate = useNavigate();
  
  const features = [
    {
      icon: "🏆",
      title: "Tournament Registration",
      description:
        "Register for indoor and outdoor sports tournaments with simple online booking.",
    },
    {
      icon: "🛒",
      title: "Sports Store",
      description:
        "Shop sports shoes, jerseys, rackets, footballs, gym accessories, and more.",
    },
    {
      icon: "💎",
      title: "NXL Credits",
      description:
        "Earn reward credits on purchases and redeem them during checkout.",
    },
    {
      icon: "📱",
      title: "QR Pass",
      description:
        "Generate QR-based event passes for smooth entry and verification.",
    },
  ];

  const steps = [
    {
      number: "01",
      title: "Register",
      icon: "📝",
      text: "Create your GLOBAL SPORTS ARENA account.",
    },
    {
      number: "02",
      title: "Choose",
      icon: "🏅",
      text: "Select tournament or product.",
    },
    {
      number: "03",
      title: "Pay",
      icon: "💳",
      text: "Complete secure online payment.",
    },
    {
      number: "04",
      title: "Enjoy",
      icon: "🎉",
      text: "Get QR pass or product delivery.",
    },
  ];

  return (
    <div className="about-page">
      <section className="about-hero">
        <div className="about-hero-content">
          <h1>Welcome to GLOBAL SPORTS ARENA</h1>
          <p>Sports Event & E-Commerce Platform</p>

          <div className="about-buttons">
            <button 
              className="about-primary-btn" 
              onClick={() => navigate("/sports-categories")}
            >
              Explore Now
            </button>
            <button 
              className="about-secondary-btn" 
              onClick={() => navigate("/products")}
            >
              Shop Now
            </button>
          </div>
        </div>
      </section>

      <section className="about-info">
        <h2>About GLOBAL SPORTS ARENA</h2>
        <p>
          GLOBAL SPORTS ARENA is a complete sports event and e-commerce platform where
          users can register for tournaments, purchase sports products, earn NXL
          credits, redeem cashback rewards, and generate QR-based event passes.
        </p>
      </section>

      <section className="about-features">
        <div className="section-title">
          <span>What We Offer</span>
          <h2>Everything for Sports Lovers</h2>
        </div>

        <div className="feature-grid">
          {features.map((item, index) => (
            <div className="about-feature-card" key={index}>
              <div className="about-feature-icon">{item.icon}</div>
              <h3>{item.title}</h3>
              <p>{item.description}</p>
            </div>
          ))}
        </div>
      </section>

      <section className="how-section">
        <div className="section-title">
          <span>Simple Process</span>
          <h2>How It Works</h2>
        </div>

        <div className="steps-grid">
          {steps.map((step, index) => (
            <div className="step-card" key={index}>
              <div className="step-number">{step.number}</div>
              <div className="step-icon">{step.icon}</div>
              <h3>{step.title}</h3>
              <p>{step.text}</p>
            </div>
          ))}
        </div>
      </section>

      <section className="nxl-section">
        <div>
          <span className="nxl-tag">NXL Rewards</span>
          <h2>Earn & Redeem NXL Credits</h2>
          <p>
            Earn NXL credits on every eligible purchase and tournament
            registration. Use credits to reduce checkout price and enjoy special
            cashback rewards.
          </p>

          <div className="nxl-boxes">
            <div onClick={() => navigate("/credits")}>
              <h3>₹1000</h3>
              <p>50 NXL Credits</p>
            </div>

            <div onClick={() => navigate("/credits")}>
              <h3>₹2000</h3>
              <p>100 NXL Credits</p>
            </div>

            <div onClick={() => navigate("/credits")}>
              <h3>100 NXL</h3>
              <p>5% Discount</p>
            </div>
          </div>

          <button 
            className="nxl-cta-btn" 
            onClick={() => navigate("/credits")}
          >
            Go to Credits & Rewards Store →
          </button>
        </div>
      </section>

      <section className="about-cta">
        <h2>Ready to Start Your Journey?</h2>
        <p>Join GLOBAL SPORTS ARENA and experience sports, shopping, and rewards.</p>

        <div className="about-buttons">
          <button 
            className="about-primary-btn" 
            onClick={() => navigate("/register")}
          >
            Register Now
          </button>
          <button 
            className="about-secondary-btn" 
            onClick={() => navigate("/sports-categories")}
          >
            Browse Events
          </button>
        </div>
      </section>
    </div>
  );
}

export default AboutEvent;