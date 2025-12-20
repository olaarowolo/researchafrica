<?php

namespace Tests\Unit\Models;

use App\Models\ContentTag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ContentTagTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $contentTag;

    protected function setUp(): void
    {
        parent::setUp();
        $this->contentTag = ContentTag::factory()->create();
    }

    /** @test */
    public function it_can_create_a_content_tag()
    {
        $data = [
            'name' => 'Technology',
            'slug' => 'technology',
        ];

        $tag = ContentTag::create($data);

        $this->assertInstanceOf(ContentTag::class, $tag);
        $this->assertEquals($data['name'], $tag->name);
        $this->assertEquals($data['slug'], $tag->slug);
    }

    /** @test */
    public function it_has_correct_table_name()
    {
        $this->assertEquals('content_tags', $this->contentTag->getTable());
    }

    /** @test */
    public function it_uses_soft_deletes()
    {
        $this->assertTrue(in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($this->contentTag)));
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

        $this->assertEquals($fillable, $this->contentTag->getFillable());
    }

    /** @test */
    public function it_serializes_date_correctly()
    {
        $date = now();
        $this->contentTag->created_at = $date;
        
        $serialized = $this->contentTag->toArray();
        
        $this->assertEquals($date->format('Y-m-d H:i:s'), $serialized['created_at']);
    }

    /** @test */
    public function it_can_be_soft_deleted()
    {
        $tag = ContentTag::factory()->create();

        $tag->delete();

        $this->assertSoftDeleted($tag);
    }

    /** @test */
    public function it_can_be_restored_from_soft_delete()
    {
        $tag = ContentTag::factory()->create();

        $tag->delete();
        $tag->restore();

        $this->assertNotSoftDeleted($tag);
    }

    /** @test */
    public function it_can_be_force_deleted()
    {
        $tag = ContentTag::factory()->create();

        $tag->delete();
        $tag->forceDelete();

        $this->assertModelMissing($tag);
    }
}

