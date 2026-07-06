<?php
$sourceFile = 'c:\\xampp\\htdocs\\Mithraa_E_Project\\project\\admin-event-edit.php';
$targetFile = 'c:\\xampp\\htdocs\\Mithraa_E_Project\\project\\admin-home-carousel-edit.php';

$sourceLines = file($sourceFile);
$targetContent = file_get_contents($targetFile);

// Extract lines 405 to 983 (array indices 404 to 982)
$extractedLines = array_slice($sourceLines, 404, 983 - 404 + 1);
$repeaterHtml = implode("", $extractedLines);

// Clean up event-specific delete block
$deleteBlockStart = strpos($repeaterHtml, '<?php if ($eventId): ?>');
if ($deleteBlockStart !== false) {
    $deleteBlockEnd = strpos($repeaterHtml, '<?php endif; ?>', $deleteBlockStart) + strlen('<?php endif; ?>');
    $repeaterHtml = substr_replace($repeaterHtml, '', $deleteBlockStart, $deleteBlockEnd - $deleteBlockStart);
}

// Clean up Save Button (we have one already in target)
$repeaterHtml = preg_replace('/<div class="form-group">\s*<button type="submit" class="btn-gold">Save Event Data<\/button>\s*<\/div>/s', '', $repeaterHtml);

// Insert into target before <div style="margin-bottom: 100px;">
$targetMarker = '<div style="margin-bottom: 100px;">';

if (strpos($targetContent, $targetMarker) !== false) {
    $targetContent = str_replace($targetMarker, $repeaterHtml . "\n" . $targetMarker, $targetContent);
    file_put_contents($targetFile, $targetContent);
    echo "HTML logic copied successfully using line numbers.\n";
} else {
    echo "Could not find target marker.\n";
}
?>
