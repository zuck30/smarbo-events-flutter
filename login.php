<?php
require_once 'includes/config.php';

// Redirect if already logged in and session is valid
if (isLoggedIn() && isset($_SESSION['role'])) {
    $role = getUserRole();
    if ($role === 'admin' || $role === 'event_owner') {
        $redirect = $role === 'admin' ? 'admin/dashboard.php' : 'owner/dashboard.php';
        redirect($redirect);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SmarboPlusEvent</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #E6521F;
            --primary-dark: #c44118;
            --primary-light: rgba(230, 82, 31, 0.1);
            --primary-glow: rgba(230, 82, 31, 0.15);
            --dark: #0a0a14;
            --dark-light: #151522;
            --gray-900: #1a1a2e;
            --gray-800: #2d2d44;
            --gray-700: #444461;
            --gray-600: #666687;
            --gray-500: #8f8fb2;
            --gray-400: #b8b8d9;
            --gray-300: #e0e0f0;
            --gray-200: #f0f0fa;
            --gray-100: #f8f8ff;
            --white: #ffffff;
            --glass-bg: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
            --glass-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.04);
            --shadow-md: 0 8px 32px rgba(230, 82, 31, 0.15);
            --shadow-lg: 0 16px 48px rgba(230, 82, 31, 0.18);
            --transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            --radius-md: 20px;
            --radius-lg: 32px;
            --radius-xl: 48px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, var(--dark) 0%, var(--dark-light) 100%);
            color: var(--white);
            line-height: 1.6;
            font-size: 16px;
            font-weight: 400;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            overflow-x: hidden;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            top: -50%;
            right: -20%;
            width: 80%;
            height: 100%;
            background: radial-gradient(circle, var(--primary-glow) 0%, transparent 70%);
            z-index: -1;
        }

        .login-container {
            width: 100%;
            max-width: 480px;
            margin: 0 auto;
        }

        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 3rem;
            text-decoration: none;
        }

        .logo-img {
            height: 50px;
            width: auto;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
        }

        .logo-text {
            font-size: 2rem;
            font-weight: 900;
            color: white;
        }

        .logo-text span {
            color: var(--primary);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--radius-lg);
            padding: 3rem;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .glass-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), #ff8a5c);
        }

        .glass-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary);
        }

        .form-title {
            font-size: 2.2rem;
            font-weight: 800;
            margin-bottom: 2rem;
            color: white;
            text-align: center;
        }

        .form-group {
            margin-bottom: 1.8rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.6rem;
            font-weight: 500;
            color: var(--gray-300);
            font-size: 0.95rem;
        }

        .form-control {
            width: 100%;
            padding: 1rem 1.2rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--radius-md);
            color: white;
            font-size: 1rem;
            font-family: 'Inter', sans-serif;
            transition: var(--transition);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 0 0 3px rgba(230, 82, 31, 0.1);
        }

        .form-control::placeholder {
            color: var(--gray-500);
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--gray-300);
            font-size: 0.9rem;
            cursor: pointer;
        }

        .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--primary);
            cursor: pointer;
        }

        .forgot-password {
            color: var(--primary);
            text-decoration: none;
            font-size: 0.9rem;
            transition: var(--transition);
        }

        .forgot-password:hover {
            color: white;
            text-decoration: underline;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 1rem 2.2rem;
            border-radius: 50px;
            font-size: 1.05rem;
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition);
            cursor: pointer;
            white-space: nowrap;
            gap: 10px;
            position: relative;
            overflow: hidden;
            z-index: 1;
            border: none;
            width: 100%;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: 0.5s;
            z-index: -1;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 20px rgba(230, 82, 31, 0.3);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }

        .form-footer {
            margin-top: 2rem;
            text-align: center;
            color: var(--gray-400);
            font-size: 0.95rem;
        }

        .form-footer a {
            color: var(--primary);
            text-decoration: none;
            transition: var(--transition);
        }

        .form-footer a:hover {
            color: white;
            text-decoration: underline;
        }

        .alert {
            padding: 1rem 1.2rem;
            border-radius: var(--radius-md);
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
            display: none;
        }

        .alert-success {
            background: rgba(46, 204, 113, 0.1);
            border: 1px solid rgba(46, 204, 113, 0.2);
            color: #2ecc71;
        }

        .alert-error {
            background: rgba(231, 76, 60, 0.1);
            border: 1px solid rgba(231, 76, 60, 0.2);
            color: #e74c3c;
        }

        .back-home {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--gray-400);
            text-decoration: none;
            margin-top: 1rem;
            font-size: 0.9rem;
            transition: var(--transition);
        }

        .back-home:hover {
            color: white;
            gap: 10px;
        }

        .session-info {
            text-align: center;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: var(--radius-md);
        }

        .session-info p {
            margin-bottom: 0.5rem;
        }

        .session-info a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }

        @media (max-width: 576px) {
            .glass-card {
                padding: 2rem;
            }
            
            .form-title {
                font-size: 1.8rem;
            }
            
            .login-container {
                padding: 0 16px;
            }
        }

        @media (max-width: 400px) {
            .glass-card {
                padding: 1.5rem;
            }
            
            .remember-forgot {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            
            .form-title {
                font-size: 1.6rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <img src="./images/INVTS.png" alt="SmarboPlusEvent" class="logo-img">
        </div>
        
        <div class="glass-card">
            <h2 class="form-title">Welcome Back</h2>
            
            <div id="alert" class="alert"></div>

            <?php if (isLoggedIn()): ?>
            <div class="session-info">
                <p>You are currently logged in as <?php echo htmlspecialchars($_SESSION['username'] ?? 'Unknown'); ?>.</p>
                <p>Not you? <a href="api/logout.php">Logout</a></p>
            </div>
            <?php endif; ?>
            
            <form id="loginForm">
                <div class="form-group">
                    <label for="username">Username or Email</label>
                    <input type="text" id="username" class="form-control" placeholder="Enter username or email" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" class="form-control" placeholder="Enter your password" required>
                </div>
                
                <div class="remember-forgot">
                    <label class="remember-me">
                        <input type="checkbox" id="remember">
                        Remember me
                    </label>
                    <a href="#" class="forgot-password">Forgot password?</a>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </form>
            
            <div class="form-footer">
                <p>Don't have an account? <a href="register.php">Register here</a></p>
                <a href="index.php" class="back-home">
                    <i class="fas fa-arrow-left"></i> Back to Home
                </a>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            
            try {
                const response = await fetch('api/login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ username, password })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showAlert('Login successful! Redirecting...', 'success');
                    setTimeout(() => {
                        window.location.href = data.data.redirect;
                    }, 1500);
                } else {
                    showAlert(data.message, 'error');
                }
            } catch (error) {
                showAlert('An error occurred. Please try again.', 'error');
            }
        });
        
        function showAlert(message, type) {
            const alert = document.getElementById('alert');
            alert.textContent = message;
            alert.className = `alert alert-${type}`;
            alert.style.display = 'block';
            
            setTimeout(() => {
                alert.style.display = 'none';
            }, 5000);
        }
    </script>
</body>
</html>