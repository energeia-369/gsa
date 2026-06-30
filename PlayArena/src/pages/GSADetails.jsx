import { useNavigate } from "react-router-dom";
import "../styles/GSADetails.css";

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

const getSingleEventDate = (month, day) => {
  const year = getFutureYearForMonth(month, day);
  return `${month} ${day}, ${year}`;
};

function GSADetails() {
  const navigate = useNavigate();

  const features = [
    { icon: "🏀", title: "Basketball Arena", desc: "FIBA-spec indoor court with professional flooring and seating." },
    { icon: "⚽", title: "Football Turf", desc: "FIFA-certified artificial grass turf for 11v11 and 7v7 matches." },
    { icon: "🎵", title: "Live Concerts", desc: "Acclaimed acoustics and space supporting up to 25,000 spectators." },
    { icon: "🏨", title: "Nearby Hotels", desc: "Partnerships with luxury hotels, offering GSA guests up to 20% off." },
    { icon: "🍔", title: "Food Courts", desc: "Dozens of multi-cuisine outlets, organic juice bars, and cafes." },
    { icon: "🎟", title: "VIP Tickets", desc: "Premium lounge seating, private parking, and exclusive catering." }
  ];

  const upcomingEvents = [
    { title: "IPL Screening - Final Match", date: getSingleEventDate("Oct", 26), time: "7:00 PM IST", bg: "https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=500" },
    { title: "GSA Basketball Pro Championship", date: getSingleEventDate("Nov", 14), time: "5:30 PM IST", bg: "https://images.unsplash.com/photo-1546519638-68e109498ffc?w=500" },
    { title: "National Club Football League", date: getSingleEventDate("Dec", 5), time: "4:00 PM IST", bg: "https://images.unsplash.com/photo-1508098682722-e99c43a406b2?w=500" },
    { title: "GSA Winter Music & Food Festival", date: getSingleEventDate("Jan", 18), time: "2:00 PM IST", bg: "https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=500" }
  ];

  const galleryImages = [
    { url: "https://images.unsplash.com/photo-1546519638-68e109498ffc?w=400", title: "Basketball Arena" },
    { url: "https://images.unsplash.com/photo-1508098682722-e99c43a406b2?w=400", title: "Football Field" },
    { url: "https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=400", title: "Live Concert Hall" },
    { url: "https://images.unsplash.com/photo-1595435934249-5df7ed86e1c0?w=400", title: "Tennis Courts" },
    { url: "https://images.unsplash.com/photo-1476480862126-209bfaa8edc8?w=400", title: "Track Running" },
    { url: "https://images.unsplash.com/photo-1517838277536-f5f99be501cd?w=400", title: "Elite Gym Center" }
  ];

  const reviews = [
    { name: "Sanket S.", stars: 5, comment: "Amazing experience! The turf is absolutely perfect and food courts are top tier.", date: "2 days ago" },
    { name: "Mithraa E.", stars: 5, comment: "Best sports arena in the country. The VIP lounge is highly recommended!", date: "1 week ago" },
    { name: "Rahul P.", stars: 5, comment: "Watched the football finals here. Energetic crowd, perfect lighting, and acoustics.", date: "3 weeks ago" }
  ];

  const handleRegister = (title) => {
    navigate("/event-registration", {
      state: { prefilledEvent: title, prefilledLocation: "Global Sports Arena, Pune HQ" }
    });
  };

  return (
    <div className="gsa-detail-page">
      {/* 1. Hero Section */}
      <section className="gsa-hero">
        <div className="gsa-hero-overlay"></div>
        <div className="gsa-hero-content">
          <button className="gsa-back-btn" onClick={() => navigate("/")}>
            ← Back to Home
          </button>
          <span className="gsa-badge">⭐ THE ULTIMATE SPORTS DESTINATION</span>
          <h1>Welcome to Global Sports Arena</h1>
          <p>Experience world-class sports, tournaments & entertainment.</p>
        </div>
      </section>

      {/* 2. About Arena */}
      <section className="gsa-about-section">
        <div className="gsa-container">
          <div className="about-grid">
            <div className="about-text-box">
              <h2>Where Sports & Entertainment Meet</h2>
              <p>
                Global Sports Arena is a world-class venue hosting premier indoor and outdoor sports tournaments. 
                Our facilities cater to international athletic events, offering professional training setups, stadium seating, and fully certified playing fields.
              </p>
              <p>
                Beyond sports, GSA acts as a dynamic cultural hub, staging massive live concerts, musical festivals, and corporate summits. 
                Our arena integrates hospitality, premium food courts, and unique tourism experiences to offer visitors an unforgettable global-stage encounter.
              </p>
            </div>
            <div className="about-stats-box">
              <div className="stat-card">
                <h3>50,000+</h3>
                <p>Capacity Crowd</p>
              </div>
              <div className="stat-card">
                <h3>15+</h3>
                <p>Professional Sports</p>
              </div>
              <div className="stat-card">
                <h3>100%</h3>
                <p>FIFA & FIBA Standard</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* 3. Features Section */}
      <section className="gsa-features-section">
        <div className="gsa-container">
          <div className="section-header">
            <span>UNMATCHED FACILITIES</span>
            <h2>Arena Features</h2>
          </div>
          <div className="features-grid">
            {features.map((feature, idx) => (
              <div className="feature-card" key={idx}>
                <div className="feature-icon-circle">{feature.icon}</div>
                <h3>{feature.title}</h3>
                <p>{feature.desc}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* 4. Upcoming Events */}
      <section className="gsa-events-section">
        <div className="gsa-container">
          <div className="section-header">
            <span>UPCOMING SHOWDOWNS</span>
            <h2>Active Events & Shows</h2>
          </div>
          <div className="events-grid">
            {upcomingEvents.map((event, idx) => (
              <div className="event-card" key={idx} style={{ backgroundImage: `linear-gradient(rgba(18, 19, 28, 0.4), #12131c), url(${event.bg})` }}>
                <div className="event-card-content">
                  <div className="event-datetime">
                    <span>{event.date}</span>
                    <span>•</span>
                    <span>{event.time}</span>
                  </div>
                  <h3>{event.title}</h3>
                  <button className="event-card-btn" onClick={() => handleRegister(event.title)}>
                    Register Now
                  </button>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* 5. Gallery Section */}
      <section className="gsa-gallery-section">
        <div className="gsa-container">
          <div className="section-header">
            <span>GALLERY EXHIBIT</span>
            <h2>Visual Tour of GSA</h2>
          </div>
          <div className="gallery-grid">
            {galleryImages.map((img, idx) => (
              <div className="gallery-item" key={idx}>
                <img src={img.url} alt={img.title} />
                <div className="gallery-hover-overlay">
                  <h4>{img.title}</h4>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* 6. Booking Section */}
      <section className="gsa-booking-section">
        <div className="gsa-container">
          <div className="booking-box card-glass">
            <h2>Book Your Spot at GSA</h2>
            <p>Schedule your match, buy concert passes, or sign up for corporate league bookings instantly.</p>
            <div className="booking-btn-row">
              <button className="booking-btn primary" onClick={() => handleRegister("GSA Custom Arena Venue Booking")}>
                📅 Book Event
              </button>
              <button className="booking-btn secondary" onClick={() => navigate("/products")}>
                🎟 Buy Tickets
              </button>
              <button className="booking-btn highlight" onClick={() => navigate("/sports-categories")}>
                🏅 Join Tournament
              </button>
            </div>
          </div>
        </div>
      </section>

      {/* 7. Reviews Section */}
      <section className="gsa-reviews-section">
        <div className="gsa-container">
          <div className="section-header">
            <span>ATHLETE & SPECTATOR FEEDBACK</span>
            <h2>Reviews</h2>
          </div>
          <div className="reviews-grid">
            {reviews.map((rev, idx) => (
              <div className="review-card" key={idx}>
                <div className="review-header">
                  <div className="stars-row">
                    {Array.from({ length: rev.stars }).map((_, i) => (
                      <span key={i}>⭐</span>
                    ))}
                  </div>
                  <span className="review-date">{rev.date}</span>
                </div>
                <p className="review-comment">"{rev.comment}"</p>
                <h4 className="review-author">- {rev.name}</h4>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* 8. Location Section */}
      <section className="gsa-location-section">
        <div className="gsa-container">
          <div className="section-header">
            <span>OUR ADDRESS</span>
            <h2>Location & Venue Map</h2>
          </div>
          <div className="location-box card-glass">
            <div className="location-info">
              <h3>Global Sports Arena HQ</h3>
              <p>📍 Sector 10, Sports Complex Area, Pune, Maharashtra - 411001</p>
              <p>📞 Helpline: +91 98765 43210</p>
              <p>✉️ Venue Inquiries: booking@globalsportsarena.com</p>
            </div>
            <div className="map-embed-placeholder">
              <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d121059.03447395568!2d73.79292686884632!3d18.52043029837554!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bc2bf2e67461101%3A0x828f43bf9d089e34!2sPune%2C%20Maharashtra!5e0!3m2!1sen!2sin!4v1717800000000!5m2!1sen!2sin" 
                width="100%" 
                height="320" 
                style={{ border: 0, borderRadius: "16px" }} 
                allowFullScreen="" 
                loading="lazy" 
                referrerPolicy="no-referrer-when-downgrade"
                title="GSA Google Maps Location"
              ></iframe>
            </div>
          </div>
        </div>
      </section>

      {/* 9. Footer CTA */}
      <section className="gsa-footer-cta">
        <div className="cta-overlay"></div>
        <div className="cta-content">
          <h2>Ready to experience the excitement?</h2>
          <p>Book a field, buy event tickets, or register for upcoming championships now.</p>
          <button className="cta-btn-book" onClick={() => handleRegister("GSA Full Access Arena Booking")}>
            BOOK NOW
          </button>
        </div>
      </section>
    </div>
  );
}

export default GSADetails;
