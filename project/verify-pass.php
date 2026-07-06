<?php
$pageTitle = "GLOBAL SPORTS ARENA | Pass Verification";
require_once __DIR__ . '/config/Database.php';

$type = $_GET['type'] ?? '';
$id = $_GET['id'] ?? '';

$isValid = false;
$data = [];
$errorMsg = "";

if ($type && $id) {
    try {
        $db = Database::getConnection();
        if ($type === 'visitor') {
            $stmt = $db->prepare("SELECT * FROM visitor_passes WHERE id = ?");
            $stmt->execute([$id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $isValid = true;
                $data = [
                    'Name' => htmlspecialchars($row['full_name']),
                    'Event' => htmlspecialchars($row['event']),
                    'Type' => 'Visitor Pass',
                    'Company' => htmlspecialchars($row['company'] ?? 'N/A'),
                    'Date Registered' => date('F j, Y', strtotime($row['created_at']))
                ];
            } else {
                $errorMsg = "Visitor pass not found.";
            }
        } else if ($type === 'exhibitor') {
            $stmt = $db->prepare("SELECT * FROM exhibitors WHERE id = ?");
            $stmt->execute([$id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $isValid = true;
                $data = [
                    'Company Name' => htmlspecialchars($row['company_name']),
                    'Event' => htmlspecialchars($row['event']),
                    'Type' => 'Exhibitor Pass',
                    'Contact' => htmlspecialchars($row['contact_person']),
                    'Booth' => htmlspecialchars($row['booth']),
                    'Date Registered' => date('F j, Y', strtotime($row['created_at']))
                ];
            } else {
                $errorMsg = "Exhibitor pass not found.";
            }
        } else {
            $errorMsg = "Invalid pass type.";
        }
    } catch (Exception $e) {
        $errorMsg = "Database error. Please try again.";
    }
} else {
    $errorMsg = "Missing pass information.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background: #0b0c10;
            color: #f5f6fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .verify-card {
            background: linear-gradient(135deg, #12131c 0%, #0b0f1e 100%);
            border: 1px solid rgba(197, 168, 92, 0.35);
            border-radius: 20px;
            padding: 40px;
            max-width: 400px;
            width: 90%;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        .icon-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 20px;
            font-size: 2rem;
        }
        .success-icon {
            background: rgba(46, 125, 50, 0.2);
            color: #4caf50;
            border: 2px solid #4caf50;
        }
        .error-icon {
            background: rgba(198, 40, 40, 0.2);
            color: #f44336;
            border: 2px solid #f44336;
        }
        h1 {
            color: #c5a85c;
            margin: 0 0 10px 0;
            font-size: 1.8rem;
        }
        .data-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            text-align: left;
            border-bottom: 1px dashed rgba(255,255,255,0.1);
            padding-bottom: 8px;
        }
        .data-label {
            color: #9aa0b4;
            font-size: 0.9rem;
        }
        .data-value {
            color: #fff;
            font-weight: 600;
            font-size: 0.95rem;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="verify-card">
        <?php if ($isValid): ?>
            <div class="icon-circle success-icon">
                <i class="fas fa-check"></i>
            </div>
            <h1>Valid Pass</h1>
            <p style="color: #4caf50; margin-bottom: 30px;">Access Granted</p>
            
            <div style="margin-top: 20px;">
                <?php foreach ($data as $label => $val): ?>
                    <div class="data-row">
                        <span class="data-label"><?php echo $label; ?></span>
                        <span class="data-value"><?php echo $val; ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="icon-circle error-icon">
                <i class="fas fa-times"></i>
            </div>
            <h1>Invalid Pass</h1>
            <p style="color: #f44336;"><?php echo $errorMsg; ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
