<?php
require_once 'includes/header.php';
require_once 'includes/navbar.php';
require_once 'config/Database.php';

$db = (new Database())->getConnection();
$stmt = $db->query("SELECT * FROM gift_cards WHERE status = 'active' ORDER BY price ASC");
$giftCards = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<link rel="stylesheet" href="assets/css/gift-cards.css">

<main class="gc-container">
    <div class="gc-hero">
        <h1>Gift Cards</h1>
        <p>Gift unforgettable sports experiences, event passes, merchandise, and premium rewards to your friends, family, and teammates.</p>
        <div class="gc-hero-actions">
            <a href="#buy" class="gc-btn gc-btn-solid">Buy Gift Card</a>
            <a href="gift-card-redeem.php" class="gc-btn">Redeem Gift Card</a>
        </div>
    </div>

    <div class="gc-grid" id="buy">
        <?php foreach ($giftCards as $card): ?>
            <?php 
                $benefits = json_decode($card['benefits'], true); 
                $icon = '🎁';
                if (stripos($card['name'], 'silver') !== false) $icon = '🥈';
                if (stripos($card['name'], 'gold') !== false) $icon = '🥇';
                if (stripos($card['name'], 'platinum') !== false) $icon = '💎';
            ?>
            <div class="gc-card">
                <?php if (!empty($card['badge'])): ?>
                    <div class="gc-badge"><?php echo htmlspecialchars($card['badge']); ?></div>
                <?php endif; ?>
                
                <div class="gc-card-icon"><?php echo $icon; ?></div>
                <h3 class="gc-card-title"><?php echo htmlspecialchars($card['name']); ?></h3>
                <div class="gc-card-price">₹<?php echo number_format($card['price']); ?></div>
                
                <ul class="gc-card-benefits">
                    <?php if (is_array($benefits)): ?>
                        <?php foreach ($benefits as $benefit): ?>
                            <li><?php echo htmlspecialchars($benefit); ?></li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
                
                <a href="gift-card-details.php?id=<?php echo $card['id']; ?>" class="gc-btn gc-btn-solid" style="width: 100%; display: block; box-sizing: border-box;">Buy Now</a>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
<script src="assets/js/gift-cards.js"></script>
