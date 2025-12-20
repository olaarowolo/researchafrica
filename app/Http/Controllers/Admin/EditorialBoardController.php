<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArticleCategory;
use App\Models\JournalEditorialBoard;
use App\Models\Member;
use App\Services\EditorialBoardService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class EditorialBoardController extends Controller
{
    protected $editorialBoardService;

    public function __construct(EditorialBoardService $editorialBoardService)
    {
        $this->editorialBoardService = $editorialBoardService;
        $this->middleware('auth');
        $this->middleware('admin');
    }

    /**
     * Display the editorial board for a journal
     */
    public function index(ArticleCategory $journal): View
    {
        try {
            $editorialBoard = $this->editorialBoardService->getActiveBoard($journal->id);

            // Get analytics
            $analytics = $this->editorialBoardService->getBoardAnalytics($journal->id);

            // Get all positions for filtering
            $positions = JournalEditorialBoard::where('journal_id', $journal->id)
                                           ->where('is_active', true)
                                           ->distinct()
                                           ->pluck('position')
                                           ->sort()
                                           ->values();

            return view('admin.editorial-boards.index', compact('journal', 'editorialBoard', 'analytics', 'positions'));
        } catch (\Exception $e) {
            Log::error('EditorialBoardController: Error loading editorial board', [
                'journal_id' => $journal->id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to load editorial board.');
        }
    }

    /**
     * Show the form for creating a new editorial board member
     */
    public function create(ArticleCategory $journal): View
    {
        try {
            // Get members who are not already on the editorial board for this position
            $availableMembers = Member::whereDoesntHave('editorialPositions', function ($query) use ($journal) {
                                    $query->where('journal_id', $journal->id)
                                          ->where('is_active', true);
                                })
                                ->where('deleted_at', null)
                                ->orderBy('first_name')
                                ->get();

            $positions = [
                'Editor-in-Chief',
                'Associate Editor',
                'Managing Editor',
                'Technical Editor',
                'Review Editor',
                'Editorial Board Member',
                'Guest Editor'
            ];

            return view('admin.editorial-boards.create', compact('journal', 'availableMembers', 'positions'));
        } catch (\Exception $e) {
            Log::error('EditorialBoardController: Error loading create form', [
                'journal_id' => $journal->id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to load editorial board creation form.');
        }
    }

    /**
     * Store a newly created editorial board member
     */
    public function store(Request $request, ArticleCategory $journal): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'member_id' => 'required|exists:members,id',
                'position' => 'required|string|max:100',
                'department' => 'nullable|string|max:255',
                'institution' => 'nullable|string|max:255',
                'bio' => 'nullable|string',
                'orcid_id' => 'nullable|string|max:50',
                'term_start' => 'nullable|date',
                'term_end' => 'nullable|date|after:term_start',
            ]);

            $boardMember = $this->editorialBoardService->addBoardMember(
                $validated['member_id'],
                $journal->id,
                $validated['position'],
                [
                    'department' => $validated['department'] ?? null,
                    'institution' => $validated['institution'] ?? null,
                    'bio' => $validated['bio'] ?? null,
                    'orcid_id' => $validated['orcid_id'] ?? null,
                    'term_start' => $validated['term_start'] ?? null,
                    'term_end' => $validated['term_end'] ?? null,
                ]
            );

            if ($boardMember) {
                Log::info('EditorialBoardController: Editorial board member added', [
                    'board_member_id' => $boardMember->id,
                    'journal_id' => $journal->id,
                    'member_id' => $validated['member_id'],
                    'position' => $validated['position'],
                    'added_by' => Auth::id()
                ]);

                return redirect()->route('admin.editorial-board.index', $journal)
                               ->with('success', 'Editorial board member added successfully!');
            }

            return back()->withInput()
                        ->with('error', 'Failed to add editorial board member. Member may already hold this position.');
        } catch (\Exception $e) {
            Log::error('EditorialBoardController: Error adding editorial board member', [
                'journal_id' => $journal->id,
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);
            return back()->withInput()
                        ->with('error', 'Failed to add editorial board member. Please try again.');
        }
    }

    /**
     * Show the form for editing an editorial board member
     */
    public function edit(ArticleCategory $journal, JournalEditorialBoard $member): View
    {
        try {
            if ($member->journal_id !== $journal->id) {
                abort(404);
            }

            $positions = [
                'Editor-in-Chief',
                'Associate Editor',
                'Managing Editor',
                'Technical Editor',
                'Review Editor',
                'Editorial Board Member',
                'Guest Editor'
            ];

            return view('admin.editorial-boards.edit', compact('journal', 'member', 'positions'));
        } catch (\Exception $e) {
            Log::error('EditorialBoardController: Error loading edit form', [
                'journal_id' => $journal->id,
                'member_id' => $member->id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to load editorial board member edit form.');
        }
    }

    /**
     * Update the specified editorial board member
     */
    public function update(Request $request, ArticleCategory $journal, JournalEditorialBoard $member): RedirectResponse
    {
        try {
            if ($member->journal_id !== $journal->id) {
                abort(404);
            }

            $validated = $request->validate([
                'position' => 'required|string|max:100',
                'department' => 'nullable|string|max:255',
                'institution' => 'nullable|string|max:255',
                'bio' => 'nullable|string',
                'orcid_id' => 'nullable|string|max:50',
                'term_start' => 'nullable|date',
                'term_end' => 'nullable|date|after:term_start',
                'display_order' => 'nullable|integer|min:0',
            ]);

            $member->update([
                'position' => $validated['position'],
                'department' => $validated['department'] ?? null,
                'institution' => $validated['institution'] ?? null,
                'bio' => $validated['bio'] ?? null,
                'orcid_id' => $validated['orcid_id'] ?? null,
                'term_start' => $validated['term_start'] ?? null,
                'term_end' => $validated['term_end'] ?? null,
                'display_order' => $validated['display_order'] ?? $member->display_order,
            ]);

            Log::info('EditorialBoardController: Editorial board member updated', [
                'board_member_id' => $member->id,
                'journal_id' => $journal->id,
                'updated_by' => Auth::id()
            ]);

            return redirect()->route('admin.editorial-board.index', $journal)
                           ->with('success', 'Editorial board member updated successfully!');
        } catch (\Exception $e) {
            Log::error('EditorialBoardController: Error updating editorial board member', [
                'journal_id' => $journal->id,
                'member_id' => $member->id,
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);
            return back()->withInput()
                        ->with('error', 'Failed to update editorial board member. Please try again.');
        }
    }

    /**
     * Remove the specified editorial board member
     */
    public function destroy(ArticleCategory $journal, JournalEditorialBoard $member): RedirectResponse
    {
        try {
            if ($member->journal_id !== $journal->id) {
                abort(404);
            }

            $success = $this->editorialBoardService->removeBoardMember($member->id);

            if ($success) {
                Log::info('EditorialBoardController: Editorial board member removed', [
                    'board_member_id' => $member->id,
                    'journal_id' => $journal->id,
                    'removed_by' => Auth::id()
                ]);

                return redirect()->route('admin.editorial-board.index', $journal)
                               ->with('success', 'Editorial board member removed successfully!');
            }

            return back()->with('error', 'Failed to remove editorial board member.');
        } catch (\Exception $e) {
            Log::error('EditorialBoardController: Error removing editorial board member', [
                'journal_id' => $journal->id,
                'member_id' => $member->id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to remove editorial board member. Please try again.');
        }
    }

    /**
     * Reorder editorial board members
     */
    public function reorder(Request $request, ArticleCategory $journal)
    {
        try {
            $validated = $request->validate([
                'member_ids' => 'required|array',
                'member_ids.*' => 'exists:journal_editorial_boards,id'
            ]);

            foreach ($validated['member_ids'] as $index => $memberId) {
                JournalEditorialBoard::where('id', $memberId)
                                   ->where('journal_id', $journal->id)
                                   ->update(['display_order' => $index + 1]);
            }

            Log::info('EditorialBoardController: Editorial board reordered', [
                'journal_id' => $journal->id,
                'member_count' => count($validated['member_ids']),
                'reordered_by' => Auth::id()
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('EditorialBoardController: Error reordering editorial board', [
                'journal_id' => $journal->id,
                'error' => $e->getMessage()
            ]);
            return response()->json(['success' => false, 'error' => 'Failed to reorder editorial board.']);
        }
    }

    /**
     * Show editorial board analytics
     */
    public function analytics(ArticleCategory $journal): View
    {
        try {
            $analytics = $this->editorialBoardService->getBoardAnalytics($journal->id);

            // Get detailed board composition
            $boardComposition = JournalEditorialBoard::where('journal_id', $journal->id)
                                                   ->where('is_active', true)
                                                   ->with('member')
                                                   ->get()
                                                   ->groupBy('position')
                                                   ->map(function ($members) {
                                                       return [
                                                           'count' => $members->count(),
                                                           'members' => $members->values()
                                                       ];
                                                   });

            return view('admin.editorial-boards.analytics', compact('journal', 'analytics', 'boardComposition'));
        } catch (\Exception $e) {
            Log::error('EditorialBoardController: Error loading analytics', [
                'journal_id' => $journal->id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to load editorial board analytics.');
        }
    }
}
