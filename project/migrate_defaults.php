<?php
require_once 'config/Database.php';
$db = Database::getConnection();

$defaultDestinations = [
    ["country" => "INDIA", "image" => "https://images.unsplash.com/photo-1564507592333-c60657eea523?w=500&auto=format&fit=crop&q=60", "date" => "24-26 July 2026", "city" => "Pune / Mumbai", "type" => "national"],
    ["country" => "SINGAPORE", "image" => "https://images.unsplash.com/photo-1525625293386-3f8f99389edd?w=500&auto=format&fit=crop&q=60", "date" => "18-20 Sept 2026", "city" => "Singapore", "type" => "international"],
    ["country" => "SWITZERLAND", "image" => "https://images.unsplash.com/photo-1506744038136-46273834b3fb?w=500&auto=format&fit=crop&q=60", "date" => "May - Sep", "city" => "Zurich", "type" => "international"],
    ["country" => "UAE", "image" => "https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=500&auto=format&fit=crop&q=60", "date" => "23-25 Oct 2026", "city" => "Dubai / Abu Dhabi", "type" => "international"],
    ["country" => "THAILAND", "image" => "https://images.unsplash.com/photo-1508009603885-50cf7c579365?w=500&auto=format&fit=crop&q=60", "date" => "18-20 Dec 2026", "city" => "Phuket / Bangkok", "type" => "international"],
    ["country" => "USA - LAS VEGAS", "image" => "https://images.unsplash.com/photo-1501183007986-d0d080b147f9?w=500&auto=format&fit=crop&q=60", "date" => "23-25 July 2026", "city" => "Las Vegas", "type" => "international"],
    ["country" => "USA - NEW YORK", "image" => "https://images.unsplash.com/photo-1526304640581-d334cdbbf45e?w=500&auto=format&fit=crop&q=60", "date" => "23-25 July 2026", "city" => "New York", "type" => "international"],
    ["country" => "MALAYSIA", "image" => "https://images.unsplash.com/photo-1596422846543-75c6fc197f07?w=500&auto=format&fit=crop&q=60", "date" => "20-22 Nov 2026", "city" => "Kuala Lumpur", "type" => "international"],
    ["country" => "INDONESIA", "image" => "https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=500&auto=format&fit=crop&q=60", "date" => "22-24 Jan 2026", "city" => "Bali / Jakarta", "type" => "international"],
    ["country" => "VIETNAM", "image" => "https://images.unsplash.com/photo-1528127269322-539801943592?w=500&auto=format&fit=crop&q=60", "date" => "19-21 Feb 2026", "city" => "Ho Chi Minh", "type" => "international"],
    ["country" => "AUSTRALIA", "image" => "https://images.unsplash.com/photo-1523482580672-f109ba8cb9be?w=500&auto=format&fit=crop&q=60", "date" => "19-21 March 2026", "city" => "Sydney", "type" => "international"],
    ["country" => "GERMANY", "image" => "https://images.unsplash.com/photo-1467269204594-9661b134dd2b?w=500&auto=format&fit=crop&q=60", "date" => "23-25 April 2026", "city" => "Berlin", "type" => "international"],
    ["country" => "UNITED KINGDOM", "image" => "https://images.unsplash.com/photo-1505761671935-60b3a7427bad?w=500&auto=format&fit=crop&q=60", "date" => "21-23 May 2026", "city" => "London", "type" => "international"],
    ["country" => "CANADA", "image" => "https://images.unsplash.com/photo-1503614472-8c93d56e92ce?w=500&auto=format&fit=crop&q=60", "date" => "18-20 June 2026", "city" => "Toronto", "type" => "international"],
];

foreach ($defaultDestinations as $dest) {
    // Check if it already exists
    $stmt = $db->prepare("SELECT * FROM home_event_cards WHERE country_or_state = ? AND module_type = 'home_carousel'");
    $stmt->execute([$dest['country']]);
    if (!$stmt->fetch()) {
        $insert = $db->prepare("INSERT INTO home_event_cards (event_title, event_type, image, event_date, city, country_or_state, link, status, module_type) VALUES (?, ?, ?, ?, ?, ?, ?, 'active', 'home_carousel')");
        $insert->execute([
            $dest['country'] . " Edition", 
            $dest['type'] === 'international' ? 'overseas' : 'state',
            $dest['image'],
            $dest['date'],
            $dest['city'],
            $dest['country'],
            '#'
        ]);
    }
}
echo "Migrated international defaults.\n";

$defaultNationalDestinations = [
    ["country" => "TAMIL NADU", "image" => "https://images.unsplash.com/photo-1582510003544-4d00b7f74220?w=500&auto=format&fit=crop&q=60", "date" => "July - Aug 2026", "city" => "Coimbatore", "type" => "national"],
    ["country" => "PUNE", "image" => "https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=500&auto=format&fit=crop&q=60", "date" => "Oct 2026", "city" => "Pune", "type" => "national"],
    ["country" => "MAHARASHTRA", "image" => "https://images.unsplash.com/photo-1570168007204-dfb528c6958f?w=500&auto=format&fit=crop&q=60", "date" => "10-12 Aug 2026", "city" => "Mumbai / Pune", "type" => "national"],
    ["country" => "KARNATAKA", "image" => "https://images.unsplash.com/photo-1596176530529-78163a4f7af2?w=500&auto=format&fit=crop&q=60", "date" => "15-17 Sept 2026", "city" => "Bangalore", "type" => "national"],
    ["country" => "DELHI", "image" => "https://images.unsplash.com/photo-1587474260584-136574528ed5?w=500&auto=format&fit=crop&q=60", "date" => "05-07 Oct 2026", "city" => "New Delhi", "type" => "national"],
    ["country" => "GOA", "image" => "https://images.unsplash.com/photo-1512343879784-a960bf40e7f2?w=500&auto=format&fit=crop&q=60", "date" => "20-22 Nov 2026", "city" => "Panaji", "type" => "national"],
    ["country" => "KERALA", "image" => "https://images.unsplash.com/photo-1602216056096-3b40cc0c9944?w=500&auto=format&fit=crop&q=60", "date" => "12-14 Dec 2026", "city" => "Kochi", "type" => "national"],
    ["country" => "RAJASTHAN", "image" => "https://images.unsplash.com/photo-1477587458883-47145ed94245?w=500&auto=format&fit=crop&q=60", "date" => "15-17 Jan 2026", "city" => "Jaipur", "type" => "national"],
    ["country" => "GUJARAT", "image" => "https://images.unsplash.com/photo-1605130284535-11dd9eedc58a?w=500&auto=format&fit=crop&q=60", "date" => "10-12 Feb 2026", "city" => "Ahmedabad", "type" => "national"],
];

foreach ($defaultNationalDestinations as $dest) {
    // Check if it already exists
    $stmt = $db->prepare("SELECT * FROM home_event_cards WHERE country_or_state = ? AND module_type = 'home_carousel'");
    $stmt->execute([$dest['country']]);
    if (!$stmt->fetch()) {
        $insert = $db->prepare("INSERT INTO home_event_cards (event_title, event_type, image, event_date, city, country_or_state, link, status, module_type) VALUES (?, ?, ?, ?, ?, ?, ?, 'active', 'home_carousel')");
        $insert->execute([
            $dest['country'] . " Edition", 
            $dest['type'] === 'international' ? 'overseas' : 'state',
            $dest['image'],
            $dest['date'],
            $dest['city'],
            $dest['country'],
            '#'
        ]);
    }
}
echo "Migrated national defaults.\n";

?>
