<?php

namespace Database\Factories\Modules\AfriScribe\Models;

use Illuminate\Database\Eloquent\Factories\Factory;

class QuoteRequestFactory extends Factory
{
    protected $model = QuoteRequest::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'ra_service' => 'afriscribe',
            'product' => $this->faker->randomElement(['proofread', 'manuscripts', 'insights', 'connect', 'archive', 'editor']),
            'location' => $this->faker->randomElement(['UK', 'Nigeria']),
            'service_type' => $this->faker->randomElement([
                'Student-Friendly Proofreading',
                'Research Editing',
                'Publication-Ready Academic Edit',
                'Basic Scholar Package',
                'Researcher\'s Advantage Package',
                'Premium Publication Package'
            ]),
            'word_count' => $this->faker->numberBetween(1000, 10000),
            'addons' => $this->faker->optional(0.3)->randomElements(['rush', 'plag'], rand(1, 2)),
            'referral' => $this->faker->optional(0.2)->word(),
            'message' => $this->faker->optional(0.7)->paragraph(),
            'original_filename' => $this->faker->optional(0.8)->word() . '.docx',
            'file_path' => $this->faker->optional(0.8) ? 'quote-requests/' . $this->faker->uuid() . '.docx' : null,
            'status' => $this->faker->randomElement(['pending', 'quoted', 'accepted', 'rejected', 'completed']),
            'estimated_cost' => $this->faker->optional(0.6)->randomFloat(2, 50, 1000),
            'estimated_turnaround' => $this->faker->optional(0.6)->randomElement([
                '3-5 business days',
                '2-3 business days',
                '5-7 business days',
                'Rush (48h)'
            ]),
            'admin_notes' => $this->faker->optional(0.4)->paragraph(),
            'quoted_at' => $this->faker->optional(0.3)->dateTime(),
            'accepted_at' => $this->faker->optional(0.2)->dateTime(),
            'completed_at' => $this->faker->optional(0.1)->dateTime(),
        ];
    }

    /**
     * Indicate that the quote request is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the quote request has a file.
     */
    public function withFile(): static
    {
        return $this->state(fn (array $attributes) => [
            'original_filename' => $this->faker->word() . '.docx',
            'file_path' => 'quote-requests/' . $this->faker->uuid() . '.docx',
        ]);
    }

    /**
     * Indicate that the quote request has a file with actual file content.
     */
    public function withActualFile(): static
    {
        return $this->state(fn (array $attributes) => [
            'original_filename' => $this->faker->word() . '.docx',
            'file_path' => 'quote-requests/' . $this->faker->uuid() . '.docx',
        ]);
    }

    /**
     * Indicate that the quote request has addons.
     */
    public function withAddons(): static
    {
        return $this->state(fn (array $attributes) => [
            'addons' => $this->faker->randomElements(['rush', 'plag'], rand(1, 2)),
        ]);
    }
}
