<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\Member;
use App\Models\PublisherAccept;
use Illuminate\Database\Eloquent\Factories\Factory;

class PublisherAcceptFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PublisherAccept::class;

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
