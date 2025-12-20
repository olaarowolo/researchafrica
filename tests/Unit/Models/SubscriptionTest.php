<?php

namespace Tests\Unit\Models;

use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $subscription;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subscription = Subscription::factory()->create();
    }

    /** @test */
    public function it_can_create_a_subscription()
    {
        $data = [
            'name' => 'Premium Plan',
            'description' => 'Best plan ever',
            'features' => 'Feature 1, Feature 2',
            'plan_type' => 'recurring',
            'cycle_type' => 'monthly',
            'cycle_number' => 1,
            'status' => '1',
        ];

        $subscription = Subscription::create($data);

        $this->assertInstanceOf(Subscription::class, $subscription);
        $this->assertEquals($data['name'], $subscription->name);
        $this->assertEquals($data['plan_type'], $subscription->plan_type);
    }

    /** @test */
    public function it_has_correct_table_name()
    {
        $this->assertEquals('subscriptions', $this->subscription->getTable());
    }

    /** @test */
    public function it_uses_soft_deletes()
    {
        $this->assertTrue(in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($this->subscription)));
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = [
            'name',
            'description',
            'features',
            'plan_type',
            'cycle_type',
            'cycle_number',
            'status',
            'created_at',
            'updated_at',
            'deleted_at',
        ];

        $this->assertEquals($fillable, $this->subscription->getFillable());
    }

    /** @test */
    public function it_has_status_constants()
    {
        $this->assertEquals([
            '1' => 'Enabled',
            '2' => 'Disabled',
        ], Subscription::STATUS_SELECT);
    }

    /** @test */
    public function it_has_cycle_type_constants()
    {
        $this->assertEquals([
            'weekly'  => 'Weekly',
            'monthly' => 'Monthly',
            'Yearly'  => 'Yearly',
        ], Subscription::CYCLE_TYPE_SELECT);
    }

    /** @test */
    public function it_has_plan_type_constants()
    {
        $this->assertEquals([
            'free'      => 'Free Plan',
            'one-time'  => 'One-time Plan',
            'recurring' => 'Recurring Plan',
        ], Subscription::PLAN_TYPE_SELECT);
    }

    /** @test */
    public function it_serializes_date_correctly()
    {
        $json = $this->subscription->toArray();
        
        $this->assertMatchesRegularExpression('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $json['created_at']);
    }

    /** @test */
    public function it_can_be_inactive()
    {
        $inactiveSubscription = Subscription::factory()->inactive()->create();
        
        $this->assertEquals('2', $inactiveSubscription->status);
    }
}
