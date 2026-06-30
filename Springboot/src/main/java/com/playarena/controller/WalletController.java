package com.playarena.controller;

import com.playarena.entity.*;
import com.playarena.repository.*;
import com.playarena.security.JwtUtil;
import org.springframework.web.bind.annotation.*;
import org.springframework.transaction.annotation.Transactional;
import java.util.List;
import java.util.Map;

@RestController
@RequestMapping("/api/wallet")
@CrossOrigin(origins = "http://localhost:5173")
public class WalletController {

    private final UserRepository userRepository;
    private final NXLWalletRepository walletRepository;
    private final NXLTransactionRepository transactionRepository;
    private final JwtUtil jwtUtil;

    public WalletController(
            UserRepository userRepository,
            NXLWalletRepository walletRepository,
            NXLTransactionRepository transactionRepository,
            JwtUtil jwtUtil
    ) {
        this.userRepository = userRepository;
        this.walletRepository = walletRepository;
        this.transactionRepository = transactionRepository;
        this.jwtUtil = jwtUtil;
    }

    private String getEmail(String emailParam, String authHeader) {
        if (emailParam != null && !emailParam.isEmpty()) {
            return emailParam;
        }
        if (authHeader != null && authHeader.startsWith("Bearer ")) {
            String token = authHeader.substring(7);
            if (jwtUtil.validateToken(token)) {
                return jwtUtil.extractEmail(token);
            }
        }
        return null;
    }

    @GetMapping("/balance")
    public Map<String, Object> getWalletBalance(
            @RequestParam(value = "email", required = false) String emailParam,
            @RequestHeader(value = "Authorization", required = false) String authHeader
    ) {
        String email = getEmail(emailParam, authHeader);
        if (email == null) {
            throw new RuntimeException("Email is required");
        }

        User user = userRepository.findByEmail(email)
                .orElseThrow(() -> new RuntimeException("User not found"));

        NXLWallet wallet = walletRepository.findByUserId(user.getId())
                .orElseGet(() -> {
                    NXLWallet newWallet = new NXLWallet();
                    newWallet.setUserId(user.getId());
                    newWallet.setBalance(0);
                    return walletRepository.save(newWallet);
                });

        return Map.of(
                "userId", user.getId(),
                "email", email,
                "nxlCredits", wallet.getBalance(),
                "walletBalance", wallet.getBalance()
        );
    }

    @GetMapping("/transactions")
    public List<NXLTransaction> getWalletTransactions(
            @RequestParam(value = "email", required = false) String emailParam,
            @RequestHeader(value = "Authorization", required = false) String authHeader
    ) {
        String email = getEmail(emailParam, authHeader);
        if (email == null) {
            throw new RuntimeException("Email is required");
        }

        User user = userRepository.findByEmail(email)
                .orElseThrow(() -> new RuntimeException("User not found"));

        return transactionRepository.findByUserIdOrderByDateDesc(user.getId());
    }

    @PostMapping("/recharge")
    @Transactional
    public Map<String, Object> rechargeWallet(@RequestBody Map<String, Object> payload) {
        String email = (String) payload.get("email");
        int amount = Integer.parseInt(payload.get("amount").toString());

        User user = userRepository.findByEmail(email)
                .orElseThrow(() -> new RuntimeException("User not found"));

        NXLWallet wallet = walletRepository.findByUserId(user.getId())
                .orElseGet(() -> {
                    NXLWallet newWallet = new NXLWallet();
                    newWallet.setUserId(user.getId());
                    newWallet.setBalance(0);
                    return walletRepository.save(newWallet);
                });

        // Conversion rate: 1 INR = 0.1 NXL (i.e. recharge of 1000 INR gives 100 NXL)
        // Let's make it 10% credit earning, matching the preset cards on About page:
        // ₹1000 recharge gives 50 NXL credits. Let's calculate: amount / 20 = NXL credits!
        int creditsToEarn = amount / 20;
        if (creditsToEarn <= 0) {
            creditsToEarn = 1; // Minimum
        }

        wallet.setBalance(wallet.getBalance() + creditsToEarn);
        walletRepository.save(wallet);

        // Update User properties
        user.setWalletBalance(wallet.getBalance());
        user.setCredits(wallet.getBalance());
        userRepository.save(user);

        // Transaction log
        NXLTransaction transaction = new NXLTransaction();
        transaction.setUserId(user.getId());
        transaction.setType("EARNED");
        transaction.setAmount(creditsToEarn);
        transaction.setDescription("Wallet recharge of ₹" + amount);
        transaction.setRefId("RECHARGE-" + System.currentTimeMillis());
        transactionRepository.save(transaction);

        return Map.of(
                "success", true,
                "amount", amount,
                "creditsEarned", creditsToEarn,
                "nxlCredits", wallet.getBalance()
        );
    }

    @PostMapping("/admin/adjust")
    @Transactional
    public Map<String, Object> adminAdjustWallet(@RequestBody Map<String, Object> payload) {
        String email = (String) payload.get("email");
        int amount = Integer.parseInt(payload.get("amount").toString());
        String actionType = (String) payload.get("action"); // ADD or SUBTRACT

        User user = userRepository.findByEmail(email)
                .orElseThrow(() -> new RuntimeException("User not found"));

        NXLWallet wallet = walletRepository.findByUserId(user.getId())
                .orElseGet(() -> {
                    NXLWallet newWallet = new NXLWallet();
                    newWallet.setUserId(user.getId());
                    newWallet.setBalance(0);
                    return walletRepository.save(newWallet);
                });

        if ("SUBTRACT".equalsIgnoreCase(actionType)) {
            if (wallet.getBalance() < amount) {
                throw new RuntimeException("User does not have sufficient NXL balance");
            }
            wallet.setBalance(wallet.getBalance() - amount);
            
            NXLTransaction tx = new NXLTransaction();
            tx.setUserId(user.getId());
            tx.setType("ADMIN_SUB");
            tx.setAmount(amount);
            tx.setDescription("Deducted by administrator adjustment");
            tx.setRefId("ADMIN-" + System.currentTimeMillis());
            transactionRepository.save(tx);
        } else {
            wallet.setBalance(wallet.getBalance() + amount);
            
            NXLTransaction tx = new NXLTransaction();
            tx.setUserId(user.getId());
            tx.setType("ADMIN_ADD");
            tx.setAmount(amount);
            tx.setDescription("Credited by administrator adjustment");
            tx.setRefId("ADMIN-" + System.currentTimeMillis());
            transactionRepository.save(tx);
        }

        walletRepository.save(wallet);

        // Update User entity
        user.setWalletBalance(wallet.getBalance());
        user.setCredits(wallet.getBalance());
        userRepository.save(user);

        return Map.of(
                "success", true,
                "action", actionType,
                "amount", amount,
                "nxlCredits", wallet.getBalance()
        );
    }
}
