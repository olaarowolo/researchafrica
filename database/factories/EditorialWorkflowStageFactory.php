<?php

namespace Database\Factories;

use App\Models\EditorialWorkflowStage;
use App\Models\EditorialWorkflow;
use Illuminate\Database\Eloquent\Factories\Factory;

class EditorialWorkflowStageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EditorialWorkflowStage::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'editorial_workflow_id' => EditorialWorkflow::factory(),
            'name' => fake()->words(2, true),
            'description' => fake()->sentence(),
            'order' => fake()->numberBetween(1, 10),
            'required_roles' => ['editor', 'reviewer'],
            'allowed_actions' => ['approve', 'reject', 'revise'],
            'deadline_days' => fake()->numberBetween(7, 30),
            'is_mandatory' => fake()->boolean(80), // 80% chance of being mandatory
            'requires_consensus' => fake()->boolean(30), // 30% chance of requiring consensus
            'min_reviewers' => fake()->numberBetween(1, 3),
            'max_reviewers' => fake()->numberBetween(3, 10),
            'stage_config' => [
                'auto_assign' => fake()->boolean(),
                'notification_enabled' => true,
            ],
        ];
    }

    /**
     * Indicate that the stage is mandatory.
     */
    public function mandatory()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_mandatory' => true,
            ];
        });
    }

    /**
     * Indicate that the stage requires consensus.
     */
    public function requiresConsensus()
    {
        return $this->state(function (array $attributes) {
            return [
                'requires_consensus' => true,
            ];
        });
    }
}