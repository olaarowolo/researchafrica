<?php

namespace App\Models;

use DateTimeInterface;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ArticleCategory extends Model implements HasMedia
{
    use SoftDeletes, HasFactory, InteractsWithMedia;

    public $table = 'article_categories';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $appends = ['cover_image'];

    protected $fillable = [
        'category_name',
        'status',
        'description',
        'aim_scope',
        'editorial_board',
        'submission',
        'subscribe',
        'issn',
        'online_issn',
        'doi_link',
        'journal_url',
        'parent_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const STATUS_SELECT = [
        'Active' => 'Active',
        'Inactive' => 'Inactive',
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

    /**
     * Get the Media
     *
     * @param  string  $value
     * @return string
     */
    public function getCoverImageAttribute()
    {
        return $this->getMedia('cover_image')->last();
    }

    /**
     * Get all of the article for the ArticleCategory
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function article(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function category()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }
    public function categories()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * Scope a query to only include parent_id
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotParent($query)
    {
        return $query->where('parent_id', null);
    }

    /**
     * Scope a query to only include parent_id
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeParent($query, int $sub)
    {
        return $query->where('parent_id', $sub);
    }

}
