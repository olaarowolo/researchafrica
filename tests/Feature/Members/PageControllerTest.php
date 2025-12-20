<?php

namespace Tests\Feature\Members;

use App\Models\Member;
use App\Models\MemberRole;
use App\Models\MemberType;
use App\Models\Country;
use App\Models\State;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Bookmark;
use App\Models\PurchasedArticle;
use App\Models\ViewArticle;
use App\Models\FaqCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PageControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $member;
    protected $article;
    protected $category;
    protected $subCategory;

    protected function setUp(): void
    {
        parent::setUp();

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

        // Create categories
        $this->category = ArticleCategory::factory()->create([
            'parent_id' => null,
            'category_name' => 'Science'
        ]);

        $this->subCategory = ArticleCategory::factory()->create([
            'parent_id' => $this->category->id,
            'category_name' => 'Physics'
        ]);

        // Create published article
        $this->article = Article::factory()->create([
            'article_status' => 3,
            'article_category_id' => $this->category->id,
            'article_sub_category_id' => $this->subCategory->id,
            'title' => 'Test Article Title',
            'author_name' => 'Test Author'
        ]);
    }

    /** @test */
    public function home_page_displays_categories_and_articles()
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertViewIs('welcome');
        $response->assertViewHas('categories');
        $response->assertViewHas('articles');
        $response->assertSee($this->category->category_name);
        $response->assertSee($this->article->title);
    }

    /** @test */
    public function about_page_displays_correctly()
    {
        $response = $this->get(route('about'));

        $response->assertStatus(200);
        $response->assertViewIs('member.pages.about');
    }

    /** @test */
    public function faq_page_displays_faq_categories()
    {
        $faqCategory = FaqCategory::factory()->create();

        $response = $this->get(route('faq'));

        $response->assertStatus(200);
        $response->assertViewIs('member.pages.faq');
        $response->assertViewHas('faqCategories');
        $response->assertSee($faqCategory->name);
    }

    /** @test */
    public function contact_page_displays_correctly()
    {
        $response = $this->get(route('contact'));

        $response->assertStatus(200);
        $response->assertViewIs('member.pages.contact');
    }

    /** @test */
    public function search_page_displays_with_no_query()
    {
        $response = $this->get(route('member.search'));

        $response->assertStatus(302); // Redirects to home when no query
        $response->assertRedirect(route('home'));
    }

    /** @test */
    public function search_page_handles_category_search()
    {
        $response = $this->get(route('member.search'), [
            'category' => 'Science',
            'q' => 'Test'
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('search');
        $response->assertViewHas('categories');
        $response->assertViewHas('journals');
        $response->assertViewHas('count');
    }

    /** @test */
    public function search_page_handles_journal_search()
    {
        $response = $this->get(route('member.search'), [
            'type' => 'journal',
            'q' => 'Physics'
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('search');
        $response->assertViewHas('categories');
        $response->assertViewHas('journals');
        $response->assertViewHas('count');
    }

    /** @test */
    public function search_page_handles_article_search()
    {
        $response = $this->get(route('member.search'), [
            'q' => 'Test Article'
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('search');
        $response->assertViewHas('categories');
        $response->assertViewHas('articles');
        $response->assertViewHas('count');
        $response->assertSee($this->article->title);
    }

    /** @test */
    public function search_page_handles_empty_search_query()
    {
        $response = $this->get(route('member.search'), [
            'q' => ''
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('member.search'));
        $response->assertSessionHasErrors();
    }

    /** @test */
    public function search_page_handles_nonexistent_category()
    {
        $response = $this->get(route('member.search'), [
            'category' => 'NonExistentCategory'
        ]);

        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Category does not exist');
    }

    /** @test */
    public function advance_search_page_displays_without_search()
    {
        $response = $this->get(route('member.advance-search'));

        $response->assertStatus(200);
        $response->assertViewIs('advance_search');
        $response->assertViewHas('categories');
        $response->assertViewHas('articles');
        $response->assertViewHas('search', false);
    }

    /** @test */
    public function advance_search_page_performs_search_with_content()
    {
        $response = $this->get(route('member.advance-search'), [
            'content' => 'Test Article',
            'from_date' => '2020',
            'to_date' => '2024',
            'access' => '1'
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('advance_search');
        $response->assertViewHas('content');
        $response->assertViewHas('articles');
        $response->assertViewHas('count');
        $response->assertViewHas('search', true);
    }

    /** @test */
    public function advance_search_page_handles_empty_content()
    {
        $response = $this->get(route('member.advance-search'), [
            'content' => ''
        ]);

        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Content does not exist');
    }

    /** @test */
    public function view_article_displays_article_for_guest_user()
    {
        $response = $this->get(route('member.view-article', $this->article));

        $response->assertStatus(200);
        $response->assertViewIs('article');
        $response->assertViewHas('article');
        $response->assertViewHas('purchased', false);
        $response->assertViewHas('bookmark', false);
        $response->assertSee($this->article->title);
    }

    /** @test */
    public function view_article_displays_article_for_authenticated_member()
    {
        $response = $this->actingAs($this->member, 'member')
            ->get(route('member.view-article', $this->article));

        $response->assertStatus(200);
        $response->assertViewIs('article');
        $response->assertViewHas('article');
        $response->assertViewHas('purchased', false);
        $response->assertViewHas('bookmark', false);
        $response->assertSee($this->article->title);
    }

    /** @test */
    public function view_article_shows_purchased_status_for_purchased_article()
    {
        PurchasedArticle::factory()->create([
            'member_id' => $this->member->id,
            'article_id' => $this->article->id
        ]);

        $response = $this->actingAs($this->member, 'member')
            ->get(route('member.view-article', $this->article));

        $response->assertStatus(200);
        $response->assertViewHas('purchased', true);
    }

    /** @test */
    public function view_article_shows_bookmark_status_for_bookmarked_article()
    {
        Bookmark::factory()->create([
            'member_id' => $this->member->id,
            'article_id' => $this->article->id
        ]);

        $response = $this->actingAs($this->member, 'member')
            ->get(route('member.view-article', $this->article));

        $response->assertStatus(200);
        $response->assertViewHas('bookmark', true);
    }

    /** @test */
    public function view_article_creates_view_record_when_none_exists()
    {
        $this->assertDatabaseMissing('view_articles', [
            'article_id' => $this->article->id
        ]);

        $response = $this->get(route('member.view-article', $this->article));

        $response->assertStatus(200);
        $this->assertDatabaseHas('view_articles', [
            'article_id' => $this->article->id,
            'view' => 1
        ]);
    }

    /** @test */
    public function view_article_increments_view_count()
    {
        ViewArticle::factory()->create([
            'article_id' => $this->article->id,
            'view' => 5
        ]);

        $response = $this->get(route('member.view-article', $this->article));

        $response->assertStatus(200);
        $this->assertDatabaseHas('view_articles', [
            'article_id' => $this->article->id,
            'view' => 6
        ]);
    }

    /** @test */
    public function cat_sub_displays_articles_for_category_and_subcategory()
    {
        $response = $this->get(route('member.cat-sub', [
            $this->category->id,
            $this->subCategory->id,
            'physics-journal'
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('category_search');
        $response->assertViewHas('articles');
        $response->assertViewHas('categories');
        $response->assertViewHas('count');
        $response->assertViewHas('cat');
        $response->assertViewHas('sub_cat');
        $response->assertViewHas('sub');
    }

    /** @test */
    public function cat_sub_handles_sort_by_latest()
    {
        $response = $this->get(route('member.cat-sub', [
            $this->category->id,
            $this->subCategory->id,
            'physics-journal'
        ]), ['sort' => 'latest']);

        $response->assertStatus(200);
        $response->assertViewIs('category_search');
        $response->assertViewHas('articles');
    }

    /** @test */
    public function cat_sub_handles_sort_by_open_access()
    {
        $response = $this->get(route('member.cat-sub', [
            $this->category->id,
            $this->subCategory->id,
            'physics-journal'
        ]), ['sort' => 'open_access']);

        $response->assertStatus(200);
        $response->assertViewIs('category_search');
        $response->assertViewHas('articles');
    }

    /** @test */
    public function cat_sub_handles_sort_by_most_read()
    {
        $response = $this->get(route('member.cat-sub', [
            $this->category->id,
            $this->subCategory->id,
            'physics-journal'
        ]), ['sort' => 'most_read']);

        $response->assertStatus(200);
        $response->assertViewIs('category_search');
        $response->assertViewHas('articles');
    }

    /** @test */
    public function advance_search_submit_route_handles_post_request()
    {
        $response = $this->post(route('member.advance-search.submit'), [
            'content' => 'Test Article'
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('advance_search');
        $response->assertViewHas('articles');
    }

    /** @test */
    public function search_handles_author_name_search()
    {
        $response = $this->get(route('member.search'), [
            'q' => 'Test Author'
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('search');
        $response->assertSee($this->article->title);
    }

    /** @test */
    public function search_handles_other_authors_search()
    {
        $this->article->update(['other_authors' => 'John Doe, Jane Smith']);

        $response = $this->get(route('member.search'), [
            'q' => 'John Doe'
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('search');
        $response->assertSee($this->article->title);
    }

    /** @test */
    public function search_handles_corresponding_authors_search()
    {
        $this->article->update(['corresponding_authors' => 'Dr. Smith']);

        $response = $this->get(route('member.search'), [
            'q' => 'Dr. Smith'
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('search');
        $response->assertSee($this->article->title);
    }

    /** @test */
    public function advance_search_handles_date_filtering()
    {
        $response = $this->get(route('member.advance-search'), [
            'content' => 'Test',
            'from_date' => '2022',
            'to_date' => '2024'
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('advance_search');
        $response->assertViewHas('articles');
    }

    /** @test */
    public function advance_search_handles_category_filtering()
    {
        $response = $this->get(route('member.advance-search'), [
            'content' => 'Test',
            'with_category' => $this->category->id
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('advance_search');
        $response->assertViewHas('articles');
    }

    /** @test */
    public function advance_search_handles_access_type_filtering()
    {
        $response = $this->get(route('member.advance-search'), [
            'content' => 'Test',
            'access' => '1'
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('advance_search');
        $response->assertViewHas('articles');
    }

    /** @test */
    public function cat_sub_displays_zero_count_when_no_articles()
    {
        $this->article->delete();

        $response = $this->get(route('member.cat-sub', [
            $this->category->id,
            $this->subCategory->id,
            'physics-journal'
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('category_search');
        $response->assertViewHas('count', 0);
    }

    /** @test */
    public function search_shows_random_articles_when_available()
    {
        $response = $this->get(route('member.search'), [
            'q' => 'Test'
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('search');
        $response->assertViewHas('randomArticle');
        $response->assertViewHas('articles');
    }

    /** @test */
    public function advance_search_shows_category_counts()
    {
        $response = $this->get(route('member.advance-search'), [
            'content' => 'Test'
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('advance_search');
        $response->assertViewHas('categories');
    }
}
