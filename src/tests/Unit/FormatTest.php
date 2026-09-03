<?php

namespace Tests\Unit;

use App\Support\Format;
use PHPUnit\Framework\TestCase;

/**
 * The workbook formulas are the source of truth for this system, so they
 * are pinned here: SAOB columns 5/6/7/9/11, the OBR/BUR status boxes, and
 * the monitoring totals.
 */
class FormatTest extends TestCase
{
    public function test_saob_row_derives_the_five_computed_columns(): void
    {
        $row = Format::computeSaobRow([
            'authorizedAppropriations' => 1_000_000.0, // 1
            'allotmentReceived' => 900_000.0,          // 2
            'augmentations' => 50_000.0,               // 3
            'modifications' => -20_000.0,              // 4
            'obligationsIncurred' => 700_000.0,        // 8
            'unpaidObligations' => 120_000.0,          // 10
        ]);

        // 5 = 1 ± 3 ± 4
        $this->assertSame(1_030_000.0, $row['adjustedAppropriations']);
        // 6 = 2 ± 3 ± 4
        $this->assertSame(930_000.0, $row['adjustedAllotment']);
        // 7 = 5 - 6
        $this->assertSame(100_000.0, $row['unreleasedAppropriations']);
        // 9 = 6 - 8
        $this->assertSame(230_000.0, $row['unobligatedAllotment']);
        // 11 = 8 - 10
        $this->assertSame(580_000.0, $row['disbursements']);
    }

    public function test_saob_totals_sum_column_wise_then_recompute(): void
    {
        $a = Format::computeSaobRow([
            'authorizedAppropriations' => 100.0,
            'allotmentReceived' => 80.0,
            'augmentations' => 10.0,
            'modifications' => 0.0,
            'obligationsIncurred' => 60.0,
            'unpaidObligations' => 10.0,
        ]);
        $b = Format::computeSaobRow([
            'authorizedAppropriations' => 200.0,
            'allotmentReceived' => 150.0,
            'augmentations' => 0.0,
            'modifications' => 5.0,
            'obligationsIncurred' => 100.0,
            'unpaidObligations' => 20.0,
        ]);

        $total = Format::sumSaobRows([$a, $b]);

        $this->assertSame(300.0, $total['authorizedAppropriations']);
        $this->assertSame(230.0, $total['allotmentReceived']);
        // Derived columns recompute from the summed inputs, never from summed outputs.
        $this->assertSame(315.0, $total['adjustedAppropriations']);
        $this->assertSame(245.0, $total['adjustedAllotment']);
        $this->assertSame(70.0, $total['unreleasedAppropriations']);
        $this->assertSame(85.0, $total['unobligatedAllotment']);
        $this->assertSame(130.0, $total['disbursements']);
    }

    public function test_obligation_status_boxes(): void
    {
        // Not Yet Due = Obligation(a) - Payable(b)
        $this->assertSame(400.0, Format::obrNotYetDue(1000.0, 600.0));
        // Due & Demandable = Payable(b) - Payment(c)
        $this->assertSame(150.0, Format::obrDueDemandable(600.0, 450.0));
    }

    public function test_working_paper_unpaid_is_amount_less_payment(): void
    {
        $this->assertSame(250.0, Format::wpUnpaid(1000.0, 750.0));
    }

    public function test_monitoring_totals_use_the_verbatim_column_sets(): void
    {
        // Utilization sheet: TOTAL = PS + MODE + CO
        $this->assertSame(60.0, Format::triTotal(10.0, 20.0, 30.0));
        // Consolidation sheets: Total = PS + MOOE + CO + Contingency + Re-alignment
        $this->assertSame(150.0, Format::consoTotal(10.0, 20.0, 30.0, 40.0, 50.0));
    }

    public function test_peso_and_amt_formatting(): void
    {
        $this->assertSame('₱1,250,000.00', Format::peso(1_250_000));
        $this->assertSame('-₱500.50', Format::peso(-500.5));
        $this->assertSame('₱0.00', Format::peso(null));

        // Blank cells show a dash, matching the workbook.
        $this->assertSame('—', Format::amt(0));
        $this->assertSame('—', Format::amt(null));
        $this->assertSame('1,250,000.00', Format::amt(1_250_000));
    }
}
