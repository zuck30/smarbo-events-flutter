<?php
require_once 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unauthorized - SmarboPlusEvent</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .unauthorized-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            text-align: center;
            padding: 2rem;
        }
        
        .unauthorized-icon {
            font-size: 6rem;
            margin-bottom: 2rem;
            color: var(--primary-color);
        }
        
        .unauthorized-title {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .unauthorized-message {
            font-size: 1.2rem;
            opacity: 0.8;
            margin-bottom: 2rem;
            max-width: 600px;
        }
    </style>
</head>
<body>
    <div class="unauthorized-container">
        <div class="unauthorized-icon">🚫</div>
        <h1 class="unauthorized-title">Access Denied</h1>
        <p class="unauthorized-message">
            You don't have permission to access this page. Please contact the administrator if you believe this is an error.
        </p>
        <div>
            <a href="admin/dashboard.php" class="btn btn-primary">Go to Dashboard</a>
            <a href="index.php" class="btn btn-outline">Back to Home</a>
        </div>
    </div>
</body>
</html>