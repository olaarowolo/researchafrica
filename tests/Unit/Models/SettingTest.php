<?php

namespace Tests\Unit\Models;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SettingTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $setting;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setting = Setting::factory()->create();
    }


    /** @test */
    public function it_can_create_a_setting()
    {
        $settingData = [
            'website_name' => 'Test Website',
            'website_email' => 'test@example.com',
            'phone_number' => '1234567890',
            'address' => 'Test Address',
            'status' => '1',
        ];
        
        $setting = Setting::create($settingData);

        $this->assertInstanceOf(Setting::class, $setting);
        $this->assertEquals('Test Website', $setting->website_name);
        $this->assertEquals('test@example.com', $setting->website_email);
    }

    /** @test */
    public function it_has_correct_table_name()
    {
        $this->assertEquals('settings', $this->setting->getTable());
    }

    /** @test */
    public function it_uses_soft_deletes()
    {
        $this->assertTrue(in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($this->setting)));
    }





    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = [
            'website_name',
            'website_email',
            'phone_number',
            'address',
            'facebook_url',
            'twitter_url',
            'linkedin_url',
            'instagram_url',
            'description',
            'status',
            'created_at',
            'updated_at',
            'deleted_at',
        ];

        $this->assertEquals($fillable, $this->setting->getFillable());
    }

    /** @test */
    public function it_has_status_constants()
    {
        $this->assertEquals('Enabled', Setting::STATUS_SELECT['1']);
        $this->assertEquals('Disabled', Setting::STATUS_SELECT['2']);
    }

    /** @test */
    public function it_serializes_date_correctly()
    {
        $setting = Setting::factory()->create();
        $json = $setting->toArray();
        
        $this->assertMatchesRegularExpression('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $json['created_at']);
    }
}

