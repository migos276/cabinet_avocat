<?php
require_once 'includes/Database.php';
require_once 'includes/config.php';

class AdminController {
    private $db;
    private $uploadDir = CONTACT_UPLOAD_PATH;
    private $teamUploadDir = TEAM_UPLOAD_PATH;
    private $newsUploadDir = NEWS_UPLOAD_PATH;
    private $eventsUploadDir = '/public/uploads/events/';

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $database = new Database();
        $this->db = $database->getConnection();
    }

    private function redirectWithMessage($success, $message, $location = '/admin/content') {
        $_SESSION['flash_message'] = ['success' => $success, 'message' => $message];
        header("Location: $location");
        exit;
    }

    private function getStats() {
        try {
            return [
                'contacts' => $this->db->query("SELECT COUNT(*) FROM contacts")->fetchColumn(),
                'new_contacts' => $this->db->query("SELECT COUNT(*) FROM contacts WHERE status = 'new'")->fetchColumn(),
                'services' => $this->db->query("SELECT COUNT(*) FROM services WHERE is_active = 1")->fetchColumn(),
                'team_members' => $this->db->query("SELECT COUNT(*) FROM team_members WHERE is_active = 1")->fetchColumn(),
                'news' => $this->db->query("SELECT COUNT(*) FROM news WHERE is_active = 1")->fetchColumn(),
                'appointments' => $this->db->query("SELECT COUNT(*) FROM appointments WHERE status IN ('pending', 'confirmed')")->fetchColumn()
            ];
        } catch (PDOException $e) {
            error_log("Database error in getStats: " . $e->getMessage());
            return [
                'contacts' => 0,
                'new_contacts' => 0,
                'services' => 0,
                'team_members' => 0,
                'news' => 0,
                'appointments' => 0
            ];
        }
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

        error_log("Image upload attempt: dir=$absolute_dir, existing_id=$existing_id, file=" . json_encode($file));

        if ($file['error'] === UPLOAD_ERR_NO_FILE) {
            error_log("No file uploaded for existing_id=$existing_id");
            if ($existing_id) {
                $table = ($upload_dir === $this->teamUploadDir) ? 'team_members' : (($upload_dir === $this->newsUploadDir) ? 'news' : 'events');
                $stmt = $this->db->prepare("SELECT image_path FROM $table WHERE id = ?");
                $stmt->execute([$existing_id]);
                $existing_path = $stmt->fetchColumn();
                return $existing_path ?: '';
            }
            return '';
        }

        $allowed_types = ALLOWED_FILE_TYPES;
        $max_size = 5 * 1024 * 1024;

        if (!is_dir($absolute_dir)) {
            if (!mkdir($absolute_dir, 0755, true)) {
                error_log("Failed to create upload directory: $absolute_dir");
                return 'Erreur : Impossible de créer le répertoire de téléchargement.';
            }
        }

        if (!in_array($file['type'], $allowed_types)) {
            error_log("Invalid file type: " . $file['type']);
            return 'Erreur : Type de fichier non autorisé. Seuls JPG et PNG sont acceptés.';
        }

        if ($file['size'] > $max_size) {
            error_log("File size too large: " . $file['size']);
            return 'Erreur : Le fichier est trop volumineux. Taille maximale : 5MB.';
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = uniqid(($upload_dir === $this->teamUploadDir ? 'team_' : ($upload_dir === $this->newsUploadDir ? 'news_' : 'event_'))) . '.' . $extension;
        $destination = $absolute_dir . $filename;
        $relative_path = $upload_dir . $filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            if ($existing_id) {
                $table = ($upload_dir === $this->teamUploadDir) ? 'team_members' : (($upload_dir === $this->newsUploadDir) ? 'news' : 'events');
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
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
                error_log("CSRF token validation failed in login");
                $error = 'Erreur de validation CSRF. Veuillez réessayer.';
                include 'views/admin/login.php';
                return;
            }

            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if (empty($username) || empty($password)) {
                error_log("Login failed: Empty username or password");
                $error = 'Veuillez fournir un nom d\'utilisateur et un mot de passe.';
                include 'views/admin/login.php';
                return;
            }

            try {
                $stmt = $this->db->prepare("SELECT * FROM admin_users WHERE username = ?");
                $stmt->execute([$username]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user && $user['is_active'] && password_verify($password, $user['password'])) {
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $user['id'];
                    session_regenerate_id(true);
                    $updateStmt = $this->db->prepare("UPDATE admin_users SET last_login = datetime('now'), updated_at = datetime('now') WHERE id = ?");
                    $updateStmt->execute([$user['id']]);
                    error_log("Login successful for user: $username");
                    header('Location: /admin/dashboard');
                    exit;
                } else {
                    error_log("Login failed for user: $username - Invalid credentials or inactive account");
                    $error = 'Identifiants incorrects ou compte inactif.';
                }
            } catch (PDOException $e) {
                error_log("Database error during login: " . $e->getMessage());
                $error = 'Erreur serveur lors de la connexion. Veuillez réessayer plus tard.';
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
        header('Cache-Control: no-cache, must-revalidate');

        try {
            $stats = $this->getStats();

            $recent_contacts = $this->db->query("
                SELECT c.*, a.status as appointment_status, s.start_time as appointment_time 
                FROM contacts c 
                LEFT JOIN appointments a ON c.appointment_id = a.id 
                LEFT JOIN appointment_slots s ON a.slot_id = s.id 
                ORDER BY c.created_at DESC 
                LIMIT 5
            ")->fetchAll(PDO::FETCH_ASSOC);

            $upcoming_appointments = $this->db->query("
                SELECT c.name, c.email, a.status as appointment_status, s.start_time as appointment_time 
                FROM appointments a 
                JOIN contacts c ON a.contact_id = c.id 
                JOIN appointment_slots s ON a.slot_id = s.id 
                WHERE a.status IN ('pending', 'confirmed') AND s.start_time >= datetime('now')
                ORDER BY s.start_time ASC
                LIMIT 5
            ")->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in dashboard: " . $e->getMessage());
            $error = 'Erreur serveur lors du chargement du tableau de bord.';
            include 'views/admin/error.php';
            return;
        }

        include 'views/admin/dashboard.php';
    }

    public function content() {
        $this->requireAuth();
        header('Cache-Control: no-cache, must-revalidate');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
                error_log("CSRF token validation failed in content");
                $this->redirectWithMessage(false, 'Erreur de validation CSRF');
            }

            $action = trim($_POST['action'] ?? '');

            try {
                if ($action === 'update_content') {
                    foreach ($_POST['content'] as $section => $keys) {
                        foreach ($keys as $key => $value) {
                            $section = htmlspecialchars(trim($section));
                            $key = htmlspecialchars(trim($key));
                            $value = htmlspecialchars(trim($value));
                            $stmt = $this->db->prepare("
                                INSERT OR REPLACE INTO site_content (section, key_name, value, updated_at) 
                                VALUES (?, ?, ?, datetime('now'))
                            ");
                            if (!$stmt->execute([$section, $key, $value])) {
                                error_log("Failed to update content: section=$section, key=$key");
                                $this->redirectWithMessage(false, 'Erreur : Échec de la mise à jour du contenu.');
                            }
                        }
                    }
                    $this->redirectWithMessage(true, 'Contenu mis à jour avec succès!');
                } elseif ($action === 'add_content_section') {
                    $section = htmlspecialchars(trim($_POST['new_section'] ?? ''));
                    $key = htmlspecialchars(trim($_POST['new_key'] ?? ''));
                    $value = htmlspecialchars(trim($_POST['new_value'] ?? ''));

                    if ($section && $key) {
                        $stmt = $this->db->prepare("
                            INSERT INTO site_content (section, key_name, value, updated_at) 
                            VALUES (?, ?, ?, datetime('now'))
                        ");
                        if (!$stmt->execute([$section, $key, $value])) {
                            error_log("Failed to add content section: section=$section, key=$key");
                            $this->redirectWithMessage(false, 'Erreur : Échec de l\'ajout du contenu.');
                        }
                        $this->redirectWithMessage(true, 'Nouveau contenu ajouté avec succès!');
                    } else {
                        $this->redirectWithMessage(false, 'Erreur : Section et clé sont requis.');
                    }
                } elseif ($action === 'delete_content') {
                    $section = htmlspecialchars(trim($_POST['content_section'] ?? ''));
                    $key = htmlspecialchars(trim($_POST['content_key'] ?? ''));

                    if ($section && $key) {
                        $stmt = $this->db->prepare("DELETE FROM site_content WHERE section = ? AND key_name = ?");
                        if (!$stmt->execute([$section, $key])) {
                            error_log("Failed to delete content: section=$section, key=$key");
                            $this->redirectWithMessage(false, 'Erreur : Échec de la suppression du contenu.');
                        }
                        $this->redirectWithMessage(true, 'Contenu supprimé avec succès!');
                    } else {
                        $this->redirectWithMessage(false, 'Erreur : Section et clé sont requis.');
                    }
                } elseif ($action === 'add_service') {
                    $title = htmlspecialchars(trim($_POST['title'] ?? ''));
                    $description = htmlspecialchars(trim($_POST['description'] ?? ''));
                    $icon = htmlspecialchars(trim($_POST['icon'] ?? 'fas fa-gavel'));
                    $color = htmlspecialchars(trim($_POST['color'] ?? '#3b82f6'));
                    $detailed_content = htmlspecialchars(trim($_POST['detailed_content'] ?? ''));

                    if ($title && $description) {
                        $stmt = $this->db->query("SELECT COALESCE(MAX(order_position), 0) + 1 as next_position FROM services");
                        $next_position = $stmt->fetchColumn();

                        $stmt = $this->db->prepare("
                            INSERT INTO services (title, description, icon, color, detailed_content, is_active, order_position, created_at, updated_at)
                            VALUES (?, ?, ?, ?, ?, 1, ?, datetime('now'), datetime('now'))
                        ");
                        if (!$stmt->execute([$title, $description, $icon, $color, $detailed_content, $next_position])) {
                            error_log("Failed to add service: title=$title");
                            $this->redirectWithMessage(false, 'Erreur : Échec de l\'ajout du service.');
                        }
                        $this->redirectWithMessage(true, 'Service ajouté avec succès!');
                    } else {
                        $this->redirectWithMessage(false, 'Erreur : Titre et description sont requis.');
                    }
                } elseif ($action === 'update_service') {
                    $id = trim($_POST['service_id'] ?? '');
                    $title = htmlspecialchars(trim($_POST['title'] ?? ''));
                    $description = htmlspecialchars(trim($_POST['description'] ?? ''));
                    $icon = htmlspecialchars(trim($_POST['icon'] ?? ''));
                    $color = htmlspecialchars(trim($_POST['color'] ?? ''));
                    $detailed_content = htmlspecialchars(trim($_POST['detailed_content'] ?? ''));

                    if ($id && $title && $description) {
                        $stmt = $this->db->prepare("
                            UPDATE services 
                            SET title = ?, description = ?, icon = ?, color = ?, detailed_content = ?, updated_at = datetime('now')
                            WHERE id = ?
                        ");
                        if (!$stmt->execute([$title, $description, $icon, $color, $detailed_content, $id])) {
                            error_log("Failed to update service: id=$id");
                            $this->redirectWithMessage(false, 'Erreur : Échec de la mise à jour du service.');
                        }
                        $this->redirectWithMessage(true, 'Service mis à jour avec succès!');
                    } else {
                        $this->redirectWithMessage(false, 'Erreur : Données invalides pour la mise à jour du service.');
                    }
                } elseif ($action === 'delete_service') {
                    $id = trim($_POST['service_id'] ?? '');
                    if ($id) {
                        $stmt = $this->db->prepare("DELETE FROM services WHERE id = ?");
                        if (!$stmt->execute([$id])) {
                            error_log("Failed to delete service: id=$id");
                            $this->redirectWithMessage(false, 'Erreur : Échec de la suppression du service.');
                        }
                        $this->redirectWithMessage(true, 'Service supprimé avec succès!');
                    } else {
                        $this->redirectWithMessage(false, 'Erreur : ID du service manquant.');
                    }
                } elseif ($action === 'reorder_services') {
                    $orders = json_decode($_POST['orders'] ?? '{}', true);
                    if ($orders && is_array($orders)) {
                        foreach ($orders as $id => $position) {
                            $stmt = $this->db->prepare("UPDATE services SET order_position = ? WHERE id = ?");
                            if (!$stmt->execute([(int)$position, (int)$id])) {
                                error_log("Failed to reorder service: id=$id, position=$position");
                                $this->redirectWithMessage(false, 'Erreur : Échec de la réorganisation des services.');
                            }
                        }
                        $this->redirectWithMessage(true, 'Ordre des services mis à jour avec succès!');
                    } else {
                        $this->redirectWithMessage(false, 'Erreur : Données d\'ordre invalides.');
                    }
                } elseif ($action === 'add_team') {
                    $name = htmlspecialchars(trim($_POST['name'] ?? ''));
                    $position = htmlspecialchars(trim($_POST['position'] ?? ''));
                    $description = htmlspecialchars(trim($_POST['description'] ?? ''));

                    if ($name && $position && $description) {
                        $image_path = $this->handleImageUpload($_FILES['image'] ?? [], null, $this->teamUploadDir);
                        if (is_string($image_path) && strpos($image_path, 'Erreur') === 0) {
                            error_log("Image upload failed for team: $image_path");
                            $this->redirectWithMessage(false, $image_path);
                        } else {
                            $stmt = $this->db->prepare("
                                INSERT INTO team_members (name, position, description, image_path, is_active, order_position, created_at, updated_at)
                                VALUES (?, ?, ?, ?, 1, (SELECT COALESCE(MAX(order_position), 0) + 1 FROM team_members), datetime('now'), datetime('now'))
                            ");
                            if (!$stmt->execute([$name, $position, $description, $image_path])) {
                                error_log("Failed to add team member: name=$name");
                                $this->redirectWithMessage(false, 'Erreur : Échec de l\'ajout du membre.');
                            }
                            $this->redirectWithMessage(true, 'Membre de l\'équipe ajouté avec succès!');
                        }
                    } else {
                        $this->redirectWithMessage(false, 'Erreur : Nom, poste et description sont requis.');
                    }
                } elseif ($action === 'update_team') {
                    $id = trim($_POST['team_id'] ?? '');
                    $name = htmlspecialchars(trim($_POST['name'] ?? ''));
                    $position = htmlspecialchars(trim($_POST['position'] ?? ''));
                    $description = htmlspecialchars(trim($_POST['description'] ?? ''));

                    if ($id && $name && $position && $description) {
                        $image_path = $this->handleImageUpload($_FILES['image'] ?? [], $id, $this->teamUploadDir);
                        if (is_string($image_path) && strpos($image_path, 'Erreur') === 0) {
                            error_log("Image upload failed for team update: $image_path");
                            $this->redirectWithMessage(false, $image_path);
                        } else {
                            $stmt = $this->db->prepare("
                                UPDATE team_members 
                                SET name = ?, position = ?, description = ?, image_path = ?, updated_at = datetime('now')
                                WHERE id = ?
                            ");
                            if (!$stmt->execute([$name, $position, $description, $image_path, $id])) {
                                error_log("Failed to update team member: id=$id");
                                $this->redirectWithMessage(false, 'Erreur : Échec de la mise à jour du membre.');
                            }
                            $this->redirectWithMessage(true, 'Membre de l\'équipe mis à jour avec succès!');
                        }
                    } else {
                        $this->redirectWithMessage(false, 'Erreur : Données invalides pour la mise à jour du membre.');
                    }
                } elseif ($action === 'delete_team') {
                    $id = trim($_POST['team_id'] ?? '');
                    if ($id) {
                        $stmt = $this->db->prepare("SELECT image_path FROM team_members WHERE id = ?");
                        $stmt->execute([$id]);
                        $image_path = $stmt->fetchColumn();
                        if ($image_path && file_exists($_SERVER['DOCUMENT_ROOT'] . $image_path)) {
                            if (!unlink($_SERVER['DOCUMENT_ROOT'] . $image_path)) {
                                error_log("Failed to delete team image: $image_path");
                            } else {
                                error_log("Deleted team image: $image_path");
                            }
                        }
                        $stmt = $this->db->prepare("DELETE FROM team_members WHERE id = ?");
                        if (!$stmt->execute([$id])) {
                            error_log("Failed to delete team member: id=$id");
                            $this->redirectWithMessage(false, 'Erreur : Échec de la suppression du membre.');
                        }
                        $this->redirectWithMessage(true, 'Membre de l\'équipe supprimé avec succès!');
                    } else {
                        $this->redirectWithMessage(false, 'Erreur : ID du membre manquant.');
                    }
                } elseif ($action === 'reorder_team') {
                    $orders = json_decode($_POST['orders'] ?? '{}', true);
                    if ($orders && is_array($orders)) {
                        foreach ($orders as $id => $position) {
                            $stmt = $this->db->prepare("UPDATE team_members SET order_position = ? WHERE id = ?");
                            if (!$stmt->execute([(int)$position, (int)$id])) {
                                error_log("Failed to reorder team member: id=$id, position=$position");
                                $this->redirectWithMessage(false, 'Erreur : Échec de la réorganisation de l\'équipe.');
                            }
                        }
                        $this->redirectWithMessage(true, 'Ordre de l\'équipe mis à jour avec succès!');
                    } else {
                        $this->redirectWithMessage(false, 'Erreur : Données d\'ordre invalides.');
                    }
                } elseif ($action === 'add_news') {
                    $title = htmlspecialchars(trim($_POST['title'] ?? ''));
                    $content = htmlspecialchars(trim($_POST['content'] ?? ''));
                    $publish_date = trim($_POST['publish_date'] ?? '');

                    error_log("Add news attempt: title=$title, content_length=" . strlen($content) . ", publish_date=$publish_date");

                    if ($title && $content && $publish_date) {
                        $image_path = $this->handleImageUpload($_FILES['image'] ?? [], null, $this->newsUploadDir);
                        if (is_string($image_path) && strpos($image_path, 'Erreur') === 0) {
                            error_log("Image upload failed for news: $image_path");
                            $this->redirectWithMessage(false, $image_path);
                        } else {
                            $stmt = $this->db->prepare("
                                INSERT INTO news (title, content, image_path, publish_date, is_active, order_position, created_at, updated_at)
                                VALUES (?, ?, ?, ?, 1, (SELECT COALESCE(MAX(order_position), 0) + 1 FROM news), datetime('now'), datetime('now'))
                            ");
                            if (!$stmt->execute([$title, $content, $image_path, $publish_date])) {
                                error_log("Failed to add news: title=$title");
                                $this->redirectWithMessage(false, 'Erreur : Échec de l\'ajout de l\'actualité.');
                            }
                            error_log("News added successfully: ID=" . $this->db->lastInsertId());
                            $this->redirectWithMessage(true, 'Actualité ajoutée avec succès!');
                        }
                    } else {
                        $errors = [];
                        if (!$title) $errors[] = 'Titre manquant';
                        if (!$content) $errors[] = 'Contenu manquant';
                        if (!$publish_date) $errors[] = 'Date de publication manquante';
                        error_log("Add news failed: " . implode(', ', $errors));
                        $this->redirectWithMessage(false, 'Erreur : ' . implode(', ', $errors));
                    }
                } elseif ($action === 'update_news') {
                    $id = trim($_POST['news_id'] ?? '');
                    $title = htmlspecialchars(trim($_POST['title'] ?? ''));
                    $content = htmlspecialchars(trim($_POST['content'] ?? ''));
                    $publish_date = trim($_POST['publish_date'] ?? '');

                    error_log("Update news attempt: id=$id, title=$title, content_length=" . strlen($content) . ", publish_date=$publish_date");

                    if ($id && $title && $content && $publish_date) {
                        $image_path = $this->handleImageUpload($_FILES['image'] ?? [], $id, $this->newsUploadDir);
                        if (is_string($image_path) && strpos($image_path, 'Erreur') === 0) {
                            error_log("Image upload failed for news update: $image_path");
                            $this->redirectWithMessage(false, $image_path);
                        } else {
                            $stmt = $this->db->prepare("
                                UPDATE news 
                                SET title = ?, content = ?, image_path = ?, publish_date = ?, updated_at = datetime('now')
                                WHERE id = ?
                            ");
                            if (!$stmt->execute([$title, $content, $image_path, $publish_date, $id])) {
                                error_log("Failed to update news: id=$id");
                                $this->redirectWithMessage(false, 'Erreur : Échec de la mise à jour de l\'actualité.');
                            }
                            error_log("News updated successfully: ID=$id");
                            $this->redirectWithMessage(true, 'Actualité mise à jour avec succès!');
                        }
                    } else {
                        $errors = [];
                        if (!$id) $errors[] = 'ID manquant';
                        if (!$title) $errors[] = 'Titre manquant';
                        if (!$content) $errors[] = 'Contenu manquant';
                        if (!$publish_date) $errors[] = 'Date de publication manquante';
                        error_log("Update news failed: " . implode(', ', $errors));
                        $this->redirectWithMessage(false, 'Erreur : ' . implode(', ', $errors));
                    }
                } elseif ($action === 'delete_news') {
                    $id = trim($_POST['news_id'] ?? '');
                    if ($id) {
                        $stmt = $this->db->prepare("SELECT image_path FROM news WHERE id = ?");
                        $stmt->execute([$id]);
                        $image_path = $stmt->fetchColumn();
                        if ($image_path && file_exists($_SERVER['DOCUMENT_ROOT'] . $image_path)) {
                            if (!unlink($_SERVER['DOCUMENT_ROOT'] . $image_path)) {
                                error_log("Failed to delete news image: $image_path");
                            } else {
                                error_log("Deleted news image: $image_path");
                            }
                        }
                        $stmt = $this->db->prepare("DELETE FROM news WHERE id = ?");
                        if (!$stmt->execute([$id])) {
                            error_log("Failed to delete news: id=$id");
                            $this->redirectWithMessage(false, 'Erreur : Échec de la suppression de l\'actualité.');
                        }
                        error_log("News deleted successfully: ID=$id");
                        $this->redirectWithMessage(true, 'Actualité supprimée avec succès!');
                    } else {
                        error_log("Delete news failed: Missing ID");
                        $this->redirectWithMessage(false, 'Erreur : ID de l\'actualité manquant.');
                    }
                } elseif ($action === 'reorder_news') {
                    $orders = json_decode($_POST['orders'] ?? '{}', true);
                    if ($orders && is_array($orders)) {
                        foreach ($orders as $id => $position) {
                            $stmt = $this->db->prepare("UPDATE news SET order_position = ? WHERE id = ?");
                            if (!$stmt->execute([(int)$position, (int)$id])) {
                                error_log("Failed to reorder news: id=$id, position=$position");
                                $this->redirectWithMessage(false, 'Erreur : Échec de la réorganisation des actualités.');
                            }
                        }
                        error_log("News order updated successfully");
                        $this->redirectWithMessage(true, 'Ordre des actualités mis à jour avec succès!');
                    } else {
                        error_log("Reorder news failed: Invalid order data");
                        $this->redirectWithMessage(false, 'Erreur : Données d\'ordre invalides.');
                    }
                } elseif ($action === 'add_event') {
                    $title = htmlspecialchars(trim($_POST['title'] ?? ''));
                    $content = htmlspecialchars(trim($_POST['content'] ?? ''));
                    $event_date = trim($_POST['event_date'] ?? '');

                    error_log("Add event attempt: title=$title, content_length=" . strlen($content) . ", event_date=$event_date");

                    if ($title && $content && $event_date) {
                        $image_path = $this->handleImageUpload($_FILES['image'] ?? [], null, $this->eventsUploadDir);
                        if (is_string($image_path) && strpos($image_path, 'Erreur') === 0) {
                            error_log("Image upload failed for event: $image_path");
                            $this->redirectWithMessage(false, $image_path);
                        } else {
                            $stmt = $this->db->prepare("
                                INSERT INTO events (title, content, image_path, event_date, is_active, order_position, created_at, updated_at)
                                VALUES (?, ?, ?, ?, 1, (SELECT COALESCE(MAX(order_position), 0) + 1 FROM events), datetime('now'), datetime('now'))
                            ");
                            if (!$stmt->execute([$title, $content, $image_path, $event_date])) {
                                error_log("Failed to add event: title=$title");
                                $this->redirectWithMessage(false, 'Erreur : Échec de l\'ajout de l\'événement.');
                            }
                            error_log("Event added successfully: ID=" . $this->db->lastInsertId());
                            $this->redirectWithMessage(true, 'Événement ajouté avec succès!');
                        }
                    } else {
                        $errors = [];
                        if (!$title) $errors[] = 'Titre manquant';
                        if (!$content) $errors[] = 'Contenu manquant';
                        if (!$event_date) $errors[] = 'Date de l\'événement manquante';
                        error_log("Add event failed: " . implode(', ', $errors));
                        $this->redirectWithMessage(false, 'Erreur : ' . implode(', ', $errors));
                    }
                } elseif ($action === 'update_event') {
                    $id = trim($_POST['event_id'] ?? '');
                    $title = htmlspecialchars(trim($_POST['title'] ?? ''));
                    $content = htmlspecialchars(trim($_POST['content'] ?? ''));
                    $event_date = trim($_POST['event_date'] ?? '');

                    error_log("Update event attempt: id=$id, title=$title, content_length=" . strlen($content) . ", event_date=$event_date");

                    if ($id && $title && $content && $event_date) {
                        $image_path = $this->handleImageUpload($_FILES['image'] ?? [], $id, $this->eventsUploadDir);
                        if (is_string($image_path) && strpos($image_path, 'Erreur') === 0) {
                            error_log("Image upload failed for event update: $image_path");
                            $this->redirectWithMessage(false, $image_path);
                        } else {
                            $stmt = $this->db->prepare("
                                UPDATE events 
                                SET title = ?, content = ?, image_path = ?, event_date = ?, updated_at = datetime('now')
                                WHERE id = ?
                            ");
                            if (!$stmt->execute([$title, $content, $image_path, $event_date, $id])) {
                                error_log("Failed to update event: id=$id");
                                $this->redirectWithMessage(false, 'Erreur : Échec de la mise à jour de l\'événement.');
                            }
                            error_log("Event updated successfully: ID=$id");
                            $this->redirectWithMessage(true, 'Événement mis à jour avec succès!');
                        }
                    } else {
                        $errors = [];
                        if (!$id) $errors[] = 'ID manquant';
                        if (!$title) $errors[] = 'Titre manquant';
                        if (!$content) $errors[] = 'Contenu manquant';
                        if (!$event_date) $errors[] = 'Date de l\'événement manquante';
                        error_log("Update event failed: " . implode(', ', $errors));
                        $this->redirectWithMessage(false, 'Erreur : ' . implode(', ', $errors));
                    }
                } elseif ($action === 'delete_event') {
                    $id = trim($_POST['event_id'] ?? '');
                    if ($id) {
                        $stmt = $this->db->prepare("SELECT image_path FROM events WHERE id = ?");
                        $stmt->execute([$id]);
                        $image_path = $stmt->fetchColumn();
                        if ($image_path && file_exists($_SERVER['DOCUMENT_ROOT'] . $image_path)) {
                            if (!unlink($_SERVER['DOCUMENT_ROOT'] . $image_path)) {
                                error_log("Failed to delete event image: $image_path");
                            } else {
                                error_log("Deleted event image: $image_path");
                            }
                        }
                        $stmt = $this->db->prepare("DELETE FROM events WHERE id = ?");
                        if (!$stmt->execute([$id])) {
                            error_log("Failed to delete event: id=$id");
                            $this->redirectWithMessage(false, 'Erreur : Échec de la suppression de l\'événement.');
                        }
                        error_log("Event deleted successfully: ID=$id");
                        $this->redirectWithMessage(true, 'Événement supprimé avec succès!');
                    } else {
                        error_log("Delete event failed: Missing ID");
                        $this->redirectWithMessage(false, 'Erreur : ID de l\'événement manquant.');
                    }
                } elseif ($action === 'reorder_events') {
                    $orders = json_decode($_POST['orders'] ?? '{}', true);
                    if ($orders && is_array($orders)) {
                        foreach ($orders as $id => $position) {
                            $stmt = $this->db->prepare("UPDATE events SET order_position = ? WHERE id = ?");
                            if (!$stmt->execute([(int)$position, (int)$id])) {
                                error_log("Failed to reorder event: id=$id, position=$position");
                                $this->redirectWithMessage(false, 'Erreur : Échec de la réorganisation des événements.');
                            }
                        }
                        error_log("Events order updated successfully");
                        $this->redirectWithMessage(true, 'Ordre des événements mis à jour avec succès!');
                    } else {
                        error_log("Reorder events failed: Invalid order data");
                        $this->redirectWithMessage(false, 'Erreur : Données d\'ordre invalides.');
                    }
                } else {
                    error_log("Unknown action: $action");
                    $this->redirectWithMessage(false, 'Action non reconnue.');
                }
            } catch (Exception $e) {
                error_log("Server error in content action $action: " . $e->getMessage());
                $this->redirectWithMessage(false, 'Erreur serveur : ' . $e->getMessage());
            }
        }

        try {
            $stmt = $this->db->query("SELECT section, key_name, value FROM site_content ORDER BY section, key_name");
            $content = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $content[$row['section']][$row['key_name']] = $row['value'];
            }

            $services = $this->db->query("SELECT * FROM services WHERE is_active = 1 ORDER BY order_position")->fetchAll(PDO::FETCH_ASSOC);
            $team = $this->db->query("SELECT * FROM team_members WHERE is_active = 1 ORDER BY order_position")->fetchAll(PDO::FETCH_ASSOC);
            $news = $this->db->query("SELECT * FROM news WHERE is_active = 1 ORDER BY order_position")->fetchAll(PDO::FETCH_ASSOC);
            $events = $this->db->query("SELECT * FROM events WHERE is_active = 1 ORDER BY order_position")->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in content: " . $e->getMessage());
            $error = 'Erreur serveur lors du chargement du contenu.';
            include 'views/admin/error.php';
            return;
        }

        include 'views/admin/content.php';
    }

    public function contacts() {
        $this->requireAuth();
        header('Cache-Control: no-cache, must-revalidate');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            error_log("Contacts POST: " . json_encode($_POST));
            if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
                error_log("CSRF token validation failed in contacts");
                $this->redirectWithMessage(false, 'Erreur de validation CSRF', '/admin/contacts');
            }

            $action = trim($_POST['action'] ?? '');
            $id = trim($_POST['id'] ?? '');

            try {
                if ($action === 'mark_read' && $id) {
                    $stmt = $this->db->prepare("UPDATE contacts SET status = 'read', updated_at = datetime('now') WHERE id = ?");
                    if (!$stmt->execute([$id])) {
                        error_log("Failed to mark contact as read: id=$id");
                        $this->redirectWithMessage(false, 'Erreur : Échec du marquage comme lu.', '/admin/contacts');
                    }
                    $this->redirectWithMessage(true, 'Message marqué comme lu', '/admin/contacts');
                } elseif ($action === 'mark_new' && $id) {
                    $stmt = $this->db->prepare("UPDATE contacts SET status = 'new', updated_at = datetime('now') WHERE id = ?");
                    if (!$stmt->execute([$id])) {
                        error_log("Failed to mark contact as new: id=$id");
                        $this->redirectWithMessage(false, 'Erreur : Échec du marquage comme nouveau.', '/admin/contacts');
                    }
                    $this->redirectWithMessage(true, 'Message marqué comme nouveau', '/admin/contacts');
                } elseif ($action === 'delete' && $id) {
                    $stmt = $this->db->prepare("SELECT id FROM contacts WHERE id = ?");
                    $stmt->execute([$id]);
                    if (!$stmt->fetch()) {
                        error_log("Contact not found for id=$id");
                        $this->redirectWithMessage(false, 'Erreur : Contact introuvable.', '/admin/contacts');
                    }

                    $stmt = $this->db->prepare("SELECT file_path FROM contact_files WHERE contact_id = ?");
                    if (!$stmt->execute([$id])) {
                        error_log("Failed to select contact files for contact_id=$id");
                        $this->redirectWithMessage(false, 'Erreur : Échec de la récupération des fichiers.', '/admin/contacts');
                    }
                    $files = $stmt->fetchAll(PDO::FETCH_COLUMN);
                    foreach ($files as $file) {
                        $file_path = $_SERVER['DOCUMENT_ROOT'] . $file;
                        if (file_exists($file_path)) {
                            if (!unlink($file_path)) {
                                error_log("Failed to delete file: $file_path");
                            } else {
                                error_log("Deleted contact file: $file_path");
                            }
                        }
                    }
                    $stmt = $this->db->prepare("DELETE FROM contact_files WHERE contact_id = ?");
                    if (!$stmt->execute([$id])) {
                        error_log("Failed to delete contact_files for contact_id=$id");
                        $this->redirectWithMessage(false, 'Erreur : Échec de la suppression des fichiers.', '/admin/contacts');
                    }
                    $stmt = $this->db->prepare("DELETE FROM contacts WHERE id = ?");
                    if (!$stmt->execute([$id])) {
                        error_log("Failed to delete contact for id=$id");
                        $this->redirectWithMessage(false, 'Erreur : Échec de la suppression du contact.', '/admin/contacts');
                    }
                    error_log("Contact deleted successfully: ID=$id");
                    $this->redirectWithMessage(true, 'Message supprimé', '/admin/contacts');
                } elseif ($action === 'confirm_appointment' && $id) {
                    $stmt = $this->db->prepare("
                        UPDATE appointments 
                        SET status = 'confirmed', updated_at = datetime('now') 
                        WHERE id = ? AND EXISTS (SELECT 1 FROM contacts WHERE appointment_id = ?)
                    ");
                    if (!$stmt->execute([$id, $id])) {
                        error_log("Failed to confirm appointment: id=$id");
                        $this->redirectWithMessage(false, 'Erreur : Échec de la confirmation du rendez-vous.', '/admin/contacts');
                    }
                    $this->redirectWithMessage(true, 'Rendez-vous confirmé', '/admin/contacts');
                } elseif ($action === 'cancel_appointment' && $id) {
                    $stmt = $this->db->prepare("SELECT slot_id FROM appointments WHERE id = ?");
                    $stmt->execute([$id]);
                    $slot_id = $stmt->fetchColumn();
                    
                    $stmt = $this->db->prepare("
                        UPDATE appointments 
                        SET status = 'cancelled', updated_at = datetime('now') 
                        WHERE id = ? AND EXISTS (SELECT 1 FROM contacts WHERE appointment_id = ?)
                    ");
                    if (!$stmt->execute([$id, $id])) {
                        error_log("Failed to cancel appointment: id=$id");
                        $this->redirectWithMessage(false, 'Erreur : Échec de l\'annulation du rendez-vous.', '/admin/contacts');
                    }
                    
                    if ($slot_id) {
                        $stmt = $this->db->prepare("UPDATE appointment_slots SET is_booked = 0, updated_at = datetime('now') WHERE id = ?");
                        $stmt->execute([$slot_id]);
                    }
                    $this->redirectWithMessage(true, 'Rendez-vous annulé', '/admin/contacts');
                } else {
                    error_log("Unknown or invalid contacts action: action=$action, id=$id");
                    $this->redirectWithMessage(false, 'Action non reconnue ou ID manquant.', '/admin/contacts');
                }
            } catch (Exception $e) {
                error_log("Server error in contacts action $action: " . $e->getMessage());
                $this->redirectWithMessage(false, 'Erreur serveur : ' . $e->getMessage(), '/admin/contacts');
            }
        }

        try {
            $stats = $this->getStats();
            $contacts = $this->db->query("
                SELECT c.*, a.status as appointment_status, s.start_time as appointment_time 
                FROM contacts c 
                LEFT JOIN appointments a ON c.appointment_id = a.id 
                LEFT JOIN appointment_slots s ON a.slot_id = s.id 
                ORDER BY c.created_at DESC
            ")->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in contacts: " . $e->getMessage());
            $error = 'Erreur serveur lors du chargement des contacts.';
            include 'views/admin/error.php';
            return;
        }

        include 'views/admin/contacts.php';
    }

    public function messageDetail() {
        $this->requireAuth();
        header('Cache-Control: no-cache, must-revalidate');

        $url = $_SERVER['REQUEST_URI'];
        $parts = explode('/', trim($url, '/'));
        $messageId = end($parts);

        if (!is_numeric($messageId)) {
            error_log("Invalid message ID: $messageId");
            header('Location: /admin/contacts');
            exit;
        }

        try {
            $stmt = $this->db->prepare("
                SELECT c.*, a.status as appointment_status, s.start_time as appointment_start, s.end_time as appointment_end 
                FROM contacts c 
                LEFT JOIN appointments a ON c.appointment_id = a.id 
                LEFT JOIN appointment_slots s ON a.slot_id = s.id 
                WHERE c.id = ?
            ");
            $stmt->execute([$messageId]);
            $contact = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$contact) {
                error_log("Contact not found for ID: $messageId");
                header('Location: /admin/contacts');
                exit;
            }

            $updateStmt = $this->db->prepare("UPDATE contacts SET status = 'read', updated_at = datetime('now') WHERE id = ?");
            if (!$updateStmt->execute([$messageId])) {
                error_log("Failed to mark contact as read in messageDetail: id=$messageId");
            }

            $filesStmt = $this->db->prepare("SELECT * FROM contact_files WHERE contact_id = ? ORDER BY uploaded_at");
            $filesStmt->execute([$messageId]);
            $files = $filesStmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in messageDetail: " . $e->getMessage());
            $error = 'Erreur serveur lors du chargement du message.';
            include 'views/admin/error.php';
            return;
        }

        include 'views/admin/message-detail.php';
    }

    public function schedule() {
        $this->requireAuth();
        header('Cache-Control: no-cache, must-revalidate');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
                error_log("CSRF token validation failed in schedule");
                $this->redirectWithMessage(false, 'Erreur de validation CSRF', '/admin/schedule');
            }

            $action = trim($_POST['action'] ?? '');

            try {
                if ($action === 'add_daily_slots') {
                    $date = trim($_POST['date'] ?? '');
                    $all_day = isset($_POST['all_day']);
                    $start_time = $all_day ? '09:00' : trim($_POST['start_time'] ?? '');
                    $end_time = $all_day ? '18:00' : trim($_POST['end_time'] ?? '');
                    $break_start = trim($_POST['break_start'] ?? '');
                    $break_end = trim($_POST['break_end'] ?? '');

                    error_log("Add daily slots attempt: date=$date, all_day=" . ($all_day ? 'true' : 'false') . ", start_time=$start_time, end_time=$end_time, break_start=$break_start, break_end=$break_end");

                    // Validate inputs
                    if (!$date || !DateTime::createFromFormat('Y-m-d', $date)) {
                        error_log("Invalid or missing date: $date");
                        $this->redirectWithMessage(false, 'Erreur : Date invalide.', '/admin/schedule');
                    }

                    $date_obj = new DateTime($date);
                    if ($date_obj < new DateTime('today')) {
                        error_log("Date is in the past: $date");
                        $this->redirectWithMessage(false, 'Erreur : La date ne peut pas être dans le passé.', '/admin/schedule');
                    }

                    if (!$all_day && (!$start_time || !$end_time)) {
                        error_log("Missing start_time or end_time when all_day is not selected");
                        $this->redirectWithMessage(false, 'Erreur : Heure de début et de fin requises.', '/admin/schedule');
                    }

                    $start_datetime = new DateTime("$date $start_time");
                    $end_datetime = new DateTime("$date $end_time");
                    if ($start_datetime >= $end_datetime) {
                        error_log("Invalid time range: start_time=$start_time, end_time=$end_time");
                        $this->redirectWithMessage(false, 'Erreur : L\'heure de début doit être avant l\'heure de fin.', '/admin/schedule');
                    }

                    // Validate break times if provided
                    $break_start_datetime = $break_end_datetime = null;
                    if ($break_start && $break_end) {
                        $break_start_datetime = new DateTime("$date $break_start");
                        $break_end_datetime = new DateTime("$date $break_end");
                        if ($break_start_datetime >= $break_end_datetime || 
                            $break_start_datetime < $start_datetime || 
                            $break_end_datetime > $end_datetime) {
                            error_log("Invalid break time range: break_start=$break_start, break_end=$break_end");
                            $this->redirectWithMessage(false, 'Erreur : Période de pause invalide.', '/admin/schedule');
                        }
                    }

                    // Generate 30-minute slots
                    $this->db->beginTransaction();
                    $slot_duration = 30 * 60; // 30 minutes in seconds
                    $current_time = clone $start_datetime;
                    $insertStmt = $this->db->prepare("
                        INSERT INTO appointment_slots (start_time, end_time, is_booked, created_at, updated_at)
                        VALUES (?, ?, 0, datetime('now'), datetime('now'))
                    ");
                    $slot_count = 0;

                    while ($current_time < $end_datetime) {
                        $slot_end = clone $current_time;
                        $slot_end->modify("+30 minutes");

                        // Skip if slot falls within break period
                        if ($break_start_datetime && $break_end_datetime &&
                            $current_time < $break_end_datetime && 
                            $slot_end > $break_start_datetime) {
                            $current_time->modify("+30 minutes");
                            continue;
                        }

                        // Check for existing slot to avoid duplicates
                        $checkStmt = $this->db->prepare("
                            SELECT 1 FROM appointment_slots 
                            WHERE start_time = ? AND end_time = ?
                        ");
                        $checkStmt->execute([
                            $current_time->format('Y-m-d H:i:s'),
                            $slot_end->format('Y-m-d H:i:s')
                        ]);
                        if ($checkStmt->fetch()) {
                            $current_time->modify("+30 minutes");
                            continue;
                        }

                        // Insert slot
                        if (!$insertStmt->execute([
                            $current_time->format('Y-m-d H:i:s'),
                            $slot_end->format('Y-m-d H:i:s')
                        ])) {
                            $this->db->rollBack();
                            error_log("Failed to insert slot: start=" . $current_time->format('Y-m-d H:i:s'));
                            $this->redirectWithMessage(false, 'Erreur : Échec de l\'ajout des créneaux.', '/admin/schedule');
                        }
                        $slot_count++;
                        $current_time->modify("+30 minutes");
                    }

                    $this->db->commit();
                    error_log("Added $slot_count slots for date=$date");
                    $this->redirectWithMessage(true, "$slot_count créneaux ajoutés avec succès!", '/admin/schedule');
                } elseif ($action === 'delete_slot') {
                    $id = trim($_POST['slot_id'] ?? '');
                    if ($id) {
                        $stmt = $this->db->prepare("SELECT id FROM appointments WHERE slot_id = ? AND status IN ('pending', 'confirmed')");
                        $stmt->execute([$id]);
                        if ($stmt->fetch()) {
                            error_log("Cannot delete slot $id: active appointments exist");
                            $this->redirectWithMessage(false, 'Erreur : Impossible de supprimer un créneau avec des rendez-vous actifs.', '/admin/schedule');
                        }

                        $stmt = $this->db->prepare("DELETE FROM appointment_slots WHERE id = ?");
                        if (!$stmt->execute([$id])) {
                            error_log("Failed to delete appointment slot: id=$id");
                            $this->redirectWithMessage(false, 'Erreur : Échec de la suppression du créneau.', '/admin/schedule');
                        }
                        error_log("Slot deleted successfully: ID=$id");
                        $this->redirectWithMessage(true, 'Créneau supprimé avec succès!', '/admin/schedule');
                    } else {
                        error_log("Delete slot failed: Missing slot ID");
                        $this->redirectWithMessage(false, 'Erreur : ID du créneau manquant.', '/admin/schedule');
                    }
                } else {
                    error_log("Unknown schedule action: $action");
                    $this->redirectWithMessage(false, 'Action non reconnue.', '/admin/schedule');
                }
            } catch (Exception $e) {
                $this->db->rollBack();
                error_log("Server error in schedule action $action: " . $e->getMessage());
                $this->redirectWithMessage(false, 'Erreur serveur : ' . $e->getMessage(), '/admin/schedule');
            }
        }

        try {
            $stats = $this->getStats();
            $slots = $this->db->query("
                SELECT s.*, COUNT(a.id) as appointment_count 
                FROM appointment_slots s 
                LEFT JOIN appointments a ON s.id = a.slot_id AND a.status IN ('pending', 'confirmed')
                WHERE s.start_time >= datetime('now')
                GROUP BY s.id
                ORDER BY s.start_time
            ")->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in schedule: " . $e->getMessage());
            $error = 'Erreur serveur lors du chargement des créneaux.';
            include 'views/admin/error.php';
            return;
        }

        include 'views/admin/schedule.php';
    }

    public function settings() {
        $this->requireAuth();
        header('Cache-Control: no-cache, must-revalidate');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
                error_log("CSRF token validation failed in settings");
                $this->redirectWithMessage(false, 'Erreur de validation CSRF', '/admin/settings');
            }

            try {
                $this->redirectWithMessage(true, 'Paramètres mis à jour avec succès!', '/admin/settings');
            } catch (Exception $e) {
                error_log("Server error in settings: " . $e->getMessage());
                $this->redirectWithMessage(false, 'Erreur serveur : ' . $e->getMessage(), '/admin/settings');
            }
        }

        include 'views/admin/settings.php';
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        header('Location: /admin');
        exit;
    }
}
?>