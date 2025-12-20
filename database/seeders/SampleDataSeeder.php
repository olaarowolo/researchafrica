<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ArticleCategory;
use App\Models\Member;
use App\Models\Article;
use App\Models\JournalMembership;
use App\Models\JournalEditorialBoard;
use App\Models\EditorialWorkflow;
use App\Models\EditorialWorkflowStage;
use App\Models\ArticleEditorialProgress;
use Illuminate\Support\Facades\Hash;

class SampleDataSeeder extends Seeder
{
    public function run()
    {
        // Create Sample Journals
        $journals = [
            [
                'name' => 'Medical Research Journal',
                'display_name' => 'Medical Research Journal',
                'is_journal' => true,
                'journal_slug' => 'medical-research',
                'journal_acronym' => 'MRJ',
                'description' => 'Leading journal in medical research and healthcare studies.',
                'status' => 'active',
            ],
            [
                'name' => 'Engineering Journal',
                'display_name' => 'Engineering Journal',
                'is_journal' => true,
                'journal_slug' => 'engineering',
                'journal_acronym' => 'EJ',
                'description' => 'Advancing engineering knowledge and innovation.',
                'status' => 'active',
            ],
            [
                'name' => 'Social Sciences Review',
                'display_name' => 'Social Sciences Review',
                'is_journal' => true,
                'journal_slug' => 'social-sciences',
                'journal_acronym' => 'SSR',
                'description' => 'Exploring human behavior and societal structures.',
                'status' => 'active',
            ],
        ];

        foreach ($journals as $journalData) {
            ArticleCategory::firstOrCreate(
                ['journal_acronym' => $journalData['journal_acronym']],
                $journalData
            );
        }

        // Create Sample Members
        $members = [
            [
                'first_name' => 'John',
                'last_name' => 'Smith',
                'title' => 'Dr',
                'email_address' => 'john.smith@example.com',
                'password' => Hash::make('password'),
                'member_type_id' => 1, // Editor
                'email_verified' => 1,
                'phone_number' => '+1234567890',
            ],
            [
                'first_name' => 'Jane',
                'last_name' => 'Doe',
                'title' => 'Dr',
                'email_address' => 'jane.doe@example.com',
                'password' => Hash::make('password'),
                'member_type_id' => 2, // Reviewer
                'email_verified' => 1,
                'phone_number' => '+1234567891',
            ],
            [
                'first_name' => 'Robert',
                'last_name' => 'Johnson',
                'title' => 'Prof',
                'email_address' => 'robert.johnson@example.com',
                'password' => Hash::make('password'),
                'member_type_id' => 3, // Author
                'email_verified' => 1,
                'phone_number' => '+1234567892',
            ],
            [
                'first_name' => 'Emily',
                'last_name' => 'Davis',
                'title' => 'Dr',
                'email_address' => 'emily.davis@example.com',
                'password' => Hash::make('password'),
                'member_type_id' => 1, // Editor
                'email_verified' => 1,
                'phone_number' => '+1234567893',
            ],
        ];

        foreach ($members as $memberData) {
            try {
                Member::firstOrCreate(
                    ['email_address' => $memberData['email_address']],
                    $memberData
                );
            } catch (\Exception $e) {
                // Skip if member creation fails due to constraints
                continue;
            }
        }

        // Get created journals
        $medicalJournal = ArticleCategory::where('journal_acronym', 'MRJ')->first();
        $engineeringJournal = ArticleCategory::where('journal_acronym', 'EJ')->first();
        $socialJournal = ArticleCategory::where('journal_acronym', 'SSR')->first();

        // Get created or existing members
        $john = Member::where('email_address', 'john.smith@example.com')->first();
        $jane = Member::where('email_address', 'jane.doe@example.com')->first();
        $robert = Member::where('email_address', 'robert.johnson@example.com')->first();
        $emily = Member::where('email_address', 'emily.davis@example.com')->first();

        // Create Journal Memberships if members exist
        if ($john) {
            JournalMembership::firstOrCreate([
                'member_id' => $john->id,
                'journal_id' => $medicalJournal->id,
            ], [
                'member_type_id' => 1, // Editor
                'status' => 'active',
            ]);
        }

        if ($jane) {
            JournalMembership::firstOrCreate([
                'member_id' => $jane->id,
                'journal_id' => $medicalJournal->id,
            ], [
                'member_type_id' => 2, // Reviewer
                'status' => 'active',
            ]);
        }

        if ($robert) {
            JournalMembership::firstOrCreate([
                'member_id' => $robert->id,
                'journal_id' => $medicalJournal->id,
            ], [
                'member_type_id' => 3, // Author
                'status' => 'active',
            ]);
        }

        if ($emily) {
            JournalMembership::firstOrCreate([
                'member_id' => $emily->id,
                'journal_id' => $engineeringJournal->id,
            ], [
                'member_type_id' => 1, // Editor
                'status' => 'active',
            ]);
        }

        // Create Editorial Board if journals and members exist
        if ($medicalJournal && $john) {
            JournalEditorialBoard::firstOrCreate([
                'journal_id' => $medicalJournal->id,
                'member_id' => $john->id,
            ], [
                'position' => 'Editor-in-Chief',
                'department' => 'Internal Medicine',
                'institution' => 'Medical University',
                'bio' => 'Expert in medical research with 20+ years experience.',
                'is_active' => true,
            ]);
        }

        if ($engineeringJournal && $emily) {
            JournalEditorialBoard::firstOrCreate([
                'journal_id' => $engineeringJournal->id,
                'member_id' => $emily->id,
            ], [
                'position' => 'Editor-in-Chief',
                'department' => 'Mechanical Engineering',
                'institution' => 'Engineering Institute',
                'bio' => 'Leading researcher in engineering innovations.',
                'is_active' => true,
            ]);
        }

        // Create Sample Articles if journals and members exist
        $articles = [];
        if ($medicalJournal && $robert) {
            $articles[] = [
                'title' => 'Advances in Medical Imaging Technology',
                'content' => 'This article explores the latest developments in medical imaging...',
                'abstract' => 'Medical imaging has revolutionized diagnostics...',
                'keywords' => 'medical imaging, technology, diagnostics',
                'member_id' => $robert->id,
                'article_category_id' => 1, // Assuming category exists
                'journal_id' => $medicalJournal->id,
                'status' => 'published',
            ];
        }
        if ($engineeringJournal && $robert) {
            $articles[] = [
                'title' => 'Sustainable Engineering Practices',
                'content' => 'Engineering for sustainability is crucial...',
                'abstract' => 'This paper discusses sustainable engineering approaches...',
                'keywords' => 'sustainable engineering, environment, innovation',
                'member_id' => $robert->id,
                'article_category_id' => 2, // Assuming category exists
                'journal_id' => $engineeringJournal->id,
                'status' => 'under_review',
            ];
        }
        if ($socialJournal && $robert) {
            $articles[] = [
                'title' => 'Social Impact of Technology',
                'content' => 'Technology influences society in profound ways...',
                'abstract' => 'Examining the societal implications of technological advancement...',
                'keywords' => 'technology, society, social impact',
                'member_id' => $robert->id,
                'article_category_id' => 3, // Assuming category exists
                'journal_id' => $socialJournal->id,
                'status' => 'draft',
            ];
        }

        foreach ($articles as $articleData) {
            Article::create($articleData);
        }

        // Create Editorial Workflow if medical journal exists
        if ($medicalJournal) {
            $workflow = EditorialWorkflow::firstOrCreate([
                'journal_id' => $medicalJournal->id,
                'name' => 'Standard Review Process',
            ], [
                'description' => '5-stage editorial workflow',
                'stages' => json_encode([
                    ['name' => 'Submission', 'order' => 1],
                    ['name' => 'Initial Review', 'order' => 2],
                    ['name' => 'Peer Review', 'order' => 3],
                    ['name' => 'Revision', 'order' => 4],
                    ['name' => 'Final Decision', 'order' => 5],
                ]),
                'required_roles' => json_encode([1, 2, 3]), // Editor, Reviewer, Author
            ]);

            // Create Workflow Stages
            $stages = [
                ['name' => 'Submission', 'order' => 1, 'description' => 'Initial submission', 'required_roles' => json_encode([3]), 'allowed_actions' => json_encode(['submit'])],
                ['name' => 'Initial Review', 'order' => 2, 'description' => 'Editor initial review', 'required_roles' => json_encode([1]), 'allowed_actions' => json_encode(['approve', 'reject'])],
                ['name' => 'Peer Review', 'order' => 3, 'description' => 'Peer review process', 'required_roles' => json_encode([2]), 'allowed_actions' => json_encode(['review'])],
                ['name' => 'Revision', 'order' => 4, 'description' => 'Author revisions', 'required_roles' => json_encode([3]), 'allowed_actions' => json_encode(['revise'])],
                ['name' => 'Final Decision', 'order' => 5, 'description' => 'Final acceptance/rejection', 'required_roles' => json_encode([1]), 'allowed_actions' => json_encode(['accept', 'reject'])],
            ];

            foreach ($stages as $stageData) {
                EditorialWorkflowStage::firstOrCreate([
                    'editorial_workflow_id' => $workflow->id,
                    'order' => $stageData['order'],
                ], $stageData);
            }

            // Create Article Editorial Progress if article exists
            $article = Article::where('journal_id', $medicalJournal->id)->first();
            if ($article) {
                ArticleEditorialProgress::firstOrCreate([
                    'article_id' => $article->id,
                ], [
                    'editorial_workflow_id' => $workflow->id,
                    'current_stage_id' => 1, // Submission
                    'status' => 'in_progress',
                ]);
            }
        }

        $this->command->info('Sample data seeded successfully!');
    }
}