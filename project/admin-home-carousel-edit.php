<?php
$pageTitle = "Edit Home Carousel Event";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

require_once 'config/Database.php';
require_once 'includes/media_utils.php';
$pdo = Database::getConnection();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$event = null;

    $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
    $timer_start_date = !empty($_POST['timer_start_date']) ? $_POST['timer_start_date'] : null;
    $gala_title = $_POST['gala_title'] ?? '';
    $gala_venue = $_POST['gala_venue'] ?? '';
    $gala_date = $_POST['gala_date'] ?? '';
    $gala_time = $_POST['gala_time'] ?? '';
    $gala_description = $_POST['gala_description'] ?? '';
    $custom_html = $_POST['custom_html'] ?? null;
    $delegate_fee = !empty($_POST['delegate_fee']) ? $_POST['delegate_fee'] : null;
    $delegate_currency = !empty($_POST['delegate_currency']) ? $_POST['delegate_currency'] : null;

    // Handle dynamic Schedule Data
    $schedule_data = null;
    if (isset($_POST['schedule_day']) && is_array($_POST['schedule_day'])) {
        $schedule_arr = [];
        for ($i = 0; $i < count($_POST['schedule_day']); $i++) {
            if (!empty($_POST['schedule_day'][$i]) || !empty($_POST['schedule_title'][$i])) {
                $schedule_arr[] = [
                    'day' => $_POST['schedule_day'][$i] ?? '',
                    'title' => $_POST['schedule_title'][$i] ?? '',
                    'time' => $_POST['schedule_time'][$i] ?? '',
                    'description' => $_POST['schedule_desc'][$i] ?? ''
                ];
            }
        }
        if (!empty($schedule_arr)) {
            $schedule_data = json_encode($schedule_arr);
        }
    }

    // Handle dynamic Sports Data
    $sports_data = null;
    if (isset($_POST['sport_title']) && is_array($_POST['sport_title'])) {
        $sports_arr = [];
        for ($i = 0; $i < count($_POST['sport_title']); $i++) {
            if (!empty($_POST['sport_title'][$i])) {
                $sportImg = $_POST['sport_image_existing'][$i] ?? '';
                if (isset($_FILES['sport_image_file']['name'][$i]) && $_FILES['sport_image_file']['error'][$i] === UPLOAD_ERR_OK) {
                    $uploadDir = 'assets/images/uploads/';
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                    
                    $fileArr = [
                        'name' => $_FILES['sport_image_file']['name'][$i],
                        'type' => $_FILES['sport_image_file']['type'][$i],
                        'tmp_name' => $_FILES['sport_image_file']['tmp_name'][$i],
                        'error' => $_FILES['sport_image_file']['error'][$i],
                        'size' => $_FILES['sport_image_file']['size'][$i]
                    ];
                    $uploaded = MediaUtils::processAndUploadImage($fileArr, $uploadDir);
                    if ($uploaded) {
                        $sportImg = $uploaded;
                    }
                }
                $sports_arr[] = [
                    'title' => $_POST['sport_title'][$i] ?? '',
                    'icon' => $_POST['sport_icon'][$i] ?? 'fa-table-tennis',
                    'image' => $sportImg,
                    'prize' => $_POST['sport_prize'][$i] ?? '',
                    'prize_currency' => $_POST['sport_prize_currency'][$i] ?? 'INR',
                    'badge' => $_POST['sport_badge'][$i] ?? 'Popular',
                    'categories' => $_POST['sport_categories'][$i] ?? '',
                    'currency' => $_POST['sport_currency'][$i] ?? 'INR',
                    'price_individual' => $_POST['sport_price_individual'][$i] ?? '',
                    'price_pair' => $_POST['sport_price_pair'][$i] ?? '',
                    'price_team' => $_POST['sport_price_team'][$i] ?? ''
                ];
            }
        }
        if (!empty($sports_arr)) {
            $sports_data = json_encode($sports_arr);
        }
    }

    // Handle dynamic Sponsors Data
    $sponsors_data = null;
    if (isset($_POST['sponsor_name']) && is_array($_POST['sponsor_name'])) {
        $sponsors_arr = [];
        for ($i = 0; $i < count($_POST['sponsor_name']); $i++) {
            if (!empty($_POST['sponsor_name'][$i]) || !empty($_POST['sponsor_img'][$i])) {
                $sponsors_arr[] = [
                    'name' => $_POST['sponsor_name'][$i] ?? '',
                    'website' => $_POST['sponsor_website'][$i] ?? '',
                    'type' => $_POST['sponsor_type'][$i] ?? '',
                    'img' => $_POST['sponsor_img'][$i] ?? ''
                ];
            }
        }
        if (!empty($sponsors_arr)) {
            $sponsors_data = json_encode($sponsors_arr);
        }
    }

    // Handle dynamic Exhibitor Data
    $exhibitor_data = null;
    if (isset($_POST['exhibitor_title']) && is_array($_POST['exhibitor_title'])) {
        $exhibitor_arr = [];
        for ($i = 0; $i < count($_POST['exhibitor_title']); $i++) {
            if (!empty($_POST['exhibitor_title'][$i])) {
                $exhibitor_arr[] = [
                    'title' => $_POST['exhibitor_title'][$i] ?? '',
                    'icon' => $_POST['exhibitor_icon'][$i] ?? 'fa-store',
                    'size' => $_POST['exhibitor_size'][$i] ?? '',
                    'desc' => $_POST['exhibitor_desc'][$i] ?? '',
                    'currency' => $_POST['exhibitor_currency'][$i] ?? 'INR',
                    'price' => $_POST['exhibitor_price'][$i] ?? '',
                    'badge' => $_POST['exhibitor_badge'][$i] ?? ''
                ];
            }
        }
        if (!empty($exhibitor_arr)) {
            $exhibitor_data = json_encode($exhibitor_arr);
        }
    }

    // Handle dynamic Locations Data
    $locations_data = null;
    if (isset($_POST['loc_name']) && is_array($_POST['loc_name'])) {
        $locations_arr = [];
        for ($i = 0; $i < count($_POST['loc_name']); $i++) {
            if (!empty($_POST['loc_name'][$i])) {
                $locBg = $_POST['loc_bg_existing'][$i] ?? '';
                if (isset($_FILES['loc_bg_file']['name'][$i]) && $_FILES['loc_bg_file']['error'][$i] === UPLOAD_ERR_OK) {
                    $uploadDir = 'assets/images/uploads/';
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                    $fileArr = [
                        'name' => $_FILES['loc_bg_file']['name'][$i],
                        'type' => $_FILES['loc_bg_file']['type'][$i],
                        'tmp_name' => $_FILES['loc_bg_file']['tmp_name'][$i],
                        'error' => $_FILES['loc_bg_file']['error'][$i],
                        'size' => $_FILES['loc_bg_file']['size'][$i]
                    ];
                    $uploaded = MediaUtils::processAndUploadImage($fileArr, $uploadDir);
                    if ($uploaded) {
                        $locBg = $uploaded;
                    }
                }
                $locations_arr[] = [
                    'name' => $_POST['loc_name'][$i] ?? '',
                    'subtitle' => $_POST['loc_subtitle'][$i] ?? '',
                    'bg' => $locBg,
                    'items' => $_POST['loc_items'][$i] ?? ''
                ];
            }
        }
        if (!empty($locations_arr)) {
            $locations_data = json_encode($locations_arr);
        }
    }

    // Handle dynamic Gala Passes
    $gala_passes_data = null;
    if (isset($_POST['gala_pass_title']) && is_array($_POST['gala_pass_title'])) {
        $passes_arr = [];
        for ($i = 0; $i < count($_POST['gala_pass_title']); $i++) {
            if (!empty($_POST['gala_pass_title'][$i])) {
                $passes_arr[] = [
                    'title' => $_POST['gala_pass_title'][$i] ?? '',
                    'currency' => $_POST['gala_pass_currency'][$i] ?? 'INR',
                    'price' => $_POST['gala_pass_price'][$i] ?? '0',
                    'features' => $_POST['gala_pass_features'][$i] ?? ''
                ];
            }
        }
        if (!empty($passes_arr)) {
            $gala_passes_data = json_encode($passes_arr);
        }
    }
        $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
    $timer_start_date = !empty($_POST['timer_start_date']) ? $_POST['timer_start_date'] : null;
    $gala_title = $_POST['gala_title'] ?? '';
    $gala_venue = $_POST['gala_venue'] ?? '';
    $gala_date = $_POST['gala_date'] ?? '';
    $gala_time = $_POST['gala_time'] ?? '';
    $gala_description = $_POST['gala_description'] ?? '';
    $custom_html = $_POST['custom_html'] ?? null;
    $delegate_fee = !empty($_POST['delegate_fee']) ? $_POST['delegate_fee'] : null;
    $delegate_currency = !empty($_POST['delegate_currency']) ? $_POST['delegate_currency'] : null;

    // Handle dynamic Schedule Data
    $schedule_data = null;
    if (isset($_POST['schedule_day']) && is_array($_POST['schedule_day'])) {
        $schedule_arr = [];
        for ($i = 0; $i < count($_POST['schedule_day']); $i++) {
            if (!empty($_POST['schedule_day'][$i]) || !empty($_POST['schedule_title'][$i])) {
                $schedule_arr[] = [
                    'day' => $_POST['schedule_day'][$i] ?? '',
                    'title' => $_POST['schedule_title'][$i] ?? '',
                    'time' => $_POST['schedule_time'][$i] ?? '',
                    'description' => $_POST['schedule_desc'][$i] ?? ''
                ];
            }
        }
        if (!empty($schedule_arr)) {
            $schedule_data = json_encode($schedule_arr);
        }
    }

    // Handle dynamic Sports Data
    $sports_data = null;
    if (isset($_POST['sport_title']) && is_array($_POST['sport_title'])) {
        $sports_arr = [];
        for ($i = 0; $i < count($_POST['sport_title']); $i++) {
            if (!empty($_POST['sport_title'][$i])) {
                $sportImg = $_POST['sport_image_existing'][$i] ?? '';
                if (isset($_FILES['sport_image_file']['name'][$i]) && $_FILES['sport_image_file']['error'][$i] === UPLOAD_ERR_OK) {
                    $uploadDir = 'assets/images/uploads/';
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                    
                    $fileArr = [
                        'name' => $_FILES['sport_image_file']['name'][$i],
                        'type' => $_FILES['sport_image_file']['type'][$i],
                        'tmp_name' => $_FILES['sport_image_file']['tmp_name'][$i],
                        'error' => $_FILES['sport_image_file']['error'][$i],
                        'size' => $_FILES['sport_image_file']['size'][$i]
                    ];
                    $uploaded = MediaUtils::processAndUploadImage($fileArr, $uploadDir);
                    if ($uploaded) {
                        $sportImg = $uploaded;
                    }
                }
                $sports_arr[] = [
                    'title' => $_POST['sport_title'][$i] ?? '',
                    'icon' => $_POST['sport_icon'][$i] ?? 'fa-table-tennis',
                    'image' => $sportImg,
                    'prize' => $_POST['sport_prize'][$i] ?? '',
                    'prize_currency' => $_POST['sport_prize_currency'][$i] ?? 'INR',
                    'badge' => $_POST['sport_badge'][$i] ?? 'Popular',
                    'categories' => $_POST['sport_categories'][$i] ?? '',
                    'currency' => $_POST['sport_currency'][$i] ?? 'INR',
                    'price_individual' => $_POST['sport_price_individual'][$i] ?? '',
                    'price_pair' => $_POST['sport_price_pair'][$i] ?? '',
                    'price_team' => $_POST['sport_price_team'][$i] ?? ''
                ];
            }
        }
        if (!empty($sports_arr)) {
            $sports_data = json_encode($sports_arr);
        }
    }

    // Handle dynamic Sponsors Data
    $sponsors_data = null;
    if (isset($_POST['sponsor_name']) && is_array($_POST['sponsor_name'])) {
        $sponsors_arr = [];
        for ($i = 0; $i < count($_POST['sponsor_name']); $i++) {
            if (!empty($_POST['sponsor_name'][$i]) || !empty($_POST['sponsor_img'][$i])) {
                $sponsors_arr[] = [
                    'name' => $_POST['sponsor_name'][$i] ?? '',
                    'website' => $_POST['sponsor_website'][$i] ?? '',
                    'type' => $_POST['sponsor_type'][$i] ?? '',
                    'img' => $_POST['sponsor_img'][$i] ?? ''
                ];
            }
        }
        if (!empty($sponsors_arr)) {
            $sponsors_data = json_encode($sponsors_arr);
        }
    }

    // Handle dynamic Exhibitor Data
    $exhibitor_data = null;
    if (isset($_POST['exhibitor_title']) && is_array($_POST['exhibitor_title'])) {
        $exhibitor_arr = [];
        for ($i = 0; $i < count($_POST['exhibitor_title']); $i++) {
            if (!empty($_POST['exhibitor_title'][$i])) {
                $exhibitor_arr[] = [
                    'title' => $_POST['exhibitor_title'][$i] ?? '',
                    'icon' => $_POST['exhibitor_icon'][$i] ?? 'fa-store',
                    'size' => $_POST['exhibitor_size'][$i] ?? '',
                    'desc' => $_POST['exhibitor_desc'][$i] ?? '',
                    'currency' => $_POST['exhibitor_currency'][$i] ?? 'INR',
                    'price' => $_POST['exhibitor_price'][$i] ?? '',
                    'badge' => $_POST['exhibitor_badge'][$i] ?? ''
                ];
            }
        }
        if (!empty($exhibitor_arr)) {
            $exhibitor_data = json_encode($exhibitor_arr);
        }
    }

    // Handle dynamic Locations Data
    $locations_data = null;
    if (isset($_POST['loc_name']) && is_array($_POST['loc_name'])) {
        $locations_arr = [];
        for ($i = 0; $i < count($_POST['loc_name']); $i++) {
            if (!empty($_POST['loc_name'][$i])) {
                $locBg = $_POST['loc_bg_existing'][$i] ?? '';
                if (isset($_FILES['loc_bg_file']['name'][$i]) && $_FILES['loc_bg_file']['error'][$i] === UPLOAD_ERR_OK) {
                    $uploadDir = 'assets/images/uploads/';
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                    $fileArr = [
                        'name' => $_FILES['loc_bg_file']['name'][$i],
                        'type' => $_FILES['loc_bg_file']['type'][$i],
                        'tmp_name' => $_FILES['loc_bg_file']['tmp_name'][$i],
                        'error' => $_FILES['loc_bg_file']['error'][$i],
                        'size' => $_FILES['loc_bg_file']['size'][$i]
                    ];
                    $uploaded = MediaUtils::processAndUploadImage($fileArr, $uploadDir);
                    if ($uploaded) {
                        $locBg = $uploaded;
                    }
                }
                $locations_arr[] = [
                    'name' => $_POST['loc_name'][$i] ?? '',
                    'subtitle' => $_POST['loc_subtitle'][$i] ?? '',
                    'bg' => $locBg,
                    'items' => $_POST['loc_items'][$i] ?? ''
                ];
            }
        }
        if (!empty($locations_arr)) {
            $locations_data = json_encode($locations_arr);
        }
    }

    // Handle dynamic Gala Passes
    $gala_passes_data = null;
    if (isset($_POST['gala_pass_title']) && is_array($_POST['gala_pass_title'])) {
        $passes_arr = [];
        for ($i = 0; $i < count($_POST['gala_pass_title']); $i++) {
            if (!empty($_POST['gala_pass_title'][$i])) {
                $passes_arr[] = [
                    'title' => $_POST['gala_pass_title'][$i] ?? '',
                    'currency' => $_POST['gala_pass_currency'][$i] ?? 'INR',
                    'price' => $_POST['gala_pass_price'][$i] ?? '0',
                    'features' => $_POST['gala_pass_features'][$i] ?? ''
                ];
            }
        }
        if (!empty($passes_arr)) {
            $gala_passes_data = json_encode($passes_arr);
        }
    }
    if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM home_carousel_events WHERE id = ?");
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
    $title = $_POST['title'] ?? '';
    $subtitle = $_POST['subtitle'] ?? '';
    $short_desc = $_POST['short_desc'] ?? '';
    $description = $_POST['description'] ?? '';
    $category = $_POST['category'] ?? '';
    $country = $_POST['country'] ?? '';
    $state = $_POST['state'] ?? '';
    $location = $_POST['location'] ?? '';
    $badge_text = $_POST['badge_text'] ?? '';
    $btn_text = $_POST['btn_text'] ?? 'Explore';
    $btn_url = $_POST['btn_url'] ?? '';
    $event_date = !empty($_POST['event_date']) ? $_POST['event_date'] : null;
    $display_order = (int)($_POST['display_order'] ?? 0);
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $status = $_POST['status'] ?? 'draft';
    
    $seo_title = $_POST['seo_title'] ?? '';
    $seo_desc = $_POST['seo_desc'] ?? '';
    $seo_keywords = $_POST['seo_keywords'] ?? '';
    
    $slug = $_POST['slug'] ?? '';
    if (empty($slug)) {
        $slug = generateSlug($title);
    }
    
    // Check slug uniqueness
    $slugCheck = $pdo->prepare("SELECT id FROM home_carousel_events WHERE slug = ? AND id != ?");
    $slugCheck->execute([$slug, $id]);
    if ($slugCheck->rowCount() > 0) {
        $slug = $slug . '-' . time(); // append timestamp to make it unique
    }

    // Handle File Uploads (simple implementation for now)
    $uploadDir = 'assets/images/uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $hero_banner = $event ? $event['hero_banner'] : '';
    if (!empty($_POST['hero_banner_url'])) {
        $hero_banner = $_POST['hero_banner_url'];
    }
    if (isset($_FILES['hero_banner']) && $_FILES['hero_banner']['error'] == 0) {
        $uploaded = MediaUtils::processAndUploadImage($_FILES['hero_banner'], $uploadDir);
        if ($uploaded) $hero_banner = $uploaded;
    }
    
    $carousel_img = $event ? $event['carousel_img'] : '';
    if (!empty($_POST['carousel_img_url'])) {
        $carousel_img = $_POST['carousel_img_url'];
    }
    if (isset($_FILES['carousel_img']) && $_FILES['carousel_img']['error'] == 0) {
        $uploaded = MediaUtils::processAndUploadImage($_FILES['carousel_img'], $uploadDir);
        if ($uploaded) $carousel_img = $uploaded;
    }

    $home_banner_img = $event ? $event['home_banner_img'] : '';
    if (!empty($_POST['home_banner_img_url'])) {
        $home_banner_img = $_POST['home_banner_img_url'];
    }
    if (isset($_FILES['home_banner_img']) && $_FILES['home_banner_img']['error'] == 0) {
        $uploaded = MediaUtils::processAndUploadImage($_FILES['home_banner_img'], $uploadDir);
        if ($uploaded) $home_banner_img = $uploaded;
    }

    $registration_image = $event ? ($event['registration_image'] ?? '') : '';
    if (!empty($_POST['registration_image_url'])) {
        $registration_image = $_POST['registration_image_url'];
    }
    if (isset($_FILES['registration_image']) && $_FILES['registration_image']['error'] == 0) {
        $uploaded = MediaUtils::processAndUploadImage($_FILES['registration_image'], $uploadDir);
        if ($uploaded) $registration_image = $uploaded;
    }

        $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
    $timer_start_date = !empty($_POST['timer_start_date']) ? $_POST['timer_start_date'] : null;
    $gala_title = $_POST['gala_title'] ?? '';
    $gala_venue = $_POST['gala_venue'] ?? '';
    $gala_date = $_POST['gala_date'] ?? '';
    $gala_time = $_POST['gala_time'] ?? '';
    $gala_description = $_POST['gala_description'] ?? '';
    $custom_html = $_POST['custom_html'] ?? null;
    $delegate_fee = !empty($_POST['delegate_fee']) ? $_POST['delegate_fee'] : null;
    $delegate_currency = !empty($_POST['delegate_currency']) ? $_POST['delegate_currency'] : null;

    // Handle dynamic Schedule Data
    $schedule_data = null;
    if (isset($_POST['schedule_day']) && is_array($_POST['schedule_day'])) {
        $schedule_arr = [];
        for ($i = 0; $i < count($_POST['schedule_day']); $i++) {
            if (!empty($_POST['schedule_day'][$i]) || !empty($_POST['schedule_title'][$i])) {
                $schedule_arr[] = [
                    'day' => $_POST['schedule_day'][$i] ?? '',
                    'title' => $_POST['schedule_title'][$i] ?? '',
                    'time' => $_POST['schedule_time'][$i] ?? '',
                    'description' => $_POST['schedule_desc'][$i] ?? ''
                ];
            }
        }
        if (!empty($schedule_arr)) {
            $schedule_data = json_encode($schedule_arr);
        }
    }

    // Handle dynamic Sports Data
    $sports_data = null;
    if (isset($_POST['sport_title']) && is_array($_POST['sport_title'])) {
        $sports_arr = [];
        for ($i = 0; $i < count($_POST['sport_title']); $i++) {
            if (!empty($_POST['sport_title'][$i])) {
                $sportImg = $_POST['sport_image_existing'][$i] ?? '';
                if (isset($_FILES['sport_image_file']['name'][$i]) && $_FILES['sport_image_file']['error'][$i] === UPLOAD_ERR_OK) {
                    $uploadDir = 'assets/images/uploads/';
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                    
                    $fileArr = [
                        'name' => $_FILES['sport_image_file']['name'][$i],
                        'type' => $_FILES['sport_image_file']['type'][$i],
                        'tmp_name' => $_FILES['sport_image_file']['tmp_name'][$i],
                        'error' => $_FILES['sport_image_file']['error'][$i],
                        'size' => $_FILES['sport_image_file']['size'][$i]
                    ];
                    $uploaded = MediaUtils::processAndUploadImage($fileArr, $uploadDir);
                    if ($uploaded) {
                        $sportImg = $uploaded;
                    }
                }
                $sports_arr[] = [
                    'title' => $_POST['sport_title'][$i] ?? '',
                    'icon' => $_POST['sport_icon'][$i] ?? 'fa-table-tennis',
                    'image' => $sportImg,
                    'prize' => $_POST['sport_prize'][$i] ?? '',
                    'prize_currency' => $_POST['sport_prize_currency'][$i] ?? 'INR',
                    'badge' => $_POST['sport_badge'][$i] ?? 'Popular',
                    'categories' => $_POST['sport_categories'][$i] ?? '',
                    'currency' => $_POST['sport_currency'][$i] ?? 'INR',
                    'price_individual' => $_POST['sport_price_individual'][$i] ?? '',
                    'price_pair' => $_POST['sport_price_pair'][$i] ?? '',
                    'price_team' => $_POST['sport_price_team'][$i] ?? ''
                ];
            }
        }
        if (!empty($sports_arr)) {
            $sports_data = json_encode($sports_arr);
        }
    }

    // Handle dynamic Sponsors Data
    $sponsors_data = null;
    if (isset($_POST['sponsor_name']) && is_array($_POST['sponsor_name'])) {
        $sponsors_arr = [];
        for ($i = 0; $i < count($_POST['sponsor_name']); $i++) {
            if (!empty($_POST['sponsor_name'][$i]) || !empty($_POST['sponsor_img'][$i])) {
                $sponsors_arr[] = [
                    'name' => $_POST['sponsor_name'][$i] ?? '',
                    'website' => $_POST['sponsor_website'][$i] ?? '',
                    'type' => $_POST['sponsor_type'][$i] ?? '',
                    'img' => $_POST['sponsor_img'][$i] ?? ''
                ];
            }
        }
        if (!empty($sponsors_arr)) {
            $sponsors_data = json_encode($sponsors_arr);
        }
    }

    // Handle dynamic Exhibitor Data
    $exhibitor_data = null;
    if (isset($_POST['exhibitor_title']) && is_array($_POST['exhibitor_title'])) {
        $exhibitor_arr = [];
        for ($i = 0; $i < count($_POST['exhibitor_title']); $i++) {
            if (!empty($_POST['exhibitor_title'][$i])) {
                $exhibitor_arr[] = [
                    'title' => $_POST['exhibitor_title'][$i] ?? '',
                    'icon' => $_POST['exhibitor_icon'][$i] ?? 'fa-store',
                    'size' => $_POST['exhibitor_size'][$i] ?? '',
                    'desc' => $_POST['exhibitor_desc'][$i] ?? '',
                    'currency' => $_POST['exhibitor_currency'][$i] ?? 'INR',
                    'price' => $_POST['exhibitor_price'][$i] ?? '',
                    'badge' => $_POST['exhibitor_badge'][$i] ?? ''
                ];
            }
        }
        if (!empty($exhibitor_arr)) {
            $exhibitor_data = json_encode($exhibitor_arr);
        }
    }

    // Handle dynamic Locations Data
    $locations_data = null;
    if (isset($_POST['loc_name']) && is_array($_POST['loc_name'])) {
        $locations_arr = [];
        for ($i = 0; $i < count($_POST['loc_name']); $i++) {
            if (!empty($_POST['loc_name'][$i])) {
                $locBg = $_POST['loc_bg_existing'][$i] ?? '';
                if (isset($_FILES['loc_bg_file']['name'][$i]) && $_FILES['loc_bg_file']['error'][$i] === UPLOAD_ERR_OK) {
                    $uploadDir = 'assets/images/uploads/';
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                    $fileArr = [
                        'name' => $_FILES['loc_bg_file']['name'][$i],
                        'type' => $_FILES['loc_bg_file']['type'][$i],
                        'tmp_name' => $_FILES['loc_bg_file']['tmp_name'][$i],
                        'error' => $_FILES['loc_bg_file']['error'][$i],
                        'size' => $_FILES['loc_bg_file']['size'][$i]
                    ];
                    $uploaded = MediaUtils::processAndUploadImage($fileArr, $uploadDir);
                    if ($uploaded) {
                        $locBg = $uploaded;
                    }
                }
                $locations_arr[] = [
                    'name' => $_POST['loc_name'][$i] ?? '',
                    'subtitle' => $_POST['loc_subtitle'][$i] ?? '',
                    'bg' => $locBg,
                    'items' => $_POST['loc_items'][$i] ?? ''
                ];
            }
        }
        if (!empty($locations_arr)) {
            $locations_data = json_encode($locations_arr);
        }
    }

    // Handle dynamic Gala Passes
    $gala_passes_data = null;
    if (isset($_POST['gala_pass_title']) && is_array($_POST['gala_pass_title'])) {
        $passes_arr = [];
        for ($i = 0; $i < count($_POST['gala_pass_title']); $i++) {
            if (!empty($_POST['gala_pass_title'][$i])) {
                $passes_arr[] = [
                    'title' => $_POST['gala_pass_title'][$i] ?? '',
                    'currency' => $_POST['gala_pass_currency'][$i] ?? 'INR',
                    'price' => $_POST['gala_pass_price'][$i] ?? '0',
                    'features' => $_POST['gala_pass_features'][$i] ?? ''
                ];
            }
        }
        if (!empty($passes_arr)) {
            $gala_passes_data = json_encode($passes_arr);
        }
    }
        $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
    $timer_start_date = !empty($_POST['timer_start_date']) ? $_POST['timer_start_date'] : null;
    $gala_title = $_POST['gala_title'] ?? '';
    $gala_venue = $_POST['gala_venue'] ?? '';
    $gala_date = $_POST['gala_date'] ?? '';
    $gala_time = $_POST['gala_time'] ?? '';
    $gala_description = $_POST['gala_description'] ?? '';
    $custom_html = $_POST['custom_html'] ?? null;
    $delegate_fee = !empty($_POST['delegate_fee']) ? $_POST['delegate_fee'] : null;
    $delegate_currency = !empty($_POST['delegate_currency']) ? $_POST['delegate_currency'] : null;

    // Handle dynamic Schedule Data
    $schedule_data = null;
    if (isset($_POST['schedule_day']) && is_array($_POST['schedule_day'])) {
        $schedule_arr = [];
        for ($i = 0; $i < count($_POST['schedule_day']); $i++) {
            if (!empty($_POST['schedule_day'][$i]) || !empty($_POST['schedule_title'][$i])) {
                $schedule_arr[] = [
                    'day' => $_POST['schedule_day'][$i] ?? '',
                    'title' => $_POST['schedule_title'][$i] ?? '',
                    'time' => $_POST['schedule_time'][$i] ?? '',
                    'description' => $_POST['schedule_desc'][$i] ?? ''
                ];
            }
        }
        if (!empty($schedule_arr)) {
            $schedule_data = json_encode($schedule_arr);
        }
    }

    // Handle dynamic Sports Data
    $sports_data = null;
    if (isset($_POST['sport_title']) && is_array($_POST['sport_title'])) {
        $sports_arr = [];
        for ($i = 0; $i < count($_POST['sport_title']); $i++) {
            if (!empty($_POST['sport_title'][$i])) {
                $sportImg = $_POST['sport_image_existing'][$i] ?? '';
                if (isset($_FILES['sport_image_file']['name'][$i]) && $_FILES['sport_image_file']['error'][$i] === UPLOAD_ERR_OK) {
                    $uploadDir = 'assets/images/uploads/';
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                    
                    $fileArr = [
                        'name' => $_FILES['sport_image_file']['name'][$i],
                        'type' => $_FILES['sport_image_file']['type'][$i],
                        'tmp_name' => $_FILES['sport_image_file']['tmp_name'][$i],
                        'error' => $_FILES['sport_image_file']['error'][$i],
                        'size' => $_FILES['sport_image_file']['size'][$i]
                    ];
                    $uploaded = MediaUtils::processAndUploadImage($fileArr, $uploadDir);
                    if ($uploaded) {
                        $sportImg = $uploaded;
                    }
                }
                $sports_arr[] = [
                    'title' => $_POST['sport_title'][$i] ?? '',
                    'icon' => $_POST['sport_icon'][$i] ?? 'fa-table-tennis',
                    'image' => $sportImg,
                    'prize' => $_POST['sport_prize'][$i] ?? '',
                    'prize_currency' => $_POST['sport_prize_currency'][$i] ?? 'INR',
                    'badge' => $_POST['sport_badge'][$i] ?? 'Popular',
                    'categories' => $_POST['sport_categories'][$i] ?? '',
                    'currency' => $_POST['sport_currency'][$i] ?? 'INR',
                    'price_individual' => $_POST['sport_price_individual'][$i] ?? '',
                    'price_pair' => $_POST['sport_price_pair'][$i] ?? '',
                    'price_team' => $_POST['sport_price_team'][$i] ?? ''
                ];
            }
        }
        if (!empty($sports_arr)) {
            $sports_data = json_encode($sports_arr);
        }
    }

    // Handle dynamic Sponsors Data
    $sponsors_data = null;
    if (isset($_POST['sponsor_name']) && is_array($_POST['sponsor_name'])) {
        $sponsors_arr = [];
        for ($i = 0; $i < count($_POST['sponsor_name']); $i++) {
            if (!empty($_POST['sponsor_name'][$i]) || !empty($_POST['sponsor_img'][$i])) {
                $sponsors_arr[] = [
                    'name' => $_POST['sponsor_name'][$i] ?? '',
                    'website' => $_POST['sponsor_website'][$i] ?? '',
                    'type' => $_POST['sponsor_type'][$i] ?? '',
                    'img' => $_POST['sponsor_img'][$i] ?? ''
                ];
            }
        }
        if (!empty($sponsors_arr)) {
            $sponsors_data = json_encode($sponsors_arr);
        }
    }

    // Handle dynamic Exhibitor Data
    $exhibitor_data = null;
    if (isset($_POST['exhibitor_title']) && is_array($_POST['exhibitor_title'])) {
        $exhibitor_arr = [];
        for ($i = 0; $i < count($_POST['exhibitor_title']); $i++) {
            if (!empty($_POST['exhibitor_title'][$i])) {
                $exhibitor_arr[] = [
                    'title' => $_POST['exhibitor_title'][$i] ?? '',
                    'icon' => $_POST['exhibitor_icon'][$i] ?? 'fa-store',
                    'size' => $_POST['exhibitor_size'][$i] ?? '',
                    'desc' => $_POST['exhibitor_desc'][$i] ?? '',
                    'currency' => $_POST['exhibitor_currency'][$i] ?? 'INR',
                    'price' => $_POST['exhibitor_price'][$i] ?? '',
                    'badge' => $_POST['exhibitor_badge'][$i] ?? ''
                ];
            }
        }
        if (!empty($exhibitor_arr)) {
            $exhibitor_data = json_encode($exhibitor_arr);
        }
    }

    // Handle dynamic Locations Data
    $locations_data = null;
    if (isset($_POST['loc_name']) && is_array($_POST['loc_name'])) {
        $locations_arr = [];
        for ($i = 0; $i < count($_POST['loc_name']); $i++) {
            if (!empty($_POST['loc_name'][$i])) {
                $locBg = $_POST['loc_bg_existing'][$i] ?? '';
                if (isset($_FILES['loc_bg_file']['name'][$i]) && $_FILES['loc_bg_file']['error'][$i] === UPLOAD_ERR_OK) {
                    $uploadDir = 'assets/images/uploads/';
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                    $fileArr = [
                        'name' => $_FILES['loc_bg_file']['name'][$i],
                        'type' => $_FILES['loc_bg_file']['type'][$i],
                        'tmp_name' => $_FILES['loc_bg_file']['tmp_name'][$i],
                        'error' => $_FILES['loc_bg_file']['error'][$i],
                        'size' => $_FILES['loc_bg_file']['size'][$i]
                    ];
                    $uploaded = MediaUtils::processAndUploadImage($fileArr, $uploadDir);
                    if ($uploaded) {
                        $locBg = $uploaded;
                    }
                }
                $locations_arr[] = [
                    'name' => $_POST['loc_name'][$i] ?? '',
                    'subtitle' => $_POST['loc_subtitle'][$i] ?? '',
                    'bg' => $locBg,
                    'items' => $_POST['loc_items'][$i] ?? ''
                ];
            }
        }
        if (!empty($locations_arr)) {
            $locations_data = json_encode($locations_arr);
        }
    }

    // Handle dynamic Gala Passes
    $gala_passes_data = null;
    if (isset($_POST['gala_pass_title']) && is_array($_POST['gala_pass_title'])) {
        $passes_arr = [];
        for ($i = 0; $i < count($_POST['gala_pass_title']); $i++) {
            if (!empty($_POST['gala_pass_title'][$i])) {
                $passes_arr[] = [
                    'title' => $_POST['gala_pass_title'][$i] ?? '',
                    'currency' => $_POST['gala_pass_currency'][$i] ?? 'INR',
                    'price' => $_POST['gala_pass_price'][$i] ?? '0',
                    'features' => $_POST['gala_pass_features'][$i] ?? ''
                ];
            }
        }
        if (!empty($passes_arr)) {
            $gala_passes_data = json_encode($passes_arr);
        }
    }
    // Determine display flags based on category
    $show_on_home = 0;
    $show_on_gsa = 0;
    
    if ($category === 'Nexus' || $category === 'Maytriya' || $category === 'Nexus & Maytriya') {
        $show_on_home = 1;
    } elseif ($category === 'GSA') {
        $show_on_gsa = 1;
    } elseif ($category === 'All') {
        $show_on_home = 1;
        $show_on_gsa = 1;
    }

    $show_home_banner = isset($_POST['show_home_banner']) ? 1 : 0;
    $show_in_overseas = isset($_POST['show_in_overseas']) ? 1 : 0;

    $stat1_val = $_POST['stat1_val'] ?? '';
    $stat1_label = $_POST['stat1_label'] ?? '';
    $stat2_val = $_POST['stat2_val'] ?? '';
    $stat2_label = $_POST['stat2_label'] ?? '';
    $stat3_val = $_POST['stat3_val'] ?? '';
    $stat3_label = $_POST['stat3_label'] ?? '';
    $stat4_val = $_POST['stat4_val'] ?? '';
    $stat4_label = $_POST['stat4_label'] ?? '';

    if ($id > 0) {
        $stmt = $pdo->prepare("UPDATE home_carousel_events SET 
            title=?, subtitle=?, short_desc=?, description=?, category=?, country=?, state=?, location=?, badge_text=?,
            hero_banner=?, carousel_img=?, home_banner_img=?, registration_image=?, btn_text=?, btn_url=?,
            event_date=?, display_order=?, is_featured=?, status=?,
            seo_title=?, seo_desc=?, seo_keywords=?, slug=?,
            end_date=?, timer_start_date=?, gala_title=?, gala_venue=?, gala_date=?, gala_time=?, gala_description=?, custom_html=?, delegate_fee=?, delegate_currency=?, schedule_data=?, sports_data=?, sponsors_data=?, exhibitor_data=?, locations_data=?, gala_passes_data=?,
            show_on_home=?, show_on_gsa=?, show_home_banner=?, show_in_overseas=?,
            stat1_val=?, stat1_label=?, stat2_val=?, stat2_label=?, stat3_val=?, stat3_label=?, stat4_val=?, stat4_label=?
            WHERE id=?");
        $stmt->execute([
            $title, $subtitle, $short_desc, $description, $category, $country, $state, $location, $badge_text,
            $hero_banner, $carousel_img, $home_banner_img, $registration_image, $btn_text, $btn_url,
            $event_date, $display_order, $is_featured, $status,
            $seo_title, $seo_desc, $seo_keywords, $slug, 
            $end_date, $timer_start_date, $gala_title, $gala_venue, $gala_date, $gala_time, $gala_description, $custom_html, $delegate_fee, $delegate_currency, $schedule_data, $sports_data, $sponsors_data, $exhibitor_data, $locations_data, $gala_passes_data,
            $show_on_home, $show_on_gsa, $show_home_banner, $show_in_overseas,
            $stat1_val, $stat1_label, $stat2_val, $stat2_label, $stat3_val, $stat3_label, $stat4_val, $stat4_label,
            $id
        ]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO home_carousel_events (
            title, subtitle, short_desc, description, category, country, state, location, badge_text,
            hero_banner, carousel_img, home_banner_img, registration_image, btn_text, btn_url,
            event_date, display_order, is_featured, status,
            seo_title, seo_desc, seo_keywords, slug,
            end_date, timer_start_date, gala_title, gala_venue, gala_date, gala_time, gala_description, custom_html, delegate_fee, delegate_currency, schedule_data, sports_data, sponsors_data, exhibitor_data, locations_data, gala_passes_data,
            show_on_home, show_on_gsa, show_home_banner, show_in_overseas,
            stat1_val, stat1_label, stat2_val, stat2_label, stat3_val, stat3_label, stat4_val, stat4_label
        ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([
            $title, $subtitle, $short_desc, $description, $category, $country, $state, $location, $badge_text,
            $hero_banner, $carousel_img, $home_banner_img, $registration_image, $btn_text, $btn_url,
            $event_date, $display_order, $is_featured, $status,
            $seo_title, $seo_desc, $seo_keywords, $slug, 
            $end_date, $timer_start_date, $gala_title, $gala_venue, $gala_date, $gala_time, $gala_description, $custom_html, $delegate_fee, $delegate_currency, $schedule_data, $sports_data, $sponsors_data, $exhibitor_data, $locations_data, $gala_passes_data,
            $show_on_home, $show_on_gsa, $show_home_banner, $show_in_overseas,
            $stat1_val, $stat1_label, $stat2_val, $stat2_label, $stat3_val, $stat3_label, $stat4_val, $stat4_label
        ]);
        $id = $pdo->lastInsertId();
    }
    
    // Auto-sync exhibitor_data to `events` table for GSA events
    // This ensures that when the user updates the dynamic page, the form dropdown (which pulls from events table) gets updated too.
    if (!empty($exhibitor_data)) {
        $searchTitle = '%' . trim(str_ireplace('GSA', '', $title)) . '%';
        $syncStmt = $pdo->prepare("UPDATE events SET exhibitor_data = ? WHERE title LIKE ? OR slug LIKE ?");
        $syncStmt->execute([$exhibitor_data, $searchTitle, $searchTitle]);
    }
    
    echo "<script>window.location.href='admin-home-carousel.php?msg=saved';</script>";
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
    
    #editor-container { height: 300px; background: rgba(0,0,0,0.2); border-color: rgba(197,168,92,0.3); color: #fff; }
    .ql-toolbar { background: #1a1a2e; border-color: rgba(197,168,92,0.3) !important; }
    .ql-stroke { stroke: #c5a85c !important; }
    .ql-fill { fill: #c5a85c !important; }
    .ql-picker { color: #c5a85c !important; }
    
    body.light-theme .form-control, body.light-theme #editor-container { background: #fff; color: #000; }
    body.light-theme .card-title { color: #000; }
</style>

<div class="admin-dashboard px-4 sm:px-8 py-10" style="background: #0b0c10; color: #f5f6fa; min-height: 100vh;">
  <?php require_once __DIR__ . '/includes/admin_navbar.php'; ?>
  
  <div class="admin-header" style="border-bottom: 1px solid rgba(197, 168, 92, 0.2); padding-bottom: 20px; margin-bottom: 20px;">
    <h1><?= $id ? 'Edit' : 'Add' ?> Home Carousel Event</h1>
    <a href="admin-home-carousel.php" style="color: #c5a85c; text-decoration: none;">&larr; Back to List</a>
  </div>

  <form method="POST" enctype="multipart/form-data" id="eventForm">
    <div class="card">
        <div class="card-title">Basic Information</div>
        <div class="form-row">
            <div class="form-group">
                <label>Event Title *</label>
                <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($event['title'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Subtitle</label>
                <input type="text" name="subtitle" class="form-control" value="<?= htmlspecialchars($event['subtitle'] ?? '') ?>">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Category</label>
                <select name="category" class="form-control" required>
                    <option value="">Select Category...</option>
                    <option value="Nexus" <?= ($event['category'] ?? '') === 'Nexus' ? 'selected' : '' ?>>Nexus (Business & Elite)</option>
                    <option value="Maytriya" <?= ($event['category'] ?? '') === 'Maytriya' ? 'selected' : '' ?>>Maytriya (Cultural & Tech)</option>
                    <option value="Nexus & Maytriya" <?= ($event['category'] ?? '') === 'Nexus & Maytriya' ? 'selected' : '' ?>>Nexus & Maytriya (Combined)</option>
                    <option value="GSA" <?= ($event['category'] ?? '') === 'GSA' ? 'selected' : '' ?>>GSA (Sports & Global)</option>
                    <option value="All" <?= ($event['category'] ?? '') === 'All' ? 'selected' : '' ?>>All Categories (Combined Festival)</option>
                </select>
                <small style="color: #9aa0b4; margin-top: 5px; display: block;">This category fully determines the visual theme on the frontend details page.</small>
            </div>
            <div class="form-group">
                <label>Event Date</label>
                <input type="date" name="event_date" class="form-control" value="<?= htmlspecialchars($event['event_date'] ?? '') ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group" style="flex: 2;">
                <label>Country / State</label>
                <input type="text" name="country" id="country_input" list="country_options" class="form-control" value="<?= htmlspecialchars($event['country'] ?? $event['state'] ?? '') ?>" placeholder="Type or select a country/state..." required>
                <datalist id="country_options">
                    <!-- International -->
                    <option value="Dubai">
                    <option value="Singapore">
                    <option value="London">
                    <option value="New York">
                    <option value="USA - Las Vegas">
                    <option value="Malaysia">
                    <option value="Thailand">
                    <!-- Indian States/Cities -->
                    <option value="Mumbai">
                    <option value="Delhi">
                    <option value="Bangalore">
                    <option value="Goa">
                    <option value="Pune">
                    <option value="Kerala">
                    <option value="Rajasthan">
                </datalist>
            </div>
            <!-- state input removed, backend uses $_POST['state'] ?? '' automatically -->
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Location (Full Address)</label>
                <input type="text" name="location" class="form-control" value="<?= htmlspecialchars($event['location'] ?? '') ?>" placeholder="e.g. Shree Shiv Chhatrapati Sports Complex, Pune">
            </div>
            <div class="form-group">
                <label>Top Badge Text</label>
                <input type="text" name="badge_text" class="form-control" value="<?= htmlspecialchars($event['badge_text'] ?? '') ?>" placeholder="e.g. GSA Championship Series">
            </div>
        </div>
        
        <div class="form-group">
            <label>Short Description</label>
            <textarea name="short_desc" class="form-control" rows="3"><?= htmlspecialchars($event['short_desc'] ?? '') ?></textarea>
        </div>
    </div>

    <!-- Custom Statistics -->
    <div class="card">
        <div class="card-title">Custom Statistics (4 Boxes) <small style="color:#aaa; font-size:0.9rem;">(Leave blank to auto-calculate)</small></div>
        <div class="form-row">
            <div class="form-group">
                <label>Box 1 Value</label>
                <input type="text" name="stat1_val" class="form-control" placeholder="e.g. 4" value="<?= htmlspecialchars($event['stat1_val'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Box 1 Label</label>
                <input type="text" name="stat1_label" class="form-control" placeholder="e.g. SPORTS CHAMPIONSHIPS" value="<?= htmlspecialchars($event['stat1_label'] ?? '') ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Box 2 Value</label>
                <input type="text" name="stat2_val" class="form-control" placeholder="e.g. 8" value="<?= htmlspecialchars($event['stat2_val'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Box 2 Label</label>
                <input type="text" name="stat2_label" class="form-control" placeholder="e.g. DAYS FESTIVAL" value="<?= htmlspecialchars($event['stat2_label'] ?? '') ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Box 3 Value</label>
                <input type="text" name="stat3_val" class="form-control" placeholder="e.g. 10L+" value="<?= htmlspecialchars($event['stat3_val'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Box 3 Label</label>
                <input type="text" name="stat3_label" class="form-control" placeholder="e.g. PRIZE POOL" value="<?= htmlspecialchars($event['stat3_label'] ?? '') ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Box 4 Value</label>
                <input type="text" name="stat4_val" class="form-control" placeholder="e.g. 1000+" value="<?= htmlspecialchars($event['stat4_val'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Box 4 Label</label>
                <input type="text" name="stat4_label" class="form-control" placeholder="e.g. PARTICIPANTS EXPECTED" value="<?= htmlspecialchars($event['stat4_label'] ?? '') ?>">
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-title">Media & Buttons</div>
        <div class="form-row">
            <div class="form-group">
                <label>Hero Banner (Detail Page Header)</label>
                <input type="file" name="hero_banner" class="form-control" accept="image/*" style="margin-bottom: 5px;">
                <input type="url" name="hero_banner_url" class="form-control" placeholder="OR enter image URL...">
                <?php if (!empty($event['hero_banner'])): ?>
                    <img src="<?= $event['hero_banner'] ?>" style="height: 60px; margin-top: 10px; border-radius: 4px;">
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label>Carousel Image (Home Page Thumbnail)</label>
                <input type="file" name="carousel_img" class="form-control" accept="image/*" style="margin-bottom: 5px;">
                <input type="url" name="carousel_img_url" class="form-control" placeholder="OR enter image URL...">
                <?php if (!empty($event['carousel_img'])): ?>
                    <img src="<?= $event['carousel_img'] ?>" style="height: 60px; margin-top: 10px; border-radius: 4px;">
                <?php endif; ?>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group" style="flex: 2;">
                <label>Full-Width Home Banner (2nd Image)</label>
                <input type="file" name="home_banner_img" class="form-control" accept="image/*" style="margin-bottom: 5px;">
                <input type="url" name="home_banner_img_url" class="form-control" placeholder="OR enter image URL...">
                <?php if (!empty($event['home_banner_img'])): ?>
                    <img src="<?= $event['home_banner_img'] ?>" style="height: 60px; margin-top: 10px; border-radius: 4px;">
                <?php endif; ?>
                <p style="font-size: 0.8rem; color: #9aa0b4; margin-top: 5px;">This is the large 2nd image rendered at the bottom of the home page (formerly Events Cards).</p>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Button Text</label>
                <input type="text" name="btn_text" class="form-control" value="<?= htmlspecialchars($event['btn_text'] ?? 'Explore') ?>">
            </div>
            <div class="form-group">
                <label>Button URL (Leave blank to auto-generate details page)</label>
                <input type="text" name="btn_url" class="form-control" placeholder="https://..." value="<?= htmlspecialchars($event['btn_url'] ?? '') ?>">
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-title">SEO & Display</div>
        <div class="form-row">
            <div class="form-group">
                <label>SEO Meta Title</label>
                <input type="text" name="seo_title" class="form-control" value="<?= htmlspecialchars($event['seo_title'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Custom Slug (Auto-generated if blank)</label>
                <input type="text" name="slug" class="form-control" value="<?= htmlspecialchars($event['slug'] ?? '') ?>">
            </div>
        </div>
        
        <div class="form-group">
            <label>SEO Description</label>
            <textarea name="seo_desc" class="form-control" rows="2"><?= htmlspecialchars($event['seo_desc'] ?? '') ?></textarea>
        </div>
        
        <div class="form-group">
            <label>SEO Keywords (Comma separated)</label>
            <input type="text" name="seo_keywords" class="form-control" value="<?= htmlspecialchars($event['seo_keywords'] ?? '') ?>">
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Display Order (Lower = First)</label>
                <input type="number" name="display_order" class="form-control" value="<?= (int)($event['display_order'] ?? 0) ?>">
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="draft" <?= ($event['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                    <option value="published" <?= ($event['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
                </select>
            </div>
            <div class="form-group" style="display: flex; align-items: center; gap: 10px; margin-top: 30px;">
                <input type="checkbox" name="is_featured" value="1" <?= (!empty($event['is_featured'])) ? 'checked' : '' ?> style="width: 20px; height: 20px;">
                <label style="margin-bottom: 0;">Featured Event</label>
            </div>
            <div class="form-group" style="display: flex; align-items: center; gap: 10px; margin-top: 30px;">
                <input type="checkbox" name="show_home_banner" value="1" <?= (!empty($event['show_home_banner'])) ? 'checked' : '' ?> style="width: 20px; height: 20px;">
                <label style="margin-bottom: 0;">Display 2nd Banner on Home Page</label>
            </div>
            <div class="form-group" style="display: flex; align-items: center; gap: 10px; margin-top: 30px;">
                <input type="checkbox" name="show_in_overseas" value="1" <?= (!empty($event['show_in_overseas'])) ? 'checked' : '' ?> style="width: 20px; height: 20px;">
                <label style="margin-bottom: 0;">Display in Overseas & Indian States Carousel</label>
            </div>
        </div>
    </div>

          <div class="form-group">
          <label>End Date</label>
          <input type="date" name="end_date" required value="<?= $event ? htmlspecialchars($event['end_date'] ?? '') : '' ?>">
      </div>

      <div class="form-group">
          <label>Description</label>
          <textarea name="description" rows="5"><?= $event ? htmlspecialchars($event['description'] ?? '') : '' ?></textarea>
      </div>

      <div class="form-group">
          <label>Custom HTML / Rich Text Section (Optional)</label>
          <p style="font-size: 0.85rem; color: #9aa0b4; margin-bottom: 8px;">Use this field to paste completely custom layouts, such as the Thailand Festival Concept. This will be rendered directly on the Event Details page below the overview.</p>
          <textarea name="custom_html" rows="10" style="font-family: monospace;"><?= $event ? htmlspecialchars($event['custom_html'] ?? '') : '' ?></textarea>
      </div>

      <div class="dynamic-block">
          <h3>Event-Specific Delegate Pricing (Optional)</h3>
          <p style="font-size: 0.85rem; color: #9aa0b4; margin-bottom: 15px;">Leave blank to use the global delegate fee set in Delegate Settings.</p>
          <div class="form-group" style="display: flex; gap: 15px;">
              <div style="flex: 1;">
                  <label>Delegate Registration Fee</label>
                  <input type="number" step="0.01" name="delegate_fee" value="<?= $event ? htmlspecialchars($event['delegate_fee'] ?? '') : '' ?>" placeholder="e.g. 200.00">
              </div>
              <div style="flex: 1;">
                  <label>Currency</label>
                  <select name="delegate_currency">
                      <option value="">Select Currency...</option>
                      <option value="USD" <?= ($event && ($event['delegate_currency'] ?? '') == 'USD') ? 'selected' : '' ?>>USD ($)</option>
                      <option value="EUR" <?= ($event && ($event['delegate_currency'] ?? '') == 'EUR') ? 'selected' : '' ?>>EUR (€)</option>
                      <option value="GBP" <?= ($event && ($event['delegate_currency'] ?? '') == 'GBP') ? 'selected' : '' ?>>GBP (£)</option>
                      <option value="AED" <?= ($event && ($event['delegate_currency'] ?? '') == 'AED') ? 'selected' : '' ?>>AED</option>
                      <option value="INR" <?= ($event && ($event['delegate_currency'] ?? '') == 'INR') ? 'selected' : '' ?>>INR (₹)</option>
                  </select>
              </div>
          </div>
      </div>
      <!-- Event Schedule Section -->
      <hr>
      <h3>Event Schedule (Dynamic)</h3>
      <p style="color:#666; margin-bottom: 15px;">Add the timeline schedule for this event.</p>
      
      <div id="schedule-container">
          <?php 
          $schedules = [];
          if ($event && !empty($event['schedule_data'])) {
              $schedules = json_decode($event['schedule_data'], true) ?? [];
          }
          ?>
      </div>
      <button type="button" class="btn-gold" style="font-size: 14px; padding: 6px 15px; margin-bottom: 20px;" onclick="addScheduleItem()">+ Add Schedule Item</button>

      <!-- Sports Categories Section -->
      <hr>
      <h3>Sports Categories (Dynamic)</h3>
      <p style="color:#666; margin-bottom: 15px;">Add sports categories for this event (Badminton, Football, etc.).</p>
      
      <div id="sports-container">
          <?php 
          $sports = [];
          if ($event && !empty($event['sports_data'])) {
              $sports = json_decode($event['sports_data'], true) ?? [];
          }
          ?>
      </div>
      <button type="button" class="btn-gold" style="font-size: 14px; padding: 6px 15px; margin-bottom: 20px;" onclick="addSportItem()">+ Add Sport Category</button>

      <!-- Sponsorship Opportunities Section -->
      <hr>
      <h3>Sponsorship Opportunities (Dynamic)</h3>
      <p style="color:#666; margin-bottom: 15px;">Add sponsors for this event.</p>
      
      <div id="sponsors-container">
          <?php 
          $sponsors = [];
          if ($event && !empty($event['sponsors_data'])) {
              $sponsors = json_decode($event['sponsors_data'], true) ?? [];
          }
          ?>
      </div>
      <button type="button" class="btn-gold" style="font-size: 14px; padding: 6px 15px; margin-bottom: 20px;" onclick="addSponsorItem()">+ Add Sponsor</button>

      <!-- Exhibitor Opportunities Section -->
      <hr>
      <h3>Exhibitor Opportunities (Dynamic)</h3>
      <p style="color:#666; margin-bottom: 15px;">Add exhibitor stalls/packages for this event.</p>
      
      <div id="exhibitors-container">
          <?php 
          $exhibitors = [];
          if ($event && !empty($event['exhibitor_data'])) {
              $exhibitors = json_decode($event['exhibitor_data'], true) ?? [];
          }
          ?>
      </div>
      <button type="button" class="btn-gold" style="font-size: 14px; padding: 6px 15px; margin-bottom: 20px;" onclick="addExhibitorItem()">+ Add Exhibitor Package</button>

      <!-- Festival Locations -->
      <hr>
      <h3>Festival Locations</h3>
      <p style="color:#666; margin-bottom: 15px;">Add dynamic locations (e.g., Bangkok, Pattaya) with background images and event points.</p>
      <div id="locations-container">
          <?php 
          $locations = [];
          if ($event && !empty($event['locations_data'])) {
              $locations = json_decode($event['locations_data'], true) ?? [];
          }
          ?>
      </div>
      <button type="button" class="btn-gold" style="font-size: 14px; padding: 6px 15px; margin-bottom: 20px;" onclick="addLocationItem()">+ Add Festival Location</button>

      <!-- Gala Dinner Section -->
      <hr>
      <h3>Award Ceremony & Gala Dinner Details</h3>
      <p style="color:#666; margin-bottom: 15px;">Fields are optional. If left blank, this section won't appear on the event details page.</p>

      <div class="form-group">
          <label>Section Title (e.g. Award Ceremony & Gala Dinner)</label>
          <input type="text" name="gala_title" placeholder="Award Ceremony & Gala Dinner" value="<?= $event ? htmlspecialchars($event['gala_title'] ?? '') : '' ?>">
      </div>

      <div class="form-group">
          <label>Venue (e.g. The Orchid Hotel Pune)</label>
          <input type="text" name="gala_venue" value="<?= $event ? htmlspecialchars($event['gala_venue'] ?? '') : '' ?>">
      </div>

      <div class="form-group">
          <label>Date (e.g. 14 October 2026)</label>
          <input type="text" name="gala_date" value="<?= $event ? htmlspecialchars($event['gala_date'] ?? '') : '' ?>">
      </div>

      <div class="form-group">
          <label>Time (e.g. 7:00 PM - 11:00 PM)</label>
          <input type="text" name="gala_time" value="<?= $event ? htmlspecialchars($event['gala_time'] ?? '') : '' ?>">
      </div>

      <div class="form-group">
          <label>Description (e.g. Join us for an exclusive evening...)</label>
          <textarea name="gala_description" rows="3"><?= $event ? htmlspecialchars($event['gala_description'] ?? '') : '' ?></textarea>
      </div>

      <!-- Gala Passes -->
      <hr>
      <h3>Gala Dinner Passes (Dynamic)</h3>
      <p style="color:#666; margin-bottom: 15px;">Add ticket types/passes for the Gala Dinner. If left empty, the award registration page will use defaults.</p>
      <div id="gala-passes-container">
          <?php 
          $galaPasses = [];
          if ($event && !empty($event['gala_passes_data'])) {
              $galaPasses = json_decode($event['gala_passes_data'], true) ?? [];
          }
          ?>
      </div>
      <button type="button" class="btn-gold" style="font-size: 14px; padding: 6px 15px; margin-bottom: 20px;" onclick="addGalaPass()">+ Add Gala Pass</button>

<script>
    // Initial exhibitor data loaded from DB
    const existingExhibitors = <?= json_encode($exhibitors ?? []) ?>;
    const exhibitorsContainer = document.getElementById('exhibitors-container');

    function createExhibitorBlock(title = '', icon = 'fa-store', size = '', desc = '', price = '', badge = '', currency = 'INR') {
        title = String(title ?? ''); icon = String(icon ?? 'fa-store'); size = String(size ?? ''); desc = String(desc ?? ''); price = String(price ?? ''); badge = String(badge ?? ''); currency = String(currency ?? 'INR');
        const div = document.createElement('div');
        div.className = 'dynamic-block';
        
        div.innerHTML = `
            <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
                <strong style="color:#c5a85c; font-size: 16px;">Exhibitor Package</strong>
                <button type="button" style="background: transparent; border: 1px solid #dc3545; color: #dc3545; padding: 4px 10px; border-radius: 4px; font-size: 12px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#dc3545'; this.style.color='#fff';" onmouseout="this.style.background='transparent'; this.style.color='#dc3545';" onclick="this.parentElement.parentElement.remove()">X Remove</button>
            </div>
            <div style="display:flex; gap:10px; margin-bottom:10px;">
                <div style="flex:1;">
                    <label>Package Title (e.g. Premium Stall)</label>
                    <input type="text" name="exhibitor_title[]" value="${title.replace(/"/g, '&quot;')}" placeholder="e.g. Premium Stall">
                </div>
                <div style="flex:1;">
                    <label>Icon Class (e.g. fa-store)</label>
                    <input type="text" name="exhibitor_icon[]" value="${icon.replace(/"/g, '&quot;')}" placeholder="e.g. fa-store">
                </div>
                <div style="flex:1;">
                    <label>Badge (Optional, e.g. HOT)</label>
                    <input type="text" name="exhibitor_badge[]" value="${badge.replace(/"/g, '&quot;')}" placeholder="e.g. HOT">
                </div>
            </div>
            <div style="display:flex; gap:10px; margin-bottom:10px;">
                <div style="flex:1;">
                    <label>Stall Size (e.g. 6m x 3m)</label>
                    <input type="text" name="exhibitor_size[]" value="${size.replace(/"/g, '&quot;')}" placeholder="e.g. 6m x 3m">
                </div>
                <div style="flex:1; display:flex; gap:10px;">
                    <div style="flex:1;">
                        <label>Currency</label>
                        <select name="exhibitor_currency[]">
                            <option value="INR" ${currency === 'INR' ? 'selected' : ''}>INR (?)</option>
                            <option value="USD" ${currency === 'USD' ? 'selected' : ''}>USD ($)</option>
                        </select>
                    </div>
                    <div style="flex:2;">
                        <label>Price (e.g. 60000)</label>
                        <input type="text" name="exhibitor_price[]" value="${price.replace(/"/g, '&quot;')}" placeholder="e.g. 60000">
                    </div>
                </div>
            </div>
            <div class="form-group" style="margin-bottom:10px;">
                <label>Description (e.g. Booth space in high footfall area)</label>
                <input type="text" name="exhibitor_desc[]" value="${desc.replace(/"/g, '&quot;')}" placeholder="e.g. Booth space in high footfall area">
            </div>
        `;
        return div;
    }

    function addExhibitorItem() {
        exhibitorsContainer.appendChild(createExhibitorBlock());
    }

    if (existingExhibitors.length > 0) {
        existingExhibitors.forEach(e => {
            exhibitorsContainer.appendChild(createExhibitorBlock(e.title, e.icon, e.size, e.desc, e.price, e.badge, e.currency));
        });
    } else {
        // Default blocks for backward compatibility if empty
        exhibitorsContainer.appendChild(createExhibitorBlock('Standard Stall', 'fa-store', '3m x 3m', 'Booth space in general area', '30000', '', 'INR'));
        exhibitorsContainer.appendChild(createExhibitorBlock('Premium Stall', 'fa-store-alt', '6m x 3m', 'Booth space in high footfall area', '60000', 'HOT', 'INR'));
        exhibitorsContainer.appendChild(createExhibitorBlock('Corner Premium', 'fa-city', '6m x 6m', 'Two-side open booth for better visibility', '90000', '', 'INR'));
        exhibitorsContainer.appendChild(createExhibitorBlock('Pavilion Partner', 'fa-building', 'Custom', 'Large space buildout', '2,00,000+', '', 'INR'));
    }

    // Initial locations data loaded from DB
    const existingLocations = <?= json_encode($locations) ?>;
    const locationsContainer = document.getElementById('locations-container');

    function createLocationBlock(name = '', subtitle = '', bg = '', items = '') {
        name = String(name ?? ''); subtitle = String(subtitle ?? ''); bg = String(bg ?? '');
        const div = document.createElement('div');
        div.className = 'dynamic-block';
        
        let safeItems = items;
        if (typeof items === 'string') {
            safeItems = items.replace(/</g, '&lt;').replace(/>/g, '&gt;');
        }
        
        div.innerHTML = `
            <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
                <strong style="color:#c5a85c; font-size: 16px;">Festival Location</strong>
                <button type="button" style="background: transparent; border: 1px solid #dc3545; color: #dc3545; padding: 4px 10px; border-radius: 4px; font-size: 12px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#dc3545'; this.style.color='#fff';" onmouseout="this.style.background='transparent'; this.style.color='#dc3545';" onclick="this.parentElement.parentElement.remove()">X Remove</button>
            </div>
            <div style="display:flex; gap:10px; margin-bottom:10px;">
                <div style="flex:1;">
                    <label>Location Name</label>
                    <input type="text" name="loc_name[]" value="${name.replace(/"/g, '&quot;')}" placeholder="e.g. Bangkok">
                </div>
                <div style="flex:1;">
                    <label>Subtitle / Description</label>
                    <input type="text" name="loc_subtitle[]" value="${subtitle.replace(/"/g, '&quot;')}" placeholder="e.g. A vibrant city...">
                </div>
            </div>
            <div class="form-group" style="margin-bottom:10px;">
                <label>Background Image (URL or File Upload)</label>
                <input type="text" name="loc_bg_existing[]" value="${bg.replace(/"/g, '&quot;')}" placeholder="Existing URL or leave blank to upload new" style="margin-bottom: 5px;">
                <input type="file" name="loc_bg_file[]" accept="image/*">
            </div>
            <div class="form-group" style="margin-bottom:10px;">
                <label>Location Items / Features (One per line)</label>
                <textarea name="loc_items[]" rows="4" placeholder="Opening Ceremony&#10;Sports Tourism Summit&#10;International Expo&#10;Business Conference">${safeItems}</textarea>
            </div>
        `;
        return div;
    }

    function addLocationItem() {
        locationsContainer.appendChild(createLocationBlock());
    }

    if (existingLocations.length > 0) {
        existingLocations.forEach(l => {
            locationsContainer.appendChild(createLocationBlock(l.name, l.subtitle, l.bg, l.items));
        });
    }

    // Initial gala passes data loaded from DB
    const existingGalaPasses = <?= json_encode($galaPasses ?? []) ?>;
    const galaPassesContainer = document.getElementById('gala-passes-container');

    function createGalaPassBlock(title = '', price = '', features = '', currency = 'INR') {
        title = String(title ?? ''); price = String(price ?? ''); currency = String(currency ?? 'INR');
        const div = document.createElement('div');
        div.className = 'dynamic-block';
        
        let safeFeatures = features;
        if (typeof features === 'string') {
            safeFeatures = features.replace(/</g, '&lt;').replace(/>/g, '&gt;');
        }
        
        div.innerHTML = `
            <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
                <strong style="color:#c5a85c; font-size: 16px;">Gala Pass</strong>
                <button type="button" style="background: transparent; border: 1px solid #dc3545; color: #dc3545; padding: 4px 10px; border-radius: 4px; font-size: 12px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#dc3545'; this.style.color='#fff';" onmouseout="this.style.background='transparent'; this.style.color='#dc3545';" onclick="this.parentElement.parentElement.remove()">X Remove</button>
            </div>
            <div style="display:flex; gap:10px; margin-bottom:10px;">
                <div style="flex:1;">
                    <label>Pass Title (e.g. Single Gala Pass)</label>
                    <input type="text" name="gala_pass_title[]" value="${title.replace(/"/g, '&quot;')}" placeholder="e.g. Single Gala Pass" required>
                </div>
                <div style="flex:1; display:flex; gap:10px;">
                    <div style="flex:1;">
                        <label>Currency</label>
                        <select name="gala_pass_currency[]">
                            <option value="INR" ${currency === 'INR' ? 'selected' : ''}>INR (?)</option>
                            <option value="USD" ${currency === 'USD' ? 'selected' : ''}>USD ($)</option>
                        </select>
                    </div>
                    <div style="flex:2;">
                        <label>Price (Eg. 500 USD)</label>
                        <input type="text" name="gala_pass_price[]" value="${price.replace(/"/g, '&quot;')}" placeholder="e.g. 4500" required>
                    </div>
                </div>
            </div>
            <div class="form-group" style="margin-bottom:10px;">
                <label>Pass Features (One per line)</label>
                <textarea name="gala_pass_features[]" rows="3" placeholder="Valid for 1 Person&#10;Award Ceremony Entry&#10;Gala Dinner">${safeFeatures}</textarea>
            </div>
        `;
        return div;
    }

    function addGalaPass() {
        galaPassesContainer.appendChild(createGalaPassBlock());
    }

    if (existingGalaPasses.length > 0) {
        existingGalaPasses.forEach(p => {
            galaPassesContainer.appendChild(createGalaPassBlock(p.title, p.price, p.features, p.currency));
        });
    }

    // Initial sponsors data loaded from DB
    const existingSponsors = <?= json_encode($sponsors) ?>;
    const sponsorsContainer = document.getElementById('sponsors-container');

    function createSponsorBlock(name = '', website = '', img = '', type = '') {
        name = String(name ?? ''); website = String(website ?? ''); img = String(img ?? ''); type = String(type ?? '');
        const div = document.createElement('div');
        div.className = 'dynamic-block';
        
        div.innerHTML = `
            <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
                <strong style="color:#c5a85c; font-size: 16px;">Sponsor</strong>
                <button type="button" style="background: transparent; border: 1px solid #dc3545; color: #dc3545; padding: 4px 10px; border-radius: 4px; font-size: 12px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#dc3545'; this.style.color='#fff';" onmouseout="this.style.background='transparent'; this.style.color='#dc3545';" onclick="this.parentElement.parentElement.remove()">X Remove</button>
            </div>
            <div style="display:flex; gap:10px; margin-bottom:10px;">
                <div style="flex:1;">
                    <label>Sponsor Name</label>
                    <input type="text" name="sponsor_name[]" value="${name.replace(/"/g, '&quot;')}" placeholder="e.g. Nike">
                </div>
                <div style="flex:1;">
                    <label>Sponsorship Type</label>
                    <input type="text" name="sponsor_type[]" value="${type.replace(/"/g, '&quot;')}" placeholder="e.g. Basic, VIP, VVIP">
                </div>
                <div style="flex:1;">
                    <label>Website URL (Optional)</label>
                    <input type="text" name="sponsor_website[]" value="${website.replace(/"/g, '&quot;')}" placeholder="e.g. https://nike.com">
                </div>
            </div>
            <div class="form-group" style="margin-bottom:10px;">
                <label>Image URL (Logo)</label>
                <input type="text" name="sponsor_img[]" value="${img.replace(/"/g, '&quot;')}" placeholder="e.g. uploads/nike.png">
            </div>
        `;
        return div;
    }

    function addSponsorItem() {
        sponsorsContainer.appendChild(createSponsorBlock());
    }

    if (existingSponsors.length > 0) {
        existingSponsors.forEach(s => {
            sponsorsContainer.appendChild(createSponsorBlock(s.name, s.website, s.img, s.type));
        });
    }

    // Initial sports data loaded from DB
    const existingSports = <?= json_encode($sports) ?>;
    const sportsContainer = document.getElementById('sports-container');

    function createSportBlock(title = '', icon = 'fa-table-tennis', image = '', prize = '', badge = 'Popular', cats = '', pInd = '', pPair = '', pTeam = '', currency = 'INR', prize_currency = 'INR') {
        title = String(title ?? ''); icon = String(icon ?? 'fa-table-tennis'); image = String(image ?? ''); prize = String(prize ?? ''); badge = String(badge ?? 'Popular'); cats = String(cats ?? ''); pInd = String(pInd ?? ''); pPair = String(pPair ?? ''); pTeam = String(pTeam ?? ''); currency = String(currency ?? 'INR'); prize_currency = String(prize_currency ?? 'INR');
        const standardIcons = ['fa-table-tennis', 'fa-futbol', 'fa-basketball-ball', 'fa-volleyball-ball', 'fa-baseball-ball', 'fa-running', 'fa-swimmer', 'fa-dumbbell', 'fa-biking', 'fa-medal', 'fa-trophy'];
        const isStandard = standardIcons.includes(icon) || icon === '';

        const div = document.createElement('div');
        div.className = 'dynamic-block';
        
        div.innerHTML = `
            <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
                <strong style="color:#c5a85c; font-size: 16px;">Sport Category</strong>
                <button type="button" style="background: transparent; border: 1px solid #dc3545; color: #dc3545; padding: 4px 10px; border-radius: 4px; font-size: 12px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#dc3545'; this.style.color='#fff';" onmouseout="this.style.background='transparent'; this.style.color='#dc3545';" onclick="this.parentElement.parentElement.remove()">X Remove</button>
            </div>
            <div style="display:flex; gap:10px; margin-bottom:10px;">
                <div style="flex:1;">
                    <label>Sport Title</label>
                    <input type="text" name="sport_title[]" value="${title.replace(/"/g, '&quot;')}" placeholder="e.g. Badminton Championship">
                </div>
                <div style="flex:1;">
                    <label>Sport Icon</label>
                    <select style="margin-bottom: 5px; width: 100%; padding: 5px;" onchange="
                        if(this.value === 'custom') {
                            this.nextElementSibling.style.display = 'block';
                            this.nextElementSibling.value = '';
                            this.nextElementSibling.focus();
                        } else {
                            this.nextElementSibling.style.display = 'none';
                            this.nextElementSibling.value = this.value;
                        }
                    ">
                        <option value="fa-table-tennis" ${icon === 'fa-table-tennis' ? 'selected' : ''}>Table Tennis</option>
                        <option value="fa-futbol" ${icon === 'fa-futbol' ? 'selected' : ''}>Football / Soccer</option>
                        <option value="fa-basketball-ball" ${icon === 'fa-basketball-ball' ? 'selected' : ''}>Basketball</option>
                        <option value="fa-volleyball-ball" ${icon === 'fa-volleyball-ball' ? 'selected' : ''}>Volleyball</option>
                        <option value="fa-baseball-ball" ${icon === 'fa-baseball-ball' ? 'selected' : ''}>Baseball / Cricket</option>
                        <option value="fa-running" ${icon === 'fa-running' ? 'selected' : ''}>Athletics / Running</option>
                        <option value="fa-swimmer" ${icon === 'fa-swimmer' ? 'selected' : ''}>Swimming</option>
                        <option value="fa-dumbbell" ${icon === 'fa-dumbbell' ? 'selected' : ''}>Gym / Fitness</option>
                        <option value="fa-biking" ${icon === 'fa-biking' ? 'selected' : ''}>Cycling</option>
                        <option value="fa-medal" ${icon === 'fa-medal' ? 'selected' : ''}>General Match / Medal</option>
                        <option value="fa-trophy" ${icon === 'fa-trophy' ? 'selected' : ''}>Tournament / Trophy</option>
                        <option value="custom" ${!isStandard ? 'selected' : ''}>Other (Custom Icon Class)</option>
                    </select>
                    <input type="text" name="sport_icon[]" value="${icon.replace(/"/g, '&quot;')}" style="display: ${isStandard ? 'none' : 'block'}; width: 100%; padding: 5px; border: 1px solid #ccc; border-radius: 4px;" placeholder="e.g. fa-golf-ball">
                </div>
            </div>
            <div class="form-group" style="margin-bottom:10px;">
                <label>Sport Image (URL or File Upload) - Replaces icon if provided</label>
                <input type="text" name="sport_image_existing[]" value="${image.replace(/"/g, '&quot;')}" placeholder="Existing URL or leave blank to upload new" style="margin-bottom: 5px;">
                <input type="file" name="sport_image_file[]" accept="image/*">
            </div>
            <div style="display:flex; gap:10px; margin-bottom:10px;">
                <div style="flex:1; display:flex; gap:10px;">
                    <div style="flex:1;">
                        <label>Currency</label>
                        <select name="sport_prize_currency[]" style="width: 100%;">
                            <option value="INR" ${prize_currency === 'INR' ? 'selected' : ''}>INR (?)</option>
                            <option value="USD" ${prize_currency === 'USD' ? 'selected' : ''}>USD ($)</option>
                        </select>
                    </div>
                    <div style="flex:2;">
                        <label>Prize Pool</label>
                        <input type="text" name="sport_prize[]" value="${prize.replace(/"/g, '&quot;')}" placeholder="e.g. 2,50,000">
                    </div>
                </div>
                <div style="flex:1;">
                    <label>Top Badge</label>
                    <select name="sport_badge[]">
                        <option value="Popular" ${badge === 'Popular' ? 'selected' : ''}>Popular</option>
                        <option value="Trending" ${badge === 'Trending' ? 'selected' : ''}>Trending</option>
                        <option value="Premium" ${badge === 'Premium' ? 'selected' : ''}>Premium</option>
                        <option value="Featured" ${badge === 'Featured' ? 'selected' : ''}>Featured</option>
                    </select>
                </div>
            </div>
            <div class="form-group" style="margin-bottom:10px;">
                <label>Categories</label>
                <select multiple style="width:100%; height:150px; padding:5px; border:1px solid #ccc; border-radius:4px;" onchange="
                    const vals = Array.from(this.selectedOptions).map(opt => opt.value);
                    this.nextElementSibling.value = vals.join(', ');
                ">
                    ${ ['U14', 'U16', 'U18', 'Open', 'Doubles', 'Corporate'].map(opt => `
                        <option value="${opt}" ${cats.split(',').map(s=>s.trim()).includes(opt) ? 'selected' : ''}>${opt}</option>
                    `).join('') }
                </select>
                <input type="hidden" name="sport_categories[]" value="${cats.replace(/"/g, '&quot;')}">
                <small style="color:#666;">Hold Ctrl (Windows) or Cmd (Mac) to select multiple options.</small>
            </div>
            <div style="display:flex; gap:10px; margin-bottom:10px;">
                <div style="flex:1;">
                    <label>Currency</label>
                    <select name="sport_currency[]" style="width: 100%;">
                        <option value="INR" ${currency === 'INR' ? 'selected' : ''}>INR (?)</option>
                        <option value="USD" ${currency === 'USD' ? 'selected' : ''}>USD ($)</option>
                    </select>
                </div>
                <div style="flex:1;">
                    <label>Individual Price (,1)</label>
                    <input type="text" name="sport_price_individual[]" value="${pInd.replace(/"/g, '&quot;')}" placeholder="Leave blank if N/A">
                </div>
                <div style="flex:1;">
                    <label>Pair Price (,1)</label>
                    <input type="text" name="sport_price_pair[]" value="${pPair.replace(/"/g, '&quot;')}" placeholder="Leave blank if N/A">
                </div>
                <div style="flex:1;">
                    <label>Team Price (,1)</label>
                    <input type="text" name="sport_price_team[]" value="${pTeam.replace(/"/g, '&quot;')}" placeholder="Leave blank if N/A">
                </div>
            </div>
        `;
        return div;
    }

    function addSportItem() {
        sportsContainer.appendChild(createSportBlock());
    }

    existingSports.forEach(s => {
        sportsContainer.appendChild(createSportBlock(s.title, s.icon, s.image || '', s.prize, s.badge, s.categories, s.price_individual, s.price_pair, s.price_team, s.currency, s.prize_currency));
    });

    // Initial schedule data loaded from DB
    const existingSchedules = <?= json_encode($schedules) ?>;
    const container = document.getElementById('schedule-container');

    function createScheduleBlock(day = '', title = '', time = '', desc = '') {
        day = String(day ?? ''); title = String(title ?? ''); time = String(time ?? ''); desc = String(desc ?? '');
        const div = document.createElement('div');
        div.className = 'dynamic-block';
        
        div.innerHTML = `
            <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
                <strong style="color:#c5a85c; font-size: 16px;">Schedule Item</strong>
                <button type="button" style="background: transparent; border: 1px solid #dc3545; color: #dc3545; padding: 4px 10px; border-radius: 4px; font-size: 12px; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#dc3545'; this.style.color='#fff';" onmouseout="this.style.background='transparent'; this.style.color='#dc3545';" onclick="this.parentElement.parentElement.remove()">X Remove</button>
            </div>
            <div class="form-group">
                <label>Day/Date (e.g. Day 1 (6 OCT))</label>
                <input type="text" name="schedule_day[]" value="${day.replace(/"/g, '&quot;')}">
            </div>
            <div class="form-group">
                <label>Event Title (e.g. Opening Ceremony)</label>
                <input type="text" name="schedule_title[]" value="${title.replace(/"/g, '&quot;')}">
            </div>
            <div class="form-group">
                <label>Time (e.g. 6:00 PM - 9:00 PM)</lab                <input type="text" name="schedule_time[]" value="${time.replace(/"/g, '&quot;')}">
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="schedule_desc[]" rows="2">${desc}</textarea>
            </div>
        `;
        return div;
    }

    function addScheduleItem() {
        container.appendChild(createScheduleBlock());
    }

    // Populate existing
    existingSchedules.forEach(s => {
        container.appendChild(createScheduleBlock(s.day, s.title, s.time, s.description));
    });
</script>

<div style="margin-bottom: 100px;">
        <button type="submit" class="btn-gold">Save Event</button>
    </div>
  </form>
</div>



<?php require_once __DIR__ . '/includes/footer.php'; ?>
