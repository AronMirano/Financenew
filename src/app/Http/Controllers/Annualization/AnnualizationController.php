<?php

namespace App\Http\Controllers\Annualization;

use App\Http\Controllers\Controller;
use App\Models\SalaryRecord;
use App\Support\Format;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Annualization — full-year projection of personnel services.
 *
 * Annualized salary is the full-year equivalent (12 months), regardless of
 * months actually served; earned-to-date = monthly basic × months served.
 */
class AnnualizationController extends Controller
{
    private const REPORTS = [
        ['name' => 'Annualized Personnel Services Schedule', 'code' => 'PS-1', 'desc' => 'Full-year basic salary per employee.'],
        ['name' => 'Statement of Mandatory Deductions', 'code' => 'PS-2', 'desc' => 'GSIS, Pag-IBIG, PhilHealth, BIR remittances.'],
        ['name' => 'Plantilla of Personnel', 'code' => 'BP Form 201', 'desc' => 'Positions, salary grades, and rates.'],
        ['name' => 'Personnel Services Itemization', 'code' => 'PSIPOP', 'desc' => 'Itemized PS and plantilla of positions.'],
    ];

    public function overview(): View
    {
        $records = SalaryRecord::query()->get();

        return view('annualization.overview', [
            'count' => $records->count(),
            'annualBasic' => Format::sum($records->map(fn ($r) => $r->annualize()['annualBasic'])),
            'annualDeductions' => Format::sum($records->map(fn ($r) => $r->annualize()['annualDeductions'])),
            'annualNet' => Format::sum($records->map(fn ($r) => $r->annualize()['annualNet'])),
        ]);
    }

    public function import(): View
    {
        return view('annualization.import');
    }

    public function records(Request $request): View
    {
        $query = trim((string) $request->query('q', ''));
        $office = (string) $request->query('office', 'all');

        $records = SalaryRecord::query()
            ->when($office !== 'all', fn ($q) => $q->where('office', $office))
            ->when($query !== '', function ($q) use ($query) {
                $like = '%'.$query.'%';
                $q->where(fn ($q) => $q->where('name', 'like', $like)->orWhere('employee_id', 'like', $like));
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('annualization.records', [
            'records' => $records,
            'query' => $query,
            'office' => $office,
            'offices' => SalaryRecord::query()->whereNotNull('office')->distinct()->orderBy('office')->pluck('office'),
        ]);
    }

    public function employee(?SalaryRecord $record = null): View
    {
        return view('annualization.employee', [
            'record' => $record,
            'schedule' => $record?->annualize(),
            'roster' => SalaryRecord::query()->orderBy('name')->get(['id', 'employee_id', 'name']),
        ]);
    }

    public function deductions(): View
    {
        $records = SalaryRecord::query()->get();

        // Annualized mandatory deductions remitted to GSIS, Pag-IBIG,
        // PhilHealth and BIR.
        $totals = [
            'gsis_ps' => Format::sum($records->pluck('gsis_ps')) * 12,
            'pagibig' => Format::sum($records->pluck('pagibig')) * 12,
            'philhealth' => Format::sum($records->pluck('philhealth')) * 12,
            'withholding_tax' => Format::sum($records->pluck('withholding_tax')) * 12,
        ];

        return view('annualization.deductions', [
            'records' => $records,
            'totals' => $totals,
            'grandTotal' => array_sum($totals),
        ]);
    }

    public function reports(): View
    {
        return view('annualization.reports', ['reports' => self::REPORTS]);
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
        return view('annualization.analytics', [
            'byOffice' => SalaryRecord::query()
                ->selectRaw('office, COUNT(*) as headcount, SUM(monthly_basic) * 12 as annual_basic')
                ->groupBy('office')
                ->orderByDesc('annual_basic')
                ->get(),
        ]);
    }
}
