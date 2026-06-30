<?php
$pageTitle = "Medical Support | Global Sports Arena";
$disableAdminTheme = true;
include 'includes/header.php';
include 'includes/navbar.php';
?>

<link rel="stylesheet" href="assets/css/medical-support.css">

<div class="medical-support-wrapper">

  <!-- =================================
       HERO SECTION
  ================================= -->
  <section class="ms-hero-section">
    <div class="container text-center ms-container-narrow">
      <div class="ms-hero-icon">
        <i class="fas fa-stethoscope"></i>
      </div>
      <h4 class="ms-small-heading">GSA HEALTH & SAFETY</h4>
      <h1 class="ms-main-heading">Medical Support</h1>
      <p class="ms-hero-subtitle">
        24/7 medical team with ambulance and first-aid services ensuring the safety and well-being of every athlete, delegate, exhibitor, sponsor and visitor.
      </p>
      
      <div class="ms-hero-buttons">
        <a href="#contact" class="ms-btn ms-btn-primary">Request Medical Assistance</a>
        <a href="#contact" class="ms-btn ms-btn-outline">Emergency Contact</a>
      </div>
      
      <div class="ms-hero-underline"></div>
    </div>
  </section>

  <!-- =================================
       ABOUT SECTION
  ================================= -->
  <section class="ms-about-section">
    <div class="container text-center ms-container-narrow">
      <h2>World-Class Medical Care</h2>
      <p>
        Global Sports Arena provides comprehensive healthcare support during tournaments, conferences, exhibitions, summits and sports festivals. Our dedicated healthcare professionals ensure quick response and quality medical assistance.
      </p>
    </div>
  </section>

  <!-- =================================
       SERVICES SECTION
  ================================= -->
  <section class="ms-services-section">
    <div class="container">
      <div class="ms-grid-3">
        <!-- Service 1 -->
        <div class="ms-service-card" onclick="openServiceModal('Medical Stations', 'fa-hospital', 'Our fully equipped medical stations are strategically placed throughout the venue to ensure rapid access to healthcare professionals during any event.')">
          <div class="ms-card-icon"><i class="fas fa-hospital"></i></div>
          <h3>Medical Stations</h3>
        </div>
        <!-- Service 2 -->
        <div class="ms-service-card" onclick="openServiceModal('Ambulance Support', 'fa-ambulance', 'We maintain a fleet of advanced life support ambulances on standby 24/7 during events to guarantee immediate transportation in critical situations.')">
          <div class="ms-card-icon"><i class="fas fa-ambulance"></i></div>
          <h3>Ambulance Support</h3>
        </div>
        <!-- Service 3 -->
        <div class="ms-service-card" onclick="openServiceModal('Doctors On Duty', 'fa-user-md', 'Board-certified sports medicine physicians and emergency doctors are on duty at all times to provide expert diagnosis and immediate treatment.')">
          <div class="ms-card-icon"><i class="fas fa-user-md"></i></div>
          <h3>Doctors On Duty</h3>
        </div>
        <!-- Service 4 -->
        <div class="ms-service-card" onclick="openServiceModal('First Aid Centers', 'fa-band-aid', 'Accessible first-aid centers are available for minor injuries, hydration needs, and immediate triage to keep athletes and spectators safe and comfortable.')">
          <div class="ms-card-icon"><i class="fas fa-band-aid"></i></div>
          <h3>First Aid Centers</h3>
        </div>
        <!-- Service 5 -->
        <div class="ms-service-card" onclick="openServiceModal('Emergency Response Team', 'fa-heartbeat', 'A highly trained, rapid-deployment emergency response team is positioned to handle acute medical crises with a guaranteed under-3-minute response time.')">
          <div class="ms-card-icon"><i class="fas fa-heartbeat"></i></div>
          <h3>Emergency Response Team</h3>
        </div>
        <!-- Service 6 -->
        <div class="ms-service-card" onclick="openServiceModal('Essential Medical Supplies', 'fa-pills', 'Our facilities are stocked with comprehensive medical supplies ranging from over-the-counter medications to critical life-saving equipment.')">
          <div class="ms-card-icon"><i class="fas fa-pills"></i></div>
          <h3>Essential Medical Supplies</h3>
        </div>
      </div>
    </div>
    
    <!-- Service Modal -->
    <div id="serviceModal" class="ms-modal" style="display: none;">
      <div class="ms-modal-content">
        <span class="ms-modal-close" onclick="closeServiceModal()">&times;</span>
        <div class="ms-modal-icon" id="modalIcon"><i class="fas fa-hospital"></i></div>
        <h3 id="modalTitle">Service Title</h3>
        <p id="modalDesc">Service Description</p>
      </div>
    </div>

    <script>
      function openServiceModal(title, iconClass, desc) {
        document.getElementById('modalTitle').innerText = title;
        document.getElementById('modalIcon').innerHTML = '<i class="fas ' + iconClass + '"></i>';
        document.getElementById('modalDesc').innerText = desc;
        document.getElementById('serviceModal').style.display = 'flex';
      }
      function closeServiceModal() {
        document.getElementById('serviceModal').style.display = 'none';
      }
      // Close when clicking outside
      window.onclick = function(event) {
        var modal = document.getElementById('serviceModal');
        if (event.target == modal) {
          modal.style.display = 'none';
        }
      }
    </script>
  </section>

  <!-- =================================
       MEDICAL COVERAGE SECTION
  ================================= -->
  <section class="ms-stats-section">
    <div class="container">
      <div class="ms-grid-4">
        <div class="ms-stat-card">
          <h3 class="ms-stat-number">24/7</h3>
          <p class="ms-stat-label">Availability</p>
        </div>
        <div class="ms-stat-card">
          <h3 class="ms-stat-number">50+</h3>
          <p class="ms-stat-label">Medical Professionals</p>
        </div>
        <div class="ms-stat-card">
          <h3 class="ms-stat-number">100+</h3>
          <p class="ms-stat-label">Events Covered</p>
        </div>
        <div class="ms-stat-card">
          <h3 class="ms-stat-number">99%</h3>
          <p class="ms-stat-label">Response Success</p>
        </div>
      </div>
    </div>
  </section>



  <!-- =================================
       ATHLETE WELLNESS SECTION
  ================================= -->
  <section class="ms-wellness-section">
    <div class="container">
      <h2 class="text-center mb-5">Athlete Wellness</h2>
      <div class="ms-grid-3">
        <!-- Feature 1 -->
        <div class="ms-wellness-card" onclick="openServiceModal('Sports Physiotherapy', 'fa-running', 'Expert physiotherapists providing customized recovery routines, deep tissue massage, and on-site physical therapy to keep athletes at peak performance.')">
          <i class="fas fa-running ms-wellness-icon"></i>
          <h4>Sports Physiotherapy</h4>
        </div>
        <!-- Feature 2 -->
        <div class="ms-wellness-card" onclick="openServiceModal('Recovery Lounge', 'fa-bed', 'A dedicated quiet zone equipped with compression boots, ice baths, and ergonomic relaxation chairs designed specifically for post-event recovery.')">
          <i class="fas fa-bed ms-wellness-icon"></i>
          <h4>Recovery Lounge</h4>
        </div>
        <!-- Feature 3 -->
        <div class="ms-wellness-card" onclick="openServiceModal('Hydration Support', 'fa-tint', 'Advanced hydration stations offering electrolyte replenishment, vitamin-infused water, and personalized hydration planning during high-intensity events.')">
          <i class="fas fa-tint ms-wellness-icon"></i>
          <h4>Hydration Support</h4>
        </div>
        <!-- Feature 4 -->
        <div class="ms-wellness-card" onclick="openServiceModal('Health Monitoring', 'fa-laptop-medical', 'Real-time biometric tracking, heart rate monitoring, and instant baseline health checkups available directly at the arena.')">
          <i class="fas fa-laptop-medical ms-wellness-icon"></i>
          <h4>Health Monitoring</h4>
        </div>
        <!-- Feature 5 -->
        <div class="ms-wellness-card" onclick="openServiceModal('Injury Prevention', 'fa-shield-alt', 'Consult with sports science experts on biomechanics, taping techniques, and proactive injury prevention strategies tailored to your sport.')">
          <i class="fas fa-shield-alt ms-wellness-icon"></i>
          <h4>Injury Prevention</h4>
        </div>
        <!-- Feature 6 -->
        <div class="ms-wellness-card" onclick="openServiceModal('Fitness Consultation', 'fa-dumbbell', 'One-on-one sessions with elite strength and conditioning coaches to discuss long-term athletic goals and tournament preparation.')">
          <i class="fas fa-dumbbell ms-wellness-icon"></i>
          <h4>Fitness Consultation</h4>
        </div>
      </div>
    </div>
  </section>

  <!-- =================================
       SAFETY COMMITMENT SECTION
  ================================= -->
  <section class="ms-commitment-section">
    <div class="container text-center ms-container-narrow">
      <div class="ms-commitment-box">
        <h2>Committed To Athlete Safety</h2>
        <p>
          At Global Sports Arena, safety is not an option—it is a standard. We are strictly committed to international safety standards and healthcare excellence. From elite athletes to visiting spectators, our infrastructure ensures a secure, health-first environment where everyone can perform and enjoy with complete peace of mind.
        </p>
      </div>
    </div>
  </section>

  <!-- =================================
       CONTACT SECTION
  ================================= -->
  <section class="ms-contact-section" id="contact">
    <div class="container text-center">
      <div class="ms-contact-card">
        <h2>Emergency Support</h2>
        <p class="ms-contact-desc">Our dedicated medical team is on standby.</p>
        
        <div class="ms-contact-details">
          <div class="ms-contact-item">
            <i class="fas fa-phone-alt"></i>
            <span>Emergency Helpline: <strong>+91 98765 43210</strong></span>
          </div>
          <div class="ms-contact-item">
            <i class="fas fa-envelope"></i>
            <span>Email: <strong>medical@globalsportsarena.com</strong></span>
          </div>
          <div class="ms-contact-item">
            <i class="fas fa-clock"></i>
            <span>Availability: <strong>24 Hours / 7 Days</strong></span>
          </div>
        </div>

        <div class="ms-hero-buttons mt-4" style="justify-content: center;">
          <a href="tel:+919876543210" class="ms-btn ms-btn-primary">Call Now</a>
          <a href="mailto:medical@globalsportsarena.com" class="ms-btn ms-btn-outline">Email Support</a>
        </div>
      </div>
    </div>
  </section>

  <!-- =================================
       FINAL CTA
  ================================= -->
  <section class="ms-cta-section">
    <div class="container text-center">
      <h2>Your Health. Our Priority.</h2>
      <p>Professional healthcare support at every Global Sports Arena event.</p>
      <a href="#contact" class="ms-btn ms-btn-primary mt-4">Request Medical Support</a>
    </div>
  </section>

</div> <!-- End wrapper -->

<?php include 'includes/footer.php'; ?>
