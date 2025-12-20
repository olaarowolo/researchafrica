<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\Member;
use App\Models\ReviewerAcceptFinal;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewerAcceptFinalFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ReviewerAcceptFinal::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'article_id' => Article::factory(),
            'member_id' => Member::factory(),
            'created_at' => fake()->dateTime(),
            'updated_at' => fake()->dateTime(),
        ];
    }
}
