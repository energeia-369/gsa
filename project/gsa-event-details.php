<?php
require_once 'config/Database.php';

$slug = $_GET['slug'] ?? '';
if (empty($slug)) {
    die("Event not found");
}

$pdo = Database::getConnection();
$stmt = $pdo->prepare("SELECT * FROM gsa_carousel_events WHERE slug = ? AND status = 'published'");
$stmt->execute([$slug]);
$event = $stmt->fetch();

if (!$event) {
    die("Sports Event not found or not published.");
}

$pageTitle = htmlspecialchars($event['seo_title'] ?: $event['tournament_name']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <meta name="description" content="<?= htmlspecialchars($event['seo_desc']) ?>">
    <meta name="keywords" content="<?= htmlspecialchars($event['seo_keywords']) ?>">
    <link rel="stylesheet" href="assets/css/Navbar.css">
    <style>
        body { margin: 0; font-family: 'Inter', sans-serif; background-color: #0b0c10; color: #fff; }
        
        .hero-section {
            background-image: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.9)), url('<?= $event['hero_banner'] ? htmlspecialchars($event['hero_banner']) : "assets/images/default_sports_hero.jpg" ?>');
            background-size: cover;
            background-position: center;
            height: 50vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            border-bottom: 5px solid #10b981;
        }
        
        .event-container {
            max-width: 1200px;
            margin: 40px auto 100px;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 40px;
        }

        .category-badge {
            display: inline-block;
            background: #10b981;
            color: #000;
            padding: 5px 15px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 0.9rem;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        .event-title {
            font-size: 3rem;
            margin: 0;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .main-content {
            background: rgba(255,255,255,0.02);
            padding: 30px;
            border-radius: 8px;
            border: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar {
            background: #12131c;
            padding: 30px;
            border-radius: 8px;
            border: 1px solid rgba(16, 185, 129, 0.3);
            height: fit-content;
        }

        .sidebar h3 {
            color: #10b981;
            margin-top: 0;
            border-bottom: 1px solid rgba(16,185,129,0.2);
            padding-bottom: 10px;
        }

        .meta-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .meta-list li {
            padding: 15px 0;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            display: flex;
            flex-direction: column;
        }
        
        .meta-list li:last-child { border-bottom: none; }
        
        .meta-label { color: #888; font-size: 0.85rem; text-transform: uppercase; margin-bottom: 5px; }
        .meta-value { font-size: 1.1rem; color: #fff; font-weight: bold; }

        .content-section {
            margin-bottom: 40px;
        }
        
        .content-section h2 {
            color: #10b981;
            font-size: 1.8rem;
            margin-bottom: 20px;
        }

        .content-body {
            line-height: 1.8;
            font-size: 1.1rem;
            color: #ccc;
        }

        .cta-button {
            display: block;
            background: #10b981;
            color: #000;
            padding: 15px;
            text-align: center;
            border-radius: 6px;
            text-decoration: none;
            font-size: 1.2rem;
            font-weight: bold;
            margin-top: 30px;
            transition: opacity 0.3s;
        }
        .cta-button:hover { opacity: 0.9; }
        
        .cta-disabled {
            background: #555;
            color: #aaa;
            pointer-events: none;
        }

        @media (max-width: 900px) {
            .event-container { grid-template-columns: 1fr; }
        }
    </style>
    <?php
        $schema = [
            "@context" => "https://schema.org",
            "@type" => "SportsEvent",
            "name" => $event['tournament_name'],
            "description" => strip_tags($event['description']),
            "image" => $event['hero_banner'] ? "http://" . $_SERVER['HTTP_HOST'] . "/" . ltrim($event['hero_banner'], '/') : "",
            "startDate" => $event['event_date'] ? date('Y-m-d', strtotime($event['event_date'])) : "",
            "location" => [
                "@type" => "Place",
                "name" => $event['venue'] . ', ' . $event['state'] . ', ' . $event['country']
            ],
            "offers" => [
                "@type" => "Offer",
                "url" => $event['reg_url'] ?: "http://" . $_SERVER['HTTP_HOST'] . "/Mithraa_E_Project/project/register.php?tournament_id=" . $event['id'],
                "price" => "0",
                "priceCurrency" => "USD"
            ]
        ];
    ?>
    <script type="application/ld+json">
    <?= json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?>
    </script>
</head>
<body>

<?php require_once 'includes/navbar.php'; ?>

<section class="hero-section">
    <div style="color: white; padding: 20px;">
        <span class="category-badge"><?= htmlspecialchars($event['sport_category']) ?></span>
        <h1 class="event-title"><?= htmlspecialchars($event['tournament_name']) ?></h1>
    </div>
</section>

<div class="event-container">
    <div class="main-content">
        <div class="content-section">
            <h2>Tournament Details</h2>
            <div class="content-body">
                <?= $event['description'] ?>
            </div>
        </div>

        <?php if (!empty(trim(strip_tags($event['rules_data'])))): ?>
        <div class="content-section">
            <h2>Rules & Regulations</h2>
            <div class="content-body" style="background: rgba(0,0,0,0.3); padding: 20px; border-radius: 8px; border-left: 4px solid #f59e0b;">
                <?= $event['rules_data'] ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="sidebar">
        <h3>Event Information</h3>
        <ul class="meta-list">
            <li>
                <span class="meta-label">Venue</span>
                <span class="meta-value"><?= htmlspecialchars($event['venue'] ?: 'TBD') ?></span>
                <span style="color: #aaa; font-size: 0.9rem;"><?= htmlspecialchars(implode(', ', array_filter([$event['state'], $event['country']]))) ?></span>
            </li>
            <li>
                <span class="meta-label">Date</span>
                <span class="meta-value"><?= $event['event_date'] ? date('F j, Y', strtotime($event['event_date'])) : 'TBA' ?></span>
            </li>
            <li>
                <span class="meta-label">Prize Pool</span>
                <span class="meta-value" style="color: #f5d87a;"><?= htmlspecialchars($event['prize_pool'] ?: 'TBA') ?></span>
            </li>
            <li>
                <span class="meta-label">Status</span>
                <span class="meta-value" style="text-transform: capitalize; color: <?= $event['reg_status'] === 'open' ? '#10b981' : ($event['reg_status'] === 'closed' ? '#ef4444' : '#f59e0b') ?>;">
                    <?= htmlspecialchars($event['reg_status']) ?>
                </span>
            </li>
        </ul>

        <?php 
            $btnText = 'Register Now';
            $btnClass = 'cta-button';
            if ($event['reg_status'] === 'closed') {
                $btnText = 'Registration Closed';
                $btnClass .= ' cta-disabled';
            } elseif ($event['reg_status'] === 'upcoming') {
                $btnText = 'Coming Soon';
                $btnClass .= ' cta-disabled';
            }
            
            $regLink = $event['reg_url'] ?: 'register.php?tournament_id=' . $event['id'];
        ?>
        <a href="<?= htmlspecialchars($regLink) ?>" class="<?= $btnClass ?>">
            <?= $btnText ?>
        </a>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
</body>
</html>
