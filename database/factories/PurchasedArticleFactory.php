<?php

namespace Database\Factories;

use App\Models\PurchasedArticle;
use App\Models\Member;
use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchasedArticleFactory extends Factory
{
    protected $model = PurchasedArticle::class;

    public function definition()
    {
        return [
            'member_id' => Member::factory(),
            'article_id' => Article::factory(),
            'amount' => $this->faker->randomFloat(2, 1, 100),
        ];
    }
}
