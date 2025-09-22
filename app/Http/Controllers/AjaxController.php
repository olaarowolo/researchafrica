<?php

namespace App\Http\Controllers;

use App\Models\State;
use App\Models\Article;
use App\Models\Bookmark;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ArticleCategory;
use App\Models\Comment;
use App\Models\DownloadArticle;
use PhpOffice\PhpWord\Settings;
use App\Models\PurchasedArticle;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

// use Illuminate\Http\Response;

class AjaxController extends Controller
{
    public function getStates(Request $request, $id)
    {
        $states = State::where('country_id', $id)->pluck('name', 'id');
        $option = '';

        foreach ($states as $id => $value) {
            $option .= '<option value="' . $id . '">' . $value . '</option>';
        }

        return response()->json($option);
    }

    public function keywordDelete(Request $request)
    {
        $article = Article::find($request->article_id);
        $article->article_keywords()->detach([$request->article_keyword_id]);
        return response()->json([
            'status' => 200
        ]);
    }

    public function verifyPayment(Request $request, $reference)
    {
        $purchased = new PurchasedArticle();
        $purchased->amount = $request->amount;
        $purchased->article_id = $request->article_id;
        $purchased->member_id = $request->member_id;
        $purchased->reference = $request->reference;

        $created = $purchased->save();
        if ($created) {
            return response()->json([
                'status' => 200
            ], 200);
        } else {
            return response()->json([
                'status' => 500
            ], 500);
        }
    }

    public function bookmark($article)
    {
        $check = Bookmark::where(['article_id' => $article, 'member_id' => auth('member')->id()])->first();

        if ($check) {
            $check->delete();
            return response()->json([
                'status' => 'delete'
            ], 200);
        } else {
            $bookmark = new Bookmark();
            $bookmark->article_id = $article;
            $bookmark->member_id = auth('member')->id();
            $bookmark->save();

            return response()->json([
                'status' => 'create'
            ], 200);
        }
    }

    public function getJournals($journal)
    {
        $journals = ArticleCategory::parent($journal)->pluck('category_name', 'id');
        $option = '';

        foreach ($journals as $id => $value) {
            $option .= '<option value="' . $id . '">' . $value . '</option>';
        }

        return response()->json([
            "data" => $option
        ], Response::HTTP_OK);
    }

    public function downloadPaperReview(Request $request, Article $article)
    {
        if (!auth('member')->user()) {
            return back()->with('error', 'Sorry, you have to login to download this article');
        }
        $file = $article->getLastPaperReviewDoc();
        return $file ? response()->download($file) : 'No file';
    }

    public function downloadCommentPaperReview(Request $request, Comment $comment)
    {
        if (!auth('member')->user()) {
            return back()->with('error', 'Sorry, you have to login to download this article');
        }
        $file = $comment?->correction_upload;
        if (!$file) {
            return 'No file';
        }
        $filename = $file->file_name;
        $storageFile = Storage::path('public/' . $file->id . '/' . $filename);
        return response()->download($storageFile);
    }

    public function downloadPdf(Request $request, Article $article)
    {

        if (!auth('member')->user()) {
            return back()->with('error', 'Sorry, you have to login to download this article');
        }

        $purchased = PurchasedArticle::where('article_id', $article->id)->where('member_id', auth('member')->id())->exists();
        if ($article->access_type == 2 && !$purchased) {
            return back()->with('error', 'Sorry, you have to pay to download this article');
        }

        if ($article->downloads) {
            $article->downloads->increment('download', 1);
        } else {
            DownloadArticle::create([
                'article_id' => $article->id,
                'download' => 1,
            ]);
        }

        $contents = $article->pdfPaper();
        $member = $article->member->first();
        $filename = Str::of($article->title . ' Authored By ' . $member->title . ' ' . $member->first_name)->title() . ' - ResearchAfricaPublications.pdf';
        return response()->streamDownload(function () use ($contents) {
            echo $contents;
        }, $filename);

        // // dd($article->last->upload_paper->id);
        // $file = $article->last->upload_paper;
        // $filename = $article->last->upload_paper->file_name;
        // $storageFile = Storage::path('public/' . $file->id . '/' . $filename);

        // $name = $file->uuid;

        // /* Set the PDF Engine Renderer Path */
        // $domPdfPath = base_path('vendor/dompdf/dompdf');
        // Settings::setPdfRendererPath($domPdfPath);
        // Settings::setPdfRendererName('DomPDF');

        // //Load word file
        // $Content = IOFactory::load($storageFile);

        // // $file->move($path, $name);

        // //Save it into PDF
        // $PDFWriter = IOFactory::createWriter($Content, 'PDF');
        // $PDFWriter->save(storage_path('download/' . $name . '.pdf'));

        // return response()->download(storage_path('download/' . $name . '.pdf'));
        // // unlink(storage_path('\download\\'.$name.'.pdf'));

        // return true;

    }
}
