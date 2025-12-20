<?php

namespace Tests\Feature\Members;

use App\Models\Member;
use App\Models\MemberRole;
use App\Models\MemberType;
use App\Models\Country;
use App\Models\State;
use App\Models\Article;
use App\Models\EditorAccept;
use App\Models\ReviewerAccept;
use App\Models\ReviewerAcceptFinal;
use App\Models\PublisherAccept;
use App\Http\Requests\Member\UpdateProfileRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileSecurityControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $member;
    protected $adminMember;
    protected $editorMember;
    protected $reviewerMember;
    protected $reviewerFinalMember;
    protected $publisherMember;
    protected $article;

    protected function seedBasicData()
    {
        // Create basic required data for relationships
        \App\Models\Country::factory()->create(['name' => 'Nigeria']);
        \App\Models\MemberType::factory()->create(['name' => 'Author', 'id' => 1]);
        \App\Models\MemberType::factory()->create(['name' => 'Editor', 'id' => 2]);
        \App\Models\MemberType::factory()->create(['name' => 'Reviewer', 'id' => 3]);
        \App\Models\MemberType::factory()->create(['name' => 'User', 'id' => 4]);
        \App\Models\MemberType::factory()->create(['name' => 'Publisher', 'id' => 5]);
        \App\Models\MemberType::factory()->create(['name' => 'Reviewer 2', 'id' => 6]);
        \App\Models\MemberRole::factory()->create(['title' => 'Author']);
        \App\Models\MemberRole::factory()->create(['title' => 'Editor']);
        \App\Models\MemberRole::factory()->create(['title' => 'Reviewer']);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedBasicData();

        // Create required models
        $country = Country::factory()->create();
        $state = State::factory()->create(['country_id' => $country->id]);

        // Create member types
        $authorType = MemberType::factory()->create(['name' => 'Author']);
        $editorType = MemberType::factory()->create(['name' => 'Editor']);
        $reviewerType = MemberType::factory()->create(['name' => 'Reviewer']);
        $reviewerFinalType = MemberType::factory()->create(['name' => 'Reviewer 2']);
        $publisherType = MemberType::factory()->create(['name' => 'Publisher']);

        // Create member roles
        $memberRole = MemberRole::factory()->create();

        // Create regular member (author) - member_type_id = 1
        $this->member = Member::factory()->create([
            'country_id' => $country->id,
            'state_id' => $state->id,
            'member_type_id' => 1, // Author type
            'member_role_id' => $memberRole->id,
        ]);

        // Create editor member - member_type_id = 2
        $this->editorMember = Member::factory()->create([
            'country_id' => $country->id,
            'state_id' => $state->id,
            'member_type_id' => 2, // Editor type
            'member_role_id' => $memberRole->id,
        ]);

        // Create reviewer member - member_type_id = 3
        $this->reviewerMember = Member::factory()->create([
            'country_id' => $country->id,
            'state_id' => $state->id,
            'member_type_id' => 3, // Reviewer type
            'member_role_id' => $memberRole->id,
        ]);

        // Create reviewer final member - member_type_id = 6
        $this->reviewerFinalMember = Member::factory()->create([
            'country_id' => $country->id,
            'state_id' => $state->id,
            'member_type_id' => 6, // Reviewer 2 type
            'member_role_id' => $memberRole->id,
        ]);

        // Create publisher member - member_type_id = 5
        $this->publisherMember = Member::factory()->create([
            'country_id' => $country->id,
            'state_id' => $state->id,
            'member_type_id' => 5, // Publisher type
            'member_role_id' => $memberRole->id,
        ]);

        // Create article for testing
        $this->article = Article::factory()->create([
            'member_id' => $this->member->id,
            'article_status' => 1
        ]);
    }

    /** @test */
    public function profile_page_redirects_unauthenticated_users()
    {
        $response = $this->get(route('member.profile'));

        $response->assertRedirect(route('member.login'));
    }

    /** @test */
    public function profile_page_displays_author_dashboard()
    {
        $response = $this->actingAs($this->member, 'member')
            ->get(route('member.profile'));

        $response->assertStatus(200);
        $response->assertViewIs('member.profile.author');
        $response->assertViewHas('reviewArticles');
        $response->assertViewHas('publishArticles');
    }

    /** @test */
    public function profile_page_displays_author_articles_correctly()
    {
        // Create articles with different statuses
        $draftArticle = Article::factory()->create([
            'member_id' => $this->member->id,
            'article_status' => 1
        ]);

        $underReviewArticle = Article::factory()->create([
            'member_id' => $this->member->id,
            'article_status' => 2
        ]);

        $publishedArticle = Article::factory()->create([
            'member_id' => $this->member->id,
            'article_status' => 3
        ]);

        $response = $this->actingAs($this->member, 'member')
            ->get(route('member.profile'));

        $response->assertStatus(200);
        $viewData = $response->viewData('reviewArticles');
        $this->assertTrue($viewData->contains($draftArticle));
        $this->assertTrue($viewData->contains($underReviewArticle));

        $publishData = $response->viewData('publishArticles');
        $this->assertTrue($publishData->contains($publishedArticle));
    }

    /** @test */
    public function profile_page_displays_editor_dashboard()
    {
        // Create unaccepted editor assignment
        EditorAccept::factory()->create([
            'article_id' => $this->article->id,
            'member_id' => null
        ]);

        $response = $this->actingAs($this->editorMember, 'member')
            ->get(route('member.profile'));

        $response->assertStatus(200);
        $response->assertViewIs('member.profile.editor');
        $response->assertViewHas('newArticles');
        $response->assertViewHas('processed');
        $response->assertViewHas('processing');
    }

    /** @test */
    public function profile_page_displays_reviewer_dashboard()
    {
        // Create unaccepted reviewer assignment
        ReviewerAccept::factory()->create([
            'article_id' => $this->article->id,
            'member_id' => null
        ]);

        $response = $this->actingAs($this->reviewerMember, 'member')
            ->get(route('member.profile'));

        $response->assertStatus(200);
        $response->assertViewIs('member.profile.reviewer');
        $response->assertViewHas('newArticles');
        $response->assertViewHas('acceptedArticle');
    }

    /** @test */
    public function profile_page_displays_reviewer_final_dashboard()
    {
        // Create unaccepted reviewer final assignment
        ReviewerAcceptFinal::factory()->create([
            'article_id' => $this->article->id,
            'member_id' => null
        ]);

        $response = $this->actingAs($this->reviewerFinalMember, 'member')
            ->get(route('member.profile'));

        $response->assertStatus(200);
        $response->assertViewIs('member.profile.reviewer');
        $response->assertViewHas('newArticles');
        $response->assertViewHas('acceptedArticle');
    }

    /** @test */
    public function profile_page_displays_publisher_dashboard()
    {
        // Create unaccepted publisher assignment
        PublisherAccept::factory()->create([
            'article_id' => $this->article->id,
            'member_id' => null
        ]);

        $response = $this->actingAs($this->publisherMember, 'member')
            ->get(route('member.profile'));

        $response->assertStatus(200);
        $response->assertViewIs('member.profile.publisher');
        $response->assertViewHas('newArticles');
        $response->assertViewHas('acceptedArticle');
    }

    /** @test */
    public function profile_page_displays_researcher_dashboard()
    {
        // Create researcher member with account type (member_type_id = 4)
        $researcherMember = Member::factory()->create([
            'country_id' => $this->member->country_id,
            'state_id' => $this->member->state_id,
            'member_type_id' => 4, // Account/Researcher type
            'member_role_id' => $this->member->member_role_id,
        ]);

        $response = $this->actingAs($researcherMember, 'member')
            ->get(route('member.profile'));

        $response->assertStatus(200);
        $response->assertViewIs('member.profile.researcher');
    }

    /** @test */
    public function edit_profile_page_displays_for_authenticated_user()
    {
        $response = $this->actingAs($this->member, 'member')
            ->get(route('member.profile.edit'));

        $response->assertStatus(200);
        $response->assertViewIs('member.profile.edit');
        $response->assertViewHas('user');
        $response->assertViewHas('countries');
        $response->assertViewHas('states');
    }

    /** @test */
    public function edit_profile_page_loads_states_based_on_country()
    {
        $response = $this->actingAs($this->member, 'member')
            ->get(route('member.profile.edit'));

        $response->assertStatus(200);
        $response->assertViewHas('states');

        $states = $response->viewData('states');
        $this->assertTrue($states->contains('id', $this->member->state_id));
    }

    /** @test */
    public function update_profile_succeeds_with_valid_data()
    {
        $updateData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone_number' => '+1234567890',
            'gender' => 'Male',
            'date_of_birth' => '1990-01-01',
            'country_id' => $this->member->country_id,
            'state_id' => $this->member->state_id,
        ];

        $response = $this->actingAs($this->member, 'member')
            ->post(route('member.profile.update'), $updateData);

        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Profile Updated Successfully');

        $this->assertDatabaseHas('members', [
            'id' => $this->member->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);
    }

    /** @test */
    public function update_profile_fails_with_invalid_email()
    {
        $updateData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'invalid-email',
        ];

        $response = $this->actingAs($this->member, 'member')
            ->post(route('member.profile.update'), $updateData);

        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function change_password_succeeds_with_valid_old_password()
    {
        $passwordData = [
            'old_password' => 'password',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ];

        $response = $this->actingAs($this->member, 'member')
            ->post(route('member.password.changePassword'), $passwordData);

        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Password Updated Successfully');

        // Verify password was updated
        $this->assertTrue(Hash::check('newpassword123', $this->member->fresh()->password));
    }

    /** @test */
    public function change_password_fails_with_invalid_old_password()
    {
        $passwordData = [
            'old_password' => 'wrongpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ];

        $response = $this->actingAs($this->member, 'member')
            ->post(route('member.password.changePassword'), $passwordData);

        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertSessionHasErrors(['message']);
    }

    /** @test */
    public function change_password_fails_without_confirmation()
    {
        $passwordData = [
            'old_password' => 'password',
            'password' => 'newpassword123',
        ];

        $response = $this->actingAs($this->member, 'member')
            ->post(route('member.password.changePassword'), $passwordData);

        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertSessionHasErrors(['password']);
    }

    /** @test */
    public function change_password_fails_with_mismatched_confirmation()
    {
        $passwordData = [
            'old_password' => 'password',
            'password' => 'newpassword123',
            'password_confirmation' => 'differentpassword',
        ];

        $response = $this->actingAs($this->member, 'member')
            ->post(route('member.password.changePassword'), $passwordData);

        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertSessionHasErrors(['password']);
    }

    /** @test */
    public function profile_picture_upload_succeeds_with_valid_file()
    {
        // Create a fake image file
        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->actingAs($this->member, 'member')
            ->post(route('member.profile_picture'), [
                'profile_picture' => $file
            ]);

        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Photo Uploaded Successfully');
    }

    /** @test */
    public function profile_picture_upload_fails_with_invalid_file_type()
    {
        // Create a fake text file
        $file = UploadedFile::fake()->create('document.txt');

        $response = $this->actingAs($this->member, 'member')
            ->post(route('member.profile_picture'), [
                'profile_picture' => $file
            ]);

        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertSessionHasErrors(['profile_picture']);
    }

    /** @test */
    public function profile_picture_upload_fails_without_file()
    {
        $response = $this->actingAs($this->member, 'member')
            ->post(route('member.profile_picture'), []);

        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertSessionHasErrors(['profile_picture']);
    }

    /** @test */
    public function editor_dashboard_shows_unaccepted_articles()
    {
        // Create multiple unaccepted articles for editor
        $unacceptedArticle1 = Article::factory()->create(['article_status' => 1]);
        $unacceptedArticle2 = Article::factory()->create(['article_status' => 1]);

        EditorAccept::factory()->create([
            'article_id' => $unacceptedArticle1->id,
            'member_id' => null
        ]);

        EditorAccept::factory()->create([
            'article_id' => $unacceptedArticle2->id,
            'member_id' => null
        ]);

        $response = $this->actingAs($this->editorMember, 'member')
            ->get(route('member.profile'));

        $response->assertStatus(200);
        $newArticles = $response->viewData('newArticles');
        $this->assertEquals(2, $newArticles->count());
    }

    /** @test */
    public function editor_dashboard_shows_accepted_articles_by_status()
    {
        // Create accepted articles with different statuses
        $processedArticle = Article::factory()->create([
            'member_id' => $this->member->id,
            'article_status' => 3
        ]);

        $processingArticle = Article::factory()->create([
            'member_id' => $this->member->id,
            'article_status' => 2
        ]);

        EditorAccept::factory()->create([
            'article_id' => $processedArticle->id,
            'member_id' => $this->editorMember->id
        ]);

        EditorAccept::factory()->create([
            'article_id' => $processingArticle->id,
            'member_id' => $this->editorMember->id
        ]);

        $response = $this->actingAs($this->editorMember, 'member')
            ->get(route('member.profile'));

        $response->assertStatus(200);

        $processed = $response->viewData('processed');
        $this->assertTrue($processed->contains($processedArticle));

        $processing = $response->viewData('processing');
        $this->assertTrue($processing->contains($processingArticle));
    }

    /** @test */
    public function reviewer_dashboard_handles_assigned_reviewer()
    {
        // Create review assignment where this reviewer is assigned
        $assignedArticle = Article::factory()->create(['article_status' => 1]);

        ReviewerAccept::factory()->create([
            'article_id' => $assignedArticle->id,
            'member_id' => null,
            'assigned_id' => $this->reviewerMember->id
        ]);

        $response = $this->actingAs($this->reviewerMember, 'member')
            ->get(route('member.profile'));

        $response->assertStatus(200);
        $newArticles = $response->viewData('newArticles');
        $this->assertTrue($newArticles->contains($assignedArticle));
    }

    /** @test */
    public function profile_methods_require_authentication()
    {
        $endpoints = [
            ['GET', route('member.profile')],
            ['GET', route('member.profile.edit')],
            ['POST', route('member.profile.update')],
            ['POST', route('member.password.changePassword')],
            ['POST', route('member.profile_picture')],
        ];

        foreach ($endpoints as [$method, $url]) {
            $response = $this->call($method, $url);
            $response->assertRedirect(route('member.login'));
        }
    }

    /** @test */
    public function edit_profile_loads_correct_states_for_user_country()
    {
        $userCountry = $this->member->country_id;

        // Create additional states for the same country
        $additionalState = State::factory()->create(['country_id' => $userCountry]);

        $response = $this->actingAs($this->member, 'member')
            ->get(route('member.profile.edit'));

        $response->assertStatus(200);

        $states = $response->viewData('states');
        $this->assertTrue($states->contains('id', $this->member->state_id));
        $this->assertTrue($states->contains('id', $additionalState->id));
    }

    /** @test */
    public function update_profile_preserves_other_member_fields()
    {
        $originalData = [
            'first_name' => $this->member->first_name,
            'last_name' => $this->member->last_name,
            'email' => $this->member->email,
            'phone' => '+1234567890',
        ];

        $response = $this->actingAs($this->member, 'member')
            ->post(route('member.profile.update'), $originalData);

        $response->assertStatus(302);

        $updatedMember = $this->member->fresh();

        $this->assertEquals($this->member->country_id, $updatedMember->country_id);
        $this->assertEquals($this->member->member_role_id, $updatedMember->member_role_id);
        $this->assertEquals($this->member->member_type_id, $updatedMember->member_type_id);
    }
}
