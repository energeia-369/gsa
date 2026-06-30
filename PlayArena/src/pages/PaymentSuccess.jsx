import "../styles/PaymentSuccess.css";
import { useEffect, useState } from "react";
import { useNavigate, useLocation } from "react-router-dom";

function PaymentSuccess() {
  const navigate = useNavigate();
  const location = useLocation();
  const [countdown, setCountdown] = useState(15); // Increase countdown to 15 seconds to allow ticket viewing
  const [cancelRedirect, setCancelRedirect] = useState(false);

  // Read dynamic order returned from backend API
  const orderDetails = location.state?.order || {
    id: "N/A",
    totalAmount: 0,
    orderDate: new Date().toISOString(),
    nxlCoinsEarned: 0,
    shippingAddress: "N/A",
    customerPhone: "N/A",
    itemsJson: "[]"
  };

  const dbOrderId = orderDetails.id;
  const transactionId = "TXN-" + dbOrderId + "-" + Math.floor(Math.random() * 10000);
  const paymentDate = new Date(orderDetails.orderDate).toLocaleString();

  useEffect(() => {
    if (cancelRedirect) return;

    const timer = setInterval(() => {
      setCountdown((prev) => {
        if (prev <= 1) {
          clearInterval(timer);
          navigate("/");
          return 0;
        }
        return prev - 1;
      });
    }, 1000);

    return () => clearInterval(timer);
  }, [navigate, cancelRedirect]);

  const handleGoHome = () => {
    navigate("/");
  };

  const handleViewOrders = () => {
    navigate("/user-dashboard");
  };

  // Compile QR Data for verification pass
  const qrVerificationData = JSON.stringify({
    orderId: dbOrderId,
    address: orderDetails.shippingAddress,
    phone: orderDetails.customerPhone,
    status: "VALID_ENTRY_PASS",
    platform: "GLOBAL_SPORTS_ARENA"
  });

  // Generates real visual QR Code via secure API
  const qrCodeUrl = `https://api.qrserver.com/v1/create-qr-code/?size=160x160&color=c5a85c&bgcolor=12131c&data=${encodeURIComponent(qrVerificationData)}`;

  return (
    <div className="success-page" style={{ background: "#0b0c10", fontFamily: "Outfit, sans-serif" }}>
      <div className="bg-animation">
        <div className="circle circle-1" style={{ background: "rgba(197, 168, 92, 0.05)" }}></div>
        <div className="circle circle-2" style={{ background: "rgba(197, 168, 92, 0.03)" }}></div>
      </div>

      <div className="success-box" style={{ background: "#12131c", border: "1px solid rgba(197, 168, 92, 0.25)", boxShadow: "0 15px 35px rgba(0,0,0,0.6)", padding: "40px", borderRadius: "24px", maxWidth: "650px", width: "90%" }}>
        <div className="success-animation">
          <div className="checkmark-circle" style={{ borderColor: "#c5a85c" }}>
            <div className="checkmark draw" style={{ borderRight: "3px solid #c5a85c", borderTop: "3px solid #c5a85c" }}></div>
          </div>
        </div>

        <h1 className="success-title" style={{ color: "#c5a85c", fontWeight: "800", fontSize: "2.2rem" }}>Payment Successful! 🎉</h1>

        <p className="success-message" style={{ color: "#9aa0b4" }}>
          Your order and event registration have been successfully synchronized with the database.
        </p>

        {/* E-Ticket Display */}
        <div style={{ background: "#0b0c10", border: "1px dashed rgba(197, 168, 92, 0.3)", borderRadius: "16px", padding: "20px", margin: "20px 0", textAlign: "left", display: "grid", gridTemplateColumns: "1.2fr 0.8fr", gap: "20px", alignItems: "center" }}>
          <div>
            <h4 style={{ color: "#c5a85c", margin: "0 0 10px 0", fontSize: "1.1rem", borderBottom: "1px solid rgba(197,168,92,0.15)", paddingBottom: "6px" }}>🏆 Dynamic QR Event Pass</h4>
            <div style={{ fontSize: "0.85rem", color: "#9aa0b4", marginBottom: "8px" }}>
              <strong>Order/Booking ID:</strong> <span style={{ color: "#f5f6fa" }}>#ORD-{dbOrderId}</span>
            </div>
            <div style={{ fontSize: "0.85rem", color: "#9aa0b4", marginBottom: "8px" }}>
              <strong>Date & Time:</strong> <span style={{ color: "#f5f6fa" }}>{paymentDate}</span>
            </div>
            <div style={{ fontSize: "0.85rem", color: "#9aa0b4", marginBottom: "8px" }}>
              <strong>Amount Synchronized:</strong> <span style={{ color: "#22c55e", fontWeight: "bold" }}>₹{orderDetails.totalAmount.toLocaleString()}</span>
            </div>
            <div style={{ fontSize: "0.85rem", color: "#9aa0b4" }}>
              <strong>Loyalty Earned:</strong> <span style={{ color: "#c5a85c", fontWeight: "bold" }}>💎 {orderDetails.nxlCoinsEarned} NXL Coins</span>
            </div>
          </div>

          <div style={{ display: "flex", flexDirection: "column", alignItems: "center", justifyContent: "center" }}>
            <img 
              src={qrCodeUrl} 
              alt="Booking QR Code Pass" 
              style={{ border: "2px solid #c5a85c", borderRadius: "10px", padding: "5px", background: "#12131c" }} 
            />
            <span style={{ fontSize: "0.7rem", color: "#c5a85c", marginTop: "6px", fontWeight: "bold", letterSpacing: "1px" }}>SCAN AT COMPLEX</span>
          </div>
        </div>

        <div className="order-details" style={{ background: "rgba(22,24,38,0.5)", border: "1px solid rgba(255,255,255,0.03)", padding: "15px", borderRadius: "12px", marginBottom: "20px" }}>
          <div className="detail-row" style={{ display: "flex", justifyContent: "space-between", margin: "4px 0", fontSize: "0.85rem" }}>
            <span className="detail-label" style={{ color: "#9aa0b4" }}>Transaction ID:</span>
            <span className="detail-value" style={{ color: "#f5f6fa", fontWeight: "bold" }}>{transactionId}</span>
          </div>

          <div className="detail-row" style={{ display: "flex", justifyContent: "space-between", margin: "4px 0", fontSize: "0.85rem" }}>
            <span className="detail-label" style={{ color: "#9aa0b4" }}>Shipping Address:</span>
            <span className="detail-value" style={{ color: "#f5f6fa" }}>{orderDetails.shippingAddress}</span>
          </div>
        </div>

        <div className="button-group" style={{ display: "flex", gap: "10px", justifyContent: "center" }}>
          <button className="home-btn" onClick={handleGoHome} style={{ background: "linear-gradient(135deg, #c5a85c 0%, #8c7237 100%)", color: "#0b0c10", border: "none", padding: "10px 20px", borderRadius: "8px", fontWeight: "bold", cursor: "pointer" }}>
            🏠 Go to Homepage
          </button>

          <button className="orders-btn" onClick={handleViewOrders} style={{ background: "transparent", color: "#c5a85c", border: "1px solid #c5a85c", padding: "10px 20px", borderRadius: "8px", fontWeight: "bold", cursor: "pointer" }}>
            📋 View My Dashboard
          </button>
        </div>

        {!cancelRedirect ? (
          <div style={{ marginTop: "20px" }}>
            <p className="redirect-info" style={{ color: "#9aa0b4", fontSize: "0.85rem", display: "inline-block", marginRight: "10px" }}>
              Redirecting to homepage in {countdown} seconds...
            </p>
            <button 
              onClick={() => setCancelRedirect(true)}
              style={{ background: "rgba(220, 38, 38, 0.2)", border: "1px solid #dc2626", color: "#ef4444", padding: "4px 10px", borderRadius: "6px", fontSize: "0.75rem", cursor: "pointer" }}
            >
              Cancel Auto-Redirect
            </button>
          </div>
        ) : (
          <p style={{ color: "#22c55e", fontSize: "0.85rem", marginTop: "20px" }}>
            ✓ Auto-redirect cancelled. You can now save your QR ticket receipt pass.
          </p>
        )}
      </div>
    </div>
  );
}

export default PaymentSuccess;