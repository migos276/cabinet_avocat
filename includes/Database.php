<?php
require_once __DIR__ . '/config.php';

class Database {
    private $connection = null;

    public function __construct() {
        // Check database file accessibility
        if (!file_exists(dirname(DB_NAME)) && !mkdir(dirname(DB_NAME), 0755, true)) {
            throw new Exception('Cannot create database directory: ' . dirname(DB_NAME));
        }
        if (file_exists(DB_NAME) && !is_writable(DB_NAME)) {
            throw new Exception('Database file is not writable: ' . DB_NAME);
        }
    }

    public function getConnection() {
        if ($this->connection === null) {
            try {
                if (!defined('DB_NAME')) {
                    throw new Exception('DB_NAME constant is not defined. Please check your config.php file.');
                }

                $this->connection = new PDO('sqlite:' . DB_NAME);
                $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                $this->connection->exec('PRAGMA foreign_keys = ON;');
                $this->connection->exec('PRAGMA journal_mode = WAL;');
                $this->connection->exec('PRAGMA synchronous = NORMAL;');
                $this->connection->exec('PRAGMA cache_size = 10000;');
                $this->connection->exec('PRAGMA temp_store = MEMORY;');

                $this->initializeTables();

            } catch (Exception $e) {
                error_log('Database Connection Error: ' . $e->getMessage());
                throw new Exception('Database connection failed: ' . $e->getMessage());
            }
        }
        return $this->connection;
    }

    private function initializeTables() {
        try {
            $this->connection->beginTransaction();

            $tables = [
                "CREATE TABLE IF NOT EXISTS site_content (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    section VARCHAR(50) NOT NULL,
                    key_name VARCHAR(100) NOT NULL,
                    value TEXT NOT NULL,
                    type VARCHAR(20) DEFAULT 'text',
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    UNIQUE(section, key_name)
                )",
                "CREATE TABLE IF NOT EXISTS contacts (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    name VARCHAR(255) NOT NULL,
                    email VARCHAR(255) NOT NULL,
                    phone VARCHAR(50),
                    subject VARCHAR(255),
                    message TEXT NOT NULL,
                    status VARCHAR(20) DEFAULT 'new',
                    appointment_id INTEGER,
                    payment_method VARCHAR(20),
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE SET NULL
                )",
                "CREATE TABLE IF NOT EXISTS services (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    title VARCHAR(255) NOT NULL,
                    description TEXT NOT NULL,
                    icon VARCHAR(50) NOT NULL,
                    color VARCHAR(255) DEFAULT '#3b82f6',
                    order_position INTEGER DEFAULT 0,
                    is_active INTEGER DEFAULT 1,
                    detailed_content TEXT,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                )",
                "CREATE TABLE IF NOT EXISTS team_members (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    name TEXT NOT NULL,
                    position TEXT NOT NULL,
                    description TEXT NOT NULL,
                    image_path TEXT DEFAULT '/public/uploads/team/default_team_member.jpeg',
                    is_active INTEGER DEFAULT 1,
                    order_position INTEGER DEFAULT 0,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                )",
                "CREATE TABLE IF NOT EXISTS news (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    title TEXT NOT NULL,
                    content TEXT NOT NULL,
                    image_path TEXT DEFAULT '/public/uploads/news/default_news.jpg',
                    publish_date DATETIME NOT NULL,
                    is_active INTEGER DEFAULT 1,
                    order_position INTEGER DEFAULT 0,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                )",
                "CREATE TABLE IF NOT EXISTS events (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    title TEXT NOT NULL,
                    content TEXT NOT NULL,
                    image_path TEXT DEFAULT '/public/uploads/events/default_event.jpg',
                    event_date DATETIME NOT NULL,
                    is_active INTEGER DEFAULT 1,
                    order_position INTEGER DEFAULT 0,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                )",
                "CREATE TABLE IF NOT EXISTS contact_files (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    contact_id INTEGER NOT NULL,
                    original_name VARCHAR(255) NOT NULL,
                    file_name VARCHAR(255) NOT NULL,
                    file_path VARCHAR(500) NOT NULL,
                    file_size INTEGER NOT NULL,
                    file_type VARCHAR(50) NOT NULL,
                    uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (contact_id) REFERENCES contacts(id) ON DELETE CASCADE
                )",
                "CREATE TABLE IF NOT EXISTS admin_users (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    username VARCHAR(100) NOT NULL UNIQUE,
                    password VARCHAR(255) NOT NULL,
                    email VARCHAR(255),
                    is_active INTEGER DEFAULT 1,
                    last_login DATETIME,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                )",
                "CREATE TABLE IF NOT EXISTS appointment_slots (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    start_time DATETIME NOT NULL,
                    end_time DATETIME NOT NULL,
                    is_booked INTEGER DEFAULT 0,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                )",
                "CREATE TABLE IF NOT EXISTS appointments (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    slot_id INTEGER NOT NULL,
                    contact_id INTEGER NOT NULL,
                    status VARCHAR(20) DEFAULT 'pending',
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (slot_id) REFERENCES appointment_slots(id) ON DELETE CASCADE,
                    FOREIGN KEY (contact_id) REFERENCES contacts(id) ON DELETE CASCADE
                )"
            ];

            foreach ($tables as $sql) {
                $this->connection->exec($sql);
            }

            // Check and update schema only if necessary
            $this->upgradeSchema();
            $this->createIndexes();
            $this->insertDefaultData();
            $this->connection->commit();

        } catch (Exception $e) {
            $this->connection->rollBack();
            error_log('Error initializing tables: ' . $e->getMessage());
            throw $e;
        }
    }

    private function upgradeSchema() {
        // Ensure order_position column in news
        $columns = $this->connection->query("PRAGMA table_info(news)")->fetchAll(PDO::FETCH_ASSOC);
        $has_order_position = false;
        foreach ($columns as $column) {
            if ($column['name'] === 'order_position') {
                $has_order_position = true;
                break;
            }
        }
        if (!$has_order_position) {
            $this->connection->exec("ALTER TABLE news ADD COLUMN order_position INTEGER DEFAULT 0");
            $this->connection->exec("UPDATE news SET order_position = id WHERE order_position IS NULL");
        }

        // Fix color column length in services
        $service_columns = $this->connection->query("PRAGMA table_info(services)")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($service_columns as $col) {
            if ($col['name'] === 'color' && $col['type'] === 'VARCHAR(7)') {
                $this->connection->exec("ALTER TABLE services RENAME COLUMN color TO old_color");
                $this->connection->exec("ALTER TABLE services ADD COLUMN color VARCHAR(255) DEFAULT '#3b82f6'");
                $this->connection->exec("UPDATE services SET color = old_color");
                $this->connection->exec("ALTER TABLE services DROP COLUMN old_color");
            }
        }

        // Ensure appointment_id and payment_method columns in contacts
        $contact_columns = $this->connection->query("PRAGMA table_info(contacts)")->fetchAll(PDO::FETCH_ASSOC);
        $has_appointment_id = false;
        $has_payment_method = false;
        foreach ($contact_columns as $column) {
            if ($column['name'] === 'appointment_id') $has_appointment_id = true;
            if ($column['name'] === 'payment_method') $has_payment_method = true;
        }
        if (!$has_appointment_id) {
            $this->connection->exec("ALTER TABLE contacts ADD COLUMN appointment_id INTEGER");
            $this->connection->exec("UPDATE contacts SET appointment_id = NULL");
        }
        if (!$has_payment_method) {
            $this->connection->exec("ALTER TABLE contacts ADD COLUMN payment_method VARCHAR(20)");
        }

        // Ensure order_position column in events
        $columns = $this->connection->query("PRAGMA table_info(events)")->fetchAll(PDO::FETCH_ASSOC);
        $has_order_position = false;
        foreach ($columns as $column) {
            if ($column['name'] === 'order_position') {
                $has_order_position = true;
                break;
            }
        }
        if (!$has_order_position) {
            $this->connection->exec("ALTER TABLE events ADD COLUMN order_position INTEGER DEFAULT 0");
            $this->connection->exec("UPDATE events SET order_position = id WHERE order_position IS NULL");
        }
    }

    private function createIndexes() {
        $indexes = [
            "CREATE INDEX IF NOT EXISTS idx_contact_files_contact_id ON contact_files(contact_id)",
            "CREATE INDEX IF NOT EXISTS idx_contacts_status ON contacts(status)",
            "CREATE INDEX IF NOT EXISTS idx_contacts_created_at ON contacts(created_at)",
            "CREATE INDEX IF NOT EXISTS idx_contacts_email ON contacts(email)",
            "CREATE INDEX IF NOT EXISTS idx_contacts_appointment_id ON contacts(appointment_id)",
            "CREATE INDEX IF NOT EXISTS idx_site_content_section ON site_content(section)",
            "CREATE INDEX IF NOT EXISTS idx_services_active ON services(is_active, order_position)",
            "CREATE INDEX IF NOT EXISTS idx_team_members_active ON team_members(is_active, order_position)",
            "CREATE INDEX IF NOT EXISTS idx_news_active ON news(is_active, order_position)",
            "CREATE INDEX IF NOT EXISTS idx_events_active ON events(is_active, order_position)",
            "CREATE INDEX IF NOT EXISTS idx_appointment_slots_time ON appointment_slots(start_time, end_time)",
            "CREATE INDEX IF NOT EXISTS idx_appointments_slot_id ON appointments(slot_id)"
        ];

        foreach ($indexes as $index) {
            try {
                $this->connection->exec($index);
            } catch (PDOException $e) {
                error_log('Error creating index: ' . $e->getMessage());
                throw $e;
            }
        }
    }

    private function insertDefaultData() {
        // Check if any table is populated to avoid redundant insertions
        $tables = ['site_content', 'services', 'team_members', 'news', 'admin_users', 'appointment_slots', 'events'];
        $isPopulated = false;
        foreach ($tables as $table) {
            $count = $this->connection->query("SELECT COUNT(*) FROM $table")->fetchColumn();
            if ($count > 0) {
                $isPopulated = true;
                break;
            }
        }
        if ($isPopulated) return;

        $this->insertDefaultSiteContent();
        $this->insertDefaultServices();
        $this->insertDefaultTeam();
        $this->insertDefaultNews();
        $this->insertDefaultEvents();
        $this->insertDefaultAdmin();
        $this->insertDefaultAppointmentSlots();
    }

    private function insertDefaultSiteContent() {
        $sql = "SELECT 1 FROM site_content WHERE section = ? AND key_name = ? LIMIT 1";
        $stmt = $this->connection->prepare($sql);
        $defaultContent = [
            ['hero', 'title', defined('SITE_NAME') ? SITE_NAME : 'Cabinet Excellence'],
            ['hero', 'subtitle', 'Votre partenaire de confiance pour tous vos besoins juridiques'],
            ['about', 'title', 'À propos de nous'],
            ['about', 'subtitle', 'Fort de plus de 20 ans d\'expérience, notre cabinet vous accompagne avec professionnalisme.'],
            ['services', 'title', 'Nos services'],
            ['services', 'subtitle', 'Des domaines d\'expertise variés pour répondre à tous vos besoins'],
            ['team', 'title', 'Notre équipe'],
            ['team', 'subtitle', 'Des experts à votre service'],
            ['news', 'title', 'Nos Dernières Actualités'],
            ['news', 'subtitle', 'Restez informé des dernières nouvelles juridiques.'],
            ['contact', 'title', 'Contactez-nous'],
            ['contact', 'address', '123 Avenue de la Justice, 75001 Paris'],
            ['contact', 'phone', '+33 1 23 45 67 89'],
            ['contact', 'email', defined('ADMIN_EMAIL') ? ADMIN_EMAIL : 'contact@cabinet-excellence.fr'],
            ['footer', 'copyright', '© ' . date('Y') . ' ' . (defined('SITE_NAME') ? SITE_NAME : 'Cabinet Excellence') . '. Tous droits réservés.']
        ];

        $insertSql = "INSERT INTO site_content (section, key_name, value) VALUES (?, ?, ?)";
        $insertStmt = $this->connection->prepare($insertSql);
        foreach ($defaultContent as $content) {
            $stmt->execute([$content[0], $content[1]]);
            if ($stmt->fetch()) continue; // Skip if record exists
            try {
                $insertStmt->execute($content);
            } catch (PDOException $e) {
                error_log('Error inserting default site content: ' . $e->getMessage());
            }
        }
    }

    private function insertDefaultServices() {
        $sql = "SELECT 1 FROM services WHERE title = ? LIMIT 1";
        $stmt = $this->connection->prepare($sql);
        $defaultServices = [
            [
                'title' => 'Droit des Affaires',
                'description' => 'Accompagnement juridique complet pour les entreprises.',
                'icon' => 'fas fa-briefcase',
                'color' => '#3b82f6',
                'order_position' => 1,
                'detailed_content' => $this->getDefaultDetailedContent()
            ],
            [
                'title' => 'Droit de la Famille',
                'description' => 'Conseil et représentation dans tous les aspects du droit familial.',
                'icon' => 'fas fa-heart',
                'color' => '#ef4444',
                'order_position' => 2,
                'detailed_content' => $this->getDefaultDetailedContent()
            ],
            [
                'title' => 'Droit Immobilier',
                'description' => 'Expertise en transactions immobilières et contentieux.',
                'icon' => 'fas fa-home',
                'color' => '#10b981',
                'order_position' => 3,
                'detailed_content' => $this->getDefaultDetailedContent()
            ],
            [
                'title' => 'Droit du Travail',
                'description' => 'Protection des droits des salariés et conseil aux employeurs.',
                'icon' => 'fas fa-users',
                'color' => '#f59e0b',
                'order_position' => 4,
                'detailed_content' => $this->getDefaultDetailedContent()
            ]
        ];

        $insertSql = "INSERT INTO services (title, description, icon, color, order_position, detailed_content, is_active) 
                      VALUES (?, ?, ?, ?, ?, ?, 1)";
        $insertStmt = $this->connection->prepare($insertSql);
        foreach ($defaultServices as $service) {
            $stmt->execute([$service['title']]);
            if ($stmt->fetch()) continue; // Skip if record exists
            try {
                $insertStmt->execute([
                    $service['title'],
                    $service['description'],
                    $service['icon'],
                    $service['color'],
                    $service['order_position'],
                    $service['detailed_content']
                ]);
            } catch (PDOException $e) {
                error_log('Error inserting default service: ' . $e->getMessage());
            }
        }
    }

    private function insertDefaultTeam() {
        $sql = "SELECT 1 FROM team_members WHERE name = ? LIMIT 1";
        $stmt = $this->connection->prepare($sql);
        $defaultTeam = [
            [
                'name' => 'Maître Jean Dupont',
                'position' => 'Avocat Associé - Droit des Affaires',
                'description' => 'Spécialisé en droit des sociétés depuis plus de 15 ans.',
                'image_path' => '/public/uploads/team/default_team_member.jpeg',
                'order_position' => 1
            ],
            [
                'name' => 'Maître Marie Martin',
                'position' => 'Avocate Spécialisée - Droit de la Famille',
                'description' => 'Experte en droit matrimonial et protection de l\'enfance.',
                'image_path' => '/public/uploads/team/default_team_member.jpeg',
                'order_position' => 2
            ]
        ];

        $insertSql = "INSERT INTO team_members (name, position, description, image_path, order_position, is_active) 
                      VALUES (?, ?, ?, ?, ?, 1)";
        $insertStmt = $this->connection->prepare($insertSql);
        foreach ($defaultTeam as $member) {
            $stmt->execute([$member['name']]);
            if ($stmt->fetch()) continue; // Skip if record exists
            try {
                $insertStmt->execute([
                    $member['name'],
                    $member['position'],
                    $member['description'],
                    $member['image_path'],
                    $member['order_position']
                ]);
            } catch (PDOException $e) {
                error_log('Error inserting default team member: ' . $e->getMessage());
            }
        }
    }

    private function insertDefaultNews() {
        $sql = "SELECT 1 FROM news WHERE title = ? LIMIT 1";
        $stmt = $this->connection->prepare($sql);
        $defaultNews = [
            [
                'title' => 'Nouvelles Réglementations en Droit des Affaires',
                'content' => 'Découvrez les dernières évolutions législatives affectant les entreprises en 2025.',
                'image_path' => '/public/uploads/news/default_news.jpg',
                'publish_date' => date('Y-m-d H:i:s'),
                'order_position' => 1,
                'is_active' => 1
            ],
            [
                'title' => 'Réforme du Droit de la Famille',
                'content' => 'Une analyse approfondie des récentes modifications du droit matrimonial.',
                'image_path' => '/public/uploads/news/default_news.jpg',
                'publish_date' => date('Y-m-d H:i:s', strtotime('-1 week')),
                'order_position' => 2,
                'is_active' => 1
            ]
        ];

        $insertSql = "INSERT INTO news (title, content, image_path, publish_date, order_position, is_active) 
                      VALUES (?, ?, ?, ?, ?, ?)";
        $insertStmt = $this->connection->prepare($insertSql);
        foreach ($defaultNews as $news) {
            $stmt->execute([$news['title']]);
            if ($stmt->fetch()) continue; // Skip if record exists
            try {
                $insertStmt->execute([
                    $news['title'],
                    $news['content'],
                    $news['image_path'],
                    $news['publish_date'],
                    $news['order_position'],
                    $news['is_active']
                ]);
            } catch (PDOException $e) {
                error_log('Error inserting default news: ' . $e->getMessage());
            }
        }
    }

    private function insertDefaultEvents() {
        $sql = "SELECT 1 FROM events WHERE title = ? LIMIT 1";
        $stmt = $this->connection->prepare($sql);
        $defaultEvents = [
            [
                'title' => 'Conférence sur le Droit Digital',
                'content' => 'Rejoignez-nous pour une conférence sur les défis juridiques du monde digital.',
                'image_path' => '/public/uploads/events/default_event.jpg',
                'event_date' => date('Y-m-d H:i:s', strtotime('+1 month')),
                'order_position' => 1,
                'is_active' => 1
            ],
            [
                'title' => 'Atelier Droit des Affaires',
                'content' => 'Atelier pratique sur les contrats commerciaux et la protection des entreprises.',
                'image_path' => '/public/uploads/events/default_event.jpg',
                'event_date' => date('Y-m-d H:i:s', strtotime('+2 months')),
                'order_position' => 2,
                'is_active' => 1
            ]
        ];

        $insertSql = "INSERT INTO events (title, content, image_path, event_date, order_position, is_active) 
                      VALUES (?, ?, ?, ?, ?, ?)";
        $insertStmt = $this->connection->prepare($insertSql);
        foreach ($defaultEvents as $event) {
            $stmt->execute([$event['title']]);
            if ($stmt->fetch()) continue; // Skip if record exists
            try {
                $insertStmt->execute([
                    $event['title'],
                    $event['content'],
                    $event['image_path'],
                    $event['event_date'],
                    $event['order_position'],
                    $event['is_active']
                ]);
            } catch (PDOException $e) {
                error_log('Error inserting default event: ' . $e->getMessage());
            }
        }
    }

    private function insertDefaultAdmin() {
        $sql = "SELECT 1 FROM admin_users WHERE username = ? LIMIT 1";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([defined('ADMIN_USERNAME') ? ADMIN_USERNAME : 'admin']);
        if ($stmt->fetch()) return;

        $insertSql = "INSERT INTO admin_users (username, password, email, is_active) VALUES (?, ?, ?, 1)";
        $insertStmt = $this->connection->prepare($insertSql);
        try {
            $defaultPassword = defined('ADMIN_PASSWORD') ? ADMIN_PASSWORD : 'admin123';
            $insertStmt->execute([
                defined('ADMIN_USERNAME') ? ADMIN_USERNAME : 'admin',
                password_hash($defaultPassword, PASSWORD_DEFAULT),
                defined('ADMIN_EMAIL') ? ADMIN_EMAIL : 'admin@cabinet-excellence.fr'
            ]);
            error_log("Default admin user created with username: admin");
        } catch (PDOException $e) {
            error_log('Error inserting default admin: ' . $e->getMessage());
        }
    }

    private function insertDefaultAppointmentSlots() {
        $sql = "SELECT 1 FROM appointment_slots WHERE start_time = ? LIMIT 1";
        $stmt = $this->connection->prepare($sql);
        $defaultSlots = [
            [
                'start_time' => date('Y-m-d 09:00:00', strtotime('next Monday')),
                'end_time' => date('Y-m-d 09:30:00', strtotime('next Monday')),
                'is_booked' => 0
            ],
            [
                'start_time' => date('Y-m-d 09:30:00', strtotime('next Monday')),
                'end_time' => date('Y-m-d 10:00:00', strtotime('next Monday')),
                'is_booked' => 0
            ],
            [
                'start_time' => date('Y-m-d 10:00:00', strtotime('next Monday')),
                'end_time' => date('Y-m-d 10:30:00', strtotime('next Monday')),
                'is_booked' => 0
            ]
        ];

        $insertSql = "INSERT INTO appointment_slots (start_time, end_time, is_booked) VALUES (?, ?, ?)";
        $insertStmt = $this->connection->prepare($insertSql);
        foreach ($defaultSlots as $slot) {
            $stmt->execute([$slot['start_time']]);
            if ($stmt->fetch()) continue; // Skip if record exists
            try {
                $insertStmt->execute([
                    $slot['start_time'],
                    $slot['end_time'],
                    $slot['is_booked']
                ]);
            } catch (PDOException $e) {
                error_log('Error inserting default appointment slot: ' . $e->getMessage());
            }
        }
    }

    public function getDefaultDetailedContent() {
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

    public function closeConnection() {
        $this->connection = null;
    }

    public function getDatabaseSize() {
        return file_exists(DB_NAME) ? filesize(DB_NAME) : 0;
    }

    public function optimizeDatabase() {
        try {
            $this->connection->exec('VACUUM;');
            $this->connection->exec('ANALYZE;');
            return true;
        } catch (PDOException $e) {
            error_log('Error optimizing database: ' . $e->getMessage());
            return false;
        }
    }
}
?>