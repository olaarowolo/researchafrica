<?php

namespace App\Models;

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

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_QUOTED = 'quoted';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';
    const STATUS_COMPLETED = 'completed';

    // Service type constants
    const SERVICE_PROOFREADING = 'proofreading';
    const SERVICE_EDITING = 'editing';
    const SERVICE_FORMATTING = 'formatting';

    /**
     * Get the available statuses
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_QUOTED => 'Quoted',
            self::STATUS_ACCEPTED => 'Accepted',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_COMPLETED => 'Completed',
        ];
    }

    /**
     * Get the available service types
     */
    public static function getServiceTypes()
    {
        return [
            self::SERVICE_PROOFREADING => 'Proofreading',
            self::SERVICE_EDITING => 'Editing',
            self::SERVICE_FORMATTING => 'Formatting',
        ];
    }
}
