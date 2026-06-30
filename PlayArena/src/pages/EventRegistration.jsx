import "../styles/EventRegistration.css";
import { useState } from "react";
import { useNavigate } from "react-router-dom";
import axios from "axios";

function EventRegistration() {
  const navigate = useNavigate();

  const [formData, setFormData] = useState({
    teamName: "",
    captainName: "",
    captainContact: "",
    email: "",
    sport: "",
    teamCategory: "",
    teamMembers: "",
    notes: "",
  });

  const [loading, setLoading] = useState(false);

  const eventStats = {
    registeredPlayers: "2,400+",
    seatsLeft: "Limited Seats Available",
  };

  const handleChange = (e) => {
    setFormData({
      ...formData,
      [e.target.name]: e.target.value,
    });
  };

  const handleRegisterEvent = async (e) => {
    e.preventDefault();

    try {
      setLoading(true);

      // Submit registration to backend on successful registration
      const apiResponse = await axios.post(
        "http://localhost:8080/api/event-registrations",
        {
          teamName: formData.teamName,
          captainName: formData.captainName,
          captainContact: formData.captainContact,
          email: formData.email,
          sport: formData.sport,
          teamCategory: formData.teamCategory,
          teamMembers: Number(formData.teamMembers),
          notes: formData.notes,
          paymentStatus: "FREE",
          registrationFee: 0.0
        }
      );

      alert("Event registration successful (Free Entry Pass)!");
      console.log(apiResponse.data);

      // Track this event in local user events
      const userEmail = localStorage.getItem("userEmail") || "guest";
      const orderKey = `orders_${userEmail}`;
      const oldOrders = JSON.parse(localStorage.getItem(orderKey)) || [];
      
      const newEventOrder = {
        id: "EVT-" + Date.now(),
        type: "event",
        title: `Tournament: ${formData.sport} Category`,
        image: "🏆",
        brand: "GLOBAL SPORTS ARENA Events",
        quantity: 1,
        price: 0,
        total: 0,
        status: "confirmed",
        date: new Date().toLocaleDateString(),
        orderDate: new Date().toLocaleDateString(),
        location: "Mumbai Sports Arena",
        trackingId: "FREE-PASS"
      };
      
      localStorage.setItem(orderKey, JSON.stringify([...oldOrders, newEventOrder]));

      // Increment events joined counter in user stats
      const currentEventsJoined = Number(localStorage.getItem("eventsJoined") || 0);
      localStorage.setItem("eventsJoined", currentEventsJoined + 1);

      navigate("/payment-success");
    } catch (error) {
      console.log("Event Registration Error:", error);
      if (error.response) {
        alert("Backend Error saving registration: " + JSON.stringify(error.response.data));
      } else {
        alert("Registration failed to record. Please try again.");
      }
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="event-page">
      <div className="event-container">
        <div className="event-info">
          <div className="info-content">
            <div className="event-badge">
              ⚡ {eventStats.seatsLeft}
            </div>

            <h1>GLOBAL SPORTS ARENA National Tournament</h1>

            <p>
              India's biggest multi-sports tournament platform for
              cricket, football, basketball, badminton, and tennis.
            </p>

            <div className="live-status">
              🔴 Live Registrations Open
            </div>

            <div className="event-details">
              <div className="detail-item">
                <span className="detail-icon">📅</span>

                <div>
                  <strong>Date & Time</strong>

                  <p>
                    Registrations Closing Soon • Matches Start Next Weekend
                  </p>
                </div>
              </div>

              <div className="detail-item">
                <span className="detail-icon">📍</span>

                <div>
                  <strong>Venue</strong>

                  <p>Mumbai • Pune • Bangalore • Delhi</p>
                </div>
              </div>

              <div className="detail-item">
                <span className="detail-icon">🏆</span>

                <div>
                  <strong>Rewards</strong>

                  <p>
                    Cash Rewards + Medals + Sponsorship Opportunities
                  </p>
                </div>
              </div>

              <div className="detail-item">
                <span className="detail-icon">👥</span>

                <div>
                  <strong>Team Size</strong>

                  <p>Depends on selected sport category</p>
                </div>
              </div>
            </div>

            <div className="price-card">
              <span className="price-label">
                Entry Pass
              </span>

              <div className="price-amount">
                <span className="price">₹0</span>

                <span className="price-old">₹1,999</span>

                <span className="price-off">
                  Free Entry
                </span>
              </div>

              <p>
                Includes Tournament Access,
                Digital Certificate,
                Team Dashboard &
                Match Tracking
              </p>

              <div className="live-stats">
                👥 {eventStats.registeredPlayers} Players Registered
              </div>
            </div>

            <div className="event-highlights">
              <div>⚡ Live Match Updates</div>

              <div>🏅 Verified Tournament System</div>

              <div>🎥 Streaming & Highlights</div>

              <div>🛒 Sports Merchandise</div>
            </div>
          </div>
        </div>

        <div className="event-form">
          <div className="form-header">
            <h2>Team Registration</h2>

            <p>
              Fill in the details to secure your spot
            </p>
          </div>

          <form onSubmit={handleRegisterEvent}>
            <div className="input-group">
              <label>Team Name *</label>

              <input
                type="text"
                name="teamName"
                placeholder="Enter your team name"
                value={formData.teamName}
                onChange={handleChange}
                required
              />
            </div>

            <div className="input-row">
              <div className="input-group half">
                <label>Captain Name *</label>

                <input
                  type="text"
                  name="captainName"
                  placeholder="Full name"
                  value={formData.captainName}
                  onChange={handleChange}
                  required
                />
              </div>

              <div className="input-group half">
                <label>Captain Contact *</label>

                <input
                  type="tel"
                  name="captainContact"
                  placeholder="Mobile number"
                  value={formData.captainContact}
                  onChange={handleChange}
                  required
                />
              </div>
            </div>

            <div className="input-group">
              <label>Email Address *</label>

              <input
                type="email"
                name="email"
                placeholder="team@example.com"
                value={formData.email}
                onChange={handleChange}
                required
              />
            </div>

            <div className="input-row">
              <div className="input-group half">
                <label>Select Sport *</label>

                <select
                  name="sport"
                  value={formData.sport}
                  onChange={handleChange}
                  required
                >
                  <option value="">
                    Select Sport
                  </option>

                  <option value="Football">
                    Football
                  </option>

                  <option value="Cricket">
                    Cricket
                  </option>

                  <option value="Basketball">
                    Basketball
                  </option>

                  <option value="Badminton">
                    Badminton
                  </option>

                  <option value="Tennis">
                    Tennis
                  </option>
                </select>
              </div>

              <div className="input-group half">
                <label>Team Category *</label>

                <select
                  name="teamCategory"
                  value={formData.teamCategory}
                  onChange={handleChange}
                  required
                >
                  <option value="">
                    Select Category
                  </option>

                  <option value="Men's">
                    Men's
                  </option>

                  <option value="Women's">
                    Women's
                  </option>

                  <option value="Mixed">
                    Mixed
                  </option>
                </select>
              </div>
            </div>

            <div className="input-group">
              <label>
                Number of Team Members *
              </label>

              <input
                type="number"
                name="teamMembers"
                placeholder="Including captain"
                min="1"
                max="15"
                value={formData.teamMembers}
                onChange={handleChange}
                required
              />
            </div>

            <div className="input-group">
              <label>Additional Notes</label>

              <textarea
                name="notes"
                placeholder="Any special requirements or requests..."
                value={formData.notes}
                onChange={handleChange}
              ></textarea>
            </div>

            <div className="terms-checkbox">
              <input
                type="checkbox"
                id="terms"
                required
              />

              <label htmlFor="terms">
                I agree to the{" "}
                <a href="#">
                  Terms & Conditions
                </a>{" "}
                and{" "}
                <a href="#">
                  Tournament Rules
                </a>
              </label>
            </div>

            <button
              type="submit"
              className="register-btn"
              disabled={loading}
            >
              {loading
                ? "Registering..."
                : "Register for Free →"}
            </button>
          </form>
        </div>
      </div>
    </div>
  );
}

export default EventRegistration;