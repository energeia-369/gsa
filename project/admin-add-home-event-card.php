<?php
$pageTitle = "GLOBAL SPORTS ARENA | Add Home Event Card";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
require_once __DIR__ . '/config/Database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['event_title'] ?? '');
    $type = $_POST['event_type'] ?? '';
    $date = trim($_POST['event_date'] ?? '');
    $destParts = explode('|', $_POST['destination'] ?? '');
    $city = trim($destParts[0] ?? '');
    $countryState = trim($destParts[1] ?? '');
    $link = trim($_POST['link'] ?? '');
    $status = $_POST['status'] ?? 'active';

    if (empty($title) || empty($type) || empty($date) || empty($city) || empty($countryState)) {
        $error = "All fields are required.";
    } elseif (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $error = "An image is required.";
    } else {
        $file = $_FILES['image'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        if (!in_array($file['type'], $allowedTypes)) {
            $error = "Only JPG, PNG, and WEBP formats are allowed.";
        } elseif ($file['size'] > $maxSize) {
            $error = "Image size cannot exceed 2MB.";
        } else {
            // Ensure upload directory exists
            $uploadDir = __DIR__ . '/assets/images/event-cards/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Create unique filename
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid('event_') . '.' . $ext;
            $destination = $uploadDir . $filename;

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $dbImagePath = 'assets/images/event-cards/' . $filename;
                
                try {
                    $db = Database::getConnection();
                    $stmt = $db->prepare("INSERT INTO home_event_cards (event_title, event_type, image, event_date, city, country_or_state, link, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$title, $type, $dbImagePath, $date, $city, $countryState, $link, $status]);
                    
                    $success = "Event card added successfully!";
                } catch (Exception $e) {
                    $error = "Database Error: " . $e->getMessage();
                }
            } else {
                $error = "Failed to upload the image.";
            }
        }
    }
}
?>
<link rel="stylesheet" href="assets/css/AdminDashboard.css?v=7">
<div class="admin-dashboard px-4 sm:px-8 py-10" style="background: #0b0c10; color: #f5f6fa; min-height: 100vh;">
  <?php require_once __DIR__ . '/includes/admin_navbar.php'; ?>
  
  <div class="admin-header" style="border-bottom: 1px solid rgba(197, 168, 92, 0.2); padding-bottom: 20px;">
    <h1>? Add Home Event Card</h1>
    <p>Create a new dynamic card for the home page carousel.</p>
  </div>

  <div class="admin-content" style="max-width: 100%; margin-top: 30px;">
    <?php if ($error): ?>
        <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid #ef4444; color: #ef4444; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div style="background: rgba(34, 197, 94, 0.1); border: 1px solid #22c55e; color: #22c55e; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <?php echo htmlspecialchars($success); ?> <a href="admin-home-event-cards.php" style="color: #fff; text-decoration: underline;">View Cards</a>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="admin-card" style="border-radius: 20px; padding: 30px; display: grid; gap: 20px;">
      
      <div>
        <label style="display: block; font-size: 0.9rem; color: #9aa0b4; margin-bottom: 8px;">Event Title *</label>
        <input type="text" name="event_title" required style="width: 100%; padding: 12px; border: 1px solid rgba(197,168,92,0.25); border-radius: 8px; background: #0b0c10; color: #fff; box-sizing: border-box;">
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div>
          <label style="display: block; font-size: 0.9rem; color: #9aa0b4; margin-bottom: 8px;">Event Type *</label>
          <select name="event_type" required style="width: 100%; padding: 12px; border: 1px solid rgba(197,168,92,0.25); border-radius: 8px; background: #0b0c10; color: #fff; box-sizing: border-box;">
            <option value="overseas">Overseas Event</option>
            <option value="state">Indian State Event</option>
          </select>
        </div>
        <div>
          <label style="display: block; font-size: 0.9rem; color: #9aa0b4; margin-bottom: 8px;">Status *</label>
          <select name="status" style="width: 100%; padding: 12px; border: 1px solid rgba(197,168,92,0.25); border-radius: 8px; background: #0b0c10; color: #fff; box-sizing: border-box;">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>
      </div>

      <div>
        <label style="display: block; font-size: 0.9rem; color: #9aa0b4; margin-bottom: 8px;">Event Image * (Max 2MB, jpg/png/webp)</label>
        <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" required style="width: 100%; padding: 10px; border: 1px solid rgba(197,168,92,0.25); border-radius: 8px; background: #0b0c10; color: #fff; box-sizing: border-box;">
      </div>

      <div>
        <label style="display: block; font-size: 0.9rem; color: #9aa0b4; margin-bottom: 8px;">Event Date Range *</label>
        <input type="text" name="event_date" placeholder="e.g. 24-26 July 2026" required style="width: 100%; padding: 12px; border: 1px solid rgba(197,168,92,0.25); border-radius: 8px; background: #0b0c10; color: #fff; box-sizing: border-box;">
      </div>

      <div>
        <label style="display: block; font-size: 0.9rem; color: #9aa0b4; margin-bottom: 8px;">Destination (City & Country/State) *</label>
        <select name="destination" required style="width: 100%; padding: 12px; border: 1px solid rgba(197,168,92,0.25); border-radius: 8px; background: #0b0c10; color: #fff; box-sizing: border-box;">
            <option value="">Select Carousel Destination</option>
            <optgroup label="Overseas Events">
                <option value="Pune / Mumbai|INDIA">INDIA</option>
                <option value="Singapore|SINGAPORE">SINGAPORE</option>
                <option value="Zurich|SWITZERLAND">SWITZERLAND</option>
                <option value="Dubai / Abu Dhabi|UAE">UAE</option>
                <option value="Phuket / Bangkok|THAILAND">THAILAND</option>
                <option value="Las Vegas|USA - LAS VEGAS">USA - LAS VEGAS</option>
                <option value="New York|USA - NEW YORK">USA - NEW YORK</option>
                <option value="Kuala Lumpur|MALAYSIA">MALAYSIA</option>
                <option value="Bali / Jakarta|INDONESIA">INDONESIA</option>
                <option value="Ho Chi Minh|VIETNAM">VIETNAM</option>
                <option value="Sydney|AUSTRALIA">AUSTRALIA</option>
                <option value="Berlin|GERMANY">GERMANY</option>
                <option value="London|UNITED KINGDOM">UNITED KINGDOM</option>
                <option value="Toronto|CANADA">CANADA</option>
            </optgroup>
            <optgroup label="Indian State Events">
                <option value="Mumbai / Pune|MAHARASHTRA">MAHARASHTRA</option>
                <option value="Bangalore|KARNATAKA">KARNATAKA</option>
                <option value="New Delhi|DELHI">DELHI</option>
                <option value="Panaji|GOA">GOA</option>
                <option value="Kochi|KERALA">KERALA</option>
                <option value="Jaipur|RAJASTHAN">RAJASTHAN</option>
                <option value="Ahmedabad|GUJARAT">GUJARAT</option>
                <option value="Coimbatore|TAMIL NADU">TAMIL NADU</option>
                <option value="Pune|PUNE">PUNE</option>
            </optgroup>
        </select>
      </div>

      <div>
        <label style="display: block; font-size: 0.9rem; color: #9aa0b4; margin-bottom: 8px;">Web Link / Page Link (Optional)</label>
        <input type="text" name="link" placeholder="e.g. events.php?country=india or https://..." style="width: 100%; padding: 12px; border: 1px solid rgba(197,168,92,0.25); border-radius: 8px; background: #0b0c10; color: #fff; box-sizing: border-box;">
      </div>

      <div style="display: flex; gap: 15px; margin-top: 10px;">
        <button type="submit" style="background: linear-gradient(135deg, #c5a85c 0%, #8c7237 100%); color: #0b0c10; border: none; padding: 15px 30px; border-radius: 8px; font-weight: bold; font-size: 1rem; cursor: pointer;">
          Save Card
        </button>
        <a href="admin-home-event-cards.php" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.15); color: #fff; padding: 15px 30px; border-radius: 8px; text-decoration: none; display: flex; align-items: center; justify-content: center; font-weight: bold;">
          Cancel
        </a>
      </div>
    </form>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const token = localStorage.getItem("token");
    const role = localStorage.getItem("userRole");

    if (!token || role !== "ADMIN") {
        alert("Access Denied: Admin login required!");
        window.location.href = "login.php";
        return;
    }
});
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
