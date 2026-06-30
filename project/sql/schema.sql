CREATE DATABASE IF NOT EXISTS playarena_db;
USE playarena_db;

-- 1. users
CREATE TABLE IF NOT EXISTS users (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL DEFAULT 'USER',
    wallet_balance INT DEFAULT 0,
    credits INT DEFAULT 0,
    total_orders INT DEFAULT 0,
    events_joined INT DEFAULT 0,
    phone_number VARCHAR(20) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. products
CREATE TABLE IF NOT EXISTS products (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    category VARCHAR(100) NOT NULL,
    price DOUBLE NOT NULL,
    description TEXT NULL,
    image_url VARCHAR(500) NULL,
    stock INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. tournaments
CREATE TABLE IF NOT EXISTS tournaments (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    sport VARCHAR(100) NOT NULL,
    venue VARCHAR(150) NOT NULL,
    date VARCHAR(100) NOT NULL,
    registration_fee DOUBLE NOT NULL DEFAULT 0.0,
    max_teams INT NOT NULL DEFAULT 16,
    current_teams INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. orders
CREATE TABLE IF NOT EXISTS orders (
    id VARCHAR(100) PRIMARY KEY,
    user_id BIGINT NOT NULL,
    total_amount DOUBLE NOT NULL,
    subtotal DOUBLE NOT NULL,
    discount_amount DOUBLE NOT NULL DEFAULT 0.0,
    payment_status VARCHAR(50) NOT NULL,
    order_status VARCHAR(50) NOT NULL,
    order_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    shipping_address TEXT NULL,
    customer_phone VARCHAR(20) NULL,
    nxl_coins_earned INT DEFAULT 0,
    nxl_coins_used INT DEFAULT 0,
    items_json TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. payments (PaymentLog)
CREATE TABLE IF NOT EXISTS payments (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(100) NULL,
    razorpay_payment_id VARCHAR(100) NULL,
    amount DOUBLE NOT NULL,
    method VARCHAR(50) NULL,
    status VARCHAR(50) NULL,
    txn_id VARCHAR(100) NULL,
    timestamp DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. otp_verifications
CREATE TABLE IF NOT EXISTS otp_verifications (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    phone_number VARCHAR(20) NULL,
    email_otp VARCHAR(10) NULL,
    mobile_otp VARCHAR(10) NULL,
    expiry_time DATETIME NOT NULL,
    verified BOOLEAN NOT NULL DEFAULT FALSE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. nxl_wallets
CREATE TABLE IF NOT EXISTS nxl_wallets (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNIQUE NOT NULL,
    balance INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. nxl_transactions
CREATE TABLE IF NOT EXISTS nxl_transactions (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT NOT NULL,
    type VARCHAR(50) NOT NULL,
    amount INT NOT NULL,
    description VARCHAR(255) NULL,
    ref_id VARCHAR(100) NULL,
    date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. newsletter_subscribers
CREATE TABLE IF NOT EXISTS newsletter_subscribers (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) UNIQUE NOT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'ACTIVE',
    date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. event_registrations
CREATE TABLE IF NOT EXISTS event_registrations (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    team_name VARCHAR(100) NULL,
    captain_name VARCHAR(100) NULL,
    captain_contact VARCHAR(20) NULL,
    email VARCHAR(100) NULL,
    sport VARCHAR(50) NULL,
    team_category VARCHAR(50) NULL,
    team_members INT DEFAULT 0,
    notes TEXT NULL,
    registration_fee DOUBLE DEFAULT 0.0,
    payment_status VARCHAR(50) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. contact_messages
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NULL,
    email VARCHAR(100) NULL,
    subject VARCHAR(200) NULL,
    message TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed default products
INSERT INTO products (name, category, price, description, image_url, stock) VALUES
('Sports Shoes', 'Shoes', 1999, 'Premium running and sports shoes', 'sports-shoes.jpg', 50),
('Football Jersey', 'Jersey', 799, 'Comfortable football jersey', 'jersey.jpg', 100),
('Badminton Racket', 'Racket', 1499, 'Lightweight badminton racket', 'racket.jpg', 30),
('Football', 'Ball', 999, 'Professional football', 'football.jpg', 40),
('Water Bottle', 'Accessories', 299, 'Sports water bottle', 'bottle.jpg', 80),
('Gym Gloves', 'Gym Accessories', 499, 'Comfortable gym gloves', 'gloves.jpg', 60)
ON DUPLICATE KEY UPDATE name=name;

-- Seed default tournaments
INSERT INTO tournaments (name, sport, venue, date, registration_fee, max_teams, current_teams) VALUES
('Badminton Championship', 'Badminton', 'Pune Indoor Stadium', '2026-06-15', 500, 16, 0),
('Football League', 'Football', 'Mumbai Sports Ground', '2026-06-20', 1200, 16, 0),
('Table Tennis Cup', 'Table Tennis', 'Delhi Indoor Arena', '2026-07-05', 400, 16, 0)
ON DUPLICATE KEY UPDATE name=name;

-- 12. business_inquiries
CREATE TABLE IF NOT EXISTS business_inquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(150) NULL,
    contact_person VARCHAR(100) NULL,
    email VARCHAR(100) NULL,
    phone_number VARCHAR(50) NULL,
    partnership_type TEXT NULL,
    status VARCHAR(50) DEFAULT 'PENDING',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. merchants
CREATE TABLE IF NOT EXISTS merchants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    merchant_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    secret_code VARCHAR(100) NOT NULL,
    status ENUM('pending', 'active', 'inactive') DEFAULT 'active',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
