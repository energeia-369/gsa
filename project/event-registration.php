<?php
$pageTitle = "GLOBAL SPORTS ARENA | Event Registration";
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/config/Settings.php';
$nxlCashbackPercentage = Settings::get('nxl_cashback_percentage', 0.05);
$membershipPlansJson = Settings::get('membership_plans', '{}');
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$db = (new Database())->getConnection();
$stmt = $db->query("SELECT setting_value FROM system_settings WHERE setting_key = 'event_fee'");
$eventFee = floatval($stmt->fetchColumn() ?: 0);
$isFree = $eventFee <= 0;

$activeTournaments = []; // Only load dynamic, location-specific events from the events table

$eventSlug = $_GET['event'] ?? '';
if (!empty($eventSlug)) {
    $evtStmt = $db->prepare("SELECT title, sports_data, status FROM events WHERE slug = ?");
    $evtStmt->execute([$eventSlug]);
    $evt = $evtStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($evt) {
        $eventTitle = trim($evt['title']);
        $eventStatus = $evt['status'] ?? 'active';
        $isAdmin = (isset($_SESSION['userRole']) && $_SESSION['userRole'] === 'ADMIN');
        
        if ($eventStatus === 'draft' && !$isAdmin) {
            echo "<script>alert('This event is not open for registration.'); window.location.href='index.php';</script>";
            exit();
        }
        if ($eventStatus === 'completed') {
            echo "<script>alert('This event has already been completed.'); window.location.href='event-details.php?slug=" . urlencode($eventSlug) . "';</script>";
            exit();
        }
        
        // Clear global sports so ONLY sports specifically created for THIS event are shown
        $activeTournaments = [];

        if (!empty($evt['sports_data'])) {
            $evtSports = json_decode($evt['sports_data'], true);
            if (is_array($evtSports)) {
                foreach ($evtSports as $s) {
                    if (!empty($s['title'])) {
                        $displayTitle = trim($s['title']) . ' (' . trim($evt['title']) . ')';
                        $exists = false;
                        foreach ($activeTournaments as $at) {
                            $atDisplay = $at['display_name'] ?? $at['name'];
                            if (strtolower(trim($atDisplay)) === strtolower(trim($displayTitle))) {
                                $exists = true; break;
                            }
                        }
                        if (!$exists) {
                            $activeTournaments[] = [
                                'id' => 0, 
                                'name' => $displayTitle,
                                'display_name' => $displayTitle,
                                'categories' => $s['categories'] ?? '',
                                'price_individual' => floatval($s['price_individual'] ?? 0),
                                'price_pair' => floatval($s['price_pair'] ?? 0),
                                'price_team' => floatval($s['price_team'] ?? 0)
                            ];
                        }
                    }
                }
            }
        }
    }
} else {
    // If no specific event was passed in URL, load ALL dynamic sports from ALL active events
    $evtStmt = $db->query("SELECT title, sports_data FROM events WHERE status = 'active'");
    $allEvents = $evtStmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($allEvents as $evt) {
        if (!empty($evt['sports_data'])) {
            $evtSports = json_decode($evt['sports_data'], true);
            if (is_array($evtSports)) {
                foreach ($evtSports as $s) {
                    if (!empty($s['title'])) {
                        $displayTitle = trim($s['title']) . ' (' . trim($evt['title']) . ')';
                        $exists = false;
                        foreach ($activeTournaments as $at) {
                            $atDisplay = $at['display_name'] ?? $at['name'];
                            if (strtolower(trim($atDisplay)) === strtolower(trim($displayTitle))) {
                                $exists = true; break;
                            }
                        }
                        if (!$exists) {
                            $activeTournaments[] = [
                                'id' => 0, 
                                'name' => $displayTitle,
                                'display_name' => $displayTitle,
                                'categories' => $s['categories'] ?? '',
                                'price_individual' => floatval($s['price_individual'] ?? 0),
                                'price_pair' => floatval($s['price_pair'] ?? 0),
                                'price_team' => floatval($s['price_team'] ?? 0)
                            ];
                        }
                    }
                }
            }
        }
    }
}
?>

<link rel="stylesheet" href="assets/css/EventRegistration.css?v=2">

<div class="main-content">
  <div class="event-page">
    <div class="event-container w-full max-w-7xl mx-auto px-4">
      <div class="event-info">
        <div class="info-content">
          <div class="event-badge">
            ⚡ Limited Seats Available
          </div>

          <h1>GLOBAL SPORTS ARENA National Tournament</h1>

          <p>
            India's biggest multi-sports tournament platform for cricket, football, basketball, badminton, and tennis.
          </p>

          <div class="live-status">
            🔴 Live Registrations Open
          </div>

          <div class="event-details">
            <div class="detail-item">
              <span class="detail-icon">📅</span>
              <div>
                <strong>Date & Time</strong>
                <p>Registrations Closing Soon • Matches Start Next Weekend</p>
              </div>
            </div>

            <div class="detail-item">
              <span class="detail-icon">📍</span>
              <div>
                <strong>Venue</strong>
                <p>Mumbai • Pune • Bangalore • Delhi</p>
              </div>
            </div>

            <div class="detail-item">
              <span class="detail-icon">🏆</span>
              <div>
                <strong>Rewards</strong>
                <p>Cash Rewards + Medals + Sponsorship Opportunities</p>
              </div>
            </div>

            <div class="detail-item">
              <span class="detail-icon">👥</span>
              <div>
                <strong>Team Size</strong>
                <p>Depends on selected sport category</p>
              </div>
            </div>
          </div>

          <div class="price-card">
            <span class="price-label">Entry Pass</span>
            <div class="price-amount">
              <span class="price" style="<?php echo $isFree ? 'font-size: 24px;' : ''; ?>"><?php echo $isFree ? 'As per the matches' : '₹' . number_format($eventFee); ?></span>
              <span class="price-old" style="display: none;">₹1,999</span>
              <span class="price-off" style="display: none;">Free Entry</span>
            </div>
            <div id="gstNote" style="font-size: 0.9rem; color: #9aa0b4; margin-top: 5px; display: none;">+ 18% GST: ₹<span id="gstEventAmount">0</span></div>
            <p>
              Includes Tournament Access, Digital Certificate, Team Dashboard & Match Tracking
            </p>
            <div class="live-stats">
              👥 2,400+ Players Registered
            </div>
          </div>

          <div class="event-highlights">
            <div>⚡ Live Match Updates</div>
            <div>🏅 Verified Tournament System</div>
            <div>🎥 Streaming & Highlights</div>
            <div>🛒 Sports Merchandise</div>
          </div>
        </div>
      </div>

      <div class="event-form">
        <div class="form-header">
          <h2>Team Registration</h2>
          <p>Fill in the details to secure your spot</p>
        </div>

        <form id="eventRegForm" onsubmit="handleEventRegistration(event)">
          <div class="input-group">
            <label>Team Name *</label>
            <input
              type="text"
              id="teamName"
              placeholder="Enter your team name"
              required
            />
          </div>

          <div class="input-row">
            <div class="input-group half">
              <label>Captain Name *</label>
              <input
                type="text"
                id="captainName"
                placeholder="Full name"
                required
              />
            </div>

            <div class="input-group half">
              <label>Captain Contact *</label>
              <input
                type="tel"
                id="captainContact"
                placeholder="Mobile number"
                required
              />
            </div>
          </div>

          <div class="input-group">
            <label>Email Address *</label>
            <input
              type="email"
              id="eventEmail"
              placeholder="team@example.com"
              required
            />
          </div>

          <div class="input-row">
            <div class="input-group half">
              <label>Select Sport *</label>
              <select id="eventSport" required>
                <option value="">Select Tournament / Sport</option>
                <?php foreach ($activeTournaments as $t): ?>
                  <option value="<?php echo htmlspecialchars($t['name']); ?>">
                    <?php echo htmlspecialchars($t['display_name'] ?? $t['name']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="input-group half">
              <label>Team Category *</label>
              <select id="eventCategory" required>
                <option value="">Select Category</option>
                <option value="Men's">Men's</option>
                <option value="Women's">Women's</option>
                <option value="Mixed">Mixed</option>
              </select>
            </div>
          </div>

          <div class="input-row">
            <div class="input-group half">
              <label>Registration Type *</label>
              <select id="regType" required>
                <option value="">Select Type</option>
                <option value="Individual">Individual</option>
                <option value="Pair">Pair / Doubles</option>
                <option value="Team">Team</option>
              </select>
            </div>

            <div class="input-group half">
              <label>Total Members *</label>
              <input
                type="number"
                id="teamMembers"
                placeholder="Including captain"
                min="1"
                max="15"
                required
              />
            </div>
          </div>

          <div class="input-group">
            <label>Additional Notes</label>
            <textarea id="eventNotes" placeholder="Any special requirements or requests..."></textarea>
          </div>

          <!-- NXL Coins Redeeming Section -->
          <div id="nxlRedeemSection" style="display:none; background: rgba(197, 168, 92, 0.05); border: 1px dashed rgba(197, 168, 92, 0.3); padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
              <span style="font-weight:bold; color:#c5a85c; font-size:0.9rem;">💎 Redeem NXL Credits</span>
              <span style="font-size:0.8rem; color:#9aa0b4;">Available Balance: <strong id="availableNxlVal" style="color:#fff;">0</strong> NXL</span>
            </div>
            <div style="display:flex; gap:10px; align-items:center;">
              <input type="number" id="redeemNxlInput" style="flex:1; margin:0; padding:10px;" placeholder="Enter NXL coins to use" min="0" />
              <button type="button" id="applyNxlBtn" class="register-btn" style="width:auto; padding:10px 20px; margin:0; font-size:0.85rem; height: auto; line-height: 1;" onclick="applyNxlCoins()">Apply</button>
            </div>
            <p id="redeemStatusMsg" style="font-size:0.75rem; color:#9aa0b4; margin: 6px 0 0 0; display:none;"></p>
          </div>

          <div class="terms-checkbox">
            <input
              type="checkbox"
              id="terms"
              required
            />
            <label for="terms">
              I agree to the <a href="terms-conditions.php">Terms & Conditions</a> and <a href="terms-conditions.php">Tournament Rules</a>
            </label>
          </div>

          <button type="submit" id="regSubmitBtn" class="register-btn">
            <?php echo $isFree ? 'Register for Free →' : 'Register & Pay ₹' . number_format($eventFee) . ' →'; ?>
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
<script>
let regLoading = false;
const tournamentsData = <?php echo json_encode($activeTournaments); ?>;
const membershipPlans = <?php echo $membershipPlansJson ?: '{}'; ?>;
let nxlCashbackPercentage = <?php echo json_encode((float)$nxlCashbackPercentage); ?>;

function calculateFee(bypassRedeem) {
    const sportName = document.getElementById("eventSport").value;
    const regTypeSelect = document.getElementById("regType");
    
    // Manage Registration Type options
    if (sportName.includes("Football")) {
        for (let i = 0; i < regTypeSelect.options.length; i++) {
            const opt = regTypeSelect.options[i];
            if (opt.value === "Individual" || opt.value === "Pair") {
                opt.disabled = true;
                opt.hidden = true;
            } else if (opt.value === "Team") {
                opt.disabled = false;
                opt.hidden = false;
            }
        }
        if (regTypeSelect.value === "Individual" || regTypeSelect.value === "Pair") {
            regTypeSelect.value = "Team";
        }
    } else if (sportName) {
        for (let i = 0; i < regTypeSelect.options.length; i++) {
            const opt = regTypeSelect.options[i];
            if (opt.value === "Team") {
                opt.disabled = true;
                opt.hidden = true;
            } else {
                opt.disabled = false;
                opt.hidden = false;
            }
        }
        if (regTypeSelect.value === "Team") {
            regTypeSelect.value = "";
        }
    }

    const regType = regTypeSelect.value;
    const members = parseInt(document.getElementById("teamMembers").value) || 1;
    let fee = <?php echo $eventFee; ?>;
    
    if (sportName) {
        const selectedTourney = tournamentsData.find(t => t.name === sportName);
        if (selectedTourney) {
            // Update Categories Dropdown
            const catSelect = document.getElementById("eventCategory");
            if (catSelect) {
                const currentCat = catSelect.value;
                if (selectedTourney.categories && selectedTourney.categories.trim() !== '') {
                    const cats = selectedTourney.categories.split(',').map(c => c.trim()).filter(c => c);
                    if (cats.length > 0) {
                        catSelect.innerHTML = '<option value="">Select Category</option>';
                        cats.forEach(c => {
                            const opt = document.createElement('option');
                            opt.value = c;
                            opt.textContent = c;
                            if (c === currentCat) opt.selected = true;
                            catSelect.appendChild(opt);
                        });
                    }
                } else {
                    catSelect.innerHTML = `
                      <option value="">Select Category</option>
                      <option value="Men's" ${currentCat==="Men's"?'selected':''}>Men's</option>
                      <option value="Women's" ${currentCat==="Women's"?'selected':''}>Women's</option>
                      <option value="Mixed" ${currentCat==="Mixed"?'selected':''}>Mixed</option>
                    `;
                }
            }
            
            let baseFee = selectedTourney.registration_fee ? parseFloat(selectedTourney.registration_fee) : <?php echo $eventFee; ?>;
            
            if (sportName.includes("Badminton")) {
                fee = (regType === "Pair" || regType === "Team") ? 2500 : 1500;
            } else if (sportName.includes("Table Tennis")) {
                fee = (regType === "Pair" || regType === "Team") ? 2000 : 1200;
            } else if (sportName.includes("Lawn Tennis")) {
                fee = (regType === "Pair" || regType === "Team") ? 4000 : 2500;
            } else if (sportName.includes("Football")) {
                fee = 10000;
            } else {
                if (selectedTourney.price_individual !== undefined) {
                    if (regType === "Individual" || (!regType && members === 1)) fee = selectedTourney.price_individual;
                    else if (regType === "Pair" || (!regType && members === 2)) fee = selectedTourney.price_pair;
                    else fee = selectedTourney.price_team;
                    
                    if (fee === 0) fee = baseFee;
                } else {
                    if (regType === "Individual" || (!regType && members === 1)) fee = baseFee;
                    else if (regType === "Pair" || (!regType && members === 2)) fee = baseFee * 1.8;
                    else fee = baseFee * members * 0.8;
                }
            }
        }
    }
    
    const priceEl = document.querySelector('.price');
    const oldPriceEl = document.querySelector('.price-old');
    const offPriceEl = document.querySelector('.price-off');
    const gstNote = document.getElementById('gstNote');
    const gstEventAmount = document.getElementById('gstEventAmount');
    
    // Retrieve active membership plan and calculate discount/cashback
    const membershipPlan = (localStorage.getItem("userMembership") || "none").toLowerCase().trim();
    let discountPercent = 0;
    if (membershipPlan !== "none" && membershipPlans[membershipPlan]) {
        discountPercent = membershipPlans[membershipPlan].cashback_percent;
    }

    let membershipDiscountAmount = Math.floor(fee * discountPercent);
    let priceAfterMembership = fee - membershipDiscountAmount;
    let gstAmount = 0;
    let amountAfterGST = priceAfterMembership;
    let nxlCreditsEarned = 0;

    if (!sportName || fee <= 0) {
        if (priceEl) {
            priceEl.textContent = 'As per the matches';
            priceEl.style.fontSize = '24px';
        }
        if (oldPriceEl) oldPriceEl.style.display = 'none';
        if (offPriceEl) offPriceEl.style.display = 'none';
        if (gstNote) gstNote.style.display = 'none';
    } else {
        gstAmount = Math.floor(priceAfterMembership * 0.18);
        amountAfterGST = priceAfterMembership + gstAmount;
        nxlCreditsEarned = Math.floor(priceAfterMembership * nxlCashbackPercentage);

        if (priceEl) {
            priceEl.textContent = '₹' + priceAfterMembership.toLocaleString('en-IN');
            priceEl.style.fontSize = ''; // Reverts to CSS default
        }
        if (gstNote) {
            gstNote.style.display = 'block';
            if (gstEventAmount) gstEventAmount.textContent = gstAmount.toLocaleString('en-IN');
        }

        if (membershipDiscountAmount > 0) {
            if (oldPriceEl) {
                oldPriceEl.textContent = '₹' + fee.toLocaleString('en-IN');
                oldPriceEl.style.display = 'inline';
                oldPriceEl.style.textDecoration = 'line-through';
                oldPriceEl.style.color = '#8c8c8c';
                oldPriceEl.style.fontSize = '0.9rem';
                oldPriceEl.style.marginLeft = '8px';
            }
            if (offPriceEl) {
                offPriceEl.textContent = Math.round(discountPercent * 100) + '% Off';
                offPriceEl.style.display = 'inline';
                offPriceEl.style.color = '#c5a85c';
                offPriceEl.style.fontWeight = 'bold';
                offPriceEl.style.fontSize = '0.9rem';
                offPriceEl.style.marginLeft = '8px';
            }
        } else {
            if (oldPriceEl) oldPriceEl.style.display = 'none';
            if (offPriceEl) offPriceEl.style.display = 'none';
        }
    }
    
    const coinsUsed = (typeof bypassRedeem === 'boolean' && bypassRedeem) ? 0 : (window.coinsRedeemed || 0);
    let finalAmount = amountAfterGST - coinsUsed;
    if (finalAmount < 0) finalAmount = 0;

    const btn = document.getElementById("regSubmitBtn");
    if (btn) btn.innerHTML = (!sportName || fee <= 0) ? 'Select Sport to Proceed →' : `Register & Pay ₹${finalAmount.toLocaleString('en-IN')} →`;
    
    return { fee: priceAfterMembership, originalFee: fee, gstAmount, totalAmount: finalAmount, nxlCreditsEarned, coinsUsed };
}

let coinsRedeemed = 0;

async function fetchUserNxlBalance() {
    const email = localStorage.getItem("userEmail") || document.getElementById("eventEmail").value.trim();
    if (!email) return;
    try {
        const res = await fetch(`api/index.php/wallet/balance?email=${encodeURIComponent(email)}`);
        const data = await res.json();
        const balance = data.nxlCredits || 0;
        document.getElementById("availableNxlVal").textContent = balance.toLocaleString();
        document.getElementById("nxlRedeemSection").style.display = "block";
        window.userNxlBalance = balance;
    } catch (e) {
        console.error("Error fetching NXL balance:", e);
    }
}

function applyNxlCoins() {
    const inputVal = parseInt(document.getElementById("redeemNxlInput").value) || 0;
    const balance = window.userNxlBalance || 0;
    const msg = document.getElementById("redeemStatusMsg");

    if (inputVal < 0) {
        alert("Please enter a valid amount of NXL coins.");
        return;
    }

    const feesInfo = calculateFee(true);
    const maxRedeemable = feesInfo.totalAmount;

    if (inputVal > balance) {
        alert("You cannot redeem more NXL coins than your available balance.");
        return;
    }

    if (inputVal > maxRedeemable) {
        window.coinsRedeemed = maxRedeemable;
        document.getElementById("redeemNxlInput").value = maxRedeemable;
    } else {
        window.coinsRedeemed = inputVal;
    }

    msg.style.display = "block";
    if (window.coinsRedeemed > 0) {
        msg.textContent = `✓ Successfully applied ${window.coinsRedeemed} NXL coins. You get a discount of ₹${window.coinsRedeemed}!`;
        msg.style.color = "#22c55e";
    } else {
        msg.style.display = "none";
    }

    calculateFee();
}

async function handleEventRegistration(e) {
    e.preventDefault();
    if (regLoading) return;

    const teamName = document.getElementById("teamName").value.trim();
    const captainName = document.getElementById("captainName").value.trim();
    const captainContact = document.getElementById("captainContact").value.trim();
    const email = document.getElementById("eventEmail").value.trim();
    const sport = document.getElementById("eventSport").value;
    const regType = document.getElementById("regType").value;
    const teamCategory = document.getElementById("eventCategory").value;
    const teamMembers = parseInt(document.getElementById("teamMembers").value);
    const notes = document.getElementById("eventNotes").value.trim();

    const btn = document.getElementById("regSubmitBtn");
    
    try {
        regLoading = true;
        btn.disabled = true;
        btn.textContent = "Registering...";

        const feesInfo = calculateFee();
        const eventFee = feesInfo.fee;
        const totalAmount = feesInfo.totalAmount;
        const coinsUsed = feesInfo.coinsUsed;
        const isFree = totalAmount <= 0;

        const payload = {
            teamName,
            captainName,
            captainContact,
            email,
            sport,
            registrationType: regType,
            teamCategory,
            teamMembers,
            notes,
            paymentStatus: isFree ? "FREE" : "PAID",
            registrationFee: eventFee,
            gstAmount: feesInfo.gstAmount,
            totalAmount: totalAmount,
            nxlRedeemed: coinsUsed
        };

        if (isFree) {
            const res = await fetch("api/index.php/event-registrations", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            
            alert("Event registration successful (Free Entry Pass)!");
            completeRegistration(payload, "FREE-PASS", eventFee, sport);
        } else {
            // 1. Fetch Razorpay Order ID from backend
            const orderRes = await fetch("api/index.php/public-payment/create-razorpay-order", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ amount: totalAmount * 100 }) // amount in paise
            });
            const orderData = await orderRes.json();
            
            if (!orderData.id) {
                throw new Error("Failed to generate payment order.");
            }
            
            // 2. Initialize Razorpay
            const options = {
                key: "<?php echo RAZORPAY_KEY_ID; ?>",
                amount: totalAmount * 100,
                currency: "INR",
                name: "GLOBAL SPORTS ARENA",
                description: `Tournament Registration: ${sport}`,
                order_id: orderData.id,
                handler: async function (response) {
                    const paymentId = response.razorpay_payment_id;
                    payload.paymentStatus = "PAID";
                    payload.razorpayPaymentId = paymentId;
                    
                    try {
                        const regRes = await fetch("api/index.php/event-registrations", {
                            method: "POST",
                            headers: { "Content-Type": "application/json" },
                            body: JSON.stringify(payload)
                        });
                        completeRegistration(payload, paymentId, totalAmount, sport);
                    } catch (err) {
                        console.error("Payment sync error:", err);
                        alert("Payment successful but registration failed. Please contact support.");
                    }
                },
                prefill: {
                    name: captainName,
                    email: email,
                    contact: captainContact
                },
                theme: {
                    color: "#d4a373"
                }
            };
            
            if (!window.Razorpay) {
                throw new Error("Razorpay SDK could not be loaded. Please check your internet connection or disable your adblocker.");
            }
            
            const rzp = new window.Razorpay(options);
            rzp.on('payment.failed', function (response){
                alert("Payment Failed. Reason: " + response.error.description);
                btn.disabled = false;
                btn.innerHTML = `Register & Pay ₹${totalAmount.toLocaleString('en-IN')} →`;
                regLoading = false;
            });
            rzp.open();
            return; // Wait for Razorpay callback
        }

    } catch (error) {
        console.error("Event Registration Error:", error);
        alert("Registration failed to record. Error: " + error.message);
        regLoading = false;
        btn.disabled = false;
        // recalculate fees for button text
        const feesInfo = calculateFee();
        btn.innerHTML = `<?php echo $isFree ? 'Register for Free →' : 'Register & Pay ₹' . number_format($eventFee) . ' →'; ?>`;
        if (feesInfo.totalAmount > 0) {
            btn.innerHTML = `Register & Pay ₹${feesInfo.totalAmount.toLocaleString('en-IN')} →`;
        }
    }
}

function completeRegistration(payload, trackingId, eventFee, sport) {
    // Record in client order history localstorage
    const userEmail = localStorage.getItem("userEmail") || "guest";
    const orderKey = `orders_${userEmail}`;
    const oldOrders = JSON.parse(localStorage.getItem(orderKey)) || [];

    const feesInfo = calculateFee();
    const earnedCredits = feesInfo.nxlCreditsEarned;

    const newEventOrder = {
        id: "EVT-" + Date.now(),
        type: "event",
        title: `Tournament: ${sport} Category`,
        image: "🏆",
        brand: "GLOBAL SPORTS ARENA Events",
        quantity: 1,
        price: feesInfo.fee,
        total: feesInfo.totalAmount,
        nxlCoinsEarned: earnedCredits,
        status: "confirmed",
        date: new Date().toLocaleDateString(),
        orderDate: new Date().toLocaleDateString(),
        location: "Mumbai Sports Arena",
        trackingId: trackingId
    };

    localStorage.setItem(orderKey, JSON.stringify([...oldOrders, newEventOrder]));

    // Increment events joined counter
    const currentEventsJoined = Number(localStorage.getItem("eventsJoined") || 0);
    localStorage.setItem("eventsJoined", currentEventsJoined + 1);

    // Save order for success page mapping
    localStorage.setItem("gsa_last_order", JSON.stringify(newEventOrder));
    window.location.href = "payment-success.php";
}

document.addEventListener("DOMContentLoaded", function() {
    const urlParams = new URLSearchParams(window.location.search);
    const sportParam = urlParams.get('sport');
    if (sportParam) {
        const sportSelect = document.getElementById("eventSport");
        let matched = false;
        
        // 1. Try exact match
        for (let i = 0; i < sportSelect.options.length; i++) {
            if (sportSelect.options[i].value === sportParam) {
                sportSelect.value = sportSelect.options[i].value;
                matched = true;
                break;
            }
        }
        
        // 2. Try partial match (case insensitive) if no exact match
        if (!matched) {
            const searchLower = sportParam.toLowerCase();
            for (let i = 0; i < sportSelect.options.length; i++) {
                if (sportSelect.options[i].value.toLowerCase().includes(searchLower)) {
                    sportSelect.value = sportSelect.options[i].value;
                    break;
                }
            }
        }
    }
    
    document.getElementById("eventSport").addEventListener("change", calculateFee);
    document.getElementById("regType").addEventListener("change", calculateFee);
    document.getElementById("teamMembers").addEventListener("input", calculateFee);
    document.getElementById("eventEmail").addEventListener("input", fetchUserNxlBalance);
    
    calculateFee();
    fetchUserNxlBalance();

    // Support prefilled values from sessionStorage
    const prefilledEvent = sessionStorage.getItem("prefilledEvent");
    const prefilledLocation = sessionStorage.getItem("prefilledLocation");
    if (prefilledEvent) {
        let notesText = `Registering for: ${prefilledEvent}`;
        if (prefilledLocation) {
            notesText += ` at ${prefilledLocation}`;
        }
        document.getElementById("eventNotes").value = notesText;
        
        // Auto-detect sport category from event name
        if (prefilledEvent.toLowerCase().includes("cricket")) {
            document.getElementById("eventSport").value = "Cricket";
        } else if (prefilledEvent.toLowerCase().includes("football") || prefilledEvent.toLowerCase().includes("soccer")) {
            document.getElementById("eventSport").value = "Football";
        } else if (prefilledEvent.toLowerCase().includes("basketball")) {
            document.getElementById("eventSport").value = "Basketball";
        } else if (prefilledEvent.toLowerCase().includes("badminton")) {
            document.getElementById("eventSport").value = "Badminton";
        } else if (prefilledEvent.toLowerCase().includes("tennis")) {
            document.getElementById("eventSport").value = "Tennis";
        }
        
        // Clear session storage so it doesn't persist across page reloads
        sessionStorage.removeItem("prefilledEvent");
        sessionStorage.removeItem("prefilledLocation");
    }
});
</script>




<?php require_once __DIR__ . '/includes/footer.php'; ?>
