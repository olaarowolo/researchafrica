<?php

namespace Tests\Unit\Models;

use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $role;

    protected function setUp(): void
    {
        parent::setUp();
        $this->role = Role::factory()->create();
    }

    /** @test */
    public function it_can_create_a_role()
    {
        $roleData = [
            'title' => 'Editor',
            'description' => 'Editor role for content management',
        ];

        $role = Role::create($roleData);

        $this->assertInstanceOf(Role::class, $role);
        $this->assertEquals('Editor', $role->title);
        $this->assertEquals('Editor role for content management', $role->description);
    }

    /** @test */
    public function it_has_correct_table_name()
    {
        $this->assertEquals('roles', $this->role->getTable());
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = ['title', 'description'];

        $this->assertEquals($fillable, $this->role->getFillable());
    }

    /** @test */
    public function it_has_many_users()
    {
        $role = Role::factory()->create();
        User::factory()->count(3)->create()->each(function ($user) use ($role) {
            $user->roles()->attach($role->id);
        });

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $role->users);
        $this->assertCount(3, $role->users);
        $this->assertInstanceOf(User::class, $role->users->first());
    }

    /** @test */
    public function it_has_many_permissions()
    {
        $role = Role::factory()->create();
        Permission::factory()->count(2)->create()->each(function ($permission) use ($role) {
            $role->permissions()->attach($permission->id);
        });

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $role->permissions);
        $this->assertCount(2, $role->permissions);
        $this->assertInstanceOf(Permission::class, $role->permissions->first());
    }

    /** @test */
    public function it_can_assign_permissions()
    {
        $role = Role::factory()->create();
        $permission1 = Permission::factory()->create(['title' => 'create_articles']);
        $permission2 = Permission::factory()->create(['title' => 'edit_articles']);

        $role->permissions()->attach([$permission1->id, $permission2->id]);

        $this->assertCount(2, $role->permissions);
        $this->assertTrue($role->permissions->contains($permission1));
        $this->assertTrue($role->permissions->contains($permission2));
    }

    /** @test */
    public function it_can_remove_permissions()
    {
        $role = Role::factory()->create();
        $permission = Permission::factory()->create();
        $role->permissions()->attach($permission->id);

        $this->assertCount(1, $role->permissions);

        $role->permissions()->detach($permission->id);

        $this->assertCount(0, $role->fresh()->permissions);
    }

    /** @test */
    public function it_can_sync_permissions()
    {
        $role = Role::factory()->create();
        $permission1 = Permission::factory()->create();
        $permission2 = Permission::factory()->create();

        // Initially attach one permission
        $role->permissions()->attach($permission1->id);
        $this->assertCount(1, $role->permissions);

        // Sync to include both permissions
        $role->permissions()->sync([$permission1->id, $permission2->id]);

        $this->assertCount(2, $role->fresh()->permissions);
    }

    /** @test */
    public function it_checks_if_role_has_permission()
    {
        $role = Role::factory()->create();
        $permission = Permission::factory()->create(['title' => 'manage_users']);

        $role->permissions()->attach($permission->id);

        $this->assertTrue($role->hasPermissionTo('manage_users'));
        $this->assertFalse($role->hasPermissionTo('non_existent_permission'));
    }

    /** @test */
    public function it_checks_if_role_has_any_permission()
    {
        $role = Role::factory()->create();
        $permission1 = Permission::factory()->create(['title' => 'create_articles']);
        $permission2 = Permission::factory()->create(['title' => 'edit_articles']);

        $role->permissions()->attach($permission1->id);

        $this->assertTrue($role->hasAnyPermission(['create_articles', 'edit_articles']));
        $this->assertFalse($role->hasAnyPermission(['delete_articles', 'publish_articles']));
    }

    /** @test */
    public function it_checks_if_role_has_all_permissions()
    {
        $role = Role::factory()->create();
        $permission1 = Permission::factory()->create(['title' => 'create_articles']);
        $permission2 = Permission::factory()->create(['title' => 'edit_articles']);

        $role->permissions()->attach([$permission1->id, $permission2->id]);

        $this->assertTrue($role->hasAllPermissions(['create_articles', 'edit_articles']));
        $this->assertFalse($role->hasAllPermissions(['create_articles', 'delete_articles']));
    }

    /** @test */
    public function it_can_give_permission_to_role()
    {
        $role = Role::factory()->create();
        $permission = Permission::factory()->create();

        $role->givePermissionTo($permission);

        $this->assertTrue($role->hasPermissionTo($permission));
    }

    /** @test */
    public function it_can_revoke_permission_from_role()
    {
        $role = Role::factory()->create();
        $permission = Permission::factory()->create();
        $role->permissions()->attach($permission->id);

        $this->assertTrue($role->hasPermissionTo($permission));

        $role->revokePermissionTo($permission);

        $this->assertFalse($role->hasPermissionTo($permission));
    }

    /** @test */
    public function it_validates_unique_title()
    {
        Role::factory()->create(['title' => 'Admin']);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Role::create([
            'title' => 'Admin',
            'description' => 'Another admin role'
        ]);
    }

    /** @test */
    public function it_can_have_null_description()
    {
        $role = Role::factory()->create(['description' => null]);

        $this->assertNull($role->description);
    }

    /** @test */
    public function it_orders_by_title()
    {
        $role1 = Role::factory()->create(['title' => 'Z Role']);
        $role2 = Role::factory()->create(['title' => 'A Role']);
        $role3 = Role::factory()->create(['title' => 'M Role']);

        $ordered = Role::orderBy('title')->get();

        $this->assertEquals($role2->id, $ordered->first()->id);
        $this->assertEquals($role3->id, $ordered->skip(1)->first()->id);
        $this->assertEquals($role1->id, $ordered->last()->id);
    }

    /** @test */
    public function it_searches_by_title()
    {
        $role1 = Role::factory()->create(['title' => 'Administrator']);
        $role2 = Role::factory()->create(['title' => 'Editor']);
        $role3 = Role::factory()->create(['title' => 'Moderator']);

        $results = Role::where('title', 'LIKE', '%Admin%')->get();

        $this->assertCount(1, $results);
        $this->assertTrue($results->contains($role1));
        $this->assertFalse($results->contains($role2));
        $this->assertFalse($results->contains($role3));
    }

    /** @test */
    public function it_counts_users_with_role()
    {
        $role = Role::factory()->create();
        User::factory()->count(5)->create()->each(function ($user) use ($role) {
            $user->roles()->attach($role->id);
        });

        $this->assertEquals(5, $role->users_count);
    }

    /** @test */
    public function it_counts_permissions_for_role()
    {
        $role = Role::factory()->create();
        Permission::factory()->count(3)->create()->each(function ($permission) use ($role) {
            $role->permissions()->attach($permission->id);
        });

        $this->assertEquals(3, $role->permissions_count);
    }

    /** @test */
    public function it_can_be_created_as_admin_role()
    {
        $adminRole = Role::factory()->admin()->create();

        $this->assertInstanceOf(Role::class, $adminRole);
        $this->assertEquals('Admin', $adminRole->title);
    }

    /** @test */
    public function it_can_be_created_as_editor_role()
    {
        $editorRole = Role::factory()->editor()->create();

        $this->assertInstanceOf(Role::class, $editorRole);
        $this->assertEquals('Editor', $editorRole->title);
    }

    /** @test */
    public function it_can_be_created_as_moderator_role()
    {
        $moderatorRole = Role::factory()->moderator()->create();

        $this->assertInstanceOf(Role::class, $moderatorRole);
        $this->assertEquals('Moderator', $moderatorRole->title);
    }

    /** @test */
    public function it_can_update_role()
    {
        $role = Role::factory()->create();

        $role->update([
            'title' => 'Updated Role',
            'description' => 'Updated description'
        ]);

        $this->assertEquals('Updated Role', $role->fresh()->title);
        $this->assertEquals('Updated description', $role->fresh()->description);
    }

    /** @test */
    public function it_can_delete_role()
    {
        $role = Role::factory()->create();
        $roleId = $role->id;

        $role->delete();

        $this->assertSoftDeleted($role);
        $this->assertNotNull(Role::withTrashed()->find($roleId));
    }

    /** @test */
    public function it_cascades_deletes_to_user_roles()
    {
        $role = Role::factory()->create();
        $user = User::factory()->create();
        $user->roles()->attach($role->id);

        $role->delete();

        // User should still exist, but relationship should be handled appropriately
        $this->assertSoftDeleted($role);
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    /** @test */
    public function it_cascades_deletes_to_role_permissions()
    {
        $role = Role::factory()->create();
        $permission = Permission::factory()->create();
        $role->permissions()->attach($permission->id);

        $role->delete();

        // Permission should still exist, but relationship should be handled
        $this->assertSoftDeleted($role);
        $this->assertDatabaseHas('permissions', ['id' => $permission->id]);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Role::create([]);
    }

    /** @test */
    public function it_can_be_restored()
    {
        $role = Role::factory()->create();
        $role->delete();

        $role->restore();

        $this->assertDatabaseHas('roles', ['id' => $role->id, 'deleted_at' => null]);
    }

    /** @test */
    public function it_can_be_force_deleted()
    {
        $role = Role::factory()->create();
        $roleId = $role->id;

        $role->forceDelete();

        $this->assertDatabaseMissing('roles', ['id' => $roleId]);
    }

    /** @test */
    public function it_has_slug_attribute()
    {
        $role = Role::factory()->create(['title' => 'Content Manager']);

        $this->assertEquals('content-manager', $role->slug);
    }

    /** @test */
    public function it_can_scope_to_active_roles()
    {
        $activeRole = Role::factory()->create();
        $inactiveRole = Role::factory()->create(['deleted_at' => now()]);

        $activeRoles = Role::whereNull('deleted_at')->get();

        $this->assertCount(1, $activeRoles);
        $this->assertTrue($activeRoles->contains($activeRole));
        $this->assertFalse($activeRoles->contains($inactiveRole));
    }
}

