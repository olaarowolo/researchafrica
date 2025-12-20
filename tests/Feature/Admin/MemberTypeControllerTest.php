<?php

namespace Tests\Feature\Admin;

use App\Models\MemberType;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MemberTypeControllerTest extends TestCase
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
            'member_type_access',
            'member_type_create',
            'member_type_edit',
            'member_type_show',
            'member_type_delete',
        ];

        foreach ($permissions as $perm) {
            $permission = Permission::factory()->create(['title' => $perm]);
            $role->permissions()->attach($permission);
        }
    }

    /** @test */
    public function admin_can_access_member_types_index()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.member-types.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.memberTypes.index');
        $response->assertViewHas('memberTypes');
    }

    /** @test */
    public function admin_can_create_member_type()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.member-types.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.memberTypes.create');
    }

    /** @test */
    public function admin_can_store_member_type()
    {
        $data = [
            'name' => 'Regular Member',
            'status' => '1',
        ];

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.member-types.store'), $data);

        $response->assertRedirect(route('admin.member-types.index'));
        $this->assertDatabaseHas('member_types', [
            'name' => 'Regular Member',
            'status' => '1',
        ]);
    }

    /** @test */
    public function admin_can_edit_member_type()
    {
        $memberType = MemberType::factory()->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.member-types.edit', $memberType));

        $response->assertStatus(200);
        $response->assertViewIs('admin.memberTypes.edit');
        $response->assertViewHas('memberType');
    }

    /** @test */
    public function admin_can_update_member_type()
    {
        $memberType = MemberType::factory()->create();

        $updateData = [
            'name' => 'Updated Member Type',
            'status' => '2',
        ];

        $response = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.member-types.update', $memberType), $updateData);

        $response->assertRedirect(route('admin.member-types.index'));
        $this->assertDatabaseHas('member_types', [
            'id' => $memberType->id,
            'name' => 'Updated Member Type',
            'status' => '2',
        ]);
    }

    /** @test */
    public function admin_can_show_member_type()
    {
        $memberType = MemberType::factory()->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.member-types.show', $memberType));

        $response->assertStatus(200);
        $response->assertViewIs('admin.memberTypes.show');
        $response->assertViewHas('memberType');
    }

    /** @test */
    public function admin_can_delete_member_type()
    {
        $memberType = MemberType::factory()->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.member-types.destroy', $memberType));

        $response->assertRedirect();
        $this->assertSoftDeleted('member_types', ['id' => $memberType->id]);
    }

    /** @test */
    public function admin_can_mass_destroy_member_types()
    {
        $memberTypes = MemberType::factory()->count(3)->create();

        $ids = $memberTypes->pluck('id')->toArray();

        $response = $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.member-types.massDestroy'), [
                'ids' => $ids
            ]);

        $response->assertStatus(204);
        foreach ($memberTypes as $type) {
            $this->assertSoftDeleted('member_types', ['id' => $type->id]);
        }
    }
}
