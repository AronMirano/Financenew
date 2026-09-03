# BURS / OBRS Data-Entry Flow

## Context

The BURS and OBRS pages are currently presentational shells: the shared `ObligationForm`
renders blank fields, amounts aren't stored, "Save" only fires a toast, and nothing
appears in the list afterward. The user wants a real data-entry flow for the Budget
Utilization Request (Appendix 14) and, symmetrically, the Obligation Request (Appendix 11):
RC/UACS auto-lookup, multi-line amounts with an auto-summed total, certifications drawn
from a personnel list, and a running **Status of Obligation** ledger with
auto-computed balances. Saved documents must persist across refreshes and drive the list.

Decisions confirmed with the user:
- **Add editable reference tables** for MFO/PAP and personnel (none exist today).
- **Persist to `localStorage`** — saved documents survive refresh and appear in the list.
- **Build both BURS and OBRS** via the shared form, with per-type terminology and payment
  instruments (OBR: Check/ADA/TRA; BUR: RCI/RADAI/RTRAI).

## Data model & reference additions

### `src/data/reference.ts` (extend — reuse existing `FundCategory`, `RcRecord`, helpers)
- Add `interface MfoPapRecord { code: string; name: string; category: FundCategory }` and
  `MFO_PAP_RECORDS: MfoPapRecord[]` — one representative MFO/PAP per program category
  (100/200/301/302/303/304). Header comment marks values as editable placeholders.
- Add helper `mfoPapForRc(rc: RcRecord): MfoPapRecord | undefined` (maps via `rc.category`)
  and `mfoPapForCategory(cat)`.
- Add `interface Personnel { id: string; name: string; position: string }` and
  `PERSONNEL: Personnel[]` (short list; include Kevin John D. Austria, Budget Officer II).
  Helper `lookupPersonnel(id)`.
- Add `defaultFundCluster(kind: "obligation" | "utilization"): string` — OBR →
  `01-1-01-101` (Regular Agency Fund), BUR → `05206441` (Trust/IGP). Editable in the form.
- **RC key caveat:** `RC_RECORDS` has a duplicate `code` (COE and MMSU IGP share
  `01-01-13-07`). Select RCs by **acronym** (unique) as the line key; store both
  `rcAcronym` and `rcCode` on each line and resolve name/MFO-PAP via
  `lookupRcByAcronym` (already exists).

### `src/data/documents.ts` (new — the persistence layer)
Richer, multi-line document type (the flat `BurRecord`/`ObrRecord` in `seed.ts` stay as-is;
they back other empty-state pages and aren't multi-line):
```ts
type DocKind = "obligation" | "utilization";
type DocStatus = "Draft" | "Certified" | "Obligated" | "Utilized" | "Paid";
interface DocLine { id; rcAcronym; rcCode; objectCode; particulars; amount: number }
interface Certification { officerId?: string; date?: string }
type LedgerKind = "obligation" | "payable" | "payment";
interface LedgerEntry { id; kind: LedgerKind; entryDate; referenceNo; amount: number }
interface FinDocument {
  id; serial; kind: DocKind; date; fundCluster;
  payeeName; office; address;
  lines: DocLine[]; certA: Certification; certB: Certification;
  ledger: LedgerEntry[]; status: DocStatus; createdAt;
}
```
- `localStorage`-backed store (key `mmsu-fin-documents`) with load/save, `createDocument`,
  `updateDocument`, `listDocuments(kind)`.
- `nextSerial(kind)` → `BUR-2026-0001` / `OBR-2026-0001`, auto-incrementing per kind/year.
- On create, seed the ledger with one `obligation` entry equal to the total.
- `deriveStatus(doc)` from ledger + certs: both certs → `Certified`; obligation entry
  present → `Obligated`(OBR)/`Utilized`(BUR); payments ≥ total → `Paid`.
- `useDocuments(kind)` React hook via `useSyncExternalStore` so lists re-render on change.
- Reuse `obrNotYetDue`, `obrDueDemandable`, `sum` from `src/lib/format.ts` for balances;
  `peso`/`amt`/`longDate`/`shortDate` for display.

## Form: `src/pages/pre/ObligationForm.tsx` (rewrite, presentational + parent-controlled draft)
- Add a `useObligationDraft(kind, existing?)` hook returning `{ draft, update..., isValid, build() }`
  so the parent Modal footer "Save" can persist. Form stays presentational.
- **Header:** Serial No. (read-only, shows `nextSerial(kind)` preview); Date (date picker,
  defaults today); Fund Cluster (Select from `FUND_CLUSTERS`, defaulted via
  `defaultFundCluster`); Payee Name / Office / Address (free text; Payee offers a
  `<datalist>` of previously used payees from the store).
- **Line items (Add Line):** RC Select **by acronym** → read-only auto-extract of RC Code,
  RC Name, and MFO/PAP code + name (`mfoPapForRc`). UACS Object Code Select → auto-fills
  Particulars from `lookupUacsTitle` (editable) and shows expense class via `uacsClass`
  (small, for reference). **Amount** numeric input **bound to state**. Delete row (keep ≥1).
- **Total Amount** auto-sums all line amounts (read-only).
- **Certifications:** Cert A (certifying officer Select from `PERSONNEL`) and Cert B (budget
  officer Select, defaulted to Austria); selecting an officer auto-stamps the date.

## Detail + Status ledger: `src/pages/pre/ObligationDetail.tsx` (new)
Opened from a list row's "View". Shows the printed-style header, line table with total, both
certifications, and the **Status of Obligation** ledger table:
- Columns: Date, Reference No., Obligation, Payable, Payment, Not Yet Due (obligation−payable),
  Due & Demandable (payable−payment) — balances computed, never typed.
- "Add entry" control: choose Payable (references a Disbursement Voucher no.) or Payment
  (payment instrument no. — **BUR: RCI/RADAI/RTRAI**, **OBR: Check/ADA/TRA**), enter
  `entryDate`, `referenceNo`, amount; appended via `updateDocument`, status re-derived.

## List pages: `src/pages/pre/Burs.tsx` & `src/pages/pre/Obrs.tsx` (extend)
- Read from `useDocuments("utilization")` / `useDocuments("obligation")`.
- When empty → keep the existing `EmptyState`. When populated → `Table` with columns
  Serial, Date, Payee / Office, Total, Status (`StatusBadge`), actions (View → detail modal).
- Keep existing `SearchInput` (filter serial/payee/object codes across lines) and status
  `Select`; add `Pagination` (existing component) for long lists.
- "New" opens the create Modal (enhanced `ObligationForm`); footer Save calls
  `createDocument` then toasts. The two pages differ only in `kind`, labels, and instruments.

## Verification
1. `npx tsc --noEmit` and `npx vite build` — both clean.
2. Manual (preview): BURS → New BUR → pick RC by acronym (confirm RC code/name + MFO/PAP
   auto-fill), pick UACS (confirm Particulars auto-fill + class), enter amounts across two
   lines (confirm total), pick both certifiers (confirm dates stamp), Save.
3. Confirm the BUR appears in the list with a `BUR-2026-0001` serial and derived status;
   **reload the page** and confirm it persists (localStorage).
4. Open detail → confirm one obligation ledger entry equals the total; add a Payable
   (DV no.) and a Payment (RCI/RADAI/RTRAI) → confirm Not-Yet-Due and Due-&-Demandable
   balances and status update.
5. Repeat New OBR on OBRS → confirm `OBR-2026-0001`, "Obligated" wording, and payment
   instruments show Check/ADA/TRA.
