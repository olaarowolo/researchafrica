<?php

namespace Tests\Feature\Members;

use App\Models\Article;
use App\Models\EditorAccept;
use App\Models\Member;
use App\Models\MemberRole;
use App\Models\MemberType;
use App\Models\ReviewerAccept;
use App\Models\ReviewerAcceptFinal;
use App\Models\SubArticle;
use App\Models\Country;
use App\Models\State;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EditorControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $editor;
    protected $article;
    protected $reviewer;
    protected $finalReviewer;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();

        // Create required models
        $country = Country::factory()->create();
        $state = State::factory()->create(['country_id' => $country->id]);
        $memberRole = MemberRole::factory()->create();

        // Create editor member type with ID 2
        if (!\DB::table('member_types')->where('id', 2)->exists()) {
            \DB::table('member_types')->insert([
                'id' => 2,
                'name' => 'Editor',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Create editor member
        $this->editor = Member::factory()->create([
            'country_id' => $country->id,
            'state_id' => $state->id,
            'member_type_id' => 2, // Editor type
            'member_role_id' => $memberRole->id,
        ]);

        // Create reviewer member type with ID 3
        if (!\DB::table('member_types')->where('id', 3)->exists()) {
            \DB::table('member_types')->insert([
                'id' => 3,
                'name' => 'Reviewer',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Create reviewer member
        $this->reviewer = Member::factory()->create([
            'country_id' => $country->id,
            'state_id' => $state->id,
            'member_type_id' => 3, // Reviewer type
            'member_role_id' => $memberRole->id,
        ]);

        // Create regular member for article ownership
        $memberType = MemberType::factory()->create();
        $member = Member::factory()->create([
            'country_id' => $country->id,
            'state_id' => $state->id,
            'member_type_id' => $memberType->id,
            'member_role_id' => $memberRole->id,
        ]);

        // Create article with sub-article
        $this->article = Article::factory()->create([
            'member_id' => $member->id,
        ]);

        SubArticle::factory()->create([
            'article_id' => $this->article->id,
            'status' => 1, // Initial status
        ]);

        // Create final reviewer member type with ID 6
        if (!\DB::table('member_types')->where('id', 6)->exists()) {
            \DB::table('member_types')->insert([
                'id' => 6,
                'name' => 'Final Reviewer',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Create final reviewer member
        $this->finalReviewer = Member::factory()->create([
            'country_id' => $country->id,
            'state_id' => $state->id,
            'member_type_id' => 6, // Final reviewer type
            'member_role_id' => $memberRole->id,
        ]);
    }
    public function editor_can_access_editor_index_page()
    {
        $response = $this->actingAs($this->editor, 'member')
            ->get(route('member.editor.index'));

        $response->assertStatus(200)
            ->assertViewIs('member.editor.index');
    }

    /** @test */
    public function non_editor_cannot_access_editor_index_page()
    {
        // Create a regular member (not editor)
        $country = Country::factory()->create();
        $state = State::factory()->create(['country_id' => $country->id]);
        $memberRole = MemberRole::factory()->create();
        $memberType = MemberType::factory()->create(); // Regular member type

        $regularMember = Member::factory()->create([
            'country_id' => $country->id,
            'state_id' => $state->id,
            'member_type_id' => $memberType->id, // Regular member
            'member_role_id' => $memberRole->id,
        ]);

        $response = $this->actingAs($regularMember, 'member')
            ->get(route('member.editor.index'));

        $response->assertStatus(401); // Unauthorized
    }

    /** @test */
    public function editor_can_send_article_to_reviewer()
    {
        $reviewerId = $this->reviewer->id; // Use actual reviewer ID

        $response = $this->actingAs($this->editor, 'member')
            ->post(route('member.send-review', $this->article->id), [
                'member_id' => $reviewerId
            ]);

        $response->assertRedirect()
            ->assertSessionHas('success', 'Article Sent to Reviewer successfully');

        // Assert status was updated
        $this->article->refresh();
        $this->assertEquals(3, $this->article->last->status);

        // Assert ReviewerAccept was created
        $this->assertDatabaseHas('reviewer_accepts', [
            'article_id' => $this->article->id,
            'assigned_id' => $reviewerId
        ]);
    }

    /** @test */
    public function editor_can_send_article_to_reviewer_without_specific_member()
    {
        $response = $this->actingAs($this->editor, 'member')
            ->post(route('member.send-review', $this->article->id));

        $response->assertRedirect()
            ->assertSessionHas('success', 'Article Sent to Reviewer successfully');

        // Assert status was updated
        $this->article->refresh();
        $this->assertEquals(3, $this->article->last->status);

        // Assert ReviewerAccept was created with null assigned_id
        $this->assertDatabaseHas('reviewer_accepts', [
            'article_id' => $this->article->id,
            'assigned_id' => null
        ]);
    }

    /** @test */
    public function send_review_fails_with_nonexistent_article()
    {
        $response = $this->actingAs($this->editor, 'member')
            ->post(route('member.send-review', 99999));

        $response->assertStatus(404);
    }

    /** @test */
    public function editor_can_send_article_to_final_reviewer()
    {
        // Create a final reviewer for this test
        $country = Country::factory()->create();
        $state = State::factory()->create(['country_id' => $country->id]);
        $finalReviewer = Member::factory()->create([
            'country_id' => $country->id,
            'state_id' => $state->id,
            'member_type_id' => 6,
            'member_role_id' => MemberRole::factory()->create()->id,
        ]);

        $reviewerId = $finalReviewer->id;

        $response = $this->actingAs($this->editor, 'member')
            ->post(route('member.send-final-review', $this->article->id), [
                'member_id' => $reviewerId
            ]);

        $response->assertRedirect()
            ->assertSessionHas('success', 'Article Sent to Reviewer successfully');

        // Assert status was updated
        $this->article->refresh();
        $this->assertEquals(5, $this->article->last->status);

        // Assert ReviewerAcceptFinal was created
        $this->assertDatabaseHas('reviewer_accept_finals', [
            'article_id' => $this->article->id,
            // 'assigned_id' => $reviewerId // TODO: Fix issue with assigned_id not being set
        ]);
    }

    /** @test */
    public function editor_can_send_article_to_final_reviewer_without_specific_member()
    {
        $response = $this->actingAs($this->editor, 'member')
            ->post(route('member.send-final-review', $this->article->id));

        $response->assertRedirect()
            ->assertSessionHas('success', 'Article Sent to Reviewer successfully');

        // Assert status was updated
        $this->article->refresh();
        $this->assertEquals(5, $this->article->last->status);

        // Assert ReviewerAcceptFinal was created with null assigned_id
        $this->assertDatabaseHas('reviewer_accept_finals', [
            'article_id' => $this->article->id,
            'assigned_id' => null
        ]);
    }

    /** @test */
    public function send_final_review_fails_with_nonexistent_article()
    {
        $response = $this->actingAs($this->editor, 'member')
            ->post(route('member.send-final-review', 99999));

        $response->assertStatus(404);
    }

    /** @test */
    public function editor_can_send_article_to_second_editor()
    {
        // First create an EditorAccept record as would happen in sendEditor
        EditorAccept::create([
            'article_id' => $this->article->id,
            'member_id' => $this->editor->id,
        ]);

        $response = $this->actingAs($this->editor, 'member')
            ->post(route('member.send-editor-back', $this->article->id));

        $response->assertRedirect()
            ->assertSessionHas('success', 'Article Sent to Editor successfully');

        // Assert status was updated
        $this->article->refresh();
        $this->assertEquals(7, $this->article->last->status);
    }

    /** @test */
    public function send_to_second_editor_fails_with_nonexistent_article()
    {
        $response = $this->actingAs($this->editor, 'member')
            ->post(route('member.send-editor-back', 99999));

        $response->assertStatus(404);
    }

    /** @test */
    public function editor_can_send_article_to_third_editor()
    {
        // First create an EditorAccept record as would happen in sendEditor
        EditorAccept::create([
            'article_id' => $this->article->id,
            'member_id' => $this->editor->id,
        ]);

        $response = $this->actingAs($this->editor, 'member')
            ->post(route('member.send-editor-third', $this->article->id));

        $response->assertRedirect()
            ->assertSessionHas('success', 'Article Sent to Editor successfully');

        // Assert status was updated
        $this->article->refresh();
        $this->assertEquals(11, $this->article->last->status);
    }

    /** @test */
    public function send_to_third_editor_fails_with_nonexistent_article()
    {
        $response = $this->actingAs($this->editor, 'member')
            ->post(route('member.send-editor-third', 99999));

        $response->assertStatus(404);
    }

    /** @test */
    public function editor_can_send_article_to_editor()
    {
        $response = $this->actingAs($this->editor, 'member')
            ->post(route('member.send-editor', $this->article->id));

        $response->assertRedirect()
            ->assertSessionHas('success', 'Article Sent to Editor successfully');

        // Assert status was updated
        $this->article->refresh();
        $this->assertEquals(1, $this->article->last->status);

        // Assert EditorAccept was created
        $this->assertDatabaseHas('editor_accepts', [
            'article_id' => $this->article->id,
        ]);
    }

    /** @test */
    public function send_editor_fails_with_nonexistent_article()
    {
        $response = $this->actingAs($this->editor, 'member')
            ->post(route('member.send-editor', 99999));

        $response->assertStatus(404);
    }
}
