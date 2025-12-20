<?php

namespace Tests\Unit\Models;

use App\Models\ContentCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ContentCategoryTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $contentCategory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->contentCategory = ContentCategory::factory()->create();
    }

    /** @test */
    public function it_can_create_a_content_category()
    {
        $data = [
            'name' => 'Research News',
            'slug' => 'research-news',
        ];

        $category = ContentCategory::create($data);

        $this->assertInstanceOf(ContentCategory::class, $category);
        $this->assertEquals($data['name'], $category->name);
        $this->assertEquals($data['slug'], $category->slug);
    }

    /** @test */
    public function it_has_correct_table_name()
    {
        $this->assertEquals('content_categories', $this->contentCategory->getTable());
    }

    /** @test */
    public function it_uses_soft_deletes()
    {
        $this->assertTrue(in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($this->contentCategory)));
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = [
            'name',
            'slug',
            'created_at',
            'updated_at',
            'deleted_at',
        ];

        $this->assertEquals($fillable, $this->contentCategory->getFillable());
    }

    /** @test */
    public function it_serializes_date_correctly()
    {
        $date = now();
        $this->contentCategory->created_at = $date;
        
        $serialized = $this->contentCategory->toArray();
        
        $this->assertEquals($date->format('Y-m-d H:i:s'), $serialized['created_at']);
    }

    /** @test */
    public function it_can_be_soft_deleted()
    {
        $category = ContentCategory::factory()->create();

        $category->delete();

        $this->assertSoftDeleted($category);
    }

    /** @test */
    public function it_can_be_restored_from_soft_delete()
    {
        $category = ContentCategory::factory()->create();

        $category->delete();
        $category->restore();

        $this->assertNotSoftDeleted($category);
    }

    /** @test */
    public function it_can_be_force_deleted()
    {
        $category = ContentCategory::factory()->create();

        $category->delete();
        $category->forceDelete();

        $this->assertModelMissing($category);
    }
}

