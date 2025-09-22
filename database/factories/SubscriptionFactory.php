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
            'price' => fake()->randomFloat(2, 10, 500),
            'duration_months' => fake()->randomElement([1, 3, 6, 12]),
            'is_active' => true,
            'max_articles' => fake()->numberBetween(5, 100),
            'max_downloads' => fake()->numberBetween(10, 200),
        ];
    }

    /**
     * Indicate that the subscription should be inactive.
     */
    public function inactive()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => false,
            ];
        });
    }

    /**
     * Create a monthly subscription.
     */
    public function monthly()
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Monthly Subscription',
                'duration_months' => 1,
                'price' => fake()->randomFloat(2, 10, 50),
            ];
        });
    }

    /**
     * Create an annual subscription.
     */
    public function annual()
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Annual Subscription',
                'duration_months' => 12,
                'price' => fake()->randomFloat(2, 50, 300),
            ];
        });
    }
}
