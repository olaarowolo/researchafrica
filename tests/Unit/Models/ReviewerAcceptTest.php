<?php

namespace Tests\Unit\Models;

use App\Models\Article;
use App\Models\Member;
use App\Models\ReviewerAccept;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReviewerAcceptTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $reviewerAccept;

    protected function setUp(): void
    {
        parent::setUp();
        $this->reviewerAccept = ReviewerAccept::factory()->create();
    }

    /** @test */
    public function it_can_create_a_reviewer_accept()
    {
        $article = Article::factory()->create();
        $member = Member::factory()->create();
        $assigner = Member::factory()->create();

        $data = [
            'article_id' => $article->id,
            'member_id' => $member->id,
            'assigned_id' => $assigner->id,
        ];

        $reviewerAccept = ReviewerAccept::create($data);

        $this->assertInstanceOf(ReviewerAccept::class, $reviewerAccept);
        $this->assertEquals($article->id, $reviewerAccept->article_id);
        $this->assertEquals($member->id, $reviewerAccept->member_id);
        $this->assertEquals($assigner->id, $reviewerAccept->assigned_id);
    }

    /** @test */
    public function it_has_correct_table_name()
    {
        $this->assertEquals('reviewer_accepts', $this->reviewerAccept->getTable());
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = [
            'article_id',
            'member_id',
            'assigned_id',
            'created_at',
            'updated_at',
        ];

        $this->assertEquals($fillable, $this->reviewerAccept->getFillable());
    }

    /** @test */
    public function it_belongs_to_a_member()
    {
        $this->assertInstanceOf(Member::class, $this->reviewerAccept->member);
        $this->assertEquals($this->reviewerAccept->member_id, $this->reviewerAccept->member->id);
    }

    /** @test */
    public function it_belongs_to_an_article()
    {
        $this->assertInstanceOf(Article::class, $this->reviewerAccept->article);
        $this->assertEquals($this->reviewerAccept->article_id, $this->reviewerAccept->article->id);
    }

    /** @test */
    public function it_can_scope_last_article()
    {
        $article1 = Article::factory()->create();
        $article2 = Article::factory()->create();
        $member1 = Member::factory()->create();
        $member2 = Member::factory()->create();

        // Create ReviewerAccepts for article1
        ReviewerAccept::factory()->create(['article_id' => $article1->id, 'member_id' => $member1->id]);
        ReviewerAccept::factory()->create(['article_id' => $article1->id, 'member_id' => $member2->id]);
        
        // Create ReviewerAccept for article2
        ReviewerAccept::factory()->create(['article_id' => $article2->id, 'member_id' => $member1->id]);

        $results = ReviewerAccept::lastArticle($article1->id)->get();

        $this->assertCount(2, $results);
        $results->each(function ($accept) use ($article1) {
            $this->assertEquals($article1->id, $accept->article_id);
            $this->assertNotNull($accept->member_id);
        });
    }

    /** @test */
    public function it_serializes_date_correctly()
    {
        $json = $this->reviewerAccept->toArray();
        
        $this->assertMatchesRegularExpression('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $json['created_at']);
    }
}
