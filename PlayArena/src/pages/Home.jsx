import "../styles/Home.css";
import { useNavigate, useLocation, Link } from "react-router-dom";
import { useEffect, useState, useRef } from "react";
import axios from "axios";

const getFutureYearForMonth = (monthStr, dayNum) => {
  const months = ["jan", "feb", "mar", "apr", "may", "jun", "jul", "aug", "sep", "oct", "nov", "dec"];
  const current = new Date();
  const currentMonth = current.getMonth(); // 0-11
  const currentDay = current.getDate(); // 1-31
  const currentYear = current.getFullYear();
  
  const cleanMonth = monthStr.toLowerCase().trim().substring(0, 3);
  const targetMonthIndex = months.indexOf(cleanMonth);
  
  if (targetMonthIndex !== -1) {
    if (targetMonthIndex < currentMonth) {
      return currentYear + 1;
    }
    if (targetMonthIndex === currentMonth && dayNum !== undefined && dayNum < currentDay) {
      return currentYear + 1;
    }
  }
  return currentYear;
};

const getRangeDate = (startMonth, endMonth) => {
  const startYear = getFutureYearForMonth(startMonth);
  let endYear = getFutureYearForMonth(endMonth);
  if (endYear < startYear) {
    endYear = startYear;
  }
  return `${startMonth} - ${endMonth} ${endYear}`;
};

const getHomeEventDate = (month, day, time) => {
  const year = getFutureYearForMonth(month, day);
  return `${month} ${day}, ${year} | ${time}`;
};

function Home() {
  const navigate = useNavigate();
  const location = useLocation();
  const [dbTournaments, setDbTournaments] = useState([]);
  const [loadingTournaments, setLoadingTournaments] = useState(false);

  // Scroll to section dynamically upon cross-page routing
  useEffect(() => {
    if (location.state?.scrollTo) {
      const sectionId = location.state.scrollTo;
      setTimeout(() => {
        const element = document.getElementById(sectionId);
        if (element) {
          element.scrollIntoView({ behavior: "smooth" });
        }
      }, 300);
      
      // Clear navigation state to prevent scrolling again
      window.history.replaceState({}, document.title);
    }
  }, [location]);

  // Fetch real-time active tournaments from Spring Boot MySQL backend
  useEffect(() => {
    const fetchTournaments = async () => {
      try {
        setLoadingTournaments(true);
        const res = await axios.get("http://localhost:8080/api/tournaments");
        setDbTournaments(res.data || []);
      } catch (err) {
        console.warn("Could not load backend tournaments, using static fallback", err);
      } finally {
        setLoadingTournaments(false);
      }
    };
    fetchTournaments();
  }, []);

  // Standard fallback events if DB is empty
  const defaultEvents = [
    {
      id: 1,
      name: "Champions League Finals",
      sport: "Soccer",
      date: getHomeEventDate("May", 15, "7:00 PM"),
      venue: "National Stadium, Mumbai",
      registrationFee: 999,
      badge: "Limited Seats"
    },
    {
      id: 2,
      name: "Basketball Pro League",
      sport: "Basketball",
      date: getHomeEventDate("June", 5, "6:30 PM"),
      venue: "Indoor Arena, Delhi",
      registrationFee: 799,
      badge: "Early Bird"
    },
    {
      id: 3,
      name: "Tennis Grand Slam",
      sport: "Tennis",
      date: getHomeEventDate("July", 20, "4:00 PM"),
      venue: "Tennis Complex, Bangalore",
      registrationFee: 1499,
      badge: "Trending"
    }
  ];

  const displayedTournaments = dbTournaments.length > 0 ? dbTournaments : defaultEvents;

  const defaultDestinations = [
    {
      id: 1,
      country: "INDIA",
      image: "https://images.unsplash.com/photo-1564507592333-c60657eea523?w=500&auto=format&fit=crop&q=60",
      date: getRangeDate("Oct", "Feb"),
      city: "Pune / Mumbai",
      region: "India",
      link: "#" // Edit this string to change the click link for India
    },
    {
      id: 2,
      country: "SINGAPORE",
      image: "https://images.unsplash.com/photo-1525625293386-3f8f99389edd?w=500&auto=format&fit=crop&q=60",
      date: getRangeDate("Feb", "Apr"),
      city: "Singapore",
      region: "Singapore",
      link: "#" // Edit this string to change the click link for Singapore
    },
    {
      id: 3,
      country: "SWITZERLAND",
      image: "https://images.unsplash.com/photo-1506744038136-46273834b3fb?w=500&auto=format&fit=crop&q=60",
      date: getRangeDate("May", "Sep"),
      city: "Zurich",
      region: "Switzerland",
      link: "#" // Edit this string to change the click link for Switzerland
    },
    {
      id: 4,
      country: "UAE",
      image: "https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=500&auto=format&fit=crop&q=60",
      date: getRangeDate("Nov", "Mar"),
      city: "Dubai / Abu Dhabi",
      region: "UAE",
      link: "#" // Edit this string to change the click link for UAE
    },
    {
      id: 5,
      country: "THAILAND",
      image: "https://images.unsplash.com/photo-1508009603885-50cf7c579365?w=500&auto=format&fit=crop&q=60",
      date: getRangeDate("Sep", "Nov"),
      city: "Phuket / Bangkok",
      region: "Thailand",
      link: "#" // Edit this string to change the click link for Thailand
    },
    {
      id: 6,
      country: "USA - LAS VEGAS",
      image: "data:image/jpeg;base64,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",
      date: getRangeDate("Apr", "Jul"),
      city: "Las Vegas",
      region: "USA",
      link: "#" // Edit this string to change the click link for Las Vegas
    },
    {
      id: 7,
      country: "USA - NEW YORK",
      image: "https://images.unsplash.com/photo-1526304640581-d334cdbbf45e?w=500&auto=format&fit=crop&q=60",
      date: getRangeDate("May", "Sep"),
      city: "New York",
      region: "USA",
      link: "#" // Edit this string to change the click link for New York
    }
  ];

  // Admin-added custom destinations (persisted to localStorage by admin panel)
  const [customDestinations, setCustomDestinations] = useState(() => {
    try { return JSON.parse(localStorage.getItem("globalsportsarena_custom_destinations") || "[]"); }
    catch { return []; }
  });

  // Re-sync when admin updates destinations in another tab or same session
  useEffect(() => {
    const sync = () => {
      try { setCustomDestinations(JSON.parse(localStorage.getItem("globalsportsarena_custom_destinations") || "[]")); }
      catch { setCustomDestinations([]); }
    };
    window.addEventListener("storage", sync);
    return () => window.removeEventListener("storage", sync);
  }, []);

  // Merge default + admin-added destinations
  const globalDestinations = [...defaultDestinations, ...customDestinations];

  const flagshipEvents = [
    {
      id: 1,
      badgeText: "N",
      badgeTitle: "NEXUS ELITE",
      badgeSubtitle: "BUSINESS SUMMIT",
      image: "https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=500&auto=format&fit=crop&q=60",
      description: "A premium business summit connecting leaders, startups, investors & innovators.",
      footerIcon: "🏢",
      footerTitle: "Nexus Elite",
      footerSubtitle: "Business Summit",
      headerBg: "#08102b",
      showProfiles: true,
      link: "#" // Edit this string to change the click link for Nexus Elite
    },
    {
      id: 2,
      badgeText: "M",
      badgeTitle: "MAYTRIYA MEET",
      badgeSubtitle: "LEADERSHIP & FRANCHISE SUMMIT",
      image: "https://images.unsplash.com/photo-1511578314322-379afb476865?w=500&auto=format&fit=crop&q=60",
      description: "Curated meet for investors, franchise brands, entrepreneurs & business leaders.",
      footerIcon: "🤝",
      footerTitle: "Maytriya Meet",
      footerSubtitle: "Leadership & Franchise Summit",
      headerBg: "#2a1704",
      showProfiles: false,
      link: "#" // Edit this string to change the click link for Maytriya Meet
    },
    {
      id: 3,
      badgeText: "GSA",
      badgeTitle: "GSA",
      badgeSubtitle: "GLOBAL SPORTS ARENA",
      image: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcST2AgJb1eJLI37Rst9gLIL-X7d1myzrrxClA&s",
      description: "Where Sports, Tourism & Entertainment come together on a global stage.",
      footerIcon: "🏃",
      footerTitle: "GSA",
      footerSubtitle: "Global Sports Arena",
      headerBg: "#1d042f",
      showProfiles: false,
      link: "/gsa-details" // Edit this string to change the click link for GSA
    }
  ];

  const fourPillars = [
    {
      id: 1,
      title: "ENERGEIA",
      tagline: "Energy • Sustainability",
      subTagline: "EV • Climate Tech",
      description: "Building a sustainable future through clean energy, EV innovation and climate action.",
      image: "https://images.unsplash.com/photo-1466611653911-95081537e5b7?w=500&auto=format&fit=crop&q=60",
      gradient: "linear-gradient(135deg, rgba(16, 80, 50, 0.85) 0%, rgba(5, 40, 20, 0.95) 100%)",
      icon: "⚡",
      link: "#" // Edit this string to change the click link for Energeia
    },
    {
      id: 2,
      title: "EKONAMIA",
      tagline: "Economy • Fintech",
      subTagline: "Investment • Trade",
      description: "Empowering global economy through finance, investment, trade and business growth.",
      image: "https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=500&auto=format&fit=crop&q=60",
      gradient: "linear-gradient(135deg, rgba(10, 40, 80, 0.85) 0%, rgba(5, 20, 45, 0.95) 100%)",
      icon: "📈",
      link: "#" // Edit this string to change the click link for Ekonamia
    },
    {
      id: 3,
      title: "EXPLORIA",
      tagline: "Tourism • Destinations",
      subTagline: "Fintech • Tech Showcase",
      description: "Exploring the world through tourism, destinations and technology showcases.",
      image: "https://images.unsplash.com/photo-1436491865332-7a61a109cc05?w=500&auto=format&fit=crop&q=60",
      gradient: "linear-gradient(135deg, rgba(100, 50, 10, 0.85) 0%, rgba(50, 25, 5, 0.95) 100%)",
      icon: "✈️",
      link: "#" // Edit this string to change the click link for Exploria
    },
    {
      id: 4,
      title: "EVEXIA",
      tagline: "Wellness • Hospitality",
      subTagline: "Lifestyle • Experiences",
      description: "Enhancing life through wellness, hospitality, lifestyle and memorable experiences.",
      image: "https://images.unsplash.com/photo-1545205597-3d9d02c29597?w=500&auto=format&fit=crop&q=60",
      gradient: "linear-gradient(135deg, rgba(80, 10, 80, 0.85) 0%, rgba(40, 5, 40, 0.95) 100%)",
      icon: "🌸",
      link: "#" // Edit this string to change the click link for Evexia
    }
  ];

  const partnersList = [
    { id: 1, name: "TATA GROUP", icon: "👔", link: "#" },
    { id: 2, name: "INFOSYS", icon: "💻", link: "#" },
    { id: 3, name: "HDFC BANK", icon: "🏦", link: "#" },
    { id: 4, name: "GOOGLE", icon: "🔍", link: "#" },
    { id: 5, name: "BOOKMYSHOW", icon: "🎟️", link: "#" },
    { id: 6, name: "DECATHLON", icon: "👟", link: "#" }
  ];

  const sliderRef = useRef(null);
  const partnersSliderRef = useRef(null);
  const isPausedRef = useRef(false);
  const isPartnersPausedRef = useRef(false);

  // Auto scroll effect for destinations
  useEffect(() => {
    const slider = sliderRef.current;
    if (!slider) return;

    const intervalId = setInterval(() => {
      if (!isPausedRef.current) {
        const halfWidth = slider.scrollWidth / 2;
        if (halfWidth > 0) {
          if (slider.scrollLeft >= halfWidth - 1) {
            slider.scrollLeft -= halfWidth;
          } else {
            slider.scrollLeft += 1;
          }
        }
      }
    }, 20);

    return () => clearInterval(intervalId);
  }, []);

  // Auto scroll effect for partners
  useEffect(() => {
    const slider = partnersSliderRef.current;
    if (!slider) return;

    const intervalId = setInterval(() => {
      if (!isPartnersPausedRef.current) {
        const halfWidth = slider.scrollWidth / 2;
        if (halfWidth > 0) {
          if (slider.scrollLeft >= halfWidth - 1) {
            slider.scrollLeft -= halfWidth;
          } else {
            slider.scrollLeft += 1;
          }
        }
      }
    }, 20);

    return () => clearInterval(intervalId);
  }, []);

  // Horizontal scroll handler for destinations
  const scrollDestinations = (direction) => {
    const slider = sliderRef.current;
    if (slider) {
      slider.style.scrollBehavior = "smooth";
      const scrollAmount = 300;
      slider.scrollLeft += direction === "left" ? -scrollAmount : scrollAmount;
      setTimeout(() => {
        if (slider) slider.style.scrollBehavior = "auto";
      }, 500);
    }
  };

  // Horizontal scroll handler for partners
  const scrollPartners = (direction) => {
    const slider = partnersSliderRef.current;
    if (slider) {
      slider.style.scrollBehavior = "smooth";
      const scrollAmount = 250;
      slider.scrollLeft += direction === "left" ? -scrollAmount : scrollAmount;
      setTimeout(() => {
        if (slider) slider.style.scrollBehavior = "auto";
      }, 500);
    }
  };

  // Safe membership navigation: Checkout if logged in, Register if logged out
  const handleJoinMembership = (tierName, price) => {
    const isLoggedIn = !!localStorage.getItem("token");
    if (!isLoggedIn) {
      navigate("/register");
    } else {
      const membershipOrder = {
        price: price,
        discountAmount: 0,
        nxlCoinsUsed: 0,
        deliveryFee: 0,
        total: price,
        nxlCoinsEarned: Math.round(price * 0.10), // 10% cashback in NXL Coins
        items: [{
          id: "membership-" + tierName.toLowerCase().replace(" ", "-"),
          name: tierName + " Subscription (1 Year)",
          price: price,
          quantity: 1
        }]
      };
      navigate("/checkout", { state: { order: membershipOrder } });
    }
  };

  return (
    <div className="home-page" id="home">
      {/* 1. PREMIUM HERO ECOSYSTEM SECTION */}
      <section className="premium-hero">
        <div className="hero-glow-blob"></div>
        <p className="hero-subtitle-top">⚡ ONE ECOSYSTEM. INFINITE POSSIBILITIES.</p>
        <h1>
          Sports Event & <span className="gold-highlight">E-Commerce Platform</span>
        </h1>
        <p className="hero-desc">
          Book premium sports tournaments, purchase official high-quality athletic merchandise, earn NXL reward credits, and redeem exclusive benefits in one seamless arena.
        </p>

        <div className="hero-action-buttons">
          <button className="btn-premium-gold" onClick={() => navigate("/sports-categories")}>
            🏆 Book Tournaments
          </button>
          <button className="btn-premium-outline" onClick={() => navigate("/products")}>
            🛒 Sports Store
          </button>
        </div>

        {/* Golden Metrics / Counters Bar */}
        <div className="hero-metrics-bar">
          <div className="metric-item">
            <span className="metric-val">4</span>
            <span className="metric-lbl">Pillars</span>
          </div>
          <div className="metric-item">
            <span className="metric-val">10+</span>
            <span className="metric-lbl">Cities</span>
          </div>
          <div className="metric-item">
            <span className="metric-val">25+</span>
            <span className="metric-lbl">Tournaments</span>
          </div>
          <div className="metric-item">
            <span className="metric-val">∞</span>
            <span className="metric-lbl">Possibilities</span>
          </div>
        </div>
      </section>

      {/* 2. OUR FLAGSHIP EVENTS */}
      <section className="flagship-events-section" id="flagship-events">
        <div className="section-premium-title">
          <span className="title-tagline">Elite Experience</span>
          <h2>Our Flagship Tournaments</h2>
          <div className="title-separator"></div>
        </div>

        <div className="premium-events-grid">
          {flagshipEvents.map((event) => {
            const isInternal = event.link && event.link.startsWith("/");
            const cardContent = (
              <>
                {/* Header Box */}
                <div className="flagship-card-header" style={{ backgroundColor: event.headerBg }}>
                  <div className="flagship-header-badge">
                    <span>{event.badgeText}</span>
                  </div>
                  <div className="flagship-header-text">
                    <h4>{event.badgeTitle}</h4>
                    <p>{event.badgeSubtitle}</p>
                  </div>
                </div>

                {/* Central Image Box */}
                <div className="flagship-card-image-box">
                  <img src={event.image} alt={event.badgeTitle} className="flagship-card-img" />
                  {event.showProfiles && (
                    <div className="flagship-profiles-row">
                      <span className="flagship-profile-dot">👤</span>
                      <span className="flagship-profile-dot">👤</span>
                      <span className="flagship-profile-dot">👤</span>
                      <span className="flagship-profile-dot">👤</span>
                      <span className="flagship-profile-dot">👤</span>
                    </div>
                  )}
                </div>

                {/* Body Text Box */}
                <div className="flagship-card-body-box">
                  <p className="flagship-card-desc">{event.description}</p>
                  <div className="flagship-know-more-btn">KNOW MORE</div>
                </div>

                {/* Footer Box */}
                <div className="flagship-card-footer">
                  <div className="flagship-footer-icon-box">
                    <span className="flagship-footer-icon">{event.footerIcon}</span>
                  </div>
                  <div className="flagship-footer-text">
                    <h5>{event.footerTitle}</h5>
                    <p>{event.footerSubtitle}</p>
                  </div>
                </div>
              </>
            );

            if (isInternal) {
              return (
                <Link 
                  to={event.link} 
                  className="flagship-card" 
                  key={event.id}
                >
                  {cardContent}
                </Link>
              );
            }

            return (
              <a 
                href={event.link} 
                className="flagship-card" 
                key={event.id}
                target="_blank"
                rel="noopener noreferrer"
              >
                {cardContent}
              </a>
            );
          })}
        </div>
      </section>

      {/* 3. GLOBAL EVENT DESTINATIONS (CAROUSEL) */}
      <section className="destinations-section" id="destinations">
        <div className="section-premium-title">
          <span className="title-tagline">Global Arenas</span>
          <h2>Global Event Destinations</h2>
          <div className="title-separator"></div>
        </div>

        <div className="destinations-slider-container">
          <button className="slider-control-btn prev" onClick={() => scrollDestinations("left")}>
            <span className="chevron-icon">◀</span>
          </button>
          
          <div 
            className="destinations-slider" 
            id="destinations-slider" 
            ref={sliderRef}
            onMouseEnter={() => { isPausedRef.current = true; }}
            onMouseLeave={() => { isPausedRef.current = false; }}
          >
            {[...globalDestinations, ...globalDestinations].map((dest, idx) => {
              const isCustomLink = dest.link && dest.link !== "#";
              const targetLink = isCustomLink 
                ? dest.link 
                : (dest.country === "THAILAND" ? "#" : `/destination/${dest.id}`);
              
              const isExternal = targetLink.startsWith("http") || targetLink === "#";

              if (isExternal) {
                return (
                  <a 
                    href={targetLink}
                    onClick={targetLink === "#" ? (e) => e.preventDefault() : undefined}
                    className="destination-card" 
                    key={`dest-${idx}`}
                  >
                    <div className="destination-image-box">
                      <img src={dest.image} alt={dest.country} className="destination-image" />
                      <div className="destination-flag-overlay">{dest.country}</div>
                    </div>
                    <div className="destination-body">
                      <div className="destination-detail-row">
                        <span className="destination-icon">📅</span> {dest.date}
                      </div>
                      <div className="destination-detail-row">
                        <span className="destination-icon">📍</span> {dest.city}
                      </div>
                      <div className="destination-detail-row">
                        <span className="destination-icon">📍</span> {dest.region}
                      </div>
                    </div>
                  </a>
                );
              }

              return (
                <Link 
                  to={targetLink} 
                  className="destination-card" 
                  key={`dest-${idx}`}
                >
                  <div className="destination-image-box">
                    <img src={dest.image} alt={dest.country} className="destination-image" />
                    <div className="destination-flag-overlay">{dest.country}</div>
                  </div>
                  <div className="destination-body">
                    <div className="destination-detail-row">
                      <span className="destination-icon">📅</span> {dest.date}
                    </div>
                    <div className="destination-detail-row">
                      <span className="destination-icon">📍</span> {dest.city}
                    </div>
                    <div className="destination-detail-row">
                      <span className="destination-icon">📍</span> {dest.region}
                    </div>
                  </div>
                </Link>
              );
            })}
          </div>

          <button className="slider-control-btn next" onClick={() => scrollDestinations("right")}>
            <span className="chevron-icon">▶</span>
          </button>
        </div>
      </section>

      {/* 4. NXL CREDITS & WALLET WORKFLOW */}
      <section className="nxl-wallet-section">
        <div className="nxl-banner-card">
          <div className="nxl-banner-glow"></div>
          
          <div className="nxl-banner-content">
            <h2>NXL Credits & Digital Wallet</h2>
            <p>
              Earn NXL reward credits automatically on every single tournament registration, sports equipment order, or community referral. Redeem coins instantly at checkout to pay for bookings, or unlock premium loyalty tiers!
            </p>

            {/* Visual Workflow Steps */}
            <div className="nxl-steps-flow">
              <div className="nxl-flow-connector"></div>
              
              <div className="nxl-flow-step">
                <div className="nxl-flow-icon-circle">👛</div>
                <span>Create Wallet</span>
              </div>
              
              <div className="nxl-flow-step">
                <div className="nxl-flow-icon-circle">💎</div>
                <span>Earn Credits</span>
              </div>
              
              <div className="nxl-flow-step">
                <div className="nxl-flow-icon-circle">🛒</div>
                <span>Use Credits</span>
              </div>
              
              <div className="nxl-flow-step">
                <div className="nxl-flow-icon-circle">🎁</div>
                <span>Redeem Rewards</span>
              </div>
            </div>

            <button className="btn-premium-gold" onClick={() => navigate("/wallet")}>
              💎 Open NXL Wallet
            </button>
          </div>

          <div className="nxl-banner-visual">
            <span className="nxl-coin-large">💎</span>
            <span className="nxl-coin-label">100 NXL = 5% Off</span>
            <span className="nxl-coin-sub">Redeem Loyalty Discount</span>
          </div>
        </div>
      </section>

      {/* 5. MEMBERSHIP PACKAGES */}
      <section className="membership-section" id="membership">
        <div className="section-premium-title">
          <span className="title-tagline">Exclusive Perks</span>
          <h2>Membership Plans</h2>
          <div className="title-separator"></div>
        </div>

        <div className="membership-grid">
          {/* Plan 1 */}
          <div className="membership-card">
            <span className="tier-icon">🥉</span>
            <h3>Standard Member</h3>
            <div className="tier-price">₹2,499 <span>/ Year</span></div>
            <ul className="tier-benefits">
              <li><span className="benefit-bullet">✓</span> Access to local tournaments</li>
              <li><span className="benefit-bullet">✓</span> 5% standard NXL coin cashback</li>
              <li><span className="benefit-bullet">✓</span> Basic newsletter updates</li>
              <li><span className="benefit-bullet">✓</span> Standard Customer Support</li>
            </ul>
            <button className="btn-membership-join" onClick={() => handleJoinMembership("Standard Member", 2499)}>
              Join Standard
            </button>
          </div>

          {/* Plan 2 */}
          <div className="membership-card premium-tier">
            <span className="tier-icon">🥈</span>
            <h3>Premium Member</h3>
            <div className="tier-price">₹3,499 <span>/ Year</span></div>
            <ul className="tier-benefits">
              <li><span className="benefit-bullet">✓</span> Pre-sale booking for major finals</li>
              <li><span className="benefit-bullet">✓</span> 10% upgraded NXL coin cashback</li>
              <li><span className="benefit-bullet">✓</span> Exclusive weekly sports digest</li>
              <li><span className="benefit-bullet">✓</span> Priority Customer Support</li>
              <li><span className="benefit-bullet">✓</span> Free delivery on sports merch</li>
            </ul>
            <button className="btn-membership-join" onClick={() => handleJoinMembership("Premium Member", 3499)}>
              Join Premium
            </button>
          </div>

          {/* Plan 3 */}
          <div className="membership-card">
            <span className="tier-icon">👑</span>
            <h3>Elite Member</h3>
            <div className="tier-price">₹10,999 <span>/ Year</span></div>
            <ul className="tier-benefits">
              <li><span className="benefit-bullet">✓</span> VIP seating lounge event passes</li>
              <li><span className="benefit-bullet">✓</span> 15% elite NXL coin cashback</li>
              <li><span className="benefit-bullet">✓</span> 1-on-1 coaching consultations</li>
              <li><span className="benefit-bullet">✓</span> Dedicated account manager support</li>
              <li><span className="benefit-bullet">✓</span> Invitation to annual offline gala</li>
            </ul>
            <button className="btn-membership-join" onClick={() => handleJoinMembership("Elite Member", 10999)}>
              Join Elite
            </button>
          </div>
        </div>
      </section>

      <section className="spotlight-banner-section">
        <div className="spotlight-box-clickable">
          <div className="spotlight-box-bg"></div>
          <div className="spotlight-box-content-wrapper">
            <div className="spotlight-box-left">
              <h3>GSA THAILAND 2026</h3>
              <h4>— GLOBAL SPORTS ARENA —</h4>
              <h2>Thailand Edition</h2>
              
              
            </div>
            
            <div className="spotlight-box-right">
              <div className="spotlight-box-badge-card">
                <h3>SEP - NOV 2026</h3>
                <p>PHUKET, THAILAND</p>
                <a 
                  href="#" 
                  className="spotlight-badge-btn"
                  target="_blank"
                  rel="noopener noreferrer"
                >
                  REGISTER NOW
                </a>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* 7. OUR FOUR PILLARS */}
      <section className="pillars-section" id="about-us">
        <div className="section-premium-title">
          <span className="title-tagline">Core Values</span>
          <h2>Our Four Pillars</h2>
          <div className="title-separator"></div>
        </div>

        <div className="pillars-grid">
          {fourPillars.map((pillar) => (
            <a 
              href={pillar.link} 
              className="pillar-card" 
              key={pillar.id}
              target="_blank"
              rel="noopener noreferrer"
            >
              {/* Top layer (Colored Gradient with Background Image & title details) */}
              <div className="pillar-card-top" style={{ backgroundImage: `url(${pillar.image})` }}>
                <div className="pillar-card-overlay" style={{ background: pillar.gradient }}></div>
                <div className="pillar-card-top-content">
                  <div className="pillar-icon-box">{pillar.icon}</div>
                  <h3>{pillar.title}</h3>
                  <h4>{pillar.tagline}</h4>
                  <p>{pillar.subTagline}</p>
                </div>
              </div>

              {/* Bottom layer (Description in Dark Mode Theme) */}
              <div className="pillar-card-bottom">
                <p>{pillar.description}</p>
              </div>
            </a>
          ))}
        </div>
      </section>

      {/* 8. OUR PARTNERS */}
      <section className="partners-section" id="partners">
        <div className="section-premium-title">
          <span className="title-tagline">Trusted Networks</span>
          <h2>Our Partners</h2>
          <div className="title-separator"></div>
        </div>

        <div className="partners-slider-container">
          <button className="slider-control-btn prev" onClick={() => scrollPartners("left")}>
            <span className="chevron-icon">◀</span>
          </button>
          
          <div 
            className="partners-slider" 
            id="partners-slider" 
            ref={partnersSliderRef}
            onMouseEnter={() => { isPartnersPausedRef.current = true; }}
            onMouseLeave={() => { isPartnersPausedRef.current = false; }}
          >
            {[...partnersList, ...partnersList].map((partner, idx) => (
              <a 
                href={partner.link} 
                className="partner-card" 
                key={`partner-${partner.id}-${idx}`}
                target="_blank"
                rel="noopener noreferrer"
              >
                <span className="partner-card-icon">{partner.icon}</span>
                <span className="partner-card-name">{partner.name}</span>
              </a>
            ))}
          </div>

          <button className="slider-control-btn next" onClick={() => scrollPartners("right")}>
            <span className="chevron-icon">▶</span>
          </button>
        </div>
      </section>

      {/* 10. GALLERY SECTION */}
      <section className="gallery-section" id="gallery" style={{ padding: "4rem 5%", background: "#0b0c10", borderTop: "1px solid rgba(197, 168, 92, 0.15)" }}>
        <div className="section-premium-title">
          <span className="title-tagline">Visual Moments</span>
          <h2>📸 GLOBAL SPORTS ARENA Gallery</h2>
          <div className="title-separator"></div>
        </div>
        <div style={{ display: "grid", gridTemplateColumns: "repeat(auto-fit, minmax(280px, 1fr))", gap: "20px", marginTop: "30px" }}>
          <div className="gallery-card">
            <img
              src="https://images.unsplash.com/photo-1579952363873-27f3bade9f55?w=600&auto=format&fit=crop&q=80"
              alt="Champions Soccer League"
              className="gallery-card-img"
            />
            <div className="gallery-overlay">
              <h4 style={{ margin: 0, color: "var(--accent-gold-light)" }}>Champions Soccer League</h4>
              <p style={{ margin: "5px 0 0", fontSize: "0.85rem", color: "#ccc" }}>Grand Arena Season Finals 2026</p>
            </div>
          </div>
          <div className="gallery-card">
            <img
              src="https://images.unsplash.com/photo-1546519638-68e109498ffc?w=600&auto=format&fit=crop&q=80"
              alt="Elite Basketball Cup"
              className="gallery-card-img"
            />
            <div className="gallery-overlay">
              <h4 style={{ margin: 0, color: "var(--accent-gold-light)" }}>Elite Basketball Cup</h4>
              <p style={{ margin: "5px 0 0", fontSize: "0.85rem", color: "#ccc" }}>Corporate Maytriya Meet Season 4</p>
            </div>
          </div>
          <div className="gallery-card">
            <img
              src="https://images.unsplash.com/photo-1554068865-24cecd4e34b8?w=600&auto=format&fit=crop&q=80"
              alt="Tennis Grand Slam Open"
              className="gallery-card-img"
            />
            <div className="gallery-overlay">
              <h4 style={{ margin: 0, color: "var(--accent-gold-light)" }}>Tennis Grand Slam Open</h4>
              <p style={{ margin: "5px 0 0", fontSize: "0.85rem", color: "#ccc" }}>Bangalore Tennis Arena matches</p>
            </div>
          </div>
        </div>
      </section>

      {/* 11. BLOG SECTION */}
      <section className="blog-section" id="blog" style={{ padding: "4rem 5%", background: "#0b0c10", borderTop: "1px solid rgba(197, 168, 92, 0.15)" }}>
        <div className="section-premium-title">
          <span className="title-tagline">Insights & News</span>
          <h2>📰 GLOBAL SPORTS ARENA Sports Blog</h2>
          <div className="title-separator"></div>
        </div>
        <div style={{ display: "grid", gridTemplateColumns: "repeat(auto-fit, minmax(320px, 1fr))", gap: "25px", marginTop: "30px" }}>
          <div className="blog-post-card">
            <div className="blog-card-img-wrap">
              <img
                src="https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=600&auto=format&fit=crop&q=80"
                alt="Stamina Training"
                className="blog-card-img"
              />
            </div>
            <span style={{ color: "var(--accent-gold)", fontSize: "0.8rem", fontWeight: "bold" }}>⚡ TRAINING TIPS • MAY 28, 2026</span>
            <h3 style={{ margin: "10px 0", color: "#f5f6fa" }}>5 Core Techniques to Maximize Stamina</h3>
            <p style={{ color: "#9aa0b4", fontSize: "0.9rem", lineHeight: "1.5" }}>Explore verified stamina drills recommended by national league coaches to raise your game stats in your next tournament match.</p>
            <span className="read-more-link">Read Article →</span>
          </div>
          <div className="blog-post-card">
            <div className="blog-card-img-wrap">
              <img
                src="https://images.unsplash.com/photo-1559526324-4b87b5e36e44?w=600&auto=format&fit=crop&q=80"
                alt="NXL Credits Guide"
                className="blog-card-img"
              />
            </div>
            <span style={{ color: "var(--accent-gold)", fontSize: "0.8rem", fontWeight: "bold" }}>💎 REWARDS GUIDE • MAY 20, 2026</span>
            <h3 style={{ margin: "10px 0", color: "#f5f6fa" }}>How to Triple Your NXL Credits Quickly</h3>
            <p style={{ color: "#9aa0b4", fontSize: "0.9rem", lineHeight: "1.5" }}>Discover hidden methods to earn coin referrals, book double-back tournaments, and redeem high-tier loyalty vouchers in the rewards store.</p>
            <span className="read-more-link">Read Article →</span>
          </div>
        </div>
      </section>

      {/* 9. DYNAMIC TOURNAMENTS REGISTER SECTION */}
      <section className="events-section" id="active-tournaments" style={{ background: "var(--bg-secondary)", borderTop: "1px solid rgba(197, 168, 92, 0.15)" }}>
        <div className="section-premium-title">
          <span className="title-tagline">Reserve Your Slot</span>
          <h2>📅 Active Sports Tournaments</h2>
          <div className="title-separator"></div>
        </div>

        {loadingTournaments ? (
          <p style={{ textAlign: "center", color: "var(--accent-gold)" }}>Loading live tournaments...</p>
        ) : (
          <div className="events-grid">
            {displayedTournaments.map((tournament) => (
              <div className="event-card" key={tournament.id} onClick={() => navigate("/event-registration")} style={{ background: "var(--bg-card)" }}>
                <div className="event-image">🏆</div>
                <div className="event-badge">{tournament.badge || "Live Pool"}</div>
                <h3>{tournament.name}</h3>
                
                <div className="event-details">
                  <p className="event-date">
                    <span className="event-icon">📅</span>
                    {tournament.date || "Scheduled Season"}
                  </p>
                  <p className="event-location">
                    <span className="event-icon">📍</span>
                    {tournament.venue || "GLOBAL SPORTS ARENA Complex"}
                  </p>
                  <p className="event-price" style={{ color: "var(--accent-gold)", fontWeight: "bold" }}>
                    <span className="event-icon">💰</span>
                    Fee: ₹{tournament.registrationFee}
                  </p>
                </div>

                <button className="book-btn">
                  Book Tournament Now <span>→</span>
                </button>
              </div>
            ))}
          </div>
        )}
      </section>
    </div>
  );
}

export default Home;