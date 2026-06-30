import { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import axios from "axios";
import "../styles/AdminDashboard.css";

function AdminDashboard() {
  const navigate = useNavigate();

  // Dynamic lists from database
  const [orders, setOrders] = useState([]);
  const [tournaments, setTournaments] = useState([]);
  const [enquiries, setEnquiries] = useState([]);
  const [subscribers, setSubscribers] = useState([]);
  const [allUsers, setAllUsers] = useState([]);

  // User editing states
  const [isEditingUser, setIsEditingUser] = useState(false);
  const [editUserId, setEditUserId] = useState(null);
  const [editUserFullName, setEditUserFullName] = useState("");
  const [editUserEmail, setEditUserEmail] = useState("");
  const [editUserPhone, setEditUserPhone] = useState("");
  const [editUserRole, setEditUserRole] = useState("USER");

  // Loading states
  const [loading, setLoading] = useState(false);

  // Tournament Form fields (for Add/Edit CRUD)
  const [isEditingEvent, setIsEditingEvent] = useState(false);
  const [editEventId, setEditEventId] = useState(null);
  const [eventName, setEventName] = useState("");
  const [eventSport, setEventSport] = useState("Cricket");
  const [eventDate, setEventDate] = useState("");
  const [eventVenue, setEventVenue] = useState("");
  const [eventFee, setEventFee] = useState("");
  const [eventMaxTeams, setEventMaxTeams] = useState("16");
  const [eventCurrTeams, setEventCurrTeams] = useState("0");

  // NXL Wallet Adjustment fields
  const [adjustEmail, setAdjustEmail] = useState("");
  const [adjustAmount, setAdjustAmount] = useState("");
  const [adjustAction, setAdjustAction] = useState("ADD");
  const [adjustLoading, setAdjustLoading] = useState(false);

  // NXL Reward Sender fields
  const [rewardUserId, setRewardUserId] = useState("");
  const [rewardUserEmail, setRewardUserEmail] = useState("");
  const [rewardAmount, setRewardAmount] = useState("");
  const [rewardReason, setRewardReason] = useState("LOYALTY_REWARD");
  const [rewardMessage, setRewardMessage] = useState("");
  const [rewardLoading, setRewardLoading] = useState(false);
  const [rewardHistory, setRewardHistory] = useState([]);

  // Global Destinations Manager
  const [customDestinations, setCustomDestinations] = useState(() => {
    try { return JSON.parse(localStorage.getItem("globalsportsarena_custom_destinations") || "[]"); }
    catch { return []; }
  });
  const [destCountry, setDestCountry] = useState("");
  const [destImageUrl, setDestImageUrl] = useState("");
  const [destDate, setDestDate] = useState("");
  const [destCity, setDestCity] = useState("");
  const [destRegion, setDestRegion] = useState("");

  // Security guard check
  useEffect(() => {
    const token = localStorage.getItem("token");
    const role = localStorage.getItem("userRole");

    if (!token || role !== "ADMIN") {
      alert("Access Denied: Admin login required!");
      navigate("/login");
    }
  }, [navigate]);

  // Load all dynamic records from Spring Boot MySQL REST endpoints
  const loadDashboardData = async () => {
    try {
      setLoading(true);
      
      // 1. Fetch Orders from MySQL
      const ordersRes = await axios.get("http://localhost:8080/api/orders/all");
      const formattedOrders = ordersRes.data.map(order => {
        let itemsList = [];
        try {
          itemsList = order.itemsJson ? JSON.parse(order.itemsJson) : [];
        } catch (e) {
          console.warn("Could not parse itemsJson for admin order " + order.id, e);
        }
        return {
          ...order,
          status: order.orderStatus || "confirmed",
          total: order.totalAmount,
          items: itemsList,
          title: itemsList.length > 0 ? itemsList.map(i => i.name).join(", ") : "Sports Product Order"
        };
      });
      setOrders(formattedOrders);

      // 2. Fetch Active Tournaments
      const tourRes = await axios.get("http://localhost:8080/api/tournaments");
      setTournaments(tourRes.data || []);

      // 3. Fetch Enquiries / Contact submissions
      const contactRes = await axios.get("http://localhost:8080/api/contact");
      setEnquiries(contactRes.data || []);

      // 4. Fetch Newsletter Subscribers
      const subRes = await axios.get("http://localhost:8080/api/newsletter/subscribers");
      setSubscribers(subRes.data || []);

      // 5. Fetch all users (customers and admins)
      const usersRes = await axios.get("http://localhost:8080/api/user/all");
      setAllUsers(usersRes.data || []);

    } catch (err) {
      console.error("Failed to load admin dashboard data from backend", err);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    loadDashboardData();
  }, []);

  // Update order status in dynamic database
  const handleOrderStatusChange = async (orderId, newStatus) => {
    try {
      setLoading(true);
      await axios.put(`http://localhost:8080/api/orders/${orderId}/status`, { status: newStatus });
      alert(`Order #ORD-${orderId} successfully updated to ${newStatus.toUpperCase()}`);
      await loadDashboardData();
    } catch (err) {
      console.error("Order status update failed", err);
      alert("Failed to update status. Server error.");
    } finally {
      setLoading(false);
    }
  };

  // Create or Update Tournament (CRUD)
  const handleSaveTournament = async (e) => {
    e.preventDefault();
    if (!eventName || !eventVenue || !eventFee) {
      alert("Please fill in all required event details");
      return;
    }

    const payload = {
      name: eventName,
      sport: eventSport,
      date: eventDate || "TBD",
      venue: eventVenue,
      registrationFee: parseFloat(eventFee),
      maxTeams: parseInt(eventMaxTeams),
      currentTeams: parseInt(eventCurrTeams)
    };

    try {
      setLoading(true);
      if (isEditingEvent) {
        await axios.put(`http://localhost:8080/api/tournaments/${editEventId}`, payload);
        alert("Tournament updated successfully!");
      } else {
        await axios.post("http://localhost:8080/api/tournaments", payload);
        alert("New Tournament created successfully!");
      }
      
      // Reset form
      setEventName("");
      setEventVenue("");
      setEventDate("");
      setEventFee("");
      setEventMaxTeams("16");
      setEventCurrTeams("0");
      setIsEditingEvent(false);
      setEditEventId(null);

      // Reload lists
      await loadDashboardData();
    } catch (err) {
      console.error("Failed to save tournament", err);
      alert("Error saving tournament to database.");
    } finally {
      setLoading(false);
    }
  };

  // Edit tournament trigger (Load details into form)
  const handleEditClick = (t) => {
    setIsEditingEvent(true);
    setEditEventId(t.id);
    setEventName(t.name);
    setEventSport(t.sport);
    setEventDate(t.date);
    setEventVenue(t.venue);
    setEventFee(t.registrationFee.toString());
    setEventMaxTeams(t.maxTeams.toString());
    setEventCurrTeams(t.currentTeams.toString());
    document.querySelector(".event-form-section")?.scrollIntoView({ behavior: "smooth" });
  };

  // Delete Tournament (CRUD)
  const handleDeleteTournament = async (id) => {
    if (window.confirm("Are you sure you want to delete this tournament permanently?")) {
      try {
        setLoading(true);
        await axios.delete(`http://localhost:8080/api/tournaments/${id}`);
        alert("Tournament deleted successfully.");
        await loadDashboardData();
      } catch (err) {
        console.error("Delete failed", err);
        alert("Failed to delete tournament.");
      } finally {
        setLoading(false);
      }
    }
  };

  // NXL Wallet Adjustments
  const handleAdjustWallet = async (e) => {
    e.preventDefault();
    if (!adjustEmail || !adjustAmount) {
      alert("Email and adjustment amount are required");
      return;
    }

    try {
      setAdjustLoading(true);
      const payload = {
        email: adjustEmail,
        amount: parseInt(adjustAmount),
        action: adjustAction
      };

      await axios.post("http://localhost:8080/api/wallet/admin/adjust", payload);
      alert(`Success! Successfully processed adjustment of ${adjustAmount} NXL coins for ${adjustEmail}.`);
      setAdjustEmail("");
      setAdjustAmount("");
      await loadDashboardData();
    } catch (err) {
      console.error("Adjustment failed", err);
      alert("Failed to process adjustment. Please verify user email address is valid.");
    } finally {
      setAdjustLoading(false);
    }
  };

  // Send NXL Reward to a selected user
  const handleSendReward = async (e) => {
    e.preventDefault();
    if (!rewardUserEmail || !rewardAmount || parseInt(rewardAmount) <= 0) {
      alert("Please select a user and enter a valid reward amount.");
      return;
    }

    const reasonLabels = {
      LOYALTY_REWARD: "Loyalty Reward Bonus",
      WIN_BONUS: "Tournament Win Bonus",
      REFERRAL_BONUS: "Referral Bonus Credit",
      PARTICIPATION_BONUS: "Event Participation Bonus",
      SPECIAL_GIFT: "Special Gift from GLOBAL SPORTS ARENA",
      PROMOTIONAL_CREDIT: "Promotional Credit",
    };

    try {
      setRewardLoading(true);
      const payload = {
        email: rewardUserEmail,
        amount: parseInt(rewardAmount),
        action: "ADD"
      };

      await axios.post("http://localhost:8080/api/wallet/admin/adjust", payload);

      // Log to local reward history
      const newEntry = {
        id: Date.now(),
        userName: allUsers.find(u => u.email === rewardUserEmail)?.fullName || rewardUserEmail,
        email: rewardUserEmail,
        amount: parseInt(rewardAmount),
        reason: reasonLabels[rewardReason] || rewardReason,
        message: rewardMessage,
        sentAt: new Date().toLocaleString()
      };
      setRewardHistory(prev => [newEntry, ...prev]);

      alert(`🎉 Success! ${rewardAmount} NXL coins sent to ${rewardUserEmail} as "${reasonLabels[rewardReason]}"`);

      // Reset form
      setRewardUserId("");
      setRewardUserEmail("");
      setRewardAmount("");
      setRewardReason("LOYALTY_REWARD");
      setRewardMessage("");
      await loadDashboardData();
    } catch (err) {
      console.error("Reward send failed", err);
      alert("Failed to send reward. Verify the user exists in the database.");
    } finally {
      setRewardLoading(false);
    }
  };

  // User edit triggers
  const handleEditUserClick = (u) => {
    setIsEditingUser(true);
    setEditUserId(u.id);
    setEditUserFullName(u.fullName);
    setEditUserEmail(u.email);
    setEditUserPhone(u.phoneNumber || "");
    setEditUserRole(u.role || "USER");
    document.querySelector(".user-edit-section")?.scrollIntoView({ behavior: "smooth" });
  };

  // Save edited user profile
  const handleSaveUser = async (e) => {
    e.preventDefault();
    if (!editUserFullName || !editUserEmail) {
      alert("Name and email are required fields.");
      return;
    }

    try {
      setLoading(true);
      const payload = {
        fullName: editUserFullName,
        email: editUserEmail,
        phoneNumber: editUserPhone,
        role: editUserRole
      };
      await axios.put(`http://localhost:8080/api/user/${editUserId}`, payload);
      alert("User profile successfully updated!");
      setIsEditingUser(false);
      setEditUserId(null);
      setEditUserFullName("");
      setEditUserEmail("");
      setEditUserPhone("");
      setEditUserRole("USER");
      await loadDashboardData();
    } catch (err) {
      console.error("User profile update failed", err);
      const serverMsg = err.response?.data?.message || err.response?.data || err.message;
      alert(`Failed to update user profile: ${serverMsg}`);
    } finally {
      setLoading(false);
    }
  };

  // Delete User
  const handleDeleteUser = async (id, role) => {
    const confirmMsg = `Are you sure you want to delete this ${role.toLowerCase()} account permanently? This action cannot be undone.`;
    if (window.confirm(confirmMsg)) {
      try {
        setLoading(true);
        await axios.delete(`http://localhost:8080/api/user/${id}`);
        alert(`${role} account deleted successfully.`);
        await loadDashboardData();
      } catch (err) {
        console.error("Delete user failed", err);
        alert("Failed to delete account. Backend constraint error.");
      } finally {
        setLoading(false);
      }
    }
  };

  // Calculate dynamics stats
  const totalSales = orders.reduce((sum, o) => sum + Number(o.total || 0), 0);
  const totalCoinsIssued = orders.reduce((sum, o) => sum + Number(o.nxlCoinsEarned || 0), 0);
  const uniqueUsers = [...new Set(orders.map(o => o.userId))];

  // Add a new country destination to the Home page carousel
  const handleAddDestination = (e) => {
    e.preventDefault();
    if (!destCountry.trim() || !destImageUrl.trim() || !destDate.trim() || !destCity.trim()) {
      alert("Country name, image URL, date range, and city are required.");
      return;
    }
    const newDest = {
      id: Date.now(),
      country: destCountry.toUpperCase().trim(),
      image: destImageUrl.trim(),
      date: destDate.trim(),
      city: destCity.trim(),
      region: destRegion.trim() || destCity.trim(),
      link: "#"
    };
    const updated = [...customDestinations, newDest];
    localStorage.setItem("globalsportsarena_custom_destinations", JSON.stringify(updated));
    // Trigger storage event so Home.jsx reacts in the same tab
    window.dispatchEvent(new StorageEvent("storage", { key: "globalsportsarena_custom_destinations" }));
    setCustomDestinations(updated);
    setDestCountry(""); setDestImageUrl(""); setDestDate(""); setDestCity(""); setDestRegion("");
    alert(`✅ "${newDest.country}" added to the Global Event Destinations carousel!`);
  };

  // Remove a custom destination
  const handleDeleteDestination = (id) => {
    if (!window.confirm("Remove this destination from the Home page carousel?")) return;
    const updated = customDestinations.filter(d => d.id !== id);
    localStorage.setItem("globalsportsarena_custom_destinations", JSON.stringify(updated));
    window.dispatchEvent(new StorageEvent("storage", { key: "globalsportsarena_custom_destinations" }));
    setCustomDestinations(updated);
  };

  return (
    <div className="admin-dashboard" style={{ background: "#0b0c10", color: "#f5f6fa" }}>
      {/* Header */}
      <div className="admin-header" style={{ borderBottom: "1px solid rgba(197, 168, 92, 0.2)" }}>
        <div className="header-left">
          <div className="admin-badge" style={{ background: "linear-gradient(135deg, #c5a85c 0%, #8c7237 100%)", color: "#0b0c10", fontWeight: "bold" }}>
            🛡️ Administrative Core
          </div>
          <h1>System Operations</h1>
          <p>Real-time tournament CRUD controls, NXL ledger wallets adjustments, and synchronized orders listings in MySQL</p>
        </div>
      </div>

      {/* Dynamic KPI Stats Grid */}
      <div className="stats-grid" style={{ gridGap: "15px", marginTop: "30px" }}>
        <div className="stat-card" style={{ background: "#12131c", border: "1px solid rgba(197, 168, 92, 0.15)" }}>
          <div className="stat-icon">💰</div>
          <div className="stat-info">
            <h3>Live Total Sales</h3>
            <p className="stat-value" style={{ color: "#22c55e" }}>₹{totalSales.toLocaleString()}</p>
            <span className="stat-change">Synchronized DB</span>
          </div>
        </div>

        <div className="stat-card" style={{ background: "#12131c", border: "1px solid rgba(197, 168, 92, 0.15)" }}>
          <div className="stat-icon">💎</div>
          <div className="stat-info">
            <h3>Total NXL Issued</h3>
            <p className="stat-value" style={{ color: "#c5a85c" }}>{totalCoinsIssued} Coins</p>
            <span className="stat-change">Loyalty Ledger</span>
          </div>
        </div>

        <div className="stat-card" style={{ background: "#12131c", border: "1px solid rgba(197, 168, 92, 0.15)" }}>
          <div className="stat-icon">👥</div>
          <div className="stat-info">
            <h3>Active Customers</h3>
            <p className="stat-value">{uniqueUsers.length} Users</p>
            <span className="stat-change">Logged Profile</span>
          </div>
        </div>

        <div className="stat-card" style={{ background: "#12131c", border: "1px solid rgba(197, 168, 92, 0.15)" }}>
          <div className="stat-icon">📧</div>
          <div className="stat-info">
            <h3>Newsletter Subscribers</h3>
            <p className="stat-value" style={{ color: "#38bdf8" }}>{subscribers.length} Emails</p>
            <span className="stat-change">Active Sign-ups</span>
          </div>
        </div>
      </div>

      <div className="admin-content" style={{ display: "grid", gridTemplateColumns: "1.2fr 0.8fr", gap: "30px", marginTop: "40px" }}>
        
        {/* LEFT COLUMN: CRUD FOR TOURNAMENTS & USER TRANSACTIONS TABLE */}
        <div>
          {/* Tournament event CRUD management form */}
          <div className="admin-card event-form-section" style={{ background: "#12131c", border: "1px solid rgba(197, 168, 92, 0.2)", borderRadius: "20px", padding: "25px", marginBottom: "30px" }}>
            <h2 style={{ color: "#c5a85c", margin: "0 0 20px 0" }}>
              {isEditingEvent ? "✏️ Edit Active Tournament" : "➕ Create Sports Tournament"}
            </h2>
            
            <form onSubmit={handleSaveTournament} style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: "15px" }}>
              <div>
                <label style={{ display: "block", fontSize: "0.85rem", color: "#9aa0b4", marginBottom: "5px" }}>Event Name *</label>
                <input 
                  type="text" 
                  value={eventName} 
                  onChange={(e) => setEventName(e.target.value)} 
                  placeholder="e.g. Cricket Pro League"
                  style={{ width: "100%", padding: "10px", border: "1px solid rgba(197,168,92,0.2)", borderRadius: "8px", background: "#0b0c10", color: "#fff" }}
                  required
                />
              </div>

              <div>
                <label style={{ display: "block", fontSize: "0.85rem", color: "#9aa0b4", marginBottom: "5px" }}>Select Sport Category *</label>
                <select 
                  value={eventSport} 
                  onChange={(e) => setEventSport(e.target.value)} 
                  style={{ width: "100%", padding: "10px", border: "1px solid rgba(197,168,92,0.2)", borderRadius: "8px", background: "#0b0c10", color: "#fff" }}
                >
                  <option value="Cricket">Cricket</option>
                  <option value="Football">Football</option>
                  <option value="Basketball">Basketball</option>
                  <option value="Tennis">Tennis</option>
                  <option value="Badminton">Badminton</option>
                </select>
              </div>

              <div>
                <label style={{ display: "block", fontSize: "0.85rem", color: "#9aa0b4", marginBottom: "5px" }}>Tournament Date & Time</label>
                <input 
                  type="text" 
                  value={eventDate} 
                  onChange={(e) => setEventDate(e.target.value)} 
                  placeholder="e.g. June 15, 2026 | 7:00 PM"
                  style={{ width: "100%", padding: "10px", border: "1px solid rgba(197,168,92,0.2)", borderRadius: "8px", background: "#0b0c10", color: "#fff" }}
                />
              </div>

              <div>
                <label style={{ display: "block", fontSize: "0.85rem", color: "#9aa0b4", marginBottom: "5px" }}>Complex Venue *</label>
                <input 
                  type="text" 
                  value={eventVenue} 
                  onChange={(e) => setEventVenue(e.target.value)} 
                  placeholder="e.g. National Complex Delhi"
                  style={{ width: "100%", padding: "10px", border: "1px solid rgba(197,168,92,0.2)", borderRadius: "8px", background: "#0b0c10", color: "#fff" }}
                  required
                />
              </div>

              <div>
                <label style={{ display: "block", fontSize: "0.85rem", color: "#9aa0b4", marginBottom: "5px" }}>Registration Fee (INR) *</label>
                <input 
                  type="number" 
                  value={eventFee} 
                  onChange={(e) => setEventFee(e.target.value)} 
                  placeholder="999"
                  style={{ width: "100%", padding: "10px", border: "1px solid rgba(197,168,92,0.2)", borderRadius: "8px", background: "#0b0c10", color: "#fff" }}
                  required
                />
              </div>

              <div style={{ display: "flex", gap: "10px" }}>
                <div style={{ flex: 1 }}>
                  <label style={{ display: "block", fontSize: "0.85rem", color: "#9aa0b4", marginBottom: "5px" }}>Max Teams</label>
                  <input 
                    type="number" 
                    value={eventMaxTeams} 
                    onChange={(e) => setEventMaxTeams(e.target.value)}
                    style={{ width: "100%", padding: "10px", border: "1px solid rgba(197,168,92,0.2)", borderRadius: "8px", background: "#0b0c10", color: "#fff" }}
                  />
                </div>
                <div style={{ flex: 1 }}>
                  <label style={{ display: "block", fontSize: "0.85rem", color: "#9aa0b4", marginBottom: "5px" }}>Curr Registered</label>
                  <input 
                    type="number" 
                    value={eventCurrTeams} 
                    onChange={(e) => setEventCurrTeams(e.target.value)}
                    style={{ width: "100%", padding: "10px", border: "1px solid rgba(197,168,92,0.2)", borderRadius: "8px", background: "#0b0c10", color: "#fff" }}
                  />
                </div>
              </div>

              <div style={{ gridColumn: "span 2", display: "flex", gap: "10px", marginTop: "10px" }}>
                <button 
                  type="submit" 
                  style={{
                    background: "linear-gradient(135deg, #c5a85c 0%, #8c7237 100%)",
                    color: "#0b0c10",
                    border: "none",
                    padding: "12px 25px",
                    borderRadius: "8px",
                    fontWeight: "bold",
                    cursor: "pointer",
                    flex: 1
                  }}
                >
                  {isEditingEvent ? "Save Tournament Updates" : "Publish Tournament Live"}
                </button>

                {isEditingEvent && (
                  <button 
                    type="button" 
                    onClick={() => {
                      setIsEditingEvent(false);
                      setEditEventId(null);
                      setEventName("");
                      setEventVenue("");
                      setEventDate("");
                      setEventFee("");
                    }}
                    style={{ background: "rgba(255,255,255,0.05)", border: "1px solid rgba(255,255,255,0.15)", color: "#fff", padding: "12px 20px", borderRadius: "8px", cursor: "pointer" }}
                  >
                    Cancel
                  </button>
                )}
              </div>
            </form>
          </div>

          {/* Active Tournament List for editing */}
          <div className="admin-card" style={{ background: "#12131c", border: "1px solid rgba(197, 168, 92, 0.2)", borderRadius: "20px", padding: "25px", marginBottom: "30px" }}>
            <h2 style={{ color: "#c5a85c", margin: "0 0 20px 0" }}>🏆 Database Tournament Pools</h2>
            
            {tournaments.length === 0 ? (
              <p style={{ color: "#9aa0b4", textAlign: "center" }}>No active tournament pools in the database.</p>
            ) : (
              <div style={{ display: "grid", gap: "10px" }}>
                {tournaments.map((t) => (
                  <div key={t.id} style={{ background: "#0b0c10", border: "1px solid rgba(197,168,92,0.1)", padding: "15px", borderRadius: "12px", display: "flex", justifyContent: "space-between", alignItems: "center" }}>
                    <div>
                      <h4 style={{ margin: "0 0 5px 0", color: "#f5f6fa" }}>{t.name} <span style={{ color: "#c5a85c", fontSize: "0.8rem" }}>(#POOL-{t.id})</span></h4>
                      <div style={{ fontSize: "0.8rem", color: "#9aa0b4" }}>
                        🏸 Sport: <strong>{t.sport}</strong> • 📍 {t.venue} • 💰 Fee: ₹{t.registrationFee}
                      </div>
                      <div style={{ fontSize: "0.8rem", color: "#9aa0b4", marginTop: "2px" }}>
                        👥 Team slots: {t.currentTeams} / {t.maxTeams} slots registered
                      </div>
                    </div>

                    <div style={{ display: "flex", gap: "8px" }}>
                      <button 
                        onClick={() => handleEditClick(t)}
                        style={{ background: "rgba(197,168,92,0.1)", border: "1px solid #c5a85c", color: "#c5a85c", padding: "6px 12px", borderRadius: "6px", fontSize: "0.8rem", cursor: "pointer" }}
                      >
                        ✏️ Edit
                      </button>
                      <button 
                        onClick={() => handleDeleteTournament(t.id)}
                        style={{ background: "rgba(220,38,38,0.15)", border: "1px solid #dc2626", color: "#f87171", padding: "6px 12px", borderRadius: "6px", fontSize: "0.8rem", cursor: "pointer" }}
                      >
                        🗑️ Delete
                      </button>
                    </div>
                  </div>
                ))}
              </div>
            )}
          </div>

          {/* Dynamic DB Order logs */}
          <div className="admin-card" style={{ background: "#12131c", border: "1px solid rgba(197, 168, 92, 0.2)", borderRadius: "20px", padding: "25px" }}>
            <h2 style={{ color: "#c5a85c", margin: "0 0 20px 0" }}>📦 Dynamic User Order Purchases</h2>
            
            {orders.length === 0 ? (
              <p style={{ color: "#9aa0b4", textAlign: "center" }}>No customer orders found in the database.</p>
            ) : (
              <div style={{ overflowX: "auto" }}>
                <table style={{ width: "100%", borderCollapse: "collapse", fontSize: "0.85rem", textAlign: "left" }}>
                  <thead>
                    <tr style={{ borderBottom: "1px solid rgba(197,168,92,0.25)", color: "#c5a85c" }}>
                      <th style={{ padding: "10px" }}>Order Ref</th>
                      <th style={{ padding: "10px" }}>Items Summary</th>
                      <th style={{ padding: "10px" }}>Total Paid</th>
                      <th style={{ padding: "10px" }}>NXL Rewards</th>
                      <th style={{ padding: "10px" }}>Update status</th>
                    </tr>
                  </thead>
                  <tbody>
                    {orders.map((o) => (
                      <tr key={o.id} style={{ borderBottom: "1px solid rgba(255,255,255,0.03)" }}>
                        <td style={{ padding: "12px 10px", fontWeight: "bold" }}>#ORD-{o.id}</td>
                        <td style={{ padding: "12px 10px", color: "#9aa0b4" }}>
                          <strong>{o.title}</strong>
                        </td>
                        <td style={{ padding: "12px 10px", color: "#22c55e", fontWeight: "bold" }}>₹{o.totalAmount}</td>
                        <td style={{ padding: "12px 10px", color: "#c5a85c" }}>💎 +{o.nxlCoinsEarned} / -{o.nxlCoinsUsed}</td>
                        <td style={{ padding: "12px 10px" }}>
                          <select
                            value={o.status}
                            onChange={(e) => handleOrderStatusChange(o.id, e.target.value)}
                            style={{ background: "#0b0c10", color: "#c5a85c", border: "1px solid rgba(197,168,92,0.25)", padding: "4px 8px", borderRadius: "6px", cursor: "pointer" }}
                          >
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="shipped">Shipped</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
                          </select>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            )}
          </div>
        </div>

        {/* RIGHT COLUMN: WALLET ADJUSTMENTS, CONTACT ENQUIRIES, NEWSLETTERS */}
        <div>
          {/* User Wallet adjustment form */}
          <div className="admin-card" style={{ background: "#12131c", border: "1px solid rgba(197, 168, 92, 0.2)", borderRadius: "20px", padding: "25px", marginBottom: "30px" }}>
            <h2 style={{ color: "#c5a85c", margin: "0 0 10px 0" }}>💎 NXL Wallet Adjustment</h2>
            <p style={{ color: "#9aa0b4", fontSize: "0.8rem", margin: "0 0 20px 0" }}>Manually adjust loyalty balances for specific registered users in MySQL</p>

            <form onSubmit={handleAdjustWallet}>
              <div style={{ marginBottom: "12px" }}>
                <label style={{ display: "block", fontSize: "0.85rem", color: "#9aa0b4", marginBottom: "5px" }}>User Email Address *</label>
                <input 
                  type="email" 
                  value={adjustEmail} 
                  onChange={(e) => setAdjustEmail(e.target.value)} 
                  placeholder="user@globalsportsarena.com"
                  style={{ width: "100%", padding: "10px", border: "1px solid rgba(197,168,92,0.2)", borderRadius: "8px", background: "#0b0c10", color: "#fff", boxSizing: "border-box" }}
                  required
                />
              </div>

              <div style={{ marginBottom: "12px" }}>
                <label style={{ display: "block", fontSize: "0.85rem", color: "#9aa0b4", marginBottom: "5px" }}>Coins Amount *</label>
                <input 
                  type="number" 
                  value={adjustAmount} 
                  onChange={(e) => setAdjustAmount(e.target.value)} 
                  placeholder="50"
                  style={{ width: "100%", padding: "10px", border: "1px solid rgba(197,168,92,0.2)", borderRadius: "8px", background: "#0b0c10", color: "#fff", boxSizing: "border-box" }}
                  required
                />
              </div>

              <div style={{ marginBottom: "20px" }}>
                <label style={{ display: "block", fontSize: "0.85rem", color: "#9aa0b4", marginBottom: "5px" }}>Select Action *</label>
                <div style={{ display: "flex", gap: "10px" }}>
                  <label style={{ display: "flex", alignItems: "center", gap: "5px", fontSize: "0.9rem", color: "#22c55e", cursor: "pointer" }}>
                    <input 
                      type="radio" 
                      name="adjust" 
                      value="ADD" 
                      checked={adjustAction === "ADD"} 
                      onChange={() => setAdjustAction("ADD")} 
                    />
                    ➕ Credit / Add Coins
                  </label>
                  <label style={{ display: "flex", alignItems: "center", gap: "5px", fontSize: "0.9rem", color: "#ef4444", cursor: "pointer" }}>
                    <input 
                      type="radio" 
                      name="adjust" 
                      value="SUBTRACT" 
                      checked={adjustAction === "SUBTRACT"} 
                      onChange={() => setAdjustAction("SUBTRACT")} 
                    />
                    ➖ Debit / Deduct Coins
                  </label>
                </div>
              </div>

              <button 
                type="submit" 
                disabled={adjustLoading}
                style={{
                  width: "100%",
                  background: "linear-gradient(135deg, #c5a85c 0%, #8c7237 100%)",
                  color: "#0b0c10",
                  border: "none",
                  padding: "12px",
                  borderRadius: "8px",
                  fontWeight: "bold",
                  cursor: "pointer"
                }}
              >
                {adjustLoading ? "Adjusting balance..." : "Process Adjustment"}
              </button>
            </form>
          </div>

          {/* 🎁 NXL REWARD SENDER PANEL */}
          <div className="admin-card reward-sender-panel" style={{
            background: "linear-gradient(135deg, #12131c 0%, #0f1020 100%)",
            border: "1px solid rgba(197, 168, 92, 0.35)",
            borderRadius: "20px",
            padding: "25px",
            marginBottom: "30px",
            position: "relative",
            overflow: "hidden"
          }}>
            {/* Glow accent */}
            <div style={{
              position: "absolute", top: 0, right: 0,
              width: "120px", height: "120px",
              background: "radial-gradient(circle, rgba(197,168,92,0.12) 0%, transparent 70%)",
              borderRadius: "50%", pointerEvents: "none"
            }} />

            <h2 style={{ color: "#c5a85c", margin: "0 0 4px 0", display: "flex", alignItems: "center", gap: "8px" }}>
              🎁 Send NXL Reward to User
            </h2>
            <p style={{ color: "#9aa0b4", fontSize: "0.8rem", margin: "0 0 22px 0" }}>
              Personally reward a user with NXL credits — for winning, participation, loyalty, or promotions
            </p>

            <form onSubmit={handleSendReward}>

              {/* Step 1: Select User */}
              <div style={{ marginBottom: "16px" }}>
                <label style={{ display: "block", fontSize: "0.82rem", color: "#c5a85c", marginBottom: "6px", fontWeight: "600", letterSpacing: "0.5px" }}>
                  STEP 1 — Select Recipient User
                </label>
                <select
                  value={rewardUserId}
                  onChange={(e) => {
                    const selected = allUsers.find(u => String(u.id) === e.target.value);
                    setRewardUserId(e.target.value);
                    setRewardUserEmail(selected ? selected.email : "");
                  }}
                  style={{ width: "100%", padding: "11px", border: "1px solid rgba(197,168,92,0.3)", borderRadius: "10px", background: "#0b0c10", color: "#fff", boxSizing: "border-box", cursor: "pointer" }}
                  required
                >
                  <option value="">— Choose a registered user —</option>
                  {allUsers.filter(u => u.role !== "ADMIN").map(u => (
                    <option key={u.id} value={u.id}>
                      {u.fullName} ({u.email})
                    </option>
                  ))}
                </select>
                {rewardUserEmail && (
                  <div style={{ marginTop: "6px", padding: "8px 12px", background: "rgba(197,168,92,0.08)", borderRadius: "8px", fontSize: "0.8rem", color: "#c5a85c" }}>
                    ✅ Recipient: <strong>{rewardUserEmail}</strong>
                  </div>
                )}
              </div>

              {/* Step 2: Choose Amount */}
              <div style={{ marginBottom: "16px" }}>
                <label style={{ display: "block", fontSize: "0.82rem", color: "#c5a85c", marginBottom: "8px", fontWeight: "600", letterSpacing: "0.5px" }}>
                  STEP 2 — Select NXL Reward Amount 💎
                </label>
                {/* Preset buttons */}
                <div style={{ display: "flex", gap: "8px", flexWrap: "wrap", marginBottom: "10px" }}>
                  {[50, 100, 250, 500, 1000].map(amt => (
                    <button
                      key={amt}
                      type="button"
                      onClick={() => setRewardAmount(String(amt))}
                      style={{
                        padding: "8px 16px",
                        borderRadius: "20px",
                        border: rewardAmount === String(amt)
                          ? "2px solid #c5a85c"
                          : "1px solid rgba(197,168,92,0.25)",
                        background: rewardAmount === String(amt)
                          ? "rgba(197,168,92,0.2)"
                          : "rgba(197,168,92,0.05)",
                        color: rewardAmount === String(amt) ? "#c5a85c" : "#9aa0b4",
                        fontWeight: "bold",
                        fontSize: "0.85rem",
                        cursor: "pointer",
                        transition: "all 0.2s"
                      }}
                    >
                      💎 {amt}
                    </button>
                  ))}
                </div>
                {/* Custom amount */}
                <input
                  type="number"
                  value={rewardAmount}
                  onChange={(e) => setRewardAmount(e.target.value)}
                  placeholder="Or enter custom amount (e.g. 750)"
                  min="1"
                  style={{ width: "100%", padding: "10px", border: "1px solid rgba(197,168,92,0.2)", borderRadius: "10px", background: "#0b0c10", color: "#fff", boxSizing: "border-box" }}
                  required
                />
              </div>

              {/* Step 3: Reward Reason */}
              <div style={{ marginBottom: "16px" }}>
                <label style={{ display: "block", fontSize: "0.82rem", color: "#c5a85c", marginBottom: "6px", fontWeight: "600", letterSpacing: "0.5px" }}>
                  STEP 3 — Reward Category
                </label>
                <select
                  value={rewardReason}
                  onChange={(e) => setRewardReason(e.target.value)}
                  style={{ width: "100%", padding: "11px", border: "1px solid rgba(197,168,92,0.2)", borderRadius: "10px", background: "#0b0c10", color: "#fff", boxSizing: "border-box", cursor: "pointer" }}
                >
                  <option value="LOYALTY_REWARD">🏅 Loyalty Reward Bonus</option>
                  <option value="WIN_BONUS">🏆 Tournament Win Bonus</option>
                  <option value="REFERRAL_BONUS">👥 Referral Bonus Credit</option>
                  <option value="PARTICIPATION_BONUS">🎽 Event Participation Bonus</option>
                  <option value="SPECIAL_GIFT">🎁 Special Gift from GLOBAL SPORTS ARENA</option>
                  <option value="PROMOTIONAL_CREDIT">📣 Promotional Credit</option>
                </select>
              </div>

              {/* Step 4: Optional Message */}
              <div style={{ marginBottom: "20px" }}>
                <label style={{ display: "block", fontSize: "0.82rem", color: "#c5a85c", marginBottom: "6px", fontWeight: "600", letterSpacing: "0.5px" }}>
                  STEP 4 — Personal Message (Optional)
                </label>
                <textarea
                  value={rewardMessage}
                  onChange={(e) => setRewardMessage(e.target.value)}
                  placeholder="e.g. Congratulations on winning the Cricket Pro League finals! 🏆"
                  rows={3}
                  style={{ width: "100%", padding: "10px", border: "1px solid rgba(197,168,92,0.2)", borderRadius: "10px", background: "#0b0c10", color: "#fff", boxSizing: "border-box", resize: "vertical", fontFamily: "inherit" }}
                />
              </div>

              {/* Preview Banner */}
              {rewardUserEmail && rewardAmount && (
                <div style={{
                  marginBottom: "18px",
                  padding: "14px 18px",
                  background: "linear-gradient(135deg, rgba(197,168,92,0.12) 0%, rgba(197,168,92,0.04) 100%)",
                  border: "1px solid rgba(197,168,92,0.3)",
                  borderRadius: "12px",
                  display: "flex",
                  justifyContent: "space-between",
                  alignItems: "center"
                }}>
                  <div>
                    <div style={{ fontSize: "0.8rem", color: "#9aa0b4" }}>Sending to</div>
                    <div style={{ fontWeight: "700", color: "#f5f6fa" }}>{rewardUserEmail}</div>
                  </div>
                  <div style={{ textAlign: "right" }}>
                    <div style={{ fontSize: "0.8rem", color: "#9aa0b4" }}>Reward Amount</div>
                    <div style={{ fontWeight: "800", color: "#c5a85c", fontSize: "1.3rem" }}>💎 {rewardAmount} NXL</div>
                  </div>
                </div>
              )}

              {/* Send Button */}
              <button
                type="submit"
                disabled={rewardLoading || !rewardUserEmail || !rewardAmount}
                style={{
                  width: "100%",
                  background: (rewardLoading || !rewardUserEmail || !rewardAmount)
                    ? "rgba(197,168,92,0.3)"
                    : "linear-gradient(135deg, #c5a85c 0%, #8c7237 100%)",
                  color: "#0b0c10",
                  border: "none",
                  padding: "14px",
                  borderRadius: "10px",
                  fontWeight: "800",
                  fontSize: "1rem",
                  cursor: (rewardLoading || !rewardUserEmail || !rewardAmount) ? "not-allowed" : "pointer",
                  letterSpacing: "0.5px",
                  transition: "all 0.3s"
                }}
              >
                {rewardLoading ? "⏳ Sending Reward..." : "🎁 Send NXL Reward Now"}
              </button>
            </form>

            {/* Reward Dispatch History */}
            {rewardHistory.length > 0 && (
              <div style={{ marginTop: "25px" }}>
                <h3 style={{ color: "#c5a85c", margin: "0 0 14px 0", fontSize: "0.95rem", borderTop: "1px solid rgba(197,168,92,0.15)", paddingTop: "18px" }}>
                  📜 Reward Dispatch History (This Session)
                </h3>
                <div style={{ display: "grid", gap: "10px" }}>
                  {rewardHistory.map((entry) => (
                    <div key={entry.id} style={{
                      background: "#0b0c10",
                      border: "1px solid rgba(34,197,94,0.2)",
                      borderLeft: "3px solid #22c55e",
                      borderRadius: "10px",
                      padding: "12px 15px",
                      fontSize: "0.82rem"
                    }}>
                      <div style={{ display: "flex", justifyContent: "space-between", alignItems: "flex-start" }}>
                        <div>
                          <div style={{ fontWeight: "700", color: "#f5f6fa", marginBottom: "3px" }}>
                            {entry.userName}
                          </div>
                          <div style={{ color: "#9aa0b4" }}>📧 {entry.email}</div>
                          <div style={{ color: "#9aa0b4", marginTop: "3px" }}>🏷️ {entry.reason}</div>
                          {entry.message && (
                            <div style={{ color: "#9aa0b4", marginTop: "3px", fontStyle: "italic" }}>
                              💬 "{entry.message}"
                            </div>
                          )}
                        </div>
                        <div style={{ textAlign: "right", flexShrink: 0, marginLeft: "12px" }}>
                          <div style={{ color: "#22c55e", fontWeight: "800", fontSize: "1.1rem" }}>
                            +{entry.amount} 💎
                          </div>
                          <div style={{ color: "#9aa0b4", fontSize: "0.75rem", marginTop: "4px" }}>
                            {entry.sentAt}
                          </div>
                        </div>
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            )}
          </div>

          {/* Dynamic DB Enquiries log */}

          <div className="admin-card" style={{ background: "#12131c", border: "1px solid rgba(197, 168, 92, 0.2)", borderRadius: "20px", padding: "25px", marginBottom: "30px" }}>
            <h2 style={{ color: "#c5a85c", margin: "0 0 20px 0" }}>💬 Contact Form Enquiries</h2>
            
            {enquiries.length === 0 ? (
              <p style={{ color: "#9aa0b4", textAlign: "center" }}>No contact form enquiries in database.</p>
            ) : (
              <div style={{ display: "grid", gap: "12px", maxHeight: "300px", overflowY: "auto" }}>
                {enquiries.map((e) => (
                  <div key={e.id} style={{ background: "#0b0c10", border: "1px solid rgba(255,255,255,0.03)", padding: "12px", borderRadius: "10px", fontSize: "0.85rem" }}>
                    <div style={{ display: "flex", justifyContent: "space-between", marginBottom: "5px" }}>
                      <strong>{e.name}</strong>
                      <span style={{ color: "#9aa0b4", fontSize: "0.75rem" }}>{new Date(e.date || Date.now()).toLocaleDateString()}</span>
                    </div>
                    <div style={{ color: "#c5a85c", fontSize: "0.8rem", marginBottom: "5px" }}>📧 {e.email} | Sub: {e.subject}</div>
                    <p style={{ margin: "0", color: "#9aa0b4", lineHeight: "1.4" }}>{e.message}</p>
                  </div>
                ))}
              </div>
            )}
          </div>

          {/* Dynamic DB Newsletter Subscribers */}
          <div className="admin-card" style={{ background: "#12131c", border: "1px solid rgba(197, 168, 92, 0.2)", borderRadius: "20px", padding: "25px" }}>
            <h2 style={{ color: "#c5a85c", margin: "0 0 20px 0" }}>📧 Newsletter Subscribers</h2>
            
            {subscribers.length === 0 ? (
              <p style={{ color: "#9aa0b4", textAlign: "center" }}>No newsletter subscribers in database.</p>
            ) : (
              <div style={{ display: "grid", gap: "8px", maxHeight: "250px", overflowY: "auto" }}>
                {subscribers.map((s) => (
                  <div key={s.id} style={{ background: "#0b0c10", border: "1px solid rgba(255,255,255,0.02)", padding: "10px 15px", borderRadius: "8px", display: "flex", justifyContent: "space-between", alignItems: "center", fontSize: "0.85rem" }}>
                    <span>{s.email}</span>
                    <span style={{ color: "#22c55e", fontWeight: "bold", fontSize: "0.75rem" }}>{s.status}</span>
                  </div>
                ))}
              </div>
            )}
          </div>

        </div>

      </div>

      {/* User and Admin Account Management Section */}
      <div className="admin-accounts-section" style={{ marginTop: "40px" }}>
        
        {/* Edit User Form */}
        {isEditingUser && (
          <div className="admin-card user-edit-section" style={{ background: "#12131c", border: "2px solid var(--accent-gold)", borderRadius: "20px", padding: "25px", marginBottom: "30px" }}>
            <h2 style={{ color: "#c5a85c", margin: "0 0 20px 0" }}>✏️ Edit Account Profile: {editUserFullName}</h2>
            <form onSubmit={handleSaveUser} style={{ display: "grid", gridTemplateColumns: "repeat(auto-fit, minmax(200px, 1fr))", gap: "15px" }}>
              <div>
                <label style={{ display: "block", fontSize: "0.85rem", color: "#9aa0b4", marginBottom: "5px" }}>Full Name *</label>
                <input 
                  type="text" 
                  value={editUserFullName} 
                  onChange={(e) => setEditUserFullName(e.target.value)} 
                  style={{ width: "100%", padding: "10px", border: "1px solid rgba(197,168,92,0.2)", borderRadius: "8px", background: "#0b0c10", color: "#fff", boxSizing: "border-box" }}
                  required
                />
              </div>
              <div>
                <label style={{ display: "block", fontSize: "0.85rem", color: "#9aa0b4", marginBottom: "5px" }}>Email *</label>
                <input 
                  type="email" 
                  value={editUserEmail} 
                  onChange={(e) => setEditUserEmail(e.target.value)} 
                  style={{ width: "100%", padding: "10px", border: "1px solid rgba(197,168,92,0.2)", borderRadius: "8px", background: "#0b0c10", color: "#fff", boxSizing: "border-box" }}
                  required
                />
              </div>
              <div>
                <label style={{ display: "block", fontSize: "0.85rem", color: "#9aa0b4", marginBottom: "5px" }}>Phone Number</label>
                <input 
                  type="text" 
                  value={editUserPhone} 
                  onChange={(e) => setEditUserPhone(e.target.value)} 
                  style={{ width: "100%", padding: "10px", border: "1px solid rgba(197,168,92,0.2)", borderRadius: "8px", background: "#0b0c10", color: "#fff", boxSizing: "border-box" }}
                />
              </div>
              <div>
                <label style={{ display: "block", fontSize: "0.85rem", color: "#9aa0b4", marginBottom: "5px" }}>Account Role</label>
                <select 
                  value={editUserRole} 
                  onChange={(e) => setEditUserRole(e.target.value)} 
                  style={{ width: "100%", padding: "10px", border: "1px solid rgba(197,168,92,0.2)", borderRadius: "8px", background: "#0b0c10", color: "#fff", boxSizing: "border-box" }}
                >
                  <option value="USER">USER (Customer)</option>
                  <option value="ADMIN">ADMIN (Staff)</option>
                </select>
              </div>
              <div style={{ gridColumn: "span 4", display: "flex", gap: "10px", marginTop: "10px" }}>
                <button 
                  type="submit" 
                  style={{
                    background: "linear-gradient(135deg, #c5a85c 0%, #8c7237 100%)",
                    color: "#0b0c10",
                    border: "none",
                    padding: "12px 25px",
                    borderRadius: "8px",
                    fontWeight: "bold",
                    cursor: "pointer"
                  }}
                >
                  Save Profile Updates
                </button>
                <button 
                  type="button" 
                  onClick={() => {
                    setIsEditingUser(false);
                    setEditUserId(null);
                    setEditUserFullName("");
                    setEditUserEmail("");
                    setEditUserPhone("");
                    setEditUserRole("USER");
                  }}
                  style={{ background: "rgba(255,255,255,0.05)", border: "1px solid rgba(255,255,255,0.15)", color: "#fff", padding: "12px 20px", borderRadius: "8px", cursor: "pointer" }}
                >
                  Cancel
                </button>
              </div>
            </form>
          </div>
        )}

        <div className="accounts-grid">
          
          {/* User List Panel */}
          <div className="admin-card" style={{ background: "#12131c", border: "1px solid rgba(197, 168, 92, 0.2)", borderRadius: "20px", padding: "25px" }}>
            <h2 style={{ color: "#c5a85c", margin: "0 0 10px 0" }}>👥 Customer Accounts</h2>
            <p style={{ color: "#9aa0b4", fontSize: "0.8rem", margin: "0 0 20px 0" }}>Edit profile information or remove customer accounts from MySQL</p>

            <div style={{ display: "grid", gap: "12px" }}>
              {allUsers.filter(u => u.role !== "ADMIN").length === 0 ? (
                <p style={{ color: "#9aa0b4", textAlign: "center" }}>No customer accounts registered yet.</p>
              ) : (
                allUsers.filter(u => u.role !== "ADMIN").map((u) => (
                  <div key={u.id} style={{ background: "#0b0c10", border: "1px solid rgba(197,168,92,0.1)", padding: "15px", borderRadius: "12px", display: "flex", justifyContent: "space-between", alignItems: "center" }}>
                    <div>
                      <h4 style={{ margin: "0 0 4px 0", color: "#f5f6fa" }}>{u.fullName} <span style={{ color: "#c5a85c", fontSize: "0.8rem" }}>(ID: {u.id})</span></h4>
                      <div style={{ fontSize: "0.8rem", color: "#9aa0b4" }}>📧 {u.email}</div>
                      <div style={{ fontSize: "0.8rem", color: "#9aa0b4", marginTop: "2px" }}>📞 Phone: {u.phoneNumber || "N/A"}</div>
                    </div>
                    <div style={{ display: "flex", gap: "8px" }}>
                      <button 
                        onClick={() => handleEditUserClick(u)}
                        style={{ background: "rgba(197,168,92,0.1)", border: "1px solid #c5a85c", color: "#c5a85c", padding: "6px 12px", borderRadius: "6px", fontSize: "0.8rem", cursor: "pointer" }}
                      >
                        ✏️ Edit
                      </button>
                      <button 
                        onClick={() => handleDeleteUser(u.id, "USER")}
                        style={{ background: "rgba(220,38,38,0.15)", border: "1px solid #dc2626", color: "#f87171", padding: "6px 12px", borderRadius: "6px", fontSize: "0.8rem", cursor: "pointer" }}
                      >
                        🗑️ Delete
                      </button>
                    </div>
                  </div>
                ))
              )}
            </div>
          </div>

          {/* Admin List Panel */}
          <div className="admin-card" style={{ background: "#12131c", border: "1px solid rgba(197, 168, 92, 0.2)", borderRadius: "20px", padding: "25px" }}>
            <h2 style={{ color: "#c5a85c", margin: "0 0 10px 0" }}>🛡️ Administrative Staff</h2>
            <p style={{ color: "#9aa0b4", fontSize: "0.8rem", margin: "0 0 20px 0" }}>Edit administrative privileges or remove moderators handling GLOBAL SPORTS ARENA</p>

            <div style={{ display: "grid", gap: "12px" }}>
              {allUsers.filter(u => u.role === "ADMIN").length === 0 ? (
                <p style={{ color: "#9aa0b4", textAlign: "center" }}>No administrative accounts found.</p>
              ) : (
                allUsers.filter(u => u.role === "ADMIN").map((u) => (
                  <div key={u.id} style={{ background: "#0b0c10", border: "1px solid rgba(197,168,92,0.1)", padding: "15px", borderRadius: "12px", display: "flex", justifyContent: "space-between", alignItems: "center" }}>
                    <div>
                      <h4 style={{ margin: "0 0 4px 0", color: "#f5f6fa" }}>{u.fullName} <span style={{ color: "#c5a85c", fontSize: "0.8rem" }}>(ID: {u.id})</span></h4>
                      <div style={{ fontSize: "0.8rem", color: "#9aa0b4" }}>📧 {u.email}</div>
                      <div style={{ fontSize: "0.8rem", color: "#9aa0b4", marginTop: "2px" }}>📞 Phone: {u.phoneNumber || "N/A"}</div>
                    </div>
                    <div style={{ display: "flex", gap: "8px" }}>
                      <button 
                        onClick={() => handleEditUserClick(u)}
                        style={{ background: "rgba(197,168,92,0.1)", border: "1px solid #c5a85c", color: "#c5a85c", padding: "6px 12px", borderRadius: "6px", fontSize: "0.8rem", cursor: "pointer" }}
                      >
                        ✏️ Edit
                      </button>
                      <button 
                        onClick={() => handleDeleteUser(u.id, "ADMIN")}
                        style={{ background: "rgba(220,38,38,0.15)", border: "1px solid #dc2626", color: "#f87171", padding: "6px 12px", borderRadius: "6px", fontSize: "0.8rem", cursor: "pointer" }}
                      >
                        🗑️ Delete
                      </button>
                    </div>
                  </div>
                ))
              )}
            </div>
          </div>

        </div>

      </div>

      {/* 🌍 GLOBAL DESTINATIONS MANAGER */}
      <div style={{ marginTop: "40px" }}>
        <div className="admin-card" style={{
          background: "linear-gradient(135deg, #12131c 0%, #0b0f1e 100%)",
          border: "1px solid rgba(197, 168, 92, 0.35)",
          borderRadius: "20px",
          padding: "30px",
          position: "relative",
          overflow: "hidden"
        }}>
          {/* top glow strip */}
          <div style={{ position:"absolute", top:0, left:0, right:0, height:"3px", background:"linear-gradient(90deg, transparent, #c5a85c, transparent)" }} />

          <h2 style={{ color:"#c5a85c", margin:"0 0 6px 0", fontSize:"1.2rem" }}>
            🌍 Manage Global Event Destinations
          </h2>
          <p style={{ color:"#9aa0b4", fontSize:"0.82rem", margin:"0 0 24px 0" }}>
            Add or remove countries shown in the <strong style={{color:"#c5a85c"}}>Global Event Destinations</strong> carousel on the Home page
          </p>

          <div style={{ display:"grid", gridTemplateColumns:"1fr 1fr", gap:"30px" }}>

            {/* ADD FORM */}
            <div>
              <h3 style={{ color:"#f5f6fa", margin:"0 0 16px 0", fontSize:"0.95rem", display:"flex", alignItems:"center", gap:"8px" }}>
                ➕ Add New Country
              </h3>
              <form onSubmit={handleAddDestination} style={{ display:"grid", gap:"12px" }}>
                <div>
                  <label style={{ display:"block", fontSize:"0.8rem", color:"#c5a85c", marginBottom:"5px", fontWeight:"600" }}>Country Name *</label>
                  <input
                    type="text"
                    value={destCountry}
                    onChange={e => setDestCountry(e.target.value)}
                    placeholder="e.g. JAPAN"
                    style={{ width:"100%", padding:"10px 12px", border:"1px solid rgba(197,168,92,0.25)", borderRadius:"8px", background:"#0b0c10", color:"#fff", boxSizing:"border-box" }}
                    required
                  />
                </div>

                <div>
                  <label style={{ display:"block", fontSize:"0.8rem", color:"#c5a85c", marginBottom:"5px", fontWeight:"600" }}>Image URL * <span style={{ color:"#9aa0b4", fontWeight:"normal" }}>(Unsplash or any public URL)</span></label>
                  <input
                    type="url"
                    value={destImageUrl}
                    onChange={e => setDestImageUrl(e.target.value)}
                    placeholder="https://images.unsplash.com/photo-..."
                    style={{ width:"100%", padding:"10px 12px", border:"1px solid rgba(197,168,92,0.25)", borderRadius:"8px", background:"#0b0c10", color:"#fff", boxSizing:"border-box" }}
                    required
                  />
                </div>

                <div style={{ display:"grid", gridTemplateColumns:"1fr 1fr", gap:"10px" }}>
                  <div>
                    <label style={{ display:"block", fontSize:"0.8rem", color:"#c5a85c", marginBottom:"5px", fontWeight:"600" }}>Date Range *</label>
                    <input
                      type="text"
                      value={destDate}
                      onChange={e => setDestDate(e.target.value)}
                      placeholder="e.g. Mar - Jun 2026"
                      style={{ width:"100%", padding:"10px 12px", border:"1px solid rgba(197,168,92,0.25)", borderRadius:"8px", background:"#0b0c10", color:"#fff", boxSizing:"border-box" }}
                      required
                    />
                  </div>
                  <div>
                    <label style={{ display:"block", fontSize:"0.8rem", color:"#c5a85c", marginBottom:"5px", fontWeight:"600" }}>City / Venue *</label>
                    <input
                      type="text"
                      value={destCity}
                      onChange={e => setDestCity(e.target.value)}
                      placeholder="e.g. Tokyo / Osaka"
                      style={{ width:"100%", padding:"10px 12px", border:"1px solid rgba(197,168,92,0.25)", borderRadius:"8px", background:"#0b0c10", color:"#fff", boxSizing:"border-box" }}
                      required
                    />
                  </div>
                </div>

                <div>
                  <label style={{ display:"block", fontSize:"0.8rem", color:"#c5a85c", marginBottom:"5px", fontWeight:"600" }}>Region / Country Label</label>
                  <input
                    type="text"
                    value={destRegion}
                    onChange={e => setDestRegion(e.target.value)}
                    placeholder="e.g. Japan (defaults to city if blank)"
                    style={{ width:"100%", padding:"10px 12px", border:"1px solid rgba(197,168,92,0.25)", borderRadius:"8px", background:"#0b0c10", color:"#fff", boxSizing:"border-box" }}
                  />
                </div>

                {/* Live Preview */}
                {destCountry && destImageUrl && (
                  <div style={{ padding:"12px", background:"rgba(197,168,92,0.06)", border:"1px solid rgba(197,168,92,0.2)", borderRadius:"10px", display:"flex", gap:"12px", alignItems:"center" }}>
                    <img
                      src={destImageUrl}
                      alt="preview"
                      style={{ width:"60px", height:"60px", objectFit:"cover", borderRadius:"8px", flexShrink:0 }}
                      onError={e => { e.target.style.display="none"; }}
                    />
                    <div>
                      <div style={{ fontWeight:"700", color:"#f5f6fa", fontSize:"0.9rem" }}>{destCountry.toUpperCase()}</div>
                      <div style={{ color:"#9aa0b4", fontSize:"0.78rem" }}>{destDate} • {destCity}</div>
                    </div>
                  </div>
                )}

                <button
                  type="submit"
                  style={{
                    background:"linear-gradient(135deg, #c5a85c 0%, #8c7237 100%)",
                    color:"#0b0c10",
                    border:"none",
                    padding:"12px",
                    borderRadius:"10px",
                    fontWeight:"800",
                    fontSize:"0.95rem",
                    cursor:"pointer",
                    letterSpacing:"0.4px"
                  }}
                >
                  🌍 Add to Home Carousel
                </button>
              </form>
            </div>

            {/* EXISTING CUSTOM DESTINATIONS LIST */}
            <div>
              <h3 style={{ color:"#f5f6fa", margin:"0 0 16px 0", fontSize:"0.95rem" }}>
                📋 Admin-Added Destinations ({customDestinations.length})
              </h3>
              {customDestinations.length === 0 ? (
                <div style={{ textAlign:"center", padding:"40px 20px", background:"rgba(255,255,255,0.02)", borderRadius:"12px", border:"1px dashed rgba(197,168,92,0.2)" }}>
                  <div style={{ fontSize:"2rem", marginBottom:"10px" }}>🌐</div>
                  <p style={{ color:"#9aa0b4", margin:0, fontSize:"0.85rem" }}>No custom destinations added yet.<br/>Use the form to add new countries.</p>
                </div>
              ) : (
                <div style={{ display:"grid", gap:"10px", maxHeight:"420px", overflowY:"auto", paddingRight:"4px" }}>
                  {customDestinations.map(dest => (
                    <div key={dest.id} style={{
                      background:"#0b0c10",
                      border:"1px solid rgba(197,168,92,0.15)",
                      borderRadius:"12px",
                      padding:"12px 14px",
                      display:"flex",
                      gap:"12px",
                      alignItems:"center"
                    }}>
                      <img
                        src={dest.image}
                        alt={dest.country}
                        style={{ width:"55px", height:"55px", objectFit:"cover", borderRadius:"8px", flexShrink:0 }}
                        onError={e => { e.target.style.display="none"; }}
                      />
                      <div style={{ flex:1, minWidth:0 }}>
                        <div style={{ fontWeight:"700", color:"#f5f6fa", fontSize:"0.9rem" }}>{dest.country}</div>
                        <div style={{ color:"#9aa0b4", fontSize:"0.78rem", marginTop:"2px" }}>📅 {dest.date}</div>
                        <div style={{ color:"#9aa0b4", fontSize:"0.78rem" }}>📍 {dest.city}</div>
                      </div>
                      <button
                        onClick={() => handleDeleteDestination(dest.id)}
                        style={{ background:"rgba(220,38,38,0.12)", border:"1px solid #dc2626", color:"#f87171", padding:"6px 10px", borderRadius:"6px", fontSize:"0.78rem", cursor:"pointer", flexShrink:0 }}
                      >
                        🗑️ Remove
                      </button>
                    </div>
                  ))}
                </div>
              )}
            </div>

          </div>
        </div>
      </div>
    </div>
  );
}

export default AdminDashboard;