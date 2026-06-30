import { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import "../styles/Credits.css";

function Credits() {
  const navigate = useNavigate();
  const [credits, setCredits] = useState(() => {
    const saved = localStorage.getItem("nxlCoins");
    return saved !== null ? Number(saved) : 1250;
  });
  const [points, setPoints] = useState(450);
  const [selectedReward, setSelectedReward] = useState(null);
  const [showRedeemModal, setShowRedeemModal] = useState(false);
  const [transactions, setTransactions] = useState([
    { id: 1, type: "Earned", description: "Event Booking - Champions League", amount: 500, date: "2026-05-15", icon: "🎫" },
    { id: 2, type: "Earned", description: "Product Purchase - Sports Shoes", amount: 300, date: "2026-05-10", icon: "👟" },
    { id: 3, type: "Redeemed", description: "Discount Voucher", amount: -200, date: "2026-05-05", icon: "🎁" },
    { id: 4, type: "Earned", description: "Referral Bonus", amount: 150, date: "2026-05-01", icon: "👥" },
    { id: 5, type: "Earned", description: "Event Check-in Bonus", amount: 100, date: "2026-04-28", icon: "✅" },
  ]);

  const rewards = [
    { id: 1, name: "10% Discount Voucher", points: 200, creditCost: 100, icon: "🎫", type: "discount" },
    { id: 2, name: "Free Event Ticket", points: 500, creditCost: 250, icon: "🎟️", type: "ticket" },
    { id: 3, name: "Premium Sports Gear", points: 1000, creditCost: 500, icon: "🏅", type: "gear" },
    { id: 4, name: "VIP Experience Pass", points: 1500, creditCost: 750, icon: "👑", type: "vip" },
    { id: 5, name: "Signed Merchandise", points: 2000, creditCost: 1000, icon: "✍️", type: "merch" },
  ];

  useEffect(() => {
    // Simulate loading animation
    const timer = setTimeout(() => {
      // You could fetch real data here
    }, 500);
    return () => clearTimeout(timer);
  }, []);

  const handleRedeem = (reward) => {
    if (credits >= reward.creditCost) {
      setSelectedReward(reward);
      setShowRedeemModal(true);
    } else {
      alert(`Insufficient credits! You need ${reward.creditCost - credits} more credits.`);
    }
  };

  const confirmRedeem = () => {
    if (selectedReward) {
      const updatedCredits = credits - selectedReward.creditCost;
      setCredits(updatedCredits);
      localStorage.setItem("nxlCoins", updatedCredits);
      setPoints(points + selectedReward.points);
      
      // Add transaction record
      const newTransaction = {
        id: transactions.length + 1,
        type: "Redeemed",
        description: `Redeemed: ${selectedReward.name}`,
        amount: -selectedReward.creditCost,
        date: new Date().toISOString().split('T')[0],
        icon: selectedReward.icon
      };
      setTransactions([newTransaction, ...transactions]);
      
      setShowRedeemModal(false);
      setSelectedReward(null);
      
      // Show success message
      alert(`Successfully redeemed ${selectedReward.name}!`);
    }
  };

  const getProgressPercentage = () => {
    const nextMilestone = rewards.find(r => r.creditCost > credits) || rewards[rewards.length - 1];
    const prevMilestone = rewards.filter(r => r.creditCost <= credits).pop() || { creditCost: 0 };
    const progress = ((credits - prevMilestone.creditCost) / (nextMilestone.creditCost - prevMilestone.creditCost)) * 100;
    return Math.min(100, Math.max(0, progress));
  };

  const getNextReward = () => {
    return rewards.find(r => r.creditCost > credits) || rewards[rewards.length - 1];
  };

  return (
    <div className="credits-page">
      {/* Hero Banner */}
      <div className="credits-hero">
        <div className="credits-hero-overlay"></div>
        <div className="credits-hero-content">
          <div className="hero-icon">💰</div>
          <h1>My <span className="highlight">Credits</span></h1>
          <p>Your loyalty rewards and earned credits at one place</p>
        </div>
      </div>

      {/* Credits Summary Cards */}
      <div className="credits-summary">
        <div className="summary-card credits-card">
          <div className="card-icon">💎</div>
          <div className="card-content">
            <h3>{credits.toLocaleString()}</h3>
            <p>Available Credits</p>
            <div className="card-progress">
              <div className="progress-bar" style={{ width: `${(credits / 2500) * 100}%` }}></div>
            </div>
            <span className="card-label">1 Credit = ₹1 Value</span>
          </div>
        </div>

        <div className="summary-card points-card">
          <div className="card-icon">⭐</div>
          <div className="card-content">
            <h3>{points.toLocaleString()}</h3>
            <p>Reward Points</p>
            <span className="card-label">100 Points = ₹10 Cashback</span>
          </div>
        </div>

        <div className="summary-card tier-card">
          <div className="card-icon">🏆</div>
          <div className="card-content">
            <h3>Silver Tier</h3>
            <p>Membership Level</p>
            <div className="card-progress">
              <div className="progress-bar" style={{ width: getProgressPercentage() }}></div>
            </div>
            <span className="card-label">{credits}/2500 to Gold</span>
          </div>
        </div>
      </div>

      {/* Next Milestone */}
      <div className="next-milestone">
        <div className="milestone-content">
          <span className="milestone-icon">🎯</span>
          <div className="milestone-info">
            <h4>Next Milestone: {getNextReward().name}</h4>
            <p>Need {getNextReward().creditCost - credits} more credits to unlock</p>
          </div>
          <div className="milestone-progress">
            <div className="progress-circle">
              <svg viewBox="0 0 36 36">
                <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" 
                      fill="none" stroke="#E0E0E0" strokeWidth="3"/>
                <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" 
                      fill="none" stroke="#D4AF37" strokeWidth="3"
                      strokeDasharray={`${getProgressPercentage()}, 100`}/>
              </svg>
              <span>{Math.round(getProgressPercentage())}%</span>
            </div>
          </div>
        </div>
      </div>

      {/* Rewards Store */}
      <div className="rewards-section">
        <div className="section-header">
          <h2>🎁 Rewards Store</h2>
          <p>Redeem your credits for exciting rewards</p>
        </div>
        <div className="rewards-grid">
          {rewards.map((reward) => (
            <div key={reward.id} className={`reward-card ${credits >= reward.creditCost ? 'available' : 'locked'}`}>
              <div className="reward-icon">{reward.icon}</div>
              <h3>{reward.name}</h3>
              <div className="reward-cost">
                <span className="cost-credits">{reward.creditCost} Credits</span>
                <span className="cost-points">+{reward.points} Points</span>
              </div>
              <button 
                className={`redeem-btn ${credits >= reward.creditCost ? '' : 'disabled'}`}
                onClick={() => handleRedeem(reward)}
                disabled={credits < reward.creditCost}
              >
                {credits >= reward.creditCost ? 'Redeem Now' : `Need ${reward.creditCost - credits} more`}
              </button>
              {reward.type === 'vip' && <div className="reward-badge">Exclusive</div>}
              {reward.type === 'merch' && <div className="reward-badge limited">Limited</div>}
            </div>
          ))}
        </div>
      </div>

      {/* Ways to Earn */}
      <div className="earn-section">
        <div className="section-header">
          <h2>💪 Ways to Earn Credits</h2>
          <p>Complete these actions to earn more credits</p>
        </div>
        <div className="earn-grid">
          <div className="earn-card">
            <div className="earn-icon">🎫</div>
            <h4>Book Events</h4>
            <p>Earn 5% back in credits</p>
            <button onClick={() => navigate("/sports-categories")} className="earn-btn">Book Now</button>
          </div>
          <div className="earn-card">
            <div className="earn-icon">🛍️</div>
            <h4>Shop Products</h4>
            <p>Earn 3% back in credits</p>
            <button onClick={() => navigate("/products")} className="earn-btn">Shop Now</button>
          </div>
          <div className="earn-card">
            <div className="earn-icon">👥</div>
            <h4>Refer Friends</h4>
            <p>Earn 100 credits per referral</p>
            <button className="earn-btn">Invite Now</button>
          </div>
          <div className="earn-card">
            <div className="earn-icon">⭐</div>
            <h4>Write Reviews</h4>
            <p>Earn 25 credits per review</p>
            <button className="earn-btn">Write Review</button>
          </div>
        </div>
      </div>

      {/* Transaction History */}
      <div className="transactions-section">
        <div className="section-header">
          <h2>📜 Transaction History</h2>
          <p>Track your credits and points activity</p>
        </div>
        <div className="transactions-list">
          {transactions.map((transaction) => (
            <div key={transaction.id} className="transaction-item">
              <div className="transaction-icon">{transaction.icon}</div>
              <div className="transaction-details">
                <div className="transaction-header">
                  <span className="transaction-type">{transaction.type}</span>
                  <span className="transaction-date">{transaction.date}</span>
                </div>
                <p className="transaction-desc">{transaction.description}</p>
              </div>
              <div className={`transaction-amount ${transaction.amount > 0 ? 'positive' : 'negative'}`}>
                {transaction.amount > 0 ? '+' : ''}{transaction.amount} Credits
              </div>
            </div>
          ))}
        </div>
      </div>

      {/* Quick Actions */}
      <div className="quick-actions">
        <button className="action-btn" onClick={() => navigate("/sports-categories")}>
          🎟️ Book Events
        </button>
        <button className="action-btn" onClick={() => navigate("/products")}>
          🛒 Shop Now
        </button>
        <button className="action-btn">
          📱 Share & Earn
        </button>
      </div>

      {/* Redeem Modal */}
      {showRedeemModal && selectedReward && (
        <div className="modal-overlay" onClick={() => setShowRedeemModal(false)}>
          <div className="modal-content" onClick={(e) => e.stopPropagation()}>
            <div className="modal-header">
              <span className="modal-icon">{selectedReward.icon}</span>
              <h3>Confirm Redemption</h3>
              <button className="modal-close" onClick={() => setShowRedeemModal(false)}>×</button>
            </div>
            <div className="modal-body">
              <p>Are you sure you want to redeem:</p>
              <h4>{selectedReward.name}</h4>
              <div className="modal-cost">
                <span>Cost: {selectedReward.creditCost} Credits</span>
                <span>You'll earn: +{selectedReward.points} Points</span>
              </div>
              <div className="modal-warning">
                ⚠️ This action cannot be undone
              </div>
            </div>
            <div className="modal-footer">
              <button className="cancel-btn" onClick={() => setShowRedeemModal(false)}>Cancel</button>
              <button className="confirm-btn" onClick={confirmRedeem}>Confirm Redemption</button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}

export default Credits;