<?php
require_once 'includes/Database.php';
require_once 'includes/config.php';

class ContactController {
    private $db;
    private $uploadDir;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        
        $this->uploadDir = __DIR__ . '/../Uploads/';
        
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
        
        // Définir le fuseau horaire pour éviter les problèmes de date
        date_default_timezone_set('Africa/Lagos'); // WAT (West Africa Time)
    }
    
    public function submit() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            exit;
        }

        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            echo json_encode(['success' => false, 'message' => 'Erreur de validation CSRF']);
            exit;
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');
        $appointment_date = trim($_POST['appointment_date'] ?? '');
        $appointment_time = trim($_POST['appointment_time'] ?? '');
        $payment_method = trim($_POST['payment_method'] ?? '');

        // Validation
        $errors = [];
        if (empty($name)) $errors[] = 'Le nom est requis';
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email valide requis';
        }
        if (empty($message)) $errors[] = 'Le message est requis';
        if (!in_array($payment_method, ['onsite', 'online'])) {
            $errors[] = 'Mode de paiement invalide';
        }
        if ($appointment_date && $appointment_time) {
            // Valider le format de la date et de l'heure
            $appointment_datetime = DateTime::createFromFormat('Y-m-d H:i', "$appointment_date $appointment_time");
            if (!$appointment_datetime || $appointment_datetime < new DateTime()) {
                $errors[] = 'Date et heure de rendez-vous invalides ou dans le passé';
            }
        } elseif ($appointment_date || $appointment_time) {
            $errors[] = 'Veuillez fournir à la fois la date et l\'heure du rendez-vous';
        }

        if (empty($errors)) {
            try {
                $this->db->beginTransaction();

                // Insérer le contact
                $stmt = $this->db->prepare("
                    INSERT INTO contacts (name, email, phone, subject, message, payment_method, status, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, ?, ?, 'new', datetime('now'), datetime('now'))
                ");
                
                if (!$stmt->execute([$name, $email, $phone, $subject, $message, $payment_method])) {
                    throw new Exception('Erreur lors de l\'insertion du contact');
                }
                
                $contactId = $this->db->lastInsertId();
                $appointmentId = null;

                // Gérer le rendez-vous si date et heure fournies
                if ($appointment_date && $appointment_time) {
                    $appointment_datetime = "$appointment_date $appointment_time:00";
                    // Vérifier si un créneau est disponible
                    $stmt = $this->db->prepare("
                        SELECT id, start_time, end_time 
                        FROM appointment_slots 
                        WHERE is_available = 1 
                        AND start_time = ? 
                        AND start_time >= datetime('now')
                    ");
                    $stmt->execute([$appointment_datetime]);
                    $slot = $stmt->fetch(PDO::FETCH_ASSOC);

                    if (!$slot) {
                        $this->db->rollBack();
                        echo json_encode(['success' => false, 'message' => 'Aucun créneau disponible pour cette date et heure. Veuillez choisir un autre créneau.']);
                        exit;
                    }

                    // Vérifier si le créneau est déjà réservé
                    $stmt = $this->db->prepare("
                        SELECT id FROM appointments 
                        WHERE slot_id = ? AND status IN ('pending', 'confirmed')
                    ");
                    $stmt->execute([$slot['id']]);
                    if ($stmt->fetch()) {
                        $this->db->rollBack();
                        echo json_encode(['success' => false, 'message' => 'Ce créneau est déjà réservé. Veuillez choisir un autre créneau.']);
                        exit;
                    }

                    // Insérer le rendez-vous
                    $stmt = $this->db->prepare("
                        INSERT INTO appointments (slot_id, contact_id, status, created_at, updated_at)
                        VALUES (?, ?, 'pending', datetime('now'), datetime('now'))
                    ");
                    if (!$stmt->execute([$slot['id'], $contactId])) {
                        throw new Exception('Erreur lors de l\'insertion du rendez-vous');
                    }
                    $appointmentId = $this->db->lastInsertId();

                    // Mettre à jour le contact avec l'appointment_id
                    $stmt = $this->db->prepare("
                        UPDATE contacts SET appointment_id = ?, updated_at = datetime('now') WHERE id = ?
                    ");
                    if (!$stmt->execute([$appointmentId, $contactId])) {
                        throw new Exception('Erreur lors de la mise à jour du contact avec le rendez-vous');
                    }

                    // Marquer le créneau comme indisponible
                    $stmt = $this->db->prepare("
                        UPDATE appointment_slots SET is_available = 0, updated_at = datetime('now') WHERE id = ?
                    ");
                    if (!$stmt->execute([$slot['id']])) {
                        throw new Exception('Erreur lors de la mise à jour du créneau');
                    }
                }

                // Gérer les fichiers téléchargés
                $uploadedFiles = [];
                if (!empty($_FILES['documents']['name'][0])) {
                    $uploadedFiles = $this->handleFileUploads($contactId);
                }
                
                $this->db->commit();
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Votre message' . ($appointmentId ? ' et rendez-vous' : '') . ' a été envoyé avec succès!',
                    'uploaded_files' => count($uploadedFiles)
                ]);
            } catch (Exception $e) {
                $this->db->rollBack();
                error_log("Error in contact submission: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'envoi: ' . $e->getMessage()]);
            }
        } else {
            echo json_encode(['success' => false, 'errors' => $errors]);
        }
        exit;
    }
    
    private function handleFileUploads($contactId) {
        $uploadedFiles = [];
        $allowedTypes = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
        $maxFileSize = 10 * 1024 * 1024; // 10MB
        
        $contactDir = $this->uploadDir . 'contact_' . $contactId . '/';
        if (!file_exists($contactDir)) {
            mkdir($contactDir, 0755, true);
        }
        
        $files = $_FILES['documents'];
        $fileCount = count($files['name']);
        
        for ($i = 0; $i < $fileCount; $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                $fileName = $files['name'][$i];
                $tmpName = $files['tmp_name'][$i];
                $fileSize = $files['size'][$i];
                $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                
                if (!in_array($fileType, $allowedTypes)) {
                    continue;
                }
                
                if ($fileSize > $maxFileSize) {
                    continue;
                }
                
                $uniqueName = time() . '_' . uniqid() . '.' . $fileType;
                $filePath = $contactDir . $uniqueName;
                $relativePath = 'Uploads/contact_' . $contactId . '/' . $uniqueName;
                
                if (move_uploaded_file($tmpName, $filePath)) {
                    $stmt = $this->db->prepare("
                        INSERT INTO contact_files (contact_id, original_name, file_name, file_path, file_size, file_type, uploaded_at) 
                        VALUES (?, ?, ?, ?, ?, ?, datetime('now'))
                    ");
                    
                    if ($stmt->execute([$contactId, $fileName, $uniqueName, $relativePath, $fileSize, $fileType])) {
                        $uploadedFiles[] = [
                            'original_name' => $fileName,
                            'file_name' => $uniqueName,
                            'file_size' => $fileSize,
                            'file_type' => $fileType
                        ];
                    }
                }
            }
        }
        
        return $uploadedFiles;
    }

    public function getAvailableSlots() {
        header('Content-Type: application/json');
        
        try {
            $date = $_GET['date'] ?? null;
            $query = "
                SELECT id, start_time, end_time 
                FROM appointment_slots 
                WHERE is_available = 1 
                AND start_time >= datetime('now')
            ";
            $params = [];
            
            if ($date) {
                $query .= " AND date(start_time) = ?";
                $params[] = $date;
            }
            
            $query .= " ORDER BY start_time";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $slots = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Journaliser les créneaux récupérés pour le débogage
            error_log("Créneaux récupérés pour la date $date : " . print_r($slots, true));
            
            echo json_encode(['success' => true, 'slots' => $slots]);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des créneaux : " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la récupération des créneaux']);
        }
        exit;
    }
}
?>