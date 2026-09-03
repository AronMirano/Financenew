<?php

namespace App\Http\Controllers\Pre;

use App\Http\Controllers\Controller;
use App\Models\ConsolidatedPpmpRow;
use App\Models\UtilizationRow;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Monitoring — utilization of funds and consolidated PPMP tracking.
 *
 * Column terminology follows the source workbooks verbatim: the Utilization
 * sheet uses MODE, the consolidation sheets use MOOE.
 */
class MonitoringController extends Controller
{
    private const TABS = [
        ['id' => 'utilization', 'label' => 'Utilization of Fund'],
        ['id' => 'fidu', 'label' => 'conso-fidu'],
        ['id' => 'nonfidu', 'label' => 'conso-nonfidu'],
    ];

    public function __invoke(Request $request): View
    {
        $tab = in_array($request->query('tab'), ['utilization', 'fidu', 'nonfidu'], true)
            ? (string) $request->query('tab')
            : 'utilization';

        $utilizationRows = $tab === 'utilization'
            ? UtilizationRow::query()->orderBy('id')->get()
            : collect();

        $consoRows = $tab === 'utilization'
            ? collect()
            : ConsolidatedPpmpRow::query()->ofFundType($tab)->orderBy('id')->get();

        return view('pre.monitoring', [
            'tabs' => self::TABS,
            'tab' => $tab,
            'kindLabel' => $tab === 'fidu' ? 'Fiduciary' : 'Non-Fiduciary',
            'utilizationRows' => $utilizationRows,
            'consoRows' => $consoRows,
        ]);
    }
}
