<?php

namespace Tests\Unit\Models;

use App\Models\JournalEditorialBoard;
use App\Models\ArticleCategory;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class JournalEditorialBoardTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $editorialBoard;


    protected function setUp(): void
    {
        parent::setUp();
        // Create a default member with a position that won't conflict with specific position tests
        $this->editorialBoard = JournalEditorialBoard::factory()->active()->create([
            'position' => 'Editorial Board Member'
        ]);
    }

    /** @test */
    public function it_can_create_a_journal_editorial_board_member()
    {
        $boardData = [
            'journal_id' => ArticleCategory::factory()->create(['is_journal' => true])->id,
            'member_id' => Member::factory()->create()->id,
            'position' => 'Associate Editor',
            'department' => 'Computer Science',
            'institution' => 'University of Lagos',
            'bio' => 'Expert in machine learning and AI research.',
            'orcid_id' => '0000-0001-2345-6789',
            'is_active' => true,
            'display_order' => 1,
        ];

        $boardMember = JournalEditorialBoard::create($boardData);

        $this->assertInstanceOf(JournalEditorialBoard::class, $boardMember);
        $this->assertEquals('Associate Editor', $boardMember->position);
        $this->assertEquals('Computer Science', $boardMember->department);
        $this->assertTrue($boardMember->is_active);
    }

    /** @test */
    public function it_has_correct_table_name()
    {
        $this->assertEquals('journal_editorial_boards', $this->editorialBoard->getTable());
    }

    /** @test */
    public function it_uses_soft_deletes()
    {
        $this->assertTrue(in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($this->editorialBoard)));
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = [
            'journal_id',
            'member_id',
            'position',
            'department',
            'institution',
            'bio',
            'orcid_id',
            'term_start',
            'term_end',
            'is_active',
            'display_order',
        ];

        $this->assertEquals($fillable, $this->editorialBoard->getFillable());
    }

    /** @test */
    public function it_has_correct_casts()
    {
        $casts = $this->editorialBoard->getCasts();

        $this->assertEquals('boolean', $casts['is_active']);
        $this->assertEquals('date', $casts['term_start']);
        $this->assertEquals('date', $casts['term_end']);
        $this->assertEquals('integer', $casts['display_order']);
    }

    /** @test */
    public function it_belongs_to_a_journal()
    {
        $this->assertInstanceOf(ArticleCategory::class, $this->editorialBoard->journal);
        $this->assertEquals($this->editorialBoard->journal_id, $this->editorialBoard->journal->id);
    }

    /** @test */
    public function it_belongs_to_a_member()
    {
        $this->assertInstanceOf(Member::class, $this->editorialBoard->member);
        $this->assertEquals($this->editorialBoard->member_id, $this->editorialBoard->member->id);
    }

    /** @test */
    public function it_has_full_name_attribute()
    {
        $expectedName = $this->editorialBoard->member->fullname;
        $this->assertEquals($expectedName, $this->editorialBoard->full_name);
    }

    /** @test */
    public function it_can_scope_active_members()
    {
        // Create active and inactive members
        JournalEditorialBoard::factory()->create(['is_active' => true]);
        JournalEditorialBoard::factory()->create(['is_active' => false]);

        $activeMembers = JournalEditorialBoard::active()->get();

        $this->assertCount(2, $activeMembers); // The setUp one is also active
        $activeMembers->each(function ($member) {
            $this->assertTrue($member->is_active);
        });
    }

    /** @test */
    public function it_can_scope_by_journal()
    {
        $journal1 = ArticleCategory::factory()->create(['is_journal' => true]);
        $journal2 = ArticleCategory::factory()->create(['is_journal' => true]);

        JournalEditorialBoard::factory()->create(['journal_id' => $journal1->id]);
        JournalEditorialBoard::factory()->create(['journal_id' => $journal2->id]);

        $journal1Members = JournalEditorialBoard::forJournal($journal1->id)->get();
        $journal2Members = JournalEditorialBoard::forJournal($journal2->id)->get();

        $this->assertCount(1, $journal1Members);
        $this->assertCount(1, $journal2Members);
        $this->assertEquals($journal1->id, $journal1Members->first()->journal_id);
        $this->assertEquals($journal2->id, $journal2Members->first()->journal_id);
    }

    /** @test */
    public function it_can_scope_by_position()
    {
        JournalEditorialBoard::factory()->create(['position' => 'Editor-in-Chief']);
        JournalEditorialBoard::factory()->create(['position' => 'Associate Editor']);

        $editorsInChief = JournalEditorialBoard::byPosition('Editor-in-Chief')->get();
        $associateEditors = JournalEditorialBoard::byPosition('Associate Editor')->get();

        $this->assertCount(1, $editorsInChief);
        $this->assertCount(1, $associateEditors);
        $this->assertEquals('Editor-in-Chief', $editorsInChief->first()->position);
        $this->assertEquals('Associate Editor', $associateEditors->first()->position);
    }

    /** @test */
    public function it_can_scope_ordered_by_display()
    {
        // Create members with different display orders (not using setUp member)
        $member1 = JournalEditorialBoard::factory()->create(['display_order' => 3]);
        $member2 = JournalEditorialBoard::factory()->create(['display_order' => 1]);
        $member3 = JournalEditorialBoard::factory()->create(['display_order' => 2]);

        $orderedMembers = JournalEditorialBoard::orderedByDisplay()->get();

        // Find the positions of our test members in the ordered results
        $member2Position = $orderedMembers->search(function ($member) use ($member2) {
            return $member->id === $member2->id;
        });
        $member3Position = $orderedMembers->search(function ($member) use ($member3) {
            return $member->id === $member3->id;
        });
        $member1Position = $orderedMembers->search(function ($member) use ($member1) {
            return $member->id === $member1->id;
        });

        // Assert that member2 (display_order: 1) comes before member3 (display_order: 2)
        // and member3 comes before member1 (display_order: 3)
        $this->assertLessThan($member3Position, $member2Position);
        $this->assertLessThan($member1Position, $member3Position);
    }

    /** @test */
    public function it_can_determine_if_active()
    {
        // Test active member
        $activeMember = JournalEditorialBoard::factory()->create([
            'is_active' => true,
            'term_end' => now()->addDays(30)
        ]);

        $this->assertTrue($activeMember->isActive());

        // Test inactive member
        $inactiveMember = JournalEditorialBoard::factory()->create([
            'is_active' => false,
            'term_end' => now()->addDays(30)
        ]);

        $this->assertFalse($inactiveMember->isActive());

        // Test member with expired term
        $expiredMember = JournalEditorialBoard::factory()->create([
            'is_active' => true,
            'term_end' => now()->subDays(1)
        ]);

        $this->assertFalse($expiredMember->isActive());
    }

    /** @test */
    public function it_can_be_soft_deleted()
    {
        $boardMember = JournalEditorialBoard::factory()->create();

        $boardMember->delete();

        $this->assertSoftDeleted($boardMember);
    }

    /** @test */
    public function it_can_be_restored_from_soft_delete()
    {
        $boardMember = JournalEditorialBoard::factory()->create();

        $boardMember->delete();
        $boardMember->restore();

        $this->assertNotSoftDeleted($boardMember);
    }

    /** @test */
    public function it_can_be_force_deleted()
    {
        $boardMember = JournalEditorialBoard::factory()->create();

        $boardMember->delete();
        $boardMember->forceDelete();

        $this->assertModelMissing($boardMember);
    }
}