<?php
/**
 * 403 Forbidden Error Page
 * ITAM System - P-line Company
 *
 * Beautiful error page with Glassmorphism design
 * UI: REQ-UI-032 (Error Handling)
 * Security: REQ-SEC-008 (Access Control)
 */

// Prevent direct access
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(dirname(__DIR__)));
}

// Set page title
$page_title = "403 - Access Denied";
$is_error_page = true;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> - ITAM System</title>

    <!-- Bootstrap 5 CSS -->
    <link href="/public/assets/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="/public/assets/css/custom.css" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            overflow: hidden;
            position: relative;
        }

        /* Animated background particles */
        .bg-particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }

        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 15s infinite ease-in-out;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) translateX(0); opacity: 0.3; }
            50% { transform: translateY(-100px) translateX(100px); opacity: 0.6; }
        }

        .error-container {
            position: relative;
            z-index: 1;
            max-width: 600px;
            width: 90%;
            text-align: center;
        }

        .error-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 60px 40px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .error-code {
            font-size: 120px;
            font-weight: 800;
            color: white;
            margin: 0;
            line-height: 1;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            animation: pulse 2s infinite ease-in-out;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .error-icon {
            width: 100px;
            height: 100px;
            margin: 0 auto 30px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: shake 1.5s infinite;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        .error-title {
            font-size: 32px;
            font-weight: 700;
            color: white;
            margin: 20px 0 15px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .error-message {
            font-size: 18px;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 40px;
            line-height: 1.6;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: white;
            color: #f5576c;
            padding: 15px 40px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 30px rgba(0, 0, 0, 0.25);
            color: #f093fb;
        }

        .security-warning {
            margin-top: 30px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            border-left: 4px solid white;
        }

        .security-warning p {
            margin: 0;
            color: rgba(255, 255, 255, 0.95);
            font-size: 14px;
            line-height: 1.6;
        }

        .help-link {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: underline;
            transition: color 0.3s ease;
        }

        .help-link:hover {
            color: white;
        }
    </style>
</head>
<body>
    <!-- Background Particles -->
    <div class="bg-particles">
        <div class="particle" style="width: 80px; height: 80px; top: 10%; left: 10%; animation-delay: 0s;"></div>
        <div class="particle" style="width: 60px; height: 60px; top: 70%; left: 20%; animation-delay: 2s;"></div>
        <div class="particle" style="width: 100px; height: 100px; top: 20%; left: 80%; animation-delay: 4s;"></div>
        <div class="particle" style="width: 50px; height: 50px; top: 80%; left: 70%; animation-delay: 1s;"></div>
        <div class="particle" style="width: 70px; height: 70px; top: 40%; left: 50%; animation-delay: 3s;"></div>
    </div>

    <!-- Error Container -->
    <div class="error-container">
        <div class="error-card">
            <!-- Error Icon -->
            <div class="error-icon">
                <i data-lucide="shield-alert" style="width: 50px; height: 50px; color: white;"></i>
            </div>

            <!-- Error Code -->
            <h1 class="error-code">403</h1>

            <!-- Error Title -->
            <h2 class="error-title">Access Denied</h2>

            <!-- Error Message -->
            <p class="error-message">
                You don't have permission to access this page.<br>
                This area is restricted to administrators only.
            </p>

            <!-- Back Button -->
            <a href="/dashboard.php" class="btn-back">
                <i data-lucide="home" style="width: 20px; height: 20px;"></i>
                Back to Dashboard
            </a>

            <!-- Security Warning -->
            <div class="security-warning">
                <p>
                    <strong><i data-lucide="info" style="width: 16px; height: 16px; display: inline-block; margin-right: 5px;"></i> Security Notice:</strong><br>
                    This action has been logged for security purposes. If you believe you should have access to this resource, please contact your system administrator at
                    <a href="mailto:admin@pline.com" class="help-link">admin@pline.com</a>.
                </p>
            </div>
        </div>
    </div>

    <!-- Initialize Lucide Icons -->
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
