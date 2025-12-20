<?php

namespace Database\Factories;

use App\Models\Bookmark;
use App\Models\Member;
use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookmarkFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Bookmark::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'member_id' => Member::factory(),
            'article_id' => Article::factory(),
        ];
    }

    /**
     * Indicate that the bookmark belongs to a specific member.
     */
    public function forMember(Member $member)
    {
        return $this->state(function (array $attributes) use ($member) {
            return [
                'member_id' => $member->id,
            ];
        });
    }

    /**
     * Indicate that the bookmark belongs to a specific article.
     */
    public function forArticle(Article $article)
    {
        return $this->state(function (array $attributes) use ($article) {
            return [
                'article_id' => $article->id,
            ];
        });
    }
}

