<?php

namespace Database\Factories;

use App\Models\MemberType;
use Illuminate\Database\Eloquent\Factories\Factory;

class MemberTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MemberType::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $types = [
            'Individual',
            'Student',
            'Professional',
            'Institutional',
            'Corporate',
            'Government',
            'Non-Profit'
        ];

        return [
            'name' => fake()->randomElement($types),
            'status' => '1', // 1 = Active, 2 = Inactive
        ];
    }

    /**
     * Indicate that the member type should be inactive.
     */
    public function inactive()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => '2',
            ];
        });
    }
}
