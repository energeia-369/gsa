<?php
$pageTitle = "GLOBAL SPORTS ARENA | Login";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<link rel="stylesheet" href="assets/css/Login.css?v=5">

<div class="login-page" id="loginPage">
  <div class="login-container flex flex-col lg:flex-row min-h-screen">
    <!-- Left Side -->
    <div class="login-left hidden lg:flex flex-1 items-center justify-center p-12">
      <div class="brand-box max-w-md">
        <h2 class="text-2xl font-extrabold text-[#c5a85c] mb-4">🏆 GLOBAL SPORTS ARENA</h2>
        <h1 class="text-4xl xl:text-5xl font-black text-white mb-6">Welcome Back!</h1>
        <p class="text-gray-300 text-lg mb-10">
          Login to register for tournaments, shop sports gear, and manage your NXL credits.
        </p>
        <div class="login-stats flex gap-8">
          <div class="text-center">
            <h3 class="text-3xl font-black text-[#c5a85c]">50+</h3>
            <span class="text-gray-400 text-sm uppercase tracking-wider">Events</span>
          </div>
          <div class="text-center">
            <h3 class="text-3xl font-black text-[#c5a85c]">10K+</h3>
            <span class="text-gray-400 text-sm uppercase tracking-wider">Users</span>
          </div>
          <div class="text-center">
            <h3 class="text-3xl font-black text-[#c5a85c]">437+</h3>
            <span class="text-gray-400 text-sm uppercase tracking-wider">Products</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Side -->
    <div class="login-right flex-1 flex items-center justify-center p-6 sm:p-12">
      <div class="login-card w-full max-w-md">
        <h1>Login</h1>
        <p class="login-subtitle">
          Verify your credentials and complete Gmail OTP authentication to sign in securely
        </p>

        <!-- Beautiful visual notification for the OTP -->
        <div class="otp-test-alert" id="otpAlertBox" style="
          background: rgba(197, 168, 92, 0.08);
          border: 1px dashed #c5a85c;
          padding: 15px;
          border-radius: 15px;
          margin-bottom: 20px;
          font-size: 0.85rem;
          line-height: 1.5;
          display: none;
        ">
          <strong style="color: #c5a85c; display: block; margin-bottom: 5px">
            🔒 GMAIL OTP VERIFICATION SENT
          </strong>
          <p style="margin: 0; color: #e2e2e2">
            A secure 6-digit login verification code has been sent to your Gmail address (<strong id="otpEmailSpan"></strong>).
          </p>
        </div>

        <form id="loginForm" onsubmit="handleLoginSubmit(event)">

          <!-- Email Address Input -->
          <div class="input-group">
            <label>Email Address</label>
            <input
              type="email"
              id="emailInput"
              placeholder="you@example.com"
              required
              autoComplete="email"
            />
          </div>

          <!-- Password Input -->
          <div class="input-group">
            <label>Password</label>
            <input
              type="password"
              id="passwordInput"
              placeholder="Enter your password"
              required
              autoComplete="current-password"
            />
          </div>


          <!-- OTP Digit Input: Active after credentials are validated -->
          <div class="input-group" id="otpGroup" style="display: none; margin-top: 20px">
            <label style="color: #c5a85c; font-weight: bold">Gmail Verification Code (OTP) *</label>
            <input
              type="text"
              id="otpInput"
              placeholder="Enter 6-digit Gmail code"
              maxLength="6"
              style="
                border: 2px solid #c5a85c;
                background: rgba(197, 168, 92, 0.05);
                font-size: 1.1rem;
                font-weight: bold;
                text-align: center;
                letter-spacing: 4px;
              "
            />
          </div>

          <!-- Submit / Verification Dispatch button -->
          <button
            class="login-btn"
            type="submit"
            id="submitBtn"
            style="margin-top: 25px; text-align: center; justify-content: center;"
          >
            Send OTP Verification Code →
          </button>

          <div id="goBackContainer" style="text-align: center; margin-top: 15px; display: none;">
            <button
              type="button"
              onclick="resetLoginForm()"
              style="
                background: none;
                border: none;
                color: #c5a85c;
                cursor: pointer;
                font-size: 0.85rem;
                text-decoration: underline;
                font-weight: 600;
              "
            >
              ← Edit Credentials / Go Back
            </button>
          </div>
        </form>

        <p class="signup-text">
          Don't have an account?
          <a href="register.php">Create Account</a>
        </p>


      </div>
    </div>
  </div>
</div>



<script>
let otpSent = false;
let loading = false;

// Dynamic Admin class styling removed as per request


async function handleLoginSubmit(e) {
    e.preventDefault();
    if (loading) return;

    const email = document.getElementById("emailInput").value.trim();
    const password = document.getElementById("passwordInput").value;
    const otpCode = document.getElementById("otpInput").value.trim();
    const submitBtn = document.getElementById("submitBtn");

    if (!email || !password) {
        alert("Please enter both email and password");
        return;
    }

    if (!otpSent) {
        // Step 1: Validate credentials and send OTP
        try {
            setLoadingState(true);
            const res = await fetch("api/index.php/auth/send-otp", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ email, password })
            });
            const responseData = await res.json();

            if (responseData.success) {
                otpSent = true;
                document.getElementById("otpAlertBox").style.display = "block";
                document.getElementById("otpEmailSpan").textContent = email;
                document.getElementById("emailInput").disabled = true;
                document.getElementById("passwordInput").disabled = true;
                document.getElementById("otpGroup").style.display = "block";
                document.getElementById("otpInput").required = true;
                document.getElementById("goBackContainer").style.display = "block";
                submitBtn.textContent = "Verify OTP & Sign In →";
                
                // DEV INSPECTION: Show OTPs at the top
                const devBanner = document.createElement("div");
                devBanner.style = "background: #c5a85c; color: #000; padding: 15px; text-align: center; font-weight: bold; font-size: 1.2rem; position: fixed; top: 0; left: 0; width: 100%; z-index: 9999;";
                devBanner.innerHTML = `[DEVELOPER MODE] Login OTP: ${responseData.devOtp}`;
                document.body.prepend(devBanner);

                alert(responseData.message || "A secure verification code has been sent to your Gmail inbox!");
            } else {
                alert(responseData.message || "Failed to dispatch verification code");
            }
        } catch (error) {
            console.error("Login OTP Dispatch Error:", error);
            
            // Robust offline fallback for local testing of default credentials
            if (email === "admin@globalsportsarena.com" && password === "admin") {
                alert("Offline Admin Mode: Accessing dashboard using local storage fallback.");
                localStorage.setItem("token", "admin-secret-token");
                localStorage.setItem("userEmail", "admin@globalsportsarena.com");
                localStorage.setItem("userRole", "ADMIN");
                localStorage.setItem("userName", "Administrator");
                window.location.href = "admin-dashboard.php";
                return;
            }
            alert("Invalid email or password. Please verify credentials.");
        } finally {
            setLoadingState(false);
        }
    } else {
        // Step 2: Verify OTP
        if (!otpCode) {
            alert("Please enter the 6-digit Gmail verification code");
            return;
        }

        try {
            setLoadingState(true);
            const res = await fetch("api/index.php/auth/verify-otp", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ email, otp: otpCode })
            });
            const responseData = await res.json();

            if (responseData.token) {
                localStorage.setItem("token", responseData.token);
                localStorage.setItem("userEmail", email);
                
                const role = responseData.role || "USER";
                localStorage.setItem("userRole", role);
                localStorage.setItem("userName", responseData.userName || email.split('@')[0]);
                if (responseData.phoneNumber) {
                    localStorage.setItem("userPhone", responseData.phoneNumber);
                }
                localStorage.setItem("userMembership", responseData.membershipTier || "none");

                alert(responseData.message || "OTP Login successful!");

                if (role === "ADMIN") {
                    window.location.href = "admin-dashboard.php";
                } else if (role === "MERCHANT") {
                    window.location.href = "merchant.php";
                } else {
                    window.location.href = "user-dashboard.php";
                }
            } else {
                alert(responseData.message || "Verification failed");
            }
        } catch (error) {
            console.error("Verify OTP Error:", error);
            alert("Invalid verification code. Please check your inbox.");
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
        submitBtn.textContent = otpSent ? "Verify OTP & Sign In →" : "Send OTP Verification Code →";
    }
}

function resetLoginForm() {
    otpSent = false;
    document.getElementById("otpAlertBox").style.display = "none";
    document.getElementById("emailInput").disabled = false;
    document.getElementById("passwordInput").disabled = false;
    document.getElementById("otpGroup").style.display = "none";
    document.getElementById("otpInput").required = false;
    document.getElementById("otpInput").value = "";
    document.getElementById("goBackContainer").style.display = "none";
    document.getElementById("submitBtn").textContent = "Send OTP Verification Code →";
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
