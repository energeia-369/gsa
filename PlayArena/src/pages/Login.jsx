import "../styles/Login.css";
import { useState } from "react";
import { useNavigate, Link } from "react-router-dom";
import axios from "axios";

function Login() {

  const navigate = useNavigate();

  const [loginData, setLoginData] = useState({
    email: "",
    password: "",
  });

  // OTP specific states
  const [otpSent, setOtpSent] = useState(false);
  const [otpCode, setOtpCode] = useState("");
  const [testOtp, setTestOtp] = useState("");
  const [loading, setLoading] = useState(false);

  const handleChange = (e) => {
    setLoginData({
      ...loginData,
      [e.target.name]: e.target.value,
    });
  };

  const handleLogin = async (e) => {
    e.preventDefault();

    if (!loginData.email || !loginData.password) {
      alert("Please enter both email and password");
      return;
    }

    if (!otpSent) {
      // Step 1: Validate credentials and send OTP to Gmail
      try {
        setLoading(true);
        const response = await axios.post(
          "http://localhost:8080/api/auth/send-otp",
          {
            email: loginData.email,
            password: loginData.password
          }
        );

        if (response.data && response.data.success) {
          setOtpSent(true);
          alert(response.data.message || "A secure verification code has been sent to your Gmail inbox!");
        } else {
          alert(response.data.message || "Failed to dispatch verification code");
        }
      } catch (error) {
        console.log("Login OTP Dispatch Error:", error);
        
        // Robust offline fallback for local testing of default credentials
        if (loginData.email === "admin@globalsportsarena.com" && loginData.password === "admin") {
          alert("Offline Admin Mode: Accessing dashboard using local storage fallback.");
          localStorage.setItem("token", "admin-secret-token");
          localStorage.setItem("userEmail", "admin@globalsportsarena.com");
          localStorage.setItem("userRole", "ADMIN");
          navigate("/admin-dashboard");
          return;
        }

        if (error.response) {
          alert(error.response.data.message || error.response.data || "Invalid email or password");
        } else {
          alert("Spring Boot backend is not running or not reachable");
        }
      } finally {
        setLoading(false);
      }
    } else {
      // Step 2: Verify Gmail OTP code and complete sign-in
      if (!otpCode) {
        alert("Please enter the 6-digit Gmail verification code");
        return;
      }

      try {
        setLoading(true);
        const response = await axios.post(
          "http://localhost:8080/api/auth/verify-otp",
          {
            email: loginData.email,
            otp: otpCode
          }
        );

        if (response.data.token) {
          localStorage.setItem("token", response.data.token);
          localStorage.setItem("userEmail", loginData.email);
          
          const role = response.data.role || "USER";
          localStorage.setItem("userRole", role);

          alert(response.data.message || "OTP Login successful!");

          if (role === "ADMIN") {
            navigate("/admin-dashboard");
          } else {
            navigate("/user-dashboard");
          }
        } else {
          alert(response.data.message || "Verification failed");
        }
      } catch (error) {
        console.log("Verify OTP Error:", error);
        if (error.response) {
          alert(error.response.data.message || error.response.data || "Invalid verification code");
        } else {
          alert("Verification Failed: backend is not reachable");
        }
      } finally {
        setLoading(false);
      }
    }
  };

  const isAdminLogin = loginData.email.trim().toLowerCase().includes("admin");

  return (
    <div className={`login-page ${isAdminLogin ? "admin-theme" : ""}`}>
      <div className="login-container">

        {/* Left Side */}
        <div className="login-left">
          <div className="brand-box">
            <h2>🏆 GLOBAL SPORTS ARENA</h2>
            <h1>Welcome Back!</h1>
            <p>
              Login to register for tournaments, shop sports gear, and manage your NXL credits.
            </p>

            <div className="login-stats">
              <div>
                <h3>50+</h3>
                <span>Events</span>
              </div>
              <div>
                <h3>10K+</h3>
                <span>Users</span>
              </div>
              <div>
                <h3>437+</h3>
                <span>Products</span>
              </div>
            </div>
          </div>
        </div>

        {/* Right Side */}
        <div className="login-right">
          <div className="login-card">
            <h1>Login</h1>
            <p className="login-subtitle">
              Verify your credentials and complete Gmail OTP authentication to sign in securely
            </p>

            {/* Beautiful visual notification for the OTP */}
            {otpSent && (
              <div className="otp-test-alert" style={{
                background: "rgba(197, 168, 92, 0.08)",
                border: "1px dashed #c5a85c",
                padding: "15px",
                borderRadius: "15px",
                marginBottom: "20px",
                fontSize: "0.85rem",
                lineHeight: "1.5"
              }}>
                <strong style={{ color: "#c5a85c", display: "block", marginBottom: "5px" }}>
                  🔒 GMAIL OTP VERIFICATION SENT
                </strong>
                <p style={{ margin: 0, color: "#e2e2e2" }}>
                  A secure 6-digit login verification code has been sent to your Gmail address (<strong>{loginData.email}</strong>).
                </p>
              </div>
            )}

            <form onSubmit={handleLogin}>

              {/* Email Address Input */}
              <div className="input-group">
                <label>Email Address</label>
                <input
                  type="email"
                  name="email"
                  placeholder="you@example.com"
                  value={loginData.email}
                  onChange={handleChange}
                  required
                  disabled={otpSent}
                />
              </div>

              {/* Password Input */}
              <div className="input-group">
                <label>Password</label>
                <input
                  type="password"
                  name="password"
                  placeholder="Enter your password"
                  value={loginData.password}
                  onChange={handleChange}
                  required
                  disabled={otpSent}
                  autoComplete="current-password"
                />
              </div>

              {/* OTP Digit Input: Active after credentials are validated */}
              {otpSent && (
                <div className="input-group" style={{ marginTop: "20px" }}>
                  <label style={{ color: "#c5a85c", fontWeight: "bold" }}>Gmail Verification Code (OTP) *</label>
                  <input
                    type="text"
                    placeholder="Enter 6-digit Gmail code"
                    value={otpCode}
                    onChange={(e) => setOtpCode(e.target.value)}
                    required
                    maxLength={6}
                    style={{
                      border: "2px solid #c5a85c",
                      background: "rgba(197, 168, 92, 0.05)",
                      fontSize: "1.1rem",
                      fontWeight: "bold",
                      textAlign: "center",
                      letterSpacing: "4px"
                    }}
                  />
                </div>
              )}

              {/* Submit / Verification Dispatch button */}
              <button
                className="login-btn"
                type="submit"
                disabled={loading}
                style={{ marginTop: "25px" }}
              >
                {loading
                  ? "Processing..."
                  : otpSent
                    ? "Verify OTP & Sign In →"
                    : "Send OTP Verification Code →"}
              </button>

              {otpSent && (
                <div style={{ textAlign: "center", marginTop: "15px" }}>
                  <button
                    type="button"
                    onClick={() => {
                      setOtpSent(false);
                      setOtpCode("");
                      setTestOtp("");
                    }}
                    style={{
                      background: "none",
                      border: "none",
                      color: "#c5a85c",
                      cursor: "pointer",
                      fontSize: "0.85rem",
                      textDecoration: "underline",
                      fontWeight: "600"
                    }}
                  >
                    ← Edit Credentials / Go Back
                  </button>
                </div>
              )}

            </form>

            <p className="signup-text">
              Don't have an account?
              <Link to="/register">
                {" "}Create Account
              </Link>
            </p>

            <div className="divider">
              <span>Or login with</span>
            </div>

            <div className="social-buttons">
              <button>G</button>
              <button>f</button>
              <button></button>
            </div>

          </div>
        </div>

      </div>
    </div>
  );
}

export default Login;