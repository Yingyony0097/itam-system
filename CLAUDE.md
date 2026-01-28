# ITAM System - Project Memory (P-line Company)

## 📌 Project Overview
- **Objective:** IT Asset Management System (ITAM) v1.0
- **Organization:** P-line Company, Vientiane, Laos
- **Tech Stack:** PHP 7.4+ (MVC), MySQL 5.7+ (PDO), Bootstrap 5, JavaScript
- **UI Style:** Glassmorphism, Responsive Design

## 🛠 Coding Standards & Guidelines
- **Architecture:** Strict Model-View-Controller (MVC)
- **Database:** Always use PDO with Prepared Statements (Security REQ-SEC-003)
- **Security:** Password hashing via `password_hash()` with bcrypt
- **Naming Convention:** 
  - Controllers: `PascalCase` (e.g., `AssetController`)
  - Models: `PascalCase` (e.g., `Asset`)
  - Folders/Files: `snake_case` or `kebab-case` for views/assets

## 🗄 Database Schema Summary
- **Tables:** `users`, `assets`, `check_logs`
- **Key Logic:** 
  - Delete Asset → Cascade Delete Check Logs
  - Delete User → Prevent if assets are assigned

## 🚀 Current Progress (Phase 3: Development)
- [x] Phase 1: Requirements Gathering (Done)
- [x] Phase 2: Database Design & Project Structure (Done)
- [x] Phase 3A: Core configuration files (Done)
  - [x] config/database.php
  - [x] config/init.php
  - [x] config/config.php
- [x] Phase 3B: Base models (Partially Done)
  - [x] Database.php (PDO wrapper)
  - [x] User.php (User model with auth methods)
  - [ ] Asset.php (Pending)
  - [ ] CheckLog.php (Pending)
- [x] Phase 3C: Authentication controller and login view (Done)
  - [x] AuthController.php
  - [x] login.php (Glassmorphism UI)
  - [x] index.php (Entry point)
  - [x] logout.php (Logout handler)
- [ ] Phase 3D: Admin dashboard with statistics (Next)
  - [ ] Admin dashboard view
  - [ ] Statistics cards
  - [ ] Recent activities
  - [ ] Navigation menu
- [ ] Phase 3E: Asset CRUD operations
  - [ ] Asset.php model (Complete)
  - [ ] AssetController.php
  - [ ] Asset list view
  - [ ] Asset form (Add/Edit)
  - [ ] Asset delete functionality
- [ ] Phase 3F: Check-in/Check-out functionality
  - [ ] CheckLog.php model
  - [ ] CheckLogController.php
  - [ ] Check-out form
  - [ ] Check-in form
  - [ ] Check history view
- [ ] Phase 3G: Reports and exports
  - [ ] ReportController.php
  - [ ] Report views
  - [ ] PDF export
  - [ ] Excel export

## ✅ Completed Requirements
- REQ-AUTH-001: User login with email/password
- REQ-AUTH-003: Session tracking
- REQ-AUTH-004: Logout functionality
- REQ-AUTH-006: Credential validation
- REQ-SEC-001: Password hashing (bcrypt)
- REQ-SEC-002: Secure session management
- REQ-SEC-003: PDO prepared statements
- REQ-SEC-004: XSS prevention
- REQ-SEC-006: Input sanitization
- REQ-SEC-007: CSRF protection
- REQ-UI-001 to REQ-UI-004: Login page requirements

## 🧪 Demo Credentials
- **Admin:** admin@pline.com / password
- **User:** user@pline.com / password

## 📂 Key Commands
- **Git Init:** `git init`
- **Git Commit:** `git commit -m "Commit message"`
- **Git Push:** `git push origin development`
- **Start Server:** Access via `http://localhost/itam-system/`

## 📝 Notes
- Always test login functionality after each auth-related change
- Ensure database is imported before testing
- Use glassmorphism design for all UI components
- Follow MVC pattern strictly for maintainability