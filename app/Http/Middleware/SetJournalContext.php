<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\JournalContextService;
use Illuminate\Support\Facades\Log;

class SetJournalContext
{
    protected $journalService;

    public function __construct(JournalContextService $journalService)
    {
        $this->journalService = $journalService;
    }

    /**
     * Handle an incoming request and set journal context
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            // Try to resolve journal from route parameters
            $journal = $this->resolveJournalFromRequest($request);

            if ($journal) {
                // Set the current journal context
                $this->journalService->setCurrentJournal($journal);

                // Add journal info to view
                view()->share('currentJournal', $journal);

                // Set SEO meta tags
                $this->setSeoMetaTags($journal);

                Log::info('SetJournalContext: Journal context set', [
                    'journal_id' => $journal->id,
                    'journal_acronym' => $journal->journal_acronym,
                    'request_path' => $request->path()
                ]);
            } else {
                Log::warning('SetJournalContext: No journal found for request', [
                    'request_path' => $request->path(),
                    'route_params' => $request->route()->parameters(),
                    'query_params' => $request->query()
                ]);
            }

            return $next($request);
        } catch (\Exception $e) {
            Log::error('SetJournalContext: Error setting journal context', [
                'request_path' => $request->path(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $next($request);
        }
    }

    /**
     * Resolve journal from request parameters
     */
    protected function resolveJournalFromRequest(Request $request)
    {
        // Try acronym first (main routing method)
        if ($request->route('acronym')) {
            $journal = $this->journalService->getJournalByAcronymWithCache(
                $request->route('acronym')
            );
            if ($journal) {
                return $journal;
            }
        }

        // Try journal_id (fallback)
        if ($request->route('journal_id')) {
            $journal = $this->journalService->getJournalById(
                (int) $request->route('journal_id')
            );
            if ($journal) {
                return $journal;
            }
        }

        // Try from query parameter
        if ($request->query('journal_id')) {
            $journal = $this->journalService->getJournalById(
                (int) $request->query('journal_id')
            );
            if ($journal) {
                return $journal;
            }
        }

        // Try legacy slug-based routing
        if ($request->route('journal_slug')) {
            $journal = $this->journalService->getJournalBySlug(
                $request->route('journal_slug')
            );
            if ($journal) {
                return $journal;
            }
        }

        return null;
    }

    /**
     * Set SEO meta tags for the journal
     */
    protected function setSeoMetaTags($journal)
    {
        // Generate SEO meta tags
        $title = $journal->display_name . ' - Research Africa';
        $description = $journal->description ?: 'Academic journal published on Research Africa platform';
        $keywords = implode(', ', [
            $journal->name,
            'academic journal',
            'research',
            'publications',
            'scholarly articles',
            'Research Africa'
        ]);

        // Share meta tags with views
        view()->share([
            'pageTitle' => $title,
            'pageDescription' => $description,
            'pageKeywords' => $keywords,
            'canonicalUrl' => url()->current(),
            'ogTitle' => $title,
            'ogDescription' => $description,
            'ogType' => 'website',
            'ogUrl' => url()->current(),
        ]);

        // Set page title if not already set
        if (!view()->shared('pageTitle')) {
            view()->share('pageTitle', $title);
        }
    }
}
