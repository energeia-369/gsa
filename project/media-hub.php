<?php
$pageTitle = "Media Hub & Event Highlights";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<link rel="stylesheet" href="assets/css/media-hub.css?v=3">

<div class="media-page">
  <!-- HERO SECTION -->
  <section class="media-hero">
    <p class="tagline">🎬 MEDIA HUB</p>
    <h1>Event <span>Highlights</span></h1>
    <p class="hero-text">
      Watch the latest match highlights, live sports coverage, behind-the-scenes clips, and exclusive interviews from our top tournaments.
    </p>
  </section>

  <!-- TABS -->
  <section class="media-tabs">
    <button class="tab-btn active" data-filter="all">All</button>
    <button class="tab-btn" data-filter="live">Live Stream</button>
    <button class="tab-btn" data-filter="highlights">Match Highlights</button>
    <button class="tab-btn" data-filter="reels">Moments & Reels</button>
  </section>

  <!-- VIDEO GRID -->
  <section class="media-grid" id="mediaGridContainer">
    <div style="grid-column: 1 / -1; text-align: center; color: #9aa0b4; padding: 40px;">
      Loading media...
    </div>

  </section>
</div>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    const tabBtns = document.querySelectorAll('.tab-btn');
    
    // Fetch and render media
    fetchMedia();

    function fetchMedia() {
      fetch('api/index.php/media/items')
        .then(res => res.json())
        .then(data => {
          const container = document.getElementById('mediaGridContainer');
          if (data.success && data.data.length > 0) {
            container.innerHTML = data.data.map(item => {
              
              let badgeHtml = '';
              if (item.status === 'Live') {
                badgeHtml = '<span class="live-badge">LIVE</span>';
              } else if (item.status === 'Upcoming') {
                badgeHtml = '<span class="live-badge">UPCOMING</span>';
              }

              let categoryText = item.category === 'live' ? 'Live Stream' :
                                 item.category === 'highlights' ? 'Match Highlights' :
                                 'Moments & Reels';

              let thumbUrl = (item.thumbnail && item.thumbnail !== 'null' && item.thumbnail !== '') ? item.thumbnail : 'https://images.unsplash.com/photo-1540747913346-19e32dc3e97e?w=800&h=450&fit=crop';

              return `
              <div class="video-card" data-category="${item.category}" style="cursor: pointer;" onclick="window.open('${item.video_link}', '_blank')">
                <div class="video-thumbnail">
                  ${badgeHtml}
                  <img src="${thumbUrl}" alt="${item.title}" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1540747913346-19e32dc3e97e?w=800&h=450&fit=crop';">
                  <div class="play-button">▶</div>
                  ${item.duration ? `<span class="video-duration">${item.duration}</span>` : ''}
                </div>
                <div class="video-info">
                  <span class="video-category">${categoryText}</span>
                  <h3 class="video-title">${item.title}</h3>
                  <div class="video-meta">
                    ${item.views ? `<span>${item.views}</span>` : ''}
                    ${item.date_time ? `<span>${item.date_time}</span>` : ''}
                  </div>
                </div>
              </div>
              `;
            }).join('');
            
            // Re-apply filters if necessary
            applyFilters();
          } else {
            container.innerHTML = '<div style="grid-column: 1 / -1; text-align: center; color: #9aa0b4; padding: 40px;">No media available currently.</div>';
          }
        })
        .catch(err => {
          console.error('Error fetching media:', err);
          document.getElementById('mediaGridContainer').innerHTML = '<div style="grid-column: 1 / -1; text-align: center; color: #ef4444; padding: 40px;">Failed to load media.</div>';
        });
    }

    function applyFilters() {
      const activeTab = document.querySelector('.tab-btn.active');
      const filterValue = activeTab ? activeTab.getAttribute('data-filter') : 'all';
      const videoCards = document.querySelectorAll('.video-card');

      videoCards.forEach(card => {
        if (filterValue === 'all') {
          card.style.display = 'block';
        } else {
          const category = card.getAttribute('data-category');
          if (category === filterValue) {
            card.style.display = 'block';
          } else {
            card.style.display = 'none';
          }
        }
      });
    }

    tabBtns.forEach(btn => {
      btn.addEventListener('click', function() {
        tabBtns.forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        applyFilters();
      });
    });
  });
</script>



<?php require_once __DIR__ . '/includes/footer.php'; ?>
