package com.playarena.security;

import org.springframework.stereotype.Component;
import java.util.Base64;

@Component
public class JwtUtil {

    public String generateToken(String email) {
        return Base64.getEncoder().encodeToString(email.getBytes());
    }

    public String extractEmail(String token) {
        return new String(Base64.getDecoder().decode(token));
    }

    public boolean validateToken(String token) {
        try {
            extractEmail(token);
            return true;
        } catch (Exception e) {
            return false;
        }
    }
}