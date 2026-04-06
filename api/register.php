<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

$auth = new Auth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $username = sanitize($data['username'] ?? '');
    $email = sanitize($data['email'] ?? '');
    $password = $data['password'] ?? '';
    $confirmPassword = $data['confirmPassword'] ?? '';
    $fullName = sanitize($data['fullName'] ?? '');
    $phone = sanitize($data['phone'] ?? '');
    $role = sanitize($data['role'] ?? 'event_owner');
    
    // Validation
    if (empty($username) || empty($email) || empty($password) || empty($fullName)) {
        jsonResponse(false, 'All fields are required');
    }
    
    if ($password !== $confirmPassword) {
        jsonResponse(false, 'Passwords do not match');
    }
    
    if (strlen($password) < 6) {
        jsonResponse(false, 'Password must be at least 6 characters long');
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        jsonResponse(false, 'Invalid email format');
    }
    
    // Register user
    $result = $auth->register($username, $email, $password, $fullName, $phone, $role);
    jsonResponse($result['success'], $result['message'], $result);
} else {
    jsonResponse(false, 'Invalid request method');
}
?>