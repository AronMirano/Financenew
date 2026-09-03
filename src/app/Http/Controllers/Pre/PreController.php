<?php

namespace App\Http\Controllers\Pre;

use App\Http\Controllers\Controller;
use App\Models\DocumentLine;
use App\Support\Reference;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * The remaining PRE pages: overview, Submit PRE, PPMP import,
 * expenditures, reports and analytics.
 */
class PreController extends Controller
{
    /** DBM budget accountability reports (FARs / BARs). */
    private const REPORTS = [
        ['name' => 'Statement of Allotments, Obligations and Balances (SAOB)', 'code' => 'FAR No. 1', 'desc' => 'Per program and object of expenditure.'],
        ['name' => 'Summary of Appropriations, Allotments, Obligations', 'code' => 'FAR No. 1-A', 'desc' => 'Consolidated across all responsibility centers.'],
        ['name' => 'List of Allotments and Sub-Allotments', 'code' => 'FAR No. 1-B', 'desc' => 'Released allotments per fund cluster.'],
        ['name' => 'Monthly Report of Disbursements', 'code' => 'FAR No. 4', 'desc' => 'Disbursements by object of expenditure.'],
        ['name' => 'Quarterly Physical Report of Operation', 'code' => 'BAR No. 1', 'desc' => 'Physical accomplishment vs. targets.'],
        ['name' => 'Working Paper — Obligations', 'code' => 'WP', 'desc' => 'Monthly obligation ledger with VLOOKUP mapping.'],
    ];

    private const PERIODS = ['Q1 2026', 'Q2 2026', 'Q3 2026', 'Q4 2026'];

    public function overview(): View
    {
        return view('pre.overview');
    }

    public function submitForm(): View
    {
        return view('pre.submit', [
            'periods' => self::PERIODS,
        ]);
    }

    public function submitStore(Request $request): RedirectResponse
    {
        $rcCodes = array_column(Reference::RC_RECORDS, 'code');
        $objectCodes = array_column(Reference::UACS_RECORDS, 'code');

        $request->validate([
            'rc' => ['required', 'string', 'in:'.implode(',', $rcCodes)],
            'category' => ['required', 'string', 'in:'.implode(',', array_keys(Reference::FUND_CATEGORIES))],
            'period' => ['required', 'string', 'in:'.implode(',', self::PERIODS)],
            'prepared_by' => ['nullable', 'string', 'max:255'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.object_code' => ['nullable', 'string', 'in:'.implode(',', $objectCodes)],
            'items.*.amount' => ['nullable', 'numeric', 'min:0'],
        ]);

        $action = $request->input('action') === 'draft' ? 'draft' : 'submit';

        // The PRE submission pipeline is not wired to storage yet — the
        // prototype only toasted. Preserve that behaviour explicitly.
        return $action === 'draft'
            ? back()->with('status', 'Draft saved')
            : redirect()->route('pre.overview')->with('status', 'PRE submitted for review');
    }

    public function ppmp(): View
    {
        return view('pre.ppmp');
    }

    public function expenditures(Request $request): View
    {
        $cls = $request->query('cls', 'all');
        $cls = array_key_exists((string) $cls, Reference::EXPENSE_CLASSES) ? (string) $cls : 'all';

        // Obligations and utilizations aggregated by UACS object of expenditure.
        $rows = DocumentLine::query()
            ->selectRaw('object_code, SUM(amount) as total, COUNT(*) as line_count')
            ->groupBy('object_code')
            ->orderByDesc('total')
            ->get()
            ->map(fn (DocumentLine $line) => [
                'object_code' => $line->object_code,
                'title' => Reference::lookupUacsTitle($line->object_code),
                'cls' => Reference::uacsClass($line->object_code),
                'total' => (float) $line->total,
                'line_count' => (int) $line->line_count,
            ])
            ->when($cls !== 'all', fn ($rows) => $rows->where('cls', $cls))
            ->values();

        return view('pre.expenditures', [
            'cls' => $cls,
            'rows' => $rows,
        ]);
    }

    public function reports(): View
    {
        return view('pre.reports', ['reports' => self::REPORTS]);
    }

    public function generateReport(Request $request): RedirectResponse
    {
        $codes = array_column(self::REPORTS, 'code');

        $validated = $request->validate([
            'code' => ['required', 'string', 'in:'.implode(',', $codes)],
            'mode' => ['required', 'string', 'in:generate,download'],
        ]);

        $verb = $validated['mode'] === 'download' ? 'downloaded' : 'generated';

        return back()->with('status', "{$validated['code']} {$verb}");
    }

    public function analytics(): View
    {
        return view('pre.analytics');
    }
}
