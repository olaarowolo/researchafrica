<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class AdminPasswordResetControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_displays_the_admin_forgot_password_view()
    {
        $response = $this->get(route('admin.password.request'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.passwords.email');
    }

    /** @test */
    public function it_sends_password_reset_link_to_valid_admin()
    {
        Notification::fake();
        $admin = Admin::factory()->create();

        $response = $this->post(route('admin.password.email'), [
            'email' => $admin->email,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('status', trans(Password::RESET_LINK_SENT));
    }

    /** @test */
    public function it_does_not_send_link_to_invalid_email()
    {
        $response = $this->post(route('admin.password.email'), [
            'email' => 'invalid@example.com',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function it_displays_the_password_reset_form()
    {
        $response = $this->get(route('admin.password.reset', ['token' => 'some-token']));

        $response->assertStatus(200);
        $response->assertViewIs('auth.passwords.reset');
        $response->assertViewHas('token', 'some-token');
    }
}
