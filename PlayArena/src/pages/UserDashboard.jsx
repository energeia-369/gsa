import "../styles/UserDashboard.css";
import { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import axios from "axios";

function UserDashboard() {
  const navigate = useNavigate();

  const [user, setUser] = useState(null);

  useEffect(() => {
    const userEmail = localStorage.getItem("userEmail");
    const role = localStorage.getItem("userRole");

    if (!userEmail) {
      navigate("/login");
      return;
    }

    if (role === "ADMIN") {
      navigate("/admin-dashboard");
      return;
    }

    axios
      .get(`http://localhost:8080/api/user/profile?email=${userEmail}`, { timeout: 2000 })
      .then((response) => {
        if (response.data && response.data.email) {
          setUser(response.data);
        } else {
          throw new Error("Invalid or empty profile data received from backend");
        }
      })
      .catch((error) => {
        console.log("Profile Error, loading local fallback:", error);
        
        let nxlCoins = 50;
        try {
          const coins = localStorage.getItem("nxlCoins");
          if (coins) {
            nxlCoins = Number(coins) || 50;
          }
        } catch (e) {
          console.error("Error reading nxlCoins:", e);
        }

        let eventsJoined = 0;
        try {
          const events = localStorage.getItem("eventsJoined");
          if (events) {
            eventsJoined = Number(events) || 0;
          }
        } catch (e) {
          console.error("Error reading eventsJoined:", e);
        }

        let totalOrders = 0;
        try {
          const orderKey = `orders_${userEmail}`;
          const ordersStr = localStorage.getItem(orderKey);
          if (ordersStr) {
            const userOrders = JSON.parse(ordersStr);
            if (Array.isArray(userOrders)) {
              totalOrders = userOrders.length;
            }
          }
        } catch (e) {
          console.error("Error parsing user orders:", e);
        }

        let fullName = "User";
        try {
          if (userEmail) {
            if (userEmail === "admin@globalsportsarena.com") {
              fullName = "Admin User";
            } else {
              const namePart = userEmail.split("@")[0];
              if (namePart) {
                fullName = namePart.charAt(0).toUpperCase() + namePart.slice(1);
              }
            }
          }
        } catch (e) {
          console.error("Error formatting name:", e);
        }

        setUser({
          fullName: fullName,
          email: userEmail || "",
          walletBalance: 0,
          credits: nxlCoins,
          totalOrders: totalOrders,
          eventsJoined: eventsJoined
        });
      });
  }, [navigate]);

  const handleEditProfile = () => {
    alert("Edit Profile feature coming soon");
  };

  const handleBrowseEvents = () => {
    navigate("/event-registration");
  };

  const handleLogout = () => {
    localStorage.removeItem("token");
    localStorage.removeItem("userEmail");
    alert("Logged out successfully");
    navigate("/login");
  };

  if (!user) {
    return <div className="dashboard-loading">Loading Dashboard...</div>;
  }

  return (
    <div className="dashboard-page">
      <div className="dashboard-hero">
        <div className="dashboard-overlay"></div>

        <div className="dashboard-hero-content">
          <div className="welcome-badge">Welcome Back!</div>
          <h1>User Dashboard</h1>
          <p>Manage your account, track orders, and view upcoming events</p>
        </div>
      </div>

      <div className="profile-card">
        <div className="profile-left">
          <div className="profile-avatar">{user.fullName?.charAt(0)}</div>

          <div className="profile-details">
            <h2>{user.fullName}</h2>
            <p>{user.email}</p>
            <span>Member since 2026</span>
          </div>
        </div>

        <button className="edit-profile-btn" onClick={handleEditProfile}>
          Edit Profile →
        </button>
      </div>

      <div className="dashboard-grid">
        <div className="dashboard-card">
          <div className="card-icon">💰</div>

          <div className="card-content">
            <h3>Wallet Balance</h3>
            <h2>₹{user.walletBalance || 0}</h2>
            <p>Recharge wallet and manage your balance</p>
          </div>

          <button className="card-btn" onClick={() => navigate("/wallet")}>
            Wallet
          </button>
        </div>

        <div className="dashboard-card">
          <div className="card-icon">💎</div>

          <div className="card-content">
            <h3>NXL Credits</h3>
            <h2>{user.credits || 0}</h2>
            <p>Redeem credits for rewards and offers</p>
          </div>

          <button className="card-btn" onClick={() => navigate("/credits")}>
            Credits
          </button>
        </div>

        <div className="dashboard-card">
          <div className="card-icon">📦</div>

          <div className="card-content">
            <h3>Total Orders</h3>
            <h2>{user.totalOrders || 0}</h2>
            <p>View your sports product orders</p>
          </div>

          <button className="card-btn" onClick={() => navigate("/orders")}>
            Orders
          </button>
        </div>

        <div className="dashboard-card">
          <div className="card-icon">🏆</div>

          <div className="card-content">
            <h3>Events Joined</h3>
            <h2>{user.eventsJoined || 0}</h2>
            <p>Browse and register for tournaments</p>
          </div>

          <button className="card-btn" onClick={handleBrowseEvents}>
            Browse Events
          </button>
        </div>
      </div>

      <div
        style={{
          display: "flex",
          justifyContent: "center",
          paddingBottom: "50px",
        }}
      >
        <button className="edit-profile-btn" onClick={handleLogout}>
          Logout
        </button>
      </div>
    </div>
  );
}

export default UserDashboard;