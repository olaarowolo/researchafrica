<?php

namespace Database\Factories;

use App\Models\SubArticle;
use App\Models\Article;
use App\Models\Comment;
use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubArticleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SubArticle::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'article_id' => Article::factory(),
            'comment_id' => Comment::factory(),
            'abstract' => $this->faker->paragraphs(3, true),
            'status' => $this->faker->randomElement(array_keys(SubArticle::STATUS_SELECT)),
        ];
    }

    /**
     * Indicate that the sub article is pending.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function pending()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => '1',
            ];
        });
    }

    /**
     * Indicate that the sub article is approved.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function approved()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => '10',
            ];
        });
    }

    /**
     * Indicate that the sub article is in editor stage.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function inEditorStage()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => '2',
            ];
        });
    }

    /**
     * Indicate that the sub article is pending reviewer.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function pendingReviewer()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => '3',
            ];
        });
    }
}