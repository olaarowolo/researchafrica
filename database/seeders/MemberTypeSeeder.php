<?php

namespace Database\Seeders;

use App\Models\MemberType;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MemberTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $member_type = [
            [
                "id" => 1,
                'name' => 'Author',
                "status" => 1,
            ],
            [
                "id" => 2,
                'name' => 'Editor',
                "status" => 1,
            ],
            [
                "id" => 3,
                'name' => 'Reviewer',
                "status" => 1,
            ],
            [
                "id" => 4,
                'name' => 'User',
                "status" => 1,
            ],
            [
                "id" => 5,
                'name' => 'Publisher',
                "status" => 1,
            ],
            [
                "id" => 6,
                'name' => 'Reviewer 2',
                "status" => 1,
            ],
        ];

        MemberType::insert($member_type);
    }
}