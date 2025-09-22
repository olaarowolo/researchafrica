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
        return view('afriscribe.pages.welcome');
    }

    public function manuscripts()
    {
        return view('afriscribe.pages.manuscripts');
    }

    public function welcomeForm()
    {
        return view('afriscribe.welcome-form');
    }

    public function processRequest(Request $request)
    {
        try {
            // Validate the request data (mapping form fields to expected fields)
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

            // Send email to admin
            try {
                Mail::to('researchfripub@gmail.com')->send(new AfriscribeRequestMail($emailData));
            } catch (\Exception $e) {
                // Log email error but don't fail the request
                Log::error('Failed to send admin notification email: ' . $e->getMessage());
            }

            // Send acknowledgment email to client
            try {
                Mail::to($processedData['email'])->send(new AfriscribeClientAcknowledgementMail($emailData));
            } catch (\Exception $e) {
                // Log email error but don't fail the request
                Log::error('Failed to send client acknowledgment email: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Your request has been submitted successfully! An acknowledgment email has been sent to you, and our team will get back to you shortly.',
                'data' => [
                    'request_id' => $afriscribeRequest->id,
                    'name' => $processedData['name'],
                    'email' => $processedData['email'],
                    'service_type' => $processedData['service_type'],
                    'status' => $afriscribeRequest->status,
                ]
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
                'message' => 'An error occurred while processing your request',
                'error' => $e->getMessage()
            ], 500);
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
}
