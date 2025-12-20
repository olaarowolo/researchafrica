# Research Africa Multi-Journal Transformation - Implementation Plan

## Current Codebase Analysis Summary

### Current State Assessment:

-   ✅ **ArticleCategory Model**: Uses `category_name` field (needs semantic clarity)
-   ✅ **Article Model**: Basic structure exists (needs journal context)
-   ✅ **Database Structure**: Foundational tables present
-   ❌ **Multi-Journal Capability**: Not implemented
-   ❌ **Data Isolation**: Missing journal-scoped access controls
-   ❌ **Semantic Clarity**: Confusing `category_name` field for journal entities

### Sprint 1 Implementation Strategy

## Phase 1: Foundation & Safety Nets (Sprint 1)

### Database Safety Infrastructure

1. **Backup System Implementation**

    - Database backup scripts
    - Automated backup scheduling
    - Rollback procedures

2. **Migration Safety Framework**
    - Staging environment setup
    - Data integrity verification
    - Zero-downtime migration strategy

### Semantic Clarity Implementation

1. **ArticleCategory Model Enhancement**

    - Add `name` field (replacement for confusing `category_name`)
    - Add `is_journal` boolean flag
    - Add `journal_slug` URL-friendly identifier
    - Add `display_name` for UI presentation
    - Implement backward compatibility accessors

2. **Data Migration Scripts**
    - Safe migration from `category_name` to `name`
    - Data integrity checks
    - Rollback capabilities

### Technical Implementation Tasks

#### 1. Database Migration for Semantic Clarity

```php
// Migration file: add_semantic_clarity_to_article_categories.php
Schema::table('article_categories', function (Blueprint $table) {
    $table->string('name')->nullable()->after('id');
    $table->string('display_name')->nullable()->after('name');
    $table->boolean('is_journal')->default(false)->after('display_name');
    $table->string('journal_slug')->nullable()->after('is_journal');
    $table->index(['is_journal', 'journal_slug']);
});
```

#### 2. Enhanced ArticleCategory Model

```php
// ArticleCategory.php enhancements
class ArticleCategory extends Model
{
    // Add new fields to $fillable
    protected $fillable = [
        'category_name', // Keep for backward compatibility
        'name', // New semantic field
        'display_name', // UI presentation
        'is_journal', // Type distinction
        'journal_slug', // URL identifier
        // ... existing fields
    ];

    // Backward compatibility accessor
    public function getCategoryNameAttribute($value)
    {
        return $this->name ?? $value;
    }

    // Type checking methods
    public function isJournal(): bool
    {
        return $this->is_journal;
    }

    public function isCategory(): bool
    {
        return !$this->is_journal;
    }

    // Scope for journal filtering
    public function scopeJournals($query)
    {
        return $query->where('is_journal', true);
    }

    public function scopeCategories($query)
    {
        return $query->where('is_journal', false);
    }
}
```

#### 3. Backup and Safety Scripts

```bash
#!/bin/bash
# backup_database.sh
# Automated backup script with integrity verification
```

## Phase 2: Database Architecture Enhancement (Sprint 2)

### Multi-Tenancy Database Schema

1. **New Tables Required:**

    - `journal_editorial_boards` - Editorial board management
    - `journal_memberships` - Journal membership tracking
    - `journal_settings` - Journal-specific configurations

2. **Enhanced Existing Tables:**
    - Add `journal_id` to articles table
    - Add `journal_id` to article_categories table
    - Add journal-specific configuration fields

## Phase 3: Core Multi-Journal Functionality (Sprint 3)

### Journal-Scoped Article System

1. **Enhanced Article Model**

    - Journal context relationships
    - Journal-specific scopes
    - Cross-journal access controls

2. **Journal Context Middleware**
    - Set current journal context
    - Scope user permissions
    - Route-based journal detection

## Phase 4: URL-Based Journal Routing (Sprint 4)

### Acronym-Based Journal System

1. **Route Structure**

    - `/journals/{acronym}/` - Journal home
    - `/journals/{acronym}/submit` - Article submission
    - `/journals/{acronym}/about` - Journal information

2. **Journal Management Interface**
    - Admin panel for journal configuration
    - Acronym management and validation
    - Branding management tools

## Implementation Approach

### Development Methodology:

1. **Iterative Development**: 3-week sprints with continuous testing
2. **Safety-First**: Comprehensive backup and rollback procedures
3. **Backward Compatibility**: Maintain existing functionality during transition
4. **Testing Strategy**: Unit tests, integration tests, and user acceptance testing

### Quality Assurance:

1. **Code Quality**: Automated testing and code review
2. **Performance**: Load testing and optimization
3. **Security**: Security audits and penetration testing
4. **Documentation**: Comprehensive technical and user documentation

## Risk Mitigation

### High-Risk Areas:

1. **Data Migration**: Multiple backup strategies and staged approach
2. **Performance Impact**: Caching strategies and optimization
3. **User Adoption**: Training and gradual feature rollout

### Mitigation Strategies:

1. **Staged Rollout**: Feature-by-feature implementation
2. **Testing Environment**: Complete staging environment for testing
3. **Rollback Procedures**: Instant rollback capabilities for all changes

## Success Criteria

### Sprint 1 Success Metrics:

-   ✅ 100% backward compatibility maintained
-   ✅ All existing functionality preserved
-   ✅ New semantic fields populated correctly
-   ✅ Zero data loss during migration
-   ✅ Rollback procedure tested and verified

### Overall Project Success Metrics:

-   ✅ Full multi-journal capability implementation
-   ✅ URL-based routing with unique acronyms
-   ✅ Data isolation and security
-   ✅ Performance maintained under load
-   ✅ User adoption and satisfaction

## Next Steps

1. **Immediate Actions:**

    - Set up development environment
    - Create database backup procedures
    - Implement Sprint 1 migrations

2. **Team Assembly:**

    - Technical lead coordination
    - Developer resource allocation
    - QA testing framework setup

3. **Environment Setup:**
    - Staging environment configuration
    - Automated testing pipeline
    - Documentation framework

This implementation plan provides a structured, risk-managed approach to transforming Research Africa into a world-class multi-journal publishing platform.
