<?php
$files = glob("admin-*.php");
$count = 0;
foreach ($files as $file) {
    $content = file_get_contents($file);
    if (strpos($content, 'padding: 40px 10%;') !== false) {
        $content = str_replace('padding: 40px 10%;', 'padding: 40px 5%;', $content);
        file_put_contents($file, $content);
        echo "Updated $file\n";
        $count++;
    }
}
echo "Total files updated: $count\n";
?>
