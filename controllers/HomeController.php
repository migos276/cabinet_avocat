<?php
require_once 'includes/Database.php';

class HomeController {
    private $db;
    private $uploadDir;
    private $maxFileSize = 10 * 1024 * 1024; // 10MB
    private $allowedTypes = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'image/jpeg',
        'image/png'
    ];

    public function __construct() {
        try {
            $this->uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/public/uploads/contact_files/';
            $database = new Database();
            $this->db = $database->getConnection();
            $this->ensureUploadDirectory();
        } catch (Exception $e) {
            error_log("HomeController Constructor Error: " . $e->getMessage());
            http_response_code(500);
            die(json_encode(['success' => false, 'message' => 'Erreur d\'initialisation du serveur']));
        }
    }

    public function index() {
        try {
            $content = $this->getContent();
            $services = $this->getServices();
            $team = $this->getTeam();
            $news = $this->getNews();
            $events = $this->getEvents();
            $appointment_slots = $this->getAvailableAppointmentSlots();

            // Validate expected structure to prevent view errors
            if (!is_array($content) || !is_array($services) || !is_array($team) || !is_array($news) || !is_array($appointment_slots) || !is_array($events)) {
                error_log("Invalid data structure in HomeController::index");
                throw new Exception("Données invalides récupérées depuis la base de données");
            }

            include 'views/home.php';
        } catch (Exception $e) {
            error_log("HomeController::index Error: " . $e->getMessage());
            $content = $this->getDefaultContent();
            $services = $this->getDefaultServices();
            $team = $this->getDefaultTeam();
            $news = $this->getDefaultNews();
            $events = $this->getDefaultEvents();
            $appointment_slots = [];
            include 'views/home.php';
        }
    }

    public function handleContact() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->sendJsonResponse(false, "Méthode non autorisée", [], 405);
        }

        $transactionStarted = false;
        
        try {
            $formData = $this->validateContactForm();
            
            // Start transaction and track its state
            $this->db->beginTransaction();
            $transactionStarted = true;
            
            $contactId = $this->saveContact($formData);
            $uploadedFiles = $this->handleFileUploads($contactId);

            $message = "Message envoyé avec succès";
            if (isset($formData['appointment_requested']) && $formData['appointment_requested'] === '1') {
                $appointmentId = $this->saveAppointment($contactId, $formData);
                if ($appointmentId) {
                    $stmt = $this->db->prepare("UPDATE contacts SET appointment_id = :appointment_id WHERE id = :contact_id");
                    $stmt->execute([':appointment_id' => $appointmentId, ':contact_id' => $contactId]);
                    $message = $formData['payment_method'] === 'online'
                        ? "Paiement effectué avec succès ! Votre rendez-vous est confirmé."
                        : "Demande de rendez-vous envoyée ! Nous vous contacterons pour confirmer.";
                } else {
                    $message = "Message envoyé, mais échec de la création du rendez-vous.";
                }
            }

            if (!empty($uploadedFiles)) {
                $message .= " (" . count($uploadedFiles) . " fichier(s) joint(s))";
            }

            // Only commit if transaction was successfully started
            if ($transactionStarted) {
                $this->db->commit();
                $transactionStarted = false;
            }
            
            return $this->sendJsonResponse(true, $message, ['uploaded_files' => count($uploadedFiles)]);
        } catch (Exception $e) {
            // Only rollback if transaction was successfully started
            if ($transactionStarted) {
                try {
                    $this->db->rollBack();
                    $transactionStarted = false;
                } catch (Exception $rollbackException) {
                    error_log("HomeController::handleContact Rollback Error: " . $rollbackException->getMessage());
                    // Continue with original exception
                }
            }
            
            error_log("HomeController::handleContact Error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            
            return $this->sendJsonResponse(false, "Erreur lors du traitement de votre demande. Veuillez réessayer.", [], 400);
        }
    }

    private function validateContactForm() {
        $requiredFields = ['name', 'email', 'message'];
        $errors = [];

        foreach ($requiredFields as $field) {
            if (empty($_POST[$field])) {
                $errors[] = "Le champ $field est requis";
            }
        }

        if (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Format d'email invalide";
        }

        $appointmentRequested = isset($_POST['appointment_requested']) && $_POST['appointment_requested'] === '1';
        if ($appointmentRequested) {
            if (empty($_POST['slot_id'])) {
                $errors[] = "Sélection d'un créneau de rendez-vous requis";
            } else {
                $slotId = filter_input(INPUT_POST, 'slot_id', FILTER_VALIDATE_INT);
                if ($slotId === false || $slotId <= 0) {
                    $errors[] = "Identifiant de créneau invalide";
                } else {
                    $stmt = $this->db->prepare("SELECT id FROM appointment_slots WHERE id = :id AND is_booked = 0 AND start_time > NOW()");
                    $stmt->execute([':id' => $slotId]);
                    if (!$stmt->fetch()) {
                        $errors[] = "Créneau de rendez-vous invalide ou déjà réservé";
                    }
                }
            }

            if (empty($_POST['payment_method'])) {
                $errors[] = "Mode de paiement requis pour le rendez-vous";
            } elseif (!in_array($_POST['payment_method'], ['online', 'in_person'])) {
                $errors[] = "Mode de paiement invalide";
            } elseif ($_POST['payment_method'] === 'online') {
                $paymentFields = ['cardNumber', 'cardExpiry', 'cardCvc', 'cardName'];
                foreach ($paymentFields as $field) {
                    if (empty($_POST[$field])) {
                        $errors[] = "Le champ $field est requis pour le paiement en ligne";
                    }
                }
                $cardNumber = str_replace(' ', '', $_POST['cardNumber'] ?? '');
                if (!preg_match('/^\d{16}$/', $cardNumber)) {
                    $errors[] = "Numéro de carte invalide";
                }
                if (!preg_match('/^\d{2}\/\d{2}$/', $_POST['cardExpiry'] ?? '')) {
                    $errors[] = "Date d'expiration invalide";
                }
                if (!preg_match('/^\d{3,4}$/', $_POST['cardCvc'] ?? '')) {
                    $errors[] = "Code CVC invalide";
                }
            }
        }

        if (!empty($errors)) {
            throw new Exception(implode(', ', $errors));
        }

        return [
            'name' => htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES, 'UTF-8'),
            'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
            'phone' => htmlspecialchars($_POST['phone'] ?? '', ENT_QUOTES, 'UTF-8'),
            'subject' => htmlspecialchars($_POST['subject'] ?? '', ENT_QUOTES, 'UTF-8'),
            'message' => htmlspecialchars($_POST['message'] ?? '', ENT_QUOTES, 'UTF-8'),
            'appointment_requested' => $appointmentRequested ? '1' : '0',
            'payment_method' => htmlspecialchars($_POST['payment_method'] ?? '', ENT_QUOTES, 'UTF-8'),
            'slot_id' => $appointmentRequested ? filter_input(INPUT_POST, 'slot_id', FILTER_VALIDATE_INT) : null
        ];
    }

    private function saveContact($formData) {
        $sql = "INSERT INTO contacts (name, email, phone, subject, message, appointment_requested, payment_method, created_at) 
                VALUES (:name, :email, :phone, :subject, :message, :appointment_requested, :payment_method, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':name' => $formData['name'],
            ':email' => $formData['email'],
            ':phone' => $formData['phone'],
            ':subject' => $formData['subject'],
            ':message' => $formData['message'],
            ':appointment_requested' => $formData['appointment_requested'],
            ':payment_method' => $formData['payment_method'] ?? null
        ]);
        return $this->db->lastInsertId();
    }

    private function saveAppointment($contactId, $formData) {
        if (!$formData['appointment_requested'] || !$formData['slot_id']) {
            return null;
        }

        $status = $formData['payment_method'] === 'online' ? 'confirmed' : 'pending';
        $sql = "INSERT INTO appointments (contact_id, slot_id, status, created_at) 
                VALUES (:contact_id, :slot_id, :status, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':contact_id' => $contactId,
            ':slot_id' => $formData['slot_id'],
            ':status' => $status
        ]);
        $appointmentId = $this->db->lastInsertId();

        $sql = "UPDATE appointment_slots SET is_booked = 1 WHERE id = :slot_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':slot_id' => $formData['slot_id']]);

        return $appointmentId;
    }

    private function handleFileUploads($contactId) {
        if (empty($_FILES['documents']['name'][0])) {
            return [];
        }

        if (!extension_loaded('fileinfo')) {
            error_log("Fileinfo extension not loaded");
            throw new Exception("Erreur serveur: extension fileinfo requise");
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $uploadedFiles = [];
        $errors = [];

        foreach ($_FILES['documents']['name'] as $key => $name) {
            if ($_FILES['documents']['error'][$key] !== UPLOAD_ERR_OK) {
                $errors[] = "Erreur de téléversement pour le fichier: $name (code: {$_FILES['documents']['error'][$key]})";
                continue;
            }

            $file = [
                'name' => $name,
                'type' => finfo_file($finfo, $_FILES['documents']['tmp_name'][$key]),
                'tmp_name' => $_FILES['documents']['tmp_name'][$key],
                'size' => $_FILES['documents']['size'][$key]
            ];

            try {
                if (!$this->validateFile($file)) {
                    $errors[] = "Fichier invalide: $name";
                    continue;
                }

                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $newFileName = uniqid('doc_') . '.' . preg_replace('/[^a-zA-Z0-9]/', '', $ext);
                $destination = $this->uploadDir . $newFileName;

                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    $sql = "INSERT INTO contact_files (contact_id, original_name, file_name, file_path, file_size, file_type, uploaded_at) 
                            VALUES (:contact_id, :original_name, :file_name, :file_path, :file_size, :file_type, NOW())";
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute([
                        ':contact_id' => $contactId,
                        ':original_name' => $file['name'],
                        ':file_name' => $newFileName,
                        ':file_path' => '/public/uploads/contact_files/' . $newFileName,
                        ':file_size' => $file['size'],
                        ':file_type' => $file['type']
                    ]);
                    $uploadedFiles[] = $newFileName;
                } else {
                    $errors[] = "Échec du déplacement du fichier: $name";
                }
            } catch (Exception $e) {
                $errors[] = "Erreur avec le fichier $name: " . $e->getMessage();
            }
        }

        finfo_close($finfo);

        if (!empty($errors)) {
            error_log("File upload errors: " . implode(', ', $errors));
        }

        return $uploadedFiles;
    }

    private function validateFile($file) {
        if (!in_array($file['type'], $this->allowedTypes)) {
            throw new Exception("Type de fichier non autorisé: {$file['name']}");
        }
        if ($file['size'] > $this->maxFileSize) {
            throw new Exception("Fichier trop volumineux: {$file['name']} (Max: 10MB)");
        }
        if ($file['size'] <= 0) {
            throw new Exception("Fichier vide: {$file['name']}");
        }
        return true;
    }

    private function ensureUploadDirectory() {
        if (!is_dir($this->uploadDir)) {
            if (!mkdir($this->uploadDir, 0755, true)) {
                throw new Exception("Impossible de créer le répertoire d'upload: {$this->uploadDir}");
            }
        }
        if (!is_writable($this->uploadDir)) {
            throw new Exception("Le répertoire d'upload n'est pas accessible en écriture: {$this->uploadDir}");
        }
    }

    private function getAvailableAppointmentSlots() {
        $stmt = $this->db->query("SELECT id, start_time, end_time FROM appointment_slots WHERE is_booked = 0 AND start_time > NOW() ORDER BY start_time ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    private function getContent() {
        $stmt = $this->db->query("SELECT section, key_name, value FROM site_content");
        $content = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $content[$row['section']][$row['key_name']] = $row['value'];
        }
        return !empty($content) ? $content : $this->getDefaultContent();
    }

    private function getServices() {
        $stmt = $this->db->query("SELECT * FROM services WHERE is_active = 1 ORDER BY order_position");
        $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return !empty($services) ? $services : $this->getDefaultServices();
    }

    private function getTeam() {
        $stmt = $this->db->query("SELECT * FROM team_members WHERE is_active = 1 ORDER BY order_position");
        $team = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($team as &$member) {
            $imagePath = $member['image_path'] ?? '';
            if (empty($imagePath) || !file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)) {
                $member['image_path'] = '/public/uploads/team/default_team_member.jpeg';
            }
        }
        return !empty($team) ? $team : $this->getDefaultTeam();
    }

    private function getNews() {
        $stmt = $this->db->query("SELECT * FROM news WHERE is_active = 1 ORDER BY publish_date DESC LIMIT 3");
        $news = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($news as &$item) {
            $imagePath = $item['image_path'] ?? '';
            if (empty($imagePath) || !file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)) {
                $item['image_path'] = '/public/uploads/news/default_news.jpg';
            }
        }
        return !empty($news) ? $news : $this->getDefaultNews();
    }

    private function getEvents() {
        $stmt = $this->db->query("SELECT * FROM events WHERE is_active = 1 ORDER BY event_date DESC LIMIT 3");
        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($events as &$item) {
            $imagePath = $item['image_path'] ?? '';
            if (empty($imagePath) || !file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)) {
                $item['image_path'] = '/public/uploads/events/default_event.jpg';
            }
        }
        return !empty($events) ? $events : $this->getDefaultEvents();
    }

    private function getDefaultContent() {
        return [
            'hero' => [
                'title' => defined('SITE_NAME') ? SITE_NAME : 'Cabinet Excellence',
                'subtitle' => 'Votre partenaire de confiance pour tous vos besoins juridiques'
            ],
            'about' => [
                'title' => 'À propos de nous',
                'subtitle' => 'Fort de plus de 20 ans d\'expérience, notre cabinet vous accompagne avec professionnalisme.'
            ],
            'services' => [
                'title' => 'Nos services',
                'subtitle' => 'Des domaines d\'expertise variés pour répondre à tous vos besoins'
            ],
            'team' => [
                'title' => 'Notre équipe',
                'subtitle' => 'Des experts à votre service'
            ],
            'news' => [
                'title' => 'Nos Dernières Actualités',
                'subtitle' => 'Restez informé des dernières nouvelles juridiques.'
            ],
            'contact' => [
                'title' => 'Contactez-nous',
                'address' => '123 Avenue de la Justice, 75001 Paris',
                'phone' => '+33 1 23 45 67 89',
                'email' => defined('ADMIN_EMAIL') ? ADMIN_EMAIL : 'contact@cabinet-excellence.fr'
            ],
            'footer' => [
                'copyright' => '© ' . date('Y') . ' ' . (defined('SITE_NAME') ? SITE_NAME : 'Cabinet Excellence') . '. Tous droits réservés.'
            ]
        ];
    }

    private function getDefaultServices() {
        return [
            [
                'id' => 1,
                'title' => 'Droit des Affaires',
                'description' => 'Accompagnement juridique complet pour les entreprises.',
                'icon' => 'fas fa-briefcase',
                'color' => '#3b82f6',
                'order_position' => 1,
                'is_active' => 1,
                'detailed_content' => $this->getDefaultDetailedContent()
            ],
            [
                'id' => 2,
                'title' => 'Droit de la Famille',
                'description' => 'Conseil et représentation dans tous les aspects du droit familial.',
                'icon' => 'fas fa-heart',
                'color' => '#ef4444',
                'order_position' => 2,
                'is_active' => 1,
                'detailed_content' => $this->getDefaultDetailedContent()
            ],
            [
                'id' => 3,
                'title' => 'Droit Immobilier',
                'description' => 'Expertise en transactions immobilières et contentieux.',
                'icon' => 'fas fa-home',
                'color' => '#10b981',
                'order_position' => 3,
                'is_active' => 1,
                'detailed_content' => $this->getDefaultDetailedContent()
            ],
            [
                'id' => 4,
                'title' => 'Droit du Travail',
                'description' => 'Protection des droits des salariés et conseil aux employeurs.',
                'icon' => 'fas fa-users',
                'color' => '#f59e0b',
                'order_position' => 4,
                'is_active' => 1,
                'detailed_content' => $this->getDefaultDetailedContent()
            ]
        ];
    }

    private function getDefaultTeam() {
        return [
            [
                'id' => 1,
                'name' => 'Maître Jean Dupont',
                'position' => 'Avocat Associé - Droit des Affaires',
                'description' => 'Spécialisé en droit des sociétés depuis plus de 15 ans.',
                'image_path' => '/public/uploads/team/default_team_member.jpeg',
                'order_position' => 1,
                'is_active' => 1
            ],
            [
                'id' => 2,
                'name' => 'Maître Marie Martin',
                'position' => 'Avocate Spécialisée - Droit de la Famille',
                'description' => 'Experte en droit matrimonial et protection de l\'enfance.',
                'image_path' => '/public/uploads/team/default_team_member.jpeg',
                'order_position' => 2,
                'is_active' => 1
            ]
        ];
    }

    private function getDefaultNews() {
        return [
            [
                'id' => 1,
                'title' => 'Nouvelles Réglementations en Droit des Affaires',
                'content' => 'Découvrez les dernières évolutions législatives affectant les entreprises en 2025.',
                'image_path' => '/public/uploads/news/default_news.jpg',
                'publish_date' => date('Y-m-d H:i:s'),
                'is_active' => 1
            ],
            [
                'id' => 2,
                'title' => 'Réforme du Droit de la Famille',
                'content' => 'Une analyse approfondie des récentes modifications du droit matrimonial.',
                'image_path' => '/public/uploads/news/default_news.jpg',
                'publish_date' => date('Y-m-d H:i:s', strtotime('-1 week')),
                'is_active' => 1
            ]
        ];
    }

    private function getDefaultEvents() {
        return [
            [
                'id' => 1,
                'title' => 'Conférence sur le Droit Digital',
                'content' => 'Rejoignez-nous pour une conférence sur les défis juridiques du monde digital.',
                'image_path' => '/public/uploads/events/default_event.jpg',
                'event_date' => date('Y-m-d H:i:s', strtotime('+1 month')),
                'is_active' => 1
            ],
            [
                'id' => 2,
                'title' => 'Atelier Droit des Affaires',
                'content' => 'Atelier pratique sur les contrats commerciaux.',
                'image_path' => '/public/uploads/events/default_event.jpg',
                'event_date' => date('Y-m-d H:i:s', strtotime('+2 months')),
                'is_active' => 1
            ]
        ];
    }

    private function getDefaultDetailedContent() {
        return '
            <h3>Notre approche</h3>
            <p>Nous offrons une expertise sur-mesure adaptée à vos besoins spécifiques, avec un suivi personnalisé.</p>
            <ul>
                <li>Analyse approfondie de votre dossier</li>
                <li>Stratégie juridique adaptée</li>
                <li>Accompagnement à chaque étape</li>
                <li>Suivi post-résolution</li>
            </ul>
            <h3>Pourquoi nous choisir ?</h3>
            <p>Notre expérience et notre engagement garantissent :</p>
            <ul>
                <li>Expertise reconnue</li>
                <li>Approche client-centrée</li>
                <li>Réactivité et disponibilité</li>
                <li>Transparence des honoraires</li>
            </ul>
        ';
    }

    private function sendJsonResponse($success, $message, $data = [], $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data' => $data
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}
?>