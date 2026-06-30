package com.playarena.controller;

import com.playarena.entity.Order;
import com.playarena.service.OrderService;
import com.playarena.security.JwtUtil;
import org.springframework.web.bind.annotation.*;
import java.util.List;
import java.util.Map;

@RestController
@RequestMapping("/api/orders")
@CrossOrigin(origins = "http://localhost:5173")
public class OrderController {

    private final OrderService orderService;
    private final JwtUtil jwtUtil;

    public OrderController(OrderService orderService, JwtUtil jwtUtil) {
        this.orderService = orderService;
        this.jwtUtil = jwtUtil;
    }

    @PostMapping("/place")
    public Order placeOrder(
            @RequestBody Map<String, Object> payload,
            @RequestHeader(value = "Authorization", required = false) String authHeader
    ) {
        String email = null;

        // Try extracting from Auth Header
        if (authHeader != null && authHeader.startsWith("Bearer ")) {
            String token = authHeader.substring(7);
            if (jwtUtil.validateToken(token)) {
                email = jwtUtil.extractEmail(token);
            }
        }

        // Fallback: extract from payload
        if (email == null && payload.containsKey("email")) {
            email = (String) payload.get("email");
        }

        if (email == null) {
            throw new RuntimeException("Authentication email required for order placement");
        }

        Order order = new Order();
        order.setTotalAmount(Double.parseDouble(payload.get("total").toString()));
        order.setSubtotal(payload.containsKey("subtotal") ? Double.parseDouble(payload.get("subtotal").toString()) : order.getTotalAmount() + (payload.containsKey("nxlCoinsUsed") ? Double.parseDouble(payload.get("nxlCoinsUsed").toString()) : 0.0));
        order.setDiscountAmount(payload.containsKey("discountAmount") ? Double.parseDouble(payload.get("discountAmount").toString()) : 0.0);
        order.setPaymentStatus(payload.get("paymentStatus").toString());
        order.setOrderStatus(payload.containsKey("status") ? payload.get("status").toString() : "confirmed");
        order.setShippingAddress(payload.get("shippingAddress").toString());
        order.setCustomerPhone(payload.get("customerPhone").toString());
        order.setNxlCoinsEarned(Integer.parseInt(payload.get("nxlCoinsEarned").toString()));
        order.setNxlCoinsUsed(Integer.parseInt(payload.get("nxlCoinsUsed").toString()));
        
        // Serialize items object to json string for database storage
        if (payload.containsKey("items")) {
            order.setItemsJson(payload.get("items").toString());
        }

        String paymentMethod = payload.containsKey("paymentMethod") ? payload.get("paymentMethod").toString() : "CARD";
        String rzpPaymentId = payload.containsKey("paymentId") ? payload.get("paymentId").toString() : null;

        return orderService.placeOrder(order, email, paymentMethod, rzpPaymentId);
    }

    @GetMapping("/my-orders")
    public List<Order> getMyOrders(
            @RequestParam(value = "email", required = false) String emailParam,
            @RequestHeader(value = "Authorization", required = false) String authHeader
    ) {
        String email = emailParam;

        if (email == null && authHeader != null && authHeader.startsWith("Bearer ")) {
            String token = authHeader.substring(7);
            if (jwtUtil.validateToken(token)) {
                email = jwtUtil.extractEmail(token);
            }
        }

        if (email == null) {
            throw new RuntimeException("User email is required");
        }

        return orderService.getOrdersByUserEmail(email);
    }

    @GetMapping("/all")
    public List<Order> getAllOrders() {
        return orderService.getAllOrders();
    }

    @PutMapping("/{id}/status")
    public Order updateOrderStatus(
            @PathVariable Long id,
            @RequestBody Map<String, String> statusPayload
    ) {
        String status = statusPayload.get("status");
        return orderService.updateOrderStatus(id, status);
    }
}
