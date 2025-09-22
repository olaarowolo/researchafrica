<?php

namespace App\Modules\AfriScribe\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuoteRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'ra_service',
        'product',
        'location',
        'service_type',
        'word_count',
        'addons',
        'referral',
        'message',
        'original_filename',
        'file_path',
        'status',
        'estimated_cost',
        'estimated_turnaround',
        'admin_notes',
        'quoted_at',
        'accepted_at',
        'completed_at',
    ];

    protected $casts = [
        'addons' => 'array',
        'estimated_cost' => 'decimal:2',
        'quoted_at' => 'datetime',
        'accepted_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Accessor for formatted cost
    public function getFormattedCostAttribute()
    {
        if ($this->estimated_cost) {
            return '£' . number_format($this->estimated_cost, 2);
        }
        return 'Not quoted yet';
    }

    // Accessor for status badge color
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'quoted' => 'info',
            'accepted' => 'success',
            'rejected' => 'danger',
            'completed' => 'primary',
            default => 'secondary'
        };
    }

    // Scope for pending requests
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Scope for active requests (not rejected or completed)
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['rejected', 'completed']);
    }
}
