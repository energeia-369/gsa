import "../styles/Register.css";
import { useState } from "react";
import { useNavigate, Link } from "react-router-dom";
import axios from "axios";

function Register() {
  const navigate = useNavigate();

  const [formData, setFormData] = useState({
    fullName: "",
    email: "",
    phoneNumber: "",
    password: "",
    role: "USER",
    adminCode: "",
  });

  // OTP specific states
  const [otpSent, setOtpSent] = useState(false);
  const [emailOtp, setEmailOtp] = useState("");
  const [mobileOtp, setMobileOtp] = useState("");
  const [loading, setLoading] = useState(false);

  const handleChange = (e) => {
    setFormData({
      ...formData,
      [e.target.name]: e.target.value,
    });
  };

  const handleRegister = async (e) => {
    e.preventDefault();

    if (!formData.fullName || !formData.email || !formData.phoneNumber || !formData.password) {
      alert("Please fill all fields");
      return;
    }

    if (formData.role === "ADMIN" && formData.adminCode !== "PLAYADMIN2026") {
      alert("Error: Invalid Admin Access Code! You are not authorized to register as an administrator.");
      return;
    }

    if (!otpSent) {
      // Step 1: Send separate registration OTPs
      try {
        setLoading(true);
        const response = await axios.post(
          "http://localhost:8080/api/auth/register-send-otp",
          {
            email: formData.email,
            phoneNumber: formData.phoneNumber
          }
        );

        if (response.data && response.data.success) {
          setOtpSent(true);
          alert(response.data.message || "Verification codes successfully sent! Please check your Gmail inbox and mobile messages.");
        } else {
          alert(response.data.message || "Failed to dispatch verification codes");
        }
      } catch (error) {
        console.log("Send OTP Error:", error);
        if (error.response) {
          alert(error.response.data.message || error.response.data || "Failed to send verification codes");
        } else {
          alert("Spring Boot backend is not running or not reachable");
        }
      } finally {
        setLoading(false);
      }
    } else {
      // Step 2: Verify both OTPs and complete registration
      if (!emailOtp || !mobileOtp) {
        alert("Please enter both your Gmail OTP and Mobile OTP to complete registration.");
        return;
      }

      try {
        setLoading(true);

        const response = await axios.post(
          "http://localhost:8080/api/auth/register",
          {
            fullName: formData.fullName,
            email: formData.email,
            phoneNumber: formData.phoneNumber,
            password: formData.password,
            role: formData.role,
            emailOtp: emailOtp,
            mobileOtp: mobileOtp
          },
          {
            headers: {
              "Content-Type": "application/json",
            },
          }
        );

        if (response.data.token) {
          alert(response.data.message || "Registration successful!");

          setFormData({
            fullName: "",
            email: "",
            phoneNumber: "",
            password: "",
            role: "USER",
            adminCode: "",
          });
          setOtpSent(false);
          setEmailOtp("");
          setMobileOtp("");

          navigate("/login");
        } else {
          alert(response.data.message || "Registration Failed");
        }
      } catch (error) {
        console.log("Registration Error:", error);

        if (error.response) {
          alert(error.response.data.message || error.response.data || "Backend error");
        } else {
          alert("Registration Failed: backend is not reachable");
        }
      } finally {
        setLoading(false);
      }
    }
  };

  return (
    <div className="register-page">
      <div className="register-container">
        <div className="register-brand">
          <div className="brand-content">
            <div className="brand-logo">🏆 GLOBAL SPORTS ARENA</div>
            <h2>Join the Sports Community</h2>
            <p>
              Create your account to register for tournaments, shop sports gear,
              and earn NXL credits.
            </p>
          </div>
        </div>

        <div className="register-form">
          <div className="form-header">
            <h1>Create Account</h1>
            <p>Get started with GLOBAL SPORTS ARENA today</p>
          </div>

          {/* Clean, professional verification dispatch status indicator */}
          {otpSent && (
            <div className="otp-test-alert" style={{
              background: "rgba(197, 168, 92, 0.08)",
              border: "1px dashed #d4af37",
              padding: "15px",
              borderRadius: "15px",
              marginBottom: "20px",
              fontSize: "0.85rem",
              lineHeight: "1.5"
            }}>
              <strong style={{ color: "#d4af37", display: "block", marginBottom: "5px" }}>
                🔒 SECURE DUAL OTP VERIFICATION
              </strong>
              <p style={{ margin: 0, color: "#e2e2e2" }}>
                Two secure 6-digit authentication codes have been generated. One has been sent to your Gmail (<strong>{formData.email}</strong>), and the other to your mobile number.
              </p>
            </div>
          )}

          <form onSubmit={handleRegister}>
            <div className="input-group">
              <label>Full Name</label>
              <input
                type="text"
                name="fullName"
                placeholder="Enter your full name"
                value={formData.fullName}
                onChange={handleChange}
                required
                disabled={otpSent}
              />
            </div>

            <div className="input-group">
              <label>Email Address</label>
              <input
                type="email"
                name="email"
                placeholder="you@example.com"
                value={formData.email}
                onChange={handleChange}
                required
                disabled={otpSent}
              />
            </div>

            <div className="input-group">
              <label>Phone Number</label>
              <input
                type="tel"
                name="phoneNumber"
                placeholder="Enter mobile number"
                value={formData.phoneNumber}
                onChange={handleChange}
                required
                disabled={otpSent}
              />
            </div>

            <div className="input-group">
              <label>Password</label>
              <input
                type="password"
                name="password"
                placeholder="Create a strong password"
                value={formData.password}
                onChange={handleChange}
                required
                disabled={otpSent}
                autoComplete="new-password"
              />
            </div>

            <div className="input-group">
              <label>Account Type / Role</label>
              <select
                name="role"
                value={formData.role}
                onChange={handleChange}
                disabled={otpSent}
                style={{
                  width: "100%",
                  padding: "15px 18px",
                  border: "2px solid #d4af37",
                  borderRadius: "15px",
                  outline: "none",
                  background: "white",
                  color: "#6b3e26",
                  fontSize: "16px",
                  fontWeight: "600",
                  cursor: "pointer",
                }}
              >
                <option value="USER">User (Standard Account)</option>
                <option value="ADMIN">Admin (Administrative Account)</option>
              </select>
            </div>

            {formData.role === "ADMIN" && (
              <div className="input-group">
                <label>Admin Access Code</label>
                <input
                  type="password"
                  name="adminCode"
                  placeholder="Enter secret admin verification code"
                  value={formData.adminCode}
                  onChange={handleChange}
                  required
                  disabled={otpSent}
                  autoComplete="new-password"
                />
              </div>
            )}

            {/* Separate OTP inputs for Gmail and Mobile */}
            {otpSent && (
              <>
                <div className="input-group" style={{ marginTop: "20px" }}>
                  <label style={{ color: "#d4af37", fontWeight: "bold" }}>Gmail Verification Code (OTP) *</label>
                  <input
                    type="text"
                    placeholder="Enter 6-digit Gmail code"
                    value={emailOtp}
                    onChange={(e) => setEmailOtp(e.target.value)}
                    required
                    maxLength={6}
                    style={{
                      border: "2px solid #d4af37",
                      background: "rgba(212, 175, 55, 0.05)",
                      fontSize: "1.1rem",
                      fontWeight: "bold",
                      textAlign: "center",
                      letterSpacing: "4px"
                    }}
                  />
                </div>

                <div className="input-group" style={{ marginTop: "15px" }}>
                  <label style={{ color: "#d4af37", fontWeight: "bold" }}>Mobile Verification Code (OTP) *</label>
                  <input
                    type="text"
                    placeholder="Enter 6-digit Mobile code"
                    value={mobileOtp}
                    onChange={(e) => setMobileOtp(e.target.value)}
                    required
                    maxLength={6}
                    style={{
                      border: "2px solid #d4af37",
                      background: "rgba(212, 175, 55, 0.05)",
                      fontSize: "1.1rem",
                      fontWeight: "bold",
                      textAlign: "center",
                      letterSpacing: "4px"
                    }}
                  />
                </div>
              </>
            )}

            <button type="submit" className="register-btn" disabled={loading} style={{ marginTop: "25px" }}>
              {loading
                ? "Processing..."
                : otpSent
                  ? "Verify & Create Account →"
                  : "Send OTP Verification Codes →"}
            </button>

            {otpSent && (
              <div style={{ textAlign: "center", marginTop: "15px" }}>
                <button
                  type="button"
                  onClick={() => {
                    setOtpSent(false);
                    setEmailOtp("");
                    setMobileOtp("");
                  }}
                  style={{
                    background: "none",
                    border: "none",
                    color: "#d4af37",
                    cursor: "pointer",
                    fontSize: "0.85rem",
                    textDecoration: "underline",
                    fontWeight: "600"
                  }}
                >
                  ← Edit Details / Change Email
                </button>
              </div>
            )}

            <div className="login-link">
              Already have an account? <Link to="/login">Sign In</Link>
            </div>
          </form>
        </div>
      </div>
    </div>
  );
}

export default Register;