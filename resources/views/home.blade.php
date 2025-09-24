@extends('layouts.admin')
@section('styles')
<style>
.modern-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.08) !important;
}

.activity-item:hover {
    background: #f8f9fa !important;
    border-left-width: 3px !important;
}

.status-item:hover {
    transform: scale(1.02);
}

.quick-action-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
}

.member-type-item:hover {
    background: #f8f9fa !important;
    transform: translateX(2px);
}

.gradient-text {
    background: linear-gradient(135deg, #495057 0%, #6c757d 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.progress-bar {
    transition: width 0.6s ease;
}

.icon-container {
    transition: transform 0.3s ease;
}

.icon-container:hover {
    transform: scale(1.05);
}

.activity-feed {
    max-height: 400px;
    overflow-y: auto;
}

.activity-feed::-webkit-scrollbar {
    width: 4px;
}

.activity-feed::-webkit-scrollbar-track {
    background: #f8f9fa;
    border-radius: 10px;
}

.activity-feed::-webkit-scrollbar-thumb {
    background: #dee2e6;
    border-radius: 10px;
}

.activity-feed::-webkit-scrollbar-thumb:hover {
    background: #ced4da;
}

.member-types-list {
    max-height: 400px;
    overflow-y: auto;
}

.member-types-list::-webkit-scrollbar {
    width: 4px;
}

.member-types-list::-webkit-scrollbar-track {
    background: #f8f9fa;
    border-radius: 10px;
}

.member-types-list::-webkit-scrollbar-thumb {
    background: #dee2e6;
    border-radius: 10px;
}

.member-types-list::-webkit-scrollbar-thumb:hover {
    background: #ced4da;
}

.quick-actions-grid .quick-action-btn {
    opacity: 0.95;
    transition: all 0.3s ease;
}

.quick-actions-grid .quick-action-btn:hover {
    opacity: 1;
    transform: translateY(-2px);
}

.progress-container .progress {
    background: #f8f9fa;
    border-radius: 10px;
    overflow: hidden;
}

.alert-modern {
    border-left: 3px solid #28a745;
    background: #f8f9fa;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(40, 167, 69, 0.05);
}
</style>
@endsection

@section('content')
<div class="content">
    <!-- Modern Header Section -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="mb-2 gradient-text">Dashboard Overview</h1>
                    <p class="text-muted">Welcome back! Here's what's happening with your research platform.</p>
                </div>
                <div class="btn-group">
                    <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-calendar mr-2"></i>Last 30 Days
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Last 7 Days</a></li>
                        <li><a class="dropdown-item" href="#">Last 30 Days</a></li>
                        <li><a class="dropdown-item" href="#">Last 90 Days</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @if(session('status'))
        <div class="row">
            <div class="col-lg-12">
                <div class="alert alert-success alert-dismissible fade show alert-modern" role="alert">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        </div>
    @endif

    <!-- Modern Stats Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
            <div class="card h-100 modern-card" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); border: none; border-radius: 12px; box-shadow: 0 4px 15px rgba(227, 242, 253, 0.3); transition: all 0.3s ease;">
                <div class="card-body" style="color: #1565c0;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="text-uppercase font-weight-bold mb-2" style="font-size: 0.85rem; opacity: 0.8;">Total Users</div>
                            <div class="h2 mb-0 font-weight-bold">{{ number_format($totalUsers) }}</div>
                            <small class="opacity-75">
                                @if($newUsersThisMonth > 0)
                                    <i class="fas fa-arrow-up text-success mr-1"></i>+{{ $newUsersThisMonth }} this month
                                @else
                                    <i class="fas fa-minus text-warning mr-1"></i>No new users
                                @endif
                            </small>
                        </div>
                        <div class="icon-container" style="background: rgba(21, 101, 192, 0.1); border-radius: 10px; padding: 10px;">
                            <i class="fas fa-users fa-2x" style="color: #1565c0;"></i>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 3px; background: rgba(21, 101, 192, 0.1);">
                        <div class="progress-bar" style="background: #1565c0; width: {{ min(100, ($totalUsers / 100) * 100) }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
            <div class="card h-100 modern-card" style="background: linear-gradient(135deg, #e8f5e8 0%, #c8e6c9 100%); border: none; border-radius: 12px; box-shadow: 0 4px 15px rgba(232, 245, 232, 0.3); transition: all 0.3s ease;">
                <div class="card-body" style="color: #2e7d32;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="text-uppercase font-weight-bold mb-2" style="font-size: 0.85rem; opacity: 0.8;">Total Articles</div>
                            <div class="h2 mb-0 font-weight-bold">{{ number_format($totalArticles) }}</div>
                            <small class="opacity-75">
                                @if($recentArticles->count() > 0)
                                    <i class="fas fa-arrow-up text-success mr-1"></i>{{ $recentArticles->count() }} recent
                                @else
                                    <i class="fas fa-minus text-warning mr-1"></i>No recent articles
                                @endif
                            </small>
                        </div>
                        <div class="icon-container" style="background: rgba(46, 125, 50, 0.1); border-radius: 10px; padding: 10px;">
                            <i class="fas fa-newspaper fa-2x" style="color: #2e7d32;"></i>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 3px; background: rgba(46, 125, 50, 0.1);">
                        <div class="progress-bar" style="background: #2e7d32; width: {{ min(100, ($totalArticles / 50) * 100) }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
            <div class="card h-100 modern-card" style="background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%); border: none; border-radius: 12px; box-shadow: 0 4px 15px rgba(255, 243, 224, 0.3); transition: all 0.3s ease;">
                <div class="card-body" style="color: #ef6c00;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="text-uppercase font-weight-bold mb-2" style="font-size: 0.85rem; opacity: 0.8;">Total Members</div>
                            <div class="h2 mb-0 font-weight-bold">{{ number_format($totalMembers) }}</div>
                            <small class="opacity-75">
                                @if($newMembersThisMonth > 0)
                                    <i class="fas fa-arrow-up text-success mr-1"></i>+{{ $newMembersThisMonth }} this month
                                @else
                                    <i class="fas fa-minus text-warning mr-1"></i>No new members
                                @endif
                            </small>
                        </div>
                        <div class="icon-container" style="background: rgba(239, 108, 0, 0.1); border-radius: 10px; padding: 10px;">
                            <i class="fas fa-user-friends fa-2x" style="color: #ef6c00;"></i>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 3px; background: rgba(239, 108, 0, 0.1);">
                        <div class="progress-bar" style="background: #ef6c00; width: {{ min(100, ($totalMembers / 20) * 100) }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
            <div class="card h-100 modern-card" style="background: linear-gradient(135deg, #f3e5f5 0%, #e1bee7 100%); border: none; border-radius: 12px; box-shadow: 0 4px 15px rgba(243, 229, 245, 0.3); transition: all 0.3s ease;">
                <div class="card-body" style="color: #7b1fa2;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="text-uppercase font-weight-bold mb-2" style="font-size: 0.85rem; opacity: 0.8;">Total Subscriptions</div>
                            <div class="h2 mb-0 font-weight-bold">{{ number_format($totalSubscriptions) }}</div>
                            <small class="opacity-75">
                                <i class="fas fa-chart-line text-success mr-1"></i>Active subscriptions
                            </small>
                        </div>
                        <div class="icon-container" style="background: rgba(123, 31, 162, 0.1); border-radius: 10px; padding: 10px;">
                            <i class="fas fa-credit-card fa-2x" style="color: #7b1fa2;"></i>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 3px; background: rgba(123, 31, 162, 0.1);">
                        <div class="progress-bar" style="background: #7b1fa2; width: {{ min(100, ($totalSubscriptions / 10) * 100) }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
            <div class="card h-100 modern-card" style="background: linear-gradient(135deg, #e0f2f1 0%, #b2dfdb 100%); border: none; border-radius: 12px; box-shadow: 0 4px 15px rgba(224, 242, 241, 0.3); transition: all 0.3s ease;">
                <div class="card-body" style="color: #00695c;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="text-uppercase font-weight-bold mb-2" style="font-size: 0.85rem; opacity: 0.8;">Total Views</div>
                            <div class="h2 mb-0 font-weight-bold">{{ number_format($totalViews) }}</div>
                            <small class="opacity-75">
                                <i class="fas fa-eye text-success mr-1"></i>Article views
                            </small>
                        </div>
                        <div class="icon-container" style="background: rgba(0, 105, 92, 0.1); border-radius: 10px; padding: 10px;">
                            <i class="fas fa-eye fa-2x" style="color: #00695c;"></i>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 3px; background: rgba(0, 105, 92, 0.1);">
                        <div class="progress-bar" style="background: #00695c; width: {{ min(100, ($totalViews / 100) * 100) }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
            <div class="card h-100 modern-card" style="background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%); border: none; border-radius: 12px; box-shadow: 0 4px 15px rgba(255, 243, 224, 0.3); transition: all 0.3s ease;">
                <div class="card-body" style="color: #ef6c00;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="text-uppercase font-weight-bold mb-2" style="font-size: 0.85rem; opacity: 0.8;">Open Access</div>
                            <div class="h2 mb-0 font-weight-bold">{{ number_format($openAccessArticles) }}</div>
                            <small class="opacity-75">
                                <i class="fas fa-unlock text-success mr-1"></i>Freely available
                            </small>
                        </div>
                        <div class="icon-container" style="background: rgba(239, 108, 0, 0.1); border-radius: 10px; padding: 10px;">
                            <i class="fas fa-unlock fa-2x" style="color: #ef6c00;"></i>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 3px; background: rgba(239, 108, 0, 0.1);">
                        <div class="progress-bar" style="background: #ef6c00; width: {{ min(100, ($openAccessArticles / 10) * 100) }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
            <div class="card h-100 modern-card" style="background: linear-gradient(135deg, #f3e5f5 0%, #e1bee7 100%); border: none; border-radius: 12px; box-shadow: 0 4px 15px rgba(243, 229, 245, 0.3); transition: all 0.3s ease;">
                <div class="card-body" style="color: #7b1fa2;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="text-uppercase font-weight-bold mb-2" style="font-size: 0.85rem; opacity: 0.8;">Total Downloads</div>
                            <div class="h2 mb-0 font-weight-bold">{{ number_format($totalDownloads) }}</div>
                            <small class="opacity-75">
                                <i class="fas fa-download text-success mr-1"></i>Article downloads
                            </small>
                        </div>
                        <div class="icon-container" style="background: rgba(123, 31, 162, 0.1); border-radius: 10px; padding: 10px;">
                            <i class="fas fa-download fa-2x" style="color: #7b1fa2;"></i>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 3px; background: rgba(123, 31, 162, 0.1);">
                        <div class="progress-bar" style="background: #7b1fa2; width: {{ min(100, ($totalDownloads / 50) * 100) }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
            <div class="card h-100 modern-card" style="background: linear-gradient(135deg, #e0f2f1 0%, #b2dfdb 100%); border: none; border-radius: 12px; box-shadow: 0 4px 15px rgba(224, 242, 241, 0.3); transition: all 0.3s ease;">
                <div class="card-body" style="color: #00695c;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="text-uppercase font-weight-bold mb-2" style="font-size: 0.85rem; opacity: 0.8;">Closed Access</div>
                            <div class="h2 mb-0 font-weight-bold">{{ number_format($closedAccessArticles) }}</div>
                            <small class="opacity-75">
                                <i class="fas fa-lock text-success mr-1"></i>Restricted access
                            </small>
                        </div>
                        <div class="icon-container" style="background: rgba(0, 105, 92, 0.1); border-radius: 10px; padding: 10px;">
                            <i class="fas fa-lock fa-2x" style="color: #00695c;"></i>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 3px; background: rgba(0, 105, 92, 0.1);">
                        <div class="progress-bar" style="background: #00695c; width: {{ min(100, ($closedAccessArticles / 10) * 100) }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Interactive Content Sections -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card h-100 modern-card" style="border: none; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); transition: all 0.3s ease;">
                <div class="card-header" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); color: #1565c0; border-radius: 12px 12px 0 0 !important;">
                    <h5 class="mb-0">
                        <i class="fas fa-stream mr-2"></i>Recent Activity
                    </h5>
                </div>
                <div class="card-body">
                    <div class="activity-feed">
                        @if($recentMembers->count() > 0)
                            @foreach($recentMembers as $member)
                                <div class="activity-item" style="padding: 12px; margin-bottom: 8px; border-left: 3px solid #28a745; background: #f8f9fa; border-radius: 0 8px 8px 0; transition: all 0.2s ease;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-user-plus text-success mr-2"></i>
                                            <strong>New member:</strong> {{ $member->first_name }} {{ $member->last_name }}
                                        </div>
                                        <small class="text-muted">{{ $member->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            @endforeach
                        @endif

                        @if($recentArticles->count() > 0)
                            @foreach($recentArticles as $article)
                                <div class="activity-item" style="padding: 12px; margin-bottom: 8px; border-left: 3px solid #17a2b8; background: #f8f9fa; border-radius: 0 8px 8px 0; transition: all 0.2s ease;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-newspaper text-info mr-2"></i>
                                            <strong>New article:</strong> {{ Str::limit($article->title, 30) }}
                                        </div>
                                        <small class="text-muted">{{ $article->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            @endforeach
                        @endif

                        @if($recentComments->count() > 0)
                            @foreach($recentComments as $comment)
                                <div class="activity-item" style="padding: 12px; margin-bottom: 8px; border-left: 3px solid #ffc107; background: #f8f9fa; border-radius: 0 8px 8px 0; transition: all 0.2s ease;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-comment text-warning mr-2"></i>
                                            <strong>New comment</strong> on article
                                        </div>
                                        <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    @if($recentMembers->count() == 0 && $recentArticles->count() == 0 && $recentComments->count() == 0)
                        <div class="text-center text-muted" style="padding: 40px;">
                            <i class="fas fa-inbox fa-3x mb-3" style="opacity: 0.5;"></i>
                            <p>No recent activity found.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card h-100 modern-card" style="border: none; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); transition: all 0.3s ease;">
                <div class="card-header" style="background: linear-gradient(135deg, #e8f5e8 0%, #c8e6c9 100%); color: #2e7d32; border-radius: 12px 12px 0 0 !important;">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie mr-2"></i>Article Status Overview
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center mb-4">
                        <div class="col-4">
                            <div class="status-item" style="padding: 20px; border-radius: 12px; background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%); transition: all 0.2s ease;">
                                <h3 class="text-warning mb-1">{{ $pendingArticles }}</h3>
                                <small class="text-muted font-weight-bold">Pending</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="status-item" style="padding: 20px; border-radius: 12px; background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%); transition: all 0.2s ease;">
                                <h3 class="text-info mb-1">{{ $draftArticles }}</h3>
                                <small class="text-muted font-weight-bold">Reviewing</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="status-item" style="padding: 20px; border-radius: 12px; background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); transition: all 0.2s ease;">
                                <h3 class="text-success mb-1">{{ $publishedArticles }}</h3>
                                <small class="text-muted font-weight-bold">Published</small>
                            </div>
                        </div>
                    </div>

                    <!-- Progress visualization -->
                    <div class="progress-container" style="margin-bottom: 20px;">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-sm font-weight-bold">Overall Progress</span>
                            <span class="text-sm text-muted">{{ round((($publishedArticles + $draftArticles) / max($totalArticles, 1)) * 100, 1) }}%</span>
                        </div>
                        <div class="progress" style="height: 8px; border-radius: 10px;">
                            <div class="progress-bar bg-warning" style="width: {{ ($pendingArticles / max($totalArticles, 1)) * 100 }}%"></div>
                            <div class="progress-bar bg-info" style="width: {{ ($draftArticles / max($totalArticles, 1)) * 100 }}%"></div>
                            <div class="progress-bar bg-success" style="width: {{ ($publishedArticles / max($totalArticles, 1)) * 100 }}%"></div>
                        </div>
                    </div>

                    <div class="text-center">
                        <a href="{{ route('admin.articles.index') }}" class="btn btn-primary" style="border-radius: 25px; padding: 8px 24px;">
                            <i class="fas fa-eye mr-2"></i>View All Articles
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card h-100 modern-card" style="border: none; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); transition: all 0.3s ease;">
                <div class="card-header" style="background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%); color: #ef6c00; border-radius: 12px 12px 0 0 !important;">
                    <h5 class="mb-0">
                        <i class="fas fa-users-cog mr-2"></i>Member Summary by Type
                    </h5>
                </div>
                <div class="card-body">
                    @if($memberTypes->count() > 0)
                        <div class="member-types-list">
                            @foreach($memberTypes as $type)
                                <div class="member-type-item" style="display: flex; justify-content: space-between; align-items: center; padding: 12px; margin-bottom: 8px; background: #f8f9fa; border-radius: 8px; transition: all 0.2s ease;">
                                    <div class="d-flex align-items-center">
                                        <div class="member-type-icon mr-3" style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white;">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <span class="font-weight-bold">{{ $type->name }}</span>
                                    </div>
                                    <span class="badge badge-primary badge-lg" style="font-size: 1rem; padding: 8px 16px; border-radius: 20px;">{{ $type->members_count }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted" style="padding: 40px;">
                            <i class="fas fa-users fa-3x mb-3" style="opacity: 0.5;"></i>
                            <p>No member types found.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card h-100 modern-card" style="border: none; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); transition: all 0.3s ease;">
                <div class="card-header" style="background: linear-gradient(135deg, #f3e5f5 0%, #e1bee7 100%); color: #7b1fa2; border-radius: 12px 12px 0 0 !important;">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt mr-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="quick-actions-grid">
                        <a href="{{ route('admin.articles.create') }}" class="quick-action-btn" style="display: block; padding: 14px; margin-bottom: 10px; background: linear-gradient(135deg, #e8f5e8 0%, #c8e6c9 100%); color: #2e7d32; text-decoration: none; border-radius: 10px; text-align: center; transition: all 0.2s ease; box-shadow: 0 2px 8px rgba(46, 125, 50, 0.15);">
                            <i class="fas fa-plus fa-2x mb-2"></i>
                            <div class="font-weight-bold">Create New Article</div>
                            <small>Add research papers and publications</small>
                        </a>

                        <a href="{{ route('admin.members.create') }}" class="quick-action-btn" style="display: block; padding: 14px; margin-bottom: 10px; background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); color: #1565c0; text-decoration: none; border-radius: 10px; text-align: center; transition: all 0.2s ease; box-shadow: 0 2px 8px rgba(21, 101, 192, 0.15);">
                            <i class="fas fa-user-plus fa-2x mb-2"></i>
                            <div class="font-weight-bold">Add New Member</div>
                            <small>Register researchers and authors</small>
                        </a>

                        <a href="{{ route('admin.articles.index') }}?status=pending" class="quick-action-btn" style="display: block; padding: 14px; margin-bottom: 10px; background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%); color: #ef6c00; text-decoration: none; border-radius: 10px; text-align: center; transition: all 0.2s ease; box-shadow: 0 2px 8px rgba(239, 108, 0, 0.15);">
                            <i class="fas fa-eye fa-2x mb-2"></i>
                            <div class="font-weight-bold">Review Pending Articles</div>
                            <small>Moderate submitted content</small>
                        </a>

                        <a href="#" class="quick-action-btn" style="display: block; padding: 14px; background: linear-gradient(135deg, #f3e5f5 0%, #e1bee7 100%); color: #7b1fa2; text-decoration: none; border-radius: 10px; text-align: center; transition: all 0.2s ease; box-shadow: 0 2px 8px rgba(123, 31, 162, 0.15);">
                            <i class="fas fa-chart-bar fa-2x mb-2"></i>
                            <div class="font-weight-bold">View Analytics</div>
                            <small>Platform insights and reports</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
@parent
<script>
$(document).ready(function() {
    // Animate counters on page load
    $('.h2').each(function() {
        var $this = $(this);
        var countTo = $this.text().replace(/,/g, '');

        $({ countNum: 0 }).animate({
            countNum: countTo
        }, {
            duration: 2000,
            easing: 'swing',
            step: function() {
                $this.text(Math.floor(this.countNum).toLocaleString());
            },
            complete: function() {
                $this.text(this.countNum.toLocaleString());
            }
        });
    });

    // Add click animations to quick action buttons
    $('.quick-action-btn').click(function(e) {
        e.preventDefault();

        var $this = $(this);
        $this.css('transform', 'scale(0.95)');

        setTimeout(function() {
            $this.css('transform', '');
            // Add your navigation logic here
            window.location.href = $this.attr('href');
        }, 150);
    });

    // Add hover effects to status items
    $('.status-item').hover(
        function() {
            $(this).css('transform', 'scale(1.05) rotate(2deg)');
        },
        function() {
            $(this).css('transform', '');
        }
    );

    // Auto-refresh activity feed every 30 seconds
    setInterval(function() {
        // You can add AJAX call here to refresh data
        console.log('Refreshing dashboard data...');
    }, 30000);
});
</script>
@endsection
