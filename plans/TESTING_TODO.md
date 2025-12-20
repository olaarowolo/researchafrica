# Laravel Testing Fix Plan - 514 Failed Tests

**Date**: December 20, 2025
**Current Status**: 514 failed, 1 risky, 610 passed (1581 assertions)
**Goal**: Reduce failures to 0 and achieve stable test suite

---

## Executive Summary

The test suite has significant failures primarily due to:

1. **Database seeding issues** (foreign key constraints, missing seeders)
2. **Afriscribe module remnants** (removed but references still exist)
3. **File upload test failures** (404 errors on removed routes)
4. **Model cast mismatches** (missing 'id' => 'int' in casts)

---

## Phase 1: Afriscribe Cleanup (Priority: HIGH)

### Remaining Afriscribe References to Remove

-   [ ] **Routes**: Remove Afriscribe routes from `routes/web.php` (lines 13-23)
-   [ ] **Factories**: Remove Afriscribe factory references
    -   `database/factories/QuoteRequestFactory.php` - remove afriscribe service
    -   `database/factories/Modules/AfriScribe/Models/QuoteRequestFactory.php` - remove file
-   [ ] **Middleware**: Remove afriscribe CSRF token from `app/Http/Middleware/VerifyCsrfToken.php`
-   [ ] **Mail Config**: Remove afriscribe mail config from `config/mail.php`
-   [ ] **Email Templates**: Remove `resources/views/emails/afriscribe_request.blade.php`
-   [ ] **Navigation**: Remove Afriscribe links from `resources/views/member/partials/header.blade.php`

### Expected Impact

-   Eliminates ~20-30 test failures related to missing Afriscribe routes/models
-   Fixes file upload test 404 errors

---

## Phase 2: Database Seeding Fixes (Priority: HIGH)

### Foreign Key Constraint Issues

**Problem**: Members table seeding fails due to missing state_id references

**Root Cause**: Members table requires state_id but states table is not seeded first

**Solution**:

-   [ ] **Fix Seeder Order**: Ensure `StatesTableSeeder` runs before `MembersTableSeeder`
-   [ ] **Update DatabaseSeeder.php**: Reorder seeders properly
-   [ ] **Add Missing Seeders**: Create any missing seeders (CountrySeeder, StateSeeder, etc.)

### DatabaseSeedingTest.php Issues

**Current Failures**:

-   19 failed tests in DatabaseSeedingTest.php
-   Foreign key constraint violations
-   Missing seeder classes
-   Type error in assertDatabaseCount (string vs int)

**Fixes Needed**:

-   [ ] **Fix assertDatabaseCount**: Change string parameter to integer in line 296
-   [ ] **Create Missing Seeders**: Add CountrySeeder, StateSeeder, etc.
-   [ ] **Fix Seeder Dependencies**: Ensure proper seeding order
-   [ ] **Update Member Factory**: Fix state_id references

### Expected Impact

-   Fixes ~19 database seeding test failures
-   Resolves foreign key constraint issues across all tests

---

## Phase 3: File Upload Test Fixes (Priority: MEDIUM)

### FileUploadTest.php Issues

**Current Failures**:

-   404 error on afriscribe routes
-   Storage facade compatibility issues

**Root Cause**: Tests still reference removed Afriscribe routes

**Solution**:

-   [ ] **Update Test Routes**: Change test routes from afriscribe to valid routes
-   [ ] **Fix Storage Assertions**: Update storage facade usage
-   [ ] **Update Test Data**: Ensure test data matches current schema

### Expected Impact

-   Fixes file upload test failures (~3-5 tests)

---

## Phase 4: Model Cast Fixes (Priority: MEDIUM)

### QuoteRequest Model Cast Issues

**Problem**: Missing 'id' => 'int' in expected casts array

**Solution**:

-   [ ] **Update Test Expectations**: Add missing 'id' => 'int' to expected casts
-   [ ] **Verify Model Casts**: Ensure QuoteRequest model has correct casts

### Expected Impact

-   Fixes quote request cast test failures

---

## Phase 5: Integration Test Fixes (Priority: MEDIUM)

### MailSendingTest.php Issues

**Problem**: Mail facade compatibility issues

**Solution**:

-   [ ] **Update Mail Assertions**: Fix mail facade method calls
-   [ ] **Standardize Mail Testing**: Use consistent Laravel testing patterns

### SearchIntegrationTest.php & ExternalServiceIntegrationTest.php

**Status**: These tests are marked as PENDING in current reports

**Solution**:

-   [ ] **Implement Search Tests**: Create comprehensive search integration tests
-   [ ] **Implement External Service Tests**: Add Google login, PDF generation tests

### Expected Impact

-   Fixes mail integration test issues
-   Completes remaining integration tests

---

## Phase 6: Browser Test Fixes (Priority: LOW)

### Dusk Browser Tests

**Current Status**: 3 browser test files exist but may have issues

**Solution**:

-   [ ] **Verify Browser Tests**: Run browser tests individually
-   [ ] **Fix Environment Setup**: Ensure Dusk environment is configured
-   [ ] **Update Test Scenarios**: Ensure tests match current UI

### Expected Impact

-   Ensures browser tests are functional

---

## Phase 7: Test Infrastructure Improvements (Priority: LOW)

### Test Configuration Updates

-   [ ] **Update phpunit.xml**: Ensure proper test database configuration
-   [ ] **Environment Setup**: Verify .env.testing configuration
-   [ ] **Factory Enhancements**: Update factories for new schema

### Test Helper Creation

-   [ ] **Create Test Helpers**: Add utility functions for common test operations
-   [ ] **Assertion Helpers**: Create custom assertions for domain logic
-   [ ] **Data Generators**: Add test data generation utilities

---

## Implementation Strategy

### Week 1: Critical Fixes (Target: Reduce failures by 60-70%)

1. **Day 1-2**: Complete Afriscribe cleanup
2. **Day 3-4**: Fix database seeding issues
3. **Day 5-7**: Address file upload and model cast issues

### Week 2: Integration Fixes (Target: Reduce failures by 20-30%)

1. **Day 1-3**: Fix mail integration tests
2. **Day 4-5**: Implement missing integration tests
3. **Day 6-7**: Browser test verification

### Week 3: Polish and Optimization (Target: 0 failures)

1. **Day 1-2**: Test infrastructure improvements
2. **Day 3-4**: Performance optimization
3. **Day 5-7**: Final testing and documentation

---

## Success Metrics

### Quantitative Targets

-   **Week 1 End**: < 200 failed tests
-   **Week 2 End**: < 50 failed tests
-   **Week 3 End**: 0 failed tests

### Quality Targets

-   All tests pass consistently
-   No syntax errors
-   Proper error handling
-   Clean test output

---

## Risk Assessment

### High Risk

-   Database schema changes during fixes
-   Test environment configuration issues

### Mitigation

-   Create database backups before changes
-   Test fixes incrementally
-   Maintain version control

---

## Dependencies

### Required Resources

-   Access to test database
-   Laravel development environment
-   Browser testing setup (for Dusk tests)

### Prerequisites

-   All migrations must be current
-   Test database must be accessible
-   Environment variables properly configured

---

## Monitoring and Reporting

### Daily Checkpoints

-   Test run results after each major fix
-   Failure count tracking
-   New issues identification

### Weekly Reports

-   Progress updates
-   Remaining issues analysis
-   Next steps planning

---

**Total Estimated Effort**: 3 weeks
**Risk Level**: Medium
**Success Probability**: High (systematic approach)

---

_This plan addresses the root causes of test failures rather than just symptoms, ensuring long-term test suite stability._
