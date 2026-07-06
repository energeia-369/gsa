<?php
$pageTitle = "GLOBAL SPORTS ARENA | Event Editor";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

// Auth check via JS
?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (!localStorage.getItem("token") || localStorage.getItem("userRole") !== "ADMIN") {
            alert("Access Denied: Admin login required!");
            window.location.href = "login.php";
        }
    });
</script>
<?php

require_once 'config/Database.php';
$pdo = Database::getConnection();

// Process Event Deletion
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    
    // Server side password check could be implemented, but right now JS checks it. 
    // Ideally we'd send it via POST, but to keep the current flow:
    $pdo->query("DELETE FROM events WHERE id = $delId");
    header("Location: admin-events.php");
    exit;
}

// Fetch delete event password
$delPassStmt = $pdo->query("SELECT setting_value FROM system_settings WHERE setting_key = 'delete_event_password'");
$delPassRow = $delPassStmt->fetch();
$deleteEventPassword = $delPassRow ? $delPassRow['setting_value'] : 'admin123';

$eventId = isset($_GET['id']) ? (int)$_GET['id'] : null;
$event = null;
$msg = '';

if ($eventId) {
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([$eventId]);
    $event = $stmt->fetch();
    if (!$event) {
        echo "<script>alert('Event not found.'); window.location.href='admin-events.php';</script>";
        exit();
    }
}

$homeCardsStmt = $pdo->query("SELECT id, event_title FROM home_event_cards ORDER BY id ASC");
$homeCards = $homeCardsStmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_tournament']) && $eventId) {
        $t_title = $_POST['t_title'] ?? '';
        $t_dates = $_POST['t_dates'] ?? '';
        $t_insert = $pdo->prepare("INSERT INTO event_tournaments (event_id, title, dates) VALUES (?, ?, ?)");
        $t_insert->execute([$eventId, $t_title, $t_dates]);
        $msg = "Tournament added successfully!";
    } elseif (isset($_POST['add_pricing']) && $eventId) {
        $p_tier = $_POST['p_tier'] ?? '';
        $p_price = $_POST['p_price'] ?? 0;
        $p_insert = $pdo->prepare("INSERT INTO event_pricing (event_id, tier_name, price) VALUES (?, ?, ?)");
        $p_insert->execute([$eventId, $p_tier, $p_price]);
        $msg = "Pricing tier added successfully!";
    } else {
        $title = $_POST['title'] ?? '';
        $slug = $_POST['slug'] ?? '';
        $status = $_POST['status'] ?? 'draft';
        $description = $_POST['description'] ?? '';
        $start_date = !empty($_POST['start_date']) ? $_POST['start_date'] : null;
        $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
        $timer_start_date = !empty($_POST['timer_start_date']) ? $_POST['timer_start_date'] : null;
        
        $hero_banner_url = $_POST['hero_banner_url'] ?? '';
        $logo_url = $_POST['logo_url'] ?? '';

        if (isset($_FILES['hero_banner_file']) && $_FILES['hero_banner_file']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'assets/images/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $fileName = time() . '_' . basename($_FILES['hero_banner_file']['name']);
            $uploadFile = $uploadDir . $fileName;
            if (move_uploaded_file($_FILES['hero_banner_file']['tmp_name'], $uploadFile)) {
                $hero_banner_url = $uploadFile;
            }
        }

        if (isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'assets/images/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $fileName = time() . '_logo_' . basename($_FILES['logo_file']['name']);
            $uploadFile = $uploadDir . $fileName;
            if (move_uploaded_file($_FILES['logo_file']['tmp_name'], $uploadFile)) {
                $logo_url = $uploadFile;
            }
        }
        $location = $_POST['location'] ?? '';
        
        $gala_title = $_POST['gala_title'] ?? '';
        $gala_venue = $_POST['gala_venue'] ?? '';
        $gala_date = $_POST['gala_date'] ?? '';
        $gala_time = $_POST['gala_time'] ?? '';
        $gala_description = $_POST['gala_description'] ?? '';
        $custom_html = $_POST['custom_html'] ?? null;
        $delegate_fee = !empty($_POST['delegate_fee']) ? $_POST['delegate_fee'] : null;
        $delegate_currency = !empty($_POST['delegate_currency']) ? $_POST['delegate_currency'] : null;

        // Handle dynamic Schedule Data
        $schedule_data = null;
        if (isset($_POST['schedule_day']) && is_array($_POST['schedule_day'])) {
            $schedule_arr = [];
            for ($i = 0; $i < count($_POST['schedule_day']); $i++) {
                if (!empty($_POST['schedule_day'][$i]) || !empty($_POST['schedule_title'][$i])) {
                    $schedule_arr[] = [
                        'day' => $_POST['schedule_day'][$i] ?? '',
                        'title' => $_POST['schedule_title'][$i] ?? '',
                        'time' => $_POST['schedule_time'][$i] ?? '',
                        'description' => $_POST['schedule_desc'][$i] ?? ''
                    ];
                }
            }
            if (!empty($schedule_arr)) {
                $schedule_data = json_encode($schedule_arr);
            }
        }

        // Handle dynamic Sports Data
        $sports_data = null;
        if (isset($_POST['sport_title']) && is_array($_POST['sport_title'])) {
            $sports_arr = [];
            for ($i = 0; $i < count($_POST['sport_title']); $i++) {
                if (!empty($_POST['sport_title'][$i])) {
                    $sportImg = $_POST['sport_image_existing'][$i] ?? '';
                    if (isset($_FILES['sport_image_file']['name'][$i]) && $_FILES['sport_image_file']['error'][$i] === UPLOAD_ERR_OK) {
                        $uploadDir = 'assets/images/';
                        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                        $fileName = time() . '_sport_' . basename($_FILES['sport_image_file']['name'][$i]);
                        $uploadFile = $uploadDir . $fileName;
                        if (move_uploaded_file($_FILES['sport_image_file']['tmp_name'][$i], $uploadFile)) {
                            $sportImg = $uploadFile;
                        }
                    }
                    $sports_arr[] = [
                        'title' => $_POST['sport_title'][$i] ?? '',
                        'icon' => $_POST['sport_icon'][$i] ?? 'fa-table-tennis',
                        'image' => $sportImg,
                        'prize' => $_POST['sport_prize'][$i] ?? '',
                        'prize_currency' => $_POST['sport_prize_currency'][$i] ?? 'INR',
                        'badge' => $_POST['sport_badge'][$i] ?? 'Popular',
                        'categories' => $_POST['sport_categories'][$i] ?? '',
                        'currency' => $_POST['sport_currency'][$i] ?? 'INR',
                        'price_individual' => $_POST['sport_price_individual'][$i] ?? '',
                        'price_pair' => $_POST['sport_price_pair'][$i] ?? '',
                        'price_team' => $_POST['sport_price_team'][$i] ?? ''
                    ];
                }
            }
            if (!empty($sports_arr)) {
                $sports_data = json_encode($sports_arr);
            }
        }

        // Handle dynamic Sponsors Data
        $sponsors_data = null;
        if (isset($_POST['sponsor_name']) && is_array($_POST['sponsor_name'])) {
            $sponsors_arr = [];
            for ($i = 0; $i < count($_POST['sponsor_name']); $i++) {
                if (!empty($_POST['sponsor_name'][$i]) || !empty($_POST['sponsor_img'][$i])) {
                    $sponsors_arr[] = [
                        'name' => $_POST['sponsor_name'][$i] ?? '',
                        'website' => $_POST['sponsor_website'][$i] ?? '',
                        'type' => $_POST['sponsor_type'][$i] ?? '',
                        'img' => $_POST['sponsor_img'][$i] ?? ''
                    ];
                }
            }
            if (!empty($sponsors_arr)) {
                $sponsors_data = json_encode($sponsors_arr);
            }
        }

        // Handle dynamic Exhibitor Data
        $exhibitor_data = null;
        if (isset($_POST['exhibitor_title']) && is_array($_POST['exhibitor_title'])) {
            $exhibitor_arr = [];
            for ($i = 0; $i < count($_POST['exhibitor_title']); $i++) {
                if (!empty($_POST['exhibitor_title'][$i])) {
                    $exhibitor_arr[] = [
                        'title' => $_POST['exhibitor_title'][$i] ?? '',
                        'icon' => $_POST['exhibitor_icon'][$i] ?? 'fa-store',
                        'size' => $_POST['exhibitor_size'][$i] ?? '',
                        'desc' => $_POST['exhibitor_desc'][$i] ?? '',
                        'currency' => $_POST['exhibitor_currency'][$i] ?? 'INR',
                        'price' => $_POST['exhibitor_price'][$i] ?? '',
                        'badge' => $_POST['exhibitor_badge'][$i] ?? ''
                    ];
                }
            }
            if (!empty($exhibitor_arr)) {
                $exhibitor_data = json_encode($exhibitor_arr);
            }
        }

        // Handle dynamic Locations Data
        $locations_data = null;
        if (isset($_POST['loc_name']) && is_array($_POST['loc_name'])) {
            $locations_arr = [];
            for ($i = 0; $i < count($_POST['loc_name']); $i++) {
                if (!empty($_POST['loc_name'][$i])) {
                    $locBg = $_POST['loc_bg_existing'][$i] ?? '';
                    if (isset($_FILES['loc_bg_file']['name'][$i]) && $_FILES['loc_bg_file']['error'][$i] === UPLOAD_ERR_OK) {
                        $uploadDir = 'assets/images/';
                        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                        $fileName = time() . '_loc_' . basename($_FILES['loc_bg_file']['name'][$i]);
                        $uploadFile = $uploadDir . $fileName;
                        if (move_uploaded_file($_FILES['loc_bg_file']['tmp_name'][$i], $uploadFile)) {
                            $locBg = $uploadFile;
                        }
                    }
                    $locations_arr[] = [
                        'name' => $_POST['loc_name'][$i] ?? '',
                        'subtitle' => $_POST['loc_subtitle'][$i] ?? '',
                        'bg' => $locBg,
                        'items' => $_POST['loc_items'][$i] ?? ''
                    ];
                }
            }
            if (!empty($locations_arr)) {
                $locations_data = json_encode($locations_arr);
            }
        }

        // Handle dynamic Gala Passes
        $gala_passes_data = null;
        if (isset($_POST['gala_pass_title']) && is_array($_POST['gala_pass_title'])) {
            $passes_arr = [];
            for ($i = 0; $i < count($_POST['gala_pass_title']); $i++) {
                if (!empty($_POST['gala_pass_title'][$i])) {
                    $passes_arr[] = [
                        'title' => $_POST['gala_pass_title'][$i] ?? '',
                        'currency' => $_POST['gala_pass_currency'][$i] ?? 'INR',
                        'price' => $_POST['gala_pass_price'][$i] ?? '0',
                        'features' => $_POST['gala_pass_features'][$i] ?? ''
                    ];
                }
            }
            if (!empty($passes_arr)) {
                $gala_passes_data = json_encode($passes_arr);
            }
        }

        if ($eventId) {
            $update = $pdo->prepare("UPDATE events SET title=?, slug=?, status=?, location=?, description=?, custom_html=?, schedule_data=?, sports_data=?, sponsors_data=?, exhibitor_data=?, locations_data=?, start_date=?, end_date=?, timer_start_date=?, hero_banner_url=?, logo_url=?, gala_title=?, gala_venue=?, gala_date=?, gala_time=?, gala_description=?, gala_passes_data=?, delegate_fee=?, delegate_currency=? WHERE id=?");
            $update->execute([$title, $slug, $status, $location, $description, $custom_html, $schedule_data, $sports_data, $sponsors_data, $exhibitor_data, $locations_data, $start_date, $end_date, $timer_start_date, $hero_banner_url, $logo_url, $gala_title, $gala_venue, $gala_date, $gala_time, $gala_description, $gala_passes_data, $delegate_fee, $delegate_currency, $eventId]);
            
            $msg_type = 'updated';
        } else {
            $insert = $pdo->prepare("INSERT INTO events (title, slug, status, location, description, custom_html, schedule_data, sports_data, sponsors_data, exhibitor_data, locations_data, start_date, end_date, timer_start_date, hero_banner_url, logo_url, gala_title, gala_venue, gala_date, gala_time, gala_description, gala_passes_data, delegate_fee, delegate_currency) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $insert->execute([$title, $slug, $status, $location, $description, $custom_html, $schedule_data, $sports_data, $sponsors_data, $exhibitor_data, $locations_data, $start_date, $end_date, $timer_start_date, $hero_banner_url, $logo_url, $gala_title, $gala_venue, $gala_date, $gala_time, $gala_description, $gala_passes_data, $delegate_fee, $delegate_currency]);
            $msg_type = 'created';
        }
        
        $home_event_card_id = !empty($_POST['home_event_card_id']) ? (int)$_POST['home_event_card_id'] : null;
        if ($home_event_card_id) {
            $link = "event-details.php?slug=" . $slug;
            $updateCard = $pdo->prepare("UPDATE home_event_cards SET link = ? WHERE id = ?");
            $updateCard->execute([$link, $home_event_card_id]);
        }

        echo "<script>window.location.href='admin-events.php?msg=" . $msg_type . "';</script>";
        exit();
    }
}
?>

<link rel="stylesheet" href="assets/css/AdminDashboard.css?v=7">

<style>
    .editor-form { background: rgba(255,255,255,0.05); padding: 2rem; border-radius: 8px; border: 1px solid rgba(197,168,92,0.3); }
    .form-group { margin-bottom: 1.5rem; }
    .form-group label { display: block; margin-bottom: 0.5rem; color: #c5a85c; font-weight: bold; }
    .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 10px; border-radius: 4px; border: 1px solid rgba(197,168,92,0.5); background: rgba(0,0,0,0.5); color: #fff; }
    .btn-gold { background: linear-gradient(135deg, #c5a85c, #f5d87a); color: #000; padding: 10px 20px; border-radius: 4px; border: none; font-weight: bold; cursor: pointer; }
    .dynamic-block { border: 1px solid rgba(197,168,92,0.4); padding: 15px; margin-bottom: 15px; border-radius: 8px; background: rgba(255,255,255,0.03); }
    body.light-theme .editor-form { background: rgba(0,0,0,0.02); }
    body.light-theme .dynamic-block { background: rgba(0,0,0,0.03); border: 1px solid rgba(197,168,92,0.4); }
    body.light-theme .form-group input, body.light-theme .form-group textarea, body.light-theme .form-group select { background: #fff; color: #000; }
</style>

<div class="admin-dashboard px-4 sm:px-8 pt-24 pb-10" style="background: #0b0c10; color: #f5f6fa; min-height: 100vh;">
  <?php require_once __DIR__ . '/includes/admin_navbar.php'; ?>
  
  <div class="admin-header" style="border-bottom: 1px solid rgba(197, 168, 92, 0.2); padding-bottom: 20px; margin-bottom: 20px;">
    <h1><?= $eventId ? 'Edit Event: ' . htmlspecialchars($event['title']) : 'Create New Event' ?></h1>
    <a href="admin-events.php" style="color: #c5a85c;">&larr; Back to Events</a>
  </div>

  <?php if ($msg): ?>
      <div style="background: rgba(16, 185, 129, 0.2); color: #10b981; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
          <?= htmlspecialchars($msg) ?>
      </div>
  <?php endif; ?>

  <form class="editor-form" method="POST" enctype="multipart/form-data">
      <div class="form-group">
          <label>Event Title</label>
          <select name="title" required id="eventTitleSelect" onchange="updateSlug()">
              <option value="">-- Select Location --</option>
              <optgroup label="International">
                  <option value="India" <?= ($event && $event['title'] === 'India') ? 'selected' : '' ?>>India</option>
                  <option value="Singapore" <?= ($event && $event['title'] === 'Singapore') ? 'selected' : '' ?>>Singapore</option>
                  <option value="Switzerland" <?= ($event && $event['title'] === 'Switzerland') ? 'selected' : '' ?>>Switzerland</option>
                  <option value="UAE" <?= ($event && $event['title'] === 'UAE') ? 'selected' : '' ?>>UAE</option>
                  <option value="Thailand" <?= ($event && $event['title'] === 'Thailand') ? 'selected' : '' ?>>Thailand</option>
                  <option value="USA - Las Vegas" <?= ($event && $event['title'] === 'USA - Las Vegas') ? 'selected' : '' ?>>USA - Las Vegas</option>
                  <option value="USA - New York" <?= ($event && $event['title'] === 'USA - New York') ? 'selected' : '' ?>>USA - New York</option>
                  <option value="Malaysia" <?= ($event && ($event['title'] === 'Malaysia' || stripos($event['title'], 'Malaysia') !== false)) ? 'selected' : '' ?>>Malaysia</option>
                  <option value="Indonesia" <?= ($event && $event['title'] === 'Indonesia') ? 'selected' : '' ?>>Indonesia</option>
                  <option value="Vietnam" <?= ($event && $event['title'] === 'Vietnam') ? 'selected' : '' ?>>Vietnam</option>
                  <option value="Australia" <?= ($event && $event['title'] === 'Australia') ? 'selected' : '' ?>>Australia</option>
                  <option value="Germany" <?= ($event && $event['title'] === 'Germany') ? 'selected' : '' ?>>Germany</option>
                  <option value="United Kingdom" <?= ($event && $event['title'] === 'United Kingdom') ? 'selected' : '' ?>>United Kingdom</option>
                  <option value="Canada" <?= ($event && $event['title'] === 'Canada') ? 'selected' : '' ?>>Canada</option>
              </optgroup>
              <optgroup label="National (India)">
                  <option value="Tamil Nadu" <?= ($event && $event['title'] === 'Tamil Nadu') ? 'selected' : '' ?>>Tamil Nadu</option>
                  <option value="Pune" <?= ($event && ($event['title'] === 'Pune' || stripos($event['title'], 'Pune') !== false)) ? 'selected' : '' ?>>Pune</option>
                  <option value="Maharashtra" <?= ($event && $event['title'] === 'Maharashtra') ? 'selected' : '' ?>>Maharashtra</option>
                  <option value="Karnataka" <?= ($event && $event['title'] === 'Karnataka') ? 'selected' : '' ?>>Karnataka</option>
                  <option value="Delhi" <?= ($event && $event['title'] === 'Delhi') ? 'selected' : '' ?>>Delhi</option>
                  <option value="Goa" <?= ($event && $event['title'] === 'Goa') ? 'selected' : '' ?>>Goa</option>
                  <option value="Kerala" <?= ($event && $event['title'] === 'Kerala') ? 'selected' : '' ?>>Kerala</option>
                  <option value="Rajasthan" <?= ($event && $event['title'] === 'Rajasthan') ? 'selected' : '' ?>>Rajasthan</option>
                  <option value="Gujarat" <?= ($event && $event['title'] === 'Gujarat') ? 'selected' : '' ?>>Gujarat</option>
              </optgroup>
          </select>
          <?php if($event && stripos($event['title'], 'Malaysia') === false && stripos($event['title'], 'Pune') === false && !in_array($event['title'], ['India','Singapore','Switzerland','UAE','Thailand','USA - Las Vegas','USA - New York','Indonesia','Vietnam','Australia','Germany','United Kingdom','Canada','Tamil Nadu','Maharashtra','Karnataka','Delhi','Goa','Kerala','Rajasthan','Gujarat'])): ?>
            <script>
                document.getElementById('eventTitleSelect').insertAdjacentHTML('beforeend', '<option value="<?= htmlspecialchars($event['title']) ?>" selected><?= htmlspecialchars($event['title']) ?></option>');
            </script>
          <?php endif; ?>
      </div>
      
      <script>
        function updateSlug() {
            const title = document.getElementById('eventTitleSelect').value;
            if(title) {
                let locationSlug = title.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
                // Special edge cases from the carousel like USA - Las Vegas should ideally become usa-las-vegas
                document.querySelector('input[name="slug"]').value = 'gsa-' + locationSlug + '-2026';
            }
        }
      </script>
      
      <div class="form-group">
          <label>URL Slug (e.g. gsa-pune-2026)</label>
          <input type="text" name="slug" required value="<?= $event ? htmlspecialchars($event['slug'] ?? '') : '' ?>">
      </div>

      <div class="form-group">
          <label>Status</label>
          <select name="status">
              <option value="draft" <?= ($event && $event['status'] === 'draft') ? 'selected' : '' ?>>Draft</option>
              <option value="active" <?= ($event && $event['status'] === 'active') ? 'selected' : '' ?>>Active</option>
              <option value="completed" <?= ($event && $event['status'] === 'completed') ? 'selected' : '' ?>>Completed</option>
          </select>
      </div>

      <div class="form-group">
          <label>Connect to Home Event Card</label>
          <select name="home_event_card_id">
              <option value="">-- Do not connect / None --</option>
              <?php foreach ($homeCards as $card): ?>
                  <option value="<?= $card['id'] ?>"><?= htmlspecialchars($card['event_title']) ?></option>
              <?php endforeach; ?>
          </select>
          <small class="text-muted" style="display:block; margin-top:5px;">Selecting a card here will automatically update its link on the home page to point to this event.</small>
      </div>
      
      <div class="form-group">
          <label>Hero Banner (Upload Image)</label>
          <input type="hidden" name="hero_banner_url" value="<?= $event ? htmlspecialchars($event['hero_banner_url'] ?? '') : '' ?>">
          <input type="file" name="hero_banner_file" accept="image/*" style="margin-top: 10px;">
      </div>
      
      <input type="hidden" name="logo_url" value="<?= $event ? htmlspecialchars($event['logo_url'] ?? '') : '' ?>">

      <div class="form-group">
          <label>Start Date</label>
          <input type="date" name="start_date" required value="<?= $event ? htmlspecialchars($event['start_date'] ?? '') : '' ?>">
      </div>

      <div class="form-group">
          <label>Timer Start Date / Today's Date</label>
          <p style="font-size: 12px; color: #666; margin-top: -5px; margin-bottom: 5px;">If set, the countdown timer will count down exactly as if today is this date.</p>
          <input type="date" name="timer_start_date" value="<?= $event ? htmlspecialchars($event['timer_start_date'] ?? '') : '' ?>">
      </div>

      <div class="form-group">
          <label>Location (e.g. Shree Shiv Chhatrapati Sports Complex, Pune)</label>
          <input type="text" name="location" value="<?= $event ? htmlspecialchars($event['location'] ?? '') : '' ?>">
      </div>
      
      <div class="form-group">
          <label>End Date</label>
          <input type="date" name="end_date" required value="<?= $event ? htmlspecialchars($event['end_date'] ?? '') : '' ?>">
      </div>

      <div class="form-group">
          <label>Description</label>
          <textarea name="description" rows="5"><?= $event ? htmlspecialchars($event['description'] ?? '') : '' ?></textarea>
      </div>

      <div class="form-group">
          <label>Custom HTML / Rich Text Section (Optional)</label>
          <p style="font-size: 0.85rem; color: #9aa0b4; margin-bottom: 8px;">Use this field to paste completely custom layouts, such as the Thailand Festival Concept. This will be rendered directly on the Event Details page below the overview.</p>
          <textarea name="custom_html" rows="10" style="font-family: monospace;"><?= $event ? htmlspecialchars($event['custom_html'] ?? '') : '' ?></textarea>
      </div>

      <div class="dynamic-block">
          <h3>Event-Specific Delegate Pricing (Optional)</h3>
          <p style="font-size: 0.85rem; color: #9aa0b4; margin-bottom: 15px;">Leave blank to use the global delegate fee set in Delegate Settings.</p>
          <div class="form-group" style="display: flex; gap: 15px;">
              <div style="flex: 1;">
                  <label>Delegate Registration Fee</label>
                  <input type="number" step="0.01" name="delegate_fee" value="<?= $event ? htmlspecialchars($event['delegate_fee'] ?? '') : '' ?>" placeholder="e.g. 200.00">
              </div>
              <div style="flex: 1;">
                  <label>Currency</label>
                  <select name="delegate_currency">
                      <option value="">Select Currency...</option>
                      <option value="USD" <?= ($event && ($event['delegate_currency'] ?? '') == 'USD') ? 'selected' : '' ?>>USD ($)</option>
                      <option value="EUR" <?= ($event && ($event['delegate_currency'] ?? '') == 'EUR') ? 'selected' : '' ?>>EUR (€)</option>
                      <option value="GBP" <?= ($event && ($event['delegate_currency'] ?? '') == 'GBP') ? 'selected' : '' ?>>GBP (£)</option>
                      <option value="AED" <?= ($event && ($event['delegate_currency'] ?? '') == 'AED') ? 'selected' : '' ?>>AED</option>
                      <option value="INR" <?= ($event && ($event['delegate_currency'] ?? '') == 'INR') ? 'selected' : '' ?>>INR (₹)</option>
                  </select>
              </div>
          </div>
      </div>
      <!-- Event Schedule Section -->
      <hr>
      <h3>Event Schedule (Dynamic)</h3>
      <p style="color:#666; margin-bottom: 15px;">Add the timeline schedule for this event.</p>
      
      <div id="schedule-container">
          <?php 
          $schedules = [];
          if ($event && !empty($event['schedule_data'])) {
              $schedules = json_decode($event['schedule_data'], true) ?? [];
          }
          ?>
      </div>
      <button type="button" class="btn-gold" style="font-size: 14px; padding: 6px 15px; margin-bottom: 20px;" onclick="addScheduleItem()">+ Add Schedule Item</button>

      <!-- Sports Categories Section -->
      <hr>
      <h3>Sports Categories (Dynamic)</h3>
      <p style="color:#666; margin-bottom: 15px;">Add sports categories for this event (Badminton, Football, etc.).</p>
      
      <div id="sports-container">
          <?php 
          $sports = [];
          if ($event && !empty($event['sports_data'])) {
              $sports = json_decode($event['sports_data'], true) ?? [];
          }
          ?>
      </div>
      <button type="button" class="btn-gold" style="font-size: 14px; padding: 6px 15px; margin-bottom: 20px;" onclick="addSportItem()">+ Add Sport Category</button>

      <!-- Sponsorship Opportunities Section -->
      <hr>
      <h3>Sponsorship Opportunities (Dynamic)</h3>
      <p style="color:#666; margin-bottom: 15px;">Add sponsors for this event.</p>
      
      <div id="sponsors-container">
          <?php 
          $sponsors = [];
          if ($event && !empty($event['sponsors_data'])) {
              $sponsors = json_decode($event['sponsors_data'], true) ?? [];
          }
          ?>
      </div>
      <button type="button" class="btn-gold" style="font-size: 14px; padding: 6px 15px; margin-bottom: 20px;" onclick="addSponsorItem()">+ Add Sponsor</button>

      <!-- Exhibitor Opportunities Section -->
      <hr>
      <h3>Exhibitor Opportunities (Dynamic)</h3>
      <p style="color:#666; margin-bottom: 15px;">Add exhibitor stalls/packages for this event.</p>
      
      <div id="exhibitors-container">
          <?php 
          $exhibitors = [];
          if ($event && !empty($event['exhibitor_data'])) {
              $exhibitors = json_decode($event['exhibitor_data'], true) ?? [];
          }
          ?>
      </div>
      <button type="button" class="btn-gold" style="font-size: 14px; padding: 6px 15px; margin-bottom: 20px;" onclick="addExhibitorItem()">+ Add Exhibitor Package</button>

      <!-- Festival Locations -->
      <hr>
      <h3>Festival Locations</h3>
      <p style="color:#666; margin-bottom: 15px;">Add dynamic locations (e.g., Bangkok, Pattaya) with background images and event points.</p>
      <div id="locations-container">
          <?php 
          $locations = [];
          if ($event && !empty($event['locations_data'])) {
              $locations = json_decode($event['locations_data'], true) ?? [];
          }
          ?>
      </div>
      <button type="button" class="btn-gold" style="font-size: 14px; padding: 6px 15px; margin-bottom: 20px;" onclick="addLocationItem()">+ Add Festival Location</button>

      <!-- Gala Dinner Section -->
      <hr>
      <h3>Award Ceremony & Gala Dinner Details</h3>
      <p style="color:#666; margin-bottom: 15px;">Fields are optional. If left blank, this section won't appear on the event details page.</p>

      <div class="form-group">
          <label>Section Title (e.g. Award Ceremony & Gala Dinner)</label>
          <input type="text" name="gala_title" placeholder="Award Ceremony & Gala Dinner" value="<?= $event ? htmlspecialchars($event['gala_title'] ?? '') : '' ?>">
      </div>

      <div class="form-group">
          <label>Venue (e.g. The Orchid Hotel Pune)</label>
          <input type="text" name="gala_venue" value="<?= $event ? htmlspecialchars($event['gala_venue'] ?? '') : '' ?>">
      </div>

      <div class="form-group">
          <label>Date (e.g. 14 October 2026)</label>
          <input type="text" name="gala_date" value="<?= $event ? htmlspecialchars($event['gala_date'] ?? '') : '' ?>">
      </div>

      <div class="form-group">
          <label>Time (e.g. 7:00 PM - 11:00 PM)</label>
          <input type="text" name="gala_time" value="<?= $event ? htmlspecialchars($event['gala_time'] ?? '') : '' ?>">
      </div>

      <div class="form-group">
          <label>Description (e.g. Join us for an exclusive evening...)</label>
          <textarea name="gala_description" rows="3"><?= $event ? htmlspecialchars($event['gala_description'] ?? '') : '' ?></textarea>
      </div>

      <!-- Gala Passes -->
      <hr>
      <h3>Gala Dinner Passes (Dynamic)</h3>
      <p style="color:#666; margin-bottom: 15px;">Add ticket types/passes for the Gala Dinner. If left empty, the award registration page will use defaults.</p>
      <div id="gala-passes-container">
          <?php 
          $galaPasses = [];
          if ($event && !empty($event['gala_passes_data'])) {
              $galaPasses = json_decode($event['gala_passes_data'], true) ?? [];
          }
          ?>
      </div>
      <button type="button" class="btn-gold" style="font-size: 14px; padding: 6px 15px; margin-bottom: 20px;" onclick="addGalaPass()">+ Add Gala Pass</button>


      <div class="form-group">
          <button type="submit" class="btn-gold">Save Event Data</button>
      </div>
  </form>
  
  <?php if ($eventId): ?>
  <div class="danger-zone" style="margin-top: 40px; padding: 25px; border: 1px solid #dc3545; border-radius: 8px; background: rgba(220, 53, 69, 0.05);">
      <h3 style="color: #dc3545; margin-bottom: 10px; font-weight: 600;"><i class="fas fa-exclamation-triangle"></i> Danger Zone</h3>
      <p style="color: #f8d7da; margin-bottom: 20px; font-size: 14px;">Once you delete an event, there is no going back. Please be certain.</p>
      <button type="button" class="btn" style="background: #dc3545; color: white; border: none; padding: 10px 20px; font-weight: bold; border-radius: 5px; cursor: pointer; transition: background 0.3s;" onmouseover="this.style.background='#c82333'" onmouseout="this.style.background='#dc3545'" onclick="confirmDelete(<?= $eventId ?>)">
          <i class="fas fa-trash-alt"></i> Delete Event
      </button>
  </div>

  <script>
  function confirmDelete(eventId) {
      let pwd = prompt("Security Check: Please enter the admin password to delete this event.");
      const requiredPwd = <?= json_encode($deleteEventPassword) ?>;
      if (pwd === requiredPwd) {
          window.location.href = "?delete=" + eventId;
      } else if (pwd !== null && pwd !== "") {
          alert("Incorrect password! Event deletion cancelled.");
      }
  }
  </script>
  <?php endif; ?>

</div>

<script>
    // Initial exhibitor data loaded from DB
    const existingExhibitors = <?= json_encode($exhibitors) ?>;
    const exhibitorsContainer = document.getElementById('exhibitors-container');

    function createExhibitorBlock(title = '', icon = 'fa-store', size = '', desc = '', price = '', badge = '', currency = 'INR') {
        title = String(title ?? ''); icon = String(icon ?? 'fa-store'); size = String(size ?? ''); desc = String(desc ?? ''); price = String(price ?? ''); badge = String(badge ?? ''); currency = String(currency ?? 'INR');
        const div = document.createElement('div');
        div.className = 'dynamic-block';
        
        div.innerHTML = `
            <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
                <strong style="color:#c5a85c; font-size: 16px;">Exhibitor Package</strong>
                <button type="button" style="background: transparent; border: 1px solid #dc3545; color: #dc3545; padding: 4px 10px; border-radius: 4px; font-size: 12px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#dc3545'; this.style.color='#fff';" onmouseout="this.style.background='transparent'; this.style.color='#dc3545';" onclick="this.parentElement.parentElement.remove()">X Remove</button>
            </div>
            <div style="display:flex; gap:10px; margin-bottom:10px;">
                <div style="flex:1;">
                    <label>Package Title (e.g. Premium Stall)</label>
                    <input type="text" name="exhibitor_title[]" value="${title.replace(/"/g, '&quot;')}" placeholder="e.g. Premium Stall">
                </div>
                <div style="flex:1;">
                    <label>Icon Class (e.g. fa-store)</label>
                    <input type="text" name="exhibitor_icon[]" value="${icon.replace(/"/g, '&quot;')}" placeholder="e.g. fa-store">
                </div>
                <div style="flex:1;">
                    <label>Badge (Optional, e.g. HOT)</label>
                    <input type="text" name="exhibitor_badge[]" value="${badge.replace(/"/g, '&quot;')}" placeholder="e.g. HOT">
                </div>
            </div>
            <div style="display:flex; gap:10px; margin-bottom:10px;">
                <div style="flex:1;">
                    <label>Stall Size (e.g. 6m x 3m)</label>
                    <input type="text" name="exhibitor_size[]" value="${size.replace(/"/g, '&quot;')}" placeholder="e.g. 6m x 3m">
                </div>
                <div style="flex:1; display:flex; gap:10px;">
                    <div style="flex:1;">
                        <label>Currency</label>
                        <select name="exhibitor_currency[]">
                            <option value="INR" ${currency === 'INR' ? 'selected' : ''}>INR (?)</option>
                            <option value="USD" ${currency === 'USD' ? 'selected' : ''}>USD ($)</option>
                        </select>
                    </div>
                    <div style="flex:2;">
                        <label>Price (e.g. 60000)</label>
                        <input type="text" name="exhibitor_price[]" value="${price.replace(/"/g, '&quot;')}" placeholder="e.g. 60000">
                    </div>
                </div>
            </div>
            <div class="form-group" style="margin-bottom:10px;">
                <label>Description (e.g. Booth space in high footfall area)</label>
                <input type="text" name="exhibitor_desc[]" value="${desc.replace(/"/g, '&quot;')}" placeholder="e.g. Booth space in high footfall area">
            </div>
        `;
        return div;
    }

    function addExhibitorItem() {
        exhibitorsContainer.appendChild(createExhibitorBlock());
    }

    if (existingExhibitors.length > 0) {
        existingExhibitors.forEach(e => {
            exhibitorsContainer.appendChild(createExhibitorBlock(e.title, e.icon, e.size, e.desc, e.price, e.badge, e.currency));
        });
    } else {
        // Default blocks for backward compatibility if empty
        exhibitorsContainer.appendChild(createExhibitorBlock('Standard Stall', 'fa-store', '3m x 3m', 'Booth space in general area', '30000', '', 'INR'));
        exhibitorsContainer.appendChild(createExhibitorBlock('Premium Stall', 'fa-store-alt', '6m x 3m', 'Booth space in high footfall area', '60000', 'HOT', 'INR'));
        exhibitorsContainer.appendChild(createExhibitorBlock('Corner Premium', 'fa-city', '6m x 6m', 'Two-side open booth for better visibility', '90000', '', 'INR'));
        exhibitorsContainer.appendChild(createExhibitorBlock('Pavilion Partner', 'fa-building', 'Custom', 'Large space buildout', '2,00,000+', '', 'INR'));
    }

    // Initial locations data loaded from DB
    const existingLocations = <?= json_encode($locations) ?>;
    const locationsContainer = document.getElementById('locations-container');

    function createLocationBlock(name = '', subtitle = '', bg = '', items = '') {
        name = String(name ?? ''); subtitle = String(subtitle ?? ''); bg = String(bg ?? '');
        const div = document.createElement('div');
        div.className = 'dynamic-block';
        
        let safeItems = items;
        if (typeof items === 'string') {
            safeItems = items.replace(/</g, '&lt;').replace(/>/g, '&gt;');
        }
        
        div.innerHTML = `
            <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
                <strong style="color:#c5a85c; font-size: 16px;">Festival Location</strong>
                <button type="button" style="background: transparent; border: 1px solid #dc3545; color: #dc3545; padding: 4px 10px; border-radius: 4px; font-size: 12px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#dc3545'; this.style.color='#fff';" onmouseout="this.style.background='transparent'; this.style.color='#dc3545';" onclick="this.parentElement.parentElement.remove()">X Remove</button>
            </div>
            <div style="display:flex; gap:10px; margin-bottom:10px;">
                <div style="flex:1;">
                    <label>Location Name</label>
                    <input type="text" name="loc_name[]" value="${name.replace(/"/g, '&quot;')}" placeholder="e.g. Bangkok">
                </div>
                <div style="flex:1;">
                    <label>Subtitle / Description</label>
                    <input type="text" name="loc_subtitle[]" value="${subtitle.replace(/"/g, '&quot;')}" placeholder="e.g. A vibrant city...">
                </div>
            </div>
            <div class="form-group" style="margin-bottom:10px;">
                <label>Background Image (URL or File Upload)</label>
                <input type="text" name="loc_bg_existing[]" value="${bg.replace(/"/g, '&quot;')}" placeholder="Existing URL or leave blank to upload new" style="margin-bottom: 5px;">
                <input type="file" name="loc_bg_file[]" accept="image/*">
            </div>
            <div class="form-group" style="margin-bottom:10px;">
                <label>Location Items / Features (One per line)</label>
                <textarea name="loc_items[]" rows="4" placeholder="Opening Ceremony&#10;Sports Tourism Summit&#10;International Expo&#10;Business Conference">${safeItems}</textarea>
            </div>
        `;
        return div;
    }

    function addLocationItem() {
        locationsContainer.appendChild(createLocationBlock());
    }

    if (existingLocations.length > 0) {
        existingLocations.forEach(l => {
            locationsContainer.appendChild(createLocationBlock(l.name, l.subtitle, l.bg, l.items));
        });
    }

    // Initial gala passes data loaded from DB
    const existingGalaPasses = <?= json_encode($galaPasses ?? []) ?>;
    const galaPassesContainer = document.getElementById('gala-passes-container');

    function createGalaPassBlock(title = '', price = '', features = '', currency = 'INR') {
        title = String(title ?? ''); price = String(price ?? ''); currency = String(currency ?? 'INR');
        const div = document.createElement('div');
        div.className = 'dynamic-block';
        
        let safeFeatures = features;
        if (typeof features === 'string') {
            safeFeatures = features.replace(/</g, '&lt;').replace(/>/g, '&gt;');
        }
        
        div.innerHTML = `
            <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
                <strong style="color:#c5a85c; font-size: 16px;">Gala Pass</strong>
                <button type="button" style="background: transparent; border: 1px solid #dc3545; color: #dc3545; padding: 4px 10px; border-radius: 4px; font-size: 12px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#dc3545'; this.style.color='#fff';" onmouseout="this.style.background='transparent'; this.style.color='#dc3545';" onclick="this.parentElement.parentElement.remove()">X Remove</button>
            </div>
            <div style="display:flex; gap:10px; margin-bottom:10px;">
                <div style="flex:1;">
                    <label>Pass Title (e.g. Single Gala Pass)</label>
                    <input type="text" name="gala_pass_title[]" value="${title.replace(/"/g, '&quot;')}" placeholder="e.g. Single Gala Pass" required>
                </div>
                <div style="flex:1; display:flex; gap:10px;">
                    <div style="flex:1;">
                        <label>Currency</label>
                        <select name="gala_pass_currency[]">
                            <option value="INR" ${currency === 'INR' ? 'selected' : ''}>INR (?)</option>
                            <option value="USD" ${currency === 'USD' ? 'selected' : ''}>USD ($)</option>
                        </select>
                    </div>
                    <div style="flex:2;">
                        <label>Price (Eg. 500 USD)</label>
                        <input type="text" name="gala_pass_price[]" value="${price.replace(/"/g, '&quot;')}" placeholder="e.g. 4500" required>
                    </div>
                </div>
            </div>
            <div class="form-group" style="margin-bottom:10px;">
                <label>Pass Features (One per line)</label>
                <textarea name="gala_pass_features[]" rows="3" placeholder="Valid for 1 Person&#10;Award Ceremony Entry&#10;Gala Dinner">${safeFeatures}</textarea>
            </div>
        `;
        return div;
    }

    function addGalaPass() {
        galaPassesContainer.appendChild(createGalaPassBlock());
    }

    if (existingGalaPasses.length > 0) {
        existingGalaPasses.forEach(p => {
            galaPassesContainer.appendChild(createGalaPassBlock(p.title, p.price, p.features, p.currency));
        });
    }

    // Initial sponsors data loaded from DB
    const existingSponsors = <?= json_encode($sponsors) ?>;
    const sponsorsContainer = document.getElementById('sponsors-container');

    function createSponsorBlock(name = '', website = '', img = '', type = '') {
        name = String(name ?? ''); website = String(website ?? ''); img = String(img ?? ''); type = String(type ?? '');
        const div = document.createElement('div');
        div.className = 'dynamic-block';
        
        div.innerHTML = `
            <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
                <strong style="color:#c5a85c; font-size: 16px;">Sponsor</strong>
                <button type="button" style="background: transparent; border: 1px solid #dc3545; color: #dc3545; padding: 4px 10px; border-radius: 4px; font-size: 12px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#dc3545'; this.style.color='#fff';" onmouseout="this.style.background='transparent'; this.style.color='#dc3545';" onclick="this.parentElement.parentElement.remove()">X Remove</button>
            </div>
            <div style="display:flex; gap:10px; margin-bottom:10px;">
                <div style="flex:1;">
                    <label>Sponsor Name</label>
                    <input type="text" name="sponsor_name[]" value="${name.replace(/"/g, '&quot;')}" placeholder="e.g. Nike">
                </div>
                <div style="flex:1;">
                    <label>Sponsorship Type</label>
                    <input type="text" name="sponsor_type[]" value="${type.replace(/"/g, '&quot;')}" placeholder="e.g. Basic, VIP, VVIP">
                </div>
                <div style="flex:1;">
                    <label>Website URL (Optional)</label>
                    <input type="text" name="sponsor_website[]" value="${website.replace(/"/g, '&quot;')}" placeholder="e.g. https://nike.com">
                </div>
            </div>
            <div class="form-group" style="margin-bottom:10px;">
                <label>Image URL (Logo)</label>
                <input type="text" name="sponsor_img[]" value="${img.replace(/"/g, '&quot;')}" placeholder="e.g. uploads/nike.png">
            </div>
        `;
        return div;
    }

    function addSponsorItem() {
        sponsorsContainer.appendChild(createSponsorBlock());
    }

    if (existingSponsors.length > 0) {
        existingSponsors.forEach(s => {
            sponsorsContainer.appendChild(createSponsorBlock(s.name, s.website, s.img, s.type));
        });
    }

    // Initial sports data loaded from DB
    const existingSports = <?= json_encode($sports) ?>;
    const sportsContainer = document.getElementById('sports-container');

    function createSportBlock(title = '', icon = 'fa-table-tennis', image = '', prize = '', badge = 'Popular', cats = '', pInd = '', pPair = '', pTeam = '', currency = 'INR', prize_currency = 'INR') {
        title = String(title ?? ''); icon = String(icon ?? 'fa-table-tennis'); image = String(image ?? ''); prize = String(prize ?? ''); badge = String(badge ?? 'Popular'); cats = String(cats ?? ''); pInd = String(pInd ?? ''); pPair = String(pPair ?? ''); pTeam = String(pTeam ?? ''); currency = String(currency ?? 'INR'); prize_currency = String(prize_currency ?? 'INR');
        const standardIcons = ['fa-table-tennis', 'fa-futbol', 'fa-basketball-ball', 'fa-volleyball-ball', 'fa-baseball-ball', 'fa-running', 'fa-swimmer', 'fa-dumbbell', 'fa-biking', 'fa-medal', 'fa-trophy'];
        const isStandard = standardIcons.includes(icon) || icon === '';

        const div = document.createElement('div');
        div.className = 'dynamic-block';
        
        div.innerHTML = `
            <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
                <strong style="color:#c5a85c; font-size: 16px;">Sport Category</strong>
                <button type="button" style="background: transparent; border: 1px solid #dc3545; color: #dc3545; padding: 4px 10px; border-radius: 4px; font-size: 12px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#dc3545'; this.style.color='#fff';" onmouseout="this.style.background='transparent'; this.style.color='#dc3545';" onclick="this.parentElement.parentElement.remove()">X Remove</button>
            </div>
            <div style="display:flex; gap:10px; margin-bottom:10px;">
                <div style="flex:1;">
                    <label>Sport Title</label>
                    <input type="text" name="sport_title[]" value="${title.replace(/"/g, '&quot;')}" placeholder="e.g. Badminton Championship">
                </div>
                <div style="flex:1;">
                    <label>Sport Icon</label>
                    <select style="margin-bottom: 5px; width: 100%; padding: 5px;" onchange="
                        if(this.value === 'custom') {
                            this.nextElementSibling.style.display = 'block';
                            this.nextElementSibling.value = '';
                            this.nextElementSibling.focus();
                        } else {
                            this.nextElementSibling.style.display = 'none';
                            this.nextElementSibling.value = this.value;
                        }
                    ">
                        <option value="fa-table-tennis" ${icon === 'fa-table-tennis' ? 'selected' : ''}>Table Tennis</option>
                        <option value="fa-futbol" ${icon === 'fa-futbol' ? 'selected' : ''}>Football / Soccer</option>
                        <option value="fa-basketball-ball" ${icon === 'fa-basketball-ball' ? 'selected' : ''}>Basketball</option>
                        <option value="fa-volleyball-ball" ${icon === 'fa-volleyball-ball' ? 'selected' : ''}>Volleyball</option>
                        <option value="fa-baseball-ball" ${icon === 'fa-baseball-ball' ? 'selected' : ''}>Baseball / Cricket</option>
                        <option value="fa-running" ${icon === 'fa-running' ? 'selected' : ''}>Athletics / Running</option>
                        <option value="fa-swimmer" ${icon === 'fa-swimmer' ? 'selected' : ''}>Swimming</option>
                        <option value="fa-dumbbell" ${icon === 'fa-dumbbell' ? 'selected' : ''}>Gym / Fitness</option>
                        <option value="fa-biking" ${icon === 'fa-biking' ? 'selected' : ''}>Cycling</option>
                        <option value="fa-medal" ${icon === 'fa-medal' ? 'selected' : ''}>General Match / Medal</option>
                        <option value="fa-trophy" ${icon === 'fa-trophy' ? 'selected' : ''}>Tournament / Trophy</option>
                        <option value="custom" ${!isStandard ? 'selected' : ''}>Other (Custom Icon Class)</option>
                    </select>
                    <input type="text" name="sport_icon[]" value="${icon.replace(/"/g, '&quot;')}" style="display: ${isStandard ? 'none' : 'block'}; width: 100%; padding: 5px; border: 1px solid #ccc; border-radius: 4px;" placeholder="e.g. fa-golf-ball">
                </div>
            </div>
            <div class="form-group" style="margin-bottom:10px;">
                <label>Sport Image (URL or File Upload) - Replaces icon if provided</label>
                <input type="text" name="sport_image_existing[]" value="${image.replace(/"/g, '&quot;')}" placeholder="Existing URL or leave blank to upload new" style="margin-bottom: 5px;">
                <input type="file" name="sport_image_file[]" accept="image/*">
            </div>
            <div style="display:flex; gap:10px; margin-bottom:10px;">
                <div style="flex:1; display:flex; gap:10px;">
                    <div style="flex:1;">
                        <label>Currency</label>
                        <select name="sport_prize_currency[]" style="width: 100%;">
                            <option value="INR" ${prize_currency === 'INR' ? 'selected' : ''}>INR (?)</option>
                            <option value="USD" ${prize_currency === 'USD' ? 'selected' : ''}>USD ($)</option>
                        </select>
                    </div>
                    <div style="flex:2;">
                        <label>Prize Pool</label>
                        <input type="text" name="sport_prize[]" value="${prize.replace(/"/g, '&quot;')}" placeholder="e.g. 2,50,000">
                    </div>
                </div>
                <div style="flex:1;">
                    <label>Top Badge</label>
                    <select name="sport_badge[]">
                        <option value="Popular" ${badge === 'Popular' ? 'selected' : ''}>Popular</option>
                        <option value="Trending" ${badge === 'Trending' ? 'selected' : ''}>Trending</option>
                        <option value="Premium" ${badge === 'Premium' ? 'selected' : ''}>Premium</option>
                        <option value="Featured" ${badge === 'Featured' ? 'selected' : ''}>Featured</option>
                    </select>
                </div>
            </div>
            <div class="form-group" style="margin-bottom:10px;">
                <label>Categories</label>
                <select multiple style="width:100%; height:150px; padding:5px; border:1px solid #ccc; border-radius:4px;" onchange="
                    const vals = Array.from(this.selectedOptions).map(opt => opt.value);
                    this.nextElementSibling.value = vals.join(', ');
                ">
                    ${ ['U14', 'U16', 'U18', 'Open', 'Doubles', 'Corporate', 'Athlete (Individual)', 'Athlete (Team - Per Person)', 'Visitor (Daily Pass)', 'Visitor (7 Day Pass)', 'Delegate (Summit / Expo)', 'Exhibitor (Booth)', 'Sponsor (Basic)', 'Government Delegate'].map(opt => `
                        <option value="${opt}" ${cats.split(',').map(s=>s.trim()).includes(opt) ? 'selected' : ''}>${opt}</option>
                    `).join('') }
                </select>
                <input type="hidden" name="sport_categories[]" value="${cats.replace(/"/g, '&quot;')}">
                <small style="color:#666;">Hold Ctrl (Windows) or Cmd (Mac) to select multiple options.</small>
            </div>
            <div style="display:flex; gap:10px; margin-bottom:10px;">
                <div style="flex:1;">
                    <label>Currency</label>
                    <select name="sport_currency[]" style="width: 100%;">
                        <option value="INR" ${currency === 'INR' ? 'selected' : ''}>INR (?)</option>
                        <option value="USD" ${currency === 'USD' ? 'selected' : ''}>USD ($)</option>
                    </select>
                </div>
                <div style="flex:1;">
                    <label>Individual Price (,1)</label>
                    <input type="text" name="sport_price_individual[]" value="${pInd.replace(/"/g, '&quot;')}" placeholder="Leave blank if N/A">
                </div>
                <div style="flex:1;">
                    <label>Pair Price (,1)</label>
                    <input type="text" name="sport_price_pair[]" value="${pPair.replace(/"/g, '&quot;')}" placeholder="Leave blank if N/A">
                </div>
                <div style="flex:1;">
                    <label>Team Price (,1)</label>
                    <input type="text" name="sport_price_team[]" value="${pTeam.replace(/"/g, '&quot;')}" placeholder="Leave blank if N/A">
                </div>
            </div>
        `;
        return div;
    }

    function addSportItem() {
        sportsContainer.appendChild(createSportBlock());
    }

    existingSports.forEach(s => {
        sportsContainer.appendChild(createSportBlock(s.title, s.icon, s.image || '', s.prize, s.badge, s.categories, s.price_individual, s.price_pair, s.price_team, s.currency, s.prize_currency));
    });

    // Initial schedule data loaded from DB
    const existingSchedules = <?= json_encode($schedules) ?>;
    const container = document.getElementById('schedule-container');

    function createScheduleBlock(day = '', title = '', time = '', desc = '') {
        day = String(day ?? ''); title = String(title ?? ''); time = String(time ?? ''); desc = String(desc ?? '');
        const div = document.createElement('div');
        div.className = 'dynamic-block';
        
        div.innerHTML = `
            <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
                <strong style="color:#c5a85c; font-size: 16px;">Schedule Item</strong>
                <button type="button" style="background: transparent; border: 1px solid #dc3545; color: #dc3545; padding: 4px 10px; border-radius: 4px; font-size: 12px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#dc3545'; this.style.color='#fff';" onmouseout="this.style.background='transparent'; this.style.color='#dc3545';" onclick="this.parentElement.parentElement.remove()">X Remove</button>
            </div>
            <div class="form-group">
                <label>Day/Date (e.g. Day 1 (6 OCT))</label>
                <input type="text" name="schedule_day[]" value="${day.replace(/"/g, '&quot;')}">
            </div>
            <div class="form-group">
                <label>Event Title (e.g. Opening Ceremony)</label>
                <input type="text" name="schedule_title[]" value="${title.replace(/"/g, '&quot;')}">
            </div>
            <div class="form-group">
                <label>Time (e.g. 6:00 PM - 9:00 PM)</label>
                <input type="text" name="schedule_time[]" value="${time.replace(/"/g, '&quot;')}">
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="schedule_desc[]" rows="2">${desc}</textarea>
            </div>
        `;
        return div;
    }

    function addScheduleItem() {
        container.appendChild(createScheduleBlock());
    }

    // Populate existing
    existingSchedules.forEach(s => {
        container.appendChild(createScheduleBlock(s.day, s.title, s.time, s.description));
    });
</script>


</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
