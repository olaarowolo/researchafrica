<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Article;
use App\Models\EditorialWorkflow;
use App\Models\ArticleEditorialProgress;

class ArticleEditorialProgressFactory extends Factory
{
    protected $model = ArticleEditorialProgress::class;

    public function definition()
    {
        $article = Article::factory()->create();
        $workflow = EditorialWorkflow::factory()->create();

        return [
            'article_id' => $article->id,
            'workflow_id' => $workflow->id,
            'current_stage' => $this->faker->randomElement(['initial_review', 'peer_review', 'editorial_decision', 'revision', 'final_approval']),
            'status' => $this->faker->randomElement(['pending', 'in_progress', 'completed', 'rejected']),
            'assigned_to' => null,
            'deadline' => $this->faker->dateTimeBetween('now', '+30 days'),
            'notes' => $this->faker->optional()->sentence(),
            'completed_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function pending()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'assigned_to' => null,
        ]);
    }

    public function inProgress()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'in_progress',
            'assigned_to' => \App\Models\Member::factory(),
        ]);
    }

    public function completed()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    public function rejected()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'completed_at' => now(),
        ]);
    }

    public function withDeadline($deadline)
    {
        return $this->state(fn (array $attributes) => [
            'deadline' => $deadline,
        ]);
    }

    public function forStage($stage)
    {
        return $this->state(fn (array $attributes) => [
            'current_stage' => $stage,
        ]);
    }
}

