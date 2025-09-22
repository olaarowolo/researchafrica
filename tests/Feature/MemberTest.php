<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Member;
use App\Models\Subscription;
use App\Models\MemberSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $member;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->member = Member::factory()->create();
    }

    /**
     * Test admin can view members.
     */
    public function test_admin_can_view_members()
    {
        Member::factory()->count(5)->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.members.index'));

        $response->assertStatus(200);
        $response->assertViewHas('members');
    }

    /**
     * Test admin can create member.
     */
    public function test_admin_can_create_member()
    {
        $memberData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@test.com',
            'password' => 'password123',
            'phone' => '1234567890',
            'country_id' => 1,
            'state_id' => 1,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.members.store'), $memberData);

        $response->assertRedirect();
        $this->assertDatabaseHas('members', [
            'email' => 'john.doe@test.com',
        ]);
    }

    /**
     * Test admin can edit member.
     */
    public function test_admin_can_edit_member()
    {
        $member = Member::factory()->create();

        $updatedData = [
            'first_name' => 'Updated John',
            'last_name' => 'Updated Doe',
            'email' => 'updated.john@test.com',
            'phone' => '0987654321',
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('admin.members.update', $member->id), $updatedData);

        $response->assertRedirect();
        $this->assertDatabaseHas('members', [
            'first_name' => 'Updated John',
        ]);
    }

    /**
     * Test admin can delete member.
     */
    public function test_admin_can_delete_member()
    {
        $member = Member::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.members.destroy', $member->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('members', [
            'id' => $member->id,
        ]);
    }

    /**
     * Test member can view profile.
     */
    public function test_member_can_view_profile()
    {
        $response = $this->actingAs($this->member, 'member')
            ->get(route('member.profile'));

        $response->assertStatus(200);
        $response->assertViewHas('member');
    }

    /**
     * Test member can update profile.
     */
    public function test_member_can_update_profile()
    {
        $updatedData = [
            'first_name' => 'Updated First',
            'last_name' => 'Updated Last',
            'phone' => '1112223333',
        ];

        $response = $this->actingAs($this->member, 'member')
            ->post(route('member.profile.update'), $updatedData);

        $response->assertRedirect();
        $this->assertDatabaseHas('members', [
            'first_name' => 'Updated First',
        ]);
    }

    /**
     * Test member can change password.
     */
    public function test_member_can_change_password()
    {
        $passwordData = [
            'current_password' => 'password',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ];

        $response = $this->actingAs($this->member, 'member')
            ->post(route('member.password'), $passwordData);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    /**
     * Test member can view subscriptions.
     */
    public function test_member_can_view_subscriptions()
    {
        Subscription::factory()->count(3)->create();

        $response = $this->actingAs($this->member, 'member')
            ->get(route('member.subscriptions.index'));

        $response->assertStatus(200);
        $response->assertViewHas('subscriptions');
    }

    /**
     * Test member can subscribe to plan.
     */
    public function test_member_can_subscribe_to_plan()
    {
        $subscription = Subscription::factory()->create();

        $subscriptionData = [
            'subscription_id' => $subscription->id,
            'payment_method' => 'card',
        ];

        $response = $this->actingAs($this->member, 'member')
            ->post(route('member.subscriptions.store'), $subscriptionData);

        $response->assertRedirect();
        $this->assertDatabaseHas('member_subscriptions', [
            'member_id' => $this->member->id,
            'subscription_id' => $subscription->id,
        ]);
    }

    /**
     * Test member can view purchased articles.
     */
    public function test_member_can_view_purchased_articles()
    {
        $response = $this->actingAs($this->member, 'member')
            ->get(route('member.purchased-article'));

        $response->assertStatus(200);
        $response->assertViewHas('purchasedArticles');
    }

    /**
     * Test member can view bookmarks.
     */
    public function test_member_can_view_bookmarks()
    {
        $response = $this->actingAs($this->member, 'member')
            ->get(route('member.view-bookmark'));

        $response->assertStatus(200);
        $response->assertViewHas('bookmarks');
    }

    /**
     * Test member can become author.
     */
    public function test_member_can_become_author()
    {
        $response = $this->actingAs($this->member, 'member')
            ->post(route('member.become-author'));

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    /**
     * Test admin can manage member types.
     */
    public function test_admin_can_manage_member_types()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.member-types.index'));

        $response->assertStatus(200);
        $response->assertViewHas('memberTypes');
    }

    /**
     * Test admin can create member type.
     */
    public function test_admin_can_create_member_type()
    {
        $memberTypeData = [
            'name' => 'Premium Member',
            'description' => 'Premium membership type',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.member-types.store'), $memberTypeData);

        $response->assertRedirect();
        $this->assertDatabaseHas('member_types', [
            'name' => 'Premium Member',
        ]);
    }

    /**
     * Test admin can manage member roles.
     */
    public function test_admin_can_manage_member_roles()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.member-roles.index'));

        $response->assertStatus(200);
        $response->assertViewHas('memberRoles');
    }

    /**
     * Test admin can manage member subscriptions.
     */
    public function test_admin_can_manage_member_subscriptions()
    {
        MemberSubscription::factory()->count(5)->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.member-subscriptions.index'));

        $response->assertStatus(200);
        $response->assertViewHas('memberSubscriptions');
    }
}
