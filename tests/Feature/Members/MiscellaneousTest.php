<?php

namespace Tests\Feature\Members;

use App\Models\Article;
use App\Models\EditorAccept;
use App\Models\Member;
use App\Models\MemberRole;
use App\Models\MemberType;
use App\Models\PublisherAccept;
use App\Models\ReviewerAccept;
use App\Models\ReviewerAcceptFinal;
use App\Models\SubArticle;
use App\Models\Country;
use App\Models\State;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MiscellaneousTest extends TestCase
{
    use RefreshDatabase;

    protected $member;
    protected $article;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();
        Storage::fake('public');

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

        // Create article with sub-article
        $this->article = Article::factory()->create([
            'member_id' => $this->member->id,
        ]);

        SubArticle::factory()->create([
            'article_id' => $this->article->id,
            'status' => 1, // Initial status
        ]);
    }

    /** @test */
    public function member_can_view_bookmark_page()
    {
        $response = $this->actingAs($this->member, 'member')
            ->get(route('member.view-bookmark'));

        $response->assertStatus(200)
            ->assertViewIs('member.profile.bookmark');
    }

    /** @test */
    public function member_can_view_purchased_articles_page()
    {
        $response = $this->actingAs($this->member, 'member')
            ->get(route('member.purchased-article'));

        $response->assertStatus(200)
            ->assertViewIs('member.profile.article-published');
    }

    /** @test */
    public function member_can_become_author()
    {
        $response = $this->actingAs($this->member, 'member')
            ->post(route('member.become-author'));

        $response->assertRedirect()
            ->assertSessionHas('success', 'Congratulations, You are now an Author');

        $this->member->refresh();
        $this->assertEquals(1, $this->member->member_type_id);
    }

    /** @test */
    public function editor_can_accept_article()
    {
        // Create EditorAccept record
        EditorAccept::factory()->create([
            'article_id' => $this->article->id,
        ]);

        $response = $this->actingAs($this->member, 'member')
            ->post(route('member.editor.accept', $this->article->id));

        $response->assertRedirect(route('member.articles.show', $this->article->id))
            ->assertSessionHas('success', 'Article accepted for review successfully');

        // Assert EditorAccept was updated
        $accept = EditorAccept::where('article_id', $this->article->id)->latest()->first();
        $this->assertEquals($this->member->id, $accept->member_id);

        // Assert article status was updated
        $this->article->refresh();
        $this->assertEquals(2, $this->article->article_status);

        // Assert sub-article status was updated
        $this->assertEquals(2, $this->article->last->status);
    }

    /** @test */
    public function editor_accept_fails_without_editor_accept_record()
    {
        $response = $this->actingAs($this->member, 'member')
            ->post(route('member.editor.accept', $this->article->id));

        $response->assertRedirect(); // Should redirect back without success message
    }

    /** @test */
    public function second_editor_can_accept_article()
    {
        // Create EditorAccept record
        EditorAccept::factory()->create([
            'article_id' => $this->article->id,
        ]);

        $response = $this->actingAs($this->member, 'member')
            ->post(route('member.editor.accept.second', $this->article->id));

        $response->assertRedirect(route('member.articles.show', $this->article->id))
            ->assertSessionHas('success', 'Article accepted for review successfully');

        // Assert sub-article status was updated
        $this->article->refresh();
        $this->assertEquals(8, $this->article->last->status);
    }

    /** @test */
    public function third_editor_can_accept_article()
    {
        // Create EditorAccept record
        EditorAccept::factory()->create([
            'article_id' => $this->article->id,
        ]);

        $response = $this->actingAs($this->member, 'member')
            ->post(route('member.editor.accept.third', $this->article->id));

        $response->assertRedirect(route('member.articles.show', $this->article->id))
            ->assertSessionHas('success', 'Article accepted for review successfully');

        // Assert sub-article status was updated
        $this->article->refresh();
        $this->assertEquals(12, $this->article->last->status);
    }

    /** @test */
    public function reviewer_can_accept_article()
    {
        // Create ReviewerAccept record
        ReviewerAccept::factory()->create([
            'article_id' => $this->article->id,
        ]);

        $response = $this->actingAs($this->member, 'member')
            ->post(route('member.reviewer.accept', $this->article->id));

        $response->assertRedirect(route('member.articles.show', $this->article->id))
            ->assertSessionHas('success', 'Article accepted for review successfully');

        // Assert ReviewerAccept was updated
        $accept = ReviewerAccept::where('article_id', $this->article->id)->latest()->first();
        $this->assertEquals($this->member->id, $accept->member_id);

        // Assert article status was updated
        $this->article->refresh();
        $this->assertEquals(2, $this->article->article_status);

        // Assert sub-article status was updated
        $this->assertEquals(4, $this->article->last->status);
    }

    /** @test */
    public function reviewer_accept_fails_without_reviewer_accept_record()
    {
        $response = $this->actingAs($this->member, 'member')
            ->post(route('member.reviewer.accept', $this->article->id));

        $response->assertRedirect(); // Should redirect back without success message
    }

    /** @test */
    public function final_reviewer_can_accept_article()
    {
        // Create ReviewerAcceptFinal record
        ReviewerAcceptFinal::factory()->create([
            'article_id' => $this->article->id,
        ]);

        $response = $this->actingAs($this->member, 'member')
            ->post(route('member.reviewer.accept.final', $this->article->id));

        $response->assertRedirect(route('member.articles.show', $this->article->id))
            ->assertSessionHas('success', 'Article accepted for review successfully');

        // Assert ReviewerAcceptFinal was updated
        $accept = ReviewerAcceptFinal::where('article_id', $this->article->id)->latest()->first();
        $this->assertEquals($this->member->id, $accept->member_id);

        // Assert sub-article status was updated
        $this->article->refresh();
        $this->assertEquals(6, $this->article->last->status);
    }

    /** @test */
    public function final_reviewer_accept_fails_without_reviewer_accept_final_record()
    {
        $response = $this->actingAs($this->member, 'member')
            ->post(route('member.reviewer.accept.final', $this->article->id));

        $response->assertRedirect(); // Should redirect back without success message
    }

    /** @test */
    public function publisher_can_accept_article()
    {
        // Create PublisherAccept record
        PublisherAccept::factory()->create([
            'article_id' => $this->article->id,
            'member_id' => null,
        ]);

        $response = $this->actingAs($this->member, 'member')
            ->post(route('member.publisher.accept', $this->article->id));

        $response->assertRedirect(route('member.articles.show', $this->article->id))
            ->assertSessionHas('success', 'Article accepted for review successfully');

        // Assert PublisherAccept was updated
        $accept = PublisherAccept::where('article_id', $this->article->id)->latest()->first();
        $this->assertEquals($this->member->id, $accept->member_id);
    }

    /** @test */
    public function publisher_accept_fails_if_already_accepted()
    {
        // Create PublisherAccept record with member_id already set
        PublisherAccept::factory()->create([
            'article_id' => $this->article->id,
            'member_id' => $this->member->id,
        ]);

        $response = $this->actingAs($this->member, 'member')
            ->post(route('member.publisher.accept', $this->article->id));

        $response->assertRedirect(); // Should redirect back without success message
    }

    /** @test */
    public function member_can_update_article_amount_and_publish()
    {
        // Create a fake PDF file
        $pdfFile = UploadedFile::fake()->create('test.pdf', 1000, 'application/pdf');

        $updateData = [
            'volume' => '1',
            'issue_no' => '1',
            'doi_link' => 'https://doi.org/10.1234/test',
            'pdf_doc' => $pdfFile,
            'amount' => 500,
        ];

        $response = $this->actingAs($this->member, 'member')
            ->post(route('member.update-amount', $this->article->id), $updateData);

        $response->assertRedirect()
            ->assertSessionHas('success', 'Article Sent to Publishers Successfully');

        // Assert article was updated
        $this->article->refresh();
        $this->assertEquals('1', $this->article->volume);
        $this->assertEquals('1', $this->article->issue_no);
        $this->assertEquals('https://doi.org/10.1234/test', $this->article->doi_link);
        $this->assertTrue($this->article->is_recommended);

        // Assert sub-article status was updated
        $this->assertEquals(9, $this->article->last->status);

        // Assert PublisherAccept was created
        $this->assertDatabaseHas('publisher_accepts', [
            'article_id' => $this->article->id,
        ]);
    }

    /** @test */
    public function update_amount_validation_fails_with_missing_required_fields()
    {
        $response = $this->actingAs($this->member, 'member')
            ->post(route('member.update-amount', $this->article->id), []);

        $response->assertSessionHasErrors(['volume', 'issue_no', 'pdf_doc']);
    }

    /** @test */
    public function update_amount_validation_fails_with_invalid_doi_link()
    {
        $pdfFile = UploadedFile::fake()->create('test.pdf', 1000, 'application/pdf');

        $updateData = [
            'volume' => '1',
            'issue_no' => '1',
            'doi_link' => 'invalid-url',
            'pdf_doc' => $pdfFile,
            'amount' => 500,
        ];

        $response = $this->actingAs($this->member, 'member')
            ->post(route('member.update-amount', $this->article->id), $updateData);

        $response->assertSessionHasErrors('doi_link');
    }

    /** @test */
    public function update_amount_validation_fails_with_invalid_file_type()
    {
        $invalidFile = UploadedFile::fake()->create('test.txt', 1000, 'text/plain');

        $updateData = [
            'volume' => '1',
            'issue_no' => '1',
            'pdf_doc' => $invalidFile,
            'amount' => 500,
        ];

        $response = $this->actingAs($this->member, 'member')
            ->post(route('member.update-amount', $this->article->id), $updateData);

        $response->assertSessionHasErrors('pdf_doc');
    }

    /** @test */
    public function update_amount_requires_amount_for_closed_access_articles()
    {
        // Update article to closed access
        $this->article->update(['access_type' => 2]);

        $pdfFile = UploadedFile::fake()->create('test.pdf', 1000, 'application/pdf');

        $updateData = [
            'volume' => '1',
            'issue_no' => '1',
            'pdf_doc' => $pdfFile,
            // Missing amount
        ];

        $response = $this->actingAs($this->member, 'member')
            ->post(route('member.update-amount', $this->article->id), $updateData);

        $response->assertSessionHasErrors('amount');
    }

    /** @test */
    public function update_amount_fails_with_insufficient_amount()
    {
        // Update article to closed access
        $this->article->update(['access_type' => 2]);

        $pdfFile = UploadedFile::fake()->create('test.pdf', 1000, 'application/pdf');

        $updateData = [
            'volume' => '1',
            'issue_no' => '1',
            'pdf_doc' => $pdfFile,
            'amount' => 100, // Below minimum 500
        ];

        $response = $this->actingAs($this->member, 'member')
            ->post(route('member.update-amount', $this->article->id), $updateData);

        $response->assertSessionHasErrors('amount');
    }
}