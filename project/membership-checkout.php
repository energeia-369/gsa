<?php
$pageTitle = "Membership Checkout | GSA";
require_once __DIR__ . '/config/Config.php';
require_once __DIR__ . '/config/Settings.php';
$nxlCashbackPercentage = Settings::get('nxl_cashback_percentage', 0.05);
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$plan = $_GET['plan'] ?? 'standard';

$plans = [
  'standard' => [
    'name' => 'Standard Member',
    'price' => '2,499',
    'icon' => 'fa-user',
    'description' => 'Perfect for getting started with GSA community',
    'badge' => 'BEGINNER'
  ],
  'premium' => [
    'name' => 'Premium Member',
    'price' => '3,499',
    'icon' => 'fa-gem',
    'description' => 'Enhanced benefits and exclusive access',
    'badge' => 'POPULAR'
  ],
  'elite' => [
    'name' => 'Elite Member',
    'price' => '10,999',
    'icon' => 'fa-crown',
    'description' => 'Ultimate VIP experience with all perks',
    'badge' => 'VIP'
  ]
];

require_once __DIR__ . '/config/Settings.php';
$membershipPlansJson = Settings::get('membership_plans', null);
if ($membershipPlansJson) {
    $dbPlans = json_decode($membershipPlansJson, true);
    if (isset($dbPlans['standard'])) $plans['standard']['price'] = number_format($dbPlans['standard']['price']);
    if (isset($dbPlans['premium'])) $plans['premium']['price'] = number_format($dbPlans['premium']['price']);
    if (isset($dbPlans['elite'])) $plans['elite']['price'] = number_format($dbPlans['elite']['price']);
}

$selectedPlan = $plans[$plan] ?? $plans['standard'];
?>

<!-- Link to your CSS file -->
<link rel="stylesheet" href="assets/css/Membership.css?v=2">
<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<!-- Razorpay SDK -->


<div class="checkout-page flex items-center justify-center min-h-screen px-4 py-10">
  <div class="checkout-card">
    <div class="checkout-card-glow"></div>
    <div class="checkout-content">
      
      <div class="plan-badge">
        <i class="fas <?php echo htmlspecialchars($selectedPlan['icon']); ?>"></i>
        <?php echo htmlspecialchars($selectedPlan['badge']); ?>
      </div>
      
      <h1 class="checkout-title">
        <?php echo htmlspecialchars($selectedPlan['name']); ?>
      </h1>
      
      <p class="plan-description">
        <?php echo htmlspecialchars($selectedPlan['description']); ?>
      </p>
      
      <h2 class="checkout-price">
        ₹<?php echo htmlspecialchars($selectedPlan['price']); ?> 
        <span>/ Year</span>
      </h2>
      <div class="gst-text">+ 18% GST: ₹<span id="gstAmountSpan">0</span></div>
      <div class="total-text">Total: ₹<span id="totalAmountSpan">0</span></div>
      
      <div class="features-section">
        <div class="feature-item <?php echo $plan === 'standard' ? 'active' : ''; ?>">
          <i class="fas fa-check-circle"></i>
          <span>Access to all GSA events</span>
        </div>
        <div class="feature-item <?php echo $plan !== 'standard' ? 'active' : ''; ?>">
          <i class="fas <?php echo $plan === 'standard' ? 'fa-clock' : 'fa-check-circle'; ?>"></i>
          <span><?php echo $plan === 'standard' ? 'Basic networking opportunities' : 'Premium networking suite'; ?></span>
        </div>
        <div class="feature-item <?php echo $plan === 'elite' ? 'active' : ($plan !== 'standard' ? 'active' : ''); ?>">
          <i class="fas <?php echo $plan === 'standard' ? 'fa-star' : 'fa-trophy'; ?>"></i>
          <span><?php echo $plan === 'standard' ? 'Community forum access' : ($plan === 'premium' ? 'Priority support & workshops' : '1-on-1 mentorship & summit ticket'); ?></span>
        </div>
        <?php if($plan === 'elite'): ?>
        <div class="feature-item active exclusive">
          <i class="fas fa-crown"></i>
          <span>VIP lounge access + Annual gala invitation</span>
        </div>
        <?php endif; ?>
      </div>
      
      <form class="checkout-form" action="payment-success.php" method="GET" id="checkoutForm">
        <input type="hidden" name="type" value="membership">
        <input type="hidden" name="plan" value="<?php echo htmlspecialchars($plan); ?>">
        
        <div class="input-group">
          <i class="fas fa-user input-icon"></i>
          <input type="text" class="checkout-input" placeholder="Full Name" required id="fullName" autocomplete="off">
        </div>
        
        <div class="input-group">
          <i class="fas fa-envelope input-icon"></i>
          <input type="email" class="checkout-input" placeholder="Email Address" required id="email" autocomplete="off">
        </div>
        
        <div class="input-group">
          <i class="fas fa-phone input-icon"></i>
          <input type="tel" class="checkout-input" placeholder="Phone Number" required id="phone" autocomplete="off">
        </div>
        
        <div class="plan-switch">
          <span class="switch-label">Other plans:</span>
          <a href="?plan=standard" class="plan-link <?php echo $plan === 'standard' ? 'active-plan' : ''; ?>">Standard</a>
          <a href="?plan=premium" class="plan-link <?php echo $plan === 'premium' ? 'active-plan' : ''; ?>">Premium</a>
          <a href="?plan=elite" class="plan-link <?php echo $plan === 'elite' ? 'active-plan' : ''; ?>">Elite</a>
        </div>
        
        <button type="submit" class="checkout-btn" id="submitBtn">
          <i class="fas fa-lock"></i>
          Continue to Payment
          <i class="fas fa-arrow-right"></i>
        </button>
      </form>
      
      <div class="secure-checkout">
        <div class="secure-item">
          <i class="fas fa-shield-alt"></i>
          <span>Secure SSL Encryption</span>
        </div>
        <div class="secure-item">
          <i class="fas fa-clock"></i>
          <span>Instant Access</span>
        </div>
        <div class="secure-item">
          <i class="fas fa-headset"></i>
          <span>24/7 Support</span>
        </div>
      </div>
      
      <div class="guarantee">
        <i class="fas fa-medal"></i>
        <p>7-day money-back guarantee • No questions asked</p>
      </div>
      
    </div>
  </div>
</div>

<script>
const nxlCashbackPercentage = <?php echo json_encode((float)$nxlCashbackPercentage); ?>;
document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('checkoutForm');
  const submitBtn = document.getElementById('submitBtn');
  
  // Auto-fill user details if logged in
  const storedName = localStorage.getItem("userName");
  const storedEmail = localStorage.getItem("userEmail");
  const storedPhone = localStorage.getItem("userPhone");
  
  if (storedName) document.getElementById('fullName').value = storedName;
  if (storedEmail) document.getElementById('email').value = storedEmail;
  if (storedPhone) document.getElementById('phone').value = storedPhone;
  
  // Extract plan details for Razorpay
  const planName = "<?php echo htmlspecialchars($selectedPlan['name']); ?>";
  const planPriceStr = "<?php echo htmlspecialchars($selectedPlan['price']); ?>";
  const planPriceNum = parseInt(planPriceStr.replace(/,/g, ''), 10);
  const gstAmount = Math.floor(planPriceNum * 0.18);
  const totalAmount = planPriceNum + gstAmount;
  
  document.getElementById('gstAmountSpan').textContent = gstAmount.toLocaleString();
  document.getElementById('totalAmountSpan').textContent = totalAmount.toLocaleString();
  
  form.addEventListener('submit', async function(e) {
    e.preventDefault(); // Always prevent default to handle Razorpay first
    
    const fullName = document.getElementById('fullName').value.trim();
    const email = document.getElementById('email').value.trim();
    const phone = document.getElementById('phone').value.trim();
    
    if (!fullName || !email || !phone) {
      alert('Please fill in all fields before continuing.');
      return;
    }
    
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      alert('Please enter a valid email address.');
      return;
    }
    
    if (phone.length < 10) {
      alert('Please enter a valid phone number.');
      return;
    }
    
    submitBtn.classList.add('btn-loading');
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    submitBtn.disabled = true;
    
    try {
        const amountInPaise = Math.round(totalAmount * 100);
        
        // 1. Fetch Razorpay Order ID from backend
        const orderRes = await fetch("api/index.php/public-payment/create-razorpay-order", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ amount: totalAmount })
        });

        if (!orderRes.ok) {
            const errData = await orderRes.json();
            throw new Error(errData.error || "Failed to create order");
        }
        const orderData = await orderRes.json();

        // 2. Razorpay Options
        const options = {
            key: "<?php echo RAZORPAY_KEY_ID; ?>",
            amount: amountInPaise,
            currency: "INR",
            name: "ENERGEIA'S Global Ventures",
            description: `Membership Purchase - ${planName}`,
            image: "assets/logo.png",
            order_id: orderData.id,
            handler: function (response) {
                // Payment succeeded!
                // Calculate earned NXL credits
                const earnedCredits = Math.floor(planPriceNum * nxlCashbackPercentage);

                // Save order details to local storage for the success page
                const orderDataObj = {
                    id: response.razorpay_payment_id || orderData.id,
                    total: totalAmount,
                    orderDate: new Date().toLocaleDateString(),
                    nxlCoinsEarned: earnedCredits,
                    shippingAddress: "Membership Activation",
                    customerPhone: phone
                };
                localStorage.setItem("gsa_last_order", JSON.stringify(orderDataObj));

                // Inject the payment ID into the form
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'razorpay_payment_id';
                hiddenInput.value = response.razorpay_payment_id;
                form.appendChild(hiddenInput);
                
                // Now submit the form to payment-success.php
                form.submit();
            },
            prefill: {
                name: fullName,
                email: email,
                contact: phone
            },
            theme: {
                color: "#D4AF37", // Matches the golden theme
            },
            modal: {
                ondismiss: function() {
                    // Reset button if user closes the modal
                    submitBtn.classList.remove('btn-loading');
                    submitBtn.innerHTML = '<i class="fas fa-lock"></i> Continue to Payment <i class="fas fa-arrow-right"></i>';
                    submitBtn.disabled = false;
                }
            }
        };

        if (!window.Razorpay) {
            throw new Error("Razorpay SDK could not be loaded. Please check your internet connection.");
        }
        
        const rzp = new window.Razorpay(options);
        
        rzp.on('payment.failed', function (response){
            alert("Payment Failed: " + response.error.description);
            submitBtn.classList.remove('btn-loading');
            submitBtn.innerHTML = '<i class="fas fa-lock"></i> Continue to Payment <i class="fas fa-arrow-right"></i>';
            submitBtn.disabled = false;
        });
        
        rzp.open();
    } catch (error) {
        console.error(error);
        alert("Error initializing payment: " + error.message);
        submitBtn.classList.remove('btn-loading');
        submitBtn.innerHTML = '<i class="fas fa-lock"></i> Continue to Payment <i class="fas fa-arrow-right"></i>';
        submitBtn.disabled = false;
    }
  });
});
</script>


<?php require_once __DIR__ . '/includes/footer.php'; ?>
