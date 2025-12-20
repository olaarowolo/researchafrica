<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Article;
use App\Models\Member;
use App\Models\Subscription;
use App\Models\Comment;
use App\Models\MemberType;
use App\Models\ViewArticle;
use App\Models\DownloadArticle;
use Carbon\Carbon;

class HomeController
{
    public function index()
    {
        $totalUsers = User::count();
        $totalArticles = Article::count();
        $totalMembers = Member::count();
        $totalSubscriptions = Subscription::count();
        $totalComments = Comment::count();

        // Get member counts by type
        $memberTypes = MemberType::withCount('members')->get();

        // Recent activities (last 10 activities)
        $recentMembers = Member::latest()->take(5)->get();
        $recentArticles = Article::latest()->take(5)->get();
        $recentComments = Comment::latest()->take(5)->get();

        // Article status counts
        $pendingArticles = Article::where('article_status', 1)->count(); // 1 = Pending
        $publishedArticles = Article::where('article_status', 3)->count(); // 3 = Published
        $draftArticles = Article::where('article_status', 2)->count(); // 2 = Reviewing (using as draft equivalent)

        // New registrations (last 30 days)
        $newMembersThisMonth = Member::where('created_at', '>=', Carbon::now()->subDays(30))->count();
        $newUsersThisMonth = User::where('created_at', '>=', Carbon::now()->subDays(30))->count();

        // View and download counts
        $totalViews = ViewArticle::sum('view');
        $totalDownloads = DownloadArticle::sum('download');

        // Access type counts (only count published articles)
        $openAccessArticles = Article::where('access_type', 1)->where('article_status', 3)->count(); // 1 = Open Access, 3 = Published
        $closedAccessArticles = Article::where('access_type', 2)->where('article_status', 3)->count(); // 2 = Close Access, 3 = Published

        return view('home', compact(
            'totalUsers', 'totalArticles', 'totalMembers', 'totalSubscriptions', 'totalComments', 'memberTypes',
            'recentMembers', 'recentArticles', 'recentComments',
            'pendingArticles', 'publishedArticles', 'draftArticles',
            'newMembersThisMonth', 'newUsersThisMonth',
            'totalViews', 'totalDownloads',
            'openAccessArticles', 'closedAccessArticles'
        ));
    }



    public function logout(Request $request)
    {
        # code...
        Auth::logout();
        $request->session()->flush();

        return redirect()->route('admin.login');
    }
}
