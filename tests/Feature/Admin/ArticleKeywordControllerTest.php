<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\ArticleKeyword;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleKeywordControllerTest extends TestCase
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
            'article_category_access',
            'article_category_create',
            'article_category_edit',
            'article_category_show',
            'article_category_delete',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['title' => $permission]);
            $role->permissions()->attach(Permission::where('title', $permission)->first());
        }
    }

    /** @test */
    public function admin_can_access_article_keywords_index()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.article-keywords.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.articleKeywords.index');
        $response->assertViewHas('articleKeywords');
    }

    /** @test */
    public function admin_can_create_article_keyword()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.article-keywords.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.articleKeywords.create');
    }

    /** @test */
    public function admin_can_store_article_keyword()
    {
        $keywordData = [
            'title' => 'Test Keyword',
            'status' => 'Active',
        ];

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.article-keywords.store'), $keywordData);

        $response->assertRedirect(route('admin.article-keywords.index'));
        $this->assertDatabaseHas('article_keywords', $keywordData);
    }

    /** @test */
    public function admin_can_edit_article_keyword()
    {
        $keyword = ArticleKeyword::factory()->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.article-keywords.edit', $keyword));

        $response->assertStatus(200);
        $response->assertViewIs('admin.articleKeywords.edit');
        $response->assertViewHas('articleKeyword');
    }

    /** @test */
    public function admin_can_update_article_keyword()
    {
        $keyword = ArticleKeyword::factory()->create();

        $updatedData = [
            'title' => 'Updated Keyword',
            'status' => 'Inactive',
        ];

        $response = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.article-keywords.update', $keyword), $updatedData);

        $response->assertRedirect(route('admin.article-keywords.index'));
        $this->assertDatabaseHas('article_keywords', $updatedData);
    }

    /** @test */
    public function admin_can_show_article_keyword()
    {
        $keyword = ArticleKeyword::factory()->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.article-keywords.show', $keyword));

        $response->assertStatus(200);
        $response->assertViewIs('admin.articleKeywords.show');
        $response->assertViewHas('articleKeyword');
    }

    /** @test */
    public function admin_can_delete_article_keyword()
    {
        $keyword = ArticleKeyword::factory()->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.article-keywords.destroy', $keyword));

        $response->assertRedirect();
        $this->assertDatabaseMissing('article_keywords', ['id' => $keyword->id]);
    }

    /** @test */
    public function admin_can_mass_destroy_article_keywords()
    {
        $keywords = ArticleKeyword::factory()->count(3)->create();
        $ids = $keywords->pluck('id')->toArray();

        $response = $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.article-keywords.massDestroy'), [
                'ids' => $ids,
            ]);

        $response->assertStatus(204);
        foreach ($ids as $id) {
            $this->assertDatabaseMissing('article_keywords', ['id' => $id]);
        }
    }

    /** @test */
    public function unauthorized_user_cannot_access_keywords()
    {
        $response = $this->get(route('admin.article-keywords.index'));
        $response->assertRedirect(route('admin.login'));
    }
}
