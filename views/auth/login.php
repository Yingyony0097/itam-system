<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Login - ITAM System</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="<?php echo BASE_URL; ?>/public/assets/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS with Glassmorphism -->
    <link href="<?php echo BASE_URL; ?>/public/assets/css/custom.css" rel="stylesheet">
    
    <style>
        /* Full-screen gradient background */
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        
        /* Login container */
        .login-container {
            width: 100%;
            max-width: 450px;
            padding: 20px;
        }
        
        /* Glass card for login form */
        .login-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 24px;
            padding: 48px 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }
        
        .login-card:hover {
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.4);
            transform: translateY(-5px);
        }
        
        /* Logo/Brand section */
        .brand-section {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .brand-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: linear-gradient(to right, #2563EB, #7C3AED);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            color: white;
            box-shadow: 0 8px 24px rgba(37, 99, 235, 0.4);
        }
        
        .brand-title {
            font-size: 32px;
            font-weight: 700;
            background: linear-gradient(to right, #2563EB, #7C3AED);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 8px;
        }
        
        .brand-subtitle {
            color: #6B7280;
            font-size: 16px;
            font-weight: 400;
        }
        
        .company-name {
            color: #9CA3AF;
            font-size: 14px;
            margin-top: 4px;
        }
        
        /* Form elements */
        .form-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .form-control {
            padding: 14px 18px;
            border: 1px solid #D1D5DB;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #2563EB;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
            outline: none;
        }
        
        /* Remember me checkbox */
        .form-check {
            margin: 20px 0;
        }
        
        .form-check-input:checked {
            background-color: #2563EB;
            border-color: #2563EB;
        }
        
        .form-check-label {
            color: #6B7280;
            font-size: 14px;
        }
        
        /* Submit button with gradient */
        .btn-login {
            width: 100%;
            padding: 14px 24px;
            background: linear-gradient(to right, #2563EB, #7C3AED);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }
        
        .btn-login:hover {
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.5);
            transform: translateY(-2px);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        /* Demo buttons */
        .demo-section {
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid #E5E7EB;
        }
        
        .demo-label {
            text-align: center;
            color: #9CA3AF;
            font-size: 12px;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .demo-buttons {
            display: flex;
            gap: 12px;
        }
        
        .btn-demo {
            flex: 1;
            padding: 10px 16px;
            border: none;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-demo-admin {
            background: #DBEAFE;
            color: #1E40AF;
        }
        
        .btn-demo-admin:hover {
            background: #BFDBFE;
        }
        
        .btn-demo-user {
            background: #D1FAE5;
            color: #065F46;
        }
        
        .btn-demo-user:hover {
            background: #A7F3D0;
        }
        
        /* Alert messages */
        .alert {
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 24px;
            border: none;
        }
        
        .alert-danger {
            background: #FEE2E2;
            color: #991B1B;
        }
        
        .alert-success {
            background: #D1FAE5;
            color: #065F46;
        }
        
        /* Footer */
        .login-footer {
            text-align: center;
            margin-top: 32px;
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
        }
        
        /* Responsive design */
        @media (max-width: 576px) {
            .login-card {
                padding: 32px 24px;
            }
            
            .brand-title {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Login Card -->
        <div class="login-card">
            <!-- Brand Section -->
            <div class="brand-section">
                <div class="brand-icon">
                    📦
                </div>
                <h1 class="brand-title">ITAM System</h1>
                <p class="brand-subtitle">IT Asset Management</p>
                <p class="company-name">P-line Company - Vientiane, Laos</p>
            </div>
            
            <!-- Flash Messages -->
            <?php if ($error_message = get_flash('error')): ?>
                <div class="alert alert-danger">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success_message = get_flash('success')): ?>
                <div class="alert alert-success">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            
            <!-- Login Form -->
            <form method="POST" action="<?php echo BASE_URL; ?>/index.php?action=authenticate" id="loginForm">
                <!-- CSRF Token (REQ-SEC-007) -->
                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                
                <!-- Email Field -->
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input 
                        type="email" 
                        class="form-control" 
                        id="email" 
                        name="email" 
                        placeholder="admin@pline.com"
                        required
                        autofocus
                    >
                </div>
                
                <!-- Password Field -->
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input 
                        type="password" 
                        class="form-control" 
                        id="password" 
                        name="password" 
                        placeholder="••••••••"
                        required
                    >
                </div>
                
                <!-- Remember Me -->
                <div class="form-check">
                    <input 
                        class="form-check-input" 
                        type="checkbox" 
                        id="remember" 
                        name="remember"
                    >
                    <label class="form-check-label" for="remember">
                        Remember me for 30 days
                    </label>
                </div>
                
                <!-- Submit Button -->
                <button type="submit" class="btn-login">
                    Sign In
                </button>
            </form>
            
            <!-- Demo Credentials Section -->
            <div class="demo-section">
                <div class="demo-label">Quick Demo Access</div>
                <div class="demo-buttons">
                    <button type="button" class="btn-demo btn-demo-admin" onclick="fillAdmin()">
                        Demo as Admin
                    </button>
                    <button type="button" class="btn-demo btn-demo-user" onclick="fillUser()">
                        Demo as User
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="login-footer">
            <p>&copy; <?php echo date('Y'); ?> P-line Company. All rights reserved.</p>
            <p style="font-size: 12px; margin-top: 8px; opacity: 0.8;">
                Version 1.0 | Vientiane, Laos
            </p>
        </div>
    </div>
    
    <!-- Bootstrap JS Bundle -->
    <script src="<?php echo BASE_URL; ?>/public/assets/js/bootstrap.bundle.min.js"></script>
    
    <!-- Demo Credentials Script -->
    <script>
        // Fill admin credentials
        function fillAdmin() {
            document.getElementById('email').value = 'admin@pline.com';
            document.getElementById('password').value = 'Admin@123';
            document.getElementById('email').focus();
        }
        
        // Fill user credentials
        function fillUser() {
            document.getElementById('email').value = 'user@pline.com';
            document.getElementById('password').value = 'User@123';
            document.getElementById('email').focus();
        }
        
        // Form validation
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            
            if (!email || !password) {
                e.preventDefault();
                alert('Please fill in all fields.');
                return false;
            }
            
            if (password.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters.');
                return false;
            }
        });
    </script>
</body>
</html>