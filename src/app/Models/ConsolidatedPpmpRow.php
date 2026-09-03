<?php

namespace App\Models;

use App\Support\Format;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Monitoring — consolidated PPMP line item (conso-fidu / conso-nonfidu).
 *
 * These sheets use MOOE (not MODE).
 * Total = PS + MOOE + CO + Contingency + Re-alignment.
 */
class ConsolidatedPpmpRow extends Model
{
    protected $table = 'consolidated_ppmp_rows';

    protected $fillable = [
        'fund_type',
        'ppmp',
        'unit',
        'item',
        'ps',
        'mooe',
        'co',
        'contingency',
        'realignment',
        'request',
    ];

    protected function casts(): array
    {
        return [
            'ps' => 'float',
            'mooe' => 'float',
            'co' => 'float',
            'contingency' => 'float',
            'realignment' => 'float',
        ];
    }

    /** @param  Builder<ConsolidatedPpmpRow>  $query */
    public function scopeOfFundType(Builder $query, string $fundType): void
    {
        $query->where('fund_type', $fundType);
    }

    public function total(): float
    {
        return Format::consoTotal(
            (float) $this->ps,
            (float) $this->mooe,
            (float) $this->co,
            (float) $this->contingency,
            (float) $this->realignment,
        );
    }
}
