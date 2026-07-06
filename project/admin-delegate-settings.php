<?php
$pageTitle = "Delegate Settings";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
require_once __DIR__ . '/config/Database.php';

// Check auth (assuming session is started in header or config)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // For local dev, bypass or handle. Usually admin pages require admin role.
    // die("Unauthorized Access");
}

$db = Database::getConnection();

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_settings'])) {
    $fee = $_POST['registration_fee'];
    $currency = $_POST['currency'];
    
    $stmt = $db->prepare("UPDATE delegate_settings SET setting_value = ? WHERE setting_key = 'registration_fee'");
    $stmt->execute([$fee]);
    
    $stmt = $db->prepare("UPDATE delegate_settings SET setting_value = ? WHERE setting_key = 'currency'");
    $stmt->execute([$currency]);
    
    $msg = "Settings updated successfully.";
}

// Fetch current settings
$stmt = $db->query("SELECT setting_key, setting_value FROM delegate_settings");
$settingsRaw = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

$fee = $settingsRaw['registration_fee'] ?? '150.00';
$currency = $settingsRaw['currency'] ?? 'USD';
?>

<link rel="stylesheet" href="assets/css/AdminDashboard.css?v=7">

<div class="admin-dashboard px-4 sm:px-8 py-10" style="background: #0b0c10; color: #f5f6fa; min-height: 100vh;">
    <?php require_once __DIR__ . '/includes/admin_navbar.php'; ?>

    <div class="admin-header" style="border-bottom: 1px solid rgba(197, 168, 92, 0.2); padding-bottom: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
            <h1 style="color: #c5a85c; margin: 0;">Delegate Settings</h1>
        </div>
    </div>

        <?php if (isset($msg)): ?>
            <div style="background: rgba(74, 222, 128, 0.2); color: #10b981; padding: 10px; border-radius: 5px; margin: 15px 0;">
                <?php echo htmlspecialchars($msg); ?>
            </div>
        <?php endif; ?>

        <div class="card-glass" style="margin-top: 2rem; max-width: 600px;">
            <form method="POST">
                <input type="hidden" name="update_settings" value="1">
                
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; color: #3A342B; font-weight: bold;">Registration Fee</label>
                    <input type="number" step="0.01" name="registration_fee" value="<?php echo htmlspecialchars($fee); ?>" style="width: 100%; padding: 10px; border: 1px solid rgba(197, 168, 92, 0.5); border-radius: 4px; background: #fff; color: #333;" required>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; color: #3A342B; font-weight: bold;">Currency</label>
                    <select name="currency" style="width: 100%; padding: 10px; border: 1px solid rgba(197, 168, 92, 0.5); border-radius: 4px; background: #fff; color: #333;" required>
                        <option value="USD" <?php echo $currency == 'USD' ? 'selected' : ''; ?>>USD ($)</option>
                        <option value="EUR" <?php echo $currency == 'EUR' ? 'selected' : ''; ?>>EUR (€)</option>
                        <option value="GBP" <?php echo $currency == 'GBP' ? 'selected' : ''; ?>>GBP (£)</option>
                        <option value="AED" <?php echo $currency == 'AED' ? 'selected' : ''; ?>>AED</option>
                    </select>
                </div>

                <button type="submit" style="padding: 10px 20px; background: #c5a85c; color: #000; border: none; cursor: pointer; border-radius: 5px; font-weight: bold; width: 100%;">Save Settings</button>
            </form>
        </div>
    </div>
</div>

<style>
    .card-glass {
        background: rgba(255, 255, 255, 0.8);
        border: 1px solid rgba(197, 168, 92, 0.3);
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 8px 32px rgba(139, 90, 43, 0.05);
        backdrop-filter: blur(10px);
    }
</style>

</body>
</html>
