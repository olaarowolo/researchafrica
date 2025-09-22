<?php

namespace App\Http\Controllers;

use App\Models\QuoteRequest;
use App\Mail\QuoteRequestMail;
use App\Mail\QuoteRequestClientAcknowledgementMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class QuoteRequestController extends Controller
{
    /**
     * Display the quote request form
     */
    public function create()
    {
        return view('afriscribe.afriscribe-proofread-order-form');
    }

    /**
     * Store a new quote request
     */
    public function store(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'ra_service' => 'required|string',
            'product' => 'required|string',
            'location' => 'required|string',
            'service_type' => 'required|string',
            'word_count' => 'nullable|integer|min:100',
            'addons' => 'nullable|array',
            'addons.*' => 'string',
            'referral' => 'nullable|string|max:255',
            'message' => 'nullable|string',
            'file' => 'required|file|mimes:doc,docx,pdf,txt|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please check your form and try again.');
        }

        try {
            // Handle file upload
            $filePath = null;
            $originalFilename = null;

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $originalFilename = $file->getClientOriginalName();

                // Create a unique filename
                $filename = time() . '_' . preg_replace('/[^A-Za-z0-9\-_.]/', '', $originalFilename);
                $filePath = $file->storeAs('quote-requests', $filename, 'public');
            }

            // Prepare addons data
            $addons = $request->addons ?? [];

            // Create the quote request
            $quoteRequest = QuoteRequest::create([
                'name' => $request->name,
                'email' => $request->email,
                'ra_service' => $request->ra_service,
                'product' => $request->product,
                'location' => $request->location,
                'service_type' => $request->service_type,
                'word_count' => $request->word_count,
                'addons' => !empty($addons) ? json_encode($addons) : null,
                'referral' => $request->referral,
                'message' => $request->message,
                'original_filename' => $originalFilename,
                'file_path' => $filePath,
                'status' => 'pending',
            ]);

            // Send email to admin
            try {
                Mail::to('researchfripub@gmail.com')->send(new QuoteRequestMail($quoteRequest));
            } catch (\Exception $e) {
                // Log email error but don't fail the request
                \Log::error('Failed to send admin notification email: ' . $e->getMessage());
            }

            // Send acknowledgment email to client
            try {
                Mail::to($quoteRequest->email)->send(new QuoteRequestClientAcknowledgementMail($quoteRequest));
            } catch (\Exception $e) {
                // Log email error but don't fail the request
                \Log::error('Failed to send client acknowledgment email: ' . $e->getMessage());
            }

            return redirect()->back()->with('success', 'Your quote request has been submitted successfully! Please check your email for confirmation.');

        } catch (\Exception $e) {
            \Log::error('Quote request submission failed: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'There was an error submitting your request. Please try again or contact support.');
        }
    }

    /**
     * Display all quote requests (admin)
     */
    public function index()
    {
        $quoteRequests = QuoteRequest::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.quote-requests.index', compact('quoteRequests'));
    }

    /**
     * Show a specific quote request (admin)
     */
    public function show($id)
    {
        $quoteRequest = QuoteRequest::findOrFail($id);
        return view('admin.quote-requests.show', compact('quoteRequest'));
    }

    /**
     * Update quote request status (admin)
     */
    public function updateStatus(Request $request, $id)
    {
        $quoteRequest = QuoteRequest::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,quoted,accepted,rejected,completed',
            'estimated_cost' => 'nullable|numeric|min:0',
            'estimated_turnaround' => 'nullable|string|max:255',
            'admin_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $updateData = ['status' => $request->status];

        // Add timestamp for quoted status
        if ($request->status === 'quoted') {
            $updateData['quoted_at'] = now();
        } elseif ($request->status === 'accepted') {
            $updateData['accepted_at'] = now();
        } elseif ($request->status === 'completed') {
            $updateData['completed_at'] = now();
        }

        // Add optional fields if provided
        if ($request->filled('estimated_cost')) {
            $updateData['estimated_cost'] = $request->estimated_cost;
        }
        if ($request->filled('estimated_turnaround')) {
            $updateData['estimated_turnaround'] = $request->estimated_turnaround;
        }
        if ($request->filled('admin_notes')) {
            $updateData['admin_notes'] = $request->admin_notes;
        }

        $quoteRequest->update($updateData);

        return back()->with('success', 'Quote request status updated successfully.');
    }

    /**
     * Download the attached file
     */
    public function downloadFile($id)
    {
        $quoteRequest = QuoteRequest::findOrFail($id);

        if (!$quoteRequest->file_path || !Storage::disk('public')->exists($quoteRequest->file_path)) {
            return back()->with('error', 'File not found.');
        }

        return Storage::disk('public')->download($quoteRequest->file_path, $quoteRequest->original_filename);
    }

    /**
     * Get pricing data for JavaScript
     */
    public function getPricingData()
    {
        // This would typically come from a database or config file
        // For now, returning sample pricing data
        return response()->json([
            'UK' => [
                'proofreading' => [
                    'rate' => 0.02, // £0.02 per word
                    'rush_multiplier' => 1.5,
                    'plagiarism_check' => 50.00
                ],
                'copy_editing' => [
                    'rate' => 0.03,
                    'rush_multiplier' => 1.5,
                    'plagiarism_check' => 50.00
                ],
                'substantive_editing' => [
                    'rate' => 0.05,
                    'rush_multiplier' => 1.5,
                    'plagiarism_check' => 50.00
                ]
            ],
            'Nigeria' => [
                'proofreading' => [
                    'rate' => 8.00, // ₦8 per word
                    'rush_multiplier' => 1.5,
                    'plagiarism_check' => 20000.00
                ],
                'copy_editing' => [
                    'rate' => 12.00,
                    'rush_multiplier' => 1.5,
                    'plagiarism_check' => 20000.00
                ],
                'substantive_editing' => [
                    'rate' => 20.00,
                    'rush_multiplier' => 1.5,
                    'plagiarism_check' => 20000.00
                ]
            ]
        ]);
    }
}
