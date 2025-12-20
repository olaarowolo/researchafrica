<?php

namespace Tests\Unit\Models;

use App\Models\Comment;
use App\Models\Article;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;

class CommentTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $comment;

    protected function setUp(): void
    {
        parent::setUp();
        $this->comment = Comment::factory()->create();
    }

    /** @test */
    public function it_can_create_a_comment()
    {
        $member = Member::factory()->create();
        $article = Article::factory()->create();

        $commentData = [
            'article_id' => $article->id,
            'member_id' => $member->id,
            'content' => 'This is a test comment',
            'status' => 'approved',
        ];

        $comment = Comment::create($commentData);

        $this->assertInstanceOf(Comment::class, $comment);
        $this->assertEquals($article->id, $comment->article_id);
        $this->assertEquals($member->id, $comment->member_id);
        $this->assertEquals('This is a test comment', $comment->content);
        $this->assertEquals('approved', $comment->status);
    }

    /** @test */
    public function it_has_correct_table_name()
    {
        $this->assertEquals('comments', $this->comment->getTable());
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = [
            'article_id',
            'member_id',
            'content',
            'status',
            'parent_id',
            'created_at',
            'updated_at',
        ];

        $this->assertEquals($fillable, $this->comment->getFillable());
    }

    /** @test */
    public function it_has_correct_date_format()
    {
        $now = Carbon::now();
        $comment = Comment::factory()->create(['created_at' => $now]);

        $this->assertEquals($now->format('Y-m-d H:i:s'), $comment->created_at->format('Y-m-d H:i:s'));
    }

    /** @test */
    public function it_has_correct_status_constants()
    {
        $expectedStatus = [
            'pending' => 'pending',
            'approved' => 'approved',
            'rejected' => 'rejected',
        ];

        $this->assertEquals($expectedStatus, Comment::COMMENT_STATUS);
    }

    /** @test */
    public function it_belongs_to_an_article()
    {
        $article = Article::factory()->create();
        $comment = Comment::factory()->create(['article_id' => $article->id]);

        $this->assertInstanceOf(Article::class, $comment->article);
        $this->assertEquals($article->id, $comment->article->id);
    }

    /** @test */
    public function it_belongs_to_a_member()
    {
        $member = Member::factory()->create();
        $comment = Comment::factory()->create(['member_id' => $member->id]);

        $this->assertInstanceOf(Member::class, $comment->member);
        $this->assertEquals($member->id, $comment->member->id);
    }

    /** @test */
    public function it_can_have_a_parent_comment()
    {
        $parentComment = Comment::factory()->create();
        $childComment = Comment::factory()->create(['parent_id' => $parentComment->id]);

        $this->assertEquals($parentComment->id, $childComment->parent_id);
        $this->assertInstanceOf(Comment::class, $childComment->parent);
        $this->assertEquals($parentComment->id, $childComment->parent->id);
    }

    /** @test */
    public function it_has_many_replies()
    {
        $parentComment = Comment::factory()->create();
        Comment::factory()->count(3)->create(['parent_id' => $parentComment->id]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $parentComment->replies);
        $this->assertCount(3, $parentComment->replies);
        $this->assertInstanceOf(Comment::class, $parentComment->replies->first());
    }

    /** @test */
    public function it_scopes_to_approved_comments()
    {
        $approvedComment1 = Comment::factory()->create(['status' => 'approved']);
        $approvedComment2 = Comment::factory()->create(['status' => 'approved']);
        $pendingComment = Comment::factory()->create(['status' => 'pending']);

        $approved = Comment::approved()->get();

        $this->assertCount(2, $approved);
        $this->assertTrue($approved->contains($approvedComment1));
        $this->assertTrue($approved->contains($approvedComment2));
        $this->assertFalse($approved->contains($pendingComment));
    }

    /** @test */
    public function it_scopes_to_pending_comments()
    {
        $pendingComment1 = Comment::factory()->create(['status' => 'pending']);
        $pendingComment2 = Comment::factory()->create(['status' => 'pending']);
        $approvedComment = Comment::factory()->create(['status' => 'approved']);

        $pending = Comment::pending()->get();

        $this->assertCount(2, $pending);
        $this->assertTrue($pending->contains($pendingComment1));
        $this->assertTrue($pending->contains($pendingComment2));
        $this->assertFalse($pending->contains($approvedComment));
    }

    /** @test */
    public function it_scopes_to_rejected_comments()
    {
        $rejectedComment = Comment::factory()->create(['status' => 'rejected']);
        $approvedComment = Comment::factory()->create(['status' => 'approved']);

        $rejected = Comment::rejected()->get();

        $this->assertCount(1, $rejected);
        $this->assertTrue($rejected->contains($rejectedComment));
        $this->assertFalse($rejected->contains($approvedComment));
    }

    /** @test */
    public function it_scopes_to_top_level_comments()
    {
        $parentComment = Comment::factory()->create();
        $childComment = Comment::factory()->create(['parent_id' => $parentComment->id]);

        $topLevel = Comment::topLevel()->get();

        $this->assertCount(1, $topLevel);
        $this->assertTrue($topLevel->contains($parentComment));
        $this->assertFalse($topLevel->contains($childComment));
    }

    /** @test */
    public function it_scopes_to_replies()
    {
        $parentComment = Comment::factory()->create();
        $childComment = Comment::factory()->create(['parent_id' => $parentComment->id]);

        $replies = Comment::replies()->get();

        $this->assertCount(1, $replies);
        $this->assertTrue($replies->contains($childComment));
        $this->assertFalse($replies->contains($parentComment));
    }

    /** @test */
    public function it_scopes_by_article()
    {
        $article1 = Article::factory()->create();
        $article2 = Article::factory()->create();

        $comment1 = Comment::factory()->create(['article_id' => $article1->id]);
        $comment2 = Comment::factory()->create(['article_id' => $article2->id]);

        $article1Comments = Comment::where('article_id', $article1->id)->get();

        $this->assertCount(1, $article1Comments);
        $this->assertTrue($article1Comments->contains($comment1));
        $this->assertFalse($article1Comments->contains($comment2));
    }

    /** @test */
    public function it_scopes_by_member()
    {
        $member1 = Member::factory()->create();
        $member2 = Member::factory()->create();

        $comment1 = Comment::factory()->create(['member_id' => $member1->id]);
        $comment2 = Comment::factory()->create(['member_id' => $member2->id]);

        $member1Comments = Comment::where('member_id', $member1->id)->get();

        $this->assertCount(1, $member1Comments);
        $this->assertTrue($member1Comments->contains($comment1));
        $this->assertFalse($member1Comments->contains($comment2));
    }

    /** @test */
    public function it_orders_comments_by_recency()
    {
        $oldComment = Comment::factory()->create(['created_at' => Carbon::now()->subDays(2)]);
        $recentComment = Comment::factory()->create(['created_at' => Carbon::now()]);
        $mediumComment = Comment::factory()->create(['created_at' => Carbon::now()->subDay()]);

        $ordered = Comment::orderBy('created_at', 'desc')->get();

        $this->assertEquals($recentComment->id, $ordered->first()->id);
        $this->assertEquals($mediumComment->id, $ordered->skip(1)->first()->id);
        $this->assertEquals($oldComment->id, $ordered->last()->id);
    }

    /** @test */
    public function it_gets_comment_depth()
    {
        // Top level comment
        $parentComment = Comment::factory()->create();
        $this->assertEquals(0, $parentComment->depth);

        // First level reply
        $childComment = Comment::factory()->create(['parent_id' => $parentComment->id]);
        $this->assertEquals(1, $childComment->depth);

        // Second level reply
        $grandchildComment = Comment::factory()->create(['parent_id' => $childComment->id]);
        $this->assertEquals(2, $grandchildComment->depth);
    }

    /** @test */
    public function it_checks_if_comment_is_reply()
    {
        $parentComment = Comment::factory()->create();
        $childComment = Comment::factory()->create(['parent_id' => $parentComment->id]);

        $this->assertFalse($parentComment->isReply());
        $this->assertTrue($childComment->isReply());
    }

    /** @test */
    public function it_checks_if_comment_is_top_level()
    {
        $parentComment = Comment::factory()->create();
        $childComment = Comment::factory()->create(['parent_id' => $parentComment->id]);

        $this->assertTrue($parentComment->isTopLevel());
        $this->assertFalse($childComment->isTopLevel());
    }

    /** @test */
    public function it_checks_if_comment_is_approved()
    {
        $approvedComment = Comment::factory()->create(['status' => 'approved']);
        $pendingComment = Comment::factory()->create(['status' => 'pending']);

        $this->assertTrue($approvedComment->isApproved());
        $this->assertFalse($pendingComment->isApproved());
    }

    /** @test */
    public function it_checks_if_comment_is_pending()
    {
        $pendingComment = Comment::factory()->create(['status' => 'pending']);
        $approvedComment = Comment::factory()->create(['status' => 'approved']);

        $this->assertTrue($pendingComment->isPending());
        $this->assertFalse($approvedComment->isPending());
    }

    /** @test */
    public function it_can_approve_comment()
    {
        $comment = Comment::factory()->create(['status' => 'pending']);

        $this->assertEquals('pending', $comment->status);

        $comment->approve();

        $this->assertEquals('approved', $comment->fresh()->status);
        $this->assertTrue($comment->fresh()->isApproved());
    }

    /** @test */
    public function it_can_reject_comment()
    {
        $comment = Comment::factory()->create(['status' => 'pending']);

        $this->assertEquals('pending', $comment->status);

        $comment->reject();

        $this->assertEquals('rejected', $comment->fresh()->status);
    }

    /** @test */
    public function it_counts_replies()
    {
        $comment = Comment::factory()->create();
        Comment::factory()->count(3)->create(['parent_id' => $comment->id]);

        $this->assertEquals(3, $comment->replies_count);
    }

    /** @test */
    public function it_gets_author_name()
    {
        $member = Member::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe'
        ]);
        $comment = Comment::factory()->create(['member_id' => $member->id]);

        $this->assertEquals('John Doe', $comment->author_name);
    }

    /** @test */
    public function it_gets_excerpt_of_content()
    {
        $longContent = str_repeat('This is a long comment content. ', 10);
        $comment = Comment::factory()->create(['content' => $longContent]);

        $excerpt = $comment->excerpt;
        $this->assertIsString($excerpt);
        $this->assertLessThanOrEqual(100, strlen($excerpt));
        $this->assertStringContainsString('This is a long comment', $excerpt);
    }

    /** @test */
    public function it_validates_content_length()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Comment::create([
            'content' => '', // Empty content
            'article_id' => Article::factory()->create()->id,
            'member_id' => Member::factory()->create()->id,
        ]);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Comment::create([]);
    }

    /** @test */
    public function it_can_update_comment()
    {
        $comment = Comment::factory()->create();

        $comment->update([
            'content' => 'Updated comment content',
            'status' => 'approved'
        ]);

        $this->assertEquals('Updated comment content', $comment->fresh()->content);
        $this->assertEquals('approved', $comment->fresh()->status);
    }

    /** @test */
    public function it_can_delete_comment()
    {
        $comment = Comment::factory()->create();
        $commentId = $comment->id;

        $comment->delete();

        $this->assertSoftDeleted($comment);
        $this->assertNotNull(Comment::withTrashed()->find($commentId));
    }

    /** @test */
    public function it_cascades_deletes_to_replies()
    {
        $parentComment = Comment::factory()->create();
        $replies = Comment::factory()->count(2)->create(['parent_id' => $parentComment->id]);

        $parentComment->delete();

        // Replies should still exist but with soft delete
        $this->assertSoftDeleted($parentComment);
        $this->assertSoftDeleted($replies->first());
        $this->assertSoftDeleted($replies->last());
    }

    /** @test */
    public function it_can_be_restored()
    {
        $comment = Comment::factory()->create();
        $comment->delete();

        $comment->restore();

        $this->assertDatabaseHas('comments', ['id' => $comment->id, 'deleted_at' => null]);
    }

    /** @test */
    public function it_can_be_force_deleted()
    {
        $comment = Comment::factory()->create();
        $commentId = $comment->id;

        $comment->forceDelete();

        $this->assertDatabaseMissing('comments', ['id' => $commentId]);
    }

    /** @test */
    public function it_orders_nested_comments_correctly()
    {
        $parentComment = Comment::factory()->create();

        // Create replies in reverse order
        $reply3 = Comment::factory()->create(['parent_id' => $parentComment->id, 'created_at' => Carbon::now()->subDays(3)]);
        $reply1 = Comment::factory()->create(['parent_id' => $parentComment->id, 'created_at' => Carbon::now()->subDay()]);
        $reply2 = Comment::factory()->create(['parent_id' => $parentComment->id, 'created_at' => Carbon::now()->subDays(2)]);

        $nestedComments = Comment::with('replies')->find($parentComment->id);

        // Replies should be ordered by creation date (oldest first)
        $replies = $nestedComments->replies->sortBy('created_at')->values();

        $this->assertEquals($reply3->id, $replies->first()->id);
        $this->assertEquals($reply2->id, $replies->skip(1)->first()->id);
        $this->assertEquals($reply1->id, $replies->last()->id);
    }

    /** @test */
    public function it_prevents_infinite_recursion_in_replies()
    {
        $comment1 = Comment::factory()->create();
        $comment2 = Comment::factory()->create(['parent_id' => $comment1->id]);
        $comment3 = Comment::factory()->create(['parent_id' => $comment2->id]);

        // This should not cause infinite recursion
        $replies = $comment1->replies;
        $this->assertCount(1, $replies);
        $this->assertInstanceOf(Comment::class, $replies->first());
    }

    /** @test */
    public function it_handles_null_parent_id()
    {
        $comment = Comment::factory()->create(['parent_id' => null]);

        $this->assertNull($comment->parent);
        $this->assertTrue($comment->isTopLevel());
        $this->assertFalse($comment->isReply());
        $this->assertEquals(0, $comment->depth);
    }

    /** @test */
    public function it_searches_in_content()
    {
        $comment1 = Comment::factory()->create(['content' => 'This is about Laravel testing']);
        $comment2 = Comment::factory()->create(['content' => 'This is about PHP development']);
        $comment3 = Comment::factory()->create(['content' => 'Database design patterns']);

        $results = Comment::where('content', 'LIKE', '%Laravel%')->get();

        $this->assertCount(1, $results);
        $this->assertTrue($results->contains($comment1));
        $this->assertFalse($results->contains($comment2));
        $this->assertFalse($results->contains($comment3));
    }

    /** @test */
    public function it_gets_comment_tree()
    {
        $parentComment = Comment::factory()->create();
        $childComment1 = Comment::factory()->create(['parent_id' => $parentComment->id]);
        $childComment2 = Comment::factory()->create(['parent_id' => $parentComment->id]);
        $grandchildComment = Comment::factory()->create(['parent_id' => $childComment1->id]);

        $tree = $parentComment->getTree();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $tree);
        $this->assertCount(3, $tree); // Parent + 2 direct children
    }
}

