<?php

namespace Tests\Integration;

use Tests\TestCase;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Member;
use App\Models\JournalMembership;
use App\Models\EditorialWorkflow;
use App\Models\ArticleEditorialProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class DatabaseRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedBasicData();
    }

    protected function seedBasicData()
    {
        // Create basic required data for relationships
        \App\Models\Country::factory()->create(['name' => 'Nigeria']);
        \App\Models\MemberType::factory()->create(['name' => 'Author']);
        \App\Models\MemberType::factory()->create(['name' => 'Editor']);
        \App\Models\MemberType::factory()->create(['name' => 'Reviewer']);
        \App\Models\MemberRole::factory()->create(['title' => 'Author']);
        \App\Models\MemberRole::factory()->create(['title' => 'Editor']);
        \App\Models\MemberRole::factory()->create(['title' => 'Reviewer']);
    }

    /** @test */
    public function it_verifies_article_article_category_relationship()
    {
        // Arrange
        $category = ArticleCategory::factory()->create([
            'name' => 'Computer Science',
            'status' => 'Active'
        ]);

        $article = Article::factory()->create([
            'article_category_id' => $category->id,
            'title' => 'Test Article',
            'article_status' => 3 // Published
        ]);

        // Act & Assert
        $this->assertModelExists($article);
        $this->assertModelExists($category);

        // Test relationship from Article to ArticleCategory
        $this->assertInstanceOf(ArticleCategory::class, $article->article_category);
        $this->assertEquals($category->id, $article->article_category->id);
        $this->assertEquals($category->name, $article->article_category->name);

        // Test reverse relationship from ArticleCategory to Articles
        $this->assertTrue($category->articles->contains($article));
        $this->assertEquals(1, $category->articles()->count());

        // Test eager loading
        $loadedArticle = Article::with('article_category')->find($article->id);
        $this->assertNotNull($loadedArticle->article_category);
        $this->assertEquals($category->name, $loadedArticle->article_category->name);
    }

    /** @test */
    public function it_verifies_member_journal_membership_relationship()
    {
        // Arrange
        $member = Member::factory()->create([
            'email_address' => 'editor@example.com',
            'member_type_id' => 2 // Editor
        ]);

        $journal = ArticleCategory::factory()->create([
            'name' => 'Engineering Journal',
            'is_journal' => true,
            'status' => 'Active'
        ]);

        $membership = JournalMembership::factory()->create([
            'member_id' => $member->id,
            'journal_id' => $journal->id,
            'member_type_id' => 2 // Editor
        ]);

        // Act & Assert
        $this->assertModelExists($member);
        $this->assertModelExists($journal);
        $this->assertModelExists($membership);

        // Test relationship from Member to JournalMemberships
        $this->assertTrue($member->journalMemberships->contains($membership));
        $this->assertEquals(1, $member->journalMemberships()->count());

        // Test reverse relationship from Journal to Members via memberships
        $this->assertTrue($journal->memberships->contains($membership));
        $this->assertEquals(1, $journal->memberships()->count());
    }

    /** @test */
    public function it_verifies_comment_relationships()
    {
        // Arrange
        $article = Article::factory()->create([
            'title' => 'Comment Test Article',
            'article_status' => 3 // Published
        ]);

        $member = Member::factory()->create([
            'email_address' => 'commenter@example.com'
        ]);

        $comment = \App\Models\Comment::factory()->create([
            'article_id' => $article->id,
            'member_id' => $member->id,
            'content' => 'This is a test comment'
        ]);

        // Act & Assert
        $this->assertModelExists($comment);

        // Test comment to article relationship
        $this->assertInstanceOf(Article::class, $comment->article);
        $this->assertEquals($article->id, $comment->article->id);

        // Test comment to member relationship
        $this->assertInstanceOf(Member::class, $comment->member);
        $this->assertEquals($member->id, $comment->member->id);
    }

    /** @test */
    public function it_verifies_bookmark_relationships()
    {
        // Arrange
        $member = Member::factory()->create([
            'email_address' => 'bookmarker@example.com'
        ]);

        $article = Article::factory()->create([
            'title' => 'Bookmark Test Article',
            'article_status' => 3 // Published
        ]);

        $bookmark = \App\Models\Bookmark::factory()->create([
            'member_id' => $member->id,
            'article_id' => $article->id
        ]);

        // Act & Assert
        $this->assertModelExists($bookmark);

        // Test bookmark relationships
        $this->assertInstanceOf(Member::class, $bookmark->member);
        $this->assertInstanceOf(Article::class, $bookmark->article);

        $this->assertEquals($member->id, $bookmark->member->id);
        $this->assertEquals($article->id, $bookmark->article->id);

        // Test reverse relationships
        $this->assertTrue($member->bookmarks->contains($bookmark));
        $this->assertTrue($article->bookmarks->contains($bookmark));
    }

    /** @test */
    public function it_verifies_afriscribe_request_relationships()
    {
        // Arrange
        $member = Member::factory()->create([
            'email_address' => 'afriscribe@example.com'
        ]);

        $article = Article::factory()->create([
            'title' => 'Afriscribe Test Article',
            'article_status' => 3 // Published
        ]);

        $request = \App\Models\AfriscribeRequest::factory()->create([
            'member_id' => $member->id,
            'article_id' => $article->id,
            'status' => 'pending'
        ]);

        // Act & Assert
        $this->assertModelExists($request);

        // Test Afriscribe request relationships
        $this->assertInstanceOf(Member::class, $request->member);
        $this->assertInstanceOf(Article::class, $request->article);

        $this->assertEquals($member->id, $request->member->id);
        $this->assertEquals($article->id, $request->article->id);
    }

    /** @test */
    public function it_tests_relationship_data_integrity()
    {
        // Arrange
        $article = Article::factory()->create();
        $category = ArticleCategory::factory()->create();

        // Act & Assert - Try to create article with invalid category
        $this->expectException(\Illuminate\Database\QueryException::class);

        Article::factory()->create([
            'article_category_id' => 99999 // Non-existent category
        ]);
    }

    /** @test */
    public function it_verifies_member_article_relationship()
    {
        // Arrange
        $member = Member::factory()->create([
            'email_address' => 'author@example.com'
        ]);

        $article = Article::factory()->create([
            'member_id' => $member->id,
            'title' => 'Member Article Test',
            'article_status' => 3 // Published
        ]);

        // Act & Assert
        $this->assertModelExists($article);
        $this->assertModelExists($member);

        // Test member to articles relationship
        $this->assertTrue($member->memberArticles->contains($article));
        $this->assertEquals(1, $member->memberArticles()->count());

        // Test article to member relationship
        $this->assertInstanceOf(Member::class, $article->member);
        $this->assertEquals($member->id, $article->member->id);
    }

    /** @test */
    public function it_verifies_country_state_relationships()
    {
        // Arrange
        $country = \App\Models\Country::factory()->create([
            'name' => 'United States'
        ]);

        $state = \App\Models\State::factory()->create([
            'name' => 'California',
            'country_id' => $country->id
        ]);

        $member = Member::factory()->create([
            'email_address' => 'location@example.com',
            'country_id' => $country->id,
            'state_id' => $state->id
        ]);

        // Act & Assert
        $this->assertModelExists($country);
        $this->assertModelExists($state);
        $this->assertModelExists($member);

        // Test country to states relationship
        $this->assertTrue($country->states->contains($state));
        $this->assertEquals(1, $country->states()->count());

        // Test state to country relationship
        $this->assertInstanceOf(\App\Models\Country::class, $state->country);
        $this->assertEquals($country->id, $state->country->id);

        // Test member to country relationship
        $this->assertInstanceOf(\App\Models\Country::class, $member->country);
        $this->assertEquals($country->id, $member->country->id);

        // Test member to state relationship
        $this->assertInstanceOf(\App\Models\State::class, $member->state);
        $this->assertEquals($state->id, $member->state->id);
    }

    /** @test */
    public function it_verifies_member_role_type_relationships()
    {
        // Arrange
        $memberRole = \App\Models\MemberRole::factory()->create([
            'title' => 'Senior Author'
        ]);

        $memberType = \App\Models\MemberType::factory()->create([
            'name' => 'Premium Member'
        ]);

        $member = Member::factory()->create([
            'email_address' => 'role@example.com',
            'member_role_id' => $memberRole->id,
            'member_type_id' => $memberType->id
        ]);

        // Act & Assert
        $this->assertModelExists($memberRole);
        $this->assertModelExists($memberType);
        $this->assertModelExists($member);

        // Test member to role relationship
        $this->assertInstanceOf(\App\Models\MemberRole::class, $member->member_role);
        $this->assertEquals($memberRole->id, $member->member_role->id);

        // Test member to type relationship
        $this->assertInstanceOf(\App\Models\MemberType::class, $member->member_type);
        $this->assertEquals($memberType->id, $member->member_type->id);
    }

    /** @test */
    public function it_tests_basic_factory_functionality()
    {
        // Arrange & Act
        $member = Member::factory()->create([
            'email_address' => 'factory@example.com'
        ]);

        $article = Article::factory()->create([
            'title' => 'Factory Test Article',
            'article_status' => 1 // Pending
        ]);

        $category = ArticleCategory::factory()->create([
            'name' => 'Factory Test Category',
            'status' => 'Active'
        ]);

        // Assert
        $this->assertModelExists($member);
        $this->assertModelExists($article);
        $this->assertModelExists($category);

        // Verify factories create valid data
        $this->assertNotNull($member->email_address);
        $this->assertNotNull($article->title);
        $this->assertNotNull($category->name);
    }
}
