<?php
$pageTitle = "GLOBAL SPORTS ARENA | Edit Blog";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
require_once __DIR__ . '/config/Database.php';

$error = '';
$success = '';
$blog = null;
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$id) {
    header("Location: admin-blogs.php");
    exit;
}

try {
    $db = Database::getConnection();
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = trim($_POST['title'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $date_published = trim($_POST['date_published'] ?? '');
        $excerpt = trim($_POST['excerpt'] ?? '');
        $link = trim($_POST['link'] ?? '');
        $status = $_POST['status'] ?? 'active';

        if (empty($title) || empty($category) || empty($date_published) || empty($excerpt)) {
            $error = "Title, Category, Date, and Excerpt are required.";
        } else {
            $dbImagePath = null;

            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['image'];
                $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
                $maxSize = 2 * 1024 * 1024; // 2MB

                if (!in_array($file['type'], $allowedTypes)) {
                    $error = "Only JPG, PNG, and WEBP formats are allowed.";
                } elseif ($file['size'] > $maxSize) {
                    $error = "Image size cannot exceed 2MB.";
                } else {
                    $uploadDir = __DIR__ . '/assets/images/blogs/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $filename = uniqid('blog_') . '.' . $ext;
                    $destination = $uploadDir . $filename;

                    if (move_uploaded_file($file['tmp_name'], $destination)) {
                        $dbImagePath = 'assets/images/blogs/' . $filename;
                    } else {
                        $error = "Failed to upload the new image.";
                    }
                }
            }

            if (empty($error)) {
                if ($dbImagePath) {
                    $stmt = $db->prepare("UPDATE blogs SET category = ?, title = ?, excerpt = ?, image = ?, link = ?, date_published = ?, status = ? WHERE id = ?");
                    $stmt->execute([$category, $title, $excerpt, $dbImagePath, $link, $date_published, $status, $id]);
                } else {
                    $stmt = $db->prepare("UPDATE blogs SET category = ?, title = ?, excerpt = ?, link = ?, date_published = ?, status = ? WHERE id = ?");
                    $stmt->execute([$category, $title, $excerpt, $link, $date_published, $status, $id]);
                }
                $success = "Blog updated successfully!";
            }
        }
    }

    $stmt = $db->prepare("SELECT * FROM blogs WHERE id = ?");
    $stmt->execute([$id]);
    $blog = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$blog) {
        header("Location: admin-blogs.php");
        exit;
    }

} catch (Exception $e) {
    $error = "Error: " . $e->getMessage();
}
?>
<link rel="stylesheet" href="assets/css/AdminDashboard.css?v=7">
<div class="admin-dashboard px-4 sm:px-8 py-10" style="background: #0b0c10; color: #f5f6fa; min-height: 100vh;">
  <?php require_once __DIR__ . '/includes/admin_navbar.php'; ?>
  
  <div class="admin-header" style="border-bottom: 1px solid rgba(197, 168, 92, 0.2); padding-bottom: 20px;">
    <h1>?? Edit Blog Post</h1>
    <p>Update the dynamic blog article details.</p>
  </div>

  <div class="admin-content" style="max-width: 100%; margin-top: 30px;">
    <?php if ($error): ?>
        <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid #ef4444; color: #ef4444; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div style="background: rgba(34, 197, 94, 0.1); border: 1px solid #22c55e; color: #22c55e; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <?php echo htmlspecialchars($success); ?> <a href="admin-blogs.php" style="color: #fff; text-decoration: underline;">View Blogs</a>
        </div>
    <?php endif; ?>

    <?php if ($blog): ?>
    <form method="POST" enctype="multipart/form-data" class="admin-card" style="border-radius: 20px; padding: 30px; display: grid; gap: 20px;">
      
      <div>
        <label style="display: block; font-size: 0.9rem; color: #9aa0b4; margin-bottom: 8px;">Blog Title *</label>
        <input type="text" name="title" value="<?php echo htmlspecialchars($blog['title']); ?>" required style="width: 100%; padding: 12px; border: 1px solid rgba(197,168,92,0.25); border-radius: 8px; background: #0b0c10; color: #fff; box-sizing: border-box;">
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div>
          <label style="display: block; font-size: 0.9rem; color: #9aa0b4; margin-bottom: 8px;">Category *</label>
          <input type="text" name="category" value="<?php echo htmlspecialchars($blog['category']); ?>" required style="width: 100%; padding: 12px; border: 1px solid rgba(197,168,92,0.25); border-radius: 8px; background: #0b0c10; color: #fff; box-sizing: border-box;">
        </div>
        <div>
          <label style="display: block; font-size: 0.9rem; color: #9aa0b4; margin-bottom: 8px;">Date Published *</label>
          <input type="text" name="date_published" value="<?php echo htmlspecialchars($blog['date_published']); ?>" required style="width: 100%; padding: 12px; border: 1px solid rgba(197,168,92,0.25); border-radius: 8px; background: #0b0c10; color: #fff; box-sizing: border-box;">
        </div>
      </div>

      <div>
        <label style="display: block; font-size: 0.9rem; color: #9aa0b4; margin-bottom: 8px;">Current Image</label>
        <img src="<?php echo htmlspecialchars($blog['image']); ?>" alt="Current Image" style="max-height: 100px; border-radius: 8px; margin-bottom: 10px; display: block;">
        <label style="display: block; font-size: 0.9rem; color: #9aa0b4; margin-bottom: 8px;">Upload New Image (Optional, Max 2MB)</label>
        <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" style="width: 100%; padding: 10px; border: 1px solid rgba(197,168,92,0.25); border-radius: 8px; background: #0b0c10; color: #fff; box-sizing: border-box;">
      </div>

      <div>
        <label style="display: block; font-size: 0.9rem; color: #9aa0b4; margin-bottom: 8px;">Excerpt (Short Description) *</label>
        <textarea name="excerpt" rows="3" required style="width: 100%; padding: 12px; border: 1px solid rgba(197,168,92,0.25); border-radius: 8px; background: #0b0c10; color: #fff; box-sizing: border-box; resize: vertical;"><?php echo htmlspecialchars($blog['excerpt']); ?></textarea>
      </div>

      <div>
        <label style="display: block; font-size: 0.9rem; color: #9aa0b4; margin-bottom: 8px;">Web Link / Article Link</label>
        <input type="text" name="link" value="<?php echo htmlspecialchars($blog['link'] ?? ''); ?>" style="width: 100%; padding: 12px; border: 1px solid rgba(197,168,92,0.25); border-radius: 8px; background: #0b0c10; color: #fff; box-sizing: border-box;">
      </div>

      <div>
        <label style="display: block; font-size: 0.9rem; color: #9aa0b4; margin-bottom: 8px;">Status</label>
        <select name="status" style="width: 100%; padding: 12px; border: 1px solid rgba(197,168,92,0.25); border-radius: 8px; background: #0b0c10; color: #fff; box-sizing: border-box;">
          <option value="active" <?php echo $blog['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
          <option value="inactive" <?php echo $blog['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
        </select>
      </div>

      <div style="display: flex; gap: 15px; margin-top: 10px;">
        <button type="submit" style="background: linear-gradient(135deg, #c5a85c 0%, #8c7237 100%); color: #0b0c10; border: none; padding: 15px 30px; border-radius: 8px; font-weight: bold; font-size: 1rem; cursor: pointer;">
          Update Blog
        </button>
        <a href="admin-delete-blog.php?id=<?php echo $id; ?>" onclick="return confirm('Are you sure you want to delete this blog?');" style="background: rgba(220,38,38,0.12); border: 1px solid #dc2626; color: #f87171; padding: 15px 30px; border-radius: 8px; text-decoration: none; display: flex; align-items: center; justify-content: center; font-weight: bold;">
          Delete Blog
        </a>
        <a href="admin-blogs.php" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.15); color: #fff; padding: 15px 30px; border-radius: 8px; text-decoration: none; display: flex; align-items: center; justify-content: center; font-weight: bold;">
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
