<?php

namespace Database\Factories;

use App\Models\MemberRole;
use Illuminate\Database\Eloquent\Factories\Factory;

class MemberRoleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MemberRole::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $roles = [
            'Researcher',
            'Student',
            'Professor',
            'Author',
            'Reviewer',
            'Editor',
            'Publisher',
            'Administrator'
        ];

        return [
            'title' => fake()->unique()->randomElement($roles),
            'status' => 1, // 1 = Active, 2 = Inactive
        ];
    }
}
