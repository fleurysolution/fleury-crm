# Enterprise Construction ERP Platform Plan

The goal is to migrate the existing CRM architecture into a full-scale Enterprise Multi-Branch Construction ERP Platform per the provided Functional Requirements Specification (FRS). Given the massive scope, this plan focuses on establishing the core foundations first.

## User Review Required

> [!CAUTION]
> This is a monumental shift from a standard CRM to an Enterprise ERP. Implementing all 20 sections is a multi-month, potentially multi-year project for a team.
> We must tackle this iteratively. **I propose we start strictly with Phase 1 and Phase 2**: establishing the multi-tenant organizational structure and upgrading the user/authentication framework to support ABAC (Attribute-Based Access Control) and the required data enforcement rules. 
> 
> Does this phased approach sound good to you before I start writing database migrations and refactoring the core models?

## Proposed Changes

We will initially focus on Sections 2 & 3 of the FRS.

### 1. Database Migrations (Foundation)
- **[NEW] `migrations/YYYYMMDDHHMMSS_CreateOrgHierarchy.php`**: Will create tables for `tenants`, [regions](file:///c:/wamp64/www/staging/app/Controllers/BranchSettings.php#27-37), [divisions](file:///c:/wamp64/www/staging/app/Controllers/BranchSettings.php#50-61), `branches`, and `departments`. 
- **[NEW] `migrations/YYYYMMDDHHMMSS_ExpandUsersTable.php`**: Will add the required FRS fields to `fs_users` (`branch_id`, `department_id`, `reporting_manager_id`, `approval_authority_level`, `geo_access_permission`, `payroll_profile_id`, `tax_profile_id`, `employment_type`). 
- **[MODIFY] `migrations/...`**: We must prepare altering existing core tables (`clients`, [projects](file:///c:/wamp64/www/staging/app/Controllers/Settings.php#663-681), etc.) to include `tenant_id` and `branch_id`.

### 2. Application Core (Data Linkage Enforcement)
- **[NEW] `app/Models/ErpBaseModel.php`**: To support **Mandatory Data Linkage (Hard Rule)**. All operational models will extend this. It will automatically inject `tenant_id` and `branch_id` from the current session upon insert/update. It will also throw explicit exceptions (and log them) if `branch_id` is missing.
- **[MODIFY] `app/Models/UsersModel.php`** (or equivalents): Update to handle the new fields and scopes.

### 3. Identity and Access Control
- **[MODIFY] [app/Controllers/Users.php](file:///c:/wamp64/www/staging/app/Controllers/Users.php)**: Update forms and data storage logic to include organizational assignments and roles.
- **[NEW] [app/Filters/AbacFilter.php](file:///c:/wamp64/www/staging/app/Filters/AbacFilter.php)**: An Attribute-Based Access Control filter to ensure users can only view records corresponding to their `branch_id` or region scope, combining with their existing `role_id`.
- **[MODIFY] [app/Controllers/Auth.php](file:///c:/wamp64/www/staging/app/Controllers/Auth.php)**: (Or wherever login is handled) to cache the user's full organizational scope in their session context.

## Verification Plan
### Automated Tests
- We will write tests to explicitly ensure that any database insert into an operational table without a `branch_id` is rejected.
- We will test the `AbacFilter` by logging in as cross-branch users and verifying they receive "not found" or "forbidden" when accessing out-of-scope data.

### Manual Verification
- We will manually navigate the Users API/UI to verify that a super admin can create regions, branches, and assign users securely.

---

> [!NOTE]
> Below is the proposed plan for Phase 3, to be executed next. Phase 1 and 2 are complete.

## Phase 3: Workflow & Approval Engine

The existing `fs_as_approval_workflows` schema is a good start but lacks enterprise ERP features (routing amounts, branch specificity, delegations, escalations, immutable logs).

### 1. Database Migrations
- **[NEW] `migrations/..._EnhanceApprovalWorkflows.php`**: Add `branch_id` (for branch-specific routing), `min_amount` and `max_amount` (for amount-based routing).
- **[NEW] `migrations/..._EnhanceApprovalSteps.php`**: Add `escalation_role_id` and `escalation_user_id` to support SLA escalations.
- **[NEW] `migrations/..._CreateApprovalDelegations.php`**: Create `fs_approval_delegations` table (`delegator_user_id`, `delegatee_user_id`, `start_date`, `end_date`, `is_active`).
- **[NEW] `migrations/..._CreateApprovalLogs.php`**: Create a hardened `fs_approval_logs` table for immutable audit trails (`request_id`, `step_id`, [user_id](file:///c:/wamp64/www/staging/app/Models/FsUserModel.php#65-72), [action](file:///c:/wamp64/www/staging/app/Controllers/Inventory.php#81-127), [comment](file:///c:/wamp64/www/staging/app/Controllers/Tasks.php#179-206), `ip_address`, `timestamp`).

### 2. Application Core (Workflow Service)
- **[NEW] [app/Services/WorkflowEngine.php](file:///c:/wamp64/www/staging/app/Services/WorkflowEngine.php)**: A robust service handling:
  - Multi-variant workflow resolution (checking module, branch_id, and amount).
  - Parallel vs Sequential execution (using `step_no` grouping).
  - Delegated approvals (auto-routing to delegatees).
  - SLA Escalation checks (a method that can be triggered via Cron).
- **[MODIFY] `app/Models/FsApproval...`**: Update the core approval models (`FsAsApprovalWorkflowModel`, `FsAsApprovalWorkflowStepModel`, etc.) to map these new parameters.

### 3. Verification Plan
- We will write a CLI script `test_workflow_engine.php` simulating a Purchase Order creation, submitting it for approval, simulating a delegated user approving it, and verifying the immutable log.

---

> [!NOTE]
> Below is the proposed plan for Phase 4.

## Phase 4: Project Management (Execution & Commercial)

The existing [projects](file:///c:/wamp64/www/staging/app/Controllers/Settings.php#663-681) table is CRM-focused. We need to upgrade it to an ERP standard, introducing strict data linkage (`branch_id`, `tenant_id`) and commercial baselines, as well as adding Daily Construction Management tables.

### 1. Database Migrations
- **[NEW] `migrations/..._EnhanceProjectsTable.php`**:
  - Add `tenant_id` and `branch_id` to [projects](file:///c:/wamp64/www/staging/app/Controllers/Settings.php#663-681) for [ErpModel](file:///c:/wamp64/www/staging/app/Models/ErpModel.php#7-115) compatibility.
  - Add `contract_type` (e.g., 'lump_sum', 'cost_plus', 'unit_price').
  - Add `versioned_budget_baseline` (JSON or Decimal) to store the original approved budget vs current forecast.
  - Add `project_stage` (e.g., 'bidding', 'pre_construction', 'active', 'closeout').
- **[NEW] `migrations/..._CreateDailyLogs.php`**:
  - `fs_daily_logs`: Main record per project per day (`project_id`, [date](file:///c:/wamp64/www/staging/app/Controllers/SiteDiary.php#108-128), `weather_conditions`, `temperature`, `site_conditions`, [notes](file:///c:/wamp64/www/staging/app/Controllers/Leads.php#120-130), `created_by`, `approved_by`).
  - `fs_daily_manpower`: Linked to daily logs (`log_id`, `contractor_id` or `trade`, `worker_count`, `hours`, [notes](file:///c:/wamp64/www/staging/app/Controllers/Leads.php#120-130)).
  - `fs_daily_equipment`: Linked to daily logs (`log_id`, `equipment_type`, `hours_used`, [status](file:///c:/wamp64/www/staging/app/Controllers/Estimates.php#191-219)).

### 2. Application Core
- **[MODIFY] [app/Models/ProjectModel.php](file:///c:/wamp64/www/staging/app/Models/ProjectModel.php)**: Make it extend [ErpModel](file:///c:/wamp64/www/staging/app/Models/ErpModel.php#7-115) to enforce `branch_id` rules automatically. Update `$allowedFields`.
- **[NEW] [app/Models/DailyLogModel.php](file:///c:/wamp64/www/staging/app/Models/DailyLogModel.php)** (and related): Create models extending [ErpModel](file:///c:/wamp64/www/staging/app/Models/ErpModel.php#7-115).

### 3. Identity and Access Control
- **[MODIFY] [app/Controllers/Projects.php](file:///c:/wamp64/www/staging/app/Controllers/Projects.php)**: Update forms and viewing logic to support the new commercial fields and branch assignments.

### 4. Verification Plan
- Create a test project ensuring [ErpModel](file:///c:/wamp64/www/staging/app/Models/ErpModel.php#7-115) properly injects the creator's `branch_id`.
- Test the Daily Log API/Controller to ensure logs can be created and linked correctly.

---

> [!NOTE]
> Below is the proposed plan for Phase 5.

## Phase 5: Field Collaboration (RFIs, Submittals, Drawings)

This phase introduces dedicated modules for managing Requests for Information (RFIs), Submittals, and Construction Drawings. All these modules will enforce the strict [ErpModel](file:///c:/wamp64/www/staging/app/Models/ErpModel.php#7-115) data linkage to guarantee organizational boundaries.

### 1. Database Migrations
- **[NEW] `migrations/..._CreateFieldCollaborationTables.php`**:
  - `fs_rfis`: Core RFI record (`project_id`, `tenant_id`, `branch_id`, `rfi_number`, `title`, `question`, `proposed_solution`, [status](file:///c:/wamp64/www/staging/app/Controllers/Estimates.php#191-219), `due_date`, `assigned_to`).
  - `fs_rfi_replies`: Threaded replies to RFIs (`rfi_id`, [user_id](file:///c:/wamp64/www/staging/app/Models/FsUserModel.php#65-72), [reply](file:///c:/wamp64/www/staging/app/Controllers/RFIs.php#71-88), `is_official_answer`).
  - `fs_submittals`: Submittal tracking (`project_id`, `tenant_id`, `branch_id`, `spec_section`, `title`, `description`, [status](file:///c:/wamp64/www/staging/app/Controllers/Estimates.php#191-219), `due_date`, `assigned_to`, `revision`).
  - `fs_drawings`: Drawing version control (`project_id`, `tenant_id`, `branch_id`, `discipline`, `drawing_number`, `title`, `revision`, `file_path`, [status](file:///c:/wamp64/www/staging/app/Controllers/Estimates.php#191-219)).

### 2. Application Core
- **[NEW] [app/Models/RfiModel.php](file:///c:/wamp64/www/staging/app/Models/RfiModel.php), `RfiReplyModel.php`, [SubmittalModel.php](file:///c:/wamp64/www/staging/app/Models/SubmittalModel.php), [DrawingModel.php](file:///c:/wamp64/www/staging/app/Models/DrawingModel.php)**: All will extend [ErpModel](file:///c:/wamp64/www/staging/app/Models/ErpModel.php#7-115) to inherit strict `branch_id` checking.

### 3. Sub-controllers & Routes
- **[NEW] [app/Controllers/RFIs.php](file:///c:/wamp64/www/staging/app/Controllers/RFIs.php), [Submittals.php](file:///c:/wamp64/www/staging/app/Controllers/Submittals.php), [Drawings.php](file:///c:/wamp64/www/staging/app/Controllers/Drawings.php)**: Handles the business logic for each document type, associating them with the active project and current user.
- **[MODIFY] [app/Config/Routes.php](file:///c:/wamp64/www/staging/app/Config/Routes.php)**: Map endpoints `projects/(:num)/rfis`, `projects/(:num)/submittals`, `projects/(:num)/drawings`.

### 4. Verification Plan
- **Automated/CLI tests**: Create a test script verifying that RFIs and Submittals correctly inherit the creator's `branch_id` via [ErpModel](file:///c:/wamp64/www/staging/app/Models/ErpModel.php#7-115) and test querying documents by project.

---

> [!NOTE]
> Below is the proposed plan for Phase 6.

## Phase 6: QHSE (Quality, Health, Safety, Environment)

This phase introduces dedicated modules for Field Management, specifically focusing on Inspections, Punch Lists, and Safety Incidents. All these modules will enforce the strict [ErpModel](file:///c:/wamp64/www/staging/app/Models/ErpModel.php#7-115) data linkage to guarantee organizational boundaries.

### 1. Database Migrations
- **[NEW] `migrations/..._CreateQhseTables.php`**:
  - `fs_inspections`: Core inspection record (`project_id`, `tenant_id`, `branch_id`, `type`, [status](file:///c:/wamp64/www/staging/app/Controllers/Estimates.php#191-219), `inspector_id`, `inspection_date`, [notes](file:///c:/wamp64/www/staging/app/Controllers/Leads.php#120-130)).
  - `fs_inspection_items`: Individual checklist items within an inspection (`inspection_id`, `description`, [status](file:///c:/wamp64/www/staging/app/Controllers/Estimates.php#191-219), `remarks`).
  - `fs_punch_lists`: Extends the existing concept but formalized for ErpModel (`project_id`, `tenant_id`, `branch_id`, `location`, `description`, [status](file:///c:/wamp64/www/staging/app/Controllers/Estimates.php#191-219), `assigned_to`, `due_date`).
  - `fs_safety_incidents`: Tracking safety issues (`project_id`, `tenant_id`, `branch_id`, `incident_date`, `type`, `severity`, `description`, `reported_by`, [status](file:///c:/wamp64/www/staging/app/Controllers/Estimates.php#191-219)).

### 2. Application Core
- **[NEW] [app/Models/InspectionModel.php](file:///c:/wamp64/www/staging/app/Models/InspectionModel.php), `InspectionItemModel.php`, [SafetyIncidentModel.php](file:///c:/wamp64/www/staging/app/Models/SafetyIncidentModel.php)**: All will extend [ErpModel](file:///c:/wamp64/www/staging/app/Models/ErpModel.php#7-115) to inherit strict `branch_id` checking. (Note: `PunchListModel` exists but may need refactoring to extend [ErpModel](file:///c:/wamp64/www/staging/app/Models/ErpModel.php#7-115)).

### 3. Sub-controllers & Routes
- **[NEW] [app/Controllers/Inspections.php](file:///c:/wamp64/www/staging/app/Controllers/Inspections.php), [SafetyIncidents.php](file:///c:/wamp64/www/staging/app/Controllers/SafetyIncidents.php)**: Handles the business logic for QHSE elements, associating them with the active project and current user.
- **[MODIFY] [app/Config/Routes.php](file:///c:/wamp64/www/staging/app/Config/Routes.php)**: Map endpoints `projects/(:num)/inspections`, `projects/(:num)/safety`. (Punch list routes already exist but controllers will be updated).

### 4. Verification Plan
- **Automated/CLI tests**: Create a test script verifying that Inspections and Safety Incidents correctly inherit the creator's `branch_id` via [ErpModel](file:///c:/wamp64/www/staging/app/Models/ErpModel.php#7-115).

---

> [!NOTE]
> Below is the proposed plan for Phase 7.

## Phase 7: Procurement & Subcontractor Management

This phase focuses on upgrading the Procurement, Purchase Orders, and Bids modules to adhere to the enterprise ERP data isolation standards using [ErpModel](file:///c:/wamp64/www/staging/app/Models/ErpModel.php#7-115). It will ensure internal users only access Procurement data within their branch, while maintaining secure external access for Subcontractors via the Vendor Portal.

### 1. Database Migrations
- **[NEW] `migrations/..._AddErpFieldsToProcurement.php`**:
  - Add `tenant_id` and `branch_id` columns to `project_purchase_orders`.
  - Add `tenant_id` and `branch_id` columns to `project_bids`.

### 2. Application Core (Tenant Isolation)
- **[MODIFY] [app/Models/PurchaseOrderModel.php](file:///c:/wamp64/www/staging/app/Models/PurchaseOrderModel.php) & [BidModel.php](file:///c:/wamp64/www/staging/app/Models/BidModel.php)**:
  - Refactor classes to extend [ErpModel](file:///c:/wamp64/www/staging/app/Models/ErpModel.php#7-115) instead of [Model](file:///c:/wamp64/www/staging/app/Models/ErpModel.php#7-115).
  - Add `tenant_id` and `branch_id` to `$allowedFields`.

### 3. Controllers
- **[MODIFY] [app/Controllers/Procurement.php](file:///c:/wamp64/www/staging/app/Controllers/Procurement.php) & [Bids.php](file:///c:/wamp64/www/staging/app/Controllers/Bids.php)**:
  - Inject `tenant_id` and `branch_id` into the `$data` arrays upon insertion in [store](file:///c:/wamp64/www/staging/app/Controllers/Timesheets.php#74-117)/[create](file:///c:/wamp64/www/staging/app/Controllers/Leads.php#37-44) methods.
- **[MODIFY] [app/Controllers/VendorPortal.php](file:///c:/wamp64/www/staging/app/Controllers/VendorPortal.php)**:
  - Ensure external vendor access correctly resolves data despite the new [ErpModel](file:///c:/wamp64/www/staging/app/Models/ErpModel.php#7-115) scoping (since vendors rely on `vendor_id`/`creator_id` matching, we must bypass branch scoping for the Subcontractor role, or configure their session correctly).

### 4. Verification Plan
- **Automated/CLI tests**: Create `test:phase7` verifying branch isolation for POs and Bids when queried by internal users.

---

> [!NOTE]
> Below is the proposed plan for Phase 8.

## Phase 8: Inventory & Asset Management

This phase introduces dedicated modules for tracking physical assets (equipment, tools, vehicles) and consumable inventory (materials, supplies) across branches and projects. It ensures strict data isolation via [ErpModel](file:///c:/wamp64/www/staging/app/Models/ErpModel.php#7-115).

### 1. Database Migrations
- **[NEW] `migrations/..._CreateAssetTables.php`**:
  - `fs_assets`: Core asset registry (`tenant_id`, `branch_id`, `asset_tag`, `name`, `category`, [status](file:///c:/wamp64/www/staging/app/Controllers/Estimates.php#191-219), `purchase_date`, `purchase_price`, `current_location_project_id`).
  - `fs_asset_assignments`: History of asset movement/allocation (`asset_id`, `project_id`, `assigned_to_user_id`, `assigned_date`, `return_date`, [status](file:///c:/wamp64/www/staging/app/Controllers/Estimates.php#191-219)).
  - `fs_asset_maintenance`: Maintenance logs for assets (`asset_id`, `maintenance_date`, `description`, `cost`, `performed_by`).
- **[NEW] `migrations/..._CreateInventoryTables.php`**:
  - `fs_inventory_items`: Core catalog (`tenant_id`, `branch_id`, `sku`, `name`, `description`, `category`, `unit_of_measure`, `reorder_level`).
  - `fs_inventory_locations`: Physical warehouses/storage locations (`tenant_id`, `branch_id`, `name`, `address`).
  - `fs_inventory_stocks`: Current stock levels (`item_id`, `location_id`, `quantity`).
  - `fs_inventory_transactions`: Log of IN/OUT movements (`item_id`, `location_id`, `project_id_destination`, `quantity`, `transaction_type`, [date](file:///c:/wamp64/www/staging/app/Controllers/SiteDiary.php#108-128), [user_id](file:///c:/wamp64/www/staging/app/Models/FsUserModel.php#65-72)).

### 2. Application Core (Tenant Isolation)
- **[NEW] [app/Models/AssetModel.php](file:///c:/wamp64/www/staging/app/Models/AssetModel.php), [AssetAssignmentModel.php](file:///c:/wamp64/www/staging/app/Models/AssetAssignmentModel.php), [AssetMaintenanceModel.php](file:///c:/wamp64/www/staging/app/Models/AssetMaintenanceModel.php)**: Extending [ErpModel](file:///c:/wamp64/www/staging/app/Models/ErpModel.php#7-115).
- **[NEW] [app/Models/InventoryItemModel.php](file:///c:/wamp64/www/staging/app/Models/InventoryItemModel.php), [InventoryLocationModel.php](file:///c:/wamp64/www/staging/app/Models/InventoryLocationModel.php), [InventoryStockModel.php](file:///c:/wamp64/www/staging/app/Models/InventoryStockModel.php), [InventoryTransactionModel.php](file:///c:/wamp64/www/staging/app/Models/InventoryTransactionModel.php)**: Extending [ErpModel](file:///c:/wamp64/www/staging/app/Models/ErpModel.php#7-115).

### 3. Sub-controllers & Routes
- **[NEW] [app/Controllers/Assets.php](file:///c:/wamp64/www/staging/app/Controllers/Assets.php)**: Handles CRUD for assets, maintenance, and project assignments.
- **[NEW] [app/Controllers/Inventory.php](file:///c:/wamp64/www/staging/app/Controllers/Inventory.php)**: Handles catalog items, warehouses, stock counts, and transactions.
- **[MODIFY] [app/Config/Routes.php](file:///c:/wamp64/www/staging/app/Config/Routes.php)**: Map endpoints `inventory/*` and `assets/*`.

### 4. Verification Plan
- **Automated/CLI tests**: Create `test:phase8` simulating asset creation, project allocation, inventory receiving, and consumption, validating ABAC data boundaries.

---

> [!NOTE]
> Below is the proposed plan for Phase 9.

## Phase 9: Workforce & Payroll Engine

This phase upgrades the legacy standalone timesheet logic to a full-blown branch-scoped workforce and payroll preparation engine. It introduces labor tracking mapped to specific payroll rules, tax profiles, and compliance requirements.

### 1. Database Migrations
- **[NEW] `migrations/..._CreatePayrollTables.php`**:
  - `fs_payroll_profiles`: Branch-specific payroll settings (`tenant_id`, `branch_id`, `name`, `pay_period`, `overtime_rule_id`).
  - `fs_tax_profiles`: Tax bracket configurations (`tenant_id`, `branch_id`, `name`, `tax_rate`, `region_code`).
  - `fs_pay_runs`: Batches of processed payrolls (`tenant_id`, `branch_id`, `pay_period_start`, `pay_period_end`, [status](file:///c:/wamp64/www/staging/app/Controllers/Estimates.php#191-219), `approved_by`).
  - `fs_pay_slips`: Individual generated pay records per run (`pay_run_id`, [user_id](file:///c:/wamp64/www/staging/app/Models/FsUserModel.php#65-72), `gross_pay`, `net_pay`, `taxes_withheld`, `deductions`).
- **[MODIFY] `migrations/..._EnhanceTimesheetsTable.php`**:
  - Update `project_timesheets` to ensure hard `tenant_id` and `branch_id` linkage if missing.
  - Add `payroll_status` (unprocessed, processed), `pay_run_id` to link approved hours to a financial run.

### 2. Application Core (Tenant Isolation)
- **[NEW] [app/Models/PayrollProfileModel.php](file:///c:/wamp64/www/staging/app/Models/PayrollProfileModel.php), [TaxProfileModel.php](file:///c:/wamp64/www/staging/app/Models/TaxProfileModel.php), [PayRunModel.php](file:///c:/wamp64/www/staging/app/Models/PayRunModel.php), [PaySlipModel.php](file:///c:/wamp64/www/staging/app/Models/PaySlipModel.php)**: All extending [ErpModel](file:///c:/wamp64/www/staging/app/Models/ErpModel.php#7-115).
- **[MODIFY] [app/Models/TimesheetModel.php](file:///c:/wamp64/www/staging/app/Models/TimesheetModel.php)**: Ensure it strictly extends [ErpModel](file:///c:/wamp64/www/staging/app/Models/ErpModel.php#7-115) and supports the new payroll tracking parameters.

### 3. Application Services & Controllers
- **[NEW] [app/Services/PayrollEngine.php](file:///c:/wamp64/www/staging/app/Services/PayrollEngine.php)**: Core service to collect all approved [Timesheet](file:///c:/wamp64/www/staging/app/Controllers/Timesheets.php#12-282) entries for a branch within a date range, apply user-specific `fs_payroll_profiles` and `fs_tax_profiles`, and generate a simulated [PayRun](file:///c:/wamp64/www/staging/app/Models/PayRunModel.php#5-27) with individual [PaySlip](file:///c:/wamp64/www/staging/app/Models/PaySlipModel.php#5-30)s.
- **[NEW] [app/Controllers/Payroll.php](file:///c:/wamp64/www/staging/app/Controllers/Payroll.php)**: API/UI endpoints to view profiles, trigger PayRuns, and approve/export them.
- **[MODIFY] [app/Config/Routes.php](file:///c:/wamp64/www/staging/app/Config/Routes.php)**: Map endpoints `payroll/*`.

### 4. Verification Plan
- **Automated/CLI tests**: Create `test:phase9` that generates mock worker timesheets, triggers the [PayrollEngine](file:///c:/wamp64/www/staging/app/Services/PayrollEngine.php#11-103), and verifies that PayRuns strictly isolate calculations to the specific branch and map the correct tax deductions.

---

> [!NOTE]
> Below is the proposed plan for Phase 10.

## Phase 10: Financial Management & Reporting

The system already has basic schemas for `project_invoices`, `project_expenses`, `project_estimates`, and payments. However, to function as an Enterprise ERP, these ledgers must be strictly isolated to the branch/regional level and feed into a consolidated reporting engine.

### 1. Database Migrations
- **[NEW] `migrations/..._EnhanceFinancialTables.php`**:
  - Add `tenant_id` and `branch_id` to `project_invoices`.
  - Add `tenant_id` and `branch_id` to `project_expenses`.
  - Add `tenant_id` and `branch_id` to `project_estimates`.
  - Add `tenant_id` and `branch_id` to the payments ledger (`payments`, `payment_certificates`, `pay_apps` depending on the current existing state).

### 2. Application Core (Tenant Isolation)
- **[MODIFY] [app/Models/ProjectInvoiceModel.php](file:///c:/wamp64/www/staging/app/Models/ProjectInvoiceModel.php), [ProjectExpenseModel.php](file:///c:/wamp64/www/staging/app/Models/ProjectExpenseModel.php), [ProjectEstimateModel.php](file:///c:/wamp64/www/staging/app/Models/ProjectEstimateModel.php), [PaymentModel.php](file:///c:/wamp64/www/staging/app/Models/PaymentModel.php)**:
  - Update all financial models to extend [ErpModel](file:///c:/wamp64/www/staging/app/Models/ErpModel.php#7-115) instead of [Model](file:///c:/wamp64/www/staging/app/Models/ErpModel.php#7-115).
  - Inject `tenant_id` and `branch_id` into their respective `$allowedFields`.

### 3. Application Services & Controllers
- **[NEW] [app/Services/FinancialReportingEngine.php](file:///c:/wamp64/www/staging/app/Services/FinancialReportingEngine.php)**: A core data aggregation service. It will query the branch-isolated models to dynamically generate P&L (Profit & Loss), Cash Flow, and Budget vs Actuals statements.
- **[NEW] [app/Controllers/FinancialReports.php](file:///c:/wamp64/www/staging/app/Controllers/FinancialReports.php)**: API/UI endpoints to request financial metric summaries (e.g., `GET /reports/financial/pnl?branch=17`).
- **[MODIFY] [app/Config/Routes.php](file:///c:/wamp64/www/staging/app/Config/Routes.php)**: Map the financial reporting endpoints.

### 4. Verification Plan
- **Automated/CLI tests**: Create `test:phase10` that seeds branch-specific invoices, expenses, and payroll runs, and utilizes the [FinancialReportingEngine](file:///c:/wamp64/www/staging/app/Services/FinancialReportingEngine.php#10-104) to assert the calculated Profit and Loss precisely matches isolated arithmetic without leaking data from other simulated branches.

---

> [!NOTE]
> Below is the proposed plan for Phase 11.

## Phase 11: Front-End User Interfaces (ERP Views)

We have built a massive multi-tenant database schema and the back-end PHP/CodeIgniter engines to power it. We now need to build the actual Web Applications (Views) so users can interact with Inventory, Assets, Payroll, and Financials.

### 1. View Architecture & Layouts
- **[MODIFY] [app/Views/layouts/dashboard.php](file:///c:/wamp64/www/staging/app/Views/layouts/dashboard.php)**: Integrate a unified sidebar navigation structure utilizing the logged-in user's RBAC and ABAC checks to conditionally show strictly necessary modules (e.g., Warehouse Manager sees Inventory, HR sees Payroll).
- **[NEW] Component Modals**: Create reusable Bootstrap 5 driven generic modals for basic CRUD additions.

### 2. Assets & Inventory UI
- **[NEW] [app/Views/assets/index.php](file:///c:/wamp64/www/staging/app/Views/assets/index.php)**: A data grid showcasing `fs_assets`. Include an "Assign Asset" modal form.
- **[NEW] [app/Views/inventory/index.php](file:///c:/wamp64/www/staging/app/Views/inventory/index.php)**: A localized branch warehouse view listing items in `fs_inventory_stocks`. Include "Receive Stock" and "Consume Stock" modals.

### 3. Workforce & Payroll UI
- **[NEW] [app/Views/payroll/dashboard.php](file:///c:/wamp64/www/staging/app/Views/payroll/dashboard.php)**: The HR branch level view summarizing processed vs unprocessed timesheets.
- **[NEW] `app/Views/payroll/run.php`**: The UI interface allowing an HR admin to set a date range, view aggregated hours, and execute the `PayrollEngine->generatePayRun()` function.

### 4. Financial Reporting UI
- **[NEW] [app/Views/finance/reports_pnl.php](file:///c:/wamp64/www/staging/app/Views/finance/reports_pnl.php)**: A comprehensive chart and matrix dashboard pulling from `FinancialReports::pnl`. This will provide executives with a real-time Profit and Loss pipeline isolated to their branch or dynamically spanning all branches if they hold global clearance.

### 5. Controller Enhancements
- **[MODIFY] Controllers ([Assets.php](file:///c:/wamp64/www/staging/app/Controllers/Assets.php), [Inventory.php](file:///c:/wamp64/www/staging/app/Controllers/Inventory.php), [Payroll.php](file:///c:/wamp64/www/staging/app/Controllers/Payroll.php))**: Augment the existing RESTful APIs we built to also return rendered HTML Views for web browser routing.

### Verification Plan
- **Manual Verification**: We will navigate to the web pages within the browser, confirming sidebar rendering, checking that the tables populate, and asserting that the backend ABAC constraints successfully filter visual UI elements securely.

---

> [!NOTE]
> Below is the proposed plan for Phase 12.

## Phase 12: Legacy CRM Data Isolation

The system already has tables for `clients`, [leads](file:///c:/wamp64/www/staging/app/Controllers/Settings.php#719-726), and [tasks](file:///c:/wamp64/www/staging/app/Controllers/Settings.php#578-592). However, to function as a multi-tenant Enterprise ERP, these legacy CRM tables must be strictly isolated to the branch level.

### 1. Database Migrations
- **[NEW] `migrations/..._EnhanceCrmTables.php`**:
  - Add `tenant_id` and `branch_id` to `clients`.
  - Add `tenant_id` and `branch_id` to [leads](file:///c:/wamp64/www/staging/app/Controllers/Settings.php#719-726).
  - Add `tenant_id` and `branch_id` to [tasks](file:///c:/wamp64/www/staging/app/Controllers/Settings.php#578-592).

### 2. Application Core (Tenant Isolation)
- **[MODIFY] [app/Models/ClientModel.php](file:///c:/wamp64/www/staging/app/Models/ClientModel.php), [app/Models/LeadModel.php](file:///c:/wamp64/www/staging/app/Models/LeadModel.php), [app/Models/TaskModel.php](file:///c:/wamp64/www/staging/app/Models/TaskModel.php)**:
  - Update all models to extend [ErpModel](file:///c:/wamp64/www/staging/app/Models/ErpModel.php#7-115) instead of `CodeIgniter\Model`.
  - Inject `tenant_id` and `branch_id` into their respective `$allowedFields`.

### 3. Application Controllers
- **[MODIFY] [app/Controllers/Clients.php](file:///c:/wamp64/www/staging/app/Controllers/Clients.php), [app/Controllers/Leads.php](file:///c:/wamp64/www/staging/app/Controllers/Leads.php), [app/Controllers/Tasks.php](file:///c:/wamp64/www/staging/app/Controllers/Tasks.php)**:
  - Ensure any custom queries or specific business logic relies on [ErpModel](file:///c:/wamp64/www/staging/app/Models/ErpModel.php#7-115)'s implicit scoping. Check usages of these models to make sure `$tenantId` and `$branchId` are properly handled during creation.
  
### 4. Verification Plan
- **Automated/CLI tests**: Create `test:phase12` that seeds branch-specific clients, leads, and tasks, verifying that queries executed under a specific branch context omit data from other branches, confirming strict ABAC isolation on legacy tables.


 ---
 
 > [!NOTE]
 > Below is the proposed plan for Phase 19.
 
 ## Phase 19: Organization Onboarding, Subscriptions & Stripe
 
 This phase introduces the multi-tenant onboarding engine, allowing new construction companies to sign up, select a plan, and initialize their instance.
 
 ### 1. Database & Core Infrastructure
 - **[MODIFY] `migrations/...`**: (Completed/Refined) Added `subscription_packages`, `tenant_subscriptions`, and tenant-level Stripe/Package fields.
 - **[NEW] `app/Services/RegistrationService.php`**: Orchestrates the multi-step creation of Tenant, Subscription, and Admin User.
 
 ### 2. Public Signup Flow (The "Visitor" Experience)
 - **[NEW] `app/Controllers/Signup.php`**: 
   - [index()](file:///c:/wamp64/www/staging/app/Controllers/Estimates.php#27-38): Display available subscription packages (Flat rate vs Per-User).
   - `account()`: Step 1 - User account details (Name, Email, Password).
   - `company()`: Step 2 - Company details (Name, Employees, Country, Currency, Timezone).
   - `payment()`: Redirect to Stripe Checkout or internal payment page based on plan.
 - **[NEW] `app/Views/signup/index.php`**: Pricing table.
 - **[NEW] `app/Views/signup/form_wizard.php`**: Step-by-step registration wizard.
 
 ### 3. Super Admin Management
 - **[NEW] `app/Controllers/Tenants.php`**: Portal for Super Admins to monitor all onboarded companies.
 - **[MODIFY] [app/Views/layouts/dashboard.php](file:///c:/wamp64/www/staging/app/Views/layouts/dashboard.php)**: Sidebar integration for Super Admin specific links.
 
 ### 4. Access Isolation (Refined)
 - **[MODIFY] [app/Controllers/BaseAppController.php](file:///c:/wamp64/www/staging/app/Controllers/BaseAppController.php)**: Enforce subscription "Lock" if payment is missing/expired.
 
 ### 5. Verification Plan
 - **Manual Verification**: Access `/signup`, select a plan, complete the wizard, and verify that:
   1. A new `tenants` record is created with the correct currency/timezone.
   2. A new admin user is created and assigned to that tenant.
   3. The user can log in and see their specific dashboard (isolated data).
