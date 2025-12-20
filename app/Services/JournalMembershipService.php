<?php

namespace App\Services;

use App\Models\JournalMembership;
use App\Models\ArticleCategory;
use App\Models\Member;
use App\Models\MemberType;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class JournalMembershipService
{
    /**
     * Get active memberships for a journal.
     *
     * @param int $journalId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveMemberships(int $journalId)
    {
        return JournalMembership::forJournal($journalId)
            ->active()
            ->with(['member', 'memberType'])
            ->get();
    }

    /**
     * Get memberships by status for a journal.
     *
     * @param int $journalId
     * @param string $status
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getMembershipsByStatus(int $journalId, string $status)
    {
        return JournalMembership::forJournal($journalId)
            ->where('status', $status)
            ->with(['member', 'memberType'])
            ->get();
    }

    /**
     * Assign a member to a journal.
     *
     * @param int $memberId
     * @param int $journalId
     * @param int $memberTypeId
     * @param int|null $assignedById
     * @param string|null $notes
     * @param string $status
     * @return JournalMembership|null
     */
    public function assignMemberToJournal($memberId, $journalId, $memberTypeId, $assignedById, $notes, $status)
    {
        // Check if exists
        $exists = JournalMembership::where('journal_id', $journalId)
            ->where('member_id', $memberId)
            ->exists();

        if ($exists) {
            return null;
        }

        return $this->addMember([
            'journal_id' => $journalId,
            'member_id' => $memberId,
            'member_type_id' => $memberTypeId,
            'assigned_by' => $assignedById,
            'notes' => $notes,
            'status' => $status
        ]);
    }

    /**
     * Add a member to a journal.
     *
     * @param array $data
     * @return JournalMembership
     * @throws ValidationException
     */
    public function addMember(array $data): JournalMembership
    {
        $validator = Validator::make($data, [
            'journal_id' => 'required|exists:article_categories,id',
            'member_id' => 'required|exists:members,id',
            'member_type_id' => 'required|exists:member_types,id',
            'status' => 'sometimes|in:active,inactive,pending,suspended',
            'assigned_by' => 'nullable|exists:members,id',
            'expires_at' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $validatedData = $validator->validated();
        if (!isset($validatedData['status'])) {
            $validatedData['status'] = JournalMembership::STATUS_PENDING;
        }

        if ($validatedData['status'] === JournalMembership::STATUS_ACTIVE) {
            $validatedData['assigned_at'] = now();
        }

        return JournalMembership::create($validatedData);
    }

    /**
     * Update a journal membership.
     *
     * @param int $membershipId
     * @param array $data
     * @return JournalMembership
     * @throws ValidationException
     */
    public function updateMembership(int $membershipId, array $data): JournalMembership
    {
        $membership = JournalMembership::findOrFail($membershipId);

        $validator = Validator::make($data, [
            'member_type_id' => 'sometimes|required|exists:member_types,id',
            'expires_at' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $membership->update($validator->validated());

        return $membership->fresh();
    }

    /**
     * Change the status of a journal membership.
     *
     * @param int $membershipId
     * @param string $status
     * @param int|null $assignedById
     * @return JournalMembership
     */
    public function changeStatus(int $membershipId, string $status, ?int $assignedById = null): JournalMembership
    {
        $membership = JournalMembership::findOrFail($membershipId);

        $validStatuses = [
            JournalMembership::STATUS_ACTIVE,
            JournalMembership::STATUS_INACTIVE,
            JournalMembership::STATUS_PENDING,
            JournalMembership::STATUS_SUSPENDED,
        ];

        if (!in_array($status, $validStatuses)) {
            throw new \InvalidArgumentException("Invalid status provided.");
        }

        $updateData = ['status' => $status];

        if ($status === JournalMembership::STATUS_ACTIVE) {
            $updateData['assigned_at'] = now();
            if ($assignedById) {
                $updateData['assigned_by'] = $assignedById;
            }
        }

        $membership->update($updateData);

        return $membership;
    }

    /**
     * Check if a user has a specific role in a journal.
     *
     * @param int $memberId
     * @param int $journalId
     * @param string $roleName
     * @return bool
     */
    public function memberHasRole(int $memberId, int $journalId, string $roleName): bool
    {
        $memberType = MemberType::where('name', $roleName)->first();
        if (!$memberType) {
            return false;
        }

        return JournalMembership::where('member_id', $memberId)
            ->where('journal_id', $journalId)
            ->where('member_type_id', $memberType->id)
            ->where('status', JournalMembership::STATUS_ACTIVE)
            ->exists();
    }
}
