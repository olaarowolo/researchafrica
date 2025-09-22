<?php

namespace Database\Seeders;

use App\Models\About;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AboutTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $about = [

            'description' => "<p>Description</p>",
            'mission' => "<p>Mission</p>",
            'vision' => "<p>Vision</p>",
        ];

        About::insert($about);
    }
}