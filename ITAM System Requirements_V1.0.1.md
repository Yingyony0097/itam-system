---
title: ITAM System Requirements_V1.0.1

---

# ITAM System  Project Requirements

**Project:** IT Asset Management System  
**Organization:** P-line Company   
**Location:** Vientiane, Laos  
**Team Size:** 20 employees  
**Version:** 1.0  
**Date:** January 2026

---

## Table of Contents

1. [Functional Requirements](#1-functional-requirements)
2. [Database Requirements](#2-database-requirements)
3. [Non-Functional Requirements](#3-non-functional-requirements)
4. [Technical Requirements](#4-technical-requirements)
5. [User Interface Requirements](#5-user-interface-requirements)
6. [Validation Requirements](#6-validation-requirements)
7. [Error Handling Requirements](#7-error-handling-requirements)
8. [Documentation Requirements](#8-documentation-requirements)
9. [Deployment Requirements](#9-deployment-requirements)
10. [Exclusions (Out of Scope)](#10-exclusions-out-of-scope)

---

## 1. FUNCTIONAL REQUIREMENTS

### 1.1 User Authentication & Authorization

| ID | Requirement |
|---|---|
| REQ-AUTH-001 | System must support user login with email and password |
| REQ-AUTH-002 | System must implement role-based access control (Admin, User) |
| REQ-AUTH-003 | System must track user sessions |
| REQ-AUTH-004 | System must support logout functionality |
| REQ-AUTH-005 | Users must be able to change their password |
| REQ-AUTH-006 | System must validate user credentials before granting access |

### 1.2 Asset Management (Admin Only)

| ID | Requirement |
|---|---|
| REQ-ASSET-001 | Admin must be able to create new assets with: Auto-generated Asset ID, Asset Name, Category, Serial Number, Brand, Model, Purchase Date, Purchase Price, Status (Available/In Use) |
| REQ-ASSET-002 | Admin must be able to view list of all assets |
| REQ-ASSET-003 | Admin must be able to edit existing asset information |
| REQ-ASSET-004 | Admin must be able to delete assets |
| REQ-ASSET-005 | When deleting an asset, all related check logs must be deleted (cascade delete) |
| REQ-ASSET-006 | Admin must be able to assign assets to users |
| REQ-ASSET-007 | Admin must be able to search assets by: Asset Name, Serial Number, Keyword |
| REQ-ASSET-008 | Admin must be able to filter assets by: Category, Status (Available/In Use) |
| REQ-ASSET-009 | Admin must be able to view detailed information for each asset |

### 1.3 Check-In/Check-Out Management

| ID | Requirement |
|---|---|
| REQ-CHECK-001 | Admin must be able to check out assets to users |
| REQ-CHECK-002 | System must only allow check-out of available assets |
| REQ-CHECK-003 | System must update asset status to "In Use" when checked out |
| REQ-CHECK-004 | System must create a check log entry for each check-out |
| REQ-CHECK-005 | Admin must be able to check in assets from users |
| REQ-CHECK-006 | System must update asset status to "Available" when checked in |
| REQ-CHECK-007 | System must remove user assignment when asset is checked in |
| REQ-CHECK-008 | System must create a check log entry for each check-in |
| REQ-CHECK-009 | System must support optional notes for check-in/check-out actions |
| REQ-CHECK-010 | System must record action date for each check-in/check-out |
| REQ-CHECK-011 | Admin must be able to view check-in/check-out history |
| REQ-CHECK-012 | System must support filtering check history by: Asset, User, Date Range |

### 1.4 Dashboard & Statistics

| ID | Requirement |
|---|---|
| REQ-DASH-001 | Admin dashboard must display: Total number of assets, Number of available assets, Number of assets in use, Total asset value, Recent activities |
| REQ-DASH-002 | User dashboard must display: List of assets assigned to the user, Asset details (Serial Number, Purchase Date, Assigned Date), User's check-in/check-out history |

### 1.5 Reporting

| ID | Requirement |
|---|---|
| REQ-REPORT-001 | System must generate "All Assets Report" showing: All assets grouped by category, All assets grouped by status |
| REQ-REPORT-002 | System must generate "User Assets Report" showing: Assets assigned to a specific user, Asset details |
| REQ-REPORT-003 | System must generate "Asset Value Report" showing: Total value by category, Total value by status, Depreciation information (if applicable) |
| REQ-REPORT-004 | System must generate "Activity Log Report" showing: Check-in/check-out history, Filtered by date range |
| REQ-REPORT-005 | All reports must support export to: PDF format, Excel format |
| REQ-REPORT-006 | All reports must support print functionality |

### 1.6 User Management (Admin Only)

| ID | Requirement |
|---|---|
| REQ-USER-001 | Admin must be able to create new users with: Name, Email, Password, Role (Admin/User) |
| REQ-USER-002 | Admin must be able to view list of all users |
| REQ-USER-003 | Admin must be able to edit user information |
| REQ-USER-004 | Admin must be able to deactivate users |
| REQ-USER-005 | Admin must be able to view assets assigned to each user |
| REQ-USER-006 | System must validate user email uniqueness |
| REQ-USER-007 | System must validate password strength |

### 1.7 User Profile Management

| ID | Requirement |
|---|---|
| REQ-PROFILE-001 | Users must be able to view their profile information: Name, Email, Role, Account Status |
| REQ-PROFILE-002 | Users must be able to change their password |
| REQ-PROFILE-003 | System must validate old password before allowing change |
| REQ-PROFILE-004 | System must validate new password confirmation |

---

## 2. DATABASE REQUIREMENTS

### 2.1 Database Schema

| ID | Requirement |
|---|---|
| REQ-DB-001 | System must use MySQL database |
| REQ-DB-002 | Database must have three main tables: Users, Assets, Check_Logs |

### 2.2 Users Table

| ID | Requirement |
|---|---|
| REQ-DB-003 | Users table must contain: user_id (Primary Key, Auto-increment), name (VARCHAR, NOT NULL), email (VARCHAR, UNIQUE, NOT NULL), password (VARCHAR, Hashed, NOT NULL), role (ENUM: 'Admin', 'User', NOT NULL), is_active (BOOLEAN, DEFAULT TRUE), created_at (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP), updated_at (TIMESTAMP, ON UPDATE CURRENT_TIMESTAMP) |

### 2.3 Assets Table

| ID | Requirement |
|---|---|
| REQ-DB-004 | Assets table must contain: asset_id (Primary Key, Auto-increment), asset_code (VARCHAR, UNIQUE, Auto-generated), asset_name (VARCHAR, NOT NULL), category (VARCHAR, NOT NULL), serial_number (VARCHAR, UNIQUE), brand (VARCHAR), model (VARCHAR), purchase_date (DATE), purchase_price (DECIMAL), status (ENUM: 'Available', 'In Use', DEFAULT 'Available'), assigned_to (Foreign Key → Users.user_id, NULL), assigned_date (DATE, NULL), photo_url (VARCHAR, NULL), created_at (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP), updated_at (TIMESTAMP, ON UPDATE CURRENT_TIMESTAMP) |

### 2.4 Check_Logs Table

| ID | Requirement |
|---|---|
| REQ-DB-005 | Check_Logs table must contain: log_id (Primary Key, Auto-increment), asset_id (Foreign Key → Assets.asset_id, NOT NULL), user_id (Foreign Key → Users.user_id, NOT NULL), action_type (ENUM: 'Check Out', 'Check In', NOT NULL), action_date (DATETIME, NOT NULL), notes (TEXT, NULL), performed_by (Foreign Key → Users.user_id, NOT NULL), created_at (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP) |

### 2.5 Data Integrity

| ID | Requirement |
|---|---|
| REQ-DB-006 | System must enforce foreign key constraints |
| REQ-DB-007 | System must cascade delete check logs when asset is deleted |
| REQ-DB-008 | System must prevent deletion of users who have assets assigned |
| REQ-DB-009 | System must maintain referential integrity across all tables |

---

## 3. NON-FUNCTIONAL REQUIREMENTS

### 3.1 Performance

| ID | Requirement |
|---|---|
| REQ-PERF-001 | System must load pages within 3 seconds |
| REQ-PERF-002 | Database queries must execute within 2 seconds |
| REQ-PERF-003 | System must support up to 100 concurrent users |
| REQ-PERF-004 | Report generation must complete within 10 seconds |

### 3.2 Security

| ID | Requirement |
|---|---|
| REQ-SEC-001 | All passwords must be hashed using password_hash() function with bcrypt |
| REQ-SEC-002 | System must implement PHP session management with secure session settings |
| REQ-SEC-003 | System must prevent SQL injection attacks using prepared statements (PDO/MySQLi) |
| REQ-SEC-004 | System must prevent XSS (Cross-Site Scripting) attacks using htmlspecialchars() |
| REQ-SEC-005 | System must implement HTTPS for all communications |
| REQ-SEC-006 | System must validate and sanitize all user inputs |
| REQ-SEC-007 | System must implement CSRF (Cross-Site Request Forgery) protection |
| REQ-SEC-008 | Unauthorized users must not access protected pages (session-based access control) |
| REQ-SEC-009 | System must set secure HTTP headers (X-Frame-Options, X-XSS-Protection) |
| REQ-SEC-010 | File upload functionality must validate file types and sizes |

### 3.3 Usability

| ID | Requirement |
|---|---|
| REQ-USE-001 | System must have intuitive navigation |
| REQ-USE-002 | System must provide clear error messages |
| REQ-USE-003 | System must provide success confirmations for all actions |
| REQ-USE-004 | System must be responsive (mobile, tablet, desktop) |
| REQ-USE-005 | System must support modern browsers (Chrome, Firefox, Safari, Edge) |
| REQ-USE-006 | UI must use consistent design patterns |
| REQ-USE-007 | Forms must include validation with clear feedback |

### 3.4 Reliability

| ID | Requirement |
|---|---|
| REQ-REL-001 | System must have 99% uptime |
| REQ-REL-002 | System must backup data daily |
| REQ-REL-003 | System must handle errors gracefully |
| REQ-REL-004 | System must log all critical errors |

### 3.5 Maintainability

| ID | Requirement |
|---|---|
| REQ-MAIN-001 | Code must follow consistent naming conventions |
| REQ-MAIN-002 | Code must be properly commented |
| REQ-MAIN-003 | System must use modular architecture |
| REQ-MAIN-004 | Database schema must be properly documented |

### 3.6 Scalability

| ID | Requirement |
|---|---|
| REQ-SCALE-001 | System architecture must support future feature additions |
| REQ-SCALE-002 | Database design must accommodate growth |
| REQ-SCALE-003 | API must be versioned for backward compatibility |

---

## 4. TECHNICAL REQUIREMENTS

### 4.1 Frontend

| ID | Requirement |
|---|---|
| REQ-TECH-001 | Frontend must be built with PHP for server-side rendering |
| REQ-TECH-002 | Styling must use Bootstrap CSS framework |
| REQ-TECH-003 | UI must implement glassmorphism design effects using custom CSS |
| REQ-TECH-004 | Frontend must use JavaScript for client-side interactions |
| REQ-TECH-005 | Components must be reusable through PHP includes/templates |
| REQ-TECH-006 | Forms must use Bootstrap validation and custom JavaScript validation |

### 4.2 Backend

| ID | Requirement |
|---|---|
| REQ-TECH-007 | Backend must be built with PHP (version 7.4 or higher) |
| REQ-TECH-008 | Database access must use PDO (PHP Data Objects) or MySQLi for prepared statements |
| REQ-TECH-009 | Backend must follow MVC (Model-View-Controller) architecture pattern |
| REQ-TECH-010 | Backend must implement RESTful API endpoints for AJAX operations |
| REQ-TECH-011 | Session management must use PHP native sessions |
| REQ-TECH-012 | Backend responses must be in JSON format for API calls |

### 4.3 Development Environment

| ID | Requirement |
|---|---|
| REQ-ENV-001 | Development must use VS Code with PHP extensions |
| REQ-ENV-002 | Version control must use Git |
| REQ-ENV-003 | Local development must use XAMPP or WAMP server |
| REQ-ENV-004 | Environment variables must be used for configuration (config files) |
| REQ-ENV-005 | Composer may be used for PHP dependency management (optional) |

### 4.4 Recommended Versions

| Technology | Recommended Version |
|---|---|
| PHP | 7.4 or higher (8.x recommended) |
| MySQL | 5.7 or higher (8.x recommended) |
| Bootstrap | 5.x |
| Apache | 2.4 or higher |
| jQuery | 3.6 or higher (for Bootstrap components) |

---

## 5. USER INTERFACE REQUIREMENTS

### 5.1 Login Page

| ID | Requirement |
|---|---|
| REQ-UI-001 | Must have email and password input fields |
| REQ-UI-002 | Must have login button |
| REQ-UI-003 | Must display error messages for invalid credentials |
| REQ-UI-004 | Must redirect to appropriate dashboard based on role |

### 5.2 Admin Dashboard

| ID | Requirement |
|---|---|
| REQ-UI-005 | Must display statistics cards for: Total Assets, Available Assets, Assets In Use, Total Value |
| REQ-UI-006 | Must display recent activities list |
| REQ-UI-007 | Must have navigation menu with: Dashboard, Assets, Check-in/Check-out, Reports, Users, Profile, Logout |

### 5.3 Asset List Page

| ID | Requirement |
|---|---|
| REQ-UI-008 | Must display assets in a table/grid format |
| REQ-UI-009 | Must have filter controls for category and status |
| REQ-UI-010 | Must have search box for keywords |
| REQ-UI-011 | Must have "Add New Asset" button |
| REQ-UI-012 | Each asset must have action buttons (Edit, Delete, Assign, View) |

### 5.4 Asset Form (Add/Edit)

| ID | Requirement |
|---|---|
| REQ-UI-013 | Must have input fields for all asset properties |
| REQ-UI-014 | Must display auto-generated Asset ID (add mode) |
| REQ-UI-015 | Must have validation indicators |
| REQ-UI-016 | Must have Save and Cancel buttons |
| REQ-UI-017 | Must pre-fill data in edit mode |

### 5.5 Check-In/Check-Out Forms

| ID | Requirement |
|---|---|
| REQ-UI-018 | Check-out form must have: Asset selector (available assets only), User selector, Action date picker, Notes textarea |
| REQ-UI-019 | Check-in form must have: Asset selector (in-use assets only), Action date picker, Notes textarea |

### 5.6 Reports Page

| ID | Requirement |
|---|---|
| REQ-UI-020 | Must have report type selector |
| REQ-UI-021 | Must display report preview |
| REQ-UI-022 | Must have export buttons (PDF, Excel, Print) |
| REQ-UI-023 | Must have filter controls for date range and other criteria |

### 5.7 User Management Page

| ID | Requirement |
|---|---|
| REQ-UI-024 | Must display users in a table format |
| REQ-UI-025 | Must have "Add User" button |
| REQ-UI-026 | Each user must have action buttons (Edit, Deactivate, View Assets) |

### 5.8 User Dashboard

| ID | Requirement |
|---|---|
| REQ-UI-027 | Must display list of assigned assets |
| REQ-UI-028 | Must show asset details for each assigned asset |
| REQ-UI-029 | Must display user's check history |

### 5.9 Profile Page

| ID | Requirement |
|---|---|
| REQ-UI-030 | Must display user information (read-only) |
| REQ-UI-031 | Must have "Change Password" section with: Old password field, New password field, Confirm password field, Update button |

---

## 6. VALIDATION REQUIREMENTS

### 6.1 Form Validation

| ID | Requirement |
|---|---|
| REQ-VAL-001 | All required fields must be validated before submission |
| REQ-VAL-002 | Email must be in valid format |
| REQ-VAL-003 | Serial numbers must be unique |
| REQ-VAL-004 | Dates must be in valid format |
| REQ-VAL-005 | Prices must be positive numbers |
| REQ-VAL-006 | Passwords must meet minimum requirements: Minimum 8 characters, At least one uppercase letter, At least one lowercase letter, At least one number |

### 6.2 Business Logic Validation

| ID | Requirement |
|---|---|
| REQ-VAL-007 | Cannot check out an asset that is already in use |
| REQ-VAL-008 | Cannot check in an asset that is not in use |
| REQ-VAL-009 | Cannot delete a user with assigned assets |
| REQ-VAL-010 | Cannot assign an asset that is already assigned |

---

## 7. ERROR HANDLING REQUIREMENTS

| ID | Requirement |
|---|---|
| REQ-ERR-001 | System must display user-friendly error messages |
| REQ-ERR-002 | System must log all errors to console/file |
| REQ-ERR-003 | System must handle network errors gracefully |
| REQ-ERR-004 | System must handle database connection errors |
| REQ-ERR-005 | System must provide recovery suggestions for errors |

---

## 8. DOCUMENTATION REQUIREMENTS

| ID | Requirement |
|---|---|
| REQ-DOC-001 | System must have user manual in Lao language |
| REQ-DOC-002 | System must have technical documentation |
| REQ-DOC-003 | API endpoints must be documented |
| REQ-DOC-004 | Database schema must be documented |
| REQ-DOC-005 | Installation guide must be provided |

---

## 9. DEPLOYMENT REQUIREMENTS

| ID | Requirement |
|---|---|
| REQ-DEP-001 | System must be deployable on standard PHP web hosting (Apache/Nginx with PHP support) |
| REQ-DEP-002 | System must support environment-based configuration through config files |
| REQ-DEP-003 | Database migrations must be version-controlled using SQL scripts |
| REQ-DEP-004 | System must have separate development and production environments |
| REQ-DEP-005 | Server must have PHP 7.4 or higher installed |
| REQ-DEP-006 | Server must have MySQL 5.7 or higher installed |
| REQ-DEP-007 | PHP extensions required: PDO, mysqli, mbstring, json |
| REQ-DEP-008 | Apache mod_rewrite must be enabled for clean URLs (optional) |

---

## 10. EXCLUSIONS (Out of Scope)

| ID | Exclusion |
|---|---|
| REQ-EXC-001 | Software license management |
| REQ-EXC-002 | Integration with external systems (ERP, HR, etc.) |
| REQ-EXC-003 | Automatic depreciation calculation |
| REQ-EXC-004 | Native mobile applications (iOS/Android) |
| REQ-EXC-005 | Maintenance and repair scheduling system |
| REQ-EXC-006 | QR code scanning functionality (initial version) |
| REQ-EXC-007 | Multi-language support (Lao only initially) |
| REQ-EXC-008 | Advanced analytics and forecasting features |
| REQ-EXC-009 | Warranty tracking and management |
| REQ-EXC-010 | Asset disposal and retirement workflows |

---

## Summary Statistics

| Category | Count |
|---|---|
| Functional Requirements | ~70 |
| Database Requirements | ~10 |
| Non-Functional Requirements | ~42 |
| Technical Requirements | ~18 |
| User Interface Requirements | ~30 |
| Validation Requirements | ~10 |
| Error Handling Requirements | ~5 |
| Documentation Requirements | ~5 |
| Deployment Requirements | ~8 |
| Exclusions | ~10 |
| **TOTAL REQUIREMENTS** | **~208** |

---

## Project Context

**Business Problem:** P-line Company lacks a systematic approach to track and manage IT assets, leading to:
- Asset losses and misplacement
- Inefficient manual Excel-based tracking
- No accountability for asset responsibility
- Difficulty in generating reports

**Solution:** A focused, practical ITAM system emphasizing simplicity and ease of use while efficiently meeting core asset tracking needs through:
- Database-driven web application
- Real-time asset tracking
- Role-based responsibility management
- Comprehensive reporting capabilities

**Success Criteria:**
- All assets tracked in centralized system
- Clear responsibility and accountability for each asset
- Ability to generate reports on demand
- Reduction in asset losses
- Improved operational efficiency

---

## Technology Stack

| Layer | Technology |
|---|---|
| Frontend | PHP + Bootstrap + JavaScript |
| Backend | PHP |
| Database | MySQL |
| ORM | PDO (PHP Data Objects) / MySQLi |
| Version Control | Git |
| IDE | VS Code |
| Design | Glassmorphism effects, Responsive design |

---

## Recommended Project Structure

```
itam-system/
├── assets/
│   ├── css/
│   │   ├── bootstrap.min.css
│   │   └── custom.css (glassmorphism styles)
│   ├── js/
│   │   ├── bootstrap.bundle.min.js
│   │   ├── jquery.min.js
│   │   └── custom.js
│   └── images/
├── config/
│   ├── database.php (DB connection)
│   └── config.php (app settings)
├── includes/
│   ├── header.php
│   ├── footer.php
│   ├── sidebar.php
│   └── functions.php
├── models/
│   ├── User.php
│   ├── Asset.php
│   └── CheckLog.php
├── controllers/
│   ├── AuthController.php
│   ├── AssetController.php
│   ├── UserController.php
│   └── ReportController.php
├── views/
│   ├── admin/
│   │   ├── dashboard.php
│   │   ├── assets/
│   │   ├── reports/
│   │   └── users/
│   ├── user/
│   │   ├── dashboard.php
│   │   └── profile.php
│   └── auth/
│       └── login.php
├── api/ (optional - for AJAX endpoints)
│   ├── assets.php
│   ├── users.php
│   └── check-logs.php
├── uploads/ (for asset photos)
├── reports/ (generated reports)
├── index.php
├── logout.php
└── .htaccess
```

---

## Project Phases

1. ✅ Requirements Gathering
2. ✅ Database Design
3. ✅ UI/UX Design & Wireframing
4. 🔄 Frontend Development with PHP + Bootstrap (In Progress)
5. 🔄 Backend Development with PHP (In Progress)
6. ⏳ Integration & Testing
7. ⏳ Deployment to PHP Hosting
8. ⏳ User Training & Documentation

---

**Document Version:** 1.0  
**Last Updated:** January 16, 2026  
**Prepared by:** Saimond  
**Status:** Approved for Development
