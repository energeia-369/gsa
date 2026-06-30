<?php
$pageTitle = "GLOBAL SPORTS ARENA | Add Team Member";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
require_once __DIR__ . '/config/Database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $qualification = trim($_POST['qualification'] ?? '');
    $role = trim($_POST['role'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $display_order = intval($_POST['display_order'] ?? 0);
    $status = $_POST['status'] ?? 'active';

    if (empty($name) || empty($role)) {
        $error = "Name and Role are required.";
    } elseif (empty($_POST['image_url']) && (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK)) {
        $error = "An image file or Image URL is required.";
    } else {
        $image_url = trim($_POST['image_url'] ?? '');
        
        if (!empty($image_url)) {
            $dbImagePath = $image_url;
            try {
                $db = Database::getConnection();
                
                // Auto-update display order series
                $stmt = $db->prepare("UPDATE team_profiles SET display_order = display_order + 1 WHERE display_order >= ?");
                $stmt->execute([$display_order]);
                
                $stmt = $db->prepare("INSERT INTO team_profiles (name, qualification, role, description, image, status, display_order) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$name, $qualification, $role, $description, $dbImagePath, $status, $display_order]);
                $success = "Team member added successfully!";
            } catch (Exception $e) {
                $error = "Database Error: " . $e->getMessage();
            }
        } else {
            $file = $_FILES['image'];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            $maxSize = 2 * 1024 * 1024; // 2MB

            if (!in_array($file['type'], $allowedTypes)) {
                $error = "Only JPG, PNG, and WEBP formats are allowed.";
            } elseif ($file['size'] > $maxSize) {
                $error = "Image size cannot exceed 2MB.";
            } else {
                $uploadDir = __DIR__ . '/assets/images/team/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = uniqid('team_') . '.' . $ext;
                $destination = $uploadDir . $filename;

                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    $dbImagePath = 'assets/images/team/' . $filename;
                    
                    try {
                        $db = Database::getConnection();
                        
                        // Auto-update display order series
                        $stmt = $db->prepare("UPDATE team_profiles SET display_order = display_order + 1 WHERE display_order >= ?");
                        $stmt->execute([$display_order]);
                        
                        $stmt = $db->prepare("INSERT INTO team_profiles (name, qualification, role, description, image, status, display_order) VALUES (?, ?, ?, ?, ?, ?, ?)");
                        $stmt->execute([$name, $qualification, $role, $description, $dbImagePath, $status, $display_order]);
                        
                        $success = "Team member added successfully!";
                    } catch (Exception $e) {
                        $error = "Database Error: " . $e->getMessage();
                    }
                } else {
                    $error = "Failed to upload the image.";
                }
            }
        }
    }
}
?>
<link rel="stylesheet" href="assets/css/AdminDashboard.css?v=8">
<div class="admin-dashboard px-4 sm:px-8 py-10" style="background: #0b0c10; color: #f5f6fa; min-height: 100vh;">
  <?php require_once __DIR__ . '/includes/admin_navbar.php'; ?>
  
  <div class="admin-header" style="border-bottom: 1px solid rgba(197, 168, 92, 0.2); padding-bottom: 20px;">
    <h1>? Add Team Member</h1>
    <p>Add a new team profile to the About page.</p>
  </div>

  <div class="admin-content" style="display: block; max-width: 100%; margin-top: 30px;">
    <?php if ($error): ?>
        <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid #ef4444; color: #ef4444; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div style="background: rgba(34, 197, 94, 0.1); border: 1px solid #22c55e; color: #22c55e; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <?php echo htmlspecialchars($success); ?> <a href="admin-team-profiles.php" style="color: #fff; text-decoration: underline;">View Team Profiles</a>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="admin-card" style="border-radius: 20px; padding: 30px; display: grid; gap: 20px;">
      
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div>
          <label style="display: block; font-size: 0.9rem; color: #9aa0b4; margin-bottom: 8px;">Name *</label>
          <input type="text" name="name" required style="width: 100%; padding: 12px; border: 1px solid rgba(197,168,92,0.25); border-radius: 8px; background: #0b0c10; color: #fff; box-sizing: border-box;">
        </div>
        <div>
          <label style="display: block; font-size: 0.9rem; color: #9aa0b4; margin-bottom: 8px;">Qualification (e.g. B.Tech)</label>
          <input type="text" name="qualification" style="width: 100%; padding: 12px; border: 1px solid rgba(197,168,92,0.25); border-radius: 8px; background: #0b0c10; color: #fff; box-sizing: border-box;">
        </div>
      </div>

      <div>
        <label style="display: block; font-size: 0.9rem; color: #9aa0b4; margin-bottom: 8px;">Job Role *</label>
        <input type="text" name="role" placeholder="e.g. Software Developer" required style="width: 100%; padding: 12px; border: 1px solid rgba(197,168,92,0.25); border-radius: 8px; background: #0b0c10; color: #fff; box-sizing: border-box;">
      </div>

      <div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
          <div>
            <label style="display: block; font-size: 0.9rem; color: #9aa0b4; margin-bottom: 8px;">Profile Image File</label>
            <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" style="width: 100%; padding: 10px; border: 1px solid rgba(197,168,92,0.25); border-radius: 8px; background: #0b0c10; color: #fff; box-sizing: border-box;">
          </div>
          <div>
            <label style="display: block; font-size: 0.9rem; color: #9aa0b4; margin-bottom: 8px;">OR Image URL</label>
            <input type="url" name="image_url" placeholder="https://example.com/avatar.jpg" style="width: 100%; padding: 12px; border: 1px solid rgba(197,168,92,0.25); border-radius: 8px; background: #0b0c10; color: #fff; box-sizing: border-box;">
          </div>
        </div>
      </div>

      <div>
        <label style="display: block; font-size: 0.9rem; color: #9aa0b4; margin-bottom: 8px;">Description</label>
        <textarea name="description" rows="4" style="width: 100%; padding: 12px; border: 1px solid rgba(197,168,92,0.25); border-radius: 8px; background: #0b0c10; color: #fff; box-sizing: border-box; resize: vertical;"></textarea>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div>
          <label style="display: block; font-size: 0.9rem; color: #9aa0b4; margin-bottom: 8px;">Display Order</label>
          <input type="number" name="display_order" value="0" style="width: 100%; padding: 12px; border: 1px solid rgba(197,168,92,0.25); border-radius: 8px; background: #0b0c10; color: #fff; box-sizing: border-box;">
        </div>
        <div>
          <label style="display: block; font-size: 0.9rem; color: #9aa0b4; margin-bottom: 8px;">Status</label>
          <select name="status" style="width: 100%; padding: 12px; border: 1px solid rgba(197,168,92,0.25); border-radius: 8px; background: #0b0c10; color: #fff; box-sizing: border-box;">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>
      </div>

      <div style="display: flex; gap: 15px; margin-top: 10px;">
        <button type="submit" style="background: linear-gradient(135deg, #c5a85c 0%, #8c7237 100%); color: #0b0c10; border: none; padding: 15px 30px; border-radius: 8px; font-weight: bold; font-size: 1rem; cursor: pointer;">
          Save Team Member
        </button>
        <a href="admin-team-profiles.php" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.15); color: #fff; padding: 15px 30px; border-radius: 8px; text-decoration: none; display: flex; align-items: center; justify-content: center; font-weight: bold;">
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
