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
            'category_name' => fake()->unique()->randomElement($categories),
            'status' => 'active',
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

    /**
     * Create a journal category.
     */
    public function journal()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_journal' => true,
                'journal_slug' => fake()->slug(),
                'journal_acronym' => fake()->regexify('[A-Z]{2,5}'), // Generate 2-5 uppercase letters
                'journal_url' => fake()->url(),
                'issn' => fake()->regexify('[0-9]{4}-[0-9]{4}'),
            ];
        });
    }

    /**
     * Create a regular category.
     */
    public function category()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_journal' => false,
            ];
        });
    }
}
