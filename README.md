# MMSU Government Financial Management System

Laravel 13 + PHP-FPM 8.4 + nginx, served through Docker Compose. Blade views,
Tailwind CSS v4 via the Laravel Vite plugin, SQLite by default.

## Running it

```bash
docker compose up -d --build
```

The app is served at <http://localhost:8000>.

First run, inside the PHP container:

```bash
docker compose exec phpservice composer setup
```

That installs dependencies, copies `.env.example` to `.env`, generates the app
key, creates `database/database.sqlite`, runs the migrations and the seeder, and
builds the frontend assets.

Sign in with the seeded account: `kaustria@mmsu.edu.ph` / `password`.

For frontend work, run Vite in watch mode alongside the containers:

```bash
docker compose exec phpservice npm run dev
```

## Layout

```
docker-compose.yml         webServer (nginx) + phpservice (php-fpm)
docker/nginx/              Dockerfile + default.conf
docs/source-workbooks/     The OBRS / BURS / SAOB / Monitoring source PDFs
src/                       The Laravel application
```

Inside `src/`:

| Path | What lives there |
| --- | --- |
| `app/Support/Reference.php` | RC, UACS, MFO/PAP, fund clusters, personnel — the workbook VLOOKUP tables |
| `app/Support/Format.php` | Peso/date formatting and every workbook formula (SAOB, OBR/BUR, monitoring) |
| `app/Support/Navigation.php` | Sidebar groups, page titles, active-route matching |
| `app/Support/SaobSheets.php` | The eight SAOB program sheets and the 11-column model |
| `app/Models/` | `FinDocument`, `DocumentLine`, `LedgerEntry`, `SaobLine`, `WorkingPaperRow`, `UtilizationRow`, `ConsolidatedPpmpRow`, `SalaryRecord`, `User` |
| `app/Http/Controllers/Pre/` | OBRS/BURS, ledger entries, SAOB, Working Paper, Monitoring, and the remaining PRE pages |
| `app/Http/Controllers/Annualization/` | Overview, import, records, employee, deductions, reports, analytics |
| `resources/views/components/` | The Blade UI kit — button, card, badge, table, tabs, empty state, toast, icons |
| `resources/views/components/layouts/app.blade.php` | Sidebar + top bar shell |
| `routes/web.php` | Every route, grouped `pre.*` and `ann.*` |

## Domain notes

**OBRS / BURS.** One controller serves both; the route's `kind` default picks
obligation (Appendix 11, serial `OBR-YYYY-NNNN`) or utilization (Appendix 14,
`BUR-YYYY-NNNN`). Saving a document seeds its Status of Obligation ledger with a
single obligation entry equal to the document total. Payable entries reference a
Disbursement Voucher; payment entries reference a Check/ADA/TRA instrument on an
OBR or an RCI/RADAI/RTRAI instrument on a BUR.

**Status is always derived**, never typed — `FinDocument::deriveStatus()` reads
the certifications and the ledger: Draft → Certified (both certifications
signed) → Obligated/Utilized (an obligation entry exists) → Paid (payments cover
the total). Running balances follow the same rule: Not Yet Due = Σobligation −
Σpayable, Due & Demandable = Σpayable − Σpayment.

**SAOB** stores only the six input columns. Columns 5, 6, 7, 9 and 11 are
computed by `Format::computeSaobRow()` on every render, so the workbook formulas
stay the single source of truth:

```
5  Adjusted Appropriations   = 1 ± 3 ± 4
6  Adjusted Allotment        = 2 ± 3 ± 4
7  Unreleased Appropriations = 5 − 6
9  Unobligated Allotment     = 6 − 8
11 Disbursements             = 8 − 10
```

Sub-totals sum the *inputs* column-wise and recompute, rather than summing
derived values.

**Column terminology is verbatim from the source workbooks.** The Monitoring
"Utilization of Fund" sheet uses **MODE**; the conso-fidu / conso-nonfidu sheets
use **MOOE**. They are not typos and should not be normalized.

**Reference vs. transactional data.** `app/Support/Reference.php` holds the
dropdown and VLOOKUP data transcribed from the workbooks. Transactional tables
start empty on purpose — the workbooks were a source of reference data only, so
every list renders its empty state until records are entered or imported. The
seeder creates user accounts and nothing else.

## Design system

Strict four-color MMSU identity, defined once in `resources/css/app.css`:

| | |
| --- | --- |
| Green `#0C4B05` | Primary — text, borders, icons, chrome |
| Yellow `#FFCD00` | Emphasis / warning |
| Red `#DE1900` | Danger / errors |
| Blue `#002AFC` | Info / certified |

White is the only background. No tints, gradients, transparency-derived shades,
or grays. Status is never signalled by color alone — `<x-status-badge>` always
pairs the color with a dot and a text label.

Fonts: Calibri (body), Garamond (headings), Arial Narrow (tabular data),
Advantage (display wordmark).

## Progressive enhancement

Every page works without JavaScript. Filters and tabs are `GET` forms and links,
forms are real `POST` submissions, and validation runs server-side. The small
`resources/js/app.js` bundle only adds the mobile drawer, add/remove line rows,
the ledger's entry-type field swap, and Particulars auto-fill from the object
code.

## Tests

```bash
docker compose exec phpservice php artisan test
```

`tests/Unit` pins the workbook formulas and the VLOOKUP fallbacks;
`tests/Feature` covers authentication, the OBR/BUR lifecycle through to Paid,
and every page in the sidebar.
