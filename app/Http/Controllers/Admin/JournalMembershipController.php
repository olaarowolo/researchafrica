<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArticleCategory;
use App\Models\JournalMembership;
use App\Models\Member;
use App\Models\MemberType;
use App\Services\JournalMembershipService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class JournalMembershipController extends Controller
{
    protected $membershipService;

    public function __construct(JournalMembershipService $membershipService)
    {
        $this->membershipService = $membershipService;
        $this->middleware('auth');
        $this->middleware('admin');
    }

    /**
     * Display the membership list for a journal
     */
    public function index(ArticleCategory $journal): View
    {
        try {
            $memberships = $journal->activeMemberships()
                                 ->with(['member', 'memberType', 'assignedBy'])
                                 ->orderBy('created_at', 'desc')
                                 ->paginate(20);

            // Get membership statistics
            $stats = [
                'total_members' => $journal->activeMemberships()->count(),
                'by_type' => $journal->activeMemberships()
                                   ->with('memberType')
                                   ->get()
                                   ->groupBy('memberType.name')
                                   ->map->count(),
                'pending_requests' => $journal->memberships()
                                            ->where('status', 'pending')
                                            ->count(),
            ];

            $memberTypes = MemberType::orderBy('name')->get();

            return view('admin.memberships.index', compact('journal', 'memberships', 'stats', 'memberTypes'));
        } catch (\Exception $e) {
            Log::error('JournalMembershipController: Error loading memberships', [
                'journal_id' => $journal->id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to load journal memberships.');
        }
    }

    /**
     * Show the form for creating a new membership
     */
    public function create(ArticleCategory $journal): View
    {
        try {
            // Get members who don't already have membership in this journal
            $existingMemberIds = $journal->memberships()
                                       ->where('status', '!=', 'inactive')
                                       ->pluck('member_id')
                                       ->toArray();

            $availableMembers = Member::whereNotIn('id', $existingMemberIds)
                                    ->where('deleted_at', null)
                                    ->orderBy('first_name')
                                    ->get();

            $memberTypes = MemberType::orderBy('name')->get();

            return view('admin.memberships.create', compact('journal', 'availableMembers', 'memberTypes'));
        } catch (\Exception $e) {
            Log::error('JournalMembershipController: Error loading create form', [
                'journal_id' => $journal->id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to load membership creation form.');
        }
    }

    /**
     * Store a newly created membership
     */
    public function store(Request $request, ArticleCategory $journal): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'member_id' => 'required|exists:members,id',
                'member_type_id' => 'required|exists:member_types,id',
                'status' => 'required|in:active,inactive,pending,suspended',
                'expires_at' => 'nullable|date|after:today',
                'notes' => 'nullable|string|max:1000',
            ]);

            $membership = $this->membershipService->assignMemberToJournal(
                $validated['member_id'],
                $journal->id,
                $validated['member_type_id'],
                Auth::id(),
                $validated['notes'],
                $validated['status']
            );

            if ($membership) {
                // Update expires_at if provided
                if (!empty($validated['expires_at'])) {
                    $membership->update(['expires_at' => $validated['expires_at']]);
                }

                Log::info('JournalMembershipController: Membership created', [
                    'membership_id' => $membership->id,
                    'journal_id' => $journal->id,
                    'member_id' => $validated['member_id'],
                    'member_type_id' => $validated['member_type_id'],
                    'status' => $validated['status'],
                    'created_by' => Auth::id()
                ]);

                return redirect()->route('admin.journal-memberships.index', $journal)
                               ->with('success', 'Membership created successfully!');
            }

            return back()->withInput()
                        ->with('error', 'Failed to create membership. Member may already have membership in this journal.');
        } catch (\Exception $e) {
            Log::error('JournalMembershipController: Error creating membership', [
                'journal_id' => $journal->id,
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);
            return back()->withInput()
                        ->with('error', 'Failed to create membership. Please try again.');
        }
    }

    /**
     * Show the form for editing a membership
     */
    public function edit(ArticleCategory $journal, JournalMembership $member): View
    {
        try {
            if ($member->journal_id !== $journal->id) {
                abort(404);
            }

            $member->load(['member', 'memberType', 'assignedBy']);
            $memberTypes = MemberType::orderBy('name')->get();

            return view('admin.memberships.edit', compact('journal', 'member', 'memberTypes'));
        } catch (\Exception $e) {
            Log::error('JournalMembershipController: Error loading edit form', [
                'journal_id' => $journal->id,
                'member_id' => $member->id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to load membership edit form.');
        }
    }

    /**
     * Update the specified membership
     */
    public function update(Request $request, ArticleCategory $journal, JournalMembership $member): RedirectResponse
    {
        try {
            if ($member->journal_id !== $journal->id) {
                abort(404);
            }

            $validated = $request->validate([
                'member_type_id' => 'required|exists:member_types,id',
                'status' => 'required|in:active,inactive,pending,suspended',
                'expires_at' => 'nullable|date|after:today',
                'notes' => 'nullable|string|max:1000',
            ]);

            $member->update([
                'member_type_id' => $validated['member_type_id'],
                'status' => $validated['status'],
                'expires_at' => $validated['expires_at'],
                'notes' => $validated['notes'],
            ]);

            Log::info('JournalMembershipController: Membership updated', [
                'membership_id' => $member->id,
                'journal_id' => $journal->id,
                'updated_by' => Auth::id()
            ]);

            return redirect()->route('admin.journal-memberships.index', $journal)
                           ->with('success', 'Membership updated successfully!');
        } catch (\Exception $e) {
            Log::error('JournalMembershipController: Error updating membership', [
                'journal_id' => $journal->id,
                'member_id' => $member->id,
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);
            return back()->withInput()
                        ->with('error', 'Failed to update membership. Please try again.');
        }
    }

    /**
     * Remove the specified membership
     */
    public function destroy(ArticleCategory $journal, JournalMembership $member): RedirectResponse
    {
        try {
            if ($member->journal_id !== $journal->id) {
                abort(404);
            }

            $member->update(['status' => 'inactive']);

            Log::info('JournalMembershipController: Membership deactivated', [
                'membership_id' => $member->id,
                'journal_id' => $journal->id,
                'member_id' => $member->member_id,
                'deactivated_by' => Auth::id()
            ]);

            return redirect()->route('admin.journal-memberships.index', $journal)
                           ->with('success', 'Membership deactivated successfully!');
        } catch (\Exception $e) {
            Log::error('JournalMembershipController: Error deactivating membership', [
                'journal_id' => $journal->id,
                'member_id' => $member->id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to deactivate membership. Please try again.');
        }
    }

    /**
     * Approve a pending membership
     */
    public function approve(ArticleCategory $journal, JournalMembership $member): RedirectResponse
    {
        try {
            if ($member->journal_id !== $journal->id) {
                abort(404);
            }

            if ($member->status !== 'pending') {
                return back()->with('error', 'Only pending memberships can be approved.');
            }

            $member->update([
                'status' => 'active',
                'assigned_at' => now()
            ]);

            Log::info('JournalMembershipController: Membership approved', [
                'membership_id' => $member->id,
                'journal_id' => $journal->id,
                'approved_by' => Auth::id()
            ]);

            return back()->with('success', 'Membership approved successfully!');
        } catch (\Exception $e) {
            Log::error('JournalMembershipController: Error approving membership', [
                'journal_id' => $journal->id,
                'member_id' => $member->id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to approve membership. Please try again.');
        }
    }

    /**
     * Reject a pending membership
     */
    public function reject(ArticleCategory $journal, JournalMembership $member): RedirectResponse
    {
        try {
            if ($member->journal_id !== $journal->id) {
                abort(404);
            }

            if ($member->status !== 'pending') {
                return back()->with('error', 'Only pending memberships can be rejected.');
            }

            $member->update(['status' => 'inactive']);

            Log::info('JournalMembershipController: Membership rejected', [
                'membership_id' => $member->id,
                'journal_id' => $journal->id,
                'rejected_by' => Auth::id()
            ]);

            return back()->with('success', 'Membership rejected successfully!');
        } catch (\Exception $e) {
            Log::error('JournalMembershipController: Error rejecting membership', [
                'journal_id' => $journal->id,
                'member_id' => $member->id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to reject membership. Please try again.');
        }
    }

    /**
     * Suspend an active membership
     */
    public function suspend(ArticleCategory $journal, JournalMembership $member): RedirectResponse
    {
        try {
            if ($member->journal_id !== $journal->id) {
                abort(404);
            }

            if ($member->status !== 'active') {
                return back()->with('error', 'Only active memberships can be suspended.');
            }

            $member->update(['status' => 'suspended']);

            Log::info('JournalMembershipController: Membership suspended', [
                'membership_id' => $member->id,
                'journal_id' => $journal->id,
                'suspended_by' => Auth::id()
            ]);

            return back()->with('success', 'Membership suspended successfully!');
        } catch (\Exception $e) {
            Log::error('JournalMembershipController: Error suspending membership', [
                'journal_id' => $journal->id,
                'member_id' => $member->id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to suspend membership. Please try again.');
        }
    }

    /**
     * Reactivate a suspended membership
     */
    public function reactivate(ArticleCategory $journal, JournalMembership $member): RedirectResponse
    {
        try {
            if ($member->journal_id !== $journal->id) {
                abort(404);
            }

            if ($member->status !== 'suspended') {
                return back()->with('error', 'Only suspended memberships can be reactivated.');
            }

            $member->update(['status' => 'active']);

            Log::info('JournalMembershipController: Membership reactivated', [
                'membership_id' => $member->id,
                'journal_id' => $journal->id,
                'reactivated_by' => Auth::id()
            ]);

            return back()->with('success', 'Membership reactivated successfully!');
        } catch (\Exception $e) {
            Log::error('JournalMembershipController: Error reactivating membership', [
                'journal_id' => $journal->id,
                'member_id' => $member->id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to reactivate membership. Please try again.');
        }
    }

    /**
     * Bulk membership operations
     */
    public function bulkUpdate(Request $request, ArticleCategory $journal): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'membership_ids' => 'required|array',
                'membership_ids.*' => 'exists:journal_memberships,id',
                'action' => 'required|in:approve,reject,suspend,activate',
                'notes' => 'nullable|string|max:1000',
            ]);

            $memberships = JournalMembership::whereIn('id', $validated['membership_ids'])
                                          ->where('journal_id', $journal->id)
                                          ->get();

            $updatedCount = 0;

            foreach ($memberships as $membership) {
                switch ($validated['action']) {
                    case 'approve':
                        if ($membership->status === 'pending') {
                            $membership->update([
                                'status' => 'active',
                                'assigned_at' => now(),
                                'notes' => $validated['notes']
                            ]);
                            $updatedCount++;
                        }
                        break;
                    case 'reject':
                        if ($membership->status === 'pending') {
                            $membership->update([
                                'status' => 'inactive',
                                'notes' => $validated['notes']
                            ]);
                            $updatedCount++;
                        }
                        break;
                    case 'suspend':
                        if ($membership->status === 'active') {
                            $membership->update([
                                'status' => 'suspended',
                                'notes' => $validated['notes']
                            ]);
                            $updatedCount++;
                        }
                        break;
                    case 'activate':
                        if ($membership->status === 'suspended') {
                            $membership->update([
                                'status' => 'active',
                                'notes' => $validated['notes']
                            ]);
                            $updatedCount++;
                        }
                        break;
                }
            }

            Log::info('JournalMembershipController: Bulk membership update', [
                'journal_id' => $journal->id,
                'action' => $validated['action'],
                'membership_count' => count($validated['membership_ids']),
                'updated_count' => $updatedCount,
                'updated_by' => Auth::id()
            ]);

            return back()->with('success', "Successfully updated {$updatedCount} memberships.");
        } catch (\Exception $e) {
            Log::error('JournalMembershipController: Error in bulk update', [
                'journal_id' => $journal->id,
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);
            return back()->with('error', 'Failed to update memberships. Please try again.');
        }
    }

    /**
     * Show membership statistics
     */
    public function statistics(ArticleCategory $journal): View
    {
        try {
            $memberships = $journal->memberships()->with(['member', 'memberType'])->get();

            $statistics = [
                'total_memberships' => $memberships->count(),
                'active_memberships' => $memberships->where('status', 'active')->count(),
                'pending_memberships' => $memberships->where('status', 'pending')->count(),
                'suspended_memberships' => $memberships->where('status', 'suspended')->count(),
                'inactive_memberships' => $memberships->where('status', 'inactive')->count(),
                'by_member_type' => $memberships->groupBy('memberType.name')->map->count(),
                'by_status' => $memberships->groupBy('status')->map->count(),
                'recent_activity' => $memberships->sortByDesc('updated_at')->take(10)->values(),
                'expiring_soon' => $memberships->where('expires_at', '!=', null)
                                             ->where('expires_at', '<=', now()->addDays(30))
                                             ->where('status', 'active')
                                             ->sortBy('expires_at')
                                             ->take(10)
                                             ->values(),
            ];

            return view('admin.memberships.statistics', compact('journal', 'statistics'));
        } catch (\Exception $e) {
            Log::error('JournalMembershipController: Error loading statistics', [
                'journal_id' => $journal->id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to load membership statistics.');
        }
    }
}
