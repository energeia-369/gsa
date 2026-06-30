package com.playarena.service;

import org.springframework.beans.factory.annotation.Value;
import org.springframework.stereotype.Service;
import org.springframework.web.client.RestTemplate;
import org.springframework.http.HttpHeaders;
import org.springframework.http.HttpEntity;
import org.springframework.http.MediaType;
import java.util.Map;
import java.util.HashMap;

@Service
public class SmsService {

    @Value("${fast2sms.api.key:}")
    private String apiKey;

    public void sendSmsOtp(String phoneNumber, String otpCode) {
        // Fast2SMS expects 10-digit Indian numbers without country codes or symbols
        String cleanNumber = phoneNumber;
        if (phoneNumber != null) {
            cleanNumber = phoneNumber.trim().replaceAll("[^0-9]", "");
            if (cleanNumber.length() > 10) {
                cleanNumber = cleanNumber.substring(cleanNumber.length() - 10);
            }
        }

        // Output simulated logs to console for local developer auditing
        System.out.println("\n=======================================================");
        System.out.println("💬 [SMS GATEWAY DISPATCH] Target Mobile: +91 " + cleanNumber);
        System.out.println("🔑 SECURE MOBILE OTP VERIFICATION CODE IS: [ " + otpCode + " ]");
        System.out.println("=======================================================\n");

        if (apiKey == null || apiKey.trim().isEmpty() || apiKey.equalsIgnoreCase("your_fast2sms_api_key_here")) {
            System.out.println("ℹ️ [Fast2SMS] API Key not set in application.properties. Operating in local simulation mode.");
            return;
        }

        // Dispatch real SMS via Fast2SMS Bulk HTTP API POST request using RestTemplate
        try {
            String url = "https://www.fast2sms.com/dev/bulkV2";

            HttpHeaders headers = new HttpHeaders();
            headers.set("authorization", apiKey);
            headers.setContentType(MediaType.APPLICATION_JSON);

            Map<String, Object> body = new HashMap<>();
            body.put("route", "q");
            body.put("message", "Your GLOBAL SPORTS ARENA OTP is: " + otpCode);
            body.put("language", "english");
            body.put("numbers", cleanNumber);

            HttpEntity<Map<String, Object>> request = new HttpEntity<>(body, headers);
            RestTemplate restTemplate = new RestTemplate();

            System.out.println("📲 [Fast2SMS API] Sending POST request via RestTemplate to: " + url);
            String response = restTemplate.postForObject(url, request, String.class);
            System.out.println("📲 [Fast2SMS Server Response] " + response);

        } catch (Exception e) {
            System.out.println("⚠️ [Fast2SMS ERROR] Failed to dispatch real SMS to +91 " + cleanNumber);
            System.out.println("Exception message: " + e.getMessage());
            System.out.println("Please check your internet connection or verify your Fast2SMS API Key has active balance.");
        }
    }
}
