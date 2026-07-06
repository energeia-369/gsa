<?php
$pageTitle = "GLOBAL SPORTS ARENA | Destination Details";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<link rel="stylesheet" href="assets/css/DestinationDetail.css?v=3">

<div id="destDetailPageContent">
  <!-- Loading state -->
  <div class="destination-detail-loading" id="destDetailLoading">
    <h2>Loading Destination...</h2>
    <button onclick="window.location.href='index.php'">Go Back Home</button>
  </div>
</div>

<script>
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
  },
  {
    id: 8,
    country: "MALAYSIA",
    image: "https://images.unsplash.com/photo-1596422846543-75c6fc197f07?w=800&auto=format&fit=crop&q=80",
    date: getRangeDate("Nov", "Dec"),
    city: "Kuala Lumpur",
    region: "Malaysia",
    description: "Experience the vibrant sports culture of Malaysia. Host to the GSA Southeast Asian Badminton Open and competitive eSports tournaments.",
    events: [
      { name: "GSA KL Badminton Masters", prize: "RM 50,000", entry: "RM 250", date: getEventDateRange("Nov", 20, "Nov", 24) },
      { name: "Malaysia Cup Sepak Takraw", prize: "RM 25,000", entry: "RM 100", date: getEventDateRange("Dec", 5, "Dec", 8) }
    ]
  },
  {
    id: 9,
    country: "INDONESIA",
    image: "https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=800&auto=format&fit=crop&q=80",
    date: getRangeDate("Jan", "Mar"),
    city: "Bali / Jakarta",
    region: "Indonesia",
    description: "Compete in the beautiful archipelago of Indonesia. Offering tropical surfing championships and national indoor volleyball cups.",
    events: [
      { name: "Bali Surf Open Championship", prize: "Rp 150,000,000", entry: "Rp 500,000", date: getEventDateRange("Jan", 15, "Jan", 20) },
      { name: "Jakarta Indoor Volleyball Cup", prize: "Rp 80,000,000", entry: "Rp 300,000", date: getEventDateRange("Feb", 10, "Feb", 14) }
    ]
  },
  {
    id: 10,
    country: "VIETNAM",
    image: "https://images.unsplash.com/photo-1528127269322-539801943592?w=800&auto=format&fit=crop&q=80",
    date: getRangeDate("Feb", "Apr"),
    city: "Ho Chi Minh",
    region: "Vietnam",
    description: "Discover the dynamic sports arenas of Vietnam. We bring you premier table tennis and marathon events in the heart of the city.",
    events: [
      { name: "Saigon City Marathon", prize: "₫ 200,000,000", entry: "₫ 500,000", date: getSingleEventDate("Feb", 19) },
      { name: "Vietnam Table Tennis Open", prize: "₫ 50,000,000", entry: "₫ 200,000", date: getEventDateRange("Mar", 12, "Mar", 16) }
    ]
  },
  {
    id: 11,
    country: "AUSTRALIA",
    image: "https://images.unsplash.com/photo-1523482580672-f109ba8cb9be?w=800&auto=format&fit=crop&q=80",
    date: getRangeDate("Mar", "May"),
    city: "Sydney",
    region: "Australia",
    description: "Participate in elite sports down under. Australia offers world-class swimming trials and outdoor rugby sevens tournaments.",
    events: [
      { name: "Sydney Harbour Swim Classic", prize: "AUD 15,000", entry: "AUD 150", date: getSingleEventDate("Mar", 20) },
      { name: "Aussie Rugby Sevens Series", prize: "AUD 30,000", entry: "AUD 300", date: getEventDateRange("Apr", 14, "Apr", 16) }
    ]
  },
  {
    id: 12,
    country: "GERMANY",
    image: "https://images.unsplash.com/photo-1467269204594-9661b134dd2b?w=800&auto=format&fit=crop&q=80",
    date: getRangeDate("Apr", "Jun"),
    city: "Berlin",
    region: "Germany",
    description: "Experience the peak of European sports performance in Germany. Hosting track and field showcases and competitive cycling tours.",
    events: [
      { name: "Berlin Track & Field Open", prize: "€ 20,000", entry: "€ 100", date: getEventDateRange("Apr", 23, "Apr", 25) },
      { name: "German Cycling Tour", prize: "€ 40,000", entry: "€ 250", date: getEventDateRange("May", 10, "May", 15) }
    ]
  },
  {
    id: 13,
    country: "UNITED KINGDOM",
    image: "https://images.unsplash.com/photo-1505761671935-60b3a7427bad?w=800&auto=format&fit=crop&q=80",
    date: getRangeDate("May", "Jul"),
    city: "London",
    region: "UK",
    description: "Compete in the historic stadiums of the UK. From prestigious grass-court tennis to premier league football experiences.",
    events: [
      { name: "London Grass Court Championship", prize: "£ 25,000", entry: "£ 200", date: getEventDateRange("May", 21, "May", 25) },
      { name: "GSA British Soccer Cup", prize: "£ 50,000", entry: "£ 500", date: getEventDateRange("Jun", 15, "Jun", 20) }
    ]
  },
  {
    id: 14,
    country: "CANADA",
    image: "https://images.unsplash.com/photo-1503614472-8c93d56e92ce?w=800&auto=format&fit=crop&q=80",
    date: getRangeDate("Jun", "Aug"),
    city: "Toronto",
    region: "Canada",
    description: "Join the northern games in Canada. Ice hockey clinics, national basketball tournaments, and beautiful outdoor trails.",
    events: [
      { name: "Toronto Ice Hockey Classic", prize: "CAD 30,000", entry: "CAD 350", date: getEventDateRange("Jun", 18, "Jun", 22) },
      { name: "Canadian Basketball Open", prize: "CAD 20,000", entry: "CAD 250", date: getEventDateRange("Jul", 10, "Jul", 14) }
    ]
  },
  {
    id: 101,
    country: "MAHARASHTRA",
    image: "https://images.unsplash.com/photo-1570168007204-dfb528c6958f?w=800&auto=format&fit=crop&q=80",
    date: getRangeDate("Aug", "Oct"),
    city: "Mumbai / Pune",
    region: "India",
    description: "Experience the vibrant energy of Maharashtra, hosting the premier GSA Cricket and Badminton Leagues. Play in state-of-the-art stadiums in Mumbai and Pune.",
    events: [
      { name: "Maharashtra T20 Cricket Cup", prize: "₹5,00,000", entry: "₹2,500", date: getEventDateRange("Aug", 15, "Aug", 22) },
      { name: "Pune Open Badminton Championship", prize: "₹2,00,000", entry: "₹1,000", date: getEventDateRange("Sep", 5, "Sep", 9) }
    ]
  },
  {
    id: 102,
    country: "KARNATAKA",
    image: "https://images.unsplash.com/photo-1596176530529-78163a4f7af2?w=800&auto=format&fit=crop&q=80",
    date: getRangeDate("Sep", "Nov"),
    city: "Bangalore",
    region: "India",
    description: "Welcome to the Silicon Valley of India, Karnataka. Host to the high-performance athletics and regional squash tournaments.",
    events: [
      { name: "Bangalore Athletics Showcase", prize: "₹3,00,000", entry: "₹1,500", date: getEventDateRange("Sep", 18, "Sep", 22) },
      { name: "Karnataka Indoor Squash Open", prize: "₹1,50,000", entry: "₹800", date: getEventDateRange("Oct", 10, "Oct", 14) }
    ]
  },
  {
    id: 103,
    country: "DELHI",
    image: "https://images.unsplash.com/photo-1587474260584-136574528ed5?w=800&auto=format&fit=crop&q=80",
    date: getRangeDate("Oct", "Dec"),
    city: "New Delhi",
    region: "India",
    description: "Compete in the capital region of India. Delhi hosts our flagship athletic matches, marathons, and tennis championships.",
    events: [
      { name: "New Delhi Capital Marathon", prize: "₹4,00,000", entry: "₹2,000", date: getSingleEventDate("Oct", 24) },
      { name: "Delhi Tennis Open", prize: "₹3,50,000", entry: "₹1,800", date: getEventDateRange("Nov", 15, "Nov", 21) }
    ]
  },
  {
    id: 104,
    country: "GOA",
    image: "https://images.unsplash.com/photo-1512343879784-a960bf40e7f2?w=800&auto=format&fit=crop&q=80",
    date: getRangeDate("Nov", "Jan"),
    city: "Panaji",
    region: "India",
    description: "Participate in luxury and elite sports in Goa. Featuring beach volleyball cups and water sports challenges along the beautiful coastline.",
    events: [
      { name: "Goa Beach Volleyball Cup", prize: "₹2,50,000", entry: "₹1,200", date: getEventDateRange("Nov", 12, "Nov", 16) },
      { name: "Panaji Water Sports Challenge", prize: "₹3,00,000", entry: "₹1,500", date: getEventDateRange("Dec", 8, "Dec", 12) }
    ]
  },
  {
    id: 105,
    country: "KERALA",
    image: "https://images.unsplash.com/photo-1602216056096-3b40cc0c9944?w=800&auto=format&fit=crop&q=80",
    date: getRangeDate("Dec", "Feb"),
    city: "Kochi",
    region: "India",
    description: "Compete in the tropical shores of God's Own Country. Kerala features exciting boat races and martial arts showcases.",
    events: [
      { name: "Kerala Grand Boat Race", prize: "₹5,00,000", entry: "₹5,000", date: getSingleEventDate("Dec", 20) },
      { name: "Kalaripayattu Martial Arts Showcase", prize: "₹2,00,000", entry: "₹1,000", date: getEventDateRange("Jan", 15, "Jan", 18) }
    ]
  },
  {
    id: 106,
    country: "RAJASTHAN",
    image: "https://images.unsplash.com/photo-1477587458883-47145ed94245?w=800&auto=format&fit=crop&q=80",
    date: getRangeDate("Jan", "Mar"),
    city: "Jaipur",
    region: "India",
    description: "Experience the royal heritage of Rajasthan. Host to the GSA polo matches and desert marathon events.",
    events: [
      { name: "Jaipur Royal Polo Match", prize: "₹10,00,000", entry: "₹10,000", date: getEventDateRange("Jan", 20, "Jan", 24) },
      { name: "Thar Desert Marathon", prize: "₹3,00,000", entry: "₹1,500", date: getSingleEventDate("Feb", 14) }
    ]
  },
  {
    id: 107,
    country: "GUJARAT",
    image: "https://images.unsplash.com/photo-1605130284535-11dd9eedc58a?w=800&auto=format&fit=crop&q=80",
    date: getRangeDate("Feb", "Apr"),
    city: "Ahmedabad",
    region: "India",
    description: "Compete in the vibrant state of Gujarat. Featuring premier cricket tournaments at the largest stadium and kite flying festivals.",
    events: [
      { name: "Ahmedabad T20 Championship", prize: "₹8,00,000", entry: "₹4,000", date: getEventDateRange("Feb", 10, "Feb", 18) },
      { name: "Gujarat International Kite Cup", prize: "₹1,50,000", entry: "₹500", date: getEventDateRange("Mar", 12, "Mar", 14) }
    ]
  }
];

document.addEventListener("DOMContentLoaded", async function() {
    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');

    let destination = defaultDestinations.find(d => d.id.toString() === id);

    if (id) {
        try {
            const response = await fetch('api/index.php/destinations');
            const data = await response.json();
            const customList = Array.isArray(data) ? data : [];
            const foundCustom = customList.find(d => d.id.toString() === id);
            
            if (foundCustom) {
                if (foundCustom.deleted) {
                    destination = null; // Don't show if deleted
                } else if (destination) {
                    // It's a default destination that was edited
                    destination = { ...destination, ...foundCustom };
                } else {
                    // It's a purely custom destination
                    destination = {
                        ...foundCustom,
                        description: `Participate in the global sports challenge in ${foundCustom.region}. Fully custom created event matches, tournaments, and packages.`,
                        events: [
                            { name: `GSA ${foundCustom.country} Sports Open`, prize: "TBD", entry: "₹2,000", date: foundCustom.date }
                        ]
                    };
                }
            }
        } catch(err) {
            console.error("Failed to load destinations", err);
        }
        
        // Fetch DB Tournaments to override hardcoded events
        if (destination) {
            try {
                const tourRes = await fetch('api/index.php/tournaments');
                const allTournaments = await tourRes.json();
                
                const destCountry = destination.country.split('-').pop().trim().toLowerCase(); // e.g. "USA - NEW YORK" -> "new york"
                const destCity = destination.city.toLowerCase();
                
                const matchingTournaments = allTournaments.filter(t => {
                    const v = t.venue.toLowerCase();
                    return v.includes(destCity) || v.includes(destCountry);
                });
                
                if (matchingTournaments.length > 0) {
                    destination.events = matchingTournaments.map(t => ({
                        name: t.name,
                        prize: "TBD", // Currently no prize field in DB
                        entry: "₹" + (t.registration_fee || t.registrationFee || 0),
                        date: t.date
                    }));
                } else {
                    destination.events = [];
                }
            } catch (err) {
                console.error("Failed to fetch DB tournaments", err);
            }
        }
    }

    const container = document.getElementById("destDetailPageContent");
    if (!destination) {
        container.innerHTML = `
            <div class="destination-detail-loading">
              <h2>Destination Not Found</h2>
              <button onclick="window.location.href='index.php'">Go Back Home</button>
            </div>
        `;
        return;
    }

    container.innerHTML = `
        <div class="dest-detail-page">
          <!-- Hero Header -->
          <div class="dest-hero" style="background-image: url(${destination.image});">
            <div class="dest-hero-content">
              <button class="dest-back-btn" onclick="window.location.href='index.php'">
                ← Back to Home
              </button>
              <span class="dest-tag">🏆 GLOBAL TOURNAMENT LOCATION</span>
              <h1>${destination.country}</h1>
              <p class="dest-meta">📅 ${destination.date} | 📍 ${destination.city}</p>
            </div>
          </div>

          <!-- Detail Content -->
          <div class="dest-content-container">
            <div class="dest-main">
              <!-- Section 1: Overview -->
              <div class="dest-section card-glass">
                <h2>About the Destination</h2>
                <p class="dest-description">${destination.description}</p>
                
                <div class="dest-highlights-grid">
                  <div class="highlight-item">
                    <span class="highlight-icon">🏟️</span>
                    <div>
                      <h4>Premium Venues</h4>
                      <p>World-class courts and arenas certified by global standards.</p>
                    </div>
                  </div>
                  <div class="highlight-item">
                    <span class="highlight-icon">💎</span>
                    <div>
                      <h4>NXL Rewards multipliers</h4>
                      <p>Earn 2x NXL credits on registrations and merchandise at this venue.</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Section 2: Active Events -->
              <div class="dest-section card-glass">
                <h2>Active & Upcoming Tournaments</h2>
                <p class="section-subtitle">Select a tournament below to secure your registration code and QR entry pass.</p>
                
                <div class="events-list">
                  ${destination.events && destination.events.length > 0 ? 
                    destination.events.map((event, idx) => `
                      <div class="event-row-card">
                        <div class="event-info-main">
                          <h3>${event.name}</h3>
                          <p class="event-date">📅 ${event.date}</p>
                        </div>
                        
                        <div class="event-meta-info">
                          <div class="meta-col">
                            <span>Prize Pool</span>
                            <strong>${event.prize}</strong>
                          </div>
                          <div class="meta-col">
                            <span>Entry Fee</span>
                            <strong>${event.entry}</strong>
                          </div>
                          <button class="event-register-btn" onclick="handleRegister('${event.name}', '${destination.city}, ${destination.region}')">
                            Register Now
                          </button>
                        </div>
                      </div>
                    `).join('') : '<p>No active events currently scheduled. Check back soon!</p>'
                  }
                </div>
              </div>
              
              <!-- Custom Thailand Festival Concept Section -->
              ${destination.country === 'THAILAND' ? `
                <div class="dest-section card-glass thailand-festival-concept">
                  <h2 style="color: #c5a85c; margin-bottom: 15px;">GSA Thailand – Official Festival Concept</h2>
                  <p style="margin-bottom: 25px; line-height: 1.6; color: #d4d4d4;">This three-city concept positions GSA Thailand as a destination festival that combines sports, business, tourism, investment, culture, sustainability, and international networking, making it attractive to participants, sponsors, government agencies, and global media.</p>
                  
                  <div class="festival-phases" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 30px;">
                    <div class="phase-card" style="background: rgba(255,255,255,0.03); padding: 20px; border-radius: 12px; border: 1px solid rgba(197, 168, 92, 0.2);">
                      <h3 style="color: #c5a85c; font-size: 1.1rem; margin-bottom: 10px;">📍 Phase 1: Bangkok <br><span style="color: #fff; font-size: 0.95rem;">"NEXUS ELITE"</span></h3>
                      <p style="font-size: 0.9rem; margin-bottom: 5px;"><strong>Duration:</strong> 2 Days</p>
                      <p style="font-size: 0.9rem; margin-bottom: 15px;"><strong>Venue:</strong> Queen Sirikit National Convention Center / Bangkok Intl Trade & Exhibition Centre</p>
                      <h4 style="font-size: 0.9rem; color: #a3a3a3; margin-bottom: 8px;">Activities:</h4>
                      <ul style="font-size: 0.85rem; padding-left: 18px; line-height: 1.6; color: #e2e2e2;">
                          <li>Global Opening Ceremony</li>
                          <li>Sports Business Summit</li>
                          <li>Investor & Startup Pitch Sessions</li>
                          <li>Sports Technology & AI Expo</li>
                          <li>EV & Sustainable Mobility Pavilion</li>
                          <li>Health & Wellness Expo</li>
                          <li>Government & Embassy Networking</li>
                          <li>B2B and B2G Meetings</li>
                          <li>Franchise & Investment Expo</li>
                          <li>Innovation Awards</li>
                      </ul>
                    </div>

                    <div class="phase-card" style="background: rgba(255,255,255,0.03); padding: 20px; border-radius: 12px; border: 1px solid rgba(197, 168, 92, 0.2);">
                      <h3 style="color: #c5a85c; font-size: 1.1rem; margin-bottom: 10px;">📍 Phase 2: Pattaya <br><span style="color: #fff; font-size: 0.95rem;">"OCEAN ADVENTURE & SPORTS FEST"</span></h3>
                      <p style="font-size: 0.9rem; margin-bottom: 5px;"><strong>Duration:</strong> 2 Days</p>
                      <p style="font-size: 0.9rem; margin-bottom: 15px;"><strong>Locations:</strong> Jomtien Beach, Pattaya Beach</p>
                      <h4 style="font-size: 0.9rem; color: #a3a3a3; margin-bottom: 8px;">Activities:</h4>
                      <ul style="font-size: 0.85rem; padding-left: 18px; line-height: 1.6; color: #e2e2e2;">
                          <li>Jet Ski Championship</li>
                          <li>Parasailing & Flyboard Competition</li>
                          <li>Beach Volleyball, Football & Kabaddi</li>
                          <li>Dragon Boat Demonstration</li>
                          <li>Paddle Board Competition</li>
                          <li>Sailing Showcase</li>
                          <li>Beach Fitness Challenge</li>
                          <li>Food Festival</li>
                          <li>Night Cultural Performances</li>
                      </ul>
                    </div>

                    <div class="phase-card" style="background: rgba(255,255,255,0.03); padding: 20px; border-radius: 12px; border: 1px solid rgba(197, 168, 92, 0.2);">
                      <h3 style="color: #c5a85c; font-size: 1.1rem; margin-bottom: 10px;">📍 Phase 3: Phuket <br><span style="color: #fff; font-size: 0.95rem;">"GLOBAL EXCELLENCE & AWARDS"</span></h3>
                      <p style="font-size: 0.9rem; margin-bottom: 5px;"><strong>Duration:</strong> 2 Days</p>
                      <p style="font-size: 0.9rem; margin-bottom: 15px;"><strong>Venue:</strong> Phuket Convention Exhibition Hall</p>
                      <h4 style="font-size: 0.9rem; color: #a3a3a3; margin-bottom: 8px;">Activities:</h4>
                      <ul style="font-size: 0.85rem; padding-left: 18px; line-height: 1.6; color: #e2e2e2;">
                          <li>International Leadership Summit</li>
                          <li>Tourism & Hospitality Forum</li>
                          <li>Sustainability Forum</li>
                          <li>Luxury Yacht Showcase</li>
                          <li>Global Business Networking</li>
                          <li>International Cultural Festival</li>
                          <li>GSA Excellence Awards</li>
                          <li>Closing Ceremony & Gala Dinner</li>
                          <li>Entertainment Show & International Artist Performances</li>
                      </ul>
                    </div>
                  </div>

                  <h3 style="color: #c5a85c; margin-bottom: 15px;">Proposed 6-Day Schedule</h3>
                  <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; margin-bottom: 25px; text-align: left; font-size: 0.95rem;">
                      <thead>
                        <tr style="background: rgba(197, 168, 92, 0.15); color: #c5a85c;">
                          <th style="padding: 12px; border-bottom: 1px solid rgba(197,168,92,0.3);">Day</th>
                          <th style="padding: 12px; border-bottom: 1px solid rgba(197,168,92,0.3);">City</th>
                          <th style="padding: 12px; border-bottom: 1px solid rgba(197,168,92,0.3);">Theme</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);"><td style="padding: 12px;">Day 1</td><td style="padding: 12px;">Bangkok</td><td style="padding: 12px;">Opening Ceremony & Expo</td></tr>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);"><td style="padding: 12px;">Day 2</td><td style="padding: 12px;">Bangkok</td><td style="padding: 12px;">Conferences, Investors & B2B</td></tr>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);"><td style="padding: 12px;">Day 3</td><td style="padding: 12px;">Pattaya</td><td style="padding: 12px;">Water Sports Festival</td></tr>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);"><td style="padding: 12px;">Day 4</td><td style="padding: 12px;">Pattaya</td><td style="padding: 12px;">Beach Sports & Cultural Night</td></tr>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.1);"><td style="padding: 12px;">Day 5</td><td style="padding: 12px;">Phuket</td><td style="padding: 12px;">Leadership Summit & Networking</td></tr>
                        <tr><td style="padding: 12px;">Day 6</td><td style="padding: 12px;">Phuket</td><td style="padding: 12px;">GSA Global Awards & Grand Closing</td></tr>
                      </tbody>
                    </table>
                  </div>

                  <div style="background: rgba(197, 168, 92, 0.08); padding: 20px; border-radius: 8px; border-left: 4px solid #c5a85c;">
                    <h4 style="color: #c5a85c; margin-bottom: 10px; font-size: 1rem;">Why this format works:</h4>
                    <ul style="margin: 0; padding-left: 20px; font-size: 0.9rem; line-height: 1.6;">
                      <li style="margin-bottom: 5px;"><strong>Bangkok</strong> serves as the business and innovation hub.</li>
                      <li style="margin-bottom: 5px;"><strong>Pattaya</strong> becomes the sports and adventure destination.</li>
                      <li><strong>Phuket</strong> provides a premium setting for leadership, tourism, networking, and the prestigious GSA Awards Ceremony.</li>
                    </ul>
                  </div>
                </div>
              ` : ''}
            </div>

            <!-- Sidebar -->
            <div class="dest-sidebar">
              <!-- Quick Info Box -->
              <div class="sidebar-card card-glass text-center">
                <h3>Quick Details</h3>
                <div class="info-item">
                  <span class="info-label">Host City</span>
                  <strong class="info-val">${destination.city}</strong>
                </div>
                <div class="info-item">
                  <span class="info-label">Schedule Block</span>
                  <strong class="info-val">${destination.date}</strong>
                </div>
                <div class="info-item">
                  <span class="info-label">Country Code</span>
                  <strong class="info-val">${destination.region}</strong>
                </div>
                
                <button class="sidebar-cta-btn" onclick="handleRegister('GSA ${destination.country} Open Showcase', '${destination.city}, ${destination.region}')">
                  Quick Registration →
                </button>
              </div>

              <!-- Travel & Accommodation Card -->
              <div class="sidebar-card card-glass">
                <h3>✈️ Travel & Lodging</h3>
                <p>Our official hospitality partner provides GSA athletes with discounted flight bookings and hotel rooms near the stadiums.</p>
                <ul class="travel-perks">
                  <li>🏨 15% off official partner hotels</li>
                  <li>🚌 Free shuttle transfer to venues</li>
                  <li>🎫 Complimentary spectator passes</li>
                </ul>
                <button class="travel-info-btn" onclick="alert('Travel assistance desk contact: support@globalsportsarena.com')">
                  Get Travel Help
                </button>
              </div>
            </div>
          </div>
        </div>
    `;
});

function handleRegister(eventName, location) {
    // Save state in session storage or URL parameters for event registration page
    sessionStorage.setItem("prefilledEvent", eventName);
    sessionStorage.setItem("prefilledLocation", location);
    window.location.href = "event-registration.php";
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
