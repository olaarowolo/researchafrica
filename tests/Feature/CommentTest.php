<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Member;
use App\Models\Article;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $member;
    private $article;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->member = Member::factory()->create();
        $this->article = Article::factory()->create(['status' => 'published']);
    }

    /**
     * Test member can create comment.
     */
    public function test_member_can_create_comment()
    {
        $commentData = [
            'content' => 'This is a test comment on the article.',
            'article_id' => $this->article->id,
        ];

        $response = $this->actingAs($this->member, 'member')
            ->post(route('comments.store'), $commentData);

        $response->assertRedirect();
        $this->assertDatabaseHas('comments', [
            'content' => 'This is a test comment on the article.',
            'article_id' => $this->article->id,
        ]);
    }

    /**
     * Test admin can view all comments.
     */
    public function test_admin_can_view_all_comments()
    {
        Comment::factory()->count(5)->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.comments.index'));

        $response->assertStatus(200);
        $response->assertViewHas('comments');
    }

    /**
     * Test admin can edit comment.
     */
    public function test_admin_can_edit_comment()
    {
        $comment = Comment::factory()->create();

        $updatedData = [
            'content' => 'Updated comment content.',
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('admin.comments.update', $comment->id), $updatedData);

        $response->assertRedirect();
        $this->assertDatabaseHas('comments', [
            'content' => 'Updated comment content.',
        ]);
    }

    /**
     * Test admin can delete comment.
     */
    public function test_admin_can_delete_comment()
    {
        $comment = Comment::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.comments.destroy', $comment->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('comments', [
            'id' => $comment->id,
        ]);
    }

    /**
     * Test member can edit own comment.
     */
    public function test_member_can_edit_own_comment()
    {
        $comment = Comment::factory()->create([
            'member_id' => $this->member->id,
        ]);

        $updatedData = [
            'content' => 'Updated my own comment.',
        ];

        $response = $this->actingAs($this->member, 'member')
            ->put(route('comments.update', $comment->id), $updatedData);

        $response->assertRedirect();
        $this->assertDatabaseHas('comments', [
            'content' => 'Updated my own comment.',
        ]);
    }

    /**
     * Test member cannot edit other member's comment.
     */
    public function test_member_cannot_edit_other_member_comment()
    {
        $otherMember = Member::factory()->create();
        $comment = Comment::factory()->create([
            'member_id' => $otherMember->id,
        ]);

        $updatedData = [
            'content' => 'Trying to edit other member comment.',
        ];

        $response = $this->actingAs($this->member, 'member')
            ->put(route('comments.update', $comment->id), $updatedData);

        $response->assertStatus(403);
    }

    /**
     * Test member can delete own comment.
     */
    public function test_member_can_delete_own_comment()
    {
        $comment = Comment::factory()->create([
            'member_id' => $this->member->id,
        ]);

        $response = $this->actingAs($this->member, 'member')
            ->delete(route('comments.destroy', $comment->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('comments', [
            'id' => $comment->id,
        ]);
    }

    /**
     * Test member cannot delete other member's comment.
     */
    public function test_member_cannot_delete_other_member_comment()
    {
        $otherMember = Member::factory()->create();
        $comment = Comment::factory()->create([
            'member_id' => $otherMember->id,
        ]);

        $response = $this->actingAs($this->member, 'member')
            ->delete(route('comments.destroy', $comment->id));

        $response->assertStatus(403);
    }

    /**
     * Test comment validation.
     */
    public function test_comment_validation()
    {
        $commentData = [
            'content' => '', // Empty content should fail validation
            'article_id' => $this->article->id,
        ];

        $response = $this->actingAs($this->member, 'member')
            ->post(route('comments.store'), $commentData);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('content');
    }

    /**
     * Test admin can bulk delete comments.
     */
    public function test_admin_can_bulk_delete_comments()
    {
        $comments = Comment::factory()->count(3)->create();

        $commentIds = $comments->pluck('id')->toArray();

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.comments.massDestroy'), [
                'ids' => $commentIds,
            ]);

        $response->assertRedirect();

        foreach ($comments as $comment) {
            $this->assertDatabaseMissing('comments', [
                'id' => $comment->id,
            ]);
        }
    }
}
