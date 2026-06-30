package com.playarena.util;

import com.playarena.repository.UserRepository;
import org.springframework.boot.CommandLineRunner;
import org.springframework.stereotype.Component;
import org.springframework.transaction.annotation.Transactional;

// @Component
public class DatabaseCleaner implements CommandLineRunner {

    private final UserRepository userRepository;

    public DatabaseCleaner(UserRepository userRepository) {
        this.userRepository = userRepository;
    }

    @Override
    @Transactional
    public void run(String... args) throws Exception {
        System.out.println("\n🧹 [DATABASE CLEANER STARTUP] Initiating database cleanup...");
        try {
            long beforeCount = userRepository.count();
            
            // Delete all users whose role is NOT ADMIN (case-insensitive)
            userRepository.findAll().stream()
                .filter(user -> user.getRole() == null || !user.getRole().equalsIgnoreCase("ADMIN"))
                .forEach(user -> {
                    System.out.println("🗑️ Deleting non-admin user: " + user.getEmail() + " (Role: " + user.getRole() + ")");
                    userRepository.delete(user);
                });
                
            long afterCount = userRepository.count();
            System.out.println("✅ [DATABASE CLEANER SUCCESS] Cleanup complete. Removed " + (beforeCount - afterCount) + " non-admin users. Remaining users in DB: " + afterCount + "\n");
        } catch (Exception e) {
            System.out.println("❌ [DATABASE CLEANER ERROR] Failed to clean database: " + e.getMessage());
        }
    }
}
