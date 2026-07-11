<?php
require_once 'config/Config.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Razorpay Test</title>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body>
    <button id="rzp-button1">Pay</button>
    <script>
        document.getElementById('rzp-button1').onclick = async function(e){
            e.preventDefault();
            const orderRes = await fetch("api/index.php/public-payment/create-razorpay-order", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ amount: 100 }) // 100 INR
            });
            const orderData = await orderRes.json();
            var options = {
                "key": "<?php echo RAZORPAY_KEY_ID; ?>",
                "amount": 10000,
                "currency": "INR",
                "name": "Test Company",
                "description": "Test Transaction",
                "order_id": orderData.id,
                "handler": function (response){
                    alert("Success! " + response.razorpay_payment_id);
                }
            };
            var rzp1 = new window.Razorpay(options);
            rzp1.on('payment.failed', function (response){
                alert("Failed: " + response.error.description);
            });
            rzp1.open();
        }
    </script>
</body>
</html>
