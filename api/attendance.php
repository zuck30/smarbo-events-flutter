<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

$auth = new Auth();
$auth->requireLogin();

$conn = getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $eventId = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;
    
    if ($eventId > 0) {
        $stmt = $conn->prepare("SELECT * FROM attendance WHERE event_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $eventId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $attendance = [];
        while($row = $result->fetch_assoc()) {
            $attendance[] = $row;
        }
        
        jsonResponse(true, 'Attendance retrieved', $attendance);
    } else {
        jsonResponse(false, 'Event ID is required');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $eventId = intval($data['event_id']);
    $guestName = sanitize($data['guest_name'], $conn);
    $status = sanitize($data['status'] ?? 'pending', $conn);
    
    // Check if already exists
    $checkStmt = $conn->prepare("SELECT id FROM attendance WHERE event_id = ? AND guest_name = ?");
    $checkStmt->bind_param("is", $eventId, $guestName);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        // Update existing
        $row = $checkResult->fetch_assoc();
        $id = $row['id'];
        $stmt = $conn->prepare("UPDATE attendance SET status = ?, attended_at = NOW() WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        $message = 'Attendance updated successfully';
    } else {
        // Insert new
        $stmt = $conn->prepare("INSERT INTO attendance (event_id, guest_name, status) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $eventId, $guestName, $status);
        $message = 'Attendance recorded successfully';
    }
    
    if ($stmt->execute()) {
        jsonResponse(true, $message);
    } else {
        jsonResponse(false, 'Failed to record attendance: ' . $stmt->error);
    }
}
?>