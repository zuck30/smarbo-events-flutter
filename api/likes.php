<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

$auth = new Auth();
$auth->requireLogin();

$conn = getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $postId = intval($data['post_id']);
    $userId = $_SESSION['user_id'];
    $action = $data['action']; // 'like' or 'unlike'
    
    if ($action === 'like') {
        $stmt = $conn->prepare("INSERT IGNORE INTO event_post_likes (post_id, user_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $postId, $userId);
        
        if ($stmt->execute()) {
            jsonResponse(true, 'Post liked');
        } else {
            jsonResponse(false, 'Failed to like post');
        }
    } else {
        $stmt = $conn->prepare("DELETE FROM event_post_likes WHERE post_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $postId, $userId);
        
        if ($stmt->execute()) {
            jsonResponse(true, 'Post unliked');
        } else {
            jsonResponse(false, 'Failed to unlike post');
        }
    }
}
?>