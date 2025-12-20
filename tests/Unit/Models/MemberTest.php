<?php

namespace Tests\Unit\Models;

use App\Models\Member;
use App\Models\Country;
use App\Models\State;
use App\Models\MemberType;
use App\Models\MemberRole;
use App\Models\JournalMembership;
use App\Models\JournalEditorialBoard;
use App\Models\Article;
use App\Models\Bookmark;
use App\Models\PurchasedArticle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class MemberTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $member;

    protected function setUp(): void
    {
        parent::setUp();
        $this->member = Member::factory()->create();
    }

    /** @test */
    public function it_can_create_a_member()
    {
        $memberData = [
            'title' => 'Dr',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email_address' => 'john.doe@example.com',
            'password' => 'password123',
            'country_id' => Country::factory()->create()->id,
            'member_type_id' => MemberType::factory()->create()->id,
            'member_role_id' => MemberRole::factory()->create()->id,
        ];

        $member = Member::create($memberData);

        $this->assertInstanceOf(Member::class, $member);
        $this->assertEquals('John', $member->first_name);
        $this->assertEquals('Doe', $member->last_name);
        $this->assertEquals('john.doe@example.com', $member->email_address);
        $this->assertTrue(Hash::check('password123', $member->password));
    }

    /** @test */
    public function it_has_correct_table_name()
    {
        $this->assertEquals('members', $this->member->getTable());
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = [
            'email_address',
            'password',
            'title',
            'first_name',
            'middle_name',
            'last_name',
            'date_of_birth',
            'member_type_id',
            'phone_number',
            'country_id',
            'state_id',
            'member_role_id',
            'gender',
            'address',
            'registration_via',
            'email_verified',
            'email_verified_at',
            'verified',
            'profile_completed',
            'created_at',
            'updated_at',
            'deleted_at',
        ];

        $this->assertEquals($fillable, $this->member->getFillable());
    }

    /** @test */
    public function it_has_hidden_attributes()
    {
        $this->assertEquals(['password'], $this->member->getHidden());
    }

    /** @test */
    public function it_has_profile_picture_appended()
    {
        $this->assertContains('profile_picture', $this->member->getAppends());
    }

    /** @test */
    public function it_generates_full_name()
    {
        $member = Member::factory()->create([
            'first_name' => 'John',
            'middle_name' => 'Michael',
            'last_name' => 'Doe'
        ]);

        $this->assertEquals('John Doe', $member->fullname);
    }

    /** @test */
    public function it_hashes_password_when_setting()
    {
        $plainPassword = 'password123';
        $member = Member::factory()->create();

        $member->setPasswordAttribute($plainPassword);

        $this->assertTrue(Hash::check($plainPassword, $member->password));
        $this->assertNotEquals($plainPassword, $member->password);
    }

    /** @test */
    public function it_formats_date_of_birth()
    {
        $member = Member::factory()->create([
            'date_of_birth' => '1990-05-15'
        ]);

        $formatted = $member->date_of_birth;
        $this->assertNotNull($formatted);
        $this->assertIsString($formatted);
    }

    /** @test */
    public function it_handles_date_of_birth_setter()
    {
        $member = Member::factory()->create();
        $date = '1990-05-15';

        $member->setDateOfBirthAttribute($date);

        $this->assertNotNull($member->date_of_birth);
        $this->assertIsString($member->date_of_birth);
    }

    /** @test */
    public function it_formats_email_verified_at()
    {
        $verifiedMember = Member::factory()->create([
            'email_verified_at' => '2024-01-01 10:30:00'
        ]);

        $formatted = $verifiedMember->email_verified_at;
        $this->assertNotNull($formatted);
    }

    /** @test */
    public function it_handles_email_verified_at_setter()
    {
        $member = Member::factory()->create();
        $formattedDate = '2024-01-01 10:30:00';

        $member->setEmailVerifiedAtAttribute($formattedDate);

        $this->assertNotNull($member->email_verified_at);
        $this->assertIsString($member->email_verified_at);
    }

    /** @test */
    public function it_checks_if_email_is_verified()
    {
        // Verified member
        $verifiedMember = Member::factory()->create([
            'email_verified' => '1',
            'email_verified_at' => now()
        ]);
        $this->assertTrue($verifiedMember->is_email_verify);

        // Unverified member
        $unverifiedMember = Member::factory()->unverified()->create();
        $this->assertFalse($unverifiedMember->is_email_verify);
    }

    /** @test */
    public function it_has_belongs_to_country_relationship()
    {
        $country = Country::factory()->create();
        $member = Member::factory()->create(['country_id' => $country->id]);

        $this->assertInstanceOf(Country::class, $member->country);
        $this->assertEquals($country->id, $member->country->id);
    }

    /** @test */
    public function it_has_belongs_to_state_relationship()
    {
        $state = State::factory()->create();
        $member = Member::factory()->create(['state_id' => $state->id]);

        $this->assertInstanceOf(State::class, $member->state);
        $this->assertEquals($state->id, $member->state->id);
    }

    /** @test */
    public function it_has_belongs_to_member_type_relationship()
    {
        $memberType = MemberType::factory()->create();
        $member = Member::factory()->create(['member_type_id' => $memberType->id]);

        $this->assertInstanceOf(MemberType::class, $member->member_type);
        $this->assertEquals($memberType->id, $member->member_type->id);
    }

    /** @test */
    public function it_has_belongs_to_member_role_relationship()
    {
        $memberRole = MemberRole::factory()->create();
        $member = Member::factory()->create(['member_role_id' => $memberRole->id]);

        $this->assertInstanceOf(MemberRole::class, $member->member_role);
        $this->assertEquals($memberRole->id, $member->member_role->id);
    }

    /** @test */
    public function it_has_many_member_articles()
    {
        $member = Member::factory()->create();
        Article::factory()->count(3)->create(['member_id' => $member->id]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $member->memberArticles);
        $this->assertCount(3, $member->memberArticles);
        $this->assertInstanceOf(Article::class, $member->memberArticles->first());
    }

    /** @test */
    public function it_has_many_bookmarks()
    {
        $member = Member::factory()->create();
        Bookmark::factory()->count(2)->create(['member_id' => $member->id]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $member->bookmarks);
        $this->assertCount(2, $member->bookmarks);
    }

    /** @test */
    public function it_has_many_purchased_articles()
    {
        $member = Member::factory()->create();
        PurchasedArticle::factory()->count(3)->create(['member_id' => $member->id]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $member->purchasedArticle);
        $this->assertCount(3, $member->purchasedArticle);
    }

    /** @test */
    public function it_has_many_journal_memberships()
    {
        $member = Member::factory()->create();
        JournalMembership::factory()->count(2)->create(['member_id' => $member->id]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $member->journalMemberships);
        $this->assertCount(2, $member->journalMemberships);
    }

    /** @test */
    public function it_has_many_active_journal_memberships()
    {
        $member = Member::factory()->create();
        JournalMembership::factory()->count(2)->create([
            'member_id' => $member->id,
            'status' => JournalMembership::STATUS_ACTIVE
        ]);
        JournalMembership::factory()->count(1)->create([
            'member_id' => $member->id,
            'status' => JournalMembership::STATUS_INACTIVE
        ]);

        $this->assertCount(2, $member->activeJournalMemberships);
    }

    /** @test */
    public function it_has_many_editorial_positions()
    {
        $member = Member::factory()->create();
        JournalEditorialBoard::factory()->count(3)->create([
            'member_id' => $member->id,
            'is_active' => true
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $member->editorialPositions);
        $this->assertCount(3, $member->editorialPositions);
    }

    /** @test */
    public function it_checks_journal_access()
    {
        $member = Member::factory()->create();
        $journalId = 1;
        $memberTypeId = 2;

        // No access initially
        $this->assertFalse($member->hasJournalAccess($journalId));

        // Grant access
        JournalMembership::factory()->create([
            'member_id' => $member->id,
            'journal_id' => $journalId,
            'member_type_id' => $memberTypeId,
            'status' => JournalMembership::STATUS_ACTIVE
        ]);

        $this->assertTrue($member->hasJournalAccess($journalId));
        $this->assertTrue($member->hasJournalAccess($journalId, $memberTypeId));
    }

    /** @test */
    public function it_checks_editor_role_for_journal()
    {
        $member = Member::factory()->create();
        $journalId = 1;

        // Not editor initially
        $this->assertFalse($member->isEditorFor($journalId));

        // Grant editor access
        JournalMembership::factory()->create([
            'member_id' => $member->id,
            'journal_id' => $journalId,
            'member_type_id' => 2, // Editor
            'status' => JournalMembership::STATUS_ACTIVE
        ]);

        $this->assertTrue($member->isEditorFor($journalId));
    }

    /** @test */
    public function it_checks_reviewer_role_for_journal()
    {
        $member = Member::factory()->create();
        $journalId = 1;

        // Grant reviewer access
        JournalMembership::factory()->create([
            'member_id' => $member->id,
            'journal_id' => $journalId,
            'member_type_id' => 3, // Reviewer
            'status' => JournalMembership::STATUS_ACTIVE
        ]);

        $this->assertTrue($member->isReviewerFor($journalId));
    }

    /** @test */
    public function it_checks_author_role_for_journal()
    {
        $member = Member::factory()->create();
        $journalId = 1;

        // Grant author access
        JournalMembership::factory()->create([
            'member_id' => $member->id,
            'journal_id' => $journalId,
            'member_type_id' => 1, // Author
            'status' => JournalMembership::STATUS_ACTIVE
        ]);

        $this->assertTrue($member->isAuthorFor($journalId));
    }

    /** @test */
    public function it_can_assign_to_journal()
    {
        $member = Member::factory()->create();
        $journalId = 1;
        $memberTypeId = 2;

        $membership = $member->assignToJournal($journalId, $memberTypeId);

        $this->assertInstanceOf(JournalMembership::class, $membership);
        $this->assertTrue($member->hasJournalAccess($journalId, $memberTypeId));
    }

    /** @test */
    public function it_can_remove_from_journal()
    {
        $member = Member::factory()->create();
        $journalId = 1;
        $memberTypeId = 2;

        // First assign
        $member->assignToJournal($journalId, $memberTypeId);
        $this->assertTrue($member->hasJournalAccess($journalId));

        // Then remove
        $member->removeFromJournal($journalId, $memberTypeId);
        $this->assertFalse($member->hasJournalAccess($journalId));
    }

    /** @test */
    public function it_checks_editorial_board_membership()
    {
        $member = Member::factory()->create();
        $journalId = 1;

        // Not on editorial board initially
        $this->assertFalse($member->isOnEditorialBoard($journalId));

        // Add to editorial board
        JournalEditorialBoard::factory()->create([
            'member_id' => $member->id,
            'journal_id' => $journalId,
            'is_active' => true
        ]);

        $this->assertTrue($member->isOnEditorialBoard($journalId));
    }

    /** @test */
    public function it_gets_editorial_position_for_journal()
    {
        $member = Member::factory()->create();
        $journalId = 1;

        $position = JournalEditorialBoard::factory()->create([
            'member_id' => $member->id,
            'journal_id' => $journalId,
            'is_active' => true
        ]);

        $retrievedPosition = $member->getEditorialPositionFor($journalId);

        $this->assertInstanceOf(JournalEditorialBoard::class, $retrievedPosition);
        $this->assertEquals($position->id, $retrievedPosition->id);
    }

    /** @test */
    public function it_counts_accessible_journals()
    {
        $member = Member::factory()->create();

        $this->assertEquals(0, $member->accessible_journals_count);

        // Add memberships
        JournalMembership::factory()->count(3)->create([
            'member_id' => $member->id,
            'status' => JournalMembership::STATUS_ACTIVE
        ]);

        $this->assertEquals(3, $member->accessible_journals_count);
    }

    /** @test */
    public function it_counts_editorial_positions()
    {
        $member = Member::factory()->create();

        $this->assertEquals(0, $member->editorial_positions_count);

        JournalEditorialBoard::factory()->count(2)->create([
            'member_id' => $member->id,
            'is_active' => true
        ]);

        $this->assertEquals(2, $member->editorial_positions_count);
    }

    /** @test */
    public function it_gets_bookmark_count()
    {
        $member = Member::factory()->create();
        Bookmark::factory()->count(5)->create(['member_id' => $member->id]);

        $this->assertEquals(5, $member->bookmark_count);
    }

    /** @test */
    public function it_uses_soft_deletes()
    {
        $member = Member::factory()->create();
        $memberId = $member->id;

        $member->delete();

        $this->assertSoftDeleted($member);
        $this->assertNotNull(Member::withTrashed()->find($memberId));
        $this->assertNull(Member::find($memberId));
    }

    /** @test */
    public function it_handles_media_uploads()
    {
        $member = Member::factory()->create();

        // Test profile picture media conversion
        $this->assertMethodExists($member, 'registerMediaConversions');
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Member::create([]);
    }

    /** @test */
    public function it_validates_unique_email()
    {
        Member::factory()->create(['email_address' => 'test@example.com']);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Member::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email_address' => 'test@example.com',
            'password' => 'password',
            'country_id' => Country::factory()->create()->id,
            'member_type_id' => MemberType::factory()->create()->id,
            'member_role_id' => MemberRole::factory()->create()->id,
        ]);
    }

    /** @test */
    public function it_can_be_created_via_factory()
    {
        $member = Member::factory()->create();

        $this->assertInstanceOf(Member::class, $member);
        $this->assertNotNull($member->first_name);
        $this->assertNotNull($member->last_name);
        $this->assertNotNull($member->email_address);
        $this->assertNotNull($member->password);
    }

    /** @test */
    public function it_can_be_created_unverified()
    {
        $unverifiedMember = Member::factory()->unverified()->create();

        $this->assertEquals('0', $unverifiedMember->email_verified);
        $this->assertNull($unverifiedMember->email_verified_at);
    }

    /** @test */
    public function it_can_be_created_inactive()
    {
        $inactiveMember = Member::factory()->inactive()->create();

        $this->assertEquals('0', $inactiveMember->verified);
    }

    /** @test */
    public function it_has_correct_member_role_constants()
    {
        $expectedRoles = [
            '1' => 'Author',
            '2' => 'Editor',
            '3' => 'Reviewer',
            '4' => 'Account',
            '5' => 'Pubisher',
            '6' => 'Reviewer Final',
        ];

        $this->assertEquals($expectedRoles, Member::MEMBER_ROLE);
    }

    /** @test */
    public function it_has_correct_title_select_options()
    {
        $expectedTitles = [
            'Mr' => 'Mr',
            'Mrs' => 'Mrs',
            'Master' => 'Master',
            'Miss' => 'Miss',
            'Dr' => 'Dr',
            'Prof' => 'Prof',
        ];

        $this->assertEquals($expectedTitles, Member::TITLE_SELECT);
    }

    /** @test */
    public function it_has_correct_gender_options()
    {
        $expectedGenders = [
            'Male' => 'Male',
            'Female' => 'Female',
        ];

        $this->assertEquals($expectedGenders, Member::GENDER_RADIO);
    }

    /** @test */
    public function it_has_correct_registration_via_options()
    {
        $expectedVia = [
            'email' => 'Email',
            'google' => 'Google login',
        ];

        $this->assertEquals($expectedVia, Member::REGISTRATION_VIA_SELECT);
    }

    private function assertMethodExists($object, $methodName)
    {
        $this->assertTrue(method_exists($object, $methodName), "Method {$methodName} does not exist on " . get_class($object));
    }
}

