<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ArticleCategory;
use App\Models\EditorialWorkflow;

class EditorialWorkflowFactory extends Factory
{
    protected $model = EditorialWorkflow::class;

    public function definition()
    {
        $journal = ArticleCategory::factory()->create();

        return [
            'journal_id' => $journal->id,
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'stages' => $this->getDefaultStages(),
            'is_default' => false,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    protected function getDefaultStages()
    {
        return [
            [
                'id' => 1,
                'name' => 'Initial Review',
                'order' => 1,
                'required_permissions' => ['editor'],
                'deadline_days' => 7,
                'description' => 'Initial editorial review'
            ],
            [
                'id' => 2,
                'name' => 'Peer Review',
                'order' => 2,
                'required_permissions' => ['reviewer'],
                'deadline_days' => 21,
                'description' => 'Expert peer review process'
            ],
            [
                'id' => 3,
                'name' => 'Editorial Decision',
                'order' => 3,
                'required_permissions' => ['editor'],
                'deadline_days' => 5,
                'description' => 'Final editorial decision'
            ],
            [
                'id' => 4,
                'name' => 'Revision (if needed)',
                'order' => 4,
                'required_permissions' => ['author'],
                'deadline_days' => 14,
                'description' => 'Author revisions based on feedback'
            ],
            [
                'id' => 5,
                'name' => 'Final Approval',
                'order' => 5,
                'required_permissions' => ['editor'],
                'deadline_days' => 3,
                'description' => 'Final approval for publication'
            ]
        ];
    }

    public function active()
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    public function inactive()
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function default()
    {
        return $this->state(fn (array $attributes) => [
            'is_default' => true,
        ]);
    }

    public function forJournal($journalId)
    {
        return $this->state(fn (array $attributes) => [
            'journal_id' => $journalId,
        ]);
    }

    public function withCustomStages(array $stages)
    {
        return $this->state(fn (array $attributes) => [
            'stages' => $stages,
        ]);
    }

    public function simpleWorkflow()
    {
        $simpleStages = [
            [
                'id' => 1,
                'name' => 'Review',
                'order' => 1,
                'required_permissions' => ['editor'],
                'deadline_days' => 7,
                'description' => 'Editorial review'
            ],
            [
                'id' => 2,
                'name' => 'Decision',
                'order' => 2,
                'required_permissions' => ['editor'],
                'deadline_days' => 3,
                'description' => 'Final decision'
            ]
        ];

        return $this->state(fn (array $attributes) => [
            'stages' => $simpleStages,
            'name' => 'Simple Review Process',
        ]);
    }

    public function extendedWorkflow()
    {
        $extendedStages = [
            [
                'id' => 1,
                'name' => 'Initial Screening',
                'order' => 1,
                'required_permissions' => ['editor'],
                'deadline_days' => 3,
                'description' => 'Initial article screening'
            ],
            [
                'id' => 2,
                'name' => 'Peer Review Assignment',
                'order' => 2,
                'required_permissions' => ['editor'],
                'deadline_days' => 2,
                'description' => 'Assign peer reviewers'
            ],
            [
                'id' => 3,
                'name' => 'First Peer Review',
                'order' => 3,
                'required_permissions' => ['reviewer'],
                'deadline_days' => 14,
                'description' => 'First round of peer review'
            ],
            [
                'id' => 4,
                'name' => 'Second Peer Review',
                'order' => 4,
                'required_permissions' => ['reviewer'],
                'deadline_days' => 14,
                'description' => 'Second round of peer review'
            ],
            [
                'id' => 5,
                'name' => 'Editorial Decision',
                'order' => 5,
                'required_permissions' => ['editor'],
                'deadline_days' => 7,
                'description' => 'Editorial decision based on reviews'
            ],
            [
                'id' => 6,
                'name' => 'Major Revision',
                'order' => 6,
                'required_permissions' => ['author'],
                'deadline_days' => 21,
                'description' => 'Major revisions required'
            ],
            [
                'id' => 7,
                'name' => 'Minor Revision',
                'order' => 7,
                'required_permissions' => ['author'],
                'deadline_days' => 7,
                'description' => 'Minor revisions required'
            ],
            [
                'id' => 8,
                'name' => 'Final Review',
                'order' => 8,
                'required_permissions' => ['editor'],
                'deadline_days' => 5,
                'description' => 'Final review before publication'
            ]
        ];

        return $this->state(fn (array $attributes) => [
            'stages' => $extendedStages,
            'name' => 'Extended Review Process',
            'description' => 'Comprehensive multi-stage review process for complex manuscripts',
        ]);
    }
}

