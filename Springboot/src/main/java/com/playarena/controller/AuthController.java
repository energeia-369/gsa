package com.playarena.controller;

import com.playarena.dto.*;
import com.playarena.service.AuthService;
import org.springframework.web.bind.annotation.*;

@RestController
@RequestMapping("/api/auth")
@CrossOrigin(origins = "http://localhost:5173")
public class AuthController {

    private final AuthService authService;

    public AuthController(AuthService authService) {
        this.authService = authService;
    }

    @PostMapping("/register")
    public AuthResponse register(@RequestBody RegisterRequest request) {
        return authService.register(request);
    }

    @PostMapping("/register-send-otp")
    public java.util.Map<String, Object> registerSendOtp(@RequestBody java.util.Map<String, String> request) {
        return authService.registerSendOtp(request.get("email"), request.get("phoneNumber"));
    }

    @PostMapping("/login")
    public AuthResponse login(@RequestBody LoginRequest request) {
        return authService.login(request);
    }

    @PostMapping("/send-otp")
    public java.util.Map<String, Object> sendOtp(@RequestBody java.util.Map<String, String> request) {
        return authService.sendOtp(request.get("email"), request.get("password"));
    }

    @PostMapping("/verify-otp")
    public AuthResponse verifyOtp(@RequestBody java.util.Map<String, String> request) {
        return authService.verifyOtp(request.get("email"), request.get("otp"));
    }
}