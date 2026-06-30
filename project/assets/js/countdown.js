/**
 * GSA Pune 2026 - Countdown Timer
 * Target: 6 October 2026, 12:00:00 AM (IST - Asia/Kolkata)
 */

document.addEventListener('DOMContentLoaded', function() {
    // Check if the timer elements exist on the page
    const daysEl = document.getElementById('days') || document.getElementById('timer-days');
    const hoursEl = document.getElementById('hours') || document.getElementById('timer-hours');
    const minutesEl = document.getElementById('minutes') || document.getElementById('timer-minutes');
    const secondsEl = document.getElementById('seconds') || document.getElementById('timer-seconds');
    
    // Optional: Get the register button to change text when event is live
    const registerBtns = document.querySelectorAll('.pulse-btn');

    // Only run if at least one countdown element exists
    if (!daysEl && !hoursEl && !minutesEl && !secondsEl) return;

    // Use dynamic date if available, otherwise fallback to hardcoded
    let dateString = window.targetEventDate ? window.targetEventDate + "T00:00:00+05:30" : '2026-10-06T00:00:00+05:30';
    
    // If the database has an empty start date, default to a future date to prevent NaN
    if (!window.targetEventDate) {
        dateString = '2026-10-06T00:00:00+05:30';
    }

    const targetDate = new Date(dateString).getTime();

    let countdownInterval;

    function updateCountdown() {
        let now = new Date().getTime();
        
        // If a simulated "Today's Date" is provided, calculate the simulated current time
        // by taking that simulated date and adding the exact number of milliseconds that 
        // have passed since the page loaded, so it still smoothly ticks down.
        if (window.timerStartDate) {
            const simulatedStart = new Date(window.timerStartDate + "T00:00:00+05:30").getTime();
            const timeSinceLoad = now - window.pageLoadTime;
            now = simulatedStart + timeSinceLoad;
        }

        const distance = targetDate - now;

        // If the countdown has finished
        if (distance <= 0) {
            clearInterval(countdownInterval);
            
            // Set values to '00'
            if (daysEl) daysEl.textContent = '00';
            if (hoursEl) hoursEl.textContent = '00';
            if (minutesEl) minutesEl.textContent = '00';
            if (secondsEl) secondsEl.textContent = '00';
            
            // Optionally change "REGISTER NOW" button text to "EVENT LIVE"
            registerBtns.forEach(btn => {
                if (btn.innerHTML.includes('Register Now')) {
                    btn.innerHTML = btn.innerHTML.replace('Register Now', 'EVENT LIVE');
                }
            });
            
            return;
        }

        // Time calculations for days, hours, minutes and seconds
        const d = Math.floor(distance / (1000 * 60 * 60 * 24));
        const h = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const m = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const s = Math.floor((distance % (1000 * 60)) / 1000);

        // Display results with leading zeros if needed
        if (daysEl) daysEl.textContent = d < 10 ? '0' + d : d;
        if (hoursEl) hoursEl.textContent = h < 10 ? '0' + h : h;
        if (minutesEl) minutesEl.textContent = m < 10 ? '0' + m : m;
        if (secondsEl) secondsEl.textContent = s < 10 ? '0' + s : s;
    }

    // Initialize immediately
    updateCountdown();
    
    // Update every 1 second (1000 ms)
    countdownInterval = setInterval(updateCountdown, 1000);
});
