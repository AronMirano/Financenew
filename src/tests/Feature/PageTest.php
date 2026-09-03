<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Every page in the sidebar renders for a signed-in officer, and every one
 * of them is behind the auth middleware.
 */
class PageTest extends TestCase
{
    use RefreshDatabase;

    /** @return list<string> */
    public static function pages(): array
    {
        return [
            ['/dashboard'],
            ['/pre/overview'],
            ['/pre/submit'],
            ['/pre/ppmp'],
            ['/pre/expenditures'],
            ['/pre/obrs'],
            ['/pre/obrs/create'],
            ['/pre/burs'],
            ['/pre/burs/create'],
            ['/pre/working-paper'],
            ['/pre/saob'],
            ['/pre/monitoring'],
            ['/pre/reports'],
            ['/pre/analytics'],
            ['/annualization/overview'],
            ['/annualization/import'],
            ['/annualization/records'],
            ['/annualization/employee'],
            ['/annualization/deductions'],
            ['/annualization/reports'],
            ['/annualization/analytics'],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('pages')]
    public function test_page_renders_for_a_signed_in_officer(string $url): void
    {
        $this->actingAs(User::factory()->create());

        $this->get($url)->assertOk();
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('pages')]
    public function test_page_requires_authentication(string $url): void
    {
        $this->get($url)->assertRedirect('/login');
    }

    public function test_saob_switches_sheets_and_appropriation_banners(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get('/pre/saob?sheet=FHE&banner=continuing')
            ->assertOk()
            ->assertSee('Free Higher Education')
            ->assertSee('MOOE-only sheet');
    }

    public function test_monitoring_switches_between_the_three_sheets(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get('/pre/monitoring')->assertOk()->assertSee('TOTAL = PS + MODE + CO');
        $this->get('/pre/monitoring?tab=fidu')->assertOk()->assertSee('Fiduciary');
        $this->get('/pre/monitoring?tab=nonfidu')->assertOk()->assertSee('Non-Fiduciary');
    }
}
