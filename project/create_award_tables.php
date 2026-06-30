<?php
require_once __DIR__ . '/config/Database.php';

$db = Database::getConnection();

$sql = "
CREATE TABLE IF NOT EXISTS award_registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    registration_no VARCHAR(50) UNIQUE,
    pass_no VARCHAR(50) UNIQUE,
    full_name VARCHAR(100),
    email VARCHAR(100),
    mobile VARCHAR(20),
    gender VARCHAR(20),
    dob DATE,
    age INT,
    city VARCHAR(100),
    state VARCHAR(100),
    country VARCHAR(100),
    pincode VARCHAR(20),
    occupation VARCHAR(100),
    company_name VARCHAR(100),
    emergency_contact VARCHAR(100),
    emergency_phone VARCHAR(20),
    id_proof_type VARCHAR(50),
    id_proof_file VARCHAR(255),
    pass_type VARCHAR(50),
    food_type VARCHAR(20),
    accommodation_required BOOLEAN DEFAULT 0,
    transport_required BOOLEAN DEFAULT 0,
    special_assistance BOOLEAN DEFAULT 0,
    medical_info TEXT,
    food_allergies TEXT,
    remarks TEXT,
    base_amount DECIMAL(10,2),
    gst_amount DECIMAL(10,2),
    discount_amount DECIMAL(10,2),
    final_amount DECIMAL(10,2),
    payment_status VARCHAR(20) DEFAULT 'Pending',
    razorpay_order_id VARCHAR(100),
    razorpay_payment_id VARCHAR(100),
    razorpay_signature VARCHAR(255),
    qr_code VARCHAR(255),
    entry_status VARCHAR(20) DEFAULT 'Not Checked In',
    checked_in_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS award_pass_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pass_name VARCHAR(100),
    price DECIMAL(10,2),
    description TEXT,
    benefits TEXT,
    status BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS award_entry_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    registration_id INT,
    pass_no VARCHAR(50),
    scanned_by INT NULL,
    scan_status VARCHAR(50),
    scan_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    remarks TEXT
);

-- Seed Pass Types if not exists
INSERT INTO award_pass_types (pass_name, price, description, benefits)
SELECT * FROM (SELECT 'Single Gala Pass', 4500.00, 'Valid for 1 Person', 'Award Ceremony Entry, Gala Dinner & Networking, Participation Certificate') AS tmp
WHERE NOT EXISTS (
    SELECT pass_name FROM award_pass_types WHERE pass_name = 'Single Gala Pass'
) LIMIT 1;

INSERT INTO award_pass_types (pass_name, price, description, benefits)
SELECT * FROM (SELECT 'Couple Gala Pass', 7000.00, 'Valid for 2 Persons', 'Award Ceremony Entry, Gala Dinner & Networking, Premium Seating') AS tmp
WHERE NOT EXISTS (
    SELECT pass_name FROM award_pass_types WHERE pass_name = 'Couple Gala Pass'
) LIMIT 1;
";

try {
    $db->exec($sql);
    echo "Tables created and seeded successfully!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
