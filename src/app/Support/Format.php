<?php

namespace App\Support;

use DateTimeInterface;

/**
 * Formatting + calculation helpers.
 * Calculation helpers preserve the EXACT workbook formulas so the
 * application stays faithful to the source-of-truth spreadsheets.
 */
class Format
{
    /** Philippine peso, e.g. ₱1,250,000.00 */
    public static function peso(int|float|null $n): string
    {
        if ($n === null || is_nan((float) $n)) {
            return '₱0.00';
        }

        $sign = $n < 0 ? '-' : '';

        return $sign.'₱'.number_format(abs((float) $n), 2, '.', ',');
    }

    /** Bare amount without the symbol, e.g. 1,250,000.00. Blank cells show a dash. */
    public static function amt(int|float|null $n): string
    {
        if ($n === null || is_nan((float) $n) || (float) $n === 0.0) {
            return '—';
        }

        return number_format((float) $n, 2, '.', ',');
    }

    public static function pct(float $n, int $digits = 1): string
    {
        return number_format($n * 100, $digits).'%';
    }

    /** e.g. "August 18, 2026" — the OBR/BUR Date =TODAY() default. */
    public static function longDate(DateTimeInterface|string|null $d = null): string
    {
        $ts = self::timestamp($d);

        return $ts === null ? '' : date('F j, Y', $ts);
    }

    /** e.g. "Aug 18, 2026" */
    public static function shortDate(DateTimeInterface|string|null $d = null): string
    {
        $ts = self::timestamp($d);

        return $ts === null ? '—' : date('M j, Y', $ts);
    }

    private static function timestamp(DateTimeInterface|string|null $d): ?int
    {
        if ($d === null || $d === '') {
            return time();
        }
        if ($d instanceof DateTimeInterface) {
            return $d->getTimestamp();
        }

        $ts = strtotime($d);

        return $ts === false ? null : $ts;
    }

    // ---------------------------------------------------------------
    // OBR / BUR — Status of Obligation/Utilization (Appendix 11 / 14)
    //   Not Yet Due       = Obligation(a) - Payable(b)
    //   Due & Demandable  = Payable(b)    - Payment(c)
    // ---------------------------------------------------------------

    public static function obrNotYetDue(float $obligation, float $payable): float
    {
        return $obligation - $payable;
    }

    public static function obrDueDemandable(float $payable, float $payment): float
    {
        return $payable - $payment;
    }

    // ---------------------------------------------------------------
    // Working Paper (Jan. sheet)
    //   Unpaid = Amount - Payment  (per Current / Continuing column)
    // ---------------------------------------------------------------

    public static function wpUnpaid(float $amount, float $payment): float
    {
        return $amount - $payment;
    }

    // ---------------------------------------------------------------
    // SAOB 11-column model (per allotment class / object of expenditure)
    //   5  Adjusted Appropriations   = 1 ± 3 ± 4
    //   6  Adjusted Allotment        = 2 ± 3 ± 4
    //   7  Unreleased Appropriations = 5 - 6
    //   9  Unobligated Allotment     = 6 - 8
    //   11 Disbursement              = 8 - 10
    // ---------------------------------------------------------------

    /**
     * @param  array{authorizedAppropriations: float, allotmentReceived: float, augmentations: float, modifications: float, obligationsIncurred: float, unpaidObligations: float}  $i
     * @return array<string, float>
     */
    public static function computeSaobRow(array $i): array
    {
        $adjustedAppropriations = $i['authorizedAppropriations'] + $i['augmentations'] + $i['modifications'];
        $adjustedAllotment = $i['allotmentReceived'] + $i['augmentations'] + $i['modifications'];

        return $i + [
            'adjustedAppropriations' => $adjustedAppropriations,
            'adjustedAllotment' => $adjustedAllotment,
            'unreleasedAppropriations' => $adjustedAppropriations - $adjustedAllotment,
            'unobligatedAllotment' => $adjustedAllotment - $i['obligationsIncurred'],
            'disbursements' => $i['obligationsIncurred'] - $i['unpaidObligations'],
        ];
    }

    /**
     * Column-wise sum of SAOB rows (Sub-total / TOTAL rows).
     *
     * @param  list<array<string, float>>  $rows
     * @return array<string, float>
     */
    public static function sumSaobRows(array $rows): array
    {
        $base = [
            'authorizedAppropriations' => 0.0,
            'allotmentReceived' => 0.0,
            'augmentations' => 0.0,
            'modifications' => 0.0,
            'obligationsIncurred' => 0.0,
            'unpaidObligations' => 0.0,
        ];

        foreach ($rows as $r) {
            foreach (array_keys($base) as $key) {
                $base[$key] += (float) ($r[$key] ?? 0);
            }
        }

        return self::computeSaobRow($base);
    }

    // ---------------------------------------------------------------
    // Monitoring (Utilization of Fund) — column terminology is verbatim:
    //   Utilization TOTAL  = PS + MODE + CO
    //   Disbursement Total = PS + MODE + CO
    // ---------------------------------------------------------------

    public static function triTotal(float $ps, float $mode, float $co): float
    {
        return $ps + $mode + $co;
    }

    /** conso-fidu / conso-nonfidu Total = PS + MOOE + CO + Contingency + Re-alignment */
    public static function consoTotal(float $ps, float $mooe, float $co, float $contingency, float $realignment): float
    {
        return $ps + $mooe + $co + $contingency + $realignment;
    }

    /** @param  iterable<int|float>  $nums */
    public static function sum(iterable $nums): float
    {
        $total = 0.0;
        foreach ($nums as $n) {
            $total += (float) $n;
        }

        return $total;
    }
}
