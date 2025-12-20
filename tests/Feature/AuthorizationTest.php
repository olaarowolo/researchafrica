<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_cannot_access_admin_dashboard()
    {
        $response = $this->get(route('admin.home'));

        $response->assertStatus(302);
        $response->assertRedirect(route('admin.login'));
    }

    /** @test */
    public function members_cannot_access_admin_dashboard()
    {
        $this->withoutExceptionHandling();

        $member = Member::factory()->create();
        
        $response = $this->actingAs($member, 'member')
            ->get(route('admin.home'));

        $response->assertStatus(302);
        $response->assertRedirect(route('admin.login'));
    }

    /** @test */
    public function admins_can_access_admin_dashboard()
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.home'));

        $response->assertStatus(200);
    }
}
