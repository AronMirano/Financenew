<?php

namespace App\Http\Controllers\Pre;

use App\Http\Controllers\Controller;
use App\Models\SaobLine;
use App\Support\Format;
use App\Support\SaobSheets;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * SAOB — Statement of Allotments, Obligations and Balances.
 *
 * Adjusted Appropriations = 1±3±4 · Adjusted Allotment = 2±3±4 ·
 * Unreleased = 5−6 · Unobligated = 6−8 · Disbursements = 8−10.
 */
class SaobController extends Controller
{
    public function __invoke(Request $request): View
    {
        $banner = $request->query('banner') === 'continuing' ? 'continuing' : 'current';
        $sheet = SaobSheets::find($request->query('sheet', SaobSheets::SHEETS[0]['key']));

        $lines = SaobLine::query()
            ->forSheet($sheet['key'], $banner)
            ->orderBy('cls')
            ->orderBy('id')
            ->get();

        $computed = $lines->map(fn (SaobLine $line) => [
            'line' => $line,
            'row' => $line->computed(),
        ])->all();

        return view('pre.saob', [
            'sheets' => SaobSheets::SHEETS,
            'columns' => SaobSheets::COLUMNS,
            'sheet' => $sheet,
            'banner' => $banner,
            'rows' => $computed,
            'totals' => Format::sumSaobRows(array_column($computed, 'row')),
        ]);
    }
}
