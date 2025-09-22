<?php

namespace Database\Seeders;

use App\Models\MemberRole;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MemberRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $member_roles = [
            [
                'id' => 1,
                'title' => 'Student',
                'status' => 1
            ],
            [
                'id' => 2,
                'title' => 'Librarian',
                'status' => 1
            ],
            [
                'id' => 3,
                'title' => 'Professor',
                'status' => 1
            ],
        ];

        MemberRole::insert($member_roles);
    }
}
