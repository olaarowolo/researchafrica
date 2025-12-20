<?php

namespace Tests\Unit\Models;

use App\Models\ArticleCategory;
use App\Models\JournalMembership;
use App\Models\Member;
use App\Models\MemberType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class JournalMembershipTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $journalMembership;

    protected function setUp(): void
    {
        parent::setUp();
        $this->journalMembership = JournalMembership::factory()->active()->create();
    }

    /** @test */
    public function it_can_create_a_journal_membership()
    {
        $membershipData = [
            'member_id' => Member::factory()->create()->id,
            'journal_id' => ArticleCategory::factory()->create(['is_journal' => true])->id,
            'member_type_id' => MemberType::factory()->create()->id,
            'status' => 'active',
            'assigned_by' => Member::factory()->create()->id,
            'assigned_at' => now(),
            'notes' => 'Test notes',
        ];

        $membership = JournalMembership::create($membershipData);

        $this->assertInstanceOf(JournalMembership::class, $membership);
        $this->assertEquals('active', $membership->status);
        $this->assertEquals('Test notes', $membership->notes);
    }

    /** @test */
    public function it_has_correct_table_name()
    {
        $this->assertEquals('journal_memberships', $this->journalMembership->getTable());
    }

    /** @test */
    public function it_uses_soft_deletes()
    {
        $this->assertTrue(in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($this->journalMembership)));
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = [
            'member_id',
            'journal_id',
            'member_type_id',
            'status',
            'assigned_by',
            'assigned_at',
            'expires_at',
            'notes',
        ];

        $this->assertEquals($fillable, $this->journalMembership->getFillable());
    }

    /** @test */
    public function it_has_correct_casts()
    {
        $casts = $this->journalMembership->getCasts();

        $this->assertEquals('datetime', $casts['assigned_at']);
        $this->assertEquals('datetime', $casts['expires_at']);
    }

    /** @test */
    public function it_belongs_to_a_member()
    {
        $this->assertInstanceOf(Member::class, $this->journalMembership->member);
        $this->assertEquals($this->journalMembership->member_id, $this->journalMembership->member->id);
    }

    /** @test */
    public function it_belongs_to_a_journal()
    {
        $this->assertInstanceOf(ArticleCategory::class, $this->journalMembership->journal);
        $this->assertEquals($this->journalMembership->journal_id, $this->journalMembership->journal->id);
    }

    /** @test */
    public function it_belongs_to_a_member_type()
    {
        $this->assertInstanceOf(MemberType::class, $this->journalMembership->memberType);
        $this->assertEquals($this->journalMembership->member_type_id, $this->journalMembership->memberType->id);
    }

    /** @test */
    public function it_belongs_to_assigned_by_member()
    {
        $this->assertInstanceOf(Member::class, $this->journalMembership->assignedBy);
        $this->assertEquals($this->journalMembership->assigned_by, $this->journalMembership->assignedBy->id);
    }

    /** @test */
    public function it_can_scope_active_memberships()
    {
        JournalMembership::factory()->create(['status' => 'active']);
        JournalMembership::factory()->create(['status' => 'inactive']);

        $activeMemberships = JournalMembership::active()->get();

        $this->assertCount(2, $activeMemberships); // Including setUp
        $activeMemberships->each(function ($membership) {
            $this->assertEquals('active', $membership->status);
        });
    }

    /** @test */
    public function it_can_scope_by_journal()
    {
        $journal1 = ArticleCategory::factory()->create(['is_journal' => true]);
        $journal2 = ArticleCategory::factory()->create(['is_journal' => true]);

        JournalMembership::factory()->create(['journal_id' => $journal1->id]);
        JournalMembership::factory()->create(['journal_id' => $journal2->id]);

        $journal1Memberships = JournalMembership::forJournal($journal1->id)->get();
        $journal2Memberships = JournalMembership::forJournal($journal2->id)->get();

        $this->assertCount(1, $journal1Memberships);
        $this->assertCount(1, $journal2Memberships);
        $this->assertEquals($journal1->id, $journal1Memberships->first()->journal_id);
    }

    /** @test */
    public function it_can_scope_by_member()
    {
        $member1 = Member::factory()->create();
        $member2 = Member::factory()->create();

        JournalMembership::factory()->create(['member_id' => $member1->id]);
        JournalMembership::factory()->create(['member_id' => $member2->id]);

        $member1Memberships = JournalMembership::forMember($member1->id)->get();
        $member2Memberships = JournalMembership::forMember($member2->id)->get();

        $this->assertCount(1, $member1Memberships);
        $this->assertCount(1, $member2Memberships);
        $this->assertEquals($member1->id, $member1Memberships->first()->member_id);
    }

    /** @test */
    public function it_can_scope_by_member_type()
    {
        $type1 = MemberType::factory()->create();
        $type2 = MemberType::factory()->create();

        JournalMembership::factory()->create(['member_type_id' => $type1->id]);
        JournalMembership::factory()->create(['member_type_id' => $type2->id]);

        $type1Memberships = JournalMembership::byMemberType($type1->id)->get();
        $type2Memberships = JournalMembership::byMemberType($type2->id)->get();

        $this->assertCount(1, $type1Memberships);
        $this->assertCount(1, $type2Memberships);
        $this->assertEquals($type1->id, $type1Memberships->first()->member_type_id);
    }

    /** @test */
    public function it_can_determine_if_active()
    {
        $activeMembership = JournalMembership::factory()->create([
            'status' => 'active',
            'expires_at' => now()->addDays(30)
        ]);

        $this->assertTrue($activeMembership->isActive());

        $inactiveMembership = JournalMembership::factory()->create([
            'status' => 'inactive',
            'expires_at' => now()->addDays(30)
        ]);

        $this->assertFalse($inactiveMembership->isActive());

        $expiredMembership = JournalMembership::factory()->create([
            'status' => 'active',
            'expires_at' => now()->subDays(1)
        ]);

        $this->assertFalse($expiredMembership->isActive());
    }

    /** @test */
    public function it_can_activate_membership()
    {
        $membership = JournalMembership::factory()->create(['status' => 'inactive']);

        $membership->activate();

        $this->assertEquals('active', $membership->fresh()->status);
        $this->assertNotNull($membership->fresh()->assigned_at);
    }

    /** @test */
    public function it_can_deactivate_membership()
    {
        $membership = JournalMembership::factory()->create(['status' => 'active']);

        $membership->deactivate();

        $this->assertEquals('inactive', $membership->fresh()->status);
    }

    /** @test */
    public function it_can_suspend_membership()
    {
        $membership = JournalMembership::factory()->create(['status' => 'active']);

        $membership->suspend();

        $this->assertEquals('suspended', $membership->fresh()->status);
    }
}
