<?php

namespace Tests\Feature\Members;

use App\Models\Article;
use App\Models\Comment;
use App\Models\Member;
use App\Models\MemberRole;
use App\Models\MemberType;
use App\Models\Country;
use App\Models\State;
use App\Models\ArticleCategory;
use App\Models\SubArticle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CommentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $member;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();

        // Create required models
        $country = Country::factory()->create();
        $state = State::factory()->create(['country_id' => $country->id]);
        $memberRole = MemberRole::factory()->create();
        $memberType = MemberType::factory()->create();

        // Create member
        $this->member = Member::factory()->create([
            'country_id' => $country->id,
            'state_id' => $state->id,
            'member_type_id' => $memberType->id,
            'member_role_id' => $memberRole->id,
        ]);
    }

    /** @test */
    public function member_can_store_comment()
    {
        $this->withoutExceptionHandling();

        $journal = ArticleCategory::factory()->create(['is_journal' => true]);
        $category = ArticleCategory::factory()->create();
        $article = Article::factory()->create([
            'member_id' => $this->member->id,
            'article_category_id' => $category->id,
            'journal_id' => $journal->id,
        ]);

        // Create a sub-article for the article
        SubArticle::create([
            'article_id' => $article->id,
            'status' => 1, // pending
            'abstract' => 'Test abstract',
        ]);

        $commentData = [
            'message' => 'This is a test comment',
        ];

        $response = $this->actingAs($this->member, 'member')
            ->post(route('member.comments.store', $article), $commentData);

        $response->assertRedirect();

        // Check that a comment was created
        $this->assertDatabaseCount('comments', 1);

        $comment = Comment::first();
        $this->assertEquals($article->id, $comment->article_id);
        $this->assertEquals($this->member->id, $comment->member_id);
        $this->assertEquals('This is a test comment', $comment->message);
    }
}
