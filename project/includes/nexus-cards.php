<link rel="stylesheet" href="assets/css/nexus-cards.css?v=3">

<!-- NEXUS ECOSYSTEM CARDS COMPONENT -->
<section class="nexus-section">
  <div class="nexus-container">
    
    <!-- Card 1: Nexus Elite -->
    <a href="event-info.php?event=elite" style="text-decoration: none; color: inherit; display: contents;">
      <div class="nexus-card card-blue">
        <div class="nexus-card-header">
          <div class="nexus-logo-circle">N</div>
          <div class="nexus-header-text">
            <h3>NEXUS ELITE</h3>
            <span>BUSINESS SUMMIT</span>
          </div>
        </div>
        
        <div class="nexus-card-image">
          <img src="assets/images/nexas card.png" alt="Business Summit" class="nexus-card-dark-img">
          <img src="assets/images/nexas card light.png" alt="Business Summit" class="nexus-card-light-img">
        </div>
        
        <div class="nexus-card-body">
          <p>A premium business summit connecting leaders, startups, investors & innovators.</p>
          <button class="nexus-btn" style="pointer-events: none;">KNOW MORE</button>
        </div>
        
        <div class="nexus-card-footer">
          <div class="footer-icon">🏢</div>
          <div class="footer-text">
            <strong>Nexus Elite</strong>
            <span>Business Summit</span>
          </div>
        </div>
      </div>
    </a>

    <!-- Card 2: Maytriya Connect -->
    <a href="event-info.php?event=maytriya" style="text-decoration: none; color: inherit; display: contents;">
      <div class="nexus-card card-brown">
        <div class="nexus-card-header">
          <div class="nexus-logo-circle">M</div>
          <div class="nexus-header-text">
            <h3>MAYTRIYA CONNECT</h3>
            <span>LEADERSHIP & FRANCHISE MEET</span>
          </div>
        </div>
        
        <div class="nexus-card-image">
          <img src="assets/images/maytriya card.png" alt="Leadership Summit" class="nexus-card-dark-img">
          <img src="assets/images/maytriya light card.png" alt="Leadership Summit" class="nexus-card-light-img">
        </div>
        
        <div class="nexus-card-body">
          <p>Curated meet for investors, franchise brands, entrepreneurs & business leaders.</p>
          <button class="nexus-btn" style="pointer-events: none;">KNOW MORE</button>
        </div>
        
        <div class="nexus-card-footer">
          <div class="footer-icon">🤝</div>
          <div class="footer-text">
            <strong>Maytriya Connect</strong>
            <span>Leadership & Franchise Meet</span>
          </div>
        </div>
      </div>
    </a>

    <!-- Card 3: GSA -->
    <?php if (!isset($hideGSACard) || !$hideGSACard): ?>
    <a href="event-info.php?event=gsa" style="text-decoration: none; color: inherit; display: contents;">
      <div class="nexus-card card-purple">
        <div class="nexus-card-header">
          <div class="nexus-logo-circle">GSA</div>
          <div class="nexus-header-text">
            <h3>GSA</h3>
            <span>GLOBAL SPORTS ARENA</span>
          </div>
        </div>
        
        <div class="nexus-card-image">
          <img src="assets/images/gsa card.png" alt="Global Sports Arena" class="nexus-card-dark-img">
          <img src="assets/images/gsa card light.png" alt="Global Sports Arena" class="nexus-card-light-img">
        </div>
        
        <div class="nexus-card-body">
          <p>Where Sports, Tourism & Entertainment come together on a global stage.</p>
          <button class="nexus-btn" style="pointer-events: none;">KNOW MORE</button>
        </div>
        
        <div class="nexus-card-footer">
          <div class="footer-icon">🏟️</div>
          <div class="footer-text">
            <strong>GSA</strong>
            <span>Global Sports Arena</span>
          </div>
        </div>
      </div>
    </a>
    <?php endif; ?>

  </div>
</section>
