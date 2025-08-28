<?php
require_once 'includes/config.php';
require_once 'includes/Database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    echo "Database connection successful!\n";
    
    // Check if appointment_slots table exists
    $stmt = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='appointment_slots'");
    $tableExists = $stmt->fetch();
    
    if ($tableExists) {
        echo "appointment_slots table exists!\n";
        
        // Check table structure
        $stmt = $db->query("PRAGMA table_info(appointment_slots)");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Table structure:\n";
        foreach ($columns as $column) {
            echo "{$column['name']} ({$column['type']})\n";
        }
    } else {
        echo "appointment_slots table does NOT exist!\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
