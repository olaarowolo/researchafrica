<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class PublisherGalleyController extends Controller
        // Show form for uploading final version (after approval)
        public function showFinalUploadForm(Article $article)
        {
            $this->authorize('update', $article);
            if ($article->galley_proof_status !== 'approved') {
                return redirect()->back()->with('error', 'Galley proof must be approved by author before uploading final version.');
            }
            return view('publisher.final-upload', compact('article'));
        }

        // Handle final version upload
        public function uploadFinalVersion(Request $request, Article $article)
        {
            $this->authorize('update', $article);
            if ($article->galley_proof_status !== 'approved') {
                return redirect()->back()->with('error', 'Galley proof must be approved by author before uploading final version.');
            }
            $request->validate([
                'final_version' => 'required|file|mimes:pdf|max:20480',
            ]);
            $file = $request->file('final_version');
            $path = $file->store('final_versions', 'public');
            $article->final_version_path = $path;
            $article->save();
            return redirect()->back()->with('success', 'Final version uploaded successfully.');
        }
    // Author galley proof approval view/action
    public function authorGalleyApproval(Request $request, Article $article)
    {
        $corresponding = trim($article->corresponding_authors);
        $expectedToken = sha1($article->id . $corresponding . $article->updated_at);
        $token = $request->query('token');
        if ($token !== $expectedToken) {
            abort(403, 'Invalid or expired approval link.');
        }
        if ($request->isMethod('post')) {
            $action = $request->input('action');
            if ($action === 'approve') {
                $article->galley_proof_status = 'approved';
                $article->save();
                return redirect()->back()->with('success', 'Galley proof approved.');
            } elseif ($action === 'reject') {
                $article->galley_proof_status = 'rejected';
                $article->save();
                return redirect()->back()->with('error', 'Galley proof rejected.');
            }
        }
        return view('author.galley-approval', compact('article', 'token'));
    }
{
    // Show upload form
    public function showUploadForm(Article $article)
    {
        $this->authorize('update', $article); // Optional: add policy for publisher
        return view('publisher.galley-upload', compact('article'));
    }

    // Handle galley proof upload
    public function uploadGalley(Request $request, Article $article)
    {
        $this->authorize('update', $article); // Optional: add policy for publisher
        $request->validate([
            'galley_proof' => 'required|file|mimes:pdf|max:20480', // 20MB max, PDF only
        ]);
        $file = $request->file('galley_proof');
        $path = $file->store('galley_proofs', 'public');
        $article->galley_proof_path = $path;
        $article->galley_proof_status = 'pending';
        $article->save();

        // Notify corresponding author for approval if email is present
        $corresponding = trim($article->corresponding_authors);
        if (filter_var($corresponding, FILTER_VALIDATE_EMAIL)) {
            $approveUrl = route('author.galley.approval', [$article->id, 'token' => sha1($article->id . $corresponding . $article->updated_at)]);
            Mail::raw(
                "Dear Author,\n\nA galley proof for your article '{$article->title}' has been uploaded. Please review and approve it using the following link:\n\n$approveUrl\n\nThank you.",
                function ($message) use ($corresponding, $article) {
                    $message->to($corresponding)
                        ->subject('Galley Proof Approval Needed: ' . $article->title);
                }
            );
        }
        return redirect()->back()->with('success', 'Galley proof uploaded successfully and sent for author approval.');
    }
}
