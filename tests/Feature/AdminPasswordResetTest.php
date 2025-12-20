<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Notifications\ResetPassword;
use Tests\TestCase;

class AdminPasswordResetTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_view_password_reset_request_form()
    {
        $response = $this->get(route('admin.password.request'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.passwords.email');
    }

    /** @test */
    public function admin_can_request_password_reset_link()
    {
        Notification::fake();

        $admin = User::factory()->admin()->create();

        $response = $this->post(route('admin.password.email'), [
            'email' => $admin->email,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('status');
    }

    /** @test */
    public function admin_can_view_reset_password_form()
    {
        $this->withoutExceptionHandling();

        $admin = User::factory()->admin()->create();
        $token = Password::broker('admins')->createToken($admin);

        // Route name should be 'admin.password.reset' (prefix 'admin.' + name 'password.reset')
        $response = $this->get(route('admin.password.reset', [
            'token' => $token,
            'email' => $admin->email,
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('auth.passwords.reset');
    }
}
