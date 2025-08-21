<?php
require_once __DIR__ . '/config.php';

class Database {
    private $connection = null;

    public function getConnection() {
        if ($this->connection === null) {
            try {
                if (!defined('DB_NAME')) {
                    throw new Exception('DB_NAME constant is not defined. Please check your config.php file.');
                }

                $dbDir = dirname(DB_NAME);
                if (!is_dir($dbDir) && !mkdir($dbDir, 0755, true) && !is_dir($dbDir)) {
                    throw new Exception('Cannot create database directory: ' . $dbDir);
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
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
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
                    image_path TEXT, -- Changed to allow NULL for cases where no image is uploaded
                    is_active INTEGER DEFAULT 1,
                    order_position INTEGER DEFAULT 0,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                )",
                "CREATE TABLE IF NOT EXISTS news (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    title TEXT NOT NULL,
                    content TEXT NOT NULL,
                    image_path TEXT, -- Changed to allow NULL for cases where no image is uploaded
                    publish_date DATETIME NOT NULL,
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
                )"
            ];

            foreach ($tables as $sql) {
                $this->connection->exec($sql);
            }

            // Ensure order_position column exists in news table
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

            // Fix color column length if needed
            $service_columns = $this->connection->query("PRAGMA table_info(services)")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($service_columns as $col) {
                if ($col['name'] === 'color' && $col['type'] === 'VARCHAR(7)') {
                    $this->connection->exec("ALTER TABLE services RENAME COLUMN color TO old_color");
                    $this->connection->exec("ALTER TABLE services ADD COLUMN color VARCHAR(255) DEFAULT '#3b82f6'");
                    $this->connection->exec("UPDATE services SET color = old_color");
                    $this->connection->exec("ALTER TABLE services DROP COLUMN old_color");
                }
            }

            $this->createIndexes();
            $this->insertDefaultData();
            $this->connection->commit();

        } catch (Exception $e) {
            $this->connection->rollBack();
            error_log('Error initializing tables: ' . $e->getMessage());
            throw $e;
        }
    }

    private function createIndexes() {
        $indexes = [
            "CREATE INDEX IF NOT EXISTS idx_contact_files_contact_id ON contact_files(contact_id)",
            "CREATE INDEX IF NOT EXISTS idx_contacts_status ON contacts(status)",
            "CREATE INDEX IF NOT EXISTS idx_contacts_created_at ON contacts(created_at)",
            "CREATE INDEX IF NOT EXISTS idx_contacts_email ON contacts(email)",
            "CREATE INDEX IF NOT EXISTS idx_site_content_section ON site_content(section)",
            "CREATE INDEX IF NOT EXISTS idx_services_active ON services(is_active, order_position)",
            "CREATE INDEX IF NOT EXISTS idx_team_members_active ON team_members(is_active, order_position)",
            "CREATE INDEX IF NOT EXISTS idx_news_active ON news(is_active, order_position)"
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
        $check = $this->connection->query("SELECT COUNT(*) FROM site_content")->fetchColumn();
        if ($check > 0) return;

        $this->insertDefaultSiteContent();
        $this->insertDefaultServices();
        $this->insertDefaultTeam();
        $this->insertDefaultNews();
        $this->insertDefaultAdmin();
    }

    private function insertDefaultSiteContent() {
        $defaultContent = [
            ['hero', 'title', defined('SITE_NAME') ? SITE_NAME : 'Cabinet Excellence'],
            ['hero', 'subtitle', 'Votre partenaire de confiance pour tous vos besoins juridiques'],
            ['about', 'title', 'À propos de nous'],
            ['about', 'subtitle', 'Fort de plus de 20 ans d\'expérience, notre cabinet vous accompagne dans tous vos besoins juridiques avec professionnalisme et rigueur.'],
            ['services', 'title', 'Nos services'],
            ['services', 'subtitle', 'Des domaines d\'expertise variés pour répondre à tous vos besoins'],
            ['team', 'title', 'Notre équipe'],
            ['team', 'subtitle', 'Des experts à votre service'],
            ['news', 'title', 'Nos Dernières Actualités'],
            ['news', 'subtitle', 'Restez informé des dernières nouvelles et mises à jour juridiques de notre cabinet.'],
            ['contact', 'title', 'Contactez-nous'],
            ['contact', 'address', '123 Avenue de la Justice, 75001 Paris'],
            ['contact', 'phone', '+33 1 23 45 67 89'],
            ['contact', 'email', defined('ADMIN_EMAIL') ? ADMIN_EMAIL : 'contact@cabinet-excellence.fr'],
            ['footer', 'copyright', '© ' . date('Y') . ' ' . (defined('SITE_NAME') ? SITE_NAME : 'Cabinet Excellence') . '. Tous droits réservés.']
        ];

        $sql = "INSERT INTO site_content (section, key_name, value) VALUES (?, ?, ?)";
        $stmt = $this->connection->prepare($sql);
        foreach ($defaultContent as $content) {
            try {
                $stmt->execute($content);
            } catch (PDOException $e) {
                error_log('Error inserting default site content: ' . $e->getMessage());
            }
        }
    }

    private function insertDefaultServices() {
        $check = $this->connection->query("SELECT COUNT(*) FROM services")->fetchColumn();
        if ($check > 0) return;

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
        $stmt = $this->connection->prepare($sql);
        foreach ($defaultServices as $service) {
            try {
                $stmt->execute([
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
        $check = $this->connection->query("SELECT COUNT(*) FROM team_members")->fetchColumn();
        if ($check > 0) return;

        $defaultTeam = [
            [
                'name' => 'Maître Jean Dupont',
                'position' => 'Avocat Associé - Droit des Affaires',
                'description' => 'Spécialisé en droit des sociétés et fusions-acquisitions, Maître Dupont accompagne les entreprises depuis plus de 15 ans.',
                'image_path' => null, // Allow NULL for optional image
                'order_position' => 1
            ],
            [
                'name' => 'Maître Marie Martin',
                'position' => 'Avocate Spécialisée - Droit de la Famille',
                'description' => 'Experte en droit matrimonial et protection de l\'enfance, Maître Martin défend les intérêts familiaux avec passion.',
                'image_path' => null,
                'order_position' => 2
            ]
        ];

        $sql = "INSERT INTO team_members (name, position, description, image_path, order_position, is_active) 
                VALUES (?, ?, ?, ?, ?, 1)";
        $stmt = $this->connection->prepare($sql);
        foreach ($defaultTeam as $member) {
            try {
                $stmt->execute([
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
        $check = $this->connection->query("SELECT COUNT(*) FROM news")->fetchColumn();
        if ($check > 0) return;

        $defaultNews = [
            [
                'title' => 'Nouvelles Réglementations en Droit des Affaires',
                'content' => 'Découvrez les dernières évolutions législatives affectant les entreprises en 2025.',
                'image_path' => null, // Allow NULL for optional image
                'publish_date' => date('Y-m-d H:i:s'),
                'order_position' => 1,
                'is_active' => 1
            ],
            [
                'title' => 'Réforme du Droit de la Famille',
                'content' => 'Une analyse approfondie des récentes modifications du droit matrimonial.',
                'image_path' => null,
                'publish_date' => date('Y-m-d H:i:s', strtotime('-1 week')),
                'order_position' => 2,
                'is_active' => 1
            ]
        ];

        $sql = "INSERT INTO news (title, content, image_path, publish_date, order_position, is_active) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->connection->prepare($sql);
        foreach ($defaultNews as $news) {
            try {
                $stmt->execute([
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

    private function insertDefaultAdmin() {
        $check = $this->connection->query("SELECT COUNT(*) FROM admin_users")->fetchColumn();
        if ($check > 0) return;

        $sql = "INSERT INTO admin_users (username, password, email, is_active) VALUES (?, ?, ?, 1)";
        $stmt = $this->connection->prepare($sql);
        try {
            $defaultPassword = defined('ADMIN_PASSWORD') ? ADMIN_PASSWORD : 'admin123';
            $stmt->execute([
                defined('ADMIN_USERNAME') ? ADMIN_USERNAME : 'admin',
                password_hash($defaultPassword, PASSWORD_DEFAULT),
                defined('ADMIN_EMAIL') ? ADMIN_EMAIL : 'admin@cabinet-excellence.fr'
            ]);
            error_log("Default admin user created with username: admin, password: $defaultPassword");
        } catch (PDOException $e) {
            error_log('Error inserting default admin: ' . $e->getMessage());
        }
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