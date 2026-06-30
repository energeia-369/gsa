<?php
$pageTitle = "GLOBAL SPORTS ARENA | Award Gala Registrations";
require_once __DIR__ . '/config/Database.php';

$db = Database::getConnection();

// Handle Mark Checked In from admin
if ($_POST['action'] ?? '' === 'checkin' && !empty($_POST['reg_id'])) {
    $upd = $db->prepare("UPDATE award_registrations SET entry_status='Checked In', checked_in_at=NOW() WHERE id=?");
    $upd->execute([$_POST['reg_id']]);
    $logStmt = $db->prepare("INSERT INTO award_entry_logs (registration_id, pass_no, scan_status, remarks) VALUES (?,?,?,?)");
    $logStmt->execute([$_POST['reg_id'], $_POST['pass_no'] ?? '', 'Checked In', 'Manually checked in by admin']);
}

// Filters
$search      = trim($_GET['search'] ?? '');
$filterPass  = $_GET['filter_pass'] ?? '';
$filterPay   = $_GET['filter_pay'] ?? '';
$filterEntry = $_GET['filter_entry'] ?? '';

$where = [];
$params = [];
if ($search) {
    $where[] = "(full_name LIKE ? OR email LIKE ? OR mobile LIKE ? OR registration_no LIKE ? OR pass_no LIKE ?)";
    $params = array_merge($params, ["%$search%", "%$search%", "%$search%", "%$search%", "%$search%"]);
}
if ($filterPass)  { $where[] = "pass_type = ?";       $params[] = $filterPass; }
if ($filterPay)   { $where[] = "payment_status = ?";  $params[] = $filterPay; }
if ($filterEntry) { $where[] = "entry_status = ?";    $params[] = $filterEntry; }

$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$regs = $db->prepare("SELECT * FROM award_registrations $whereSQL ORDER BY created_at DESC");
$regs->execute($params);
$registrations = $regs->fetchAll(PDO::FETCH_ASSOC);

// Stats
$stats = $db->query("SELECT 
    COUNT(*) AS total,
    SUM(CASE WHEN payment_status='Paid' THEN 1 ELSE 0 END) AS paid,
    SUM(CASE WHEN payment_status='Pending' THEN 1 ELSE 0 END) AS pending,
    SUM(CASE WHEN entry_status='Checked In' THEN 1 ELSE 0 END) AS checked_in,
    SUM(CASE WHEN payment_status='Paid' THEN final_amount ELSE 0 END) AS revenue,
    SUM(CASE WHEN pass_type='Single Gala Pass' THEN 1 ELSE 0 END) AS single_count,
    SUM(CASE WHEN pass_type='Couple Gala Pass' THEN 1 ELSE 0 END) AS couple_count
FROM award_registrations")->fetch(PDO::FETCH_ASSOC);

// Export CSV
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="gala_registrations_' . date('Ymd') . '.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Reg No','Pass No','Name','Email','Mobile','Pass Type','Amount','Payment','Entry Status','Registered At']);
    foreach ($registrations as $r) {
        fputcsv($out, [$r['registration_no'],$r['pass_no'],$r['full_name'],$r['email'],$r['mobile'],$r['pass_type'],$r['final_amount'],$r['payment_status'],$r['entry_status'],$r['created_at']]);
    }
    fclose($out);
    exit;
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
require_once __DIR__ . '/includes/admin_navbar.php';
?>

<style>
/* ----- RESET & GLOBAL (integrated theme) ----- */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Outfit', 'Segoe UI', sans-serif;
    background: #0b0c10;
    color: #f5f6fa;
    min-height: 100vh;
}

/* light-theme toggle simulation (matches body.light-theme from your theme) */
body.light-theme {
    background: #f4eee1;
    color: #1a1a1a;
}

/* consistent container */
.award-admin-page {
    max-width: 1440px;
    margin: 0 auto;
    padding: 100px 24px 40px;
    font-family: 'Outfit', 'Segoe UI', sans-serif;
}

/* ----- header / title ----- */
.page-header {
    display: flex;
    flex-wrap: wrap;
    align-items: baseline;
    justify-content: space-between;
    margin-bottom: 8px;
}

.page-title {
    font-size: 2.2rem;
    font-weight: 900;
    letter-spacing: -0.02em;
    background: linear-gradient(135deg, #c5a85c, #f5e6b0);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    display: inline-flex;
    align-items: center;
    gap: 12px;
}
body.light-theme .page-title {
    background: linear-gradient(135deg, #a07d2e, #c5a85c);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.page-sub {
    color: #9aa0b4;
    font-size: 1rem;
    font-weight: 400;
    margin-bottom: 28px;
    border-left: 3px solid #c5a85c;
    padding-left: 16px;
}
body.light-theme .page-sub {
    color: #3a3a3a;
    border-left-color: #a07d2e;
}

/* ----- stats grid (glass-like) ----- */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 16px;
    margin-bottom: 32px;
}

.stat-box {
    background: rgba(18, 19, 28, 0.85);
    backdrop-filter: blur(2px);
    border: 1px solid rgba(197, 168, 92, 0.25);
    border-radius: 20px;
    padding: 20px 12px;
    text-align: center;
    transition: 0.25s ease;
    box-shadow: 0 8px 20px rgba(0,0,0,0.4);
}
.stat-box:hover {
    transform: translateY(-4px);
    border-color: #c5a85c;
    box-shadow: 0 12px 28px rgba(197, 168, 92, 0.15);
}
body.light-theme .stat-box {
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(2px);
    border-color: rgba(160, 125, 46, 0.25);
    box-shadow: 0 6px 18px rgba(0,0,0,0.05);
}
body.light-theme .stat-box:hover {
    border-color: #a07d2e;
    box-shadow: 0 10px 24px rgba(160, 125, 46, 0.12);
}

.stat-box .val {
    font-size: 2.2rem;
    font-weight: 900;
    color: #c5a85c;
    letter-spacing: -0.01em;
    line-height: 1.2;
}
body.light-theme .stat-box .val {
    color: #8c6010;
}
.stat-box .val.green { color: #22c55e; }
.stat-box .val.yellow { color: #fbbf24; }
.stat-box .val.cyan { color: #38bdf8; }

.stat-box .lbl {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #9aa0b4;
    margin-top: 6px;
}
body.light-theme .stat-box .lbl {
    color: #4a4a4a;
}

/* ----- filter bar (modern) ----- */
.filter-bar {
    display: flex;
    flex-wrap: wrap;
    gap: 12px 16px;
    margin-bottom: 28px;
    align-items: center;
    background: rgba(18, 19, 28, 0.5);
    backdrop-filter: blur(2px);
    padding: 16px 20px;
    border-radius: 30px;
    border: 1px solid rgba(197, 168, 92, 0.15);
}
body.light-theme .filter-bar {
    background: rgba(255, 245, 235, 0.7);
    border-color: rgba(160, 125, 46, 0.2);
}

.filter-bar input,
.filter-bar select {
    padding: 10px 18px;
    background: #0b0c10;
    border: 1px solid rgba(197, 168, 92, 0.25);
    border-radius: 40px;
    color: #f5f6fa;
    font-family: 'Outfit', 'Segoe UI', sans-serif;
    font-size: 0.9rem;
    min-width: 160px;
    transition: 0.2s;
}
.filter-bar input:focus,
.filter-bar select:focus {
    outline: none;
    border-color: #c5a85c;
    box-shadow: 0 0 0 3px rgba(197, 168, 92, 0.2);
}
body.light-theme .filter-bar input,
body.light-theme .filter-bar select {
    background: #fff;
    color: #1a1a1a;
    border-color: #d1c5a9;
}

.filter-bar button,
.filter-bar .btn-outline {
    padding: 10px 22px;
    background: linear-gradient(135deg, #c5a85c, #8c7237);
    color: #0b0c10;
    border: none;
    border-radius: 40px;
    font-weight: 700;
    font-size: 0.9rem;
    cursor: pointer;
    transition: 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-family: 'Outfit', 'Segoe UI', sans-serif;
}
.filter-bar button:hover {
    transform: scale(1.02);
    box-shadow: 0 6px 16px rgba(197, 168, 92, 0.3);
}
.filter-bar .btn-outline {
    background: transparent;
    color: #9aa0b4;
    border: 1px solid rgba(197, 168, 92, 0.25);
    padding: 9px 20px;
    text-decoration: none;
}
.filter-bar .btn-outline:hover {
    background: rgba(197, 168, 92, 0.08);
    color: #f5f6fa;
}
body.light-theme .filter-bar .btn-outline:hover {
    color: #1a1a1a;
}

.filter-bar .export-btn {
    background: rgba(34, 197, 94, 0.12);
    color: #22c55e;
    border: 1px solid rgba(34, 197, 94, 0.25);
    padding: 9px 20px;
    border-radius: 40px;
    text-decoration: none;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: 0.2s;
    font-size: 0.9rem;
}
.filter-bar .export-btn:hover {
    background: rgba(34, 197, 94, 0.2);
    border-color: #22c55e;
}

/* ----- table card ----- */
.table-wrapper {
    background: rgba(18, 19, 28, 0.6);
    backdrop-filter: blur(2px);
    border-radius: 28px;
    border: 1px solid rgba(197, 168, 92, 0.1);
    overflow-x: auto;
    padding: 4px;
    box-shadow: 0 16px 40px rgba(0,0,0,0.5);
}
body.light-theme .table-wrapper {
    background: rgba(255, 248, 240, 0.7);
    border-color: rgba(160, 125, 46, 0.15);
}

.reg-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.85rem;
    min-width: 900px;
}

.reg-table th {
    padding: 16px 14px;
    background: rgba(197, 168, 92, 0.06);
    color: #c5a85c;
    font-weight: 700;
    text-transform: uppercase;
    font-size: 0.7rem;
    letter-spacing: 0.06em;
    text-align: left;
    border-bottom: 1px solid rgba(197, 168, 92, 0.1);
}
body.light-theme .reg-table th {
    color: #8c6010;
    background: rgba(160, 125, 46, 0.05);
}

.reg-table td {
    padding: 14px 14px;
    border-bottom: 1px solid rgba(197, 168, 92, 0.06);
    vertical-align: middle;
    color: #f0f2f8;
}
body.light-theme .reg-table td {
    color: #1a1a1a;
    border-bottom-color: rgba(160, 125, 46, 0.08);
}

.reg-table tr:hover td {
    background: rgba(197, 168, 92, 0.04);
}
body.light-theme .reg-table tr:hover td {
    background: rgba(160, 125, 46, 0.04);
}

/* badges */
.badge-paid { background: rgba(34,197,94,0.15); color: #22c55e; padding: 4px 14px; border-radius: 40px; font-weight: 700; font-size: 0.7rem; }
.badge-pending { background: rgba(251,191,36,0.15); color: #fbbf24; padding: 4px 14px; border-radius: 40px; font-weight: 700; font-size: 0.7rem; }
.badge-checkin { background: rgba(56,189,248,0.12); color: #38bdf8; padding: 4px 14px; border-radius: 40px; font-weight: 700; font-size: 0.7rem; }
.badge-not { background: rgba(255,255,255,0.04); color: #9aa0b4; padding: 4px 14px; border-radius: 40px; font-weight: 600; font-size: 0.7rem; }
body.light-theme .badge-not { background: rgba(0,0,0,0.03); color: #6a6a6a; }

.reg-no { font-family: 'Outfit', monospace; font-weight: 600; color: #c5a85c; letter-spacing: -0.02em; }
body.light-theme .reg-no { color: #8c6010; }

.qr-preview {
    width: 36px; height: 36px; background: #fff; padding: 3px; border-radius: 8px; display: inline-block;
    border: 1px solid rgba(197,168,92,0.2);
}
.qr-preview img { width: 100%; height: 100%; object-fit: contain; display: block; }

/* action buttons */
.btn-sm {
    padding: 5px 14px;
    font-size: 0.7rem;
    font-weight: 700;
    border-radius: 40px;
    border: none;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: 0.15s;
    font-family: 'Outfit', 'Segoe UI', sans-serif;
}
.btn-view {
    background: rgba(197, 168, 92, 0.12);
    color: #c5a85c;
    border: 1px solid rgba(197, 168, 92, 0.2);
}
.btn-view:hover { background: rgba(197, 168, 92, 0.2); }
.btn-checkin-sm {
    background: rgba(34, 197, 94, 0.1);
    color: #22c55e;
    border: 1px solid rgba(34, 197, 94, 0.2);
}
.btn-checkin-sm:hover { background: rgba(34, 197, 94, 0.2); }

.action-cell { display: flex; flex-wrap: wrap; gap: 6px; align-items: center; }

/* empty state */
.empty-row td { text-align: center; padding: 40px 20px; color: #9aa0b4; font-weight: 400; }

/* responsive */
@media (max-width: 768px) {
    .page-title { font-size: 1.7rem; }
    .stats-grid { grid-template-columns: repeat(2, 1fr); }
    .filter-bar { flex-direction: column; align-items: stretch; border-radius: 24px; }
    .filter-bar input, .filter-bar select { width: 100%; }
}
</style>

<div class="admin-dashboard award-admin-page">

    <!-- header -->
    <div class="page-header">
        <h1 class="page-title"><i class="fas fa-trophy" style="background: none; -webkit-text-fill-color: #c5a85c;"></i> Award Gala Registrations</h1>
        <span style="color:#9aa0b4; font-weight:400; background:rgba(197,168,92,0.08); padding:6px 18px; border-radius:40px; font-size:0.8rem;">
            <i class="fas fa-calendar-alt"></i> 2026 · Global Sports Arena
        </span>
    </div>
    <div class="page-sub">
        <i class="fas fa-user-check" style="margin-right:8px; color:#c5a85c;"></i> Manage all Award Ceremony &amp; Gala Dinner registrations.
    </div>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-box"><div class="val"><?php echo $stats['total'] ?? 0; ?></div><div class="lbl">Total Registrations</div></div>
        <div class="stat-box"><div class="val green">₹<?php echo number_format((float)($stats['revenue'] ?? 0), 0); ?></div><div class="lbl">Total Revenue</div></div>
        <div class="stat-box"><div class="val green"><?php echo $stats['paid'] ?? 0; ?></div><div class="lbl">Paid</div></div>
        <div class="stat-box"><div class="val yellow"><?php echo $stats['pending'] ?? 0; ?></div><div class="lbl">Pending</div></div>
        <div class="stat-box"><div class="val cyan"><?php echo $stats['checked_in'] ?? 0; ?></div><div class="lbl">Checked In</div></div>
        <div class="stat-box"><div class="val"><?php echo $stats['single_count'] ?? 0; ?></div><div class="lbl">Single Pass</div></div>
        <div class="stat-box"><div class="val"><?php echo $stats['couple_count'] ?? 0; ?></div><div class="lbl">Couple Pass</div></div>
    </div>

    <!-- Filter Bar -->
    <form method="GET" class="filter-bar">
        <input type="text" name="search" placeholder="Search name, email, mobile, reg no..." value="<?php echo htmlspecialchars($search); ?>" style="min-width:200px;">
        <select name="filter_pass">
            <option value="">All Pass Types</option>
            <option value="Single Gala Pass" <?php echo $filterPass==='Single Gala Pass'?'selected':''; ?>>Single</option>
            <option value="Couple Gala Pass"  <?php echo $filterPass==='Couple Gala Pass'?'selected':''; ?>>Couple</option>
        </select>
        <select name="filter_pay">
            <option value="">All Payments</option>
            <option value="Paid"    <?php echo $filterPay==='Paid'?'selected':''; ?>>Paid</option>
            <option value="Pending" <?php echo $filterPay==='Pending'?'selected':''; ?>>Pending</option>
        </select>
        <select name="filter_entry">
            <option value="">All Entry Status</option>
            <option value="Not Checked In" <?php echo $filterEntry==='Not Checked In'?'selected':''; ?>>Not Checked In</option>
            <option value="Checked In"     <?php echo $filterEntry==='Checked In'?'selected':''; ?>>Checked In</option>
        </select>
        <button type="submit"><i class="fas fa-search"></i> Filter</button>
        <a href="award-registrations.php" class="btn-outline"><i class="fas fa-undo-alt"></i> Reset</a>
        <a href="award-registrations.php?export=csv&search=<?php echo urlencode($search); ?>&filter_pass=<?php echo urlencode($filterPass); ?>&filter_pay=<?php echo urlencode($filterPay); ?>&filter_entry=<?php echo urlencode($filterEntry); ?>" class="export-btn">
            <i class="fas fa-file-csv"></i> Export CSV
        </a>
    </form>

    <!-- Table -->
    <div class="table-wrapper">
        <div class="overflow-x-auto"><table class="reg-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Reg No.</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Pass Type</th>
                    <th>Amount</th>
                    <th>Payment</th>
                    <th>Entry</th>
                    <th>QR</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($registrations)): ?>
                <tr class="empty-row"><td colspan="12"><i class="fas fa-inbox" style="margin-right:8px;"></i> No registrations found.</td></tr>
            <?php else: foreach ($registrations as $i => $r): ?>
                <tr>
                    <td><?php echo $i+1; ?></td>
                    <td class="reg-no"><?php echo htmlspecialchars($r['registration_no']); ?></td>
                    <td><strong><?php echo htmlspecialchars($r['full_name']); ?></strong></td>
                    <td style="font-size:0.8rem;"><?php echo htmlspecialchars($r['email']); ?></td>
                    <td><?php echo htmlspecialchars($r['mobile']); ?></td>
                    <td style="font-size:0.8rem;"><?php echo htmlspecialchars($r['pass_type']); ?></td>
                    <td style="color:#22c55e; font-weight:700;">₹<?php echo number_format($r['final_amount'],0); ?></td>
                    <td><span class="badge-<?php echo strtolower($r['payment_status'])==='paid'?'paid':'pending'; ?>"><?php echo $r['payment_status']; ?></span></td>
                    <td><span class="<?php echo $r['entry_status']==='Checked In'?'badge-checkin':'badge-not'; ?>"><?php echo $r['entry_status']; ?></span></td>
                    <td>
                        <?php if ($r['qr_code'] && file_exists(__DIR__ . '/' . $r['qr_code'])): ?>
                            <span class="qr-preview"><img src="<?php echo htmlspecialchars($r['qr_code']); ?>" alt="QR"></span>
                        <?php else: ?>—<?php endif; ?>
                    </td>
                    <td style="font-size:0.75rem; color:#9aa0b4;"><?php echo date('d M Y', strtotime($r['created_at'])); ?></td>
                    <td>
                        <div class="action-cell">
                            <a href="award-ticket.php?reg=<?php echo urlencode($r['registration_no']); ?>" target="_blank" class="btn-sm btn-view"><i class="fas fa-ticket-alt"></i> Pass</a>
                            <?php if ($r['entry_status'] !== 'Checked In' && $r['payment_status'] === 'Paid'): ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="checkin">
                                <input type="hidden" name="reg_id" value="<?php echo $r['id']; ?>">
                                <input type="hidden" name="pass_no" value="<?php echo htmlspecialchars($r['pass_no']); ?>">
                                <button type="submit" class="btn-sm btn-checkin-sm" onclick="return confirm('Mark this attendee as Checked In?')"><i class="fas fa-check-circle"></i> Check In</button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table></div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>