package com.playarena.entity;

import jakarta.persistence.*;
import lombok.*;
import java.time.LocalDateTime;

@Entity
@Table(name = "payments")
@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
public class PaymentLog {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    private Long orderId;

    private String razorpayPaymentId;

    private double amount;

    private String method; // CARD, UPI, NETBANKING, WALLET, NXL_CREDIT, FREE

    private String status; // SUCCESS, FAILED

    private String txnId;

    private LocalDateTime timestamp = LocalDateTime.now();
}
