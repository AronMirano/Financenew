<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Annualization — per-employee salary record.
 *
 * Annualized salary is the full-year equivalent (12 months), regardless of
 * months actually served. Earned-to-date = monthly basic × months served.
 */
class SalaryRecord extends Model
{
    protected $fillable = [
        'employee_id',
        'name',
        'position',
        'office',
        'sg_step',
        'monthly_basic',
        'months_served',
        'gsis_ps',
        'pagibig',
        'philhealth',
        'withholding_tax',
    ];

    protected function casts(): array
    {
        return [
            'monthly_basic' => 'float',
            'months_served' => 'integer',
            'gsis_ps' => 'float',
            'pagibig' => 'float',
            'philhealth' => 'float',
            'withholding_tax' => 'float',
        ];
    }

    public function monthlyDeductions(): float
    {
        return (float) $this->gsis_ps + (float) $this->pagibig
            + (float) $this->philhealth + (float) $this->withholding_tax;
    }

    /**
     * The full annualization schedule for this employee.
     *
     * @return array<string, float>
     */
    public function annualize(): array
    {
        $monthlyDeductions = $this->monthlyDeductions();
        $annualBasic = (float) $this->monthly_basic * 12;
        $earnedBasic = (float) $this->monthly_basic * (int) $this->months_served;
        $annualDeductions = $monthlyDeductions * 12;
        $earnedDeductions = $monthlyDeductions * (int) $this->months_served;

        return [
            'monthlyDeductions' => $monthlyDeductions,
            'annualBasic' => $annualBasic,
            'earnedBasic' => $earnedBasic,
            'annualDeductions' => $annualDeductions,
            'earnedDeductions' => $earnedDeductions,
            'annualNet' => $annualBasic - $annualDeductions,
            'earnedNet' => $earnedBasic - $earnedDeductions,
        ];
    }
}
