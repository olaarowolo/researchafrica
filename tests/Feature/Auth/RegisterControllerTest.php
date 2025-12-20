<?php

namespace Tests\Feature\Auth;

use App\Models\Member;
use App\Models\MemberType;
use App\Models\MemberRole;
use App\Models\Country;
use App\Models\State;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailVerification;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    /** @test */
    public function registration_page_is_accessible()
    {
        // Setup required data for the view
        Country::factory()->create();
        MemberRole::factory()->create();
        MemberType::factory()->create(['id' => 1]); // Ensure ID 1 exists as used in controller (or 4)

        $response = $this->get(route('member.register'));

        $response->assertStatus(200);
        $response->assertViewIs('member.auth.register');
    }

    /** @test */
    public function new_members_can_register()
    {
        // $this->withoutExceptionHandling();
        $memberType = MemberType::factory()->create();
        $memberRole = MemberRole::factory()->create();
        $country = Country::factory()->create();
        $state = State::factory()->create();

        $response = $this->post(route('member.submit-register'), [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email_address' => 'test@example.com',
            'phone_number' => '1234567890',
            'member_type_id' => $memberType->id,
            'member_role_id' => $memberRole->id,
            'country_id' => $country->id,
            'state_id' => $state->id,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('email-verify'));

        $this->assertDatabaseHas('members', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email_address' => 'test@example.com',
        ]);

        $member = Member::where('email_address', 'test@example.com')->first();

        // Verify email sent
        Mail::assertSent(EmailVerification::class, function ($mail) use ($member) {
            return $mail->hasTo($member->email_address);
        });
    }

    /** @test */
    public function registration_requires_all_fields()
    {
        $response = $this->post(route('member.submit-register'), []);

        $response->assertSessionHasErrors([
            'first_name', 'last_name', 'email_address', 'phone_number',
            'member_type_id', 'member_role_id', 'country_id', 'state_id', 'password'
        ]);
    }

    /** @test */
    public function registration_requires_unique_email()
    {
        Member::factory()->create(['email_address' => 'test@example.com']);

        $memberType = MemberType::factory()->create();
        $memberRole = MemberRole::factory()->create();
        $country = Country::factory()->create();
        $state = State::factory()->create();

        $response = $this->post(route('member.submit-register'), [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email_address' => 'test@example.com',
            'phone_number' => '1234567890',
            'member_type_id' => $memberType->id,
            'member_role_id' => $memberRole->id,
            'country_id' => $country->id,
            'state_id' => $state->id,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('email_address');
    }

    /** @test */
    public function registration_requires_confirmed_password()
    {
        $memberType = MemberType::factory()->create();
        $memberRole = MemberRole::factory()->create();
        $country = Country::factory()->create();
        $state = State::factory()->create();

        $response = $this->post(route('member.submit-register'), [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email_address' => 'test@example.com',
            'phone_number' => '1234567890',
            'member_type_id' => $memberType->id,
            'member_role_id' => $memberRole->id,
            'country_id' => $country->id,
            'state_id' => $state->id,
            'password' => 'password',
            'password_confirmation' => 'wrong_password',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function authenticated_members_cannot_visit_registration_page()
    {
        $member = Member::factory()->create();

        $response = $this->actingAs($member, 'member')->get(route('member.register'));

        // The controller returns back() if authenticated
        $response->assertRedirect();
    }
}
