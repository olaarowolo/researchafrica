# Sprint 2 Implementation Plan - Database Architecture Enhancement

**Sprint Duration**: Weeks 4-6 (3 weeks)  
**Priority**: Critical  
**Risk Level**: Medium  
**Status**: 🚀 **READY TO START**

---

## Executive Summary

Sprint 2 builds upon the solid foundation established in Sprint 1 by implementing the core multi-tenancy database architecture. This sprint focuses on creating journal-specific data isolation, editorial board management, and journal membership tracking systems.

### Sprint 1 Achievements Recap ✅

-   ✅ Semantic clarity fields added to ArticleCategory model
-   ✅ Backward compatibility maintained (100%)
-   ✅ Database backup and rollback procedures operational
-   ✅ Zero data loss migration strategy validated
-   ✅ Comprehensive test coverage (9/9 tests passing)

### Sprint 2 Goals 🎯

1. **Multi-Tenancy Database Structure**: Implement journal-specific data isolation at database level
2. **Editorial Board Management**: Create structured editorial board system per journal
3. **Journal Membership Framework**: Build comprehensive membership tracking system
4. **Data Isolation Framework**: Ensure complete data separation between journals

---

## Current State Analysis

### Existing Database Structure

#### ✅ ArticleCategory Table (Enhanced in Sprint 1)

```sql
- id (BIGINT, PRIMARY KEY)
- name (VARCHAR, nullable) ✅ NEW
- display_name (VARCHAR, nullable) ✅ NEW
- is_journal (BOOLEAN, nullable) ✅ NEW
- journal_slug (VARCHAR, nullable) ✅ NEW
- category_name (VARCHAR, nullable)
- status (VARCHAR)
- description (TEXT)
- aim_scope (TEXT)
- editorial_board (TEXT)
- submission (TEXT)
- subscribe (TEXT)
- issn (VARCHAR)
- online_issn (VARCHAR)
- doi_link (VARCHAR)
- journal_url (VARCHAR)
- parent_id (BIGINT)
```

#### ✅ Articles Table (Needs Enhancement)

```sql
- id (BIGINT, PRIMARY KEY)
- member_id (BIGINT, FOREIGN KEY)
- article_category_id (BIGINT, FOREIGN KEY)
- article_sub_category_id (BIGINT, FOREIGN KEY)
- title (VARCHAR)
- author_name (VARCHAR)
- other_authors (TEXT)
- corresponding_authors (TEXT)
- institute_organization (VARCHAR)
- amount (DECIMAL)
- doi_link (VARCHAR)
- volume (VARCHAR)
- issue_no (VARCHAR)
- publish_date (DATE)
- published_online (TIMESTAMP)
- access_type (ENUM)
- article_status (ENUM)
- is_recommended (BOOLEAN)
- storage_disk (VARCHAR)
- file_path (VARCHAR)
❌ MISSING: journal_id (BIGINT, FOREIGN KEY)
```

#### ✅ Members Table (Needs Enhancement)

```sql
- id (BIGINT, PRIMARY KEY)
- email_address (VARCHAR)
- password (VARCHAR)
- title (VARCHAR)
- first_name (VARCHAR)
- middle_name (VARCHAR)
- last_name (VARCHAR)
- date_of_birth (DATE)
- member_type_id (BIGINT, FOREIGN KEY)
- phone_number (VARCHAR)
- country_id (BIGINT, FOREIGN KEY)
- state_id (BIGINT, FOREIGN KEY)
- member_role_id (BIGINT, FOREIGN KEY)
- gender (VARCHAR)
- address (TEXT)
- registration_via (VARCHAR)
- email_verified (BOOLEAN)
- email_verified_at (TIMESTAMP)
- verified (BOOLEAN)
- profile_completed (BOOLEAN)
❌ MISSING: Journal-specific relationships
```

### Gap Analysis

#### ❌ Missing Tables

1. **journal_editorial_boards** - Editorial board management per journal
2. **journal_memberships** - Journal membership tracking with status
3. **journal_settings** - Journal-specific configurations (optional for Sprint 2)

#### ❌ Missing Relationships

1. Articles → Journal (journal_id foreign key)
2. Members → Journal Memberships (many-to-many through journal_memberships)
3. Editorial Board → Journal (through journal_editorial_boards)

#### ❌ Missing Functionality

1. Journal-scoped query methods
2. Data isolation middleware
3. Permission scoping per journal
4. Editorial board assignment logic

---

## Sprint 2 Detailed Implementation Plan

### Phase 1: Database Schema Enhancement (Week 1)

#### Task 1.1: Create Editorial Board Management Table

**Migration**: `2025_12_19_000001_create_journal_editorial_boards_table.php`

```php
Schema::create('journal_editorial_boards', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('journal_id');
    $table->unsignedBigInteger('member_id');
    $table->string('position', 100); // e.g., "Editor-in-Chief", "Associate Editor"
    $table->string('department', 255)->nullable();
    $table->string('institution', 255)->nullable();
    $table->text('bio')->nullable();
    $table->string('orcid_id', 50)->nullable();
    $table->date('term_start')->nullable();
    $table->date('term_end')->nullable();
    $table->boolean('is_active')->default(true);
    $table->integer('display_order')->default(0);
    $table->timestamps();
    $table->softDeletes();

    // Foreign keys
    $table->foreign('journal_id')
          ->references('id')
          ->on('article_categories')
          ->onDelete('cascade');

    $table->foreign('member_id')
          ->references('id')
          ->on('members')
          ->onDelete('cascade');

    // Indexes
    $table->index(['journal_id', 'is_active']);
    $table->index(['member_id', 'is_active']);
    $table->index('position');

    // Unique constraint: One active position per member per journal
    $table->unique(['journal_id', 'member_id', 'position', 'is_active'], 'unique_active_editor');
});
```

**Purpose**:

-   Manage editorial board members for each journal
-   Track editorial positions and terms
-   Support multiple editors per journal
-   Enable editorial board display on journal pages

#### Task 1.2: Create Journal Membership Tracking Table

**Migration**: `2025_12_19_000002_create_journal_memberships_table.php`

```php
Schema::create('journal_memberships', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('member_id');
    $table->unsignedBigInteger('journal_id');
    $table->unsignedBigInteger('member_type_id'); // Author, Editor, Reviewer, etc.
    $table->enum('status', ['active', 'inactive', 'pending', 'suspended'])->default('pending');
    $table->unsignedBigInteger('assigned_by')->nullable(); // Admin who assigned
    $table->timestamp('assigned_at')->nullable();
    $table->timestamp('expires_at')->nullable();
    $table->text('notes')->nullable();
    $table->timestamps();
    $table->softDeletes();

    // Foreign keys
    $table->foreign('member_id')
          ->references('id')
          ->on('members')
          ->onDelete('cascade');

    $table->foreign('journal_id')
          ->references('id')
          ->on('article_categories')
          ->onDelete('cascade');

    $table->foreign('member_type_id')
          ->references('id')
          ->on('member_types')
          ->onDelete('cascade');

    $table->foreign('assigned_by')
          ->references('id')
          ->on('members')
          ->onDelete('set null');

    // Indexes
    $table->index(['member_id', 'journal_id', 'status']);
    $table->index(['journal_id', 'member_type_id', 'status']);
    $table->index('status');

    // Unique constraint: One active membership per member per journal per type
    $table->unique(['member_id', 'journal_id', 'member_type_id', 'status'], 'unique_active_membership');
});
```

**Purpose**:

-   Track member associations with specific journals
-   Manage member roles per journal (Author, Editor, Reviewer)
-   Support membership status tracking (active, pending, suspended)
-   Enable journal-scoped user permissions

#### Task 1.3: Add Journal Context to Articles Table

**Migration**: `2025_12_19_000003_add_journal_context_to_articles_table.php`

```php
Schema::table('articles', function (Blueprint $table) {
    $table->unsignedBigInteger('journal_id')->nullable()->after('id');

    // Foreign key
    $table->foreign('journal_id')
          ->references('id')
          ->on('article_categories')
          ->onDelete('set null');

    // Indexes for performance
    $table->index(['journal_id', 'article_status']);
    $table->index(['journal_id', 'created_at']);
    $table->index(['journal_id', 'member_id']);
});
```

**Purpose**:

-   Link articles directly to journals
-   Enable journal-scoped article queries
-   Support data isolation at database level
-   Improve query performance with proper indexing

#### Task 1.4: Add Journal Configuration Fields to ArticleCategory

**Migration**: `2025_12_19_000004_add_journal_configuration_to_article_categories.php`

```php
Schema::table('article_categories', function (Blueprint $table) {
    // Journal identification
    $table->string('journal_acronym', 10)->nullable()->after('journal_slug');

    // Multi-domain support (for future sprints)
    $table->string('subdomain', 100)->nullable()->after('journal_acronym');
    $table->string('custom_domain', 255)->nullable()->after('subdomain');

    // Journal configuration
    $table->json('theme_config')->nullable()->after('custom_domain');
    $table->json('email_settings')->nullable()->after('theme_config');
    $table->json('submission_settings')->nullable()->after('email_settings');

    // Journal metadata
    $table->string('publisher_name', 255)->nullable()->after('submission_settings');
    $table->string('editor_in_chief', 255)->nullable()->after('publisher_name');
    $table->text('contact_email')->nullable()->after('editor_in_chief');

    // Indexes
    $table->unique('journal_acronym');
    $table->unique('subdomain');
    $table->unique('custom_domain');
    $table->index('is_journal');
});
```

**Purpose**:

-   Add journal acronym for URL routing (Sprint 4 preparation)
-   Support future multi-domain functionality
-   Store journal-specific configurations
-   Enable journal branding and customization

#### Task 1.5: Data Migration Script for Existing Articles

**Migration**: `2025_12_19_000005_migrate_existing_articles_to_journals.php`

```php
public function up()
{
    // Migrate existing articles to their respective journals
    // Based on article_category_id or article_sub_category_id

    DB::statement('
        UPDATE articles a
        INNER JOIN article_categories ac ON a.article_sub_category_id = ac.id
        SET a.journal_id = ac.id
        WHERE ac.is_journal = 1
    ');

    // For articles without journal assignment, use parent category
    DB::statement('
        UPDATE articles a
        INNER JOIN article_categories ac ON a.article_category_id = ac.id
        SET a.journal_id = ac.id
        WHERE a.journal_id IS NULL AND ac.is_journal = 1
    ');

    // Log articles that couldn't be migrated
    $unmigrated = DB::table('articles')
        ->whereNull('journal_id')
        ->count();

    if ($unmigrated > 0) {
        Log::warning("Sprint 2 Migration: {$unmigrated} articles could not be assigned to journals");
    }
}

public function down()
{
    Schema::table('articles', function (Blueprint $table) {
        $table->dropColumn('journal_id');
    });
}
```

**Purpose**:

-   Safely migrate existing articles to journal structure
-   Maintain data integrity during migration
-   Log any migration issues for manual review
-   Support rollback if needed

---

### Phase 2: Model Enhancement (Week 2)

#### Task 2.1: Create JournalEditorialBoard Model

**File**: `app/Models/JournalEditorialBoard.php`

```php
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
```

#### Task 2.2: Create JournalMembership Model

**File**: `app/Models/JournalMembership.php`

```php
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
```

#### Task 2.3: Enhance Article Model with Journal Context

**File**: `app/Models/Article.php` (Add to existing model)

```php
// Add to existing Article model

/**
 * Get the journal this article belongs to
 */
public function journal()
{
    return $this->belongsTo(ArticleCategory::class, 'journal_id');
}

/**
 * Get the editorial board for this article's journal
 */
public function editorialBoard()
{
    return $this->hasManyThrough(
        JournalEditorialBoard::class,
        ArticleCategory::class,
        'id',           // article_categories.id
        'journal_id',   // editorial_boards.journal_id
        'journal_id',   // articles.journal_id
        'id'            // article_categories.id
    )->where('is_active', true);
}

/**
 * Scope to filter articles by journal
 */
public function scopeForJournal($query, $journalId)
{
    return $query->where('journal_id', $journalId);
}

/**
 * Scope to filter articles by journal slug
 */
public function scopeForJournalSlug($query, $slug)
{
    return $query->whereHas('journal', function ($q) use ($slug) {
        $q->where('journal_slug', $slug);
    });
}

/**
 * Scope to filter articles by journal acronym
 */
public function scopeForJournalAcronym($query, $acronym)
{
    return $query->whereHas('journal', function ($q) use ($acronym) {
        $q->where('journal_acronym', $acronym);
    });
}

/**
 * Check if article belongs to a specific journal
 */
public function belongsToJournal($journalId): bool
{
    return $this->journal_id == $journalId;
}
```

#### Task 2.4: Enhance ArticleCategory Model with Journal Relationships

**File**: `app/Models/ArticleCategory.php` (Add to existing model)

```php
// Add to existing ArticleCategory model

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
public function journalArticles()
{
    return $this->hasMany(Article::class, 'journal_id');
}

/**
 * Get published journal articles
 */
public function publishedArticles()
{
    return $this->hasMany(Article::class, 'journal_id')
                ->where('article_status', 3); // Published status
}

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
```

#### Task 2.5: Enhance Member Model with Journal Relationships

**File**: `app/Models/Member.php` (Add to existing model)

```php
// Add to existing Member model

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
     ->where('article_categories.is_journal', true);
}

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
```

---

### Phase 3: Data Isolation Framework (Week 3)

#### Task 3.1: Create Journal Context Service

**File**: `app/Services/JournalContextService.php`

```php
<?php

namespace App\Services;

use App\Models\ArticleCategory;
use Illuminate\Support\Facades\Cache;

class JournalContextService
{
    protected $currentJournal = null;

    /**
     * Get the current journal from request context
     */
    public function getCurrentJournal()
    {
        if ($this->currentJournal) {
            return $this->currentJournal;
        }

        // Try to get from app instance
        if (app()->has('current_journal')) {
            $this->currentJournal = app('current_journal');
            return $this->currentJournal;
        }

        return null;
    }

    /**
     * Set the current journal context
     */
    public function setCurrentJournal($journal)
    {
        $this->currentJournal = $journal;
        app()->instance('current_journal', $journal);
        app()->instance('current_journal_id', $journal->id);
    }

    /**
     * Get journal by slug with caching
     */
    public function getJournalBySlug($slug)
    {
        return Cache::remember("journal_slug_{$slug}", 3600, function () use ($slug) {
            return ArticleCategory::where('journal_slug', $slug)
                                 ->where('is_journal', true)
                                 ->where('status', 'Active')
                                 ->first();
        });
    }

    /**
     * Get journal by acronym with caching
     */
    public function getJournalByAcronym($acronym)
    {
        return Cache::remember("journal_acronym_{$acronym}", 3600, function () use ($acronym) {
            return ArticleCategory::where('journal_acronym', $acronym)
                                 ->where('is_journal', true)
                                 ->where('status', 'Active')
                                 ->first();
        });
    }

    /**
     * Get journal by ID with caching
     */
    public function getJournalById($id)
    {
        return Cache::remember("journal_id_{$id}", 3600, function () use ($id) {
            return ArticleCategory::where('id', $id)
                                 ->where('is_journal', true)
                                 ->first();
        });
    }

    /**
     * Clear journal cache
     */
    public function clearJournalCache($journal)
    {
        Cache::forget("journal_id_{$journal->id}");
        Cache::forget("journal_slug_{$journal->journal_slug}");
        Cache::forget("journal_acronym_{$journal->journal_acronym}");
    }

    /**
     * Check if user has access to journal
     */
    public function userHasAccess($user, $journalId, $memberTypeId = null): bool
    {
        if (!$user) {
            return false;
        }

        return $user->hasJournalAccess($journalId, $memberTypeId);
    }

    /**
     * Get accessible journals for user
     */
    public function getUserJournals($user)
    {
        if (!$user) {
            return collect();
        }

        return $user->accessibleJournals;
    }
}
```

#### Task 3.2: Create Journal Scope Middleware

**File**: `app/Http/Middleware/SetJournalContext.php`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\JournalContextService;

class SetJournalContext
{
    protected $journalService;

    public function __construct(JournalContextService $journalService)
    {
        $this->journalService = $journalService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Try to resolve journal from route parameters
        $journal = $this->resolveJournalFromRequest($request);

        if ($journal) {
            $this->journalService->setCurrentJournal($journal);

            // Add journal info to view
            view()->share('currentJournal', $journal);
        }

        return $next($request);
    }

    /**
     * Resolve journal from request
     */
    protected function resolveJournalFromRequest(Request $request)
    {
        // Try acronym first (for Sprint 4 URL structure)
        if ($request->route('acronym')) {
            return $this->journalService->getJournalByAcronym(
                $request->route('acronym')
            );
        }

        // Try slug
        if ($request->route('journal_slug')) {
            return $this->journalService->getJournalBySlug(
                $request->route('journal_slug')
            );
        }

        // Try journal_id
        if ($request->route('journal_id')) {
            return $this->journalService->getJournalById(
                $request->route('journal_id')
            );
        }

        // Try from query parameter
        if ($request->query('journal_id')) {
            return $this->journalService->getJournalById(
                $request->query('journal_id')
            );
        }

        return null;
    }
}
```

#### Task 3.3: Create Journal Access Middleware

**File**: `app/Http/Middleware/EnsureJournalAccess.php`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\JournalContextService;

class EnsureJournalAccess
{
    protected $journalService;

    public function __
```
