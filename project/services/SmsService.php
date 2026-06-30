<?php
require_once __DIR__ . '/../config/Config.php';

class SmsService {
    public function sendSmsOtp($phoneNumber, $otpCode) {
        // Fast2SMS expects 10-digit Indian numbers without country codes or symbols
        $cleanNumber = $phoneNumber;
        if ($phoneNumber !== null) {
            $cleanNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
            if (strlen($cleanNumber) > 10) {
                $cleanNumber = substr($cleanNumber, -10);
            }
        }

        // Output simulated logs to error_log/console
        error_log("\n=======================================================");
        error_log("💬 [SMS GATEWAY DISPATCH] Target Mobile: +91 " . $cleanNumber);
        error_log("🔑 SECURE MOBILE OTP VERIFICATION CODE IS: [ " . $otpCode . " ]");
        error_log("=======================================================\n");

        // Write to log file for developers
        $logDir = __DIR__ . '/../logs';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0777, true);
        }
        @file_put_contents($logDir . '/sms_otp.log', "[" . date('Y-m-d H:i:s') . "] Phone: +91 $cleanNumber | OTP: $otpCode\n", FILE_APPEND);

        $apiKey = FAST2SMS_API_KEY;
        if (empty($apiKey) || $apiKey === 'your_fast2sms_api_key_here') {
            error_log("ℹ️ [Fast2SMS] API Key not set. Operating in local simulation mode.");
            return true;
        }

        // Dispatch real SMS via Fast2SMS Bulk HTTP API POST request using curl
        try {
            $url = "https://www.fast2sms.com/dev/bulkV2";
            $body = [
                "route" => "q",
                "message" => "Your GLOBAL SPORTS ARENA OTP is: " . $otpCode,
                "language" => "english",
                "numbers" => $cleanNumber
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "authorization: $apiKey",
                "Content-Type: application/json"
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            $response = curl_exec($ch);
            $err = curl_error($ch);
            curl_close($ch);

            if ($err) {
                error_log("⚠️ [Fast2SMS ERROR] curl error: " . $err);
            } else {
                error_log("📲 [Fast2SMS Server Response] " . $response);
            }
        } catch (Exception $e) {
            error_log("⚠️ [Fast2SMS ERROR] Failed to dispatch real SMS to +91 " . $cleanNumber);
            error_log("Exception message: " . $e->getMessage());
        }

        return true;
    }
}
