<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

$auth = new Auth();
$auth->requireLogin();

$conn = getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        $eventId = intval($_GET['id']);
        $stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
        $stmt->bind_param("i", $eventId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $event = $result->fetch_assoc();
            jsonResponse(true, 'Event retrieved', $event);
        } else {
            jsonResponse(false, 'Event not found');
        }
    } else {
        // Get all events based on role
        if ($_SESSION['role'] === 'admin') {
            $events = $conn->query("
                SELECT e.*, u.full_name as owner_name 
                FROM events e 
                JOIN users u ON e.event_owner_id = u.id 
                ORDER BY e.event_date DESC
            ");
        } else {
            $userId = $_SESSION['user_id'];
            $stmt = $conn->prepare("
                SELECT e.*, u.full_name as owner_name 
                FROM events e 
                JOIN users u ON e.event_owner_id = u.id 
                WHERE e.event_owner_id = ? 
                ORDER BY e.event_date DESC
            ");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $events = $stmt->get_result();
        }
        
        $eventsList = [];
        while($event = $events->fetch_assoc()) {
            $eventsList[] = $event;
        }
        
        jsonResponse(true, 'Events retrieved', $eventsList);
    }
}
?>