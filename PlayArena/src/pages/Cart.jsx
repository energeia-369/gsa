import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { useCart } from "../context/CartContext";
import "../styles/Cart.css";

function Cart() {
  const navigate = useNavigate();

  const [discountCode, setDiscountCode] = useState("");
  const [discountAmount, setDiscountAmount] = useState(0);
  const [discountMessage, setDiscountMessage] = useState("");

  const [nxlCoins, setNxlCoins] = useState(
    Number(localStorage.getItem("nxlCoins")) || 0
  );

  const [coinPercentage, setCoinPercentage] = useState(0); // 0, 5, 10, or 15

  const {
    cartItems = [],
    removeFromCart,
    updateQuantity,
    clearCart,
    getCartTotal,
  } = useCart();

  const subtotal =
    typeof getCartTotal === "function"
      ? getCartTotal()
      : cartItems.reduce(
          (sum, item) =>
            sum + Number(item.price || 0) * Number(item.quantity || 1),
          0
        );

  const originalTotal = cartItems.reduce(
    (sum, item) =>
      sum +
      Number(item.originalPrice || item.price || 0) *
        Number(item.quantity || 1),
    0
  );

  const savings = originalTotal - subtotal;
  const deliveryFee = 0;
  const nxlCreditsEarned = Math.floor(subtotal / 20);

  const coinsNeeded = coinPercentage === 5 ? 100 : (coinPercentage === 10 ? 200 : (coinPercentage === 15 ? 300 : 0));
  const coinDiscount = coinPercentage > 0 ? Math.floor(subtotal * (coinPercentage / 100)) : 0;

  const total = subtotal + deliveryFee - discountAmount - coinDiscount;

  const totalItems = cartItems.reduce(
    (sum, item) => sum + Number(item.quantity || 1),
    0
  );

  const handleQuantityChange = (id, quantity) => {
    if (quantity <= 0) {
      removeFromCart(id);
    } else {
      updateQuantity(id, quantity);
    }
  };

  const handleApplyDiscount = () => {
    const code = discountCode.trim().toUpperCase();

    if (code === "") {
      setDiscountAmount(0);
      setDiscountMessage("Please enter discount code");
      return;
    }

    if (code === "GLOBAL10") {
      const discount = Math.floor(subtotal * 0.1);
      setDiscountAmount(discount);
      setDiscountMessage(`GLOBAL10 applied! You saved ₹${discount}`);
    } else if (code === "NXL100") {
      setDiscountAmount(100);
      setDiscountMessage("NXL100 applied! You saved ₹100");
    } else {
      setDiscountAmount(0);
      setDiscountMessage("Invalid discount code");
    }
  };


  const handleCheckout = () => {
    if (cartItems.length === 0) {
      alert("Your cart is empty");
      return;
    }

    if (!localStorage.getItem("token")) {
      alert("Please login first to proceed to checkout!");
      navigate("/login");
      return;
    }

    const newOrder = {
      id: "ORD-" + Date.now(),
      type: "product",
      title: cartItems.map((item) => item.name).join(", "),
      image: "🛒",
      brand: "GLOBAL SPORTS ARENA",
      quantity: totalItems,
      price: subtotal,
      total: total,
      status: "pending",
      orderDate: new Date().toLocaleDateString(),
      trackingId: "TRK-" + Math.floor(100000 + Math.random() * 900000),
      estimatedDelivery: "5-7 working days",
      items: cartItems,
      discountCode: discountCode,
      discountAmount: discountAmount,
      nxlCoinsUsed: coinsNeeded,
      nxlCoinDiscount: coinDiscount,
      nxlCoinsEarned: nxlCreditsEarned,
      deliveryFee: deliveryFee,
    };

    // Navigate to the checkout page and pass the order details in state
    navigate("/checkout", { state: { order: newOrder } });
  };

  return (
    <div className="cart-page">
      <div className="cart-header">
        <div className="cart-header-content">
          <h1>🛒 Your Cart</h1>
          <p>
            {cartItems.length} item{cartItems.length !== 1 ? "s" : ""} in your
            cart
          </p>
        </div>
      </div>

      <div className="cart-container">
        <div className="cart-items-section">
          {cartItems.length === 0 ? (
            <div className="empty-cart">
              <div className="empty-cart-icon">🛒</div>
              <h3>Your cart is empty</h3>
              <p>Looks like you haven't added any items yet</p>

              <button
                className="continue-shopping-btn"
                onClick={() => navigate("/products")}
              >
                Continue Shopping →
              </button>
            </div>
          ) : (
            <>
              <div className="cart-header-actions">
                <div className="cart-items-header">
                  <span>Product</span>
                  <span>Price</span>
                  <span>Quantity</span>
                  <span>Total</span>
                  <span></span>
                </div>

                <button onClick={clearCart} className="clear-cart-btn">
                  Clear All
                </button>
              </div>

              {cartItems.map((item) => (
                <div className="cart-item" key={item.id}>
                  <div className="item-info">
                    <div className="item-image">
                      <img
                        src={item.image}
                        alt={item.name}
                        onError={(e) => {
                          e.target.src = "/placeholder.png";
                        }}
                      />
                    </div>

                    <div className="item-details">
                      <h3>{item.name}</h3>
                      <p className="item-category">{item.category}</p>

                      {item.originalPrice > item.price && (
                        <span className="item-savings">
                          Save ₹{item.originalPrice - item.price}
                        </span>
                      )}
                    </div>
                  </div>

                  <div className="item-price">
                    <span className="current-price">
                      ₹{Number(item.price || 0).toLocaleString()}
                    </span>

                    {item.originalPrice > item.price && (
                      <span className="original-price">
                        ₹{Number(item.originalPrice || 0).toLocaleString()}
                      </span>
                    )}
                  </div>

                  <div className="item-quantity">
                    <button
                      className="qty-btn minus"
                      onClick={() =>
                        handleQuantityChange(
                          item.id,
                          Number(item.quantity || 1) - 1
                        )
                      }
                    >
                      −
                    </button>

                    <span className="qty-value">{item.quantity}</span>

                    <button
                      className="qty-btn plus"
                      onClick={() =>
                        handleQuantityChange(
                          item.id,
                          Number(item.quantity || 1) + 1
                        )
                      }
                    >
                      +
                    </button>
                  </div>

                  <div className="item-total">
                    ₹
                    {(
                      Number(item.price || 0) * Number(item.quantity || 1)
                    ).toLocaleString()}
                  </div>

                  <button
                    className="remove-btn"
                    onClick={() => removeFromCart(item.id)}
                  >
                    <span>🗑️</span>
                  </button>
                </div>
              ))}
            </>
          )}
        </div>

        {cartItems.length > 0 && (
          <div className="order-summary">
            <h2>Order Summary</h2>

            <div className="summary-details">
              <div className="summary-row">
                <span>Subtotal ({totalItems} items)</span>
                <span>₹{subtotal.toLocaleString()}</span>
              </div>

              <div className="summary-row">
                <span>Original Price</span>
                <span className="original">
                  ₹{originalTotal.toLocaleString()}
                </span>
              </div>

              {savings > 0 && (
                <div className="summary-row savings">
                  <span>You Save</span>
                  <span className="savings-amount">
                    - ₹{savings.toLocaleString()}
                  </span>
                </div>
              )}

              <div className="summary-row">
                <span>Delivery Fee</span>
                <span>{deliveryFee === 0 ? "FREE" : `₹${deliveryFee}`}</span>
              </div>

              <div className="summary-row discount-code">
                <span>Discount Code</span>
                <div className="discount-input-wrapper">
                  <input
                    type="text"
                    placeholder="Enter code"
                    className="discount-input"
                    value={discountCode}
                    onChange={(e) => setDiscountCode(e.target.value)}
                  />
                  <button className="apply-btn" onClick={handleApplyDiscount}>
                    Apply
                  </button>
                </div>
              </div>

              {discountMessage && (
                <p className="discount-message">{discountMessage}</p>
              )}

              {discountAmount > 0 && (
                <div className="summary-row savings">
                  <span>Coupon Discount</span>
                  <span className="savings-amount">
                    - ₹{discountAmount.toLocaleString()}
                  </span>
                </div>
              )}

              <div className="nxl-coins-section" style={{ display: "flex", flexDirection: "column", gap: "12px", alignItems: "stretch", padding: "18px", background: "rgba(197, 168, 92, 0.04)", border: "1px dashed rgba(197, 168, 92, 0.25)", borderRadius: "12px" }}>
                <div className="coins-info" style={{ display: "flex", justifyContent: "space-between", alignItems: "center" }}>
                  <div>
                    <span style={{ display: "block", fontSize: "0.85rem", color: "#9aa0b4", marginBottom: "2px" }}>Available NXL Coins</span>
                    <strong style={{ fontSize: "1.25rem", color: "#f8fafc", fontWeight: "700" }}>{nxlCoins} Coins</strong>
                  </div>
                  {coinPercentage > 0 && (
                    <div style={{ textAlign: "right" }}>
                      <span style={{ display: "block", fontSize: "0.8rem", color: "#c5a85c" }}>Redeeming</span>
                      <strong style={{ fontSize: "1.1rem", color: "#c5a85c" }}>{coinsNeeded} Coins (₹{coinDiscount} Off)</strong>
                    </div>
                  )}
                </div>

                <div style={{ display: "flex", flexDirection: "column", gap: "8px", marginTop: "4px" }}>
                  <span style={{ fontSize: "0.82rem", color: "#c5a85c", fontWeight: "600", letterSpacing: "0.5px" }}>Redemption Options</span>
                  <div style={{ display: "flex", gap: "8px", alignItems: "center" }}>
                    {[5, 10, 15].map((pct) => {
                      const needed = pct === 5 ? 100 : (pct === 10 ? 200 : 300);
                      const isSelectable = nxlCoins >= needed;
                      const isSelected = coinPercentage === pct;
                      return (
                        <button
                          key={pct}
                          type="button"
                          disabled={!isSelectable}
                          onClick={() => setCoinPercentage(isSelected ? 0 : pct)}
                          style={{
                            padding: "8px 14px",
                            borderRadius: "20px",
                            border: isSelected
                              ? "2px solid #c5a85c"
                              : "1px solid rgba(197,168,92,0.25)",
                            background: isSelected
                              ? "rgba(197,168,92,0.2)"
                              : "rgba(197,168,92,0.05)",
                            color: isSelected ? "#c5a85c" : (isSelectable ? "#9aa0b4" : "rgba(154, 160, 180, 0.25)"),
                            fontWeight: "bold",
                            fontSize: "0.85rem",
                            cursor: isSelectable ? "pointer" : "not-allowed",
                            transition: "all 0.2s",
                            outline: "none"
                          }}
                        >
                          {pct}% ({needed} NXL)
                        </button>
                      );
                    })}
                    {coinPercentage > 0 && (
                      <button
                        type="button"
                        onClick={() => setCoinPercentage(0)}
                        style={{
                          padding: "6px 10px",
                          background: "transparent",
                          border: "none",
                          color: "#ef4444",
                          fontSize: "0.8rem",
                          fontWeight: "600",
                          cursor: "pointer",
                          marginLeft: "auto",
                          outline: "none"
                        }}
                      >
                        ✕ Clear Option
                      </button>
                    )}
                  </div>
                </div>
              </div>

              {coinDiscount > 0 && (
                <div className="summary-row savings">
                  <span>NXL Coins Used</span>
                  <span className="savings-amount">
                    - ₹{coinDiscount.toLocaleString()}
                  </span>
                </div>
              )}

              <div className="summary-row total">
                <span>Total Amount</span>
                <span>₹{total.toLocaleString()}</span>
              </div>
            </div>

            <div className="nxl-credits">
              <div className="nxl-icon">💎</div>
              <div className="nxl-info">
                <span>You'll earn</span>
                <strong>{nxlCreditsEarned} NXL Credits</strong>
                <span>on this purchase</span>
              </div>
            </div>

            <button className="checkout-btn" onClick={handleCheckout}>
              Proceed to Checkout →
            </button>

            <div className="payment-methods">
              <p>Secure payment methods</p>
              <div className="payment-icons">
                <span>💳</span>
                <span>📱</span>
                <span>🏦</span>
                <span>🪪</span>
              </div>
            </div>
          </div>
        )}
      </div>

      <footer className="cart-footer">
        <p>© 2026 GLOBAL SPORTS ARENA. All rights reserved.</p>
      </footer>
    </div>
  );
}

export default Cart;