# Figma Prototype — Excel-Based Dropdown Data Only

Build the prototype using the uploaded Excel files as the **only source of dropdown data**.

Uploaded files:

* `Monitoring.xlsx`
* `SAOB-Current-Year-Appropriations-2026.xlsx`
* `SAOB-Continuing-Appropriations-2026.xlsx`
* `SAOB-Working-Paper-2026.xlsx`
* `OBRS.xlsx`
* `BURS.xlsx`

## CRITICAL REQUIREMENT

There is **NO system data yet**.

Do not display any existing transactions, employees, expenditures, salaries, financial amounts, reports, balances, or other records as system data.

The only data that should appear in the prototype is **reference/dropdown data extracted from the uploaded Excel files**.

Do not invent, assume, abbreviate, or modify dropdown options.

If an Excel file contains a column, lookup table, reference sheet, code list, or categorical value used by another sheet, use those actual values in the corresponding dropdown.

---

# DROPDOWN DATA RULE

Every dropdown must be populated from the actual Excel reference data.

For example, if an Excel workbook contains:

* RC codes
* UACS codes
* Fund codes
* Account codes
* Office/department codes
* Classification codes
* Other lookup/reference values

use those exact values.

Preserve:

* Exact spelling
* Exact capitalization
* Exact codes
* Exact descriptions
* Leading zeros
* Punctuation
* Original terminology

Do not create generic options such as:

* "Department A"
* "Department B"
* "Other"
* "Select Category 1"
* "Sample Employee"

unless those exact values exist in the Excel files.

---

# EXCEL REFERENCE SHEETS

Inspect the uploaded workbooks and identify all sheets that function as reference/lookup tables.

In particular, inspect the reference data used by formulas such as:

`VLOOKUP(...,'RC'!...)`

and

`VLOOKUP(...,'UACS'!...)`

and the corresponding RC and UACS sheets in the other workbooks.

Use the actual lookup values from those sheets.

Do not merely display the formulas.

The dropdown should show the actual human-readable value and, where appropriate, the associated code.

Example format:

```text
[ Select UACS ]

1001 — [actual description from Excel]
1002 — [actual description from Excel]
1003 — [actual description from Excel]
```

Only use this format if the workbook actually contains both a code and description.

---

# PRE — RECEIPTS & EXPENDITURES

Use the existing navigation:

```text
PRE — Receipts & Expenditures

Overview
Submit PRE
PPMP Import
Expenditures
OBRS
BURS
Working Paper
SAOB
Monitoring
Reports
Analytics
```

For forms such as Submit PRE, Expenditures, OBRS, BURS, Working Paper, and SAOB:

* Use actual Excel-derived dropdown options.
* Keep record tables empty.
* Keep financial values empty.
* Do not create fake transactions.

---

# OBRS

Use the actual reference data from `OBRS.xlsx`.

The workbook contains lookup relationships involving:

* RC
* UACS Code

Populate the corresponding dropdowns with the actual values from the workbook/reference sheets.

Do not create fake RC or UACS values.

The OBR form should otherwise be empty and ready for user input.

---

# BURS

Use the actual reference data from `BURS.xlsx`.

The workbook contains lookup relationships involving:

* RC
* UACS Code

Populate the corresponding dropdowns using the actual values from the uploaded workbook/reference sheets.

Do not create fake BURS records.

The BUR form should otherwise be empty.

---

# WORKING PAPER

Use the actual reference data from:

`SAOB-Working-Paper-2026.xlsx`

including the workbook's:

* `RC`
* `UACS`

reference sheets.

Populate relevant dropdowns with the actual values found in these sheets.

Do not create working-paper records.

---

# SAOB

Use the actual structures and reference values from:

`SAOB-Current-Year-Appropriations-2026.xlsx`

and

`SAOB-Continuing-Appropriations-2026.xlsx`

Preserve the actual categories/sheet terminology, including:

* `100`
* `200`
* `301`
* `FHE`
* `302`
* `303`
* `303-NBERIC`
* `304`

where applicable.

Do not invent additional SAOB categories.

The SAOB tables themselves should remain empty.

---

# MONITORING

Use reference/dropdown values from:

`Monitoring.xlsx`

including the actual reference values used by its:

* `Utilization`
* `conso-fidu`
* `conso-nonfidu`

structures.

Do not display existing financial records as system data.

---

# ANNUALIZATION

The Annualization module should also start empty.

Use dropdown/reference values from the uploaded Excel files **only where the files actually contain corresponding reference data**.

Do not invent:

* Employee names
* Positions
* Salaries
* Deductions
* Departments
* Salary records

unless those values are actually present in the uploaded files and are specifically being used as reference/dropdown data.

---

# EMPTY TABLE STATE

All data tables should look like a newly installed system.

Example:

```text
Search...

────────────────────────────────────────────

             No data available

      Import or add records to display them.
```

Do not fill empty tables with fake rows.

---

# EMPTY DASHBOARD

Do not show fake financial statistics.

Instead show:

```text
No data available

Import your Excel files or add records
to begin using the system.
```

Do not display:

* ₱0 unless explicitly appropriate for the UI
* Fake totals
* Fake percentages
* Fake charts
* Fake transaction counts

---

# DESIGN

Maintain the existing minimalist design:

* White primary background
* Green primary accent
* Dark neutral text
* Light gray borders
* Minimal shadows
* Simple tables
* Clean forms
* Compact sidebar
* Professional government-system appearance

Use the existing navigation:

```text
GENERAL

Dashboard


PRE — RECEIPTS & EXPENDITURES

Overview
Submit PRE
PPMP Import
Expenditures
OBRS
BURS
Working Paper
SAOB
Monitoring
Reports
Analytics


ANNUALIZATION

Overview
Import Salary
Salary Records
Employee Details
Deductions
Reports
Analytics
```

User profile at the bottom:

```text
KA

Kevin John D. Austria
Budget Officer II
```

---

# MOST IMPORTANT

The prototype must distinguish between:

**REFERENCE DATA**
→ extracted from the uploaded Excel files
→ allowed in dropdowns

and

**SYSTEM RECORDS**
→ not yet available
→ must remain empty

Do not use the Excel's existing financial records as fake live system records.

Use the Excel files primarily to determine the **available dropdown/reference values and form structure**.

Do not hallucinate any values that cannot be found in the uploaded Excel files.
