<?php

namespace Tests\Unit\Models;

use App\Models\Admin;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Admin::factory()->create();
    }

    /** @test */
    public function it_can_create_an_admin()
    {
        $this->assertInstanceOf(Admin::class, $this->admin);
        $this->assertNotNull($this->admin->email);
    }

    /** @test */
    public function it_has_correct_table_name()
    {
        $this->assertEquals('users', $this->admin->getTable());
    }

    /** @test */
    public function it_has_hidden_attributes()
    {
        $hidden = [
            'password',
            'remember_token',
        ];

        $this->assertEquals($hidden, $this->admin->getHidden());
    }

    /** @test */
    public function it_can_have_roles()
    {
        $role = Role::factory()->create();
        $this->admin->roles()->attach($role);

        $this->assertTrue($this->admin->roles->contains($role));
        $this->assertInstanceOf(Role::class, $this->admin->roles->first());
    }
}
