# BPMS247 - Organization & Branch Setup Guide

This guide provides step-by-step instructions for setting up a new construction company (Tenant) and its regional offices (Branches) within the BPMS247 ERP system.

---

## 🏗️ Phase 1: Organization Initialization (Tenant)

In the BPMS247 multi-tenant architecture, the **Tenant** represents the entire construction enterprise. 

### 1.1 Accessing General Settings
1.  Log in as a **System Administrator**.
2.  Navigate to the **Settings** gear icon in the sidebar.
3.  Click on **General Settings**.
4.  **Organization Name**: Ensure your company's legal name is set here. This name will appear on all Invoices, RFIs, and Reports.
5.  **Company Branding**: Upload your company logo. This will be automatically embedded into PDF exports.

---

## 🌎 Phase 2: Regional Structure

For companies operating across multiple states or territories, BPMS247 uses a tiered hierarchy: **Region -> Office -> Division**.

### 2.1 Creating Regions
1.  Go to **Settings** -> **Branch Structure** -> **Regions**.
2.  Click **Add Region**.
3.  **Name**: (e.g., *Southeast Region*).
4.  **Code**: A 2-3 letter identifier (e.g., *SE*).
5.  Click **Save**.

### 2.2 Creating Offices (Branches)
Offices are your physical operational hubs.

1.  Go to **Settings** -> **Branch Structure** -> **Offices**.
2.  **Select Region**: Choose the region you created in the previous step.
3.  **Office Name**: (e.g., *Miami Field Office*).
4.  **Contact Info**: Enter the branch-specific email and phone number. This ensures local project communications are routed correctly.
5.  Click **Save**.

### 2.3 Creating Divisions (Optional)
If a branch has specialized teams (e.g., *Civil Engineering* vs. *Vertical Construction*):
1.  Go to **Settings** -> **Branch Structure** -> **Divisions**.
2.  Link the division to the specific **Office**.

---

## 👥 Phase 3: Workforce Assignment

Once the structure is built, you must link your staff to their respective branches.

1.  Navigate to **Management** -> **Users**.
2.  Select a user or **Create New User**.
3.  **Primary Branch**: Select the physical office they are based in.
4.  **Geo-Access**: 
    *   **Branch Only**: The user only sees projects and data for their specific office.
    *   **Region Wide**: The user can see data for all offices within their assigned region.
    *   **Organization Wide**: Full visibility across all regions (ideal for C-suite and Finance).

---

## 📋 Summary Checklist for New Company Setup
- [ ] Confirm Organization Name in General Settings.
- [ ] Create at least one **Region**.
- [ ] Create at least one **Office/Branch**.
- [ ] Assign at least one **Project Manager** to the new branch.
- [ ] Define **Approval Workflows** for the branch (Expenses & Site Diaries).

---
_BPMS247 - Precision Construction Management_
