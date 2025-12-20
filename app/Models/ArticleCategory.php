<?php

namespace App\Models;


use DateTimeInterface;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Str;

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
        'name',
        'display_name',
        'category_name',
        'parent_id',
        'description',
        'is_journal',
        'journal_slug',
        'journal_acronym',
        'journal_description',
        'journal_logo',
        'journal_website',
        'journal_issn',
        'journal_scope',
        'journal_url',
        'created_at',
        'updated_at',
        'status',
        'aim_scope',
        'issn',
        'online_issn',
        'doi_link',
        'publisher_name',
        'editor_in_chief',
        'contact_email',
        'subdomain',
        'custom_domain',
        'theme_config',
        'email_settings',
        'submission_settings',
    ];

    protected $casts = [
        'theme_config' => 'array',
        'email_settings' => 'array',
        'submission_settings' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($category) {
            // Cascade soft delete to related articles
            $category->articles()->delete();
        });
    }

    public const STATUS_SELECT = [
        'Active' => 'Active',
        'Inactive' => 'Inactive',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function registerMediaConversions(?Media $media = null): void
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

    /**
     * Get all articles for the ArticleCategory (alias for article())
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function articles(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->article();
    }

    public function category()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }
    public function categories()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    // ========================================
    // SEMANTIC CLARITY & BACKWARD COMPATIBILITY
    // ========================================

    /**
     * Get the category name (backward compatibility accessor)
     *
     * @param string|null $value
     * @return string|null
     */
    public function getCategoryNameAttribute($value = null)
    {
        // Return new 'name' field if available, otherwise fall back to original value
        return $this->name ?? $value;
    }


    /**
     * Set the category name (backward compatibility mutator)
     *
     * @param string $value
     * @return void
     */
    public function setCategoryNameAttribute($value)
    {
        // Set both the new 'name' field and keep backward compatibility
        if ($value !== null) {
            $this->attributes['name'] = $value;
        }
        $this->attributes['category_name'] = $value;
    }

    /**
     * Check if this entity is a journal
     *
     * @return bool
     */
    public function isJournal(): bool
    {
        return $this->is_journal === true;
    }

    /**
     * Check if this entity is a category
     *
     * @return bool
     */
    public function isCategory(): bool
    {
        return $this->is_journal === false || is_null($this->is_journal);
    }


    /**
     * Get the display name, falling back to name or category_name
     *
     * @return string|null
     */
    public function getDisplayNameAttribute()
    {
        // Return display_name if explicitly set, otherwise fall back to name or category_name
        return $this->getRawOriginal('display_name') ?? $this->name ?? $this->category_name;
    }


    /**
     * Get the journal slug
     *
     * @return string|null
     */
    public function getJournalSlug(): ?string
    {
        return $this->journal_slug;
    }

    /**
     * Generate a URL-friendly slug from the name
     *
     * @return string
     */
    public function generateJournalSlug(): string
    {
        $name = $this->display_name ?? $this->name ?? $this->category_name;
        return \Str::slug($name);
    }

    // ========================================
    // SCOPES FOR QUERYING
    // ========================================


    /**
     * Scope to only include journals
     *
     * @param \Illuminate\Database\EloquentBuilder $query
     * @return \Illuminate\Database\EloquentBuilder
     */
    public static function scopeJournals($query)
    {
        return $query->where('is_journal', true);
    }

    /**
     * Scope to only include categories
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\EloquentBuilder
     */

    public static function scopeCategories($query)
    {
        return $query->where('is_journal', false);
    }

    /**
     * Scope to filter by journal slug
     *
     * @param \Illuminate\Database\Eloquent.Builder $query
     * @param string $slug
     * @return \Illuminate\Database\Eloquent Builder
     */

    public static function scopeByJournalSlug($query, $slug)
    {
        return $query->where('journal_slug', $slug);
    }

    /**
     * Scope to only include active journals
     *
     * @param \Illuminate\Database\EloquentBuilder $query
     * @return \Illuminate\Database\EloquentBuilder
     */

    public static function scopeActiveJournals($query)
    {
        return $query->where('is_journal', true)
                    ->where('status', 'Active');
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

    // ========================================
    // SPRINT 2: JOURNAL RELATIONSHIPS
    // ========================================

    /**
     * Get the editorial board for this journal
     */
    public function editorialBoard()
    {
        return $this->hasMany(JournalEditorialBoard::class, 'journal_id')
                    ->where('is_active', true)
                    ->orderBy('display_order', 'asc');
    }

    /**
     * Get all editorial board members (including inactive)
     */
    public function allEditorialBoard()
    {
        return $this->hasMany(JournalEditorialBoard::class, 'journal_id');
    }

    /**
     * Get journal memberships
     */
    public function memberships()
    {
        return $this->hasMany(JournalMembership::class, 'journal_id');
    }

    /**
     * Get journal memberships (alias for memberships())
     */
    public function journalMemberships()
    {
        return $this->memberships();
    }

    /**
     * Get active journal memberships
     */
    public function activeMemberships()
    {
        return $this->hasMany(JournalMembership::class, 'journal_id')
                    ->where('status', JournalMembership::STATUS_ACTIVE);
    }

    /**
     * Get journal articles
     */
    public function journal_articles()
    {
        return $this->hasMany(Article::class, 'journal_id');
    }


/**
     * Get journal articles (alias for journal_articles)
     */
    public function journalArticles()
    {
        return $this->journal_articles();
    }

    /**
     * Get published journal articles
     */
    public function publishedArticles()
    {
        return $this->hasMany(Article::class, 'journal_id')
                    ->where('article_status', 3); // Published status
    }

    // ========================================
    // SPRINT 2: JOURNAL HELPER METHODS
    // ========================================

    /**
     * Get members with specific role in this journal
     */
    public function getMembersByRole($memberTypeId)
    {
        return $this->memberships()
                    ->where('member_type_id', $memberTypeId)
                    ->where('status', JournalMembership::STATUS_ACTIVE)
                    ->with('member')
                    ->get()
                    ->pluck('member');
    }

    /**
     * Get journal editors
     */
    public function getEditorsAttribute()
    {
        return $this->getMembersByRole(2); // Editor type
    }

    /**
     * Get journal reviewers
     */
    public function getReviewersAttribute()
    {
        return $this->getMembersByRole(3); // Reviewer type
    }

    /**
     * Get journal authors
     */
    public function getAuthorsAttribute()
    {
        return $this->getMembersByRole(1); // Author type
    }

    /**
     * Check if member has access to this journal
     */
    public function hasMemberAccess($memberId, $memberTypeId = null): bool
    {
        $query = $this->memberships()
                      ->where('member_id', $memberId)
                      ->where('status', JournalMembership::STATUS_ACTIVE);

        if ($memberTypeId) {
            $query->where('member_type_id', $memberTypeId);
        }

        return $query->exists();
    }

    /**
     * Check if member is editor for this journal
     */
    public function hasEditor($memberId): bool
    {
        return $this->hasMemberAccess($memberId, 2);
    }

    /**
     * Check if member is reviewer for this journal
     */
    public function hasReviewer($memberId): bool
    {
        return $this->hasMemberAccess($memberId, 3);
    }

    /**
     * Get active editorial board count
     */
    public function getEditorialBoardCountAttribute()
    {
        return $this->editorialBoard()->count();
    }

    /**
     * Get active memberships count
     */
    public function getActiveMembershipsCountAttribute()
    {
        return $this->activeMemberships()->count();
    }

    /**
     * Get journal memberships count (all memberships)
     */
    public function getJournalMembershipsCountAttribute()
    {
        return $this->memberships()->count();
    }

    /**
     * Get articles count
     */
    public function getArticlesCountAttribute()
    {
        return $this->articles()->count();
    }

    /**
     * Check if journal is active
     */
    public function getIsActiveAttribute()
    {
        return $this->is_journal && !is_null($this->journal_url);
    }

    /**
     * Get journal statistics
     */
    public function getJournalStatsAttribute()
    {
        if (!$this->is_journal) {
            return null;
        }

        return [
            'articles_count' => $this->publishedArticles()->count(),
            'memberships_count' => $this->activeMemberships()->count(),
            'editorial_board_count' => $this->editorialBoard()->count(),
        ];
    }

    /**
     * Get journal URL based on acronym
     */
    public function getJournalUrlAttribute()
    {
        if ($this->journal_acronym) {
            return "/journals/{$this->journal_acronym}/";
        }

        if ($this->journal_slug) {
            return "/journals/{$this->journal_slug}/";
        }

        return null;
    }

    /**
     * Generate journal acronym from name
     */
    public function generateAcronym(): string
    {
        if (!$this->name && !$this->category_name) {
            return '';
        }

        $name = $this->name ?? $this->category_name;

        // Extract first letters of each word
        $words = explode(' ', $name);
        $acronym = '';

        foreach ($words as $word) {
            if (strlen($word) > 0 && !in_array(strtolower($word), ['the', 'of', 'and', 'for', 'in', 'on'])) {
                $acronym .= strtoupper($word[0]);
            }
        }

        return $acronym;
    }

    /**
     * Assign member to journal
     */
    public function assignMember($memberId, $memberTypeId, $assignedBy = null)
    {
        return JournalMembership::create([
            'member_id' => $memberId,
            'journal_id' => $this->id,
            'member_type_id' => $memberTypeId,
            'status' => JournalMembership::STATUS_ACTIVE,
            'assigned_by' => $assignedBy,
            'assigned_at' => now(),
        ]);
    }

    /**
     * Remove member from journal
     */
    public function removeMember($memberId, $memberTypeId = null)
    {
        $query = $this->memberships()
                      ->where('member_id', $memberId);

        if ($memberTypeId) {
            $query->where('member_type_id', $memberTypeId);
        }

        return $query->delete();
    }

}
