<?php
require 'config/Database.php';
$db = (new Database())->getConnection();

$stmt = $db->prepare("SELECT custom_html FROM home_carousel_events WHERE slug='gsa-thailand-2026'");
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$html = $row['custom_html'];

// Fix Day 6
$html = str_replace('<tr style="color: #444;">
<td style="padding: 15px; font-weight: 600;">Day 6</td>', '<tr style="border-bottom: 1px solid rgba(197,168,92,0.2); color: #444;">
<td style="padding: 15px; font-weight: 600;">Day 6</td>', $html);

// Fix Day 7
$html = str_replace('<tr>
<td style="padding: 15px; font-weight: 600;">Day 7</td>', '<tr style="border-bottom: 1px solid rgba(197,168,92,0.2); color: #444;">
<td style="padding: 15px; font-weight: 600;">Day 7</td>', $html);

// Fix Day 8
$html = str_replace('<tr>
<td style="padding-top: 15px; padding-right: 15px; padding-bottom: 15px; font-weight: 600;">&nbsp;Day 8&nbsp;</td>', '<tr style="border-bottom: 1px solid rgba(197,168,92,0.2); color: #444;">
<td style="padding: 15px; font-weight: 600;">Day 8</td>', $html);

$updateStmt = $db->prepare("UPDATE home_carousel_events SET custom_html = :html WHERE slug='gsa-thailand-2026'");
$updateStmt->bindParam(':html', $html);
if ($updateStmt->execute()) {
    echo "Successfully updated custom_html for gsa-thailand-2026";
} else {
    echo "Failed to update.";
}
