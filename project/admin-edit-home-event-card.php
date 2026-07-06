<?php
$pageTitle = "GLOBAL SPORTS ARENA | Edit Home Event Card";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
require_once __DIR__ . '/config/Database.php';

$allDestinations = [
    'international' => [],
    'national' => []
];

try {
    $db = Database::getConnection();
    
    $stmt1 = $db->query("SELECT city, country FROM home_carousel_destinations WHERE is_deleted = 0");
    $dest1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);
    
    $stmt2 = $db->query("SELECT city, country FROM custom_destinations WHERE is_deleted = 0");
    $dest2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    
    $stmt3 = $db->query("SELECT state AS city, country FROM home_carousel_events WHERE state IS NOT NULL AND country IS NOT NULL");
    $dest3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);

    $stmt4 = $db->query("SELECT city, country_or_state AS country FROM home_event_cards");
    $dest4 = $stmt4->fetchAll(PDO::FETCH_ASSOC);
    
    $hardcodedDests = [
        ['city' => 'Multiple Cities', 'country' => 'INDIA'],
        ['city' => 'Singapore', 'country' => 'SINGAPORE'],
        ['city' => 'Multiple Cities', 'country' => 'SWITZERLAND'],
        ['city' => 'Dubai', 'country' => 'UAE'],
        ['city' => 'Phuket', 'country' => 'THAILAND'],
        ['city' => 'Las Vegas', 'country' => 'USA - LAS VEGAS'],
        ['city' => 'New York', 'country' => 'USA - NEW YORK'],
        ['city' => 'Kuala Lumpur', 'country' => 'MALAYSIA'],
        ['city' => 'Bali', 'country' => 'INDONESIA'],
        ['city' => 'Ho Chi Minh', 'country' => 'VIETNAM'],
        ['city' => 'Sydney', 'country' => 'AUSTRALIA'],
        ['city' => 'Berlin', 'country' => 'GERMANY'],
        ['city' => 'London', 'country' => 'UNITED KINGDOM'],
        ['city' => 'Toronto', 'country' => 'CANADA'],
        ['city' => 'Chennai', 'country' => 'TAMIL NADU'],
        ['city' => 'Pune', 'country' => 'PUNE'],
        ['city' => 'Mumbai', 'country' => 'MAHARASHTRA'],
        ['city' => 'Bangalore', 'country' => 'KARNATAKA'],
        ['city' => 'New Delhi', 'country' => 'DELHI'],
        ['city' => 'Panaji', 'country' => 'GOA'],
        ['city' => 'Kochi', 'country' => 'KERALA'],
        ['city' => 'Jaipur', 'country' => 'RAJASTHAN'],
        ['city' => 'Ahmedabad', 'country' => 'GUJARAT']
    ];
    
    $combined = array_merge($dest1, $dest2, $dest3, $dest4, $hardcodedDests);
    $uniqueDests = [];
    
    foreach ($combined as $d) {
        $c = trim(strtoupper($d['country']));
        $city = trim($d['city']);
        $key = $city . '|' . $c;
        if (!isset($uniqueDests[$key])) {
            $uniqueDests[$key] = [
                'city' => $city,
                'country' => $c
            ];
            
            $isNational = ($c === 'INDIA' || in_array($c, ['MAHARASHTRA', 'KARNATAKA', 'TAMIL NADU', 'DELHI', 'GOA', 'KERALA', 'RAJASTHAN', 'GUJARAT', 'PUNE']));
            if ($isNational) {
                $allDestinations['national'][] = $uniqueDests[$key];
            } else {
                $allDestinations['international'][] = $uniqueDests[$key];
            }
        }
    }
} catch (Exception $e) {
    // Fail silently, fallback to empty arrays
}

$error = '';
$success = '';
$card = null;
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$id) {
    header("Location: admin-home-event-cards.php");
    exit;
}

try {
    $db = Database::getConnection();
    
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
            $error = "All fields except image are required.";
        } else {
            $dbImagePath = null;

            // Handle image upload if provided
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['image'];
                $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
                $maxSize = 2 * 1024 * 1024; // 2MB

                if (!in_array($file['type'], $allowedTypes)) {
                    $error = "Only JPG, PNG, and WEBP formats are allowed.";
                } elseif ($file['size'] > $maxSize) {
                    $error = "Image size cannot exceed 2MB.";
                } else {
                    $uploadDir = __DIR__ . '/assets/images/event-cards/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $filename = uniqid('event_') . '.' . $ext;
                    $destination = $uploadDir . $filename;

                    if (move_uploaded_file($file['tmp_name'], $destination)) {
                        $dbImagePath = 'assets/images/event-cards/' . $filename;
                    } else {
                        $error = "Failed to upload the new image.";
                    }
                }
            }

            if (!$error) {
                if ($dbImagePath) {
                    $stmt = $db->prepare("UPDATE home_event_cards SET event_title=?, event_type=?, image=?, event_date=?, city=?, country_or_state=?, link=?, status=?, module_type=? WHERE id=?");
                    $stmt->execute([$title, $type, $dbImagePath, $date, $city, $countryState, $link, $status, $_POST['module_type'] ?? 'home_carousel', $id]);
                } else {
                    $stmt = $db->prepare("UPDATE home_event_cards SET event_title=?, event_type=?, event_date=?, city=?, country_or_state=?, link=?, status=?, module_type=? WHERE id=?");
                    $stmt->execute([$title, $type, $date, $city, $countryState, $link, $status, $_POST['module_type'] ?? 'home_carousel', $id]);
                }
                $success = "Event card updated successfully!";
            }
        }
    }

    $stmt = $db->prepare("SELECT * FROM home_event_cards WHERE id = ?");
    $stmt->execute([$id]);
    $card = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$card) {
        header("Location: admin-home-event-cards.php");
        exit;
    }

} catch (Exception $e) {
    $error = "Database Error: " . $e->getMessage();
}
?>
<link rel="stylesheet" href="assets/css/AdminDashboard.css?v=7">
<div class="admin-dashboard px-4 sm:px-8 py-10" style="background: #0b0c10; color: #f5f6fa; min-height: 100vh;">
  <?php require_once __DIR__ . '/includes/admin_navbar.php'; ?>
  
  <div class="admin-header" style="border-bottom: 1px solid rgba(197, 168, 92, 0.2); padding-bottom: 20px;">
    <h1><i class="fas fa-edit"></i> Edit Events Card</h1>
    <p>Update an existing dynamic card for the home page carousel.</p>
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

    <?php if ($card): ?>
    <form method="POST" enctype="multipart/form-data" class="admin-card" style="border-radius: 20px; padding: 30px; display: grid; gap: 20px;">
      
      <div style="background: rgba(197, 168, 92, 0.05); padding: 20px; border-radius: 12px; border: 1px solid rgba(197, 168, 92, 0.2); margin-bottom: 10px;">
        <h3 style="margin-top: 0; color: #c5a85c; margin-bottom: 15px; font-size: 1.1rem; font-weight: normal;">Event Management Type</h3>
        <div style="display: flex; gap: 20px; flex-wrap: wrap;">
            <label style="cursor: pointer; display: flex; align-items: center; gap: 8px;">
                <input type="radio" name="module_type" value="gsa_carousel" <?= (isset($card['module_type']) && $card['module_type'] === 'gsa_carousel') ? 'checked' : '' ?> required>
                <span>GSA Page Carousel Events</span>
            </label>
            <label style="cursor: pointer; display: flex; align-items: center; gap: 8px;">
                <input type="radio" name="module_type" value="home_carousel" <?= (!isset($card['module_type']) || $card['module_type'] === 'home_carousel') ? 'checked' : '' ?> required>
                <span>Home Page Carousel Events</span>
            </label>
        </div>
      </div>

      <div>
        <label style="display: block; font-size: 0.9rem; color: #9aa0b4; margin-bottom: 8px;">Event Title *</label>
        <input type="text" name="event_title" value="<?php echo htmlspecialchars($card['event_title']); ?>" required style="width: 100%; padding: 12px; border: 1px solid rgba(197,168,92,0.25); border-radius: 8px; background: #0b0c10; color: #fff; box-sizing: border-box;">
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div>
          <label style="display: block; font-size: 0.9rem; color: #9aa0b4; margin-bottom: 8px;">Event Type *</label>
          <select name="event_type" required style="width: 100%; padding: 12px; border: 1px solid rgba(197,168,92,0.25); border-radius: 8px; background: #0b0c10; color: #fff; box-sizing: border-box;">
            <option value="overseas" <?php echo $card['event_type'] === 'overseas' ? 'selected' : ''; ?>>Overseas Event</option>
            <option value="state" <?php echo $card['event_type'] === 'state' ? 'selected' : ''; ?>>Indian State Event</option>
          </select>
        </div>
        <div>
          <label style="display: block; font-size: 0.9rem; color: #9aa0b4; margin-bottom: 8px;">Status *</label>
          <select name="status" style="width: 100%; padding: 12px; border: 1px solid rgba(197,168,92,0.25); border-radius: 8px; background: #0b0c10; color: #fff; box-sizing: border-box;">
            <option value="active" <?php echo $card['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
            <option value="inactive" <?php echo $card['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
          </select>
        </div>
      </div>

      <div>
        <label style="display: block; font-size: 0.9rem; color: #9aa0b4; margin-bottom: 8px;">Event Image (Leave blank to keep current)</label>
        <div style="margin-bottom: 10px;">
            <img src="<?php echo htmlspecialchars($card['image']); ?>" alt="Current Image" style="max-height: 100px; border-radius: 8px; border: 1px solid rgba(197,168,92,0.2);">
        </div>
        <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" style="width: 100%; padding: 10px; border: 1px solid rgba(197,168,92,0.25); border-radius: 8px; background: #0b0c10; color: #fff; box-sizing: border-box;">
      </div>

      <div>
        <label style="display: block; font-size: 0.9rem; color: #9aa0b4; margin-bottom: 8px;">Event Date Range *</label>
        <input type="text" name="event_date" value="<?php echo htmlspecialchars($card['event_date']); ?>" required style="width: 100%; padding: 12px; border: 1px solid rgba(197,168,92,0.25); border-radius: 8px; background: #0b0c10; color: #fff; box-sizing: border-box;">
      </div>

      <div>
        <label style="display: block; font-size: 0.9rem; color: #9aa0b4; margin-bottom: 8px;">Destination (City & Country/State) *</label>
        <select name="destination" required style="width: 100%; padding: 12px; border: 1px solid rgba(197,168,92,0.25); border-radius: 8px; background: #0b0c10; color: #fff; box-sizing: border-box;">
            <?php $currDest = $card['city'] . '|' . $card['country_or_state']; ?>
            <option value="">Select Carousel Destination</option>
            <optgroup label="Overseas Events">
                <?php foreach ($allDestinations['international'] as $dest): ?>
                    <?php $val = $dest['city'] . '|' . $dest['country']; ?>
                    <option value="<?= htmlspecialchars($val) ?>" <?= ($currDest === $val) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($dest['country'] . ' - ' . $dest['city']) ?>
                    </option>
                <?php endforeach; ?>
                
                <?php if (empty($allDestinations['international'])): ?>
                    <option value="Dubai|UAE" <?= ($currDest === 'Dubai|UAE') ? 'selected' : '' ?>>UAE - Dubai</option>
                <?php endif; ?>
            </optgroup>
            <optgroup label="Indian State Events">
                <?php foreach ($allDestinations['national'] as $dest): ?>
                    <?php $val = $dest['city'] . '|' . $dest['country']; ?>
                    <option value="<?= htmlspecialchars($val) ?>" <?= ($currDest === $val) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($dest['country'] . ' - ' . $dest['city']) ?>
                    </option>
                <?php endforeach; ?>
                
                <?php if (empty($allDestinations['national'])): ?>
                    <option value="Mumbai|INDIA" <?= ($currDest === 'Mumbai|INDIA') ? 'selected' : '' ?>>INDIA - Mumbai</option>
                <?php endif; ?>
            </optgroup>
        </select>
      </div>

      <div>
        <label style="display: block; font-size: 0.9rem; color: #9aa0b4; margin-bottom: 8px;">Web Link / Page Link (Optional)</label>
        <input type="text" name="link" value="<?php echo htmlspecialchars($card['link'] ?? ''); ?>" placeholder="e.g. events.php?country=india or https://..." style="width: 100%; padding: 12px; border: 1px solid rgba(197,168,92,0.25); border-radius: 8px; background: #0b0c10; color: #fff; box-sizing: border-box;">
      </div>

      <div style="display: flex; gap: 15px; margin-top: 10px;">
        <button type="submit" style="background: linear-gradient(135deg, #c5a85c 0%, #8c7237 100%); color: #0b0c10; border: none; padding: 15px 30px; border-radius: 8px; font-weight: bold; font-size: 1rem; cursor: pointer;">
          Update Card
        </button>
        <a href="admin-delete-home-event-card.php?id=<?php echo $id; ?>" onclick="return confirm('Are you sure you want to delete this card?');" style="background: rgba(220,38,38,0.12); border: 1px solid #dc2626; color: #f87171; padding: 15px 30px; border-radius: 8px; text-decoration: none; display: flex; align-items: center; justify-content: center; font-weight: bold;">
          Delete Card
        </a>
        <a href="admin-home-event-cards.php" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.15); color: #fff; padding: 15px 30px; border-radius: 8px; text-decoration: none; display: flex; align-items: center; justify-content: center; font-weight: bold;">
          Cancel
        </a>
      </div>
    </form>
    <?php endif; ?>
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
