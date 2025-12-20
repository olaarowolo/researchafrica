<?php

namespace Tests\Unit\Models;

use App\Models\Article;
use App\Models\Member;
use App\Models\PublisherAccept;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PublisherAcceptTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $publisherAccept;

    protected function setUp(): void
    {
        parent::setUp();
        $this->publisherAccept = PublisherAccept::factory()->create();
    }

    /** @test */
    public function it_can_create_a_publisher_accept()
    {
        $article = Article::factory()->create();
        $member = Member::factory()->create();

        $data = [
            'article_id' => $article->id,
            'member_id' => $member->id,
        ];

        $publisherAccept = PublisherAccept::create($data);

        $this->assertInstanceOf(PublisherAccept::class, $publisherAccept);
        $this->assertEquals($article->id, $publisherAccept->article_id);
        $this->assertEquals($member->id, $publisherAccept->member_id);
    }

    /** @test */
    public function it_has_correct_table_name()
    {
        $this->assertEquals('publisher_accepts', $this->publisherAccept->getTable());
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

        $this->assertEquals($fillable, $this->publisherAccept->getFillable());
    }

    /** @test */
    public function it_belongs_to_a_member()
    {
        $this->assertInstanceOf(Member::class, $this->publisherAccept->member);
        $this->assertEquals($this->publisherAccept->member_id, $this->publisherAccept->member->id);
    }

    /** @test */
    public function it_belongs_to_an_article()
    {
        $this->assertInstanceOf(Article::class, $this->publisherAccept->article);
        $this->assertEquals($this->publisherAccept->article_id, $this->publisherAccept->article->id);
    }

    /** @test */
    public function it_serializes_date_correctly()
    {
        $json = $this->publisherAccept->toArray();
        
        $this->assertMatchesRegularExpression('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $json['created_at']);
    }
}
