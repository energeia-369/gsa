<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/Mithraa_E_Project/project/api/index.php/public-payment/create-razorpay-order');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['amount' => 47802.28]));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$res = curl_exec($ch);
echo "Response: " . $res . "\n";
?>
