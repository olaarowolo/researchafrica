<?php

namespace Tests\Unit\Models;

use App\Models\Bookmark;
use App\Models\Article;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;

class BookmarkTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $bookmark;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bookmark = Bookmark::factory()->create();
    }

    /** @test */
    public function it_can_create_a_bookmark()
    {
        $member = Member::factory()->create();
        $article = Article::factory()->create();

        $bookmarkData = [
            'member_id' => $member->id,
            'article_id' => $article->id,
        ];

        $bookmark = Bookmark::create($bookmarkData);

        $this->assertInstanceOf(Bookmark::class, $bookmark);
        $this->assertEquals($member->id, $bookmark->member_id);
        $this->assertEquals($article->id, $bookmark->article_id);
    }

    /** @test */
    public function it_has_correct_table_name()
    {
        $this->assertEquals('bookmarks', $this->bookmark->getTable());
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = [
            'member_id',
            'article_id',
            'created_at',
            'updated_at',
        ];

        $this->assertEquals($fillable, $this->bookmark->getFillable());
    }

    /** @test */
    public function it_has_correct_date_format()
    {
        $now = Carbon::now();
        $bookmark = Bookmark::factory()->create(['created_at' => $now]);

        $this->assertEquals($now->format('Y-m-d H:i:s'), $bookmark->created_at->format('Y-m-d H:i:s'));
    }

    /** @test */
    public function it_belongs_to_a_member()
    {
        $member = Member::factory()->create();
        $bookmark = Bookmark::factory()->create(['member_id' => $member->id]);

        $this->assertInstanceOf(Member::class, $bookmark->member);
        $this->assertEquals($member->id, $bookmark->member->id);
    }

    /** @test */
    public function it_belongs_to_an_article()
    {
        $article = Article::factory()->create();
        $bookmark = Bookmark::factory()->create(['article_id' => $article->id]);

        $this->assertInstanceOf(Article::class, $bookmark->article);
        $this->assertEquals($article->id, $bookmark->article->id);
    }

    /** @test */
    public function it_scopes_bookmarks_by_member()
    {
        $member1 = Member::factory()->create();
        $member2 = Member::factory()->create();

        $bookmark1 = Bookmark::factory()->create(['member_id' => $member1->id]);
        $bookmark2 = Bookmark::factory()->create(['member_id' => $member2->id]);

        $member1Bookmarks = Bookmark::where('member_id', $member1->id)->get();

        $this->assertCount(1, $member1Bookmarks);
        $this->assertTrue($member1Bookmarks->contains($bookmark1));
        $this->assertFalse($member1Bookmarks->contains($bookmark2));
    }

    /** @test */
    public function it_scopes_bookmarks_by_article()
    {
        $article1 = Article::factory()->create();
        $article2 = Article::factory()->create();

        $bookmark1 = Bookmark::factory()->create(['article_id' => $article1->id]);
        $bookmark2 = Bookmark::factory()->create(['article_id' => $article2->id]);

        $article1Bookmarks = Bookmark::where('article_id', $article1->id)->get();

        $this->assertCount(1, $article1Bookmarks);
        $this->assertTrue($article1Bookmarks->contains($bookmark1));
        $this->assertFalse($article1Bookmarks->contains($bookmark2));
    }

    /** @test */
    public function it_orders_bookmarks_by_creation_date()
    {
        $oldBookmark = Bookmark::factory()->create(['created_at' => Carbon::now()->subDays(2)]);
        $recentBookmark = Bookmark::factory()->create(['created_at' => Carbon::now()]);
        $mediumBookmark = Bookmark::factory()->create(['created_at' => Carbon::now()->subDay()]);

        $ordered = Bookmark::orderBy('created_at', 'desc')->get();

        $this->assertEquals($recentBookmark->id, $ordered->first()->id);
        $this->assertEquals($mediumBookmark->id, $ordered->skip(1)->first()->id);
        $this->assertEquals($oldBookmark->id, $ordered->last()->id);
    }

    /** @test */
    public function it_prevents_duplicate_bookmarks_by_same_member()
    {
        $member = Member::factory()->create();
        $article = Article::factory()->create();

        // Create first bookmark
        $bookmark1 = Bookmark::create([
            'member_id' => $member->id,
            'article_id' => $article->id,
        ]);

        // Try to create duplicate bookmark - should fail due to unique constraint
        $this->expectException(\Illuminate\Database\QueryException::class);

        Bookmark::create([
            'member_id' => $member->id,
            'article_id' => $article->id,
        ]);
    }

    /** @test */
    public function it_allows_different_members_to_bookmark_same_article()
    {
        $article = Article::factory()->create();
        $member1 = Member::factory()->create();
        $member2 = Member::factory()->create();

        $bookmark1 = Bookmark::create([
            'member_id' => $member1->id,
            'article_id' => $article->id,
        ]);

        $bookmark2 = Bookmark::create([
            'member_id' => $member2->id,
            'article_id' => $article->id,
        ]);

        $this->assertNotEquals($bookmark1->id, $bookmark2->id);
        $this->assertCount(2, Bookmark::where('article_id', $article->id)->get());
    }

    /** @test */
    public function it_allows_same_member_to_bookmark_different_articles()
    {
        $member = Member::factory()->create();
        $article1 = Article::factory()->create();
        $article2 = Article::factory()->create();


        $bookmark1 = Bookmark::create([
            'member_id' => $member->id,
            'article_id' => $article1->id,
        ]);

        $bookmark2 = Bookmark::create([
            'member_id' => $member->id,
            'article_id' => $article2->id,
        ]);

        $this->assertNotEquals($bookmark1->id, $bookmark2->id);
        $this->assertCount(2, Bookmark::where('member_id', $member->id)->get());
    }

    /** @test */
    public function it_checks_if_member_has_bookmarked_article()
    {
        $member = Member::factory()->create();
        $article = Article::factory()->create();

        // Initially not bookmarked
        $this->assertFalse(Bookmark::where('member_id', $member->id)
            ->where('article_id', $article->id)
            ->exists());

        // After bookmarking
        $bookmark = Bookmark::create([
            'member_id' => $member->id,
            'article_id' => $article->id,
        ]);

        $this->assertTrue(Bookmark::where('member_id', $member->id)
            ->where('article_id', $article->id)
            ->exists());
    }

    /** @test */
    public function it_counts_bookmarks_for_article()
    {
        $article = Article::factory()->create();
        $member1 = Member::factory()->create();
        $member2 = Member::factory()->create();
        $member3 = Member::factory()->create();

        Bookmark::create(['member_id' => $member1->id, 'article_id' => $article->id]);
        Bookmark::create(['member_id' => $member2->id, 'article_id' => $article->id]);
        Bookmark::create(['member_id' => $member3->id, 'article_id' => $article->id]);

        $this->assertEquals(3, Bookmark::where('article_id', $article->id)->count());
    }

    /** @test */
    public function it_counts_bookmarks_for_member()
    {
        $member = Member::factory()->create();
        $article1 = Article::factory()->create();
        $article2 = Article::factory()->create();
        $article3 = Article::factory()->create();

        Bookmark::create(['member_id' => $member->id, 'article_id' => $article1->id]);
        Bookmark::create(['member_id' => $member->id, 'article_id' => $article2->id]);
        Bookmark::create(['member_id' => $member->id, 'article_id' => $article3->id]);

        $this->assertEquals(3, Bookmark::where('member_id', $member->id)->count());
    }

    /** @test */
    public function it_gets_member_bookmarks_with_articles()
    {
        $member = Member::factory()->create();
        $article = Article::factory()->create();

        $bookmark = Bookmark::create([
            'member_id' => $member->id,
            'article_id' => $article->id,
        ]);

        $bookmarkWithRelations = Bookmark::with(['member', 'article'])->find($bookmark->id);

        $this->assertInstanceOf(Member::class, $bookmarkWithRelations->member);
        $this->assertInstanceOf(Article::class, $bookmarkWithRelations->article);
        $this->assertEquals($member->id, $bookmarkWithRelations->member->id);
        $this->assertEquals($article->id, $bookmarkWithRelations->article->id);
    }

    /** @test */
    public function it_can_remove_bookmark()
    {
        $bookmark = Bookmark::factory()->create();
        $bookmarkId = $bookmark->id;

        $bookmark->delete();

        $this->assertDatabaseMissing('bookmarks', ['id' => $bookmarkId]);
    }

    /** @test */
    public function it_can_bulk_create_bookmarks()
    {
        $member = Member::factory()->create();
        $articles = Article::factory()->count(5)->create();

        $articleIds = $articles->pluck('id')->toArray();
        $bookmarksData = array_map(function ($articleId) use ($member) {
            return ['member_id' => $member->id, 'article_id' => $articleId];
        }, $articleIds);

        Bookmark::insert($bookmarksData);

        $this->assertCount(5, Bookmark::where('member_id', $member->id)->get());
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Bookmark::create([]);
    }

    /** @test */
    public function it_validates_member_exists()
    {
        $article = Article::factory()->create();

        $this->expectException(\Illuminate\Database\QueryException::class);

        Bookmark::create([
            'member_id' => 999, // Non-existent member
            'article_id' => $article->id,
        ]);
    }

    /** @test */
    public function it_validates_article_exists()
    {
        $member = Member::factory()->create();

        $this->expectException(\Illuminate\Database\QueryException::class);

        Bookmark::create([
            'member_id' => $member->id,
            'article_id' => 999, // Non-existent article
        ]);
    }

    /** @test */
    public function it_searches_by_member_name()
    {
        $member = Member::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe'
        ]);
        $article = Article::factory()->create();

        $bookmark = Bookmark::create([
            'member_id' => $member->id,
            'article_id' => $article->id,
        ]);

        $results = Bookmark::whereHas('member', function ($query) {
            $query->where('first_name', 'LIKE', '%John%');
        })->get();

        $this->assertCount(1, $results);
        $this->assertTrue($results->contains($bookmark));
    }

    /** @test */
    public function it_searches_by_article_title()
    {
        $member = Member::factory()->create();
        $article = Article::factory()->create(['title' => 'Laravel Testing Guide']);

        $bookmark = Bookmark::create([
            'member_id' => $member->id,
            'article_id' => $article->id,
        ]);

        $results = Bookmark::whereHas('article', function ($query) {
            $query->where('title', 'LIKE', '%Laravel%');
        })->get();

        $this->assertCount(1, $results);
        $this->assertTrue($results->contains($bookmark));
    }

    /** @test */
    public function it_orders_member_bookmarks_by_article_title()
    {
        $member = Member::factory()->create();
        $article1 = Article::factory()->create(['title' => 'Z Article']);
        $article2 = Article::factory()->create(['title' => 'A Article']);
        $article3 = Article::factory()->create(['title' => 'M Article']);

        Bookmark::create(['member_id' => $member->id, 'article_id' => $article1->id]);
        Bookmark::create(['member_id' => $member->id, 'article_id' => $article2->id]);
        Bookmark::create(['member_id' => $member->id, 'article_id' => $article3->id]);

        $orderedBookmarks = Bookmark::where('member_id', $member->id)
            ->with('article')
            ->get()
            ->sortBy('article.title');

        $this->assertEquals('A Article', $orderedBookmarks->first()->article->title);
        $this->assertEquals('M Article', $orderedBookmarks->skip(1)->first()->article->title);
        $this->assertEquals('Z Article', $orderedBookmarks->last()->article->title);
    }

    /** @test */
    public function it_limits_bookmarks_per_member()
    {
        $member = Member::factory()->create();
        $articles = Article::factory()->count(100)->create();

        // Create 100 bookmarks
        foreach ($articles as $index => $article) {
            Bookmark::create([
                'member_id' => $member->id,
                'article_id' => $article->id,
            ]);
        }

        $this->assertEquals(100, Bookmark::where('member_id', $member->id)->count());
    }

    /** @test */
    public function it_handles_soft_deletes_for_articles()
    {
        $member = Member::factory()->create();
        $article = Article::factory()->create();

        $bookmark = Bookmark::create([
            'member_id' => $member->id,
            'article_id' => $article->id,
        ]);

        // Soft delete the article
        $article->delete();

        // Bookmark should still exist
        $this->assertDatabaseHas('bookmarks', ['id' => $bookmark->id]);
        $this->assertSoftDeleted($article);
    }

    /** @test */
    public function it_handles_soft_deletes_for_members()
    {
        $member = Member::factory()->create();
        $article = Article::factory()->create();

        $bookmark = Bookmark::create([
            'member_id' => $member->id,
            'article_id' => $article->id,
        ]);

        // Soft delete the member
        $member->delete();

        // Bookmark should still exist
        $this->assertDatabaseHas('bookmarks', ['id' => $bookmark->id]);
        $this->assertSoftDeleted($member);
    }

    /** @test */
    public function it_can_get_recent_bookmarks()
    {
        $oldBookmark = Bookmark::factory()->create(['created_at' => Carbon::now()->subDays(10)]);
        $recentBookmark1 = Bookmark::factory()->create(['created_at' => Carbon::now()->subDay()]);
        $recentBookmark2 = Bookmark::factory()->create(['created_at' => Carbon::now()]);

        $recent = Bookmark::where('created_at', '>=', Carbon::now()->subDays(7))->get();

        $this->assertCount(2, $recent);
        $this->assertTrue($recent->contains($recentBookmark1));
        $this->assertTrue($recent->contains($recentBookmark2));
        $this->assertFalse($recent->contains($oldBookmark));
    }

    /** @test */
    public function it_can_get_bookmark_statistics()
    {
        $member1 = Member::factory()->create();
        $member2 = Member::factory()->create();
        $article1 = Article::factory()->create();
        $article2 = Article::factory()->create();

        // Create bookmarks
        Bookmark::create(['member_id' => $member1->id, 'article_id' => $article1->id]);
        Bookmark::create(['member_id' => $member1->id, 'article_id' => $article2->id]);
        Bookmark::create(['member_id' => $member2->id, 'article_id' => $article1->id]);

        $stats = [
            'total_bookmarks' => Bookmark::count(),
            'unique_members' => Bookmark::distinct('member_id')->count('member_id'),
            'unique_articles' => Bookmark::distinct('article_id')->count('article_id'),
            'most_bookmarked_article' => Article::find($article1->id)->bookmarks()->count(),
        ];

        $this->assertEquals(3, $stats['total_bookmarks']);
        $this->assertEquals(2, $stats['unique_members']);
        $this->assertEquals(2, $stats['unique_articles']);
        $this->assertEquals(2, $stats['most_bookmarked_article']);
    }
}

