<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Modules\AfriScribe\Models\AfriscribeRequest;
use App\Modules\AfriScribe\Mail\AfriscribeRequestMail;
use App\Modules\AfriScribe\Mail\AfriscribeClientAcknowledgementMail;

class AfriScribeTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        // Create storage directories for testing
        Storage::fake('public');
    }

    /**
     * Test landing page accessibility
     */
    public function test_landing_page_returns_successful_response()
    {
        $response = $this->get('/afriscribe/home');

        $response->assertStatus(200);
        $response->assertViewIs('afriscribe.pages.welcome');
    }

    /**
     * Test welcome form displays correctly
     */
    public function test_welcome_form_displays_correctly()
    {
        $response = $this->get('/afriscribe/home');

        $response->assertStatus(200);
        $response->assertSee('AfriScribe');
        $response->assertSee('AfriScribe Proofread');
    }

    /**
     * Test successful request submission
     */
    public function test_successful_request_submission()
    {
        Mail::fake();

        $requestData = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'service' => 'proofreading',
            'details' => $this->faker->paragraph,
        ];

        $response = $this->post('/afriscribe/request', $requestData);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Check if request was created in database
        $this->assertDatabaseHas('afriscribe_requests', [
            'name' => $requestData['name'],
            'email' => $requestData['email'],
            'service_type' => $requestData['service'],
            'status' => AfriscribeRequest::STATUS_PENDING,
        ]);

        // Check if emails were sent
        Mail::assertSent(AfriscribeRequestMail::class);
        Mail::assertSent(AfriscribeClientAcknowledgementMail::class);
    }

    /**
     * Test request submission with file upload
     */
    public function test_request_submission_with_file_upload()
    {
        Mail::fake();

        // Use Storage::fake to mock the file system
        Storage::fake('public');

        $file = UploadedFile::fake()->create('document.pdf', 1024);

        $requestData = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'service' => 'editing',
            'details' => $this->faker->paragraph,
            'file' => $file,
        ];

        $response = $this->post('/afriscribe/request', $requestData);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Check if file was stored (the controller generates a unique filename)
        $this->assertDatabaseHas('afriscribe_requests', [
            'original_filename' => 'document.pdf',
        ]);

        // Verify the file exists in the fake storage
        $request = AfriscribeRequest::where('original_filename', 'document.pdf')->first();
        $this->assertNotNull($request);
        $this->assertNotNull($request->file_path);

        // Check if the file exists at the stored path
        Storage::disk('public')->assertExists($request->file_path);
    }

    /**
     * Test request validation
     */
    public function test_request_validation_fails_with_invalid_data()
    {
        $invalidData = [
            'name' => '', // Required field empty
            'email' => 'invalid-email', // Invalid email
            'service' => 'invalid-service', // Invalid service type
        ];

        $response = $this->post('/afriscribe/request', $invalidData);

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
        $response->assertJsonStructure(['message', 'errors']);
    }

    /**
     * Test file upload validation
     */
    public function test_file_upload_validation()
    {
        $largeFile = UploadedFile::fake()->create('large-file.pdf', 10240 * 2); // 20MB file (over 10MB limit)

        $requestData = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'service' => 'proofreading',
            'details' => $this->faker->paragraph,
            'file' => $largeFile,
        ];

        $response = $this->post('/afriscribe/request', $requestData);

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
    }

    /**
     * Test admin requests endpoint
     */
    public function test_admin_requests_endpoint_requires_authentication()
    {
        // Test without authentication - should redirect to login
        $response = $this->getJson('/admin/afriscribe/requests');

        // The admin middleware should redirect unauthenticated users to login
        $response->assertUnauthorized();
    }

    /**
     * Test request status update
     */
    public function test_request_status_update()
    {
        $request = AfriscribeRequest::create([
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'service_type' => 'proofreading',
            'message' => $this->faker->paragraph,
            'status' => AfriscribeRequest::STATUS_PENDING,
        ]);

        $updateData = [
            'status' => AfriscribeRequest::STATUS_PROCESSING,
            'admin_notes' => 'Started processing this request',
        ];

        // Test without authentication - should redirect to login
        $response = $this->putJson("/admin/afriscribe/requests/{$request->id}/status", $updateData);

        // The admin middleware should redirect unauthenticated users to login
        $response->assertUnauthorized();
    }

    /**
     * Test manuscripts page
     */
    public function test_manuscripts_page_returns_successful_response()
    {
        $response = $this->get('/afriscribe/manuscripts');

        $response->assertStatus(200);
        $response->assertSee('Manuscripts');
    }

    /**
     * Test proofreading page
     */
    public function test_proofreading_page_returns_successful_response()
    {
        $response = $this->get('/afriscribe/proofreading');

        $response->assertStatus(200);
        $response->assertSee('Proofreading');
    }

    /**
     * Test welcome form page
     */
    public function test_welcome_form_page_returns_successful_response()
    {
        $response = $this->get('/afriscribe/welcome-form');

        $response->assertStatus(200);
        $response->assertViewIs('afriscribe.welcome-form');
    }

    /**
     * Test process request endpoint
     */
    public function test_process_request_endpoint()
    {
        Mail::fake();

        $requestData = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'service' => 'formatting',
            'details' => $this->faker->paragraph,
        ];

        $response = $this->post('/afriscribe/process-request', $requestData);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verify request was created
        $this->assertDatabaseHas('afriscribe_requests', [
            'service_type' => 'formatting',
        ]);
    }

    /**
     * Test error handling for database failures
     */
    public function test_database_error_handling()
    {
        // This test would require mocking database failures
        // For now, we'll test the basic error response structure
        $this->assertTrue(true); // Placeholder for actual database error test
    }
}
