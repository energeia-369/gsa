<?php
require_once __DIR__ . '/config/Database.php';
$db = (new Database())->getConnection();

// Handle Destinations CRUD BEFORE any HTML is output
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add_destination') {
        $country = $_POST['country'] ?? '';
        $image = $_POST['image'] ?? '';
        if ($country && $image) {
            $stmt = $db->prepare("INSERT INTO custom_destinations (id, country, image, date, city, region, type) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([time(), $country, $image, date('M Y'), 'Multiple Cities', 'Global', 'Sports']);
        }
    } elseif ($_POST['action'] === 'delete_destination') {
        $id = $_POST['id'] ?? 0;
        if ($id) {
            $stmt = $db->prepare("UPDATE custom_destinations SET is_deleted = 1 WHERE id = ?");
            $stmt->execute([$id]);
        }
    }
    header("Location: admin-tournaments.php");
    exit;
}

$pageTitle = "GLOBAL SPORTS ARENA | System Operations";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

// Fetch active destinations
$destStmt = $db->query("SELECT * FROM custom_destinations WHERE is_deleted = 0 ORDER BY id DESC");
$destinations = $destStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<link rel="stylesheet" href="assets/css/AdminDashboard.css?v=7">

<div class="admin-dashboard px-4 sm:px-8 py-10" style="background: #0b0c10; color: #f5f6fa; min-height: 100vh;">
  <?php require_once __DIR__ . '/includes/admin_navbar.php'; ?>
  <!-- Header -->
  <div class="admin-header" style="border-bottom: 1px solid rgba(197, 168, 92, 0.2); padding-bottom: 20px;">
    <div class="header-left">
      <div class="admin-badge" style="background: linear-gradient(135deg, #c5a85c 0%, #8c7237 100%); color: #0b0c10; fontWeight: bold; display: inline-block; padding: 4px 12px; border-radius: 4px; margin-bottom: 10px;">
        ⚙️ Administrative Core
      </div>
      <h1>System Operations</h1>
      <p>Real-time tournament CRUD controls, NXL ledger wallets adjustments, and synchronized orders listings in MySQL</p>
    </div>
  </div>

  <!-- Loading Overlay -->
  <div id="adminGlobalLoading" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(11,12,16,0.8); z-index: 9999; justify-content: center; align-items: center; color: #c5a85c; font-size: 1.5rem; font-weight: bold;">
    ? Processing request...
  </div>

  <!-- Dynamic KPI Stats Grid -->
  <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 15px; margin-top: 30px;">
    <div class="stat-card" style="background: #12131c; border: 1px solid rgba(197, 168, 92, 0.15); padding: 20px; border-radius: 12px; display: flex; align-items: center; gap: 15px;">
      <div class="stat-icon" style="font-size: 2rem;">📈</div>
      <div class="stat-info">
        <h3 style="font-size: 0.9rem; color: #9aa0b4; margin: 0 0 5px 0;">Live Total Sales</h3>
        <p class="stat-value" id="statTotalSales" style="color: #22c55e; font-size: 1.5rem; font-weight: bold; margin: 0;">?0</p>
        <span class="stat-change" style="font-size: 0.75rem; color: #9aa0b4;">Synchronized DB</span>
      </div>
    </div>

    <div class="stat-card" style="background: #12131c; border: 1px solid rgba(197, 168, 92, 0.15); padding: 20px; border-radius: 12px; display: flex; align-items: center; gap: 15px;">
      <div class="stat-icon" style="font-size: 2rem;">📈</div>
      <div class="stat-info">
        <h3 style="font-size: 0.9rem; color: #9aa0b4; margin: 0 0 5px 0;">Total NXL Issued</h3>
        <p class="stat-value" id="statTotalNxl" style="color: #c5a85c; font-size: 1.5rem; font-weight: bold; margin: 0;">0 Coins</p>
        <span class="stat-change" style="font-size: 0.75rem; color: #9aa0b4;">Loyalty Ledger</span>
      </div>
    </div>

    <div class="stat-card" style="background: #12131c; border: 1px solid rgba(197, 168, 92, 0.15); padding: 20px; border-radius: 12px; display: flex; align-items: center; gap: 15px;">
      <div class="stat-icon" style="font-size: 2rem;">📈</div>
      <div class="stat-info">
        <h3 style="font-size: 0.9rem; color: #9aa0b4; margin: 0 0 5px 0;">Active Customers</h3>
        <p class="stat-value" id="statActiveCustomers" style="font-size: 1.5rem; font-weight: bold; margin: 0;">0 Users</p>
        <span class="stat-change" style="font-size: 0.75rem; color: #9aa0b4;">Logged Profile</span>
      </div>
    </div>

    <div class="stat-card" style="background: #12131c; border: 1px solid rgba(197, 168, 92, 0.15); padding: 20px; border-radius: 12px; display: flex; align-items: center; gap: 15px;">
      <div class="stat-icon" style="font-size: 2rem;">📈</div>
      <div class="stat-info">
        <h3 style="font-size: 0.9rem; color: #9aa0b4; margin: 0 0 5px 0;">Active Merchants</h3>
        <p class="stat-value" id="statMerchants" style="color: #38bdf8; font-size: 1.5rem; font-weight: bold; margin: 0;">0 Merchants</p>
        <span class="stat-change" style="font-size: 0.75rem; color: #9aa0b4;">Registered Partners</span>
      </div>
    </div>
  </div>

  <div class="admin-content" style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 30px; margin-top: 40px;">
    
    <div style='display:flex; flex-direction:column; gap:30px;'>
<!-- Manage Sports Categories -->
      <div class="admin-card" style="background: #12131c; border: 1px solid rgba(197, 168, 92, 0.2); border-radius: 20px; padding: 25px; margin-bottom: 30px;">
        <h2 id="categoryFormTitle" style="color: #c5a85c; margin: 0 0 20px 0;">🏅 Manage Sports Categories</h2>
        
        <form id="categoryForm" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
          <input type="hidden" id="editCategoryId" value="">
          
          <div>
            <label style="display: block; font-size: 0.85rem; color: #9aa0b4; margin-bottom: 5px;">Category Name *</label>
            <input type="text" id="catName" placeholder="e.g. Volleyball" style="width: 100%; padding: 10px; border: 1px solid rgba(197,168,92,0.2); border-radius: 8px; background: #0b0c10; color: #fff; box-sizing: border-box;" required>
          </div>
          
          <div>
            <label style="display: block; font-size: 0.85rem; color: #9aa0b4; margin-bottom: 5px;">Icon (Emoji) *</label>
            <input type="text" id="catIcon" placeholder="e.g. ⚽" style="width: 100%; padding: 10px; border: 1px solid rgba(197,168,92,0.2); border-radius: 8px; background: #0b0c10; color: #fff; box-sizing: border-box;" required>
          </div>
          
          <div style="grid-column: span 2;">
            <label style="display: block; font-size: 0.85rem; color: #9aa0b4; margin-bottom: 5px;">Description</label>
            <input type="text" id="catDesc" placeholder="e.g. Nets, balls, and court accessories" style="width: 100%; padding: 10px; border: 1px solid rgba(197,168,92,0.2); border-radius: 8px; background: #0b0c10; color: #fff; box-sizing: border-box;">
          </div>
          
          <div style="grid-column: span 2; display: flex; align-items: center; gap: 10px;">
            <input type="checkbox" id="catFeatured" style="accent-color: #c5a85c;">
            <label for="catFeatured" style="font-size: 0.85rem; color: #9aa0b4;">Feature this category (Trending)</label>
          </div>
          
          <div style="grid-column: span 2; display: flex; gap: 10px; margin-top: 10px;">
            <button type="submit" id="btnSaveCategory" style="background: linear-gradient(135deg, #c5a85c 0%, #8c7237 100%); color: #0b0c10; border: none; padding: 12px 25px; border-radius: 8px; font-weight: bold; cursor: pointer; flex: 1;">Add Category</button>
            <button type="button" id="btnCancelEditCategory" style="display: none; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.15); color: #fff; padding: 12px 20px; border-radius: 8px; cursor: pointer;">Cancel</button>
          </div>
        </form>
        
        <div id="categoriesListContainer" style="display: grid; gap: 10px; max-height: 250px; overflow-y: auto;">
          <p style="color: #9aa0b4; text-align: center;">Loading categories...</p>
        </div>
      </div>

      
<!-- Tournament event CRUD management form -->
      <div class="admin-card event-form-section" style="display: none; background: #12131c; border: 1px solid rgba(197, 168, 92, 0.2); border-radius: 20px; padding: 25px; margin-bottom: 30px;">
        <h2 id="eventFormTitle" style="color: #c5a85c; margin: 0 0 20px 0;">? Create Sports Tournament</h2>
        
        <form id="tournamentForm" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
          <input type="hidden" id="editEventId" value="">
          <div>
            <label style="display: block; font-size: 0.85rem; color: #9aa0b4; margin-bottom: 5px;">Event Name *</label>
            <input 
              type="text" 
              id="eventName"
              placeholder="e.g. Cricket Pro League"
              style="width: 100%; padding: 10px; border: 1px solid rgba(197,168,92,0.2); border-radius: 8px; background: #0b0c10; color: #fff; box-sizing: border-box;"
              required
            />
          </div>

          <input type="hidden" id="eventSport" value="General">

          <div>
            <label style="display: block; font-size: 0.85rem; color: #9aa0b4; margin-bottom: 5px;">Tournament Date & Time</label>
            <input 
              type="text" 
              id="eventDate"
              placeholder="e.g. June 15, 2026 | 7:00 PM"
              style="width: 100%; padding: 10px; border: 1px solid rgba(197,168,92,0.2); border-radius: 8px; background: #0b0c10; color: #fff; box-sizing: border-box;"
            />
          </div>

          <div>
            <label style="display: block; font-size: 0.85rem; color: #9aa0b4; margin-bottom: 5px;">Complex Venue *</label>
            <input 
              type="text" 
              id="eventVenue"
              placeholder="e.g. National Complex Delhi"
              style="width: 100%; padding: 10px; border: 1px solid rgba(197,168,92,0.2); border-radius: 8px; background: #0b0c10; color: #fff; box-sizing: border-box;"
              required
            />
          </div>

          <div>
            <label style="display: block; font-size: 0.85rem; color: #9aa0b4; margin-bottom: 5px;">Registration Fee (INR) *</label>
            <input 
              type="number" 
              id="eventFee"
              placeholder="999"
              style="width: 100%; padding: 10px; border: 1px solid rgba(197,168,92,0.2); border-radius: 8px; background: #0b0c10; color: #fff; box-sizing: border-box;"
              required
            />
          </div>

          <div style="display: flex; gap: 10px;">
            <div style="flex: 1;">
              <label style="display: block; font-size: 0.85rem; color: #9aa0b4; margin-bottom: 5px;">Max Teams</label>
              <input 
                type="number" 
                id="eventMaxTeams"
                value="16"
                style="width: 100%; padding: 10px; border: 1px solid rgba(197,168,92,0.2); border-radius: 8px; background: #0b0c10; color: #fff; box-sizing: border-box;"
              />
            </div>
            <div style="flex: 1;">
              <label style="display: block; font-size: 0.85rem; color: #9aa0b4; margin-bottom: 5px;">Curr Registered</label>
              <input 
                type="number" 
                id="eventCurrTeams"
                value="0"
                style="width: 100%; padding: 10px; border: 1px solid rgba(197,168,92,0.2); border-radius: 8px; background: #0b0c10; color: #fff; box-sizing: border-box;"
              />
            </div>
          </div>

          <div style="grid-column: span 2; display: flex; gap: 10px; margin-top: 10px;">
            <button 
              type="submit" 
              id="btnSaveTournament"
              style="background: linear-gradient(135deg, #c5a85c 0%, #8c7237 100%); color: #0b0c10; border: none; padding: 12px 25px; border-radius: 8px; font-weight: bold; cursor: pointer; flex: 1;"
            >
              Publish Tournament Live
            </button>

            <button 
              type="button" 
              id="btnCancelEditTournament"
              style="display: none; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.15); color: #fff; padding: 12px 20px; border-radius: 8px; cursor: pointer;"
            >
              Cancel
            </button>
          </div>
        </form>
      </div>

      
<!-- Active Tournament List for editing -->
      <div class="admin-card" style="background: #12131c; border: 1px solid rgba(197, 168, 92, 0.2); border-radius: 20px; padding: 25px; margin-bottom: 30px;">
        <h2 style="color: #c5a85c; margin: 0 0 20px 0;">🏆 Database Tournament Pools</h2>
        
        <div id="tournamentsListContainer" style="display: grid; gap: 10px; max-height: 400px; overflow-y: auto;">
          <p style="color: #9aa0b4; text-align: center;">Loading tournament pools...</p>
        </div>
      </div>

      
<!-- 🌍 GLOBAL DESTINATIONS MANAGER -->
  <style>
      .dest-panel { background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); }
      body.light-theme .dest-panel { background: #f8f9fa; border: 1px solid rgba(0,0,0,0.1); }
      
      .dest-title { color: #fff; }
      body.light-theme .dest-title { color: #1a1a1a; }
      
      .dest-input { width: 100%; padding: 10px; border-radius: 6px; border: 1px solid rgba(197,168,92,0.3); background: rgba(0,0,0,0.2); color: #fff; }
      body.light-theme .dest-input { background: #fff; color: #1a1a1a; border: 1px solid rgba(197,168,92,0.5); }
      
      .dest-item { display: flex; justify-content: space-between; align-items: center; background: rgba(0,0,0,0.2); padding: 10px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.05); }
      body.light-theme .dest-item { background: #fff; border: 1px solid rgba(0,0,0,0.1); }
  </style>
  <div style="display: none; margin-top: 40px;">
    <div class="admin-card" style="border-radius: 20px; padding: 30px; position: relative; overflow: hidden;">
      <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, transparent, #c5a85c, transparent);"></div>

      <h2 style="color: #c5a85c; margin: 0 0 6px 0; font-size: 1.2rem;">
        🌍 Manage Global Event Destinations
      </h2>
      <p style="color: #9aa0b4; font-size: 0.82rem; margin: 0 0 24px 0;">
        Add or remove countries shown in the <strong style="color: #c5a85c;">Global Event Destinations</strong> carousel on the Home page
      </p>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
        
        <!-- Add Form -->
        <div class="dest-panel" style="padding: 20px; border-radius: 12px;">
            <h3 class="dest-title" style="margin-top: 0;">Add New Destination</h3>
            <form method="POST" style="display: flex; flex-direction: column; gap: 15px;">
                <input type="hidden" name="action" value="add_destination">
                <div>
                    <label style="display: block; color: #9aa0b4; margin-bottom: 5px; font-size: 0.9rem;">Country Name</label>
                    <select name="country" class="dest-input" required>
                        <option value="" disabled selected>Select a location...</option>
                        <optgroup label="International">
                            <option value="INDIA">INDIA</option>
                            <option value="SINGAPORE">SINGAPORE</option>
                            <option value="SWITZERLAND">SWITZERLAND</option>
                            <option value="UAE">UAE</option>
                            <option value="THAILAND">THAILAND</option>
                            <option value="USA - LAS VEGAS">USA - LAS VEGAS</option>
                            <option value="USA - NEW YORK">USA - NEW YORK</option>
                            <option value="MALAYSIA">MALAYSIA</option>
                            <option value="INDONESIA">INDONESIA</option>
                            <option value="VIETNAM">VIETNAM</option>
                            <option value="AUSTRALIA">AUSTRALIA</option>
                            <option value="GERMANY">GERMANY</option>
                            <option value="UNITED KINGDOM">UNITED KINGDOM</option>
                            <option value="CANADA">CANADA</option>
                        </optgroup>
                        <optgroup label="National (India)">
                            <option value="TAMIL NADU">TAMIL NADU</option>
                            <option value="PUNE">PUNE</option>
                            <option value="MAHARASHTRA">MAHARASHTRA</option>
                            <option value="KARNATAKA">KARNATAKA</option>
                            <option value="DELHI">DELHI</option>
                            <option value="GOA">GOA</option>
                            <option value="KERALA">KERALA</option>
                            <option value="RAJASTHAN">RAJASTHAN</option>
                            <option value="GUJARAT">GUJARAT</option>
                        </optgroup>
                    </select>
                </div>
                <div>
                    <label style="display: block; color: #9aa0b4; margin-bottom: 5px; font-size: 0.9rem;">Image URL</label>
                    <input type="text" name="image" placeholder="https://..." class="dest-input" required>
                </div>
                <button type="submit" style="background: #c5a85c; color: #000; padding: 10px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">Add Destination</button>
            </form>
        </div>

        <!-- List -->
        <div class="dest-panel" style="padding: 20px; border-radius: 12px; max-height: 300px; overflow-y: auto;">
            <h3 class="dest-title" style="margin-top: 0;">Active Destinations</h3>
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <?php if(empty($destinations)): ?>
                    <p style="color: #9aa0b4;">No destinations found.</p>
                <?php else: ?>
                    <?php foreach($destinations as $dest): ?>
                    <div class="dest-item">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <img src="<?= htmlspecialchars($dest['image']) ?>" style="width: 40px; height: 30px; object-fit: cover; border-radius: 4px;">
                            <strong style="color: #c5a85c;"><?= htmlspecialchars($dest['country']) ?></strong>
                        </div>
                        <form method="POST" style="margin: 0;" onsubmit="return confirm('Remove this destination?');">
                            <input type="hidden" name="action" value="delete_destination">
                            <input type="hidden" name="id" value="<?= $dest['id'] ?>">
                            <button type="submit" style="background: transparent; color: #dc3545; border: none; cursor: pointer;" title="Remove"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

      </div> <!-- /grid -->
    </div> <!-- /card -->
  </div> <!-- /margin-top -->
  </div> <!-- /flex-col -->
  </div> <!-- /admin-content -->
</div> <!-- /admin-dashboard -->
<script>
(function() {
    const container = document.getElementById("tournamentsListContainer");
    <?php
    // Fetch all active events and extract their user-created sports/tournaments
    $allEventsStmt = $db->query("SELECT id, title, slug, sports_data FROM events WHERE status = 'active' OR status = 'draft' ORDER BY id DESC");
    $allEvents = $allEventsStmt->fetchAll(PDO::FETCH_ASSOC);

    $allTournaments = [];
    foreach ($allEvents as $evt) {
        if (!empty($evt['sports_data'])) {
            $sports = json_decode($evt['sports_data'], true);
            if (is_array($sports)) {
                foreach ($sports as $s) {
                    if (!empty($s['title'])) {
                        $allTournaments[] = [
                            'event_title' => $evt['title'],
                            'event_slug'  => $evt['slug'],
                            'name'        => $s['title'],
                            'categories'  => $s['categories'] ?? '',
                            'price_individual' => floatval($s['price_individual'] ?? 0),
                            'price_pair'       => floatval($s['price_pair'] ?? 0),
                            'price_team'       => floatval($s['price_team'] ?? 0),
                            'badge'            => $s['badge'] ?? '',
                            'prize'            => $s['prize'] ?? '',
                        ];
                    }
                }
            }
        }
    }
    ?>

    const tournaments = <?= json_encode($allTournaments, JSON_HEX_TAG) ?>;

    if (tournaments.length === 0) {
        container.innerHTML = `<p style="color: #9aa0b4; text-align: center; padding: 20px;">No tournaments found. Create an event and add sports to it first.</p>`;
        return;
    }

    container.innerHTML = tournaments.map((t, idx) => `
        <div style="background: rgba(0,0,0,0.15); border: 1px solid rgba(197,168,92,0.12); padding: 15px; border-radius: 12px; display: flex; justify-content: space-between; align-items: center; gap: 10px;">
          <div style="flex: 1; min-width: 0;">
            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 4px;">
              <h4 style="margin: 0; color: #c5a85c; font-size: 0.95rem;">${t.name}</h4>
              ${t.badge ? `<span style="background: rgba(197,168,92,0.15); color: #c5a85c; border: 1px solid rgba(197,168,92,0.3); border-radius: 20px; padding: 1px 8px; font-size: 0.7rem;">${t.badge}</span>` : ''}
            </div>
            <div style="font-size: 0.8rem; color: #9aa0b4; margin-bottom: 2px;">
              🎪 Event: <strong style="color: #e0e0e0;">${t.event_title}</strong>
            </div>
            <div style="font-size: 0.78rem; color: #9aa0b4; display: flex; flex-wrap: wrap; gap: 10px;">
              ${t.categories ? `<span>🏅 ${t.categories}</span>` : ''}
              ${t.price_individual > 0 ? `<span>👤 Ind: <strong style="color:#fff;">₹${t.price_individual}</strong></span>` : ''}
              ${t.price_pair > 0 ? `<span>👥 Pair: <strong style="color:#fff;">₹${t.price_pair}</strong></span>` : ''}
              ${t.price_team > 0 ? `<span>🏅 Team: <strong style="color:#fff;">₹${t.price_team}</strong></span>` : ''}
              ${t.prize ? `<span>🏆 Prize: <strong style="color:#c5a85c;">₹${t.prize}</strong></span>` : ''}
            </div>
          </div>
          <a href="admin-event-edit.php?slug=${t.event_slug}" style="background: rgba(197,168,92,0.1); border: 1px solid rgba(197,168,92,0.4); color: #c5a85c; padding: 6px 12px; border-radius: 6px; font-size: 0.78rem; cursor: pointer; white-space: nowrap; text-decoration: none;">✏️ Edit Event</a>
        </div>
    `).join('');
})();
</script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
