<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ChangePasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create permission
        $permission = Permission::factory()->create(['title' => 'profile_password_edit']);

        // Create role and attach permission
        $role = Role::factory()->create();
        $role->permissions()->attach($permission);

        // Create user and assign role
        $this->user = User::factory()->create();
        $this->user->roles()->attach($role);
    }

    /** @test */
    public function it_can_view_change_password_page()
    {
        $response = $this->actingAs($this->user)
            ->get(route('profile.password.edit'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.passwords.edit');
    }

    /** @test */
    public function it_denies_access_if_user_does_not_have_permission()
    {
        // Create a user without the required permission
        $userWithoutPermission = User::factory()->create();

        $response = $this->actingAs($userWithoutPermission)
            ->get(route('profile.password.edit'));

        $response->assertForbidden();
    }

    /** @test */
    public function it_can_update_password()
    {
        $newPassword = 'newpassword123';

        $response = $this->actingAs($this->user)
            ->post(route('profile.password.update'), [
                'password' => $newPassword,
                'password_confirmation' => $newPassword,
            ]);

        $response->assertRedirect(route('profile.password.edit'));
        $response->assertSessionHas('message');

        // Verify password was changed
        $this->assertTrue(Hash::check($newPassword, $this->user->fresh()->password));
    }

    /** @test */
    public function it_validates_password_update()
    {
        $response = $this->actingAs($this->user)
            ->post(route('profile.password.update'), [
                'password' => 'short',
                'password_confirmation' => 'short',
            ]);

        $response->assertSessionHasErrors('password');

        $response = $this->actingAs($this->user)
            ->post(route('profile.password.update'), [
                'password' => 'password123',
                'password_confirmation' => 'mismatch',
            ]);

        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function it_can_update_profile()
    {
        $newName = 'Updated Name';
        $newEmail = 'updated@example.com';

        $response = $this->actingAs($this->user)
            ->post(route('profile.password.updateProfile'), [
                'name' => $newName,
                'email' => $newEmail,
            ]);

        $response->assertRedirect(route('profile.password.edit'));
        $response->assertSessionHas('message');

        $this->assertEquals($newName, $this->user->fresh()->name);
        $this->assertEquals($newEmail, $this->user->fresh()->email);
    }

    /** @test */
    public function it_validates_profile_update()
    {
        // Create another user to test unique email
        User::factory()->create(['email' => 'taken@example.com']);

        $response = $this->actingAs($this->user)
            ->post(route('profile.password.updateProfile'), [
                'name' => '',
                'email' => 'not-an-email',
            ]);

        $response->assertSessionHasErrors(['name', 'email']);

        $response = $this->actingAs($this->user)
            ->post(route('profile.password.updateProfile'), [
                'name' => 'Valid Name',
                'email' => 'taken@example.com',
            ]);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function it_can_destroy_profile()
    {
        $originalEmail = $this->user->email;

        $response = $this->actingAs($this->user)
            ->post(route('profile.password.destroyProfile'));

        $response->assertRedirect(route('admin.login'));
        $response->assertSessionHas('message');

        // Check if user is soft deleted
        $this->assertSoftDeleted($this->user);

        // Check if email is mangled (as per controller logic)
        $this->assertStringContainsString('_' . $originalEmail, $this->user->fresh()->email); // The controller prepends timestamp
    }
}
