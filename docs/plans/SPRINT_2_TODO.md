# Sprint 2 - Database Architecture Enhancement

## Implementation Checklist

**Sprint Duration**: Weeks 4-6 (3 weeks)  
**Status**: 🚀 READY TO START  
**Last Updated**: December 19, 2025

---

## 📋 Overview

This TODO list tracks all tasks for Sprint 2: Database Architecture Enhancement. Each task should be checked off as completed and tested.

---

## Week 1: Database Schema Enhancement

### Phase 1.1: Create New Tables

-   [x] **Task 1.1.1**: Create migration for `journal_editorial_boards` table

    -   File: `database/migrations/2025_12_19_000001_create_journal_editorial_boards_table.php`
    -   Fields: journal_id, member_id, position, department, institution, bio, orcid_id, term_start, term_end, is_active, display_order
    -   Foreign keys: journal_id → article_categories, member_id → members
    -   Indexes: journal_id + is_active, member_id + is_active, position
    -   Unique constraint: journal_id + member_id + position + is_active

-   [x] **Task 1.1.2**: Create migration for `journal_memberships` table
    -   File: `database/migrations/2025_12_19_000002_create_journal_memberships_table.php`
    -   Fields: member_id, journal_id, member_type_id, status, assigned_by, assigned_at, expires_at, notes
    -   Foreign keys: member_id → members, journal_id → article_categories, member_type_id → member_types, assigned_by → members
    -   Indexes: member_id + journal_id + status, journal_id + member_type_id + status, status
    -   Unique constraint: member_id + journal_id + member_type_id + status

### Phase 1.2: Enhance Existing Tables

-   [x] **Task 1.2.1**: Add journal context to articles table

    -   File: `database/migrations/2025_12_19_000003_add_journal_context_to_articles_table.php`
    -   Add field: journal_id (nullable, foreign key to article_categories)
    -   Add indexes: journal_id + article_status, journal_id + created_at, journal_id + member_id

-   [x] **Task 1.2.2**: Add journal configuration to article_categories table
    -   File: `database/migrations/2025_12_19_000004_add_journal_configuration_to_article_categories.php`
    -   Add fields: journal_acronym, subdomain, custom_domain, theme_config (JSON), email_settings (JSON), submission_settings (JSON)
    -   Add fields: publisher_name, editor_in_chief, contact_email
    -   Add unique indexes: journal_acronym, subdomain, custom_domain

### Phase 1.3: Data Migration

-   [x] **Task 1.3.1**: Create data migration script for existing articles

    -   File: `database/migrations/2025_12_19_000005_migrate_existing_articles_to_journals.php`
    -   Migrate articles to journals based on article_sub_category_id
    -   Fallback to article_category_id if sub_category is not a journal
    -   Log unmigrated articles for manual review

-   [x] **Task 1.3.2**: Run all migrations in staging environment

    -   Backup database before migration
    -   Run migrations: `php artisan migrate`
    -   Verify all tables created successfully
    -   Check foreign keys and indexes

-   [x] **Task 1.3.3**: Verify data integrity after migration
    -   Check article counts before and after
    -   Verify all articles have journal_id assigned
    -   Review migration logs for any issues
    -   Test rollback procedure

---

## Week 2: Model Enhancement & Relationships

### Phase 2.1: Create New Models

-   [x] **Task 2.1.1**: Create JournalEditorialBoard model

    -   File: `app/Models/JournalEditorialBoard.php`
    -   Add fillable fields
    -   Add relationships: journal(), member()
    -   Add scopes: active(), forJournal(), byPosition(), orderedByDisplay()
    -   Add helper methods: isActive(), getFullNameAttribute()

-   [x] **Task 2.1.2**: Create JournalMembership model
    -   File: `app/Models/JournalMembership.php`
    -   Add fillable fields and status constants
    -   Add relationships: member(), journal(), memberType(), assignedBy()
    -   Add scopes: active(), forJournal(), forMember(), byMemberType()
    -   Add helper methods: isActive(), activate(), deactivate(), suspend()

### Phase 2.2: Enhance Existing Models

-   [x] **Task 2.2.1**: Enhance Article model with journal context

    -   Add relationship: journal()
    -   Add relationship: editorialBoard() (hasManyThrough)
    -   Add scopes: forJournal(), forJournalSlug(), forJournalAcronym()
    -   Add helper method: belongsToJournal()

-   [x] **Task 2.2.2**: Enhance ArticleCategory model with journal relationships

    -   Add relationships: editorialBoard(), allEditorialBoard(), memberships(), activeMemberships()
    -   Add relationships: journalArticles(), publishedArticles()
    -   Add helper methods: getMembersByRole(), hasMemberAccess()
    -   Add computed attributes: getEditorsAttribute(), getReviewersAttribute()

-   [x] **Task 2.2.3**: Enhance Member model with journal relationships
    -   Add relationships: journalMemberships(), activeJournalMemberships(), editorialPositions()
    -   Add relationship: accessibleJournals() (belongsToMany)
    -   Add helper methods: hasJournalAccess(), isEditorFor(), isReviewerFor()
    -   Add computed relationships: editorJournals(), reviewerJournals()

### Phase 2.3: Model Testing

-   [ ] **Task 2.3.1**: Create model factory for JournalEditorialBoard

    -   File: `database/factories/JournalEditorialBoardFactory.php`
    -   Define factory fields with realistic data

-   [ ] **Task 2.3.2**: Create model factory for JournalMembership

    -   File: `database/factories/JournalMembershipFactory.php`
    -   Define factory fields with realistic data

-   [ ] **Task 2.3.3**: Write unit tests for new models
    -   File: `tests/Unit/JournalEditorialBoardTest.php`
    -   File: `tests/Unit/JournalMembershipTest.php`
    -   Test relationships, scopes, and helper methods

---

## Week 3: Data Isolation Framework & Services

### Phase 3.1: Create Service Layer

-   [ ] **Task 3.1.1**: Create JournalContextService

    -   File: `app/Services/JournalContextService.php`
    -   Methods: getCurrentJournal(), setCurrentJournal()
    -   Methods: getJournalBySlug(), getJournalByAcronym(), getJournalById()
    -   Methods: clearJournalCache(), userHasAccess(), getUserJournals()

-   [ ] **Task 3.1.2**: Create JournalMembershipService

    -   File: `app/Services/JournalMembershipService.php`
    -   Methods: assignMemberToJournal(), removeMemberFromJournal()
    -   Methods: updateMembershipStatus(), getMemberJournals()
    -   Methods: getJournalMembers(), checkMemberAccess()

-   [ ] **Task 3.1.3**: Create EditorialBoardService
    -   File: `app/Services/EditorialBoardService.php`
    -   Methods: addBoardMember(), removeBoardMember(), updateBoardMember()
    -   Methods: getActiveBoard(), reorderBoard(), getBoardByPosition()

### Phase 3.2: Create Middleware

-   [ ] **Task 3.2.1**: Create SetJournalContext middleware

    -   File: `app/Http/Middleware/SetJournalContext.php`
    -   Resolve journal from route parameters (acronym, slug, id)
    -   Set journal in app container and share with views
    -   Handle missing journal gracefully

-   [ ] **Task 3.2.2**: Create EnsureJournalAccess middleware

    -   File: `app/Http/Middleware/EnsureJournalAccess.php`
    -   Check if user has access to current journal
    -   Support role-based access (editor, reviewer, author)
    -   Return 403 if access denied

-   [ ] **Task 3.2.3**: Register middleware in Kernel
    -   File: `app/Http/Kernel.php`
    -   Add to $routeMiddleware array
    -   Document usage examples

### Phase 3.3: Database Seeders

-   [ ] **Task 3.3.1**: Create JournalEditorialBoardSeeder

    -   File: `database/seeders/JournalEditorialBoardSeeder.php`
    -   Seed sample editorial board members for test journals
    -   Include various positions (Editor-in-Chief, Associate Editor, etc.)

-   [ ] **Task 3.3.2**: Create JournalMembershipSeeder

    -   File: `database/seeders/JournalMembershipSeeder.php`
    -   Seed sample memberships for test users
    -   Include various member types and statuses

-   [ ] **Task 3.3.3**: Update DatabaseSeeder
    -   File: `database/seeders/DatabaseSeeder.php`
    -   Call new seeders in correct order
    -   Ensure data consistency

### Phase 3.4: Query Optimization

-   [ ] **Task 3.4.1**: Add database indexes for performance

    -   Review query patterns
    -   Add composite indexes where needed
    -   Test query performance with EXPLAIN

-   [ ] **Task 3.4.2**: Implement query caching

    -   Cache frequently accessed journal data
    -   Cache editorial board listings
    -   Cache membership lookups
    -   Set appropriate TTL values

-   [ ] **Task 3.4.3**: Optimize N+1 queries
    -   Review relationships for eager loading
    -   Add with() clauses where appropriate
    -   Test with query logging enabled

---

## Testing & Quality Assurance

### Integration Testing

-   [ ] **Task 4.1**: Create integration tests for journal isolation

    -   File: `tests/Feature/JournalIsolationTest.php`
    -   Test article isolation between journals
    -   Test membership isolation
    -   Test editorial board isolation

-   [ ] **Task 4.2**: Create integration tests for journal memberships

    -   File: `tests/Feature/JournalMembershipTest.php`
    -   Test member assignment to journals
    -   Test membership status changes
    -   Test access control

-   [ ] **Task 4.3**: Create integration tests for editorial boards
    -   File: `tests/Feature/EditorialBoardTest.php`
    -   Test board member assignment
    -   Test board member removal
    -   Test board ordering and display

### Performance Testing

-   [ ] **Task 4.4**: Test query performance with large datasets

    -   Create test data: 10 journals, 1000 articles, 100 members
    -   Measure query execution times
    -   Ensure queries < 100ms for complex operations

-   [ ] **Task 4.5**: Test concurrent journal access
    -   Simulate multiple users accessing different journals
    -   Verify no data leakage between journals
    -   Check for race conditions

### Security Testing

-   [ ] **Task 4.6**: Test data isolation security

    -   Verify users cannot access other journals' data
    -   Test SQL injection prevention
    -   Test authorization checks

-   [ ] **Task 4.7**: Test permission boundaries
    -   Verify role-based access control
    -   Test edge cases (expired memberships, suspended users)
    -   Test admin override capabilities

---

## Documentation

### Technical Documentation

-   [ ] **Task 5.1**: Document database schema changes

    -   Create ER diagram for new tables
    -   Document relationships and foreign keys
    -   Document indexes and constraints

-   [ ] **Task 5.2**: Document model relationships

    -   Create relationship diagram
    -   Document all scopes and methods
    -   Add inline code documentation

-   [ ] **Task 5.3**: Document service layer
    -   Document JournalContextService API
    -   Document JournalMembershipService API
    -   Document EditorialBoardService API
    -   Add usage examples

### API Documentation

-   [ ] **Task 5.4**: Document middleware usage

    -   Document SetJournalContext middleware
    -   Document EnsureJournalAccess middleware
    -   Provide route examples

-   [ ] **Task 5.5**: Create migration guide
    -   Document migration steps
    -   Document rollback procedures
    -   Document data verification steps

### User Documentation

-   [ ] **Task 5.6**: Create admin guide for journal management

    -   How to create journals
    -   How to manage editorial boards
    -   How to assign members to journals

-   [ ] **Task 5.7**: Create user guide for journal access
    -   How to join journals
    -   How to submit to specific journals
    -   How to view journal-specific content

---

## Deployment & Rollout

### Pre-Deployment

-   [ ] **Task 6.1**: Review all code changes

    -   Code review by team lead
    -   Security review
    -   Performance review

-   [ ] **Task 6.2**: Run full test suite

    -   Unit tests: `php artisan test --testsuite=Unit`
    -   Feature tests: `php artisan test --testsuite=Feature`
    -   Ensure 100% pass rate

-   [ ] **Task 6.3**: Test in staging environment
    -   Deploy to staging
    -   Run smoke tests
    -   Verify all functionality

### Deployment

-   [ ] **Task 6.4**: Create deployment checklist

    -   Backup production database
    -   Schedule maintenance window
    -   Prepare rollback plan

-   [ ] **Task 6.5**: Deploy to production

    -   Run migrations: `php artisan migrate --force`
    -   Clear caches: `php artisan cache:clear`
    -   Verify deployment success

-   [ ] **Task 6.6**: Post-deployment verification
    -   Check application logs
    -   Verify database integrity
    -   Test critical user flows

### Post-Deployment

-   [ ] **Task 6.7**: Monitor system performance

    -   Monitor query performance
    -   Monitor error rates
    -   Monitor user feedback

-   [ ] **Task 6.8**: Create Sprint 2 completion report
    -   Document achievements
    -   Document issues encountered
    -   Document lessons learned
    -   Prepare for Sprint 3

---

## Success Criteria

### Technical Metrics

-   [ ] All new tables created successfully
-   [ ] All migrations run without errors
-   [ ] Zero data loss during migration
-   [ ] All tests passing (100% pass rate)
-   [ ] Query performance < 100ms for complex operations
-   [ ] Database response time < 500ms

### Functional Metrics

-   [ ] Articles properly scoped to journals
-   [ ] Editorial board management functional
-   [ ] Journal membership system operational
-   [ ] Data isolation working correctly
-   [ ] User permissions properly scoped

### Quality Metrics

-   [ ] Code coverage > 90%
-   [ ] Zero critical bugs
-   [ ] All documentation complete
-   [ ] Security audit passed

---

## Risk Mitigation

### High-Risk Items

-   [ ] **Data Migration**: Test thoroughly in staging before production
-   [ ] **Performance Impact**: Monitor query performance closely
-   [ ] **Data Isolation**: Verify no data leakage between journals

### Rollback Plan

-   [ ] Database backup created before migration
-   [ ] Rollback script tested in staging
-   [ ] Rollback procedure documented
-   [ ] Team trained on rollback process

---

## Notes & Issues

### Blockers

-   None currently

### Dependencies

-   Sprint 1 completion (✅ COMPLETED)
-   Staging environment access
-   Database backup system operational

### Questions for Team

-   None currently

---

## Sprint 2 Completion Checklist

Before marking Sprint 2 as complete, ensure:

-   [ ] All tasks above are completed and checked off
-   [ ] All tests are passing
-   [ ] Documentation is complete
-   [ ] Code is deployed to production
-   [ ] Post-deployment verification successful
-   [ ] Sprint 2 completion report created
-   [ ] Sprint 3 planning initiated

---

**Status Legend:**

-   ⏳ Not Started
-   🔄 In Progress
-   ✅ Completed
-   ❌ Blocked
-   ⚠️ Needs Review

**Last Updated**: December 19, 2025  
**Next Review**: End of Week 1 (Sprint 2)
