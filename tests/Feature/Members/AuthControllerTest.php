<?php

namespace Tests\Feature\Members;

use App\Models\Member;
use App\Models\EmailVerify;
use App\Models\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_access_register_page()
    {
        $response = $this->get(route('member.register'));

        $response->assertStatus(200);
        $response->assertViewIs('member.auth.register');
        $response->assertViewHas(['title', 'countries', 'member_roles', 'member_types']);
    }

    public function test_authenticated_member_cannot_access_register_page()
    {
        $member = Member::factory()->create();

        $response = $this->actingAs($member, 'member')->get(route('member.register'));

        $response->assertRedirect();
    }

    public function test_member_can_register()
    {
        Mail::fake();

        // Create required related data
        $country = \App\Models\Country::factory()->create();
        $state = \App\Models\State::factory()->create(['country_id' => $country->id]);
        $memberRole = \App\Models\MemberRole::factory()->create();
        $memberType = \App\Models\MemberType::factory()->create();

        $memberData = [
            'title' => 'Mr',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email_address' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone_number' => '+1234567890',
            'country_id' => $country->id,
            'state_id' => $state->id,
            'address' => '123 Test Street',
            'member_role_id' => $memberRole->id,
            'member_type_id' => $memberType->id,
            'gender' => 'Male',
            'date_of_birth' => '1990-01-01',
        ];

        $response = $this->post(route('member.submit-register'), $memberData);

        $response->assertRedirect('/email-verify');
        $response->assertSessionHas('success', 'Please check your email for the verification code');

        $this->assertDatabaseHas('members', [
            'email_address' => 'john@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $member = Member::where('email_address', 'john@example.com')->first();
        $this->assertDatabaseHas('email_verifies', [
            'member_id' => $member->id,
        ]);

        Mail::assertSent(\App\Mail\EmailVerification::class, function ($mail) use ($member) {
            return $mail->hasTo($member->email_address);
        });
    }

    public function test_member_can_access_login_page()
    {
        $response = $this->get(route('member.login'));

        $response->assertStatus(200);
        $response->assertViewIs('member.auth.login');
    }

    public function test_authenticated_member_cannot_access_login_page()
    {
        $member = Member::factory()->create();

        $response = $this->actingAs($member, 'member')->get(route('member.login'));

        $response->assertRedirect();
    }

    public function test_member_can_login_with_valid_credentials()
    {
        // Create required member type
        \App\Models\MemberType::factory()->create(['id' => 4, 'name' => 'User', 'status' => 1]);

        $member = Member::factory()->create([
            'email_address' => 'test@example.com',
            'password' => 'password', // Will be hashed by mutator
            'email_verified' => 1,
            'member_type_id' => 4, // User type
        ]);

        $response = $this->post(route('member.submit-login'), [
            'email_address' => 'test@example.com',
            'password' => 'password',
        ]);

        // Check if we can access the profile page
        $profileResponse = $this->actingAs($member, 'member')->get(route('member.profile'));
        $this->assertEquals(200, $profileResponse->getStatusCode());

        $response->assertRedirect(route('member.profile'));
        $this->assertAuthenticatedAs($member, 'member');
    }

    public function test_member_cannot_login_with_invalid_credentials()
    {
        $member = Member::factory()->create();

        $response = $this->post(route('member.submit-login'), [
            'email_address' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('email');
        $this->assertGuest('member');
    }

    public function test_member_can_verify_email()
    {
        $member = Member::factory()->create([
            'email_verified' => 0,
        ]);

        $emailVerify = EmailVerify::factory()->create([
            'member_id' => $member->id,
            'token' => '123456',
        ]);

        $response = $this->post(route('member.verify_email'), [
            'token' => '123456',
        ]);

        $response->assertRedirect(route('member.login'));
        $response->assertSessionHas('success', 'Email Verification Completed');

        $this->assertDatabaseHas('members', [
            'id' => $member->id,
            'email_verified' => 1,
            'registration_via' => 'email',
        ]);

        $this->assertDatabaseMissing('email_verifies', [
            'id' => $emailVerify->id,
        ]);
    }

    public function test_member_cannot_verify_email_with_invalid_token()
    {
        $response = $this->post(route('member.verify_email'), [
            'token' => 'invalid',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('message');
    }

    public function test_member_can_access_forget_password_page()
    {
        $response = $this->get(route('member.forget-password'));

        $response->assertStatus(200);
        $response->assertViewIs('member.auth.password.forget-password');
    }

    public function test_member_can_request_password_reset()
    {
        Mail::fake();

        $member = Member::factory()->create([
            'email_address' => 'test@example.com',
            'email_verified' => 1,
        ]);

        $response = $this->post(route('member.email-password'), [
            'email_address' => 'test@example.com',
        ]);

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('success', 'Reset Link Sent to your E-mail');

        $this->assertDatabaseHas('reset_passwords', [
            'member_id' => $member->id,
        ]);

        Mail::assertSent(\App\Mail\ResetPassword::class, function ($mail) {
            return $mail->hasTo('test@example.com');
        });
    }

    public function test_member_cannot_request_password_reset_with_nonexistent_email()
    {
        $response = $this->post(route('member.email-password'), [
            'email_address' => 'nonexistent@example.com',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Email does not exist. Please use the email you use to register');
    }

    public function test_member_can_access_reset_password_page()
    {
        $response = $this->get(route('member.reset-password', 'somehash'));

        $response->assertStatus(200);
        $response->assertViewIs('member.auth.password.reset-password');
        $response->assertViewHas('hash', 'somehash');
    }

    public function test_member_can_reset_password()
    {
        $member = Member::factory()->create();

        $resetPassword = ResetPassword::create([
            'member_id' => $member->id,
            'token' => '123456',
            'hash' => 'test-hash',
        ]);

        $response = $this->post(route('member.reset-password-submit', 'test-hash'), [
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('success', 'Your password has been updated successfully');

        $this->assertDatabaseMissing('reset_passwords', [
            'id' => $resetPassword->id,
        ]);
    }

    public function test_member_cannot_reset_password_with_invalid_hash()
    {
        $response = $this->post(route('member.reset-password-submit', 'invalid-hash'), [
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Something Went Wrong. Please try again later');
    }

    public function test_member_can_logout()
    {
        $member = Member::factory()->create();

        $response = $this->actingAs($member, 'member')->post(route('member.log-out'));

        $response->assertRedirect(route('home'));
        $this->assertGuest('member');
    }
}
