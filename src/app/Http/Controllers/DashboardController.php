<?php

namespace App\Http\Controllers;

use App\Models\FinDocument;
use App\Models\SalaryRecord;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('dashboard', [
            'hasData' => FinDocument::query()->exists() || SalaryRecord::query()->exists(),
            'obligationCount' => FinDocument::query()->ofKind('obligation')->count(),
            'utilizationCount' => FinDocument::query()->ofKind('utilization')->count(),
            'salaryCount' => SalaryRecord::query()->count(),
        ]);
    }
}
