<?php

namespace Database\Factories;

use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Subscription::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => fake()->words(2, true) . ' Subscription',
            'description' => fake()->paragraph(),
            'features' => fake()->paragraph(),
            'plan_type' => fake()->randomElement(array_keys(Subscription::PLAN_TYPE_SELECT)),
            'cycle_type' => fake()->randomElement(array_keys(Subscription::CYCLE_TYPE_SELECT)),
            'cycle_number' => fake()->numberBetween(1, 12),
            'status' => fake()->randomElement(array_keys(Subscription::STATUS_SELECT)),
        ];
    }

    /**
     * Indicate that the subscription should be inactive.
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
