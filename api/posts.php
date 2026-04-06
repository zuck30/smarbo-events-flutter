<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

$auth = new Auth();
$auth->requireLogin();

$functions = new Functions();
$conn = getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        $postId = intval($_GET['id']);
        
        $stmt = $conn->prepare("
            SELECT ep.*, e.event_name, u.full_name as posted_by_name, u.avatar as posted_by_avatar
            FROM event_posts ep
            JOIN events e ON ep.event_id = e.id
            JOIN users u ON ep.posted_by = u.id
            WHERE ep.id = ?
        ");
        $stmt->bind_param("i", $postId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $post = $result->fetch_assoc();
            $post['media'] = $functions->getPostMedia($postId);
            jsonResponse(true, 'Post retrieved', $post);
        } else {
            jsonResponse(false, 'Post not found');
        }
    } else {
        $eventId = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $perPage = isset($_GET['per_page']) ? intval($_GET['per_page']) : 10;
        
        if ($eventId > 0) {
            $postsData = $functions->getEventPosts($eventId, $page, $perPage);
            jsonResponse(true, 'Posts retrieved', $postsData);
        } else {
            jsonResponse(false, 'Event ID is required');
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventId = intval($_POST['event_id']);
    $title = sanitize($_POST['title'], $conn);
    $content = sanitize($_POST['content'], $conn);
    $postType = sanitize($_POST['post_type'], $conn);
    
    // Handle file uploads
    $media = [];
    if (!empty($_FILES['media'])) {
        $uploadDir = '../assets/uploads/posts/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        foreach ($_FILES['media']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['media']['error'][$key] === 0) {
                $fileName = uniqid() . '_' . basename($_FILES['media']['name'][$key]);
                $filePath = $uploadDir . $fileName;
                
                if (move_uploaded_file($tmp_name, $filePath)) {
                    $media[] = [
                        'url' => '/assets/uploads/posts/' . $fileName,
                        'caption' => ''
                    ];
                }
            }
        }
    }
    
    $result = $functions->createEventPost($eventId, $title, $content, $postType, $_SESSION['user_id'], $media);
    jsonResponse($result['success'], $result['message'], $result);
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $postId = intval($data['id']);
    
    $stmt = $conn->prepare("DELETE FROM event_posts WHERE id = ?");
    $stmt->bind_param("i", $postId);
    
    if ($stmt->execute()) {
        jsonResponse(true, 'Post deleted successfully');
    } else {
        jsonResponse(false, 'Failed to delete post');
    }
}
?>