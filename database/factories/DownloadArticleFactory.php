<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\DownloadArticle;
use Illuminate\Database\Eloquent\Factories\Factory;

class DownloadArticleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DownloadArticle::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'article_id' => Article::factory(),
            'download' => fake()->numberBetween(1, 100),
        ];
    }
}
