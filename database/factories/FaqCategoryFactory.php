<?php

namespace Database\Factories;

use App\Models\FaqCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class FaqCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FaqCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $categories = [
            'General Questions',
            'Account & Billing',
            'Technical Support',
            'Content & Publishing',
            'Membership',
            'Subscription Plans',
            'Research Guidelines'
        ];

        return [
            'category' => fake()->unique()->randomElement($categories),
        ];
    }
}
