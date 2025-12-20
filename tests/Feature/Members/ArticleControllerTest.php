<?php

namespace Tests\Feature\Members;

use App\Models\Member;
use App\Models\MemberRole;
use App\Models\MemberType;
use App\Models\Country;
use App\Models\State;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ArticleControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $member;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();

        // Create required models
        MemberRole::create(['id' => 1, 'title' => 'Test Role', 'status' => 1]);
        MemberType::create(['id' => 1, 'name' => 'Author', 'status' => 1]);
        Country::create(['id' => 1, 'name' => 'Test Country', 'short_code' => 'TC']);
        State::create(['id' => 1, 'name' => 'Test State', 'country_id' => 1]);

        // Create member
        $this->member = Member::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email_address' => 'john.doe@test.com',
            'password' => 'password123',
            'phone_number' => '1234567890',
            'country_id' => 1,
            'state_id' => 1,
            'member_type_id' => 1,
            'member_role_id' => 1,
        ]);
    }

    /** @test */
    public function member_can_access_article_create_page()
    {
        $response = $this->actingAs($this->member, 'member')
            ->get(route('member.articles.create'));

        $response->assertStatus(200);
        $response->assertViewIs('member.articles.create');
    }
}
