package com.playarena.service;

import com.playarena.dto.*;
import com.playarena.entity.User;
import com.playarena.entity.OtpVerification;
import com.playarena.repository.UserRepository;
import com.playarena.security.JwtUtil;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.stereotype.Service;
import java.util.Map;
import java.util.Random;
import java.util.concurrent.ConcurrentHashMap;

@Service
public class AuthService {

    private final UserRepository userRepository;
    private final PasswordEncoder passwordEncoder;
    private final JwtUtil jwtUtil;
    private final EmailService emailService;
    private final OtpService otpService;
    
    // Memory cache for active verification OTPs (for login)
    private final Map<String, String> activeOtps = new ConcurrentHashMap<>();

    public AuthService(
            UserRepository userRepository,
            PasswordEncoder passwordEncoder,
            JwtUtil jwtUtil,
            EmailService emailService,
            OtpService otpService
    ) {
        this.userRepository = userRepository;
        this.passwordEncoder = passwordEncoder;
        this.jwtUtil = jwtUtil;
        this.emailService = emailService;
        this.otpService = otpService;
    }

    public AuthResponse register(RegisterRequest request) {
        if (userRepository.findByEmail(request.getEmail()).isPresent()) {
            return new AuthResponse("Email already exists", null);
        }

        // Registration OTP Verification: Separate Email and Mobile checks using OtpService
        String email = request.getEmail();
        boolean verified = otpService.verifyOtps(email, request.getEmailOtp(), request.getMobileOtp());

        if (!verified) {
            return new AuthResponse("Invalid or expired OTP codes. Please check your inbox and messages.", null);
        }

        // Verification successful: Clear the OTP and save the user
        otpService.clearOtp(email);

        User user = new User();
        user.setFullName(request.getFullName());
        user.setEmail(request.getEmail());
        user.setPassword(passwordEncoder.encode(request.getPassword()));
        user.setPhoneNumber(request.getPhoneNumber());
        
        String requestedRole = request.getRole();
        if (requestedRole != null && requestedRole.equalsIgnoreCase("ADMIN")) {
            user.setRole("ADMIN");
        } else {
            user.setRole("USER");
        }

        userRepository.save(user);

        String token = jwtUtil.generateToken(user.getEmail());

        return new AuthResponse("Registration successful", token, user.getRole());
    }

    public AuthResponse login(LoginRequest request) {
        User user = userRepository.findByEmail(request.getEmail())
                .orElseThrow(() -> new RuntimeException("User not found"));

        if (!passwordEncoder.matches(request.getPassword(), user.getPassword())) {
            return new AuthResponse("Invalid password", null);
        }

        String token = jwtUtil.generateToken(user.getEmail());

        return new AuthResponse("Login successful", token, user.getRole());
    }

    public Map<String, Object> sendOtp(String email, String password) {
        User user = userRepository.findByEmail(email)
                .orElseThrow(() -> new RuntimeException("User not found"));

        if (!passwordEncoder.matches(password, user.getPassword())) {
            throw new RuntimeException("Invalid email or password");
        }

        String phone = user.getPhoneNumber();
        if (phone == null || phone.trim().isEmpty()) {
            throw new RuntimeException("No registered phone number found for email: " + email + ". Please register with a phone number first.");
        }

        // Generate 6-digit random code
        Random random = new Random();
        int otpNum = 100000 + random.nextInt(900000);
        String otpStr = String.valueOf(otpNum);

        // Cache the OTP code
        activeOtps.put(email, otpStr);

        // Mask the phone number for security in response (e.g. +91 ******1234)
        String maskedPhone = phone;
        if (phone.length() >= 4) {
            String lastFour = phone.substring(phone.length() - 4);
            maskedPhone = "+91 ******" + lastFour;
        }

        // Debug prints for developer tracking (Best debugging method)
        System.out.println("\n--- [DEBUG OTP GENERATION - LOGIN] ---");
        System.out.println("OTP Generated: " + otpStr);
        System.out.println("Phone: " + phone);
        System.out.println("Email: " + email);
        System.out.println("---------------------------------------\n");

        // Send actual Email to Gmail using our unified EmailService
        emailService.sendLoginOtp(email, otpStr);

        // Terminal Log representing the SMS Gateway message dispatch
        System.out.println("\n=======================================================");
        System.out.println("💬 [SMS GATEWAY DISPATCH] sending OTP to register: " + phone);
        System.out.println("🔑 SECURE OTP VERIFICATION CODE IS: [ " + otpStr + " ]");
        System.out.println("=======================================================\n");

        return Map.of(
                "success", true,
                "maskedPhone", maskedPhone,
                "message", "A secure login verification code has been sent to your Gmail inbox!"
        );
    }

    public AuthResponse verifyOtp(String email, String enteredOtp) {
        if (!activeOtps.containsKey(email)) {
            return new AuthResponse("No active verification code found for this user", null);
        }

        String cachedOtp = activeOtps.get(email);
        if (!cachedOtp.equals(enteredOtp)) {
            return new AuthResponse("Invalid OTP code. Please check your messages.", null);
        }

        // Verification successful: Clear OTP and issue token
        activeOtps.remove(email);

        User user = userRepository.findByEmail(email)
                .orElseThrow(() -> new RuntimeException("User not found"));

        String token = jwtUtil.generateToken(user.getEmail());

        return new AuthResponse("OTP Verification successful", token, user.getRole());
    }

    public Map<String, Object> registerSendOtp(String email, String phoneNumber) {
        if (userRepository.findByEmail(email).isPresent()) {
            return Map.of(
                    "success", false,
                    "message", "Email already exists"
            );
        }

        OtpVerification verification = otpService.generateAndSendOtp(email, phoneNumber);

        // Mask phone number for security
        String maskedPhone = phoneNumber;
        if (phoneNumber != null && phoneNumber.length() >= 4) {
            String lastFour = phoneNumber.substring(phoneNumber.length() - 4);
            maskedPhone = "+91 ******" + lastFour;
        }

        return Map.of(
                "success", true,
                "maskedPhone", maskedPhone,
                "message", "Verification codes successfully dispatched to your Gmail inbox and mobile phone!"
        );
    }
}