package com.playarena.service;

import com.playarena.entity.OtpVerification;
import com.playarena.repository.OtpRepository;
import org.springframework.stereotype.Service;
import java.time.LocalDateTime;
import java.util.Random;

@Service
public class OtpService {

    private final OtpRepository otpRepository;
    private final EmailService emailService;
    private final SmsService smsService;

    public OtpService(OtpRepository otpRepository, EmailService emailService, SmsService smsService) {
        this.otpRepository = otpRepository;
        this.emailService = emailService;
        this.smsService = smsService;
    }

    public OtpVerification generateAndSendOtp(String email, String phoneNumber) {
        Random random = new Random();
        int emailOtpNum = 100000 + random.nextInt(900000);
        int mobileOtpNum = 100000 + random.nextInt(900000);
        String emailOtpStr = String.valueOf(emailOtpNum);
        String mobileOtpStr = String.valueOf(mobileOtpNum);

        // Delete any existing OTP for this email
        otpRepository.findByEmail(email).ifPresent(otpRepository::delete);

        OtpVerification verification = new OtpVerification();
        verification.setEmail(email);
        verification.setPhoneNumber(phoneNumber);
        verification.setEmailOtp(emailOtpStr);
        verification.setMobileOtp(mobileOtpStr);
        verification.setExpiryTime(LocalDateTime.now().plusMinutes(10));
        verification.setVerified(false);

        OtpVerification saved = otpRepository.save(verification);

        // Debug prints for developer tracking (Best debugging method)
        System.out.println("\n--- [DEBUG OTP GENERATION] ---");
        System.out.println("OTP Generated (Email): " + emailOtpStr);
        System.out.println("OTP Generated (Mobile): " + mobileOtpStr);
        System.out.println("Phone: " + phoneNumber);
        System.out.println("Email: " + email);
        System.out.println("-------------------------------\n");

        // Send OTP codes via respective services
        emailService.sendEmailOtp(email, emailOtpStr);
        smsService.sendSmsOtp(phoneNumber, mobileOtpStr);

        return saved;
    }

    public boolean verifyOtps(String email, String enteredEmailOtp, String enteredMobileOtp) {
        OtpVerification verification = otpRepository.findByEmail(email).orElse(null);
        if (verification == null) {
            return false;
        }

        if (verification.getExpiryTime().isBefore(LocalDateTime.now())) {
            otpRepository.delete(verification);
            return false; // Expired
        }

        boolean emailMatches = verification.getEmailOtp().equals(enteredEmailOtp);
        boolean mobileMatches = verification.getMobileOtp().equals(enteredMobileOtp);

        if (emailMatches && mobileMatches) {
            verification.setVerified(true);
            otpRepository.save(verification);
            return true;
        }

        return false;
    }

    public void clearOtp(String email) {
        otpRepository.findByEmail(email).ifPresent(otpRepository::delete);
    }
}
