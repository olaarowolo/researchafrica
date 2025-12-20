<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\JournalContextService;
use Illuminate\Support\Facades\Auth;

class EnsureJournalAccess
{
    protected $journalService;

    public function __construct(JournalContextService $journalService)
    {
        $this->journalService = $journalService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $memberTypeId
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $memberTypeId = null)
    {
        $journal = $this->journalService->getCurrentJournal();

        if (!$journal) {
            // No journal context, decide if we should block or allow.
            // For now, let's allow, as other routes might not need journal context.
            return $next($request);
        }

        $user = Auth::user();

        if (!$user) {
            // No authenticated user, block access.
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if (!$this->journalService->userHasAccess($user, $journal->id, $memberTypeId)) {
            // User does not have access to this journal with the required role.
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}
