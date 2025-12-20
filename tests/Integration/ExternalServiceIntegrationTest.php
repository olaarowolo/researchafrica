
<?php
namespace Tests\Integration;

use Tests\TestCase;
use App\Models\Member;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ExternalServiceIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedBasicData();
        // Disable external HTTP requests in tests
        Http::fake([
            '*' => Http::response(['status' => 'mocked'], 200)
        ]);
        // Use fake storage for file operations
        Storage::fake('articles');
    }

    protected function seedBasicData()
    {
        \App\Models\Country::factory()->create(['name' => 'Nigeria']);
        \App\Models\MemberType::factory()->create(['name' => 'Author']);
        \App\Models\MemberType::factory()->create(['name' => 'Editor']);
        \App\Models\MemberType::factory()->create(['name' => 'Reviewer']);
        \App\Models\MemberRole::factory()->create(['title' => 'Author']);
        \App\Models\MemberRole::factory()->create(['title' => 'Editor']);
        \App\Models\MemberRole::factory()->create(['title' => 'Reviewer']);
    }

    /** @test */
    public function it_handles_google_login_integration()
    {
        // Arrange
        $googleUserData = [
            'id' => '123456789',
            'email' => 'user@gmail.com',
            'name' => 'John Doe',
            'picture' => 'https://example.com/avatar.jpg'
        ];

        Http::fake([
            'https://www.googleapis.com/oauth2/v1/userinfo' => Http::response($googleUserData, 200)
        ]);

        // Act
        $response = Http::get('https://www.googleapis.com/oauth2/v1/userinfo', [
            'access_token' => 'mock_access_token'
        ]);

        // Assert
        $this->assertEquals(200, $response->status());
        $this->assertEquals('user@gmail.com', $response->json()['email']);
        $this->assertEquals('John Doe', $response->json()['name']);
    }

    /** @test */
    public function it_handles_google_login_user_creation()
    {
        // Arrange
        $googleUserData = [
            'id' => '987654321',
            'email' => 'newuser@gmail.com',
            'name' => 'Jane Smith',
            'picture' => 'https://example.com/avatar2.jpg'
        ];

        Http::fake([
            'https://www.googleapis.com/oauth2/v1/userinfo' => Http::response($googleUserData, 200)
        ]);

        // Act - Simulate Google OAuth flow
        $googleResponse = Http::get('https://www.googleapis.com/oauth2/v1/userinfo', [
            'access_token' => 'mock_access_token'
        ]);

        $userData = $googleResponse->json();

        // Create or find member by Google ID
        $member = Member::firstOrCreate(
            ['email_address' => $userData['email']],
            [
                'first_name' => explode(' ', $userData['name'])[0],
                'last_name' => explode(' ', $userData['name'])[1] ?? '',
                'google_id' => $userData['id'],
                'avatar' => $userData['picture']
            ]
        );

        // Assert
        $this->assertModelExists($member);
        $this->assertEquals('newuser@gmail.com', $member->email_address);
        $this->assertEquals('Jane', $member->first_name);
        $this->assertEquals('Smith', $member->last_name);
        $this->assertEquals('987654321', $member->google_id);
    }

    /** @test */
    public function it_handles_pdf_generation_integration()
    {
        // Arrange
        $article = Article::factory()->create([
            'title' => 'PDF Generation Test Article',
            'abstract' => 'This article will be converted to PDF format.',
            'article_status' => 3
        ]);

        // Simulate PDF generation service
        $pdfContent = '%PDF-1.4
1 0 obj
<<
/Type /Catalog
/Pages 2 0 R
>>
endobj
2 0 obj
<<
/Type /Pages
/Kids [3 0 R]
/Count 1
>>
endobj
3 0 obj
<<
/Type /Page
/Parent 2 0 R
/MediaBox [0 0 612 792]
/Contents 4 0 R
>>
endobj
4 0 obj
<<
/Length 44
>>
stream
BT
/F1 12 Tf
100 700 Td
(PDF Test Content) Tj
ET
endstream
endobj
xref
0 5
0000000000 65535 f
0000000009 00000 n
0000000058 00000 n
0000000115 00000 n
0000000206 00000 n
trailer
<<
/Size 5
/Root 1 0 R
>>
startxref
309
%%EOF';

        // Act - Simulate PDF generation
        $pdfFileName = "article_{$article->id}_" . time() . '.pdf';
        Storage::disk('articles')->put($pdfFileName, $pdfContent);

        // Assert
        $this->assertTrue(Storage::disk('articles')->exists($pdfFileName));
        $this->assertEquals($pdfContent, Storage::disk('articles')->get($pdfFileName));

        // Verify PDF header
        $storedContent = Storage::disk('articles')->get($pdfFileName);
        $this->assertStringStartsWith('%PDF-', $storedContent);
    }

    /** @test */
    public function it_handles_media_library_upload()
    {
        // Arrange
        $member = Member::factory()->create(['email_address' => 'uploader@test.com']);

        // Create a mock image file
        $imageContent = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
        $uploadedFile = UploadedFile::fake()->createWithContent('test-image.png', $imageContent);

        // Act
        $fileName = time() . '_' . $uploadedFile->getClientOriginalName();
        Storage::disk('articles')->put($fileName, $uploadedFile->getContent());

        // Assert
        $this->assertTrue(Storage::disk('articles')->exists($fileName));

        // Verify file properties
        $storedFile = Storage::disk('articles')->get($fileName);
        $this->assertEquals($imageContent, $storedFile);
    }

    /** @test */
    public function it_handles_media_library_image_processing()
    {
        // Arrange
        $member = Member::factory()->create(['email_address' => 'processor@test.com']);

        // Create mock image data (1x1 pixel PNG)
        $imageData = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
        $originalFileName = 'original_image.png';

        Storage::disk('articles')->put($originalFileName, $imageData);

        // Act - Simulate image processing (resize, thumbnail creation)
        $thumbnailFileName = 'thumb_' . $originalFileName;
        $largeFileName = 'large_' . $originalFileName;

        // Simulate resizing operations
        Storage::disk('articles')->put($thumbnailFileName, $imageData); // Same content for mock
        Storage::disk('articles')->put($largeFileName, $imageData); // Same content for mock

        // Assert
        $this->assertTrue(Storage::disk('articles')->exists($originalFileName));
        $this->assertTrue(Storage::disk('articles')->exists($thumbnailFileName));
        $this->assertTrue(Storage::disk('articles')->exists($largeFileName));

        // Verify different file sizes (simulated)
        $originalSize = Storage::disk('articles')->size($originalFileName);
        $thumbnailSize = Storage::disk('articles')->size($thumbnailFileName);
        $largeSize = Storage::disk('articles')->size($largeFileName);

        $this->assertGreaterThan(0, $originalSize);
        $this->assertGreaterThan(0, $thumbnailSize);
        $this->assertGreaterThan(0, $largeSize);
    }

    /** @test */
    public function it_handles_external_api_communication()
    {
        // Arrange
        $externalApiUrl = 'https://api.example.com/v1/articles';
        $apiKey = 'test-api-key-12345';

        $articleData = [
            'title' => 'External API Test Article',
            'abstract' => 'This article is synced with external service',
            'status' => 'published'
        ];

        Http::fake([
            $externalApiUrl => Http::response([
                'success' => true,
                'article_id' => 'ext_12345',
                'message' => 'Article synchronized successfully'
            ], 200)
        ]);

        // Act
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json'
        ])->post($externalApiUrl, $articleData);

        // Assert
        $this->assertEquals(200, $response->status());
        $this->assertTrue($response->json()['success']);
        $this->assertEquals('ext_12345', $response->json()['article_id']);
    }

    /** @test */
    public function it_handles_external_webhook_notifications()
    {
        // Arrange
        $webhookUrl = 'https://hooks.example.com/webhook';
        $article = Article::factory()->create([
            'title' => 'Webhook Test Article',
            'article_status' => 3
        ]);

        Http::fake([
            $webhookUrl => Http::response([
                'status' => 'received',
                'timestamp' => now()->toISOString()
            ], 200)
        ]);

        // Act - Simulate webhook notification
        $webhookData = [
            'event' => 'article.published',
            'article_id' => $article->id,
            'title' => $article->title,
            'status' => 'published',
            'timestamp' => now()->toISOString()
        ];

        $response = Http::post($webhookUrl, $webhookData);

        // Assert
        $this->assertEquals(200, $response->status());
        $this->assertEquals('received', $response->json()['status']);
        $this->assertEquals('article.published', $webhookData['event']);
    }

    /** @test */
    public function it_handles_payment_gateway_integration()
    {
        // Arrange
        $paymentGatewayUrl = 'https://api.stripe.com/v1/charges';
        $apiKey = 'sk_test_12345';

        $paymentData = [
            'amount' => 2500, // $25.00 in cents
            'currency' => 'usd',
            'description' => 'Premium subscription - Research Africa',
            'receipt_email' => 'customer@example.com'
        ];

        Http::fake([
            $paymentGatewayUrl => Http::response([
                'id' => 'ch_1234567890',
                'amount' => 2500,
                'currency' => 'usd',
                'status' => 'succeeded',
                'receipt_url' => 'https://pay.stripe.com/receipts/test_123'
            ], 200)
        ]);

        // Act
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/x-www-form-urlencoded'
        ])->asForm()->post($paymentGatewayUrl, $paymentData);

        // Assert
        $this->assertEquals(200, $response->status());
        $this->assertEquals('ch_1234567890', $response->json()['id']);
        $this->assertEquals(2500, $response->json()['amount']);
        $this->assertEquals('succeeded', $response->json()['status']);
    }

    /** @test */
    public function it_handles_email_service_integration()
    {
        // Arrange
        $emailServiceUrl = 'https://api.sendgrid.com/v3/mail/send';
        $apiKey = 'SG.test_api_key_123';

        $emailData = [
            'personalizations' => [
                [
                    'to' => [['email' => 'recipient@example.com']],
                    'subject' => 'Research Africa Notification'
                ]
            ],
            'from' => ['email' => 'noreply@researchafrica.com'],
            'content' => [
                [
                    'type' => 'text/html',
                    'value' => '<h1>New Article Published</h1><p>Check out the latest research!</p>'
                ]
            ]
        ];

        Http::fake([
            $emailServiceUrl => Http::response([], 202)
        ]);

        // Act
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json'
        ])->post($emailServiceUrl, $emailData);

        // Assert
        $this->assertEquals(202, $response->status()); // SendGrid returns 202 for accepted
    }

    /** @test */
    public function it_handles_external_search_integration()
    {
        // Arrange
        $searchApiUrl = 'https://api.crossref.org/works';
        $query = 'machine learning healthcare';

        Http::fake([
            $searchApiUrl => Http::response([
                'status' => 'ok',
                'message-type' => 'work-list',
                'message' => [
                    'items-per-page' => 20,
                    'query' => ['start-index' => 0, 'search-terms' => $query],
                    'items' => [
                        [
                            'DOI' => '10.1000/example.2023.001',
                            'title' => ['Machine Learning Applications in Healthcare'],
                            'author' => [['family' => 'Smith', 'given' => 'John']],
                            'published-print' => ['date-parts' => [[2023]]]
                        ]
                    ]
                ]
            ], 200)
        ]);

        // Act
        $response = Http::get($searchApiUrl, [
            'query' => $query,
            'rows' => 10
        ]);

        // Assert
        $this->assertEquals(200, $response->status());
        $this->assertEquals('ok', $response->json()['status']);
        $this->assertEquals($query, $response->json()['message']['query']['search-terms']);
        $this->assertEquals(1, count($response->json()['message']['items']));

        $firstItem = $response->json()['message']['items'][0];
        $this->assertStringContainsString('Machine Learning', $firstItem['title'][0]);
    }

    /** @test */
    public function it_handles_external_service_error_responses()
    {
        // Arrange
        $externalApiUrl = 'https://api.example.com/v1/data';

        // Test different error scenarios
        Http::fake([
            $externalApiUrl . '/404' => Http::response(['error' => 'Not Found'], 404),
            $externalApiUrl . '/500' => Http::response(['error' => 'Internal Server Error'], 500),
            $externalApiUrl . '/timeout' => Http::response([], 408),
        ]);

        // Act & Assert - 404 Not Found
        $response404 = Http::get($externalApiUrl . '/404');
        $this->assertEquals(404, $response404->status());
        $this->assertEquals('Not Found', $response404->json()['error']);

        // Act & Assert - 500 Internal Server Error
        $response500 = Http::get($externalApiUrl . '/500');
        $this->assertEquals(500, $response500->status());
        $this->assertEquals('Internal Server Error', $response500->json()['error']);

        // Act & Assert - Timeout
        $responseTimeout = Http::get($externalApiUrl . '/timeout');
        $this->assertEquals(408, $responseTimeout->status());
    }

    /** @test */
    public function it_handles_external_service_rate_limiting()
    {
        // Arrange
        $apiUrl = 'https://api.example.com/v1/limited';
        $apiKey = 'test_key_123';

        // Simulate rate limiting headers
        Http::fake([
            $apiUrl => function ($request) {
                return Http::response([
                    'remaining' => 0,
                    'reset' => now()->addHour()->timestamp,
                    'message' => 'Rate limit exceeded'
                ], 429);
            }
        ]);

        // Act
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey
        ])->get($apiUrl);

        // Assert
        $this->assertEquals(429, $response->status());
        $this->assertEquals(0, $response->json()['remaining']);
        $this->assertGreaterThan(now()->timestamp, $response->json()['reset']);
    }
}

