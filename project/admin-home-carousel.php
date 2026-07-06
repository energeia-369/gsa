<?php
$pageTitle = "Carousel Events Management";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

require_once 'config/Database.php';
$pdo = Database::getConnection();

// Handle deletion
if (isset($_POST['delete_event_id'])) {
    $delId = (int)$_POST['delete_event_id'];
    $stmt = $pdo->prepare("DELETE FROM home_carousel_events WHERE id = ?");
    $stmt->execute([$delId]);
    echo "<script>window.location.href='admin-home-carousel.php?msg=deleted';</script>";
    exit();
}

// Handle bulk actions
if (isset($_POST['bulk_action']) && isset($_POST['selected_ids'])) {
    $action = $_POST['bulk_action'];
    $ids = array_map('intval', $_POST['selected_ids']);
    
    if (!empty($ids)) {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        
        if ($action === 'delete') {
            $stmt = $pdo->prepare("DELETE FROM home_carousel_events WHERE id IN ($placeholders)");
            $stmt->execute($ids);
        } elseif ($action === 'publish') {
            $stmt = $pdo->prepare("UPDATE home_carousel_events SET status = 'published' WHERE id IN ($placeholders)");
            $stmt->execute($ids);
        } elseif ($action === 'unpublish') {
            $stmt = $pdo->prepare("UPDATE home_carousel_events SET status = 'draft' WHERE id IN ($placeholders)");
            $stmt->execute($ids);
        }
        echo "<script>window.location.href='admin-home-carousel.php?msg=bulk_updated';</script>";
        exit();
    }
}

// Handle ordering
$orderBy = isset($_GET['order']) ? $_GET['order'] : 'display_order ASC';

// Fetch events
$events = $pdo->query("SELECT * FROM home_carousel_events ORDER BY $orderBy")->fetchAll();
?>

<link rel="stylesheet" href="assets/css/AdminDashboard.css?v=7">

<style>
    .events-table { width: 100%; border-collapse: collapse; margin-top: 1rem; color: #f5f6fa; }
    .events-table th, .events-table td { padding: 12px; border: 1px solid rgba(197, 168, 92, 0.3); text-align: left; }
    .events-table th { background: rgba(197, 168, 92, 0.1); color: #c5a85c; }
    .btn-gold { background: linear-gradient(135deg, #c5a85c, #f5d87a); color: #000; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: bold; border: none; cursor: pointer; display: inline-block; }
    .btn-danger { background: #ef4444; color: #fff; padding: 6px 12px; border-radius: 4px; border: none; cursor: pointer; }
    .btn-outline { background: transparent; border: 1px solid #c5a85c; color: #c5a85c; padding: 6px 12px; border-radius: 4px; cursor: pointer; }
    .status-badge { padding: 4px 8px; border-radius: 12px; font-size: 0.8rem; font-weight: bold; }
    .status-published { background: #10b981; color: #fff; }
    .status-draft { background: #f59e0b; color: #fff; }
    
    body.light-theme .events-table { color: #3d3d3d; }
</style>

<div class="admin-dashboard px-4 sm:px-8 py-10" style="background: #0b0c10; color: #f5f6fa; min-height: 100vh;">
  <?php require_once __DIR__ . '/includes/admin_navbar.php'; ?>
  
  <div class="admin-header" style="border-bottom: 1px solid rgba(197, 168, 92, 0.2); padding-bottom: 20px;">
    <div class="header-left">
      <div class="admin-badge" style="background: linear-gradient(135deg, #c5a85c 0%, #8c7237 100%); color: #0b0c10; fontWeight: bold; display: inline-block; padding: 4px 12px; border-radius: 4px; margin-bottom: 10px;">
        🏠 Carousel Management
      </div>
      <h1>Carousel Events</h1>
      <p>Manage the dynamic cards appearing on the main homepage carousel and GSA carousel.</p>
    </div>
  </div>

  <div style="margin-top: 30px; margin-bottom: 60px;">
        <?php if (isset($_GET['msg'])): ?>
            <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid #10b981; color: #10b981; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                Action completed successfully!
            </div>
        <?php endif; ?>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <div>
                <a href="admin-home-carousel-edit.php" class="btn-gold">+ Add New Event</a>
            </div>
            
            <form method="POST" id="bulkForm" style="display: flex; gap: 10px;">
                <select name="bulk_action" style="padding: 8px; border-radius: 4px; border: 1px solid rgba(197, 168, 92, 0.5); background: #12131c; color: white;">
                    <option value="">Bulk Actions</option>
                    <option value="publish">Publish</option>
                    <option value="unpublish">Unpublish</option>
                    <option value="delete">Delete</option>
                </select>
                <button type="button" class="btn-outline" onclick="submitBulk()">Apply</button>
            </form>
        </div>

        <div style="overflow-x: auto;">
            <table class="events-table">
                <thead>
                    <tr>
                        <th style="width: 40px;"><input type="checkbox" id="selectAll" onclick="toggleAll(this)"></th>
                        <th>Order</th>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($events)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 30px;">
                                No events found. The homepage is currently displaying the static fallback carousel.<br><br>
                                <a href="admin-home-carousel-edit.php" style="color: #c5a85c; text-decoration: underline;">Create one now</a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($events as $evt): ?>
                        <tr>
                            <td><input type="checkbox" class="row-checkbox" value="<?= $evt['id'] ?>"></td>
                            <td><?= $evt['display_order'] ?></td>
                            <td>
                                <?php if ($evt['carousel_img']): ?>
                                    <img src="<?= htmlspecialchars($evt['carousel_img']) ?>" alt="img" style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px;">
                                <?php else: ?>
                                    <div style="width: 60px; height: 40px; background: rgba(255,255,255,0.1); border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 10px;">No Img</div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($evt['title']) ?></strong><br>
                                <small style="color: #888;"><?= htmlspecialchars($evt['subtitle'] ?? '') ?></small>
                            </td>
                            <td>
                                <?php if ($evt['category']): ?>
                                    <span style="background: rgba(197, 168, 92, 0.2); color: #c5a85c; padding: 2px 8px; border-radius: 12px; font-size: 0.8em; border: 1px solid rgba(197,168,92,0.5);">
                                        <?= htmlspecialchars(strtoupper($evt['category'])) ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td><?= $evt['event_date'] ? date('M d, Y', strtotime($evt['event_date'])) : '-' ?></td>
                            <td>
                                <?php if ($evt['status'] === 'published'): ?>
                                    <span class="status-badge status-published">Published</span>
                                <?php else: ?>
                                    <span class="status-badge status-draft">Draft</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="admin-home-carousel-edit.php?id=<?= $evt['id'] ?>" class="btn-outline" style="margin-right: 5px;">Edit</a>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this event? This cannot be undone.');">
                                    <input type="hidden" name="delete_event_id" value="<?= $evt['id'] ?>">
                                    <button type="submit" class="btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
  </div>
</div>

<script>
function toggleAll(source) {
    checkboxes = document.querySelectorAll('.row-checkbox');
    for(var i=0; i<checkboxes.length; i++) {
        checkboxes[i].checked = source.checked;
    }
}

function submitBulk() {
    const action = document.querySelector('select[name="bulk_action"]').value;
    if (!action) {
        alert('Please select an action');
        return;
    }
    
    const checked = document.querySelectorAll('.row-checkbox:checked');
    if (checked.length === 0) {
        alert('Please select at least one row');
        return;
    }
    
    if (action === 'delete' && !confirm('Are you sure you want to delete the selected items?')) {
        return;
    }
    
    const form = document.getElementById('bulkForm');
    checked.forEach(cb => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'selected_ids[]';
        input.value = cb.value;
        form.appendChild(input);
    });
    
    form.submit();
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
