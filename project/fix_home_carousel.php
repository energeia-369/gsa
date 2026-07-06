<?php
$targetFile = 'c:\\xampp\\htdocs\\Mithraa_E_Project\\project\\admin-home-carousel-edit.php';
$content = file_get_contents($targetFile);

$marker = "              \$galaPasses = json_decode(\$event['gala_passes_data'], true) ?? [];\r\n          }\r\n";
$pos = strpos($content, $marker);

if ($pos !== false) {
    // We found the marker. The broken stuff starts exactly after this.
    $insertPos = $pos + strlen($marker);
    
    $missingHtml = <<<EOT
          ?>
      </div>
      <button type="button" class="btn-gold" style="font-size: 14px; padding: 6px 15px; margin-bottom: 20px;" onclick="addGalaPass()">+ Add Gala Pass</button>

<script>
    // Initial exhibitor data loaded from DB
    const existingExhibitors = <?= json_encode(\$exhibitors ?? []) ?>;
    const exhibitorsContainer = document.getElementById('exhibitors-container');

    function createExhibitorBlock(title = '', icon = 'fa-store', size = '', desc = '', price = '', badge = '', currency = 'INR') {

EOT;

    $content = substr_replace($content, $missingHtml, $insertPos, 0);
    file_put_contents($targetFile, $content);
    echo "Fixed via strpos!\n";
} else {
    // Try without \r
    $marker = "              \$galaPasses = json_decode(\$event['gala_passes_data'], true) ?? [];\n          }\n";
    $pos = strpos($content, $marker);
    if ($pos !== false) {
        $insertPos = $pos + strlen($marker);
        $missingHtml = <<<EOT
          ?>
      </div>
      <button type="button" class="btn-gold" style="font-size: 14px; padding: 6px 15px; margin-bottom: 20px;" onclick="addGalaPass()">+ Add Gala Pass</button>

<script>
    // Initial exhibitor data loaded from DB
    const existingExhibitors = <?= json_encode(\$exhibitors ?? []) ?>;
    const exhibitorsContainer = document.getElementById('exhibitors-container');

    function createExhibitorBlock(title = '', icon = 'fa-store', size = '', desc = '', price = '', badge = '', currency = 'INR') {

EOT;
        $content = substr_replace($content, $missingHtml, $insertPos, 0);
        file_put_contents($targetFile, $content);
        echo "Fixed via strpos (\\n)!\n";
    } else {
        echo "Marker not found!\n";
    }
}
?>
