<?php

namespace Database\Factories;

use App\Models\FaqQuestion;
use App\Models\FaqCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class FaqQuestionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FaqQuestion::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'question' => fake()->sentence() . '?',
            'answer' => fake()->paragraphs(3, true),
            'category_id' => FaqCategory::factory(),
        ];
    }
}
