// award-registration.js

let selectedPass = null;
let basePrice = 0;
let discountCode = "";
let couponDiscount = 0;
let redeemedCoins = 0;

let nxlCoins = 0;
let isPremiumUser = false;
let membershipPlan = "none";
let currencySym = "₹";

document.addEventListener('DOMContentLoaded', () => {
    // 1. Initialize user info from localStorage
    const userEmail = localStorage.getItem("userEmail") || "";
    const userFullName = localStorage.getItem("userName") || "";
    const userMobile = localStorage.getItem("userContact") || "";
    membershipPlan = (localStorage.getItem("userMembership") || "none").toLowerCase().trim();
    isPremiumUser = (membershipPlan !== "none" && membershipPlan !== "");
    
    // Auto fill fields
    if (userFullName) {
        const nameField = document.getElementById('regFullName');
        if (nameField) nameField.value = userFullName;
    }
    if (userEmail) {
        const emailField = document.getElementById('regEmail');
        if (emailField) emailField.value = userEmail;
    }
    if (userMobile) {
        const mobileField = document.getElementById('regMobile');
        if (mobileField) mobileField.value = userMobile;
    }

    // Set hidden user ID to email for tracking (since localstorage only has email)
    document.getElementById('hiddenUserId').value = userEmail;

    // Fetch NXL Coins from API
    if (userEmail) {
        fetch(`api/index.php/wallet/balance?email=${encodeURIComponent(userEmail)}`)
            .then(res => res.json())
            .then(data => {
                if (data && data.nxlCredits !== undefined) {
                    nxlCoins = data.nxlCredits;
                }
            })
            .catch(err => console.error(err));
    }

    const form = document.getElementById('awardRegistrationForm');
    if (form) {
        form.addEventListener('submit', (e) => {
            if (!selectedPass) {
                e.preventDefault();
                alert('Please select a Gala Pass before proceeding.');
                return;
            }
            if (!document.getElementById('termsAgreed').checked) {
                e.preventDefault();
                alert('Please agree to the Terms & Conditions.');
                return;
            }
        });
    }
});

function selectPass(passType, price, sym, element) {
    selectedPass = passType;
    basePrice = price;
    if(sym) currencySym = sym;
    
    // UI Update
    document.querySelectorAll('.pass-card').forEach(el => el.classList.remove('selected'));
    element.classList.add('selected');
    
    document.getElementById('hiddenPassType').value = passType;
    document.getElementById('hiddenBasePrice').value = price;
    
    let currencyVal = (currencySym === '$') ? 'USD' : 'INR';
    document.getElementById('hiddenCurrency').value = currencyVal;
    
    // Check Membership container display
    const memContainer = document.getElementById("membershipOptionContainer");
    const display = document.getElementById("membershipDisplay");
    
    if (memContainer && isPremiumUser) {
        memContainer.style.display = "flex";
        let discountPercent = 0;
        if (membershipPlan === "standard") discountPercent = 5;
        else if (membershipPlan === "premium") discountPercent = 10;
        else if (membershipPlan === "elite") discountPercent = 15;
        if (display) {
            display.textContent = `${membershipPlan.charAt(0).toUpperCase() + membershipPlan.slice(1)} Member (${discountPercent}% Off)`;
        }
    } else if (memContainer) {
        memContainer.style.display = "none";
    }

    applyCouponCode(); 
    calculateTotal();
    
    document.getElementById('btnProceedPayment').disabled = false;
}

function applyCouponCode() {
    if (basePrice === 0) return; // Wait until pass is selected
    
    const code = document.getElementById("discountCodeInput").value.trim().toUpperCase();
    const msg = document.getElementById("discountMessage");

    if (code === "") {
        couponDiscount = 0;
        discountCode = "";
        msg.style.display = "block";
        msg.textContent = "Please enter discount code";
        msg.style.color = "#ff6b6b";
        calculateTotal();
        return;
    }

    if (code === "GLOBAL10") {
        couponDiscount = Math.floor(basePrice * 0.1);
        discountCode = code;
        msg.style.display = "block";
        msg.textContent = `GLOBAL10 applied! You saved ${currencySym}${couponDiscount}`;
        msg.style.color = "#22c55e";
    } else if (code === "NXL100") {
        couponDiscount = 100;
        discountCode = code;
        msg.style.display = "block";
        msg.textContent = `NXL100 applied! You saved ${currencySym}100`;
        msg.style.color = "#22c55e";
    } else {
        couponDiscount = 0;
        discountCode = "";
        msg.style.display = "block";
        msg.textContent = "Invalid discount code";
        msg.style.color = "#ff6b6b";
    }
    calculateTotal();
}

function renderRedemptionOptions(maxAmountAllowed = Infinity) {
    const group = document.getElementById("redemptionOptionsGroup");
    if (!group) return;

    if (nxlCoins < 100) {
        group.innerHTML = `<span style="font-size: 0.8rem; color: #9aa0b4">You need at least 100 NXL Credits to redeem.</span>`;
        return;
    }

    const maxRedeemable = Math.min(nxlCoins, maxAmountAllowed);

    group.innerHTML = `
      <div style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center; margin-bottom: 8px;">
        <input 
          type="number" 
          id="customCreditInput" 
          placeholder="Min 100" 
          min="100" 
          step="0.01"
          max="${maxRedeemable}"
          value="${redeemedCoins > 0 ? redeemedCoins : ''}"
          class="nxl-input"
        />
        <button 
          type="button"
          onclick="applyCustomCredits(${maxRedeemable})"
          class="nxl-apply-btn"
        >
          Apply
        </button>
        ${redeemedCoins > 0 ? `
          <button
            type="button"
            onclick="removeCredits()"
            style="padding: 8px 16px; border-radius: 8px; background: transparent; color: #ff6b6b; font-weight: bold; border: 1px solid #ff6b6b; cursor: pointer;"
          >
            Remove
          </button>
        ` : ''}
      </div>
      <span style="font-size: 0.8rem; color: #9aa0b4; display: block;">You can redeem up to ${maxRedeemable} credits on this order.</span>
    `;
}

function applyCustomCredits(maxRedeemable) {
    const input = document.getElementById("customCreditInput");
    if (!input) return;

    let val = parseFloat(input.value);
    if (isNaN(val) || val < 100) {
        alert("Minimum redemption is 100 credits.");
        return;
    }
    if (val > maxRedeemable) {
        val = maxRedeemable;
        input.value = val;
    }
    if (val > nxlCoins) {
        val = nxlCoins;
    }
    redeemedCoins = val;
    calculateTotal();
}

function removeCredits() {
    redeemedCoins = 0;
    calculateTotal();
}

function calculateTotal() {
    if (basePrice === 0) return;
    
    let productPrice = basePrice;
    
    const redemTitle = document.querySelector('.nxl-redemption-title');
    if (redemTitle) redemTitle.innerText = `Redemption Options (1 Credit = ${currencySym}1)`;

    document.getElementById('summaryBase').innerText = currencySym + basePrice.toFixed(2);
    
    // 1. Coupon Discount
    if (couponDiscount > 0) {
        document.getElementById("couponDiscountRow").style.display = "flex";
        document.getElementById("summaryCouponDiscount").textContent = `- ${currencySym}${couponDiscount.toFixed(2)}`;
        productPrice -= couponDiscount;
    } else {
        document.getElementById("couponDiscountRow").style.display = "none";
    }
    
    // 2. Premium Membership Discount
    let discountPercent = 0;
    if (isPremiumUser && membershipPlan !== "none" && typeof membershipPlans !== "undefined" && membershipPlans[membershipPlan]) {
        discountPercent = membershipPlans[membershipPlan].cashback_percent;
    }
    
    let premiumDiscountAmount = 0;
    if (discountPercent > 0) {
        premiumDiscountAmount = parseFloat((productPrice * discountPercent).toFixed(2));
        document.getElementById("premiumDiscountRow").style.display = "flex";
        document.getElementById("premiumDiscountLabel").textContent = `Premium Discount (${discountPercent * 100}%)`;
        document.getElementById("summaryPremiumDiscount").textContent = `- ${currencySym}${premiumDiscountAmount.toFixed(2)}`;
        productPrice -= premiumDiscountAmount;
    } else {
        document.getElementById("premiumDiscountRow").style.display = "none";
    }
    
    if (productPrice < 0) productPrice = 0;
    
    // 3. GST (18%)
    let gst = 0;
    if (currencySym !== '$') {
        gst = parseFloat((productPrice * 0.18).toFixed(2));
    }
    document.getElementById('summaryGst').innerText = '+ ' + currencySym + gst.toFixed(2);
    
    let amountAfterGST = parseFloat((productPrice + gst).toFixed(2));
    
    // Check redeemed credits bounds
    if (redeemedCoins > nxlCoins) redeemedCoins = nxlCoins;
    if (redeemedCoins > amountAfterGST) redeemedCoins = parseFloat(amountAfterGST.toFixed(2));
    
    // 4. Render NXL Credits logic
    if (nxlCoins > 0) {
        document.getElementById("nxlCoinsSection").style.display = "flex";
        const balanceLabel = document.getElementById("nxlBalanceLabel");
        if (balanceLabel) balanceLabel.textContent = `${nxlCoins} Credits`;
        
        if (redeemedCoins > 0) {
            document.getElementById("nxlDiscountRow").style.display = "flex";
            document.getElementById("summaryNxlDiscount").textContent = `- ${currencySym}${redeemedCoins.toFixed(2)}`;
        } else {
            document.getElementById("nxlDiscountRow").style.display = "none";
        }
        
        // Re-render UI inputs based on max possible redeemable for this price
        renderRedemptionOptions(amountAfterGST);
    }
    
    // 5. Earned NXL Coins (if premium)
    let earnedNxlCoins = 0;
    if (isPremiumUser) {
        earnedNxlCoins = parseFloat((productPrice * nxlCashbackPercentage).toFixed(2)); // Use dynamic cashback rate
        document.getElementById("nxlEarnSection").style.display = "flex";
        document.getElementById("earnedCreditsLabel").textContent = `${earnedNxlCoins} NXL Credits`;
    }
    
    let finalAmount = parseFloat((amountAfterGST - redeemedCoins).toFixed(2));
    document.getElementById('summaryTotal').innerText = currencySym + finalAmount.toFixed(2);
    
    // Update Hidden Fields for form submission
    let totalDiscount = couponDiscount + premiumDiscountAmount;
    document.getElementById('hiddenCouponCode').value = discountCode;
    document.getElementById('hiddenDiscountAmount').value = totalDiscount;
    document.getElementById('hiddenNxlRedeemed').value = redeemedCoins;
}
