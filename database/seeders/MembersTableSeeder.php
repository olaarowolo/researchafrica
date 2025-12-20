<?php

namespace Database\Seeders;

use App\Models\Member;
use Illuminate\Database\Seeder;

class MembersTableSeeder extends Seeder
{
    public function run()
    {
        $members = [
            [
                'id' => 1,
                'email_address' => 'author@example.com',
                'password' => bcrypt('password'),
                'title' => 'Mr',
                'first_name' => 'John',
                'last_name' => 'Author',
                'member_type_id' => 1, // Author
                'phone_number' => '1234567890',
                'country_id' => 1,
                'state_id' => 1,
                'member_role_id' => 1,
                'gender' => 'Male',
                'address' => '123 Author St',
                'registration_via' => 'email',
                'email_verified' => 1,
                'verified' => 1,
                'profile_completed' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'email_address' => 'editor@example.com',
                'password' => bcrypt('password'),
                'title' => 'Dr',
                'first_name' => 'Jane',
                'last_name' => 'Editor',
                'member_type_id' => 2, // Editor
                'phone_number' => '0987654321',
                'country_id' => 1,
                'state_id' => 1,
                'member_role_id' => 2,
                'gender' => 'Female',
                'address' => '456 Editor Ave',
                'registration_via' => 'email',
                'email_verified' => 1,
                'verified' => 0,
                'profile_completed' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'email_address' => 'reviewer@example.com',
                'password' => bcrypt('password'),
                'title' => 'Prof',
                'first_name' => 'Bob',
                'last_name' => 'Reviewer',
                'member_type_id' => 3, // Reviewer
                'phone_number' => '1122334455',
                'country_id' => 1,
                'state_id' => 1,
                'member_role_id' => 3,
                'gender' => 'Male',
                'address' => '789 Reviewer Blvd',
                'registration_via' => 'email',
                'email_verified' => 1,
                'verified' => 0,
                'profile_completed' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'email_address' => 'user@example.com',
                'password' => bcrypt('password'),
                'title' => 'Ms',
                'first_name' => 'Alice',
                'last_name' => 'User',
                'member_type_id' => 4, // User
                'phone_number' => '5566778899',
                'country_id' => 1,
                'state_id' => 1,
                'member_role_id' => 4,
                'gender' => 'Female',
                'address' => '321 User Lane',
                'registration_via' => 'email',
                'email_verified' => 1,
                'verified' => 0,
                'profile_completed' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'email_address' => 'publisher@example.com',
                'password' => bcrypt('password'),
                'title' => 'Mr',
                'first_name' => 'Charlie',
                'last_name' => 'Publisher',
                'member_type_id' => 5, // Publisher
                'phone_number' => '6677889900',
                'country_id' => 1,
                'state_id' => 1,
                'member_role_id' => 5,
                'gender' => 'Male',
                'address' => '654 Publisher St',
                'registration_via' => 'email',
                'email_verified' => 1,
                'verified' => 0,
                'profile_completed' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'email_address' => 'reviewer2@example.com',
                'password' => bcrypt('password'),
                'title' => 'Dr',
                'first_name' => 'Diana',
                'last_name' => 'Reviewer2',
                'member_type_id' => 6, // Reviewer 2
                'phone_number' => '7788990011',
                'country_id' => 1,
                'state_id' => 1,
                'member_role_id' => 6,
                'gender' => 'Female',
                'address' => '987 Reviewer2 Ave',
                'registration_via' => 'email',
                'email_verified' => 1,
                'verified' => 0,
                'profile_completed' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        Member::insert($members);
    }

    /**
     * Seed basic data required for members
     */
    private function seedBasicData()
    {
        // Seed countries if not already seeded
        if (\DB::table('countries')->count() == 0) {
            $this->call(CountrySeeder::class);
        }

        // Seed states if not already seeded
        if (\DB::table('states')->count() == 0) {
            $this->call(StateSeeder::class);
        }

        // Seed member types if not already seeded
        if (\DB::table('member_types')->count() == 0) {
            $this->call(MemberTypeSeeder::class);
        }

        // Seed member roles if not already seeded
        if (\DB::table('member_roles')->count() == 0) {
            $this->call(MemberRoleSeeder::class);
        }
    }
}
