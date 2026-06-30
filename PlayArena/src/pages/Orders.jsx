import { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import axios from "axios";
import "../styles/Order.css";

function Orders() {
  const navigate = useNavigate();

  const [selectedOrder, setSelectedOrder] = useState(null);
  const [showTrackModal, setShowTrackModal] = useState(false);
  const [showTicketModal, setShowTicketModal] = useState(false);
  const [ticketOrder, setTicketOrder] = useState(null);
  const [filterStatus, setFilterStatus] = useState("all");
  const [orders, setOrders] = useState([]);
  const [loading, setLoading] = useState(false);

  const userEmail = localStorage.getItem("userEmail") || "guest@globalsportsarena.com";

  const fetchOrders = async () => {
    try {
      setLoading(true);
      const res = await axios.get(`http://localhost:8080/api/orders/my-orders?email=${userEmail}`);
      
      // Parse itemsJson if present
      const formattedOrders = res.data.map(order => {
        let itemsList = [];
        try {
          itemsList = order.itemsJson ? JSON.parse(order.itemsJson) : [];
        } catch (e) {
          console.warn("Could not parse itemsJson for order " + order.id, e);
        }
        return {
          ...order,
          status: order.orderStatus || "confirmed",
          total: order.totalAmount,
          items: itemsList,
          title: itemsList.length > 0 ? itemsList.map(i => i.name).join(", ") : "Sports Order",
          quantity: itemsList.reduce((acc, curr) => acc + (curr.quantity || 1), 0),
          type: itemsList.some(i => i.name.toLowerCase().includes("champions") || i.name.toLowerCase().includes("basketball") || i.name.toLowerCase().includes("tennis")) ? "event" : "product"
        };
      });

      setOrders(formattedOrders);
    } catch (err) {
      console.warn("Failed to fetch orders from backend, checking local storage as fallback", err);
      const orderKey = `orders_${userEmail}`;
      const savedOrders = JSON.parse(localStorage.getItem(orderKey)) || [];
      setOrders(savedOrders);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    if (userEmail) {
      fetchOrders();
    }
  }, [userEmail]);

  const getStatusBadge = (status) => {
    const statusMap = {
      confirmed: { text: "Confirmed", icon: "✅", class: "success" },
      shipped: { text: "Shipped", icon: "📦", class: "warning" },
      delivered: { text: "Delivered", icon: "🎁", class: "success" },
      pending: { text: "Pending", icon: "⏳", class: "info" },
      cancelled: { text: "Cancelled", icon: "❌", class: "danger" },
    };

    const info = statusMap[status] || statusMap.pending;

    return (
      <span className={`status-badge ${info.class}`}>
        {info.icon} {info.text}
      </span>
    );
  };

  const filteredOrders = orders.filter((order) => {
    if (filterStatus === "all") return true;
    return order.status === filterStatus;
  });

  const handleTrackOrder = (order) => {
    setSelectedOrder(order);
    setShowTrackModal(true);
  };

  const handleShowTicket = (order) => {
    setTicketOrder(order);
    setShowTicketModal(true);
  };

  const handleCancelOrder = async (orderId) => {
    if (window.confirm("Are you sure you want to cancel this order?")) {
      try {
        setLoading(true);
        // Call backend PUT mapping to cancel order
        await axios.put(`http://localhost:8080/api/orders/${orderId}/status`, { status: "cancelled" });
        alert("Order cancelled successfully.");
        await fetchOrders();
      } catch (err) {
        console.error("Cancel failed", err);
        alert("Failed to cancel order via API.");
      } finally {
        setLoading(false);
      }
    }
  };

  const handleReorder = (order) => {
    if (order.type === "event") {
      navigate("/event-registration");
    } else {
      navigate("/products");
    }
  };

  const getOrderStats = () => {
    const totalOrders = orders.length;
    const delivered = orders.filter((o) => o.status === "delivered").length;
    const pending = orders.filter(
      (o) => o.status === "pending" || o.status === "shipped" || o.status === "confirmed"
    ).length;
    const totalSpent = orders.reduce((sum, o) => sum + Number(o.total || 0), 0);

    return { totalOrders, delivered, pending, totalSpent };
  };

  const stats = getOrderStats();

  return (
    <div className="orders-page" style={{ background: "#0b0c10", color: "#f5f6fa" }}>
      <div className="orders-hero" style={{ background: "linear-gradient(135deg, #12131c 0%, #0b0c10 100%)" }}>
        <div className="orders-hero-overlay"></div>
        <div className="orders-hero-content">
          <div className="hero-icon">📋</div>
          <h1 style={{ color: "#c5a85c", fontWeight: "800" }}>
            My Event <span className="highlight" style={{ color: "#c5a85c" }}>Passes & Orders</span>
          </h1>
          <p>Verify active event booking tickets, print dynamic passes, and review store orders in MySQL</p>
        </div>
      </div>

      <div className="orders-stats" style={{ gridGap: "15px" }}>
        <div className="stat-card" style={{ background: "#12131c", border: "1px solid rgba(197, 168, 92, 0.15)" }}>
          <div className="stat-icon">📦</div>
          <div className="stat-info">
            <h3 style={{ color: "#c5a85c" }}>{stats.totalOrders}</h3>
            <p>Total Bookings</p>
          </div>
        </div>

        <div className="stat-card" style={{ background: "#12131c", border: "1px solid rgba(197, 168, 92, 0.15)" }}>
          <div className="stat-icon">✅</div>
          <div className="stat-info">
            <h3 style={{ color: "#c5a85c" }}>{stats.delivered}</h3>
            <p>Delivered / Completed</p>
          </div>
        </div>

        <div className="stat-card" style={{ background: "#12131c", border: "1px solid rgba(197, 168, 92, 0.15)" }}>
          <div className="stat-icon">⏳</div>
          <div className="stat-info">
            <h3 style={{ color: "#c5a85c" }}>{stats.pending}</h3>
            <p>Active Passes</p>
          </div>
        </div>

        <div className="stat-card" style={{ background: "#12131c", border: "1px solid rgba(197, 168, 92, 0.15)" }}>
          <div className="stat-icon">💰</div>
          <div className="stat-info">
            <h3 style={{ color: "#c5a85c" }}>₹{stats.totalSpent.toLocaleString()}</h3>
            <p>Total Spending</p>
          </div>
        </div>
      </div>

      <div className="orders-filter">
        <div className="filter-tabs">
          {["all", "confirmed", "shipped", "delivered", "pending"].map((status) => (
            <button
              key={status}
              className={`filter-tab ${filterStatus === status ? "active" : ""}`}
              onClick={() => setFilterStatus(status)}
              style={{
                border: "1px solid rgba(197,168,92,0.15)",
                background: filterStatus === status ? "linear-gradient(135deg, #c5a85c 0%, #8c7237 100%)" : "transparent",
                color: filterStatus === status ? "#0b0c10" : "#9aa0b4"
              }}
            >
              {status === "all"
                ? "All Orders"
                : status.charAt(0).toUpperCase() + status.slice(1)}
            </button>
          ))}
        </div>
      </div>

      <div className="orders-list">
        {loading ? (
          <p style={{ textAlign: "center", color: "#c5a85c" }}>Syncing orders with server...</p>
        ) : filteredOrders.length === 0 ? (
          <div className="empty-orders" style={{ background: "#12131c", border: "1px dashed rgba(197,168,92,0.2)" }}>
            <div className="empty-icon">📭</div>
            <h3>No Active Orders</h3>
            <p>No transactions or registered passes found for your account.</p>

            <div className="empty-actions">
              <button onClick={() => navigate("/sports-categories")} className="empty-btn">
                🎟️ Book Tournaments
              </button>
              <button onClick={() => navigate("/products")} className="empty-btn">
                🛒 Shop Products
              </button>
            </div>
          </div>
        ) : (
          filteredOrders.map((order) => (
            <div key={order.id} className="order-card" style={{ background: "#12131c", border: "1px solid rgba(197,168,92,0.2)" }}>
              <div className="order-header" style={{ borderBottom: "1px solid rgba(255,255,255,0.05)", paddingBottom: "15px" }}>
                <div className="order-id">
                  <span className="id-label" style={{ color: "#9aa0b4" }}>Order Ref:</span>
                  <span className="id-value" style={{ color: "#c5a85c", fontWeight: "bold" }}>#ORD-{order.id}</span>
                </div>
                {getStatusBadge(order.status)}
              </div>

              <div className="order-body" style={{ padding: "20px 0" }}>
                <div className="order-image">
                  <span className="product-icon" style={{ fontSize: "2.5rem" }}>
                    {order.type === "event" ? "🎫" : "👟"}
                  </span>
                </div>

                <div className="order-details">
                  <h3 style={{ color: "#f5f6fa", fontSize: "1.2rem", margin: "0 0 10px 0" }}>{order.title}</h3>
                  <div className="order-info">
                    <span className="info-icon">📅</span>
                    <span>Order Date: {new Date(order.orderDate).toLocaleString()}</span>
                  </div>
                  <div className="order-info">
                    <span className="info-icon">📍</span>
                    <span>Shipping Address: {order.shippingAddress}</span>
                  </div>
                  <div className="order-info">
                    <span className="info-icon">📞</span>
                    <span>Contact Phone: {order.customerPhone}</span>
                  </div>
                </div>

                <div className="order-price" style={{ textAlign: "right" }}>
                  <div className="total-amount" style={{ color: "#c5a85c", fontSize: "1.4rem", fontWeight: "800" }}>
                    ₹{Number(order.total || 0).toLocaleString()}
                  </div>
                  <div style={{ color: "#22c55e", fontSize: "0.8rem", marginTop: "4px" }}>
                    💎 Earned: {order.nxlCoinsEarned} NXL
                  </div>
                  {order.nxlCoinsUsed > 0 && (
                    <div style={{ color: "#ef4444", fontSize: "0.8rem" }}>
                      💎 Redeemed: {order.nxlCoinsUsed} NXL
                    </div>
                  )}
                </div>
              </div>

              <div className="order-footer" style={{ borderTop: "1px solid rgba(255,255,255,0.05)", paddingTop: "15px", display: "flex", gap: "10px", justifyContent: "flex-end" }}>
                {order.status !== "cancelled" ? (
                  <>
                    <button
                      className="footer-btn track-btn"
                      onClick={() => handleTrackOrder(order)}
                      style={{ background: "rgba(255,255,255,0.05)", border: "1px solid rgba(255,255,255,0.15)", color: "#f5f6fa" }}
                    >
                      🚚 Track Status
                    </button>

                    {order.type === "event" && (
                      <button
                        className="footer-btn track-btn"
                        onClick={() => handleShowTicket(order)}
                        style={{ background: "rgba(197, 168, 92, 0.1)", border: "1px solid #c5a85c", color: "#c5a85c", fontWeight: "bold" }}
                      >
                        🎟️ Print QR Pass
                      </button>
                    )}

                    {order.status === "pending" && (
                      <button
                        className="footer-btn cancel-btn"
                        onClick={() => handleCancelOrder(order.id)}
                        style={{ background: "rgba(239, 68, 68, 0.15)", border: "1px solid #ef4444", color: "#f87171" }}
                      >
                        ❌ Cancel Booking
                      </button>
                    )}

                    <button
                      className="footer-btn reorder-btn"
                      onClick={() => handleReorder(order)}
                      style={{ background: "linear-gradient(135deg, #c5a85c 0%, #8c7237 100%)", color: "#0b0c10", border: "none", fontWeight: "bold" }}
                    >
                      🔄 Reorder
                    </button>
                  </>
                ) : (
                  <div className="cancelled-info" style={{ color: "#f87171", fontSize: "0.85rem" }}>
                    ❌ Order Cancelled. Refund processed to customer account.
                  </div>
                )}
              </div>
            </div>
          ))
        )}
      </div>

      {/* Dynamic QR Event Pass Modal */}
      {showTicketModal && ticketOrder && (
        <div className="modal-overlay" onClick={() => setShowTicketModal(false)} style={{ background: "rgba(0,0,0,0.85)" }}>
          <div className="track-modal" onClick={(e) => e.stopPropagation()} style={{ background: "#12131c", border: "1px solid rgba(197,168,92,0.25)", color: "#fff", maxWidth: "480px" }}>
            <div className="modal-header" style={{ borderBottom: "1px solid rgba(197,168,92,0.25)" }}>
              <div className="modal-icon">🎫</div>
              <h3 style={{ color: "#c5a85c" }}>Dynamic QR Entry Ticket</h3>
              <button className="modal-close" onClick={() => setShowTicketModal(false)}>×</button>
            </div>

            <div className="modal-body" style={{ textAlign: "center", padding: "20px" }}>
              <p style={{ fontSize: "0.9rem", color: "#9aa0b4", marginBottom: "15px" }}>
                Present this scanned pass at the GLOBAL SPORTS ARENA sports complex check-in desk for entry.
              </p>
              
              <div style={{ background: "#0b0c10", border: "1px dashed rgba(197,168,92,0.35)", padding: "20px", borderRadius: "16px", display: "inline-block", margin: "10px auto" }}>
                <img 
                  src={`https://api.qrserver.com/v1/create-qr-code/?size=170x170&color=c5a85c&bgcolor=12131c&data=${encodeURIComponent(
                    JSON.stringify({
                      orderId: ticketOrder.id,
                      address: ticketOrder.shippingAddress,
                      phone: ticketOrder.customerPhone,
                      status: "VALID_ENTRY_PASS",
                      platform: "GLOBAL_SPORTS_ARENA"
                    })
                  )}`}
                  alt="Entry Ticket QR"
                  style={{ border: "2px solid #c5a85c", borderRadius: "10px", background: "#12131c", padding: "6px" }}
                />
                <h4 style={{ color: "#c5a85c", margin: "12px 0 4px 0", fontSize: "1.1rem" }}>#ORD-{ticketOrder.id}</h4>
                <span style={{ fontSize: "0.75rem", color: "#9aa0b4", textTransform: "uppercase" }}>{ticketOrder.title}</span>
              </div>

              <div style={{ textAlign: "left", background: "rgba(255,255,255,0.03)", padding: "12px", borderRadius: "8px", marginTop: "15px", fontSize: "0.85rem" }}>
                <div><strong>Registered Email:</strong> {userEmail}</div>
                <div><strong>Phone Number:</strong> {ticketOrder.customerPhone}</div>
                <div><strong>Registration Date:</strong> {new Date(ticketOrder.orderDate).toLocaleDateString()}</div>
              </div>
            </div>

            <div className="modal-footer" style={{ borderTop: "1px solid rgba(255,255,255,0.05)" }}>
              <button className="close-modal-btn" onClick={() => setShowTicketModal(false)} style={{ background: "linear-gradient(135deg, #c5a85c 0%, #8c7237 100%)", color: "#0b0c10", border: "none" }}>
                Close Event Pass
              </button>
            </div>
          </div>
        </div>
      )}

      {/* Track status modal */}
      {showTrackModal && selectedOrder && (
        <div className="modal-overlay" onClick={() => setShowTrackModal(false)} style={{ background: "rgba(0,0,0,0.8)" }}>
          <div className="track-modal" onClick={(e) => e.stopPropagation()} style={{ background: "#12131c", border: "1px solid rgba(197,168,92,0.25)", color: "#fff" }}>
            <div className="modal-header">
              <div className="modal-icon">🚚</div>
              <h3 style={{ color: "#c5a85c" }}>Track Order - #ORD-{selectedOrder.id}</h3>
              <button className="modal-close" onClick={() => setShowTrackModal(false)}>×</button>
            </div>

            <div className="modal-body">
              <div className="tracking-info">
                <div className="tracking-id" style={{ color: "#9aa0b4" }}>
                  <strong>Courier ID:</strong> GLOBAL-SPORTS-ARENA-LOGISTICS-{selectedOrder.id}
                </div>

                <div className="tracking-steps">
                  <div className="step active">
                    <div className="step-icon" style={{ background: "#c5a85c", color: "#0b0c10" }}>✓</div>
                    <div className="step-info">
                      <h4 style={{ color: "#c5a85c" }}>Order Confirmed</h4>
                      <p style={{ color: "#9aa0b4" }}>Your dynamic transaction has been verified securely in MySQL</p>
                    </div>
                  </div>

                  <div className={`step ${(selectedOrder.status === "shipped" || selectedOrder.status === "delivered") ? "active" : ""}`}>
                    <div className="step-icon">📦</div>
                    <div className="step-info">
                      <h4>Order Dispatched</h4>
                      <p>Your merchandise package has been handed over to courier</p>
                    </div>
                  </div>

                  <div className={`step ${selectedOrder.status === "delivered" ? "active" : ""}`}>
                    <div className="step-icon">🎁</div>
                    <div className="step-info">
                      <h4>Completed / Delivered</h4>
                      <p>Order delivered successfully or event checked-in</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div className="modal-footer">
              <button className="close-modal-btn" onClick={() => setShowTrackModal(false)}>
                Close
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}

export default Orders;