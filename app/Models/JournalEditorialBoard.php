<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JournalEditorialBoard extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'journal_editorial_boards';

    protected $fillable = [
        'journal_id',
        'member_id',
        'position',
        'department',
        'institution',
        'bio',
        'orcid_id',
        'term_start',
        'term_end',
        'is_active',
        'display_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'term_start' => 'date',
        'term_end' => 'date',
        'display_order' => 'integer',
    ];

    // Relationships
    public function journal()
    {
        return $this->belongsTo(ArticleCategory::class, 'journal_id');
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForJournal($query, $journalId)
    {
        return $query->where('journal_id', $journalId);
    }

    public function scopeByPosition($query, $position)
    {
        return $query->where('position', $position);
    }

    public function scopeOrderedByDisplay($query)
    {
        return $query->orderBy('display_order', 'asc')
                     ->orderBy('created_at', 'asc');
    }

    // Helper methods
    public function isActive(): bool
    {
        return $this->is_active &&
               (!$this->term_end || $this->term_end->isFuture());
    }

    public function getFullNameAttribute()
    {
        return $this->member->fullname ?? 'Unknown';
    }
}