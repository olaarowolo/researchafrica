<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\Member;
use App\Models\ArticleCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Article::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'member_id' => Member::factory(),
            'access_type' => fake()->randomElement(array_keys(Article::ACCESS_TYPE)),
            'title' => fake()->sentence(6, true),
            'article_category_id' => ArticleCategory::factory(),
            'article_sub_category_id' => ArticleCategory::factory(),
            'author_name' => fake()->name(),
            'other_authors' => fake()->name() . ', ' . fake()->name(),
            'corresponding_authors' => fake()->email(),
            'institute_organization' => fake()->company(),
            'amount' => fake()->randomFloat(2, 10, 500),
            'doi_link' => 'https://doi.org/' . fake()->uuid(),
            'volume' => fake()->numberBetween(1, 20),
            'issue_no' => fake()->numberBetween(1, 4),
            'publish_date' => fake()->date(),
            'published_online' => fake()->dateTime(),
            'is_recommended' => fake()->boolean(20), // 20% chance of being recommended
            'storage_disk' => 'local',
            'file_path' => 'articles/sample-' . fake()->uuid() . '.docx',
            'article_status' => fake()->randomElement(array_keys(Article::ARTICLE_STATUS)),
        ];
    }

    /**
     * Indicate that the article should be published.
     */
    public function published()
    {
        return $this->state(function (array $attributes) {
            return [
                'article_status' => 3, // Published
                'published_online' => fake()->dateTimeBetween('-1 year', 'now'),
            ];
        });
    }

    /**
     * Indicate that the article should be pending.
     */
    public function pending()
    {
        return $this->state(function (array $attributes) {
            return [
                'article_status' => 1, // Pending
            ];
        });
    }

    /**
     * Indicate that the article should be under review.
     */
    public function underReview()
    {
        return $this->state(function (array $attributes) {
            return [
                'article_status' => 2, // Reviewing
            ];
        });
    }

    /**
     * Indicate that the article should be open access.
     */
    public function openAccess()
    {
        return $this->state(function (array $attributes) {
            return [
                'access_type' => 1, // Open Access
            ];
        });
    }

    /**
     * Indicate that the article should be closed access.
     */
    public function closedAccess()
    {
        return $this->state(function (array $attributes) {
            return [
                'access_type' => 2, // Close Access
            ];
        });
    }
}
