<?php
require 'config/Database.php';
try {
    $db = Database::getConnection();
    $db->exec("CREATE TABLE IF NOT EXISTS chatbot_faqs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        question VARCHAR(255) NOT NULL,
        answer TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Insert some defaults if empty
    $count = $db->query("SELECT COUNT(*) FROM chatbot_faqs")->fetchColumn();
    if ($count == 0) {
        $db->exec("INSERT INTO chatbot_faqs (question, answer) VALUES 
            ('How can I book a venue?', 'You can book a venue by visiting the Venues page and clicking on Book Now next to your preferred location.'), 
            ('Do you offer refunds?', 'Refunds are generally available up to 48 hours before the event. Please check our cancellation policy for more details.'), 
            ('How do I contact support?', 'You can email us at support@globalsportsarena.com or call our helpline.')
        ");
    }
    echo "Chatbot FAQs table created and populated successfully";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
