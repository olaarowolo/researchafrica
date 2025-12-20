<?php

namespace Tests\Feature\Admin;

use App\Models\About;
use App\Models\Admin;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AboutControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $about;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['title' => 'Admin']);
        $this->admin = Admin::factory()->create();
        $this->admin->roles()->attach($role);

        $this->about = About::create([
            'description' => 'Original Description',
            'mission' => 'Original Mission',
            'vision' => 'Original Vision',
        ]);
    }

    /** @test */
    public function admin_can_view_about_page()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.abouts.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.abouts.index');
        $response->assertViewHas('about');
        $this->assertEquals($this->about->id, $response->viewData('about')->id);
    }

    /** @test */
    public function admin_can_update_about_info()
    {
        $newData = [
            'description' => 'New Description',
            'mission' => 'New Mission',
            'vision' => 'New Vision',
        ];

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.abouts.update'), $newData);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('abouts', $newData);
    }

    /** @test */
    public function update_requires_valid_data()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.abouts.update'), [
                'description' => '',
                'mission' => '',
                'vision' => '',
            ]);

        $response->assertSessionHasErrors(['description', 'mission', 'vision']);
    }

    /** @test */
    public function non_admin_cannot_access_about_page()
    {
        $response = $this->get(route('admin.abouts.index'));

        $response->assertRedirect(route('admin.login'));
    }
}
