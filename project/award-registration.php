<?php
$pageTitle = "Award Ceremony & Gala Dinner | Registration";
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<link rel="stylesheet" href="assets/css/award-registration.css?v=3">

<?php
$db = (new Database())->getConnection();
$nxlCashbackPercentage = Settings::get('nxl_cashback_percentage', 0.05);
$membershipPlansJson = Settings::get('membership_plans', '{}');
?>
<script>
    const membershipPlans = <?php echo $membershipPlansJson ?: '{}'; ?>;
    let nxlCashbackPercentage = <?= $nxlCashbackPercentage ?>;
</script>
<?php
$eventSlug = $_GET['event'] ?? '';
$galaPasses = [];
$eventTitle = "GSA Award Ceremony & Gala Dinner";
if (!empty($eventSlug)) {
    $evtStmt = $db->prepare("SELECT title, gala_passes_data FROM events WHERE slug = ?");
    $evtStmt->execute([$eventSlug]);
    $evt = $evtStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$evt) {
        $evtStmt = $db->prepare("SELECT title, gala_passes_data FROM home_carousel_events WHERE slug = ?");
        $evtStmt->execute([$eventSlug]);
        $evt = $evtStmt->fetch(PDO::FETCH_ASSOC);
    }
    
    if (!$evt) {
        $evtStmt = $db->prepare("SELECT tournament_name as title, gala_passes_data FROM gsa_carousel_events WHERE slug = ?");
        $evtStmt->execute([$eventSlug]);
        $evt = $evtStmt->fetch(PDO::FETCH_ASSOC);
    }

    if ($evt) {
        if (!empty($evt['gala_passes_data'])) {
            $galaPasses = json_decode($evt['gala_passes_data'], true) ?? [];
        }
        $eventTitle = htmlspecialchars($evt['title']) . " - Award Ceremony & Gala Dinner";
    }
}
// Default passes if none exist in DB
if (empty($galaPasses)) {
    $galaPasses = [
        [
            'title' => 'Single Gala Pass',
            'price' => '4500.00',
            'features' => "Valid for 1 Person\nAward Ceremony Entry\nGala Dinner & Networking\nParticipation Certificate"
        ],
        [
            'title' => 'Couple Gala Pass',
            'price' => '7000.00',
            'features' => "Valid for 2 Persons\nAward Ceremony Entry\nGala Dinner & Networking\nPremium Seating"
        ]
    ];
}
?>

<div class="award-hero px-4 py-16 md:py-24 text-center">
    <h1 class="text-3xl md:text-5xl font-extrabold mb-4 break-words"><?= $eventTitle ?> Registration</h1>
    <p>Complete your registration to attend the prestigious GSA Award Ceremony & Gala Dinner.</p>
    <p style="font-size: 0.95rem;">Celebrate excellence with awards, premium dining, networking, entertainment and unforgettable experiences.</p>
</div>

<div class="award-registration-container w-full max-w-full lg:max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col lg:flex-row gap-6 lg:gap-10 my-10 overflow-hidden box-border" style="width: 100%; max-width: 100vw; overflow-x: hidden; box-sizing: border-box;">
    <div class="award-form-wrapper flex-1 min-w-0 p-4 sm:p-6 lg:p-8 w-full max-w-full box-border" style="width: 100%; min-width: 0; box-sizing: border-box; overflow: hidden;">
        <form id="awardRegistrationForm" action="award-payment.php" method="POST" enctype="multipart/form-data">
            
            <input type="hidden" id="hiddenEventSlug" name="event_slug" value="<?= htmlspecialchars($eventSlug) ?>">
            <input type="hidden" id="hiddenPassType" name="pass_type" value="">
            <input type="hidden" id="hiddenBasePrice" name="base_amount" value="0">
            <input type="hidden" id="hiddenCurrency" name="currency" value="INR">
            
            <div class="award-form-section">
                <h3>1. Select Your Gala Pass</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 w-full max-w-full box-border">
                    <?php foreach ($galaPasses as $pass): ?>
                    <div class="pass-card" onclick="selectPass('<?= htmlspecialchars(addslashes($pass['title'])) ?>', <?= (float)$pass['price'] ?>, '<?= (isset($pass['currency']) && $pass['currency'] === 'USD') ? '$' : '₹' ?>', this)">
                        <div class="pass-title"><?= htmlspecialchars($pass['title']) ?></div>
                        <div class="pass-price"><?= (isset($pass['currency']) && $pass['currency'] === 'USD') ? '$' : '₹' ?><?= number_format((float)$pass['price']) ?></div>
                        <ul class="pass-features">
                            <?php 
                            $features = explode("\n", trim($pass['features']));
                            foreach ($features as $feature):
                                if (trim($feature) !== ''):
                            ?>
                                <li><?= htmlspecialchars(trim($feature)) ?></li>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </ul>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="award-form-section">
                <h3>2. Personal Details</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Full Name *</label>
                        <input type="text" name="full_name" id="regFullName" required placeholder="John Doe">
                    </div>
                    <div class="form-group">
                        <label>Email Address *</label>
                        <input type="email" name="email" id="regEmail" required placeholder="john@example.com">
                    </div>
                    <div class="form-group">
                        <label>Mobile Number *</label>
                        <input type="tel" name="mobile" id="regMobile" required placeholder="+91 xxxxxxxxxx">
                    </div>
                    <div class="form-group">
                        <label>Date of Birth *</label>
                        <input type="date" name="dob" required>
                    </div>
                    <div class="form-group">
                        <label>Age *</label>
                        <input type="number" name="age" required min="18" max="100">
                    </div>
                    <div class="form-group">
                        <label>Gender *</label>
                        <select name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>City *</label>
                        <input type="text" name="city" required>
                    </div>
                    <div class="form-group">
                        <label>State *</label>
                        <input type="text" name="state" required>
                    </div>
                    <div class="form-group">
                        <label>Country *</label>
                        <input type="text" name="country" required value="India">
                    </div>
                    <div class="form-group">
                        <label>Pincode *</label>
                        <input type="text" name="pincode" required>
                    </div>
                    <div class="form-group">
                        <label>Occupation *</label>
                        <input type="text" name="occupation" required>
                    </div>
                    <div class="form-group">
                        <label>Company Name (Optional)</label>
                        <input type="text" name="company_name">
                    </div>
                </div>
            </div>

            <div class="award-form-section">
                <h3>3. Emergency Contact</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Emergency Contact Name *</label>
                        <input type="text" name="emergency_contact" required>
                    </div>
                    <div class="form-group">
                        <label>Emergency Contact Number *</label>
                        <input type="tel" name="emergency_phone" required>
                    </div>
                </div>
            </div>

            <div class="award-form-section">
                <h3>4. Identity Proof</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Select ID Proof *</label>
                        <select name="id_proof_type" required>
                            <option value="">Select ID Type</option>
                            <option value="Aadhaar">Aadhaar Card</option>
                            <option value="PAN">PAN Card</option>
                            <option value="Passport">Passport</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Upload ID Proof * (JPG, PNG, PDF)</label>
                        <input type="file" name="id_proof_file" accept=".jpg,.jpeg,.png,.pdf" required>
                    </div>
                </div>
            </div>

            <div class="award-form-section">
                <h3>5. Additional Options</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Food Preference *</label>
                        <select name="food_type" required>
                            <option value="">Select Preference</option>
                            <option value="Veg">Vegetarian</option>
                            <option value="Non-Veg">Non-Vegetarian</option>
                            <option value="Vegan">Vegan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Accommodation Required?</label>
                        <select name="accommodation_required">
                            <option value="0">No</option>
                            <option value="1">Yes (We will contact you with options)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Transportation Required?</label>
                        <select name="transport_required">
                            <option value="0">No</option>
                            <option value="1">Yes (We will contact you with options)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Special Assistance Required?</label>
                        <select name="special_assistance">
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label>Medical Information (Optional)</label>
                        <textarea name="medical_info" rows="2" placeholder="Any pre-existing conditions we should be aware of?"></textarea>
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label>Food Allergies (Optional)</label>
                        <textarea name="food_allergies" rows="2" placeholder="List any food allergies"></textarea>
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label>Additional Remarks (Optional)</label>
                        <textarea name="remarks" rows="2" placeholder="Any other requests?"></textarea>
                    </div>
                </div>
            </div>
            
            <!-- Summary -->
            <div class="award-form-section order-summary-box">
                <h3>Order Summary</h3>
                
                <div class="summary-row">
                    <span>Base Price</span>
                    <span id="summaryBase">₹0.00</span>
                </div>
                
                <div id="membershipOptionContainer" style="display: none; flex-direction: column; gap: 8px; margin-bottom: 15px;">
                    <label style="font-size: 0.9rem; color: #c5a85c; font-weight: 600;">Active Membership</label>
                    <div id="membershipDisplay" class="membership-display-box">
                        None
                    </div>
                </div>

                <div id="premiumDiscountRow" class="summary-row discount-row" style="display: none;">
                    <span id="premiumDiscountLabel">Premium Discount</span>
                    <span id="summaryPremiumDiscount">- ₹0.00</span>
                </div>

                <div style="margin-bottom: 15px;">
                    <div class="discount-input-container">
                        <input type="text" id="discountCodeInput" class="discount-input" placeholder="Got a Discount Code?">
                        <button type="button" onclick="applyCouponCode()" class="discount-btn">Apply</button>
                    </div>
                    <div id="discountMessage" style="display:none; font-size: 0.8rem; margin-top: 5px;"></div>
                </div>
                
                <div id="couponDiscountRow" class="summary-row discount-row" style="display: none;">
                    <span>Coupon Discount</span>
                    <span id="summaryCouponDiscount">- ₹0.00</span>
                </div>

                <div class="summary-row">
                    <span>GST (18%)</span>
                    <span id="summaryGst">+ ₹0.00</span>
                </div>

                <!-- NXL Credits Section -->
                <div id="nxlCoinsSection" class="nxl-coins-box" style="display: none; flex-direction: column;">
                    <div style="display: flex; justify-content: space-between; align-items: center">
                        <div>
                            <span class="nxl-title">Available NXL Credits</span>
                            <strong class="nxl-balance" id="nxlBalanceLabel">0 Credits</strong>
                        </div>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 8px; margin-top: 4px">
                        <span class="nxl-redemption-title">Redemption Options (1 Credit = ₹1)</span>
                        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;" id="redemptionOptionsGroup">
                            <!-- JS injected inputs -->
                        </div>
                    </div>
                </div>

                <div id="nxlDiscountRow" class="summary-row nxl-discount-row" style="display: none;">
                    <span>NXL Coin Redemption</span>
                    <span id="summaryNxlDiscount">- ₹0.00</span>
                </div>

                <div class="summary-total-row">
                    <span>Final Amount Payable</span>
                    <span id="summaryTotal">₹0.00</span>
                </div>
                
                <div id="nxlEarnSection" class="nxl-earn-box" style="display: none;">
                    <div style="font-size: 24px;">💎</div>
                    <div style="display: flex; flex-direction: column; line-height: 1.2;">
                        <span>You'll earn</span>
                        <strong id="earnedCreditsLabel" style="font-size: 1rem;">0 NXL Credits</strong>
                        <span>on this purchase</span>
                    </div>
                </div>
            </div>
            
            <input type="hidden" id="hiddenCouponCode" name="coupon_code" value="">
            <input type="hidden" id="hiddenDiscountAmount" name="discount_amount" value="0">
            <input type="hidden" id="hiddenNxlRedeemed" name="nxl_redeemed" value="0">
            <input type="hidden" id="hiddenUserId" name="user_id" value="">
            
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
                <input type="checkbox" id="termsAgreed" required style="width: 20px; height: 20px; accent-color: #c5a85c;">
                <label for="termsAgreed" style="font-size: 0.9rem;">I agree to the <a href="#" style="color: #c5a85c;">Terms & Conditions</a> and Refund Policy.</label>
            </div>

            <button type="submit" class="award-submit-btn" id="btnProceedPayment" disabled>Proceed to Payment</button>

        </form>
    </div>
</div>

<script src="assets/js/award-registration.js?v=3"></script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
