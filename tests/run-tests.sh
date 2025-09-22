#!/bin/bash

echo "=========================================="
echo "Running Research Africa Application Tests"
echo "=========================================="

# Run all tests
echo "Running all tests..."
php artisan test

# Run specific test suites
echo ""
echo "Running Feature Tests..."
php artisan test --testsuite=Feature

echo ""
echo "Running Unit Tests..."
php artisan test --testsuite=Unit

echo ""
echo "Running specific test files..."
php artisan test tests/Feature/AdminTest.php
php artisan test tests/Feature/AuthenticationTest.php
php artisan test tests/Feature/ArticleTest.php
php artisan test tests/Feature/MemberTest.php
php artisan test tests/Feature/CommentTest.php
php artisan test tests/Feature/FaqTest.php

echo ""
echo "Test execution completed!"
echo "Check the output above for any failures or errors."
