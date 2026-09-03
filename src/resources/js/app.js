// Progressive enhancement only — every page works without JavaScript.
// The sidebar drawer, the SAOB/monitoring tab links and the ledger's
// entry-type switch are the only interactive pieces.

// ---- Mobile sidebar drawer -------------------------------------------
const sidebar = document.querySelector('[data-sidebar]');
const scrim = document.querySelector('[data-sidebar-scrim]');

function setSidebar(open) {
    if (!sidebar || !scrim) return;
    sidebar.classList.toggle('-translate-x-full', !open);
    sidebar.classList.toggle('translate-x-0', open);
    scrim.hidden = !open;
}

document.querySelector('[data-sidebar-open]')?.addEventListener('click', () => setSidebar(true));
scrim?.addEventListener('click', () => setSidebar(false));
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') setSidebar(false);
});

// ---- Toast auto-dismiss ----------------------------------------------
document.querySelectorAll('[data-toast]').forEach((toast) => {
    setTimeout(() => toast.remove(), 3200);
});

// ---- Ledger entry form: swap DV / instrument fields ------------------
const ledgerKind = document.querySelector('[data-ledger-kind]');
if (ledgerKind) {
    const sync = () => {
        const payment = ledgerKind.value === 'payment';
        document.querySelectorAll('[data-when-payable]').forEach((el) => {
            el.hidden = payment;
        });
        document.querySelectorAll('[data-when-payment]').forEach((el) => {
            el.hidden = !payment;
        });
    };
    ledgerKind.addEventListener('change', sync);
    sync();
}

// ---- Obligation form: auto-fill Particulars from the object code -----
document.querySelectorAll('[data-object-code]').forEach((select) => {
    select.addEventListener('change', () => {
        const row = select.closest('[data-line-row]');
        const particulars = row?.querySelector('[data-particulars]');
        const title = select.selectedOptions[0]?.dataset.title ?? '';
        if (particulars && !particulars.dataset.touched) {
            particulars.value = title;
        }
        const badge = row?.querySelector('[data-class-badge]');
        if (badge) {
            const cls = select.selectedOptions[0]?.dataset.cls ?? '';
            badge.textContent = cls || '—';
        }
    });
});

document.querySelectorAll('[data-particulars]').forEach((input) => {
    input.addEventListener('input', () => {
        input.dataset.touched = '1';
    });
});

// ---- Obligation form: add / remove Particulars lines -----------------
const lineBody = document.querySelector('[data-line-body]');
const lineTemplate = document.querySelector('[data-line-template]');

document.querySelector('[data-add-line]')?.addEventListener('click', () => {
    if (!lineBody || !lineTemplate) return;
    const index = lineBody.querySelectorAll('[data-line-row]').length;
    const html = lineTemplate.innerHTML.replaceAll('__INDEX__', String(index));
    lineBody.insertAdjacentHTML('beforeend', html);
});

lineBody?.addEventListener('click', (e) => {
    const button = e.target.closest('[data-remove-line]');
    if (!button) return;
    const rows = lineBody.querySelectorAll('[data-line-row]');
    if (rows.length > 1) button.closest('[data-line-row]')?.remove();
});
