<?php

namespace Tests\Unit\Models;

use App\Models\About;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AboutTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $about;

    protected function setUp(): void
    {
        parent::setUp();
        $this->about = About::factory()->create();
    }

    /** @test */
    public function it_can_create_an_about_entry()
    {
        $data = [
            'description' => 'Test description',
            'mission' => 'Test mission',
            'vision' => 'Test vision',
        ];

        $about = About::create($data);

        $this->assertInstanceOf(About::class, $about);
        $this->assertEquals('Test description', $about->description);
    }

    /** @test */
    public function it_has_correct_table_name()
    {
        $this->assertEquals('abouts', $this->about->getTable());
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = [
            'description',
            'mission',
            'vision',
            'created_at',
            'updated_at',
        ];

        $this->assertEquals($fillable, $this->about->getFillable());
    }

    /** @test */
    public function it_serializes_date_correctly()
    {
        $json = $this->about->toArray();
        
        $this->assertMatchesRegularExpression('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $json['created_at']);
    }
}
