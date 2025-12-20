<?php

namespace App\Http\Controllers\Members;

use App\Models\SubArticle;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SubArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subArticles = SubArticle::paginate(10);
        return response()->json(['subArticles' => $subArticles]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response()->json(['message' => 'Create form']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'article_id' => 'required|exists:articles,id',
            'comment_id' => 'required|exists:comments,id',
            'abstract' => 'required|string',
        ]);

        $data = $request->all();
        $data['status'] = $data['status'] ?? '1'; // Default to pending status

        $subArticle = SubArticle::create($data);
        return response()->json(['message' => 'Sub Article created successfully.', 'subArticle' => $subArticle], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(SubArticle $subArticle)
    {
        return response()->json(['subArticle' => $subArticle]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SubArticle $subArticle)
    {
        return response()->json(['subArticle' => $subArticle, 'message' => 'Edit form']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SubArticle $subArticle)
    {
        $request->validate([
            'article_id' => 'required|exists:articles,id',
            'comment_id' => 'required|exists:comments,id',
            'abstract' => 'required|string',
        ]);

        $subArticle->update($request->all());
        return response()->json(['message' => 'Sub Article updated successfully.', 'subArticle' => $subArticle]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubArticle $subArticle)
    {
        $subArticle->delete();
        return response()->json(['message' => 'Sub Article deleted successfully.']);
    }
}
