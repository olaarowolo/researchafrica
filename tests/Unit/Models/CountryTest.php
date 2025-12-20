<?php

namespace Tests\Unit\Models;

use App\Models\Country;
use App\Models\State;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CountryTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $country;

    protected function setUp(): void
    {
        parent::setUp();
        $this->country = Country::factory()->create();
    }

    /** @test */
    public function it_can_create_a_country()
    {
        $countryData = [
            'name' => 'United States',
            'short_code' => 'US',
        ];

        $country = Country::create($countryData);

        $this->assertInstanceOf(Country::class, $country);
        $this->assertEquals('United States', $country->name);
        $this->assertEquals('US', $country->short_code);
    }

    /** @test */
    public function it_has_correct_table_name()
    {
        $this->assertEquals('countries', $this->country->getTable());
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = [
            'short_code',
            'name',
            'created_at',
            'updated_at',
        ];

        $this->assertEquals($fillable, $this->country->getFillable());
    }

    /** @test */
    public function it_has_many_states()
    {
        $country = Country::factory()->create();
        State::factory()->count(3)->create(['country_id' => $country->id]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $country->states);
        $this->assertCount(3, $country->states);
        $this->assertInstanceOf(State::class, $country->states->first());
    }

    /** @test */
    public function it_has_many_members()
    {
        $country = Country::factory()->create();
        Member::factory()->count(5)->create(['country_id' => $country->id]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $country->members);
        $this->assertCount(5, $country->members);
    }

    /** @test */
    public function it_scopes_by_short_code()
    {
        $us = Country::factory()->create(['short_code' => 'US']);
        $uk = Country::factory()->create(['short_code' => 'UK']);
        $ca = Country::factory()->create(['short_code' => 'CA']);

        $foundCountry = Country::where('short_code', 'US')->first();

        $this->assertNotNull($foundCountry);
        $this->assertEquals($us->id, $foundCountry->id);
    }

    /** @test */
    public function it_validates_unique_short_code()
    {
        Country::factory()->create(['short_code' => 'US']);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Country::create([
            'short_code' => 'US',
            'name' => 'Another United States'
        ]);
    }

    /** @test */
    public function it_validates_unique_name()
    {
        Country::factory()->create(['name' => 'United States']);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Country::create([
            'short_code' => 'XX',
            'name' => 'United States'
        ]);
    }

    /** @test */
    public function it_orders_by_name()
    {
        $country1 = Country::factory()->create(['name' => 'Zimbabwe']);
        $country2 = Country::factory()->create(['name' => 'Australia']);
        $country3 = Country::factory()->create(['name' => 'Mexico']);

        $ordered = Country::orderBy('name')->get();

        $this->assertEquals($country2->id, $ordered->first()->id);
        $this->assertEquals($country3->id, $ordered->skip(1)->first()->id);
        $this->assertEquals($country1->id, $ordered->last()->id);
    }

    /** @test */
    public function it_searches_by_name()
    {
        $country1 = Country::factory()->create(['name' => 'United States']);
        $country2 = Country::factory()->create(['name' => 'United Kingdom']);
        $country3 = Country::factory()->create(['name' => 'Australia']);

        $results = Country::where('name', 'LIKE', '%United%')->get();

        $this->assertCount(2, $results);
        $this->assertTrue($results->contains($country1));
        $this->assertTrue($results->contains($country2));
        $this->assertFalse($results->contains($country3));
    }

    /** @test */
    public function it_counts_states()
    {
        $country = Country::factory()->create();
        State::factory()->count(4)->create(['country_id' => $country->id]);

        $this->assertEquals(4, $country->states_count);
    }

    /** @test */
    public function it_counts_members()
    {
        $country = Country::factory()->create();
        Member::factory()->count(6)->create(['country_id' => $country->id]);

        $this->assertEquals(6, $country->members_count);
    }

    /** @test */
    public function it_checks_if_country_has_states()
    {
        $countryWithStates = Country::factory()->create();
        State::factory()->create(['country_id' => $countryWithStates->id]);

        $countryWithoutStates = Country::factory()->create();

        $this->assertTrue($countryWithStates->hasStates());
        $this->assertFalse($countryWithoutStates->hasStates());
    }

    /** @test */
    public function it_checks_if_country_has_members()
    {
        $countryWithMembers = Country::factory()->create();
        Member::factory()->create(['country_id' => $countryWithMembers->id]);

        $countryWithoutMembers = Country::factory()->create();

        $this->assertTrue($countryWithMembers->hasMembers());
        $this->assertFalse($countryWithoutMembers->hasMembers());
    }

    /** @test */
    public function it_can_be_created_with_common_countries()
    {
        $countries = ['US', 'UK', 'CA', 'AU', 'DE', 'FR', 'JP', 'CN'];

        foreach ($countries as $code) {
            $country = Country::factory()->create(['short_code' => $code]);
            $this->assertInstanceOf(Country::class, $country);
            $this->assertEquals($code, $country->short_code);
        }
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Country::create([]);
    }

    /** @test */
    public function it_can_update_country()
    {
        $country = Country::factory()->create();

        $country->update([
            'name' => 'Updated Country Name',
            'short_code' => 'UC'
        ]);

        $this->assertEquals('Updated Country Name', $country->fresh()->name);
        $this->assertEquals('UC', $country->fresh()->short_code);
    }

    /** @test */
    public function it_can_delete_country()
    {
        $country = Country::factory()->create();
        $countryId = $country->id;

        $country->delete();

        $this->assertSoftDeleted($country);
        $this->assertNotNull(Country::withTrashed()->find($countryId));
    }

    /** @test */
    public function it_cascades_deletes_to_states()
    {
        $country = Country::factory()->create();
        $states = State::factory()->count(2)->create(['country_id' => $country->id]);

        $country->delete();

        // States should still exist but with soft delete
        $this->assertSoftDeleted($country);
        $this->assertSoftDeleted($states->first());
        $this->assertSoftDeleted($states->last());
    }

    /** @test */
    public function it_cascades_deletes_to_members()
    {
        $country = Country::factory()->create();
        $members = Member::factory()->count(3)->create(['country_id' => $country->id]);

        $country->delete();

        // Members should still exist but with soft delete
        $this->assertSoftDeleted($country);
        $this->assertSoftDeleted($members->first());
    }

    /** @test */
    public function it_can_be_restored()
    {
        $country = Country::factory()->create();
        $country->delete();

        $country->restore();

        $this->assertDatabaseHas('countries', ['id' => $country->id, 'deleted_at' => null]);
    }

    /** @test */
    public function it_can_be_force_deleted()
    {
        $country = Country::factory()->create();
        $countryId = $country->id;

        $country->forceDelete();

        $this->assertDatabaseMissing('countries', ['id' => $countryId]);
    }

    /** @test */
    public function it_handles_short_code_case_insensitively()
    {
        $country = Country::factory()->create(['short_code' => 'US']);

        // Should be able to find by different case
        $foundByUpper = Country::where('short_code', 'US')->first();
        $foundByLower = Country::where('short_code', 'us')->first();

        $this->assertNotNull($foundByUpper);
        $this->assertNotNull($foundByLower);
        $this->assertEquals($country->id, $foundByUpper->id);
        $this->assertEquals($country->id, $foundByLower->id);
    }

    /** @test */
    public function it_gets_country_flag_emoji()
    {
        $country = Country::factory()->create(['short_code' => 'US']);

        $flag = $country->flag;

        $this->assertIsString($flag);
        $this->assertNotEmpty($flag);
    }

    /** @test */
    public function it_scopes_to_countries_with_members()
    {
        $countryWithMembers = Country::factory()->create();
        Member::factory()->create(['country_id' => $countryWithMembers->id]);

        $countryWithoutMembers = Country::factory()->create();

        $countriesWithMembers = Country::has('members')->get();

        $this->assertCount(1, $countriesWithMembers);
        $this->assertTrue($countriesWithMembers->contains($countryWithMembers));
        $this->assertFalse($countriesWithMembers->contains($countryWithoutMembers));
    }

    /** @test */
    public function it_scopes_to_countries_with_states()
    {
        $countryWithStates = Country::factory()->create();
        State::factory()->create(['country_id' => $countryWithStates->id]);

        $countryWithoutStates = Country::factory()->create();

        $countriesWithStates = Country::has('states')->get();

        $this->assertCount(1, $countriesWithStates);
        $this->assertTrue($countriesWithStates->contains($countryWithStates));
        $this->assertFalse($countriesWithStates->contains($countryWithoutStates));
    }

    /** @test */
    public function it_gets_popular_countries()
    {
        // This would typically be based on member count or other criteria
        $popularCountry = Country::factory()->create(['name' => 'United States']);
        Member::factory()->count(10)->create(['country_id' => $popularCountry->id]);

        $lessPopularCountry = Country::factory()->create(['name' => 'Small Country']);
        Member::factory()->count(1)->create(['country_id' => $lessPopularCountry->id]);

        // Assuming there's a scope for popular countries
        $popular = Country::withCount('members')
            ->having('members_count', '>', 5)
            ->get();

        $this->assertCount(1, $popular);
        $this->assertTrue($popular->contains($popularCountry));
        $this->assertFalse($popular->contains($lessPopularCountry));
    }
}

