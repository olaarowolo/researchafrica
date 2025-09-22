<?php

namespace Database\Factories;

use App\Models\Member;
use App\Models\Country;
use App\Models\MemberRole;
use App\Models\MemberType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MemberFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Member::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => fake()->randomElement(Member::TITLE_SELECT),
            'first_name' => fake()->firstName(),
            'middle_name' => fake()->optional()->firstName(),
            'last_name' => fake()->lastName(),
            'email_address' => fake()->unique()->safeEmail(),
            'phone_number' => fake()->phoneNumber(),
            'password' => bcrypt('password'),
            'country_id' => Country::factory(),
            'state_id' => 1,
            'address' => fake()->address(),
            'member_role_id' => MemberRole::factory(),
            'member_type_id' => MemberType::factory(),
            'registration_via' => 'email',
            'email_verified' => '1',
            'email_verified_at' => now(),
            'verified' => '1',
            'profile_completed' => '1',
            'gender' => fake()->randomElement(['Male', 'Female']),
            'date_of_birth' => fake()->date(),
        ];
    }

    /**
     * Indicate that the member should be unverified.
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified' => '0',
                'email_verified_at' => null,
            ];
        });
    }

    /**
     * Indicate that the member should be inactive.
     */
    public function inactive()
    {
        return $this->state(function (array $attributes) {
            return [
                'verified' => '0',
            ];
        });
    }
}
