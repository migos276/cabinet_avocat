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
        
        // Get statistics
        $stats = [
            'contacts' => $this->db->query("SELECT COUNT(*) FROM contacts")->fetchColumn(),
            'new_contacts' => $this->db->query("SELECT COUNT(*) FROM contacts WHERE status = 'new'")->fetchColumn(),
            'services' => $this->db->query("SELECT COUNT(*) FROM services WHERE is_active = 1")->fetchColumn(),
            'team_members' => $this->db->query("SELECT COUNT(*) FROM team_members WHERE is_active = 1")->fetchColumn()
        ];
        
        // Get recent contacts
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
            } elseif ($action === 'update_service') {
                $id = $_POST['service_id'];
                $title = $_POST['title'];
                $description = $_POST['description'];
                $icon = $_POST['icon'];
                $color = $_POST['color'];
                
                $stmt = $this->db->prepare("
                    UPDATE services 
                    SET title = ?, description = ?, icon = ?, color = ?, updated_at = datetime('now')
                    WHERE id = ?
                ");
                $stmt->execute([$title, $description, $icon, $color, $id]);
                $success = 'Service mis à jour avec succès!';
            } elseif ($action === 'update_team') {
                $id = $_POST['team_id'];
                $name = $_POST['name'];
                $position = $_POST['position'];
                $description = $_POST['description'];
                $image_url = $_POST['image_url'];
                
                $stmt = $this->db->prepare("
                    UPDATE team_members 
                    SET name = ?, position = ?, description = ?, image_url = ?, updated_at = datetime('now')
                    WHERE id = ?
                ");
                $stmt->execute([$name, $position, $description, $image_url, $id]);
                $success = 'Membre de l\'équipe mis à jour avec succès!';
            }
        }
        
        // Get all content
        $stmt = $this->db->query("SELECT section, key_name, value FROM site_content ORDER BY section, key_name");
        $content = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $content[$row['section']][$row['key_name']] = $row['value'];
        }
        
        // Get services
        $services = $this->db->query("SELECT * FROM services ORDER BY order_position")->fetchAll(PDO::FETCH_ASSOC);
        
        // Get team members
        $team = $this->db->query("SELECT * FROM team_members ORDER BY order_position")->fetchAll(PDO::FETCH_ASSOC);
        
        include 'views/admin/content.php';
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
        
        // Get all contacts
        $contacts = $this->db->query("
            SELECT * FROM contacts 
            ORDER BY created_at DESC
        ")->fetchAll(PDO::FETCH_ASSOC);
        
        include 'views/admin/contacts.php';
    }
    
    public function messageDetail() {
        $this->requireAuth();
        
        // Get message ID from URL
        $url = $_SERVER['REQUEST_URI'];
        $parts = explode('/', $url);
        $messageId = end($parts);
        
        if (!is_numeric($messageId)) {
            header('Location: /admin/contacts');
            exit;
        }
        
        // Get message details
        $stmt = $this->db->prepare("SELECT * FROM contacts WHERE id = ?");
        $stmt->execute([$messageId]);
        $contact = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$contact) {
            header('Location: /admin/contacts');
            exit;
        }
        
        // Marquer comme lu
        $updateStmt = $this->db->prepare("UPDATE contacts SET status = 'read', updated_at = datetime('now') WHERE id = ?");
        $updateStmt->execute([$messageId]);
        
        // Récupérer les fichiers
        $filesStmt = $this->db->prepare("SELECT * FROM contact_files WHERE contact_id = ? ORDER BY uploaded_at");
        $filesStmt->execute([$messageId]);
        $files = $filesStmt->fetchAll(PDO::FETCH_ASSOC);
        
        include 'views/admin/message-detail.php';
    }
    
    public function settings() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Handle settings update
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