<?php
$pageTitle = "Registration Pending Approval";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
require_once __DIR__ . '/config/Database.php';

$delegate_id = $_GET['id'] ?? '';
if (empty($delegate_id)) {
    echo "<div style='text-align:center; padding: 5rem;'><h2>Invalid Delegate ID.</h2></div>";
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$db = Database::getConnection();
$stmt = $db->prepare("SELECT * FROM delegates WHERE delegate_id = ?");
$stmt->execute([$delegate_id]);
$delegate = $stmt->fetch();

if (!$delegate) {
    echo "<div style='text-align:center; padding: 5rem;'><h2>Delegate not found.</h2></div>";
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

?>

<link rel="stylesheet" href="assets/css/delegate.css?v=5">

<section class="delegate-section" style="background-color: transparent; min-height: 70vh;">
    <div class="delegate-form-container mx-auto" style="max-width: 700px; text-align: center; padding: 4rem 2rem;">
        <div style="color: #f59e0b; font-size: 4rem; margin-bottom: 1rem;">
            <i class="fa-solid fa-clock"></i>
        </div>
        <h2 class="section-title" style="margin-bottom: 1rem; color: #12131c;">Registration Submitted!</h2>
        <p style="color: #666; font-size: 1.1rem; margin-bottom: 2rem;">
            Thank you, <strong><?php echo htmlspecialchars($delegate['full_name']); ?></strong>. Your delegate registration has been successfully submitted and is currently <strong>Pending Admin Approval</strong>.
        </p>

        <div style="background: #fff; padding: 2rem; border-radius: 8px; border: 1px solid #ddd; margin-bottom: 2rem; text-align: left;">
            <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center;">
                <div>
                    <p style="margin-bottom: 0.5rem; color: #666;">Delegate ID / Registration No.</p>
                    <h3 style="font-size: 2rem; color: #c5a85c; margin-bottom: 1rem; letter-spacing: 2px;">
                        <?php echo htmlspecialchars($delegate_id); ?>
                    </h3>
                    <p style="margin-bottom: 0.5rem; color: #333;"><strong>Email:</strong> <?php echo htmlspecialchars($delegate['email']); ?></p>
                    <p style="margin-bottom: 0.5rem; color: #333;"><strong>Type:</strong> <?php echo htmlspecialchars($delegate['delegate_type']); ?></p>
                    <p style="margin-bottom: 0.5rem; color: #333;"><strong>Status:</strong> <span style="color: #f59e0b; font-weight: bold;"><?php echo htmlspecialchars($delegate['registration_status']); ?></span></p>
                </div>
            </div>
            <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px dashed #ddd; color: #666; font-size: 0.9rem;">
                <p>You will be notified once your registration is approved. Payment will be processed only after approval.</p>
            </div>
        </div>

        <div style="display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
            <a href="user-dashboard.php" class="delegate-btn"><i class="fa-solid fa-user"></i> Go to Dashboard</a>
            <a href="index.php" class="delegate-btn" style="margin-left: 1rem;">Return to Home</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
