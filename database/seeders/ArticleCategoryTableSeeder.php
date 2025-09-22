<?php

namespace Database\Seeders;

use App\Models\ArticleCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ArticleCategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $article_category = [
            [
                "id" => 1,
                'category_name' => 'Science',
                "status" => 'Active',
            ],
            [
                "id" => 2,
                'category_name' => 'Technology',
                "status" => 'Active',
            ],
            [
                "id" => 3,
                'category_name' => 'Finance',
                "status" => 'Active',
            ],
            [
                "id" => 4,
                'category_name' => 'Climate Change',
                "status" => 'Active',
            ],
        ];

        ArticleCategory::insert($article_category);
    }
}
