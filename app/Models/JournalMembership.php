<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JournalMembership extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'journal_memberships';

    protected $fillable = [
        'member_id',
        'journal_id',
        'member_type_id',
        'status',
        'assigned_by',
        'assigned_at',
        'expires_at',
        'notes',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_PENDING = 'pending';
    public const STATUS_SUSPENDED = 'suspended';

    // Relationships
    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function journal()
    {
        return $this->belongsTo(ArticleCategory::class, 'journal_id');
    }

    public function memberType()
    {
        return $this->belongsTo(MemberType::class, 'member_type_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(Member::class, 'assigned_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeForJournal($query, $journalId)
    {
        return $query->where('journal_id', $journalId);
    }

    public function scopeForMember($query, $memberId)
    {
        return $query->where('member_id', $memberId);
    }

    public function scopeByMemberType($query, $memberTypeId)
    {
        return $query->where('member_type_id', $memberTypeId);
    }

    // Helper methods
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE &&
               (!$this->expires_at || $this->expires_at->isFuture());
    }

    public function activate()
    {
        $this->update([
            'status' => self::STATUS_ACTIVE,
            'assigned_at' => now(),
        ]);
    }

    public function deactivate()
    {
        $this->update(['status' => self::STATUS_INACTIVE]);
    }

    public function suspend()
    {
        $this->update(['status' => self::STATUS_SUSPENDED]);
    }
}