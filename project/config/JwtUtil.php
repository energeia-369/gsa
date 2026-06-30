<?php
class JwtUtil {
    public static function generateToken($email) {
        return base64_encode($email);
    }

    public static function extractEmail($token) {
        if (empty($token)) {
            return null;
        }
        $decoded = base64_decode($token, true);
        if ($decoded === false) {
            return null;
        }
        return $decoded;
    }

    public static function validateToken($token) {
        try {
            $email = self::extractEmail($token);
            return !empty($email);
        } catch (Exception $e) {
            return false;
        }
    }
}
