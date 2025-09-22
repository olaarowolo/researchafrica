<?php

namespace Database\Factories;

use App\Models\ArticleCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ArticleCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $categories = [
            'Computer Science',
            'Medicine',
            'Engineering',
            'Physics',
            'Chemistry',
            'Biology',
            'Mathematics',
            'Environmental Science',
            'Social Sciences',
            'Business',
            'Education',
            'Law',
            'Arts and Humanities',
            'Agriculture',
            'Materials Science'
        ];

        return [
            'name' => fake()->unique()->randomElement($categories),
            'description' => fake()->paragraph(),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the category should be inactive.
     */
    public function inactive()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => false,
            ];
        });
    }
}
