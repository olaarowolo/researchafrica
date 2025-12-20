<?php

namespace Tests\Feature\Admin;

use App\Models\ArticleCategory;
use App\Models\JournalMembership;
use App\Models\Member;
use App\Models\MemberType;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class JournalMembershipControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $journal;
    protected $member;
    protected $memberType;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Admin User
        $this->admin = User::factory()->create();
        $role = Role::factory()->create(['title' => 'Admin']);
        $this->admin->roles()->attach($role);

        // Create Journal
        $this->journal = ArticleCategory::factory()->create(['is_journal' => true, 'status' => 'Active']);

        // Create Member
        $this->member = Member::factory()->create();

        // Create Member Type
        $this->memberType = MemberType::factory()->create(['name' => 'Author']);
    }

    /** @test */
    public function admin_can_access_journal_memberships_index()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.journal-memberships.index', $this->journal));

        $response->assertStatus(200);
        $response->assertViewIs('admin.memberships.index');
        $response->assertViewHas('journal');
        $response->assertViewHas('memberships');
    }

    /** @test */
    public function admin_can_create_journal_membership()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.journal-memberships.create', $this->journal));

        $response->assertStatus(200);
        $response->assertViewIs('admin.memberships.create');
        $response->assertViewHas('journal');
        $response->assertViewHas('availableMembers');
        $response->assertViewHas('memberTypes');
    }

    /** @test */
    public function admin_can_store_journal_membership()
    {
        $membershipData = [
            'member_id' => $this->member->id,
            'member_type_id' => $this->memberType->id,
            'status' => 'active',
            'expires_at' => now()->addYear()->format('Y-m-d'),
            'notes' => 'Test membership note',
        ];

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.journal-memberships.store', $this->journal), $membershipData);

        $response->assertRedirect(route('admin.journal-memberships.index', $this->journal));
        $this->assertDatabaseHas('journal_memberships', [
            'journal_id' => $this->journal->id,
            'member_id' => $this->member->id,
            'member_type_id' => $this->memberType->id,
            'status' => 'active',
        ]);
    }

    /** @test */
    public function admin_can_edit_journal_membership()
    {
        $membership = JournalMembership::factory()->create([
            'journal_id' => $this->journal->id,
            'member_id' => $this->member->id,
            'member_type_id' => $this->memberType->id,
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.journal-memberships.edit', [$this->journal, $membership]));

        $response->assertStatus(200);
        $response->assertViewIs('admin.memberships.edit');
        $response->assertViewHas('journal');
        $response->assertViewHas('member');
    }

    /** @test */
    public function admin_can_update_journal_membership()
    {
        $membership = JournalMembership::factory()->create([
            'journal_id' => $this->journal->id,
            'member_id' => $this->member->id,
            'member_type_id' => $this->memberType->id,
            'status' => 'active',
        ]);

        $newMemberType = MemberType::factory()->create(['name' => 'Editor']);

        $updateData = [
            'member_type_id' => $newMemberType->id,
            'status' => 'suspended',
            'expires_at' => now()->addYears(2)->format('Y-m-d'),
            'notes' => 'Updated note',
        ];

        $response = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.journal-memberships.update', [$this->journal, $membership]), $updateData);

        $response->assertRedirect(route('admin.journal-memberships.index', $this->journal));
        $this->assertDatabaseHas('journal_memberships', [
            'id' => $membership->id,
            'member_type_id' => $newMemberType->id,
            'status' => 'suspended',
            'notes' => 'Updated note',
        ]);
    }

    /** @test */
    public function admin_can_deactivate_journal_membership()
    {
        $membership = JournalMembership::factory()->create([
            'journal_id' => $this->journal->id,
            'member_id' => $this->member->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.journal-memberships.destroy', [$this->journal, $membership]));

        $response->assertRedirect(route('admin.journal-memberships.index', $this->journal));
        $this->assertDatabaseHas('journal_memberships', [
            'id' => $membership->id,
            'status' => 'inactive',
        ]);
    }

    /** @test */
    public function admin_can_approve_pending_membership()
    {
        $membership = JournalMembership::factory()->create([
            'journal_id' => $this->journal->id,
            'member_id' => $this->member->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.journal-memberships.approve', [$this->journal, $membership]));

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('journal_memberships', [
            'id' => $membership->id,
            'status' => 'active',
        ]);
    }

    /** @test */
    public function admin_can_reject_pending_membership()
    {
        $membership = JournalMembership::factory()->create([
            'journal_id' => $this->journal->id,
            'member_id' => $this->member->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.journal-memberships.reject', [$this->journal, $membership]));

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('journal_memberships', [
            'id' => $membership->id,
            'status' => 'inactive',
        ]);
    }

    /** @test */
    public function admin_can_suspend_active_membership()
    {
        $membership = JournalMembership::factory()->create([
            'journal_id' => $this->journal->id,
            'member_id' => $this->member->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.journal-memberships.suspend', [$this->journal, $membership]));

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('journal_memberships', [
            'id' => $membership->id,
            'status' => 'suspended',
        ]);
    }

    /** @test */
    public function admin_can_reactivate_suspended_membership()
    {
        $membership = JournalMembership::factory()->create([
            'journal_id' => $this->journal->id,
            'member_id' => $this->member->id,
            'status' => 'suspended',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.journal-memberships.reactivate', [$this->journal, $membership]));

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('journal_memberships', [
            'id' => $membership->id,
            'status' => 'active',
        ]);
    }

    /** @test */
    public function admin_can_bulk_update_memberships()
    {
        $memberships = JournalMembership::factory()->count(3)->create([
            'journal_id' => $this->journal->id,
            'status' => 'pending',
        ]);

        $membershipIds = $memberships->pluck('id')->toArray();

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.journal-memberships.bulk-update', $this->journal), [
                'membership_ids' => $membershipIds,
                'action' => 'approve',
                'notes' => 'Bulk approved',
            ]);

        $response->assertSessionHas('success');
        foreach ($memberships as $membership) {
            $this->assertDatabaseHas('journal_memberships', [
                'id' => $membership->id,
                'status' => 'active',
                'notes' => 'Bulk approved',
            ]);
        }
    }

    /** @test */
    public function admin_can_view_membership_statistics()
    {
        JournalMembership::factory()->count(5)->create([
            'journal_id' => $this->journal->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.journal-memberships.statistics', $this->journal));

        $response->assertStatus(200);
        $response->assertViewIs('admin.memberships.statistics');
        $response->assertViewHas('journal');
        $response->assertViewHas('statistics');
    }
}
