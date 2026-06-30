<?php
$pageTitle = "GLOBAL SPORTS ARENA | Manage Events";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

// Auth check via JS (since the app uses localStorage)
// We will output a script to verify auth.
?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const token = localStorage.getItem("token");
        const role = localStorage.getItem("userRole");
        if (!token || role !== "ADMIN") {
            alert("Access Denied: Admin login required!");
            window.location.href = "login.php";
        }
    });
</script>
<?php

require_once 'config/Database.php';
$pdo = Database::getConnection();

// Determine which module to load
$moduleType = isset($_GET['type']) ? $_GET['type'] : 'gsa';

if ($moduleType === 'home_carousel') {
    // Handle home carousel event deletion
    if (isset($_POST['delete_home_event_id'])) {
        $delId = (int)$_POST['delete_home_event_id'];
        $stmt = $pdo->prepare("DELETE FROM home_carousel_events WHERE id = ?");
        $stmt->execute([$delId]);
        echo "<script>window.location.href='admin-events.php?type=home_carousel&msg=deleted';</script>";
        exit();
    }
    // Fetch home carousel events
    $events = $pdo->query("SELECT * FROM home_carousel_events ORDER BY created_at DESC")->fetchAll();
} else {
    // Handle GSA basic event deletion
    if (isset($_POST['delete_event_id'])) {
        $delId = (int)$_POST['delete_event_id'];
        $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
        $stmt->execute([$delId]);
        echo "<script>window.location.href='admin-events.php?msg=deleted';</script>";
        exit();
    }
    // Fetch GSA events
    $events = $pdo->query("SELECT * FROM events ORDER BY created_at DESC")->fetchAll();
}
?>

<link rel="stylesheet" href="assets/css/AdminDashboard.css?v=7">

<style>
    .events-table { width: 100%; border-collapse: collapse; margin-top: 1rem; color: #f5f6fa; }
    .events-table th, .events-table td { padding: 12px; border: 1px solid rgba(197, 168, 92, 0.3); text-align: left; }
    .events-table th { background: rgba(197, 168, 92, 0.1); color: #c5a85c; }
    .btn-gold { background: linear-gradient(135deg, #c5a85c, #f5d87a); color: #000; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: bold; border: none; cursor: pointer; display: inline-block; }
    .btn-danger { background: #ef4444; color: #fff; padding: 6px 12px; border-radius: 4px; border: none; cursor: pointer; }
    .status-badge { padding: 4px 8px; border-radius: 12px; font-size: 0.8rem; font-weight: bold; }
    .status-active { background: #10b981; color: #fff; }
    .status-draft { background: #f59e0b; color: #fff; }
    .status-inactive { background: #6b7280; color: #fff; }
    .header-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
    
    body.light-theme .events-table { color: #3d3d3d; }
</style>

<div class="admin-dashboard px-4 sm:px-8 py-10" style="background: #0b0c10; color: #f5f6fa; min-height: 100vh;">
  <?php require_once __DIR__ . '/includes/admin_navbar.php'; ?>
  
  <!-- Header -->
  <div class="admin-header" style="border-bottom: 1px solid rgba(197, 168, 92, 0.2); padding-bottom: 20px;">
    <div class="header-left">
      <div class="admin-badge" style="background: linear-gradient(135deg, #c5a85c 0%, #8c7237 100%); color: #0b0c10; fontWeight: bold; display: inline-block; padding: 4px 12px; border-radius: 4px; margin-bottom: 10px;">
        🛡️ Administrative Core
      </div>
      <h1>Manage Dynamic Events</h1>
      <p>Create, update, and manage the GSA unified event database and CMS modules</p>
    </div>
  </div>

  <div style="margin-top: 30px; margin-bottom: 60px;">
        
        <!-- MODULE SWITCHER -->
        <div style="margin-bottom: 2rem; background: rgba(197, 168, 92, 0.05); padding: 20px; border-radius: 12px; border: 1px solid rgba(197, 168, 92, 0.2);">
            <h3 style="margin-top: 0; color: #c5a85c; margin-bottom: 15px;">Event Management Type</h3>
            <div class="flex flex-col sm:flex-row gap-4 flex-wrap">
                <label style="cursor: pointer; display: flex; align-items: center; gap: 8px;">
                    <input type="radio" name="moduleType" value="gsa" <?= $moduleType === 'gsa' ? 'checked' : '' ?> onchange="window.location.href='admin-events.php?type=gsa'">
                    <span>GSA Page Carousel Events</span>
                </label>
                <label style="cursor: pointer; display: flex; align-items: center; gap: 8px;">
                    <input type="radio" name="moduleType" value="home_carousel" <?= $moduleType === 'home_carousel' ? 'checked' : '' ?> onchange="window.location.href='admin-events.php?type=home_carousel'">
                    <span>Home Page Carousel Events</span>
                </label>
            </div>
        </div>

        <?php if (isset($_GET['msg'])): ?>
            <?php if ($_GET['msg'] === 'deleted'): ?>
            <div style="background: rgba(16, 185, 129, 0.2); color: #10b981; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                Event successfully deleted.
            </div>
            <?php elseif ($_GET['msg'] === 'updated'): ?>
            <div style="background: rgba(16, 185, 129, 0.2); color: #10b981; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                Event updated successfully!
            </div>
            <?php elseif ($_GET['msg'] === 'created'): ?>
            <div style="background: rgba(16, 185, 129, 0.2); color: #10b981; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                Event created successfully!
            </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($moduleType === 'gsa'): ?>
        
        <div class="header-actions flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <h2>Dynamic Events Database (GSA)</h2>
            <a href="admin-event-edit.php" class="btn-gold"><i class="fas fa-plus"></i> Add New Event</a>
        </div>

        <div class="overflow-x-auto">
        <table class="events-table min-w-full">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Slug</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($events) > 0): ?>
                    <?php foreach ($events as $event): ?>
                        <tr>
                            <td><?= htmlspecialchars($event['id']) ?></td>
                            <td><?= htmlspecialchars($event['title']) ?></td>
                            <td><?= htmlspecialchars($event['slug']) ?></td>
                            <td>
                                <span class="status-badge status-<?= htmlspecialchars($event['status']) ?>">
                                    <?= strtoupper($event['status']) ?>
                                </span>
                            </td>
                            <td><?= date('Y-m-d', strtotime($event['created_at'])) ?></td>
                            <td>
                                <a href="admin-event-edit.php?id=<?= $event['id'] ?>" class="btn-gold" style="padding: 6px 12px; font-size: 0.9rem;">Edit</a>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this event? All associated locations, tournaments, and data will be lost.');">
                                    <input type="hidden" name="delete_event_id" value="<?= $event['id'] ?>">
                                    <button type="submit" class="btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                                <a href="event-details.php?slug=<?= $event['slug'] ?>" class="btn-gold" style="background: #3b82f6; padding: 6px 12px; font-size: 0.9rem; color:#fff;" target="_blank">View</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="text-align:center;">No events found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
        
        <?php else: ?>
        
        <div class="header-actions">
            <h2>Home Carousel Events Database</h2>
            <a href="admin-home-carousel-edit.php" class="btn-gold"><i class="fas fa-plus"></i> Add Home Carousel Event</a>
        </div>

        <table class="events-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Slug</th>
                    <th>Status</th>
                    <th>Dyn. Page</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($events) > 0): ?>
                    <?php foreach ($events as $event): ?>
                        <tr>
                            <td><?= htmlspecialchars($event['id']) ?></td>
                            <td>
                                <?php if (!empty($event['thumbnail'])): ?>
                                    <img src="<?= htmlspecialchars($event['thumbnail']) ?>" alt="img" style="width: 80px; border-radius: 4px;">
                                <?php else: ?>
                                    No Image
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($event['title']) ?></td>
                            <td><?= htmlspecialchars($event['slug']) ?></td>
                            <td>
                                <span class="status-badge status-<?= htmlspecialchars($event['status']) ?>">
                                    <?= strtoupper($event['status']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($event['dynamic_page_enabled'] == 1): ?>
                                    <span style="color: #10b981; font-weight: bold;">YES</span>
                                <?php else: ?>
                                    <span style="color: #6b7280; font-weight: bold;">NO</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="admin-home-carousel-edit.php?id=<?= $event['id'] ?>" class="btn-gold" style="padding: 6px 12px; font-size: 0.9rem;">Edit</a>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this Home Carousel event?');">
                                    <input type="hidden" name="delete_home_event_id" value="<?= $event['id'] ?>">
                                    <button type="submit" class="btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                                <?php if ($event['dynamic_page_enabled'] == 1): ?>
                                    <a href="home-event.php?slug=<?= $event['slug'] ?>" class="btn-gold" style="background: #3b82f6; padding: 6px 12px; font-size: 0.9rem; color:#fff;" target="_blank">View</a>
                                <?php else: ?>
                                    <a href="<?= htmlspecialchars($event['button_link']) ?>" class="btn-gold" style="background: #6b7280; padding: 6px 12px; font-size: 0.9rem; color:#fff;" target="_blank">Static URL</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7" style="text-align:center;">No home carousel events found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <?php endif; ?>
        
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>


