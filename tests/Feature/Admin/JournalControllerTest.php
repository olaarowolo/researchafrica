<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\ArticleCategory;
use App\Models\MemberType;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class JournalControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['title' => 'Admin']);
        $this->admin = Admin::factory()->create();
        $this->admin->roles()->attach($role);

        // Create MemberType for create view
        MemberType::factory()->create(['name' => 'Author']);
    }

    /** @test */
    public function admin_can_access_journals_index()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.journals.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.journals.index');
        $response->assertViewHas('journals');
    }

    /** @test */
    public function admin_can_create_journal()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.journals.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.journals.create');
        $response->assertViewHas('memberTypes');
    }

    /** @test */
    public function admin_can_store_journal()
    {
        $journalData = [
            'name' => 'Test Journal',
            'display_name' => 'Test Journal Display',
            'journal_acronym' => 'TJ',
            'journal_slug' => 'test-journal',
            'description' => 'Description of Test Journal',
            'status' => 'Active',
        ];

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.journals.store'), $journalData);

        $response->assertRedirect();
        $this->assertDatabaseHas('article_categories', [
            'name' => 'Test Journal',
            'journal_acronym' => 'TJ',
            'is_journal' => true,
        ]);
    }

    /** @test */
    public function admin_can_show_journal()
    {
        $journal = ArticleCategory::factory()->create([
            'is_journal' => true,
            'journal_acronym' => 'TJ',
            'journal_slug' => 'tj'
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.journals.show', $journal->id));

        $response->assertStatus(200);
        $response->assertViewIs('admin.journals.show');
        $response->assertViewHas(['journal', 'analytics']);
    }

    /** @test */
    public function admin_can_edit_journal()
    {
        $journal = ArticleCategory::factory()->create([
            'is_journal' => true,
            'journal_acronym' => 'TJ',
            'journal_slug' => 'tj'
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.journals.edit', $journal->id));

        $response->assertStatus(200);
        $response->assertViewIs('admin.journals.edit');
        $response->assertViewHas(['journal', 'memberTypes']);
    }

    /** @test */
    public function admin_can_update_journal()
    {
        $journal = ArticleCategory::factory()->create([
            'is_journal' => true,
            'journal_acronym' => 'TJ',
            'journal_slug' => 'tj'
        ]);

        $updatedData = [
            'name' => 'Updated Journal',
            'display_name' => 'Updated Journal Display',
            'journal_acronym' => 'UJ',
            'journal_slug' => 'updated-journal',
            'status' => 'Inactive',
        ];

        $response = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.journals.update', $journal->id), $updatedData);

        $response->assertRedirect(route('admin.journals.show', $journal));

        $this->assertDatabaseHas('article_categories', [
            'id' => $journal->id,
            'name' => 'Updated Journal',
            'journal_acronym' => 'UJ',
        ]);
    }

    /** @test */
    public function admin_can_delete_journal()
    {
        $journal = ArticleCategory::factory()->create([
            'is_journal' => true,
            'journal_acronym' => 'TJ',
            'journal_slug' => 'tj'
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.journals.destroy', $journal->id));

        $response->assertRedirect(route('admin.journals.index'));
        $this->assertSoftDeleted('article_categories', ['id' => $journal->id]);
    }

    /** @test */
    public function admin_can_access_journal_settings()
    {
        $journal = ArticleCategory::factory()->create([
            'is_journal' => true,
            'journal_acronym' => 'TJ',
            'journal_slug' => 'tj'
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.journals.settings', $journal->id));

        $response->assertStatus(200);
        $response->assertViewIs('admin.journals.settings');
        $response->assertViewHas('journal');
    }

    /** @test */
    public function admin_can_update_journal_settings()
    {
        $journal = ArticleCategory::factory()->create([
            'is_journal' => true,
            'journal_acronym' => 'TJ',
            'journal_slug' => 'tj'
        ]);

        $settingsData = [
            'subdomain' => 'myjournal',
        ];

        $response = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.journals.settings.update', $journal->id), $settingsData);

        $response->assertRedirect(route('admin.journals.settings', $journal));
        $this->assertDatabaseHas('article_categories', [
            'id' => $journal->id,
            'subdomain' => 'myjournal',
        ]);
    }

    /** @test */
    public function admin_can_access_journal_analytics()
    {
        $journal = ArticleCategory::factory()->create([
            'is_journal' => true,
            'journal_acronym' => 'TJ',
            'journal_slug' => 'tj'
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.journals.analytics', $journal->id));

        $response->assertStatus(200);
        $response->assertViewIs('admin.journals.analytics');
        $response->assertViewHas(['journal', 'analytics']);
    }
}
