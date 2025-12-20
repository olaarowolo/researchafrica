<?php

namespace Tests\Unit\Models;

use App\Models\Article;
use App\Models\Member;
use App\Models\ReviewerAcceptFinal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReviewerAcceptFinalTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $reviewerAcceptFinal;

    protected function setUp(): void
    {
        parent::setUp();
        $this->reviewerAcceptFinal = ReviewerAcceptFinal::factory()->create();
    }

    /** @test */
    public function it_can_create_a_reviewer_accept_final()
    {
        $article = Article::factory()->create();
        $member = Member::factory()->create();

        $data = [
            'article_id' => $article->id,
            'member_id' => $member->id,
        ];

        $reviewerAcceptFinal = ReviewerAcceptFinal::create($data);

        $this->assertInstanceOf(ReviewerAcceptFinal::class, $reviewerAcceptFinal);
        $this->assertEquals($article->id, $reviewerAcceptFinal->article_id);
        $this->assertEquals($member->id, $reviewerAcceptFinal->member_id);
    }

    /** @test */
    public function it_has_correct_table_name()
    {
        $this->assertEquals('reviewer_accept_finals', $this->reviewerAcceptFinal->getTable());
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = [
            'article_id',
            'member_id',
            'created_at',
            'updated_at',
        ];

        $this->assertEquals($fillable, $this->reviewerAcceptFinal->getFillable());
    }

    /** @test */
    public function it_belongs_to_a_member()
    {
        $this->assertInstanceOf(Member::class, $this->reviewerAcceptFinal->member);
        $this->assertEquals($this->reviewerAcceptFinal->member_id, $this->reviewerAcceptFinal->member->id);
    }

    /** @test */
    public function it_belongs_to_an_article()
    {
        $this->assertInstanceOf(Article::class, $this->reviewerAcceptFinal->article);
        $this->assertEquals($this->reviewerAcceptFinal->article_id, $this->reviewerAcceptFinal->article->id);
    }

    /** @test */
    public function it_can_scope_last_article()
    {
        $article1 = Article::factory()->create();
        $article2 = Article::factory()->create();
        $member1 = Member::factory()->create();
        $member2 = Member::factory()->create();

        // Create ReviewerAcceptFinals for article1
        ReviewerAcceptFinal::factory()->create(['article_id' => $article1->id, 'member_id' => $member1->id]);
        ReviewerAcceptFinal::factory()->create(['article_id' => $article1->id, 'member_id' => $member2->id]);
        
        // Create ReviewerAcceptFinal for article2
        ReviewerAcceptFinal::factory()->create(['article_id' => $article2->id, 'member_id' => $member1->id]);

        $results = ReviewerAcceptFinal::lastArticle($article1->id)->get();

        $this->assertCount(2, $results);
        $results->each(function ($accept) use ($article1) {
            $this->assertEquals($article1->id, $accept->article_id);
            $this->assertNotNull($accept->member_id);
        });
    }

    /** @test */
    public function it_serializes_date_correctly()
    {
        $json = $this->reviewerAcceptFinal->toArray();
        
        $this->assertMatchesRegularExpression('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $json['created_at']);
    }
}
