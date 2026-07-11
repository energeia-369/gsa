<?php
$pageTitle = "Delegate Management";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/config/Database.php';

// Check auth (assuming session is started in header or config)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // For local dev, bypass or handle. Usually admin pages require admin role.
    // die("Unauthorized Access");
}

$db = Database::getConnection();
require_once __DIR__ . '/includes/navbar.php';

// Handle Actions (Approve/Reject)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && isset($_POST['id'])) {
        $id = $_POST['id'];
        $action = $_POST['action'];
        $status = '';
        if ($action === 'approve') $status = 'Approved';
        if ($action === 'reject') $status = 'Rejected';
        
        if ($status) {
            $stmt = $db->prepare("UPDATE delegates SET registration_status = ? WHERE id = ?");
            $stmt->execute([$status, $id]);
            $msg = "Delegate status updated to $status.";
        }
    }
}

// Fetch Stats
$stats = $db->query("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN payment_status = 'Pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN payment_status = 'Paid' THEN 1 ELSE 0 END) as paid,
    COUNT(DISTINCT country) as countries
FROM delegates")->fetch();

// Fetch Delegates
$stmt = $db->query("SELECT * FROM delegates ORDER BY id DESC");
$delegates = $stmt->fetchAll();

$viewDelegate = null;
if (isset($_GET['view_id'])) {
    $stmt = $db->prepare("SELECT * FROM delegates WHERE id = ?");
    $stmt->execute([$_GET['view_id']]);
    $viewDelegate = $stmt->fetch();
    
    $isPremium = false;
    if ($viewDelegate && !empty($viewDelegate['email'])) {
        $userStmt = $db->prepare("SELECT membership_tier FROM users WHERE email = ?");
        $userStmt->execute([$viewDelegate['email']]);
        $userRow = $userStmt->fetch();
        if ($userRow && !empty($userRow['membership_tier']) && $userRow['membership_tier'] !== 'none') {
            $isPremium = true;
        }
    }
}
?>

<link rel="stylesheet" href="assets/css/AdminDashboard.css?v=9">
<div class="admin-dashboard-container" style="display: flex; background: #0b0c10; color: #f5f6fa; min-height: 100vh;">
    <?php require_once __DIR__ . '/includes/admin_navbar.php'; ?>

    <div class="admin-dashboard" style="flex: 1; padding: 2rem;">
        <div class="admin-header" style="border-bottom: 1px solid rgba(197, 168, 92, 0.2); padding-bottom: 20px; margin-bottom: 20px;">
            <h1 style="color: #c5a85c; margin: 0;">Delegate Management</h1>
        </div>

        <?php if (isset($msg)): ?>
            <div style="background: rgba(74, 222, 128, 0.2); color: #4ade80; padding: 10px; border-radius: 5px; margin: 15px 0;">
                <?php echo htmlspecialchars($msg); ?>
            </div>
        <?php endif; ?>

        <?php if ($viewDelegate): ?>
            <!-- Delegate Details View -->
            <div class="card-glass" style="margin-top: 2rem; <?php echo $isPremium ? 'border: 2px solid #c5a85c !important; background: linear-gradient(135deg, rgba(197, 168, 92, 0.15) 0%, rgba(140, 100, 30, 0.05) 100%) !important; box-shadow: 0 4px 20px rgba(197, 168, 92, 0.3) !important;' : ''; ?>">
                <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                    <h2>Delegate Details: <?php echo htmlspecialchars($viewDelegate['delegate_id']); ?> <span style="font-size: 0.7em; color: #9aa0b4; font-weight: normal;">(Registration Gmail: <?php echo htmlspecialchars($viewDelegate['email']); ?>)</span></h2>
                    <a href="admin-delegates.php" style="color: #c5a85c; text-decoration: none;">&larr; Back to List</a>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                    <div>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($viewDelegate['full_name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($viewDelegate['email']); ?></p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($viewDelegate['phone']); ?></p>
                        <p><strong>Country:</strong> <?php echo htmlspecialchars($viewDelegate['country']); ?></p>
                        <p><strong>Passport:</strong> <?php echo htmlspecialchars($viewDelegate['passport_number']); ?></p>
                    </div>
                    <div>
                        <p><strong>Organization:</strong> <?php echo htmlspecialchars($viewDelegate['organization']); ?></p>
                        <p><strong>Type:</strong> <?php echo htmlspecialchars($viewDelegate['delegate_type']); ?></p>
                        <p><strong>Reg Status:</strong> <?php echo htmlspecialchars($viewDelegate['registration_status']); ?></p>
                        <p><strong>Payment Status:</strong> <?php echo htmlspecialchars($viewDelegate['payment_status']); ?></p>
                        <p><strong>Registered At:</strong> <?php echo htmlspecialchars($viewDelegate['created_at']); ?></p>
                    </div>
                </div>

                <div style="margin-top: 2rem; display: flex; gap: 1rem;">
                    <?php if ($viewDelegate['passport_file']): ?>
                        <button type="button" onclick="window.open('<?php echo htmlspecialchars($viewDelegate['passport_file']); ?>', '_blank')" style="padding: 10px 20px; background: #12131c; color: #f5f6fa; border: 1px solid rgba(197, 168, 92, 0.5); cursor: pointer; border-radius: 5px; font-weight: bold;">View Passport</button>
                    <?php endif; ?>
                    <?php if ($viewDelegate['profile_photo']): ?>
                        <button type="button" onclick="window.open('<?php echo htmlspecialchars($viewDelegate['profile_photo']); ?>', '_blank')" style="padding: 10px 20px; background: #12131c; color: #f5f6fa; border: 1px solid rgba(197, 168, 92, 0.5); cursor: pointer; border-radius: 5px; font-weight: bold;">View Photo</button>
                    <?php endif; ?>
                </div>

                <div style="margin-top: 2rem; border-top: 1px solid #333; padding-top: 1rem;">
                    <form method="POST" style="display: inline-block; margin-right: 1rem;">
                        <input type="hidden" name="id" value="<?php echo $viewDelegate['id']; ?>">
                        <input type="hidden" name="action" value="approve">
                        <button type="submit" <?php echo ($viewDelegate['registration_status'] === 'Approved') ? 'disabled' : ''; ?> style="padding: 10px 20px; background: #c5a85c; color: #000; border: none; border-radius: 5px; font-weight: bold; <?php echo ($viewDelegate['registration_status'] === 'Approved') ? 'opacity: 0.5; cursor: not-allowed;' : 'cursor: pointer;'; ?>">Approve Delegate</button>
                    </form>
                    <form method="POST" style="display: inline-block;">
                        <input type="hidden" name="id" value="<?php echo $viewDelegate['id']; ?>">
                        <input type="hidden" name="action" value="reject">
                        <button type="submit" <?php echo ($viewDelegate['registration_status'] === 'Rejected') ? 'disabled' : ''; ?> style="padding: 10px 20px; background: #ef4444; color: #fff; border: none; border-radius: 5px; font-weight: bold; <?php echo ($viewDelegate['registration_status'] === 'Rejected') ? 'opacity: 0.5; cursor: not-allowed;' : 'cursor: pointer;'; ?>">Reject Delegate</button>
                    </form>
                </div>
            </div>

        <?php else: ?>
            <!-- Dashboard Stats -->
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-top: 2rem;">
                <div class="card-glass" style="text-align: center;">
                    <h3>Total Delegates</h3>
                    <p style="font-size: 2rem; color: #c5a85c;"><?php echo $stats['total']; ?></p>
                </div>
                <div class="card-glass" style="text-align: center;">
                    <h3>Pending Payments</h3>
                    <p style="font-size: 2rem; color: #c5a85c;"><?php echo $stats['pending']; ?></p>
                </div>
                <div class="card-glass" style="text-align: center;">
                    <h3>Paid Registrations</h3>
                    <p style="font-size: 2rem; color: #c5a85c;"><?php echo $stats['paid']; ?></p>
                </div>
                <div class="card-glass" style="text-align: center;">
                    <h3>Countries</h3>
                    <p style="font-size: 2rem; color: #c5a85c;"><?php echo $stats['countries']; ?></p>
                </div>
            </div>

            <!-- Delegates Table -->
            <div class="card-glass" style="margin-top: 2rem; overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 1px solid rgba(197, 168, 92, 0.3);">
                            <th style="padding: 10px; text-align: left; color: #c5a85c;">ID</th>
                            <th style="padding: 10px; text-align: left; color: #c5a85c;">Name</th>
                            <th style="padding: 10px; text-align: left; color: #c5a85c;">Country</th>
                            <th style="padding: 10px; text-align: left; color: #c5a85c;">Type</th>
                            <th style="padding: 10px; text-align: left; color: #c5a85c;">Reg Status</th>
                            <th style="padding: 10px; text-align: left; color: #c5a85c;">Payment</th>
                            <th style="padding: 10px; text-align: left; color: #c5a85c;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($delegates as $del): ?>
                        <tr style="border-bottom: 1px solid rgba(197, 168, 92, 0.15);">
                            <td style="padding: 10px;"><?php echo htmlspecialchars($del['delegate_id']); ?></td>
                            <td style="padding: 10px;"><?php echo htmlspecialchars($del['full_name']); ?></td>
                            <td style="padding: 10px;"><?php echo htmlspecialchars($del['country']); ?></td>
                            <td style="padding: 10px;"><?php echo htmlspecialchars($del['delegate_type']); ?></td>
                            <td style="padding: 10px;">
                                <span style="background: rgba(197, 168, 92, 0.2); padding: 3px 8px; border-radius: 4px; font-size: 0.8rem; color: inherit;"><?php echo htmlspecialchars($del['registration_status']); ?></span>
                            </td>
                            <td style="padding: 10px;"><?php echo htmlspecialchars($del['payment_status']); ?></td>
                            <td style="padding: 10px;">
                                <a href="admin-delegates.php?view_id=<?php echo $del['id']; ?>" style="color: #c5a85c; text-decoration: none; font-size: 0.9rem; font-weight: bold;">View/Manage</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($delegates)): ?>
                        <tr><td colspan="7" style="padding: 20px; text-align: center; color: #999;">No delegates registered yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .admin-dashboard-container { min-height: 100vh; background: #0b0c10; }
    .admin-dashboard { flex: 1; padding: 2rem 5%; }
    .card-glass {
        background: rgba(22, 24, 38, 0.6);
        border: 1px solid rgba(197, 168, 92, 0.15);
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        backdrop-filter: blur(10px);
    }
</style>

</body>
</html>
