<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\ContentTag;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentTagControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['title' => 'Admin']);
        $this->admin = Admin::factory()->create();
        $this->admin->roles()->attach($role);

        $permissions = [
            'content_tag_access',
            'content_tag_create',
            'content_tag_edit',
            'content_tag_show',
            'content_tag_delete',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['title' => $permission]);
            $role->permissions()->attach(Permission::where('title', $permission)->first());
        }
    }

    /** @test */
    public function admin_can_access_content_tags_index()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.content-tags.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.contentTags.index');
        $response->assertViewHas('contentTags');
    }

    /** @test */
    public function admin_can_create_content_tag()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.content-tags.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.contentTags.create');
    }

    /** @test */
    public function admin_can_store_content_tag()
    {
        $tagData = [
            'name' => 'Test Tag',
            'slug' => 'test-tag',
        ];

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.content-tags.store'), $tagData);

        $response->assertRedirect(route('admin.content-tags.index'));

        $this->assertDatabaseHas('content_tags', [
            'name' => 'Test Tag',
            'slug' => 'test-tag',
        ]);
    }

    /** @test */
    public function admin_can_edit_content_tag()
    {
        $tag = ContentTag::factory()->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.content-tags.edit', $tag->id));

        $response->assertStatus(200);
        $response->assertViewIs('admin.contentTags.edit');
        $response->assertViewHas('contentTag');
    }

    /** @test */
    public function admin_can_update_content_tag()
    {
        $tag = ContentTag::factory()->create();

        $updatedData = [
            'name' => 'Updated Tag',
            'slug' => 'updated-tag',
        ];

        $response = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.content-tags.update', $tag->id), $updatedData);

        $response->assertRedirect(route('admin.content-tags.index'));

        $this->assertDatabaseHas('content_tags', [
            'id' => $tag->id,
            'name' => 'Updated Tag',
            'slug' => 'updated-tag',
        ]);
    }

    /** @test */
    public function admin_can_show_content_tag()
    {
        $tag = ContentTag::factory()->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.content-tags.show', $tag->id));

        $response->assertStatus(200);
        $response->assertViewIs('admin.contentTags.show');
        $response->assertViewHas('contentTag');
    }

    /** @test */
    public function admin_can_delete_content_tag()
    {
        $tag = ContentTag::factory()->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.content-tags.destroy', $tag->id));

        $response->assertRedirect();
        $this->assertSoftDeleted('content_tags', ['id' => $tag->id]);
    }

    /** @test */
    public function admin_can_mass_destroy_content_tags()
    {
        $tags = ContentTag::factory()->count(3)->create();
        $ids = $tags->pluck('id')->toArray();

        $response = $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.content-tags.massDestroy'), [
                'ids' => $ids,
            ]);

        $response->assertStatus(204);
        foreach ($ids as $id) {
            $this->assertSoftDeleted('content_tags', ['id' => $id]);
        }
    }
}
