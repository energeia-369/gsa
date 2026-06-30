CREATE TABLE IF NOT EXISTS gift_cards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    description TEXT,
    validity_days INT DEFAULT 365,
    benefits TEXT,
    badge VARCHAR(50) NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS gift_card_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    gift_card_id INT NOT NULL,
    gift_code VARCHAR(50) UNIQUE NOT NULL,
    recipient_name VARCHAR(100) NOT NULL,
    recipient_email VARCHAR(100) NOT NULL,
    recipient_mobile VARCHAR(20) NOT NULL,
    sender_name VARCHAR(100) NOT NULL,
    sender_email VARCHAR(100) NULL,
    message TEXT,
    delivery_date DATE,
    quantity INT DEFAULT 1,
    amount DECIMAL(10,2) NOT NULL,
    gst DECIMAL(10,2) NOT NULL,
    discount DECIMAL(10,2) DEFAULT 0,
    final_amount DECIMAL(10,2) NOT NULL,
    payment_status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    redeem_status ENUM('unredeemed', 'partial', 'redeemed', 'expired') DEFAULT 'unredeemed',
    balance DECIMAL(10,2) NOT NULL,
    expiry_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (gift_card_id) REFERENCES gift_cards(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS gift_card_redemptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    gift_code VARCHAR(50) NOT NULL,
    user_email VARCHAR(100) NOT NULL,
    redeemed_amount DECIMAL(10,2) NOT NULL,
    redeemed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('success', 'failed') DEFAULT 'success',
    FOREIGN KEY (gift_code) REFERENCES gift_card_orders(gift_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default gift cards
INSERT INTO gift_cards (name, price, description, validity_days, benefits, badge)
VALUES
('Bronze Gift Card', 500, 'Perfect for small discounts on sports events and store items.', 365, '["Event discount", "Sports store discount", "Food coupon"]', NULL),
('Silver Gift Card', 1000, 'A great step up offering better discounts and bonus NXL credits.', 365, '["Event ticket discount", "Merchandise discount", "NXL credits bonus"]', NULL),
('Gold Gift Card', 2500, 'The best value with priority booking and VIP discounts.', 365, '["VIP event access discount", "Premium sports gear discount", "Priority booking"]', 'Best Seller'),
('Platinum Gift Card', 5000, 'The ultimate luxury sports experience gift for true enthusiasts.', 365, '["VIP experience", "Premium merchandise", "Priority registration", "Exclusive event benefits"]', NULL)
ON DUPLICATE KEY UPDATE name=VALUES(name);
