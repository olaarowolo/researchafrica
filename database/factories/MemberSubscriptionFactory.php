<?php

namespace Database\Factories;

use App\Models\Member;
use App\Models\MemberSubscription;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

class MemberSubscriptionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MemberSubscription::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'member_email_id' => Member::factory(),
            'subscription_name_id' => Subscription::factory(),
            'payment_method' => fake()->randomElement(['Automatic', 'Manual']),
            'amount' => fake()->randomFloat(2, 10, 500),

'status' => fake()->randomElement(['1', '2']),
            'expiry_date' => fake()->dateTimeBetween('now', '+1 year')->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Indicate that the subscription should be active.
     */
    public function active()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => '1',
            ];
        });
    }

    /**
     * Indicate that the subscription should be inactive.
     */
    public function inactive()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => '2',
            ];
        });
    }
}