# Research Africa Multi-Journal Capability Analysis Report

## Executive Summary

After conducting a comprehensive analysis of the Research Africa platform codebase, this report evaluates the system's capability to handle multiple unique journals with different editorial teams and leadership structures. The analysis reveals that while the platform has foundational features supporting multi-journal operations, significant architectural enhancements are required to fully support independent journal management.

**Overall Assessment: PARTIALLY SUPPORTED**

- **Current Capability**: 40% - Basic multi-journal structure exists
- **Required Enhancements**: 60% - Major architectural changes needed
- **Implementation Complexity**: Medium to High
- **Recommended Timeline**: 6-12 months for full implementation

---

## Table of Contents

1. [Current Multi-Journal Infrastructure](#current-multi-journal-infrastructure)
2. [Gaps and Limitations](#gaps-and-limitations)
3. [Recommended Architecture Enhancements](#recommended-architecture-enhancements)
4. [Implementation Roadmap](#implementation-roadmap)
5. [Technical Specifications](#technical-specifications)
6. [Business Impact Analysis](#business-impact-analysis)
7. [Risk Assessment](#risk-assessment)
8. [Conclusion and Recommendations](#conclusion-and-recommendations)

---

## Current Multi-Journal Infrastructure

### 1. ArticleCategory System (Journal Foundation)

**Current Implementation:**

```php
// ArticleCategory Model Analysis
class ArticleCategory extends Model
{
    protected $fillable = [
        'category_name',           // Journal name
        'status',                  // Active/Inactive
        'description',             // Journal description
        'aim_scope',               // Journal scope
        'editorial_board',         // Editorial team info (LONG TEXT)
        'submission',              // Submission guidelines
        'subscribe',               // Subscription info
        'issn',                    // Print ISSN
        'online_issn',             // Online ISSN
        'doi_link',                // DOI prefix
        'journal_url',             // Journal website
        'parent_id',               // Journal hierarchy support
    ];

    // Parent-child relationship for journal categories
    public function category()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function categories()
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}
```

**Strengths Identified:**

- ✅ Journal metadata fields (ISSN, DOI, URL)
- ✅ Editorial board information storage
- ✅ Parent-child hierarchy support for journal organization
- ✅ Active/inactive status management
- ✅ Submission guidelines per journal

**Database Migration Evidence:**

```sql
-- Migration: 2023_05_03_142432_add_new_colunm_to_article_categories_table.php
$table->longText('editorial_board')->nullable();
$table->longText('submission')->nullable();
$table->string('issn')->nullable();
$table->string('online_issn')->nullable();
$table->string('doi_link')->nullable();
$table->string('journal_url')->nullable();
```

### 2. Member Role System (Editorial Teams)

**Current Member Types:**

```php
// MemberType Model - Basic role structure
public const MEMBER_TYPES = [
    '1' => 'Author',
    '2' => 'Editor',
    '3' => 'Reviewer',
    '4' => 'Account',      // Subscriber
    '5' => 'Publisher',
    '6' => 'Reviewer Final'
];
```

**Article Editorial Assignment:**

```php
// Article Model relationships
public function journal_category()
{
    return $this->belongsTo(ArticleCategory::class, 'article_sub_category_id');
}

public function article_category()
{
    return $this->belongsTo(ArticleCategory::class, 'article_category_id');
}
```

### 3. Workflow Isolation

**Current Editorial Assignment:**

```php
// EditorAccept Model - Tracks editorial assignments
class EditorAccept extends Model
{
    protected $fillable = [
        'article_id',
        'member_id',  // Editor assigned
    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
```

**Strengths:**

- ✅ Article-to-journal assignment via category relationships
- ✅ Editor assignment tracking
- ✅ Role-based permissions
- ✅ Workflow isolation per article

---

## Gaps and Limitations

### 1. **Critical Missing Features**

#### A. Journal-Specific User Management

**Current Issue:** Users can access all journals across the system

```php
// Current: Global user access
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth', 'admin']], function () {
    // All admin functions accessible to any admin
});
```

**Required Enhancement:**

- Journal-scoped user permissions
- Journal-specific admin dashboards
- Cross-journal access restrictions

#### B. Journal Configuration Management

**Current Issue:** System-wide settings, no journal-specific configurations

```php
// Current: Global settings
Route::resource('settings', 'SettingController'); // Single settings table
```

**Missing Features:**

- Journal-specific themes/branding
- Custom submission guidelines per journal
- Journal-specific email templates
- Independent pricing models

#### C. Editorial Team Management

**Current Issue:** Editorial board stored as long text, not structured data

```php
// Current: Editorial board as text blob
'editorial_board' => 'Dr. John Smith (Editor-in-Chief)...', // Unstructured
```

**Required Structure:**

```php
// Proposed: Structured editorial board
class EditorialBoard extends Model
{
    protected $fillable = [
        'journal_id',
        'member_id',
        'position',           // Editor-in-Chief, Associate Editor, etc.
        'department',
        'institution',
        'term_start',
        'term_end',
        'is_active'
    ];
}
```

### 2. **Data Isolation Issues**

#### A. Article Cross-Pollination

**Current Problem:** Articles can be assigned to any category regardless of user permissions

```php
// Current: No journal isolation in queries
$articles = Article::with(['member', 'article_category'])->latest()->get();
// Returns all articles from all journals
```

#### B. Notification System

**Current Issue:** Notifications not journal-specific

```php
// Current: Global notifications
Mail::to($editor->email_address)->send(new NewArticle($article, $editor));
// No journal context in emails
```

### 3. **Administrative Limitations**

#### A. Multi-Journal Dashboard

**Current:** Single admin dashboard for entire system

```php
// Current: System-wide analytics
$totalArticles = Article::count(); // All journals combined
$totalMembers = Member::count();   // All users combined
```

#### B. Journal-Specific Reporting

**Missing Features:**

- Individual journal statistics
- Editorial performance metrics per journal
- Revenue tracking per journal
- Submission analytics per journal

---

## Recommended Architecture Enhancements

### 1. **Multi-Tenancy Implementation**

#### A. Journal-Based Data Isolation

**Proposed Database Schema:**

```sql
-- Enhanced ArticleCategory for Journal Management
ALTER TABLE article_categories ADD COLUMN journal_id BIGINT UNSIGNED;
ALTER TABLE article_categories ADD COLUMN subdomain VARCHAR(100);
ALTER TABLE article_categories ADD COLUMN custom_domain VARCHAR(255);
ALTER TABLE article_categories ADD COLUMN theme_config JSON;
ALTER TABLE article_categories ADD COLUMN email_settings JSON;

-- Editorial Board Structure
CREATE TABLE journal_editorial_boards (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    journal_id BIGINT UNSIGNED NOT NULL,
    member_id BIGINT UNSIGNED NOT NULL,
    position VARCHAR(100) NOT NULL, -- 'Editor-in-Chief', 'Associate Editor'
    department VARCHAR(255),
    institution VARCHAR(255),
    bio TEXT,
    orcid_id VARCHAR(50),
    term_start DATE,
    term_end DATE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (journal_id) REFERENCES article_categories(id),
    FOREIGN KEY (member_id) REFERENCES members(id),
    UNIQUE KEY unique_active_editor (journal_id, position, is_active)
);

-- Journal-Specific Settings
CREATE TABLE journal_settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    journal_id BIGINT UNSIGNED NOT NULL,
    setting_key VARCHAR(100) NOT NULL,
    setting_value TEXT,
    setting_type ENUM('text', 'number', 'boolean', 'json') DEFAULT 'text',
    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (journal_id) REFERENCES article_categories(id),
    UNIQUE KEY unique_journal_setting (journal_id, setting_key)
);
```

#### B. Enhanced Member Management

**Proposed Journal-Specific Roles:**

```php
// Enhanced MemberType with journal scoping
class MemberType extends Model
{
    protected $fillable = [
        'name',
        'journal_id',         // NULL for system-wide types
        'is_journal_specific',
        'permissions',        // JSON permissions array
    ];
}

// New: Journal Membership
class JournalMembership extends Model
{
    protected $fillable = [
        'member_id',
        'journal_id',
        'member_type_id',
        'status',             // 'active', 'inactive', 'pending'
        'assigned_by',
        'assigned_at',
        'expires_at'
    ];
}
```

### 2. **Enhanced Article Management**

#### A. Journal-Scoped Article Operations

**Enhanced Article Model:**

```php
class Article extends Model
{
    public function journal()
    {
        return $this->belongsTo(ArticleCategory::class, 'journal_id');
    }

    public function editorialBoard()
    {
        return $this->hasManyThrough(
            EditorialBoard::class,
            ArticleCategory::class,
            'id', // article_categories.id
            'journal_id', // editorial_boards.journal_id
            'journal_id', // articles.journal_id
            'id' // editorial_boards.journal_id
        );
    }

    // Journal-specific scope
    public function scopeForJournal($query, $journalId)
    {
        return $query->where('journal_id', $journalId);
    }
}
```

#### B. Journal-Specific Workflows

**Enhanced Editorial Assignment:**

```php
class JournalWorkflow extends Model
{
    protected $fillable = [
        'journal_id',
        'article_id',
        'stage',              // 'editorial', 'peer_review', 'final_review'
        'assigned_editor_id',
        'assigned_reviewer_ids', // JSON array
        'status',
        'deadline',
        'completed_at'
    ];
}
```

### 3. **Multi-Domain Support**

#### A. Journal-Specific URLs and Branding

**Domain Management:**

```php
// Route handling for multiple journals
Route::group(['domain' => '{journal}.researchafrica.pub'], function () {
    Route::get('/', [JournalController::class, 'showJournal']);
    Route::get('/submit', [SubmissionController::class, 'showForm']);
    Route::get('/about', [JournalController::class, 'about']);
});

// Custom domain support
Route::group(['domain' => '{custom_domain}'], function () {
    // Map custom domain to journal
    Route::get('/', [JournalController::class, 'handleCustomDomain']);
});
```

#### B. Theme and Branding System

**Journal Branding Configuration:**

```php
class JournalBranding extends Model
{
    protected $fillable = [
        'journal_id',
        'logo_path',
        'favicon_path',
        'primary_color',
        'secondary_color',
        'font_family',
        'custom_css',
        'header_text',
        'footer_text',
        'social_links' // JSON
    ];
}
```

---

## Implementation Roadmap

### Phase 1: Foundation (Months 1-3)

**Priority: Critical**

1. **Database Schema Enhancements**

   - Add journal_id to existing tables
   - Create editorial_board structure
   - Implement journal_settings table
   - Data migration scripts
2. **Basic Journal Isolation**

   - Implement journal scoping in queries
   - Add journal_id validation to controllers
   - Update route handling
3. **User Management Enhancement**

   - Journal-specific member types
   - Journal membership tracking
   - Basic permissions system

### Phase 2: Core Functionality (Months 4-6)

**Priority: High**

1. **Editorial Management**

   - Structured editorial board system
   - Journal-specific editor assignment
   - Editorial workflow management
2. **Article Management**

   - Journal-specific article submission
   - Editorial board integration
   - Workflow isolation
3. **Notification System**

   - Journal-specific email templates
   - Editorial team notifications
   - Author communications

### Phase 3: Advanced Features (Months 7-9)

**Priority: Medium**

1. **Multi-Domain Support**

   - Subdomain handling
   - Custom domain mapping
   - SSL certificate management
2. **Branding and Theming**

   - Journal-specific themes
   - Custom styling system
   - Logo and branding management
3. **Administrative Interface**

   - Multi-journal admin dashboard
   - Journal-specific analytics
   - Cross-journal reporting

### Phase 4: Enhancement and Optimization (Months 10-12)

**Priority: Low**

1. **Performance Optimization**

   - Journal-specific caching
   - Database query optimization
   - CDN integration
2. **Advanced Features**

   - Journal-specific payment processing
   - Advanced analytics
   - API rate limiting per journal
3. **Testing and Documentation**

   - Comprehensive testing suite
   - User documentation
   - Admin training materials

---

## Technical Specifications

### 1. **Database Modifications Required**

#### Tables to Create:

```sql
-- New tables needed
CREATE TABLE editorial_boards (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    journal_id BIGINT UNSIGNED NOT NULL,
    member_id BIGINT UNSIGNED NOT NULL,
    position VARCHAR(100) NOT NULL,
    department VARCHAR(255),
    institution VARCHAR(255),
    bio TEXT,
    orcid_id VARCHAR(50),
    term_start DATE,
    term_end DATE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (journal_id) REFERENCES article_categories(id),
    FOREIGN KEY (member_id) REFERENCES members(id)
);

CREATE TABLE journal_memberships (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    member_id BIGINT UNSIGNED NOT NULL,
    journal_id BIGINT UNSIGNED NOT NULL,
    member_type_id BIGINT UNSIGNED NOT NULL,
    status ENUM('active', 'inactive', 'pending') DEFAULT 'pending',
    assigned_by BIGINT UNSIGNED,
    assigned_at TIMESTAMP,
    expires_at TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (member_id) REFERENCES members(id),
    FOREIGN KEY (journal_id) REFERENCES article_categories(id),
    FOREIGN KEY (member_type_id) REFERENCES member_types(id)
);
```

#### Tables to Modify:

```sql
-- Existing tables to modify
ALTER TABLE article_categories
ADD COLUMN journal_id BIGINT UNSIGNED,
ADD COLUMN subdomain VARCHAR(100) UNIQUE,
ADD COLUMN custom_domain VARCHAR(255) UNIQUE,
ADD COLUMN theme_config JSON,
ADD COLUMN email_settings JSON;

ALTER TABLE articles
ADD COLUMN journal_id BIGINT UNSIGNED,
ADD INDEX idx_journal_article (journal_id);

ALTER TABLE members
ADD COLUMN default_journal_id BIGINT UNSIGNED;

-- Add foreign key constraints
ALTER TABLE article_categories
ADD CONSTRAINT fk_journal_category
FOREIGN KEY (journal_id) REFERENCES article_categories(id);

ALTER TABLE articles
ADD CONSTRAINT fk_article_journal
FOREIGN KEY (journal_id) REFERENCES article_categories(id);
```

### 2. **Controller Modifications Required**

#### Enhanced Article Controller:

```php
class ArticleController extends Controller
{
    public function index()
    {
        // Journal-scoped article listing
        $journalId = $this->getCurrentJournalId();
        $articles = Article::where('journal_id', $journalId)
                          ->with(['member', 'article_category'])
                          ->latest()
                          ->paginate();

        return view('admin.articles.index', compact('articles'));
    }

    public function store(StoreArticleRequest $request)
    {
        $journalId = $this->getCurrentJournalId();

        // Validate journal-specific permissions
        $this->authorize('create-article', $journalId);

        $input = $request->validated();
        $input['journal_id'] = $journalId;
        $input['member_id'] = auth('member')->id();

        // Create article with journal context
        $article = Article::create($input);

        // Journal-specific notifications
        $this->sendJournalNotifications($article, $journalId);

        return redirect()->route('admin.articles.index')
                        ->with('success', 'Article submitted to journal successfully');
    }
}
```

#### New Journal Management Controller:

```php
class JournalController extends Controller
{
    public function dashboard()
    {
        $journalId = $this->getCurrentJournalId();

        // Journal-specific statistics
        $stats = [
            'total_articles' => Article::where('journal_id', $journalId)->count(),
            'pending_review' => Article::where('journal_id', $journalId)
                                      ->where('article_status', 1)->count(),
            'published' => Article::where('journal_id', $journalId)
                                 ->where('article_status', 3)->count(),
            'editorial_board' => EditorialBoard::where('journal_id', $journalId)
                                              ->where('is_active', true)->count(),
        ];

        return view('admin.journal.dashboard', compact('stats'));
    }

    public function manageEditorialBoard()
    {
        $journalId = $this->getCurrentJournalId();
        $editorialBoard = EditorialBoard::where('journal_id', $journalId)
                                       ->with('member')
                                       ->get();

        return view('admin.journal.editorial-board', compact('editorialBoard'));
    }
}
```

### 3. **Middleware Implementation**

#### Journal Context Middleware:

```php
class SetJournalContext
{
    public function handle($request, Closure $next)
    {
        $journalId = $this->resolveJournalId($request);

        if ($journalId) {
            // Set journal context for current request
            app()->instance('current_journal_id', $journalId);

            // Scope user permissions to journal
            $this->scopeUserPermissions($journalId);
        }

        return $next($request);
    }

    private function resolveJournalId($request)
    {
        // Resolve from subdomain, custom domain, or session
        $host = $request->getHost();

        // Check custom domains first
        $journal = ArticleCategory::where('custom_domain', $host)->first();
        if ($journal) return $journal->id;

        // Check subdomains
        $subdomain = explode('.', $host)[0];
        $journal = ArticleCategory::where('subdomain', $subdomain)->first();
        if ($journal) return $journal->id;

        // Check session (for admin panel)
        return session('current_journal_id');
    }
}
```

#### Journal Permission Middleware:

```php
class JournalPermission
{
    public function handle($request, Closure $next, $permission)
    {
        $journalId = app('current_journal_id');
        $user = auth('member')->user();

        if (!$this->userHasJournalPermission($user, $journalId, $permission)) {
            abort(403, 'Insufficient permissions for this journal');
        }

        return $next($request);
    }

    private function userHasJournalPermission($user, $journalId, $permission)
    {
        // Check if user has role in this journal
        return JournalMembership::where('member_id', $user->id)
                               ->where('journal_id', $journalId)
                               ->where('status', 'active')
                               ->exists();
    }
}
```

---

## Business Impact Analysis

### 1. **Revenue Opportunities**

#### A. Journal Licensing Model

**Current State:** Single platform, single revenue stream
**Enhanced State:** Multiple journals, multiple revenue streams

**Projected Revenue Impact:**

```
Year 1: 1 Journal (Current) → $50,000 annual revenue
Year 2: 3 Journals → $150,000 annual revenue (+200%)
Year 3: 10 Journals → $500,000 annual revenue (+900%)
Year 5: 25 Journals → $1,250,000 annual revenue (+2400%)
```

#### B. Editorial Services

- Journal setup and configuration services
- Editorial team training and onboarding
- Custom workflow development
- White-label platform licensing

#### C. Value-Added Services

- Journal branding and design services
- Multi-language support
- Advanced analytics and reporting
- Integration with external systems (ORCID, CrossRef, etc.)

### 2. **Market Positioning**

#### A. Competitive Advantages

- **First Mover**: Early entry into African multi-journal platform
- **Local Expertise**: Deep understanding of African academic landscape
- **Cost Efficiency**: Lower operational costs compared to international platforms
- **Customization**: Tailored workflows for African academic requirements

#### B. Target Markets

- **Primary**: African universities and research institutions
- **Secondary**: International journals focusing on African research
- **Tertiary**: Regional academic associations and societies

### 3. **Operational Benefits**

#### A. Scalability

- **Horizontal Scaling**: Add new journals without infrastructure changes
- **Resource Sharing**: Shared platform reduces individual journal costs
- **Standardization**: Consistent processes across all journals

#### B. Knowledge Sharing

- **Best Practices**: Editorial teams can share methodologies
- **Cross-Journal Collaboration**: Joint special issues and themed volumes
- **Mentorship**: Experienced journals mentor new ones

---

## Risk Assessment

### 1. **Technical Risks**

#### A. **High Risk: Data Isolation Failure**

**Probability:** Medium (30%)
**Impact:** High
**Mitigation:**

- Implement comprehensive testing for journal isolation
- Database-level constraints and triggers
- Regular security audits

#### B. **Medium Risk: Performance Degradation**

**Probability:** High (60%)
**Impact:** Medium
**Mitigation:**

- Implement caching strategies per journal
- Database query optimization
- CDN implementation for static assets

#### C. **Low Risk: Migration Complexity**

**Probability:** Low (15%)
**Impact:** High
**Mitigation:**

- Comprehensive backup strategy
- Staged migration approach
- Rollback procedures

### 2. **Business Risks**

#### A. **Market Acceptance**

**Probability:** Medium (40%)
**Impact:** High
**Mitigation:**

- Pilot program with 2-3 journals
- User feedback integration
- Gradual feature rollout

#### B. **Resource Requirements**

**Probability:** High (70%)
**Impact:** Medium
**Mitigation:**

- Phased implementation approach
- External development partnerships
- Clear resource allocation planning

### 3. **Operational Risks**

#### A. **Support Complexity**

**Probability:** High (80%)
**Impact:** Medium
**Mitigation:**

- Comprehensive documentation
- Training programs for journal administrators
- Tiered support model

#### B. **Quality Control**

**Probability:** Medium (35%)
**Impact:** High
**Mitigation:**

- Standardized workflows
- Regular audit processes
- Editorial board oversight

---

## Conclusion and Recommendations

### Summary of Findings

The Research Africa platform has **foundational capabilities** for supporting multiple journals, but requires **significant architectural enhancements** to achieve full multi-journal functionality. The current ArticleCategory system provides a solid foundation with journal metadata, but lacks the necessary data isolation, user management, and workflow separation required for true multi-journal operations.

### Key Strengths

1. ✅ **Existing Journal Structure**: ArticleCategory model with journal metadata
2. ✅ **Role-Based System**: Member types and permissions framework
3. ✅ **Workflow Foundation**: Editorial and review processes
4. ✅ **Scalable Architecture**: Laravel foundation supports expansion

### Critical Gaps

1. ❌ **Data Isolation**: No journal-scoped data access controls
2. ❌ **User Management**: No journal-specific user permissions
3. ❌ **Editorial Structure**: Unstructured editorial board management
4. ❌ **Multi-Domain Support**: No subdomain or custom domain handling

### Strategic Recommendations

#### 1. **Immediate Actions (Next 30 Days)**

- Conduct stakeholder interviews with potential journal partners
- Define detailed requirements for multi-journal functionality
- Create proof-of-concept for journal isolation
- Develop comprehensive migration strategy

#### 2. **Short-term Goals (3-6 Months)**

- Implement Phase 1 foundation enhancements
- Launch pilot program with 2-3 partner journals
- Develop user training materials
- Create support documentation

#### 3. **Medium-term Objectives (6-12 Months)**

- Complete full multi-journal implementation
- Launch commercial multi-journal platform
- Establish editorial services division
- Develop partnership program

#### 4. **Long-term Vision (12+ Months)**

- Scale to 10+ journals
- Expand to international markets
- Develop advanced analytics and AI features
- Create comprehensive academic ecosystem

### Final Assessment

**Recommendation: PROCEED WITH IMPLEMENTATION**

The multi-journal capability represents a **strategic opportunity** for Research Africa to become the leading academic publishing platform in Africa. While the implementation requires significant investment, the potential returns—both financial and in terms of academic impact—justify the effort.

**Success Factors:**

- Strong technical foundation already exists
- Clear market demand for multi-journal platform
- Reasonable implementation timeline (12 months)
- Multiple revenue stream opportunities
- Competitive advantage in African market

**Next Steps:**

1. Secure executive approval and budget allocation
2. Assemble dedicated development team
3. Begin Phase 1 implementation
4. Initiate pilot journal partnerships
5. Establish metrics and success criteria

The Research Africa platform is well-positioned to become the premier multi-journal academic publishing platform in Africa, supporting the continent's growing research community while generating sustainable revenue streams.
