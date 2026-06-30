package com.playarena.dto;

import lombok.*;

@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
public class AuthResponse {
    private String message;
    private String token;
    private String role;

    public AuthResponse(String message, String token) {
        this.message = message;
        this.token = token;
    }
}