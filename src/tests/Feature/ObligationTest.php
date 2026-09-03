<?php

namespace Tests\Feature;

use App\Models\FinDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * End-to-end coverage of the OBR / BUR lifecycle: creating a document seeds
 * the ledger with the total obligation, and the derived status advances as
 * payable and payment entries land.
 */
class ObligationTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsOfficer(): User
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        return $user;
    }

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $this->get('/pre/obrs')->assertRedirect('/login');
    }

    public function test_an_obr_can_be_created_and_seeds_its_ledger(): void
    {
        $this->actingAsOfficer();

        $response = $this->post('/pre/obrs', [
            'date' => '2026-08-18',
            'fund_cluster' => '01-1-01-101',
            'payee_name' => 'Acme Supplies Inc.',
            'office' => 'Budget Section',
            'address' => 'Batac City',
            'lines' => [
                ['rc_acronym' => 'COE', 'object_code' => '5020301002', 'particulars' => '', 'amount' => '25000'],
                ['rc_acronym' => 'CAS', 'object_code' => '5020402000', 'particulars' => '', 'amount' => '15000'],
            ],
            'cert_a_officer_id' => 'p-ramos',
            'cert_b_officer_id' => 'p-austria',
        ]);

        $document = FinDocument::query()->firstOrFail();
        $response->assertRedirect(route('pre.obrs.show', $document));

        $this->assertSame('OBR-'.date('Y').'-0001', $document->serial);
        $this->assertSame('obligation', $document->kind);
        $this->assertCount(2, $document->lines);
        $this->assertSame(40000.0, $document->total());

        // Particulars falls back to the UACS account title when left blank.
        $this->assertSame('Office Supplies Expenses', $document->lines->first()->particulars);

        // The ledger opens with one obligation entry equal to the total.
        $this->assertCount(1, $document->ledgerEntries);
        $this->assertSame(40000.0, $document->ledgerSum('obligation'));
        $this->assertSame(40000.0, $document->notYetDue());
        $this->assertSame('Obligated', $document->status);
    }

    public function test_a_bur_gets_its_own_serial_sequence(): void
    {
        $this->actingAsOfficer();

        $this->post('/pre/burs', [
            'date' => '2026-08-18',
            'fund_cluster' => '05206441',
            'payee_name' => 'MMSU IGP',
            'lines' => [
                ['rc_acronym' => 'BD', 'object_code' => '5021601000', 'amount' => '5000'],
            ],
        ]);

        $document = FinDocument::query()->firstOrFail();
        $this->assertSame('BUR-'.date('Y').'-0001', $document->serial);
        $this->assertSame('Utilized', $document->status);
    }

    public function test_a_document_without_a_complete_line_is_rejected(): void
    {
        $this->actingAsOfficer();

        $this->post('/pre/obrs', [
            'date' => '2026-08-18',
            'fund_cluster' => '01-1-01-101',
            'payee_name' => 'Acme Supplies Inc.',
            // No object code and a zero amount — not persistable.
            'lines' => [['rc_acronym' => 'COE', 'object_code' => '', 'amount' => '0']],
        ])->assertSessionHasErrors('lines');

        $this->assertSame(0, FinDocument::query()->count());
    }

    public function test_ledger_entries_advance_the_derived_status_to_paid(): void
    {
        $this->actingAsOfficer();

        $this->post('/pre/obrs', [
            'date' => '2026-08-18',
            'fund_cluster' => '01-1-01-101',
            'payee_name' => 'Acme Supplies Inc.',
            'lines' => [['rc_acronym' => 'COE', 'object_code' => '5020301002', 'amount' => '1000']],
        ]);

        $document = FinDocument::query()->firstOrFail();

        $this->post(route('pre.ledger.store', $document), [
            'kind' => 'payable',
            'entry_date' => '2026-08-20',
            'reference_no' => '2026-08-0011',
            'amount' => '1000',
        ])->assertRedirect(route('pre.obrs.show', $document));

        $document->refresh()->load(['lines', 'ledgerEntries']);
        $this->assertSame(0.0, $document->notYetDue());
        $this->assertSame(1000.0, $document->dueDemandable());
        $this->assertSame('Obligated', $document->status);

        $this->post(route('pre.ledger.store', $document), [
            'kind' => 'payment',
            'entry_date' => '2026-08-25',
            'instrument' => 'Check',
            'reference_no' => '778812',
            'amount' => '1000',
        ]);

        $document->refresh()->load(['lines', 'ledgerEntries']);
        $this->assertSame(0.0, $document->dueDemandable());
        $this->assertSame('Paid', $document->status);

        // Payments are prefixed with their instrument; payables with "DV".
        $this->assertSame('Check 778812', $document->ledgerEntries->last()->reference_no);
    }

    public function test_obrs_and_burs_do_not_render_each_others_records(): void
    {
        $this->actingAsOfficer();

        $this->post('/pre/burs', [
            'date' => '2026-08-18',
            'fund_cluster' => '05206441',
            'payee_name' => 'MMSU IGP',
            'lines' => [['rc_acronym' => 'BD', 'object_code' => '5021601000', 'amount' => '5000']],
        ]);

        $bur = FinDocument::query()->firstOrFail();

        $this->get('/pre/obrs/'.$bur->id)->assertNotFound();
        $this->get('/pre/burs/'.$bur->id)->assertOk();
    }
}
