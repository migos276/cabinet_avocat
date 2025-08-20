<?php
require_once 'includes/Database.php';

class HomeController {
    private $db;
    private $uploadDir = __DIR__ . '/public/uploads/contact_files/';
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
            $database = new Database();
            $this->db = $database->getConnection();
            $this->ensureUploadDirectory();
            $this->ensureDefaultData();
        } catch (Exception $e) {
            error_log("HomeController Constructor Error: " . $e->getMessage());
            http_response_code(500);
            die("Erreur d'initialisation du serveur. Veuillez réessayer plus tard.");
        }
    }

    public function index() {
        try {
            $content = $this->getContent();
            $services = $this->getServices();
            $team = $this->getTeam();
            $news = $this->getNews();

            // Vérifier les données critiques
            if (empty($content) || empty($services) || empty($team)) {
                error_log("Données critiques manquantes, initialisation des valeurs par défaut");
                $this->forceResetData();
                $content = $this->getContent();
                $services = $this->getServices();
                $team = $this->getTeam();
                $news = $this->getNews();
            }

            // Inclure la vue
            include 'views/home.php';

        } catch (Exception $e) {
            error_log("HomeController::index Error: " . $e->getMessage());
            $content = $this->getDefaultContent();
            $services = $this->getDefaultServices();
            $team = $this->getDefaultTeam();
            $news = $this->getDefaultNews();
            include 'views/home.php';
        }
    }

    public function handleContact() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->sendJsonResponse(false, "Méthode non autorisée", [], 405);
        }

        try {
            $formData = $this->validateContactForm();
            $contactId = $this->saveContact($formData);
            $uploadedFiles = $this->handleFileUploads($contactId);

            return $this->sendJsonResponse(true, "Message envoyé avec succès", ['uploaded_files' => count($uploadedFiles)]);
        } catch (Exception $e) {
            error_log("HomeController::handleContact Error: " . $e->getMessage());
            return $this->sendJsonResponse(false, "Erreur lors de l'envoi du message: " . $e->getMessage(), [], 500);
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

        if (!empty($errors)) {
            throw new Exception(implode(', ', $errors));
        }

        return [
            'name' => filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING),
            'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
            'phone' => filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING),
            'subject' => filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_STRING),
            'message' => filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING)
        ];
    }

    private function saveContact($formData) {
        $sql = "INSERT INTO contacts (name, email, phone, subject, message) VALUES (:name, :email, :phone, :subject, :message)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':name' => $formData['name'],
            ':email' => $formData['email'],
            ':phone' => $formData['phone'],
            ':subject' => $formData['subject'],
            ':message' => $formData['message']
        ]);
        return $this->db->lastInsertId();
    }

    private function handleFileUploads($contactId) {
        if (empty($_FILES['documents']['name'][0])) {
            return [];
        }

        $uploadedFiles = [];
        foreach ($_FILES['documents']['name'] as $key => $name) {
            if ($_FILES['documents']['error'][$key] !== UPLOAD_ERR_OK) {
                continue;
            }

            $file = [
                'name' => $_FILES['documents']['name'][$key],
                'type' => $_FILES['documents']['type'][$key],
                'tmp_name' => $_FILES['documents']['tmp_name'][$key],
                'size' => $_FILES['documents']['size'][$key]
            ];

            if (!$this->validateFile($file)) {
                continue;
            }

            $newFileName = uniqid('doc_') . '_' . preg_replace('/[^A-Za-z0-9\.\-_]/', '', $file['name']);
            $destination = $this->uploadDir . $newFileName;

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $sql = "INSERT INTO contact_files (contact_id, original_name, file_name, file_path, file_size, file_type) 
                        VALUES (:contact_id, :original_name, :file_name, :file_path, :file_size, :file_type)";
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
            }
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
        return true;
    }

    private function ensureUploadDirectory() {
        if (!is_dir($this->uploadDir)) {
            if (!mkdir($this->uploadDir, 0755, true)) {
                throw new Exception("Impossible de créer le répertoire d'upload: {$this->uploadDir}");
            }
        }
    }

    private function getContent() {
        $stmt = $this->db->query("SELECT section, key_name, value FROM site_content");
        $content = [];
        while ($row = $stmt->fetch()) {
            $content[$row['section']][$row['key_name']] = $row['value'];
        }
        return !empty($content) ? $content : $this->getDefaultContent();
    }

    private function getServices() {
        $stmt = $this->db->query("SELECT * FROM services WHERE is_active = 1 ORDER BY order_position");
        $services = $stmt->fetchAll();
        return !empty($services) ? $services : $this->getDefaultServices();
    }

    private function getTeam() {
        $stmt = $this->db->query("SELECT * FROM team_members WHERE is_active = 1 ORDER BY order_position");
        $team = $stmt->fetchAll();
        foreach ($team as &$member) {
            if (empty($member['image_path']) || !file_exists($_SERVER['DOCUMENT_ROOT'] . $member['image_path'])) {
                $member['image_path'] = '/public/uploads/team/default_team_member.jpg';
            }
        }
        return !empty($team) ? $team : $this->getDefaultTeam();
    }

    private function getNews() {
        $stmt = $this->db->query("SELECT * FROM news WHERE is_active = 1 ORDER BY publish_date DESC LIMIT 3");
        $news = $stmt->fetchAll();
        return !empty($news) ? $news : $this->getDefaultNews();
    }

    private function ensureDefaultData() {
        $serviceCount = $this->db->query("SELECT COUNT(*) FROM services")->fetchColumn();
        $teamCount = $this->db->query("SELECT COUNT(*) FROM team_members")->fetchColumn();
        $contentCount = $this->db->query("SELECT COUNT(*) FROM site_content")->fetchColumn();
        $newsCount = $this->db->query("SELECT COUNT(*) FROM news")->fetchColumn();

        if ($serviceCount == 0) $this->insertDefaultServices();
        if ($teamCount == 0) $this->insertDefaultTeam();
        if ($contentCount == 0) $this->insertDefaultContent();
        if ($newsCount == 0) $this->insertDefaultNews();
    }

    private function forceResetData() {
        $this->db->exec("DELETE FROM services");
        $this->db->exec("DELETE FROM team_members");
        $this->db->exec("DELETE FROM site_content");
        $this->db->exec("DELETE FROM news");
        $this->insertDefaultServices();
        $this->insertDefaultTeam();
        $this->insertDefaultContent();
        $this->insertDefaultNews();
    }

    private function insertDefaultContent() {
        $defaultContent = [
            ['hero', 'title', 'Excellence Juridique à Votre Service'],
            ['hero', 'subtitle', 'Depuis plus de 20 ans, nous accompagnons nos clients avec expertise, intégrité et dévouement.'],
            ['about', 'title', 'Votre Réussite, Notre Mission'],
            ['about', 'subtitle', 'Fort d\'une expérience reconnue et d\'une approche personnalisée, notre cabinet vous offre un accompagnement juridique d\'excellence.'],
            ['services', 'title', 'Domaines d\'Expertise'],
            ['services', 'subtitle', 'Une expertise reconnue dans des domaines juridiques essentiels pour répondre à tous vos besoins'],
            ['team', 'title', 'Des Experts à Vos Côtés'],
            ['team', 'subtitle', 'Des avocats expérimentés et passionnés, reconnus pour leur expertise et leur engagement'],
            ['contact', 'title', 'Parlons de Votre Situation'],
            ['contact', 'subtitle', 'Bénéficiez d\'un premier échange gratuit pour évaluer vos besoins juridiques']
        ];

        $sql = "INSERT INTO site_content (section, key_name, value) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        foreach ($defaultContent as $content) {
            $stmt->execute($content);
        }
    }

    private function insertDefaultServices() {
        $defaultServices = [
            [
                'title' => 'Droit des Affaires',
                'description' => 'Accompagnement juridique complet pour les entreprises, de la création aux opérations complexes.',
                'icon' => 'fas fa-briefcase',
                'color' => '#3b82f6',
                'order_position' => 1,
                'detailed_content' => $this->getDefaultDetailedContent()
            ],
            [
                'title' => 'Droit de la Famille',
                'description' => 'Conseil et représentation dans tous les aspects du droit familial et matrimonial.',
                'icon' => 'fas fa-heart',
                'color' => '#ef4444',
                'order_position' => 2,
                'detailed_content' => $this->getDefaultDetailedContent()
            ],
            [
                'title' => 'Droit Immobilier',
                'description' => 'Expertise en transactions immobilières, copropriété et contentieux immobiliers.',
                'icon' => 'fas fa-home',
                'color' => '#10b981',
                'order_position' => 3,
                'detailed_content' => $this->getDefaultDetailedContent()
            ],
            [
                'title' => 'Droit du Travail',
                'description' => 'Protection des droits des salariés et conseil aux employeurs en droit social.',
                'icon' => 'fas fa-users',
                'color' => '#f59e0b',
                'order_position' => 4,
                'detailed_content' => $this->getDefaultDetailedContent()
            ]
        ];

        $sql = "INSERT INTO services (title, description, icon, color, order_position, detailed_content, is_active) 
                VALUES (?, ?, ?, ?, ?, ?, 1)";
        $stmt = $this->db->prepare($sql);
        foreach ($defaultServices as $service) {
            $stmt->execute([
                $service['title'],
                $service['description'],
                $service['icon'],
                $service['color'],
                $service['order_position'],
                $service['detailed_content']
            ]);
        }
    }

    private function insertDefaultTeam() {
        $defaultTeam = [
            [
                'name' => 'Maître Jean Dupont',
                'position' => 'Avocat Associé - Droit des Affaires',
                'description' => 'Spécialisé en droit des sociétés et fusions-acquisitions, Maître Dupont accompagne les entreprises depuis plus de 15 ans.',
                'image_path' => '/public/uploads/team/avocat4.jpg',
                'order_position' => 1
            ],
            [
                'name' => 'Maître Marie Martin',
                'position' => 'Avocate Spécialisée - Droit de la Famille',
                'description' => 'Experte en droit matrimonial et protection de l\'enfance, Maître Martin défend les intérêts familiaux avec passion.',
                'image_path' => '/public/uploads/team/avocat5.jpg',
                'order_position' => 2
            ]
        ];

        $sql = "INSERT INTO team_members (name, position, description, image_path, order_position, is_active) 
                VALUES (?, ?, ?, ?, ?, 1)";
        $stmt = $this->db->prepare($sql);
        foreach ($defaultTeam as $member) {
            $stmt->execute([
                $member['name'],
                $member['position'],
                $member['description'],
                $member['image_path'],
                $member['order_position']
            ]);
        }
    }

    private function insertDefaultNews() {
        $defaultNews = [
            [
                'title' => 'Nouvelles Réglementations en Droit des Affaires',
                'content' => 'Découvrez les dernières évolutions législatives affectant les entreprises en 2025.',
                'image_path' => '/public/uploads/news/news1.jpg',
                'publish_date' => date('Y-m-d H:i:s'),
                'is_active' => 1
            ],
            [
                'title' => 'Réforme du Droit de la Famille',
                'content' => 'Une analyse approfondie des récentes modifications du droit matrimonial.',
                'image_path' => '/public/uploads/news/news2.jpg',
                'publish_date' => date('Y-m-d H:i:s', strtotime('-1 week')),
                'is_active' => 1
            ]
        ];

        $sql = "INSERT INTO news (title, content, image_path, publish_date, is_active) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        foreach ($defaultNews as $news) {
            $stmt->execute([
                $news['title'],
                $news['content'],
                $news['image_path'],
                $news['publish_date'],
                $news['is_active']
            ]);
        }
    }

    private function getDefaultContent() {
        return [
            'hero' => [
                'title' => 'Excellence Juridique à Votre Service',
                'subtitle' => 'Depuis plus de 20 ans, nous accompagnons nos clients avec expertise, intégrité et dévouement.'
            ],
            'about' => [
                'title' => 'Votre Réussite, Notre Mission',
                'subtitle' => 'Fort d\'une expérience reconnue et d\'une approche personnalisée, notre cabinet vous offre un accompagnement juridique d\'excellence.'
            ],
            'services' => [
                'title' => 'Domaines d\'Expertise',
                'subtitle' => 'Une expertise reconnue dans des domaines juridiques essentiels pour répondre à tous vos besoins'
            ],
            'team' => [
                'title' => 'Des Experts à Vos Côtés',
                'subtitle' => 'Des avocats expérimentés et passionnés, reconnus pour leur expertise et leur engagement'
            ],
            'contact' => [
                'title' => 'Parlons de Votre Situation',
                'subtitle' => 'Bénéficiez d\'un premier échange gratuit pour évaluer vos besoins juridiques'
            ]
        ];
    }

    private function getDefaultServices() {
        return [
            [
                'id' => 1,
                'title' => 'Droit des Affaires',
                'description' => 'Accompagnement juridique complet pour les entreprises, de la création aux opérations complexes.',
                'icon' => 'fas fa-briefcase',
                'color' => '#3b82f6',
                'order_position' => 1,
                'is_active' => 1
            ],
            [
                'id' => 2,
                'title' => 'Droit de la Famille',
                'description' => 'Conseil et représentation dans tous les aspects du droit familial et matrimonial.',
                'icon' => 'fas fa-heart',
                'color' => '#ef4444',
                'order_position' => 2,
                'is_active' => 1
            ],
            [
                'id' => 3,
                'title' => 'Droit Immobilier',
                'description' => 'Expertise en transactions immobilières, copropriété et contentieux immobiliers.',
                'icon' => 'fas fa-home',
                'color' => '#10b981',
                'order_position' => 3,
                'is_active' => 1
            ],
            [
                'id' => 4,
                'title' => 'Droit du Travail',
                'description' => 'Protection des droits des salariés et conseil aux employeurs en droit social.',
                'icon' => 'fas fa-users',
                'color' => '#f59e0b',
                'order_position' => 4,
                'is_active' => 1
            ]
        ];
    }

    private function getDefaultTeam() {
        return [
            [
                'id' => 1,
                'name' => 'Maître Jean Dupont',
                'position' => 'Avocat Associé - Droit des Affaires',
                'description' => 'Spécialisé en droit des sociétés et fusions-acquisitions, Maître Dupont accompagne les entreprises depuis plus de 15 ans.',
                'image_path' => '/public/uploads/team/avocat4.jpeg',
                'order_position' => 1,
                'is_active' => 1
            ],
            [
                'id' => 2,
                'name' => 'Maître Marie Martin',
                'position' => 'Avocate Spécialisée - Droit de la Famille',
                'description' => 'Experte en droit matrimonial et protection de l\'enfance, Maître Martin défend les intérêts familiaux avec passion.',
                'image_path' => '/public/uploads/team/avocat5.jpeg',
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
                'image_path' => '/public/uploads/news/news1.jpg',
                'publish_date' => date('Y-m-d H:i:s'),
                'is_active' => 1
            ],
            [
                'id' => 2,
                'title' => 'Réforme du Droit de la Famille',
                'content' => 'Une analyse approfondie des récentes modifications du droit matrimonial.',
                'image_path' => '/public/uploads/news/news2.jpg',
                'publish_date' => date('Y-m-d H:i:s', strtotime('-1 week')),
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
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data' => $data
        ]);
        exit;
    }
}