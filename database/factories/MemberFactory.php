<?php

namespace Database\Factories;

use App\Models\Member;
use App\Models\Country;
use App\Models\MemberRole;
use App\Models\MemberType;
use App\Models\State;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MemberFactory extends Factory
{
    /**
     * Create an admin member with admin role.
     */
    public function admin()
    {
        return $this->afterCreating(function (\App\Models\Member $member) {
            $adminRole = \App\Models\Role::firstOrCreate(
                ['title' => 'Admin'],
                ['title' => 'Admin']
            );
            // Attach using the related User's ID, not the Member's ID
            if ($member->user) {
                $member->roles()->attach($adminRole->id, ['user_id' => $member->user->id]);
            }
        });
    }
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
        $email = fake()->unique()->safeEmail();
        $password = bcrypt('password');
        // Create a User record for this Member
        $user = \App\Models\User::factory()->create([
            'email' => $email,
            'password' => $password,
        ]);
        return [
            'title' => fake()->randomElement(Member::TITLE_SELECT),
            'first_name' => fake()->firstName(),
            'middle_name' => fake()->optional()->firstName(),
            'last_name' => fake()->lastName(),
            'email_address' => $email,
            'phone_number' => fake()->phoneNumber(),
            'password' => $password,
            'country_id' => Country::factory(),
            'state_id' => State::factory(),
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
            // Optionally, if Member has user_id foreign key:
            // 'user_id' => $user->id,
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
