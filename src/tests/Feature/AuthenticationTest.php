<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_login_page_renders(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('Mariano Marcos State University');
    }

    public function test_an_officer_can_sign_in(): void
    {
        $user = User::factory()->create([
            'email' => 'kaustria@mmsu.edu.ph',
            'password' => Hash::make('password'),
        ]);

        $this->post('/login', [
            'email' => 'kaustria@mmsu.edu.ph',
            'password' => 'password',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($user);
    }

    public function test_bad_credentials_are_rejected(): void
    {
        User::factory()->create(['email' => 'kaustria@mmsu.edu.ph']);

        $this->post('/login', [
            'email' => 'kaustria@mmsu.edu.ph',
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_an_officer_can_sign_out(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post('/logout')->assertRedirect('/login');

        $this->assertGuest();
    }
}
