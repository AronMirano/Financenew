<?php

namespace App\Models;

use App\Support\Format;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * One SAOB line — per allotment class / object of expenditure, within a
 * program sheet and an appropriations banner (current / continuing).
 *
 * Only the six input columns are stored; columns 5, 6, 7, 9 and 11 are
 * always computed by Format::computeSaobRow so the workbook formulas
 * remain the single source of truth.
 */
class SaobLine extends Model
{
    protected $fillable = [
        'sheet_key',
        'banner',
        'label',
        'cls',
        'object_code',
        'authorized_appropriations',
        'allotment_received',
        'augmentations',
        'modifications',
        'obligations_incurred',
        'unpaid_obligations',
    ];

    protected function casts(): array
    {
        return [
            'authorized_appropriations' => 'float',
            'allotment_received' => 'float',
            'augmentations' => 'float',
            'modifications' => 'float',
            'obligations_incurred' => 'float',
            'unpaid_obligations' => 'float',
        ];
    }

    /** @param  Builder<SaobLine>  $query */
    public function scopeForSheet(Builder $query, string $sheetKey, string $banner): void
    {
        $query->where('sheet_key', $sheetKey)->where('banner', $banner);
    }

    /**
     * The 11-column computed row for this line.
     *
     * @return array<string, float>
     */
    public function computed(): array
    {
        return Format::computeSaobRow($this->inputs());
    }

    /** @return array<string, float> */
    public function inputs(): array
    {
        return [
            'authorizedAppropriations' => (float) $this->authorized_appropriations,
            'allotmentReceived' => (float) $this->allotment_received,
            'augmentations' => (float) $this->augmentations,
            'modifications' => (float) $this->modifications,
            'obligationsIncurred' => (float) $this->obligations_incurred,
            'unpaidObligations' => (float) $this->unpaid_obligations,
        ];
    }
}
