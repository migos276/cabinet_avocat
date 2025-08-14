<?php
class Database {
    private $connection = null;
    
    public function getConnection() {
        if ($this->connection === null) {
            try {
                $this->connection = new PDO('sqlite:' . DB_NAME);
                $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->initializeTables();
            } catch (PDOException $e) {
                die('Connection failed: ' . $e->getMessage());
            }
        }
        return $this->connection;
    }
    
    private function initializeTables() {
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
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS team_members (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(255) NOT NULL,
            position VARCHAR(255) NOT NULL,
            description TEXT NOT NULL,
            image_url VARCHAR(500),
            order_position INTEGER DEFAULT 0,
            is_active INTEGER DEFAULT 1,
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
            uploaded_at DATETIME NOT NULL,
            FOREIGN KEY (contact_id) REFERENCES contacts(id) ON DELETE CASCADE
        );
        ";
        
        // Exécuter le SQL pour créer les tables
        $this->connection->exec($sql);
        
        // Créer les index séparément pour une meilleure lisibilité
        $indexes = [
            "CREATE INDEX IF NOT EXISTS idx_contact_files_contact_id ON contact_files(contact_id);",
            "CREATE INDEX IF NOT EXISTS idx_contacts_status ON contacts(status);",
            "CREATE INDEX IF NOT EXISTS idx_contacts_created_at ON contacts(created_at);"
        ];
        
        foreach ($indexes as $index) {
            $this->connection->exec($index);
        }
        
        // Vérifier et ajouter la colonne detailed_content si elle n'existe pas
        $checkColumn = $this->connection->query("PRAGMA table_info(services)")->fetchAll(PDO::FETCH_COLUMN, 1);
        if (!in_array('detailed_content', $checkColumn)) {
            $this->connection->exec("ALTER TABLE services ADD COLUMN detailed_content TEXT;");
        }
        
        // Mettre à jour les contenus détaillés par défaut
        $this->updateDefaultContent();
        
        // Insérer les données par défaut
        $this->insertDefaultData();
    }
    
    private function updateDefaultContent() {
        $sql = "
        UPDATE services SET detailed_content = ?
        WHERE detailed_content IS NULL OR detailed_content = ''";
        
        $defaultContent = '
        <h3>Notre approche</h3>
        <p>Nous privilégions une approche personnalisée et sur-mesure pour chaque client. Notre méthode comprend :</p>
        <ul>
            <li>Analyse approfondie de votre situation</li>
            <li>Conseil juridique adapté à vos besoins</li>
            <li>Accompagnement tout au long de la procédure</li>
            <li>Suivi post-dossier et conseils préventifs</li>
        </ul>

        <h3>Pourquoi nous choisir ?</h3>
        <p>Fort de plus de 20 ans d\'expérience, notre cabinet vous garantit :</p>
        <ul>
            <li>Une expertise reconnue dans ce domaine</li>
            <li>Un accompagnement personnalisé</li>
            <li>Une disponibilité et une réactivité optimales</li>
            <li>Des tarifs transparents et compétitifs</li>
        </ul>

        <h3>Première consultation</h3>
        <p>Nous vous proposons une première consultation gratuite pour évaluer votre situation et vous présenter les différentes options qui s\'offrent à vous. Cette rencontre nous permet de mieux comprendre vos besoins et de vous proposer la stratégie la plus adaptée.</p>
        ';
        
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$defaultContent]);
    }
    
    private function insertDefaultData() {
        // Vérifier si des données existent déjà
        $check = $this->connection->query("SELECT COUNT(*) FROM site_content")->fetchColumn();
        if ($check > 0) return;
        
        // Insérer le contenu par défaut
        $defaultContent = [
            ['hero', 'title', 'Cabinet d\'Excellence Juridique'],
            ['hero', 'subtitle', 'Votre partenaire de confiance pour tous vos besoins juridiques'],
            ['hero', 'cta_text', 'Prendre rendez-vous'],
            ['about', 'title', 'À propos de nous'],
            ['about', 'description', 'Fort de plus de 20 ans d\'expérience, notre cabinet vous accompagne dans tous vos besoins juridiques avec professionnalisme et rigueur.'],
            ['services', 'title', 'Nos services'],
            ['contact', 'title', 'Contactez-nous'],
            ['contact', 'address', '123 Avenue de la Justice, 75001 Paris'],
            ['contact', 'phone', '+33 1 23 45 67 89'],
            ['contact', 'email', 'contact@cabinet-excellence.fr']
        ];
        
        $sql = "INSERT INTO site_content (section, key_name, value) VALUES (?, ?, ?)";
        $stmt = $this->connection->prepare($sql);
        
        foreach ($defaultContent as $content) {
            $stmt->execute($content);
        }
        
        // Insérer les services par défaut
        $this->insertDefaultServices();
        
        // Insérer l'équipe par défaut
        $this->insertDefaultTeam();
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
                'order_position' => 1
            ],
            [
                'title' => 'Droit de la Famille',
                'description' => 'Conseil et représentation dans tous les aspects du droit familial et matrimonial.',
                'icon' => 'fas fa-heart',
                'color' => '#ef4444',
                'order_position' => 2
            ],
            [
                'title' => 'Droit Immobilier',
                'description' => 'Expertise en transactions immobilières, copropriété et contentieux immobiliers.',
                'icon' => 'fas fa-home',
                'color' => '#10b981',
                'order_position' => 3
            ],
            [
                'title' => 'Droit du Travail',
                'description' => 'Protection des droits des salariés et conseil aux employeurs en droit social.',
                'icon' => 'fas fa-users',
                'color' => '#f59e0b',
                'order_position' => 4
            ]
        ];
        
        $sql = "INSERT INTO services (title, description, icon, color, order_position, detailed_content) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->connection->prepare($sql);
        
        $defaultDetailedContent = '
        Notre approche
        Nous privilégions une approche personnalisée et sur-mesure pour chaque client. Notre méthode comprend
        
            Analyse approfondie de votre situation
            Conseil juridique adapté à vos besoins
            Accompagnement tout au long de la procédure
            Suivi post-dossier et conseils préventifs

        Pourquoi nous choisir ?
        Fort de plus de 20 ans d\'expérience, notre cabinet vous garantit
            Une expertise reconnue dans ce domaine
            Un accompagnement personnalisé
            Une disponibilité et une réactivité optimales
            Des tarifs transparents et compétitifs
        

        Première consultation
        Nous vous proposons une première consultation gratuite pour évaluer votre situation et vous présenter les différentes options qui s\'offrent à vous.
        ';
        
        foreach ($defaultServices as $service) {
            $stmt->execute([
                $service['title'],
                $service['description'],
                $service['icon'],
                $service['color'],
                $service['order_position'],
                $defaultDetailedContent
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
                'description' => 'Spécialisé en droit des sociétés et fusions-acquisitions, Maître Dupont accompagne les entreprises dans leurs projets de développement depuis plus de 15 ans.',
                'image_url' => 'https://images.pexels.com/photos/2182970/pexels-photo-2182970.jpeg?auto=compress&cs=tinysrgb&w=400',
                'order_position' => 1
            ],
            [
                'name' => 'Maître Marie Martin',
                'position' => 'Avocate Spécialisée - Droit de la Famille',
                'description' => 'Experte en droit matrimonial et protection de l\'enfance, Maître Martin défend avec passion les intérêts de ses clients dans les situations familiales complexes.',
                'image_url' => 'https://images.pexels.com/photos/3760263/pexels-photo-3760263.jpeg?auto=compress&cs=tinysrgb&w=400',
                'order_position' => 2
            ]
        ];
        
        $sql = "INSERT INTO team_members (name, position, description, image_url, order_position) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->connection->prepare($sql);
        
        foreach ($defaultTeam as $member) {
            $stmt->execute([
                $member['name'],
                $member['position'],
                $member['description'],
                $member['image_url'],
                $member['order_position']
            ]);
        }
    }
}
?>