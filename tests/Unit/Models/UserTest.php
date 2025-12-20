<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class UserTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_can_create_a_user()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
        ];

        $user = User::create($userData);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = [
            'name',
            'email',
            'email_verified_at',
            'password',
            'remember_token',
            'created_at',
            'updated_at',
            'deleted_at',
        ];

        $this->assertEquals($fillable, $this->user->getFillable());
    }

    /** @test */
    public function it_has_hidden_attributes()
    {
        $hidden = ['remember_token', 'password'];

        $this->assertEquals($hidden, $this->user->getHidden());
    }

    /** @test */
    public function it_has_correct_date_format()
    {
        $now = Carbon::now();
        $user = User::factory()->create(['created_at' => $now]);

        $this->assertEquals($now->format('Y-m-d H:i:s'), $user->created_at->format('Y-m-d H:i:s'));
    }

    /** @test */
    public function it_can_check_if_user_is_admin()
    {
        // Create admin role
        $adminRole = Role::create(['title' => 'Admin']);

        // User without admin role
        $regularUser = User::factory()->create();
        $this->assertFalse($regularUser->is_admin);

        // User with admin role
        $adminUser = User::factory()->create();
        $adminUser->roles()->attach($adminRole->id);
        $this->assertTrue($adminUser->fresh()->is_admin);
    }

    /** @test */
    public function it_formats_email_verified_at_correctly()
    {
        $verifiedUser = User::factory()->create([
            'email_verified_at' => '2024-01-01 10:30:00'
        ]);

        // The accessor should format the date
        $formatted = $verifiedUser->email_verified_at;
        $this->assertNotNull($formatted);
    }

    /** @test */
    public function it_handles_email_verified_at_setter()
    {
        $user = User::factory()->create();
        $formattedDate = '2024-01-01 10:30:00';

        $user->setEmailVerifiedAtAttribute($formattedDate);

        $this->assertNotNull($user->email_verified_at);
        $this->assertIsString($user->email_verified_at);
    }

    /** @test */
    public function it_hashes_password_when_setting()
    {
        $plainPassword = 'password123';
        $user = User::factory()->create();

        $user->setPasswordAttribute($plainPassword);

        $this->assertTrue(Hash::check($plainPassword, $user->password));
        $this->assertNotEquals($plainPassword, $user->password);
    }

    /** @test */
    public function it_sends_password_reset_notification()
    {
        $user = User::factory()->create();
        $token = 'test-token';

        // This should not throw an exception
        $this->expectNotToPerformAssertions();
        $user->sendPasswordResetNotification($token);
    }


    /** @test */
    public function it_has_many_roles_relationship()
    {
        $role = Role::factory()->create();
        $user = User::factory()->create();
        $user->roles()->attach($role->id);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $user->roles);
        $this->assertCount(1, $user->roles);
        $this->assertInstanceOf(Role::class, $user->roles->first());
    }

    /** @test */
    public function it_can_have_multiple_roles()
    {
        $roles = Role::factory()->count(3)->create();
        $user = User::factory()->create();
        $user->roles()->attach($roles->pluck('id')->toArray());

        $this->assertCount(3, $user->roles);
    }

    /** @test */
    public function it_uses_soft_deletes()
    {
        $user = User::factory()->create();
        $userId = $user->id;

        $user->delete();

        $this->assertSoftDeleted($user);
        $this->assertNotNull(User::withTrashed()->find($userId));
        $this->assertNull(User::find($userId));
    }

    /** @test */
    public function it_can_restore_deleted_user()
    {
        $user = User::factory()->create();
        $user->delete();

        $user->restore();

        $this->assertDatabaseHas('users', ['id' => $user->id, 'deleted_at' => null]);
    }

    /** @test */
    public function it_can_force_delete_user()
    {
        $user = User::factory()->create();
        $userId = $user->id;

        $user->forceDelete();

        $this->assertDatabaseMissing('users', ['id' => $userId]);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        User::create([]);
    }

    /** @test */
    public function it_validates_unique_email()
    {
        User::factory()->create(['email' => 'test@example.com']);

        $this->expectException(\Illuminate\Database\QueryException::class);

        User::create([
            'name' => 'Another User',
            'email' => 'test@example.com',
            'password' => 'password'
        ]);
    }

    /** @test */
    public function it_can_be_created_via_factory()
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);
        $this->assertNotNull($user->name);
        $this->assertNotNull($user->email);
        $this->assertNotNull($user->password);
    }

    /** @test */
    public function it_can_be_created_as_admin()
    {
        $admin = User::factory()->admin()->create();

        $this->assertInstanceOf(User::class, $admin);
        $this->assertTrue($admin->is_admin);
    }

    /** @test */
    public function it_can_be_created_unverified()
    {
        $unverifiedUser = User::factory()->unverified()->create();

        $this->assertNull($unverifiedUser->email_verified_at);
    }

    /** @test */
    public function it_notifies_for_password_reset()
    {
        $user = User::factory()->create();

        // Mock the notification
        $this->mock(\App\Notifications\AdminResetPasswordNotification::class)
            ->shouldReceive('via')
            ->andReturn(['mail']);

        $user->sendPasswordResetNotification('test-token');
    }

    /** @test */
    public function it_has_correct_table_name()
    {
        $this->assertEquals('users', $this->user->getTable());
    }

    /** @test */
    public function it_uses_correct_primary_key()
    {
        $this->assertEquals('id', $this->user->getKeyName());
    }

    /** @test */
    public function it_increments_primary_key()
    {
        $this->assertTrue($this->user->getIncrementing());
    }

    /** @test */
    public function it_has_integer_primary_key()
    {
        $this->assertIsInt($this->user->getKeyType());
    }
}

