<?php
require_once __DIR__ . '/config/Database.php';

try {
    $db = Database::getConnection();
    
    // Clear existing for a fresh start
    $db->query("TRUNCATE TABLE team_profiles");
    
    $profiles = [
        [
            'name' => 'Akshata Ingale',
            'qualification' => 'B.Tech',
            'role' => 'Software Developer',
            'description' => 'Akshata Ingale is a passionate Software Developer focused on building modern, scalable, and user-friendly web apps.',
            'image' => 'https://ui-avatars.com/api/?name=Akshata+Ingale&background=0D8ABC&color=fff&rounded=true&size=400',
            'display_order' => 1
        ],
        [
            'name' => 'Kalpesh Patil',
            'qualification' => 'B.Sc',
            'role' => 'UI/UX Designer',
            'description' => 'Kalpesh Patil crafts intuitive digital experiences with a focus on accessibility and clean visual design.',
            'image' => 'https://ui-avatars.com/api/?name=Kalpesh+Patil&background=0D8ABC&color=fff&rounded=true&size=400',
            'display_order' => 2
        ],
        [
            'name' => 'Monali Patil',
            'qualification' => 'B.Tech',
            'role' => 'DevOps Engineer',
            'description' => 'Monali Patil builds reliable CI/CD pipelines and cloud infrastructure that keep applications secure.',
            'image' => 'https://ui-avatars.com/api/?name=Monali+Patil&background=0D8ABC&color=fff&rounded=true&size=400',
            'display_order' => 3
        ],
        [
            'name' => 'Kalyani',
            'qualification' => 'B.Tech',
            'role' => 'QA Engineer',
            'description' => 'Ensuring top-notch quality and bug-free releases across all digital platforms.',
            'image' => 'https://ui-avatars.com/api/?name=Kalyani&background=0D8ABC&color=fff&rounded=true&size=400',
            'display_order' => 4
        ]
    ];
    
    $stmt = $db->prepare("INSERT INTO team_profiles (name, qualification, role, description, image, display_order, status) VALUES (?, ?, ?, ?, ?, ?, 'active')");
    
    foreach ($profiles as $p) {
        $stmt->execute([$p['name'], $p['qualification'], $p['role'], $p['description'], $p['image'], $p['display_order']]);
    }
    
    echo "Seeded team profiles.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
