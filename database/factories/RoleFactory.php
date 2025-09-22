<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Role::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => fake()->randomElement(['Admin', 'Editor', 'Moderator', 'User']),
        ];
    }

    /**
     * Create an admin role.
     */
    public function admin()
    {
        return $this->state(function (array $attributes) {
            return [
                'title' => 'Admin',
            ];
        });
    }

    /**
     * Create an editor role.
     */
    public function editor()
    {
        return $this->state(function (array $attributes) {
            return [
                'title' => 'Editor',
            ];
        });
    }
}
