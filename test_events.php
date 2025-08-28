<?php
require_once 'includes/config.php';
require_once 'includes/Database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    echo "Database connection successful!\n";
    
    // Check if events table exists
    $stmt = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='events'");
    $tableExists = $stmt->fetch();
    
    if ($tableExists) {
        echo "events table exists!\n";
        
        // Check table structure
        $stmt = $db->query("PRAGMA table_info(events)");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Events table structure:\n";
        foreach ($columns as $column) {
            echo "{$column['name']} ({$column['type']})\n";
        }
        
        // Check if there are any events in the database
        $stmt = $db->query("SELECT COUNT(*) as count FROM events WHERE is_active = 1");
        $count = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "Active events in database: " . $count['count'] . "\n";
        
        // Show actual events
        $stmt = $db->query("SELECT id, title, event_date, is_active FROM events ORDER BY event_date DESC");
        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Events in database:\n";
        foreach ($events as $event) {
            echo "ID: {$event['id']}, Title: {$event['title']}, Date: {$event['event_date']}, Active: {$event['is_active']}\n";
        }
    } else {
        echo "events table does NOT exist!\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
