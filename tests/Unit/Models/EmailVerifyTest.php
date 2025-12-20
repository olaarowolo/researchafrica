<?php

namespace Tests\Unit\Models;

use App\Models\EmailVerify;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class EmailVerifyTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $emailVerify;

    protected function setUp(): void
    {
        parent::setUp();
        $this->emailVerify = EmailVerify::factory()->create();
    }

    /** @test */
    public function it_can_create_an_email_verification_record()
    {
        $member = Member::factory()->create();
        $token = Str::random(60);
        $data = [
            'member_id' => $member->id,
            'token' => $token,
        ];

        $emailVerify = EmailVerify::create($data);

        $this->assertInstanceOf(EmailVerify::class, $emailVerify);
        $this->assertEquals($member->id, $emailVerify->member_id);
        $this->assertEquals($token, $emailVerify->token);
    }

    /** @test */
    public function it_has_correct_table_name()
    {
        $this->assertEquals('email_verifies', $this->emailVerify->getTable());
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = [
            'token',
            'member_id',
        ];

        $this->assertEquals($fillable, $this->emailVerify->getFillable());
    }

    /** @test */
    public function it_belongs_to_a_member()
    {
        $this->assertInstanceOf(Member::class, $this->emailVerify->member);
        $this->assertEquals($this->emailVerify->member_id, $this->emailVerify->member->id);
    }
}
