<?php
$pageTitle = "GLOBAL SPORTS ARENA | Sports Store";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
require_once __DIR__ . '/models/Product.php';

$productModel = new Product();
$dbProducts = [];
try {
    $dbProducts = $productModel->findAll();
} catch (Exception $e) {
    error_log("Failed to load products: " . $e->getMessage());
}

// Fallback categories list
$categories = [
    "All",
    "Footwear",
    "Apparel",
    "Equipment",
    "Accessories",
    "Fitness",
    "Protective Gear",
];

// Fallback products matching the exact React template if DB is empty
$defaultProducts = [
    ["id" => 1, "name" => "Sports Shoes", "price" => 1999, "originalPrice" => 2999, "category" => "Footwear", "image" => "https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&h=400&fit=crop", "rating" => 4.5, "inStock" => true],
    ["id" => 2, "name" => "Running Shoes", "price" => 2499, "originalPrice" => 3999, "category" => "Footwear", "image" => "https://images.unsplash.com/photo-1608231387042-66d1773070a5?w=400&h=400&fit=crop", "rating" => 4.7, "inStock" => true],
    ["id" => 3, "name" => "Football Cleats", "price" => 2999, "originalPrice" => 4999, "category" => "Footwear", "image" => "https://images.unsplash.com/photo-1579952363873-27f3bade9f55?w=400&h=400&fit=crop", "rating" => 4.6, "inStock" => true],
    ["id" => 4, "name" => "Basketball Shoes", "price" => 3499, "originalPrice" => 5499, "category" => "Footwear", "image" => "https://images.unsplash.com/photo-1600185365483-26d7a4cc7519?w=400&h=400&fit=crop", "rating" => 4.8, "inStock" => true],
    ["id" => 7, "name" => "Football Jersey", "price" => 799, "originalPrice" => 1299, "category" => "Apparel", "image" => "https://images.unsplash.com/photo-1517466787929-bc90951d0974?w=400&h=400&fit=crop", "rating" => 4.3, "inStock" => true],
    ["id" => 8, "name" => "Cricket Jersey", "price" => 899, "originalPrice" => 1499, "category" => "Apparel", "image" => "https://images.unsplash.com/photo-1531415074968-036ba1b575da?w=400&h=400&fit=crop", "rating" => 4.5, "inStock" => true],
    ["id" => 15, "name" => "Badminton Racket", "price" => 1499, "originalPrice" => 2499, "category" => "Equipment", "image" => "https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?w=400&h=400&fit=crop", "rating" => 4.7, "inStock" => true],
    ["id" => 16, "name" => "Football", "price" => 999, "originalPrice" => 1599, "category" => "Equipment", "image" => "https://images.unsplash.com/photo-1614632537190-23e4146777db?w=400&h=400&fit=crop", "rating" => 4.4, "inStock" => true],
    ["id" => 27, "name" => "Water Bottle", "price" => 299, "originalPrice" => 499, "category" => "Accessories", "image" => "https://images.unsplash.com/photo-1602143407151-7111542de6e8?w=400&h=400&fit=crop", "rating" => 4.2, "inStock" => true],
    ["id" => 28, "name" => "Gym Gloves", "price" => 499, "originalPrice" => 899, "category" => "Accessories", "image" => "https://images.unsplash.com/photo-1581009146145-b5ef050c2e1e?w=400&h=400&fit=crop", "rating" => 4.6, "inStock" => true],
    ["id" => 29, "name" => "Tennis Racket", "price" => 2999, "originalPrice" => 3999, "category" => "Equipment", "image" => "https://images.unsplash.com/photo-1622279457486-640c4cb71c4c?w=400&h=400&fit=crop", "rating" => 4.8, "inStock" => true],
    ["id" => 30, "name" => "Yoga Mat", "price" => 899, "originalPrice" => 1299, "category" => "Fitness", "image" => "https://images.unsplash.com/photo-1601925260368-ae2f83cf8b7f?w=400&h=400&fit=crop", "rating" => 4.5, "inStock" => true],
    ["id" => 31, "name" => "Boxing Gloves", "price" => 1599, "originalPrice" => 2299, "category" => "Protective Gear", "image" => "https://images.unsplash.com/photo-1549719386-74dfcbf7dbed?w=400&h=400&fit=crop", "rating" => 4.7, "inStock" => true],
    ["id" => 32, "name" => "Track Pants", "price" => 699, "originalPrice" => 1099, "category" => "Apparel", "image" => "https://images.unsplash.com/photo-1556821840-3a63f95609a7?w=400&h=400&fit=crop", "rating" => 4.4, "inStock" => true],
    ["id" => 33, "name" => "Sports Socks (Pack of 3)", "price" => 249, "originalPrice" => 399, "category" => "Apparel", "image" => "https://images.unsplash.com/photo-1582966772680-860e372bb558?w=400&h=400&fit=crop", "rating" => 4.3, "inStock" => true],
    ["id" => 34, "name" => "Jump Rope", "price" => 199, "originalPrice" => 299, "category" => "Fitness", "image" => "https://images.unsplash.com/photo-1579566270505-8a2b535d48af?w=400&h=400&fit=crop", "rating" => 4.6, "inStock" => true]
];

// Map DB products to standard keys or merge
$productList = [];
if (!empty($dbProducts)) {
    foreach ($dbProducts as $dbProd) {
        $categoryName = $dbProd['category'];
        // Map category to a standard one if it contains substrings
        if (stripos($categoryName, 'shoe') !== false || stripos($categoryName, 'footwear') !== false) {
            $catMapped = "Footwear";
        } elseif (stripos($categoryName, 'jersey') !== false || stripos($categoryName, 'apparel') !== false || stripos($categoryName, 'shirt') !== false) {
            $catMapped = "Apparel";
        } elseif (stripos($categoryName, 'racket') !== false || stripos($categoryName, 'ball') !== false || stripos($categoryName, 'equipment') !== false) {
            $catMapped = "Equipment";
        } elseif (stripos($categoryName, 'accessories') !== false) {
            $catMapped = "Accessories";
        } else {
            $catMapped = "Equipment";
        }

        // Generate a nice image url if it is just a local name
        $img = $dbProd['image_url'];
        if (empty($img) || (strpos($img, 'http') !== 0 && strpos($img, 'uploads/') !== 0 && strpos($img, 'data:image') !== 0)) {
            // Use unsplash fallbacks for seeding
            if (stripos($dbProd['name'], 'shoes') !== false) {
                $img = "https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&h=400&fit=crop";
            } elseif (stripos($dbProd['name'], 'jersey') !== false) {
                $img = "https://images.unsplash.com/photo-1517466787929-bc90951d0974?w=400&h=400&fit=crop";
            } elseif (stripos($dbProd['name'], 'racket') !== false) {
                $img = "https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?w=400&h=400&fit=crop";
            } elseif (stripos($dbProd['name'], 'ball') !== false || stripos($dbProd['name'], 'football') !== false) {
                $img = "https://images.unsplash.com/photo-1614632537190-23e4146777db?w=400&h=400&fit=crop";
            } elseif (stripos($dbProd['name'], 'gloves') !== false) {
                $img = "https://images.unsplash.com/photo-1581009146145-b5ef050c2e1e?w=400&h=400&fit=crop";
            } else {
                $img = "https://images.unsplash.com/photo-1602143407151-7111542de6e8?w=400&h=400&fit=crop";
            }
        }

        $productList[] = [
            "id" => intval($dbProd['id']),
            "name" => $dbProd['name'],
            "price" => floatval($dbProd['price']),
            "originalPrice" => floatval($dbProd['price'] * 1.5), // Simulate discount
            "category" => $catMapped,
            "image" => $img,
            "rating" => 4.5,
            "inStock" => intval($dbProd['stock']) > 0
        ];
    }
} else {
    $productList = $defaultProducts;
}
?>

<link rel="stylesheet" href="assets/css/Products.css?v=2">

<div class="products-page">
  <!-- Toast notification -->
  <div class="notification-toast" id="toastNotification" style="display: none;">
    ✅ Product added to cart!
  </div>

  <div class="products-hero">
    <h1>Sports Products</h1>
    <p>Shop premium quality sports gear at unbeatable prices</p>
  </div>

  <div class="stats-bar grid grid-cols-2 md:flex md:flex-row justify-between gap-4 max-w-7xl mx-auto px-4 py-6">
    <div class="stat-item text-center">
      <span class="stat-number block text-2xl md:text-3xl font-bold" id="statsProductCount"><?php echo count($productList); ?>+</span>
      <span class="stat-label text-sm text-gray-400">Products</span>
    </div>
    <div class="stat-item text-center">
      <span class="stat-number block text-2xl md:text-3xl font-bold">7</span>
      <span class="stat-label text-sm text-gray-400">Categories</span>
    </div>
    <div class="stat-item text-center">
      <span class="stat-number block text-2xl md:text-3xl font-bold">50%+</span>
      <span class="stat-label text-sm text-gray-400">Average Savings</span>
    </div>
    <div class="stat-item text-center">
      <span class="stat-number block text-2xl md:text-3xl font-bold">4.5+</span>
      <span class="stat-label text-sm text-gray-400">Rating</span>
    </div>
  </div>

  <div class="filter-bar flex flex-col md:flex-row justify-between items-center max-w-7xl mx-auto px-4 gap-4 py-4">
    <div class="category-filters flex flex-wrap justify-center md:justify-start gap-2">
      <?php foreach ($categories as $cat): ?>
        <button
          class="filter-btn <?php echo $cat === 'All' ? 'active' : ''; ?>"
          onclick="filterByCategory('<?php echo $cat; ?>', this)"
        >
          <?php echo $cat; ?>
        </button>
      <?php endforeach; ?>
    </div>

    <div class="sort-options">

      <select class="sort-select" id="sortSelect" onchange="sortCatalog()">
        <option value="featured">Sort by: Featured</option>
        <option value="price-low">Price: Low to High</option>
        <option value="price-high">Price: High to Low</option>
        <option value="rating">Best Rating</option>
      </select>
    </div>
  </div>

  <div class="products-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 max-w-7xl mx-auto px-4 mt-8" id="productsGrid">
    <!-- Rendered dynamically via JS to support client-side filtering & sorting -->
  </div>
</div>

<script>
const productsData = <?php echo json_encode($productList); ?>;
let selectedCategory = "All";
let sortBy = "featured";

function renderProducts() {
    const grid = document.getElementById("productsGrid");
    if (!grid) return;



    let filtered = productsData.filter(p => selectedCategory === "All" || p.category === selectedCategory);
    
    // Sort
    if (sortBy === "price-low") {
        filtered.sort((a, b) => a.price - b.price);
    } else if (sortBy === "price-high") {
        filtered.sort((a, b) => b.price - a.price);
    } else if (sortBy === "rating") {
        filtered.sort((a, b) => b.rating - a.rating);
    }

    grid.innerHTML = filtered.map(product => {
        const discountPercent = product.originalPrice > product.price 
            ? Math.round(((product.originalPrice - product.price) / product.originalPrice) * 100) 
            : 0;

        const usdPrice = (product.price * 0.012).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        const usdOrigPrice = (product.originalPrice * 0.012).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});

        return `
          <div class="product-card ${!product.inStock ? 'out-of-stock' : ''}" onclick="window.location.href='product-details.php?id=${product.id}'" style="cursor:pointer;">
            <div class="product-badge">
              ${discountPercent > 0 ? `<span class="discount-badge">-${discountPercent}%</span>` : ''}
              ${!product.inStock ? `<span class="out-stock-badge">Out of Stock</span>` : ''}
            </div>

            <div class="product-image-container">
              <img src="${product.image}" alt="${product.name}" class="product-image" />
            </div>

            <span class="product-category">${product.category}</span>
            <h3>${product.name}</h3>

            <div class="product-rating">
              <span class="stars">
                ${"★".repeat(Math.floor(product.rating))}
                ${product.rating % 1 !== 0 ? "½" : ""}
                ${"☆".repeat(5 - Math.ceil(product.rating))}
              </span>
              <span class="rating-value">${product.rating}</span>
            </div>

            <div class="product-price">
              <span class="current-price" style="display: block;">₹${product.price.toLocaleString()} <span style="font-size: 0.8em; color: #9aa0b4; font-weight: normal;">($${usdPrice})</span></span>
              ${product.originalPrice > product.price ? `<span class="original-price" style="display: block; margin-top: 4px;">₹${product.originalPrice.toLocaleString()} <span style="font-size: 0.8em; color: #9aa0b4;">($${usdOrigPrice})</span></span>` : ''}
            </div>

            <button
              class="add-to-cart-btn"
              onclick="event.stopPropagation(); addToStoreCart(${JSON.stringify(product).replace(/"/g, '&quot;')})"
              ${!product.inStock ? 'disabled' : ''}
            >
              ${product.inStock ? '<span class="cart-icon">🛒</span> Add to Cart' : 'Out of Stock'}
            </button>
          </div>
        `;
    }).join('');
}

function filterByCategory(cat, btn) {
    selectedCategory = cat;
    
    // Toggle active classes
    const buttons = document.querySelectorAll(".filter-btn");
    buttons.forEach(b => b.classList.remove("active"));
    btn.classList.add("active");
    
    renderProducts();
}

function sortCatalog() {
    sortBy = document.getElementById("sortSelect").value;
    renderProducts();
}

function addToStoreCart(product) {
    if (!window.Cart) return;
    window.Cart.addToCart(product);
    
    // Trigger toast notification
    const toast = document.getElementById("toastNotification");
    toast.textContent = `✅ ${product.name} added to cart!`;
    toast.style.display = "block";
    setTimeout(() => {
        toast.style.display = "none";
    }, 2000);
}

document.addEventListener("DOMContentLoaded", function() {
    // Check if query param specifies sport/category
    const urlParams = new URLSearchParams(window.location.search);
    const sportParam = urlParams.get('sport');
    if (sportParam) {
        // Map sport to category if relevant
        if (sportParam === "Badminton" || sportParam === "Table Tennis" || sportParam === "Tennis" || sportParam === "Football" || sportParam === "Cricket" || sportParam === "Basketball") {
            selectedCategory = "Equipment";
            // Highlight Equipment filter button
            const buttons = document.querySelectorAll(".filter-btn");
            buttons.forEach(b => {
                if (b.textContent.trim() === "Equipment") {
                    buttons.forEach(x => x.classList.remove("active"));
                    b.classList.add("active");
                }
            });
        }
    }
    
    renderProducts();
});
</script>



<?php require_once __DIR__ . '/includes/footer.php'; ?>
