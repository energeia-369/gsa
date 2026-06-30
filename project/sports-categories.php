<?php
$pageTitle = "GLOBAL SPORTS ARENA | Sports Categories";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<link rel="stylesheet" href="assets/css/SportsCategories.css?v=2">

<div class="sports-page">
  <div class="categories-hero">
    <div class="hero-badge">🏆 Explore Our Collection</div>
    <h1>Sports Categories</h1>
    <p>
      Discover the perfect gear and events for your favorite sports. From professional equipment to casual training - we have it all.
    </p>

    <div class="hero-search flex flex-col sm:flex-row gap-4 justify-center items-center">
      <input
        type="text"
        id="sportSearchInput"
        placeholder="Search for a sport..."
        class="search-input w-full sm:w-auto"
        onkeyup="handleSportSearch(event)"
      />
      <button class="search-btn w-full sm:w-auto" onclick="triggerSearch()">🔍 Search</button>
    </div>
  </div>

  <div class="quick-stats grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-8 mx-auto max-w-7xl px-4 my-8">
    <div class="stat-card p-4 flex flex-col items-center">
      <span class="stat-number text-2xl md:text-3xl font-bold" id="totalCategoriesStat">0+</span>
      <span class="stat-name text-sm md:text-base text-gray-400">Sports Categories</span>
    </div>
    <div class="stat-card p-4 flex flex-col items-center">
      <span class="stat-number text-2xl md:text-3xl font-bold">102+</span>
      <span class="stat-name text-sm md:text-base text-gray-400">Live Events</span>
    </div>
    <div class="stat-card p-4 flex flex-col items-center">
      <span class="stat-number text-2xl md:text-3xl font-bold">437+</span>
      <span class="stat-name text-sm md:text-base text-gray-400">Products</span>
    </div>
    <div class="stat-card p-4 flex flex-col items-center">
      <span class="stat-number text-2xl md:text-3xl font-bold">50K+</span>
      <span class="stat-name text-sm md:text-base text-gray-400">Active Players</span>
    </div>
  </div>

  <div class="categories-section max-w-7xl mx-auto px-4 py-8">
    <div class="section-header mb-8 text-center">
      <span class="section-tag inline-block bg-[rgba(197,168,92,0.15)] text-[#c5a85c] px-4 py-1 rounded-full text-sm font-bold uppercase tracking-wider mb-3">Shop by Sport</span>
      <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-2">All Sports Categories</h2>
      <p class="text-gray-400">Find everything you need for your favorite sport</p>
    </div>

    <div class="category-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6" id="categoryGrid">
      <p style="color: #9aa0b4; text-align: center; grid-column: 1 / -1; padding: 50px;">Loading categories...</p>
    </div>
  </div>

  <div class="featured-products max-w-7xl mx-auto px-4 py-8">
    <div class="section-header mb-8 text-center">
      <span class="section-tag inline-block bg-[rgba(197,168,92,0.15)] text-[#c5a85c] px-4 py-1 rounded-full text-sm font-bold uppercase tracking-wider mb-3">Top Picks</span>
      <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-2">Best Selling Products</h2>
      <p class="text-gray-400">Most loved items across all sports</p>
    </div>

    <div class="products-preview grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <div class="product-item bg-[#12131c] p-6 rounded-2xl border border-[rgba(197,168,92,0.2)] text-center transition hover:-translate-y-2 hover:border-[#c5a85c]">
        <div class="product-icon text-4xl mb-4">🏸</div>
        <h4 class="font-bold text-lg mb-2">Pro Badminton Racket</h4>
        <p class="text-[#c5a85c] font-bold mb-4">₹2,499</p>
        <button class="w-full bg-[linear-gradient(135deg,#c5a85c_0%,#8c7237_100%)] text-[#0b0c10] font-bold py-2 rounded-xl" onclick="window.location.href='products.php'">Buy Now</button>
      </div>
      <div class="product-item bg-[#12131c] p-6 rounded-2xl border border-[rgba(197,168,92,0.2)] text-center transition hover:-translate-y-2 hover:border-[#c5a85c]">
        <div class="product-icon text-4xl mb-4">⚽</div>
        <h4 class="font-bold text-lg mb-2">Football Studs</h4>
        <p class="text-[#c5a85c] font-bold mb-4">₹1,299</p>
        <button class="w-full bg-[linear-gradient(135deg,#c5a85c_0%,#8c7237_100%)] text-[#0b0c10] font-bold py-2 rounded-xl" onclick="window.location.href='products.php'">Buy Now</button>
      </div>
      <div class="product-item bg-[#12131c] p-6 rounded-2xl border border-[rgba(197,168,92,0.2)] text-center transition hover:-translate-y-2 hover:border-[#c5a85c]">
        <div class="product-icon text-4xl mb-4">👟</div>
        <h4 class="font-bold text-lg mb-2">Running Shoes</h4>
        <p class="text-[#c5a85c] font-bold mb-4">₹3,999</p>
        <button class="w-full bg-[linear-gradient(135deg,#c5a85c_0%,#8c7237_100%)] text-[#0b0c10] font-bold py-2 rounded-xl" onclick="window.location.href='products.php'">Buy Now</button>
      </div>
      <div class="product-item bg-[#12131c] p-6 rounded-2xl border border-[rgba(197,168,92,0.2)] text-center transition hover:-translate-y-2 hover:border-[#c5a85c]">
        <div class="product-icon text-4xl mb-4">💪</div>
        <h4 class="font-bold text-lg mb-2">Gym Gloves</h4>
        <p class="text-[#c5a85c] font-bold mb-4">₹599</p>
        <button class="w-full bg-[linear-gradient(135deg,#c5a85c_0%,#8c7237_100%)] text-[#0b0c10] font-bold py-2 rounded-xl" onclick="window.location.href='products.php'">Buy Now</button>
      </div>
    </div>
  </div>
</div>

<script>
function exploreSport(sportName) {
    window.location.href = `event-registration.php?sport=${encodeURIComponent(sportName)}`;
}

function shopSport(sportName) {
    window.location.href = `products.php?sport=${encodeURIComponent(sportName)}`;
}

function triggerSearch() {
    const query = document.getElementById("sportSearchInput").value.trim();
    if (query) {
        window.location.href = `products.php?search=${encodeURIComponent(query)}`;
    }
}

function handleSportSearch(event) {
    if (event.key === "Enter") {
        triggerSearch();
    }
}

document.addEventListener('DOMContentLoaded', async () => {
    try {
        const res = await fetch('api/index.php/categories/all');
        const data = await res.json();
        
        if (data.success && data.data) {
            document.getElementById('totalCategoriesStat').textContent = data.data.length + '+';
            
            const grid = document.getElementById('categoryGrid');
            if (data.data.length > 0) {
                grid.innerHTML = data.data.map(cat => {
                    const isFeatured = cat.is_featured == 1;
                    return `
                      <div class="category-card ${isFeatured ? 'featured' : ''}">
                        <div class="card-badge">${isFeatured ? '🔥 Trending' : ''}</div>
                        <div class="category-icon">${cat.icon}</div>
                        <h3>${cat.name}</h3>
                        <p class="category-desc">${cat.description || 'Explore our collection'}</p>
                        <div class="category-stats">
                          <span class="stat"><span class="stat-icon">📅</span> ${cat.events_count || 0} Events</span>
                          <span class="stat"><span class="stat-icon">🛍️</span> ${cat.products_count || 0} Products</span>
                        </div>
                        <div class="card-actions">
                          <button class="explore-category" onclick="exploreSport('${cat.name}')">Explore →</button>
                          <button class="shop-category" onclick="shopSport('${cat.name}')">Shop Now</button>
                        </div>
                      </div>
                    `;
                }).join('');
            } else {
                grid.innerHTML = '<p style="color: #9aa0b4; text-align: center; grid-column: 1 / -1; padding: 50px;">No categories available.</p>';
            }
        }
    } catch (e) {
        console.error("Failed to load categories:", e);
        document.getElementById('categoryGrid').innerHTML = '<p style="color: #ef4444; text-align: center; grid-column: 1 / -1; padding: 50px;">Error loading categories.</p>';
    }
});
</script>



<?php require_once __DIR__ . '/includes/footer.php'; ?>
