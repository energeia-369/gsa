import "../styles/ProductDetails.css";
import { useState } from "react";

function ProductDetails() {
  const [selectedSize, setSelectedSize] = useState(null);
  const [quantity, setQuantity] = useState(1);

  const sizes = [6, 7, 8, 9, 10];
  const colors = ["#22c55e", "#3b82f6", "#a855f7", "#ef4444"];

  const incrementQuantity = () => setQuantity(prev => prev + 1);
  const decrementQuantity = () => setQuantity(prev => (prev > 1 ? prev - 1 : 1));

  return (
    <div className="product-details">
      {/* Left Column - Gallery */}
      <div className="product-gallery">
        <div className="product-image-box">
          <div className="product-image">
            <div className="shoe-icon">
              <svg viewBox="0 0 100 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M85,40 L70,35 L55,30 L40,28 L25,30 L15,35 L10,42 L12,50 L20,55 L35,58 L50,56 L65,52 L80,46 L85,40Z" fill="#22c55e" stroke="#16a34a" strokeWidth="1.5"/>
                <circle cx="30" cy="42" r="4" fill="#0f172a"/>
                <circle cx="55" cy="38" r="4" fill="#0f172a"/>
                <path d="M40,50 L50,48 L60,46" stroke="#0f172a" strokeWidth="2" strokeLinecap="round"/>
              </svg>
            </div>
          </div>
        </div>
        <div className="thumbnail-strip">
          <div className="thumbnail active"></div>
          <div className="thumbnail"></div>
          <div className="thumbnail"></div>
        </div>
      </div>

      {/* Right Column - Product Info */}
      <div className="product-info">
        <span className="product-badge">🔥 Best Seller</span>
        <h1>Nexus Velocity Pro</h1>
        <div className="rating">
          <span className="stars">★★★★★</span>
          <span className="review-count">(2,384 reviews)</span>
        </div>
        <p className="product-description">
          Experience next-level performance with our premium sports shoes. 
          Engineered for runners and athletes, featuring breathable mesh, 
          responsive cushioning, and durable rubber outsole for maximum grip.
        </p>
        
        <div className="price-section">
          <h2>₹1999</h2>
          <span className="original-price">₹3999</span>
          <span className="discount">50% OFF</span>
        </div>

        {/* Color Options */}
        <div className="color-section">
          <p>Color: <span className="selected-value">Neon Green</span></p>
          <div className="color-options">
            {colors.map((color, index) => (
              <div 
                key={index}
                className="color-circle"
                style={{ backgroundColor: color }}
              ></div>
            ))}
          </div>
        </div>

        {/* Size Selection */}
        <div className="size-section">
          <p>Select Size</p>
          <div className="size-options">
            {sizes.map(size => (
              <button 
                key={size}
                className={`size-btn ${selectedSize === size ? 'active' : ''}`}
                onClick={() => setSelectedSize(size)}
              >
                {size}
              </button>
            ))}
          </div>
        </div>

        {/* Quantity Selector */}
        <div className="quantity-section">
          <p>Quantity</p>
          <div className="quantity-selector">
            <button className="qty-btn" onClick={decrementQuantity}>−</button>
            <span className="qty-number">{quantity}</span>
            <button className="qty-btn" onClick={incrementQuantity}>+</button>
          </div>
        </div>

        {/* Action Buttons */}
        <div className="action-buttons">
          <button className="cart-btn">Add to Cart 🛒</button>
          <button className="buy-btn">Buy Now ⚡</button>
        </div>

        {/* Delivery Info */}
        <div className="delivery-info">
          <div className="info-item">
            <span>🚚</span>
            <span>Free Delivery on orders above ₹999</span>
          </div>
          <div className="info-item">
            <span>↺</span>
            <span>30-Day Easy Returns</span>
          </div>
        </div>
      </div>
    </div>
  );
}

export default ProductDetails;