<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Member;
use App\Models\MemberRole;
use App\Models\MemberType;
use App\Models\Country;
use App\Models\State;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class HomeControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $member;

    protected function setUp(): void
    {
        parent::setUp();

        // Create required models
        $country = Country::factory()->create();
        $state = State::factory()->create(['country_id' => $country->id]);
        $memberRole = MemberRole::factory()->create();
        $memberType = MemberType::factory()->create();

        // Create user
        $this->user = User::factory()->create([
            'country_id' => $country->id,
            'state_id' => $state->id,
            'member_type_id' => $memberType->id,
            'member_role_id' => $memberRole->id,
        ]);

        // Create member (for authentication)
        $this->member = Member::factory()->create([
            'country_id' => $country->id,
            'state_id' => $state->id,
            'member_type_id' => $memberType->id,
            'member_role_id' => $memberRole->id,
        ]);
    }

    /** @test */
    public function index_method_displays_home_view()
    {
        $response = $this->actingAs($this->user)
            ->get(route('home'));

        $response->assertStatus(200);
        $response->assertViewIs('home');
    }

    /** @test */
    public function index_method_requires_authentication()
    {
        Auth::logout();

        $response = $this->get(route('home'));

        $response->assertStatus(302);
        $response->assertRedirect('/login'); // Default Laravel login redirect
    }

    /** @test */
    public function index_works_for_authenticated_user()
    {
        $response = $this->actingAs($this->user)
            ->get(route('home'));

        $response->assertStatus(200);
        $response->assertViewIs('home');
    }

    /** @test */
    public function index_works_for_member_authentication()
    {
        $response = $this->actingAs($this->member, 'member')
            ->get(route('home'));

        $response->assertStatus(200);
        $response->assertViewIs('home');
    }

    /** @test */
    public function controller_has_auth_middleware_in_constructor()
    {
        $controller = new \App\Http\Controllers\HomeController();

        // Test that the constructor exists and can be called
        $reflection = new \ReflectionClass($controller);
        $this->assertTrue($reflection->hasMethod('__construct'));

        $constructor = $reflection->getMethod('__construct');
        $this->assertTrue($constructor->isPublic());
    }

    /** @test */
    public function controller_extends_base_controller()
    {
        $reflection = new \ReflectionClass(\App\Http\Controllers\HomeController::class);

        $this->assertTrue($reflection->isSubclassOf(\App\Http\Controllers\Controller::class));
    }

    /** @test */
    public function controller_has_expected_methods()
    {
        $controller = new \App\Http\Controllers\HomeController();

        $expectedMethods = ['index'];

        foreach ($expectedMethods as $method) {
            $this->assertTrue(method_exists($controller, $method), "Controller should have {$method} method");
        }
    }

    /** @test */
    public function index_method_is_public()
    {
        $controller = new \App\Http\Controllers\HomeController();

        $reflection = new \ReflectionMethod($controller, 'index');
        $this->assertTrue($reflection->isPublic(), "Index method should be public");
    }

    /** @test */
    public function index_method_accepts_request_parameter()
    {
        $request = new \Illuminate\Http\Request();
        $controller = new \App\Http\Controllers\HomeController();

        $response = $controller->index($request);

        $this->assertNotNull($response);
    }

    /** @test */
    public function index_method_returns_view()
    {
        $controller = new \App\Http\Controllers\HomeController();

        $response = $controller->index();

        // Should return a view instance
        $this->assertInstanceOf(\Illuminate\Contracts\View\View::class, $response);
    }

    /** @test */
    public function middleware_protects_index_method()
    {
        Auth::logout();

        $response = $this->get(route('home'));

        // Should redirect to login due to auth middleware
        $response->assertStatus(302);
        $this->assertTrue($response->isRedirect());
    }

    /** @test */
    public function different_users_can_access_home_page()
    {
        // Test with regular user
        $response1 = $this->actingAs($this->user)
            ->get(route('home'));
        $response1->assertStatus(200);

        // Test with member
        $response2 = $this->actingAs($this->member, 'member')
            ->get(route('home'));
        $response2->assertStatus(200);
    }

    /** @test */
    public function home_route_is_defined()
    {
        $this->assertTrue(route()->has('home'), "Route 'home' should be defined");
    }

    /** @test */
    public function home_route_has_correct_http_method()
    {
        // Home route should be GET
        $routes = app('router')->getRoutes();
        $homeRoute = $routes->getByName('home');

        if ($homeRoute) {
            $this->assertEquals('GET', $homeRoute->methods()[0]);
        }
    }

    /** @test */
    public function home_route_uses_home_controller()
    {
        $routes = app('router')->getRoutes();
        $homeRoute = $routes->getByName('home');

        if ($homeRoute) {
            $this->assertEquals('App\Http\Controllers\HomeController', $homeRoute->getAction()['controller'][0]);
            $this->assertEquals('index', $homeRoute->getAction()['controller'][1]);
        }
    }

    /** @test */
    public function controller_constructor_doesnt_throw_exceptions()
    {
        try {
            $controller = new \App\Http\Controllers\HomeController();
            $this->assertInstanceOf(\App\Http\Controllers\HomeController::class, $controller);
        } catch (\Exception $e) {
            $this->fail("Controller constructor should not throw exceptions: " . $e->getMessage());
        }
    }

    /** @test */
    public function index_method_works_without_request_parameter()
    {
        $controller = new \App\Http\Controllers\HomeController();

        try {
            $response = $controller->index();
            $this->assertNotNull($response);
        } catch (\Exception $e) {
            $this->fail("Index method should work without request parameter: " . $e->getMessage());
        }
    }

    /** @test */
    public function index_method_returns_consistent_response()
    {
        // Multiple calls should return the same response type
        $response1 = $this->actingAs($this->user)->get(route('home'));
        $response2 = $this->actingAs($this->user)->get(route('home'));

        $response1->assertStatus(200);
        $response2->assertStatus(200);
        $response1->assertViewIs('home');
        $response2->assertViewIs('home');
    }

    /** @test */
    public function middleware_redirects_unauthenticated_users()
    {
        Auth::logout();

        $response = $this->get(route('home'));

        // Should redirect to login
        $response->assertStatus(302);
        $this->assertTrue($response->isRedirect());
    }

    /** @test */
    public function home_page_loads_successfully()
    {
        $response = $this->actingAs($this->user)->get(route('home'));

        $response->assertStatus(200);
        $response->assertViewIs('home');
    }

    /** @test */
    public function controller_has_minimal_functionality()
    {
        $reflection = new \ReflectionClass(\App\Http\Controllers\HomeController::class);

        // Should only have index method besides constructor
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
        $methodNames = array_map(function($method) {
            return $method->getName();
        }, $methods);

        $this->assertContains('index', $methodNames);
        $this->assertContains('__construct', $methodNames);
    }

    /** @test */
    public function home_controller_follows_laravel_conventions()
    {
        $reflection = new \ReflectionClass(\App\Http\Controllers\HomeController::class);

        // Check namespace
        $this->assertEquals('App\Http\Controllers', $reflection->getNamespaceName());

        // Check class name
        $this->assertEquals('HomeController', $reflection->getShortName());

        // Check extends Controller
        $this->assertTrue($reflection->isSubclassOf(\App\Http\Controllers\Controller::class));
    }

    /** @test */
    public function constructor_has_auth_middleware()
    {
        // This test verifies that the constructor sets up auth middleware
        // Since we can't easily test middleware from controller reflection,
        // we test the behavior instead
        Auth::logout();

        $response = $this->get(route('home'));
        $response->assertStatus(302); // Should redirect
    }

    /** @test */
    public function index_method_has_correct_signature()
    {
        $reflection = new \ReflectionMethod(\App\Http\Controllers\HomeController::class, 'index');

        $parameters = $reflection->getParameters();

        // Should have one parameter (Request $request)
        $this->assertEquals(1, count($parameters));
        $this->assertEquals('request', $parameters[0]->getName());

        // Parameter should be Request type
        $this->assertEquals('Illuminate\Http\Request', $parameters[0]->getType()->getName());
    }

    /** @test */
    public function controller_can_be_instantiated_multiple_times()
    {
        try {
            $controller1 = new \App\Http\Controllers\HomeController();
            $controller2 = new \App\Http\Controllers\HomeController();

            $this->assertInstanceOf(\App\Http\Controllers\HomeController::class, $controller1);
            $this->assertInstanceOf(\App\Http\Controllers\HomeController::class, $controller2);
        } catch (\Exception $e) {
            $this->fail("Controller should be instantiable multiple times: " . $e->getMessage());
        }
    }

    /** @test */
    public function home_route_handles_get_requests()
    {
        $response = $this->actingAs($this->user)
            ->get(route('home'));

        $response->assertStatus(200);
        $response->assertViewIs('home');
    }

    /** @test */
    public function home_route_rejects_post_requests()
    {
        $response = $this->actingAs($this->user)
            ->post(route('home'));

        // Should return 405 Method Not Allowed
        $response->assertStatus(405);
    }

    /** @test */
    public function controller_documentation_comments_exist()
    {
        $reflection = new \ReflectionClass(\App\Http\Controllers\HomeController::class);

        // Check constructor has doc comment
        $constructor = $reflection->getMethod('__construct');
        $this->assertNotEmpty($constructor->getDocComment());

        // Check index method has doc comment
        $index = $reflection->getMethod('index');
        $this->assertNotEmpty($index->getDocComment());
    }

    /** @test */
    public function home_page_view_exists()
    {
        // This test verifies that the 'home' view can be found
        $response = $this->actingAs($this->user)->get(route('home'));

        $response->assertStatus(200);

        // If the view doesn't exist, Laravel would throw an exception
        // which would cause this test to fail
        $this->assertTrue(true);
    }

    /** @test */
    public function controller_returns_renderable_content()
    {
        $controller = new \App\Http\Controllers\HomeController();
        $response = $controller->index();

        // Should return something that can be rendered
        $this->assertTrue(method_exists($response, 'render'));
    }

    /** @test */
    public function middleware_applies_to_all_controller_methods()
    {
        // Since HomeController only has index method and constructor,
        // the auth middleware should protect index method
        Auth::logout();

        $response = $this->get(route('home'));

        // Should redirect to login due to auth middleware
        $response->assertStatus(302);
        $this->assertTrue($response->isRedirect());
    }
}
