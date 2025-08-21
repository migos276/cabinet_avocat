<?php
require_once 'includes/Database.php';
require_once 'includes/config.php';

class AdminController {
    private $db;
    private $uploadDir = CONTACT_UPLOAD_PATH;
    private $teamUploadDir = TEAM_UPLOAD_PATH;
    private $newsUploadDir = NEWS_UPLOAD_PATH;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    private function sendJsonResponse($success, $message = '') {
        header('Content-Type: application/json');
        echo json_encode(['success' => $success, 'message' => $message]);
        exit;
    }

    private function requireAuth() {
        if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
            header('Location: /admin');
            exit;
        }
    }

    private function handleImageUpload($file, $existing_id = null, $upload_dir = null) {
        $upload_dir = $upload_dir ?: $this->teamUploadDir;
        $absolute_dir = $_SERVER['DOCUMENT_ROOT'] . $upload_dir;

        // Log the upload attempt
        error_log("Image upload attempt: dir=$absolute_dir, existing_id=$existing_id, file=" . json_encode($file));

        // Handle no file uploaded case
        if ($file['error'] === UPLOAD_ERR_NO_FILE) {
            error_log("No file uploaded for existing_id=$existing_id");
            if ($existing_id) {
                $table = ($upload_dir === $this->teamUploadDir) ? 'team_members' : 'news';
                $stmt = $this->db->prepare("SELECT image_path FROM $table WHERE id = ?");
                $stmt->execute([$existing_id]);
                $existing_path = $stmt->fetchColumn();
                return $existing_path ?: null; // Return null instead of empty string for DB compatibility
            }
            return null; // Return null for no file
        }

        $allowed_types = ALLOWED_FILE_TYPES;
        $max_size = 5 * 1024 * 1024; // 5MB

        // Create directory if it doesn't exist
        if (!is_dir($absolute_dir)) {
            if (!mkdir($absolute_dir, 0755, true)) {
                error_log("Failed to create upload directory: $absolute_dir");
                return 'Erreur : Impossible de créer le répertoire de téléchargement.';
            }
        }

        // Verify file type
        if (!in_array($file['type'], $allowed_types)) {
            error_log("Invalid file type: " . $file['type']);
            return 'Erreur : Type de fichier non autorisé. Seuls JPG et PNG sont acceptés.';
        }

        // Verify file size
        if ($file['size'] > $max_size) {
            error_log("File size too large: " . $file['size']);
            return 'Erreur : Le fichier est trop volumineux. Taille maximale : 5MB.';
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid(($upload_dir === $this->teamUploadDir ? 'team_' : 'news_')) . '.' . $extension;
        $destination = $absolute_dir . $filename;
        $relative_path = $upload_dir . $filename;

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            // Delete old image if updating
            if ($existing_id) {
                $table = ($upload_dir === $this->teamUploadDir) ? 'team_members' : 'news';
                $stmt = $this->db->prepare("SELECT image_path FROM $table WHERE id = ?");
                $stmt->execute([$existing_id]);
                $old_image = $stmt->fetchColumn();
                if ($old_image && file_exists($_SERVER['DOCUMENT_ROOT'] . $old_image)) {
                    unlink($_SERVER['DOCUMENT_ROOT'] . $old_image);
                    error_log("Deleted old image: $old_image");
                }
            }
            error_log("Image uploaded successfully: $relative_path");
            return $relative_path;
        }

        error_log("Failed to move uploaded file to: $destination");
        return 'Erreur : Échec de l\'upload du fichier.';
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
                error_log("CSRF token validation failed in login");
                $error = 'Erreur de validation CSRF';
                include 'views/admin/login.php';
                return;
            }

            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            $stmt = $this->db->prepare("SELECT * FROM admin_users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && $user['is_active'] && password_verify($password, $user['password'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $user['id'];
                $updateStmt = $this->db->prepare("UPDATE admin_users SET last_login = datetime('now'), updated_at = datetime('now') WHERE id = ?");
                $updateStmt->execute([$user['id']]);
                error_log("Login successful for user: $username");
                header('Location: /admin/dashboard');
                exit;
            } else {
                error_log("Login failed for user: $username - Invalid credentials or inactive account");
                $error = 'Identifiants incorrects ou compte inactif';
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
            'team_members' => $this->db->query("SELECT COUNT(*) FROM team_members WHERE is_active = 1")->fetchColumn(),
            'news' => $this->db->query("SELECT COUNT(*) FROM news WHERE is_active = 1")->fetchColumn()
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
            if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
                error_log("CSRF token validation failed");
                $this->sendJsonResponse(false, 'Erreur de validation CSRF');
            }

            $action = $_POST['action'] ?? '';

            try {
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
                    $this->sendJsonResponse(true, 'Contenu mis à jour avec succès!');
                } elseif ($action === 'add_content_section') {
                    $section = $_POST['new_section'] ?? '';
                    $key = $_POST['new_key'] ?? '';
                    $value = $_POST['new_value'] ?? '';

                    if ($section && $key) {
                        $stmt = $this->db->prepare("
                            INSERT INTO site_content (section, key_name, value, updated_at) 
                            VALUES (?, ?, ?, datetime('now'))
                        ");
                        $stmt->execute([$section, $key, $value]);
                        $this->sendJsonResponse(true, 'Nouveau contenu ajouté avec succès!');
                    } else {
                        $this->sendJsonResponse(false, 'Erreur : Section et clé sont requis.');
                    }
                } elseif ($action === 'delete_content') {
                    $section = $_POST['content_section'] ?? '';
                    $key = $_POST['content_key'] ?? '';

                    if ($section && $key) {
                        $stmt = $this->db->prepare("DELETE FROM site_content WHERE section = ? AND key_name = ?");
                        $stmt->execute([$section, $key]);
                        $this->sendJsonResponse(true, 'Contenu supprimé avec succès!');
                    } else {
                        $this->sendJsonResponse(false, 'Erreur : Section et clé sont requis.');
                    }
                } elseif ($action === 'add_service') {
                    $title = $_POST['title'] ?? '';
                    $description = $_POST['description'] ?? '';
                    $icon = $_POST['icon'] ?? 'fas fa-gavel';
                    $color = $_POST['color'] ?? '#3b82f6';
                    $detailed_content = $_POST['detailed_content'] ?? '';

                    if ($title && $description) {
                        $stmt = $this->db->query("SELECT COALESCE(MAX(order_position), 0) + 1 as next_position FROM services");
                        $next_position = $stmt->fetchColumn();

                        $stmt = $this->db->prepare("
                            INSERT INTO services (title, description, icon, color, detailed_content, is_active, order_position, created_at, updated_at)
                            VALUES (?, ?, ?, ?, ?, 1, ?, datetime('now'), datetime('now'))
                        ");
                        $stmt->execute([$title, $description, $icon, $color, $detailed_content, $next_position]);
                        $this->sendJsonResponse(true, 'Service ajouté avec succès!');
                    } else {
                        $this->sendJsonResponse(false, 'Erreur : Titre et description sont requis.');
                    }
                } elseif ($action === 'update_service') {
                    $id = $_POST['service_id'] ?? '';
                    $title = $_POST['title'] ?? '';
                    $description = $_POST['description'] ?? '';
                    $icon = $_POST['icon'] ?? '';
                    $color = $_POST['color'] ?? '';
                    $detailed_content = $_POST['detailed_content'] ?? '';

                    if ($id && $title && $description) {
                        $stmt = $this->db->prepare("
                            UPDATE services 
                            SET title = ?, description = ?, icon = ?, color = ?, detailed_content = ?, updated_at = datetime('now')
                            WHERE id = ?
                        ");
                        $stmt->execute([$title, $description, $icon, $color, $detailed_content, $id]);
                        $this->sendJsonResponse(true, 'Service mis à jour avec succès!');
                    } else {
                        $this->sendJsonResponse(false, 'Erreur : Données invalides pour la mise à jour du service.');
                    }
                } elseif ($action === 'delete_service') {
                    $id = $_POST['service_id'] ?? '';
                    if ($id) {
                        $stmt = $this->db->prepare("DELETE FROM services WHERE id = ?");
                        $stmt->execute([$id]);
                        $this->sendJsonResponse(true, 'Service supprimé avec succès!');
                    } else {
                        $this->sendJsonResponse(false, 'Erreur : ID du service manquant.');
                    }
                } elseif ($action === 'reorder_services') {
                    $orders = json_decode($_POST['orders'] ?? '{}', true);
                    if ($orders) {
                        foreach ($orders as $id => $position) {
                            $stmt = $this->db->prepare("UPDATE services SET order_position = ? WHERE id = ?");
                            $stmt->execute([$position, $id]);
                        }
                        $this->sendJsonResponse(true, 'Ordre des services mis à jour avec succès!');
                    } else {
                        $this->sendJsonResponse(false, 'Erreur : Données d\'ordre invalides.');
                    }
                } elseif ($action === 'add_team') {
                    $name = $_POST['name'] ?? '';
                    $position = $_POST['position'] ?? '';
                    $description = $_POST['description'] ?? '';

                    if ($name && $position && $description) {
                        $image_path = $this->handleImageUpload($_FILES['image'] ?? [], null, $this->teamUploadDir);
                        if (is_string($image_path) && strpos($image_path, 'Erreur') === 0) {
                            $this->sendJsonResponse(false, $image_path);
                        } else {
                            $stmt = $this->db->prepare("
                                INSERT INTO team_members (name, position, description, image_path, is_active, order_position, created_at, updated_at)
                                VALUES (?, ?, ?, ?, 1, (SELECT COALESCE(MAX(order_position), 0) + 1 FROM team_members), datetime('now'), datetime('now'))
                            ");
                            $stmt->execute([$name, $position, $description, $image_path]);
                            $this->sendJsonResponse(true, 'Membre de l\'équipe ajouté avec succès!');
                        }
                    } else {
                        $this->sendJsonResponse(false, 'Erreur : Nom, poste et description sont requis.');
                    }
                } elseif ($action === 'update_team') {
                    $id = $_POST['team_id'] ?? '';
                    $name = $_POST['name'] ?? '';
                    $position = $_POST['position'] ?? '';
                    $description = $_POST['description'] ?? '';

                    if ($id && $name && $position && $description) {
                        $image_path = $this->handleImageUpload($_FILES['image'] ?? [], $id, $this->teamUploadDir);
                        if (is_string($image_path) && strpos($image_path, 'Erreur') === 0) {
                            $this->sendJsonResponse(false, $image_path);
                        } else {
                            $stmt = $this->db->prepare("
                                UPDATE team_members 
                                SET name = ?, position = ?, description = ?, image_path = ?, updated_at = datetime('now')
                                WHERE id = ?
                            ");
                            $stmt->execute([$name, $position, $description, $image_path, $id]);
                            $this->sendJsonResponse(true, 'Membre de l\'équipe mis à jour avec succès!');
                        }
                    } else {
                        $this->sendJsonResponse(false, 'Erreur : Données invalides pour la mise à jour du membre.');
                    }
                } elseif ($action === 'delete_team') {
                    $id = $_POST['team_id'] ?? '';
                    if ($id) {
                        $stmt = $this->db->prepare("SELECT image_path FROM team_members WHERE id = ?");
                        $stmt->execute([$id]);
                        $image_path = $stmt->fetchColumn();
                        if ($image_path && file_exists($_SERVER['DOCUMENT_ROOT'] . $image_path)) {
                            unlink($_SERVER['DOCUMENT_ROOT'] . $image_path);
                            error_log("Deleted team image: $image_path");
                        }

                        $stmt = $this->db->prepare("DELETE FROM team_members WHERE id = ?");
                        $stmt->execute([$id]);
                        $this->sendJsonResponse(true, 'Membre de l\'équipe supprimé avec succès!');
                    } else {
                        $this->sendJsonResponse(false, 'Erreur : ID du membre manquant.');
                    }
                } elseif ($action === 'reorder_team') {
                    $orders = json_decode($_POST['orders'] ?? '{}', true);
                    if ($orders) {
                        foreach ($orders as $id => $position) {
                            $stmt = $this->db->prepare("UPDATE team_members SET order_position = ? WHERE id = ?");
                            $stmt->execute([$position, $id]);
                        }
                        $this->sendJsonResponse(true, 'Ordre de l\'équipe mis à jour avec succès!');
                    } else {
                        $this->sendJsonResponse(false, 'Erreur : Données d\'ordre invalides.');
                    }
                } elseif ($action === 'add_news') {
                    $title = trim($_POST['title'] ?? '');
                    $content = trim($_POST['content'] ?? '');
                    $publish_date = trim($_POST['publish_date'] ?? '');

                    error_log("Add news attempt: title=$title, content_length=" . strlen($content) . ", publish_date=$publish_date");

                    if ($title && $content && $publish_date) {
                        $image_path = $this->handleImageUpload($_FILES['image'] ?? [], null, $this->newsUploadDir);
                        if (is_string($image_path) && strpos($image_path, 'Erreur') === 0) {
                            error_log("Image upload failed: $image_path");
                            $this->sendJsonResponse(false, $image_path);
                        } else {
                            $stmt = $this->db->prepare("
                                INSERT INTO news (title, content, image_path, publish_date, is_active, order_position, created_at, updated_at)
                                VALUES (?, ?, ?, ?, 1, (SELECT COALESCE(MAX(order_position), 0) + 1 FROM news), datetime('now'), datetime('now'))
                            ");
                            $stmt->execute([$title, $content, $image_path, $publish_date]);
                            error_log("News added successfully: ID=" . $this->db->lastInsertId());
                            $this->sendJsonResponse(true, 'Actualité ajoutée avec succès!');
                        }
                    } else {
                        $errors = [];
                        if (!$title) $errors[] = 'Titre manquant';
                        if (!$content) $errors[] = 'Contenu manquant';
                        if (!$publish_date) $errors[] = 'Date de publication manquante';
                        error_log("Add news failed: " . implode(', ', $errors));
                        $this->sendJsonResponse(false, 'Erreur : ' . implode(', ', $errors));
                    }
                } elseif ($action === 'update_news') {
                    $id = $_POST['news_id'] ?? '';
                    $title = trim($_POST['title'] ?? '');
                    $content = trim($_POST['content'] ?? '');
                    $publish_date = trim($_POST['publish_date'] ?? '');

                    error_log("Update news attempt: id=$id, title=$title, content_length=" . strlen($content) . ", publish_date=$publish_date");

                    if ($id && $title && $content && $publish_date) {
                        $image_path = $this->handleImageUpload($_FILES['image'] ?? [], $id, $this->newsUploadDir);
                        if (is_string($image_path) && strpos($image_path, 'Erreur') === 0) {
                            error_log("Image upload failed: $image_path");
                            $this->sendJsonResponse(false, $image_path);
                        } else {
                            $stmt = $this->db->prepare("
                                UPDATE news 
                                SET title = ?, content = ?, image_path = ?, publish_date = ?, updated_at = datetime('now')
                                WHERE id = ?
                            ");
                            $stmt->execute([$title, $content, $image_path, $publish_date, $id]);
                            error_log("News updated successfully: ID=$id");
                            $this->sendJsonResponse(true, 'Actualité mise à jour avec succès!');
                        }
                    } else {
                        $errors = [];
                        if (!$id) $errors[] = 'ID manquant';
                        if (!$title) $errors[] = 'Titre manquant';
                        if (!$content) $errors[] = 'Contenu manquant';
                        if (!$publish_date) $errors[] = 'Date de publication manquante';
                        error_log("Update news failed: " . implode(', ', $errors));
                        $this->sendJsonResponse(false, 'Erreur : ' . implode(', ', $errors));
                    }
                } elseif ($action === 'delete_news') {
                    $id = $_POST['news_id'] ?? '';
                    if ($id) {
                        $stmt = $this->db->prepare("SELECT image_path FROM news WHERE id = ?");
                        $stmt->execute([$id]);
                        $image_path = $stmt->fetchColumn();
                        if ($image_path && file_exists($_SERVER['DOCUMENT_ROOT'] . $image_path)) {
                            unlink($_SERVER['DOCUMENT_ROOT'] . $image_path);
                            error_log("Deleted news image: $image_path");
                        }

                        $stmt = $this->db->prepare("DELETE FROM news WHERE id = ?");
                        $stmt->execute([$id]);
                        error_log("News deleted successfully: ID=$id");
                        $this->sendJsonResponse(true, 'Actualité supprimée avec succès!');
                    } else {
                        error_log("Delete news failed: Missing ID");
                        $this->sendJsonResponse(false, 'Erreur : ID de l\'actualité manquant.');
                    }
                } elseif ($action === 'reorder_news') {
                    $orders = json_decode($_POST['orders'] ?? '{}', true);
                    if ($orders) {
                        foreach ($orders as $id => $position) {
                            $stmt = $this->db->prepare("UPDATE news SET order_position = ? WHERE id = ?");
                            $stmt->execute([$position, $id]);
                        }
                        error_log("News order updated successfully");
                        $this->sendJsonResponse(true, 'Ordre des actualités mis à jour avec succès!');
                    } else {
                        error_log("Reorder news failed: Invalid order data");
                        $this->sendJsonResponse(false, 'Erreur : Données d\'ordre invalides.');
                    }
                } else {
                    error_log("Unknown action: $action");
                    $this->sendJsonResponse(false, 'Action non reconnue.');
                }
            } catch (Exception $e) {
                error_log("Server error in content action $action: " . $e->getMessage());
                $this->sendJsonResponse(false, 'Erreur serveur : ' . $e->getMessage());
            }
        }

        // Load content
        $stmt = $this->db->query("SELECT section, key_name, value FROM site_content ORDER BY section, key_name");
        $content = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $content[$row['section']][$row['key_name']] = $row['value'];
        }

        $services = $this->db->query("SELECT * FROM services WHERE is_active = 1 ORDER BY order_position")->fetchAll(PDO::FETCH_ASSOC);
        $team = $this->db->query("SELECT * FROM team_members WHERE is_active = 1 ORDER BY order_position")->fetchAll(PDO::FETCH_ASSOC);
        $news = $this->db->query("SELECT * FROM news WHERE is_active = 1 ORDER BY order_position")->fetchAll(PDO::FETCH_ASSOC);

        include 'views/admin/content.php';
    }

    public function contacts() {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
                error_log("CSRF token validation failed in contacts");
                $this->sendJsonResponse(false, 'Erreur de validation CSRF');
            }

            $action = $_POST['action'] ?? '';
            $id = $_POST['id'] ?? '';

            try {
                if ($action === 'mark_read' && $id) {
                    $stmt = $this->db->prepare("UPDATE contacts SET status = 'read', updated_at = datetime('now') WHERE id = ?");
                    $stmt->execute([$id]);
                    $this->sendJsonResponse(true, 'Message marqué comme lu');
                } elseif ($action === 'mark_new' && $id) {
                    $stmt = $this->db->prepare("UPDATE contacts SET status = 'new', updated_at = datetime('now') WHERE id = ?");
                    $stmt->execute([$id]);
                    $this->sendJsonResponse(true, 'Message marqué comme nouveau');
                } elseif ($action === 'delete' && $id) {
                    $stmt = $this->db->prepare("SELECT file_path FROM contact_files WHERE contact_id = ?");
                    $stmt->execute([$id]);
                    $files = $stmt->fetchAll(PDO::FETCH_COLUMN);
                    foreach ($files as $file) {
                        if (file_exists($_SERVER['DOCUMENT_ROOT'] . $file)) {
                            unlink($_SERVER['DOCUMENT_ROOT'] . $file);
                            error_log("Deleted contact file: $file");
                        }
                    }
                    $stmt = $this->db->prepare("DELETE FROM contact_files WHERE contact_id = ?");
                    $stmt->execute([$id]);
                    $stmt = $this->db->prepare("DELETE FROM contacts WHERE id = ?");
                    $stmt->execute([$id]);
                    $this->sendJsonResponse(true, 'Message supprimé');
                } else {
                    error_log("Unknown contacts action: $action");
                    $this->sendJsonResponse(false, 'Action non reconnue.');
                }
            } catch (Exception $e) {
                error_log("Server error in contacts action $action: " . $e->getMessage());
                $this->sendJsonResponse(false, 'Erreur serveur : ' . $e->getMessage());
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
            if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
                error_log("CSRF token validation failed in settings");
                $this->sendJsonResponse(false, 'Erreur de validation CSRF');
            }

            try {
                // Handle settings update logic here
                $this->sendJsonResponse(true, 'Paramètres mis à jour avec succès!');
            } catch (Exception $e) {
                error_log("Server error in settings: " . $e->getMessage());
                $this->sendJsonResponse(false, 'Erreur serveur : ' . $e->getMessage());
            }
        }

        include 'views/admin/settings.php';
    }

    public function logout() {
        session_destroy();
        header('Location: /admin');
        exit;
    }
}
?>