<?php

namespace Database\Factories;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Factories\Factory;

class PermissionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Permission::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */

public function definition()
    {
        return [
            'title' => $this->faker->unique()->word(),
        ];
    }

    /**
     * Create a CRUD permission.
     */

public function crud()
    {
        return $this->state(function (array $attributes) {
            $actions = ['create', 'read', 'update', 'delete'];
            $action = $this->faker->randomElement($actions);
            $entities = ['articles', 'users', 'comments', 'members'];
            $entity = $this->faker->randomElement($entities);

            return [
                'title' => "{$action}_{$entity}",
            ];
        });
    }

    /**
     * Create an admin permission.
     */

public function admin()
    {
        return $this->state(function (array $attributes) {
            return [
                'title' => "admin_" . $this->faker->word(),
            ];
        });
    }

    /**
     * Create a user management permission.
     */

public function userManagement()
    {
        return $this->state(function (array $attributes) {
            return [
                'title' => 'manage_users',
            ];
        });
    }

    /**
     * Create an article management permission.
     */

public function articleManagement()
    {
        return $this->state(function (array $attributes) {
            return [
                'title' => 'manage_articles',
            ];
        });
    }
}

