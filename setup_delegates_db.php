<?php
require_once __DIR__ . '/project/config/Database.php';

try {
    $db = Database::getConnection();
    
    // Create delegates table
    $sqlDelegates = "
    CREATE TABLE IF NOT EXISTS delegates (
        id INT AUTO_INCREMENT PRIMARY KEY,
        delegate_id VARCHAR(50) UNIQUE NOT NULL,
        full_name VARCHAR(255) NOT NULL,
        gender VARCHAR(50),
        dob DATE,
        nationality VARCHAR(100),
        country VARCHAR(100),
        passport_number VARCHAR(100) UNIQUE NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        phone VARCHAR(50) NOT NULL,
        organization VARCHAR(255),
        designation VARCHAR(255),
        delegate_type VARCHAR(100),
        address TEXT,
        city VARCHAR(100),
        state VARCHAR(100),
        postal_code VARCHAR(50),
        emergency_name VARCHAR(255),
        emergency_phone VARCHAR(50),
        diet VARCHAR(100),
        tshirt_size VARCHAR(20),
        arrival_date DATE,
        departure_date DATE,
        hotel_required ENUM('Yes', 'No') DEFAULT 'No',
        airport_pickup ENUM('Yes', 'No') DEFAULT 'No',
        medical_conditions TEXT,
        special_requirements TEXT,
        passport_file VARCHAR(255),
        profile_photo VARCHAR(255),
        resume_file VARCHAR(255),
        registration_status ENUM('Pending', 'Under Review', 'Approved', 'Rejected') DEFAULT 'Pending',
        payment_status ENUM('Pending', 'Paid', 'Failed', 'Refunded') DEFAULT 'Pending',
        certificate_status ENUM('Pending', 'Generated') DEFAULT 'Pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    
    $db->exec($sqlDelegates);
    echo "Delegates table created successfully.\n";

    // Create delegate_settings table
    $sqlSettings = "
    CREATE TABLE IF NOT EXISTS delegate_settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(100) UNIQUE NOT NULL,
        setting_value TEXT,
        description VARCHAR(255),
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    $db->exec($sqlSettings);
    echo "Delegate Settings table created successfully.\n";

    // Insert default settings
    $defaultSettings = [
        ['registration_open', 'Yes', 'Is delegate registration open? (Yes/No)'],
        ['maximum_delegates', '500', 'Maximum number of delegates allowed'],
        ['registration_fee', '150.00', 'Registration fee amount'],
        ['currency', 'USD', 'Currency for registration fee'],
        ['terms_text', 'By registering, you agree to abide by the Global Sports Academy rules and regulations.', 'Terms and conditions text'],
        ['privacy_policy', 'Your data will be securely stored and only used for event management purposes.', 'Privacy policy text']
    ];

    $stmt = $db->prepare("INSERT IGNORE INTO delegate_settings (setting_key, setting_value, description) VALUES (?, ?, ?)");
    foreach ($defaultSettings as $setting) {
        $stmt->execute($setting);
    }
    echo "Default settings inserted successfully.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
