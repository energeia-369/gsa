<?php

class DestinationController {
    private $db;

    public function __construct($db = null) {
        if ($db) {
            $this->db = $db;
        } else {
            require_once __DIR__ . '/../config/Config.php';
            $this->db = new PDO("mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
    }

    public function handleRequest($method, $pathParts) {
        switch ($method) {
            case 'GET':
                $this->getDestinations();
                break;
            case 'POST':
                $this->saveDestination();
                break;
            case 'DELETE':
                if (isset($pathParts[1])) {
                    $this->deleteDestination($pathParts[1]);
                } else {
                    $this->sendResponse(400, false, "Destination ID missing.");
                }
                break;
            default:
                $this->sendResponse(405, false, "Method Not Allowed");
        }
    }

    private function getDestinations() {
        try {
            $stmt = $this->db->query("SELECT *, is_deleted as deleted FROM custom_destinations");
            $destinations = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Format ID properly
            foreach ($destinations as &$dest) {
                $dest['id'] = intval($dest['id']);
            }

            echo json_encode($destinations);
        } catch (Exception $e) {
            $this->sendResponse(500, false, "Database Error: " . $e->getMessage());
        }
    }

    private function saveDestination() {
        $input = json_decode(file_get_contents("php://input"), true);
        if (!$input) {
            $this->sendResponse(400, false, "Invalid JSON data");
        }

        $id = isset($input['id']) ? intval($input['id']) : time();
        $country = isset($input['country']) ? trim($input['country']) : '';
        $image = isset($input['image']) ? trim($input['image']) : '';
        $date = isset($input['date']) ? trim($input['date']) : '';
        $city = isset($input['city']) ? trim($input['city']) : '';
        $region = isset($input['region']) ? trim($input['region']) : $city;
        $type = isset($input['type']) ? trim($input['type']) : 'international';
        $link = isset($input['link']) ? trim($input['link']) : '';

        try {
            $sql = "INSERT INTO custom_destinations (id, country, image, date, city, region, type, link) 
                    VALUES (:id, :country, :image, :date, :city, :region, :type, :link)
                    ON DUPLICATE KEY UPDATE 
                    country = VALUES(country), image = VALUES(image), date = VALUES(date), 
                    city = VALUES(city), region = VALUES(region), type = VALUES(type), link = VALUES(link), is_deleted = 0";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':id' => $id,
                ':country' => $country,
                ':image' => $image,
                ':date' => $date,
                ':city' => $city,
                ':region' => $region,
                ':type' => $type,
                ':link' => $link
            ]);

            $this->sendResponse(200, true, "Destination saved successfully", ['id' => $id]);
        } catch (Exception $e) {
            $this->sendResponse(500, false, "Database Error: " . $e->getMessage());
        }
    }

    private function deleteDestination($id) {
        $id = intval($id);
        try {
            // Check if it's a default ID (1-200 roughly). If it is, we explicitly mark it deleted.
            // If it's a custom ID, we can just delete it entirely or mark it deleted. Let's mark deleted.
            $stmt = $this->db->prepare("INSERT INTO custom_destinations (id, country, image, date, city, region, type, link, is_deleted)
                                        VALUES (:id, 'Deleted', '', '', '', '', 'international', '', 1)
                                        ON DUPLICATE KEY UPDATE is_deleted = 1");
            $stmt->execute([':id' => $id]);

            $this->sendResponse(200, true, "Destination deleted successfully");
        } catch (Exception $e) {
            $this->sendResponse(500, false, "Database Error: " . $e->getMessage());
        }
    }

    private function sendResponse($code, $success, $message, $data = null) {
        http_response_code($code);
        $response = ["success" => $success, "message" => $message];
        if ($data !== null) {
            $response = array_merge($response, $data);
        }
        echo json_encode($response);
        exit();
    }
}
?>
