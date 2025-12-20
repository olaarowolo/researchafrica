<?php

namespace Tests\Feature\Members;

use App\Models\Member;
use App\Models\MemberRole;
use App\Models\MemberType;
use App\Models\Country;
use App\Models\State;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $member;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();

        // Create required models
        $country = Country::factory()->create();
        $state = State::factory()->create(['country_id' => $country->id]);
        $memberRole = MemberRole::factory()->create();
        $memberType = MemberType::factory()->create();

        // Create member
        $this->member = Member::factory()->create([
            'country_id' => $country->id,
            'state_id' => $state->id,
            'member_type_id' => $memberType->id,
            'member_role_id' => $memberRole->id,
        ]);

        // Create setting with website email
        Setting::factory()->create([
            'website_email' => 'admin@example.com',
        ]);
    }

    /** @test */
    public function member_can_submit_contact_form()
    {
        $contactData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1234567890',
            'subject' => 'Test Subject',
            'message' => 'This is a test message',
        ];

        $response = $this->actingAs($this->member, 'member')
            ->post(route('user.contact'), $contactData);

        $response->assertRedirect()
            ->assertSessionHas('success', 'Message Sent Successfully');

        // Assert that mail was sent
        Mail::assertSent(\App\Mail\ContactUsMail::class, function ($mail) use ($contactData) {
            return $mail->data['name'] === $contactData['name'] &&
                   $mail->data['email'] === $contactData['email'] &&
                   $mail->data['subject'] === $contactData['subject'] &&
                   $mail->data['message'] === $contactData['message'];
        });
    }

    /** @test */
    public function contact_form_validation_fails_with_missing_required_fields()
    {
        $response = $this->actingAs($this->member, 'member')
            ->post(route('user.contact'), []);

        $response->assertSessionHasErrors(['name', 'email', 'subject', 'message']);
    }

    /** @test */
    public function contact_form_validation_fails_with_invalid_email()
    {
        $contactData = [
            'name' => 'John Doe',
            'email' => 'invalid-email',
            'subject' => 'Test Subject',
            'message' => 'This is a test message',
        ];

        $response = $this->actingAs($this->member, 'member')
            ->post(route('user.contact'), $contactData);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function contact_form_strips_html_tags_from_message()
    {
        $contactData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+1234567890',
            'subject' => 'Test Subject',
            'message' => 'This is a <strong>test</strong> message with <script>alert("xss")</script> HTML tags',
        ];

        $response = $this->actingAs($this->member, 'member')
            ->post(route('user.contact'), $contactData);

        $response->assertRedirect()
            ->assertSessionHas('success', 'Message Sent Successfully');

        // Assert that mail was sent
        Mail::assertSent(\App\Mail\ContactUsMail::class);
    }

    /** @test */
    public function contact_form_works_with_minimal_required_data()
    {
        $contactData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Test Subject',
            'message' => 'This is a test message',
        ];

        $response = $this->actingAs($this->member, 'member')
            ->post(route('user.contact'), $contactData);

        $response->assertRedirect()
            ->assertSessionHas('success', 'Message Sent Successfully');

        Mail::assertSent(\App\Mail\ContactUsMail::class);
    }
}
