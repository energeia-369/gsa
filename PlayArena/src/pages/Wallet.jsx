import { useState, useEffect } from "react";
import axios from "axios";
import "../styles/Wallet.css";

function Wallet() {
  const [balance, setBalance] = useState(0);
  const [showRechargeModal, setShowRechargeModal] = useState(false);
  const [rechargeAmount, setRechargeAmount] = useState("");
  const [selectedPayment, setSelectedPayment] = useState("card");
  const [transactions, setTransactions] = useState([]);
  const [loading, setLoading] = useState(false);

  const userEmail = localStorage.getItem("userEmail") || "guest@globalsportsarena.com";

  const fetchWalletDetails = async () => {
    try {
      setLoading(true);
      // 1. Fetch balance
      const balanceRes = await axios.get(`http://localhost:8080/api/wallet/balance?email=${userEmail}`);
      setBalance(balanceRes.data.nxlCredits || 0);
      localStorage.setItem("nxlCoins", balanceRes.data.nxlCredits || 0);

      // 2. Fetch transaction logs
      const logsRes = await axios.get(`http://localhost:8080/api/wallet/transactions?email=${userEmail}`);
      setTransactions(logsRes.data || []);
    } catch (err) {
      console.error("Wallet details fetch failed", err);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    if (userEmail) {
      fetchWalletDetails();
    }
  }, [userEmail]);

  const handleRecharge = async () => {
    const amount = parseFloat(rechargeAmount);
    if (isNaN(amount) || amount <= 0) {
      alert("Please enter a valid amount");
      return;
    }

    if (!window.Razorpay) {
      alert("Razorpay SDK not loaded. Please verify your internet connection or index.html script tags.");
      return;
    }
    
    setLoading(true);

    const options = {
      key: "rzp_test_Sv9Ri6GpwqTesi", // Standard testing Razorpay Key matching checkout
      amount: Math.round(amount * 100), // convert to paise
      currency: "INR",
      name: "GLOBAL SPORTS ARENA Wallet",
      description: `Add Money & Earn NXL (Recharge ₹${amount})`,
      image: "https://cdn-icons-png.flaticon.com/512/857/857455.png",
      handler: async function (response) {
        try {
          const payload = {
            email: userEmail,
            amount: amount,
            paymentId: response.razorpay_payment_id
          };

          const res = await axios.post("http://localhost:8080/api/wallet/recharge", payload);
          alert(`₹${amount} recharged successfully! You earned ${res.data.creditsEarned || 0} NXL Credits.`);
          
          setShowRechargeModal(false);
          setRechargeAmount("");
          
          // Reload wallet details from MySQL database
          await fetchWalletDetails();
        } catch (err) {
          console.error("Recharge MySQL sync failed", err);
          alert("Payment completed, but failed to sync credits to the database. Please contact support.");
        } finally {
          setLoading(false);
        }
      },
      prefill: {
        email: userEmail,
      },
      theme: {
        color: "#c5a85c", // Gold theme matching GLOBAL SPORTS ARENA UI
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
    <div className="wallet-page" style={{ background: "#0b0c10", color: "#f5f6fa" }}>
      <div className="wallet-container" style={{ border: "1px solid rgba(197,168,92,0.15)", borderRadius: "24px", background: "#12131c", padding: "40px" }}>
        {/* Header Section */}
        <div className="wallet-header">
          <div className="wallet-icon" style={{ color: "#c5a85c" }}>👛</div>
          <h1 style={{ color: "#c5a85c", fontWeight: "800" }}>My NXL Wallet</h1>
          <p style={{ color: "#9aa0b4" }}>Manage your dynamic loyalty funds and track credit logs in MySQL</p>
        </div>

        {/* Balance Card */}
        <div className="balance-card" style={{ background: "linear-gradient(135deg, #1e202c 0%, #0b0c10 100%)", border: "1px solid var(--accent-gold)" }}>
          <div className="balance-glow" style={{ background: "rgba(197, 168, 92, 0.15)" }}></div>
          <div className="balance-content">
            <span className="balance-label" style={{ color: "#9aa0b4", letterSpacing: "1px" }}>Dynamic NXL Balance</span>
            <div className="balance-amount">
              <span className="currency" style={{ color: "#c5a85c" }}>💎</span>
              <span className="amount" style={{ color: "#c5a85c" }}>{balance.toLocaleString()}</span>
            </div>
            <p className="balance-subtext" style={{ color: "#9aa0b4" }}>Ready to redeem dynamically at sports checkout</p>
            <button className="recharge-btn" onClick={() => setShowRechargeModal(true)} style={{ background: "linear-gradient(135deg, #c5a85c 0%, #8c7237 100%)", color: "#0b0c10", fontWeight: "bold" }}>
              <span>+</span> Add Money & Earn NXL
            </button>
          </div>
        </div>

        {/* Transaction History */}
        <div className="transactions-section" style={{ marginTop: "40px" }}>
          <div className="section-header" style={{ display: "flex", justifyContent: "space-between", alignItems: "center", borderBottom: "1px solid rgba(197,168,92,0.15)", paddingBottom: "10px" }}>
            <h2 style={{ color: "#c5a85c", margin: "0" }}>Credit History</h2>
            <span className="transaction-count" style={{ color: "#9aa0b4" }}>{transactions.length} entries recorded</span>
          </div>
          
          <div className="transactions-list" style={{ marginTop: "20px" }}>
            {loading ? (
              <p style={{ textAlign: "center", color: "#c5a85c" }}>Refreshing logs from database...</p>
            ) : transactions.length === 0 ? (
              <div className="empty-state" style={{ textAlign: "center", padding: "40px" }}>
                <span style={{ fontSize: "3rem" }}>📭</span>
                <p style={{ color: "#9aa0b4" }}>No transaction history found in database.</p>
              </div>
            ) : (
              transactions.map((transaction) => (
                <div 
                  key={transaction.id} 
                  className={`transaction-item ${transaction.type.toLowerCase().includes("earned") || transaction.type.toLowerCase().includes("add") ? "credit" : "debit"}`}
                  style={{
                    background: "rgba(22, 24, 38, 0.5)",
                    border: "1px solid rgba(197, 168, 92, 0.1)",
                    borderRadius: "12px",
                    padding: "15px",
                    marginBottom: "10px",
                    display: "flex",
                    justifyContent: "space-between",
                    alignItems: "center"
                  }}
                >
                  <div>
                    <div className="transaction-description" style={{ fontWeight: "600", fontSize: "0.95rem" }}>
                      {transaction.description}
                    </div>
                    <div className="transaction-date" style={{ fontSize: "0.8rem", color: "#9aa0b4", marginTop: "4px" }}>
                      {new Date(transaction.date).toLocaleString()} • Ref: {transaction.refId}
                    </div>
                  </div>
                  <div 
                    className="transaction-amount" 
                    style={{ 
                      fontWeight: "bold", 
                      fontSize: "1.1rem",
                      color: (transaction.type === "EARNED" || transaction.type === "ADMIN_ADD") ? "#22c55e" : "#ef4444" 
                    }}
                  >
                    {(transaction.type === "EARNED" || transaction.type === "ADMIN_ADD") ? "+" : "-"} {transaction.amount} NXL
                  </div>
                </div>
              ))
            )}
          </div>
        </div>
      </div>

      {/* Recharge Modal */}
      {showRechargeModal && (
        <div className="modal-overlay" onClick={() => setShowRechargeModal(false)} style={{ background: "rgba(0,0,0,0.8)" }}>
          <div className="modal-content" onClick={(e) => e.stopPropagation()} style={{ background: "#12131c", border: "1px solid rgba(197,168,92,0.25)", color: "#f5f6fa" }}>
            <div className="modal-header">
              <h2 style={{ color: "#c5a85c" }}>Recharge Wallet & Earn NXL</h2>
              <button className="modal-close" onClick={() => setShowRechargeModal(false)}>✕</button>
            </div>
            
            <div className="modal-body">
              <div className="amount-presets">
                <button className="preset-btn" onClick={() => setRechargeAmount("1000")}>₹1,000</button>
                <button className="preset-btn" onClick={() => setRechargeAmount("2000")}>₹2,000</button>
                <button className="preset-btn" onClick={() => setRechargeAmount("5000")}>₹5,000</button>
              </div>
              
              <div className="input-group" style={{ margin: "20px 0" }}>
                <label style={{ display: "block", marginBottom: "8px", fontSize: "0.9rem", color: "#9aa0b4" }}>Enter Amount (₹)</label>
                <input
                  type="number"
                  placeholder="Enter custom amount"
                  value={rechargeAmount}
                  onChange={(e) => setRechargeAmount(e.target.value)}
                  min="1"
                  style={{ width: "100%", padding: "10px", border: "1px solid rgba(197,168,92,0.2)", borderRadius: "8px", background: "rgba(22, 24, 38, 0.8)", color: "#fff", outline: "none" }}
                />
                <span style={{ fontSize: "0.8rem", color: "#c5a85c", marginTop: "6px", display: "block" }}>
                  💡 Conversion: ₹20 spend gives 1 NXL credit reward (e.g. ₹1000 recharge gives 50 NXL credits)
                </span>
              </div>
              
              <div className="payment-methods">
                <label style={{ display: "block", marginBottom: "8px", fontSize: "0.9rem", color: "#9aa0b4" }}>Payment Method</label>
                <div className="method-options">
                  <label className="method-option">
                    <input
                      type="radio"
                      name="payment"
                      value="card"
                      checked={selectedPayment === "card"}
                      onChange={(e) => setSelectedPayment(e.target.value)}
                    />
                    <span>💳 Credit/Debit Card</span>
                  </label>
                  <label className="method-option">
                    <input
                      type="radio"
                      name="payment"
                      value="upi"
                      checked={selectedPayment === "upi"}
                      onChange={(e) => setSelectedPayment(e.target.value)}
                    />
                    <span>📱 UPI</span>
                  </label>
                </div>
              </div>
            </div>
            
            <div className="modal-footer">
              <button className="cancel-btn" onClick={() => setShowRechargeModal(false)}>Cancel</button>
              <button className="confirm-btn" onClick={handleRecharge} style={{ background: "linear-gradient(135deg, #c5a85c 0%, #8c7237 100%)", color: "#0b0c10", fontWeight: "bold" }}>
                Proceed to Pay ₹{rechargeAmount || "0"}
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}

export default Wallet;