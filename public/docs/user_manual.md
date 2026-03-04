# BPMS247 - User Workflow Manual

Welcome to the comprehensive workflow guide for the BPMS247 construction management suite. This document outlines standard administrative and field procedures.

## 1. Creating a New Project Workspace

Before work can be logged, an active Project Workspace is required.

1.  Navigate to **Projects** -> **Create New**.
2.  Assign the project to an active **Client** (or leave unassigned for internal ventures).
3.  Fill in the start date, estimated budget, and general description.
4.  Once created, click on the **Project Title** to open the dedicated workspace containing tabs for Tasks, RFIs, Submittals, Finance, and more.

## 2. Inviting Team Members and Clients

1.  Go to **Management** -> **Users**.
2.  Click **Create User**.
3.  Assign an appropriate role:
    *   **Project Manager**: Full operational control over specific assigned projects.
    *   **Field Worker**: Restricted to time logging, punch lists, daily logs (site diary) and task updates.
    *   **Client**: Restricted to viewing progress reports, signing change orders, and viewing estimates linked to their `client_id`.
4.  Once created, an automated notification will be sent (if configured) with their default login details.

## 3. Financial Workflows

### Generating Estimates

1.  Navigate to **Financial** -> **Estimates**.
2.  Create a general client estimate, or from a Project Dashboard, navigate to the **Estimates** tab.
3.  Use the line-item builder to break down specific labor and material costs.
4.  Click **Export PDF** to send to the client, or **Send via Email** (if configured).
5.  Once approved, estimates can be **Cloned** or converted into **Invoices** -> `convert_to_invoice($id)`.

### Managing Vendor Bids

1.  Navigate to a specific **Project Workspace** -> **Estimates & Bids** tab.
2.  Log a new quote via **Add Bid Quote**.
3.  Input the Vendor's name, their trade package (e.g., Electrical), and the total quoted amount.
4.  Upload the quote PDF they sent you for record-keeping.
5.  If accepted, change the status to **Awarded**.

### Issuing Purchase Orders

1.  Once a Vendor is selected, navigate to **Project Workspace** -> **Procurement** tab.
2.  Click **Draft PO**. Select the Vendor from the dropdown menu.
3.  Add line items detailing the quantities, unit prices (e.g., LS, EA, HR), and descriptions.
4.  Click **Mark Executed**. This will automatically calculate the total run in the backend and assign it to the Vendor's dedicated portal.

## 4. Subcontractor & Vendor Portal

Vendors have a dedicated, simplified portal accessible only when logging in as a `subcontractor_vendor`.

*   **Registration**: Potential vendors can register themselves at the public url: `yoursite.com/vendor/apply`.
*   **Onboarding**: Project Managers review these applications under **Management** -> **Vendor Apps**. Once approved, an account is automatically provisioned for them.
*   **Vendor Dashboard**: Vendors can securely view:
    *   Purchase Orders marked as 'Executed' or 'Sent'.
    *   Bids they originated.
    *   Tasks explicitly assigned to their User Account. They cannot view general project Tasks or Budgets.

## 5. Field Management Workflows

### Site Diaries (Daily Logs)

1.  Field supervisors should log into the mobile-friendly web view.
2.  Go to **Project Workspace** -> **Field Management** -> **Site Diary**.
3.  Click **Create Daily Log**.
4.  Record weather patterns, daily workforce hours, and specific activities occurring on the job site.
5.  Click **Submit for Review**. An internal PM will then be able to **Approve** the log.

### Punch Lists

1.  Go to **Project Workspace** -> **Field Management** -> **Punch List**.
2.  Create a new defect item. Assign a priority and due date.
3.  (Optional) Assign the ticket to an internal `Field Worker` or a `Subcontractor` to resolve.
4.  Once resolved, mark as `Resolved` and ultimately close the ticket.

### Progress Photos and Reporting

1.  Go to **Project Workspace** -> **Photos** tab.
2.  Batch upload images detailing recent site progress.
3.  To generate a client report, click **Generate Progress Report PDF**. The system will dynamically generate an A4 document containing the project summary, phase percentage completed, recent activities, and thumbnails of the uploaded photos.

***
_Generated internally by BPMS247 Documentation_
