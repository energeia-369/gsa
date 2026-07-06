<?php
require_once __DIR__ . '/../config/Database.php';

class DelegateController {
    
    public static function registerDelegate($data, $files) {
        try {
            $db = Database::getConnection();
            
            // 1. Validation
            if (empty($data['full_name']) || empty($data['email']) || empty($data['passport_number'])) {
                return ["success" => false, "message" => "Missing required fields."];
            }

            // Check duplicate email
            $stmt = $db->prepare("SELECT id FROM delegates WHERE email = ?");
            $stmt->execute([$data['email']]);
            if ($stmt->fetch()) {
                return ["success" => false, "message" => "Email is already registered."];
            }

            // Check duplicate passport
            $stmt = $db->prepare("SELECT id FROM delegates WHERE passport_number = ?");
            $stmt->execute([$data['passport_number']]);
            if ($stmt->fetch()) {
                return ["success" => false, "message" => "Passport Number is already registered."];
            }

            // 2. Generate Delegate ID
            $stmt = $db->query("SELECT MAX(id) as max_id FROM delegates");
            $row = $stmt->fetch();
            $nextId = ($row['max_id'] ?? 0) + 1;
            $delegateId = 'GSADEL' . str_pad($nextId, 6, '0', STR_PAD_LEFT);

            // 3. Handle File Uploads
            $uploadDir = __DIR__ . '/../uploads/delegates/';
            if (!is_dir($uploadDir . 'passport')) mkdir($uploadDir . 'passport', 0777, true);
            if (!is_dir($uploadDir . 'photos')) mkdir($uploadDir . 'photos', 0777, true);
            if (!is_dir($uploadDir . 'resume')) mkdir($uploadDir . 'resume', 0777, true);

            $passportPath = self::uploadFile($files['passport_file'] ?? null, $uploadDir . 'passport/', $delegateId . '_passport');
            $photoPath = self::uploadFile($files['profile_photo'] ?? null, $uploadDir . 'photos/', $delegateId . '_photo');
            $resumePath = self::uploadFile($files['resume_file'] ?? null, $uploadDir . 'resume/', $delegateId . '_resume');

            if (!$passportPath || !$photoPath) {
                return ["success" => false, "message" => "Error uploading required files. Please ensure they are valid PDF/JPG/PNG and under 5MB."];
            }

            // 4. Insert into DB
            $sql = "INSERT INTO delegates (
                delegate_id, event_id, full_name, gender, dob, nationality, country, passport_number, email, phone,
                organization, designation, delegate_type, address, city, state, postal_code,
                emergency_name, emergency_phone, diet, tshirt_size, arrival_date, departure_date,
                hotel_required, airport_pickup, medical_conditions, special_requirements,
                passport_file, profile_photo, resume_file
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([
                $delegateId,
                !empty($data['event_id']) ? $data['event_id'] : null,
                $data['full_name'],
                $data['gender'],
                $data['dob'],
                $data['nationality'],
                $data['country'],
                $data['passport_number'],
                $data['email'],
                $data['phone'],
                $data['organization'],
                $data['designation'],
                $data['delegate_type'],
                $data['address'],
                $data['city'],
                $data['state'],
                $data['postal_code'],
                $data['emergency_name'],
                $data['emergency_phone'],
                $data['diet'],
                $data['tshirt_size'],
                $data['arrival_date'],
                $data['departure_date'],
                $data['hotel_required'],
                $data['airport_pickup'],
                $data['medical_conditions'],
                $data['special_requirements'],
                $passportPath,
                $photoPath,
                $resumePath
            ]);

            return [
                "success" => true,
                "message" => "Registration successful. Proceeding to payment.",
                "delegate_id" => $delegateId
            ];

        } catch (Exception $e) {
            error_log("Delegate Registration Error: " . $e->getMessage());
            return ["success" => false, "message" => "Server Error: " . $e->getMessage()];
        }
    }

    private static function uploadFile($file, $dir, $prefix) {
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $maxSize = 5 * 1024 * 1024; // 5MB
        if ($file['size'] > $maxSize) {
            return null;
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'];
        if (!in_array($ext, $allowed)) {
            return null;
        }

        $filename = $prefix . '_' . time() . '.' . $ext;
        $destination = $dir . $filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return 'uploads/delegates/' . basename($dir) . '/' . $filename;
        }

        return null;
    }
}
