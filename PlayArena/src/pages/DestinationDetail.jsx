import { useParams, useNavigate } from "react-router-dom";
import { useEffect, useState } from "react";
import "../styles/DestinationDetail.css";

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

const getEventDateRange = (startMonth, startDay, endMonth, endDay) => {
  const startYear = getFutureYearForMonth(startMonth, startDay);
  let endYear = getFutureYearForMonth(endMonth, endDay);
  if (endYear < startYear) {
    endYear = startYear;
  }
  return `${startMonth} ${startDay} - ${endMonth} ${endDay}, ${endYear}`;
};

const getSingleEventDate = (month, day) => {
  const year = getFutureYearForMonth(month, day);
  return `${month} ${day}, ${year}`;
};

const defaultDestinations = [
  {
    id: 1,
    country: "INDIA",
    image: "https://images.unsplash.com/photo-1564507592333-c60657eea523?w=800&auto=format&fit=crop&q=80",
    date: getRangeDate("Oct", "Feb"),
    city: "Pune / Mumbai",
    region: "India",
    description: "Experience the vibrant energy of India, hosting the premier GSA Cricket and Badminton Leagues. Play in state-of-the-art stadiums in Mumbai and Pune.",
    events: [
      { name: "GSA National T20 Cricket Cup", prize: "₹5,00,000", entry: "₹2,500", date: getEventDateRange("Oct", 15, "Oct", 22) },
      { name: "Pune Open Badminton Championship", prize: "₹2,00,000", entry: "₹1,000", date: getEventDateRange("Nov", 5, "Nov", 9) },
      { name: "Mumbai Corporate Football Shield", prize: "₹3,50,000", entry: "₹3,000", date: getEventDateRange("Dec", 18, "Dec", 24) }
    ]
  },
  {
    id: 2,
    country: "SINGAPORE",
    image: "https://images.unsplash.com/photo-1525625293386-3f8f99389edd?w=800&auto=format&fit=crop&q=80",
    date: getRangeDate("Feb", "Apr"),
    city: "Singapore",
    region: "Singapore",
    description: "Welcome to the ultra-modern arena of Singapore, hosting high-performance swimming trials and squash tournaments under the iconic skyline.",
    events: [
      { name: "Marina Bay Aquatics Cup", prize: "S$10,000", entry: "S$100", date: getEventDateRange("Feb", 18, "Feb", 22) },
      { name: "Singapore Indoor Squash Open", prize: "S$8,000", entry: "S$75", date: getEventDateRange("Mar", 10, "Mar", 14) }
    ]
  },
  {
    id: 3,
    country: "SWITZERLAND",
    image: "https://images.unsplash.com/photo-1506744038136-46273834b3fb?w=800&auto=format&fit=crop&q=80",
    date: getRangeDate("May", "Sep"),
    city: "Zurich",
    region: "Switzerland",
    description: "Compete in the breathtaking Swiss Alps. Switzerland hosts our flagship alpine athletic matches, trail running, and tennis championships.",
    events: [
      { name: "Alpine Trail Marathon Zurich", prize: "CHF 12,000", entry: "CHF 150", date: getSingleEventDate("May", 24) },
      { name: "Swiss Clay Court Tennis Open", prize: "CHF 20,000", entry: "CHF 200", date: getEventDateRange("Jul", 15, "Jul", 21) }
    ]
  },
  {
    id: 4,
    country: "UAE",
    image: "https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=800&auto=format&fit=crop&q=80",
    date: getRangeDate("Nov", "Mar"),
    city: "Dubai / Abu Dhabi",
    region: "UAE",
    description: "Participate in luxury and elite sports in the UAE. Featuring premium football tournaments, desert motorsport challenges, and track events.",
    events: [
      { name: "GSA Gulf Gold Cup (Soccer)", prize: "AED 50,000", entry: "AED 500", date: getEventDateRange("Nov", 12, "Nov", 20) },
      { name: "Dubai Track & Field Showcase", prize: "AED 30,000", entry: "AED 300", date: getEventDateRange("Jan", 8, "Jan", 12) }
    ]
  },
  {
    id: 5,
    country: "THAILAND",
    image: "https://images.unsplash.com/photo-1508009603885-50cf7c579365?w=800&auto=format&fit=crop&q=80",
    date: getRangeDate("Sep", "Nov"),
    city: "Phuket / Bangkok",
    region: "Thailand",
    description: "Compete on tropical shores. Thailand features beach volleyball cups, martial arts showcases, and national table tennis tourneys.",
    events: [
      { name: "Phuket Beach Volleyball Cup", prize: "฿150,000", entry: "฿1,500", date: getEventDateRange("Sep", 22, "Sep", 26) },
      { name: "Bangkok Muay Thai Invitational", prize: "฿250,000", entry: "฿2,000", date: getEventDateRange("Nov", 2, "Nov", 5) }
    ]
  },
  {
    id: 6,
    country: "USA - LAS VEGAS",
    image: "https://images.unsplash.com/photo-1516900557549-41557d405adf?w=800&auto=format&fit=crop&q=80",
    date: getRangeDate("Apr", "Jul"),
    city: "Las Vegas",
    region: "USA",
    description: "Enter the entertainment capital of the world. Las Vegas hosts the GSA Ultimate Basketball Showdown and table tennis arenas.",
    events: [
      { name: "GSA Vegas 3x3 Basketball Showdown", prize: "$15,000", entry: "$150", date: getEventDateRange("Apr", 20, "Apr", 24) },
      { name: "Vegas Table Tennis Masters", prize: "$8,000", entry: "$80", date: getEventDateRange("Jun", 14, "Jun", 18) }
    ]
  },
  {
    id: 7,
    country: "USA - NEW YORK",
    image: "https://images.unsplash.com/photo-1526304640581-d334cdbbf45e?w=800&auto=format&fit=crop&q=80",
    date: getRangeDate("May", "Sep"),
    city: "New York",
    region: "USA",
    description: "Compete in the Empire State. New York hosts our summer marathon, metropolitan tennis league, and athletic field opens.",
    events: [
      { name: "GSA New York Summer Marathon", prize: "$20,000", entry: "$120", date: getSingleEventDate("May", 17) },
      { name: "Metropolitan Tennis Championship", prize: "$12,000", entry: "$100", date: getEventDateRange("Aug", 2, "Aug", 8) }
    ]
  }
];

function DestinationDetail() {
  const { id } = useParams();
  const navigate = useNavigate();
  const [destination, setDestination] = useState(null);

  useEffect(() => {
    // 1. Check default list
    let dest = defaultDestinations.find((d) => d.id.toString() === id);

    // 2. Check custom list from localstorage
    if (!dest) {
      try {
        const customList = JSON.parse(localStorage.getItem("globalsportsarena_custom_destinations") || "[]");
        const foundCustom = customList.find((d) => d.id.toString() === id);
        if (foundCustom) {
          dest = {
            ...foundCustom,
            description: `Participate in the global sports challenge in ${foundCustom.region}. Fully custom created event matches, tournaments, and packages.`,
            events: [
              { name: `GSA ${foundCustom.country} Sports Open`, prize: "TBD", entry: "₹2,000", date: foundCustom.date }
            ]
          };
        }
      } catch (err) {
        console.error("Failed to load custom destinations", err);
      }
    }

    setDestination(dest);
  }, [id]);

  if (!destination) {
    return (
      <div className="destination-detail-loading">
        <h2>Loading Destination...</h2>
        <button onClick={() => navigate("/")}>Go Back Home</button>
      </div>
    );
  }

  const handleRegister = (eventName) => {
    // Prefill register page via react state
    navigate("/event-registration", {
      state: {
        prefilledEvent: eventName,
        prefilledLocation: `${destination.city}, ${destination.region}`
      }
    });
  };

  return (
    <div className="dest-detail-page">
      {/* Hero Header */}
      <div className="dest-hero" style={{ backgroundImage: `linear-gradient(rgba(11, 12, 16, 0.4), #0b0c10), url(${destination.image})` }}>
        <div className="dest-hero-content">
          <button className="dest-back-btn" onClick={() => navigate("/")}>
            ← Back to Home
          </button>
          <span className="dest-tag">🏆 GLOBAL TOURNAMENT LOCATION</span>
          <h1>{destination.country}</h1>
          <p className="dest-meta">📅 {destination.date} | 📍 {destination.city}</p>
        </div>
      </div>

      {/* Detail Content */}
      <div className="dest-content-container">
        <div className="dest-main">
          {/* Section 1: Overview */}
          <div className="dest-section card-glass">
            <h2>About the Destination</h2>
            <p className="dest-description">{destination.description}</p>
            
            <div className="dest-highlights-grid">
              <div className="highlight-item">
                <span className="highlight-icon">🏟️</span>
                <div>
                  <h4>Premium Venues</h4>
                  <p>World-class courts and arenas certified by global standards.</p>
                </div>
              </div>
              <div className="highlight-item">
                <span className="highlight-icon">💎</span>
                <div>
                  <h4>NXL Rewards multipliers</h4>
                  <p>Earn 2x NXL credits on registrations and merchandise at this venue.</p>
                </div>
              </div>
            </div>
          </div>

          {/* Section 2: Active Events */}
          <div className="dest-section card-glass">
            <h2>Active & Upcoming Tournaments</h2>
            <p className="section-subtitle">Select a tournament below to secure your registration code and QR entry pass.</p>
            
            <div className="events-list">
              {destination.events && destination.events.length > 0 ? (
                destination.events.map((event, idx) => (
                  <div className="event-row-card" key={idx}>
                    <div className="event-info-main">
                      <h3>{event.name}</h3>
                      <p className="event-date">📅 {event.date}</p>
                    </div>
                    
                    <div className="event-meta-info">
                      <div className="meta-col">
                        <span>Prize Pool</span>
                        <strong>{event.prize}</strong>
                      </div>
                      <div className="meta-col">
                        <span>Entry Fee</span>
                        <strong>{event.entry}</strong>
                      </div>
                      <button className="event-register-btn" onClick={() => handleRegister(event.name)}>
                        Register Now
                      </button>
                    </div>
                  </div>
                ))
              ) : (
                <p>No active events currently scheduled. Check back soon!</p>
              )}
            </div>
          </div>
        </div>

        {/* Sidebar */}
        <div className="dest-sidebar">
          {/* Quick Info Box */}
          <div className="sidebar-card card-glass text-center">
            <h3>Quick Details</h3>
            <div className="info-item">
              <span className="info-label">Host City</span>
              <strong className="info-val">{destination.city}</strong>
            </div>
            <div className="info-item">
              <span className="info-label">Schedule Block</span>
              <strong className="info-val">{destination.date}</strong>
            </div>
            <div className="info-item">
              <span className="info-label">Country Code</span>
              <strong className="info-val">{destination.region}</strong>
            </div>
            
            <button className="sidebar-cta-btn" onClick={() => handleRegister(`GSA ${destination.country} Open Showcase`)}>
              Quick Registration →
            </button>
          </div>

          {/* Travel & Accommodation Card */}
          <div className="sidebar-card card-glass">
            <h3>✈️ Travel & Lodging</h3>
            <p>Our official hospitality partner provides GSA athletes with discounted flight bookings and hotel rooms near the stadiums.</p>
            <ul className="travel-perks">
              <li>🏨 15% off official partner hotels</li>
              <li>🚌 Free shuttle transfer to venues</li>
              <li>🎫 Complimentary spectator passes</li>
            </ul>
            <button className="travel-info-btn" onClick={() => alert("Travel assistance desk contact: support@globalsportsarena.com")}>
              Get Travel Help
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}

export default DestinationDetail;
