import "../styles/SportsCategories.css";
import { useNavigate } from "react-router-dom";

function SportsCategories() {
  const navigate = useNavigate();

  const categories = [
    {
      name: "Badminton",
      icon: "🏸",
      description: "Rackets, shuttlecocks, nets & professional gear",
      events: 12,
      products: 48,
    },
    {
      name: "Table Tennis",
      icon: "🏓",
      description: "Blades, rubbers, balls & training equipment",
      events: 8,
      products: 36,
    },
    {
      name: "Tennis",
      icon: "🎾",
      description: "Rackets, balls, strings & court accessories",
      events: 15,
      products: 62,
    },
    {
      name: "Football",
      icon: "⚽",
      description: "Boots, jerseys, goals & training gear",
      events: 20,
      products: 85,
    },
    {
      name: "Running",
      icon: "👟",
      description: "Shoes, apparel, track gear & accessories",
      events: 10,
      products: 54,
    },
    {
      name: "Gym Accessories",
      icon: "💪",
      description: "Gloves, belts, bottles & fitness equipment",
      events: 5,
      products: 42,
    },
    {
      name: "Cricket",
      icon: "🏏",
      description: "Bats, balls, pads & protective gear",
      events: 18,
      products: 72,
    },
    {
      name: "Basketball",
      icon: "🏀",
      description: "Balls, hoops, jerseys & training aids",
      events: 14,
      products: 38,
    },
  ];

  const featuredCategories = ["Football", "Cricket", "Tennis"];

  const handleExplore = (sportName) => {
    navigate(`/event-registration?sport=${sportName}`);
  };

  const handleShop = (sportName) => {
    navigate(`/products?sport=${sportName}`);
  };

  return (
    <div className="sports-page">
      <div className="categories-hero">
        <div className="hero-badge">🏆 Explore Our Collection</div>

        <h1>Sports Categories</h1>

        <p>
          Discover the perfect gear and events for your favorite sports. From
          professional equipment to casual training - we have it all.
        </p>

        <div className="hero-search">
          <input
            type="text"
            placeholder="Search for a sport..."
            className="search-input"
          />
          <button className="search-btn">🔍 Search</button>
        </div>
      </div>

      <div className="quick-stats">
        <div className="stat-card">
          <span className="stat-number">8+</span>
          <span className="stat-name">Sports Categories</span>
        </div>

        <div className="stat-card">
          <span className="stat-number">102+</span>
          <span className="stat-name">Live Events</span>
        </div>

        <div className="stat-card">
          <span className="stat-number">437+</span>
          <span className="stat-name">Products</span>
        </div>

        <div className="stat-card">
          <span className="stat-number">50K+</span>
          <span className="stat-name">Active Players</span>
        </div>
      </div>

      <div className="categories-section">
        <div className="section-header">
          <span className="section-tag">Shop by Sport</span>
          <h2>All Sports Categories</h2>
          <p>Find everything you need for your favorite sport</p>
        </div>

        <div className="category-grid">
          {categories.map((category, index) => (
            <div
              className={`category-card ${
                featuredCategories.includes(category.name) ? "featured" : ""
              }`}
              key={index}
            >
              <div className="card-badge">
                {featuredCategories.includes(category.name) && "🔥 Trending"}
              </div>

              <div className="category-icon">{category.icon}</div>

              <h3>{category.name}</h3>

              <p className="category-desc">{category.description}</p>

              <div className="category-stats">
                <span className="stat">
                  <span className="stat-icon">📅</span> {category.events} Events
                </span>

                <span className="stat">
                  <span className="stat-icon">🛍️</span>{" "}
                  {category.products} Products
                </span>
              </div>

              <div className="card-actions">
                <button
                  className="explore-category"
                  onClick={() => handleExplore(category.name)}
                >
                  Explore →
                </button>

                <button
                  className="shop-category"
                  onClick={() => handleShop(category.name)}
                >
                  Shop Now
                </button>
              </div>
            </div>
          ))}
        </div>
      </div>

      <div className="featured-products">
        <div className="section-header">
          <span className="section-tag">Top Picks</span>
          <h2>Best Selling Products</h2>
          <p>Most loved items across all sports</p>
        </div>

        <div className="products-preview">
          <div className="product-item">
            <div className="product-icon">🏸</div>
            <h4>Pro Badminton Racket</h4>
            <p>₹2,499</p>
            <button onClick={() => navigate("/products")}>Buy Now</button>
          </div>

          <div className="product-item">
            <div className="product-icon">⚽</div>
            <h4>Football Studs</h4>
            <p>₹1,299</p>
            <button onClick={() => navigate("/products")}>Buy Now</button>
          </div>

          <div className="product-item">
            <div className="product-icon">👟</div>
            <h4>Running Shoes</h4>
            <p>₹3,999</p>
            <button onClick={() => navigate("/products")}>Buy Now</button>
          </div>

          <div className="product-item">
            <div className="product-icon">💪</div>
            <h4>Gym Gloves</h4>
            <p>₹599</p>
            <button onClick={() => navigate("/products")}>Buy Now</button>
          </div>
        </div>
      </div>
    </div>
  );
}

export default SportsCategories;