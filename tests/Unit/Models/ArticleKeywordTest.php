<?php

namespace Tests\Unit\Models;

use App\Models\ArticleKeyword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ArticleKeywordTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $articleKeyword;

    protected function setUp(): void
    {
        parent::setUp();
        $this->articleKeyword = ArticleKeyword::factory()->create();
    }

    /** @test */
    public function it_can_create_an_article_keyword()
    {
        $keywordData = [
            'title' => 'Machine Learning',
            'status' => 'Active',
        ];

        $keyword = ArticleKeyword::create($keywordData);

        $this->assertInstanceOf(ArticleKeyword::class, $keyword);
        $this->assertEquals('Machine Learning', $keyword->title);
        $this->assertEquals('Active', $keyword->status);
    }

    /** @test */
    public function it_has_correct_table_name()
    {
        $this->assertEquals('article_keywords', $this->articleKeyword->getTable());
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = [
            'title',
            'status',
            'created_at',
            'updated_at',
        ];

        $this->assertEquals($fillable, $this->articleKeyword->getFillable());
    }

    /** @test */
    public function it_has_correct_dates()
    {
        $dates = [
            'created_at',
            'updated_at',
        ];

        foreach ($dates as $date) {
            $this->assertContains($date, $this->articleKeyword->getDates());
        }
    }

    /** @test */
    public function it_has_status_select_constant()
    {
        $expectedStatuses = [
            'Active' => 'Active',
            'Inactive' => 'Inactive',
        ];

        $this->assertEquals($expectedStatuses, ArticleKeyword::STATUS_SELECT);
    }

    /** @test */
    public function it_can_be_active()
    {
        $activeKeyword = ArticleKeyword::factory()->create(['status' => 'Active']);
        $inactiveKeyword = ArticleKeyword::factory()->create(['status' => 'Inactive']);

        $this->assertEquals('Active', $activeKeyword->status);
        $this->assertEquals('Inactive', $inactiveKeyword->status);
    }

    /** @test */
    public function it_can_be_inactive()
    {
        $keyword = ArticleKeyword::factory()->create(['status' => 'Inactive']);

        $this->assertEquals('Inactive', $keyword->status);
    }

    /** @test */
    public function it_can_update_title()
    {
        $newTitle = 'Artificial Intelligence';

        $this->articleKeyword->update(['title' => $newTitle]);

        $this->assertEquals($newTitle, $this->articleKeyword->fresh()->title);
    }

    /** @test */
    public function it_can_update_status()
    {
        $this->articleKeyword->update(['status' => 'Inactive']);

        $this->assertEquals('Inactive', $this->articleKeyword->fresh()->status);
    }

    /** @test */
    public function it_can_scope_active_keywords()
    {
        // Clear existing data and create fresh test data
        ArticleKeyword::query()->delete();
        
        ArticleKeyword::factory()->create(['status' => 'Active']);
        ArticleKeyword::factory()->create(['status' => 'Inactive']);

        $activeKeywords = ArticleKeyword::where('status', 'Active')->get();

        $this->assertCount(1, $activeKeywords);
        $this->assertEquals('Active', $activeKeywords->first()->status);
    }

    /** @test */
    public function it_can_scope_inactive_keywords()
    {
        // Clear existing data and create fresh test data
        ArticleKeyword::query()->delete();
        
        ArticleKeyword::factory()->create(['status' => 'Active']);
        ArticleKeyword::factory()->create(['status' => 'Inactive']);

        $inactiveKeywords = ArticleKeyword::where('status', 'Inactive')->get();

        $this->assertCount(1, $inactiveKeywords);
        $this->assertEquals('Inactive', $inactiveKeywords->first()->status);
    }
}