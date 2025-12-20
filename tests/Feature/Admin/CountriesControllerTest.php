<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Country;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CountriesControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::create(['title' => 'Admin']);
        $this->admin = Admin::factory()->create();
        $this->admin->roles()->attach($role);

        $permissions = [
            'country_access',
            'country_create',
            'country_edit',
            'country_show',
            'country_delete',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['title' => $permission]);
            $role->permissions()->attach(Permission::where('title', $permission)->first());
        }
    }

    /** @test */
    public function admin_can_access_countries_index()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.countries.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.countries.index');
        $response->assertViewHas('countries');
    }

    /** @test */
    public function admin_can_create_country()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.countries.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.countries.create');
    }

    /** @test */
    public function admin_can_store_country()
    {
        $countryData = [
            'name' => 'Test Country',
            'short_code' => 'TC',
        ];

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.countries.store'), $countryData);

        $response->assertRedirect(route('admin.countries.index'));

        $this->assertDatabaseHas('countries', [
            'name' => 'Test Country',
            'short_code' => 'TC',
        ]);
    }

    /** @test */
    public function admin_can_edit_country()
    {
        $country = Country::factory()->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.countries.edit', $country->id));

        $response->assertStatus(200);
        $response->assertViewIs('admin.countries.edit');
        $response->assertViewHas('country');
    }

    /** @test */
    public function admin_can_update_country()
    {
        $country = Country::factory()->create();

        $updatedData = [
            'name' => 'Updated Country',
            'short_code' => 'UC',
        ];

        $response = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.countries.update', $country->id), $updatedData);

        $response->assertRedirect(route('admin.countries.index'));

        $this->assertDatabaseHas('countries', [
            'id' => $country->id,
            'name' => 'Updated Country',
            'short_code' => 'UC',
        ]);
    }

    /** @test */
    public function admin_can_show_country()
    {
        $country = Country::factory()->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.countries.show', $country->id));

        $response->assertStatus(200);
        $response->assertViewIs('admin.countries.show');
        $response->assertViewHas('country');
    }

    /** @test */
    public function admin_can_delete_country()
    {
        $country = Country::factory()->create();

        $response = $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.countries.destroy', $country->id));

        $response->assertRedirect();
        $this->assertSoftDeleted('countries', ['id' => $country->id]);
    }

    /** @test */
    public function admin_can_mass_destroy_countries()
    {
        $countries = Country::factory()->count(3)->create();
        $ids = $countries->pluck('id')->toArray();

        $response = $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.countries.massDestroy'), [
                'ids' => $ids,
            ]);

        $response->assertStatus(204);
        foreach ($ids as $id) {
            $this->assertSoftDeleted('countries', ['id' => $id]);
        }
    }
}
