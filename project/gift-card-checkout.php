<?php
require_once 'includes/header.php';
require_once 'includes/navbar.php';
require_once 'config/Database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['card_id'])) {
    echo "<script>window.location.href='gift-cards.php';</script>";
    exit;
}

$id = intval($_POST['card_id']);
$db = (new Database())->getConnection();
$stmt = $db->prepare("SELECT * FROM gift_cards WHERE id = ?");
$stmt->execute([$id]);
$card = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$card) {
    echo "<script>window.location.href='gift-cards.php';</script>";
    exit;
}
?>

<link rel="stylesheet" href="assets/css/gift-cards.css">

<main class="gc-container">
    <div class="gc-form-container w-full max-w-xl mx-auto px-4">
        <h2 style="color: var(--gc-gold); margin-bottom: 20px; text-align: center;">Gift Card Checkout</h2>
        
        <form action="gift-card-success.php" method="POST" id="gc_checkout_form">
            <input type="hidden" name="gift_card_id" value="<?php echo $card['id']; ?>">
            <input type="hidden" id="gc_amount" value="<?php echo $card['price']; ?>">
            <input type="hidden" name="final_amount" id="gc_final_amount_hidden" value="">
            <input type="hidden" name="gst" id="gc_gst_hidden" value="">
            <input type="hidden" name="discount" id="gc_discount_hidden" value="">
            <input type="hidden" name="nxl_earned" id="gc_nxl_earned_hidden" value="">
            <input type="hidden" name="payment_method" id="payment_method" value="upi">

            <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                <!-- Buyer Details -->
                <div style="flex: 1; min-width: 300px;">
                    <h3 style="margin-bottom: 15px; color: var(--gc-gold);">Your Details</h3>
                    <div class="gc-form-group">
                        <label class="gc-form-label">Your Name *</label>
                        <input type="text" id="sender_name" name="sender_name" class="gc-form-input" required placeholder="Loading...">
                    </div>
                    <div class="gc-form-group">
                        <label class="gc-form-label">Your Email *</label>
                        <input type="email" id="sender_email" name="sender_email" class="gc-form-input" required placeholder="Loading...">
                    </div>
                    <div class="gc-form-group">
                        <label class="gc-form-label">Your Mobile *</label>
                        <input type="text" id="sender_mobile" name="sender_mobile" class="gc-form-input" placeholder="Loading...">
                    </div>
                    <div class="gc-form-group">
                        <label class="gc-form-label">Delivery Date *</label>
                        <input type="date" name="delivery_date" class="gc-form-input" required value="<?php echo date('Y-m-d'); ?>" min="<?php echo date('Y-m-d'); ?>">
                    </div>
                </div>

                <div style="flex: 1; min-width: 300px;">
                    <h3 style="margin-bottom: 15px; color: transparent; user-select: none;">Options</h3>
                    <div class="gc-form-group">
                        <label class="gc-form-label">Personal Message (Optional)</label>
                        <textarea name="message" class="gc-form-input" rows="4"></textarea>
                    </div>
                    <div class="gc-form-group">
                        <label class="gc-form-label">Quantity</label>
                        <input type="number" name="quantity" id="gc_qty" class="gc-form-input" value="1" min="1" max="10">
                    </div>
                    <div class="gc-form-group">
                        <label class="gc-form-label">Use NXL Credits</label>
                        <div style="display:flex; align-items:center; gap:10px;">
                            <input type="number" id="gc_nxl_use" name="nxl_coins_used" class="gc-form-input" placeholder="0" min="0" style="width:100px;">
                            <span style="font-size:0.85rem; color:#888;">Available: <span id="gc_nxl_available">0</span></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="gc-summary">
                <h3 style="margin-bottom: 15px;">Order Summary</h3>
                <div class="gc-summary-row">
                    <span><?php echo htmlspecialchars($card['name']); ?></span>
                    <span id="gc_subtotal_display">₹0.00</span>
                </div>
                <div class="gc-summary-row" id="gc_membership_discount_row" style="color: #c5a85c; display: none;">
                    <span>Membership Discount</span>
                    <span id="gc_membership_discount">-₹0.00</span>
                </div>
                <div class="gc-summary-row" id="gc_nxl_discount_row" style="color: #10b981; display: none;">
                    <span>NXL Credits Used</span>
                    <span id="gc_nxl_discount">-₹0.00</span>
                </div>
                <div class="gc-summary-row">
                    <span>GST (18%)</span>
                    <span id="gc_gst">₹0.00</span>
                </div>
                <div class="gc-summary-row gc-summary-total">
                    <span>Final Amount</span>
                    <span id="gc_total">₹0.00</span>
                </div>
                <div style="text-align: right; margin-top: 5px; font-size: 0.85rem; color: #10b981; font-weight: 600;">
                    🎉 You will earn <span id="gc_nxl_earned_display">0</span> NXL Credits
                </div>
            </div>


            <div style="margin-top: 30px; text-align: center;">
                <button type="submit" class="gc-btn gc-btn-solid" style="width: 100%; padding: 15px; font-size: 1.1rem;">Proceed to Payment</button>
            </div>
        </form>
    </div>
</main>

<style>
@keyframes autofillGlow {
    0%   { background-color: #fffbe6; box-shadow: 0 0 0 3px rgba(201,163,74,0.35); }
    100% { background-color: #fdf6e3; box-shadow: 0 0 0 0 transparent; }
}
.autofilled {
    animation: autofillGlow 1.4s ease forwards;
    border-color: #c9a34a !important;
}
</style>

<script>
// Auto-fill user details on page load
document.addEventListener('DOMContentLoaded', async function() {
    const userEmail = localStorage.getItem('userEmail');
    if (!userEmail) return;

    try {
        const res = await fetch(`api/index.php/user/profile?email=${encodeURIComponent(userEmail)}`);
        const data = await res.json();
        if (!data.email) return;

        const fill = (id, value) => {
            if (!value) return;
            const el = document.getElementById(id);
            if (el && !el.value) {
                el.value = value;
                el.placeholder = '';
                el.classList.add('autofilled');
                setTimeout(() => el.classList.remove('autofilled'), 1500);
            }
        };

        fill('sender_name',   data.fullName     || data.full_name || '');
        fill('sender_email',  data.email        || '');
        fill('sender_mobile', data.phone_number || data.phone    || '');
    } catch(e) {
        // Silently fail — user can fill manually
        console.warn('Could not auto-fill user profile:', e);
    }
});

document.getElementById('gc_checkout_form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = this.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.textContent = 'Processing Payment...';
    
    // Convert FormData to JSON
    const formData = new FormData(this);
    const data = {};
    formData.forEach((value, key) => data[key] = value);
    // Add amount which is hidden
    data['amount'] = document.getElementById('gc_amount').value;
    
    // Map sender details to recipient details
    data['recipient_name'] = data['sender_name'];
    data['recipient_email'] = data['sender_email'];
    data['recipient_mobile'] = data['sender_mobile'];
    
    try {
        const finalAmount = parseFloat(data['final_amount']);

        // Bypass Razorpay entirely if the order amount is zero
        if (finalAmount <= 0) {
            data['payment_status'] = 'completed';
            
            try {
                const res = await fetch('api/index.php/giftcards/checkout', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(data)
                });
                const result = await res.json();
                if(result.success) {
                    // Post result to success page
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = 'gift-card-success.php';
                    
                    for(const key in result.data) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = key;
                        input.value = result.data[key];
                        form.appendChild(input);
                    }
                    document.body.appendChild(form);
                    form.submit();
                } else {
                    alert(result.message || 'Payment failed to register on server.');
                    btn.disabled = false;
                    btn.textContent = 'Proceed to Payment';
                }
            } catch(err) {
                alert('Network error during checkout processing.');
                btn.disabled = false;
                btn.textContent = 'Proceed to Payment';
            }
            return;
        }

        // 1. Create Razorpay Order
        const orderRes = await fetch("api/index.php/public-payment/create-razorpay-order", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ amount: finalAmount })
        });
        const orderData = await orderRes.json();
        
        if (orderData.error) {
            alert("Payment Gateway Error: " + (orderData.error.description || orderData.error));
            btn.disabled = false;
            btn.textContent = 'Proceed to Payment';
            return;
        }

        // 2. Initialize Razorpay
        const options = {
            key: "<?php echo RAZORPAY_KEY_ID; ?>",
            amount: Math.round(finalAmount * 100),
            order_id: orderData.id,
            currency: "INR",
            name: "ENERGEIA'S Global Ventures",
            description: "Gift Card Purchase",
            image: "assets/logo.png",
            handler: async function (response) {
                // 3. Payment Success - Process Checkout
                data['payment_status'] = 'completed';
                
                try {
                    const res = await fetch('api/index.php/giftcards/checkout', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify(data)
                    });
                    const result = await res.json();
                    if(result.success) {
                        // Post result to success page
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = 'gift-card-success.php';
                        
                        for(const key in result.data) {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = key;
                            input.value = result.data[key];
                            form.appendChild(input);
                        }
                        document.body.appendChild(form);
                        form.submit();
                    } else {
                        alert(result.message || 'Payment failed to register on server.');
                        btn.disabled = false;
                        btn.textContent = 'Proceed to Payment';
                    }
                } catch(err) {
                    alert('Network error during checkout processing.');
                    btn.disabled = false;
                    btn.textContent = 'Proceed to Payment';
                }
            },
            prefill: {
                name: data['sender_name'],
                email: data['sender_email'],
                contact: data['sender_mobile']
            },
            theme: {
                color: "#c5a85c"
            },
            modal: {
                ondismiss: function() {
                    btn.disabled = false;
                    btn.textContent = 'Proceed to Payment';
                }
            }
        };

        const razorpay = new window.Razorpay(options);
        razorpay.open();
        
    } catch(err) {
        alert('Network error initializing payment gateway.');
        btn.disabled = false;
        btn.textContent = 'Proceed to Payment';
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>
<script src="assets/js/gift-cards.js"></script>
