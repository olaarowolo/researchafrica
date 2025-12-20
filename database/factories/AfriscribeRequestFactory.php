<?php

namespace Database\Factories;

use App\Models\AfriscribeRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

class AfriscribeRequestFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AfriscribeRequest::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'service_type' => fake()->randomElement(['proofreading', 'editing', 'formatting']),
            'message' => fake()->paragraph(),
            'file_path' => fake()->optional(0.7)->word() . '.docx',
            'original_filename' => fake()->optional(0.7)->word() . '.docx',
            'status' => fake()->randomElement(['pending', 'processing', 'completed']),
            'admin_notes' => fake()->optional(0.3)->paragraph(),
            'processed_at' => fake()->optional(0.5)->dateTime(),
        ];
    }

    /**
     * Indicate that the request is pending.
     */
    public function pending()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => AfriscribeRequest::STATUS_PENDING,
                'processed_at' => null,
            ];
        });
    }

    /**
     * Indicate that the request is processing.
     */
    public function processing()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => AfriscribeRequest::STATUS_PROCESSING,
                'processed_at' => null,
            ];
        });
    }

    /**
     * Indicate that the request is completed.
     */
    public function completed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => AfriscribeRequest::STATUS_COMPLETED,
                'processed_at' => fake()->dateTime(),
            ];
        });
    }
}