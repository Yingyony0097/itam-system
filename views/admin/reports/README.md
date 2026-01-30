# Phase 3H: Reporting System

## Overview
Complete reporting and export system for the ITAM System with PDF and Excel export capabilities.

## Features Implemented

### 1. Report Types (5 Types)
- **Asset Inventory Report**: Complete list of all assets with full details
- **Check-In/Check-Out History Report**: All transaction logs with timestamps
- **User Assignment Report**: Assets assigned to each user with value totals
- **Asset by Category Report**: Grouped by category with statistics and percentages
- **Asset Valuation Report**: Financial breakdown by status and category

### 2. Filtering System
- **Status Filter**: Available, In Use, Maintenance, Retired
- **Category Filter**: All asset categories from database
- **Date Range Filter**: From/To dates for transaction logs
- **User Filter**: Specific user's assets and activities
- **Action Type Filter**: Check Out / Check In (for history reports)

### 3. Export Capabilities
- **PDF Export** (via TCPDF):
  - Professional P-line Company branding
  - Header with company name and report title
  - Footer with page numbers and generation date
  - Applied filters summary
  - Formatted tables with color coding

- **Excel Export** (via PhpSpreadsheet):
  - Auto-width columns
  - Frozen header rows
  - Header styling (bold, blue background #2563EB)
  - Auto-filters on data tables
  - Currency formatting for prices
  - Filter summary at top

### 4. Report Preview
- Real-time data preview before export
- Statistics cards with key metrics
- Interactive table views
- Responsive design

### 5. Security
- Admin-only access control
- CSRF protection on forms
- Input sanitization on all filters
- Prepared statements for database queries

## Installation

### 1. Install Dependencies
```bash
composer install
```

This will install:
- `tecnickcom/tcpdf`: ^6.6 (PDF generation)
- `phpoffice/phpspreadsheet`: ^1.29 (Excel generation)

### 2. Access the Reports
Navigate to: `/controllers/ReportController.php` or use the "Reports" menu item in the admin sidebar.

## File Structure
```
controllers/
  └── ReportController.php           # Main controller with all report logic

views/admin/reports/
  ├── index.php                      # Main reports dashboard
  ├── preview_asset_inventory.php    # Asset inventory table
  ├── preview_checkin_checkout.php   # Check-in/out history table
  ├── preview_user_assignment.php    # User assignments table
  ├── preview_asset_by_category.php  # Category breakdown table
  └── preview_asset_valuation.php    # Valuation summary

public/assets/css/
  └── custom.css                     # Print media queries added
```

## Usage

### 1. Select Report Type
Click on one of the 5 report cards to select the type of report you want to generate.

### 2. Apply Filters
Choose relevant filters based on the report type:
- Asset reports: Status, Category
- History reports: Date Range, User, Action Type
- Assignment reports: User, Category

### 3. Generate Report
Click "Generate Report" to preview the data with applied filters.

### 4. Export
Choose your preferred export format:
- **Export PDF**: Professional printable report
- **Export Excel**: Editable spreadsheet with formulas

## Report Statistics

Each report type shows relevant statistics:
- **Asset Inventory**: Total Records, Total Value, Available, In Use
- **Check-In/Out History**: Total Records, Check Outs, Check Ins, Unique Assets/Users
- **User Assignments**: Total Users, Total Assets, Total Value
- **Asset by Category**: Total Categories, Total Assets, Total Value
- **Asset Valuation**: Total Assets, Total Value, Total Categories

## Print Optimization

The system includes print-specific CSS (@media print) that:
- Hides navigation, buttons, and non-essential UI
- Removes glassmorphism effects
- Optimizes table printing
- Shows page breaks appropriately
- Ensures backgrounds print correctly

## Requirements Met

### Phase 3H Requirements
- ✅ REQ-REPORT-001: Report dashboard with type selection
- ✅ REQ-REPORT-002: Asset Inventory Report
- ✅ REQ-REPORT-003: Check-In/Check-Out History Report
- ✅ REQ-REPORT-004: User Assignment Report
- ✅ REQ-REPORT-005: Asset by Category Report
- ✅ REQ-REPORT-006: Asset Valuation Report
- ✅ REQ-REPORT-007: PDF export with TCPDF
- ✅ REQ-REPORT-008: Professional PDF formatting
- ✅ REQ-REPORT-009: Excel export with PhpSpreadsheet
- ✅ REQ-REPORT-010: Excel formatting with auto-width and filters

### Security Requirements
- ✅ REQ-SEC-003: PDO Prepared Statements
- ✅ REQ-SEC-004: XSS Prevention
- ✅ REQ-SEC-006: Input Sanitization
- ✅ REQ-SEC-007: CSRF Protection (on forms)
- ✅ REQ-SEC-008: Session-based access control

## Notes
- Reports are generated on-demand (not cached)
- Large datasets may take a few seconds to export
- Excel files support up to 1 million rows
- PDF files are optimized for A4 paper
- All currency values are formatted in USD ($) by default

## Future Enhancements (Optional)
- Report scheduling and email delivery
- Custom report builder
- Chart visualizations (Chart.js integration)
- Report templates system
- Export to CSV format
- Multi-currency support
