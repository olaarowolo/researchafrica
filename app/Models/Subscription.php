<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Subscription extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia, HasFactory;

    public $table = 'subscriptions';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const STATUS_SELECT = [
        '1' => 'Enabled',
        '2' => 'Disabled',
    ];

    public const CYCLE_TYPE_SELECT = [
        'weekly'  => 'Weekly',
        'monthly' => 'Monthly',
        'Yearly'  => 'Yearly',
    ];

    public const PLAN_TYPE_SELECT = [
        'free'      => 'Free Plan',
        'one-time'  => 'One-time Plan',
        'recurring' => 'Recurring Plan',
    ];

    protected $fillable = [
        'name',
        'description',
        'features',
        'plan_type',
        'cycle_type',
        'cycle_number',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')->fit('crop', 50, 50);
        $this->addMediaConversion('preview')->fit('crop', 120, 120);
    }
}
