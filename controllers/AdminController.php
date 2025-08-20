<?php
require_once 'includes/Database.php';

class AdminController {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if ($username === ADMIN_USERNAME && password_verify($password, ADMIN_PASSWORD)) {
                $_SESSION['admin_logged_in'] = true;
                header('Location: /admin/dashboard');
                exit;
            } else {
                $error = 'Identifiants incorrects';
            }
        }
        
        if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']) {
            header('Location: /admin/dashboard');
            exit;
        }
        
        include 'views/admin/login.php';
    }
    
    public function dashboard() {
        $this->requireAuth();
        
        $stats = [
            'contacts' => $this->db->query("SELECT COUNT(*) FROM contacts")->fetchColumn(),
            'new_contacts' => $this->db->query("SELECT COUNT(*) FROM contacts WHERE status = 'new'")->fetchColumn(),
            'services' => $this->db->query("SELECT COUNT(*) FROM services WHERE is_active = 1")->fetchColumn(),
            'team_members' => $this->db->query("SELECT COUNT(*) FROM team_members WHERE is_active = 1")->fetchColumn()
        ];
        
        $recent_contacts = $this->db->query("
            SELECT * FROM contacts 
            ORDER BY created_at DESC 
            LIMIT 5
        ")->fetchAll(PDO::FETCH_ASSOC);
        
        include 'views/admin/dashboard.php';
    }
    
    public function content() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            
            if ($action === 'update_content') {
                foreach ($_POST['content'] as $section => $keys) {
                    foreach ($keys as $key => $value) {
                        $stmt = $this->db->prepare("
                            INSERT OR REPLACE INTO site_content (section, key_name, value, updated_at) 
                            VALUES (?, ?, ?, datetime('now'))
                        ");
                        $stmt->execute([$section, $key, $value]);
                    }
                }
                $success = 'Contenu mis à jour avec succès!';
            } 
            elseif ($action === 'add_content_section') {
                $section = $_POST['new_section'] ?? '';
                $key = $_POST['new_key'] ?? '';
                $value = $_POST['new_value'] ?? '';
                
                if ($section && $key) {
                    $stmt = $this->db->prepare("
                        INSERT INTO site_content (section, key_name, value, updated_at) 
                        VALUES (?, ?, ?, datetime('now'))
                    ");
                    $stmt->execute([$section, $key, $value]);
                    $success = 'Nouveau contenu ajouté avec succès!';
                } else {
                    $success = 'Erreur : Section et clé sont requis.';
                }
            }
            elseif ($action === 'delete_content') {
                $section = $_POST['content_section'] ?? '';
                $key = $_POST['content_key'] ?? '';
                
                if ($section && $key) {
                    $stmt = $this->db->prepare("DELETE FROM site_content WHERE section = ? AND key_name = ?");
                    $stmt->execute([$section, $key]);
                    $success = 'Contenu supprimé avec succès!';
                }
            }
            elseif ($action === 'add_service') {
                $title = $_POST['title'] ?? '';
                $description = $_POST['description'] ?? '';
                $icon = $_POST['icon'] ?? 'fas fa-gavel';
                $color = $_POST['color'] ?? '#3b82f6';
                $detailed_content = $_POST['detailed_content'] ?? '';
                
                if ($title && $description) {
                    // Obtenir le prochain order_position
                    $stmt = $this->db->query("SELECT COALESCE(MAX(order_position), 0) + 1 as next_position FROM services");
                    $next_position = $stmt->fetchColumn();
                    
                    $stmt = $this->db->prepare("
                        INSERT INTO services (title, description, icon, color, detailed_content, is_active, order_position, created_at, updated_at)
                        VALUES (?, ?, ?, ?, ?, 1, ?, datetime('now'), datetime('now'))
                    ");
                    $stmt->execute([$title, $description, $icon, $color, $detailed_content, $next_position]);
                    $success = 'Service ajouté avec succès!';
                } else {
                    $success = 'Erreur : Titre et description sont requis.';
                }
            }
            elseif ($action === 'update_service') {
                $id = $_POST['service_id'];
                $title = $_POST['title'];
                $description = $_POST['description'];
                $icon = $_POST['icon'];
                $color = $_POST['color'];
                $detailed_content = $_POST['detailed_content'];
                
                $stmt = $this->db->prepare("
                    UPDATE services 
                    SET title = ?, description = ?, icon = ?, color = ?, detailed_content = ?, updated_at = datetime('now')
                    WHERE id = ?
                ");
                $stmt->execute([$title, $description, $icon, $color, $detailed_content, $id]);
                $success = 'Service mis à jour avec succès!';
            }
            elseif ($action === 'delete_service') {
                $id = $_POST['service_id'] ?? '';
                if ($id) {
                    $stmt = $this->db->prepare("DELETE FROM services WHERE id = ?");
                    $stmt->execute([$id]);
                    $success = 'Service supprimé avec succès!';
                } else {
                    $success = 'Erreur : ID du service manquant.';
                }
            }
            elseif ($action === 'reorder_services') {
                $orders = json_decode($_POST['orders'], true);
                if ($orders) {
                    foreach ($orders as $id => $position) {
                        $stmt = $this->db->prepare("UPDATE services SET order_position = ? WHERE id = ?");
                        $stmt->execute([$position, $id]);
                    }
                    $success = 'Ordre des services mis à jour avec succès!';
                }
            }
            elseif ($action === 'update_team') {
                $id = $_POST['team_id'];
                $name = $_POST['name'];
                $position = $_POST['position'];
                $description = $_POST['description'];
                
                $image_path = $this->handleImageUpload($_FILES['image'], $id);
                if (is_string($image_path) && strpos($image_path, 'Erreur') === 0) {
                    $success = $image_path;
                } else {
                    $stmt = $this->db->prepare("
                        UPDATE team_members 
                        SET name = ?, position = ?, description = ?, image_path = ?, updated_at = datetime('now')
                        WHERE id = ?
                    ");
                    $stmt->execute([$name, $position, $description, $image_path, $id]);
                    $success = 'Membre de l\'équipe mis à jour avec succès!';
                }
            } 
            elseif ($action === 'add_team') {
                $name = $_POST['name'] ?? '';
                $position = $_POST['position'] ?? '';
                $description = $_POST['description'] ?? '';
                
                if ($name && $position && $description && isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
                    $image_path = $this->handleImageUpload($_FILES['image']);
                    if (is_string($image_path) && strpos($image_path, 'Erreur') === 0) {
                        $success = $image_path;
                    } else {
                        $stmt = $this->db->prepare("
                            INSERT INTO team_members (name, position, description, image_path, is_active, order_position, created_at, updated_at)
                            VALUES (?, ?, ?, ?, 1, (SELECT COALESCE(MAX(order_position), 0) + 1 FROM team_members), datetime('now'), datetime('now'))
                        ");
                        $stmt->execute([$name, $position, $description, $image_path]);
                        $success = 'Membre de l\'équipe ajouté avec succès!';
                    }
                } else {
                    $success = 'Erreur : Tous les champs, y compris l\'image, sont requis pour ajouter un membre.';
                }
            } 
            elseif ($action === 'delete_team') {
                $id = $_POST['team_id'] ?? '';
                if ($id) {
                    // Supprimer l'image associée
                    $stmt = $this->db->prepare("SELECT image_path FROM team_members WHERE id = ?");
                    $stmt->execute([$id]);
                    $image_path = $stmt->fetchColumn();
                    if ($image_path && file_exists($_SERVER['DOCUMENT_ROOT'] . $image_path)) {
                        unlink($_SERVER['DOCUMENT_ROOT'] . $image_path);
                    }
                    
                    $stmt = $this->db->prepare("DELETE FROM team_members WHERE id = ?");
                    $stmt->execute([$id]);
                    $success = 'Membre de l\'équipe supprimé avec succès!';
                } else {
                    $success = 'Erreur : ID du membre manquant.';
                }
            }
            elseif ($action === 'reorder_team') {
                $orders = json_decode($_POST['orders'], true);
                if ($orders) {
                    foreach ($orders as $id => $position) {
                        $stmt = $this->db->prepare("UPDATE team_members SET order_position = ? WHERE id = ?");
                        $stmt->execute([$position, $id]);
                    }
                    $success = 'Ordre de l\'équipe mis à jour avec succès!';
                }
            }
        }
        
        // Charger le contenu
        $stmt = $this->db->query("SELECT section, key_name, value FROM site_content ORDER BY section, key_name");
        $content = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $content[$row['section']][$row['key_name']] = $row['value'];
        }
        
        // Charger toutes les sections uniques pour la gestion
        $stmt = $this->db->query("SELECT DISTINCT section FROM site_content ORDER BY section");
        $sections = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $services = $this->db->query("SELECT * FROM services ORDER BY order_position")->fetchAll(PDO::FETCH_ASSOC);
        $team = $this->db->query("SELECT * FROM team_members ORDER BY order_position")->fetchAll(PDO::FETCH_ASSOC);
        
        include 'views/admin/content.php';
    }
    
    private function handleImageUpload($file, $existing_id = null) {
        if ($file['error'] === UPLOAD_ERR_NO_FILE) {
            if ($existing_id) {
                // Si mise à jour sans nouveau fichier, conserver l'image existante
                $stmt = $this->db->prepare("SELECT image_path FROM team_members WHERE id = ?");
                $stmt->execute([$existing_id]);
                return $stmt->fetchColumn();
            }
            return 'Erreur : Aucun fichier sélectionné';
        }
        
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5MB
        $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/public/uploads/team/';
        
        // Créer le dossier si nécessaire
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        if (!in_array($file['type'], $allowed_types)) {
            return 'Erreur : Type de fichier non autorisé. Seuls JPG, PNG et GIF sont acceptés.';
        }
        
        if ($file['size'] > $max_size) {
            return 'Erreur : Le fichier est trop volumineux. Taille maximale : 5MB.';
        }
        
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('team_') . '.' . $extension;
        $destination = $upload_dir . $filename;
        $relative_path = '/public/uploads/team/' . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            // Supprimer l'ancienne image si mise à jour
            if ($existing_id) {
                $stmt = $this->db->prepare("SELECT image_path FROM team_members WHERE id = ?");
                $stmt->execute([$existing_id]);
                $old_image = $stmt->fetchColumn();
                if ($old_image && file_exists($_SERVER['DOCUMENT_ROOT'] . $old_image)) {
                    unlink($_SERVER['DOCUMENT_ROOT'] . $old_image);
                }
            }
            return $relative_path;
        }
        
        return 'Erreur : Échec de l\'upload du fichier.';
    }
    
    public function contacts() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            $id = $_POST['id'] ?? '';
            
            if ($action === 'mark_read' && $id) {
                $stmt = $this->db->prepare("UPDATE contacts SET status = 'read', updated_at = datetime('now') WHERE id = ?");
                $stmt->execute([$id]);
                $success = 'Message marqué comme lu';
            } elseif ($action === 'mark_new' && $id) {
                $stmt = $this->db->prepare("UPDATE contacts SET status = 'new', updated_at = datetime('now') WHERE id = ?");
                $stmt->execute([$id]);
                $success = 'Message marqué comme nouveau';
            } elseif ($action === 'delete' && $id) {
                $stmt = $this->db->prepare("DELETE FROM contacts WHERE id = ?");
                $stmt->execute([$id]);
                $success = 'Message supprimé';
            }
        }
        
        $contacts = $this->db->query("
            SELECT * FROM contacts 
            ORDER BY created_at DESC
        ")->fetchAll(PDO::FETCH_ASSOC);
        
        include 'views/admin/contacts.php';
    }
    
    public function messageDetail() {
        $this->requireAuth();
        
        $url = $_SERVER['REQUEST_URI'];
        $parts = explode('/', $url);
        $messageId = end($parts);
        
        if (!is_numeric($messageId)) {
            header('Location: /admin/contacts');
            exit;
        }
        
        $stmt = $this->db->prepare("SELECT * FROM contacts WHERE id = ?");
        $stmt->execute([$messageId]);
        $contact = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$contact) {
            header('Location: /admin/contacts');
            exit;
        }
        
        $updateStmt = $this->db->prepare("UPDATE contacts SET status = 'read', updated_at = datetime('now') WHERE id = ?");
        $updateStmt->execute([$messageId]);
        
        $filesStmt = $this->db->prepare("SELECT * FROM contact_files WHERE contact_id = ? ORDER BY uploaded_at");
        $filesStmt->execute([$messageId]);
        $files = $filesStmt->fetchAll(PDO::FETCH_ASSOC);
        
        include 'views/admin/message-detail.php';
    }
    
    public function settings() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $success = 'Paramètres mis à jour!';
        }
        
        include 'views/admin/settings.php';
    }
    
    public function logout() {
        session_destroy();
        header('Location: /admin');
        exit;
    }
    
    private function requireAuth() {
        if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
            header('Location: /admin');
            exit;
        }
    }
}
?>