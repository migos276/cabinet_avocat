<?php
require_once 'includes/Database.php';

class HomeController {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    public function index() {
        // Get content
        $content = $this->getContent();
        $services = $this->getServices();
        $team = $this->getTeam();
        
        include 'views/home.php';
    }
    
    private function getContent() {
        $stmt = $this->db->query("SELECT section, key_name, value FROM site_content");
        $content = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $content[$row['section']][$row['key_name']] = $row['value'];
        }
        return $content;
    }
    
    private function getServices() {
        $stmt = $this->db->query("SELECT * FROM services WHERE is_active = 1 ORDER BY order_position");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function getTeam() {
        $stmt = $this->db->query("SELECT * FROM team_members WHERE is_active = 1 ORDER BY order_position");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>