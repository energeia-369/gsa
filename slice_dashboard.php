<?php
$content = file_get_contents('project/admin-dashboard.php');

function extractBlock($content, $startComment, $endComment) {
    $start = strpos($content, $startComment);
    if ($start === false) return '';
    if ($endComment) {
        $end = strpos($content, $endComment, $start);
        if ($end === false) return '';
        return substr($content, $start, $end - $start);
    } else {
        // If no end comment, we assume it ends at the next block or something
        return '';
    }
}

// Just outputting the positions of the comments to verify they exist
$comments = [
    '<!-- Manage Sports Categories -->',
    '<!-- Security Settings -->',
    '<!-- Tournament event CRUD management form -->',
    '<!-- Active Tournament List for editing -->',
    '<!-- Gallery Photo Upload & Manage Section -->',
    '<!-- Manage Products Form -->',
    '<!-- Dynamic User Order Purchases Table -->',
    '<!-- Manage E.V.A. Chatbot -->',
    '<!-- Newsletter Subscribers -->',
    '<!-- Manage Page Reviews -->',
    '<!-- Manage Global Partners -->',
    '<!-- Manage Sponsorship Opportunities -->',
    '<!-- Media Hub / Video Manager -->'
];

foreach ($comments as $c) {
    echo $c . " : " . (strpos($content, $c) !== false ? "FOUND" : "NOT FOUND") . "\n";
}
?>
