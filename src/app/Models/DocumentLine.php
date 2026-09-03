<?php

namespace App\Models;

use App\Support\Reference;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One Particulars line on an OBR / BUR: RC · UACS object code · amount.
 */
class DocumentLine extends Model
{
    protected $fillable = [
        'fin_document_id',
        'rc_acronym',
        'rc_code',
        'object_code',
        'particulars',
        'amount',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'float',
        ];
    }

    /** @return BelongsTo<FinDocument, $this> */
    public function document(): BelongsTo
    {
        return $this->belongsTo(FinDocument::class, 'fin_document_id');
    }

    /** Account Title = VLOOKUP(Object Code, UACS!A:B, 2, FALSE) */
    public function accountTitle(): string
    {
        return Reference::lookupUacsTitle($this->object_code);
    }

    public function expenseClass(): ?string
    {
        return Reference::uacsClass($this->object_code);
    }

    /** @return array{code: string, name: string, category: string}|null */
    public function mfoPap(): ?array
    {
        return Reference::mfoPapForRcAcronym($this->rc_acronym);
    }
}
