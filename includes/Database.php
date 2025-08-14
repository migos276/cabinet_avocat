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
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            detailed_content TEXT
        );

        CREATE TABLE IF NOT EXISTS team_members (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            position TEXT NOT NULL,
            description TEXT NOT NULL,
            image_url TEXT NOT NULL,
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
        
        // Insérer les données par défaut
        $this->insertDefaultData();
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
                'order_position' => 1,
                'detailed_content' => '
                    Notre expertise en droit des affaires
                    Nous offrons un accompagnement juridique complet pour les entreprises, couvrant la création, le développement et les opérations complexes telles que fusions et acquisitions.
                    
                        Conseil en création et structuration d\'entreprises
                        Rédaction et négociation de contrats commerciaux
                        Accompagnement dans les fusions, acquisitions et restructurations
                        Gestion des litiges commerciaux

                    Pourquoi nous choisir ?
                    Avec plus de 20 ans d expérience, notre cabinet garantit
                    
                        Une expertise pointue en droit des sociétés
                        Des solutions stratégiques pour optimiser vos opérations
                        Une réactivité face aux besoins urgents de votre entreprise
                        Des honoraires transparents et adaptés
                    

                    Première consultation
                    Profitez d une première consultation gratuite pour discuter de vos projets d\'entreprise et identifier les meilleures solutions juridiques adaptées à vos objectifs
                '
            ],
            [
                'title' => 'Droit de la Famille',
                'description' => 'Conseil et représentation dans tous les aspects du droit familial et matrimonial.',
                'icon' => 'fas fa-heart',
                'color' => '#ef4444',
                'order_position' => 2,
                'detailed_content' => '
                    Notre accompagnement en droit de la famille
                    Nous vous assistons avec sensibilité et rigueur dans toutes les questions liées au droit de la famille, y compris les divorces, les successions et la protection des mineurs.
                    
                        Conseil et médiation en cas de divorce ou séparation
                        Gestion des pensions alimentaires et des droits de garde
                        Planification successorale et partage des biens
                        Protection juridique des enfants et adoption
                    

                    Pourquoi nous choisir ?
                    Notre cabinet se distingue par :
                    
                        Une approche empathique et respectueuse
                        Une expertise approfondie en droit familial
                        Un accompagnement personnalisé à chaque étape
                        Une disponibilité pour répondre à vos préoccupations
                    

                    Première consultation
                    Nous offrons une consultation initiale gratuite pour évaluer votre situation familiale et vous proposer des solutions adaptées à vos besoins spécifiques.
                '
            ],
            [
                'title' => 'Droit Immobilier',
                'description' => 'Expertise en transactions immobilières, copropriété et contentieux immobiliers.',
                'icon' => 'fas fa-home',
                'color' => '#10b981',
                'order_position' => 3,
                'detailed_content' => '
                    Notre expertise en droit immobilier
                    Que vous soyez acheteur, vendeur ou propriétaire, nous vous accompagnons dans toutes vos démarches immobilières, des transactions aux litiges complexes.
                    
                        Rédaction et analyse de contrats de vente ou de location
                        Gestion des litiges en copropriété
                        Conseil en droit de la construction
                        Représentation dans les contentieux immobiliers
                    

                    Pourquoi nous choisir ?
                    Notre cabinet se démarque par :
                    
                        Une connaissance approfondie du marché immobilier
                        Une expertise en négociation de contrats complexes
                        Une défense rigoureuse de vos intérêts
                        Des solutions pratiques et efficaces
                    

                    Première consultation
                    Contactez-nous pour une première consultation gratuite afin d\'évaluer vos besoins immobiliers et recevoir des conseils juridiques personnalisés.
                '
            ],
            [
                'title' => 'Droit du Travail',
                'description' => 'Protection des droits des salariés et conseil aux employeurs en droit social.',
                'icon' => 'fas fa-users',
                'color' => '#f59e0b',
                'order_position' => 4,
                'detailed_content' => '
                    Notre accompagnement en droit du travail
                    Nous défendons les intérêts des salariés et conseillons les employeurs pour assurer la conformité et la résolution des conflits en droit social.
            
                        Rédaction et négociation de contrats de travail
                        Conseil en licenciements et ruptures conventionnelles
                        Représentation devant les prud\'hommes
                        Accompagnement en matière de santé et sécurité au travail

                    Pourquoi nous choisir ?
                    Notre cabinet vous offre 
                        Une expertise reconnue en droit social
                        Une défense proactive de vos droits
                        Des conseils pratiques pour éviter les litiges
                        Une disponibilité pour répondre à vos urgences
                    

                    Première consultation
                    Profitez d une première consultation gratuite pour analyser votre situation en droit du travail et explorer les options disponibles.
                '
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
                'description' => 'Spécialisé en droit des sociétés et fusions-acquisitions, Maître Dupont accompagne les entreprises dans leurs projets de développement depuis plus de 15 ans.',
                'image_url' => 'https://www.startpage.com/av/proxy-image?piurl=https%3A%2F%2Ftse1.mm.bing.net%2Fth%2Fid%2FOIP.-TP4HAOrEqyzCIs1445m_AHaGP%3Fr%3D0%26pid%3DApi&sp=1755176715Tc341f8588c5dd15639858351feee0b6d86788ed3165bda54ea22ba2f0351162a',
                'order_position' => 1
            ],
            [
                'name' => 'Maître Marie Martin',
                'position' => 'Avocate Spécialisée - Droit de la Famille',
                'description' => 'Experte en droit matrimonial et protection de l\'enfance, Maître Martin défend avec passion les intérêts de ses clients dans les situations familiales complexes.',
                'image_url' => 'https://www.startpage.com/av/proxy-image?piurl=https%3A%2F%2Ftse4.mm.bing.net%2Fth%2Fid%2FOIP.urLU6Si-1Zy58duQh7PNmAAAAA%3Fr%3D0%26pid%3DApi&sp=1755176715T4e8fe4dce8e822ab3885eba5d3768dd4a85f6d828c58fe96bfaecca44fa020e4',
                'order_position' => 2
            ]
        ];
        
        $sql = "INSERT INTO team_members (name, position, description, image_url, is_active, order_position) VALUES (?, ?, ?, ?, 1, ?)";
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