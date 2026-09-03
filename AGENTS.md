# MMSU Government Financial Management System

Laravel 13 + Blade + Tailwind CSS v4, running under Docker (nginx + php-fpm).

## Running the app

The stack is already defined in `docker-compose.yml` at the repository root:

```bash
docker compose up -d --build          # nginx on :8000, php-fpm behind it
docker compose exec phpservice composer setup   # first run only
docker compose exec phpservice npm run dev      # Vite watch, for frontend work
```

Artisan and Composer run inside the `phpservice` container:

```bash
docker compose exec phpservice php artisan migrate
docker compose exec phpservice php artisan test
```

## Project structure

This is the canonical structure. Start with the task-relevant files; only follow
imports or inspect other files when required, when a documented path is missing,
or when the repository contradicts this guide.

- `src/routes/web.php` — every route, grouped `pre.*` (Program of Receipts and
  Expenditures) and `ann.*` (Annualization). Start here to find a page.
- `src/app/Support/Reference.php` — RC codes, UACS object codes, MFO/PAP,
  fund clusters and personnel. The workbook VLOOKUP tables.
- `src/app/Support/Format.php` — peso/date formatting plus **every workbook
  formula**. Change a calculation here, never in a view.
- `src/app/Support/Navigation.php` — sidebar groups, page titles, active-route
  matching. Adding a page means adding it here *and* to `routes/web.php`.
- `src/app/Support/SaobSheets.php` — the eight SAOB program sheets and the
  11-column model.
- `src/app/Models/` — `FinDocument` (an OBR or BUR), `DocumentLine`,
  `LedgerEntry`, `SaobLine`, `WorkingPaperRow`, `UtilizationRow`,
  `ConsolidatedPpmpRow`, `SalaryRecord`, `User`.
- `src/app/Http/Controllers/Pre/ObligationController.php` — serves both OBRS and
  BURS; the route's `kind` default selects which, and `self::CONFIG` holds the
  per-kind labels.
- `src/resources/views/components/` — the Blade UI kit (button, card, badge,
  table, th/td, tabs, empty-state, stat, toast, icon).
- `src/resources/views/components/layouts/app.blade.php` — the sidebar + top bar
  shell every authenticated page extends.
- `src/resources/css/app.css` — Tailwind v4 entrypoint and the entire design
  token set.
- `src/resources/js/app.js` — the small progressive-enhancement bundle.

## Domain rules

**Status is derived, never typed.** `FinDocument::deriveStatus()` reads the
certifications and the ledger. Do not add a status dropdown; add ledger entries
and let the status follow. Same for balances: Not Yet Due = Σobligation −
Σpayable, Due & Demandable = Σpayable − Σpayment.

**SAOB stores inputs only.** Columns 5, 6, 7, 9 and 11 are computed on render by
`Format::computeSaobRow()`. Never persist a derived column, and never sum derived
values — `Format::sumSaobRows()` sums the inputs and recomputes.

**Workbook terminology is verbatim.** The Monitoring "Utilization of Fund" sheet
uses **MODE**; the conso-fidu / conso-nonfidu sheets use **MOOE**. Both are
correct as written. `lookupRcName()` and `lookupUacsTitle()` return `#N/A` for
unknown codes, matching the source spreadsheets — keep that fallback.

**Transactional tables start empty on purpose.** The uploaded workbooks were a
source of *reference* data only. Lists render empty states until records are
entered or imported; the seeder creates user accounts and nothing else. Do not
add fake transactional seed data.

## Styling

Tailwind CSS v4 through `@tailwindcss/vite`, configured in `src/vite.config.js`.
No Tailwind config file and no PostCSS config — theme customization goes in the
`@theme` block in `src/resources/css/app.css`.

The MMSU identity is a **strict four-color system**: green `#0C4B05` (primary),
yellow `#FFCD00` (emphasis), red `#DE1900` (danger), blue `#002AFC` (info), on
white. No tints, gradients, transparency-derived shades, or grays — the
`--color-brand-*` scale deliberately resolves to flat values. Use the existing
tokens (`text-ink`, `border-line`, `bg-canvas`, `bg-surface`, `text-brand-700`)
rather than raw Tailwind colors.

Never signal status by color alone. `<x-status-badge>` pairs every color with a
dot and a text label.

## Code quality

- Reuse the Blade components in `resources/views/components/` instead of
  hand-writing markup; new shared markup becomes a component.
- Every page must work without JavaScript: filters and tabs are GET forms and
  links, mutations are POST forms with `@csrf`, validation is server-side.
- Validate against the reference tables (`in:` rules built from
  `Reference::RC_RECORDS` etc.), as `StoreObligationRequest` does.
- Follow Laravel Pint formatting: `docker compose exec phpservice ./vendor/bin/pint`.
