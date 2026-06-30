<?php
$file = 'project/admin-dashboard.php';
$content = file_get_contents($file);

$leftColStart = strpos($content, '<!-- LEFT COLUMN');
$headerAndStats = substr($content, 0, $leftColStart);

$rest = substr($content, $leftColStart);
// Split by "<!-- "
$blocks = explode('<!-- ', $rest);

$cards = [];
foreach ($blocks as $block) {
    if (trim($block) === '') continue;
    $block = '<!-- ' . $block; // put it back
    if (preg_match('/<h2.*?>(.*?)<\/h2>/is', $block, $h2)) {
        $title = strip_tags($h2[1]);
        $cards[trim(preg_replace('/[^a-zA-Z\s]/', '', $title))] = $block;
    }
}

foreach (array_keys($cards) as $k) {
    echo "Found block: $k\n";
}

$groups = [
    'admin-tournaments.php' => ['Manage Sports Categories', 'Create Sports Tournament', 'Database Tournament Pools', 'Manage Global Event Destinations'],
    'admin-store-operations.php' => ['Manage Products', 'Dynamic User Order Purchases'],
    'admin-media-gallery.php' => ['Manage Gallery Photos', 'Media Hub  Video Manager'],
    'admin-partners-sponsors.php' => ['Manage Global Partners', 'Manage Sponsorship Opportunities'],
    'admin-site-settings.php' => ['Security Settings', 'EVA Chatbot FAQs', 'Newsletter Subscribers', 'Manage Page Reviews']
];

$footer = "</div>\n</div>\n</div>\n<?php require_once __DIR__ . '/includes/footer.php'; ?>\n";

foreach ($groups as $page => $titles) {
    $pageHtml = $headerAndStats;
    $pageHtml .= "<div style='display:flex; flex-direction:column; gap:30px;'>\n";
    foreach ($titles as $t) {
        $found = false;
        foreach ($cards as $k => $html) {
            if (stripos($k, $t) !== false || stripos($t, $k) !== false) {
                // Remove trailing tags that belong to the global layout
                $html = preg_replace('/<\/div>\s*<\/div>\s*<\/div>\s*<\?php require_once.*?$/is', '', $html);
                $html = preg_replace('/<!-- RIGHT COLUMN -->/is', '', $html);
                $pageHtml .= $html . "\n";
                $found = true;
                break;
            }
        }
        if (!$found) echo "Warning: Title not found for $t\n";
    }
    $pageHtml .= $footer;
    file_put_contents('project/' . $page, $pageHtml);
    echo "Created $page\n";
}

$newDashboard = $headerAndStats . "
    <div style='padding: 20px; color: #c5a85c; text-align: center; border: 1px solid rgba(197, 168, 92, 0.2); border-radius: 20px; background: #12131c;'>
        <h2>Select an option from the 'Site Content & Operations' sidebar menu to manage your site.</h2>
    </div>
" . $footer;

file_put_contents('project/admin-dashboard.php', $newDashboard);
echo "Updated admin-dashboard.php\n";
?>
