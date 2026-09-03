<?php

namespace App\Models;

use App\Support\Format;
use Illuminate\Database\Eloquent\Model;

/**
 * Monitoring — Utilization of Fund row.
 *
 * Column terminology is verbatim from the source workbook: this sheet uses
 * MODE (not MOOE). Utilization TOTAL = PS + MODE + CO.
 */
class UtilizationRow extends Model
{
    protected $fillable = [
        'particulars',
        'section',
        'balance_2022',
        'receipts_fhe',
        'collections_2023',
        'pre',
        'carry_over_2022',
        'util_ps',
        'util_mode',
        'util_co',
        'disb_ps',
        'disb_mode',
        'disb_co',
    ];

    protected function casts(): array
    {
        return [
            'balance_2022' => 'float',
            'receipts_fhe' => 'float',
            'collections_2023' => 'float',
            'pre' => 'float',
            'carry_over_2022' => 'float',
            'util_ps' => 'float',
            'util_mode' => 'float',
            'util_co' => 'float',
            'disb_ps' => 'float',
            'disb_mode' => 'float',
            'disb_co' => 'float',
        ];
    }

    public function utilizationTotal(): float
    {
        return Format::triTotal((float) $this->util_ps, (float) $this->util_mode, (float) $this->util_co);
    }

    public function disbursementTotal(): float
    {
        return Format::triTotal((float) $this->disb_ps, (float) $this->disb_mode, (float) $this->disb_co);
    }
}
