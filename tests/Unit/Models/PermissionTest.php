<?php

namespace Tests\Unit\Models;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $permission;

    protected function setUp(): void
    {
        parent::setUp();
        $this->permission = Permission::factory()->create();
    }

    /** @test */
    public function it_can_create_a_permission()
    {
        $permissionData = [
            'title' => 'manage_users',
            'description' => 'Permission to manage users',
        ];

        $permission = Permission::create($permissionData);

        $this->assertInstanceOf(Permission::class, $permission);
        $this->assertEquals('manage_users', $permission->title);
        $this->assertEquals('Permission to manage users', $permission->description);
    }

    /** @test */
    public function it_has_correct_table_name()
    {
        $this->assertEquals('permissions', $this->permission->getTable());
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = ['title', 'description'];

        $this->assertEquals($fillable, $this->permission->getFillable());
    }

    /** @test */
    public function it_has_many_roles()
    {
        $permission = Permission::factory()->create();
        Role::factory()->count(3)->create()->each(function ($role) use ($permission) {
            $role->permissions()->attach($permission->id);
        });

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $permission->roles);
        $this->assertCount(3, $permission->roles);
        $this->assertInstanceOf(Role::class, $permission->roles->first());
    }

    /** @test */
    public function it_can_be_assigned_to_roles()
    {
        $permission = Permission::factory()->create();
        $role1 = Role::factory()->create();
        $role2 = Role::factory()->create();

        $permission->roles()->attach([$role1->id, $role2->id]);

        $this->assertCount(2, $permission->roles);
        $this->assertTrue($permission->roles->contains($role1));
        $this->assertTrue($permission->roles->contains($role2));
    }

    /** @test */
    public function it_can_be_removed_from_roles()
    {
        $permission = Permission::factory()->create();
        $role = Role::factory()->create();
        $permission->roles()->attach($role->id);

        $this->assertCount(1, $permission->roles);

        $permission->roles()->detach($role->id);

        $this->assertCount(0, $permission->fresh()->roles);
    }

    /** @test */
    public function it_can_sync_roles()
    {
        $permission = Permission::factory()->create();
        $role1 = Role::factory()->create();
        $role2 = Role::factory()->create();
        $role3 = Role::factory()->create();

        // Initially attach one role
        $permission->roles()->attach($role1->id);
        $this->assertCount(1, $permission->roles);

        // Sync to include multiple roles
        $permission->roles()->sync([$role2->id, $role3->id]);

        $this->assertCount(2, $permission->fresh()->roles);
        $this->assertFalse($permission->fresh()->roles->contains($role1));
        $this->assertTrue($permission->fresh()->roles->contains($role2));
        $this->assertTrue($permission->fresh()->roles->contains($role3));
    }

    /** @test */
    public function it_validates_unique_title()
    {
        Permission::factory()->create(['title' => 'create_articles']);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Permission::create([
            'title' => 'create_articles',
            'description' => 'Another permission with same title'
        ]);
    }

    /** @test */
    public function it_can_have_null_description()
    {
        $permission = Permission::factory()->create(['description' => null]);

        $this->assertNull($permission->description);
    }

    /** @test */
    public function it_orders_by_title()
    {
        $permission1 = Permission::factory()->create(['title' => 'z_permission']);
        $permission2 = Permission::factory()->create(['title' => 'a_permission']);
        $permission3 = Permission::factory()->create(['title' => 'm_permission']);

        $ordered = Permission::orderBy('title')->get();

        $this->assertEquals($permission2->id, $ordered->first()->id);
        $this->assertEquals($permission3->id, $ordered->skip(1)->first()->id);
        $this->assertEquals($permission1->id, $ordered->last()->id);
    }

    /** @test */
    public function it_searches_by_title()
    {
        $permission1 = Permission::factory()->create(['title' => 'create_articles']);
        $permission2 = Permission::factory()->create(['title' => 'edit_users']);
        $permission3 = Permission::factory()->create(['title' => 'delete_comments']);

        $results = Permission::where('title', 'LIKE', '%articles%')->get();

        $this->assertCount(1, $results);
        $this->assertTrue($results->contains($permission1));
        $this->assertFalse($results->contains($permission2));
        $this->assertFalse($results->contains($permission3));
    }

    /** @test */
    public function it_counts_roles_with_permission()
    {
        $permission = Permission::factory()->create();
        Role::factory()->count(4)->create()->each(function ($role) use ($permission) {
            $role->permissions()->attach($permission->id);
        });

        $this->assertEquals(4, $permission->roles_count);
    }

    /** @test */
    public function it_scopes_to_permissions_by_crud()
    {
        $createPermission = Permission::factory()->create(['title' => 'create_articles']);
        $readPermission = Permission::factory()->create(['title' => 'read_articles']);
        $updatePermission = Permission::factory()->create(['title' => 'update_articles']);
        $deletePermission = Permission::factory()->create(['title' => 'delete_articles']);

        $createPermissions = Permission::where('title', 'LIKE', 'create_%')->get();
        $readPermissions = Permission::where('title', 'LIKE', 'read_%')->get();
        $updatePermissions = Permission::where('title', 'LIKE', 'update_%')->get();
        $deletePermissions = Permission::where('title', 'LIKE', 'delete_%')->get();

        $this->assertCount(1, $createPermissions);
        $this->assertCount(1, $readPermissions);
        $this->assertCount(1, $updatePermissions);
        $this->assertCount(1, $deletePermissions);

        $this->assertTrue($createPermissions->contains($createPermission));
        $this->assertTrue($readPermissions->contains($readPermission));
        $this->assertTrue($updatePermissions->contains($updatePermission));
        $this->assertTrue($deletePermissions->contains($deletePermission));
    }

    /** @test */
    public function it_scopes_to_admin_permissions()
    {
        $adminPermission = Permission::factory()->create(['title' => 'admin_users']);
        $regularPermission = Permission::factory()->create(['title' => 'edit_own_articles']);

        $adminPermissions = Permission::where('title', 'LIKE', 'admin_%')->get();

        $this->assertCount(1, $adminPermissions);
        $this->assertTrue($adminPermissions->contains($adminPermission));
        $this->assertFalse($adminPermissions->contains($regularPermission));
    }

    /** @test */
    public function it_can_be_created_as_crud_permission()
    {
        $createPermission = Permission::factory()->create(['title' => 'create_articles']);

        $this->assertInstanceOf(Permission::class, $createPermission);
        $this->assertEquals('create_articles', $createPermission->title);
    }

    /** @test */
    public function it_can_be_created_as_admin_permission()
    {
        $adminPermission = Permission::factory()->admin()->create();

        $this->assertInstanceOf(Permission::class, $adminPermission);
        $this->assertStringContainsString('admin', $adminPermission->title);
    }

    /** @test */
    public function it_can_update_permission()
    {
        $permission = Permission::factory()->create();

        $permission->update([
            'title' => 'updated_permission',
            'description' => 'Updated description'
        ]);

        $this->assertEquals('updated_permission', $permission->fresh()->title);
        $this->assertEquals('Updated description', $permission->fresh()->description);
    }

    /** @test */
    public function it_can_delete_permission()
    {
        $permission = Permission::factory()->create();
        $permissionId = $permission->id;

        $permission->delete();

        $this->assertSoftDeleted($permission);
        $this->assertNotNull(Permission::withTrashed()->find($permissionId));
    }

    /** @test */
    public function it_cascades_deletes_to_role_permissions()
    {
        $permission = Permission::factory()->create();
        $role = Role::factory()->create();
        $permission->roles()->attach($role->id);

        $permission->delete();

        // Role should still exist, but relationship should be handled
        $this->assertSoftDeleted($permission);
        $this->assertDatabaseHas('roles', ['id' => $role->id]);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Permission::create([]);
    }

    /** @test */
    public function it_can_be_restored()
    {
        $permission = Permission::factory()->create();
        $permission->delete();

        $permission->restore();

        $this->assertDatabaseHas('permissions', ['id' => $permission->id, 'deleted_at' => null]);
    }

    /** @test */
    public function it_can_be_force_deleted()
    {
        $permission = Permission::factory()->create();
        $permissionId = $permission->id;

        $permission->forceDelete();

        $this->assertDatabaseMissing('permissions', ['id' => $permissionId]);
    }

    /** @test */
    public function it_has_slug_attribute()
    {
        $permission = Permission::factory()->create(['title' => 'Create Articles']);

        $this->assertEquals('create-articles', $permission->slug);
    }

    /** @test */
    public function it_can_scope_to_active_permissions()
    {
        $activePermission = Permission::factory()->create();
        $inactivePermission = Permission::factory()->create(['deleted_at' => now()]);

        $activePermissions = Permission::whereNull('deleted_at')->get();

        $this->assertCount(1, $activePermissions);
        $this->assertTrue($activePermissions->contains($activePermission));
        $this->assertFalse($activePermissions->contains($inactivePermission));
    }

    /** @test */
    public function it_checks_if_permission_is_assigned_to_role()
    {
        $permission = Permission::factory()->create();
        $assignedRole = Role::factory()->create();
        $unassignedRole = Role::factory()->create();

        $permission->roles()->attach($assignedRole->id);

        $this->assertTrue($permission->isAssignedToRole($assignedRole));
        $this->assertFalse($permission->isAssignedToRole($unassignedRole));
    }

    /** @test */
    public function it_gets_assigned_roles_count()
    {
        $permission = Permission::factory()->create();
        Role::factory()->count(3)->create()->each(function ($role) use ($permission) {
            $role->permissions()->attach($permission->id);
        });

        $this->assertEquals(3, $permission->getAssignedRolesCount());
    }

    /** @test */
    public function it_checks_permission_category()
    {
        $articlePermission = Permission::factory()->create(['title' => 'create_articles']);
        $userPermission = Permission::factory()->create(['title' => 'manage_users']);
        $commentPermission = Permission::factory()->create(['title' => 'moderate_comments']);

        $this->assertEquals('article', $articlePermission->category);
        $this->assertEquals('user', $userPermission->category);
        $this->assertEquals('comment', $commentPermission->category);
    }

    /** @test */
    public function it_groups_permissions_by_category()
    {
        $articlePermissions = Permission::factory()->count(2)->create([
            'title' => 'create_articles'
        ]);
        $userPermissions = Permission::factory()->count(2)->create([
            'title' => 'manage_users'
        ]);

        $grouped = Permission::all()->groupBy('category');

        $this->assertTrue($grouped->has('article'));
        $this->assertTrue($grouped->has('user'));
        $this->assertCount(2, $grouped['article']);
        $this->assertCount(2, $grouped['user']);
    }

    /** @test */
    public function it_validates_permission_title_format()
    {
        $validPermission = Permission::factory()->create(['title' => 'create_articles']);
        $this->assertInstanceOf(Permission::class, $validPermission);

        // Test underscore format
        $underscorePermission = Permission::factory()->create(['title' => 'manage_user_permissions']);
        $this->assertInstanceOf(Permission::class, $underscorePermission);
    }

    /** @test */
    public function it_handles_special_characters_in_title()
    {
        $permission = Permission::factory()->create(['title' => 'special_permission_123']);

        $this->assertEquals('special_permission_123', $permission->title);
        $this->assertNotNull($permission->slug);
    }

    /** @test */
    public function it_can_get_permission_usage_statistics()
    {
        $permission = Permission::factory()->create();
        Role::factory()->count(2)->create()->each(function ($role) use ($permission) {
            $role->permissions()->attach($permission->id);
        });

        $stats = $permission->getUsageStats();

        $this->assertIsArray($stats);
        $this->assertArrayHasKey('roles_count', $stats);
        $this->assertEquals(2, $stats['roles_count']);
        $this->assertArrayHasKey('is_widely_used', $stats);
        $this->assertIsBool($stats['is_widely_used']);
    }
}

