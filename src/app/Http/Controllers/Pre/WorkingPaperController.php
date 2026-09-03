<?php

namespace App\Http\Controllers\Pre;

use App\Http\Controllers\Controller;
use App\Models\WorkingPaperRow;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Working Paper — monthly obligation ledger.
 *
 * RC Name and Account Title are resolved by VLOOKUP against the RC and
 * UACS reference sheets; Unpaid = Amount − Payment.
 */
class WorkingPaperController extends Controller
{
    private const MONTHS = ['Jan.', 'Feb.', 'Mar.', 'Apr.', 'May', 'Jun.'];

    public function __invoke(Request $request): View
    {
        $month = in_array($request->query('month'), self::MONTHS, true)
            ? (string) $request->query('month')
            : self::MONTHS[0];
        $query = trim((string) $request->query('q', ''));

        $rows = WorkingPaperRow::query()
            ->where('month', $month)
            ->when($query !== '', function ($q) use ($query) {
                $like = '%'.$query.'%';
                $q->where(fn ($q) => $q
                    ->where('obr_no', 'like', $like)
                    ->orWhere('payee', 'like', $like)
                    ->orWhere('object_code', 'like', $like));
            })
            ->orderBy('obr_date')
            ->orderBy('id')
            ->get();

        return view('pre.working-paper', [
            'months' => self::MONTHS,
            'month' => $month,
            'query' => $query,
            'rows' => $rows,
        ]);
    }
}
