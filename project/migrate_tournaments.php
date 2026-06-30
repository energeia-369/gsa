<?php
require_once __DIR__ . '/config/Database.php';

$db = (new Database())->getConnection();

$destinations = [
  [
    "country" => "USA - NEW YORK",
    "city" => "New York",
    "events" => [
      [ "name" => "GSA New York Summer Marathon", "sport" => "Athletics", "entry" => 120, "date" => "May 17, 2026" ],
      [ "name" => "Metropolitan Tennis Championship", "sport" => "Tennis", "entry" => 100, "date" => "Aug 2-8, 2026" ]
    ]
  ],
  [
    "country" => "MALAYSIA",
    "city" => "Kuala Lumpur",
    "events" => [
      [ "name" => "GSA KL Badminton Masters", "sport" => "Badminton", "entry" => 250, "date" => "Nov 20-24, 2026" ],
      [ "name" => "Malaysia Cup Sepak Takraw", "sport" => "Sepak Takraw", "entry" => 100, "date" => "Dec 5-8, 2026" ]
    ]
  ],
  [
    "country" => "INDONESIA",
    "city" => "Bali / Jakarta",
    "events" => [
      [ "name" => "Bali Surf Open Championship", "sport" => "Surfing", "entry" => 500, "date" => "Jan 15-20, 2026" ],
      [ "name" => "Jakarta Indoor Volleyball Cup", "sport" => "Volleyball", "entry" => 300, "date" => "Feb 10-14, 2026" ]
    ]
  ],
  [
    "country" => "VIETNAM",
    "city" => "Ho Chi Minh",
    "events" => [
      [ "name" => "Saigon City Marathon", "sport" => "Athletics", "entry" => 500, "date" => "Feb 19, 2026" ],
      [ "name" => "Vietnam Table Tennis Open", "sport" => "Table Tennis", "entry" => 200, "date" => "Mar 12-16, 2026" ]
    ]
  ],
  [
    "country" => "AUSTRALIA",
    "city" => "Sydney",
    "events" => [
      [ "name" => "Sydney Harbour Swimming Marathon", "sport" => "Swimming", "entry" => 150, "date" => "Mar 5, 2026" ],
      [ "name" => "Australian Rugby Sevens Invitational", "sport" => "Rugby", "entry" => 400, "date" => "Apr 10-12, 2026" ]
    ]
  ],
  [
    "country" => "GERMANY",
    "city" => "Berlin",
    "events" => [
      [ "name" => "Berlin Olympic Track & Field", "sport" => "Athletics", "entry" => 50, "date" => "May 22-24, 2026" ],
      [ "name" => "German E-Sports Championship", "sport" => "Esports", "entry" => 100, "date" => "Jun 18-21, 2026" ]
    ]
  ],
  [
    "country" => "UNITED KINGDOM",
    "city" => "London",
    "events" => [
      [ "name" => "London Grass Court Tennis", "sport" => "Tennis", "entry" => 250, "date" => "Jul 1-14, 2026" ],
      [ "name" => "British Rowing Cup", "sport" => "Rowing", "entry" => 180, "date" => "Aug 8-10, 2026" ]
    ]
  ],
  [
    "country" => "CANADA",
    "city" => "Toronto / Vancouver",
    "events" => [
      [ "name" => "Canadian Winter Games Qualifier", "sport" => "Winter Sports", "entry" => 150, "date" => "Jan 20-30, 2026" ],
      [ "name" => "Vancouver International Hockey Cup", "sport" => "Ice Hockey", "entry" => 300, "date" => "Feb 14-20, 2026" ]
    ]
  ]
];

$stmt = $db->prepare("INSERT INTO tournaments (name, sport, venue, date, registration_fee, max_teams, current_teams) VALUES (?, ?, ?, ?, ?, ?, ?)");

$added = 0;
foreach ($destinations as $dest) {
    foreach ($dest['events'] as $event) {
        $venue = $dest['city'] . ", " . $dest['country'];
        // check if exists
        $check = $db->prepare("SELECT id FROM tournaments WHERE name = ?");
        $check->execute([$event['name']]);
        if (!$check->fetch()) {
            $stmt->execute([
                $event['name'],
                $event['sport'],
                $venue,
                $event['date'],
                $event['entry'],
                16,
                0
            ]);
            $added++;
        }
    }
}
echo "Migrated $added tournaments.";
