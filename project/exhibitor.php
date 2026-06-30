<?php
$pageTitle = "GLOBAL SPORTS ARENA | Become an Exhibitor";
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';



$successMsg = "";
$errorMsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $db = Database::getConnection();
        
        $eventName = $_POST['event'] ?? '';
        $eventDate = null;
        if ($eventName) {
            // First check GSA events
            $evtStmt = $db->prepare("SELECT end_date FROM events WHERE title = ? LIMIT 1");
            $evtStmt->execute([$eventName]);
            $evtRow = $evtStmt->fetch(PDO::FETCH_ASSOC);
            if ($evtRow && $evtRow['end_date']) {
                $eventDate = $evtRow['end_date'];
            } else {
                // Check home_carousel_events
                $hcStmt = $db->prepare("SELECT end_date FROM home_carousel_events WHERE title = ? LIMIT 1");
                $hcStmt->execute([$eventName]);
                $hcRow = $hcStmt->fetch(PDO::FETCH_ASSOC);
                if ($hcRow && $hcRow['end_date']) {
                    $eventDate = $hcRow['end_date'];
                }
            }
        }
        
        $feeAmount = isset($_POST['fee_amount']) ? floatval($_POST['fee_amount']) : 0.00;
        
        $authEmail = $_POST['auth_email'] ?? '';
        $uStmt = $db->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $uStmt->execute([$authEmail]);
        $uRow = $uStmt->fetch(PDO::FETCH_ASSOC);
        $userId = $uRow ? $uRow['id'] : null;
        
        $stmt = $db->prepare("INSERT INTO exhibitors (user_id, company_name, contact_person, email, phone, country, city, website, industry, reps, booth, custom_build_details, event, event_date, fee_amount, approval_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
        $stmt->execute([
            $userId,
            $_POST['company_name'] ?? '',
            $_POST['contact_person'] ?? '',
            $_POST['email'] ?? '',
            $_POST['phone'] ?? '',
            $_POST['country'] ?? '',
            $_POST['city'] ?? '',
            $_POST['website'] ?? '',
            $_POST['industry'] ?? '',
            $_POST['reps'] ?? 1,
            $_POST['booth'] ?? '',
            $_POST['custom_build_details'] ?? '',
            $eventName,
            $eventDate,
            $feeAmount
        ]);
        $successMsg = "Application submitted for review! Check your dashboard for updates.";
    } catch (Exception $e) {
        $errorMsg = "An error occurred. Please try again.";
    }
}

// Fetch dynamic tournaments for the dropdown
try {
    $db = Database::getConnection();
    $tStmt = $db->query("SELECT title as name, location as sport, exhibitor_data FROM events WHERE status != 'inactive' ORDER BY id DESC");
    $tournamentsList = $tStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Fetch home carousel events
    $hcStmt = $db->query("SELECT title, country, exhibitor_data FROM home_carousel_events WHERE status='active' ORDER BY id DESC");
    $homeCarouselEvents = $hcStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch exhibitor registration fee (base fee)
    $fStmt = $db->query("SELECT setting_value FROM system_settings WHERE setting_key = 'exhibitor_fee'");
    $feeRow = $fStmt->fetch(PDO::FETCH_ASSOC);
    $exhibitorFee = $feeRow ? floatval($feeRow['setting_value']) : 0;

    // Fetch dynamic booth options
    $bStmt = $db->query("SELECT * FROM booth_options ORDER BY id ASC");
    $boothOptionsList = $bStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch exhibitor_pricing
    $pStmt = $db->query("SELECT setting_value FROM system_settings WHERE setting_key = 'exhibitor_pricing'");
    $pricingRow = $pStmt->fetch(PDO::FETCH_ASSOC);
    $exhibitorPricingJSON = $pricingRow ? $pricingRow['setting_value'] : '{}';

} catch (Exception $e) {
    $tournamentsList = [];
    $homeCarouselEvents = [];
    $exhibitorFee = 0;
    $boothOptionsList = [];
}
?>

<link rel="stylesheet" href="assets/css/exhibitor.css?v=2">

<div id="loginRequiredMessage" style="display: none;">
    <div class="exhibitor-wrapper exhibitor-page" style="min-height: 50vh; display: flex; align-items: center; justify-content: center;">
        <div class="exhibitor-card" style="text-align: center; max-width: 500px;">
            <div class="card-header">
                <h1>Login Required</h1>
                <p>You must be logged in to apply as an exhibitor.</p>
            </div>
            <a href="login.php" class="apply-btn" style="display: inline-block; width: auto; padding: 12px 30px; margin-top: 20px;">Go to Login</a>
        </div>
    </div>
</div>

<div id="exhibitorMainContent" style="display: none;">
<div class="exhibitor-wrapper exhibitor-page">
    <div class="exhibitor-card max-w-4xl mx-auto px-4">
        <div class="card-header">
            <h1>Become an Exhibitor</h1>
            <p>Showcase your brand, products, services, and innovations to a global audience.</p>
        </div>
        
        <?php if ($successMsg): ?>
            <div style="background: rgba(46, 125, 50, 0.2); border: 1px solid #2e7d32; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
                <div style="margin-bottom: 15px; font-weight: bold;"><?php echo $successMsg; ?></div>
                <?php if (isset($qrUrl)): ?>
                    <p style="font-size: 0.9rem; margin-bottom: 10px;">Please save this QR Code for event check-in:</p>
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?php echo urlencode($qrUrl); ?>" alt="QR Code" style="background: #fff; padding: 10px; border-radius: 8px;">
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($errorMsg): ?>
            <div style="background: rgba(198, 40, 40, 0.2); border: 1px solid #c62828; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
                <?php echo $errorMsg; ?>
            </div>
        <?php endif; ?>
        
        <form id="exhibitorForm" action="exhibitor.php" method="POST">
            <div class="grid-form grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group md:col-span-2">
                    <label>Company Name</label>
                    <input type="text" class="form-control w-full" name="company_name" placeholder="Enter your company name" required>
                </div>
                
                <div class="form-group">
                    <label>Contact Person</label>
                    <input type="text" class="form-control" name="contact_person" placeholder="Full name of contact person" required>
                </div>
                
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" class="form-control" name="email" placeholder="Enter email address" required>
                </div>
                
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="tel" class="form-control" name="phone" placeholder="Enter phone number" required>
                </div>
                
                <div class="form-group">
                    <label>Country</label>
                    <input type="text" class="form-control" name="country" placeholder="Enter country" required oninput="updateBoothOptionsForLocation()">
                </div>
                
                <div class="form-group">
                    <label>City</label>
                    <input type="text" class="form-control" name="city" placeholder="Enter city" required oninput="updateBoothOptionsForLocation()">
                </div>
                
                <div class="form-group">
                    <label>Website</label>
                    <input type="url" class="form-control" name="website" placeholder="https://example.com" required>
                </div>
                
                <div class="form-group">
                    <label>Industry</label>
                    <input type="text" class="form-control" name="industry" placeholder="E.g., Tech, Finance, Health" required>
                </div>
                
                <div class="form-group">
                    <label>Number of Representatives</label>
                    <input type="number" class="form-control" name="reps" min="1" placeholder="Number of reps" required>
                </div>
                
                <div class="form-group">
                    <label>Booth Requirement</label>
                    <select class="form-control" name="booth" id="boothSelect" required onchange="toggleCustomBuild()">
                        <option value="" data-price="0" disabled selected>-- Select Booth Size --</option>
                        <?php foreach ($boothOptionsList as $booth): ?>
                            <option value="<?php echo htmlspecialchars($booth['name']); ?>" data-price="<?php echo htmlspecialchars($booth['price']); ?>">
                                <?php $sym = (isset($booth['currency']) && strtoupper($booth['currency']) === 'USD') ? '$' : '₹'; echo htmlspecialchars($booth['name']); ?> <?php echo $booth['price'] > 0 ? '(+' . $sym . number_format($booth['price']) . ')' : ''; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group full-width" id="customBuildGroup" style="display: none;">
                    <label>Custom Build Details</label>
                    <textarea class="form-control" name="custom_build_details" rows="3" placeholder="Please specify your custom build requirements (dimensions, special needs, etc.)"></textarea>
                </div>
                
                <div class="form-group full-width">
                    <label>Event Type</label>
                    <div style="display: flex; gap: 20px; align-items: center; margin-top: 10px;">
                        <label style="cursor: pointer; display: flex; align-items: center; gap: 5px;">
                            <input type="radio" name="event_type" value="gsa" checked onchange="updateEventDropdown()"> GSA Events
                        </label>
                        <label style="cursor: pointer; display: flex; align-items: center; gap: 5px;">
                            <input type="radio" name="event_type" value="nexus" onchange="updateEventDropdown()"> Nexus / Maytriya Events
                        </label>
                    </div>
                </div>

                <div class="form-group full-width">
                    <label>Select Event</label>
                    <select class="form-control" name="event" id="eventSelect" required onchange="handleEventSelection()">
                        <option value="" disabled selected>-- Select an Event --</option>
                        <!-- Options will be populated by JS based on Event Type -->
                    </select>
                </div>
            </div>
            
            <div class="form-group full-width" style="<?php echo $exhibitorFee > 0 ? 'display:block;' : 'display:none;'; ?>" id="feeDisplayGroup">
                <label>Registration Fee (Base + Booth Option)</label>
                <input type="text" id="registrationFeeInput" class="form-control" value="₹<?php echo number_format($exhibitorFee); ?>" style="background: rgba(197, 168, 92, 0.1); color: #c5a85c; font-weight: bold; border-color: rgba(197, 168, 92, 0.3); margin-bottom: 5px;" disabled>
                <div style="font-size: 0.9rem; color: #9aa0b4; text-align: right;">+ 18% GST: ₹<span id="gstAmountSpan"><?php echo number_format(floor($exhibitorFee * 0.18)); ?></span></div>
            </div>
            
            <input type="hidden" name="auth_email" id="authEmailField" value="">
            <button type="submit" id="submitApplyBtn" class="apply-btn">
                <?php echo $exhibitorFee > 0 ? "Apply & Pay ₹" . number_format($exhibitorFee + floor($exhibitorFee * 0.18)) . " →" : "APPLY AS EXHIBITOR"; ?>
            </button>
        </form>
    </div>
</div>
</div> <!-- Close exhibitorMainContent -->

<script>
    const userEmail = localStorage.getItem("userEmail");
    if (!userEmail) {
        document.getElementById("loginRequiredMessage").style.display = "block";
    } else {
        document.getElementById("exhibitorMainContent").style.display = "block";
        document.getElementById("authEmailField").value = userEmail;
    }
</script>

<script>
const baseFee = <?php echo $exhibitorFee; ?>;
let currentBaseFee = baseFee;
let currentTotalFee = baseFee + Math.floor(baseFee * 0.18);

const defaultBoothOptions = <?php echo json_encode($boothOptionsList); ?>;
const exhibitorPricingData = <?php echo $exhibitorPricingJSON; ?>;
const tournamentsList = <?php echo json_encode($tournamentsList); ?>;
const homeCarouselEvents = <?php echo json_encode($homeCarouselEvents); ?>;

function updateEventDropdown() {
    const eventType = document.querySelector('input[name="event_type"]:checked').value;
    const select = document.getElementById("eventSelect");
    
    let optionsHtml = '<option value="" disabled selected>-- Select an Event --</option>';
    
    if (eventType === 'gsa') {
        tournamentsList.forEach(t => {
            const label = t.name + (t.sport ? ' (' + t.sport + ')' : '');
            optionsHtml += `<option value="${t.name}">${label}</option>`;
        });
    } else {
        homeCarouselEvents.forEach(e => {
            const label = e.title + (e.country ? ' (' + e.country + ')' : '');
            optionsHtml += `<option value="${e.title}">${label}</option>`;
        });
    }
    select.innerHTML = optionsHtml;
    handleEventSelection();
}

function handleEventSelection() {
    const eventType = document.querySelector('input[name="event_type"]:checked').value;
    const eventName = document.getElementById("eventSelect").value;
    const boothSelect = document.getElementById("boothSelect");
    
    let optionsHtml = '<option value="" data-price="0" disabled selected>-- Select Booth Size --</option>';
    let eventObj = null;

    if (eventType === 'nexus') {
        eventObj = homeCarouselEvents.find(e => e.title === eventName);
    } else if (eventType === 'gsa') {
        eventObj = tournamentsList.find(e => e.name === eventName);
    }
    
    if (eventObj && eventObj.exhibitor_data) {
        try {
            const exhibitorData = JSON.parse(eventObj.exhibitor_data);
            if (exhibitorData && exhibitorData.length > 0) {
                exhibitorData.forEach(pkg => {
                    const price = parseFloat(pkg.price || 0);
                    let label = pkg.title || '';
                    if (pkg.size) label += ` (${pkg.size})`;
                    const sym = (pkg.currency && pkg.currency.toUpperCase() === 'USD') ? '$' : '₹';
                    if (price > 0) label += ` (+${sym}${price.toLocaleString('en-IN')})`;
                    optionsHtml += `<option value="${pkg.title}" data-price="${price}">${label}</option>`;
                });
                boothSelect.innerHTML = optionsHtml;
                toggleCustomBuild();
                return; // successfully loaded custom options, exit
            }
        } catch (e) {
            console.error("Error parsing exhibitor data", e);
        }
    }
    
    // Fallback if no custom data
    if (eventType === 'nexus') {
        boothSelect.innerHTML = optionsHtml; // empty
    } else {
        updateBoothOptionsForLocation();
    }
    toggleCustomBuild();
}

// Call on initial load
document.addEventListener('DOMContentLoaded', function() {
    updateEventDropdown();
});

function updateBoothOptionsForLocation() {
    const eventType = document.querySelector('input[name="event_type"]:checked').value;
    const eventName = document.getElementById("eventSelect").value;
    
    let eventObj = null;
    if (eventType === 'nexus') {
        eventObj = homeCarouselEvents.find(e => e.title === eventName);
    } else if (eventType === 'gsa') {
        eventObj = tournamentsList.find(e => e.name === eventName);
    }
    
    // If the event has its own custom exhibitor pricing, don't overwrite it based on city/country
    if (eventObj && eventObj.exhibitor_data && JSON.parse(eventObj.exhibitor_data).length > 0) {
        return; 
    }

    const cityInput = document.querySelector('input[name="city"]');
    const countryInput = document.querySelector('input[name="country"]');
    const cityStr = cityInput ? cityInput.value.trim().toLowerCase() : '';
    const countryStr = countryInput ? countryInput.value.trim().toLowerCase() : '';

    const select = document.getElementById("boothSelect");
    
    // Store currently selected value if any
    const currentValue = select.value;
    
    // Check if city or country has custom pricing
    let optionsHtml = '<option value="" data-price="0" disabled selected>-- Select Booth Size --</option>';
    
    let targetKey = '';
    if (exhibitorPricingData[cityStr]) {
        targetKey = cityStr;
    } else if (exhibitorPricingData[countryStr]) {
        targetKey = countryStr;
    }

    if (targetKey) {
        const d = exhibitorPricingData[targetKey];
        const tiers = [
            { key: 'standard', name: 'Standard Stall' },
            { key: 'premium', name: 'Premium Stall' },
            { key: 'corner', name: 'Corner Premium' },
            { key: 'pavilion', name: 'Pavilion Partner' }
        ];
        
        tiers.forEach(t => {
            const size = d[t.key].size || '';
            const rawPrice = String(d[t.key].price || '0');
            const numericPrice = parseFloat(rawPrice.replace(/[^0-9.]/g, '')) || 0;
            const originalPriceDisplay = rawPrice.includes('+') ? numericPrice.toLocaleString('en-IN') + '+' : numericPrice.toLocaleString('en-IN');
            const sym = (d[t.key].currency && d[t.key].currency.toUpperCase() === 'USD') ? '$' : '₹';
            const label = t.name + (numericPrice > 0 ? ` (+${sym}` + originalPriceDisplay + ')' : '');
            optionsHtml += `<option value="${t.name}" data-price="${numericPrice}">${label}</option>`;
        });
    } else {
        defaultBoothOptions.forEach(b => {
            const price = parseFloat(b.price || 0);
            const sym = (b.currency && b.currency.toUpperCase() === 'USD') ? '$' : '₹';
            const label = b.name + (price > 0 ? ` (+${sym}` + price.toLocaleString('en-IN') + ')' : '');
            optionsHtml += `<option value="${b.name}" data-price="${price}">${label}</option>`;
        });
    }
    
    select.innerHTML = optionsHtml;
    
    // Try to restore previous selection
    let found = false;
    for (let i = 0; i < select.options.length; i++) {
        if (select.options[i].value === currentValue) {
            select.selectedIndex = i;
            found = true;
            break;
        }
    }
    if (!found) {
        select.selectedIndex = 0;
    }
    
    toggleCustomBuild();
}

function toggleCustomBuild() {
    var select = document.getElementById("boothSelect");
    var group = document.getElementById("customBuildGroup");
    var textarea = group.querySelector("textarea");
    
    // Toggle textarea for Custom
    if (select.value.toLowerCase().includes("custom")) {
        group.style.display = "block";
        textarea.required = true;
    } else {
        group.style.display = "none";
        textarea.required = false;
        textarea.value = "";
    }
    
    // Update price dynamically
    var selectedOption = select.options[select.selectedIndex];
    var optionPrice = selectedOption ? parseFloat(selectedOption.getAttribute("data-price") || 0) : 0;
    currentBaseFee = baseFee + optionPrice;
    var gstAmount = Math.floor(currentBaseFee * 0.18);
    currentTotalFee = currentBaseFee + gstAmount;
    
    var feeInput = document.getElementById("registrationFeeInput");
    var gstSpan = document.getElementById("gstAmountSpan");
    var submitBtn = document.getElementById("submitApplyBtn");
    var feeGroup = document.getElementById("feeDisplayGroup");
    
    if (currentTotalFee > 0) {
        if(feeInput) {
            feeInput.value = "₹" + currentBaseFee.toLocaleString('en-IN');
            if(feeGroup) feeGroup.style.display = "block";
        }
        if(gstSpan) gstSpan.textContent = gstAmount.toLocaleString('en-IN');
        if(submitBtn) {
            submitBtn.innerHTML = "SUBMIT APPLICATION";
        }
    } else {
        if(feeGroup) feeGroup.style.display = "none";
        if(submitBtn) submitBtn.innerHTML = "SUBMIT APPLICATION";
    }
    
    // Set hidden fee input
    let hiddenFeeInput = document.getElementById("hiddenFeeAmount");
    if (!hiddenFeeInput) {
        hiddenFeeInput = document.createElement("input");
        hiddenFeeInput.type = "hidden";
        hiddenFeeInput.id = "hiddenFeeAmount";
        hiddenFeeInput.name = "fee_amount";
        document.getElementById("exhibitorForm").appendChild(hiddenFeeInput);
    }
    hiddenFeeInput.value = currentTotalFee;
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
