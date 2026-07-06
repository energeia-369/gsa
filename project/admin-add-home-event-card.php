<?php
$pageTitle = "GLOBAL SPORTS ARENA | Add Home Event Card";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
require_once __DIR__ . '/config/Database.php';

$allDestinations = [
    'international' => [],
    'national' => []
];

try {
    $db = Database::getConnection();
    
    // Handle activate/deactivate for existing cards
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_status_id'])) {
        $id = intval($_POST['toggle_status_id']);
        $newStatus = $_POST['current_status'] === 'active' ? 'inactive' : 'active';
        $stmt = $db->prepare("UPDATE home_event_cards SET status = ? WHERE id = ?");
        $stmt->execute([$newStatus, $id]);
        header("Location: admin-add-home-event-card.php");
        exit;
    }
    
    // Fetch all existing cards for the table at the bottom
    $stmtCards = $db->query("SELECT * FROM home_event_cards ORDER BY id DESC");
    $cards = $stmtCards->fetchAll(PDO::FETCH_ASSOC);
    
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
                    $stmt = $db->prepare("INSERT INTO home_event_cards (event_title, event_type, image, event_date, city, country_or_state, link, status, module_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$title, $type, $dbImagePath, $date, $city, $countryState, $link, $status, $_POST['module_type'] ?? 'home_carousel']);
                    
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
  
  <div class="admin-header" style="border-bottom: 1px solid rgba(197, 168, 92, 0.2); padding-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
    <div>
      <h1><i class="fas fa-plus-circle"></i> Add Events Card</h1>
      <p>Create a new dynamic card for the home page carousel.</p>
    </div>
    <a href="admin-home-event-cards.php" style="background: linear-gradient(135deg, #c5a85c 0%, #8c7237 100%); color: #0b0c10; padding: 12px 25px; border-radius: 8px; font-weight: bold; text-decoration: none;"><i class="fas fa-list"></i> Manage Old Cards</a>
  </div>
  <div class="admin-content" style="max-width: 100%; margin-top: 30px; display: block;">
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
      
      <div style="background: rgba(197, 168, 92, 0.05); padding: 20px; border-radius: 12px; border: 1px solid rgba(197, 168, 92, 0.2); margin-bottom: 10px;">
        <h3 style="margin-top: 0; color: #c5a85c; margin-bottom: 15px; font-size: 1.1rem; font-weight: normal;">Event Management Type</h3>
        <div style="display: flex; gap: 20px; flex-wrap: wrap;">
            <label style="cursor: pointer; display: flex; align-items: center; gap: 8px;">
                <input type="radio" name="module_type" value="gsa_carousel" required>
                <span>GSA Page Carousel Events</span>
            </label>
            <label style="cursor: pointer; display: flex; align-items: center; gap: 8px;">
                <input type="radio" name="module_type" value="home_carousel" checked required>
                <span>Home Page Carousel Events</span>
            </label>
        </div>
      </div>

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
                  <?php foreach ($allDestinations['international'] as $dest): ?>
                      <option value="<?= htmlspecialchars($dest['city'] . '|' . $dest['country']) ?>">
                          <?= htmlspecialchars($dest['country'] . ' - ' . $dest['city']) ?>
                      </option>
                  <?php endforeach; ?>
                  
                  <?php if (empty($allDestinations['international'])): ?>
                      <option value="Dubai|UAE">UAE - Dubai</option>
                  <?php endif; ?>
              </optgroup>
              <optgroup label="Indian State Events">
                  <?php foreach ($allDestinations['national'] as $dest): ?>
                      <option value="<?= htmlspecialchars($dest['city'] . '|' . $dest['country']) ?>">
                          <?= htmlspecialchars($dest['country'] . ' - ' . $dest['city']) ?>
                      </option>
                  <?php endforeach; ?>
                  
                  <?php if (empty($allDestinations['national'])): ?>
                      <option value="Mumbai|INDIA">INDIA - Mumbai</option>
                  <?php endif; ?>
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
    
    <!-- Table of existing cards -->
    <div class="admin-header" style="margin-top: 50px; border-bottom: 1px solid rgba(197, 168, 92, 0.2); padding-bottom: 15px;">
      <h2><i class="fas fa-list-alt"></i> Existing Event Cards</h2>
      <p style="color: #9aa0b4; font-size: 0.9rem;">Manage, edit, or remove your existing carousel cards here.</p>
    </div>
    
    <div class="admin-card" style="border-radius: 20px; padding: 25px; overflow-x: auto; margin-top: 20px; background: rgba(255,255,255,0.03); border: 1px solid rgba(197,168,92,0.15);">
      <div class="overflow-x-auto"><table style="width: 100%; border-collapse: collapse; font-size: 0.9rem; text-align: left;">
        <thead class="orders-table">
          <tr style="border-bottom: 1px solid rgba(197,168,92,0.25); color: #c5a85c;">
            <th style="padding: 15px 10px;">ID</th>
            <th style="padding: 15px 10px;">Image</th>
            <th style="padding: 15px 10px;">Module Type</th>
            <th style="padding: 15px 10px;">Title</th>
            <th style="padding: 15px 10px;">Type</th>
            <th style="padding: 15px 10px;">Date</th>
            <th style="padding: 15px 10px;">Location</th>
            <th style="padding: 15px 10px;">Status</th>
            <th style="padding: 15px 10px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($cards)): ?>
            <tr id="no-cards-row">
              <td colspan="9" style="text-align: center; padding: 20px; color: #9aa0b4;">No cards found.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($cards as $card): ?>
              <tr class="card-row" data-module="<?php echo htmlspecialchars($card['module_type'] ?? 'home_carousel'); ?>" style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                <td style="padding: 15px 10px;"><?php echo htmlspecialchars($card['id']); ?></td>
                <td style="padding: 15px 10px;"><img src="<?php echo htmlspecialchars($card['image']); ?>" alt="Card" style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px;"></td>
                <td style="padding: 15px 10px;">
                    <?php 
                        if (isset($card['module_type']) && $card['module_type'] === 'gsa_carousel') {
                            echo '<span style="background: rgba(197, 168, 92, 0.2); color: #c5a85c; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: bold; text-transform: uppercase;">GSA Page</span>';
                        } else {
                            echo '<span style="background: rgba(255, 255, 255, 0.1); color: #9aa0b4; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: bold; text-transform: uppercase;">Home Page</span>';
                        }
                    ?>
                </td>
                <td style="padding: 15px 10px;"><?php echo htmlspecialchars($card['event_title']); ?></td>
                <td style="padding: 15px 10px;">
                  <span style="background: rgba(197,168,92,0.1); padding: 4px 8px; border-radius: 4px; color: #c5a85c; font-size: 0.8rem; text-transform: uppercase;">
                    <?php echo htmlspecialchars($card['event_type']); ?>
                  </span>
                </td>
                <td style="padding: 15px 10px;"><?php echo htmlspecialchars($card['event_date']); ?></td>
                <td style="padding: 15px 10px;"><?php echo htmlspecialchars($card['city'] . ', ' . $card['country_or_state']); ?></td>
                <td style="padding: 15px 10px;">
                  <?php if ($card['status'] === 'active'): ?>
                    <span style="color: #22c55e; font-weight: bold;">Active</span>
                  <?php else: ?>
                    <span style="color: #eab308; font-weight: bold;">Inactive</span>
                  <?php endif; ?>
                </td>
                <td style="padding: 15px 10px; display: flex; gap: 10px; align-items: center;">
                  <a href="admin-edit-home-event-card.php?id=<?php echo $card['id']; ?>" style="color: #38bdf8; text-decoration: none;"><i class="fas fa-edit"></i> Edit</a>
                  <a href="admin-delete-home-event-card.php?id=<?php echo $card['id']; ?>" style="color: #ef4444; text-decoration: none;" onclick="return confirm('Are you sure you want to delete this card?');"><i class="fas fa-trash"></i> Delete</a>
                  <form method="POST" action="admin-add-home-event-card.php" style="display:inline; margin:0;">
                    <input type="hidden" name="toggle_status_id" value="<?php echo $card['id']; ?>">
                    <input type="hidden" name="current_status" value="<?php echo $card['status']; ?>">
                    <button type="submit" style="background: none; border: none; color: #c5a85c; cursor: pointer; padding: 0; font-size: 0.9rem;">
                      <?php echo $card['status'] === 'active' ? '<i class="fas fa-times-circle"></i> Deactivate' : '<i class="fas fa-check-circle"></i> Activate'; ?>
                    </button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
            <tr id="no-cards-row" style="display: none;">
              <td colspan="9" style="text-align: center; padding: 20px; color: #9aa0b4;">No cards match the selected type.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table></div>
    </div>
    
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

    // Filter table rows by Event Management Type
    const radioButtons = document.querySelectorAll('input[name="module_type"]');
    const rows = document.querySelectorAll('.card-row');

    function filterTable() {
        const selectedType = document.querySelector('input[name="module_type"]:checked').value;
        let visibleCount = 0;
        
        rows.forEach(row => {
            if (row.getAttribute('data-module') === selectedType) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Show/hide the "No cards found" row
        const noCardsRow = document.getElementById('no-cards-row');
        if (noCardsRow) {
            noCardsRow.style.display = visibleCount === 0 ? '' : 'none';
        }
    }

    // Add event listeners to radio buttons
    radioButtons.forEach(radio => {
        radio.addEventListener('change', filterTable);
    });

    // Initial filter
    filterTable();
});
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
