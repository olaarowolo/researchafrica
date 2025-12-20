# Testing Fixes TODO - Systematic Resolution Plan

**Date**: December 19, 2025  
**Status**: 🔄 **IN PROGRESS**  
**Goal**: Fix all 514 failing tests systematically

---

## ✅ COMPLETED FIXES

### 1. Unit Test Model Casting Issue - RESOLVED

-   **File**: `tests/Unit/AfriScribeModelTest.php`
-   **Issue**: Test expected specific casts but model had additional casts
-   **Fix**: Updated test to include Laravel's automatic `id => 'int'` cast
-   **Status**: ✅ PASSING

### 2. Admin Logout Route Issue - RESOLVED

-   **File**: `app/Http/Controllers/Admin/HomeController.php`
-   **Issue**: Logout redirected to home page instead of admin login
-   **Fix**: Changed redirect from `redirect('/')` to `redirect()->route('admin.login')`
-   **Status**: ✅ PASSING

---

## 🔄 IN PROGRESS / NEXT FIXES

### 3. User Factory Missing - HIGH PRIORITY

-   **File**: `tests/Feature/AuthenticationTest.php`
-   **Issue**: `User::factory()->admin()` method doesn't exist
-   **Affected Tests**: AuthenticationTest::test_admin_can_logout
-   **Fix Needed**: Create missing factory method or update test

### 4. Article Creation 500 Error - HIGH PRIORITY

-   **File**: `tests/Feature/ArticleTest.php`
-   **Issue**: Server error when creating articles
-   **Error**: Expected response status code [201, 301, 302, 303, 307, 308] but received 500
-   **Affected Tests**: ArticleTest::test_admin_can_create_article
-   **Root Cause**: Article controller store method has dependency issues

---

## 📋 SYSTEMATIC FIXES NEEDED

### Phase A: Factory and Model Issues

-   [ ] **Fix missing factory methods**

    -   [ ] Create `User::factory()->admin()` method
    -   [ ] Fix any other missing factory relationships
    -   [ ] Ensure all factories have required dependencies

-   [ ] **Fix model namespace issues**
    -   [ ] Review all test imports for correct model namespaces
    -   [ ] Fix any incorrect `App\Modules\*` references that should be `App\Models\*`

### Phase B: Controller and Route Issues

-   [ ] **Article Controller Store Method**

    -   [ ] Fix missing dependencies in ArticleController
    -   [ ] Ensure traits are properly loaded
    -   [ ] Fix any missing method calls

-   [ ] **Route Issues**
    -   [ ] Verify all routes used in tests exist
    -   [ ] Fix any route name mismatches
    -   [ ] Ensure proper middleware setup

### Phase C: Database and Factory Dependencies

-   [ ] **Factory Dependency Chain**

    -   [ ] Ensure all factories can create required dependencies
    -   [ ] Fix any missing relationships in factories
    -   [ ] Add proper seeding for dependent data

-   [ ] **Database Schema Alignment**
    -   [ ] Verify field names match between tests and database
    -   [ ] Fix any column name mismatches
    -   [ ] Ensure all required tables exist

### Phase D: Mail and Notification Issues

-   [ ] **Mail Class Constructor Fixes**
    -   [ ] Fix mail class parameter issues
    -   [ ] Ensure all mail classes are properly constructed
    -   [ ] Fix mail facade assertion issues

### Phase E: Storage and File Upload Issues

-   [ ] **File Upload Tests**
    -   [ ] Fix storage facade method calls
    -   [ ] Ensure proper file upload testing
    -   [ ] Fix any storage path issues

---

## 🏃‍♂️ IMMEDIATE NEXT STEPS (Next 2-4 Hours)

### Step 1: Fix User Factory (30 minutes)

```bash
# Check User factory and add missing admin() method
```

### Step 2: Investigate Article Controller 500 Error (60 minutes)

```bash
# Run article creation test with detailed error output
php artisan test tests/Feature/ArticleTest::test_admin_can_create_article --stop-on-failure --verbose
```

### Step 3: Run Individual Test Categories (90 minutes)

```bash
# Test each major category to identify patterns
php artisan test tests/Unit --stop-on-failure
php artisan test tests/Feature --stop-on-failure
php artisan test tests/Integration --stop-on-failure
```

### Step 4: Categorize All Remaining Failures (60 minutes)

-   Group failures by type (Factory, Model, Controller, Database, etc.)
-   Prioritize by impact and complexity
-   Create specific fix plan for each category

---

## 📊 PROGRESS TRACKING

### Test Results Tracking

| Category          | Total Tests | Passing  | Failing  | Status             |
| ----------------- | ----------- | -------- | -------- | ------------------ |
| Unit Tests        | ~200        | ~150     | ~50      | 🔄 In Progress     |
| Feature Tests     | ~300        | ~200     | ~100     | 🔄 In Progress     |
| Integration Tests | ~80         | ~60      | ~20      | 🔄 In Progress     |
| Browser Tests     | ~20         | ~15      | ~5       | 🔄 In Progress     |
| **TOTAL**         | **~600**    | **~425** | **~175** | **🔄 In Progress** |

### Fix Progress

-   **Completed**: 2 fixes ✅
-   **In Progress**: 2 fixes 🔄
-   **Planned**: 15+ fixes 📋
-   **Success Rate**: Target 95%+ (currently tracking)

---

## 🎯 SUCCESS METRICS

### Short-term Goals (Today)

-   [ ] Fix User factory issue
-   [ ] Resolve Article creation 500 error
-   [ ] Complete failure categorization
-   [ ] Fix 10+ high-priority failures

### Medium-term Goals (This Week)

-   [ ] Complete Phase A (Factory and Model issues)
-   [ ] Complete Phase B (Controller and Route issues)
-   [ ] Achieve 70%+ test success rate
-   [ ] Fix 200+ test failures

### Long-term Goals (Next Week)

-   [ ] Complete all phases (A-E)
-   [ ] Achieve 95%+ test success rate
-   [ ] Reduce failures to <50
-   [ ] Establish stable test foundation

---

## 📝 NOTES

-   **Progress Update**: Starting systematic approach with incremental fixes
-   **Testing Strategy**: Run individual tests to verify fixes before moving to next
-   **Documentation**: Recording all changes for future reference
-   **Quality Control**: Ensuring fixes don't break other functionality

---

_Last Updated: December 19, 2025_  
_Next Review: After completing Step 4_
