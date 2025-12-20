<?php

namespace Tests\Unit\Models;

use App\Models\Member;
use App\Models\MemberRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MemberRoleTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $memberRole;

    protected function setUp(): void
    {
        parent::setUp();
        $this->memberRole = MemberRole::factory()->create();
    }

    /** @test */
    public function it_can_create_a_member_role()
    {
        $roleTitle = 'Test Role';
        
        $memberRole = MemberRole::create([
            'title' => $roleTitle,
            'status' => '1',
        ]);

        $this->assertInstanceOf(MemberRole::class, $memberRole);
        $this->assertEquals($roleTitle, $memberRole->title);
        $this->assertEquals('1', $memberRole->status);
    }

    /** @test */
    public function it_has_correct_table_name()
    {
        $this->assertEquals('member_roles', $this->memberRole->getTable());
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = [
            'title',
            'status',
            'created_at',
            'updated_at',
        ];

        $this->assertEquals($fillable, $this->memberRole->getFillable());
    }

    /** @test */
    public function it_has_status_constants()
    {
        $this->assertEquals('Active', MemberRole::STATUS_SELECT['1']);
        $this->assertEquals('Inactive', MemberRole::STATUS_SELECT['2']);
    }

    /** @test */
    public function it_has_many_members()
    {
        // Assuming Member model has a relationship to MemberRole.
        // Based on previous MemberType test, let's verify if Member has member_role_id or similar.
        // Or if it's a many-to-many relationship. The MemberRole model defines hasMany(Member::class).
        
        // Let's create a member and associate it.
        // Note: We need to know the foreign key on Member table.
        // Usually it would be member_role_id.
        
        // Let's inspect Member model structure if needed, but assuming standard convention based on MemberRole model:
        // public function members() { return $this->hasMany(Member::class); }
        
        // Trying to create a member associated with this role.
        // We might need to check MemberFactory or Member model to see the foreign key.
        // But let's try assuming member_role_id exists.
        
        try {
            $member = Member::factory()->create([
                'member_role_id' => $this->memberRole->id
            ]);
            
            $this->assertInstanceOf(Member::class, $this->memberRole->members->first());
            $this->assertEquals($member->id, $this->memberRole->members->first()->id);
        } catch (\Exception $e) {
            // If column doesn't exist, this test might fail.
            // But let's assume the relationship method exists in MemberRole, so the column should exist in Member.
            throw $e;
        }
    }

    /** @test */
    public function it_serializes_date_correctly()
    {
        $memberRole = MemberRole::factory()->create();
        $json = $memberRole->toArray();
        
        // Format is 'y-m-d H:i:s' based on model
        $this->assertMatchesRegularExpression('/\d{2}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $json['created_at']);
    }
}

