<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Member;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Journal-specific users for each journal in ArticleCategoryTableSeeder
        $journals = [
            1 => 'Science',
            2 => 'Technology',
            3 => 'Finance',
            4 => 'Climate Change',
        ];

            $roles = [
                'Editor',
                'Reviewer',
                'Author',
                'Contributor',
            ];

        foreach ($journals as $journalId => $journalName) {
            foreach ($roles as $role) {
                $email = strtolower($role) . ".{$journalName}@journal.org";
                $user = User::firstOrCreate([
                    'email' => $email,
                ], [
                    'name' => ucfirst($role) . " ({$journalName})",
                    'password' => Hash::make('password'),
                ]);
                // Assign role if exists
                $roleModel = Role::where('title', $role)->first();
                if ($roleModel) {
                    $user->roles()->syncWithoutDetaching([$roleModel->id]);
                }
                // Create a Member profile (no user_id field)
                $member = Member::firstOrCreate([
                    'email_address' => $email,
                ], [
                    'first_name' => ucfirst($role),
                    'last_name' => $journalName,
                    'password' => Hash::make('password'),
                    'country_id' => 1,
                    'state_id' => 1,
                    'member_role_id' => 1,
                    'member_type_id' => 1,
                    'phone_number' => '1234567890',
                    'address' => 'N/A',
                    'registration_via' => 'email',
                    'email_verified' => '1',
                    'email_verified_at' => now(),
                    'verified' => '1',
                    'profile_completed' => '1',
                    'gender' => 'Other',
                    'date_of_birth' => '1990-01-01',
                ]);
                // Assign member to journal using assignToJournal
                if (method_exists($member, 'assignToJournal')) {
                    // Find member_type_id for this role if possible
                    $memberTypeId = 1; // Default to 1 (Author), update as needed
                    if ($role === 'Editor') $memberTypeId = 2;
                    if ($role === 'Reviewer') $memberTypeId = 3;
                    if ($role === 'Contributor') $memberTypeId = 4;
                    $existing = $member->journalMemberships()
                        ->where('journal_id', $journalId)
                        ->where('member_type_id', $memberTypeId)
                        ->where('status', 'active')
                        ->first();
                    if (!$existing) {
                        $member->assignToJournal($journalId, $memberTypeId);
                    }
                }
            }
        }
        // Platform Super Admin
        $superAdmin = User::firstOrCreate([
            'email' => 'superadmin@researchafrica.org',
        ], [
            'name' => 'Platform Super Admin',
            'password' => Hash::make('password'),
        ]);
        // Assign super admin role if exists
        if (Role::where('title', 'Super Admin')->exists()) {
            $superAdmin->roles()->syncWithoutDetaching([Role::where('title', 'Super Admin')->first()->id]);
        }

        // Journal Admin
        $journalAdmin = User::firstOrCreate([
            'email' => 'journaladmin@journal.org',
        ], [
            'name' => 'Journal Admin',
            'password' => Hash::make('password'),
        ]);
        if (Role::where('title', 'Journal Admin')->exists()) {
            $journalAdmin->roles()->syncWithoutDetaching([Role::where('title', 'Journal Admin')->first()->id]);
        }

        // Editor
        $editor = User::firstOrCreate([
            'email' => 'editor@journal.org',
        ], [
            'name' => 'Editor',
            'password' => Hash::make('password'),
        ]);
        if (Role::where('title', 'Editor')->exists()) {
            $editor->roles()->syncWithoutDetaching([Role::where('title', 'Editor')->first()->id]);
        }

        // Reviewer
        $reviewer = User::firstOrCreate([
            'email' => 'reviewer@journal.org',
        ], [
            'name' => 'Reviewer',
            'password' => Hash::make('password'),
        ]);
        if (Role::where('title', 'Reviewer')->exists()) {
            $reviewer->roles()->syncWithoutDetaching([Role::where('title', 'Reviewer')->first()->id]);
        }

        // Author
        $author = User::firstOrCreate([
            'email' => 'author@journal.org',
        ], [
            'name' => 'Author',
            'password' => Hash::make('password'),
        ]);
        if (Role::where('title', 'Author')->exists()) {
            $author->roles()->syncWithoutDetaching([Role::where('title', 'Author')->first()->id]);
        }

        // Contributor
        $contributor = User::firstOrCreate([
            'email' => 'contributor@journal.org',
        ], [
            'name' => 'Contributor',
            'password' => Hash::make('password'),
        ]);
        if (Role::where('title', 'Contributor')->exists()) {
            $contributor->roles()->syncWithoutDetaching([Role::where('title', 'Contributor')->first()->id]);
        }
    }
}
