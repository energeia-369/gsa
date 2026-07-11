<?php
$pageTitle = "Register as Delegate";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
require_once __DIR__ . '/config/Database.php';

$pdo = Database::getConnection();
// Fetch active events to populate the dropdown
$eventsStmt = $pdo->query("SELECT id, title, delegate_fee, delegate_currency FROM home_carousel_events ORDER BY id ASC");
$activeEvents = $eventsStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<link rel="stylesheet" href="assets/css/delegate.css?v=4">

<!-- Hero Section -->
<section class="delegate-hero w-full">
    <h1>Register as a <span>Delegate</span></h1>
    <p class="hero-text">
        Join Global Sports Academy events as an official delegate and become part of international sporting excellence.
    </p>
    <div>
        <a href="#registrationFormSection" class="delegate-btn" onclick="document.getElementById('registrationFormSection').scrollIntoView({ behavior: 'smooth' }); return false;">Register Now</a>
    </div>
</section>

<!-- About Delegates Section -->
<section class="delegate-section">
    <h2 class="section-title">Why Become a Delegate?</h2>
    <div class="cards-grid">
        <div class="info-card">
            <i class="fa-solid fa-globe"></i>
            <h3>Official Representation</h3>
            <p>Represent your organization or country officially on a global sports platform.</p>
        </div>
        <div class="info-card">
            <i class="fa-solid fa-handshake"></i>
            <h3>Networking Opportunities</h3>
            <p>Connect with industry leaders, federation heads, and top-tier sports management professionals.</p>
        </div>
        <div class="info-card">
            <i class="fa-solid fa-plane"></i>
            <h3>International Exposure</h3>
            <p>Gain valuable global exposure at premium sporting events and summits.</p>
        </div>
        <div class="info-card">
            <i class="fa-solid fa-users"></i>
            <h3>Leadership Experience</h3>
            <p>Engage in decision-making forums and high-level sports discussions.</p>
        </div>
        <div class="info-card">
            <i class="fa-solid fa-book"></i>
            <h3>Sports Management Learning</h3>
            <p>Attend exclusive workshops and learning sessions tailored for sports leaders.</p>
        </div>
        <div class="info-card">
            <i class="fa-solid fa-award"></i>
            <h3>Certificate of Participation</h3>
            <p>Receive an officially recognized certificate for your active involvement.</p>
        </div>
    </div>
</section>

<!-- Benefits Section -->
<section class="delegate-section">
    <h2 class="section-title">Exclusive Benefits</h2>
    <div class="circular-layout-container">
        
        <div class="circular-center">
            <i class="fa-solid fa-crown"></i>
            GSA<br>Benefits
        </div>

        <div class="circular-item">
            <i class="fa-solid fa-network-wired"></i>
            <h3>International Networking</h3>
        </div>
        <div class="circular-item">
            <i class="fa-solid fa-ticket"></i>
            <h3>Exclusive Event Access</h3>
        </div>
        <div class="circular-item">
            <i class="fa-solid fa-id-badge"></i>
            <h3>Official Delegate Badge</h3>
        </div>
        <div class="circular-item">
            <i class="fa-solid fa-microphone"></i>
            <h3>Conference Sessions</h3>
        </div>
        <div class="circular-item">
            <i class="fa-solid fa-chalkboard-user"></i>
            <h3>Workshops</h3>
        </div>
        <div class="circular-item">
            <i class="fa-solid fa-champagne-glasses"></i>
            <h3>Gala Dinner Access</h3>
        </div>
        <div class="circular-item">
            <i class="fa-solid fa-comments"></i>
            <h3>Sports Forums</h3>
        </div>
        <div class="circular-item">
            <i class="fa-solid fa-certificate"></i>
            <h3>Professional Recognition</h3>
        </div>
        <div class="circular-item">
            <i class="fa-solid fa-star"></i>
            <h3>Future Event Priority</h3>
        </div>
    </div>
</section>

<!-- Registration Form Section -->
<section class="delegate-section" id="registrationFormSection">
    <h2 class="section-title" style="margin-bottom: 1rem;">Delegate Registration Form</h2>
    <p style="text-align: center; color: #9aa0b4; margin-bottom: 3rem;">Please fill out all required fields to register.</p>
    
    <div class="delegate-form-container mx-auto" style="max-width: 900px;">
        <form id="delegateRegistrationForm" enctype="multipart/form-data">
            
            <h3 class="form-section-title">Event Selection</h3>
            <div class="form-group" style="margin-bottom: 2rem;">
                <label>Select Event (Optional)</label>
                <select name="event_id" class="form-control" id="eventSelection">
                    <option value="">-- General Delegate Registration --</option>
                    <?php foreach ($activeEvents as $evt): ?>
                        <?php $isDisabled = empty($evt['delegate_fee']) || $evt['delegate_fee'] <= 0 ? 'disabled' : ''; ?>
                        <option value="<?= $evt['id'] ?>" data-fee="<?= htmlspecialchars($evt['delegate_fee'] ?? '') ?>" data-currency="<?= htmlspecialchars($evt['delegate_currency'] ?? '') ?>" <?= $isDisabled ?>>
                            <?= htmlspecialchars($evt['title']) ?> <?= $isDisabled ? '(Fee Not Set)' : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small style="color: #9aa0b4; display: block; margin-top: 5px;">Selecting an event may apply specific registration rates.</small>
            </div>
            
            <div id="feePreviewBox" style="background: rgba(197, 168, 92, 0.1); border: 1px solid rgba(197, 168, 92, 0.4); padding: 15px; border-radius: 8px; margin-bottom: 2rem; display: none;">
                <p style="margin: 0; color: #c5a85c; font-weight: bold; font-size: 1.1rem;">Registration Fee for Selected Event: <span id="feePreviewAmount"></span></p>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const eventSelect = document.getElementById('eventSelection');
                    const feePreviewBox = document.getElementById('feePreviewBox');
                    const feePreviewAmount = document.getElementById('feePreviewAmount');
                    
                    if (eventSelect) {
                        eventSelect.addEventListener('change', function() {
                            const selectedOption = this.options[this.selectedIndex];
                            const fee = selectedOption.getAttribute('data-fee');
                            const currency = selectedOption.getAttribute('data-currency');
                            
                            if (fee && fee > 0) {
                                feePreviewAmount.textContent = `${currency || 'USD'} ${fee}`;
                                feePreviewBox.style.display = 'block';
                                const submitBtn = document.querySelector('button[type="submit"]');
                                if (submitBtn) {
                                    submitBtn.textContent = `Submit Registration (Fee: ${currency || 'USD'} ${fee})`;
                                }
                            } else {
                                feePreviewBox.style.display = 'none';
                                const submitBtn = document.querySelector('button[type="submit"]');
                                if (submitBtn) {
                                    submitBtn.textContent = `Submit Registration`;
                                }
                            }
                        });
                    }
                });
            </script>

            <h3 class="form-section-title">Personal Information</h3>
            <div class="form-row">
                <div class="form-col form-group">
                    <label>Full Name *</label>
                    <input type="text" name="full_name" class="form-control" required>
                </div>
                <div class="form-col form-group">
                    <label>Gender *</label>
                    <select name="gender" class="form-control" required>
                        <option value="">Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col form-group">
                    <label>Date of Birth *</label>
                    <input type="date" name="dob" class="form-control" required>
                </div>
                <div class="form-col form-group">
                    <label>Nationality *</label>
                    <input type="text" name="nationality" class="form-control" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-col form-group">
                    <label>Country of Residence *</label>
                    <input type="text" name="country" class="form-control" required>
                </div>
                <div class="form-col form-group">
                    <label>Passport Number *</label>
                    <input type="text" name="passport_number" class="form-control" required>
                </div>
            </div>

            <h3 class="form-section-title">Contact Information</h3>
            <div class="form-row">
                <div class="form-col form-group">
                    <label>Email Address *</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="form-col form-group">
                    <label>Mobile Number (with Country Code) *</label>
                    <input type="tel" name="phone" class="form-control" required>
                </div>
            </div>

            <h3 class="form-section-title">Professional Details</h3>
            <div class="form-row">
                <div class="form-col form-group">
                    <label>Organization *</label>
                    <input type="text" name="organization" class="form-control" required>
                </div>
                <div class="form-col form-group">
                    <label>Designation *</label>
                    <input type="text" name="designation" class="form-control" required>
                </div>
            </div>
            <div class="form-group">
                <label>Delegate Type *</label>
                <select name="delegate_type" id="delegate_type" class="form-control" required>
                    <option value="">Select Type</option>
                    <option value="Student Delegate">Student Delegate</option>
                    <option value="Professional Delegate">Professional Delegate</option>
                    <option value="Government Delegate">Government Delegate</option>
                    <option value="Sports Federation">Sports Federation</option>
                    <option value="Sponsor">Sponsor</option>
                    <option value="Volunteer">Volunteer</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="form-group" id="delegate_type_other_group" style="display: none;">
                <label>Describe The Type *</label>
                <input type="text" name="delegate_type_other" id="delegate_type_other" class="form-control" placeholder="Please specify">
            </div>

            <h3 class="form-section-title">Address</h3>
            <div class="form-group">
                <label>Street Address *</label>
                <textarea name="address" class="form-control" rows="2" required></textarea>
            </div>
            <div class="form-row">
                <div class="form-col form-group">
                    <label>City *</label>
                    <input type="text" name="city" class="form-control" required>
                </div>
                <div class="form-col form-group">
                    <label>State/Province *</label>
                    <input type="text" name="state" class="form-control" required>
                </div>
                <div class="form-col form-group">
                    <label>Postal Code *</label>
                    <input type="text" name="postal_code" class="form-control" required>
                </div>
            </div>

            <h3 class="form-section-title">Emergency Contact</h3>
            <div class="form-row">
                <div class="form-col form-group">
                    <label>Emergency Contact Name *</label>
                    <input type="text" name="emergency_name" class="form-control" required>
                </div>
                <div class="form-col form-group">
                    <label>Emergency Contact Number *</label>
                    <input type="tel" name="emergency_phone" class="form-control" required>
                </div>
            </div>

            <h3 class="form-section-title">Document Uploads (Max 5MB)</h3>
            <div class="form-row">
                <div class="form-col form-group">
                    <label>Passport Upload (PDF/JPG/PNG) *</label>
                    <div class="file-upload-wrapper">
                        <input type="file" name="passport_file" accept=".pdf,.jpg,.jpeg,.png" required>
                    </div>
                </div>
                <div class="form-col form-group">
                    <label>Profile Photo (JPG/PNG) *</label>
                    <div class="file-upload-wrapper">
                        <input type="file" name="profile_photo" accept=".jpg,.jpeg,.png" required>
                    </div>
                </div>
            </div>


            <h3 class="form-section-title">Preferences & Logistics</h3>
            <div class="form-row">
                <div class="form-col form-group">
                    <label>Diet Preference *</label>
                    <select name="diet" class="form-control" required>
                        <option value="">Select Diet</option>
                        <option value="Vegetarian">Vegetarian</option>
                        <option value="Non Vegetarian">Non Vegetarian</option>
                        <option value="Vegan">Vegan</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="form-col form-group">
                    <label>T-Shirt Size *</label>
                    <select name="tshirt_size" class="form-control" required>
                        <option value="">Select Size</option>
                        <option value="XS">XS</option>
                        <option value="S">S</option>
                        <option value="M">M</option>
                        <option value="L">L</option>
                        <option value="XL">XL</option>
                        <option value="XXL">XXL</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col form-group">
                    <label>Arrival Date *</label>
                    <input type="date" name="arrival_date" class="form-control" required>
                </div>
                <div class="form-col form-group">
                    <label>Departure Date *</label>
                    <input type="date" name="departure_date" class="form-control" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-col form-group">
                    <label>Hotel Required? * <span style="font-size: 0.8rem; color: #888; font-weight: normal; margin-left: 5px;">(extra charges will be taken for this)</span></label>
                    <select name="hotel_required" class="form-control" required>
                        <option value="No">No</option>
                        <option value="Yes">Yes</option>
                    </select>
                </div>
                <div class="form-col form-group">
                    <label>Airport Pickup Required? * <span style="font-size: 0.8rem; color: #888; font-weight: normal; margin-left: 5px;">(extra charges will be taken for this)</span></label>
                    <select name="airport_pickup" class="form-control" required>
                        <option value="No">No</option>
                        <option value="Yes">Yes</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Medical Conditions (Optional)</label>
                <textarea name="medical_conditions" class="form-control" rows="2"></textarea>
            </div>
            <div class="form-group">
                <label>Special Requirements (Optional)</label>
                <textarea name="special_requirements" class="form-control" rows="2"></textarea>
            </div>

            <h3 class="form-section-title">Terms & Conditions</h3>
            <div class="checkbox-group">
                <input type="checkbox" id="terms_agree" name="terms_agree" required>
                <label for="terms_agree">I agree to the Terms and Conditions of Global Sports Academy.</label>
            </div>
            <div class="checkbox-group">
                <input type="checkbox" id="privacy_agree" name="privacy_agree" required>
                <label for="privacy_agree">I agree to the Privacy Policy and data handling.</label>
            </div>

            <div style="text-align: center; margin-top: 2rem;">
                <button type="submit" class="delegate-btn" style="width: 100%; font-size: 1.1rem; padding: 1rem;">Submit Registration</button>
            </div>
        </form>
    </div>
</section>

<!-- Loading Overlay -->
<div id="loadingOverlay">
    <div class="spinner"></div>
    <p style="margin-top: 1rem; font-weight: bold; color: #12131c;">Processing Registration...</p>
</div>

<script src="assets/js/delegate.js?v=1"></script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
