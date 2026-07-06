<?php
$pageTitle = "Edit GSA Carousel Event";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

require_once 'config/Database.php';
require_once 'includes/media_utils.php';
$pdo = Database::getConnection();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$event = null;

if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM gsa_carousel_events WHERE id = ?");
    $stmt->execute([$id]);
    $event = $stmt->fetch();
    if (!$event) {
        die("Event not found");
    }
}

function generateSlug($string) {
    $slug = preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower($string));
    return trim($slug, '-');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tournament_name = $_POST['tournament_name'] ?? '';
    $sport_category = $_POST['sport_category'] ?? '';
    $country = $_POST['country'] ?? '';
    $state = $_POST['state'] ?? '';
    $venue = $_POST['venue'] ?? '';
    $description = $_POST['description'] ?? '';
    $reg_status = $_POST['reg_status'] ?? 'open';
    $reg_url = $_POST['reg_url'] ?? '';
    $prize_pool = $_POST['prize_pool'] ?? '';
    $rules_data = $_POST['rules_data'] ?? '';
    $event_date = !empty($_POST['event_date']) ? $_POST['event_date'] : null;
    $display_order = (int)($_POST['display_order'] ?? 0);
    $status = $_POST['status'] ?? 'draft';
    
    $seo_title = $_POST['seo_title'] ?? '';
    $seo_desc = $_POST['seo_desc'] ?? '';
    $seo_keywords = $_POST['seo_keywords'] ?? '';
    
    $slug = $_POST['slug'] ?? '';
    if (empty($slug)) {
        $slug = generateSlug($tournament_name);
    }
    
    // Check slug uniqueness
    $slugCheck = $pdo->prepare("SELECT id FROM gsa_carousel_events WHERE slug = ? AND id != ?");
    $slugCheck->execute([$slug, $id]);
    if ($slugCheck->rowCount() > 0) {
        $slug = $slug . '-' . time();
    }

    // File Uploads
    $uploadDir = 'assets/images/uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $hero_banner = $event ? $event['hero_banner'] : '';
    if (isset($_FILES['hero_banner']) && $_FILES['hero_banner']['error'] == 0) {
        $uploaded = MediaUtils::processAndUploadImage($_FILES['hero_banner'], $uploadDir);
        if ($uploaded) $hero_banner = $uploaded;
    }
    
    if (isset($_FILES['carousel_img']) && $_FILES['carousel_img']['error'] == 0) {
        $uploaded = MediaUtils::processAndUploadImage($_FILES['carousel_img'], $uploadDir);
        if ($uploaded) $carousel_img = $uploaded;
    }


    if ($id > 0) {
        $stmt = $pdo->prepare("UPDATE gsa_carousel_events SET 
            tournament_name=?, sport_category=?, country=?, state=?, venue=?,
            hero_banner=?, carousel_img=?, description=?, reg_status=?, reg_url=?,
            prize_pool=?, rules_data=?, event_date=?, display_order=?, status=?,
            seo_title=?, seo_desc=?, seo_keywords=?, slug=?
            WHERE id=?");
        $stmt->execute([
            $tournament_name, $sport_category, $country, $state, $venue,
            $hero_banner, $carousel_img, $description, $reg_status, $reg_url,
            $prize_pool, $rules_data, $event_date, $display_order, $status,
            $seo_title, $seo_desc, $seo_keywords, $slug, $id
        ]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO gsa_carousel_events (
            tournament_name, sport_category, country, state, venue,
            hero_banner, carousel_img, description, reg_status, reg_url,
            prize_pool, rules_data, event_date, display_order, status,
            seo_title, seo_desc, seo_keywords, slug
        ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([
            $tournament_name, $sport_category, $country, $state, $venue,
            $hero_banner, $carousel_img, $description, $reg_status, $reg_url,
            $prize_pool, $rules_data, $event_date, $display_order, $status,
            $seo_title, $seo_desc, $seo_keywords, $slug
        ]);
        $id = $pdo->lastInsertId();
    }
    
    echo "<script>window.location.href='admin-gsa-carousel.php?msg=saved';</script>";
    exit();
}
?>

<link rel="stylesheet" href="assets/css/AdminDashboard.css?v=7">
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

<style>
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 8px; color: #c5a85c; font-weight: bold; }
    .form-control { width: 100%; padding: 10px; border: 1px solid rgba(197, 168, 92, 0.3); background: rgba(0,0,0,0.2); color: #fff; border-radius: 4px; box-sizing: border-box; }
    .form-control:focus { outline: none; border-color: #c5a85c; }
    .form-row { display: flex; gap: 20px; }
    .form-row .form-group { flex: 1; }
    .btn-gold { background: linear-gradient(135deg, #c5a85c, #f5d87a); color: #000; padding: 10px 20px; border-radius: 4px; text-decoration: none; font-weight: bold; border: none; cursor: pointer; display: inline-block; font-size: 1rem; }
    .card { background: rgba(197, 168, 92, 0.05); border: 1px solid rgba(197, 168, 92, 0.2); border-radius: 8px; padding: 20px; margin-bottom: 20px; }
    .card-title { font-size: 1.2rem; color: #fff; border-bottom: 1px solid rgba(197, 168, 92, 0.2); padding-bottom: 10px; margin-bottom: 20px; }
    
    .quill-editor { height: 200px; background: rgba(0,0,0,0.2); border-color: rgba(197,168,92,0.3); color: #fff; }
    .ql-toolbar { background: #1a1a2e; border-color: rgba(197,168,92,0.3) !important; }
    .ql-stroke { stroke: #c5a85c !important; }
    .ql-fill { fill: #c5a85c !important; }
    .ql-picker { color: #c5a85c !important; }
    
    body.light-theme .form-control, body.light-theme .quill-editor { background: #fff; color: #000; }
    body.light-theme .card-title { color: #000; }
</style>

<div class="admin-dashboard px-4 sm:px-8 py-10" style="background: #0b0c10; color: #f5f6fa; min-height: 100vh;">
  <?php require_once __DIR__ . '/includes/admin_navbar.php'; ?>
  
  <div class="admin-header" style="border-bottom: 1px solid rgba(197, 168, 92, 0.2); padding-bottom: 20px; margin-bottom: 20px;">
    <h1><?= $id ? 'Edit' : 'Add' ?> GSA Tournament</h1>
    <a href="admin-gsa-carousel.php" style="color: #c5a85c; text-decoration: none;">&larr; Back to List</a>
  </div>

  <form method="POST" enctype="multipart/form-data" id="eventForm">
    <div class="card">
        <div class="card-title">Tournament Information</div>
        <div class="form-row">
            <div class="form-group">
                <label>Tournament Name *</label>
                <input type="text" name="tournament_name" class="form-control" required value="<?= htmlspecialchars($event['tournament_name'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Sport Category *</label>
                <select name="sport_category" class="form-control" required>
                    <option value="">Select Sport...</option>
                    <?php
                    $sports = ['Football', 'Cricket', 'Badminton', 'Table Tennis', 'Basketball', 'Volleyball', 'Swimming', 'Athletics', 'Tennis', 'Cycling', 'Chess', 'Martial Arts', 'Watersports', 'Motorsports', 'Esports'];
                    foreach ($sports as $sport) {
                        $selected = ($event['sport_category'] ?? '') === $sport ? 'selected' : '';
                        echo "<option value=\"$sport\" $selected>$sport</option>";
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Venue</label>
                <input type="text" name="venue" class="form-control" value="<?= htmlspecialchars($event['venue'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Event Date</label>
                <input type="date" name="event_date" class="form-control" value="<?= htmlspecialchars($event['event_date'] ?? '') ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Country</label>
                <input type="text" name="country" class="form-control" value="<?= htmlspecialchars($event['country'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>State / Region</label>
                <input type="text" name="state" class="form-control" value="<?= htmlspecialchars($event['state'] ?? '') ?>">
            </div>
        </div>
        
        <div class="form-group">
            <label>Full Description</label>
            <input type="hidden" name="description" id="description_input">
            <div id="desc-editor" class="quill-editor"><?= $event['description'] ?? '' ?></div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-title">Registration & Rules</div>
        <div class="form-row">
            <div class="form-group">
                <label>Registration Status</label>
                <select name="reg_status" class="form-control">
                    <option value="open" <?= ($event['reg_status'] ?? '') === 'open' ? 'selected' : '' ?>>Open</option>
                    <option value="closed" <?= ($event['reg_status'] ?? '') === 'closed' ? 'selected' : '' ?>>Closed</option>
                    <option value="upcoming" <?= ($event['reg_status'] ?? '') === 'upcoming' ? 'selected' : '' ?>>Upcoming</option>
                </select>
            </div>
            <div class="form-group">
                <label>Custom Registration URL (Leave blank to use default portal)</label>
                <input type="text" name="reg_url" class="form-control" placeholder="https://..." value="<?= htmlspecialchars($event['reg_url'] ?? '') ?>">
            </div>
        </div>
        
        <div class="form-group">
            <label>Prize Pool</label>
            <input type="text" name="prize_pool" class="form-control" placeholder="e.g. $10,000 USD" value="<?= htmlspecialchars($event['prize_pool'] ?? '') ?>">
        </div>
        
        <div class="form-group">
            <label>Tournament Rules</label>
            <input type="hidden" name="rules_data" id="rules_input">
            <div id="rules-editor" class="quill-editor"><?= $event['rules_data'] ?? '' ?></div>
        </div>
    </div>

    <div class="card">
        <div class="card-title">Media Uploads</div>
        <div class="form-row">
            <div class="form-group">
                <label>Hero Banner (Detail Page)</label>
                <input type="file" name="hero_banner" class="form-control" accept="image/*">
                <?php if (!empty($event['hero_banner'])): ?>
                    <img src="<?= $event['hero_banner'] ?>" style="height: 60px; margin-top: 10px; border-radius: 4px;">
                <?php endif; ?>
            </div>
            <div class="form-group" style="flex: 1; min-width: 250px;">
                <label>Carousel Cover Image</label>
                <input type="file" name="carousel_img" class="form-control" accept="image/*">
                <?php if (!empty($event['carousel_img'])): ?>
                    <img src="<?= $event['carousel_img'] ?>" style="height: 60px; margin-top: 10px; border-radius: 4px;">
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-title">SEO & Display Options</div>
        <div class="form-row">
            <div class="form-group">
                <label>SEO Meta Title</label>
                <input type="text" name="seo_title" class="form-control" value="<?= htmlspecialchars($event['seo_title'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Custom Slug</label>
                <input type="text" name="slug" class="form-control" value="<?= htmlspecialchars($event['slug'] ?? '') ?>">
            </div>
        </div>
        
        <div class="form-group">
            <label>SEO Description</label>
            <textarea name="seo_desc" class="form-control" rows="2"><?= htmlspecialchars($event['seo_desc'] ?? '') ?></textarea>
        </div>
        
        <div class="form-group">
            <label>SEO Keywords</label>
            <input type="text" name="seo_keywords" class="form-control" value="<?= htmlspecialchars($event['seo_keywords'] ?? '') ?>">
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Display Order</label>
                <input type="number" name="display_order" class="form-control" value="<?= (int)($event['display_order'] ?? 0) ?>">
            </div>
            <div class="form-group">
                <label>Publishing Status</label>
                <select name="status" class="form-control">
                    <option value="draft" <?= ($event['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                    <option value="published" <?= ($event['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
                </select>
            </div>
        </div>
    </div>

    <div style="margin-bottom: 100px;">
        <button type="submit" class="btn-gold">Save Tournament</button>
    </div>
  </form>
</div>

<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
    var toolbarOptions = [
        [{ 'header': [1, 2, 3, false] }],
        ['bold', 'italic', 'underline', 'strike'],
        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
        ['link'],
        ['clean']
    ];
    
    var descQuill = new Quill('#desc-editor', { theme: 'snow', modules: { toolbar: toolbarOptions }});
    var rulesQuill = new Quill('#rules-editor', { theme: 'snow', modules: { toolbar: toolbarOptions }});

    document.getElementById('eventForm').onsubmit = function() {
        document.getElementById('description_input').value = descQuill.root.innerHTML;
        document.getElementById('rules_input').value = rulesQuill.root.innerHTML;
        return true;
    };
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
