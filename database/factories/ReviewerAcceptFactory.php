<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\Member;
use App\Models\ReviewerAccept;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewerAcceptFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ReviewerAccept::class;

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
            'assigned_id' => Member::factory(),
            'created_at' => fake()->dateTime(),
            'updated_at' => fake()->dateTime(),
        ];
    }
}
