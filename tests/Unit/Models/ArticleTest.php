<?php

namespace Tests\Unit\Models;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\ArticleKeyword;
use App\Models\Member;
use App\Models\Comment;
use App\Models\Bookmark;
use App\Models\SubArticle;
use App\Models\EditorAccept;
use App\Models\PublisherAccept;
use App\Models\ReviewerAccept;
use App\Models\ReviewerAcceptFinal;
use App\Models\ViewArticle;
use App\Models\DownloadArticle;
use App\Models\PurchasedArticle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ArticleTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $article;

    protected function setUp(): void
    {
        parent::setUp();
        $this->article = Article::factory()->create();
        Storage::fake('public');
    }

    /** @test */
    public function it_can_create_an_article()
    {
        $member = Member::factory()->create();
        $category = ArticleCategory::factory()->create();

        $articleData = [
            'member_id' => $member->id,
            'title' => 'Test Article',
            'article_category_id' => $category->id,
            'author_name' => 'John Doe',
            'article_status' => 1,
            'access_type' => 1,
        ];

        $article = Article::create($articleData);

        $this->assertInstanceOf(Article::class, $article);
        $this->assertEquals('Test Article', $article->title);
        $this->assertEquals($member->id, $article->member_id);
        $this->assertEquals($category->id, $article->article_category_id);
    }

    /** @test */
    public function it_has_correct_table_name()
    {
        $this->assertEquals('articles', $this->article->getTable());
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = [
            'journal_id',
            'member_id',
            'access_type',
            'title',
            'article_category_id',
            'article_sub_category_id',
            'author_name',
            'other_authors',
            'corresponding_authors',
            'institute_organization',
            'amount',
            'doi_link',
            'volume',
            'issue_no',
            'publish_date',
            'published_online',
            'is_recommended',
            'storage_disk',
            'file_path',
            'article_status',
            'created_at',
            'updated_at',
            'deleted_at',
        ];

        $this->assertEquals($fillable, $this->article->getFillable());
    }

    /** @test */
    public function it_has_correct_date_format()
    {
        $now = Carbon::now();
        $article = Article::factory()->create(['created_at' => $now]);

        $this->assertEquals($now->format('Y-m-d H:i:s'), $article->created_at->format('Y-m-d H:i:s'));
    }

    /** @test */
    public function it_has_correct_article_status_constants()
    {
        $expectedStatus = [
            '1' => 'Pending',
            '2' => 'Reviewing',
            '3' => 'Published',
        ];

        $this->assertEquals($expectedStatus, Article::ARTICLE_STATUS);
    }

    /** @test */
    public function it_has_correct_access_type_constants()
    {
        $expectedAccess = [
            '1' => 'Open Access',
            '2' => 'Close Access',
        ];

        $this->assertEquals($expectedAccess, Article::ACCESS_TYPE);
    }

    /** @test */
    public function it_has_belongs_to_member_relationship()
    {
        $member = Member::factory()->create();
        $article = Article::factory()->create(['member_id' => $member->id]);

        $this->assertInstanceOf(Member::class, $article->member);
        $this->assertEquals($member->id, $article->member->id);
    }

    /** @test */
    public function it_has_belongs_to_article_category_relationship()
    {
        $category = ArticleCategory::factory()->create();
        $article = Article::factory()->create(['article_category_id' => $category->id]);

        $this->assertInstanceOf(ArticleCategory::class, $article->article_category);
        $this->assertEquals($category->id, $article->article_category->id);
    }

    /** @test */
    public function it_has_belongs_to_journal_relationship()
    {
        $journal = ArticleCategory::factory()->create(['is_journal' => true]);
        $article = Article::factory()->create(['journal_id' => $journal->id]);

        $this->assertInstanceOf(ArticleCategory::class, $article->journal);
        $this->assertEquals($journal->id, $article->journal->id);
    }

    /** @test */
    public function it_has_many_comments()
    {
        $article = Article::factory()->create();
        Comment::factory()->count(3)->create(['article_id' => $article->id]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $article->comments);
        $this->assertCount(3, $article->comments);
        $this->assertInstanceOf(Comment::class, $article->comments->first());
    }

    /** @test */
    public function it_has_many_sub_articles()
    {
        $article = Article::factory()->create();
        SubArticle::factory()->count(2)->create(['article_id' => $article->id]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $article->sub_articles);
        $this->assertCount(2, $article->sub_articles);
    }

    /** @test */
    public function it_has_one_view_article()
    {
        $article = Article::factory()->create();
        ViewArticle::factory()->create(['article_id' => $article->id]);

        $this->assertInstanceOf(ViewArticle::class, $article->views);
        $this->assertEquals($article->id, $article->views->article_id);
    }

    /** @test */
    public function it_has_one_download_article()
    {
        $article = Article::factory()->create();
        DownloadArticle::factory()->create(['article_id' => $article->id]);

        $this->assertInstanceOf(DownloadArticle::class, $article->downloads);
        $this->assertEquals($article->id, $article->downloads->article_id);
    }

    /** @test */
    public function it_has_many_purchased_articles()
    {
        $article = Article::factory()->create();
        PurchasedArticle::factory()->count(3)->create(['article_id' => $article->id]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $article->purchasedArticle);
        $this->assertCount(3, $article->purchasedArticle);
    }

    /** @test */
    public function it_has_one_editor_accept()
    {
        $article = Article::factory()->create();
        EditorAccept::factory()->create(['article_id' => $article->id]);

        $this->assertInstanceOf(EditorAccept::class, $article->editor_accept);
        $this->assertEquals($article->id, $article->editor_accept->article_id);
    }

    /** @test */
    public function it_has_one_publisher_accept()
    {
        $article = Article::factory()->create();
        PublisherAccept::factory()->create(['article_id' => $article->id]);

        $this->assertInstanceOf(PublisherAccept::class, $article->publisher_accept);
        $this->assertEquals($article->id, $article->publisher_accept->article_id);
    }

    /** @test */
    public function it_has_one_reviewer_accept()
    {
        $article = Article::factory()->create();
        ReviewerAccept::factory()->create(['article_id' => $article->id]);

        $this->assertInstanceOf(ReviewerAccept::class, $article->reviewer_accept);
        $this->assertEquals($article->id, $article->reviewer_accept->article_id);
    }

    /** @test */
    public function it_has_one_reviewer_accept_final()
    {
        $article = Article::factory()->create();
        ReviewerAcceptFinal::factory()->create(['article_id' => $article->id]);

        $this->assertInstanceOf(ReviewerAcceptFinal::class, $article->reviewer_accept_final);
        $this->assertEquals($article->id, $article->reviewer_accept_final->article_id);
    }

    /** @test */
    public function it_has_many_to_many_article_keywords()
    {
        $article = Article::factory()->create();
        $keyword = ArticleKeyword::factory()->create();

        $article->article_keywords()->attach($keyword->id);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $article->article_keywords);
        $this->assertCount(1, $article->article_keywords);
        $this->assertInstanceOf(ArticleKeyword::class, $article->article_keywords->first());
    }

    /** @test */
    public function it_scopes_to_published_articles()
    {
        $publishedArticle1 = Article::factory()->create(['article_status' => 3]);
        $publishedArticle2 = Article::factory()->create(['article_status' => 3]);
        $draftArticle = Article::factory()->create(['article_status' => 1]);

        $published = Article::publish()->get();

        $this->assertCount(2, $published);
        $this->assertTrue($published->contains($publishedArticle1));
        $this->assertTrue($published->contains($publishedArticle2));
        $this->assertFalse($published->contains($draftArticle));
    }

    /** @test */
    public function it_scopes_to_journal_articles()
    {
        $journalId = 1;
        $article1 = Article::factory()->create(['journal_id' => $journalId]);
        $article2 = Article::factory()->create(['journal_id' => $journalId]);
        $article3 = Article::factory()->create(['journal_id' => null]);

        $journalArticles = Article::forJournal($journalId)->get();

        $this->assertCount(2, $journalArticles);
        $this->assertTrue($journalArticles->contains($article1));
        $this->assertTrue($journalArticles->contains($article2));
        $this->assertFalse($journalArticles->contains($article3));
    }

    /** @test */
    public function it_checks_belongs_to_journal()
    {
        $article = Article::factory()->create(['journal_id' => 1]);
        $anotherArticle = Article::factory()->create(['journal_id' => 2]);

        $this->assertTrue($article->belongsToJournal(1));
        $this->assertFalse($article->belongsToJournal(2));
        $this->assertFalse($anotherArticle->belongsToJournal(1));
    }

    /** @test */
    public function it_checks_if_has_journal()
    {
        $articleWithJournal = Article::factory()->create(['journal_id' => 1]);
        $articleWithoutJournal = Article::factory()->create(['journal_id' => null]);

        $this->assertTrue($articleWithJournal->hasJournal());
        $this->assertFalse($articleWithoutJournal->hasJournal());
    }

    /** @test */
    public function it_gets_journal_name()
    {
        $journal = ArticleCategory::factory()->create(['name' => 'Test Journal']);
        $article = Article::factory()->create(['journal_id' => $journal->id]);

        $this->assertEquals('Test Journal', $article->journal_name);
    }

    /** @test */
    public function it_gets_journal_acronym()
    {
        $journal = ArticleCategory::factory()->create(['journal_acronym' => 'TJ']);
        $article = Article::factory()->create(['journal_id' => $journal->id]);

        $this->assertEquals('TJ', $article->journal_acronym);
    }

    /** @test */
    public function it_can_assign_to_journal()
    {
        $article = Article::factory()->create(['journal_id' => null]);
        $journalId = 1;

        $this->assertFalse($article->hasJournal());

        $article->assignToJournal($journalId);

        $this->assertTrue($article->hasJournal());
        $this->assertEquals($journalId, $article->fresh()->journal_id);
    }

    /** @test */
    public function it_can_remove_journal_assignment()
    {
        $article = Article::factory()->create(['journal_id' => 1]);

        $this->assertTrue($article->hasJournal());

        $article->removeJournalAssignment();

        $this->assertFalse($article->fresh()->hasJournal());
        $this->assertNull($article->fresh()->journal_id);
    }

    /** @test */
    public function it_formats_amount_correctly()
    {
        $article = Article::factory()->create(['amount' => 100]); // Store as cents

        // The amount accessor should convert from cents to dollars
        $formattedAmount = $article->amount;
        $this->assertEquals(10000, $formattedAmount); // 100 dollars * 100 cents = 10000 cents
    }

    /** @test */
    public function it_sets_amount_correctly()
    {
        $article = Article::factory()->create();
        $dollars = 100;

        $article->amount = $dollars;

        // The amount mutator should convert from dollars to cents
        $this->assertEquals(10000, $article->attributes['amount']); // 100 dollars * 100 cents = 10000 cents
    }

    /** @test */
    public function it_gets_paper_size()
    {
        $article = Article::factory()->create();

        // Test with no file
        $size = $article->paper_size;
        $this->assertEquals('0 KB', $size);

        // Test with file (would need actual file handling for full test)
        // This is a basic test of the accessor
    }

    /** @test */
    public function it_gets_published_online_date()
    {
        $article = Article::factory()->create([
            'published_online' => '2024-01-15 10:30:00'
        ]);

        $publishedDate = $article->publishedOnline;
        $this->assertInstanceOf(Carbon::class, $publishedDate);
    }

    /** @test */
    public function it_gets_last_sub_article()
    {
        $article = Article::factory()->create();
        $subArticle1 = SubArticle::factory()->create(['article_id' => $article->id]);
        $subArticle2 = SubArticle::factory()->create(['article_id' => $article->id]);

        $last = $article->last;
        $this->assertInstanceOf(SubArticle::class, $last);
        $this->assertEquals($subArticle2->id, $last->id);
    }

    /** @test */
    public function it_uses_soft_deletes()
    {
        $article = Article::factory()->create();
        $articleId = $article->id;

        $article->delete();

        $this->assertSoftDeleted($article);
        $this->assertNotNull(Article::withTrashed()->find($articleId));
        $this->assertNull(Article::find($articleId));
    }

    /** @test */
    public function it_can_be_restored()
    {
        $article = Article::factory()->create();
        $article->delete();

        $article->restore();

        $this->assertDatabaseHas('articles', ['id' => $article->id, 'deleted_at' => null]);
    }

    /** @test */
    public function it_can_be_force_deleted()
    {
        $article = Article::factory()->create();
        $articleId = $article->id;

        $article->forceDelete();

        $this->assertDatabaseMissing('articles', ['id' => $articleId]);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Article::create([]);
    }

    /** @test */
    public function it_can_be_created_via_factory()
    {
        $article = Article::factory()->create();

        $this->assertInstanceOf(Article::class, $article);
        $this->assertNotNull($article->title);
        $this->assertNotNull($article->article_status);
    }

    /** @test */
    public function it_has_editorial_board_relationship()
    {
        $article = Article::factory()->create();
        $journal = ArticleCategory::factory()->create(['is_journal' => true]);
        $article->update(['journal_id' => $journal->id]);

        // This would need the editorial board factory to test properly
        // but we can test the relationship structure
        $this->assertMethodExists($article, 'editorialBoard');
    }

    /** @test */
    public function it_has_journal_memberships_relationship()
    {
        $article = Article::factory()->create();
        $journal = ArticleCategory::factory()->create(['is_journal' => true]);
        $article->update(['journal_id' => $journal->id]);

        $this->assertMethodExists($article, 'journalMemberships');
    }

    /** @test */
    public function it_handles_journal_slug_scope()
    {
        $journal = ArticleCategory::factory()->create([
            'is_journal' => true,
            'journal_slug' => 'test-journal'
        ]);
        $article = Article::factory()->create(['journal_id' => $journal->id]);

        $articles = Article::forJournalSlug('test-journal')->get();

        $this->assertCount(1, $articles);
        $this->assertTrue($articles->contains($article));
    }

    /** @test */
    public function it_handles_journal_acronym_scope()
    {
        $journal = ArticleCategory::factory()->create([
            'is_journal' => true,
            'journal_acronym' => 'TJ'
        ]);
        $article = Article::factory()->create(['journal_id' => $journal->id]);

        $articles = Article::forJournalAcronym('TJ')->get();

        $this->assertCount(1, $articles);
        $this->assertTrue($articles->contains($article));
    }

    /** @test */
    public function it_scopes_to_articles_with_journal()
    {
        $articleWithJournal = Article::factory()->create(['journal_id' => 1]);
        $articleWithoutJournal = Article::factory()->create(['journal_id' => null]);

        $withJournal = Article::withJournal()->get();
        $withoutJournal = Article::withoutJournal()->get();

        $this->assertCount(1, $withJournal);
        $this->assertTrue($withJournal->contains($articleWithJournal));
        $this->assertFalse($withJournal->contains($articleWithoutJournal));

        $this->assertCount(1, $withoutJournal);
        $this->assertTrue($withoutJournal->contains($articleWithoutJournal));
        $this->assertFalse($withoutJournal->contains($articleWithJournal));
    }

    private function assertMethodExists($object, $methodName)
    {
        $this->assertTrue(method_exists($object, $methodName), "Method {$methodName} does not exist on " . get_class($object));
    }
}

