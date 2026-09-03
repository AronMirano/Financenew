<?php

namespace App\Models;

use App\Support\Format;
use Illuminate\Database\Eloquent\Model;

/**
 * Working Paper (Jan. sheet) row.
 *
 * RC Name and Account Title are resolved by VLOOKUP against the RC and UACS
 * reference sheets; Unpaid = Amount − Payment per Current / Continuing column.
 */
class WorkingPaperRow extends Model
{
    protected $fillable = [
        'month',
        'obr_date',
        'obr_no',
        'payee',
        'particulars',
        'mfo',
        'rc_code',
        'object_code',
        'amount_current',
        'amount_continuing',
        'payment',
        'payment_date',
        'dv_no',
        'check_no',
    ];

    protected function casts(): array
    {
        return [
            'obr_date' => 'date',
            'payment_date' => 'date',
            'amount_current' => 'float',
            'amount_continuing' => 'float',
            'payment' => 'float',
        ];
    }

    public function unpaidCurrent(): float
    {
        return Format::wpUnpaid((float) $this->amount_current, (float) $this->payment);
    }

    public function unpaidContinuing(): float
    {
        return Format::wpUnpaid((float) $this->amount_continuing, (float) $this->payment);
    }
}
