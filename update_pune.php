<?php
require_once 'project/config/Database.php';
$db = (new Database())->getConnection();

$schedule = '[
    {"day": "Day 1 (6 OCT)", "title": "Opening Ceremony & League Matches", "time": "6:00 PM - 9:00 PM", "description": "Grand inauguration with torch lighting, cultural performances, and athlete parade."},
    {"day": "Day 2 - 4 (7-9 OCT)", "title": "Group Matches All Sports", "time": "8:00 AM - 8:00 PM", "description": "Initial knockout and league stage matches across all categories and age groups."},
    {"day": "Day 5 - 6 (10-11 OCT)", "title": "Quarter Finals All Sports", "time": "8:00 AM - 8:00 PM", "description": "Intense battles for the semi-final spots with top athletes competing."},
    {"day": "Day 7 - 8 (12-13 OCT)", "title": "Semi Finals & Finals", "time": "8:00 AM - 8:00 PM", "description": "The best compete for the ultimate championship spot with high-stakes matches."},
    {"day": "Day 9 (14 OCT)", "title": "Finals, Prize Distribution & Closing Ceremony", "time": "10:00 AM - 10:00 PM", "description": "Championship matches followed by closing ceremony, awards, and celebrations."}
]';

$exhibitors = '[
    {"title": "Standard Stall", "icon": "fa-store", "badge": "", "size": "3m x 3m", "price": "30000", "description": "Booth space in general area"},
    {"title": "Premium Stall", "icon": "fa-store-alt text-gold", "badge": "HOT", "size": "6m x 3m", "price": "60000", "description": "Booth space in high footfall area"},
    {"title": "Corner Premium", "icon": "fa-city", "badge": "", "size": "6m x 6m", "price": "90000", "description": "Two-side open booth for better visibility"},
    {"title": "Pavilion Partner", "icon": "fa-building", "badge": "", "size": "Custom", "price": "200000", "description": "Large space buildout"}
]';

$updateSql = "UPDATE events SET 
    description = :description,
    location = :location,
    schedule_data = :schedule_data,
    exhibitor_data = :exhibitor_data,
    gala_title = :gala_title,
    gala_venue = :gala_venue,
    gala_date = :gala_date,
    gala_time = :gala_time,
    gala_description = :gala_description,
    start_date = :start_date,
    end_date = :end_date
    WHERE id = 1";

$stmt = $db->prepare($updateSql);
$success = $stmt->execute(array(
    ':description' => 'Join the biggest sports championship in Pune 2026',
    ':location' => 'Shree Shiv Chhatrapati Sports Complex, Pune',
    ':schedule_data' => $schedule,
    ':exhibitor_data' => $exhibitors,
    ':gala_title' => 'Award Ceremony & Gala Dinner',
    ':gala_venue' => 'The Orchid Hotel Pune',
    ':gala_date' => '14 October 2026',
    ':gala_time' => '7:00 PM - 11:00 PM',
    ':gala_description' => 'Join us for an exclusive evening celebrating the champions, featuring live entertainment, premium dining, and networking with sports industry leaders.',
    ':start_date' => '2026-10-06',
    ':end_date' => '2026-10-14'
));

if ($success) {
    echo "Successfully updated Pune event data in DB.";
} else {
    print_r($stmt->errorInfo());
}
?>
