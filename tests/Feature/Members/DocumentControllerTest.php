<?php

namespace Tests\Feature\Members;

use App\Models\Article;
use App\Models\SubArticle;
use App\Models\Member;
use App\Models\MemberRole;
use App\Models\MemberType;
use App\Models\Country;
use App\Models\State;
use App\Models\ArticleCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $member;
    protected $article;
    protected $subArticle;

    protected function setUp(): void
    {
        parent::setUp();

        // Create required models
        $country = Country::factory()->create();
        $state = State::factory()->create(['country_id' => $country->id]);
        $memberRole = MemberRole::factory()->create();
        $memberType = MemberType::factory()->create();
        $articleCategory = ArticleCategory::factory()->create();

        // Create member
        $this->member = Member::factory()->create([
            'country_id' => $country->id,
            'state_id' => $state->id,
            'member_type_id' => $memberType->id,
            'member_role_id' => $memberRole->id,
        ]);

        // Create article with loaded relationships
        $this->article = Article::factory()->create([
            'member_id' => $this->member->id,
            'article_category_id' => $articleCategory->id,
            'title' => 'Test Article',
            'article_status' => 3, // Published
        ]);

        // Load the article with relationships
        $this->article->load('sub_articles');

        // Create sub article with uploaded paper
        $this->subArticle = SubArticle::factory()->create([
            'article_id' => $this->article->id,
            'status' => 10, // Approved
        ]);

        // Add media file to sub article
        $tempFile = tmpfile();
        fwrite($tempFile, 'Test document content');
        $filePath = stream_get_meta_data($tempFile)['uri'];

        $this->subArticle->addMedia($filePath)
            ->usingName('test-document')
            ->usingFileName('test-document.docx')
            ->withCustomProperties(['mime_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
            ->toMediaCollection('upload_paper');

        fclose($tempFile);

        // Refresh the article to load relationships
        $this->article->refresh();
        $this->article->load('sub_articles');
    }

    /** @test */
    public function authenticated_member_can_open_document()
    {
        // Act as authenticated member
        $this->actingAs($this->member, 'member');

        // Make request to open document
        $response = $this->get(route('member.open.docx', ['article' => $this->article->id]));

        // Assert successful response
        $response->assertStatus(200);

        // Assert proper headers for file download
        $response->assertHeader('Content-Disposition', 'inline; filename="test-document.docx"');

        // Check that Content-Type header is present (MIME type may vary in testing)
        $response->assertHeader('Content-Type');
    }

    /** @test */
    public function unauthenticated_user_cannot_open_document()
    {
        // Make request without authentication
        $response = $this->get(route('member.open.docx', ['article' => $this->article->id]));

        // Assert redirect to home (middleware behavior)
        $response->assertRedirect('/');
    }

    /** @test */
    public function member_cannot_open_document_for_nonexistent_article()
    {
        // Act as authenticated member
        $this->actingAs($this->member, 'member');

        // Make request with non-existent article ID
        $response = $this->get(route('member.open.docx', ['article' => 99999]));

        // Assert 404 response
        $response->assertStatus(404);
    }

    /** @test */
    public function member_cannot_open_document_without_upload_paper()
    {
        // Create article without sub article or media
        $articleWithoutMedia = Article::factory()->create([
            'member_id' => $this->member->id,
            'article_category_id' => ArticleCategory::factory()->create()->id,
            'title' => 'Article Without Media',
            'article_status' => 3,
        ]);

        // Act as authenticated member
        $this->actingAs($this->member, 'member');

        // Make request to open document
        $response = $this->get(route('member.open.docx', ['article' => $articleWithoutMedia->id]));

        // Assert 404 response (since no media file exists)
        $response->assertStatus(404);
    }
}
