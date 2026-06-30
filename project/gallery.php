<?php
$pageTitle = "Event Gallery";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<link rel="stylesheet" href="assets/css/gallery.css?v=3">

  <!-- GALLERY HERO -->
  <section class="gallery-hero w-full">
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



<?php require_once __DIR__ . '/includes/footer.php'; ?>
