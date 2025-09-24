<?php

namespace Database\Seeders;

use App\Models\SubArticle;
use Illuminate\Database\Seeder;

class SubArticlesTableSeeder extends Seeder
{
    public function run()
    {
        $subArticles = [
            // Sub-article for Pending Article (ID: 1)
            [
                'id' => 1,
                'article_id' => 1, // Pending Article
                'comment_id' => null,
                'abstract' => 'This paper explores recent advances in machine learning algorithms and their applications in various domains. We discuss the evolution of neural networks, deep learning techniques, and their impact on artificial intelligence research.',
                'status' => 1, // Pending
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Sub-article for Reviewing Article (ID: 2)
            [
                'id' => 2,
                'article_id' => 2, // Reviewing Article
                'comment_id' => null,
                'abstract' => 'Climate change represents one of the most significant challenges of our time. This comprehensive review examines the scientific evidence, impacts on ecosystems, and potential mitigation strategies to address global warming.',
                'status' => 3, // Pending Reviewer
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Sub-article for Published Article (ID: 3)
            [
                'id' => 3,
                'article_id' => 3, // Published Article
                'comment_id' => null,
                'abstract' => 'Financial technology (FinTech) has revolutionized the financial services industry. This article analyzes current trends, emerging technologies, and their implications for traditional banking and investment practices.',
                'status' => 10, // Approved
                'created_at' => now()->subDays(60),
                'updated_at' => now()->subDays(30),
            ],
        ];

        SubArticle::insert($subArticles);
    }
}
