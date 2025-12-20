<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Member;
use App\Models\Role;
use App\Models\ArticleCategory;
use Illuminate\Support\Facades\Hash;

class JournalUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Example journals (by id from ArticleCategoryTableSeeder)
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
                // Create a Member profile linked to this user and journal
                Member::firstOrCreate([
                    'user_id' => $user->id,
                    'journal_id' => $journalId,
                ], [
                    'first_name' => ucfirst($role),
                    'last_name' => $journalName,
                    'email_address' => $email,
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
            }
        }
    }
}
