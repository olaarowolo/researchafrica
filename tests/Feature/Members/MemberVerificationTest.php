<?php

namespace Tests\Feature\Members;

use App\Models\Member;
use App\Models\EmailVerify;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberVerificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_verifies_email_with_valid_token()
    {
        $member = Member::factory()->create([
            'email_verified' => false,
            'email_verified_at' => null,
        ]);

        $token = '123456';
        EmailVerify::create([
            'member_id' => $member->id,
            'token' => $token,
        ]);

        $response = $this->post(route('member.verify_email'), [
            'token' => $token,
        ]);

        $response->assertRedirect(route('member.login'));
        $response->assertSessionHas('success', 'Email Verification Completed');

        $this->assertTrue((bool)$member->fresh()->email_verified);
        $this->assertNotNull($member->fresh()->email_verified_at);
        $this->assertEquals('email', $member->fresh()->registration_via);

        $this->assertDatabaseMissing('email_verifies', [
            'member_id' => $member->id,
            'token' => $token,
        ]);
    }

    /** @test */
    public function it_fails_with_invalid_token()
    {
        $member = Member::factory()->create([
            'email_verified' => false,
        ]);

        $validToken = '123456';
        EmailVerify::create([
            'member_id' => $member->id,
            'token' => $validToken,
        ]);

        $response = $this->post(route('member.verify_email'), [
            'token' => 'invalid_token',
        ]);

        $response->assertSessionHasErrors('message');

        $this->assertFalse((bool)$member->fresh()->email_verified);

        $this->assertDatabaseHas('email_verifies', [
            'member_id' => $member->id,
            'token' => $validToken,
        ]);
    }

    /** @test */
    public function it_requires_token()
    {
        $response = $this->post(route('member.verify_email'), []);

        $response->assertSessionHasErrors('token');
    }
}
