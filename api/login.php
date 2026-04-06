<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

$auth = new Auth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $username = sanitize($data['username'] ?? '');
    $password = $data['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        jsonResponse(false, 'Username and password are required');
    }
    
    $result = $auth->login($username, $password);
    
    if ($result['success']) {
        $redirectUrl = $result['role'] === 'admin' ? 'admin/dashboard.php' : 'owner/dashboard.php';
        $data = ['redirect' => $redirectUrl];
        jsonResponse(true, 'Login successful', $data);
    } else {
        jsonResponse(false, $result['message']);
    }
} else {
    jsonResponse(false, 'Invalid request method');
}
?>