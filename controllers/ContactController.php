<?php
require_once 'includes/Database.php';

class ContactController {
    private $db;
    private $uploadDir;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        
        $this->uploadDir = __DIR__ . '/../Uploads/';
        
        // Créer le dossier d'upload s'il n'existe pas
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }
    
    public function submit() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $subject = $_POST['subject'] ?? '';
            $message = trim($_POST['message'] ?? '');
            
            // Validation
            $errors = [];
            if (empty($name)) $errors[] = 'Le nom est requis';
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Email valide requis';
            }
            if (empty($message)) $errors[] = 'Le message est requis';
            
            if (empty($errors)) {
                try {
                    $this->db->beginTransaction();
                    
                    // Insérer le contact
                    $stmt = $this->db->prepare("
                        INSERT INTO contacts (name, email, phone, subject, message) 
                        VALUES (?, ?, ?, ?, ?)
                    ");
                    
                    if ($stmt->execute([$name, $email, $phone, $subject, $message])) {
                        $contactId = $this->db->lastInsertId();
                        
                        // Gérer les fichiers uploadés
                        $uploadedFiles = [];
                        if (!empty($_FILES['documents']['name'][0])) {
                            $uploadedFiles = $this->handleFileUploads($contactId);
                        }
                        
                        $this->db->commit();
                        
                        echo json_encode([
                            'success' => true, 
                            'message' => 'Votre message a été envoyé avec succès!',
                            'uploaded_files' => count($uploadedFiles)
                        ]);
                    } else {
                        throw new Exception('Erreur lors de l\'insertion');
                    }
                    
                } catch (Exception $e) {
                    $this->db->rollBack();
                    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'envoi: ' . $e->getMessage()]);
                }
            } else {
                echo json_encode(['success' => false, 'errors' => $errors]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
        }
        exit;
    }
    
    private function handleFileUploads($contactId) {
        $uploadedFiles = [];
        $allowedTypes = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
        $maxFileSize = 10 * 1024 * 1024; // 10MB
        
        // Créer un dossier pour ce contact
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
                
                // Vérifications
                if (!in_array($fileType, $allowedTypes)) {
                    continue;
                }
                
                if ($fileSize > $maxFileSize) {
                    continue;
                }
                
                // Générer un nom de fichier unique
                $uniqueName = time() . '_' . uniqid() . '.' . $fileType;
                $filePath = $contactDir . $uniqueName;
                // Stocker le chemin relatif pour la base de données
                $relativePath = 'Uploads/contact_' . $contactId . '/' . $uniqueName;
                
                if (move_uploaded_file($tmpName, $filePath)) {
                    // Sauvegarder en base avec le chemin relatif
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
}
?>