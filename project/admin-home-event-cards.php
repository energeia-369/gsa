<?php
$pageTitle = "GLOBAL SPORTS ARENA | Home Event Cards";
require_once __DIR__ . '/config/Database.php';

$db = null;
$cards = [];
try {
    $db = Database::getConnection();
    
    // Handle activate/deactivate
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_status_id'])) {
        $id = intval($_POST['toggle_status_id']);
        $newStatus = $_POST['current_status'] === 'active' ? 'inactive' : 'active';
        $stmt = $db->prepare("UPDATE home_event_cards SET status = ? WHERE id = ?");
        $stmt->execute([$newStatus, $id]);
        header("Location: admin-home-event-cards.php");
        exit;
    }

    $stmt = $db->query("SELECT * FROM home_event_cards ORDER BY id DESC");
    $cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = "Error loading cards: " . $e->getMessage();
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
<link rel="stylesheet" href="assets/css/AdminDashboard.css?v=7">
<div class="admin-dashboard px-4 sm:px-8 py-10" style="background: #0b0c10; color: #f5f6fa; min-height: 100vh;">
  <?php require_once __DIR__ . '/includes/admin_navbar.php'; ?>
  
  <div class="admin-header" style="border-bottom: 1px solid rgba(197, 168, 92, 0.2); padding-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
    <div>
      <h1><i class="fas fa-list-alt"></i> Events Cards</h1>
      <p>Manage the dynamic carousel event cards on the home page.</p>
    </div>
    <a href="admin-add-home-event-card.php" style="background: linear-gradient(135deg, #c5a85c 0%, #8c7237 100%); color: #0b0c10; padding: 12px 25px; border-radius: 8px; font-weight: bold; text-decoration: none;"><i class="fas fa-plus"></i> Add New Card</a>
  </div>

  <div class="admin-content" style="display: block; margin-top: 30px;">
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    
    <div class="admin-card" style="border-radius: 20px; padding: 25px; overflow-x: auto;">
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
            <tr>
              <td colspan="8" style="text-align: center; padding: 20px; color: #9aa0b4;">No cards found.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($cards as $card): ?>
              <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
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
                  <a href="admin-edit-home-event-card.php?id=<?php echo $card['id']; ?>" style="color: #38bdf8; text-decoration: none;">✏️ Edit</a>
                  <a href="admin-delete-home-event-card.php?id=<?php echo $card['id']; ?>" style="color: #ef4444; text-decoration: none;" onclick="return confirm('Are you sure you want to delete this card?');">🗑️ Delete</a>
                  <form method="POST" action="admin-home-event-cards.php" style="display:inline; margin:0;">
                    <input type="hidden" name="toggle_status_id" value="<?php echo $card['id']; ?>">
                    <input type="hidden" name="current_status" value="<?php echo $card['status']; ?>">
                    <button type="submit" style="background: none; border: none; color: #c5a85c; cursor: pointer; padding: 0; font-size: 0.9rem;">
                      <?php echo $card['status'] === 'active' ? '🚫 Deactivate' : '✅ Activate'; ?>
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
