<?php
$pageTitle = "GLOBAL SPORTS ARENA | Arena Details";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
require_once __DIR__ . '/config/Database.php';

$db = (new Database())->getConnection();
$dynamicLinksMap = [];
$stmtEv = $db->query("SELECT slug, title FROM events");
while($row = $stmtEv->fetch(PDO::FETCH_ASSOC)){
    $eventCountry = str_replace(['GSA ', ' 2026', ' Edition'], '', $row['title']);
    $dynamicLinksMap[strtoupper(trim($eventCountry))] = 'event-details.php?slug=' . $row['slug'];
}

$stmtCarousel = $db->query("SELECT * FROM home_carousel_events WHERE status='published' AND show_in_overseas=1 ORDER BY display_order ASC");
$dynamicCards = [];
while($row = $stmtCarousel->fetch(PDO::FETCH_ASSOC)){
    $loc = !empty($row['country']) ? strtoupper(trim($row['country'])) : strtoupper(trim($row['state']));
    $link = !empty($row['btn_url']) ? $row['btn_url'] : 'home-event-details.php?slug=' . $row['slug'];
    $dynamicLinksMap[$loc] = $link;
    
    $countryStr = $row['country'] ?? '';
    if (empty($countryStr)) $countryStr = $row['state'] ?? '';
    $type = (strtolower(trim($countryStr)) === 'india' || in_array(strtolower(trim($countryStr)), ['maharashtra', 'karnataka', 'tamil nadu', 'delhi', 'goa', 'kerala', 'rajasthan', 'gujarat', 'pune'])) ? 'national' : 'international';
    
    $dynamicCards[] = [
        'id' => (int)$row['id'] + 1000,
        'event_title' => $row['title'] ?? '', 
        'image' => $row['carousel_img'] ?: $row['hero_banner'] ?: '',
        'country' => strtoupper($countryStr),
        'city' => $row['state'] ?? '',
        'date' => $row['event_date'] ?? '',
        'link' => $link,
        'type' => $type,
        'country_or_state' => $countryStr
    ];
}

$stmtGsa = $db->query("SELECT country, slug FROM gsa_carousel_events WHERE status='published'");
while($row = $stmtGsa->fetch(PDO::FETCH_ASSOC)){
    if(!empty($row['country'])) {
        $dynamicLinksMap[strtoupper(trim($row['country']))] = 'event-details.php?slug=' . $row['slug'];
    }
}
?>

<link rel="stylesheet" href="assets/css/GSADetails.css?v=<?php echo time(); ?>">

<?php
$futureYearOct = date("Y");
if (intval(date("n")) > 10) {
    $futureYearOct += 1;
}

$futureYearNov = date("Y");
if (intval(date("n")) > 11) {
    $futureYearNov += 1;
}

$futureYearDec = date("Y");
if (intval(date("n")) > 12) {
    $futureYearDec += 1;
}

$futureYearJan = date("Y");
// January event is next year if current month is not Jan
if (intval(date("n")) > 1) {
    $futureYearJan += 1;
}
?>

<div class="gsa-detail-page">
  <!-- 1. Hero Section -->
  <section class="gsa-hero">
    <div class="gsa-hero-overlay"></div>
    <button class="gsa-back-btn" onclick="window.location.href='index.php'">
      ← Back to Home
    </button>
    <div class="gsa-hero-content">
      <h1 class="gsa-creative-title">Welcome to</h1>
      <h2 class="gsa-creative-subtitle">
        GLOBAL SPORTS<br>
        <span class="gold-highlight">ARENA.</span>
      </h2>
      <p class="gsa-creative-desc">
        Experience world-class sports.<br>Tournaments & entertainment.
      </p>
    </div>
  </section>

<style>
/* CSS for Destinations Section */
.destinations-section {
  padding: 70px 5%;
  background-color: var(--bg-secondary, #12131c);
  border-top: 1px solid rgba(197, 168, 92, 0.15);
  border-bottom: 1px solid rgba(197, 168, 92, 0.15);
}
.destinations-slider-container {
  max-width: 100%; margin: 0 auto; position: relative; padding: 0 45px;
}
.destinations-slider {
  display: flex; gap: 1.5rem; overflow-x: hidden; padding: 1.5rem 0.5rem; scroll-behavior: auto;
}
.destination-card {
  flex: 0 0 260px; min-height: 520px; height: auto; background: rgba(22, 24, 38, 0.7); border: 1px solid rgba(197, 168, 92, 0.25); border-radius: 24px; overflow: hidden; box-shadow: 0 20px 45px rgba(0, 0, 0, 0.55); transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1); display: flex; flex-direction: column; text-decoration: none;
}
.destination-card:hover {
  transform: translateY(-6px); border-color: rgba(197, 168, 92, 0.55); box-shadow: 0 15px 30px rgba(197, 168, 92, 0.15);
}
.destination-image-box {
  flex: 0 0 260px; height: 260px; background-color: #12131c; position: relative; overflow: hidden;
}
.destination-image {
  width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;
}
.destination-card:hover .destination-image { transform: scale(1.08); }
.destination-flag-overlay {
  position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(to top, rgba(0, 0, 0, 0.85) 0%, rgba(0, 0, 0, 0.3) 70%, transparent 100%); padding: 1.2rem 0.5rem 0.5rem; color: #ffffff; font-size: 0.9rem; font-weight: 800; text-align: center; letter-spacing: 1.5px; text-transform: uppercase;
}
.destination-body { flex: 1; height: auto; padding: 1.5rem; background: transparent; display: flex; flex-direction: column; justify-content: space-between; }
.destination-detail-row { display: flex; align-items: center; gap: 0.5rem; font-size: 0.8rem; color: #9aa0b4; margin-bottom: 0.5rem; }
.destination-detail-row:last-child { margin-bottom: 0; }
.destination-icon { font-size: 0.9rem; color: #c5a85c; }
.slider-control-btn {
  position: absolute; top: 50%; transform: translateY(-50%); width: 38px; height: 38px; background: rgba(22, 24, 38, 0.95); border: 1px solid #c5a85c; border-radius: 50%; color: #c5a85c; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 10; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4); transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.slider-control-btn:hover { background: #c5a85c; color: #0b0c10; transform: translateY(-50%) scale(1.1); box-shadow: 0 6px 16px rgba(197, 168, 92, 0.3); }
.slider-control-btn.prev { left: 0; }
.slider-control-btn.next { right: 0; }
body.light-theme .slider-control-btn { background: #ffffff; border-color: #c5a85c; color: #8c6010; box-shadow: 0 4px 12px rgba(197, 168, 92, 0.2); }
body.light-theme .slider-control-btn:hover { background: #c5a85c; color: #ffffff; }
.section-premium-title { text-align: center; margin-bottom: 3.5rem; }
.title-tagline { font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 3px; color: #c5a85c; margin-bottom: 0.5rem; display: block; }
.section-premium-title h2 { font-size: 2.8rem; font-weight: 800; letter-spacing: -0.5px; color:#f5f6fa; }
.title-separator { width: 80px; height: 2px; background: linear-gradient(135deg, #c5a85c 0%, #8c7237 100%); margin: 1rem auto; }

/* Filter Buttons */
.dest-filter-btn {
  background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.15); color: #9aa0b4;
  padding: 8px 16px; border-radius: 20px; font-weight: bold; cursor: pointer; transition: all 0.3s ease;
}
.dest-filter-btn:hover { background: rgba(197, 168, 92, 0.1); border-color: rgba(197, 168, 92, 0.5); color: #c5a85c; }
.dest-filter-btn.active { background: rgba(197, 168, 92, 0.2); border: 1px solid #c5a85c; color: #c5a85c; }

body.light-theme .dest-filter-btn { background: rgba(0, 0, 0, 0.03); border: 1px solid rgba(0, 0, 0, 0.1); color: #666666; }
body.light-theme .dest-filter-btn:hover { background: rgba(140, 96, 16, 0.05); border-color: rgba(140, 96, 16, 0.3); color: #8c6010; }
body.light-theme .dest-filter-btn.active { background: rgba(197, 168, 92, 0.15); border: 1px solid #8c6010; color: #8c6010; }

body.light-theme .destinations-section { background-color: #f5f5dc !important; border-color: rgba(197, 168, 92, 0.3) !important; }
body.light-theme .destination-card { background: #ffffff !important; border-color: rgba(197, 168, 92, 0.3) !important; box-shadow: 0 4px 12px rgba(197, 168, 92, 0.1) !important; }
body.light-theme .destination-card:hover { border-color: #8c6010 !important; box-shadow: 0 15px 30px rgba(197, 168, 92, 0.2) !important; }
body.light-theme .destination-flag-overlay { background: transparent !important; color: #ffffff !important; -webkit-text-fill-color: #ffffff !important; text-shadow: 0 2px 8px rgba(0,0,0,0.8), 0 0 4px rgba(0,0,0,0.6) !important; }
body.light-theme .destination-detail-row { color: #1a1a1a !important; font-weight: 500 !important; }
body.light-theme .destination-icon { color: #8c6010 !important; }
</style>

  <!-- NEXUS ECOSYSTEM CARDS -->
  <div style="background: var(--bg-secondary, #12131c); padding-top: 40px;">
    <?php 
       $hideGSACard = true;
       require_once __DIR__ . '/includes/nexus-cards.php'; 
    ?>
  </div>

  <!-- GLOBAL CHAPTERS (CAROUSEL) -->
  <section class="destinations-section" id="destinations">
    <div class="section-premium-title" style="display: flex; flex-direction: column; align-items: center;">
      <span class="title-tagline">Global Chapters</span>
      <h2 id="destinations-title">Overseas Events</h2>
      <div style="display: flex; gap: 10px; margin-top: 15px;">
        <button id="btnHomeIntl" class="dest-filter-btn active" onclick="setHomeDestFilter('international')">International</button>
        <button id="btnHomeNatl" class="dest-filter-btn" onclick="setHomeDestFilter('national')">Indian States</button>
      </div>
      <div class="title-separator" style="margin-top: 20px;"></div>
    </div>

    <div class="destinations-slider-container">
      <button class="slider-control-btn prev" onclick="scrollDestinations('left')">
        <span class="chevron-icon"><i class="fas fa-chevron-left"></i></span>
      </button>
      
      <div 
        class="destinations-slider" 
        id="destinations-slider"
      >
        <!-- Slides will repeat for infinite scroll effect in JavaScript -->
      </div>

      <button class="slider-control-btn next" onclick="scrollDestinations('right')">
        <span class="chevron-icon"><i class="fas fa-chevron-right"></i></span>
      </button>
    </div>
  </section>

  <!-- 2. About Arena -->
  <section class="gsa-about-section">
    <div class="gsa-container">
      <div class="about-grid">
        <div class="about-text-box">
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
        <div class="about-stats-box">
          <div class="stat-card">
            <h3>50,000+</h3>
            <p>Capacity Crowd</p>
          </div>
          <div class="stat-card">
            <h3>15+</h3>
            <p>Professional Sports</p>
          </div>
          <div class="stat-card">
            <h3>100%</h3>
            <p>FIFA & FIBA Standard</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- 3. Features Section -->
  <section class="gsa-features-section">
    <div class="gsa-container">
      <div class="section-header">
        <span>UNMATCHED FACILITIES</span>
        <h2>Arena Features</h2>
      </div>
      <div class="features-grid">
        <div class="feature-card">
          <div class="feature-icon-circle">🏀</div>
          <h3>Basketball Arena</h3>
          <p>FIBA-spec indoor court with professional flooring and seating.</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon-circle">⚽</div>
          <h3>Football Turf</h3>
          <p>FIFA-certified artificial grass turf for 11v11 and 7v7 matches.</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon-circle">🎵</div>
          <h3>Live Concerts</h3>
          <p>Acclaimed acoustics and space supporting up to 25,000 spectators.</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon-circle">🏨</div>
          <h3>Nearby Hotels</h3>
          <p>Partnerships with luxury hotels, offering GSA guests up to 20% off.</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon-circle">🍔</div>
          <h3>Food Courts</h3>
          <p>Dozens of multi-cuisine outlets, organic juice bars, and cafes.</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon-circle">🎟</div>
          <h3>VIP Tickets</h3>
          <p>Premium lounge seating, private parking, and exclusive catering.</p>
        </div>
      </div>
    </div>
  </section>

  <link rel="stylesheet" href="assets/css/gallery.css?v=3">
  
  <!-- GALLERY HERO -->
  <section class="gallery-hero w-full" style="padding-top: 4rem; padding-bottom: 2rem;">
    <p class="tagline">⚡ CAPTURED MOMENTS</p>
    <h1>Event <span>Gallery</span></h1>
    <p class="hero-text">
      Explore the best memories from our tournaments, sports events,
      award ceremonies, and community moments.
    </p>
  </section>

  <!-- FILTER BUTTONS -->
  <section class="filter-section max-w-7xl mx-auto px-4 py-6 flex flex-wrap gap-3 justify-center">
    <button class="filter-btn active" data-filter="all">All</button>
    <button class="filter-btn" data-filter="cricket">Cricket</button>
    <button class="filter-btn" data-filter="football">Football</button>
    <button class="filter-btn" data-filter="basketball">Basketball</button>
    <button class="filter-btn" data-filter="tennis">Tennis</button>
    <button class="filter-btn" data-filter="badminton">Badminton</button>
    <button class="filter-btn" data-filter="volleyball">Volleyball</button>
    <button class="filter-btn" data-filter="athletics">Athletics</button>
    <button class="filter-btn" data-filter="winners">Winners</button>
  </section>

  <!-- GALLERY GRID -->
  <section class="gallery-section grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 max-w-7xl mx-auto px-4 py-8" id="galleryGrid">
    <p style="text-align: center; color: #c5a85c; grid-column: 1 / -1;">Loading gallery photos...</p>
  </section>

  <script>
    document.addEventListener("DOMContentLoaded", async function() {
      const filterBtns = document.querySelectorAll('.filter-btn');
      const galleryGrid = document.getElementById('galleryGrid');
      let allGalleryItems = [];

      // Fetch dynamic gallery items from DB
      try {
        const res = await fetch("api/index.php/gallery/items");
        const data = await res.json();
        if (data.success) {
          allGalleryItems = data.data;
          renderGallery('all');
        } else {
          galleryGrid.innerHTML = `<p style="text-align: center; color: #ff4d4d; grid-column: 1 / -1;">Failed to load gallery.</p>`;
        }
      } catch (err) {
        console.error(err);
        galleryGrid.innerHTML = `<p style="text-align: center; color: #ff4d4d; grid-column: 1 / -1;">Error loading gallery.</p>`;
      }

      function renderGallery(filterCategory) {
        if (allGalleryItems.length === 0) {
          galleryGrid.innerHTML = `<p style="text-align: center; color: #9aa0b4; grid-column: 1 / -1;">No gallery photos available.</p>`;
          return;
        }

        const filteredItems = filterCategory === 'all' 
          ? allGalleryItems 
          : allGalleryItems.filter(item => item.category === filterCategory);

        if (filteredItems.length === 0) {
          galleryGrid.innerHTML = `<p style="text-align: center; color: #9aa0b4; grid-column: 1 / -1;">No photos found for this category.</p>`;
          return;
        }

        galleryGrid.innerHTML = filteredItems.map(item => `
          <div class="gallery-card" data-category="${item.category}">
            <img src="${item.image_url}" alt="${item.title}">
            <div class="gallery-content">
              <h3>${item.title}</h3>
              <p>${item.subtitle}</p>
            </div>
          </div>
        `).join('');
      }

      filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
          // Remove active class from all buttons
          filterBtns.forEach(b => b.classList.remove('active'));
          // Add active class to clicked button
          this.classList.add('active');

          const filterValue = this.getAttribute('data-filter');
          renderGallery(filterValue);
        });
      });
    });
  </script>

  <!-- 6. Booking Section -->
  <section class="gsa-booking-section">
    <div class="gsa-container">
      <div class="booking-box card-glass">
        <h2>Book Your Spot at GSA</h2>
        <p>Schedule your match, buy concert passes, or sign up for corporate league bookings instantly.</p>
        <div class="booking-btn-row">
          <button class="booking-btn primary" onclick="handleRegister('GSA Custom Arena Venue Booking')">
            📅 Book Event
          </button>
          <button class="booking-btn secondary" onclick="window.location.href='visitor-pass.php'">
            🎟 Visitor Pass
          </button>
          <button class="booking-btn highlight" onclick="window.location.href='sports-categories.php'">
            🏅 Join Tournament
          </button>
        </div>
      </div>
    </div>
  </section>

  <!-- 7. Reviews Section -->
  <section class="gsa-reviews-section">
    <div class="gsa-container">
      <div class="section-header" style="display: flex; align-items: center; justify-content: center; position: relative;">
        <div style="text-align: center;">
          <span>ATHLETE & SPECTATOR FEEDBACK</span>
          <h2 style="margin: 0;">Reviews</h2>
        </div>
        <button class="booking-btn secondary" style="position: absolute; right: 0; padding: 0.5rem 1rem; font-size: 0.9rem;" onclick="openReviewModal()">+ Add Review</button>
      </div>
      <div class="reviews-grid" id="reviews-grid">
        <div class="review-card">
          <div class="review-header">
            <div class="stars-row">
              <span>⭐</span><span>⭐</span><span>⭐</span><span>⭐</span><span>⭐</span>
            </div>
            <span class="review-date">2 days ago</span>
          </div>
          <p class="review-comment">"Amazing experience! The turf is absolutely perfect and food courts are top tier."</p>
          <h4 class="review-author">- Sanket S.</h4>
        </div>

        <div class="review-card">
          <div class="review-header">
            <div class="stars-row">
              <span>⭐</span><span>⭐</span><span>⭐</span><span>⭐</span><span>⭐</span>
            </div>
            <span class="review-date">1 week ago</span>
          </div>
          <p class="review-comment">"Best sports arena in the country. The VIP lounge is highly recommended!"</p>
          <h4 class="review-author">- Mithraa E.</h4>
        </div>

        <div class="review-card">
          <div class="review-header">
            <div class="stars-row">
              <span>⭐</span><span>⭐</span><span>⭐</span><span>⭐</span><span>⭐</span>
            </div>
            <span class="review-date">3 weeks ago</span>
          </div>
          <p class="review-comment">"Watched the football finals here. Energetic crowd, perfect lighting, and acoustics."</p>
          <h4 class="review-author">- Rahul P.</h4>
        </div>
      </div>
    </div>
  </section>

  <!-- 8. Location Section -->
  <section class="gsa-location-section">
    <div class="gsa-container">
      <div class="section-header">
        <span>OUR ADDRESS</span>
        <h2>Location & Venue Map</h2>
      </div>
      <div class="location-box card-glass">
        <div class="location-info">
          <h3>Global Sports Arena HQ</h3>
          <p>📍 Sector 10, Sports Complex Area, Pune, Maharashtra - 411001</p>
          <p>📞 Helpline: +91 98765 43210</p>
          <p>✉️ Venue Inquiries: booking@globalsportsarena.com</p>
        </div>
        <div class="map-embed-placeholder">
          <iframe 
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d121059.03447395568!2d73.79292686884632!3d18.52043029837554!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bc2bf2e67461101%3A0x828f43bf9d089e34!2sPune%2C%20Maharashtra!5e0!3m2!1sen!2sin!4v1717800000000!5m2!1sen!2sin" 
            width="100%" 
            height="320" 
            style="border: 0; border-radius: 16px;" 
            allowfullscreen="" 
            loading="lazy" 
            referrerpolicy="no-referrer-when-downgrade"
            title="GSA Google Maps Location"
          ></iframe>
        </div>
      </div>
    </div>
  </section>

  <!-- 9. Footer CTA -->
  <section class="gsa-footer-cta">
    <div class="cta-overlay"></div>
    <div class="cta-content">
      <h2>Ready to experience the excitement?</h2>
      <p>Book a field, buy event tickets, or register for upcoming championships now.</p>
      <button class="cta-btn-book" onclick="handleRegister('GSA Full Access Arena Booking')">
        BOOK NOW
      </button>
    </div>
  </section>
</div>

<!-- Review Modal -->
<div id="reviewModal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000; justify-content: center; align-items: center;">
  <div class="modal-content card-glass" style="width: 400px; padding: 2rem; position: relative;">
    <span onclick="closeReviewModal()" style="position: absolute; top: 15px; right: 20px; font-size: 1.5rem; cursor: pointer; color: #fff;">&times;</span>
    <h3 style="color: #c5a85c; margin-bottom: 1.5rem;">Add a Review</h3>
    <form id="addReviewForm" onsubmit="submitReview(event)">
      <div style="margin-bottom: 1rem;">
        <label style="display: block; color: #fff; margin-bottom: 0.5rem;">Your Name</label>
        <input type="text" id="reviewAuthor" required style="width: 100%; padding: 0.5rem; border-radius: 4px; border: 1px solid #444; background: #222; color: #fff;">
      </div>
      <div style="margin-bottom: 1rem;">
        <label style="display: block; color: #fff; margin-bottom: 0.5rem;">I am a</label>
        <select id="reviewRole" style="width: 100%; padding: 0.5rem; border-radius: 4px; border: 1px solid #444; background: #222; color: #fff;">
          <option value="User">User / Athlete</option>
          <option value="Merchant">Merchant / Partner</option>
        </select>
      </div>
      <div style="margin-bottom: 1rem;">
        <label style="display: block; color: #fff; margin-bottom: 0.5rem;">Rating (1-5)</label>
        <input type="number" id="reviewRating" min="1" max="5" value="5" required style="width: 100%; padding: 0.5rem; border-radius: 4px; border: 1px solid #444; background: #222; color: #fff;">
      </div>
      <div style="margin-bottom: 1.5rem;">
        <label style="display: block; color: #fff; margin-bottom: 0.5rem;">Comment</label>
        <textarea id="reviewComment" required rows="4" style="width: 100%; padding: 0.5rem; border-radius: 4px; border: 1px solid #444; background: #222; color: #fff;"></textarea>
      </div>
      <button type="submit" class="booking-btn primary" style="width: 100%;">Submit Review</button>
    </form>
  </div>
</div>

<script>
function handleRegister(eventName) {
    sessionStorage.setItem("prefilledEvent", eventName);
    sessionStorage.setItem("prefilledLocation", "Global Sports Arena, Pune HQ");
    window.location.href = "event-registration.php";
}

// Destinations Logic
const defaultDestinations = [
    { id: 1, country: "INDIA", image: "https://images.unsplash.com/photo-1564507592333-c60657eea523?w=500&auto=format&fit=crop&q=60", date: "24-26 July 2026", city: "Pune / Mumbai", region: "India", link: "gsa-pune-2026.php" },
    { id: 2, country: "SINGAPORE", image: "https://images.unsplash.com/photo-1525625293386-3f8f99389edd?w=500&auto=format&fit=crop&q=60", date: "18-20 Sept 2026", city: "Singapore", region: "Singapore", link: "#" },
    { id: 3, country: "SWITZERLAND", image: "https://images.unsplash.com/photo-1506744038136-46273834b3fb?w=500&auto=format&fit=crop&q=60", date: "May - Sep", city: "Zurich", region: "Switzerland", link: "#" },
    { id: 4, country: "UAE", image: "https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=500&auto=format&fit=crop&q=60", date: "23-25 Oct 2026", city: "Dubai / Abu Dhabi", region: "UAE", link: "#" },
    { id: 5, country: "THAILAND", image: "assets/images/Thailand Card.png", date: "18-20 Dec 2026", city: "Phuket / Bangkok", region: "Thailand", link: "#" },
    { id: 6, country: "USA - LAS VEGAS", image: "https://images.unsplash.com/photo-1501183007986-d0d080b147f9?w=500&auto=format&fit=crop&q=60", date: "23-25 July 2026", city: "Las Vegas", region: "USA", link: "#" },
    { id: 7, country: "USA - NEW YORK", image: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTfwahV5yF4az000LrH5UPZhBV23NNv9LTd7penHXR4ew&s=10", date: "23-25 July 2026", city: "New York", region: "USA", link: "#" },
    { id: 8, country: "MALAYSIA", image: "https://images.unsplash.com/photo-1596422846543-75c6fc197f07?w=500&auto=format&fit=crop&q=60", date: "20-22 Nov 2026", city: "Kuala Lumpur", region: "Malaysia", link: "#" },
    { id: 9, country: "INDONESIA", image: "https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=500&auto=format&fit=crop&q=60", date: "22-24 Jan 2026", city: "Bali / Jakarta", region: "Indonesia", link: "#" },
    { id: 10, country: "VIETNAM", image: "https://images.unsplash.com/photo-1528127269322-539801943592?w=500&auto=format&fit=crop&q=60", date: "19-21 Feb 2026", city: "Ho Chi Minh", region: "Vietnam", link: "#" },
    { id: 11, country: "AUSTRALIA", image: "https://images.unsplash.com/photo-1523482580672-f109ba8cb9be?w=500&auto=format&fit=crop&q=60", date: "19-21 March 2026", city: "Sydney", region: "Australia", link: "#" },
    { id: 12, country: "GERMANY", image: "https://images.unsplash.com/photo-1467269204594-9661b134dd2b?w=500&auto=format&fit=crop&q=60", date: "23-25 April 2026", city: "Berlin", region: "Germany", link: "#" },
    { id: 13, country: "UNITED KINGDOM", image: "https://images.unsplash.com/photo-1505761671935-60b3a7427bad?w=500&auto=format&fit=crop&q=60", date: "21-23 May 2026", city: "London", region: "UK", link: "#" },
    { id: 14, country: "CANADA", image: "https://images.unsplash.com/photo-1503614472-8c93d56e92ce?w=500&auto=format&fit=crop&q=60", date: "18-20 June 2026", city: "Toronto", region: "Canada", link: "#" }
];

const defaultNationalDestinations = [
    { id: 108, country: "TAMIL NADU", image: "https://images.unsplash.com/photo-1582510003544-4d00b7f74220?w=500&auto=format&fit=crop&q=60", date: "July - Aug 2026", city: "Coimbatore", region: "Tamil Nadu", type: "national", link: "#" },
    { id: 109, country: "PUNE", image: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQXEfjgnsk5LwgVg_cpZweTw5MIz_QgU8dWPg7dLZi3Og&s=10", date: "Oct 2026", city: "Pune", region: "Pune", type: "national", link: "gsa-pune-2026.php" },
    { id: 101, country: "MAHARASHTRA", image: "https://images.unsplash.com/photo-1570168007204-dfb528c6958f?w=500&auto=format&fit=crop&q=60", date: "10-12 Aug 2026", city: "Mumbai / Pune", region: "India", type: "national", link: "gsa-pune-2026.php" },
    { id: 102, country: "KARNATAKA", image: "https://images.unsplash.com/photo-1596176530529-78163a4f7af2?w=500&auto=format&fit=crop&q=60", date: "15-17 Sept 2026", city: "Bangalore", region: "India", type: "national", link: "#" },
    { id: 103, country: "DELHI", image: "https://images.unsplash.com/photo-1587474260584-136574528ed5?w=500&auto=format&fit=crop&q=60", date: "05-07 Oct 2026", city: "New Delhi", region: "India", type: "national", link: "#" },
    { id: 104, country: "GOA", image: "https://images.unsplash.com/photo-1512343879784-a960bf40e7f2?w=500&auto=format&fit=crop&q=60", date: "20-22 Nov 2026", city: "Panaji", region: "India", type: "national", link: "#" },
    { id: 105, country: "KERALA", image: "https://images.unsplash.com/photo-1602216056096-3b40cc0c9944?w=500&auto=format&fit=crop&q=60", date: "12-14 Dec 2026", city: "Kochi", region: "India", type: "national", link: "#" },
    { id: 106, country: "RAJASTHAN", image: "https://images.unsplash.com/photo-1477587458883-47145ed94245?w=500&auto=format&fit=crop&q=60", date: "15-17 Jan 2026", city: "Jaipur", region: "India", type: "national", link: "#" },
    { id: 107, country: "GUJARAT", image: "https://images.unsplash.com/photo-1605130284535-11dd9eedc58a?w=500&auto=format&fit=crop&q=60", date: "10-12 Feb 2026", city: "Ahmedabad", region: "India", type: "national", link: "#" }
];

let homeDestFilter = 'international';

function setHomeDestFilter(type) {
    homeDestFilter = type;
    document.getElementById("btnHomeIntl").classList.toggle('active', type === 'international');
    document.getElementById("btnHomeNatl").classList.toggle('active', type === 'national');
    
    document.getElementById("destinations-title").textContent = type === 'international' ? 'Overseas Events' : 'Indian States Events';
    
    renderDestinations();
}

let isDestPaused = false;

function renderDestinations() {
    const slider = document.getElementById("destinations-slider");
    if (!slider) return;

    let customDest = window.apiDestinations || [];

    const dynamicCardsFromDB = <?php echo json_encode($dynamicCards); ?>;
    dynamicCardsFromDB.forEach(card => {
        if (!card.country_or_state) return;
        const targetCountry = card.country_or_state.toUpperCase();
        
        if (card.type === 'international') {
            const match = defaultDestinations.find(dest => dest.country === targetCountry || (dest.region && dest.region.toUpperCase() === targetCountry));
            if (match) {
                if (card.link) match.link = card.link;
                if (card.image) match.image = card.image;
                if (card.event_title) match.city = card.event_title;
            } else {
                defaultDestinations.push({
                    id: 'dyn_intl_' + card.id,
                    country: targetCountry,
                    city: card.city || targetCountry,
                    region: targetCountry,
                    image: card.image || '',
                    link: card.link || '#',
                    date: card.date || '',
                    type: 'international'
                });
            }
        } else {
            const match = defaultNationalDestinations.find(dest => dest.country === targetCountry || (dest.region && dest.region.toUpperCase() === targetCountry));
            if (match) {
                if (card.link) match.link = card.link;
                if (card.image) match.image = card.image;
                if (card.event_title) match.city = card.event_title;
            } else {
                defaultNationalDestinations.push({
                    id: 'dyn_nat_' + card.id,
                    country: targetCountry,
                    city: card.city || targetCountry,
                    region: targetCountry,
                    image: card.image || '',
                    link: card.link || '#',
                    date: card.date || '',
                    type: 'national'
                });
            }
        }
    });

    const allDefaults = [...defaultDestinations, ...defaultNationalDestinations];
    let mergedAll = allDefaults.map(d => {
        const customOverride = customDest.find(c => c.id === d.id);
        return customOverride ? customOverride : d;
    });
    
    const purelyNew = customDest.filter(c => !allDefaults.some(d => d.id === c.id));
    
    let finalDest = [...mergedAll, ...purelyNew].filter(d => !d.deleted);
    
    finalDest = finalDest.filter(d => {
        const type = d.type || (d.id > 100 ? 'national' : 'international');
        return type === homeDestFilter;
    });
    
    const displayList = [...finalDest, ...finalDest, ...finalDest, ...finalDest]; // duplicate for infinite scroll effect
    
    const dynamicLinks = <?php echo json_encode($dynamicLinksMap); ?>;
    slider.innerHTML = displayList.map((dest, idx) => {
        let locKey = (dest.country || '').toUpperCase();
        if(!locKey && dest.region) locKey = dest.region.toUpperCase();
        
        let defaultLink = dynamicLinks[locKey] ? dynamicLinks[locKey] : `destination-detail.php?id=${dest.id}`;
        let targetLink = dest.link && dest.link !== "#" ? dest.link : defaultLink;
        let targetAttr = '';
        if (locKey === "PUNE") {
            targetLink = "gsa-pune-2026.php";
        }
        return `
          <a href="${targetLink}" ${targetAttr} class="destination-card" onclick="${targetLink === '#' ? 'event.preventDefault()' : ''}">
            <div class="destination-image-box">
              <img src="${dest.image}" alt="${dest.country}" class="destination-image" />
              <div class="destination-flag-overlay">${dest.country}</div>
            </div>
            <div class="destination-body" style="display: flex; flex-direction: column; align-items: center; text-align: center; gap: 1rem; flex: 1; justify-content: space-between;">
              <p style="color: #9aa0b4; font-size: 0.85rem; line-height: 1.4; margin: 0;">Where Sports, Tourism & Entertainment come together on a global stage.</p>
              <button class="nexus-btn" style="pointer-events: none; padding: 0.6rem 1.2rem; font-size: 0.75rem;">KNOW MORE</button>
              <div style="width: 100%; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 1rem; display: flex; flex-direction: column; gap: 0.5rem; text-align: left;">
                <div class="destination-detail-row">
                  <span class="destination-icon">📅</span> ${dest.date}
                </div>
                <div class="destination-detail-row">
                  <span class="destination-icon">📍</span> ${dest.city}
                </div>
                <div class="destination-detail-row">
                  <span class="destination-icon">📍</span> ${dest.region}
                </div>
              </div>
            </div>
          </a>
        `;
    }).join('');
}

function scrollDestinations(direction) {
    const slider = document.getElementById("destinations-slider");
    if (slider) {
        slider.style.scrollBehavior = "smooth";
        const scrollAmount = 300;
        slider.scrollLeft += direction === "left" ? -scrollAmount : scrollAmount;
        setTimeout(() => {
            if (slider) slider.style.scrollBehavior = "auto";
        }, 500);
    }
}

// Load destinations from API on page load
window.apiDestinations = [];

// Review Logic
function openReviewModal() {
    document.getElementById('reviewModal').style.display = 'flex';
}

function closeReviewModal() {
    document.getElementById('reviewModal').style.display = 'none';
    document.getElementById('addReviewForm').reset();
}

function fetchReviews() {
    fetch('api/index.php/reviews')
        .then(res => res.json())
        .then(data => {
            if (Array.isArray(data) && data.length > 0) {
                renderReviews(data);
            }
        })
        .catch(err => console.error("Error fetching reviews:", err));
}

function renderReviews(reviews) {
    const grid = document.getElementById('reviews-grid');
    if (!grid) return;
    grid.innerHTML = reviews.map(r => {
        let stars = '';
        for (let i = 0; i < 5; i++) {
            stars += i < r.rating ? '<span>⭐</span>' : '<span style="opacity: 0.3">⭐</span>';
        }
        const date = new Date(r.created_at).toLocaleDateString();
        return `
            <div class="review-card">
              <div class="review-header">
                <div class="stars-row">${stars}</div>
                <span class="review-date">${date}</span>
              </div>
              <p class="review-comment">"${r.comment}"</p>
              <h4 class="review-author">- ${r.author} <small style="color: #c5a85c; display: block; font-size: 0.8em; margin-top: 4px;">(${r.role})</small></h4>
            </div>
        `;
    }).join('');
}

function submitReview(e) {
    e.preventDefault();
    const data = {
        author: document.getElementById('reviewAuthor').value,
        role: document.getElementById('reviewRole').value,
        rating: parseInt(document.getElementById('reviewRating').value),
        comment: document.getElementById('reviewComment').value
    };

    fetch('api/index.php/reviews', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(result => {
        if (result.success) {
            closeReviewModal();
            fetchReviews();
        } else {
            alert("Error: " + result.message);
        }
    })
    .catch(err => {
        alert("An error occurred. Please try again.");
        console.error(err);
    });
}

document.addEventListener("DOMContentLoaded", function() {
    fetchReviews();

    fetch('api/index.php/destinations')
        .then(res => res.json())
        .then(data => {
            if (Array.isArray(data)) {
                window.apiDestinations = data;
            }
            renderDestinations();
        })
        .catch(err => {
            console.error("Failed to load custom destinations", err);
            renderDestinations();
        });
    const destSlider = document.getElementById("destinations-slider");
    if (destSlider) {
        destSlider.addEventListener("mouseenter", () => { isDestPaused = true; });
        destSlider.addEventListener("mouseleave", () => { isDestPaused = false; });
        
        setInterval(() => {
            if (!isDestPaused) {
                const segmentWidth = destSlider.scrollWidth / 4;
                if (segmentWidth > 0) {
                    if (destSlider.scrollLeft >= segmentWidth - 1) {
                        destSlider.scrollLeft -= segmentWidth;
                    } else {
                        destSlider.scrollLeft += 1;
                    }
                }
            }
        }, 20);
    }
});
</script>



<?php require_once __DIR__ . '/includes/footer.php'; ?>
