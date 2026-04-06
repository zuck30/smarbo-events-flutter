<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

$auth = new Auth();
$auth->requireLogin();

$conn = getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $eventId = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;
    
    if ($eventId > 0) {
        $stmt = $conn->prepare("SELECT * FROM contributions WHERE event_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $eventId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $contributions = [];
        while($row = $result->fetch_assoc()) {
            $contributions[] = $row;
        }
        
        jsonResponse(true, 'Contributions retrieved', $contributions);
    } else {
        jsonResponse(false, 'Event ID is required');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $eventId = intval($data['event_id']);
    $contributorName = sanitize($data['contributor_name'], $conn);
    $phoneNumber = sanitize($data['phone_number'], $conn);
    $promisedAmount = floatval($data['promised_amount']);
    $paidAmount = floatval($data['paid_amount']);
    
    // Check if contribution exists (for update)
    if (isset($data['id']) && $data['id'] > 0) {
        $id = intval($data['id']);
        $stmt = $conn->prepare("UPDATE contributions SET contributor_name = ?, phone_number = ?, promised_amount = ?, paid_amount = ? WHERE id = ?");
        $stmt->bind_param("ssddi", $contributorName, $phoneNumber, $promisedAmount, $paidAmount, $id);
        $message = 'Contribution updated successfully';
    } else {
        $stmt = $conn->prepare("INSERT INTO contributions (event_id, contributor_name, phone_number, promised_amount, paid_amount) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issdd", $eventId, $contributorName, $phoneNumber, $promisedAmount, $paidAmount);
        $message = 'Contribution added successfully';
    }
    
    if ($stmt->execute()) {
        // Update status based on payment
        $contributionId = isset($data['id']) ? $data['id'] : $stmt->insert_id;
        $status = ($paidAmount >= $promisedAmount) ? 'approved' : 'pending';
        
        $updateStmt = $conn->prepare("UPDATE contributions SET status = ? WHERE id = ?");
        $updateStmt->bind_param("si", $status, $contributionId);
        $updateStmt->execute();
        
        jsonResponse(true, $message, ['id' => $contributionId]);
    } else {
        jsonResponse(false, 'Failed to save contribution: ' . $stmt->error);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $id = intval($data['id']);
    $stmt = $conn->prepare("DELETE FROM contributions WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        jsonResponse(true, 'Contribution deleted successfully');
    } else {
        jsonResponse(false, 'Failed to delete contribution');
    }
}
?>