<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\ArticleCategory;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ArticleSubCategoryControllerTest extends TestCase
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
    public function admin_can_access_article_sub_categories_index()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.article-sub-categories.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.articleSubCategories.index');
        $response->assertViewHas('articleCategories');
    }

    /** @test */
    public function admin_can_create_article_sub_category()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.article-sub-categories.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.articleSubCategories.create');
        $response->assertViewHas('articleCategories');
    }

    /** @test */
    public function admin_can_store_article_sub_category()
    {
        Storage::fake('public');

        $parentCategory = ArticleCategory::factory()->create(['parent_id' => null]);

        $categoryData = [
            'category_name' => 'Test Sub Category',
            'parent_id' => $parentCategory->id,
            'cover_image' => UploadedFile::fake()->image('cover.jpg'),
        ];


        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.article-sub-categories.store'), $categoryData);

        $response->assertRedirect(route('admin.article-sub-categories.index'));

        $this->assertDatabaseHas('article_categories', [
            'category_name' => 'Test Sub Category',
            'parent_id' => $parentCategory->id,
        ]);
    }

    /** @test */
    public function store_requires_cover_image()
    {
        $parentCategory = ArticleCategory::factory()->create(['parent_id' => null]);

        $categoryData = [
            'category_name' => 'Test Sub Category',
            'parent_id' => $parentCategory->id,
        ];

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.article-sub-categories.store'), $categoryData);

        $response->assertSessionHasErrors('cover_image');
    }

    /** @test */
    public function admin_can_edit_article_sub_category()
    {
        $parentCategory = ArticleCategory::factory()->create(['parent_id' => null]);
        $subCategory = ArticleCategory::factory()->create(['parent_id' => $parentCategory->id]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.article-sub-categories.edit', $subCategory->id));

        $response->assertStatus(200);
        $response->assertViewIs('admin.articleSubCategories.edit');
        $response->assertViewHas(['articleCategory', 'articleCategories']);
    }

    /** @test */
    public function admin_can_update_article_sub_category()
    {
        $parentCategory = ArticleCategory::factory()->create(['parent_id' => null]);
        $subCategory = ArticleCategory::factory()->create(['parent_id' => $parentCategory->id]);

        $updatedData = [
            'category_name' => 'Updated Sub Category',
            'parent_id' => $parentCategory->id,
        ];

        $response = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.article-sub-categories.update', $subCategory->id), $updatedData);

        $response->assertRedirect(route('admin.article-sub-categories.index'));

        $this->assertDatabaseHas('article_categories', [
            'id' => $subCategory->id,
            'category_name' => 'Updated Sub Category',
        ]);
    }

    /** @test */
    public function admin_can_show_article_sub_category()
    {
        $parentCategory = ArticleCategory::factory()->create(['parent_id' => null]);
        $subCategory = ArticleCategory::factory()->create(['parent_id' => $parentCategory->id]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.article-sub-categories.show', $subCategory->id));

        $response->assertStatus(200);
        $response->assertViewIs('admin.articleSubCategories.show');
        $response->assertViewHas('articleCategory');
    }

    /** @test */
    public function admin_can_delete_article_sub_category()
    {
        $parentCategory = ArticleCategory::factory()->create(['parent_id' => null]);
        $subCategory = ArticleCategory::factory()->create(['parent_id' => $parentCategory->id]);

        $response = $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.article-sub-categories.destroy', $subCategory->id));

        $response->assertRedirect();
        $this->assertSoftDeleted('article_categories', ['id' => $subCategory->id]);
    }

    /** @test */
    public function admin_can_mass_destroy_article_sub_categories()
    {
        $parentCategory = ArticleCategory::factory()->create(['parent_id' => null]);
        $subCategories = ArticleCategory::factory()->count(3)->create(['parent_id' => $parentCategory->id]);
        $ids = $subCategories->pluck('id')->toArray();

        $response = $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.article-sub-categories.massDestroy'), [
                'ids' => $ids,
            ]);

        $response->assertStatus(204);
        foreach ($ids as $id) {
            $this->assertSoftDeleted('article_categories', ['id' => $id]);
        }
    }
}
