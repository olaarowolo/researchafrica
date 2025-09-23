<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Modules\AfriScribe\Models\AfriscribeRequest;
use App\Modules\AfriScribe\Models\QuoteRequest;

class AfriScribeModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test AfriscribeRequest model fillable attributes
     */
    public function test_afriscribe_request_fillable_attributes()
    {
        $request = new AfriscribeRequest();

        $expectedFillable = [
            'name',
            'email',
            'service_type',
            'message',
            'file_path',
            'original_filename',
            'status',
            'admin_notes',
            'processed_at'
        ];

        $this->assertEquals($expectedFillable, $request->getFillable());
    }

    /**
     * Test AfriscribeRequest model casts
     */
    public function test_afriscribe_request_casts()
    {
        $request = new AfriscribeRequest();

        $expectedCasts = [
            'processed_at' => 'datetime',
        ];

        $this->assertEquals($expectedCasts, $request->getCasts());
    }

    /**
     * Test AfriscribeRequest service types
     */
    public function test_afriscribe_request_service_types()
    {
        $expectedServiceTypes = [
            AfriscribeRequest::SERVICE_PROOFREADING => 'Proofreading',
            AfriscribeRequest::SERVICE_EDITING => 'Editing',
            AfriscribeRequest::SERVICE_FORMATTING => 'Formatting',
        ];

        $this->assertEquals($expectedServiceTypes, AfriscribeRequest::getServiceTypes());
    }

    /**
     * Test AfriscribeRequest statuses
     */
    public function test_afriscribe_request_statuses()
    {
        $expectedStatuses = [
            AfriscribeRequest::STATUS_PENDING => 'Pending',
            AfriscribeRequest::STATUS_PROCESSING => 'Processing',
            AfriscribeRequest::STATUS_COMPLETED => 'Completed',
        ];

        $this->assertEquals($expectedStatuses, AfriscribeRequest::getStatuses());
    }

    /**
     * Test AfriscribeRequest isPending method
     */
    public function test_afriscribe_request_is_pending()
    {
        $request = AfriscribeRequest::factory()->create([
            'status' => AfriscribeRequest::STATUS_PENDING,
        ]);

        $this->assertTrue($request->isPending());
        $this->assertFalse($request->isProcessing());
        $this->assertFalse($request->isCompleted());
    }

    /**
     * Test AfriscribeRequest isProcessing method
     */
    public function test_afriscribe_request_is_processing()
    {
        $request = AfriscribeRequest::factory()->create([
            'status' => AfriscribeRequest::STATUS_PROCESSING,
        ]);

        $this->assertFalse($request->isPending());
        $this->assertTrue($request->isProcessing());
        $this->assertFalse($request->isCompleted());
    }

    /**
     * Test AfriscribeRequest isCompleted method
     */
    public function test_afriscribe_request_is_completed()
    {
        $request = AfriscribeRequest::factory()->create([
            'status' => AfriscribeRequest::STATUS_COMPLETED,
        ]);

        $this->assertFalse($request->isPending());
        $this->assertFalse($request->isProcessing());
        $this->assertTrue($request->isCompleted());
    }

    /**
     * Test AfriscribeRequest markAsProcessing method
     */
    public function test_afriscribe_request_mark_as_processing()
    {
        $request = AfriscribeRequest::factory()->create([
            'status' => AfriscribeRequest::STATUS_PENDING,
        ]);

        $request->markAsProcessing();

        $this->assertEquals(AfriscribeRequest::STATUS_PROCESSING, $request->status);
        $this->assertNotNull($request->processed_at);
    }

    /**
     * Test AfriscribeRequest markAsCompleted method
     */
    public function test_afriscribe_request_mark_as_completed()
    {
        $request = AfriscribeRequest::factory()->create([
            'status' => AfriscribeRequest::STATUS_PENDING,
        ]);

        $request->markAsCompleted();

        $this->assertEquals(AfriscribeRequest::STATUS_COMPLETED, $request->status);
    }

    /**
     * Test QuoteRequest model fillable attributes
     */
    public function test_quote_request_fillable_attributes()
    {
        $quoteRequest = new QuoteRequest();

        $expectedFillable = [
            'name',
            'email',
            'ra_service',
            'product',
            'location',
            'service_type',
            'word_count',
            'addons',
            'referral',
            'message',
            'original_filename',
            'file_path',
            'status',
            'estimated_cost',
            'estimated_turnaround',
            'admin_notes',
            'quoted_at',
            'accepted_at',
            'completed_at',
        ];

        $this->assertEquals($expectedFillable, $quoteRequest->getFillable());
    }

    /**
     * Test QuoteRequest model casts
     */
    public function test_quote_request_casts()
    {
        $quoteRequest = new QuoteRequest();

        $expectedCasts = [
            'addons' => 'array',
            'estimated_cost' => 'decimal:2',
            'quoted_at' => 'datetime',
            'accepted_at' => 'datetime',
            'completed_at' => 'datetime',
        ];

        $this->assertEquals($expectedCasts, $quoteRequest->getCasts());
    }

    /**
     * Test QuoteRequest formatted cost accessor
     */
    public function test_quote_request_formatted_cost_accessor()
    {
        $quoteRequest = new QuoteRequest([
            'estimated_cost' => 150.50,
        ]);

        $this->assertEquals('£150.50', $quoteRequest->formatted_cost);
    }

    /**
     * Test QuoteRequest formatted cost accessor when null
     */
    public function test_quote_request_formatted_cost_accessor_when_null()
    {
        $quoteRequest = new QuoteRequest([
            'estimated_cost' => null,
        ]);

        $this->assertEquals('Not quoted yet', $quoteRequest->formatted_cost);
    }

    /**
     * Test QuoteRequest status color accessor
     */
    public function test_quote_request_status_color_accessor()
    {
        $testCases = [
            ['status' => 'pending', 'expected' => 'warning'],
            ['status' => 'quoted', 'expected' => 'info'],
            ['status' => 'accepted', 'expected' => 'success'],
            ['status' => 'rejected', 'expected' => 'danger'],
            ['status' => 'completed', 'expected' => 'primary'],
            ['status' => 'unknown', 'expected' => 'secondary'],
        ];

        foreach ($testCases as $testCase) {
            $quoteRequest = new QuoteRequest([
                'status' => $testCase['status'],
            ]);

            $this->assertEquals($testCase['expected'], $quoteRequest->status_color);
        }
    }

    /**
     * Test QuoteRequest pending scope
     */
    public function test_quote_request_pending_scope()
    {
        QuoteRequest::factory()->create(['status' => 'pending']);
        QuoteRequest::factory()->create(['status' => 'quoted']);
        QuoteRequest::factory()->create(['status' => 'accepted']);

        $pendingRequests = QuoteRequest::pending()->get();

        $this->assertCount(1, $pendingRequests);
        $this->assertEquals('pending', $pendingRequests->first()->status);
    }

    /**
     * Test QuoteRequest active scope
     */
    public function test_quote_request_active_scope()
    {
        QuoteRequest::factory()->create(['status' => 'pending']);
        QuoteRequest::factory()->create(['status' => 'quoted']);
        QuoteRequest::factory()->create(['status' => 'accepted']);
        QuoteRequest::factory()->create(['status' => 'rejected']);
        QuoteRequest::factory()->create(['status' => 'completed']);

        $activeRequests = QuoteRequest::active()->get();

        $this->assertCount(3, $activeRequests);
        $this->assertNotContains('rejected', $activeRequests->pluck('status')->toArray());
        $this->assertNotContains('completed', $activeRequests->pluck('status')->toArray());
    }

    /**
     * Test QuoteRequest model relationships
     */
    public function test_quote_request_model_relationships()
    {
        // Since there are no explicit relationships defined in the model,
        // this test ensures the model can be instantiated and used
        $quoteRequest = new QuoteRequest();

        $this->assertInstanceOf(QuoteRequest::class, $quoteRequest);
    }

    /**
     * Test AfriscribeRequest model relationships
     */
    public function test_afriscribe_request_model_relationships()
    {
        // Since there are no explicit relationships defined in the model,
        // this test ensures the model can be instantiated and used
        $afriscribeRequest = new AfriscribeRequest();

        $this->assertInstanceOf(AfriscribeRequest::class, $afriscribeRequest);
    }
}
