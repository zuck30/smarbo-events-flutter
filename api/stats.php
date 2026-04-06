<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

$auth = new Auth();
$auth->requireLogin();

$functions = new Functions();

if ($_SESSION['role'] === 'admin') {
    $stats = $functions->getAdminStats();
} else {
    // Event owner stats
    $conn = getConnection();
    $userId = $_SESSION['user_id'];
    
    $stats = [];
    
    // Get user's events
    $stmt = $conn->prepare("SELECT id FROM events WHERE event_owner_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $events = $stmt->get_result();
    
    $eventIds = [];
    while($event = $events->fetch_assoc()) {
        $eventIds[] = $event['id'];
    }
    
    if (!empty($eventIds)) {
        $eventIdsStr = implode(',', $eventIds);
        
        // Contributions stats
        $result = $conn->query("
            SELECT 
                SUM(promised_amount) as total_promised,
                SUM(paid_amount) as total_paid
            FROM contributions 
            WHERE event_id IN ($eventIdsStr)
        ");
        $stats['contributions'] = $result->fetch_assoc();
        
        // Events count
        $result = $conn->query("SELECT COUNT(*) as total FROM events WHERE event_owner_id = $userId");
        $stats['total_events'] = $result->fetch_assoc()['total'];
    }
}

jsonResponse(true, 'Stats retrieved', $stats);
?>