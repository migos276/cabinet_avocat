<?php
require_once 'includes/Database.php';

class HomeController {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        
        // Vérifier et initialiser les données si nécessaire
        $this->ensureDefaultData();
    }
    
    public function index() {
        try {
            // Get content
            $content = $this->getContent();
            $services = $this->getServices();
            $team = $this->getTeam();
            
            // Debug : vérifier si on a des données
            error_log("HomeController - Content count: " . count($content));
            error_log("HomeController - Services count: " . count($services));
            error_log("HomeController - Team count: " . count($team));
            
            // Si pas de données, forcer l'initialisation
            if (empty($services) || empty($team) || empty($content)) {
                error_log("Données manquantes détectées - réinitialisation");
                $this->forceResetData();
                
                // Récupérer les données à nouveau
                $content = $this->getContent();
                $services = $this->getServices();
                $team = $this->getTeam();
            }
            
            include 'views/home.php';
            
        } catch (Exception $e) {
            error_log("Erreur HomeController::index - " . $e->getMessage());
            
            // En cas d'erreur, utiliser des données par défaut
            $content = $this->getDefaultContent();
            $services = $this->getDefaultServices();
            $team = $this->getDefaultTeam();
            
            include 'views/home.php';
        }
    }
    
    private function ensureDefaultData() {
        try {
            // Vérifier si les tables ont des données
            $serviceCount = $this->db->query("SELECT COUNT(*) FROM services")->fetchColumn();
            $teamCount = $this->db->query("SELECT COUNT(*) FROM team_members")->fetchColumn();
            $contentCount = $this->db->query("SELECT COUNT(*) FROM site_content")->fetchColumn();
            
            error_log("Counts - Services: $serviceCount, Team: $teamCount, Content: $contentCount");
            
            if ($serviceCount == 0) {
                $this->insertDefaultServices();
            }
            
            if ($teamCount == 0) {
                $this->insertDefaultTeam();
            }
            
            if ($contentCount == 0) {
                $this->insertDefaultContent();
            }
            
        } catch (Exception $e) {
            error_log("Erreur ensureDefaultData: " . $e->getMessage());
        }
    }
    
    private function forceResetData() {
        try {
            // Supprimer et recréer les données
            $this->db->exec("DELETE FROM services");
            $this->db->exec("DELETE FROM team_members");
            $this->db->exec("DELETE FROM site_content");
            
            $this->insertDefaultServices();
            $this->insertDefaultTeam();
            $this->insertDefaultContent();
            
            error_log("Données par défaut réinsérées avec succès");
            
        } catch (Exception $e) {
            error_log("Erreur forceResetData: " . $e->getMessage());
        }
    }
    
    private function getContent() {
        try {
            $stmt = $this->db->query("SELECT section, key_name, value FROM site_content");
            $content = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $content[$row['section']][$row['key_name']] = $row['value'];
            }
            
            // Si vide, retourner des valeurs par défaut
            if (empty($content)) {
                return $this->getDefaultContent();
            }
            
            return $content;
        } catch (Exception $e) {
            error_log("Erreur getContent: " . $e->getMessage());
            return $this->getDefaultContent();
        }
    }
    
    private function getServices() {
        try {
            $stmt = $this->db->query("SELECT * FROM services WHERE is_active = 1 ORDER BY order_position");
            $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Si vide, retourner des services par défaut
            if (empty($services)) {
                return $this->getDefaultServices();
            }
            
            return $services;
        } catch (Exception $e) {
            error_log("Erreur getServices: " . $e->getMessage());
            return $this->getDefaultServices();
        }
    }
    
    private function getTeam() {
        try {
            $stmt = $this->db->query("SELECT id, name, position, description, image_path, order_position, is_active FROM team_members WHERE is_active = 1 ORDER BY order_position");
            $team = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Vérifier les chemins d'image
            foreach ($team as &$member) {
                if (empty($member['image_path']) || !file_exists($_SERVER['DOCUMENT_ROOT'] . $member['image_path'])) {
                    error_log("Image manquante pour {$member['name']}: {$member['image_path']}");
                    $member['image_path'] = '/public/uploads/team/default_team_member.jpg';
                }
            }
            
            // Si vide, retourner une équipe par défaut
            if (empty($team)) {
                return $this->getDefaultTeam();
            }
            
            return $team;
        } catch (Exception $e) {
            error_log("Erreur getTeam: " . $e->getMessage());
            return $this->getDefaultTeam();
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
        
        $sql = "INSERT INTO services (title, description, icon, color, order_position, detailed_content, is_active) VALUES (?, ?, ?, ?, ?, ?, 1)";
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
        
        error_log("Services par défaut insérés");
    }
    
    private function insertDefaultTeam() {
        $defaultTeam = [
            [
                'name' => 'Maître Jean Dupont',
                'position' => 'Avocat Associé - Droit des Affaires',
                'description' => 'Spécialisé en droit des sociétés et fusions-acquisitions, Maître Dupont accompagne les entreprises dans leurs projets de développement depuis plus de 15 ans.',
                'image_path' => '/public/uploads/team/default_team_member_1.jpg',
                'order_position' => 1
            ],
            [
                'name' => 'Maître Marie Martin',
                'position' => 'Avocate Spécialisée - Droit de la Famille',
                'description' => 'Experte en droit matrimonial et protection de l\'enfance, Maître Martin défend avec passion les intérêts de ses clients dans les situations familiales complexes.',
                'image_path' => '/public/uploads/team/default_team_member_2.jpg',
                'order_position' => 2
            ]
        ];
        
        $sql = "INSERT INTO team_members (name, position, description, image_path, order_position, is_active) VALUES (?, ?, ?, ?, ?, 1)";
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
        
        error_log("Équipe par défaut insérée");
    }
    
    private function insertDefaultContent() {
        $defaultContent = [
            ['hero', 'title', 'Cabinet d\'Excellence Juridique'],
            ['hero', 'subtitle', 'Votre partenaire de confiance pour tous vos besoins juridiques'],
            ['hero', 'cta_text', 'Prendre rendez-vous'],
            ['about', 'title', 'À propos de nous'],
            ['about', 'subtitle', 'Fort de plus de 20 ans d\'expérience, notre cabinet vous accompagne dans tous vos besoins juridiques avec professionnalisme et rigueur.'],
            ['services', 'title', 'Nos services'],
            ['services', 'subtitle', 'Des domaines d\'expertise variés pour répondre à tous vos besoins'],
            ['team', 'title', 'Notre équipe'],
            ['team', 'subtitle', 'Des experts à votre service'],
            ['contact', 'title', 'Contactez-nous'],
            ['contact', 'address', '123 Avenue de la Justice, 75001 Paris'],
            ['contact', 'phone', '+33 1 23 45 67 89'],
            ['contact', 'email', 'contact@cabinet-excellence.fr']
        ];
        
        $sql = "INSERT INTO site_content (section, key_name, value) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        
        foreach ($defaultContent as $content) {
            $stmt->execute($content);
        }
        
        error_log("Contenu par défaut inséré");
    }
    
    private function getDefaultDetailedContent() {
        return '
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
        <p>Nous vous proposons une première consultation gratuite pour évaluer votre situation et vous présenter les différentes options qui s\'offrent à vous.</p>
        ';
    }
    
    // Méthodes de fallback avec des données par défaut
    private function getDefaultContent() {
        return [
            'hero' => [
                'title' => 'Cabinet d\'Excellence Juridique',
                'subtitle' => 'Votre partenaire de confiance pour tous vos besoins juridiques',
                'cta_text' => 'Prendre rendez-vous'
            ],
            'about' => [
                'title' => 'À propos de nous',
                'subtitle' => 'Fort de plus de 20 ans d\'expérience, notre cabinet vous accompagne dans tous vos besoins juridiques avec professionnalisme et rigueur.'
            ],
            'services' => [
                'title' => 'Nos services',
                'subtitle' => 'Des domaines d\'expertise variés pour répondre à tous vos besoins'
            ],
            'team' => [
                'title' => 'Notre équipe',
                'subtitle' => 'Des experts à votre service'
            ],
            'contact' => [
                'title' => 'Contactez-nous',
                'address' => '123 Avenue de la Justice, 75001 Paris',
                'phone' => '+33 1 23 45 67 89',
                'email' => 'contact@cabinet-excellence.fr'
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
                'description' => 'Spécialisé en droit des sociétés et fusions-acquisitions, Maître Dupont accompagne les entreprises dans leurs projets de développement depuis plus de 15 ans.',
                'image_path' => '/public/uploads/team/avocat4.jpg',
                'order_position' => 1,
                'is_active' => 1
            ],
            [
                'id' => 2,
                'name' => 'Maître Marie Martin',
                'position' => 'Avocate Spécialisée - Droit de la Famille',
                'description' => 'Experte en droit matrimonial et protection de l\'enfance, Maître Martin défend avec passion les intérêts de ses clients dans les situations familiales complexes.',
                'image_path' => '/public/uploads/team/avocat5.jpg',
                'order_position' => 2,
                'is_active' => 1
            ]
        ];
    }
}
?>