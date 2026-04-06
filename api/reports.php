<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

// Prevent any output before the PDF is generated
ob_start();

$auth = new Auth();
if (!$auth->isLoggedIn()) {
    header("Location: ../login.php");
    exit();
}

$functions = new Functions();
$user = $auth->getCurrentUser();

$type = $_GET['type'] ?? 'admin';
$eventId = isset($_GET['event_id']) ? intval($_GET['event_id']) : null;

// Permission check
if ($type === 'admin') {
    if ($user['role'] !== 'admin') {
        die("Unauthorized access. Admin role required.");
    }
} elseif ($type === 'event' && $eventId) {
    if ($user['role'] !== 'admin') {
        // For event owners, check if they own the event
        $conn = getConnection();
        $stmt = $conn->prepare("SELECT id FROM events WHERE id = ? AND event_owner_id = ?");
        $stmt->bind_param("ii", $eventId, $user['id']);
        $stmt->execute();
        if ($stmt->get_result()->num_rows === 0) {
            die("Unauthorized access to this event report");
        }
    }
} else {
    die("Invalid report request");
}

try {
    $pdf = $functions->generatePDFReport($type, $eventId);
    $filename = ($type === 'admin' ? 'Admin_System_Report_' : 'Event_Report_') . date('Ymd_His') . '.pdf';

    // Clear any previous output (warnings, whitespace)
    if (ob_get_length()) ob_clean();

    $pdf->Output($filename, 'D');
} catch (Exception $e) {
    die("Error generating report: " . $e->getMessage());
}
?>