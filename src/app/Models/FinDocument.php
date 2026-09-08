<?php

namespace App\Models;

use App\Support\Format;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Obligation / Utilization document — the live data-entry layer.
 *
 * An OBR (kind = obligation, Appendix 11) or BUR (kind = utilization,
 * Appendix 14). Records are multi-line and carry a running Status of
 * Obligation ledger; the status is always derived, never typed.
 *
 * @property string $kind
 * @property string $serial
 */
class FinDocument extends Model
{
    /** @use HasFactory<\Database\Factories\FinDocumentFactory> */
    use HasFactory;

    protected $fillable = [
        'serial',
        'kind',
        'date',
        'fund_cluster',
        'payee_name',
        'office',
        'address',
        'allotment_balance',
        'cert_a_officer_id',
        'cert_a_date',
        'cert_b_officer_id',
        'cert_b_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'allotment_balance' => 'float',
        ];
    }

    /** @return HasMany<DocumentLine, $this> */
    public function lines(): HasMany
    {
        return $this->hasMany(DocumentLine::class);
    }

    /** @return HasMany<LedgerEntry, $this> */
    public function ledgerEntries(): HasMany
    {
        return $this->hasMany(LedgerEntry::class)->orderBy('entry_date')->orderBy('id');
    }

    /** @param  Builder<FinDocument>  $query */
    public function scopeOfKind(Builder $query, string $kind): void
    {
        $query->where('kind', $kind);
    }

    // ---- Derived helpers -------------------------------------------------

    public function total(): float
    {
        return Format::sum($this->lines->pluck('amount'));
    }

    public function ledgerSum(string $kind): float
    {
        return Format::sum($this->ledgerEntries->where('kind', $kind)->pluck('amount'));
    }

    /** Running Not-Yet-Due = Σobligation − Σpayable. */
    public function notYetDue(): float
    {
        return Format::obrNotYetDue($this->ledgerSum('obligation'), $this->ledgerSum('payable'));
    }

    /** Running Due & Demandable = Σpayable − Σpayment. */
    public function dueDemandable(): float
    {
        return Format::obrDueDemandable($this->ledgerSum('payable'), $this->ledgerSum('payment'));
    }

    /** Remaining allotment balance = manual allotment balance − total obligations. */
    public function remainingBalance(): float
    {
        return (float) $this->allotment_balance - $this->total();
    }

    /** Status is derived from the certifications and the running ledger. */
    public function deriveStatus(): string
    {
        $total = $this->total();
        $obligated = $this->ledgerSum('obligation');
        $paid = $this->ledgerSum('payment');

        if ($total > 0 && $paid >= $total) {
            return 'Paid';
        }
        if ($obligated > 0) {
            return $this->kind === 'utilization' ? 'Utilized' : 'Obligated';
        }
        if ($this->cert_a_officer_id && $this->cert_b_officer_id) {
            return 'Certified';
        }

        return 'Draft';
    }

    /** Recompute and persist the derived status. */
    public function refreshStatus(): void
    {
        $this->load(['lines', 'ledgerEntries']);
        $this->forceFill(['status' => $this->deriveStatus()])->save();
    }

    /** The label used for the "a" column and the ledger heading. */
    public function referenceLabel(): string
    {
        return $this->kind === 'obligation' ? 'Obligation' : 'Utilization';
    }

    /**
     * Serial generation (per kind + year, auto-incrementing) —
     * OBR-2026-0001 / BUR-2026-0001.
     */
    public static function nextSerial(string $kind): string
    {
        $prefix = $kind === 'utilization' ? 'BUR' : 'OBR';
        $stamp = $prefix.'-'.date('Y').'-';

        $max = static::query()
            ->where('serial', 'like', $stamp.'%')
            ->pluck('serial')
            ->map(fn (string $s): int => (int) substr($s, strlen($stamp)))
            ->max() ?? 0;

        return $stamp.str_pad((string) ($max + 1), 4, '0', STR_PAD_LEFT);
    }

    /** Distinct payee names already used — powers the payee autocomplete. */
    public static function knownPayees(): array
    {
        return static::query()
            ->whereNotNull('payee_name')
            ->where('payee_name', '!=', '')
            ->distinct()
            ->orderBy('payee_name')
            ->pluck('payee_name')
            ->all();
    }
}
