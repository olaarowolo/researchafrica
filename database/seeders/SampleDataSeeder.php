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
use App\Models\MemberType;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\Country;
use App\Models\State;
use App\Models\MemberRole;
use App\Models\ArticleEditorialProgress;
use App\Models\SubArticle;

class SampleDataSeeder extends Seeder
{
    public function run()
    {
        // Create Country and State for members
        $country = Country::firstOrCreate(['name' => 'USA'], ['short_code' => 'US']);
        $state = State::firstOrCreate(['name' => 'California', 'country_id' => $country->id]);

        // Create Member Roles
        $memberRoles = [
            ['title' => 'Author', 'status' => '1'],
            ['title' => 'Editor', 'status' => '1'],
            ['title' => 'Reviewer', 'status' => '1'],
        ];

        foreach ($memberRoles as $roleData) {
            MemberRole::firstOrCreate(['title' => $roleData['title']], $roleData);
        }

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
                'member_role_id' => 2, // Editor
                'email_verified_at' => Carbon::now(),
                'phone_number' => '+1234567890',
                'state_id' => $state->id,
            ],
            [
                'first_name' => 'Jane',
                'last_name' => 'Doe',
                'title' => 'Dr',
                'email_address' => 'jane.doe@example.com',
                'password' => Hash::make('password'),
                'member_role_id' => 3, // Reviewer
                'email_verified_at' => Carbon::now(),
                'phone_number' => '+1234567891',
                'state_id' => $state->id,
            ],
            [
                'first_name' => 'Robert',
                'last_name' => 'Johnson',
                'title' => 'Prof',
                'email_address' => 'robert.johnson@example.com',
                'password' => Hash::make('password'),
                'member_role_id' => 1, // Author
                'email_verified_at' => Carbon::now(),
                'phone_number' => '+1234567892',
                'state_id' => $state->id,
            ],
            [
                'first_name' => 'Emily',
                'last_name' => 'Davis',
                'title' => 'Dr',
                'email_address' => 'emily.davis@example.com',
                'password' => Hash::make('password'),
                'member_role_id' => 2, // Editor
                'email_verified_at' => Carbon::now(),
                'phone_number' => '+1234567893',
                'state_id' => $state->id,
            ],
        ];

        foreach ($members as $memberData) {
            try {
                Member::firstOrCreate(
                    ['email_address' => $memberData['email_address']],
                    $memberData
                );
            } catch (\Exception $e) {
                $this->command->error('Failed to create member: ' . $e->getMessage());
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
            $this->command->info('Creating articles for Medical Journal');
            $articles[] = [
                'title' => 'Advances in Medical Imaging Technology',
                'abstract' => 'Medical imaging has revolutionized diagnostics...',
                'keywords' => 'medical imaging, technology, diagnostics',
                'member_id' => $robert->id,
                'article_category_id' => 1, // Assuming category exists
                'journal_id' => $medicalJournal->id,
                'article_status' => '3', // Published
                'access_type' => '1', // Open Access
                'article_sub_category_id' => 0,
            ];
            $articles[] = [
                'title' => 'Impact of Telemedicine on Rural Healthcare',
                'abstract' => 'This study examines the effects of telemedicine implementation...',
                'keywords' => 'telemedicine, rural healthcare, access',
                'member_id' => $robert->id,
                'article_category_id' => 1,
                'journal_id' => $medicalJournal->id,
                'article_status' => '2', // Reviewing
                'access_type' => '1',
                'article_sub_category_id' => 0,
            ];
            $articles[] = [
                'title' => 'New Vaccine Development Strategies',
                'abstract' => 'Exploring cutting-edge vaccine technologies...',
                'keywords' => 'vaccines, immunology, development',
                'member_id' => $robert->id,
                'article_category_id' => 1,
                'journal_id' => $medicalJournal->id,
                'article_status' => '1', // Pending
                'access_type' => '1',
                'article_sub_category_id' => 0,
            ];
        }
        if ($engineeringJournal && $robert) {
            $articles[] = [
                'title' => 'Sustainable Engineering Practices',
                'abstract' => 'This paper discusses sustainable engineering approaches...',
                'keywords' => 'sustainable engineering, environment, innovation',
                'member_id' => $robert->id,
                'article_category_id' => 2, // Assuming category exists
                'journal_id' => $engineeringJournal->id,
                'article_status' => '3', // Published
                'access_type' => '1',
                'article_sub_category_id' => 0,
            ];
            $articles[] = [
                'title' => 'AI Applications in Structural Engineering',
                'abstract' => 'This article explores AI-driven structural engineering solutions...',
                'keywords' => 'AI, structural engineering, design',
                'member_id' => $robert->id,
                'article_category_id' => 2,
                'journal_id' => $engineeringJournal->id,
                'article_status' => '2', // Reviewing
                'access_type' => '1',
                'article_sub_category_id' => 0,
            ];
            $articles[] = [
                'title' => 'Renewable Energy Systems Design',
                'abstract' => 'Comprehensive design principles for renewable energy...',
                'keywords' => 'renewable energy, systems design, sustainability',
                'member_id' => $robert->id,
                'article_category_id' => 2,
                'journal_id' => $engineeringJournal->id,
                'article_status' => '1', // Pending
                'access_type' => '1',
                'article_sub_category_id' => 0,
            ];
        }
        if ($socialJournal && $robert) {
            $articles[] = [
                'title' => 'Social Impact of Technology',
                'abstract' => 'Examining the societal implications of technological advancement...',
                'keywords' => 'technology, society, social impact',
                'member_id' => $robert->id,
                'article_category_id' => 3, // Assuming category exists
                'journal_id' => $socialJournal->id,
                'article_status' => '3', // Published
                'access_type' => '1',
                'article_sub_category_id' => 0,
            ];
            $articles[] = [
                'title' => 'Cultural Changes in Digital Age',
                'abstract' => 'An analysis of cultural transformations driven by digital innovation...',
                'keywords' => 'digital culture, social change, technology',
                'member_id' => $robert->id,
                'article_category_id' => 3,
                'journal_id' => $socialJournal->id,
                'article_status' => '2', // Reviewing
                'access_type' => '1',
                'article_sub_category_id' => 0,
            ];
            $articles[] = [
                'title' => 'Education Policy and Inequality',
                'abstract' => 'This research investigates the relationship between education policies and inequality...',
                'keywords' => 'education policy, inequality, social justice',
                'member_id' => $robert->id,
                'article_category_id' => 3,
                'journal_id' => $socialJournal->id,
                'article_status' => '1', // Pending
                'access_type' => '1',
                'article_sub_category_id' => 0,
            ];
        }

        foreach ($articles as $articleData) {
            try {
                $abstract = $articleData['abstract'] ?? '';
                unset($articleData['abstract']);
                $article = Article::create($articleData);
                if ($article && $abstract) {
                    SubArticle::create([
                        'article_id' => $article->id,
                        'abstract' => $abstract,
                        'status' => '1', // Pending
                    ]);
                }
            } catch (\Exception $e) {
                $this->command->error('Failed to create article: ' . $e->getMessage());
            }
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
                    'status' => 'draft',
                ]);
            }
        }

        $this->command->info('Sample data seeded successfully!');
    }
}
