<?php
$pageTitle = "GLOBAL SPORTS ARENA | Manage Blogs";
require_once __DIR__ . '/config/Database.php';

$db = null;
$blogs = [];
try {
    $db = Database::getConnection();
    
    // Handle activate/deactivate
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_status_id'])) {
        $id = intval($_POST['toggle_status_id']);
        $newStatus = $_POST['current_status'] === 'active' ? 'inactive' : 'active';
        $stmt = $db->prepare("UPDATE blogs SET status = ? WHERE id = ?");
        $stmt->execute([$newStatus, $id]);
        header("Location: admin-blogs.php");
        exit;
    }

    $stmt = $db->query("SELECT * FROM blogs ORDER BY id DESC");
    $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = "Error loading blogs: " . $e->getMessage();
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
<link rel="stylesheet" href="assets/css/AdminDashboard.css?v=7">
<div class="admin-dashboard px-4 sm:px-8 py-10" style="background: #0b0c10; color: #f5f6fa; min-height: 100vh;">
  <?php require_once __DIR__ . '/includes/admin_navbar.php'; ?>
  
  <div class="admin-header" style="border-bottom: 1px solid rgba(197, 168, 92, 0.2); padding-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
    <div>
      <h1>📰 Sports Blog</h1>
      <p>Manage the dynamic blog posts on the home page.</p>
    </div>
    <a href="admin-add-blog.php" style="background: linear-gradient(135deg, #c5a85c 0%, #8c7237 100%); color: #0b0c10; border: none; padding: 12px 25px; border-radius: 8px; font-weight: bold; text-decoration: none; display: flex; align-items: center; gap: 8px;">
      ➕ Add New Blog
    </a>
  </div>

  <div class="admin-content" style="display: block; max-width: 100%; margin-top: 30px;">
    <?php if (isset($error)): ?>
        <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid #ef4444; color: #ef4444; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <div class="admin-card" style="width: 100%; border-radius: 20px; overflow-x: auto; background: rgba(255,255,255,0.02); border: 1px solid rgba(197,168,92,0.1);">
      <div class="overflow-x-auto"><table style="width: 100%; border-collapse: collapse;">
        <thead style="background: rgba(197,168,92,0.05); border-bottom: 1px solid rgba(197,168,92,0.2);">
          <tr>
            <th style="padding: 15px 10px; text-align: left; color: #c5a85c; font-size: 0.85rem; text-transform: uppercase;">ID</th>
            <th style="padding: 15px 10px; text-align: left; color: #c5a85c; font-size: 0.85rem; text-transform: uppercase;">Image</th>
            <th style="padding: 15px 10px; text-align: left; color: #c5a85c; font-size: 0.85rem; text-transform: uppercase;">Title</th>
            <th style="padding: 15px 10px; text-align: left; color: #c5a85c; font-size: 0.85rem; text-transform: uppercase;">Category</th>
            <th style="padding: 15px 10px; text-align: left; color: #c5a85c; font-size: 0.85rem; text-transform: uppercase;">Date Published</th>
            <th style="padding: 15px 10px; text-align: left; color: #c5a85c; font-size: 0.85rem; text-transform: uppercase;">Status</th>
            <th style="padding: 15px 10px; text-align: left; color: #c5a85c; font-size: 0.85rem; text-transform: uppercase;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($blogs)): ?>
            <tr>
              <td colspan="7" style="padding: 30px; text-align: center; color: #9aa0b4;">No blogs found.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($blogs as $blog): ?>
              <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                <td style="padding: 15px 10px;"><?php echo $blog['id']; ?></td>
                <td style="padding: 15px 10px;">
                  <img src="<?php echo htmlspecialchars($blog['image']); ?>" alt="Image" style="width: 80px; height: 50px; object-fit: cover; border-radius: 4px;">
                </td>
                <td style="padding: 15px 10px;"><?php echo htmlspecialchars($blog['title']); ?></td>
                <td style="padding: 15px 10px;">
                  <span style="background: rgba(197,168,92,0.1); padding: 4px 8px; border-radius: 4px; color: #c5a85c; font-size: 0.8rem; text-transform: uppercase;">
                    <?php echo htmlspecialchars($blog['category']); ?>
                  </span>
                </td>
                <td style="padding: 15px 10px;"><?php echo htmlspecialchars($blog['date_published']); ?></td>
                <td style="padding: 15px 10px;">
                  <?php if ($blog['status'] === 'active'): ?>
                    <span style="color: #22c55e; font-weight: bold;">Active</span>
                  <?php else: ?>
                    <span style="color: #eab308; font-weight: bold;">Inactive</span>
                  <?php endif; ?>
                </td>
                <td style="padding: 15px 10px; display: flex; gap: 10px; align-items: center;">
                  <a href="admin-edit-blog.php?id=<?php echo $blog['id']; ?>" style="color: #38bdf8; text-decoration: none;">✏️ Edit</a>
                  <a href="admin-delete-blog.php?id=<?php echo $blog['id']; ?>" style="color: #ef4444; text-decoration: none;" onclick="return confirm('Are you sure you want to delete this blog?');">🗑️ Delete</a>
                  <form method="POST" action="admin-blogs.php" style="display:inline; margin:0;">
                    <input type="hidden" name="toggle_status_id" value="<?php echo $blog['id']; ?>">
                    <input type="hidden" name="current_status" value="<?php echo $blog['status']; ?>">
                    <button type="submit" style="background: none; border: none; color: #c5a85c; cursor: pointer; padding: 0; font-size: 0.9rem;">
                      <?php echo $blog['status'] === 'active' ? '🚫 Deactivate' : '✅ Activate'; ?>
                    </button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
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
});
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
