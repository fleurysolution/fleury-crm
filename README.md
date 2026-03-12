# BPMS247 - User Workflow Manual

Welcome to the comprehensive workflow guide for the BPMS247 construction management suite. This document outlines standard administrative and field procedures for the next-generation project management platform.

## 1. Enterprise Hierarchy Setup

Before managing projects, you must define your organization's geographic and operational structure.

### 1.1 Regions & Offices
1.  **Define Regions**: Navigate to **Settings** -> **Branch Structure** -> **Regions**. Create high-level areas (e.g., Northwest Division).
2.  **Establish Branches**: Go to **Settings** -> **Branch Structure** -> **Offices**. Link physical office locations to your regions.
3.  **Divisions**: (Optional) For specialized teams, use the **Divisions** setting to segment branches further.

### 1.2 User Onboarding
1.  Go to **Management** -> **Users**.
2.  Click **Create User**.
3.  **Branch Assignment**: Select the primary office the user belongs to. This enforces data isolation.
4.  **Role Assignment**:
    *   **Project Manager**: Full operational control over assigned projects.
    *   **Field Worker**: Restricted to time logging, site diaries, and task updates.
    *   **Client**: View-only access to specific project progress and financials.

## 2. Project Management & Advanced Scheduling

BPMS247 uses a Primavera P6-style management engine to "Drive the Project" using the Critical Path Method (CPM).

### 2.1 Project Setup
1.  Navigate to **Projects** -> **Create New**.
2.  Assign to a **Branch** and fill in basic project details (Budget, Timeline).
3.  Define **Phases** and **Milestones** to structure your project logic.

### 2.2 Advanced Scheduling (CPM)
1.  **XER/XML Import**: Upload schedules directly from Primavera P6 via the **Gantt** tab.
2.  **Logic Linking**: Define Task dependencies (Finish-to-Start, etc.) and add **Lag/Lead** times.
3.  **CPM Recalculation**: Use the "Recalculate Schedule" button to update the critical path after any change in duration or logic.
4.  **Baselines**: Capture snapshots of your schedule to track variance against the original plan.

## 3. Financial Workflows

### 3.1 Advanced Estimating
1.  **Multi-Tab Estimates**: Manage your detailed breakdown, Risks, and Clarifications in a single interface.
2.  **General Conditions (GCs)**: Input staff and site overhead costs separately to ensure accurate project pricing.
3.  **Risk Analysis**: Add contingency and high-risk factors to your budget summary.

### 3.2 Invoicing & Pay Apps
1.  **AIA Payment Applications**: Generate G702/G703 style progress billings directly from your BOQ/SOV.
2.  **Change Order Integration**: Approved change orders are automatically integrated into the revised budget and payment applications.

## 4. Intelligent Preconstruction & Procurement

Surpass standard task management by linking procurement directly to your project timeline.

### 4.1 Procurement-Driven Scheduling
1.  **Material Tracking**: Register long-lead items in the **Procurement** tab.
2.  **Schedule Sync**: Link material `On-Site` dates to project tasks. If delivery is delayed, the CPM engine will automatically shift the project schedule.

### 4.2 Automated Bid Leveling
1.  **Bid Matrix**: Compare subcontractor quotes side-by-side against your target budget.
2.  **Scope Gap Analysis**: The system automatically flags "Missing" or "Outlier" line items to ensure bid completeness.

## 5. "First-Time Quality" & Smart Handover

Enforce quality from day one and deliver a superior digital experience to the client.

### 5.1 Preventative Quality Control (QC)
1.  **Integrated Checklists**: PMs define mandatory QA checks for critical task categories.
2.  **Verification**: Field teams must complete these checklists (often requiring photo proof) before a task can be marked "Done."

### 5.2 Smart Punch List
1.  **Mobile-First Capture**: Record defects in real-time using a mobile browser.
2.  **Geotagging**: Every punch item automatically pins its precise GPS location.
3.  **Photo Evidence**: Attach "Before" and "After" photos for absolute accountability.

### 5.3 Digital Handover Vault
1.  **Asset Registry**: Track every major piece of equipment (AHUs, Pumps, Switchgear) in the vault.
2.  **Warranty Tracking**: Store warranty certificates and O&M manuals electronically.
3.  **QR Code Labels**: Generate and print unique labels for physical equipment. Scanning these in the field provides instant access to all asset documentation.

## 6. Field Production & Execution

### 6.1 Area Management & Drivers
1.  Divide projects into **Zones** or **Buildings** to track granular progress.
2.  Define **Quantity Drivers** (e.g., Linear Feet of Pipe, Cubic Yards of Concrete) to measure performance against baseline metrics.

### 6.2 Site Diaries & Progress Reports
1.  **Daily Logs**: Capture weather, manpower, and equipment details.
2.  **Geotagged Progress Photos**: Upload site photos with captions; these are automatically pulled into the **Executive Progress Report**.
3.  **PDF Distribution**: Generate professional, branded progress reports for stakeholders with one click.

## 7. Elite Operational & Accounting Control

BPMS247 provides a closed-loop system that links field progress directly to financial performance.

### 7.1 Real-Time Production Dashboard
1.  Navigate to **Projects** -> **[Select Project]** -> **Production & Control**.
2.  **Performance Metrics**:
    *   **CPI (Cost Performance Index)**: Measure cost efficiency against the budget.
    *   **SPI (Schedule Performance Index)**: Track schedule adherence based on earned value.
    *   **Labor Efficiency**: Compare actual labor hours against estimated production rates.
3.  **BOQ Production Tracking**: View granular progress for every line item in the Bill of Quantities.

### 7.2 Linking Field Work to Financials
1.  **Site Diary Entry**: When logging field work, select the relevant **BOQ Item**.
2.  **Quantity Reporting**: Enter the physical quantity completed.
3.  **Automated Updates**: Upon diary approval, the system automatically updates the project's actual quantities and financial metrics.

## 8. Enterprise Cybersecurity Hardening

Our multi-layered security suite ensures your project data remains protected against modern threats.

### 8.1 Identity & Access
1.  **Multi-Factor Authentication (MFA)**: Users are required to verify their identity via TOTP (e.g., Google Authenticator) for critical actions or logins.
2.  **Session Hardening**: The system uses session pinning and IP-bound verification to prevent unauthorized access.
3.  **Brute Force Protection**: Accounts are automatically locked for 15 minutes after 5 failed login attempts.

### 8.2 Security Auditing
1.  **Security Logs**: Administrators can view real-time security events in **Settings** -> **Cybersecurity**.
2.  **Audit Trail**: Every significant action within the system (data edits, project changes) is logged with a permanent timestamp and user ID.

## 9. Feature Management & Permissions

Customize your experience by enabling only the modules your organization requires.

### 9.1 Global Module Toggles
1.  Go to **Settings** -> **Modules**.
2.  Use the **Construction & Controls** toggles to enable or disable features like the P6 Scheduler or Production Control globally.

### 9.2 Granular Permissions
1.  **Role Configuration**: Define access at a granular level (e.g., allow a user to view Production metrics but not edit Schedules).
2.  **Inheritance**: Permissions can be assigned directly to users or inherited via roles.

***
_Visit the [Developer Manual](developer_manual.md) for technical integration and API details._
