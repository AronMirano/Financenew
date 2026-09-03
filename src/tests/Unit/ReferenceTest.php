<?php

namespace Tests\Unit;

use App\Support\Reference;
use PHPUnit\Framework\TestCase;

/**
 * The reference lookups mirror the workbook VLOOKUPs, including the
 * `#N/A` fallback for codes that are not on the reference sheets.
 */
class ReferenceTest extends TestCase
{
    public function test_rc_name_lookup_matches_the_rc_sheet(): void
    {
        $this->assertSame('College of Engineering', Reference::lookupRcName('01-01-13-07'));
        $this->assertSame('#N/A', Reference::lookupRcName('99-99-99'));
    }

    public function test_account_title_lookup_matches_the_uacs_sheet(): void
    {
        $this->assertSame('Office Supplies Expenses', Reference::lookupUacsTitle('5020301002'));
        $this->assertSame('#N/A', Reference::lookupUacsTitle(''));
    }

    public function test_object_codes_carry_their_allotment_class(): void
    {
        $this->assertSame('PS', Reference::uacsClass('5010101001'));
        $this->assertSame('MOOE', Reference::uacsClass('5020402000'));
        $this->assertSame('CO', Reference::uacsClass('5060405002'));
        $this->assertNull(Reference::uacsClass('0000000000'));
    }

    public function test_mfo_pap_resolves_through_the_rc_category(): void
    {
        // COE is a 301 (Higher Education) responsibility center.
        $mfo = Reference::mfoPapForRcAcronym('COE');

        $this->assertNotNull($mfo);
        $this->assertSame('310100100001000', $mfo['code']);
        $this->assertNull(Reference::mfoPapForRcAcronym('NOT-AN-RC'));
    }

    public function test_default_fund_cluster_differs_per_document_type(): void
    {
        $this->assertSame('01-1-01-101', Reference::defaultFundCluster('obligation'));
        $this->assertSame('05206441', Reference::defaultFundCluster('utilization'));
    }

    public function test_payment_instruments_differ_per_document_type(): void
    {
        $this->assertSame(['Check', 'ADA', 'TRA'], Reference::paymentInstruments('obligation'));
        $this->assertSame(['RCI', 'RADAI', 'RTRAI'], Reference::paymentInstruments('utilization'));
    }
}
