function initGiftCardCheckout() {
    const qtyInput = document.getElementById('gc_qty');
    const amountInput = document.getElementById('gc_amount');
    const discountEl = document.getElementById('gc_discount');
    const gstEl = document.getElementById('gc_gst');
    const totalEl = document.getElementById('gc_total');
    
    if(!qtyInput || !amountInput) return;

    let walletBalance = 0;
    const userEmail = localStorage.getItem("userEmail");
    if (userEmail) {
        fetch(`api/index.php/user/profile?email=${encodeURIComponent(userEmail)}`)
            .then(res => res.json())
            .then(data => {
                if (data.walletBalance) {
                    walletBalance = data.walletBalance;
                    const availEl = document.getElementById('gc_nxl_available');
                    if(availEl) availEl.textContent = walletBalance;
                    updateSummary();
                }
            });
    }

    const nxlInput = document.getElementById('gc_nxl_use');
    if (nxlInput) nxlInput.addEventListener('input', updateSummary);

    function updateSummary() {
        let qty = parseInt(qtyInput.value) || 1;
        if(qty < 1) { qty = 1; qtyInput.value = 1; }
        
        let basePrice = parseFloat(amountInput.value) || 0;
        let subtotal = basePrice * qty;
        
        // Membership Discount
        const membership = (localStorage.getItem('userMembership') || 'none').toLowerCase().trim();
        let membershipDiscountPercent = 0;
        
        if (membership !== 'none' && typeof membershipPlans !== 'undefined' && membershipPlans[membership]) {
            let percentDecimal = membershipPlans[membership].cashback_percent;
            membershipDiscountPercent = percentDecimal * 100;
        }

        let membershipDiscount = subtotal * (membershipDiscountPercent / 100);
        let subtotalAfterMembership = subtotal - membershipDiscount;

        // NXL Discount
        let maxNxl = Math.min(walletBalance, Math.floor(subtotalAfterMembership));
        if (nxlInput) {
            nxlInput.max = maxNxl;
            let val = parseInt(nxlInput.value) || 0;
            if (val > maxNxl) { val = maxNxl; nxlInput.value = maxNxl; }
            if (val < 0) { val = 0; nxlInput.value = 0; }
        }
        let nxlDiscount = parseInt(nxlInput ? nxlInput.value : 0) || 0;
        
        let totalDiscount = membershipDiscount + nxlDiscount;

        let gst = (subtotalAfterMembership - nxlDiscount) * 0.18; // 18% GST
        let finalAmount = subtotalAfterMembership - nxlDiscount + gst;

        document.getElementById('gc_subtotal_display').textContent = '₹' + subtotal.toFixed(2);
        
        const memRow = document.getElementById('gc_membership_discount_row');
        const memVal = document.getElementById('gc_membership_discount');
        if (membershipDiscount > 0) {
            memRow.style.display = 'flex';
            memVal.textContent = '-₹' + membershipDiscount.toFixed(2);
        } else {
            memRow.style.display = 'none';
        }

        const nxlRow = document.getElementById('gc_nxl_discount_row');
        const nxlVal = document.getElementById('gc_nxl_discount');
        if (nxlDiscount > 0) {
            nxlRow.style.display = 'flex';
            nxlVal.textContent = '-₹' + nxlDiscount.toFixed(2);
        } else {
            nxlRow.style.display = 'none';
        }

        // Calculate NXL Earned (dynamic % of base price after membership discount)
        let nxlEarned = Math.floor(subtotalAfterMembership * nxlCashbackPercentage);

        gstEl.textContent = '₹' + gst.toFixed(2);
        totalEl.textContent = '₹' + finalAmount.toFixed(2);
        document.getElementById('gc_nxl_earned_display').textContent = nxlEarned;
        
        document.getElementById('gc_final_amount_hidden').value = finalAmount.toFixed(2);
        document.getElementById('gc_gst_hidden').value = gst.toFixed(2);
        document.getElementById('gc_discount_hidden').value = totalDiscount.toFixed(2);
        document.getElementById('gc_nxl_earned_hidden').value = nxlEarned;
    }

    qtyInput.addEventListener('change', updateSummary);
    updateSummary();

    // Payment Option Selection
    const options = document.querySelectorAll('.gc-payment-option');
    options.forEach(opt => {
        opt.addEventListener('click', () => {
            options.forEach(o => o.classList.remove('active'));
            opt.classList.add('active');
            document.getElementById('payment_method').value = opt.getAttribute('data-method');
        });
    });
}

async function handleGiftCardRedeem(event) {
    event.preventDefault();
    const code = document.getElementById('gc_redeem_code').value.trim();
    const btn = document.getElementById('btn_redeem');
    const resultDiv = document.getElementById('redeem_result');
    
    if(!code) {
        resultDiv.innerHTML = '<p style="color:#ef4444;">Please enter a gift card code.</p>';
        return;
    }

    btn.disabled = true;
    btn.textContent = 'Processing...';

    try {
        const formData = new FormData();
        formData.append('code', code);
        
        // Add email from session/localStorage
        const userEmail = localStorage.getItem('userEmail');
        if(userEmail) {
            formData.append('email', userEmail);
        }

        const res = await fetch('api/index.php/giftcards/redeem', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();
        
        if(data.success) {
            // Show full-screen success popup
            showRedeemSuccessPopup(data.amount, data.new_balance);
            document.getElementById('gc_redeem_code').value = '';
        } else {
            resultDiv.innerHTML = `<p style="color:#ef4444; margin-top:15px;">❌ ${data.message || 'Failed to redeem'}</p>`;
            btn.disabled = false;
            btn.textContent = 'Redeem Gift Card';
        }
    } catch(err) {
        resultDiv.innerHTML = `<p style="color:#ef4444; margin-top:15px;">❌ Network error. Please try again.</p>`;
        btn.disabled = false;
        btn.textContent = 'Redeem Gift Card';
    }
}

function showRedeemSuccessPopup(amount, newBalance) {
    // Create overlay
    const overlay = document.createElement('div');
    overlay.id = 'redeemSuccessOverlay';
    overlay.style.cssText = `
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.65); backdrop-filter: blur(6px);
        z-index: 99999; display: flex; align-items: center; justify-content: center;
        animation: fadeInOverlay 0.3s ease;
    `;

    overlay.innerHTML = `
        <style>
            @keyframes fadeInOverlay { from { opacity:0; } to { opacity:1; } }
            @keyframes popIn { from { transform: scale(0.7); opacity:0; } to { transform: scale(1); opacity:1; } }
            @keyframes confettiFall {
                0%   { transform: translateY(-20px) rotate(0deg); opacity:1; }
                100% { transform: translateY(110vh) rotate(720deg); opacity:0; }
            }
            .redeem-popup-card {
                background: #fff;
                border-radius: 20px;
                padding: 45px 40px;
                text-align: center;
                max-width: 400px;
                width: 90%;
                box-shadow: 0 25px 60px rgba(0,0,0,0.35);
                animation: popIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
                position: relative;
                overflow: hidden;
            }
            .redeem-popup-card::before {
                content: '';
                position: absolute; top: 0; left: 0; right: 0; height: 5px;
                background: linear-gradient(90deg, #c9a34a, #f0d080, #c9a34a);
            }
            .redeem-timer-bar {
                position: absolute; bottom: 0; left: 0; height: 4px;
                background: linear-gradient(90deg, #10b981, #c9a34a);
                width: 100%;
                animation: shrinkBar 2.5s linear forwards;
            }
            @keyframes shrinkBar { from { width: 100%; } to { width: 0%; } }
            .confetti-piece {
                position: fixed;
                width: 10px; height: 10px;
                border-radius: 2px;
                animation: confettiFall linear forwards;
                pointer-events: none;
                z-index: 100000;
            }
        </style>

        <div class="redeem-popup-card">
            <div class="redeem-timer-bar"></div>
            <div style="font-size: 4rem; margin-bottom: 10px; line-height:1;">🎉</div>
            <h2 style="color: #1a1a1a; font-size: 1.6rem; margin: 0 0 6px; font-weight: 800;">Gift Card Redeemed!</h2>
            <p style="color: #666; font-size: 0.95rem; margin-bottom: 24px;">Successfully added to your wallet</p>
            
            <div style="background: linear-gradient(135deg, #f0f9f4, #e6f7ee); border: 1.5px solid #10b981; border-radius: 12px; padding: 18px; margin-bottom: 20px;">
                <p style="color: #555; font-size: 0.85rem; margin: 0 0 4px;">Amount Added</p>
                <p style="color: #10b981; font-size: 2rem; font-weight: 800; margin: 0;">₹${Number(amount).toLocaleString()}</p>
            </div>

            <div style="background: #faf6ee; border: 1px solid #c9a34a; border-radius: 10px; padding: 12px; margin-bottom: 24px;">
                <p style="color: #888; font-size: 0.8rem; margin: 0 0 2px;">New Wallet Balance</p>
                <p style="color: #c9a34a; font-size: 1.3rem; font-weight: 700; margin: 0;">₹${Number(newBalance).toLocaleString()} Credits</p>
            </div>

            <p style="color: #aaa; font-size: 0.8rem; margin: 0;">Redirecting to your wallet...</p>
        </div>
    `;

    document.body.appendChild(overlay);

    // Spawn confetti
    spawnConfetti();

    // Redirect to wallet after 2.5 seconds
    setTimeout(() => {
        window.location.href = 'wallet.php';
    }, 2500);
}

function spawnConfetti() {
    const colors = ['#c9a34a', '#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6', '#f0d080'];
    for (let i = 0; i < 60; i++) {
        setTimeout(() => {
            const piece = document.createElement('div');
            piece.className = 'confetti-piece';
            piece.style.left = Math.random() * 100 + 'vw';
            piece.style.top = '-15px';
            piece.style.background = colors[Math.floor(Math.random() * colors.length)];
            piece.style.width  = (Math.random() * 8 + 6) + 'px';
            piece.style.height = (Math.random() * 8 + 6) + 'px';
            piece.style.borderRadius = Math.random() > 0.5 ? '50%' : '2px';
            piece.style.animationDuration = (Math.random() * 2 + 1.5) + 's';
            piece.style.animationDelay = Math.random() * 0.8 + 's';
            document.body.appendChild(piece);
            setTimeout(() => piece.remove(), 4000);
        }, i * 20);
    }
}


document.addEventListener('DOMContentLoaded', () => {
    initGiftCardCheckout();
    
    const redeemForm = document.getElementById('gc_redeem_form');
    if(redeemForm) {
        redeemForm.addEventListener('submit', handleGiftCardRedeem);
    }
});
