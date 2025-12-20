<?php

namespace Tests\Unit\Models;

use App\Models\Article;
use App\Models\EditorAccept;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EditorAcceptTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $editorAccept;

    protected function setUp(): void
    {
        parent::setUp();
        $this->editorAccept = EditorAccept::factory()->create();
    }

    /** @test */
    public function it_can_create_an_editor_accept()
    {
        $article = Article::factory()->create();
        $member = Member::factory()->create();

        $data = [
            'article_id' => $article->id,
            'member_id' => $member->id,
        ];

        $editorAccept = EditorAccept::create($data);

        $this->assertInstanceOf(EditorAccept::class, $editorAccept);
        $this->assertEquals($article->id, $editorAccept->article_id);
        $this->assertEquals($member->id, $editorAccept->member_id);
    }

    /** @test */
    public function it_has_correct_table_name()
    {
        $this->assertEquals('editor_accepts', $this->editorAccept->getTable());
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

        $this->assertEquals($fillable, $this->editorAccept->getFillable());
    }

    /** @test */
    public function it_belongs_to_a_member()
    {
        $this->assertInstanceOf(Member::class, $this->editorAccept->member);
        $this->assertEquals($this->editorAccept->member_id, $this->editorAccept->member->id);
    }

    /** @test */
    public function it_belongs_to_an_article()
    {
        $this->assertInstanceOf(Article::class, $this->editorAccept->article);
        $this->assertEquals($this->editorAccept->article_id, $this->editorAccept->article->id);
    }

    /** @test */
    public function it_can_scope_last_article()
    {
        $article1 = Article::factory()->create();
        $article2 = Article::factory()->create();
        $member1 = Member::factory()->create();
        $member2 = Member::factory()->create();

        // Create EditorAccepts for article1
        EditorAccept::factory()->create(['article_id' => $article1->id, 'member_id' => $member1->id]);
        EditorAccept::factory()->create(['article_id' => $article1->id, 'member_id' => $member2->id]);
        
        // Create EditorAccept for article2
        EditorAccept::factory()->create(['article_id' => $article2->id, 'member_id' => $member1->id]);

        $results = EditorAccept::lastArticle($article1->id)->get();

        $this->assertCount(2, $results);
        $results->each(function ($accept) use ($article1) {
            $this->assertEquals($article1->id, $accept->article_id);
            $this->assertNotNull($accept->member_id);
        });
    }

    /** @test */
    public function it_serializes_date_correctly()
    {
        $json = $this->editorAccept->toArray();
        
        $this->assertMatchesRegularExpression('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $json['created_at']);
    }
}
