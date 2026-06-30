package com.playarena.controller;

import com.playarena.entity.User;
import com.playarena.service.UserService;
import org.springframework.web.bind.annotation.*;

@RestController
@RequestMapping("/api/user")
@CrossOrigin(origins = "http://localhost:5173")
public class UserController {

    private final UserService userService;

    public UserController(UserService userService) {
        this.userService = userService;
    }

    @GetMapping("/profile")
    public User getUserProfile(@RequestParam String email) {
        return userService.getUserProfile(email);
    }

    @GetMapping("/all")
    public java.util.List<User> getAllUsers() {
        return userService.getAllUsers();
    }

    @PutMapping("/{id}")
    public User updateUser(@PathVariable Long id, @RequestBody java.util.Map<String, String> payload) {
        User userDetails = new User();
        userDetails.setFullName(payload.get("fullName"));
        userDetails.setEmail(payload.get("email"));
        userDetails.setPhoneNumber(payload.get("phoneNumber"));
        userDetails.setRole(payload.get("role"));
        return userService.updateUser(id, userDetails);
    }

    @DeleteMapping("/{id}")
    public java.util.Map<String, Boolean> deleteUser(@PathVariable Long id) {
        userService.deleteUser(id);
        java.util.Map<String, Boolean> response = new java.util.HashMap<>();
        response.put("deleted", Boolean.TRUE);
        return response;
    }
}