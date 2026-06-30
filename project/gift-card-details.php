<?php
require_once 'includes/header.php';
require_once 'includes/navbar.php';
require_once 'config/Database.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>window.location.href='gift-cards.php';</script>";
    exit;
}

$id = intval($_GET['id']);
$db = (new Database())->getConnection();
$stmt = $db->prepare("SELECT * FROM gift_cards WHERE id = ? AND status = 'active'");
$stmt->execute([$id]);
$card = $stmt->fetch(PDO::FETCH_ASSOC);

$nxlCashbackPercentage = Settings::get('nxl_cashback_percentage', 0.05);
$membershipPlansJson = Settings::get('membership_plans', '{}');

if (!$card) {
    echo "<main class='gc-container w-full max-w-4xl mx-auto px-4 py-10'><h2 style='text-align:center;'>Gift Card not found.</h2></main>";
    require_once 'includes/footer.php';
    exit;
}

$benefits = json_decode($card['benefits'], true);
?>

<link rel="stylesheet" href="assets/css/gift-cards.css">
<script>
    const nxlCashbackPercentage = <?= $nxlCashbackPercentage ?>;
    const membershipPlans = <?php echo $membershipPlansJson ?: '{}'; ?>;
</script>

<main class="gc-container w-full max-w-4xl mx-auto px-4 py-10">
    <div style="max-width: 800px; margin: 0 auto; background: var(--gc-card-bg); border: 1px solid var(--gc-border); border-radius: 16px; padding: 40px; display: flex; gap: 40px; flex-wrap: wrap;">
        
        <div style="flex: 1; min-width: 300px; text-align: center; border: 2px dashed var(--gc-gold); border-radius: 12px; padding: 40px; background: var(--gc-hover-bg); display: flex; flex-direction: column; justify-content: center;">
            <div style="font-size: 5rem; margin-bottom: 20px;">🎁</div>
            <h2 style="color: var(--gc-gold); margin-bottom: 10px;"><?php echo htmlspecialchars($card['name']); ?></h2>
            <div style="font-size: 2.5rem; font-weight: bold;">₹<?php echo number_format($card['price']); ?></div>
        </div>

        <div style="flex: 1; min-width: 300px; display: flex; flex-direction: column; justify-content: center;">
            <h1 style="color: var(--gc-gold); margin-bottom: 15px;"><?php echo htmlspecialchars($card['name']); ?></h1>
            <p style="color: var(--gc-text); opacity: 0.9; margin-bottom: 20px; line-height: 1.6;">
                <?php echo htmlspecialchars($card['description']); ?>
            </p>

            <h4 style="margin-bottom: 10px; color: var(--gc-gold);">Benefits Includes:</h4>
            <ul class="gc-card-benefits" style="margin-bottom: 30px;">
                <?php if (is_array($benefits)): ?>
                    <?php foreach ($benefits as $b): ?>
                        <li style="font-size: 1rem; margin-bottom: 12px;"><?php echo htmlspecialchars($b); ?></li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>

            <div style="font-size: 0.85rem; color: #888; margin-bottom: 30px;">
                * Validity: <?php echo $card['validity_days']; ?> Days from purchase. Non-refundable.
            </div>

            <form action="gift-card-checkout.php" method="POST">
                <input type="hidden" name="card_id" value="<?php echo $card['id']; ?>">
                <button type="submit" class="gc-btn gc-btn-solid" style="width: 100%; padding: 15px; font-size: 1.1rem;">Buy This Gift Card</button>
            </form>
            <div style="margin-top: 15px; text-align: center;">
                <a href="gift-cards.php" style="color: var(--gc-gold); text-decoration: underline; font-size: 0.9rem;">Back to Gift Cards</a>
            </div>
        </div>

    </div>
</main>

<?php require_once 'includes/footer.php'; ?>
<script src="assets/js/gift-cards.js"></script>
