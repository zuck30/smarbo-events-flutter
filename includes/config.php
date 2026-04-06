<?php
session_start();

// Load Composer autoload
require_once __DIR__ . '/../vendor/autoload.php';

// Base URL configuration
// Adjust this to match your local development environment path
define('BASE_URL', '/');

// Database configuration
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'smarbo_event_db');

// Create connection
function getConnection() {
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }
        
        $conn->set_charset("utf8mb4");
        return $conn;
        
    } catch (Exception $e) {
        die("Database connection error: " . $e->getMessage());
    }
}

// Sanitize input
function sanitize($input, $conn = null) {
    if ($conn) {
        return $conn->real_escape_string(htmlspecialchars(trim($input)));
    }
    return htmlspecialchars(trim($input));
}

// Redirect function
function redirect($url) {
    header("Location: " . $url);
    exit();
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Get user role
function getUserRole() {
    return $_SESSION['role'] ?? null;
}

// Check if user has specific role
function hasRole($role) {
    return isLoggedIn() && getUserRole() === $role;
}

// JSON response helper
function jsonResponse($success, $message = '', $data = []) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit();
}
?>