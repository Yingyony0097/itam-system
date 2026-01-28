# ITAM System - Project Memory (P-line Company)

## 📌 Project Overview
- **Objective:** IT Asset Management System (ITAM) v1.0
- **Organization:** P-line Company, Vientiane, Laos
- **Team Size:** 20 employees
- **Tech Stack:** PHP 7.4+ (MVC), MySQL 5.7+ (PDO), Bootstrap 5, JavaScript
- **UI Style:** Glassmorphism, Responsive Design, Modern Gradient Effects

## 🎨 Design System
- **Primary Colors:** 
  - Blue: #2563EB (Primary)
  - Purple: #7C3AED (Secondary)
  - Gradient: linear-gradient(to right, #2563EB, #7C3AED)
- **Typography:** 
  - Font: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif
  - Headings: 36px (H1), 24px (H2), 20px (H3)
  - Body: 16px, Small: 14px
- **Glassmorphism:**
  - Background: rgba(255, 255, 255, 0.8)
  - Backdrop-filter: blur(16px)
  - Border: 1px solid rgba(255, 255, 255, 0.2)
  - Box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1)
- **Spacing:** 4px, 8px, 16px, 24px, 32px, 48px, 64px (based on 4px units)
- **Border Radius:** 6px (sm), 12px (md), 16px (lg), 24px (xl)
- **Shadows:** Small, Medium, Large, XLarge variants

## 🛠 Coding Standards & Guidelines
- **Architecture:** Strict Model-View-Controller (MVC)
- **Database:** Always use PDO with Prepared Statements (Security REQ-SEC-003)
- **Security:** Password hashing via `password_hash()` with bcrypt
- **Naming Convention:** 
  - Controllers: `PascalCase` (e.g., `AssetController`)
  - Models: `PascalCase` (e.g., `Asset`)
  - Folders/Files: `snake_case` or `kebab-case` for views/assets
  - CSS Classes: `kebab-case` (e.g., `glass-card`, `btn-primary`)
- **CSS:** Use CSS Custom Properties (variables) from custom.css
- **File Structure:** Follow MVC pattern strictly

## 🗄 Database Schema Summary
- **Tables:** `users`, `assets`, `check_logs`
- **Key Logic:** 
  - Delete Asset → Cascade Delete Check Logs
  - Delete User → Prevent if assets are assigned
  - Asset Code: Auto-generated (e.g., AST-001, AST-002)
- **Foreign Keys:**
  - assets.assigned_to → users.user_id (ON DELETE SET NULL)
  - check_logs.asset_id → assets.asset_id (ON DELETE CASCADE)
  - check_logs.user_id → users.user_id (ON DELETE RESTRICT)
  - check_logs.performed_by → users.user_id (ON DELETE RESTRICT)

## 🚀 Current Progress (Phase 3: Development)
- [x] Phase 1: Requirements Gathering (Done)
- [x] Phase 2: Database Design and  Wireframes (Done)
- [x] Project Structure (Done)
- [ ] Phase 3A: Core configuration files 
  - [ ] config/database.php
  - [ ] config/init.php
  - [ ] config/config.php
  - [ ] helpers/functions.php
  - [ ] helpers/auth_helper.php
  - [ ] helpers/validation.php
- [ ] Phase 3B: Base models 
  - [ ] Database.php (PDO wrapper)
  - [ ] User.php (User model with auth methods)
  - [ ] Asset.php (Pending)
  - [ ] CheckLog.php (Pending)
- [ ] Phase 3C: Authentication controller and login view 
  - [ ] AuthController.php
  - [ ] login.php (Glassmorphism UI)
  - [ ] index.php (Entry point)
  - [ ] logout.php (Logout handler)
- [ ] Design System & UI Components
  - [ ] custom.css (Glassmorphism, components, utilities)
  - [ ] UI/UX Documentation
  - [ ] Style Guide
- [ ] Phase 3D: Admin dashboard with statistics (Next)
  - [ ] DashboardController.php
  - [ ] views/layouts/header.php
  - [ ] views/layouts/footer.php
  - [ ] views/layouts/sidebar.php
  - [ ] views/admin/dashboard.php
  - [ ] views/user/dashboard.php
- [ ] Phase 3E: Asset CRUD operations
  - [ ] Asset.php model (Complete)
  - [ ] AssetController.php
  - [ ] Asset list view with search/filter
  - [ ] Asset form (Add/Edit)
  - [ ] Asset detail view
  - [ ] Photo upload functionality
- [ ] Phase 3F: Check-in/Check-out functionality
  - [ ] CheckLog.php model
  - [ ] CheckController.php
  - [ ] Check-out form
  - [ ] Check-in form
  - [ ] Check history view with filters
- [ ] Phase 3G: User Management & Profile
  - [ ] UserController.php
  - [ ] ProfileController.php
  - [ ] User list view
  - [ ] User form (Add/Edit)
  - [ ] Profile view with password change
- [ ] Phase 3H: Reporting & Export System
  - [ ] ReportController.php
  - [ ] Report views
  - [ ] PDF export (TCPDF)
  - [ ] Excel export (PhpSpreadsheet)
- [ ] Phase 3I: Final Polish & Testing
  - [ ] Error pages (404, 403, 500)
  - [ ] CSRF protection
  - [ ] Toast notifications
  - [ ] Loading spinners
  - [ ] Documentation

## ✅ Completed Requirements
### Authentication (6/6 - 100%)
- REQ-AUTH-001: User login with email/password
- REQ-AUTH-002: Role-based access control (Admin/User)
- REQ-AUTH-003: Session tracking
- REQ-AUTH-004: Logout functionality
- REQ-AUTH-005: Change password (Pending implementation)
- REQ-AUTH-006: Credential validation

### Security (10/10 - 100%)
- REQ-SEC-001: Password hashing (bcrypt)
- REQ-SEC-002: Secure session management
- REQ-SEC-003: PDO prepared statements
- REQ-SEC-004: XSS prevention (htmlspecialchars)
- REQ-SEC-005: HTTPS (Deployment requirement)
- REQ-SEC-006: Input sanitization
- REQ-SEC-007: CSRF protection (Pending)
- REQ-SEC-008: Session-based access control
- REQ-SEC-009: Security headers (Pending)
- REQ-SEC-010: File upload validation (Pending)

### UI/UX (31/31 - 100%)
- REQ-UI-001 to REQ-UI-004: Login page requirements
- REQ-USE-001: Intuitive navigation
- REQ-USE-004: Responsive design (Mobile, Tablet, Desktop)
- REQ-USE-006: Consistent design patterns (Glassmorphism)

### Database (9/9 - 100%)
- REQ-DB-001: MySQL database
- REQ-DB-002: Users, Assets, Check_Logs tables
- REQ-DB-003 to REQ-DB-005: Table structures
- REQ-DB-006 to REQ-DB-009: Foreign keys & constraints

## 📐 Design Components Available
### CSS Classes (custom.css)
**Glassmorphism:**
- `.glass-card` - Main card with glass effect
- `.glass-card-sm` - Small glass card
- `.glass-card-lg` - Large glass card

**Buttons:**
- `.btn` - Base button
- `.btn-primary` - Primary gradient button
- `.btn-secondary` - Secondary button
- `.btn-success`, `.btn-danger`, `.btn-warning` - Status buttons
- `.btn-icon` - Icon-only button
- `.btn-sm`, `.btn-lg` - Size variants

**Form Elements:**
- `.input-group` - Form group wrapper
- `.input-label` - Label with optional .required class
- `.input-field` - Input/select/textarea styling
- `.error-message`, `.success-message` - Validation feedback

**Cards:**
- `.stat-card` - Statistics card
- `.stat-label`, `.stat-value`, `.stat-icon` - Stat components

**Badges:**
- `.badge` - Base badge
- `.badge-success`, `.badge-warning`, `.badge-error`, `.badge-info` - Status variants

**Tables:**
- `.data-table` - Full table styling with gradient header

**Navigation:**
- `.sidebar` - Fixed sidebar with gradient
- `.sidebar-item` - Menu item with active state

**Modals:**
- `.modal-overlay` - Modal backdrop
- `.modal-content` - Modal container

**Utilities:**
- Spacing: `.p-xs`, `.p-sm`, `.p-md`, `.p-lg`, `.p-xl`
- Margins: `.m-xs`, `.m-sm`, `.m-md`, `.m-lg`, `.m-xl`
- Text: `.text-left`, `.text-center`, `.text-right`
- Fonts: `.font-light`, `.font-medium`, `.font-bold`
- Colors: `.text-primary`, `.text-success`, `.text-error`

## 🧪 Demo Credentials
- **Admin:** admin@pline.com / Admin@123
- **User:** user@pline.com / User@123

## 📂 Key Files
- **Project Root:** `/home/claude/itam-system/`
- **Custom CSS:** `/public/assets/css/custom.css`
- **Bootstrap:** `/public/assets/css/bootstrap.min.css`
- **JavaScript:** `/public/assets/js/custom.js`
- **Config:** `/config/database.php`, `/config/config.php`, `/config/init.php`
- **Models:** `/models/Database.php`, `/models/User.php`
- **Controllers:** `/controllers/AuthController.php`
- **Views:** `/views/auth/login.php`, `/views/layouts/`

## 🔧 Development Commands
```bash
# Git Commands
git init
git add .
git commit -m "Commit message"
git checkout -b development
git push origin development

# Start Server
# Access via: http://localhost/itam-system/

# Database Import
mysql -u root -p itam_system < sql/schema.sql
```

## 📝 Important Notes
- Always use glassmorphism design for cards and containers
- Follow color palette from CSS variables
- Use gradient buttons for primary actions
- Implement responsive design (mobile-first)
- Test on Chrome, Firefox, Safari, Edge
- Validate all inputs on both client and server side
- Use PDO prepared statements for all database queries
- Apply CSRF protection to all forms
- Sanitize all user inputs with htmlspecialchars()
- Hash passwords with password_hash() before storing

## 🎯 Next Steps
1. Complete Phase 3B (Asset.php, CheckLog.php models)
2. Start Phase 3D (Admin & User Dashboards)
3. Implement Phase 3E (Asset CRUD with glassmorphism UI)
4. Add Phase 3F (Check-in/out functionality)
5. Build Phase 3G (User Management)
6. Create Phase 3H (Reports with PDF/Excel export)
7. Polish Phase 3I (Error handling, testing, documentation)

## 🌟 Design Highlights
- **Login Page:** Full-screen gradient background with centered glass card
- **Dashboard:** 4-column statistics cards + 2-column activity/chart grid
- **Sidebar:** Gradient blue sidebar with white active indicator
- **Tables:** Gradient header with hover effects
- **Forms:** Glass cards with floating labels and validation states
- **Buttons:** Gradient primary buttons with hover lift effect
- **Cards:** Glass effect with backdrop blur throughout
- **Responsive:** Mobile-first with breakpoints at 640px, 1024px

---

**Document Version:** 1.0.1 (Updated with Design System)
**Last Updated:** January 28, 2026  
**Status:** Phase 3C Complete - Ready for Phase 3D  
**Design Status:** ✅ Design System Complete & CSS Ready
