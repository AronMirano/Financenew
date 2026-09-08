<?php

namespace App\Http\Controllers\Pre;

use App\Http\Controllers\Controller;
use App\Models\FinDocument;
use App\Support\Format;
use App\Support\Reference;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Adds a payable (b) or payment (c) row to a document's Status of
 * Obligation / Utilization ledger. Balances are never typed — they are
 * recomputed from the ledger on every render.
 */
class LedgerEntryController extends Controller
{
public function __invoke(Request $request, FinDocument $document): RedirectResponse
    {
        // Route model binding may not load all columns; explicitly reload with kind
        $document = $document->fresh(['kind']);

        $instruments = Reference::paymentInstruments($document->kind);

        $validated = $request->validate([
            'kind' => ['required', Rule::in(['payable', 'payment'])],
            'entry_date' => ['required', 'date'],
            'reference_no' => ['required', 'string', 'max:255'],
            'instrument' => ['required_if:kind,payment', 'nullable', Rule::in($instruments)],
            'amount' => ['required', 'numeric', 'gt:0'],
        ]);

        // Payable references a Disbursement Voucher; Payment references a
        // Check / ADA / TRA (OBR) or RCI / RADAI / RTRAI (BUR) instrument.
        $reference = $validated['kind'] === 'payment'
            ? $validated['instrument'].' '.trim($validated['reference_no'])
            : 'DV '.trim($validated['reference_no']);

        $document->ledgerEntries()->create([
            'kind' => $validated['kind'],
            'entry_date' => $validated['entry_date'],
            'reference_no' => $reference,
            'amount' => (float) $validated['amount'],
        ]);

        $document->refreshStatus();

        $route = $document->kind === 'utilization' ? 'pre.burs.show' : 'pre.obrs.show';

        return redirect()
            ->route($route, $document)
            ->with('status', ucfirst($validated['kind']).' entry of '.Format::peso($validated['amount']).' recorded');
    }
}
