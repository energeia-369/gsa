<?php
$source = file_get_contents('c:\\xampp\\htdocs\\Mithraa_E_Project\\project\\admin-event-edit.php');

// Extract the dynamic tabs navigation
preg_match('/<div class="tabs">(.*?)<\/div>/s', $source, $tabsMatch);
$tabsHTML = $tabsMatch ? $tabsMatch[0] : '';

// Extract the dynamic tab contents
preg_match('/<!-- Dynamic Repeater Fields -->(.*?)<!-- SEO /s', $source, $contentMatch);
$contentHTML = $contentMatch ? '<!-- Dynamic Repeater Fields -->' . $contentMatch[1] : '';

// Extract JS
preg_match('/<script>(.*?)<\/script>/s', $source, $jsMatch); // Need to be careful to extract the right JS block
file_put_contents('tabs.html', $tabsHTML);
file_put_contents('content.html', $contentHTML);
echo "Extracted length: " . strlen($contentHTML) . "\n";
?>
