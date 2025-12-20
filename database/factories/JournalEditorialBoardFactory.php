<?php

namespace Database\Factories;

use App\Models\ArticleCategory;
use App\Models\JournalEditorialBoard;
use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

class JournalEditorialBoardFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = JournalEditorialBoard::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'journal_id' => ArticleCategory::factory(),
            'member_id' => Member::factory(),
            'position' => fake()->randomElement(['Editor-in-Chief', 'Associate Editor', 'Editorial Board Member', 'Review Editor']),
            'department' => fake()->word(),
            'institution' => fake()->company(),
            'bio' => fake()->paragraph(),
            'orcid_id' => fake()->optional(0.5)->regexify('[0-9]{4}-[0-9]{4}-[0-9]{4}-[0-9]{4}'),
            'term_start' => fake()->date(),
            'term_end' => fake()->date(),
            'is_active' => fake()->boolean(80), // 80% chance of being active
            'display_order' => fake()->numberBetween(1, 100),
        ];
    }

    /**
     * Indicate that the editorial board member is active.
     */
    public function active()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => true,
            ];
        });
    }

    /**
     * Indicate that the editorial board member is inactive.
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