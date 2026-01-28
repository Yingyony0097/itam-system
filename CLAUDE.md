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
- **Naming Convention:** - Controllers: `PascalCase` (e.g., `AssetController`)
  - Models: `PascalCase` (e.g., `Asset`)
  - Folders/Files: `snake_case` or `kebab-case` for views/assets

## 🗄 Database Schema Summary
- **Tables:** `users`, `assets`, `check_logs`
- **Key Logic:** - Delete Asset -> Cascade Delete Check Logs
  - Delete User -> Prevent if assets are assigned

## 🚀 Current Progress (Phase 3: Development)
- [x] Phase 1: Requirements Gathering (Done)
- [x] Phase 2: Database Design & Project Structure (Done)
- [x]Phase 3A: Core configuration files (config/database.php, config/init.php)  (Done)
Phase 3B: Base models (Database.php, User.php, Asset.php, CheckLog.php) (Next)
Phase 3C: Authentication controller and login view
Phase 3D: Admin dashboard with statistics
Phase 3E: Asset CRUD operations
Phase 3F: Check-in/Check-out functionality
Phase 3G: Reports and exports

## 📂 Key Commands
- **Git Init:** `git init`
- **Git Commit:** `git commit -m "Commit message"`
- **Git Push:** `git push origin development`

