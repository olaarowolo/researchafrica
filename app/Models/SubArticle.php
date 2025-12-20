<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class SubArticle extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia, HasFactory;

    public $table = 'sub_articles';

    protected $appends = [
        'upload_paper',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const STATUS_SELECT = [
        '1' => 'Pending',
        '2' => 'Stage 1 (Editor)',
        '3' => 'Pending Reviewer',
        '4' => 'Stage 2 (First Reviewer)',
        '5' => 'Pending Reviewer (Second Reviewer)',
        '6' => 'Stage 3 (Second Reviewer)',
        '7' => 'Pending Editor (Second Editor)',
        '8' => 'Stage 4 (Second Editor)',
        '9' => 'Pending Publisher',
        '10' => 'Approved',
        '11' => 'Pending Editor (Third Editor)',
        '12' => 'Stage 5 (Third Editor)',
    ];

    protected $fillable = [
        'article_id',
        'comment_id',
        'abstract',
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

    public function getUploadPaperAttribute()
    {
        return $this->getMedia('upload_paper')->last();
    }

    public function reviewDocument(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getMedia('correction_upload')->last()
        );
    }

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
