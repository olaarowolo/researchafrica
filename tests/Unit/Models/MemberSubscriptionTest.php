<?php

namespace Tests\Unit\Models;

use App\Models\Member;
use App\Models\MemberSubscription;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class MemberSubscriptionTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $memberSubscription;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Set config for date format testing
        Config::set('panel.date_format', 'Y-m-d');
        Config::set('panel.time_format', 'H:i:s');

        $this->memberSubscription = MemberSubscription::factory()->create();
    }

    /** @test */
    public function it_can_create_a_member_subscription()
    {
        $data = [
            'member_email_id' => Member::factory()->create()->id,
            'subscription_name_id' => Subscription::factory()->create()->id,
            'payment_method' => 'Automatic',
            'amount' => 99.99,
            'status' => '1',
            'expiry_date' => now()->addYear()->format('Y-m-d H:i:s'),
        ];

        $memberSubscription = MemberSubscription::create($data);

        $this->assertInstanceOf(MemberSubscription::class, $memberSubscription);
        $this->assertEquals($data['member_email_id'], $memberSubscription->member_email_id);
        $this->assertEquals($data['amount'], $memberSubscription->amount);
    }

    /** @test */
    public function it_has_correct_table_name()
    {
        $this->assertEquals('member_subscriptions', $this->memberSubscription->getTable());
    }

    /** @test */
    public function it_uses_soft_deletes()
    {
        $this->assertTrue(in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($this->memberSubscription)));
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = [
            'member_email_id',
            'subscription_name_id',
            'payment_method',
            'amount',
            'status',
            'expiry_date',
            'created_at',
            'updated_at',
            'deleted_at',
        ];

        $this->assertEquals($fillable, $this->memberSubscription->getFillable());
    }

    /** @test */
    public function it_belongs_to_a_member()
    {
        $this->assertInstanceOf(Member::class, $this->memberSubscription->member_email);
        $this->assertEquals($this->memberSubscription->member_email_id, $this->memberSubscription->member_email->id);
    }

    /** @test */
    public function it_belongs_to_a_subscription()
    {
        $this->assertInstanceOf(Subscription::class, $this->memberSubscription->subscription_name);
        $this->assertEquals($this->memberSubscription->subscription_name_id, $this->memberSubscription->subscription_name->id);
    }

    /** @test */
    public function it_has_status_constants()
    {
        $this->assertEquals([
            '1' => 'Active',
            '2' => 'Inactive',
        ], MemberSubscription::STATUS_SELECT);
    }

    /** @test */
    public function it_has_payment_method_constants()
    {
        $this->assertEquals([
            'Automatic' => 'Automatic',
            'Manual'    => 'Manual',
        ], MemberSubscription::PAYMENT_METHOD_SELECT);
    }

    /** @test */
    public function it_casts_expiry_date()
    {
        $this->assertNotNull($this->memberSubscription->expiry_date);
    }

    /** @test */
    public function it_serializes_date_correctly()
    {
        $json = $this->memberSubscription->toArray();
        
        $this->assertMatchesRegularExpression('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $json['created_at']);
    }
}
