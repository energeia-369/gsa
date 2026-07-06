<?php
$file = 'c:\\xampp\\htdocs\\Mithraa_E_Project\\project\\admin-home-carousel-edit.php';
$content = file_get_contents($file);

$new_post_logic = <<<'PHP'
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
PHP;

$content = str_replace(
    'if ($id > 0) {',
    $new_post_logic . "\n" . '    if ($id > 0) {',
    $content
);

$content = str_replace(
    '$seo_title, $seo_desc, $seo_keywords, $slug',
    '$seo_title, $seo_desc, $seo_keywords, $slug, $end_date, $timer_start_date, $gala_title, $gala_venue, $gala_date, $gala_time, $gala_description, $custom_html, $delegate_fee, $delegate_currency, $schedule_data, $sports_data, $sponsors_data, $exhibitor_data, $locations_data, $gala_passes_data',
    $content
);

$update_query_search = 'title=?, subtitle=?, short_desc=?, description=?, category=?, country=?, state=?,
            hero_banner=?, carousel_img=?, mobile_banner=?, btn_text=?, btn_url=?,
            event_date=?, display_order=?, is_featured=?, status=?,
            seo_title=?, seo_desc=?, seo_keywords=?, slug=?';
$update_query_replace = 'title=?, subtitle=?, short_desc=?, description=?, category=?, country=?, state=?,
            hero_banner=?, carousel_img=?, mobile_banner=?, btn_text=?, btn_url=?,
            event_date=?, display_order=?, is_featured=?, status=?,
            seo_title=?, seo_desc=?, seo_keywords=?, slug=?,
            end_date=?, timer_start_date=?, gala_title=?, gala_venue=?, gala_date=?, gala_time=?, gala_description=?, custom_html=?, delegate_fee=?, delegate_currency=?, schedule_data=?, sports_data=?, sponsors_data=?, exhibitor_data=?, locations_data=?, gala_passes_data=?';
$content = str_replace($update_query_search, $update_query_replace, $content);

$insert_cols_search = 'title, subtitle, short_desc, description, category, country, state,
            hero_banner, carousel_img, mobile_banner, btn_text, btn_url,
            event_date, display_order, is_featured, status,
            seo_title, seo_desc, seo_keywords, slug';
$insert_cols_replace = 'title, subtitle, short_desc, description, category, country, state,
            hero_banner, carousel_img, mobile_banner, btn_text, btn_url,
            event_date, display_order, is_featured, status,
            seo_title, seo_desc, seo_keywords, slug,
            end_date, timer_start_date, gala_title, gala_venue, gala_date, gala_time, gala_description, custom_html, delegate_fee, delegate_currency, schedule_data, sports_data, sponsors_data, exhibitor_data, locations_data, gala_passes_data';
$content = str_replace($insert_cols_search, $insert_cols_replace, $content);

$insert_vals_search = '?, ?, ?, ?, ?, ?, ?,
            ?, ?, ?, ?, ?,
            ?, ?, ?, ?,
            ?, ?, ?, ?';
$insert_vals_replace = '?, ?, ?, ?, ?, ?, ?,
            ?, ?, ?, ?, ?,
            ?, ?, ?, ?,
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?';
$content = str_replace($insert_vals_search, $insert_vals_replace, $content);

file_put_contents($file, $content);
echo "PHP Logic Updated.";
?>
