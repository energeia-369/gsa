package com.playarena.entity;

import jakarta.persistence.*;
import java.time.LocalDateTime;

@Entity
@Table(name = "nxl_transactions")
public class NXLTransaction {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @Column(nullable = false)
    private Long userId;

    private String type; // EARNED, USED, EXPIRED, ADMIN_ADD, ADMIN_SUB

    private int amount;

    private String description;

    private String refId; // Reference ID (e.g., Order ID or payment ID)

    private LocalDateTime date = LocalDateTime.now();

    public NXLTransaction() {}

    public NXLTransaction(Long userId, String type, int amount, String description, String refId) {
        this.userId = userId;
        this.type = type;
        this.amount = amount;
        this.description = description;
        this.refId = refId;
        this.date = LocalDateTime.now();
    }

    public Long getId() {
        return id;
    }

    public void setId(Long id) {
        this.id = id;
    }

    public Long getUserId() {
        return userId;
    }

    public void setUserId(Long userId) {
        this.userId = userId;
    }

    public String getType() {
        return type;
    }

    public void setType(String type) {
        this.type = type;
    }

    public int getAmount() {
        return amount;
    }

    public void setAmount(int amount) {
        this.amount = amount;
    }

    public String getDescription() {
        return description;
    }

    public void setDescription(String description) {
        this.description = description;
    }

    public String getRefId() {
        return refId;
    }

    public void setRefId(String refId) {
        this.refId = refId;
    }

    public LocalDateTime getDate() {
        return date;
    }

    public void setDate(LocalDateTime date) {
        this.date = date;
    }
}
