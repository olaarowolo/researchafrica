<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Models\QuoteRequest;
use App\Modules\AfriScribe\Mail\QuoteRequestMail;
use App\Modules\AfriScribe\Mail\QuoteRequestClientAcknowledgementMail;

class QuoteRequestTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        // Create storage directories for testing
        Storage::fake('public');
    }

    /**
     * Test quote request form displays correctly
     */
    public function test_quote_request_form_displays_correctly()
    {
        $response = $this->get('/afriscribe/quote-request');

        $response->assertStatus(200);
        $response->assertViewIs('afriscribe.afriscribe-proofread-order-form');
        $response->assertSee('Request a Quote');
    }

    /**
     * Test successful quote request submission
     */
    public function test_successful_quote_request_submission()
    {
        Mail::fake();

        $file = UploadedFile::fake()->create('research_paper.docx', 2048);

        $requestData = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'ra_service' => 'afriscribe',
            'product' => 'proofread',
            'location' => 'Nigeria',
            'service_type' => 'Student-Friendly Proofreading',
            'word_count' => 2500,
            'addons' => ['plag', 'rush'],
            'referral' => 'Google Search',
            'message' => $this->faker->paragraph,
            'file' => $file,
        ];

        $response = $this->post('/afriscribe/quote-request', $requestData);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check if quote request was created in database
        $this->assertDatabaseHas('quote_requests', [
            'name' => $requestData['name'],
            'email' => $requestData['email'],
            'ra_service' => $requestData['ra_service'],
            'product' => $requestData['product'],
            'location' => $requestData['location'],
            'service_type' => $requestData['service_type'],
            'word_count' => $requestData['word_count'],
            'status' => 'pending',
        ]);

        // Check if file was stored
        $quoteRequest = QuoteRequest::where('email', $requestData['email'])->firstOrFail();
        $this->assertNotNull($quoteRequest->file_path);
        Storage::disk('public')->assertExists($quoteRequest->file_path);

        // Check that emails were sent to the correct recipients
        Mail::assertSent(QuoteRequestMail::class);
        Mail::assertSent(QuoteRequestClientAcknowledgementMail::class);
    }

    /**
     * Test quote request validation fails with missing required fields
     */
    public function test_quote_request_validation_fails_with_missing_fields()
    {
        $incompleteData = [
            'name' => '', // Required field empty
            'email' => 'invalid-email', // Invalid email
            'ra_service' => 'afriscribe',
            'product' => 'proofread',
            'location' => 'Nigeria',
            'service_type' => 'Student-Friendly Proofreading',
            // Missing required field: file
        ];

        $response = $this->post('/afriscribe/quote-request', $incompleteData);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['name', 'email', 'file']);
    }

    /**
     * Test quote request with invalid file type
     */
    public function test_quote_request_with_invalid_file_type()
    {
        $invalidFile = UploadedFile::fake()->create('image.jpg', 1024); // Invalid file type

        $requestData = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'ra_service' => 'afriscribe',
            'product' => 'proofread',
            'location' => 'Nigeria',
            'service_type' => 'Student-Friendly Proofreading',
            'word_count' => 2500,
            'file' => $invalidFile,
        ];

        $response = $this->post('/afriscribe/quote-request', $requestData);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['file']);
    }

    /**
     * Test pricing data endpoint
     */
    public function test_pricing_data_endpoint_returns_correct_structure()
    {
        $response = $this->get('/afriscribe/pricing-data');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'UK' => [
                'proofreading' => [
                    'rate',
                    'rush_multiplier',
                    'plagiarism_check'
                ],
                'copy_editing' => [
                    'rate',
                    'rush_multiplier',
                    'plagiarism_check'
                ],
                'substantive_editing' => [
                    'rate',
                    'rush_multiplier',
                    'plagiarism_check'
                ]
            ],
            'Nigeria' => [
                'proofreading' => [
                    'rate',
                    'rush_multiplier',
                    'plagiarism_check'
                ],
                'copy_editing' => [
                    'rate',
                    'rush_multiplier',
                    'plagiarism_check'
                ],
                'substantive_editing' => [
                    'rate',
                    'rush_multiplier',
                    'plagiarism_check'
                ]
            ]
        ]);
    }

    /**
     * Test admin quote requests index page requires authentication
     */
    public function test_admin_quote_requests_index_requires_authentication()
    {
        $response = $this->get('/admin/afriscribe/quote-requests');

        $response->assertStatus(302);
        $response->assertRedirect('/admin/login');
    }

    /**
     * Test admin quote request show page requires authentication
     */
    public function test_admin_quote_request_show_requires_authentication()
    {
        $quoteRequest = QuoteRequest::factory()->create();

        $response = $this->get("/admin/afriscribe/quote-requests/{$quoteRequest->id}");

        $response->assertStatus(302);
        $response->assertRedirect('/admin/login');
    }

    /**
     * Test quote request status update
     */
    public function test_quote_request_status_update()
    {
        // Create a user and authenticate
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $quoteRequest = QuoteRequest::factory()->create([
            'status' => 'pending',
        ]);

        $updateData = [
            'status' => 'quoted',
            'estimated_cost' => 150.00,
            'estimated_turnaround' => '5-7 business days',
            'admin_notes' => 'Quote provided to client',
        ];

        $response = $this->put("/admin/afriscribe/quote-requests/{$quoteRequest->id}/status", $updateData);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check if status was updated in database
        $this->assertDatabaseHas('quote_requests', [
            'id' => $quoteRequest->id,
            'status' => 'quoted',
            'estimated_cost' => 150.00,
            'estimated_turnaround' => '5-7 business days',
            'admin_notes' => 'Quote provided to client',
        ]);
    }

    /**
     * Test file download functionality
     */
    public function test_file_download_functionality()
    {
        // Create a user and authenticate
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $file = UploadedFile::fake()->create('research_paper.docx', 2048);
        $filePath = $file->store('quote-requests', 'public');

        $quoteRequest = QuoteRequest::factory()->create([
            'file_path' => $filePath,
            'original_filename' => 'research_paper.docx',
        ]);

        $response = $this->get("/admin/afriscribe/quote-requests/{$quoteRequest->id}/download");

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename="research_paper.docx"');
    }

    /**
     * Test file download with missing file
     */
    public function test_file_download_with_missing_file()
    {
        // Create a user and authenticate
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $quoteRequest = QuoteRequest::factory()->create([
            'file_path' => 'nonexistent-file.docx',
            'original_filename' => 'research_paper.docx',
        ]);

        $response = $this->get("/admin/afriscribe/quote-requests/{$quoteRequest->id}/download");

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /**
     * Test quote request with addons
     */
    public function test_quote_request_with_addons()
    {
        Mail::fake();

        $requestData = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'ra_service' => 'afriscribe',
            'product' => 'proofread',
            'location' => 'UK',
            'service_type' => 'Research Editing',
            'word_count' => 5000,
            'addons' => ['plag', 'rush'],
            'referral' => 'University Website',
            'message' => 'Please provide detailed feedback',
            'file' => UploadedFile::fake()->create('thesis.docx', 4096),
        ];

        $response = $this->post('/afriscribe/quote-request', $requestData);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check if addons were stored correctly (Laravel will cast array to JSON automatically)
        $this->assertDatabaseHas('quote_requests', [
            'name' => $requestData['name'],
            'email' => $requestData['email'],
        ]);

        // Verify the addons are stored as expected by checking the raw database value
        $quoteRequest = \App\Models\QuoteRequest::where('name', $requestData['name'])->first();
        $this->assertEquals(['plagiarism_check', 'rush_service'], $quoteRequest->addons);

        Mail::assertSent(QuoteRequestMail::class);
        Mail::assertSent(QuoteRequestClientAcknowledgementMail::class);
    }

    /**
     * Test quote request without optional fields
     */
    public function test_quote_request_without_optional_fields()
    {
        Mail::fake();

        $minimalData = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'ra_service' => 'afriscribe',
            'product' => 'proofread',
            'location' => 'Nigeria',
            'service_type' => 'Student-Friendly Proofreading',
            'file' => UploadedFile::fake()->create('paper.pdf', 1024),
        ];

        $response = $this->post('/afriscribe/quote-request', $minimalData);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check if optional fields are null or default values
        $this->assertDatabaseHas('quote_requests', [
            'name' => $minimalData['name'],
            'word_count' => null,
            'referral' => null,
            'message' => null,
        ]);

        Mail::assertSent(QuoteRequestMail::class);
        Mail::assertSent(QuoteRequestClientAcknowledgementMail::class);
    }

    /**
     * Test email is sent correctly when the message field is null.
     * This specifically tests the fix for the "TextPart body cannot be null" error.
     */
    public function test_email_is_sent_when_message_is_null()
    {
        Mail::fake();

        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'ra_service' => 'afriscribe',
            'product' => 'proofread',
            'location' => 'Nigeria',
            'service_type' => 'Student-Friendly Proofreading',
            'message' => null, // Explicitly null to trigger the error condition
            'file' => UploadedFile::fake()->create('document.docx', 1024),
        ];

        $this->post('/afriscribe/quote-request', $data);

        // Assert an email was sent to the admin
        Mail::assertSent(QuoteRequestMail::class);

        // Assert an email was sent to the client
        Mail::assertSent(QuoteRequestClientAcknowledgementMail::class);
    }
}
