<?php

namespace Tests\Unit\Models;

use App\Models\Article;
use App\Models\DownloadArticle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DownloadArticleTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $downloadArticle;

    protected function setUp(): void
    {
        parent::setUp();
        $this->downloadArticle = DownloadArticle::factory()->create();
    }

    /** @test */
    public function it_can_create_a_download_article_record()
    {
        $article = Article::factory()->create();
        $data = [
            'article_id' => $article->id,
            'download' => 10,
        ];

        $downloadArticle = DownloadArticle::create($data);

        $this->assertInstanceOf(DownloadArticle::class, $downloadArticle);
        $this->assertEquals($article->id, $downloadArticle->article_id);
        $this->assertEquals(10, $downloadArticle->download);
    }

    /** @test */
    public function it_has_correct_table_name()
    {
        $this->assertEquals('download_articles', $this->downloadArticle->getTable());
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = [
            'article_id',
            'download',
            'created_at',
            'updated_at',
        ];

        $this->assertEquals($fillable, $this->downloadArticle->getFillable());
    }

    /** @test */
    public function it_belongs_to_an_article()
    {
        $this->assertInstanceOf(Article::class, $this->downloadArticle->article);
        $this->assertEquals($this->downloadArticle->article_id, $this->downloadArticle->article->id);
    }
}
