<?php
require_once '../../config/init.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect(isAdmin() ? 'views/admin/dashboard.php' : 'views/user/dashboard.php');
}

// Get flash message
$flash = getFlash();

// Get login errors
$errors = $_SESSION['login_errors'] ?? [];
unset($_SESSION['login_errors']);

// Get old email value
$oldEmail = $_SESSION['old_email'] ?? '';
unset($_SESSION['old_email']);
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo APP_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-dark: #4338ca;
            --glass-bg: rgba(255, 255, 255, 0.1);
            --glass-border: rgba(255, 255, 255, 0.2);
        }
        
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        /* Glassmorphism effect */
        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid var(--glass-border);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            padding: 2.5rem;
            width: 100%;
            max-width: 450px;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .login-header h1 {
            color: white;
            font-weight: 700;
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }
        
        .login-header p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.95rem;
        }
        
        .form-control, .form-select {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            color: #333;
        }
        
        .form-control:focus, .form-select:focus {
            background: rgba(255, 255, 255, 0.95);
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.25);
        }
        
        .form-label {
            color: white;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        
        .btn-login {
            background: var(--primary-color);
            border: none;
            border-radius: 10px;
            padding: 0.75rem;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(79, 70, 229, 0.4);
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        .footer-text {
            text-align: center;
            margin-top: 1.5rem;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="glass-card mx-auto">
            <div class="login-header">
                <h1><?php echo APP_NAME; ?></h1>
                <p>IT Asset Management System</p>
            </div>
            
            <!-- Flash Messages -->
            <?php if ($flash): ?>
                <div class="alert alert-<?php echo $flash['type'] === 'error' ? 'danger' : $flash['type']; ?> alert-dismissible fade show" role="alert">
                    <?php echo escape($flash['message']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <!-- Login Errors -->
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger" role="alert">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo escape($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <!-- Login Form -->
            <form action="<?php echo url('index.php'); ?>" method="POST">
                <!-- CSRF Token (REQ-SEC-007) -->
                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                
                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input 
                        type="email" 
                        class="form-control" 
                        id="email" 
                        name="email" 
                        placeholder="Enter your email"
                        value="<?php echo escape($oldEmail); ?>"
                        required
                        autofocus
                    >
                </div>
                
                <!-- Password -->
                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <input 
                        type="password" 
                        class="form-control" 
                        id="password" 
                        name="password" 
                        placeholder="Enter your password"
                        required
                    >
                </div>
                
                <!-- Submit Button -->
                <button type="submit" class="btn btn-login">
                    Login
                </button>
            </form>
            
            <!-- Footer -->
            <div class="footer-text">
                <p class="mb-0">&copy; <?php echo date('Y'); ?> P-line Company</p>
                <small>Version <?php echo APP_VERSION; ?></small>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>