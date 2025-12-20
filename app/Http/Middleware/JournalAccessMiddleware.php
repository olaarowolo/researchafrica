<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\JournalContextService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class JournalAccessMiddleware
{
    protected $journalService;

    public function __construct(JournalContextService $journalService)
    {
        $this->journalService = $journalService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $requiredRole = null)
    {
        try {
            $user = Auth::user();
            $journalId = $this->resolveJournalId($request);

            if (!$journalId) {
                Log::warning('JournalAccessMiddleware: Journal ID not found in request', [
                    'user_id' => $user?->id,
                    'path' => $request->path(),
                    'method' => $request->method()
                ]);
                abort(404, 'Journal not found');
            }

            if (!$user) {
                Log::warning('JournalAccessMiddleware: Unauthenticated access attempt', [
                    'journal_id' => $journalId,
                    'path' => $request->path(),
                    'ip' => $request->ip()
                ]);
                return redirect()->route('login');
            }

            // Check if user has access to this journal
            if (!$this->userHasAccess($user, $journalId, $requiredRole)) {
                Log::warning('JournalAccessMiddleware: Access denied', [
                    'user_id' => $user->id,
                    'journal_id' => $journalId,
                    'required_role' => $requiredRole,
                    'path' => $request->path()
                ]);
                abort(403, 'Access denied to this journal');
            }

            return $next($request);
        } catch (\Exception $e) {
            Log::error('JournalAccessMiddleware: Error handling request', [
                'user_id' => Auth::id(),
                'path' => $request->path(),
                'error' => $e->getMessage()
            ]);
            abort(500, 'Internal server error');
        }
    }

    /**
     * Resolve journal ID from request
     */
    private function resolveJournalId(Request $request): ?int
    {
        // Try route parameters first
        if ($request->route('journal_id')) {
            return (int) $request->route('journal_id');
        }

        // Try from route parameter (for future acronym-based routing)
        if ($request->route('acronym')) {
            $journal = $this->journalService->getJournalByAcronym($request->route('acronym'));
            return $journal?->id;
        }

        // Try from query parameter
        if ($request->query('journal_id')) {
            return (int) $request->query('journal_id');
        }

        // Try from current journal context
        $currentJournal = $this->journalService->getCurrentJournal();
        return $currentJournal?->id;
    }

    /**
     * Check if user has access to journal
     */
    private function userHasAccess($user, int $journalId, ?string $requiredRole = null): bool
    {
        // Admin has access to all journals
        if ($user->hasRole('admin')) {
            return true;
        }

        // Check basic journal access
        if (!$user->hasJournalAccess($journalId)) {
            return false;
        }

        // If specific role is required, check for it
        if ($requiredRole) {
            return $this->userHasSpecificRole($user, $journalId, $requiredRole);
        }

        return true;
    }

    /**
     * Check if user has specific role in journal
     */
    private function userHasSpecificRole($user, int $journalId, string $role): bool
    {
        switch (strtolower($role)) {
            case 'editor':
            case 'admin':
                return $user->hasJournalAccess($journalId, 2); // Editor role

            case 'reviewer':
                return $user->hasJournalAccess($journalId, 3); // Reviewer role

            case 'author':
                return $user->hasJournalAccess($journalId, 1); // Author role

            default:
                return false;
        }
    }
}
