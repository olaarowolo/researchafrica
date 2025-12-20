<?php

namespace Tests\Unit\Models;

use App\Models\Country;
use App\Models\State;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StateTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $state;

    protected function setUp(): void
    {
        parent::setUp();
        $this->state = State::factory()->create();
    }

    /** @test */
    public function it_can_create_a_state()
    {
        $country = Country::factory()->create();
        $stateName = $this->faker->state;

        $state = State::create([
            'name' => $stateName,
            'country_id' => $country->id,
        ]);

        $this->assertInstanceOf(State::class, $state);
        $this->assertEquals($stateName, $state->name);
        $this->assertEquals($country->id, $state->country_id);
    }

    /** @test */
    public function it_has_correct_table_name()
    {
        $this->assertEquals('states', $this->state->getTable());
    }

    /** @test */
    public function it_has_guarded_attributes()
    {
        // Since $guarded is empty array in model
        $this->assertEquals([], $this->state->getGuarded());
    }

    /** @test */
    public function it_belongs_to_a_country()
    {
        $this->assertInstanceOf(Country::class, $this->state->country);
        $this->assertEquals($this->state->country_id, $this->state->country->id);
    }
}

