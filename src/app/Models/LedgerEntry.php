<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One row of the Status of Obligation / Utilization ledger
 * (Appendix 11 / 14, boxes a, b and c).
 *
 * kind = obligation (a) | payable (b) | payment (c)
 */
class LedgerEntry extends Model
{
    protected $fillable = [
        'fin_document_id',
        'kind',
        'entry_date',
        'reference_no',
        'amount',
    ];

    protected function casts(): array
    {
        return [
            'entry_date' => 'date',
            'amount' => 'float',
        ];
    }

    /** @return BelongsTo<FinDocument, $this> */
    public function document(): BelongsTo
    {
        return $this->belongsTo(FinDocument::class, 'fin_document_id');
    }
}
