package com.playarena.service;

import com.playarena.entity.*;
import com.playarena.repository.*;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;
import java.util.List;
import java.util.Optional;

@Service
public class OrderService {

    private final OrderRepository orderRepository;
    private final UserRepository userRepository;
    private final NXLWalletRepository walletRepository;
    private final NXLTransactionRepository transactionRepository;
    private final PaymentLogRepository paymentLogRepository;

    public OrderService(
            OrderRepository orderRepository,
            UserRepository userRepository,
            NXLWalletRepository walletRepository,
            NXLTransactionRepository transactionRepository,
            PaymentLogRepository paymentLogRepository
    ) {
        this.orderRepository = orderRepository;
        this.userRepository = userRepository;
        this.walletRepository = walletRepository;
        this.transactionRepository = transactionRepository;
        this.paymentLogRepository = paymentLogRepository;
    }

    @Transactional
    public Order placeOrder(Order order, String email, String paymentMethod, String rzpPaymentId) {
        User user = userRepository.findByEmail(email)
                .orElseThrow(() -> new RuntimeException("User not found for email: " + email));

        order.setUserId(user.getId());
        
        // 1. Process Wallet Credits
        NXLWallet wallet = walletRepository.findByUserId(user.getId())
                .orElseGet(() -> {
                    NXLWallet newWallet = new NXLWallet();
                    newWallet.setUserId(user.getId());
                    newWallet.setBalance(0);
                    return walletRepository.save(newWallet);
                });

        // Deduct used coins
        if (order.getNxlCoinsUsed() > 0) {
            if (order.getNxlCoinsUsed() != 100 && order.getNxlCoinsUsed() != 200 && order.getNxlCoinsUsed() != 300) {
                throw new RuntimeException("Invalid NXL redemption option selected");
            }
            double expectedDiscount = 0;
            if (order.getNxlCoinsUsed() == 100) {
                expectedDiscount = order.getSubtotal() * 0.05;
            } else if (order.getNxlCoinsUsed() == 200) {
                expectedDiscount = order.getSubtotal() * 0.10;
            } else if (order.getNxlCoinsUsed() == 300) {
                expectedDiscount = order.getSubtotal() * 0.15;
            }
            
            double expectedTotal = order.getSubtotal() - order.getDiscountAmount() - expectedDiscount;
            if (Math.abs(order.getTotalAmount() - expectedTotal) > 2.0) {
                throw new RuntimeException("Order total mismatch due to invalid NXL discount calculation");
            }
            if (wallet.getBalance() < order.getNxlCoinsUsed()) {
                throw new RuntimeException("Insufficient NXL Credits balance");
            }
            wallet.setBalance(wallet.getBalance() - order.getNxlCoinsUsed());
            
            NXLTransaction debitTx = new NXLTransaction();
            debitTx.setUserId(user.getId());
            debitTx.setType("USED");
            debitTx.setAmount(order.getNxlCoinsUsed());
            debitTx.setDescription("Redeemed at order checkout");
            debitTx.setRefId(rzpPaymentId != null ? rzpPaymentId : "ORDER-" + System.currentTimeMillis());
            transactionRepository.save(debitTx);
        }

        // Earn new coins
        if (order.getNxlCoinsEarned() > 0) {
            wallet.setBalance(wallet.getBalance() + order.getNxlCoinsEarned());
            
            NXLTransaction creditTx = new NXLTransaction();
            creditTx.setUserId(user.getId());
            creditTx.setType("EARNED");
            creditTx.setAmount(order.getNxlCoinsEarned());
            creditTx.setDescription("Cashback reward earned on order");
            creditTx.setRefId(rzpPaymentId != null ? rzpPaymentId : "ORDER-" + System.currentTimeMillis());
            transactionRepository.save(creditTx);
        }
        
        walletRepository.save(wallet);

        // Update User entity fields to synchronize
        user.setWalletBalance(wallet.getBalance());
        user.setCredits(wallet.getBalance());
        user.setTotalOrders(user.getTotalOrders() + 1);
        userRepository.save(user);

        // 2. Save Order
        Order savedOrder = orderRepository.save(order);

        // 3. Log Payment Details
        PaymentLog paymentLog = new PaymentLog();
        paymentLog.setOrderId(savedOrder.getId());
        paymentLog.setAmount(order.getTotalAmount());
        paymentLog.setRazorpayPaymentId(rzpPaymentId != null ? rzpPaymentId : "FREE_ORDER");
        paymentLog.setMethod(paymentMethod != null ? paymentMethod.toUpperCase() : "FREE");
        paymentLog.setStatus("SUCCESS");
        paymentLog.setTxnId(rzpPaymentId != null ? rzpPaymentId : "TXN-" + System.currentTimeMillis());
        paymentLogRepository.save(paymentLog);

        return savedOrder;
    }

    public List<Order> getOrdersByUserEmail(String email) {
        User user = userRepository.findByEmail(email)
                .orElseThrow(() -> new RuntimeException("User not found"));
        return orderRepository.findByUserId(user.getId());
    }

    public List<Order> getAllOrders() {
        return orderRepository.findAll();
    }

    @Transactional
    public Order updateOrderStatus(Long orderId, String status) {
        Order order = orderRepository.findById(orderId)
                .orElseThrow(() -> new RuntimeException("Order not found"));
        order.setOrderStatus(status);
        return orderRepository.save(order);
    }
}
