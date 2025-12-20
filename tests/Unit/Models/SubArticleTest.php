<?php

namespace Tests\Unit\Models;

use App\Models\SubArticle;
use App\Models\Article;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SubArticleTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $subArticle;

    protected function setUp(): void
    {
        parent::setUp();
        // Create related models first
        $article = Article::factory()->create();
        $comment = Comment::factory()->create();
        
        // Create sub article with valid foreign keys
        $this->subArticle = SubArticle::create([
            'article_id' => $article->id,
            'comment_id' => $comment->id,
            'abstract' => 'Test abstract',
            'status' => '1',
        ]);
    }

    /** @test */
    public function it_can_create_a_sub_article()
    {
        $article = Article::factory()->create();
        $comment = Comment::factory()->create();

        $subArticleData = [
            'article_id' => $article->id,
            'comment_id' => $comment->id,
            'abstract' => 'This is a test abstract for the sub article.',
            'status' => '1',
        ];

        $subArticle = SubArticle::create($subArticleData);

        $this->assertInstanceOf(SubArticle::class, $subArticle);
        $this->assertEquals($subArticleData['article_id'], $subArticle->article_id);
        $this->assertEquals($subArticleData['comment_id'], $subArticle->comment_id);
        $this->assertEquals($subArticleData['abstract'], $subArticle->abstract);
        $this->assertEquals($subArticleData['status'], $subArticle->status);
    }

    /** @test */
    public function it_has_correct_table_name()
    {
        $this->assertEquals('sub_articles', $this->subArticle->getTable());
    }

    /** @test */
    public function it_uses_soft_deletes()
    {
        $this->assertTrue(in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($this->subArticle)));
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = [
            'article_id',
            'comment_id',
            'abstract',
            'status',
            'created_at',
            'updated_at',
            'deleted_at',
        ];

        $this->assertEquals($fillable, $this->subArticle->getFillable());
    }

    /** @test */
    public function it_has_correct_dates()
    {
        // Check that the model has the expected date attributes
        $this->assertTrue($this->subArticle->usesTimestamps());
        $this->assertContains('created_at', $this->subArticle->getDates());
        $this->assertContains('updated_at', $this->subArticle->getDates());
        // deleted_at is handled by SoftDeletes trait
        $this->assertTrue(in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($this->subArticle)));
    }

    /** @test */
    public function it_has_status_select_constant()
    {
        $this->assertIsArray(SubArticle::STATUS_SELECT);
        $this->assertArrayHasKey('1', SubArticle::STATUS_SELECT);
        $this->assertArrayHasKey('10', SubArticle::STATUS_SELECT);
        $this->assertEquals('Pending', SubArticle::STATUS_SELECT['1']);
        $this->assertEquals('Approved', SubArticle::STATUS_SELECT['10']);
    }

    /** @test */
    public function it_belongs_to_an_article()
    {
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $this->subArticle->article());
    }

    /** @test */
    public function it_belongs_to_a_comment()
    {
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $this->subArticle->comment());
    }

    /** @test */
    public function it_belongs_to_a_member()
    {
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $this->subArticle->member());
    }

    /** @test */
    public function it_can_update_abstract()
    {
        $newAbstract = 'Updated abstract content for testing purposes.';

        $this->subArticle->update(['abstract' => $newAbstract]);

        $this->assertEquals($newAbstract, $this->subArticle->fresh()->abstract);
    }

    /** @test */
    public function it_can_update_status()
    {
        $this->subArticle->update(['status' => '2']);

        $this->assertEquals('2', $this->subArticle->fresh()->status);
    }

    /** @test */
    public function it_can_be_soft_deleted()
    {
        $subArticle = SubArticle::factory()->createQuietly();

        $subArticle->delete();

        $this->assertSoftDeleted($subArticle);
    }

    /** @test */
    public function it_can_scope_by_status()
    {
        // Get the existing article and comment from setUp
        $existingArticle = $this->subArticle->article;
        $existingComment = $this->subArticle->comment;

        // Create additional sub articles with different statuses using existing relationships
        SubArticle::create([
            'article_id' => $existingArticle->id,
            'comment_id' => $existingComment->id,
            'abstract' => 'Test abstract 2',
            'status' => '2',
        ]);

        SubArticle::create([
            'article_id' => $existingArticle->id,
            'comment_id' => $existingComment->id,
            'abstract' => 'Test abstract 3',
            'status' => '10',
        ]);

        $pendingArticles = SubArticle::where('status', '1')->get();
        $editorArticles = SubArticle::where('status', '2')->get();
        $approvedArticles = SubArticle::where('status', '10')->get();

        $this->assertCount(1, $pendingArticles);
        $this->assertCount(1, $editorArticles);
        $this->assertCount(1, $approvedArticles);
        $this->assertEquals('1', $pendingArticles->first()->status);
        $this->assertEquals('2', $editorArticles->first()->status);
        $this->assertEquals('10', $approvedArticles->first()->status);
    }

    /** @test */
    public function it_implements_has_media_interface()
    {
        $this->assertInstanceOf(\Spatie\MediaLibrary\HasMedia::class, $this->subArticle);
    }
}