<?php

namespace Tests\Feature\Admin;

use App\Models\Subscription;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SubscriptionsControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Admin User
        $this->admin = User::factory()->create();
        $role = Role::factory()->create(['title' => 'Admin']);
        $this->admin->roles()->attach($role);

        // Permissions
        $permissions = [
            'subscription_access',
            'subscription_create',
            'subscription_edit',
            'subscription_show',
            'subscription_delete',
        ];

        foreach ($permissions as $perm) {
            $permission = Permission::factory()->create(['title' => $perm]);
            $role->permissions()->attach($permission);
        }
    }

    /** @test */
    public function admin_can_access_subscriptions_index()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.subscriptions.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.subscriptions.index');
        $response->assertViewHas('subscriptions');
    }

    /** @test */
    public function admin_can_create_subscription()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.subscriptions.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.subscriptions.create');
    }

    /** @test */
    public function admin_can_store_subscription()
    {
        $data = [
            'name' => 'Premium Plan',
            'description' => 'Best value',
            'features' => 'Feature 1, Feature 2',
            'plan_type' => 'recurring',
            'cycle_type' => 'monthly',
            'cycle_number' => '1',
            'status' => '1',
        ];

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.subscriptions.store'), $data);

        $response->assertRedirect(route('admin.subscriptions.index'));
        $this->assertDatabaseHas('subscriptions', [
            'name' => 'Premium Plan',
            'status' => '1',
        ]);
    }

    /** @test */
    public function admin_can_edit_subscription()
    {
        $subscription = Subscription::factory()->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.subscriptions.edit', $subscription));

        $response->assertStatus(200);
        $response->assertViewIs('admin.subscriptions.edit');
        $response->assertViewHas('subscription');
    }

    /** @test */
    public function admin_can_update_subscription()
    {
        $subscription = Subscription::factory()->create();

        $updateData = [
            'name' => 'Updated Plan',
            'description' => 'Updated desc',
            'features' => 'Updated features',
            'plan_type' => 'one-time',
            'cycle_type' => 'Yearly',
            'cycle_number' => '2',
            'status' => '2',
        ];

        $response = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.subscriptions.update', $subscription), $updateData);

        $response->assertRedirect(route('admin.subscriptions.index'));
        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscription->id,
            'name' => 'Updated Plan',
            'status' => '2',
        ]);
    }

    /** @test */
    public function admin_can_show_subscription()
    {
        $subscription = Subscription::factory()->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.subscriptions.show', $subscription));

        $response->assertStatus(200);
        $response->assertViewIs('admin.subscriptions.show');
        $response->assertViewHas('subscription');
    }

    /** @test */
    public function admin_can_delete_subscription()
    {
        $subscription = Subscription::factory()->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.subscriptions.destroy', $subscription));

        $response->assertRedirect();
        $this->assertSoftDeleted('subscriptions', ['id' => $subscription->id]);
    }

    /** @test */
    public function admin_can_mass_destroy_subscriptions()
    {
        $subscriptions = Subscription::factory()->count(3)->create();

        $ids = $subscriptions->pluck('id')->toArray();

        $response = $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.subscriptions.massDestroy'), [
                'ids' => $ids
            ]);

        $response->assertStatus(204);
        foreach ($subscriptions as $sub) {
            $this->assertSoftDeleted('subscriptions', ['id' => $sub->id]);
        }
    }
}
