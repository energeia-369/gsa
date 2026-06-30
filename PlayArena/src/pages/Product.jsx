import { useState } from "react";
import { useCart } from "../context/CartContext";
import "../styles/Products.css";

function Products() {
  const { addToCart } = useCart();

  const [showNotification, setShowNotification] = useState(false);
  const [notificationProduct, setNotificationProduct] = useState("");
  const [selectedCategory, setSelectedCategory] = useState("All");
  const [sortBy, setSortBy] = useState("featured");

  const products = [
    { id: 1, name: "Sports Shoes", price: 1999, originalPrice: 2999, category: "Footwear", image: "https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&h=400&fit=crop", rating: 4.5, inStock: true },
    { id: 2, name: "Running Shoes", price: 2499, originalPrice: 3999, category: "Footwear", image: "https://images.unsplash.com/photo-1608231387042-66d1773070a5?w=400&h=400&fit=crop", rating: 4.7, inStock: true },
    { id: 3, name: "Football Cleats", price: 2999, originalPrice: 4999, category: "Footwear", image: "https://images.unsplash.com/photo-1579952363873-27f3bade9f55?w=400&h=400&fit=crop", rating: 4.6, inStock: true },
    { id: 4, name: "Basketball Shoes", price: 3499, originalPrice: 5499, category: "Footwear", image: "https://images.unsplash.com/photo-1600185365483-26d7a4cc7519?w=400&h=400&fit=crop", rating: 4.8, inStock: true },
    { id: 5, name: "Training Shoes", price: 1799, originalPrice: 2799, category: "Footwear", image: "https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?w=400&h=400&fit=crop", rating: 4.4, inStock: true },
    { id: 6, name: "Cricket Shoes", price: 2199, originalPrice: 3499, category: "Footwear", image: "https://images.unsplash.com/photo-1605348532760-6753d2c43329?w=400&h=400&fit=crop", rating: 4.3, inStock: true },

    { id: 7, name: "Football Jersey", price: 799, originalPrice: 1299, category: "Apparel", image: "https://images.unsplash.com/photo-1517466787929-bc90951d0974?w=400&h=400&fit=crop", rating: 4.3, inStock: true },
    { id: 8, name: "Cricket Jersey", price: 899, originalPrice: 1499, category: "Apparel", image: "https://images.unsplash.com/photo-1531415074968-036ba1b575da?w=400&h=400&fit=crop", rating: 4.5, inStock: true },
    { id: 9, name: "Basketball Jersey", price: 699, originalPrice: 1199, category: "Apparel", image: "https://images.unsplash.com/photo-1584736286279-4e5a3d6f2c5c?w=400&h=400&fit=crop", rating: 4.4, inStock: true },
    { id: 10, name: "Running Shorts", price: 599, originalPrice: 999, category: "Apparel", image: "https://pyxis.nymag.com/v1/imgs/63a/f37/02f32669118a054b6ad8f71f1a4ae70bb1-nike-shorts.rsquare.w600.jpg", rating: 4.3, inStock: true },
    { id: 11, name: "Compression Tights", price: 899, originalPrice: 1499, category: "Apparel", image: "https://images.unsplash.com/photo-1577221084712-45b0445d2b00?w=400&h=400&fit=crop", rating: 4.6, inStock: true },
    { id: 12, name: "Gym T-Shirt", price: 399, originalPrice: 799, category: "Apparel", image: "https://images.unsplash.com/photo-1581655353564-df123a1eb820?w=400&h=400&fit=crop", rating: 4.2, inStock: true },
    { id: 13, name: "Track Pants", price: 999, originalPrice: 1599, category: "Apparel", image: "https://images.unsplash.com/photo-1556906781-9a412961c28c?w=400&h=400&fit=crop", rating: 4.4, inStock: true },
    { id: 14, name: "Sports Jacket", price: 1499, originalPrice: 2499, category: "Apparel", image: "https://images.unsplash.com/photo-1591047139829-d91aecb6caea?w=400&h=400&fit=crop", rating: 4.7, inStock: true },

    { id: 15, name: "Badminton Racket", price: 1499, originalPrice: 2499, category: "Equipment", image: "https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?w=400&h=400&fit=crop", rating: 4.7, inStock: true },
    { id: 16, name: "Football", price: 999, originalPrice: 1599, category: "Equipment", image: "https://images.unsplash.com/photo-1577460551100-5ba8d6f05a0e?w=400&h=400&fit=crop", rating: 4.4, inStock: true },
    { id: 17, name: "Cricket Bat", price: 2499, originalPrice: 3999, category: "Equipment", image: "https://images.unsplash.com/photo-1531415074968-036ba1b575da?w=400&h=400&fit=crop", rating: 4.8, inStock: false },
    { id: 18, name: "Tennis Racket", price: 1999, originalPrice: 3499, category: "Equipment", image: "https://images.unsplash.com/photo-1595435934249-5df7ed86e1f0?w=400&h=400&fit=crop", rating: 4.6, inStock: true },
    { id: 19, name: "Basketball", price: 799, originalPrice: 1299, category: "Equipment", image: "https://images.unsplash.com/photo-1519861531473-9200262188bf?w=400&h=400&fit=crop", rating: 4.5, inStock: true },
    { id: 20, name: "Volleyball", price: 699, originalPrice: 1199, category: "Equipment", image: "https://images.unsplash.com/photo-1612872087720-bb876e2e67d1?w=400&h=400&fit=crop", rating: 4.3, inStock: true },
    { id: 21, name: "Table Tennis Racket", price: 599, originalPrice: 999, category: "Equipment", image: "https://images.unsplash.com/photo-1615199374903-6e8f5f6e4e3a?w=400&h=400&fit=crop", rating: 4.4, inStock: true },
    { id: 22, name: "Hockey Stick", price: 1799, originalPrice: 2999, category: "Equipment", image: "https://images.unsplash.com/photo-1540747913346-19e32dc3e97e?w=400&h=400&fit=crop", rating: 4.5, inStock: true },
    { id: 23, name: "Baseball Bat", price: 1499, originalPrice: 2499, category: "Equipment", image: "https://images.unsplash.com/photo-1542884841-5bcf274d5e5e?w=400&h=400&fit=crop", rating: 4.3, inStock: true },
    { id: 24, name: "Skipping Rope", price: 199, originalPrice: 399, category: "Equipment", image: "https://images.unsplash.com/photo-1599058917212-d750089bc07e?w=400&h=400&fit=crop", rating: 4.1, inStock: true },
    { id: 25, name: "Resistance Bands", price: 299, originalPrice: 599, category: "Equipment", image: "https://images.unsplash.com/photo-1598266663439-0f65ef7b0a8a?w=400&h=400&fit=crop", rating: 4.2, inStock: true },
    { id: 26, name: "Punching Bag", price: 3999, originalPrice: 5999, category: "Equipment", image: "https://images.unsplash.com/photo-1599058917765-a3b3754a4df2?w=400&h=400&fit=crop", rating: 4.9, inStock: true },

    { id: 27, name: "Water Bottle", price: 299, originalPrice: 499, category: "Accessories", image: "https://images.unsplash.com/photo-1602143407151-7111542de6e8?w=400&h=400&fit=crop", rating: 4.2, inStock: true },
    { id: 28, name: "Gym Gloves", price: 499, originalPrice: 899, category: "Accessories", image: "https://images.unsplash.com/photo-1599058918147-1b2b1c6e7e4a?w=400&h=400&fit=crop", rating: 4.6, inStock: true },
    { id: 29, name: "Sports Cap", price: 299, originalPrice: 599, category: "Accessories", image: "https://images.unsplash.com/photo-1588850561407-ed78c282e89b?w=400&h=400&fit=crop", rating: 4.3, inStock: true },
    { id: 30, name: "Wrist Band", price: 99, originalPrice: 199, category: "Accessories", image: "https://images.unsplash.com/photo-1584735935682-2f2b69dff9d2?w=400&h=400&fit=crop", rating: 4.0, inStock: true },
    { id: 31, name: "Sports Sunglasses", price: 799, originalPrice: 1499, category: "Accessories", image: "https://images.unsplash.com/photo-1577803645773-f96470509666?w=400&h=400&fit=crop", rating: 4.5, inStock: true },
    { id: 32, name: "Gym Bag", price: 999, originalPrice: 1799, category: "Accessories", image: "https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=400&h=400&fit=crop", rating: 4.7, inStock: true },
    { id: 33, name: "Fitness Tracker", price: 1999, originalPrice: 3499, category: "Accessories", image: "https://images.unsplash.com/photo-1575311373937-040b8e1fd5b6?w=400&h=400&fit=crop", rating: 4.8, inStock: true },
    { id: 34, name: "Sports Towel", price: 199, originalPrice: 399, category: "Accessories", image: "https://images.unsplash.com/photo-1585435557343-3b092031a4ec?w=400&h=400&fit=crop", rating: 4.1, inStock: true },

    { id: 35, name: "Yoga Mat", price: 899, originalPrice: 1499, category: "Fitness", image: "https://images.unsplash.com/photo-1592432678016-e910b452f9a2?w=400&h=400&fit=crop", rating: 4.4, inStock: true },
    { id: 36, name: "Dumbbell Set", price: 2499, originalPrice: 3999, category: "Fitness", image: "https://images.unsplash.com/photo-1586401100295-7a8096fd231a?w=400&h=400&fit=crop", rating: 4.7, inStock: true },
    { id: 37, name: "Kettlebell", price: 1499, originalPrice: 2499, category: "Fitness", image: "https://images.unsplash.com/photo-1584735935682-2f2b69dff9d2?w=400&h=400&fit=crop", rating: 4.6, inStock: true },
    { id: 38, name: "Pull Up Bar", price: 999, originalPrice: 1699, category: "Fitness", image: "https://images.unsplash.com/photo-1598266663439-0f65ef7b0a8a?w=400&h=400&fit=crop", rating: 4.5, inStock: true },
    { id: 39, name: "Ab Roller", price: 399, originalPrice: 799, category: "Fitness", image: "https://images.unsplash.com/photo-1599058917765-a3b3754a4df2?w=400&h=400&fit=crop", rating: 4.3, inStock: true },
    { id: 40, name: "Push Up Stand", price: 499, originalPrice: 899, category: "Fitness", image: "https://images.unsplash.com/photo-1599058918147-1b2b1c6e7e4a?w=400&h=400&fit=crop", rating: 4.4, inStock: true },

    { id: 41, name: "Cricket Helmet", price: 1499, originalPrice: 2499, category: "Protective Gear", image: "https://images.unsplash.com/photo-1531415074968-036ba1b575da?w=400&h=400&fit=crop", rating: 4.7, inStock: true },
    { id: 42, name: "Knee Guards", price: 599, originalPrice: 999, category: "Protective Gear", image: "https://images.unsplash.com/photo-1584735935682-2f2b69dff9d2?w=400&h=400&fit=crop", rating: 4.5, inStock: true },
    { id: 43, name: "Elbow Guards", price: 499, originalPrice: 899, category: "Protective Gear", image: "https://images.unsplash.com/photo-1585435557343-3b092031a4ec?w=400&h=400&fit=crop", rating: 4.4, inStock: true },
    { id: 44, name: "Shin Guards", price: 699, originalPrice: 1199, category: "Protective Gear", image: "https://images.unsplash.com/photo-1577460551100-5ba8d6f05a0e?w=400&h=400&fit=crop", rating: 4.6, inStock: true },
  ].map(p => p.id === 1 ? p : { ...p, price: 0, originalPrice: p.price });

  const categories = [
    "All",
    "Footwear",
    "Apparel",
    "Equipment",
    "Accessories",
    "Fitness",
    "Protective Gear",
  ];

  const handleAddToCart = (product) => {
    if (!product.inStock) return;

    addToCart(product);
    setNotificationProduct(product.name);
    setShowNotification(true);

    setTimeout(() => {
      setShowNotification(false);
    }, 2000);
  };

  const filteredProducts = products.filter((product) => {
    return selectedCategory === "All" || product.category === selectedCategory;
  });

  const sortedProducts = [...filteredProducts].sort((a, b) => {
    if (sortBy === "price-low") return a.price - b.price;
    if (sortBy === "price-high") return b.price - a.price;
    if (sortBy === "rating") return b.rating - a.rating;
    return 0;
  });

  return (
    <div className="products-page">
      {showNotification && (
        <div className="notification-toast">
          ✅ {notificationProduct} added to cart!
        </div>
      )}

      <div className="products-hero">
        <div className="hero-badge">🛍️ Limited Time Offer</div>
        <h1>Sports Products</h1>
        <p>Shop premium quality sports gear at unbeatable prices</p>
        <div className="hero-offer">🔥 Up to 60% Off on selected items</div>
      </div>

      <div className="stats-bar">
        <div className="stat-item">
          <span className="stat-number">{products.length}+</span>
          <span className="stat-label">Products</span>
        </div>

        <div className="stat-item">
          <span className="stat-number">{categories.length}</span>
          <span className="stat-label">Categories</span>
        </div>

        <div className="stat-item">
          <span className="stat-number">50%+</span>
          <span className="stat-label">Average Savings</span>
        </div>

        <div className="stat-item">
          <span className="stat-number">4.5+</span>
          <span className="stat-label">Rating</span>
        </div>
      </div>

      <div className="filter-bar">
        <div className="category-filters">
          {categories.map((cat) => (
            <button
              key={cat}
              className={`filter-btn ${selectedCategory === cat ? "active" : ""}`}
              onClick={() => setSelectedCategory(cat)}
            >
              {cat}
            </button>
          ))}
        </div>

        <div className="sort-options">
          <select
            className="sort-select"
            value={sortBy}
            onChange={(e) => setSortBy(e.target.value)}
          >
            <option value="featured">Sort by: Featured</option>
            <option value="price-low">Price: Low to High</option>
            <option value="price-high">Price: High to Low</option>
            <option value="rating">Best Rating</option>
          </select>
        </div>
      </div>

      <div className="products-grid">
        {sortedProducts.map((product) => (
          <div
            className={`product-card ${!product.inStock ? "out-of-stock" : ""}`}
            key={product.id}
          >
            <div className="product-badge">
              {product.originalPrice > product.price && (
                <span className="discount-badge">
                  -
                  {Math.round(
                    ((product.originalPrice - product.price) /
                      product.originalPrice) *
                      100
                  )}
                  %
                </span>
              )}

              {!product.inStock && (
                <span className="out-stock-badge">Out of Stock</span>
              )}
            </div>

            <div className="product-image-container">
              <img
                src={product.image}
                alt={product.name}
                className="product-image"
              />
            </div>

            <span className="product-category">{product.category}</span>

            <h3>{product.name}</h3>

            <div className="product-rating">
              <span className="stars">
                {"★".repeat(Math.floor(product.rating))}
                {product.rating % 1 !== 0 && "½"}
                {"☆".repeat(5 - Math.ceil(product.rating))}
              </span>

              <span className="rating-value">{product.rating}</span>
            </div>

            <div className="product-price">
              <span className="current-price">
                ₹{product.price.toLocaleString()}
              </span>

              {product.originalPrice > product.price && (
                <span className="original-price">
                  ₹{product.originalPrice.toLocaleString()}
                </span>
              )}
            </div>

            <button
              className="add-to-cart-btn"
              onClick={() => handleAddToCart(product)}
              disabled={!product.inStock}
            >
              {product.inStock ? (
                <>
                  <span className="cart-icon">🛒</span>
                  Add to Cart
                </>
              ) : (
                "Out of Stock"
              )}
            </button>
          </div>
        ))}
      </div>

      <footer className="footer">
        <div className="footer-content">
          <div className="footer-section">
            <h4>GLOBAL SPORTS ARENA</h4>
            <p>Your ultimate destination for sports events and e-commerce</p>
          </div>

          <div className="footer-section">
            <h4>Products</h4>
            <a href="#">Footwear</a>
            <a href="#">Apparel</a>
            <a href="#">Equipment</a>
            <a href="#">Accessories</a>
            <a href="#">Fitness</a>
            <a href="#">Protective Gear</a>
          </div>

          <div className="footer-section">
            <h4>Quick Links</h4>
            <a href="#">About Us</a>
            <a href="#">Events</a>
            <a href="#">Shop</a>
            <a href="#">NXL Credits</a>
          </div>

          <div className="footer-section">
            <h4>Follow Us</h4>

            <div className="social-links">
              <a href="#">📘</a>
              <a href="#">📷</a>
              <a href="#">🐦</a>
              <a href="#">🎵</a>
            </div>
          </div>
        </div>

        <div className="footer-bottom">
          <p>&copy; 2026 GLOBAL SPORTS ARENA. All Rights Reserved.</p>
        </div>
      </footer>
    </div>
  );
}

export default Products;