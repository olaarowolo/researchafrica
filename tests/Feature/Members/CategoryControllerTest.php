<?php

namespace Tests\Feature\Members;

use App\Models\ArticleCategory;
use App\Models\Member;
use App\Models\MemberRole;
use App\Models\MemberType;
use App\Models\Country;
use App\Models\State;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $member;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();

        // Create required models
        $country = Country::factory()->create();
        $state = State::factory()->create(['country_id' => $country->id]);
        $memberRole = MemberRole::factory()->create();
        $memberType = MemberType::factory()->create();

        // Create member
        $this->member = Member::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email_address' => 'john.doe@test.com',
            'password' => 'password123',
            'phone_number' => '1234567890',
            'country_id' => $country->id,
            'state_id' => $state->id,
            'member_type_id' => $memberType->id,
            'member_role_id' => $memberRole->id,
        ]);
    }

    /** @test */
    public function member_can_view_journal()
    {
        $journal = ArticleCategory::create([
            'category_name' => 'Test Journal',
            'is_journal' => true,
        ]);

        $response = $this->actingAs($this->member, 'member')
            ->get(route('member.journal', $journal->id));

        $response->assertStatus(200);
        $response->assertViewIs('member.journal.index');
        $response->assertViewHas('journal');
    }

    /** @test */
    public function member_cannot_view_nonexistent_journal()
    {
        $response = $this->actingAs($this->member, 'member')
            ->get(route('member.journal', 999));

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Journal does not exist');
    }
}
