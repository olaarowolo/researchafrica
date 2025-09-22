<?php

namespace App\Http\Controllers\Members;

use Illuminate\Http\Request;
use App\Models\ArticleCategory;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    public function journal($id)
    {
        $journal = ArticleCategory::find($id);
        if(is_null($journal)){
            return back()->with('error', 'Journal does not exist');
        }
        return view('member.journal.index', compact('journal'));
    }
}
