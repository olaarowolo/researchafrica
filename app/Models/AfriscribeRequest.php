<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AfriscribeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'service_type',
        'message',
        'file_path',
        'original_filename',
        'status',
        'admin_notes',
        'processed_at'
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';

    // Service type constants
    const SERVICE_PROOFREADING = 'proofreading';
    const SERVICE_EDITING = 'editing';
    const SERVICE_FORMATTING = 'formatting';

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

    /**
     * Get the available statuses
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_COMPLETED => 'Completed',
        ];
    }

    /**
     * Check if request is pending
     */
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if request is processing
     */
    public function isProcessing()
    {
        return $this->status === self::STATUS_PROCESSING;
    }

    /**
     * Check if request is completed
     */
    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Mark request as processing
     */
    public function markAsProcessing()
    {
        $this->update([
            'status' => self::STATUS_PROCESSING,
            'processed_at' => now()
        ]);
    }

    /**
     * Mark request as completed
     */
    public function markAsCompleted()
    {
        $this->update(['status' => self::STATUS_COMPLETED]);
    }
}
