package com.playarena.entity;

import jakarta.persistence.*;
import lombok.*;
import java.time.LocalDateTime;

@Entity
@Table(name = "orders")
@Getter
@Setter
@NoArgsConstructor
@AllArgsConstructor
public class Order {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    private Long userId;
    
    private double totalAmount;
    
    private double subtotal;
    
    private double discountAmount;
    
    private String paymentStatus; // PENDING, PAID, FREE
    
    private String orderStatus; // pending, confirmed, shipped, delivered, cancelled
    
    private LocalDateTime orderDate = LocalDateTime.now();

    private String shippingAddress;

    private String customerPhone;

    private int nxlCoinsEarned;

    private int nxlCoinsUsed;

    @Column(columnDefinition = "TEXT")
    private String itemsJson; // JSON representation of products purchased or event booked
}