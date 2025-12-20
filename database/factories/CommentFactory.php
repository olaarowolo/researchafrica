<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Article;
use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Comment::class;

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
            'message' => fake()->paragraph(),
            'status' => fake()->randomElement(array_keys(Comment::STATUS_SELECT)),
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'updated_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }

    /**
     * Indicate that the comment should be approved.
     */
    public function approved()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_approved' => true,
            ];
        });
    }

    /**
     * Indicate that the comment should be pending approval.
     */
    public function pending()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_approved' => false,
            ];
        });
    }
}
