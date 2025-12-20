<?php

namespace App\Services;

use App\Models\JournalEditorialBoard;
use App\Models\ArticleCategory;
use App\Models\Member;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class EditorialBoardService
{
    /**
     * Get active editorial board for a journal.
     *
     * @param int $journalId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveBoard(int $journalId)
    {
        return JournalEditorialBoard::forJournal($journalId)
            ->active()
            ->orderedByDisplay()
            ->with('member')
            ->get();
    }

    /**
     * Get all editorial board members for a journal (including inactive).
     *
     * @param int $journalId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllBoardMembers(int $journalId)
    {
        return JournalEditorialBoard::forJournal($journalId)
            ->with('member')
            ->orderedByDisplay()
            ->get();
    }

    /**
     * Add a member to the editorial board.
     *
     * @param array $data
     * @return JournalEditorialBoard
     * @throws ValidationException
     */
    public function addMember(array $data): JournalEditorialBoard
    {
        $validator = Validator::make($data, [
            'journal_id' => 'required|exists:article_categories,id',
            'member_id' => 'required|exists:members,id',
            'position' => 'required|string|max:100',
            'department' => 'nullable|string|max:255',
            'institution' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'orcid_id' => 'nullable|string|max:50',
            'term_start' => 'nullable|date',
            'term_end' => 'nullable|date|after_or_equal:term_start',
            'is_active' => 'boolean',
            'display_order' => 'integer',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return JournalEditorialBoard::create($validator->validated());
    }

    /**
     * Update an editorial board member's details.
     *
     * @param int $boardMemberId
     * @param array $data
     * @return JournalEditorialBoard
     * @throws ValidationException
     */
    public function updateMember(int $boardMemberId, array $data): JournalEditorialBoard
    {
        $boardMember = JournalEditorialBoard::findOrFail($boardMemberId);

        $validator = Validator::make($data, [
            'position' => 'sometimes|required|string|max:100',
            'department' => 'nullable|string|max:255',
            'institution' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'orcid_id' => 'nullable|string|max:50',
            'term_start' => 'nullable|date',
            'term_end' => 'nullable|date|after_or_equal:term_start',
            'is_active' => 'sometimes|boolean',
            'display_order' => 'sometimes|integer',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $boardMember->update($validator->validated());

        return $boardMember->fresh();
    }

    /**
     * Remove a member from the editorial board.
     *
     * @param int $boardMemberId
     * @return void
     */
    public function removeMember(int $boardMemberId): void
    {
        $boardMember = JournalEditorialBoard::findOrFail($boardMemberId);
        $boardMember->delete();
    }

    /**
     * Update the display order of board members.
     *
     * @param array $order
     * @return void
     */
    public function updateOrder(array $order): void
    {
        foreach ($order as $item) {
            JournalEditorialBoard::where('id', $item['id'])->update(['display_order' => $item['order']]);
        }
    }

    /**
     * Get analytics for the editorial board.
     *
     * @param int $journalId
     * @return array
     */
    public function getBoardAnalytics(int $journalId)
    {
        $totalMembers = JournalEditorialBoard::forJournal($journalId)->count();
        $activeMembers = JournalEditorialBoard::forJournal($journalId)->active()->count();

        return [
            'total_members' => $totalMembers,
            'active_members' => $activeMembers,
            'inactive_members' => $totalMembers - $activeMembers,
        ];
    }

    /**
     * Add a member to the editorial board (Controller compatible).
     *
     * @param int $memberId
     * @param int $journalId
     * @param string $position
     * @param array $details
     * @return JournalEditorialBoard
     */
    public function addBoardMember($memberId, $journalId, $position, $details = [])
    {
        $data = array_merge([
            'member_id' => $memberId,
            'journal_id' => $journalId,
            'position' => $position,
            'is_active' => true,
        ], $details);

        return $this->addMember($data);
    }

    /**
     * Remove a member from the editorial board (Controller compatible).
     *
     * @param int $memberId
     * @return bool
     */
    public function removeBoardMember($memberId)
    {
        try {
            $this->removeMember($memberId);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
