<footer class="footer-premium">
  <div class="footer-top-grid">
    <!-- Brand Column -->
    <div class="footer-brand-col">
      <h2> ENERGIA</h2>
      <p>
        One Ecosystem. Infinite Possibilities. The leading championship platform for sports tournament bookings and authentic merchandise.
      </p>
      


    </div>

    <!-- Company Links -->
    <div class="footer-links-col">
      <h4>Company</h4>
      <ul>
        <li><a href="about-event.php">About Us</a></li>
        <li><a href="index.php#partners">Our Pillars</a></li>
        <li><a href="faq.php">F.A.Q.</a></li>
        <li><a href="contact.php">Press & Media</a></li>
      </ul>
    </div>

    <!-- Events Links -->
    <div class="footer-links-col">
      <h4>Events</h4>
      <ul>
        <li><a href="event-registration.php">Nexus Elite</a></li>
        <li><a href="event-registration.php">Maytriya Meet</a></li>
        <li><a href="gsa-details.php">GSA League</a></li>
        <li><a href="sports-categories.php">All Events</a></li>
      </ul>
    </div>

    <!-- Destinations Links -->
    <div class="footer-links-col">
      <h4>Destinations</h4>
      <ul>
        <li><a href="index.php#destinations">India</a></li>
        <li><a href="index.php#destinations">Singapore</a></li>
        <li><a href="index.php#destinations">UK</a></li>
        <li><a href="index.php#destinations">All Cities</a></li>
      </ul>
    </div>

    <!-- Membership Links -->
    <div class="footer-links-col">
      <h4>Membership</h4>
      <ul>
        <li><a href="index.php#membership">Membership Plans</a></li>
        <li><a href="index.php#membership">Member Benefits</a></li>
        <li><a href="register.php">How to Join</a></li>
      </ul>
    </div>

    <!-- NXL Credits Links -->
    <div class="footer-links-col">
      <h4>NXL Credits</h4>
      <ul>
        <li><a href="credits.php">About NXL Credits</a></li>
        <li><a href="credits.php">How It Works</a></li>
        <li><a href="credits.php">Earn Credits</a></li>
        <li><a href="credits.php">Redeem Credits</a></li>
      </ul>
    </div>
  </div>

  <div style="display: flex; flex-direction: column; align-items: center; margin-bottom: 1rem; gap: 0.75rem;">
    <form class="newsletter-form flex flex-col sm:flex-row gap-2 items-center w-full max-w-md" id="newsletterForm" onsubmit="handleSubscribe(event)">
      <input
        type="email"
        id="newsletterEmail"
        placeholder="Your email address"
        class="w-full flex-1"
        style="
          background: rgba(22, 24, 38, 0.9);
          border: 1px solid rgba(197, 168, 92, 0.3);
          padding: 10px 15px;
          border-radius: 8px;
          color: #fff;
          outline: none;
          font-size: 0.85rem;
          box-sizing: border-box;
        "
        required
      />
      <button
        type="submit"
        id="newsletterBtn"
        class="w-full sm:w-auto mt-2 sm:mt-0"
        style="
          background: linear-gradient(135deg, #c5a85c 0%, #8c7237 100%);
          color: #0b0c10;
          border: none;
          padding: 10px 15px;
          border-radius: 8px;
          font-weight: 700;
          cursor: pointer;
          font-size: 0.85rem;
          white-space: nowrap;
        "
      >
        Subscribe
      </button>
    </form>

    <div class="footer-socials" style="display: flex; gap: 1rem; align-items: center;">
      <a href="index.php" title="Home"><i class="fa-solid fa-house"></i></a>
      <a href="gallery.php" title="Photo Gallery"><i class="fa-brands fa-instagram"></i></a>
      <a href="media-hub.php" title="Event Highlights & Media"><i class="fa-brands fa-youtube"></i></a>
      <a href="sponsors.php" class="icon-link" title="Sponsorship & Business"><i class="fa-solid fa-briefcase"></i></a>
    </div>
  </div>

  <div class="footer-bottom-strip">
    <p>&copy; <?php echo date('Y'); ?> Energia . All Rights Reserved.</p>
    


    <div class="footer-bottom-info">
      <span>
        <strong class="footer-bottom-icon">✉️</strong> <a href="contact.php" style="color: inherit; text-decoration: none;">Contact Us</a>
      </span>
      
    </div>
  </div>
</footer>
</div> <!-- Closing tag for main-layout -->



<script>
async function handleSubscribe(e) {
    e.preventDefault();
    const emailInput = document.getElementById("newsletterEmail");
    const email = emailInput.value.trim();
    if (!email) return;

    const btn = document.getElementById("newsletterBtn");
    btn.disabled = true;
    btn.textContent = "...";

    try {
        const res = await fetch("api/index.php/newsletter/subscribe", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ email: email })
        });
        const data = await res.json();
        alert(data.message || "Subscribed successfully!");
        emailInput.value = "";
    } catch (err) {
        console.error("Newsletter error", err);
        alert("Failed to subscribe. Please try again.");
    } finally {
        btn.disabled = false;
        btn.textContent = "Subscribe";
    }
}
</script>
</body>
</html>
