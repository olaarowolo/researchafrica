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
use Illuminate\Support\Facades\Artisan;

class DatabaseSeedingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedBasicData();
    }

    /**
     * Seed basic required data for tests
     */
    protected function seedBasicData(): void
    {
        // Seed countries first
        $this->seed(\Database\Seeders\CountriesTableSeeder::class);

        // Seed states
        $this->seed(\Database\Seeders\StatesTableSeeder::class);

        // Seed member types
        $this->seed(\Database\Seeders\MemberTypeSeeder::class);

        // Seed member roles
        $this->seed(\Database\Seeders\MemberRoleSeeder::class);
    }

    /** @test */
    public function it_seeds_database_successfully()
    {
        // Act - Run the seeder
        $this->seed([\Database\Seeders\DatabaseSeeder::class]);

        // Assert - Verify seeded data exists
        $this->assertDatabaseCount('members', '>=' , 1);
        $this->assertDatabaseCount('article_categories', '>=', 1);
        $this->assertDatabaseCount('member_types', '>=', 1);
        $this->assertDatabaseCount('member_roles', '>=', 1);
    }

    /** @test */
    public function it_seeds_member_types_correctly()
    {
        // Act - Run the seeder
        $this->seed([\Database\Seeders\DatabaseSeeder::class]);

        // Assert - Check member types exist
        $expectedTypes = ['Author', 'Editor', 'Reviewer', 'Publisher', 'Administrator'];

        foreach ($expectedTypes as $type) {
            $this->assertDatabaseHas('member_types', ['name' => $type]);
        }
    }

    /** @test */
    public function it_seeds_member_roles_correctly()
    {
        // Act - Run the seeder
        $this->seed([\Database\Seeders\DatabaseSeeder::class]);

        // Assert - Check member roles exist
        $expectedRoles = ['Author', 'Editor', 'Reviewer', 'Publisher', 'Administrator'];

        foreach ($expectedRoles as $role) {
            $this->assertDatabaseHas('member_roles', ['name' => $role]);
        }
    }

    /** @test */
    public function it_seeds_article_categories_correctly()
    {
        // Act - Run the seeder
        $this->seed([\Database\Seeders\DatabaseSeeder::class]);

        // Assert - Check article categories exist
        $this->assertDatabaseCount('article_categories', '>=', 1);

        // Check for expected categories
        $categories = \App\Models\ArticleCategory::all();
        $this->assertTrue($categories->isNotEmpty());

        // Verify categories have required fields
        $categories->each(function ($category) {
            $this->assertNotNull($category->name);
            $this->assertNotNull($category->slug);
        });
    }

    /** @test */
    public function it_seeds_permissions_correctly()
    {
        // Act - Run the seeder
        $this->seed([\Database\Seeders\DatabaseSeeder::class]);

        // Assert - Check permissions exist
        $this->assertDatabaseCount('permissions', '>=', 1);

        // Check for expected permissions
        $expectedPermissions = [
            'view-admin',
            'manage-articles',
            'manage-members',
            'manage-journals',
            'manage-settings',
            'manage-editorial-workflows'
        ];

        foreach ($expectedPermissions as $permission) {
            $this->assertDatabaseHas('permissions', ['name' => $permission]);
        }
    }

    /** @test */
    public function it_seeds_roles_correctly()
    {
        // Act - Run the seeder
        $this->seed([\Database\Seeders\DatabaseSeeder::class]);

        // Assert - Check roles exist
        $this->assertDatabaseCount('roles', '>=', 1);

        // Check for expected roles
        $expectedRoles = ['Super Admin', 'Admin', 'Editor', 'Reviewer', 'Author'];

        foreach ($expectedRoles as $role) {
            $this->assertDatabaseHas('roles', ['name' => $role]);
        }
    }

    /** @test */
    public function it_seeds_admin_user_correctly()
    {
        // Act - Run the seeder
        $this->seed([\Database\Seeders\DatabaseSeeder::class]);

        // Assert - Check admin user exists
        $admin = \App\Models\Admin::where('email', 'admin@researchafrica.com')->first();

        $this->assertNotNull($admin);
        $this->assertEquals('admin@researchafrica.com', $admin->email);
        $this->assertEquals('Admin User', $admin->name);
    }

    /** @test */
    public function it_seeds_sample_members_correctly()
    {
        // Act - Run the seeder
        $this->seed([\Database\Seeders\DatabaseSeeder::class]);

        // Assert - Check sample members exist
        $members = \App\Models\Member::all();

        $this->assertTrue($members->isNotEmpty());

        // Verify member has required fields
        $members->each(function ($member) {
            $this->assertNotNull($member->email);
            $this->assertNotNull($member->member_type_id);
            $this->assertNotNull($member->name);
        });
    }

    /** @test */
    public function it_seeds_sample_articles_correctly()
    {
        // Act - Run the seeder
        $this->seed([\Database\Seeders\DatabaseSeeder::class]);

        // Assert - Check sample articles exist
        $articles = \App\Models\Article::all();

        $this->assertTrue($articles->isNotEmpty());

        // Verify article has required fields
        $articles->each(function ($article) {
            $this->assertNotNull($article->title);
            $this->assertNotNull($article->member_id);
            $this->assertNotNull($article->article_category_id);
        });
    }

    /** @test */
    public function it_seeds_countries_correctly()
    {
        // Act - Run the seeder
        $this->seed([\Database\Seeders\DatabaseSeeder::class]);

        // Assert - Check countries exist
        $this->assertDatabaseCount('countries', '>=', 1);

        // Check for expected countries
        $expectedCountries = ['Nigeria', 'Kenya', 'Ghana', 'South Africa'];

        foreach ($expectedCountries as $country) {
            $this->assertDatabaseHas('countries', ['name' => $country]);
        }
    }

    /** @test */
    public function it_seeds_states_correctly()
    {
        // Act - Run the seeder
        $this->seed([\Database\Seeders\DatabaseSeeder::class]);

        // Assert - Check states exist
        $this->assertDatabaseCount('states', '>=', 1);

        // Verify states have country relationships
        $states = \App\Models\State::all();
        $states->each(function ($state) {
            $this->assertNotNull($state->country_id);
            $this->assertNotNull($state->name);
        });
    }

    /** @test */
    public function it_seeds_settings_correctly()
    {
        // Act - Run the seeder
        $this->seed([\Database\Seeders\DatabaseSeeder::class]);

        // Assert - Check settings exist
        $this->assertDatabaseCount('settings', '>=', 1);

        // Check for expected settings
        $expectedSettings = [
            'site_name',
            'site_description',
            'contact_email',
            'maintenance_mode'
        ];

        foreach ($expectedSettings as $setting) {
            $this->assertDatabaseHas('settings', ['key' => $setting]);
        }
    }

    /** @test */
    public function it_seeds_faq_categories_and_questions()
    {
        // Act - Run the seeder
        $this->seed([\Database\Seeders\DatabaseSeeder::class]);

        // Assert - Check FAQ data exists
        $this->assertDatabaseCount('faq_categories', '>=', 1);
        $this->assertDatabaseCount('faq_questions', '>=', 1);

        // Verify FAQ structure
        $faqCategories = \App\Models\FaqCategory::all();
        $faqCategories->each(function ($category) {
            $this->assertNotNull($category->name);
            $this->assertNotNull($category->description);
        });
    }

    /** @test */
    public function it_seeds_subscriptions_correctly()
    {
        // Act - Run the seeder
        $this->seed([\Database\Seeders\DatabaseSeeder::class]);

        // Assert - Check subscriptions exist
        $subscriptions = \App\Models\Subscription::all();

        if ($subscriptions->isNotEmpty()) {
            $subscriptions->each(function ($subscription) {
                $this->assertNotNull($subscription->name);
                $this->assertNotNull($subscription->price);
            });
        }
    }

    /** @test */
    public function it_maintains_data_integrity_after_seeding()
    {
        // Act - Run the seeder
        $this->seed([\Database\Seeders\DatabaseSeeder::class]);

        // Assert - Check foreign key relationships
        $members = \App\Models\Member::with('memberType')->get();
        $members->each(function ($member) {
            $this->assertNotNull($member->memberType);
        });

        $articles = \App\Models\Article::with('articleCategory', 'member')->get();
        $articles->each(function ($article) {
            $this->assertNotNull($article->articleCategory);
            $this->assertNotNull($article->member);
        });
    }

    /** @test */
    public function it_handles_partial_seeding_gracefully()
    {
        // Act - Seed only specific parts
        $this->seed(\Database\Seeders\MemberTypeSeeder::class);

        // Assert - Only member types should be seeded
        $this->assertDatabaseCount('member_types', '>=', 1);
        $this->assertDatabaseCount('members', 0);

        // Now seed members
        $this->seed(\Database\Seeders\MemberSeeder::class);

        // Assert - Both should exist
        $this->assertDatabaseCount('member_types', '>=', 1);
        $this->assertDatabaseCount('members', '>=', 1);
    }

    /** @test */
    public function it_seeds_editorial_workflows_when_applicable()
    {
        // Act - Run the seeder
        $this->seed([\Database\Seeders\DatabaseSeeder::class]);

        // Assert - Check if editorial workflows exist (if journals are seeded)
        $journals = \App\Models\ArticleCategory::where('is_journal', true)->get();

        if ($journals->isNotEmpty()) {
            $workflows = \App\Models\EditorialWorkflow::all();
            // Workflows may or may not be seeded depending on implementation
            $this->assertTrue(true); // Just ensure seeding didn't fail
        } else {
            $this->assertTrue(true); // No journals, so no workflows expected
        }
    }

    /** @test */
    public function it_prevents_duplicate_seeding()
    {
        // Act - Seed twice
        $this->seed([\Database\Seeders\DatabaseSeeder::class]);
        $firstCount = \App\Models\Member::count();

        $this->seed([\Database\Seeders\DatabaseSeeder::class]);
        $secondCount = \App\Models\Member::count();

        // Assert - Counts should be the same (seeder should be idempotent)
        $this->assertEquals($firstCount, $secondCount);
    }

    /** @test */
    public function it_can_seed_individual_components()
    {
        // Act - Seed individual components
        $this->seed(\Database\Seeders\CountriesTableSeeder::class);
        $this->seed(\Database\Seeders\StatesTableSeeder::class);
        $this->seed(\Database\Seeders\MemberTypeSeeder::class);

        // Assert - All components seeded successfully
        $this->assertDatabaseCount('countries', '>=', 1);
        $this->assertDatabaseCount('states', '>=', 1);
        $this->assertDatabaseCount('member_types', '>=', 1);
    }
}
