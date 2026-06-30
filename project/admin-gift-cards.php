<?php
$pageTitle = "Gift Card Management | Admin Dashboard";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
require_once __DIR__ . '/config/Database.php';

$pdo = Database::getConnection();

$tab = $_GET['tab'] ?? 'active';

// Handle Toggle Status
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $stmt = $pdo->prepare("SELECT status FROM gift_cards WHERE id = ?");
    $stmt->execute([$id]);
    $gc = $stmt->fetch();
    if ($gc) {
        $newStatus = ($gc['status'] == 'active') ? 'inactive' : 'active';
        $pdo->prepare("UPDATE gift_cards SET status = ? WHERE id = ?")->execute([$newStatus, $id]);
    }
    header("Location: admin-gift-cards.php?tab=" . urlencode($tab));
    exit;
}

// Fetch tab data
$giftCards = [];
$purchases = [];
$redemptions = [];

if ($tab === 'active') {
    $stmt = $pdo->query("SELECT * FROM gift_cards ORDER BY price ASC");
    $giftCards = $stmt->fetchAll(PDO::FETCH_ASSOC);
} elseif ($tab === 'purchases') {
    $stmt = $pdo->query("SELECT gco.*, gc.name as card_name FROM gift_card_orders gco LEFT JOIN gift_cards gc ON gco.gift_card_id = gc.id ORDER BY gco.created_at DESC");
    $purchases = $stmt->fetchAll(PDO::FETCH_ASSOC);
} elseif ($tab === 'redemptions') {
    $stmt = $pdo->query("SELECT gcr.*, gco.recipient_name, gco.recipient_email FROM gift_card_redemptions gcr LEFT JOIN gift_card_orders gco ON gcr.gift_code = gco.gift_code ORDER BY gcr.redeemed_at DESC");
    $redemptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<link rel="stylesheet" href="assets/css/AdminDashboard.css?v=8">

<style>
    .gc-management-card {
        background: #fdfbf7;
        border-radius: 12px;
        padding: 30px;
        max-width: 100%;
        margin: 40px auto;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        font-family: 'Inter', sans-serif;
    }
    .gc-title {
        color: #1a1a1a;
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .gc-tabs {
        display: flex;
        gap: 10px;
        margin-bottom: 25px;
        border-bottom: 2px solid #ebdcb7;
        padding-bottom: 10px;
    }
    .gc-tab {
        padding: 8px 20px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        border: 1px solid #d4af37;
        background: transparent;
        color: #bfa35b;
        transition: all 0.3s;
        text-decoration: none;
    }
    .gc-tab:hover {
        background: rgba(197, 168, 92, 0.1);
    }
    .gc-tab.active {
        background: #cba862;
        color: #1a1a1a;
        border-color: #cba862;
    }
    .gc-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    .gc-item {
        border: 1px solid #ebdcb7;
        border-radius: 8px;
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #fdfbf7;
    }
    .gc-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
        flex: 1;
    }
    .gc-name {
        color: #cba862;
        font-weight: 700;
        font-size: 16px;
    }
    .gc-details {
        color: #8c8c8c;
        font-size: 13px;
        line-height: 1.4;
    }
    .gc-actions {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-left: 15px;
    }
    .gc-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }
    .gc-badge.active, .gc-badge.completed, .gc-badge.success {
        background: #e6f7ef;
        color: #198754;
        border: 1px solid #198754;
    }
    .gc-badge.inactive, .gc-badge.failed, .gc-badge.expired {
        background: #fdf2f2;
        color: #dc3545;
        border: 1px solid #dc3545;
    }
    .gc-badge.pending {
        background: #fff9db;
        color: #f08c00;
        border: 1px solid #f08c00;
    }
    .gc-toggle-btn {
        padding: 6px 15px;
        border-radius: 6px;
        border: 1px solid #ebdcb7;
        background: transparent;
        color: #cba862;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.3s;
    }
    .gc-toggle-btn:hover {
        background: #ebdcb7;
        color: #1a1a1a;
    }
</style>

<div class="admin-dashboard px-4 sm:px-8 py-10" style="background: #e9e3d5; min-height: 100vh;">
    <?php require_once __DIR__ . '/includes/admin_navbar.php'; ?>
    
    <div class="gc-management-card">
        <h2 class="gc-title">üéÅ Gift Card Management</h2>
        
        <div class="gc-tabs">
            <a href="?tab=active" class="gc-tab <?= $tab === 'active' ? 'active' : '' ?>">Active Cards</a>
            <a href="?tab=purchases" class="gc-tab <?= $tab === 'purchases' ? 'active' : '' ?>">Purchases</a>
            <a href="?tab=redemptions" class="gc-tab <?= $tab === 'redemptions' ? 'active' : '' ?>">Redemptions</a>
        </div>
        
        <div class="gc-list">
            <?php if ($tab === 'active'): ?>
                <?php if (count($giftCards) > 0): ?>
                    <?php foreach ($giftCards as $card): ?>
                        <div class="gc-item">
                            <div class="gc-info">
                                <div class="gc-name">?? <?= htmlspecialchars($card['name']) ?></div>
                                <div class="gc-details">Price: ‚Çπ<?= number_format($card['price'], 2) ?> ï Validity: <?= htmlspecialchars($card['validity_days']) ?> days</div>
                                <?php if (!empty($card['description'])): ?>
                                    <div class="gc-details" style="font-style: italic; font-size: 12px; margin-top: 2px;"><?= htmlspecialchars($card['description']) ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="gc-actions">
                                <?php if ($card['status'] === 'active'): ?>
                                    <span class="gc-badge active">ACTIVE</span>
                                <?php else: ?>
                                    <span class="gc-badge inactive">INACTIVE</span>
                                <?php endif; ?>
                                <a href="?tab=active&toggle=<?= $card['id'] ?>" class="gc-toggle-btn">Toggle Status</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color:#666; text-align: center; padding: 20px;">No gift cards found.</p>
                <?php endif; ?>

            <?php elseif ($tab === 'purchases'): ?>
                <?php if (count($purchases) > 0): ?>
                    <?php foreach ($purchases as $order): ?>
                        <div class="gc-item">
                            <div class="gc-info">
                                <div class="gc-name">Order: #<?= htmlspecialchars($order['gift_code']) ?></div>
                                <div class="gc-details">
                                    <strong>Card:</strong> <?= htmlspecialchars($order['card_name'] ?? 'N/A') ?> 
                                    ï <strong>Amount Paid:</strong> ?<?= number_format($order['final_amount'], 2) ?><br>
                                    <strong>Recipient:</strong> <?= htmlspecialchars($order['recipient_name']) ?> (<?= htmlspecialchars($order['recipient_email']) ?>)<br>
                                    <strong>Sender:</strong> <?= htmlspecialchars($order['sender_name']) ?> (<?= htmlspecialchars($order['sender_email']) ?>)<br>
                                    <strong>Date Purchased:</strong> <?= date('d M Y, h:i A', strtotime($order['created_at'])) ?>
                                </div>
                            </div>
                            <div class="gc-actions" style="flex-direction: column; align-items: flex-end; gap: 8px;">
                                <span class="gc-badge <?= strtolower($order['payment_status']) ?>"><?= htmlspecialchars($order['payment_status']) ?></span>
                                <span class="gc-details" style="font-size: 11px;">Redeem: <strong><?= strtoupper($order['redeem_status']) ?></strong></span>
                                <span class="gc-details" style="font-size: 11px;">Bal: <strong>?<?= number_format($order['balance'], 2) ?></strong></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color:#666; text-align: center; padding: 20px;">No gift card purchases found.</p>
                <?php endif; ?>

            <?php elseif ($tab === 'redemptions'): ?>
                <?php if (count($redemptions) > 0): ?>
                    <?php foreach ($redemptions as $red): ?>
                        <div class="gc-item">
                            <div class="gc-info">
                                <div class="gc-name">Redemption: #<?= htmlspecialchars($red['gift_code']) ?></div>
                                <div class="gc-details">
                                    <strong>Redeemed by:</strong> <?= htmlspecialchars($red['user_email']) ?><br>
                                    <strong>Recipient name:</strong> <?= htmlspecialchars($red['recipient_name'] ?? 'N/A') ?> 
                                    (<?= htmlspecialchars($red['recipient_email'] ?? 'N/A') ?>)<br>
                                    <strong>Redeemed At:</strong> <?= date('d M Y, h:i A', strtotime($red['redeemed_at'])) ?>
                                </div>
                            </div>
                            <div class="gc-actions">
                                <span class="gc-badge <?= strtolower($red['status']) ?>"><?= htmlspecialchars($red['status'] === 'success' ? 'SUCCESSFUL' : $red['status']) ?></span>
                                <div style="font-weight: bold; color: #198754; font-size: 15px; margin-left: 5px;">- ?<?= number_format($red['redeemed_amount'], 2) ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color:#666; text-align: center; padding: 20px;">No redemptions recorded yet.</p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

