<?php

namespace App\Http\Controllers\Pre;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreObligationRequest;
use App\Models\FinDocument;
use App\Support\Format;
use App\Support\Reference;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * OBRS (Obligation Request and Status, Appendix 11) and
 * BURS (Budget Utilization Request and Status, Appendix 14).
 *
 * Both share this controller — the route's `kind` default selects which.
 */
class ObligationController extends Controller
{
    private const PAGE_SIZE = 10;

    /**
     * Per-kind presentation config — the PHP equivalent of the prototype's
     * ListConfig object.
     *
     * @var array<string, array<string, mixed>>
     */
    private const CONFIG = [
        'obligation' => [
            'kind' => 'obligation',
            'title' => 'OBRS',
            'description' => 'Obligation Request and Status (Appendix 11). Records obligations against allotments per responsibility center and UACS object code.',
            'icon' => 'receipt',
            'newLabel' => 'New OBR',
            'noun' => 'OBR',
            'fullName' => 'New Obligation Request and Status',
            'statuses' => ['Draft', 'Certified', 'Obligated', 'Paid'],
            'routePrefix' => 'pre.obrs',
            'certBNote' => 'Allotment available and obligated for the purpose.',
        ],
        'utilization' => [
            'kind' => 'utilization',
            'title' => 'BURS',
            'description' => 'Budget Utilization Request and Status (Appendix 14). Records fund utilizations for internally-generated and trust funds.',
            'icon' => 'file-text',
            'newLabel' => 'New BUR',
            'noun' => 'BUR',
            'fullName' => 'New Budget Utilization Request and Status',
            'statuses' => ['Draft', 'Certified', 'Utilized', 'Paid'],
            'routePrefix' => 'pre.burs',
            'certBNote' => 'Funds available and utilization recorded.',
        ],
    ];

    public function index(Request $request, string $kind): View
    {
        $cfg = $this->config($kind);
        $query = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', 'all');

        $documents = $this->filtered($kind, $query, $status)
            ->paginate(self::PAGE_SIZE)
            ->withQueryString();

        return view('pre.obligations.index', [
            'cfg' => $cfg,
            'documents' => $documents,
            'query' => $query,
            'status' => $status,
            'totalOfKind' => FinDocument::query()->ofKind($kind)->count(),
        ]);
    }

    public function create(string $kind): View
    {
        $cfg = $this->config($kind);

        return view('pre.obligations.create', [
            'cfg' => $cfg,
            'serialPreview' => FinDocument::nextSerial($kind),
            'defaultFundCluster' => Reference::defaultFundCluster($kind),
            'knownPayees' => FinDocument::knownPayees(),
            // Certification B is pre-stamped with the signed-in budget officer,
            // matching the prototype's default.
            'defaultCertB' => 'p-austria',
        ]);
    }

    public function store(StoreObligationRequest $request, string $kind): RedirectResponse
    {
        $cfg = $this->config($kind);
        $lines = $request->completeLines();
        $total = Format::sum(array_column($lines, 'amount'));

        $document = DB::transaction(function () use ($request, $kind, $lines, $total): FinDocument {
            $today = Format::longDate();

            $document = FinDocument::create([
                'serial' => FinDocument::nextSerial($kind),
                'kind' => $kind,
                'date' => $request->date('date'),
                'fund_cluster' => $request->string('fund_cluster')->toString(),
                'payee_name' => $request->string('payee_name')->toString(),
                'office' => $request->string('office')->toString() ?: null,
                'address' => $request->string('address')->toString() ?: null,
                'cert_a_officer_id' => $request->input('cert_a_officer_id') ?: null,
                'cert_a_date' => $request->input('cert_a_officer_id') ? $today : null,
                'cert_b_officer_id' => $request->input('cert_b_officer_id') ?: null,
                'cert_b_date' => $request->input('cert_b_officer_id') ? $today : null,
                'status' => 'Draft',
            ]);

            $document->lines()->createMany($lines);

            // The Status of Obligation ledger starts with one obligation entry
            // equal to the document total (Appendix 11 / 14, box "a").
            $document->ledgerEntries()->create([
                'kind' => 'obligation',
                'entry_date' => $request->date('date'),
                'reference_no' => $document->serial,
                'amount' => $total,
            ]);

            $document->refreshStatus();

            return $document;
        });

        return redirect()
            ->route($cfg['routePrefix'].'.show', $document)
            ->with('status', "{$cfg['noun']} {$document->serial} saved");
    }

    public function show(FinDocument $document, string $kind): View
    {
        $cfg = $this->config($kind);

        // Keep OBRS and BURS from rendering each other's records.
        abort_unless($document->kind === $kind, 404);

        $document->load(['lines', 'ledgerEntries']);

        return view('pre.obligations.show', [
            'cfg' => $cfg,
            'document' => $document,
            'instruments' => Reference::paymentInstruments($kind),
            'ledgerRows' => $this->runningLedger($document),
        ]);
    }

    /**
     * Running balances after each entry, in chronological order — the same
     * accumulation the prototype did while rendering.
     *
     * @return list<array{entry: \App\Models\LedgerEntry, notYetDue: float, dueDemandable: float}>
     */
    private function runningLedger(FinDocument $document): array
    {
        $obl = 0.0;
        $pay = 0.0;
        $paid = 0.0;
        $rows = [];

        foreach ($document->ledgerEntries as $entry) {
            match ($entry->kind) {
                'obligation' => $obl += (float) $entry->amount,
                'payable' => $pay += (float) $entry->amount,
                'payment' => $paid += (float) $entry->amount,
                default => null,
            };

            $rows[] = [
                'entry' => $entry,
                'notYetDue' => $obl - $pay,
                'dueDemandable' => $pay - $paid,
            ];
        }

        return $rows;
    }

    /** @return \Illuminate\Database\Eloquent\Builder<FinDocument> */
    private function filtered(string $kind, string $query, string $status)
    {
        return FinDocument::query()
            ->ofKind($kind)
            ->with(['lines', 'ledgerEntries'])
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->when($query !== '', function ($q) use ($query) {
                $like = '%'.$query.'%';
                $q->where(function ($q) use ($like) {
                    $q->where('serial', 'like', $like)
                        ->orWhere('payee_name', 'like', $like)
                        ->orWhere('office', 'like', $like)
                        ->orWhereHas('lines', fn ($l) => $l
                            ->where('object_code', 'like', $like)
                            ->orWhere('rc_acronym', 'like', $like));
                });
            })
            ->latest('id');
    }

    /** @return array<string, mixed> */
    private function config(string $kind): array
    {
        return self::CONFIG[$kind] ?? throw new NotFoundHttpException;
    }
}
