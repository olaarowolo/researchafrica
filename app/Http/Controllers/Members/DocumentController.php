<?php

namespace App\Http\Controllers\Members;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DocumentController extends Controller
{
    public function openDocument(Article $article)
    {
        // Check if article has sub-articles and upload paper
        if (!$article->last || !$article->last->upload_paper) {
            abort(404, 'Document not found');
        }

        $pathToFile = $article->last->upload_paper->getPath();
        $mime = $article->last->upload_paper->mime_type;

        $fileName = $article->last->upload_paper->file_name;

        $headers = [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="'.$fileName.'"',
        ];

        return response()->file($pathToFile, $headers);
    }
}
