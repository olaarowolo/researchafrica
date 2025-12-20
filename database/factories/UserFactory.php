<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return $this
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Create an admin user with admin role.
     */
    public function admin()
    {
        return $this->afterCreating(function (\App\Models\User $user) {
            // Create admin role if it doesn't exist
            $adminRole = Role::firstOrCreate(
                ['title' => 'Admin'],
                ['title' => 'Admin']
            );

            // Create permissions if they don't exist
            $permissions = [
                'article_access',
                'article_create',
                'article_edit',
                'article_delete',
                'article_show',
                'article_category_access',
                'article_category_create',
                'article_category_edit',
                'article_category_delete',
            ];

            $permissionIds = [];
            foreach ($permissions as $perm) {
                $permission = \App\Models\Permission::firstOrCreate(['title' => $perm]);
                $permissionIds[] = $permission->id;
            }

            // Attach permissions to the admin role
            $adminRole->permissions()->attach($permissionIds);

            // Assign admin role to user
            $user->roles()->attach($adminRole->id);
        });
    }
}
