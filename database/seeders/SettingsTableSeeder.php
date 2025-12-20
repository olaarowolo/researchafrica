<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks, truncate, then re-enable
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \DB::table('settings')->truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $settings = [
            "website_name" => "Research Africa",
            "website_email" => "xejihohe@mailinator.com",
            "phone_number" => "+1 (667) 215-3617",
            "address" => "Maiores rerum modi e",
            "description" => "<p>dsfvghy gsdgyug hsdyug ygsd dsygysdg ygsdygsdyg ysdgygsd</p>",
            "facebook_url" => "https://www.facebook.com/researchafripub/",
            "twitter_url" => "Nesciunt libero nos",
            "instagram_url" => "Maxime unde dolores",
            "status" => "1",
        ];

        Setting::insert($settings);
    }
}
