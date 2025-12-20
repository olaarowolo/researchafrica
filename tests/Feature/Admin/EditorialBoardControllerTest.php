<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Role;
use App\Models\ArticleCategory;
use App\Models\JournalEditorialBoard;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EditorialBoardControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $journal;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an admin role
        $role = Role::factory()->create(['title' => 'Admin']);

        // Create an admin user
        $this->admin = User::factory()->create();
        $this->admin->roles()->attach($role);

        // Create a journal
        $this->journal = ArticleCategory::factory()->create([
            'is_journal' => true,
            'journal_acronym' => 'TEST'
        ]);
    }

    /** @test */
    public function admin_can_view_editorial_board_index()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.editorial-board.index', $this->journal->id));

        $response->assertStatus(200);
        $response->assertViewIs('admin.editorial-boards.index');
        $response->assertViewHas(['journal', 'editorialBoard', 'analytics', 'positions']);
    }

    /** @test */
    public function admin_can_view_create_board_member_page()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.editorial-board.create', $this->journal->id));

        $response->assertStatus(200);
        $response->assertViewIs('admin.editorial-boards.create');
        $response->assertViewHas(['journal', 'availableMembers', 'positions']);
    }


/** @test */
    public function admin_can_store_new_board_member()
    {
        $this->withoutExceptionHandling();
        $member = Member::factory()->create();

        $data = [
            'member_id' => $member->id,
            'position' => 'Editor-in-Chief',
            'department' => 'Computer Science',
            'institution' => 'University of Test',
            'bio' => 'Test bio',
            'term_start' => now()->format('Y-m-d'),
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.editorial-board.store', $this->journal->id), $data);

        $response->assertRedirect(route('admin.editorial-board.index', $this->journal->id));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('journal_editorial_boards', [
            'journal_id' => $this->journal->id,
            'member_id' => $member->id,
            'position' => 'Editor-in-Chief',
        ]);
    }

    /** @test */
    public function admin_can_view_edit_board_member_page()
    {
        $boardMember = JournalEditorialBoard::factory()->create([
            'journal_id' => $this->journal->id
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.editorial-board.edit', [$this->journal->id, $boardMember->id]));

        $response->assertStatus(200);
        $response->assertViewIs('admin.editorial-boards.edit');
        $response->assertViewHas(['journal', 'member', 'positions']);
    }

    /** @test */
    public function admin_can_update_board_member()
    {
        $boardMember = JournalEditorialBoard::factory()->create([
            'journal_id' => $this->journal->id,
            'position' => 'Associate Editor'
        ]);

        $data = [
            'position' => 'Editor-in-Chief',
            'department' => 'Updated Dept',
            'institution' => $boardMember->institution, // Keep existing
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('admin.editorial-board.update', [$this->journal->id, $boardMember->id]), $data);

        $response->assertRedirect(route('admin.editorial-board.index', $this->journal->id));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('journal_editorial_boards', [
            'id' => $boardMember->id,
            'position' => 'Editor-in-Chief',
            'department' => 'Updated Dept',
        ]);
    }

    /** @test */
    public function admin_can_delete_board_member()
    {
        $boardMember = JournalEditorialBoard::factory()->create([
            'journal_id' => $this->journal->id
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.editorial-board.destroy', [$this->journal->id, $boardMember->id]));

        $response->assertRedirect(route('admin.editorial-board.index', $this->journal->id));
        $response->assertSessionHas('success');

        $this->assertSoftDeleted('journal_editorial_boards', [
            'id' => $boardMember->id
        ]);
    }

    /** @test */
    public function admin_can_reorder_board_members()
    {
        $member1 = JournalEditorialBoard::factory()->create(['journal_id' => $this->journal->id, 'display_order' => 1]);
        $member2 = JournalEditorialBoard::factory()->create(['journal_id' => $this->journal->id, 'display_order' => 2]);

        $response = $this->actingAs($this->admin)
            ->postJson(route('admin.editorial-board.reorder', $this->journal->id), [
                'member_ids' => [$member2->id, $member1->id]
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertEquals(1, $member2->fresh()->display_order);
        $this->assertEquals(2, $member1->fresh()->display_order);
    }
}
