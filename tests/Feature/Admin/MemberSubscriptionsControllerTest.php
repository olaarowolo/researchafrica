<?php

namespace Tests\Feature\Admin;

use App\Models\Member;
use App\Models\MemberSubscription;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MemberSubscriptionsControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $member;
    protected $subscription;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Admin User
        $this->admin = User::factory()->create();
        $role = Role::factory()->create(['title' => 'Admin']);
        $this->admin->roles()->attach($role);

        // Permissions
        $permissions = [
            'member_subscription_access',
            'member_subscription_create',
            'member_subscription_edit',
            'member_subscription_show',
            'member_subscription_delete',
        ];

        foreach ($permissions as $perm) {
            $permission = Permission::factory()->create(['title' => $perm]);
            $role->permissions()->attach($permission);
        }

        // Create Member
        $this->member = Member::factory()->create();

        // Create Subscription
        $this->subscription = Subscription::factory()->create();
    }

    /** @test */
    public function admin_can_access_member_subscriptions_index()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.member-subscriptions.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.memberSubscriptions.index');
        $response->assertViewHas('memberSubscriptions');
    }

    /** @test */
    public function admin_can_create_member_subscription()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.member-subscriptions.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.memberSubscriptions.create');
        $response->assertViewHas('member_emails');
        $response->assertViewHas('subscription_names');
    }

    /** @test */
    public function admin_can_store_member_subscription()
    {
        $subscriptionData = [
            'member_email_id' => $this->member->id,
            'subscription_name_id' => $this->subscription->id,
            'payment_method' => 'Manual',
            'status' => '1', // Active
            'amount' => '100.00',
            'expiry_date' => now()->addYear()->format('Y-m-d H:i:s'),
        ];

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.member-subscriptions.store'), $subscriptionData);

        $response->assertRedirect(route('admin.member-subscriptions.index'));
        $this->assertDatabaseHas('member_subscriptions', [
            'member_email_id' => $this->member->id,
            'subscription_name_id' => $this->subscription->id,
            'amount' => '100.00',
        ]);
    }

    /** @test */
    public function admin_can_edit_member_subscription()
    {
        $memberSubscription = MemberSubscription::factory()->create([
            'member_email_id' => $this->member->id,
            'subscription_name_id' => $this->subscription->id,
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.member-subscriptions.edit', $memberSubscription));

        $response->assertStatus(200);
        $response->assertViewIs('admin.memberSubscriptions.edit');
        $response->assertViewHas('memberSubscription');
    }

    /** @test */
    public function admin_can_update_member_subscription()
    {
        $memberSubscription = MemberSubscription::factory()->create([
            'member_email_id' => $this->member->id,
            'subscription_name_id' => $this->subscription->id,
            'status' => '1',
        ]);


$updateData = [
            'member_email_id' => $this->member->id,
            'subscription_name_id' => $this->subscription->id,
            'payment_method' => 'Automatic',
            'status' => '2', // Inactive
            'amount' => '150.00',
        ];

        $response = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.member-subscriptions.update', $memberSubscription), $updateData);

        $response->assertRedirect(route('admin.member-subscriptions.index'));
        $this->assertDatabaseHas('member_subscriptions', [
            'id' => $memberSubscription->id,
            'status' => '2',
            'amount' => '150.00',
        ]);
    }

    /** @test */
    public function admin_can_show_member_subscription()
    {
        $memberSubscription = MemberSubscription::factory()->create([
            'member_email_id' => $this->member->id,
            'subscription_name_id' => $this->subscription->id,
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.member-subscriptions.show', $memberSubscription));

        $response->assertStatus(200);
        $response->assertViewIs('admin.memberSubscriptions.show');
        $response->assertViewHas('memberSubscription');
    }

    /** @test */
    public function admin_can_delete_member_subscription()
    {
        $memberSubscription = MemberSubscription::factory()->create([
            'member_email_id' => $this->member->id,
            'subscription_name_id' => $this->subscription->id,
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.member-subscriptions.destroy', $memberSubscription));

        $response->assertRedirect();
        $this->assertSoftDeleted('member_subscriptions', ['id' => $memberSubscription->id]);
    }

    /** @test */
    public function admin_can_mass_destroy_member_subscriptions()
    {
        $subscriptions = MemberSubscription::factory()->count(3)->create([
            'member_email_id' => $this->member->id,
            'subscription_name_id' => $this->subscription->id,
        ]);

        $ids = $subscriptions->pluck('id')->toArray();

        $response = $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.member-subscriptions.massDestroy'), [
                'ids' => $ids
            ]);

        $response->assertStatus(204);
        foreach ($subscriptions as $sub) {
            $this->assertSoftDeleted('member_subscriptions', ['id' => $sub->id]);
        }
    }
}
