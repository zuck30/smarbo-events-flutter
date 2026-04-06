<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

$auth = new Auth();
$auth->requireLogin();

$conn = getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $postId = intval($data['post_id']);
    $comment = sanitize($data['comment'], $conn);
    $userId = $_SESSION['user_id'];
    
    $stmt = $conn->prepare("INSERT INTO event_post_comments (post_id, user_id, comment) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $postId, $userId, $comment);
    
    if ($stmt->execute()) {
        // Get the created comment with user info
        $commentId = $stmt->insert_id;
        $getStmt = $conn->prepare("
            SELECT c.*, u.full_name, u.avatar 
            FROM event_post_comments c
            JOIN users u ON c.user_id = u.id
            WHERE c.id = ?
        ");
        $getStmt->bind_param("i", $commentId);
        $getStmt->execute();
        $commentData = $getStmt->get_result()->fetch_assoc();
        
        jsonResponse(true, 'Comment posted', ['comment' => $commentData]);
    } else {
        jsonResponse(false, 'Failed to post comment');
    }
}
?>