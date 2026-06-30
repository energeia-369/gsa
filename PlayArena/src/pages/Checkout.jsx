import { useLocation, useNavigate } from "react-router-dom";
import { useCart } from "../context/CartContext";
import { useEffect, useState } from "react";
import axios from "axios";
import "../styles/Checkout.css";

function Checkout() {
  const navigate = useNavigate();
  const location = useLocation();
  const { clearCart } = useCart();

  // Read order from state, or fallback
  const [order, setOrder] = useState(location.state?.order || null);
  const [nxlCoins, setNxlCoins] = useState(0);
  const [address, setAddress] = useState("");
  const [phone, setPhone] = useState(localStorage.getItem("userContact") || "");
  const [loading, setLoading] = useState(false);

  const userEmail = localStorage.getItem("userEmail") || "guest@globalsportsarena.com";
  const userName = localStorage.getItem("userName") || "Player";

  // Enforce login
  useEffect(() => {
    if (!localStorage.getItem("token")) {
      alert("Please login first to place an order!");
      navigate("/login");
    }
  }, [navigate]);

  // Load actual wallet balance dynamically from MySQL Spring Boot backend
  useEffect(() => {
    const fetchBalance = async () => {
      try {
        const res = await axios.get(`http://localhost:8080/api/wallet/balance?email=${userEmail}`);
        const balance = res.data.nxlCredits || 0;
        setNxlCoins(balance);
        localStorage.setItem("nxlCoins", balance);
      } catch (err) {
        console.warn("Could not fetch balance from backend", err);
        setNxlCoins(Number(localStorage.getItem("nxlCoins")) || 0);
      }
    };
    if (userEmail) {
      fetchBalance();
    }
  }, [userEmail]);

  const isDigital = order.items && order.items.every(item => 
    item.id && (
      item.id.toString().startsWith("membership-") || 
      item.id.toString().startsWith("event-") || 
      item.id.toString().startsWith("ticket-")
    )
  );

  // Auto-fill address for virtual digital purchases
  useEffect(() => {
    if (order && isDigital && !address) {
      setAddress("Digital Delivery (Email Upgrade)");
    }
  }, [order, isDigital, address]);

  if (!order) {
    return null;
  }

  const handlePayment = async () => {
    setLoading(true);

    const orderPayload = {
      total: order.total,
      subtotal: order.price || order.total,
      discountAmount: order.discountAmount || 0,
      paymentStatus: order.total <= 0 ? "FREE" : "PENDING",
      shippingAddress: address || "GLOBAL SPORTS ARENA Sports Store",
      customerPhone: phone,
      nxlCoinsEarned: order.nxlCoinsEarned || 0,
      nxlCoinsUsed: order.nxlCoinsUsed || 0,
      items: JSON.stringify(order.items),
      email: userEmail,
      paymentMethod: "CARD",
      paymentId: "FREE-ORDER-" + Date.now()
    };

    // Bypass Razorpay entirely if the order amount is zero
    if (order.total <= 0) {
      try {
        const res = await axios.post("http://localhost:8080/api/orders/place", orderPayload);
        clearCart();
        alert(`Order confirmed successfully! You earned ${order.nxlCoinsEarned} NXL Coins.`);
        navigate("/payment-success", { state: { order: res.data } });
      } catch (err) {
        console.error("Order placement failed", err);
        alert("Failed to complete order. Please try again.");
      } finally {
        setLoading(false);
      }
      return;
    }

    if (!window.Razorpay) {
      alert("Razorpay SDK not loaded. Check index.html script.");
      setLoading(false);
      return;
    }

    const options = {
      key: "rzp_test_Sv9Ri6GpwqTesi",
      amount: Math.round(order.total * 100), // in paise
      currency: "INR",
      name: "GLOBAL SPORTS ARENA",
      description: "Sports Tournaments & Gear",
      image: "https://cdn-icons-png.flaticon.com/512/857/857455.png",
      handler: async function (response) {
        const paymentId = response.razorpay_payment_id;
        
        const finalPayload = {
          ...orderPayload,
          paymentStatus: "PAID",
          paymentId: paymentId,
          paymentMethod: "RAZORPAY"
        };

        try {
          const res = await axios.post("http://localhost:8080/api/orders/place", finalPayload);
          clearCart();
          
          // Sync new coin balance in localStorage
          const newBalance = nxlCoins - (order.nxlCoinsUsed || 0) + (order.nxlCoinsEarned || 0);
          localStorage.setItem("nxlCoins", newBalance);
          
          alert(`Payment successful! You earned ${order.nxlCoinsEarned} NXL Coins.`);
          navigate("/payment-success", { state: { order: res.data } });
        } catch (err) {
          console.error("Failed to sync paid order to backend database", err);
          alert("Payment received, but database sync failed. Please contact support.");
        } finally {
          setLoading(false);
        }
      },
      prefill: {
        name: userName,
        email: userEmail,
        contact: phone,
      },
      notes: {
        address: address || "GLOBAL SPORTS ARENA Sports Store",
        orderId: order.id
      },
      theme: {
        color: "#c5a85c",
      },
      modal: {
        ondismiss: function () {
          setLoading(false);
        }
      }
    };

    const razorpay = new window.Razorpay(options);
    razorpay.open();
  };

  return (
    <div className="checkout-page" style={{ color: "#f8fafc" }}>
      <div className="checkout-box" style={{ maxWidth: "600px", width: "90%", background: "#12131c", padding: "30px", borderRadius: "24px", border: "1px solid rgba(197, 168, 92, 0.25)", boxShadow: "0 15px 35px rgba(0,0,0,0.5)" }}>
        <h1 style={{ textAlign: "center", fontSize: "2rem", marginBottom: "20px", color: "#c5a85c", fontWeight: "800", letterSpacing: "1px" }}>💳 Secure Checkout</h1>
        
        {/* Order Details list */}
        <div style={{ background: "rgba(22, 24, 38, 0.5)", padding: "15px", borderRadius: "16px", border: "1px solid rgba(255,255,255,0.05)", marginBottom: "20px" }}>
          <h3 style={{ borderBottom: "1px solid rgba(197,168,92,0.2)", paddingBottom: "8px", marginBottom: "12px", color: "#f8fafc", fontWeight: "700" }}>Order Summary</h3>
          <div style={{ maxHeight: "150px", overflowY: "auto", marginBottom: "15px" }}>
            {order.items && order.items.map((item) => (
              <div key={item.id} style={{ display: "flex", justifyContent: "space-between", alignItems: "center", marginBottom: "10px", fontSize: "0.9rem" }}>
                <span style={{ color: "#9aa0b4" }}>{item.name} <strong style={{ color: "#c5a85c" }}>x{item.quantity}</strong></span>
                <span style={{ fontWeight: "600" }}>₹{(item.price * item.quantity).toLocaleString()}</span>
              </div>
            ))}
          </div>

          <div style={{ display: "flex", justifyContent: "space-between", fontSize: "0.9rem", color: "#9aa0b4", marginBottom: "6px" }}>
            <span>Subtotal</span>
            <span>₹{order.price.toLocaleString()}</span>
          </div>

          {order.discountAmount > 0 && (
            <div style={{ display: "flex", justifyContent: "space-between", fontSize: "0.9rem", color: "#ef4444", marginBottom: "6px" }}>
              <span>Coupon Discount</span>
              <span>- ₹{order.discountAmount.toLocaleString()}</span>
            </div>
          )}

          {order.nxlCoinsUsed > 0 && (
            <div style={{ display: "flex", justifyContent: "space-between", fontSize: "0.9rem", color: "#ef4444", marginBottom: "6px" }}>
              <span>NXL Coins Used ({order.nxlCoinsUsed} Coins)</span>
              <span>- ₹{(order.nxlCoinDiscount || order.nxlCoinsUsed).toLocaleString()}</span>
            </div>
          )}

          <div style={{ display: "flex", justifyContent: "space-between", fontSize: "0.9rem", color: "#9aa0b4", marginBottom: "6px" }}>
            <span>Delivery Fee</span>
            <span>{order.deliveryFee === 0 ? "FREE" : `₹${order.deliveryFee}`}</span>
          </div>

          <div style={{ display: "flex", justifyContent: "space-between", fontSize: "1.2rem", fontWeight: "800", color: "#c5a85c", borderTop: "1px solid rgba(197,168,92,0.2)", paddingTop: "8px", marginTop: "8px" }}>
            <span>Total Payable</span>
            <span>₹{order.total.toLocaleString()}</span>
          </div>
        </div>

        {/* NXL Coins Earning */}
        <div style={{ background: "rgba(197, 168, 92, 0.08)", border: "1px dashed #c5a85c", padding: "12px", borderRadius: "12px", textAlign: "center", marginBottom: "20px", fontSize: "0.9rem", color: "#f5f6fa" }}>
          💎 You will earn <strong style={{ color: "#c5a85c", fontSize: "1rem" }}>{order.nxlCoinsEarned} NXL Credits</strong> on this purchase!
        </div>

        {/* Shipping details form */}
        <div style={{ marginBottom: "20px" }}>
          {!isDigital && (
            <>
              <label style={{ display: "block", fontSize: "0.85rem", color: "#9aa0b4", marginBottom: "5px" }}>Delivery Address *</label>
              <input
                type="text"
                placeholder="Enter your shipping address"
                value={address}
                onChange={(e) => setAddress(e.target.value)}
                style={{ width: "100%", padding: "10px", border: "1px solid rgba(197, 168, 92, 0.2)", borderRadius: "8px", background: "rgba(22, 24, 38, 0.8)", color: "#f5f6fa", boxSizing: "border-box", outline: "none", marginBottom: "12px" }}
                required
              />
            </>
          )}

          <label style={{ display: "block", fontSize: "0.85rem", color: "#9aa0b4", marginBottom: "5px" }}>Phone Number *</label>
          <input
            type="tel"
            placeholder="Enter your phone number"
            value={phone}
            onChange={(e) => setPhone(e.target.value)}
            style={{ width: "100%", padding: "10px", border: "1px solid rgba(197, 168, 92, 0.2)", borderRadius: "8px", background: "rgba(22, 24, 38, 0.8)", color: "#f5f6fa", boxSizing: "border-box", outline: "none" }}
            required
          />
        </div>

        {/* Action Button */}
        <button 
          onClick={handlePayment} 
          disabled={!address.trim() || !phone.trim() || loading}
          style={{ 
            width: "100%", 
            padding: "12px", 
            background: (!address.trim() || !phone.trim() || loading) 
              ? "#475569" 
              : "linear-gradient(135deg, #c5a85c 0%, #8c7237 100%)", 
            color: (!address.trim() || !phone.trim() || loading) ? "#94a3b8" : "#0b0c10", 
            border: "none", 
            borderRadius: "8px", 
            fontSize: "1.1rem", 
            fontWeight: "bold", 
            cursor: (!address.trim() || !phone.trim() || loading) ? "not-allowed" : "pointer",
            transition: "background 0.3s ease"
          }}
        >
          {loading 
            ? "Processing Transaction..." 
            : (!address.trim() || !phone.trim()) 
              ? (isDigital ? "Enter Phone to Pay" : "Fill Shipping Details to Pay")
              : order.total <= 0 
                ? "Place Free Order →" 
                : `Pay ₹${order.total.toLocaleString()} via Razorpay`}
        </button>

        <button 
          onClick={() => navigate("/cart")} 
          disabled={loading}
          style={{ 
            width: "100%", 
            padding: "10px", 
            background: "transparent", 
            color: "#9aa0b4", 
            border: "1px solid rgba(197, 168, 92, 0.2)", 
            borderRadius: "8px", 
            fontSize: "0.95rem", 
            marginTop: "12px",
            cursor: "pointer"
          }}
        >
          ← Cancel and Return to Cart
        </button>
      </div>
    </div>
  );
}

export default Checkout;