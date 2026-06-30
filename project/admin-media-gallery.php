<?php
$pageTitle = "GLOBAL SPORTS ARENA | System Operations";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
require_once __DIR__ . '/config/Database.php';

$conn = Database::getConnection();
$message = '';
$error = '';

// Handle form submission for Gallery Photos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && in_array($_POST['action'], ['add_gallery_photo', 'edit_gallery_photo', 'delete_gallery_photo'])) {
    
    if ($_POST['action'] === 'delete_gallery_photo') {
        $id = (int)($_POST['id'] ?? 0);
        try {
            $stmt = $conn->prepare("DELETE FROM gallery_items WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $message = "Gallery photo deleted successfully!";
        } catch (PDOException $e) {
            $error = "Database Error: " . $e->getMessage();
        }
    } else {
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $photoUrl = trim($_POST['photo_url'] ?? '');
        $category = strtolower(trim($_POST['category'] ?? ''));
        if ($category === 'all / general') $category = 'general';
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');

        $imageUrl = $photoUrl;

        // Handle file upload if present
        if (isset($_FILES['gallery_image']) && $_FILES['gallery_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/uploads/gallery/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $fileName = uniqid('gallery_') . '_' . basename($_FILES['gallery_image']['name']);
            $targetFile = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['gallery_image']['tmp_name'], $targetFile)) {
                $imageUrl = 'uploads/gallery/' . $fileName;
            } else {
                $error = "Failed to upload image.";
            }
        }

        // If editing and no new image URL/file provided, keep the old one
        if ($_POST['action'] === 'edit_gallery_photo' && empty($imageUrl)) {
            try {
                $stmt = $conn->prepare("SELECT image_url FROM gallery_items WHERE id = :id");
                $stmt->execute([':id' => $id]);
                $existing = $stmt->fetch();
                if ($existing) {
                    $imageUrl = $existing['image_url'];
                }
            } catch (PDOException $e) {}
        }

        if (empty($error) && !empty($imageUrl)) {
            try {
                if ($_POST['action'] === 'add_gallery_photo') {
                    $stmt = $conn->prepare("INSERT INTO gallery_items (title, subtitle, image_url, category) VALUES (:title, :subtitle, :image_url, :category)");
                    $stmt->execute([
                        ':title' => $title,
                        ':subtitle' => $description,
                        ':image_url' => $imageUrl,
                        ':category' => $category
                    ]);
                    $message = "Gallery photo added successfully!";
                } else {
                    $stmt = $conn->prepare("UPDATE gallery_items SET title = :title, subtitle = :subtitle, image_url = :image_url, category = :category WHERE id = :id");
                    $stmt->execute([
                        ':title' => $title,
                        ':subtitle' => $description,
                        ':image_url' => $imageUrl,
                        ':category' => $category,
                        ':id' => $id
                    ]);
                    $message = "Gallery photo updated successfully!";
                }
            } catch (PDOException $e) {
                $error = "Database Error: " . $e->getMessage();
            }
        } else if (empty($imageUrl)) {
            $error = "Please provide either a Photo URL or Upload an Image.";
        }
    }
}

// Fetch all gallery items
$galleryItems = [];
try {
    $stmt = $conn->query("SELECT * FROM gallery_items ORDER BY id DESC");
    $galleryItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Failed to fetch gallery items: " . $e->getMessage();
}

// Handle form submission for Media Hub
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && in_array($_POST['action'], ['add_media_hub', 'edit_media_hub', 'delete_media_hub'])) {

    if ($_POST['action'] === 'delete_media_hub') {
        $id = (int)($_POST['id'] ?? 0);
        try {
            $stmt = $conn->prepare("DELETE FROM media_hub WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $message = "Media item deleted successfully!";
        } catch (PDOException $e) {
            $error = "Database Error: " . $e->getMessage();
        }
    } else {
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $title = trim($_POST['video_title'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $videoLink = trim($_POST['video_url'] ?? '');
        $tournament = trim($_POST['event'] ?? '');
        $duration = trim($_POST['duration'] ?? '');
        $status = trim($_POST['status'] ?? '');
        $shortDesc = trim($_POST['short_desc'] ?? '');
        $dateTime = trim($_POST['date_time'] ?? '');
        $thumbnail = '';

        // Handle file upload
        if (isset($_FILES['card_image']) && $_FILES['card_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/uploads/media/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $fileName = uniqid('media_') . '_' . basename($_FILES['card_image']['name']);
            $targetFile = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['card_image']['tmp_name'], $targetFile)) {
                $thumbnail = 'uploads/media/' . $fileName;
            } else {
                $error = "Failed to upload thumbnail image.";
            }
        }

        // If editing and no new image URL/file provided, keep the old one
        if ($_POST['action'] === 'edit_media_hub' && empty($thumbnail)) {
            try {
                $stmt = $conn->prepare("SELECT thumbnail FROM media_hub WHERE id = :id");
                $stmt->execute([':id' => $id]);
                $existing = $stmt->fetch();
                if ($existing) {
                    $thumbnail = $existing['thumbnail'];
                }
            } catch (PDOException $e) {}
        }

        if (empty($error)) {
            try {
                if ($_POST['action'] === 'add_media_hub') {
                    $stmt = $conn->prepare("INSERT INTO media_hub (title, category, video_link, thumbnail, tournament_name, duration, status, date_time, short_description) VALUES (:title, :category, :video_link, :thumbnail, :tournament_name, :duration, :status, :date_time, :short_description)");
                    $stmt->execute([
                        ':title' => $title,
                        ':category' => $category,
                        ':video_link' => $videoLink,
                        ':thumbnail' => $thumbnail,
                        ':tournament_name' => $tournament,
                        ':duration' => $duration,
                        ':status' => $status,
                        ':date_time' => $dateTime,
                        ':short_description' => $shortDesc
                    ]);
                    $message = "Media item added successfully!";
                } else {
                    $stmt = $conn->prepare("UPDATE media_hub SET title = :title, category = :category, video_link = :video_link, thumbnail = :thumbnail, tournament_name = :tournament_name, duration = :duration, status = :status, date_time = :date_time, short_description = :short_description WHERE id = :id");
                    $stmt->execute([
                        ':title' => $title,
                        ':category' => $category,
                        ':video_link' => $videoLink,
                        ':thumbnail' => $thumbnail,
                        ':tournament_name' => $tournament,
                        ':duration' => $duration,
                        ':status' => $status,
                        ':date_time' => $dateTime,
                        ':short_description' => $shortDesc,
                        ':id' => $id
                    ]);
                    $message = "Media item updated successfully!";
                }
            } catch (PDOException $e) {
                $error = "Database Error: " . $e->getMessage();
            }
        }
    }
}

// Fetch all media items
$mediaItems = [];
try {
    $stmt = $conn->query("SELECT * FROM media_hub ORDER BY id DESC");
    $mediaItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Failed to fetch media items: " . $e->getMessage();
}
?>

<link rel="stylesheet" href="assets/css/AdminDashboard.css?v=7">

<div class="admin-dashboard px-4 sm:px-8 py-10" style="background: #0b0c10; color: #f5f6fa; min-height: 100vh;">
  <?php require_once __DIR__ . '/includes/admin_navbar.php'; ?>
  <!-- Header -->
  <div class="admin-header" style="border-bottom: 1px solid rgba(197, 168, 92, 0.2); padding-bottom: 20px;">
    <div class="header-left">
      <div class="admin-badge" style="background: linear-gradient(135deg, #c5a85c 0%, #8c7237 100%); color: #0b0c10; fontWeight: bold; display: inline-block; padding: 4px 12px; border-radius: 4px; margin-bottom: 10px;">
        ⚙️ Administrative Core
      </div>
      <h1>System Operations</h1>
      <p>Real-time tournament CRUD controls, NXL ledger wallets adjustments, and synchronized orders listings in MySQL</p>
    </div>
  </div>

  <!-- Loading Overlay -->
  <div id="adminGlobalLoading" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(11,12,16,0.8); z-index: 9999; justify-content: center; align-items: center; color: #c5a85c; font-size: 1.5rem; font-weight: bold;">
    ? Processing request...
  </div>

  <!-- Dynamic KPI Stats Grid -->
  <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 15px; margin-top: 30px;">
    <div class="stat-card" style="background: #12131c; border: 1px solid rgba(197, 168, 92, 0.15); padding: 20px; border-radius: 12px; display: flex; align-items: center; gap: 15px;">
      <div class="stat-icon" style="font-size: 2rem;">📈</div>
      <div class="stat-info">
        <h3 style="font-size: 0.9rem; color: #9aa0b4; margin: 0 0 5px 0;">Live Total Sales</h3>
        <p class="stat-value" id="statTotalSales" style="color: #22c55e; font-size: 1.5rem; font-weight: bold; margin: 0;">?0</p>
        <span class="stat-change" style="font-size: 0.75rem; color: #9aa0b4;">Synchronized DB</span>
      </div>
    </div>

    <div class="stat-card" style="background: #12131c; border: 1px solid rgba(197, 168, 92, 0.15); padding: 20px; border-radius: 12px; display: flex; align-items: center; gap: 15px;">
      <div class="stat-icon" style="font-size: 2rem;">📈</div>
      <div class="stat-info">
        <h3 style="font-size: 0.9rem; color: #9aa0b4; margin: 0 0 5px 0;">Total NXL Issued</h3>
        <p class="stat-value" id="statTotalNxl" style="color: #c5a85c; font-size: 1.5rem; font-weight: bold; margin: 0;">0 Coins</p>
        <span class="stat-change" style="font-size: 0.75rem; color: #9aa0b4;">Loyalty Ledger</span>
      </div>
    </div>

    <div class="stat-card" style="background: #12131c; border: 1px solid rgba(197, 168, 92, 0.15); padding: 20px; border-radius: 12px; display: flex; align-items: center; gap: 15px;">
      <div class="stat-icon" style="font-size: 2rem;">📈</div>
      <div class="stat-info">
        <h3 style="font-size: 0.9rem; color: #9aa0b4; margin: 0 0 5px 0;">Active Customers</h3>
        <p class="stat-value" id="statActiveCustomers" style="font-size: 1.5rem; font-weight: bold; margin: 0;">0 Users</p>
        <span class="stat-change" style="font-size: 0.75rem; color: #9aa0b4;">Logged Profile</span>
      </div>
    </div>

    <div class="stat-card" style="background: #12131c; border: 1px solid rgba(197, 168, 92, 0.15); padding: 20px; border-radius: 12px; display: flex; align-items: center; gap: 15px;">
      <div class="stat-icon" style="font-size: 2rem;">📈</div>
      <div class="stat-info">
        <h3 style="font-size: 0.9rem; color: #9aa0b4; margin: 0 0 5px 0;">Active Merchants</h3>
        <p class="stat-value" id="statMerchants" style="color: #38bdf8; font-size: 1.5rem; font-weight: bold; margin: 0;">0 Merchants</p>
        <span class="stat-change" style="font-size: 0.75rem; color: #9aa0b4;">Registered Partners</span>
      </div>
    </div>
  </div>

  <div class="admin-content" style="display: grid; grid-template-columns: 1fr; gap: 30px; margin-top: 40px;">
    <div style='display:flex; flex-direction:column; gap:30px;'>
      
      <?php if (!empty($message)): ?>
        <div style="background: rgba(34, 197, 94, 0.15); border: 1px solid #22c55e; color: #22c55e; padding: 15px; border-radius: 8px;">
            <?php echo htmlspecialchars($message); ?>
        </div>
      <?php endif; ?>
      <?php if (!empty($error)): ?>
        <div style="background: rgba(239, 68, 68, 0.15); border: 1px solid #ef4444; color: #ef4444; padding: 15px; border-radius: 8px;">
            <?php echo htmlspecialchars($error); ?>
        </div>
      <?php endif; ?>

      <!-- Gallery Photo Upload & Manage Section -->
      <form class="admin-card gallery-form-section" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="add_gallery_photo">
        <h2 style="color: #c5a85c; margin: 0 0 20px 0; font-size: 1.3rem;">?? Manage Gallery Photos</h2>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">
          
          <!-- Left Column: Add / Edit Photo -->
          <div>
            <h4 style="color: #6366f1; margin-bottom: 15px; font-size: 1rem; display: flex; align-items: center; gap: 8px;">
              <span style="font-size: 1.2rem;">+</span> Add / Edit Photo
            </h4>
            
            <div style="display: flex; flex-direction: column; gap: 15px;">
              <div style="display: flex; flex-direction: column; gap: 6px;">
                <label style="color: #9aa0b4; font-size: 0.85rem; font-weight: 600;">Photo URL *</label>
                <input type="url" name="photo_url" placeholder="https://..." style="padding: 10px 12px; border-radius: 6px; background: transparent; border: 1px solid rgba(197, 168, 92, 0.4); color: inherit; outline: none; width: 100%;">
              </div>
              
              <div style="display: flex; flex-direction: column; gap: 6px;">
                <label style="color: #9aa0b4; font-size: 0.85rem; font-weight: 600;">Upload Image (Device)</label>
                <input type="file" name="gallery_image" accept="image/*" style="padding: 8px; border-radius: 6px; background: transparent; border: 1px solid rgba(197, 168, 92, 0.4); color: inherit; outline: none; width: 100%;">
              </div>
              
              <div style="display: flex; flex-direction: column; gap: 6px;">
                <label style="color: #9aa0b4; font-size: 0.85rem; font-weight: 600;">Select Category *</label>
                <select name="category" required style="padding: 10px 12px; border-radius: 6px; background: transparent; border: 1px solid rgba(197, 168, 92, 0.4); color: inherit; outline: none; width: 100%;">
                  <option value="All / General">All / General</option>
                  <option value="Cricket">Cricket</option>
                  <option value="Football">Football</option>
                  <option value="Basketball">Basketball</option>
                  <option value="Tennis">Tennis</option>
                  <option value="Badminton">Badminton</option>
                  <option value="Volleyball">Volleyball</option>
                  <option value="Athletics">Athletics</option>
                  <option value="Winners">Winners</option>
                </select>
              </div>
              
              <div style="display: flex; flex-direction: column; gap: 6px;">
                <label style="color: #9aa0b4; font-size: 0.85rem; font-weight: 600;">Title *</label>
                <input type="text" name="title" placeholder="e.g. Cricket Tournament" required style="padding: 10px 12px; border-radius: 6px; background: transparent; border: 1px solid rgba(197, 168, 92, 0.4); color: inherit; outline: none; width: 100%;">
              </div>
              
              <div style="display: flex; flex-direction: column; gap: 6px;">
                <label style="color: #9aa0b4; font-size: 0.85rem; font-weight: 600;">Subtitle / Description *</label>
                <input type="text" name="description" placeholder="e.g. Pune - 2024" required style="padding: 10px 12px; border-radius: 6px; background: transparent; border: 1px solid rgba(197, 168, 92, 0.4); color: inherit; outline: none; width: 100%;">
              </div>
              
              <button type="submit" style="margin-top: 10px; padding: 12px; border-radius: 6px; background: #967425; color: #fff; font-weight: bold; border: none; cursor: pointer; width: 100%; transition: opacity 0.3s;">
                Add to Gallery
              </button>
            </div>
          </div>
          
          <!-- Right Column: Existing Gallery Photos -->
          <div>
            <h4 style="color: #9aa0b4; margin-bottom: 15px; font-size: 1rem; display: flex; align-items: center; gap: 8px;">
              ?? Existing Gallery Photos
            </h4>
            <div style="display: flex; flex-direction: column; gap: 15px; height: 480px; overflow-y: auto; padding-right: 10px;">
              <?php if (empty($galleryItems)): ?>
                <div style="display: flex; justify-content: center; align-items: center; height: 100%; color: #9aa0b4;">
                  No photos in gallery.
                </div>
              <?php else: ?>
                <?php foreach ($galleryItems as $item): ?>
                  <div style="display: flex; gap: 15px; padding: 15px; border: 1px solid rgba(197, 168, 92, 0.2); border-radius: 8px; background: rgba(255, 255, 255, 0.02); align-items: center; position: relative;">
                    <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="Gallery Image" style="width: 80px; height: 80px; object-fit: cover; border-radius: 6px;">
                    <div style="flex: 1;">
                      <h5 style="margin: 0 0 5px 0; color: #fff; font-size: 1rem;"><?php echo htmlspecialchars($item['title']); ?></h5>
                      <p style="margin: 0 0 5px 0; color: #9aa0b4; font-size: 0.85rem;"><?php echo htmlspecialchars($item['subtitle']); ?></p>
                      <span style="display: inline-block; padding: 2px 8px; background: rgba(197, 168, 92, 0.1); border: 1px solid rgba(197, 168, 92, 0.3); border-radius: 4px; color: #c5a85c; font-size: 0.75rem; text-transform: uppercase;">
                        <?php echo htmlspecialchars($item['category']); ?>
                      </span>
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                      <button type="button" onclick='editGallery(<?php echo json_encode($item, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)' style="padding: 4px 12px; border-radius: 4px; background: transparent; color: #c5a85c; border: 1px solid #c5a85c; font-size: 0.75rem; cursor: pointer;">Edit</button>
                      <form method="POST" style="margin: 0;" onsubmit="return confirm('Are you sure you want to delete this photo?');">
                        <input type="hidden" name="action" value="delete_gallery_photo">
                        <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                        <button type="submit" style="padding: 4px 12px; border-radius: 4px; background: transparent; color: #ff4444; border: 1px solid #ff4444; font-size: 0.75rem; cursor: pointer; width: 100%;">Delete</button>
                      </form>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>
          
        </div>
      </form>
      
      <script>
      function editGallery(item) {
          const form = document.querySelector('.gallery-form-section');
          form.querySelector('input[name="action"]').value = 'edit_gallery_photo';
          
          let idField = document.getElementById('edit_gallery_id');
          if (!idField) {
              idField = document.createElement('input');
              idField.type = 'hidden';
              idField.name = 'id';
              idField.id = 'edit_gallery_id';
              form.appendChild(idField);
          }
          idField.value = item.id;
          
          form.querySelector('input[name="photo_url"]').value = item.image_url;
          form.querySelector('input[name="title"]').value = item.title;
          form.querySelector('input[name="description"]').value = item.subtitle;
          
          let select = form.querySelector('select[name="category"]');
          let catToMatch = item.category.toLowerCase() === 'general' ? 'all / general' : item.category.toLowerCase();
          for (let i = 0; i < select.options.length; i++) {
              if (select.options[i].value.toLowerCase() === catToMatch) {
                  select.selectedIndex = i;
                  break;
              }
          }
          
          form.querySelector('button[type="submit"]').textContent = 'Update Gallery Photo';
          window.scrollTo({ top: form.offsetTop - 50, behavior: 'smooth' });
      }
      </script>
          
      <!-- 📹 MEDIA HUB / VIDEO MANAGER -->
      <form class="admin-card" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="add_media_hub">
        <h2 style="color: #c5a85c; margin: 0 0 20px 0; font-size: 1.3rem;">📹 Media Hub / Video Manager</h2>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
          
          <!-- Left Column -->
          <div style="display: flex; flex-direction: column; gap: 15px;">
            <div style="display: flex; flex-direction: column; gap: 6px;">
              <label style="color: #9aa0b4; font-size: 0.85rem; font-weight: 600;">Video Title *</label>
              <input type="text" name="video_title" placeholder="e.g. 2024 Finals Highlights" required style="padding: 10px 12px; border-radius: 6px; background: transparent; border: 1px solid rgba(197, 168, 92, 0.4); color: inherit; outline: none; width: 100%;">
            </div>
            
            <div style="display: flex; flex-direction: column; gap: 6px;">
              <label style="color: #9aa0b4; font-size: 0.85rem; font-weight: 600;">Video Link / YouTube URL *</label>
              <input type="url" name="video_url" placeholder="https://..." required style="padding: 10px 12px; border-radius: 6px; background: transparent; border: 1px solid rgba(197, 168, 92, 0.4); color: inherit; outline: none; width: 100%;">
            </div>
            
            <div style="display: flex; flex-direction: column; gap: 6px;">
              <label style="color: #9aa0b4; font-size: 0.85rem; font-weight: 600;">Tournament / Event</label>
              <input type="text" name="event" placeholder="e.g. Cricket Tournament" style="padding: 10px 12px; border-radius: 6px; background: transparent; border: 1px solid rgba(197, 168, 92, 0.4); color: inherit; outline: none; width: 100%;">
            </div>
            
            <div style="display: flex; flex-direction: column; gap: 6px;">
              <label style="color: #9aa0b4; font-size: 0.85rem; font-weight: 600;">Duration</label>
              <input type="text" name="duration" placeholder="e.g. 05:30 MIN" style="padding: 10px 12px; border-radius: 6px; background: transparent; border: 1px solid rgba(197, 168, 92, 0.4); color: inherit; outline: none; width: 100%;">
            </div>
            
            <div style="display: flex; flex-direction: column; gap: 6px;">
              <label style="color: #9aa0b4; font-size: 0.85rem; font-weight: 600;">Status</label>
              <select name="status" style="padding: 10px 12px; border-radius: 6px; background: transparent; border: 1px solid rgba(197, 168, 92, 0.4); color: inherit; outline: none; width: 100%;">
                <option value="Published">Published</option>
                <option value="Draft">Draft</option>
              </select>
            </div>
          </div>
          
          <!-- Right Column -->
          <div style="display: flex; flex-direction: column; gap: 15px;">
            <div style="display: flex; flex-direction: column; gap: 6px;">
              <label style="color: #9aa0b4; font-size: 0.85rem; font-weight: 600;">Category *</label>
              <select name="category" required style="padding: 10px 12px; border-radius: 6px; background: transparent; border: 1px solid rgba(197, 168, 92, 0.4); color: inherit; outline: none; width: 100%;">
                <option value="Live Stream">Live Stream</option>
                <option value="Highlights">Highlights</option>
                <option value="Interviews">Interviews</option>
              </select>
            </div>
            
            <div style="display: flex; flex-direction: column; gap: 6px;">
              <label style="color: #9aa0b4; font-size: 0.85rem; font-weight: 600;">Post / Card Image *</label>
              <input type="file" name="card_image" accept="image/*" required style="padding: 8px; border-radius: 6px; background: transparent; border: 1px solid rgba(197, 168, 92, 0.4); color: inherit; outline: none; width: 100%;">
            </div>
            
            <div style="display: flex; flex-direction: column; gap: 6px;">
              <label style="color: #9aa0b4; font-size: 0.85rem; font-weight: 600;">Short Description</label>
              <input type="text" name="short_desc" placeholder="e.g. Pune Matches" style="padding: 10px 12px; border-radius: 6px; background: transparent; border: 1px solid rgba(197, 168, 92, 0.4); color: inherit; outline: none; width: 100%;">
            </div>
            
            <div style="display: flex; flex-direction: column; gap: 6px;">
              <label style="color: #9aa0b4; font-size: 0.85rem; font-weight: 600;">Video GUID</label>
              <input type="text" name="video_guid" placeholder="e.g. v-45" style="padding: 10px 12px; border-radius: 6px; background: transparent; border: 1px solid rgba(197, 168, 92, 0.4); color: inherit; outline: none; width: 100%;">
            </div>
            
            <div style="display: flex; flex-direction: column; gap: 6px;">
              <label style="color: #9aa0b4; font-size: 0.85rem; font-weight: 600;">Date / Time</label>
              <input type="text" name="date_time" placeholder="e.g. 1 day ago or Oct 15 2024" style="padding: 10px 12px; border-radius: 6px; background: transparent; border: 1px solid rgba(197, 168, 92, 0.4); color: inherit; outline: none; width: 100%;">
            </div>
          </div>
          
        </div>
        
        <!-- Full Width -->
        <div style="display: flex; flex-direction: column; gap: 6px; margin-top: 15px;">
          <label style="color: #9aa0b4; font-size: 0.85rem; font-weight: 600;">Post Description</label>
          <input type="text" name="post_description" style="padding: 10px 12px; border-radius: 6px; background: transparent; border: 1px solid rgba(197, 168, 92, 0.4); color: inherit; outline: none; width: 100%;">
        </div>
        
        <button type="submit" style="margin-top: 20px; padding: 12px; border-radius: 6px; background: #967425; color: #fff; font-weight: bold; border: none; cursor: pointer; width: 100%; transition: opacity 0.3s;">
          Add to Media Hub
        </button>
        
        <!-- Table -->
        <div class="orders-table" style="margin-top: 30px; border-top: 1px solid rgba(197,168,92,0.2); padding-top: 20px;">
          <div class="overflow-x-auto"><table style="width: 100%;">
            <thead>
              <tr>
                <th style="text-align: center; color: #c5a85c;">Thumbnail</th>
                <th style="text-align: center; color: #c5a85c;">Title & Category</th>
                <th style="text-align: center; color: #c5a85c;">Details</th>
                <th style="text-align: center; color: #c5a85c;">Visibility</th>
                <th style="text-align: center; color: #c5a85c;">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($mediaItems)): ?>
                <tr>
                  <td colspan="5" style="text-align: center; padding: 20px; color: #9aa0b4;">No media items found.</td>
                </tr>
              <?php else: ?>
                <?php foreach ($mediaItems as $media): ?>
                  <tr>
                    <td style="text-align: center; padding: 15px;">
                      <?php if (!empty($media['thumbnail'])): ?>
                        <img src="<?php echo htmlspecialchars($media['thumbnail']); ?>" alt="Thumbnail" style="width: 80px; height: 50px; object-fit: cover; border-radius: 4px;">
                      <?php else: ?>
                        <div style="width: 80px; height: 50px; background: rgba(255,255,255,0.05); border-radius: 4px; display: flex; align-items: center; justify-content: center; margin: 0 auto; color: #9aa0b4; font-size: 0.75rem;">No Image</div>
                      <?php endif; ?>
                    </td>
                    <td style="text-align: center; padding: 15px;">
                      <div style="font-weight: bold; color: inherit; margin-bottom: 5px;"><?php echo htmlspecialchars($media['title']); ?></div>
                      <span style="display: inline-block; padding: 2px 8px; background: rgba(197, 168, 92, 0.1); border: 1px solid rgba(197, 168, 92, 0.3); border-radius: 4px; color: #c5a85c; font-size: 0.7rem; text-transform: uppercase;">
                        <?php echo htmlspecialchars($media['category']); ?>
                      </span>
                    </td>
                    <td style="text-align: center; padding: 15px; color: #9aa0b4; font-size: 0.85rem;">
                      <div>Duration: <?php echo htmlspecialchars($media['duration'] ?? '-'); ?></div>
                      <div>Date: <?php echo htmlspecialchars($media['date_time'] ?? '-'); ?></div>
                    </td>
                    <td style="text-align: center; padding: 15px; color: #9aa0b4;">
                      <?php echo htmlspecialchars($media['status'] ?? 'Published'); ?>
                    </td>
                    <td style="text-align: center; padding: 15px;">
                      <form method="POST" style="margin: 0; display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this video?');">
                        <input type="hidden" name="action" value="delete_media_hub">
                        <input type="hidden" name="id" value="<?php echo $media['id']; ?>">
                        <button type="button" onclick='editMediaHub(<?php echo json_encode($media, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)' style="padding: 4px 12px; border-radius: 4px; background: transparent; color: #c5a85c; border: 1px solid #c5a85c; font-size: 0.75rem; cursor: pointer; margin-right: 4px;">Edit</button>
                        <button type="submit" style="padding: 4px 12px; border-radius: 4px; background: transparent; color: #ff4444; border: 1px solid #ff4444; font-size: 0.75rem; cursor: pointer;">Delete</button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table></div>
        </div>
      </form>
      
      <script>
      function editMediaHub(media) {
          const form = document.querySelector('form.admin-card:last-of-type');
          form.querySelector('input[name="action"]').value = 'edit_media_hub';
          
          let idField = document.getElementById('edit_media_id');
          if (!idField) {
              idField = document.createElement('input');
              idField.type = 'hidden';
              idField.name = 'id';
              idField.id = 'edit_media_id';
              form.appendChild(idField);
          }
          idField.value = media.id;
          
          form.querySelector('input[name="video_title"]').value = media.title || '';
          form.querySelector('input[name="video_url"]').value = media.video_link || '';
          form.querySelector('input[name="event"]').value = media.tournament_name || '';
          form.querySelector('input[name="duration"]').value = media.duration || '';
          form.querySelector('input[name="short_desc"]').value = media.short_description || '';
          form.querySelector('input[name="date_time"]').value = media.date_time || '';
          
          let catSelect = form.querySelector('select[name="category"]');
          for (let i = 0; i < catSelect.options.length; i++) {
              if (catSelect.options[i].value.toLowerCase() === (media.category || '').toLowerCase()) {
                  catSelect.selectedIndex = i;
                  break;
              }
          }
          
          let statusSelect = form.querySelector('select[name="status"]');
          for (let i = 0; i < statusSelect.options.length; i++) {
              if (statusSelect.options[i].value.toLowerCase() === (media.status || '').toLowerCase()) {
                  statusSelect.selectedIndex = i;
                  break;
              }
          }
          
          form.querySelector('button[type="submit"]').textContent = 'Update Media Hub';
          window.scrollTo({ top: form.offsetTop - 50, behavior: 'smooth' });
      }
      </script>
      
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
