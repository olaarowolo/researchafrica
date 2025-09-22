<?php

namespace App\Http\Controllers\Members;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DocumentController extends Controller
{
    public function openDocument(Article $article)
    {
        $pathToFile =$article->last->upload_paper->getUrl()   ;
        $mime = $article->last->upload_paper->mime_type;

        $fileName = $article->last->upload_paper->file_name;

        $headers = [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="'.$fileName.'"',
        ];

        return response()->file($pathToFile, $headers);
    }
}
