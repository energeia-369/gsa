<?php
$pageTitle = "GLOBAL SPORTS ARENA | Register";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<link rel="stylesheet" href="assets/css/Register.css?v=2">
<!-- intl-tel-input CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/css/intlTelInput.css"/>
<style>
  .iti { width: 100%; display: block; }
  .iti__country-list { 
    background-color: #ffffff !important; 
    border: 1px solid #ccc !important; 
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
  }
  .iti__country-name, .iti__dial-code { 
    color: #333 !important; 
  }
  .iti__country:hover { background-color: #f0f0f0 !important; }
  .iti__divider { border-bottom: 1px solid #eee !important; }
  .iti__selected-dial-code { color: #000 !important; }
</style>

<div class="register-page">
  <div class="register-container flex flex-col lg:flex-row min-h-screen">
    <div class="register-brand hidden lg:flex flex-1 items-center justify-center p-12">
      <div class="brand-content max-w-md">
        <div class="brand-logo text-[#c5a85c] text-2xl font-bold mb-4">🏆 GLOBAL SPORTS ARENA</div>
        <h2 class="text-4xl font-black text-white mb-6">Join the Sports Community</h2>
        <p class="text-gray-300 text-lg">
          Create your account to register for tournaments, shop sports gear, and earn NXL credits.
        </p>
      </div>
    </div>

    <div class="register-form flex-1 flex items-center justify-center p-6 sm:p-12">
      <div class="w-full max-w-lg">
        <h1>Create Account</h1>
        <p>Get started with GLOBAL SPORTS ARENA today</p>

      <!-- Clean, professional verification dispatch status indicator -->
      <div class="otp-test-alert" id="otpAlertBox" style="
        background: rgba(197, 168, 92, 0.08);
        border: 1px dashed #d4af37;
        padding: 15px;
        border-radius: 15px;
        margin-bottom: 20px;
        font-size: 0.85rem;
        line-height: 1.5;
        display: none;
      ">
        <strong style="color: #d4af37; display: block; margin-bottom: 5px">
          🔒 SECURE DUAL OTP VERIFICATION
        </strong>
        <p style="margin: 0; color: #e2e2e2">
          Two secure 6-digit authentication codes have been generated. One has been sent to your Gmail (<strong id="otpEmailSpan"></strong>), and the other to your mobile number.
        </p>
      </div>

      <form id="registerForm" onsubmit="handleRegisterSubmit(event)">
        <div class="input-group">
          <label>Full Name</label>
          <input
            type="text"
            id="fullNameInput"
            placeholder="Enter your full name"
            required
          />
        </div>

        <div class="input-group">
          <label>Email Address</label>
          <input
            type="email"
            id="emailInput"
            placeholder="you@example.com"
            required
          />
        </div>

        <div class="input-group">
          <label>Phone Number</label>
          <input
            type="tel"
            id="phoneInput"
            placeholder="Enter mobile number"
            required
          />
        </div>

        <div class="input-group">
          <label>Password</label>
          <input
            type="password"
            id="passwordInput"
            placeholder="Create a strong password"
            required
            autoComplete="new-password"
          />
        </div>

        <div class="input-group">
          <label>Account Type / Role</label>
          <select id="roleSelect" onchange="toggleAdminCode()" style="width: 100%; padding: 0.9rem 1rem; border: 1px solid rgba(197, 168, 92, 0.25); border-radius: 12px; font-size: 0.95rem; background-color: #0b0c10; color: #fff; outline: none; appearance: none; background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23c5a85c%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem top 50%; background-size: 0.65rem auto; cursor: pointer;">
            <option value="USER" style="background-color: #12131c; color: #fff; padding: 10px;">User (Standard Account)</option>
            <option value="ADMIN" style="background-color: #12131c; color: #fff; padding: 10px;">Admin (Administrative Account)</option>
            <option value="MERCHANT" style="background-color: #12131c; color: #fff; padding: 10px;">Merchant (Merchant Account)</option>
          </select>
        </div>

        <div class="input-group" id="adminCodeGroup" style="display: none;">
          <label>Admin Access Code</label>
          <input
            type="password"
            id="adminCodeInput"
            placeholder="Enter secret admin verification code"
            autoComplete="new-password"
          />
        </div>

        <div class="input-group" id="merchantCodeGroup" style="display: none;">
          <label>Merchant Secret Code</label>
          <input
            type="password"
            id="merchantCodeInput"
            placeholder="Enter Merchant Secret Code"
            autoComplete="new-password"
          />
        </div>

        <!-- Separate OTP inputs for Gmail and Mobile -->
        <div id="otpFieldsGroup" style="display: none;">
          <div class="input-group" style="margin-top: 20px">
            <label style="color: #d4af37; font-weight: bold">Gmail Verification Code (OTP) *</label>
            <input
              type="text"
              id="emailOtpInput"
              placeholder="Enter 6-digit Gmail code"
              maxLength="6"
              style="
                border: 2px solid #d4af37;
                background: rgba(212, 175, 55, 0.05);
                font-size: 1.1rem;
                font-weight: bold;
                text-align: center;
                letter-spacing: 4px;
              "
            />
          </div>

          <div class="input-group" style="margin-top: 15px">
            <label style="color: #d4af37; font-weight: bold">Mobile Verification Code (OTP) *</label>
            <input
              type="text"
              id="mobileOtpInput"
              placeholder="Enter 6-digit Mobile code"
              maxLength="6"
              style="
                border: 2px solid #d4af37;
                background: rgba(212, 175, 55, 0.05);
                font-size: 1.1rem;
                font-weight: bold;
                text-align: center;
                letter-spacing: 4px;
              "
            />
          </div>
        </div>

        <button type="submit" id="submitBtn" class="register-btn" style="margin-top: 25px">
          Send OTP Verification Codes →
        </button>

        <div id="goBackContainer" style="text-align: center; margin-top: 15px; display: none;">
          <button
            type="button"
            onclick="resetRegisterForm()"
            style="
              background: none;
              border: none;
              color: #d4af37;
              cursor: pointer;
              font-size: 0.85rem;
              text-decoration: underline;
              font-weight: 600;
            "
          >
            ← Edit Details / Change Email
          </button>
        </div>

        <div class="login-link">
          Already have an account? <a href="login.php">Sign In</a>
        </div>
      </form>
      </div>
    </div>
  </div>
</div>

<script>
let otpSent = false;
let loading = false;

function toggleAdminCode() {
    const role = document.getElementById("roleSelect").value;
    const adminGroup = document.getElementById("adminCodeGroup");
    const adminInput = document.getElementById("adminCodeInput");
    const merchantGroup = document.getElementById("merchantCodeGroup");
    const merchantInput = document.getElementById("merchantCodeInput");
    
    if (role === "ADMIN") {
        adminGroup.style.display = "block";
        adminInput.required = true;
        merchantGroup.style.display = "none";
        merchantInput.required = false;
        merchantInput.value = "";
    } else if (role === "MERCHANT") {
        merchantGroup.style.display = "block";
        merchantInput.required = true;
        adminGroup.style.display = "none";
        adminInput.required = false;
        adminInput.value = "";
    } else {
        adminGroup.style.display = "none";
        adminInput.required = false;
        adminInput.value = "";
        merchantGroup.style.display = "none";
        merchantInput.required = false;
        merchantInput.value = "";
    }
}

async function handleRegisterSubmit(e) {
    e.preventDefault();
    if (loading) return;

    const fullName = document.getElementById("fullNameInput").value.trim();
    const email = document.getElementById("emailInput").value.trim();
    const phoneNumber = window.iti ? window.iti.getNumber() : document.getElementById("phoneInput").value.trim();
    const password = document.getElementById("passwordInput").value;
    const role = document.getElementById("roleSelect").value;
    const adminCode = document.getElementById("adminCodeInput").value.trim();
    const merchantCode = document.getElementById("merchantCodeInput").value.trim();
    
    const emailOtp = document.getElementById("emailOtpInput").value.trim();
    const mobileOtp = document.getElementById("mobileOtpInput").value.trim();

    if (!fullName || !email || !phoneNumber || !password) {
        alert("Please fill all fields");
        return;
    }

    if (role === "ADMIN" && adminCode !== "PLAYADMIN2026") {
        alert("Error: Invalid Admin Access Code! You are not authorized to register as an administrator.");
        return;
    }

    if (!otpSent) {
        // Step 1: Send separate registration OTPs
        try {
            setLoadingState(true);
            const res = await fetch("api/index.php/auth/register-send-otp", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    fullName, email, phoneNumber, password, role, secretCode: merchantCode || adminCode
                })
            });
            const responseData = await res.json();

            if (responseData.success) {
                otpSent = true;
                document.getElementById("otpAlertBox").style.display = "block";
                document.getElementById("otpEmailSpan").textContent = email;
                
                // Disable inputs
                document.getElementById("fullNameInput").disabled = true;
                document.getElementById("emailInput").disabled = true;
                document.getElementById("phoneInput").disabled = true;
                document.getElementById("passwordInput").disabled = true;
                document.getElementById("roleSelect").disabled = true;
                document.getElementById("adminCodeInput").disabled = true;
                
                document.getElementById("otpFieldsGroup").style.display = "block";
                document.getElementById("emailOtpInput").required = true;
                document.getElementById("mobileOtpInput").required = true;
                document.getElementById("goBackContainer").style.display = "block";
                
                // DEV INSPECTION: Show OTPs at the top
                const devBanner = document.createElement("div");
                devBanner.style = "background: #c5a85c; color: #000; padding: 15px; text-align: center; font-weight: bold; font-size: 1.2rem; position: fixed; top: 0; left: 0; width: 100%; z-index: 9999;";
                devBanner.innerHTML = `[DEVELOPER MODE] Email OTP: ${responseData.devEmailOtp} &nbsp;|&nbsp; Mobile OTP: ${responseData.devMobileOtp}`;
                document.body.prepend(devBanner);

                alert(responseData.message || "Verification codes successfully sent! Please check your Gmail inbox and mobile messages.");
            } else {
                alert(responseData.message || "Failed to dispatch verification codes");
            }
        } catch (error) {
            console.error("Send OTP Error:", error);
            alert("Failed to send verification codes. Please check email/phone formatting.");
        } finally {
            setLoadingState(false);
        }
    } else {
        // Step 2: Verify both OTPs and complete registration
        if (!emailOtp || !mobileOtp) {
            alert("Please enter both your Gmail OTP and Mobile OTP to complete registration.");
            return;
        }

        try {
            setLoadingState(true);
            const res = await fetch("api/index.php/auth/register", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    fullName,
                    email,
                    phoneNumber,
                    password,
                    role,
                    emailOtp,
                    mobileOtp,
                    secretCode: merchantCode || adminCode
                })
            });
            const responseData = await res.json();

            if (responseData.token || responseData.success) {
                alert(responseData.message || "Registration successful!");
                resetRegisterForm();
                document.getElementById("registerForm").reset();
                window.location.href = "login.php";
            } else {
                alert(responseData.message || "Registration Failed");
            }
        } catch (error) {
            console.error("Registration Error:", error);
            alert("Registration Failed. Invalid OTP codes entered.");
        } finally {
            setLoadingState(false);
        }
    }
}

function setLoadingState(isLoading) {
    loading = isLoading;
    const submitBtn = document.getElementById("submitBtn");
    if (isLoading) {
        submitBtn.disabled = true;
        submitBtn.textContent = "Processing...";
    } else {
        submitBtn.disabled = false;
        submitBtn.textContent = otpSent ? "Verify & Create Account →" : "Send OTP Verification Codes →";
    }
}

function resetRegisterForm() {
    otpSent = false;
    document.getElementById("otpAlertBox").style.display = "none";
    document.getElementById("fullNameInput").disabled = false;
    document.getElementById("emailInput").disabled = false;
    document.getElementById("phoneInput").disabled = false;
    document.getElementById("passwordInput").disabled = false;
    document.getElementById("roleSelect").disabled = false;
    document.getElementById("adminCodeInput").disabled = false;
    
    document.getElementById("otpFieldsGroup").style.display = "none";
    document.getElementById("emailOtpInput").required = false;
    document.getElementById("mobileOtpInput").required = false;
    document.getElementById("emailOtpInput").value = "";
    document.getElementById("mobileOtpInput").value = "";
    document.getElementById("goBackContainer").style.display = "none";
    
    const submitBtn = document.getElementById("submitBtn");
    submitBtn.textContent = "Send OTP Verification Codes →";
}
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>
<script>
// Initialize intl-tel-input
const phoneInputEl = document.querySelector("#phoneInput");
if (phoneInputEl) {
    window.iti = window.intlTelInput(phoneInputEl, {
        initialCountry: "in",
        preferredCountries: ["in", "us", "gb", "ae", "sg"],
        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js",
        separateDialCode: true
    });
}
</script>



<?php require_once __DIR__ . '/includes/footer.php'; ?>
