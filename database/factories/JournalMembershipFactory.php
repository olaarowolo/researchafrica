<?php

namespace Database\Factories;

use App\Models\ArticleCategory;
use App\Models\JournalMembership;
use App\Models\Member;
use App\Models\MemberType;
use Illuminate\Database\Eloquent\Factories\Factory;

class JournalMembershipFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = JournalMembership::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'member_id' => Member::factory(),
            'journal_id' => ArticleCategory::factory(),
            'member_type_id' => MemberType::factory(),
            'status' => fake()->randomElement(['active', 'inactive', 'pending', 'suspended']),
            'assigned_by' => Member::factory(),
            'assigned_at' => now()->subDays(rand(1, 365)),
            'expires_at' => fake()->optional(0.7)->dateTime('+1 year'),
            'notes' => fake()->optional(0.3)->paragraph(),
        ];
    }

    /**
     * Indicate that the membership is active.
     */
    public function active()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => JournalMembership::STATUS_ACTIVE,
            ];
        });
    }

    /**
     * Indicate that the membership is inactive.
     */
    public function inactive()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => JournalMembership::STATUS_INACTIVE,
            ];
        });
    }

    /**
     * Indicate that the membership is pending.
     */
    public function pending()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => JournalMembership::STATUS_PENDING,
            ];
        });
    }
}
