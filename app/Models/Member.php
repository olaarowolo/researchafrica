<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use App\Models\MemberRole;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Foundation\Auth\User;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Member extends User implements HasMedia
{
    // ...existing code...
    public function roles()
    {
        // Use the correct pivot table name 'role_user' (user_id, role_id)
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id');
    }
    // ...existing code...
    use SoftDeletes, InteractsWithMedia, HasFactory;

    public $table = 'members';

    protected $appends = [
        'profile_picture',
    ];

    protected $hidden = [
        'password',
    ];

    public const MEMBER_ROLE = [
        '1' => 'Author',
        '2' => 'Editor',
        '3' => 'Reviewer',
        '4' => 'Account',
        '5' => 'Pubisher',
        '6' => 'Reviewer Final',
    ];

    public const VERIFIED_SELECT = [
        '1' => 'Yes',
        '2' => 'No',
    ];

    public const EMAIL_VERIFIED_SELECT = [
        '1' => 'Yes',
        '0' => 'No',
    ];

    public const PROFILE_COMPLETED_SELECT = [
        '1' => 'Yes',
        '2' => 'No',
    ];

    public const GENDER_RADIO = [
        'Male'   => 'Male',
        'Female' => 'Female',
    ];

    public const REGISTRATION_VIA_SELECT = [
        'email'  => 'Email',
        'google' => 'Google login',
    ];

    protected $dates = [
        'date_of_birth',
        'email_verified_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const TITLE_SELECT = [
        'Mr'     => 'Mr',
        'Mrs'    => 'Mrs',
        'Master' => 'Master',
        'Miss'   => 'Miss',
        'Dr'     => 'Dr',
        'Prof'   => 'Prof',
    ];



    protected $fillable = [
        'email_address',
        'password',
        'title',
        'first_name',
        'middle_name',
        'last_name',
        'date_of_birth',
        'member_type_id',
        'phone_number',
        'country_id',
        'state_id',
        'member_role_id',
        'gender',
        'address',
        'registration_via',
        'email_verified',
        'email_verified_at',
        'verified',
        'profile_completed',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function purchasedArticle()
    {
        return $this->hasMany(PurchasedArticle::class);
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }


    public function getBookmarkCountAttribute()
    {
        return $this->bookmarks()->count();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')->fit('crop', 50, 50);
        $this->addMediaConversion('preview')->fit('crop', 120, 120);
    }

    public function memberArticles()
    {
        return $this->hasMany(Article::class, 'member_id', 'id');
    }

    public function getUnderReviewAttribute()
    {
        return $this->memberArticles()->where('article_status', 2);
    }

    public function member_role()
    {
        return $this->belongsTo(MemberRole::class);
    }

    // public function getAuthorMemberAttribute()
    // {
    //     return $this->where('member_type_id', 1)->exists();
    // }

    // public function getEditorMemberAttribute()
    // {
    //     return $this->where('member_type_id', 2)->exists();
    // }

    // public function getReviewMemberAttribute()
    // {
    //     return $this->where('member_type_id', 3)->exists();
    // }

    public function getIsEmailVerifyAttribute()
    {
        return !is_null($this->email_verified_at) && $this->email_verified == 1;
    }

    public function getFullnameAttribute()
    {
        return $this->first_name ." ". $this->last_name;
    }

    public function getAuthIdentifierName()
    {
        return 'email_address';
    }

    public function id()
    {
        return $this->getAttribute('id');
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }
    public function getDateOfBirthAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setDateOfBirthAttribute($value)
    {
        $this->attributes['date_of_birth'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function member_type()
    {
        return $this->belongsTo(MemberType::class, 'member_type_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }
    public function getProfilePictureAttribute()
    {
        $file = $this->getMedia('profile_picture')->last();
        if ($file) {
            $file->url       = $file->getUrl();
            $file->thumbnail = $file->getUrl('thumb');
            $file->preview   = $file->getUrl('preview');
        }

        return $file;
    }

    public function getEmailVerifiedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('panel.date_format') . ' ' . config('panel.time_format')) : null;
    }

    public function setEmailVerifiedAtAttribute($value)
    {
        $this->attributes['email_verified_at'] = $value ? Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value)->format('Y-m-d H:i:s') : null;
    }

    // ========================================
    // SPRINT 2: JOURNAL RELATIONSHIPS
    // ========================================

    /**
     * Get journal memberships for this member
     */
    public function journalMemberships()
    {
        return $this->hasMany(JournalMembership::class, 'member_id');
    }

    /**
     * Get active journal memberships
     */
    public function activeJournalMemberships()
    {
        return $this->hasMany(JournalMembership::class, 'member_id')
                    ->where('status', JournalMembership::STATUS_ACTIVE);
    }

    /**
     * Get editorial board positions
     */
    public function editorialPositions()
    {
        return $this->hasMany(JournalEditorialBoard::class, 'member_id')
                    ->where('is_active', true);
    }

    /**
     * Get all editorial board positions (including inactive)
     */
    public function allEditorialPositions()
    {
        return $this->hasMany(JournalEditorialBoard::class, 'member_id');
    }

    /**
     * Get journals this member has access to
     */
    public function accessibleJournals()
    {
        return $this->belongsToMany(
            ArticleCategory::class,
            'journal_memberships',
            'member_id',
            'journal_id'
        )->where('journal_memberships.status', JournalMembership::STATUS_ACTIVE)
         ->where('article_categories.is_journal', true)
         ->withPivot('member_type_id', 'status', 'assigned_at', 'expires_at');
    }

    // ========================================
    // SPRINT 2: JOURNAL HELPER METHODS
    // ========================================

    /**
     * Check if member has access to a specific journal
     */
    public function hasJournalAccess($journalId, $memberTypeId = null): bool
    {
        $query = $this->journalMemberships()
                      ->where('journal_id', $journalId)
                      ->where('status', JournalMembership::STATUS_ACTIVE);

        if ($memberTypeId) {
            $query->where('member_type_id', $memberTypeId);
        }

        return $query->exists();
    }

    /**
     * Check if member is editor for a journal
     */
    public function isEditorFor($journalId): bool
    {
        return $this->hasJournalAccess($journalId, 2); // Editor type
    }

    /**
     * Check if member is reviewer for a journal
     */
    public function isReviewerFor($journalId): bool
    {
        return $this->hasJournalAccess($journalId, 3); // Reviewer type
    }

    /**
     * Check if member is author for a journal
     */
    public function isAuthorFor($journalId): bool
    {
        return $this->hasJournalAccess($journalId, 1); // Author type
    }

    /**
     * Get journals where member is an editor
     */
    public function editorJournals()
    {
        return $this->accessibleJournals()
                    ->wherePivot('member_type_id', 2);
    }

    /**
     * Get journals where member is a reviewer
     */
    public function reviewerJournals()
    {
        return $this->accessibleJournals()
                    ->wherePivot('member_type_id', 3);
    }

    /**
     * Get journals where member is an author
     */
    public function authorJournals()
    {
        return $this->accessibleJournals()
                    ->wherePivot('member_type_id', 1);
    }

    /**
     * Get editorial board positions for a specific journal
     */
    public function getEditorialPositionFor($journalId)
    {
        return $this->editorialPositions()
                    ->where('journal_id', $journalId)
                    ->first();
    }

    /**
     * Check if member is on editorial board of a journal
     */
    public function isOnEditorialBoard($journalId): bool
    {
        return $this->editorialPositions()
                    ->where('journal_id', $journalId)
                    ->exists();
    }

    /**
     * Get count of journals member has access to
     */
    public function getAccessibleJournalsCountAttribute()
    {
        return $this->accessibleJournals()->count();
    }

    /**
     * Get count of editorial board positions
     */
    public function getEditorialPositionsCountAttribute()
    {
        return $this->editorialPositions()->count();
    }

    /**
     * Assign member to a journal
     */
    public function assignToJournal($journalId, $memberTypeId, $assignedBy = null)
    {
        return JournalMembership::create([
            'member_id' => $this->id,
            'journal_id' => $journalId,
            'member_type_id' => $memberTypeId,
            'status' => JournalMembership::STATUS_ACTIVE,
            'assigned_by' => $assignedBy,
            'assigned_at' => now(),
        ]);
    }

    /**
     * Remove member from a journal
     */
    public function removeFromJournal($journalId, $memberTypeId = null)
    {
        $query = $this->journalMemberships()
                      ->where('journal_id', $journalId);

        if ($memberTypeId) {
            $query->where('member_type_id', $memberTypeId);
        }

        return $query->delete();
    }
}
