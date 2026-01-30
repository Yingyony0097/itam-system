# ITAM System - Installation Guide
## P-line Company IT Asset Management System v1.0

---

## 📋 Prerequisites

- **PHP:** 7.4 or higher
- **MySQL:** 5.7 or higher
- **Web Server:** Apache (XAMPP recommended)
- **Composer:** Latest version ([Download here](https://getcomposer.org/download/))

---

## 🚀 Installation Steps

### 1. Clone or Extract Project

```bash
# If using git
git clone <repository-url> c:\xampp\htdocs\itam-system

# Or extract the ZIP file to:
c:\xampp\htdocs\itam-system
```

### 2. Install Composer Dependencies

Open Command Prompt or Terminal and navigate to the project directory:

```bash
cd c:\xampp\htdocs\itam-system
composer install
```

This will install the required libraries:
- **TCPDF** (^6.6) - For PDF report generation
- **PhpSpreadsheet** (^1.29) - For Excel export functionality

### 3. Configure Database Connection

Edit `config/database.php` with your MySQL credentials:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'itam_system');
define('DB_USER', 'root');        // Your MySQL username
define('DB_PASS', '');            // Your MySQL password
define('DB_CHARSET', 'utf8mb4');
```

### 4. Import Database Schema

```bash
# Using MySQL command line
mysql -u root -p itam_system < sql/schema.sql

# Or use phpMyAdmin:
# 1. Create database: itam_system
# 2. Import sql/schema.sql
```

### 5. Set File Permissions

Ensure the following directories are writable:

```bash
# Windows (XAMPP)
# Right-click folder → Properties → Security → Edit → Add write permissions

# Linux/Mac
chmod -R 755 c:\xampp\htdocs\itam-system
chmod -R 777 public/assets/uploads
chmod -R 777 public/assets/temp
```

### 6. Start Apache and MySQL

- Open XAMPP Control Panel
- Start **Apache** module
- Start **MySQL** module

### 7. Access the Application

Open your browser and navigate to:

```
http://localhost/itam-system/
```

---

## 🔑 Demo Credentials

### Admin Account
- **Email:** admin@pline.com
- **Password:** Admin@123

### Regular User Account
- **Email:** user@pline.com
- **Password:** User@123

---

## 📦 Required Libraries (Auto-installed via Composer)

### TCPDF (PDF Generation)
- **Purpose:** Generate professional PDF reports with P-line branding
- **Version:** ^6.6
- **Usage:** Asset Inventory, Check-In/Out History, User Assignment, Category, and Valuation reports
- **Documentation:** [https://tcpdf.org/](https://tcpdf.org/)

### PhpSpreadsheet (Excel Export)
- **Purpose:** Export reports to Excel (.xlsx) format
- **Version:** ^1.29
- **Features:** Auto-width columns, frozen headers, currency formatting, professional styling
- **Documentation:** [https://phpspreadsheet.readthedocs.io/](https://phpspreadsheet.readthedocs.io/)

---

## 🛠 Troubleshooting

### Issue: "Composer not found"
**Solution:** Install Composer from [getcomposer.org](https://getcomposer.org/download/)

### Issue: "Class 'TCPDF' not found"
**Solution:** Run `composer install` in the project directory

### Issue: "Database connection failed"
**Solution:**
1. Verify MySQL is running
2. Check database credentials in `config/database.php`
3. Ensure `itam_system` database exists

### Issue: "Permission denied" when uploading files
**Solution:** Set write permissions on `public/assets/uploads/` directory

### Issue: Reports not generating
**Solution:**
1. Verify Composer dependencies are installed: `composer install`
2. Check PHP error log at `c:\xampp\php\logs\php_error_log`
3. Ensure PHP memory_limit is at least 256M in `php.ini`

---

## 📁 Directory Structure

```
itam-system/
├── config/              # Configuration files
├── controllers/         # MVC Controllers
├── models/              # Database Models
├── views/               # UI Views
│   ├── admin/          # Admin-only views
│   ├── user/           # User views
│   ├── auth/           # Login/Logout
│   └── layouts/        # Header, Footer, Sidebar
├── helpers/            # Helper functions
├── public/             # Public assets
│   └── assets/
│       ├── css/        # Stylesheets
│       ├── js/         # JavaScript
│       └── uploads/    # User uploads
├── sql/                # Database schema
├── vendor/             # Composer dependencies (auto-generated)
├── composer.json       # Composer config
└── composer.lock       # Dependency lock file
```

---

## 🔧 Development Mode

### Enable Error Reporting

Edit `config/config.php`:

```php
// Development mode
define('ENVIRONMENT', 'development');

if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}
```

### Git Workflow

```bash
# Switch to development branch
git checkout development

# Make changes and commit
git add .
git commit -m "Description of changes"

# Push to remote
git push origin development
```

---

## 📊 Features Overview

### ✅ Completed Features (Phase 3A-3H)

- **Authentication System:** Login/Logout with session management
- **Role-Based Access:** Admin and User roles with different permissions
- **Dashboard:** Statistics cards, recent activities, category charts
- **Asset Management:** Full CRUD operations with photo upload
- **Check-In/Check-Out:** Transaction-based tracking with history
- **User Management:** Create, edit, delete users with self-deletion prevention
- **Profile Management:** Update profile, change password with current password validation
- **Reporting System:** 5 report types with PDF and Excel export

### 📝 Report Types Available

1. **Asset Inventory Report** - Complete list of all assets with details
2. **Check-In/Check-Out History** - Tracking logs with timeline
3. **User Assignment Report** - Assets assigned to each user
4. **Asset by Category Report** - Grouped by category with counts
5. **Asset Valuation Report** - Financial summary with total values

---

## 🎨 Design System

- **UI Style:** Glassmorphism with gradient effects
- **Primary Colors:** Blue (#2563EB), Purple (#7C3AED)
- **Icons:** Lucide Icons (CDN)
- **CSS Framework:** Bootstrap 5
- **Responsive:** Mobile, Tablet, Desktop support

---

## 🔒 Security Features

- ✅ Password hashing with bcrypt (REQ-SEC-001)
- ✅ PDO prepared statements (REQ-SEC-003)
- ✅ XSS prevention with htmlspecialchars() (REQ-SEC-004)
- ✅ Input sanitization (REQ-SEC-006)
- ✅ CSRF protection (REQ-SEC-007)
- ✅ Current password validation (REQ-SEC-008)
- ✅ Session-based access control
- ✅ Role-based authorization

---

## 📞 Support

For technical support or questions:
- **Email:** support@pline.com
- **Phone:** +856 20 XXXX XXXX
- **Address:** P-line Company, Vientiane, Laos

---

**Installation Version:** 1.0
**Last Updated:** January 30, 2026
**Status:** Production Ready ✅
