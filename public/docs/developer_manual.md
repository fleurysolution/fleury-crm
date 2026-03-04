# BPMS247 - Developer Manual

This document provides technical documentation for the BPMS247 construction management platform, specifically outlining the architecture implemented in Phase 1 through 15.

## 1. Roles and Permissions (RBAC) Architecture

The application uses a database-driven Role-Based Access Control (RBAC) system.

### Core Tables:
*   `roles`: (id, name, slug) Defines the available system roles.
*   `permissions`: (id, name, slug) Defines specific granular actions (e.g., `view_financials`, `manage_users`).
*   `role_permissions`: (role_id, permission_id) Maps permissions to roles.
*   `user_roles`: (user_id, role_id) Maps users to their assigned roles.

### Key Roles:
1.  **admin**: Full system access, company-wide settings, user management, and financial overrides.
2.  **project_manager**: Can manage tasks, schedules, view project-level financials, and approve RFIs/Submittals.
3.  **field_worker**: Can log time, view drawings, submit daily logs (site diary), and update assigned task status.
4.  **client**: Restricted access. Can only view projects they are explicitly assigned to via `client_id`, view progress reports, and approve change orders/estimates.
5.  **subcontractor_vendor**: Sandboxed access. Directed exclusively to `/vendor-portal/dashboard` upon login. Can only view POs issued to them, Bids they originated, and Tasks directly assigned to them.

### Middleware/Filters:
*   `AuthFilter` (`auth` alias): Ensures the user is logged in. It also redirects `subcontractor_vendor` users attempting to access internal CRM URIs (like `/projects`) to their `/vendor-portal/dashboard`.
*   `PermissionFilter` (`permission:slug` alias): Used on specific routes to enforce granular permission checks.

## 2. Vendor Portal Routing Strategy

Vendors are kept separate from the internal CRM views to prevent accidental exposure of cross-project financial data.

*   **Public Sandbox**: Vendors register themselves at `GET /vendor/apply`. Internal PMs review applications at `/vendor-applications`.
*   **Routing Group**: All vendor views are grouped under `$routes->group('vendor-portal', ['filter' => 'auth'])`.
*   **Controller Isolation**: The `VendorPortal` controller is entirely separate from the `Dashboard` or `Projects` controllers. It explicitly filters data based on the logged-in user's `$userId` (`assigned_to == $userId` or `vendor_id == $userId`).

## 3. Financial Module Flow

*   **Estimates/Bids**: Handled individually or within projects. Subcontractors can submit bids, which PMs can convert into active project Estimates or accept directly.
*   **Purchase Orders (POs)**: Created internally and assigned to a specific Vendor. They sync to the Vendor Portal once the status is marked as 'Sent' or 'Executed'.
*   **Schedule of Values (SOV)**: The master contract value breakdown for the client. Used to generate Pay Applications.
*   **Invoicing**: Can be generated from Estimates or created independently.

## 4. Field Management

*   **Inspections / Punch Lists**: Logged from the field. Items must be assigned to an internal user or vendor to resolve.
*   **Site Diary / Daily Logs**: Used for tracking weather, workforce numbers, and daily activities.
*   **Progress Photos**: Uploaded to a project and compiled into a 'Progress Report' PDF for clients.

## 5. Core Entities and Databases

*   **Projects (`projects`)**: The center of the data model. (id, client_id, title, status).
*   **Users (`fs_users`)**: Unified authentication table for Employees, Clients, and Vendors. Uses `client_id` to link to a specific client company, or relies on `Roles` to differentiate a vendor from an internal user.
*   **Tasks (`tasks`)**: Work items. Can be synced with a Gantt chart. Assigned to users. Contains checklists and comments.

## 6. Directory Structure

*   `app/Controllers/`: Contains all routing logic.
*   `app/Models/`: CodeIgniter 4 Model classes representing the database tables.
*   `app/Views/`:
    *   `layouts/`: Contains `dashboard.php` (internal), `vendor_dashboard.php` (vendors), and `auth.php` (public).
    *   `vendor_portal/`: Dedicated views for the vendor UI.
    *   `projects/`, `finance/`, `field/`, etc: Component-based views.
*   `public/assets/`: Compiled CSS, JS, and vendor libraries (Bootstrap 5, FontAwesome).

***
_Generated internally by BPMS247 Documentation Engine_
