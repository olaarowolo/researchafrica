<?php

namespace Tests\Unit\Models;

use App\Models\ContentCategory;
use App\Models\ContentPage;
use App\Models\ContentTag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ContentPageTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $contentPage;

    protected function setUp(): void
    {
        parent::setUp();
        $this->contentPage = ContentPage::factory()->create();
    }

    /** @test */
    public function it_can_create_a_content_page()
    {
        $data = [
            'title' => 'About Us',
            'page_text' => 'This is the about us page content.',
            'excerpt' => 'Learn more about us.',
        ];

        $page = ContentPage::create($data);

        $this->assertInstanceOf(ContentPage::class, $page);
        $this->assertEquals($data['title'], $page->title);
        $this->assertEquals($data['page_text'], $page->page_text);
        $this->assertEquals($data['excerpt'], $page->excerpt);
    }

    /** @test */
    public function it_has_correct_table_name()
    {
        $this->assertEquals('content_pages', $this->contentPage->getTable());
    }

    /** @test */
    public function it_uses_soft_deletes()
    {
        $this->assertTrue(in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($this->contentPage)));
    }

    /** @test */
    public function it_uses_media_library()
    {
        $this->assertTrue(in_array('Spatie\MediaLibrary\InteractsWithMedia', class_uses($this->contentPage)));
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = [
            'title',
            'page_text',
            'excerpt',
            'created_at',
            'updated_at',
            'deleted_at',
        ];

        $this->assertEquals($fillable, $this->contentPage->getFillable());
    }

    /** @test */
    public function it_belongs_to_many_categories()
    {
        $category = ContentCategory::factory()->create();
        $this->contentPage->categories()->attach($category);

        $this->assertTrue($this->contentPage->categories->contains($category));
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->contentPage->categories);
    }

    /** @test */
    public function it_serializes_date_correctly()
    {
        $date = now();
        $this->contentPage->created_at = $date;
        
        $serialized = $this->contentPage->toArray();
        
        $this->assertEquals($date->format('Y-m-d H:i:s'), $serialized['created_at']);
    }

    /** @test */
    public function it_can_be_soft_deleted()
    {
        $page = ContentPage::factory()->create();

        $page->delete();

        $this->assertSoftDeleted($page);
    }

    /** @test */
    public function it_can_be_restored_from_soft_delete()
    {
        $page = ContentPage::factory()->create();

        $page->delete();
        $page->restore();

        $this->assertNotSoftDeleted($page);
    }

    /** @test */
    public function it_can_be_force_deleted()
    {
        $page = ContentPage::factory()->create();

        $page->delete();
        $page->forceDelete();

        $this->assertModelMissing($page);
    }
}

