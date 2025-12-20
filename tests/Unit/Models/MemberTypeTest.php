<?php

namespace Tests\Unit\Models;

use App\Models\Member;
use App\Models\MemberType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MemberTypeTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $memberType;

    protected function setUp(): void
    {
        parent::setUp();
        $this->memberType = MemberType::factory()->create();
    }

    /** @test */
    public function it_can_create_a_member_type()
    {
        $typeName = 'Test Type';
        
        $memberType = MemberType::create([
            'name' => $typeName,
            'status' => '1',
        ]);

        $this->assertInstanceOf(MemberType::class, $memberType);
        $this->assertEquals($typeName, $memberType->name);
        $this->assertEquals('1', $memberType->status);
    }

    /** @test */
    public function it_has_correct_table_name()
    {
        $this->assertEquals('member_types', $this->memberType->getTable());
    }

    /** @test */
    public function it_uses_soft_deletes()
    {
        $this->assertTrue(in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($this->memberType)));
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = [
            'name',
            'status',
            'created_at',
            'updated_at',
            'deleted_at',
        ];

        $this->assertEquals($fillable, $this->memberType->getFillable());
    }

    /** @test */
    public function it_has_status_constants()
    {
        $this->assertEquals('Active', MemberType::STATUS_SELECT['1']);
        $this->assertEquals('Inactive', MemberType::STATUS_SELECT['2']);
    }

    /** @test */
    public function it_has_many_members()
    {
        $member = Member::factory()->create([
            'member_type_id' => $this->memberType->id
        ]);

        $this->assertInstanceOf(Member::class, $this->memberType->members->first());
        $this->assertEquals($member->id, $this->memberType->members->first()->id);
    }

    /** @test */
    public function it_serializes_date_correctly()
    {
        // This tests the serializeDate method indirectly
        $memberType = MemberType::factory()->create();
        $json = $memberType->toArray();
        
        $this->assertMatchesRegularExpression('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $json['created_at']);
    }
}

