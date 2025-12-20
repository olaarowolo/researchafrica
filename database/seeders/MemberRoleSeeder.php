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

        $memberRoles = [
            ['title' => 'Student', 'status' => 1],
            ['title' => 'Librarian', 'status' => 1],
            ['title' => 'Professor', 'status' => 1],
        ];

        foreach ($memberRoles as $role) {
            MemberRole::firstOrCreate($role);
        }
    }
}
