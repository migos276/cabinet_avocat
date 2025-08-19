<?php
// Inclure le fichier de configuration
require_once __DIR__ . '/config.php';

class Database {
    private $connection = null;
    
    public function getConnection() {
        if ($this->connection === null) {
            try {
                // Vérifier que la constante DB_NAME est définie
                if (!defined('DB_NAME')) {
                    throw new Exception('DB_NAME constant is not defined. Please check your config.php file.');
                }
                
                // Créer le répertoire de la base de données si nécessaire
                $dbDir = dirname(DB_NAME);
                if (!is_dir($dbDir)) {
                    if (!mkdir($dbDir, 0755, true)) {
                        throw new Exception('Cannot create database directory: ' . $dbDir);
                    }
                }
                
                // Connexion SQLite avec le chemin complet
                $this->connection = new PDO('sqlite:' . DB_NAME);
                $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                
                // Activer les clés étrangères pour SQLite
                $this->connection->exec('PRAGMA foreign_keys = ON;');
                
                // Optimisations SQLite
                $this->connection->exec('PRAGMA journal_mode = WAL;');
                $this->connection->exec('PRAGMA synchronous = NORMAL;');
                $this->connection->exec('PRAGMA cache_size = 10000;');
                $this->connection->exec('PRAGMA temp_store = MEMORY;');
                
                $this->initializeTables();
                
            } catch (PDOException $e) {
                error_log('Database PDO Error: ' . $e->getMessage());
                throw new Exception('Database connection failed: ' . $e->getMessage());
            } catch (Exception $e) {
                error_log('Database Error: ' . $e->getMessage());
                throw new Exception('Database error: ' . $e->getMessage());
            }
        }
        return $this->connection;
    }
    
    private function initializeTables() {
        try {
            // Commencer une transaction pour l'initialisation
            $this->connection->beginTransaction();
            
            $sql = "
            CREATE TABLE IF NOT EXISTS site_content (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                section VARCHAR(50) NOT NULL,
                key_name VARCHAR(100) NOT NULL,
                value TEXT NOT NULL,
                type VARCHAR(20) DEFAULT 'text',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                UNIQUE(section, key_name)
            );

            CREATE TABLE IF NOT EXISTS contacts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL,
                phone VARCHAR(50),
                subject VARCHAR(255),
                message TEXT NOT NULL,
                status VARCHAR(20) DEFAULT 'new',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS services (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title VARCHAR(255) NOT NULL,
                description TEXT NOT NULL,
                icon VARCHAR(50) NOT NULL,
                color VARCHAR(7) DEFAULT '#3b82f6',
                order_position INTEGER DEFAULT 0,
                is_active INTEGER DEFAULT 1,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                detailed_content TEXT
            );

            CREATE TABLE IF NOT EXISTS team_members (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                position TEXT NOT NULL,
                description TEXT NOT NULL,
                image_path TEXT NOT NULL,
                is_active INTEGER DEFAULT 1,
                order_position INTEGER DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS admin_settings (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                setting_key VARCHAR(100) NOT NULL UNIQUE,
                setting_value TEXT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
            
            CREATE TABLE IF NOT EXISTS contact_files (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                contact_id INTEGER NOT NULL,
                original_name VARCHAR(255) NOT NULL,
                file_name VARCHAR(255) NOT NULL,
                file_path VARCHAR(500) NOT NULL,
                file_size INTEGER NOT NULL,
                file_type VARCHAR(50) NOT NULL,
                uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (contact_id) REFERENCES contacts(id) ON DELETE CASCADE
            );

            CREATE TABLE IF NOT EXISTS admin_users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username VARCHAR(100) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                email VARCHAR(255),
                is_active INTEGER DEFAULT 1,
                last_login DATETIME,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
            ";
            
            // Exécuter le SQL pour créer les tables
            $this->connection->exec($sql);
            
            // Créer les index pour optimiser les performances
            $this->createIndexes();
            
            // Vérifier et ajouter les colonnes manquantes
            $this->updateTables();
            
            // Insérer les données par défaut
            $this->insertDefaultData();
            
            // Valider la transaction
            $this->connection->commit();
            
        } catch (Exception $e) {
            // Annuler la transaction en cas d'erreur
            $this->connection->rollBack();
            error_log('Error initializing tables: ' . $e->getMessage());
            throw $e;
        }
    }
    
    private function createIndexes() {
        $indexes = [
            "CREATE INDEX IF NOT EXISTS idx_contact_files_contact_id ON contact_files(contact_id);",
            "CREATE INDEX IF NOT EXISTS idx_contacts_status ON contacts(status);",
            "CREATE INDEX IF NOT EXISTS idx_contacts_created_at ON contacts(created_at);",
            "CREATE INDEX IF NOT EXISTS idx_contacts_email ON contacts(email);",
            "CREATE INDEX IF NOT EXISTS idx_site_content_section ON site_content(section);",
            "CREATE INDEX IF NOT EXISTS idx_site_content_key ON site_content(section, key_name);",
            "CREATE INDEX IF NOT EXISTS idx_services_active ON services(is_active, order_position);",
            "CREATE INDEX IF NOT EXISTS idx_team_members_active ON team_members(is_active, order_position);",
            "CREATE INDEX IF NOT EXISTS idx_admin_settings_key ON admin_settings(setting_key);"
        ];
        
        foreach ($indexes as $index) {
            try {
                $this->connection->exec($index);
            } catch (PDOException $e) {
                // Ignorer les erreurs d'index déjà existants
                if (strpos($e->getMessage(), 'already exists') === false) {
                    error_log('Error creating index: ' . $e->getMessage());
                }
            }
        }
    }
    
    private function updateTables() {
        // Vérifier et ajouter la colonne detailed_content à services
        try {
            $checkColumn = $this->connection->query("PRAGMA table_info(services)")->fetchAll(PDO::FETCH_COLUMN, 1);
            if (!in_array('detailed_content', $checkColumn)) {
                $this->connection->exec("ALTER TABLE services ADD COLUMN detailed_content TEXT;");
            }
        } catch (PDOException $e) {
            error_log('Error updating services table: ' . $e->getMessage());
        }
    }
    
    private function insertDefaultData() {
        try {
            // Vérifier si des données existent déjà
            $check = $this->connection->query("SELECT COUNT(*) FROM site_content")->fetchColumn();
            if ($check > 0) return;
            
            // Insérer le contenu par défaut
            $this->insertDefaultSiteContent();
            $this->insertDefaultServices();
            $this->insertDefaultTeam();
            $this->insertDefaultAdmin();
            
        } catch (PDOException $e) {
            error_log('Error inserting default data: ' . $e->getMessage());
            throw $e;
        }
    }
    
    private function insertDefaultSiteContent() {
        $defaultContent = [
            ['hero', 'title', SITE_NAME],
            ['hero', 'subtitle', 'Votre partenaire de confiance pour tous vos besoins juridiques'],
            ['hero', 'cta_text', 'Prendre rendez-vous'],
            ['about', 'title', 'À propos de nous'],
            ['about', 'description', 'Fort de plus de 20 ans d\'expérience, notre cabinet vous accompagne dans tous vos besoins juridiques avec professionnalisme et rigueur.'],
            ['services', 'title', 'Nos services'],
            ['team', 'title', 'Notre équipe'],
            ['contact', 'title', 'Contactez-nous'],
            ['contact', 'address', '123 Avenue de la Justice, 75001 Paris'],
            ['contact', 'phone', '+33 1 23 45 67 89'],
            ['contact', 'email', ADMIN_EMAIL],
            ['footer', 'copyright', '© 2025 ' . SITE_NAME . '. Tous droits réservés.']
        ];
        
        $sql = "INSERT INTO site_content (section, key_name, value) VALUES (?, ?, ?)";
        $stmt = $this->connection->prepare($sql);
        
        foreach ($defaultContent as $content) {
            $stmt->execute($content);
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
                'detailed_content' => 'Notre expertise en droit des affaires couvre tous les aspects juridiques de la vie d\'entreprise : création, structuration, développement et opérations complexes.'
            ],
            [
                'title' => 'Droit de la Famille',
                'description' => 'Conseil et représentation dans tous les aspects du droit familial et matrimonial.',
                'icon' => 'fas fa-heart',
                'color' => '#ef4444',
                'order_position' => 2,
                'detailed_content' => 'Nous vous accompagnons avec sensibilité et professionnalisme dans toutes les démarches liées au droit de la famille.'
            ],
            [
                'title' => 'Droit Immobilier',
                'description' => 'Expertise en transactions immobilières, copropriété et contentieux immobiliers.',
                'icon' => 'fas fa-home',
                'color' => '#10b981',
                'order_position' => 3,
                'detailed_content' => 'Spécialistes en droit immobilier, nous vous assistons dans vos projets d\'acquisition, vente et gestion immobilière.'
            ],
            [
                'title' => 'Droit du Travail',
                'description' => 'Protection des droits des salariés et conseil aux employeurs en droit social.',
                'icon' => 'fas fa-users',
                'color' => '#f59e0b',
                'order_position' => 4,
                'detailed_content' => 'Notre expertise en droit du travail vous garantit une défense efficace de vos droits et une conformité réglementaire.'
            ]
        ];
        
        $sql = "INSERT INTO services (title, description, icon, color, order_position, detailed_content) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->connection->prepare($sql);
        
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
        $check = $this->connection->query("SELECT COUNT(*) FROM team_members")->fetchColumn();
        if ($check > 0) return;
        
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
        
        $sql = "INSERT INTO team_members (name, position, description, image_path, is_active, order_position) VALUES (?, ?, ?, ?, 1, ?)";
        $stmt = $this->connection->prepare($sql);
        
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
    
    private function insertDefaultAdmin() {
        $check = $this->connection->query("SELECT COUNT(*) FROM admin_users")->fetchColumn();
        if ($check > 0) return;
        
        $sql = "INSERT INTO admin_users (username, password, email, is_active) VALUES (?, ?, ?, 1)";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            ADMIN_USERNAME,
            ADMIN_PASSWORD,
            ADMIN_EMAIL
        ]);
    }
    
    public function closeConnection() {
        $this->connection = null;
    }
    
    // Méthode utilitaire pour obtenir la taille de la base de données
    public function getDatabaseSize() {
        if (file_exists(DB_NAME)) {
            return filesize(DB_NAME);
        }
        return 0;
    }
    
    // Méthode pour optimiser la base de données
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