<?php

namespace Database\Seeders;

use App\Models\Article;
use Illuminate\Database\Seeder;

class ArticlesTableSeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key checks, truncate, then re-enable
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \DB::table('articles')->truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $articles = [
            // Pending Article
            [
                'id' => 1,
                'member_id' => 1, // Author
                'access_type' => 1, // Open Access
                'title' => 'Sample Pending Article: Advances in Machine Learning',
                'article_category_id' => 1, // Science
                'article_sub_category_id' => 1,
                'author_name' => 'John Author',
                'other_authors' => 'Jane Editor, Bob Reviewer',
                'corresponding_authors' => 'john.author@example.com',
                'institute_organization' => 'Research University',
                'amount' => 0,
                'doi_link' => 'https://doi.org/10.1234/sample1',
                'volume' => '1',
                'issue_no' => '1',
                'publish_date' => null,
                'published_online' => null,
                'is_recommended' => 0,
                'storage_disk' => 'local',
                'file_path' => 'sample_articles/pending_article.pdf',
                'article_status' => 1, // Pending
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Reviewing Article
            [
                'id' => 2,
                'member_id' => 2, // Editor
                'access_type' => 1, // Open Access
                'title' => 'Sample Reviewing Article: Climate Change Impacts',
                'article_category_id' => 4, // Climate Change
                'article_sub_category_id' => 4,
                'author_name' => 'Jane Editor',
                'other_authors' => 'Alice User, Charlie Publisher',
                'corresponding_authors' => 'jane.editor@example.com',
                'institute_organization' => 'Environmental Institute',
                'amount' => 0,
                'doi_link' => 'https://doi.org/10.1234/sample2',
                'volume' => '2',
                'issue_no' => '1',
                'publish_date' => null,
                'published_online' => null,
                'is_recommended' => 0,
                'storage_disk' => 'local',
                'file_path' => 'sample_articles/reviewing_article.pdf',
                'article_status' => 2, // Reviewing
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Published Article
            [
                'id' => 3,
                'member_id' => 3, // Reviewer
                'access_type' => 1, // Open Access
                'title' => 'Sample Published Article: Financial Technology Trends',
                'article_category_id' => 3, // Finance
                'article_sub_category_id' => 3,
                'author_name' => 'Bob Reviewer',
                'other_authors' => 'Diana Reviewer2',
                'corresponding_authors' => 'bob.reviewer@example.com',
                'institute_organization' => 'Finance Research Center',
                'amount' => 0,
                'doi_link' => 'https://doi.org/10.1234/sample3',
                'volume' => '1',
                'issue_no' => '2',
                'publish_date' => now()->subDays(30),
                'published_online' => now()->subDays(30),
                'is_recommended' => 1,
                'storage_disk' => 'local',
                'file_path' => 'sample_articles/published_article.pdf',
                'article_status' => 3, // Published
                'created_at' => now()->subDays(60),
                'updated_at' => now()->subDays(30),
            ],
        ];

        Article::insert($articles);
    }
}
