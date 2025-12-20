<?php

namespace App\Services;

use App\Models\ArticleCategory;
use Illuminate\Support\Facades\Cache;

class JournalContextService
{
    protected $currentJournal = null;

    /**
     * Get the current journal from request context
     */
    public function getCurrentJournal()
    {
        if ($this->currentJournal) {
            return $this->currentJournal;
        }

        // Try to get from app instance
        if (app()->has('current_journal')) {
            $this->currentJournal = app('current_journal');
            return $this->currentJournal;
        }

        return null;
    }

    /**
     * Set the current journal context
     */
    public function setCurrentJournal($journal)
    {
        $this->currentJournal = $journal;
        app()->instance('current_journal', $journal);
        app()->instance('current_journal_id', $journal->id);
    }

    /**
     * Get journal by slug with caching
     */
    public function getJournalBySlug($slug)
    {
        return Cache::remember("journal_slug_{$slug}", 3600, function () use ($slug) {
            return ArticleCategory::where('journal_slug', $slug)
                                 ->where('is_journal', true)
                                 ->where('status', 'Active')
                                 ->first();
        });
    }


    /**
     * Get journal by acronym with caching
     */
    public function getJournalByAcronym($acronym)
    {
        return Cache::remember("journal_acronym_{$acronym}", 3600, function () use ($acronym) {
            return ArticleCategory::where('journal_acronym', $acronym)
                                 ->where('is_journal', true)
                                 ->where('status', 'Active')
                                 ->first();
        });
    }

    /**
     * Get journal by acronym with enhanced caching
     */
    public function getJournalByAcronymWithCache($acronym)
    {
        return Cache::remember("journal_acronym_{$acronym}", 3600, function () use ($acronym) {
            return ArticleCategory::where('journal_acronym', $acronym)
                                 ->where('is_journal', true)
                                 ->where('status', 'Active')
                                 ->first();
        });
    }

    /**
     * Validate journal acronym uniqueness
     */
    public function validateAcronym($acronym, $excludeId = null)
    {
        $query = ArticleCategory::where('journal_acronym', $acronym)
                               ->where('is_journal', true);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return !$query->exists();
    }

    /**
     * Generate unique acronym from name
     */
    public function generateUniqueAcronym($name, $excludeId = null)
    {
        $baseAcronym = \Str::slug($name, '');
        $acronym = $baseAcronym;
        $counter = 1;

        while (!$this->validateAcronym($acronym, $excludeId)) {
            $acronym = $baseAcronym . $counter;
            $counter++;
        }

        return $acronym;
    }

    /**
     * Get journal by ID with caching
     */
    public function getJournalById($id)
    {
        return Cache::remember("journal_id_{$id}", 3600, function () use ($id) {
            return ArticleCategory::where('id', $id)
                                 ->where('is_journal', true)
                                 ->first();
        });
    }

    /**
     * Clear journal cache
     */
    public function clearJournalCache($journal)
    {
        Cache::forget("journal_id_{$journal->id}");
        Cache::forget("journal_slug_{$journal->journal_slug}");
        Cache::forget("journal_acronym_{$journal->journal_acronym}");
    }

    /**
     * Check if user has access to journal
     */
    public function userHasAccess($user, $journalId, $memberTypeId = null): bool
    {
        if (!$user) {
            return false;
        }

        return $user->hasJournalAccess($journalId, $memberTypeId);
    }

    /**
     * Get accessible journals for user
     */
    public function getUserJournals($user)
    {
        if (!$user) {
            return collect();
        }

        return $user->accessibleJournals;
    }
}
