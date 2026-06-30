<?php
require_once __DIR__ . '/config/Database.php';

$db = (new Database())->getConnection();

// -- Create tables if they don't exist --------------------------------
$db->exec("CREATE TABLE IF NOT EXISTS admin_partners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    logo_url VARCHAR(500),
    website_url VARCHAR(500),
    tag VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$db->exec("CREATE TABLE IF NOT EXISTS admin_sponsors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(255) NOT NULL,
    tier ENUM('Title','Platinum','Gold','Silver','Bronze') DEFAULT 'Gold',
    logo_url VARCHAR(500),
    website_url VARCHAR(500),
    description TEXT,
    event_name VARCHAR(255),
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// -- Seed default partners if table is empty ---------------------------
$count = $db->query("SELECT COUNT(*) FROM admin_partners")->fetchColumn();
if ($count == 0) {
    $defaults = [
        ['TATA GROUP',  '',  'https://www.tata.com',          'Conglomerate'],
        ['INFOSYS',     '',  'https://www.infosys.com',        'Technology'],
        ['HDFC BANK',   '',  'https://www.hdfcbank.com',       'Banking'],
        ['GOOGLE',      '',  'https://www.google.com',         'Technology'],
        ['BOOKMYSHOW',  '',  'https://in.bookmyshow.com',      'Entertainment'],
        ['DECATHLON',   '',  'https://www.decathlon.in',       'Sports & Retail'],
        ['KRAFTON',     '',  '#',                              'Gaming'],
    ];
    $ins = $db->prepare("INSERT INTO admin_partners (name, logo_url, website_url, tag) VALUES (?,?,?,?)");
    foreach ($defaults as $d) $ins->execute($d);
}



// -- Handle POST actions BEFORE any HTML ------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'add_partner') {
        $name    = trim($_POST['name'] ?? '');
        $logo    = trim($_POST['logo_url'] ?? '');
        $website = trim($_POST['website_url'] ?? '');
        $tag     = trim($_POST['tag'] ?? '');
        if ($name) {
            $stmt = $db->prepare("INSERT INTO admin_partners (name, logo_url, website_url, tag) VALUES (?,?,?,?)");
            $stmt->execute([$name, $logo, $website, $tag]);
        }
    } elseif ($action === 'edit_partner') {
        $id      = intval($_POST['id'] ?? 0);
        $name    = trim($_POST['name'] ?? '');
        $logo    = trim($_POST['logo_url'] ?? '');
        $website = trim($_POST['website_url'] ?? '');
        $tag     = trim($_POST['tag'] ?? '');
        if ($id && $name) {
            $stmt = $db->prepare("UPDATE admin_partners SET name=?, logo_url=?, website_url=?, tag=? WHERE id=?");
            $stmt->execute([$name, $logo, $website, $tag, $id]);
        }
    } elseif ($action === 'delete_partner') {
        $id = intval($_POST['id'] ?? 0);
        if ($id) $db->prepare("DELETE FROM admin_partners WHERE id=?")->execute([$id]);
    } elseif ($action === 'add_sponsor') {
        $company  = trim($_POST['company_name'] ?? '');
        $tier     = $_POST['tier'] ?? 'Gold';
        $logo     = trim($_POST['logo_url'] ?? '');
        $website  = trim($_POST['website_url'] ?? '');
        $desc     = trim($_POST['description'] ?? '');
        $events   = $_POST['event_names'] ?? [''];
        
        if ($company) {
            $stmt = $db->prepare("INSERT INTO admin_sponsors (company_name, tier, logo_url, website_url, description, event_name) VALUES (?,?,?,?,?,?)");
            // If empty/global is part of selection alongside others, handle appropriately
            foreach ($events as $event) {
                $stmt->execute([$company, $tier, $logo, $website, $desc, $event ?: null]);
            }
        }
    } elseif ($action === 'delete_sponsor') {
        $id = intval($_POST['id'] ?? 0);
        if ($id) $db->prepare("DELETE FROM admin_sponsors WHERE id=?")->execute([$id]);
    } elseif ($action === 'toggle_sponsor') {
        $id = intval($_POST['id'] ?? 0);
        if ($id) $db->prepare("UPDATE admin_sponsors SET status = IF(status='active','inactive','active') WHERE id=?")->execute([$id]);
    }

    header("Location: admin-partners-sponsors.php");
    exit;
}

// -- Fetch data --------------------------------------------------------
$partners = $db->query("SELECT * FROM admin_partners ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
$sponsors = $db->query("SELECT * FROM admin_sponsors ORDER BY FIELD(tier,'Title','Platinum','Gold','Silver','Bronze'), created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "GLOBAL SPORTS ARENA | Partners & Sponsors";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<link rel="stylesheet" href="assets/css/AdminDashboard.css?v=7">
<style>
  .ps-input {
    width: 100%; padding: 10px 14px; border-radius: 8px;
    border: 1px solid rgba(197,168,92,0.3);
    background: rgba(0,0,0,0.25); color: #f5f6fa;
    font-size: 0.9rem; box-sizing: border-box; outline: none;
    transition: border-color 0.2s;
  }
  .ps-input:focus { border-color: #c5a85c; }
  body.light-theme .ps-input { background: #fff; color: #1a1a1a; border-color: rgba(197,168,92,0.5); }

  .ps-label { display: block; font-size: 0.82rem; color: #9aa0b4; margin-bottom: 5px; }
  body.light-theme .ps-label { color: #555; }

  .ps-btn-gold {
    width: 100%; padding: 12px; border: none; border-radius: 8px;
    background: linear-gradient(135deg, #c5a85c, #8c7237);
    color: #0b0c10; font-weight: bold; font-size: 0.9rem; cursor: pointer;
    transition: opacity 0.2s;
  }
  .ps-btn-gold:hover { opacity: 0.88; }

  .ps-card-inner {
    background: rgba(255,255,255,0.04); border: 1px solid rgba(197,168,92,0.15);
    border-radius: 12px; padding: 16px 18px; display: flex; justify-content: space-between;
    align-items: center; gap: 12px;
    transition: border-color 0.2s;
  }
  .ps-card-inner:hover { border-color: rgba(197,168,92,0.4); }
  body.light-theme .ps-card-inner {
    background: #fff;
    border-color: rgba(197,168,92,0.25);
    box-shadow: 0 1px 6px rgba(0,0,0,0.06);
  }

  .ps-card-name {
    font-weight: 700; font-size: 0.9rem;
    color: #f5f6fa;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
  }
  body.light-theme .ps-card-name { color: #1a1a1a; }

  .ps-card-inner img { width: 50px; height: 40px; object-fit: contain; border-radius: 4px; background: #fff; padding: 2px; }

  .ps-del-btn {
    background: rgba(220,38,38,0.15); border: 1px solid rgba(220,38,38,0.5);
    color: #f87171; padding: 6px 12px; border-radius: 6px;
    font-size: 0.8rem; cursor: pointer; white-space: nowrap;
    transition: background 0.2s;
  }
  .ps-del-btn:hover { background: rgba(220,38,38,0.3); }
  body.light-theme .ps-del-btn { color: #dc2626; border-color: #dc2626; }

  .ps-edit-btn {
    background: rgba(197,168,92,0.12); border: 1px solid rgba(197,168,92,0.4);
    color: #c5a85c; padding: 6px 12px; border-radius: 6px;
    font-size: 0.8rem; cursor: pointer; white-space: nowrap;
    transition: background 0.2s;
  }
  .ps-edit-btn:hover { background: rgba(197,168,92,0.25); }

  .tier-badge {
    font-size: 0.7rem; font-weight: bold; padding: 2px 8px; border-radius: 20px; letter-spacing: 0.5px;
  }
  .tier-Title    { background: rgba(220,38,38,0.2);  color: #f87171; border: 1px solid #dc2626; }
  .tier-Platinum { background: rgba(200,200,220,0.15); color: #c8c8dc; border: 1px solid #a0a0c0; }
  .tier-Gold     { background: rgba(197,168,92,0.15); color: #c5a85c; border: 1px solid rgba(197,168,92,0.5); }
  .tier-Silver   { background: rgba(180,180,180,0.15); color: #b0b0b0; border: 1px solid #909090; }
  .tier-Bronze   { background: rgba(180,100,40,0.15); color: #cd7f32; border: 1px solid #a0602a; }

  .ps-grid-form { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
  @media(max-width:640px) { .ps-grid-form { grid-template-columns: 1fr; } }
  .ps-form-group { display: flex; flex-direction: column; gap: 5px; }
  .ps-full { grid-column: 1 / -1; }
  .ps-list { display: flex; flex-direction: column; gap: 10px; max-height: 380px; overflow-y: auto; padding-right: 4px; }
  .ps-empty { color: #9aa0b4; text-align: center; padding: 20px 0; font-size: 0.9rem; }
  body.light-theme .ps-empty { color: #888; }
</style>

<div class="admin-dashboard px-4 sm:px-8 py-10" style="background: #0b0c10; color: #f5f6fa; min-height: 100vh;">
  <?php require_once __DIR__ . '/includes/admin_navbar.php'; ?>

  <div class="admin-header" style="border-bottom: 1px solid rgba(197,168,92,0.2); padding-bottom: 20px;">
    <div class="header-left">
      <div class="admin-badge" style="background: linear-gradient(135deg,#c5a85c 0%,#8c7237 100%); color:#0b0c10; display:inline-block; padding:4px 12px; border-radius:4px; margin-bottom:10px;">
        ⚙️ Administrative Core
      </div>
      <h1>Partners &amp; Sponsors</h1>
      <p>Manage companies shown in the home page partner carousel and event sponsorship opportunities.</p>
    </div>
  </div>

  <div style="display:flex; flex-direction:column; gap:30px; margin-top:40px;">

    <!-- ----------------------------------------------- -->
    <!-- ?? MANAGE GLOBAL PARTNERS                      -->
    <!-- ----------------------------------------------- -->
    <div class="admin-card" style="border-radius:20px; padding:30px; position:relative; overflow:hidden;">
      <div style="position:absolute; top:0; left:0; right:0; height:3px; background:linear-gradient(90deg,transparent,#c5a85c,transparent);"></div>

      <h2 style="color:#c5a85c; margin:0 0 6px; font-size:1.2rem;">🤝 Manage Global Partners</h2>
      <p style="color:#9aa0b4; font-size:0.82rem; margin:0 0 24px;">
        Add or remove companies shown in the <strong style="color:#c5a85c;">Our Partners</strong> carousel on the Home page
      </p>

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:30px;">

        <!-- Add / Edit Partner Form -->
        <div>
          <h3 id="partnerFormTitle" style="color:#fff; font-size:1rem; margin:0 0 16px;">? Add New Partner</h3>
          <form method="POST" id="partnerForm" style="display:flex; flex-direction:column; gap:14px;">
            <input type="hidden" name="action" id="partnerAction" value="add_partner">
            <input type="hidden" name="id"     id="partnerEditId" value="">

            <div class="ps-form-group">
              <label class="ps-label">Company Name *</label>
              <input type="text" name="name" id="partnerName" class="ps-input" placeholder="e.g. Nike" required>
            </div>
            <div class="ps-form-group">
              <label class="ps-label">Logo URL</label>
              <input type="text" name="logo_url" id="partnerLogo" class="ps-input" placeholder="https://...logo.png">
            </div>
            <div class="ps-form-group">
              <label class="ps-label">Website URL</label>
              <input type="text" name="website_url" id="partnerWebsite" class="ps-input" placeholder="https://www.example.com">
            </div>
            <div class="ps-form-group">
              <label class="ps-label">Tag / Category</label>
              <input type="text" name="tag" id="partnerTag" class="ps-input" placeholder="e.g. Apparel, Tech, Media">
            </div>
            <div style="display:flex; gap:10px;">
              <button type="submit" id="partnerSubmitBtn" class="ps-btn-gold" style="flex:1;">Add Partner</button>
              <button type="button" id="partnerCancelBtn" onclick="resetPartnerForm()" style="display:none; padding:12px 16px; border-radius:8px; background:transparent; border:1px solid #9aa0b4; color:#9aa0b4; cursor:pointer; font-size:0.85rem;">Cancel</button>
            </div>
          </form>
        </div>

        <!-- Active Partners List -->
        <div>
          <h3 style="color:var(--admin-text,#fff); font-size:1rem; margin:0 0 16px;">✅ Active Partners (<?= count($partners) ?>)</h3>
          <?php
          $emojiMap = [
              'TATA GROUP'  => '🏢',
              'INFOSYS'     => '💻',
              'HDFC BANK'   => '🏦',
              'GOOGLE'      => '🔍',
              'BOOKMYSHOW'  => '🎟️',
              'DECATHLON'   => '🏅',
              'KRAFTON'     => '🎮',
          ];
          ?>
          <div class="ps-list">
            <?php if (empty($partners)): ?>
              <p class="ps-empty">No partners yet. Add one using the form.</p>
            <?php else: ?>
              <?php foreach ($partners as $p): ?>
              <div class="ps-card-inner">
                <div style="display:flex; align-items:center; gap:12px; flex:1; min-width:0;">
                  <?php
                    $emoji = $p['logo_url'] ? null : ($emojiMap[strtoupper(trim($p['name']))] ?? '🏢');
                  ?>
                  <?php if ($p['logo_url']): ?>
                    <img src="<?= htmlspecialchars($p['logo_url']) ?>" alt="logo" style="width:50px;height:40px;object-fit:contain;border-radius:4px;background:#fff;padding:4px;">
                  <?php else: ?>
                    <div style="width:50px;height:40px;background:rgba(197,168,92,0.12);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;flex-shrink:0;"><?= $emoji ?></div>
                  <?php endif; ?>
                  <div style="min-width:0;">
                    <div class="ps-card-name">
                      <?= htmlspecialchars(strtoupper($p['name'])) ?>
                    </div>
                    <?php if ($p['tag']): ?>
                      <div style="font-size:0.75rem; color:#c5a85c; margin-top:2px; font-weight:600;"><?= htmlspecialchars($p['tag']) ?></div>
                    <?php endif; ?>
                    <?php if ($p['website_url'] && $p['website_url'] !== '#'): ?>
                      <div style="font-size:0.7rem; color:#9aa0b4; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:160px;">🌐 <?= htmlspecialchars($p['website_url']) ?></div>
                    <?php endif; ?>
                  </div>
                </div>
                <div style="display:flex; gap:6px; flex-shrink:0;">
                  <button type="button"
                    onclick="editPartner(<?= $p['id'] ?>, '<?= addslashes($p['name']) ?>', '<?= addslashes($p['logo_url']) ?>', '<?= addslashes($p['website_url']) ?>', '<?= addslashes($p['tag']) ?>')"
                    class="ps-edit-btn" title="Edit">✏️</button>
                  <form method="POST" onsubmit="return confirm('Remove this partner?');" style="margin:0;">
                    <input type="hidden" name="action" value="delete_partner">
                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                    <button type="submit" class="ps-del-btn" title="Remove"><i class="fas fa-trash"></i></button>
                  </form>
                </div>
              </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>

      </div>
    </div>

    <!-- ----------------------------------------------- -->
    <!-- ?? MANAGE SPONSORSHIP OPPORTUNITIES            -->
    <!-- ----------------------------------------------- -->
    <div class="admin-card" style="border-radius:20px; padding:30px; position:relative; overflow:hidden;">
      <div style="position:absolute; top:0; left:0; right:0; height:3px; background:linear-gradient(90deg,transparent,#c5a85c,transparent);"></div>

      <h2 style="color:#c5a85c; margin:0 0 6px; font-size:1.2rem;">💼 Manage Sponsorship Opportunities</h2>
      <p style="color:#9aa0b4; font-size:0.82rem; margin:0 0 24px;">
        Add companies sponsoring your events. Their logos and tiers can be shown on event pages.
      </p>

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:30px;">

        <!-- Add Sponsor Form -->
        <div>
          <h3 style="color:#fff; font-size:1rem; margin:0 0 16px;">? Add New Sponsor</h3>
          <form method="POST" style="display:flex; flex-direction:column; gap:14px;">
            <input type="hidden" name="action" value="add_sponsor">

            <div class="ps-form-group">
              <label class="ps-label">Company Name *</label>
              <input type="text" name="company_name" class="ps-input" placeholder="e.g. Adidas" required>
            </div>
            <div class="ps-form-group">
              <label class="ps-label">Sponsorship Tier *</label>
              <select name="tier" class="ps-input">
                <option value="Title">🥇 Title Sponsor</option>
                <option value="Platinum">? Platinum</option>
                <option value="Gold" selected>🥇 Gold</option>
                <option value="Silver">🥈 Silver</option>
                <option value="Bronze">🥉 Bronze</option>
              </select>
            </div>
            <div class="ps-form-group">
              <label class="ps-label">Logo URL</label>
              <input type="text" name="logo_url" class="ps-input" placeholder="https://...logo.png">
            </div>
            <div class="ps-form-group">
              <label class="ps-label">Website URL</label>
              <input type="text" name="website_url" class="ps-input" placeholder="https://www.example.com">
            </div>
            <div class="ps-form-group">
              <label class="ps-label">Associated Events (Hold Ctrl/Cmd to select multiple)</label>
              <select name="event_names[]" class="ps-input" multiple style="height: 110px;">
                <option value="" selected>� All Events / Global �</option>
                <?php
                $evts = $db->query("SELECT title FROM events ORDER BY title")->fetchAll(PDO::FETCH_COLUMN);
                foreach ($evts as $evtTitle) {
                    echo '<option value="' . htmlspecialchars($evtTitle) . '">' . htmlspecialchars($evtTitle) . '</option>';
                }
                ?>
              </select>
            </div>
            <div class="ps-form-group">
              <label class="ps-label">Description (optional)</label>
              <textarea name="description" class="ps-input" rows="3" placeholder="Brief note about this sponsorship..."></textarea>
            </div>
            <button type="submit" class="ps-btn-gold">Add Sponsor</button>
          </form>
        </div>

        <!-- Active Sponsors List -->
        <div>
          <h3 style="color:#fff; font-size:1rem; margin:0 0 16px;">🤝 Active Sponsors (<?= count($sponsors) ?>)</h3>
          <div class="ps-list">
            <?php if (empty($sponsors)): ?>
              <p class="ps-empty">No sponsors yet. Add one using the form.</p>
            <?php else: ?>
              <?php foreach ($sponsors as $s): ?>
              <div class="ps-card-inner" style="<?= $s['status'] === 'inactive' ? 'opacity:0.55;' : '' ?>">
                <div style="display:flex; align-items:center; gap:12px; flex:1; min-width:0;">
                  <?php if ($s['logo_url']): ?>
                    <img src="<?= htmlspecialchars($s['logo_url']) ?>" alt="logo">
                  <?php else: ?>
                    <div style="width:50px;height:40px;background:rgba(197,168,92,0.1);border-radius:4px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;">??</div>
                  <?php endif; ?>
                  <div style="min-width:0;">
                    <div style="display:flex; align-items:center; gap:6px; margin-bottom:3px;">
                      <span style="font-weight:700; color:#f5f6fa; font-size:0.88rem;"><?= htmlspecialchars($s['company_name']) ?></span>
                      <span class="tier-badge tier-<?= $s['tier'] ?>"><?= $s['tier'] ?></span>
                    </div>
                    <?php if ($s['event_name']): ?>
                      <div style="font-size:0.72rem; color:#c5a85c;">?? <?= htmlspecialchars($s['event_name']) ?></div>
                    <?php else: ?>
                      <div style="font-size:0.72rem; color:#9aa0b4;">🌍 Global Sponsor</div>
                    <?php endif; ?>
                  </div>
                </div>
                <div style="display:flex; gap:6px; align-items:center;">
                  <form method="POST" style="margin:0;" title="Toggle active/inactive">
                    <input type="hidden" name="action" value="toggle_sponsor">
                    <input type="hidden" name="id" value="<?= $s['id'] ?>">
                    <button type="submit" class="ps-edit-btn"><?= $s['status'] === 'active' ? '?' : '?' ?></button>
                  </form>
                  <form method="POST" onsubmit="return confirm('Remove this sponsor?');" style="margin:0;">
                    <input type="hidden" name="action" value="delete_sponsor">
                    <input type="hidden" name="id" value="<?= $s['id'] ?>">
                    <button type="submit" class="ps-del-btn" title="Remove"><i class="fas fa-trash"></i></button>
                  </form>
                </div>
              </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>

      </div>
    </div>

  </div><!-- /flex column -->
</div><!-- /admin-dashboard -->

<script>
function editPartner(id, name, logo, website, tag) {
    document.getElementById('partnerFormTitle').textContent = '✏️ Edit Partner';
    document.getElementById('partnerAction').value    = 'edit_partner';
    document.getElementById('partnerEditId').value    = id;
    document.getElementById('partnerName').value      = name;
    document.getElementById('partnerLogo').value      = logo;
    document.getElementById('partnerWebsite').value   = website;
    document.getElementById('partnerTag').value       = tag;
    document.getElementById('partnerSubmitBtn').textContent = 'Save Changes';
    document.getElementById('partnerCancelBtn').style.display = 'inline-block';
    document.getElementById('partnerForm').scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function resetPartnerForm() {
    document.getElementById('partnerFormTitle').textContent = '? Add New Partner';
    document.getElementById('partnerAction').value    = 'add_partner';
    document.getElementById('partnerEditId').value    = '';
    document.getElementById('partnerForm').reset();
    document.getElementById('partnerSubmitBtn').textContent = 'Add Partner';
    document.getElementById('partnerCancelBtn').style.display = 'none';
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
