<?php
require_once __DIR__ . '/../config/Config.php';

class EmailService {
    public function sendEmailOtp($toEmail, $otpCode) {
        $subject = "🔑 GLOBAL SPORTS ARENA Account Registration Email Code";
        $message = "Hello,\n\n"
                 . "Thank you for joining GLOBAL SPORTS ARENA! Your secure registration email verification code is: [ " . $otpCode . " ]\n\n"
                 . "Please enter this code in the Gmail OTP field to complete your registration.\n\n"
                 . "Best regards,\n"
                 . "The GLOBAL SPORTS ARENA Team";

        return $this->sendMail($toEmail, $subject, $message, "Registration", $otpCode);
    }

    public function sendLoginOtp($toEmail, $otpCode) {
        $subject = "🔑 GLOBAL SPORTS ARENA Secure Login OTP Verification Code";
        $message = "Hello,\n\n"
                 . "Your secure login OTP verification code is: [ " . $otpCode . " ]\n\n"
                 . "This code is valid for 10 minutes. Please do not share this code with anyone.\n\n"
                 . "Best regards,\n"
                 . "The GLOBAL SPORTS ARENA Team";

        return $this->sendMail($toEmail, $subject, $message, "Login", $otpCode);
    }

    public function sendMembershipExpiryWarning($toEmail, $daysLeft) {
        $subject = "⚠️ Action Required: Your GLOBAL SPORTS ARENA Membership is Expiring!";
        $timeframe = $daysLeft > 0 ? "in exactly $daysLeft day(s)" : "today";
        
        $message = "Hello,\n\n"
                 . "This is a friendly reminder that your premium membership at GLOBAL SPORTS ARENA is scheduled to expire $timeframe.\n\n"
                 . "Don't lose out on your exclusive NXL cashback rates, VIP perks, and early event access! "
                 . "Please log in to your dashboard and upgrade or renew your membership to keep your benefits active.\n\n"
                 . "Best regards,\n"
                 . "The GLOBAL SPORTS ARENA Team";

        return $this->sendMail($toEmail, $subject, $message, "Membership Expiry Reminder");
    }

    private function sendMail($toEmail, $subject, $message, $type, $otpCode = "N/A") {
        // Since configuring SMTP inside PHP from scratch can sometimes fail on local XAMPP without postfix,
        // we implement a robust sender and output developer log alerts.
        $headers = "From: " . SMTP_FROM_NAME . " <" . SMTP_FROM_EMAIL . ">\r\n" .
                   "Reply-To: " . SMTP_FROM_EMAIL . "\r\n" .
                   "X-Mailer: PHP/" . phpversion();

        // Try using PHP mail function
        $sent = false;
        try {
            $sent = @mail($toEmail, $subject, $message, $headers);
        } catch (Exception $e) {
            $sent = false;
        }

        if ($sent) {
            error_log("📧 [SMTP EMAIL SUCCESS] $type email sent successfully to: " . $toEmail);
            return true;
        } else {
            // Log fallback for developer local debugging, matching the Java Spring Boot fallback output
            error_log("⚠️ [SMTP EMAIL FAILURE] Failed to send $type email to " . $toEmail);
            if ($otpCode !== "N/A") {
                error_log("🔑 FALLBACK TEST $type OTP: [ " . $otpCode . " ]");
            }
            
            // For testing/cPanel, also write to a local log file in workspace
            $logDir = __DIR__ . '/../logs';
            if (!is_dir($logDir)) {
                @mkdir($logDir, 0777, true);
            }
            $logMsg = "[" . date('Y-m-d H:i:s') . "] To: $toEmail | Type: $type";
            if ($otpCode !== "N/A") $logMsg .= " | OTP: $otpCode";
            $logMsg .= "\n";
            @file_put_contents($logDir . '/email_activity.log', $logMsg, FILE_APPEND);
            return false;
        }
    }
}
