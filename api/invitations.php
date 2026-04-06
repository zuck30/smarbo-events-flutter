<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

$auth = new Auth();
$auth->requireLogin();

$conn = getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $eventId = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;
    
    if ($eventId > 0) {
        // Check if user has access to this event
        if ($_SESSION['role'] === 'event_owner') {
            $checkStmt = $conn->prepare("SELECT id FROM events WHERE id = ? AND event_owner_id = ?");
            $checkStmt->bind_param("ii", $eventId, $_SESSION['user_id']);
            $checkStmt->execute();
            
            if ($checkStmt->get_result()->num_rows === 0) {
                jsonResponse(false, 'Access denied');
                exit();
            }
        }
        
        $stmt = $conn->prepare("SELECT * FROM invitations WHERE event_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $eventId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $invitations = [];
        while($row = $result->fetch_assoc()) {
            $invitations[] = $row;
        }
        
        jsonResponse(true, 'Invitations retrieved', $invitations);
    } else {
        jsonResponse(false, 'Event ID is required');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $eventId = intval($data['event_id']);
    $guestName = sanitize($data['guest_name'], $conn);
    $phoneNumber = sanitize($data['phone_number'], $conn);
    $status = sanitize($data['status'] ?? 'pending', $conn);
    
    if (isset($data['id']) && $data['id'] > 0) {
        $id = intval($data['id']);
        $stmt = $conn->prepare("UPDATE invitations SET guest_name = ?, phone_number = ?, status = ? WHERE id = ?");
        $stmt->bind_param("sssi", $guestName, $phoneNumber, $status, $id);
        $message = 'Invitation updated successfully';
    } else {
        $stmt = $conn->prepare("INSERT INTO invitations (event_id, guest_name, phone_number, status) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $eventId, $guestName, $phoneNumber, $status);
        $message = 'Invitation added successfully';
    }
    
    if ($stmt->execute()) {
        jsonResponse(true, $message);
    } else {
        jsonResponse(false, 'Failed to save invitation: ' . $stmt->error);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $id = intval($data['id']);
    $stmt = $conn->prepare("DELETE FROM invitations WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        jsonResponse(true, 'Invitation deleted successfully');
    } else {
        jsonResponse(false, 'Failed to delete invitation');
    }
}
?>