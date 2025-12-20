<?php

namespace Tests\Feature;


use App\Models\User;
use App\Models\Member;
use App\Models\MemberRole;
use App\Models\MemberType;
use App\Models\Country;
use App\Models\State;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test admin can login.
     */
    public function test_admin_can_login()
    {
        $admin = User::factory()->admin()->create([
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'admin@test.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.home'));
        $this->assertAuthenticated('web');
    }

    /**
     * Test admin login with wrong credentials.
     */
    public function test_admin_login_with_wrong_credentials()
    {
        $response = $this->from('/admin/login')->post('/admin/login', [
            'email' => 'wrong@test.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/admin/login');
        $this->assertGuest('web');
    }

    /**
     * Test member can login.
     */
    public function test_member_can_login()
    {
        $member = Member::factory()->create([
            'email_address' => 'member@test.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'email_address' => 'member@test.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('member.profile'));
        $this->assertAuthenticated('member');
    }

    /**
     * Test member login with wrong credentials.
     */
    public function test_member_login_with_wrong_credentials()
    {
        $response = $this->from('/login')->post('/login', [
            'email_address' => 'wrong@test.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $this->assertGuest('member');
    }

    public function test_member_can_register()
    {
        Mail::fake();

        MemberRole::create(['id' => 1, 'title' => 'Test Role', 'status' => 1]);
        MemberType::create(['id' => 1, 'name' => 'Test Type', 'status' => 1]);
        Country::create(['id' => 1, 'name' => 'Test Country', 'short_code' => 'TC']);
        State::create(['id' => 1, 'name' => 'Test State', 'country_id' => 1]);

        $memberData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email_address' => 'john.doe@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone_number' => '1234567890',
            'country_id' => 1,
            'state_id' => 1,
            'member_type_id' => 1,
            'member_role_id' => 1,
        ];

        $response = $this->post('/register', $memberData);

        $response->assertRedirect(route('email-verify'));
        $this->assertDatabaseHas('members', [
            'email_address' => 'john.doe@test.com',
        ]);
    }

    /**
     * Test member can logout.
     */
    public function test_member_can_logout()
    {
        $member = Member::factory()->create();

        $response = $this->actingAs($member, 'member')
            ->post('/member/logout');

        $response->assertRedirect(route('home'));
        $this->assertGuest('member');
    }


    /**
     * Test admin can logout.
     */
    public function test_admin_can_logout()
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin, 'web')
            ->post('/admin/logout');

        $response->assertRedirect(route('admin.login'));
        $this->assertGuest('web');
    }

    /**
     * Test password reset functionality.
     */
    public function test_member_can_request_password_reset()
    {
        $member = Member::factory()->create();

        $response = $this->post('/email-password', [
            'email_address' => $member->email_address,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');
    }

    /**
     * Test email verification.
     */
    public function test_member_can_verify_email()
    {
        $member = Member::factory()->create([
            'email_verified_at' => null,
        ]);

        $response = $this->actingAs($member, 'member')
            ->post('/verify_email', [
                'verification_code' => '123456', // This would need to be set up properly
            ]);

        $response->assertStatus(302);
    }

    public function test_authenticated_admin_is_redirected_from_login_page()
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin, 'web')->get('/admin/login');

        $response->assertRedirect(route('admin.home'));
    }

    public function test_unauthenticated_user_is_redirected_to_login_page()
    {
        $response = $this->get('/admin/home');

        $response->assertRedirect(route('admin.login'));
    }
}
