<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {

        $this->call([
            PermissionsTableSeeder::class,
            RolesTableSeeder::class,
            PermissionRoleTableSeeder::class,
            UsersTableSeeder::class,
            RoleUserTableSeeder::class,
            CountriesTableSeeder::class,
            StatesTableSeeder::class,
            MemberTypeSeeder::class,
            MemberRoleSeeder::class,
            MembersTableSeeder::class,
            ArticleCategoryTableSeeder::class,
            ArticlesTableSeeder::class,
            SubArticlesTableSeeder::class,
            SettingsTableSeeder::class,
            AboutTableSeeder::class,
        ]);

    }
}
