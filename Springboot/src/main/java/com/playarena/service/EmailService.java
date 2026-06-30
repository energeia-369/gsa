package com.playarena.service;

import org.springframework.mail.SimpleMailMessage;
import org.springframework.mail.javamail.JavaMailSender;
import org.springframework.stereotype.Service;

@Service
public class EmailService {

    private final JavaMailSender mailSender;

    public EmailService(JavaMailSender mailSender) {
        this.mailSender = mailSender;
    }

    public void sendEmailOtp(String toEmail, String otpCode) {
        try {
            SimpleMailMessage message = new SimpleMailMessage();
            message.setTo(toEmail);
            message.setSubject("🔑 GLOBAL SPORTS ARENA Account Registration Email Code");
            message.setText("Hello,\n\n"
                    + "Thank you for joining GLOBAL SPORTS ARENA! Your secure registration email verification code is: [ " + otpCode + " ]\n\n"
                    + "Please enter this code in the Gmail OTP field to complete your registration.\n\n"
                    + "Best regards,\n"
                    + "The GLOBAL SPORTS ARENA Team");
            
            mailSender.send(message);
            System.out.println("\n📧 [SMTP EMAIL SUCCESS] Registration email sent successfully to: " + toEmail);
            System.out.println("Mail sent successfully\n");
        } catch (Exception ex) {
            System.out.println("\n⚠️ [SMTP EMAIL FAILURE] Failed to send registration email to " + toEmail);
            System.out.println("Error details: " + ex.getMessage());
            System.out.println("🔑 FALLBACK TEST GMAIL OTP: [ " + otpCode + " ]\n");
        }
    }

    public void sendLoginOtp(String toEmail, String otpCode) {
        try {
            SimpleMailMessage message = new SimpleMailMessage();
            message.setTo(toEmail);
            message.setSubject("🔑 GLOBAL SPORTS ARENA Secure Login OTP Verification Code");
            message.setText("Hello,\n\n"
                    + "Your secure login OTP verification code is: [ " + otpCode + " ]\n\n"
                    + "This code is valid for 10 minutes. Please do not share this code with anyone.\n\n"
                    + "Best regards,\n"
                    + "The GLOBAL SPORTS ARENA Team");
            
            mailSender.send(message);
            System.out.println("\n📧 [SMTP EMAIL SUCCESS] Login email sent successfully to: " + toEmail);
            System.out.println("Mail sent successfully\n");
        } catch (Exception ex) {
            System.out.println("\n⚠️ [SMTP EMAIL FAILURE] Failed to send login email to " + toEmail);
            System.out.println("Error details: " + ex.getMessage());
            System.out.println("🔑 FALLBACK TEST LOGIN OTP: [ " + otpCode + " ]\n");
        }
    }
}
