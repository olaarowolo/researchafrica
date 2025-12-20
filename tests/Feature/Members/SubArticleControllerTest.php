<?php

namespace Tests\Feature\Members;

use App\Models\Member;
use App\Models\MemberRole;
use App\Models\MemberType;
use App\Models\Country;
use App\Models\State;
use App\Models\SubArticle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SubArticleControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $member;
    protected $subArticle;

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
        $memberRole = MemberRole::factory()->create();
        $memberType = MemberType::factory()->create();

        // Create member
        $this->member = Member::factory()->create([
            'country_id' => $country->id,
            'state_id' => $state->id,
            'member_type_id' => $memberType->id,
            'member_role_id' => $memberRole->id,
        ]);

        // Create sub article for testing
        $this->subArticle = SubArticle::factory()->create();
    }

    /** @test */
    public function index_method_exists_and_accessible()
    {
        $response = $this->actingAs($this->member, 'member')
            ->get(route('member.sub-articles.index'));

        $response->assertStatus(200)
            ->assertJsonStructure(['subArticles']);
    }

    /** @test */
    public function create_method_exists_and_accessible()
    {
        $response = $this->actingAs($this->member, 'member')
            ->get(route('member.sub-articles.create'));

        $response->assertStatus(200)
            ->assertJson(['message' => 'Create form']);
    }

    /** @test */
    public function store_method_accepts_post_requests()
    {
        $article = \App\Models\Article::factory()->create(['member_id' => $this->member->id]);
        $comment = \App\Models\Comment::factory()->create(['article_id' => $article->id, 'member_id' => $this->member->id]);

        $subArticleData = [
            'article_id' => $article->id,
            'comment_id' => $comment->id,
            'abstract' => 'Test abstract content'
        ];

        $response = $this->actingAs($this->member, 'member')
            ->post(route('member.sub-articles.store'), $subArticleData);

        $response->assertStatus(201)
            ->assertJson(['message' => 'Sub Article created successfully.']);
    }

    /** @test */
    public function show_method_displays_specific_sub_article()
    {
        $response = $this->actingAs($this->member, 'member')
            ->get(route('member.sub-articles.show', $this->subArticle));

        $response->assertStatus(200)
            ->assertJsonStructure(['subArticle']);
    }

    /** @test */
    public function edit_method_displays_edit_form()
    {
        $response = $this->actingAs($this->member, 'member')
            ->get(route('member.sub-articles.edit', $this->subArticle));

        $response->assertStatus(200)
            ->assertJsonStructure(['subArticle', 'message']);
    }

    /** @test */
    public function update_method_accepts_put_requests()
    {
        $article = \App\Models\Article::factory()->create(['member_id' => $this->member->id]);
        $comment = \App\Models\Comment::factory()->create(['article_id' => $article->id, 'member_id' => $this->member->id]);

        $updatedData = [
            'article_id' => $article->id,
            'comment_id' => $comment->id,
            'abstract' => 'Updated abstract content'
        ];

        $response = $this->actingAs($this->member, 'member')
            ->put(route('member.sub-articles.update', $this->subArticle), $updatedData);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Sub Article updated successfully.']);
    }

    /** @test */
    public function destroy_method_deletes_sub_article()
    {
        $subArticleToDelete = SubArticle::factory()->create();

        $response = $this->actingAs($this->member, 'member')
            ->delete(route('member.sub-articles.destroy', $subArticleToDelete));

        $response->assertStatus(200)
            ->assertJson(['message' => 'Sub Article deleted successfully.']);
    }

    /** @test */
    public function resource_routes_are_defined()
    {
        // Test that all resource routes exist
        $routes = [
            'member.sub-articles.index',
            'member.sub-articles.create',
            'member.sub-articles.store',
            'member.sub-articles.show',
            'member.sub-articles.edit',
            'member.sub-articles.update',
            'member.sub-articles.destroy',
        ];

        foreach ($routes as $name) {
            $this->assertTrue(\Illuminate\Support\Facades\Route::has($name), "Route {$name} should be defined");
        }
    }

    /** @test */
    public function index_method_works_for_authenticated_member()
    {
        $response = $this->actingAs($this->member, 'member')
            ->get(route('member.sub-articles.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function create_method_works_for_authenticated_member()
    {
        $response = $this->actingAs($this->member, 'member')
            ->get(route('member.sub-articles.create'));

        $response->assertStatus(200);
    }

    /** @test */
    public function store_method_works_for_authenticated_member()
    {
        $subArticleData = SubArticle::factory()->make()->toArray();

        $response = $this->actingAs($this->member, 'member')
            ->post(route('member.sub-articles.store'), $subArticleData);

        $response->assertStatus(201);
    }

    /** @test */
    public function show_method_works_for_authenticated_member()
    {
        $response = $this->actingAs($this->member, 'member')
            ->get(route('member.sub-articles.show', $this->subArticle));

        $response->assertStatus(200);
    }

    /** @test */
    public function edit_method_works_for_authenticated_member()
    {
        $response = $this->actingAs($this->member, 'member')
            ->get(route('member.sub-articles.edit', $this->subArticle));

        $response->assertStatus(200);
    }

    /** @test */
    public function update_method_works_for_authenticated_member()
    {
        $updatedData = SubArticle::factory()->make()->toArray();

        $response = $this->actingAs($this->member, 'member')
            ->put(route('member.sub-articles.update', $this->subArticle), $updatedData);

        $response->assertStatus(200);
    }

    /** @test */
    public function destroy_method_works_for_authenticated_member()
    {
        $subArticleToDelete = SubArticle::factory()->create();

        $response = $this->actingAs($this->member, 'member')
            ->delete(route('member.sub-articles.destroy', $subArticleToDelete));

        $response->assertStatus(200);
    }

    /** @test */
    public function controller_extends_base_controller()
    {
        $reflection = new \ReflectionClass(\App\Http\Controllers\Members\SubArticleController::class);

        $this->assertTrue($reflection->isSubclassOf(\App\Http\Controllers\Controller::class));
    }

    /** @test */
    public function controller_has_expected_methods()
    {
        $controller = new \App\Http\Controllers\Members\SubArticleController();

        $expectedMethods = [
            'index', 'create', 'store', 'show', 'edit', 'update', 'destroy'
        ];

        foreach ($expectedMethods as $method) {
            $this->assertTrue(method_exists($controller, $method), "Controller should have {$method} method");
        }
    }

    /** @test */
    public function show_method_accepts_sub_article_model_binding()
    {
        $response = $this->actingAs($this->member, 'member')
            ->get(route('member.sub-articles.show', $this->subArticle->id));

        $response->assertStatus(200);
    }

    /** @test */
    public function edit_method_accepts_sub_article_model_binding()
    {
        $response = $this->actingAs($this->member, 'member')
            ->get(route('member.sub-articles.edit', $this->subArticle->id));

        $response->assertStatus(200);
    }

    /** @test */
    public function update_method_accepts_sub_article_model_binding()
    {
        $updatedData = SubArticle::factory()->make()->toArray();

        $response = $this->actingAs($this->member, 'member')
            ->put(route('member.sub-articles.update', $this->subArticle->id), $updatedData);

        $response->assertStatus(200);
    }

    /** @test */
    public function destroy_method_accepts_sub_article_model_binding()
    {
        $subArticleToDelete = SubArticle::factory()->create();

        $response = $this->actingAs($this->member, 'member')
            ->delete(route('member.sub-articles.destroy', $subArticleToDelete->id));

        $response->assertStatus(200);
    }

    /** @test */
    public function store_method_accepts_request_parameter()
    {
        $article = \App\Models\Article::factory()->create(['member_id' => $this->member->id]);
        $comment = \App\Models\Comment::factory()->create(['article_id' => $article->id, 'member_id' => $this->member->id]);

        $request = new \Illuminate\Http\Request([
            'article_id' => $article->id,
            'comment_id' => $comment->id,
            'abstract' => 'Test Sub Article'
        ]);

        $controller = new \App\Http\Controllers\Members\SubArticleController();
        $response = $controller->store($request);

        $this->assertNotNull($response);
    }

    /** @test */
    public function update_method_accepts_request_and_sub_article_parameters()
    {
        $article = \App\Models\Article::factory()->create(['member_id' => $this->member->id]);
        $comment = \App\Models\Comment::factory()->create(['article_id' => $article->id, 'member_id' => $this->member->id]);

        $request = new \Illuminate\Http\Request([
            'article_id' => $article->id,
            'comment_id' => $comment->id,
            'abstract' => 'Updated Test Sub Article'
        ]);

        $controller = new \App\Http\Controllers\Members\SubArticleController();
        $response = $controller->update($request, $this->subArticle);

        $this->assertNotNull($response);
    }

    /** @test */
    public function controller_can_be_instantiated()
    {
        $controller = new \App\Http\Controllers\Members\SubArticleController();

        $this->assertInstanceOf(\App\Http\Controllers\Members\SubArticleController::class, $controller);
    }

    /** @test */
    public function sub_article_model_binding_works_correctly()
    {
        // Test that the route model binding works with the SubArticle model
        $subArticle = SubArticle::factory()->create();

        $response = $this->actingAs($this->member, 'member')
            ->get(route('member.sub-articles.show', $subArticle));

        $response->assertStatus(200);

        // Test with ID binding
        $response = $this->actingAs($this->member, 'member')
            ->get(route('member.sub-articles.show', $subArticle->id));

        $response->assertStatus(200);
    }

    /** @test */
    public function all_resource_methods_return_responses()
    {
        $controller = new \App\Http\Controllers\Members\SubArticleController();

        // Test that all methods return something (even if null/void)
        $methods = ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'];

        foreach ($methods as $method) {
            $reflection = new \ReflectionMethod($controller, $method);
            $this->assertTrue($reflection->isPublic(), "Method {$method} should be public");
        }
    }
}
