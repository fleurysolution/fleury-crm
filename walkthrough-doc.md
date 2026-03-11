# Phase 4: Project Management (Execution + Commercial) Walkthrough

## Overview
We have successfully upgraded the CRM-centric `projects` functionality into an Enterprise ERP-grade implementation by enforcing data linkage and introducing Daily Construction Logs.

## 1. Database Migrations
Executed the following structural changes to support the FRS:
- **`projects` table**: Added `tenant_id`, `branch_id`, `contract_type`, `versioned_budget_baseline`, and `project_stage`.
- **`fs_daily_logs`**: Created the master daily log table representing site execution data per project.
- **`fs_daily_manpower` & `fs_daily_equipment`**: Linked detail tables supporting labor force tracking and asset utilization for that log entry.

## 2. Abac / ErpModel Enforcement
- **[ProjectModel](file:///c:/wamp64/www/staging/app/Models/ProjectModel.php#7-57)** now strictly extends [ErpModel](file:///c:/wamp64/www/staging/app/Models/ErpModel.php#7-82), meaning it is impossible for a Project to be created without a valid `branch_id`. Disconnected records are intercepted instantly at the application layer, logging an exception and aborting.
- The **[DailyLogs](file:///c:/wamp64/www/staging/app/Controllers/DailyLogs.php#10-101)** controller explicitly passes the active user's `branch_id` and `tenant_id` from their session context to the [DailyLogModel](file:///c:/wamp64/www/staging/app/Models/DailyLogModel.php#7-21) wrapper, ensuring the logs cascade down the same permissions tree as the project itself.

## 3. UI and MVC
- Refactored [app/Controllers/Projects.php](file:///c:/wamp64/www/staging/app/Controllers/Projects.php) to securely capture the new `contract_type`, `versioned_budget_baseline`, and `project_stage` inputs.
- Adapted [app/Views/projects/create.php](file:///c:/wamp64/www/staging/app/Views/projects/create.php) and [app/Views/projects/edit.php](file:///c:/wamp64/www/staging/app/Views/projects/edit.php) to display and edit these critical commercial data fields seamlessly alongside existing fields.
- Registered endpoints in [app/Config/Routes.php](file:///c:/wamp64/www/staging/app/Config/Routes.php) to support the incoming Daily Logs module ([DailyLogs.php](file:///c:/wamp64/www/staging/app/Controllers/DailyLogs.php)).

## 4. Verification Results
We formulated and ran a CodeIgniter Spark Command (`php spark test:phase4`) to simulate a session context inserting project management records.
- **Test Objective:** Validating that data falls precisely into the specified Organizational Hierarchy.
- **Outcome:** Submitting payloads dynamically mapped to `branch_id: 10` and `tenant_id: 1` successfully inserted records across `projects`, `fs_daily_logs`, and `fs_daily_manpower`. Attempting to omit the payload correctly fired the [logAndThrow](file:///c:/wamp64/www/staging/app/Models/ErpModel.php#71-81) security event. Storage logic works cleanly.

All Phase 4 objectives have been met.

### Phase 5: Field Collaboration (Completed)
We developed the core field collaboration modules (RFIs, Submittals, Drawings) with enterprise data isolation via [ErpModel](file:///c:/wamp64/www/staging/app/Models/ErpModel.php#7-82). Verified operations successfully through extensive automated testing.

### Phase 6: QHSE (Quality, Health, Safety, Environment) (Completed)
- **Database Migrations:** Created scalable schema for custom Inspections, Inspection Items, and Safety Incidents (`fs_inspections`, `fs_safety_incidents`). Also refactored the legacy `project_punch_lists` and `punch_list_items` tables to strictly enforce `tenant_id` and `branch_id`.
- **Tenant Isolation ([ErpModel](file:///c:/wamp64/www/staging/app/Models/ErpModel.php#7-82)):** Upgraded [InspectionModel](file:///c:/wamp64/www/staging/app/Models/InspectionModel.php#5-29), [SafetyIncidentModel](file:///c:/wamp64/www/staging/app/Models/SafetyIncidentModel.php#5-28), and Punch List Models to extend [ErpModel](file:///c:/wamp64/www/staging/app/Models/ErpModel.php#7-82). Data will now automatically be filtered and restricted to the respective branch and tenant contexts per ABAC permissions.
- **RESTful Controllers & Routes:** [Inspections](file:///c:/wamp64/www/staging/app/Controllers/Inspections.php#9-91) and [SafetyIncidents](file:///c:/wamp64/www/staging/app/Controllers/SafetyIncidents.php#8-85) controllers were developed, adhering to dependency injection and route mapping rules for nested project views.
- **Automated Validations:** The `php spark test:phase6` verification script was executed successfully, simulating RBAC permission blocks appropriately and validating creation/read scope restrictions across branches.

### Phase 7: Procurement & Subcontractor Management (Completed)
- **Database Migrations:** Expanded legacy `project_purchase_orders` and `project_bids` tables by explicitly injecting `tenant_id` and `branch_id` columns via an `ALTER TABLE` execution, securely syncing existing records to their parent project roots.
- **Tenant Isolation ([ErpModel](file:///c:/wamp64/www/staging/app/Models/ErpModel.php#7-82)):** Refactored [PurchaseOrderModel](file:///c:/wamp64/www/staging/app/Models/PurchaseOrderModel.php#5-44) and [BidModel](file:///c:/wamp64/www/staging/app/Models/BidModel.php#5-47) to implement [ErpModel](file:///c:/wamp64/www/staging/app/Models/ErpModel.php#7-82). Procurement records inherently enforce branch-level data leakage protection for internal users. 
- **Controllers Validation:** Controllers orchestrating Bids and Purchase Orders ([Bids.php](file:///c:/wamp64/www/staging/app/Controllers/Bids.php), [Procurement.php](file:///c:/wamp64/www/staging/app/Controllers/Procurement.php)) were wired to explicitly pass the `branch_id` session constraint upon storing records.
- **Automated Validations:** Formulated the `php spark test:phase7` command ensuring that attempting to inject a branch-less PO or Bid triggers ErpModel's mandatory [logAndThrow](file:///c:/wamp64/www/staging/app/Models/ErpModel.php#71-81) branch data restriction exceptions. Querying data successfully proved functional ABAC branch isolation constraints.

### Phase 8: Inventory & Asset Management (Completed)
- **Database Migrations:** Created scalable Enterprise schema for physical assets `fs_assets`, `fs_asset_assignments`, `fs_asset_maintenance` and consumable inventory data structures `fs_inventory_items`, `fs_inventory_locations`, `fs_inventory_stocks`, and `fs_inventory_transactions`.
- **Tenant Isolation ([ErpModel](file:///c:/wamp64/www/staging/app/Models/ErpModel.php#7-82)):** Created 7 new distinct models representing the data schema and configured inheritance to automatically hook into [ErpModel](file:///c:/wamp64/www/staging/app/Models/ErpModel.php#7-82). Linked metadata records like transactions intelligently bypass primary tenant_id checks knowing parent schemas strictly secure entry.
- **RESTful Controllers & Routes:** Setup [Assets](file:///c:/wamp64/www/staging/app/Controllers/Assets.php#9-89) and [Inventory](file:///c:/wamp64/www/staging/app/Controllers/Inventory.php#10-103) controllers for branch-isolated cataloging, assignment logging, and inbound/outbound material transfer transaction ledgers.
- **Automated Validations:** The `php spark test:phase8` script successfully validated branch scoping during insertion attempts, and successfully resolved automated cascading logs for assignments, maintenance, and stock adjustments securely wrapped in the mock PM session scope.

### Phase 9: Workforce & Payroll Engine (Completed)
- **Database Migrations:** Expanded legacy timesheet capabilities by adding `fs_payroll_profiles`, `fs_tax_profiles`, `fs_pay_runs`, and `fs_pay_slips`. Injected `payroll_status` into `timesheets` tables.
- **Tenant Isolation ([ErpModel](file:///c:/wamp64/www/staging/app/Models/ErpModel.php#7-82)):** Upgraded [TimesheetModel](file:///c:/wamp64/www/staging/app/Models/TimesheetModel.php#7-53) to inherit [ErpModel](file:///c:/wamp64/www/staging/app/Models/ErpModel.php#7-82) and built corresponding Payroll models to automatically shield internal HR metrics by `branch_id`.
- **Payroll Service Engine:** Formulated the [PayrollEngine.php](file:///c:/wamp64/www/staging/app/Services/PayrollEngine.php) capable of digitally harvesting branch-specific `timesheets`, calculating gross/net wages based on the worker's mapped [TaxProfile](file:///c:/wamp64/www/staging/app/Controllers/Payroll.php#33-53), and outputting immutable `PayRuns` populated with `PaySlips`.
- **RESTful Controllers:** Built the [Payroll](file:///c:/wamp64/www/staging/app/Controllers/Payroll.php#10-109) controller with discrete endpoints for branch-level policy creation and batch payroll trigger execution.
- **Automated Validations:** The `php spark test:phase9` securely validated that the engine processes accurate ledgers and locks queried data uniquely to the user's `branch_id`. Discovered and patched an [ErpModel](file:///c:/wamp64/www/staging/app/Models/ErpModel.php#7-82) core bug preventing intentional bypasses on child data relationships.

### Phase 10: Financial Management & Reporting (Completed)
- **Database Migrations:** Hardened the standard CRM billing ledgers (`project_invoices`, `project_expenses`, `project_estimates`, `invoice_payments`) to now store multi-tenant arrays `tenant_id` and `branch_id`.
- **Tenant Isolation ([ErpModel](file:///c:/wamp64/www/staging/app/Models/ErpModel.php#7-82)):** Refactored [ProjectInvoiceModel](file:///c:/wamp64/www/staging/app/Models/ProjectInvoiceModel.php#7-45), [ProjectExpenseModel](file:///c:/wamp64/www/staging/app/Models/ProjectExpenseModel.php#7-48), [ProjectEstimateModel](file:///c:/wamp64/www/staging/app/Models/ProjectEstimateModel.php#7-57), and [PaymentModel](file:///c:/wamp64/www/staging/app/Models/PaymentModel.php#7-34) to intrinsically query against [ErpModel](file:///c:/wamp64/www/staging/app/Models/ErpModel.php#7-82) constraints, completely severing external access via the codebase.
- **Financial Aggregation Engine:** Engineered [FinancialReportingEngine.php](file:///c:/wamp64/www/staging/app/Services/FinancialReportingEngine.php) capable of safely consuming `income` invoices and parsing `expenses` against `pay_slips` to compile comprehensive Branch/Organizational Profit and Loss (P&L) matrixes dynamically.
- **RESTful Endpoints:** Setup [FinancialReports](file:///c:/wamp64/www/staging/app/Controllers/FinancialReports.php#7-35) P&L controller and mapped `GET /reports/financial/pnl`.
- **Automated Validations:** The `php spark test:phase10` simulated arbitrary commercial transactions in a specific branch, and the engine correctly filtered them down to calculate the precise Gross and Net profitability percentages while denying ledger ghosting from alternate simulated branch queries.
