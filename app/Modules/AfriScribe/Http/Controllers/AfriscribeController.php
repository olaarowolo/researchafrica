<?php

namespace App\Modules\AfriScribe\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Modules\AfriScribe\Mail\AfriscribeRequestMail;
use App\Modules\AfriScribe\Mail\AfriscribeClientAcknowledgementMail;
use App\Modules\AfriScribe\Models\AfriscribeRequest;

class AfriscribeController extends Controller
{
    public function welcome()
    {
        // Fetch dashboard data
        $totalRequests = AfriscribeRequest::count();
        $pendingRequests = AfriscribeRequest::where('status', AfriscribeRequest::STATUS_PENDING)->count();
        $processingRequests = AfriscribeRequest::where('status', AfriscribeRequest::STATUS_PROCESSING)->count();
        $completedRequests = AfriscribeRequest::where('status', AfriscribeRequest::STATUS_COMPLETED)->count();
        $recentRequests = AfriscribeRequest::orderBy('created_at', 'desc')->limit(5)->get();

        return view('afriscribe.pages.welcome', compact(
            'totalRequests',
            'pendingRequests',
            'processingRequests',
            'completedRequests',
            'recentRequests'
        ));
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // Manually start the session if not started
        if (!$request->hasSession()) {
            $request->setLaravelSession(session());
        }

        if (auth()->guard('web')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('afriscribe.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput();
    }

    public function manuscripts()
    {
        return view('afriscribe.pages.manuscripts');
    }
    public function proofreading()
    {
        return view('afriscribe.pages.proofreading');
    }

    public function about()
    {
        return view('afriscribe.pages.about');
    }

    public function welcomeForm()
    {
        return view('afriscribe.welcome-form');
    }

    public function processRequest(Request $request)
    {
        try {
            // Check if this is a proofreading form submission
            $isProofreadingForm = $request->input('form_type') === 'proofreading_quote';

            if ($isProofreadingForm) {
                // Validation for proofreading form
                $validatedData = $request->validate([
                    'name' => 'required|string|max:255',
                    'email' => 'required|email|max:255',
                    'institution' => 'nullable|string|max:255',
                    'word_count' => 'required|integer|min:1',
                    'turnaround_time' => 'required|string|in:standard,advanced,express',
                    'details' => 'nullable|string',
                    'form_type' => 'required|string|in:proofreading_quote',
                ]);

                // Map proofreading form fields to expected field names
                $processedData = [
                    'name' => $validatedData['name'],
                    'email' => $validatedData['email'],
                    'service_type' => 'proofreading',
                    'message' => $validatedData['details'] ?: 'Word count: ' . $validatedData['word_count'] . ', Turnaround: ' . $validatedData['turnaround_time'] . ($validatedData['institution'] ? ', Institution: ' . $validatedData['institution'] : ''),
                    'document' => null,
                ];
            } else {
                // Validation for regular form
                $validatedData = $request->validate([
                    'name' => 'required|string|max:255',
                    'email' => 'required|email|max:255',
                    'service' => 'required|string|in:' . implode(',', array_keys(AfriscribeRequest::getServiceTypes())),
                    'details' => 'required|string',
                    'file' => 'nullable|file|mimes:pdf,doc,docx|max:10240', // 10MB max
                ]);

                // Map form fields to expected field names
                $processedData = [
                    'name' => $validatedData['name'],
                    'email' => $validatedData['email'],
                    'service_type' => $validatedData['service'],
                    'message' => $validatedData['details'],
                    'document' => $validatedData['file'] ?? null,
                ];
            }

            $filePath = null;
            $originalFilename = null;

            // Handle file upload if present
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $originalFilename = $file->getClientOriginalName();

                // Create a unique filename
                $filename = time() . '_' . preg_replace('/[^A-Za-z0-9\-_.]/', '', $originalFilename);
                $filePath = $file->storeAs('afriscribe_uploads', $filename, 'public');
            }

            // Save to database
            $afriscribeRequest = AfriscribeRequest::create([
                'name' => $processedData['name'],
                'email' => $processedData['email'],
                'service_type' => $processedData['service_type'],
                'message' => $processedData['message'],
                'file_path' => $filePath,
                'original_filename' => $originalFilename,
                'status' => AfriscribeRequest::STATUS_PENDING,
            ]);

            // Prepare email data
            $emailData = [
                'name' => $processedData['name'],
                'email' => $processedData['email'],
                'service_type' => $processedData['service_type'],
                'message' => $processedData['message'],
                'file_path' => $filePath,
                'original_filename' => $originalFilename,
                'request_id' => $afriscribeRequest->id,
            ];

            // Send email to admin with custom sender name for proofreading requests
            try {
                $senderName = $processedData['service_type'] === 'proofreading'
                    ? 'AfriScribe Proofreading Service'
                    : 'AfriScribe';

                // The Mailable likely uses its constructor arguments to set the 'from' header.
                // The original code was incorrectly passing the recipient's email as the sender's email.
                // We now use the 'from' address configured for the 'afriscribe' mailer.
                $fromAddress = config('mail.mailers.afriscribe.from.address');
                $email = new AfriscribeRequestMail($emailData, $senderName, $fromAddress);

                Mail::mailer('afriscribe')->to('researchafrpub@gmail.com')->send($email);
                Log::info('Admin email sent successfully to: researchafripub@gmail.com');
            } catch (\Exception $e) {
                // Log email error but don't fail the request
                Log::error('Failed to send admin notification email: ' . $e->getMessage());
                Log::error('Stack trace: ' . $e->getTraceAsString());
            }

            // Send acknowledgment email to client
            try {
                // Use the 'afriscribe' mailer for consistency and to add CC/BCC recipients.
                Mail::mailer('afriscribe')->to($processedData['email'])->cc('ola@researchafrica.pub')->bcc('olasunkanmiarowolo@gmail.com')->send(new AfriscribeClientAcknowledgementMail($emailData));
                Log::info('Client acknowledgment email sent successfully to: ' . $processedData['email']);
            } catch (\Exception $e) {
                // Log email error but don't fail the request
                Log::error('Failed to send client acknowledgment email: ' . $e->getMessage());
                Log::error('Stack trace: ' . $e->getTraceAsString());
            }

            return view('afriscribe.success', [
                'message'     => 'Your request has been submitted successfully! An acknowledgment email has been sent to you, and our team will get back to you shortly.',
                'redirectUrl' => '/afriscribe/home',
                'countdown'   => 5,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            Log::error('An error occurred while processing your request: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()
                ->withInput()
                ->with('error', 'An error occurred while processing your request. Please try again or contact support.');
        }
    }

    /**
     * Get all AfriScribe requests (for admin panel)
     */
    public function getRequests()
    {
        $requests = AfriscribeRequest::orderBy('created_at', 'desc')->get();
        return response()->json($requests);
    }

    /**
     * Update request status (for admin panel)
     */
    public function updateRequestStatus(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'status' => 'required|string|in:' . implode(',', array_keys(AfriscribeRequest::getStatuses())),
                'admin_notes' => 'nullable|string',
            ]);

            $afriscribeRequest = AfriscribeRequest::findOrFail($id);
            $afriscribeRequest->update($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Request status updated successfully',
                'data' => $afriscribeRequest
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Dashboard page
     */
    public function dashboard()
    {
        // Fetch dashboard data
        $totalRequests = AfriscribeRequest::count();
        $pendingRequests = AfriscribeRequest::where('status', AfriscribeRequest::STATUS_PENDING)->count();
        $processingRequests = AfriscribeRequest::where('status', AfriscribeRequest::STATUS_PROCESSING)->count();
        $completedRequests = AfriscribeRequest::where('status', AfriscribeRequest::STATUS_COMPLETED)->count();
        $recentRequests = AfriscribeRequest::orderBy('created_at', 'desc')->limit(10)->get();

        return view('afriscribe.pages.dashboard', compact(
            'totalRequests',
            'pendingRequests',
            'processingRequests',
            'completedRequests',
            'recentRequests'
        ));
    }

    /**
     * Insights page
     */
    public function insights()
    {
        return view('afriscribe.pages.insights');
    }

    /**
     * Connect page
     */
    public function connect()
    {
        return view('afriscribe.pages.connect');
    }

    /**
     * Archive page
     */
    public function archive()
    {
        return view('afriscribe.pages.archive');
    }

    /**
     * Editor page
     */
    public function editor()
    {
        return view('afriscribe.pages.editor');
    }
}
