# Afriscribe Cleanup Plan

**Date**: December 20, 2025
**Goal**: Remove Afriscribe references to reduce test failures, keeping only external URL to afriscribe.org

## Tasks

### Middleware

- [X] Remove 'afriscribe/request' from CSRF middleware except array in app/Http/Middleware/VerifyCsrfToken.php

### Mail Config

- [X] Remove 'afriscribe' mailer from config/mail.php

### Navigation Views

- [X] Keep only external Afriscribe link (https://afriscribe.org) in resources/views/member/partials/header.blade.php, remove children links
- [X] Remove Afriscribe admin links from resources/views/partials/menu.blade.php

### Factories

- [X] Delete database/factories/AfriscribeRequestFactory.php
- [X] Delete empty database/factories/Modules/AfriScribe/Models/ directory

### Email Templates

- [X] Delete resources/views/emails/afriscribe-client-acknowledgement.blade.php

### Tests

- [X] Update tests/Integration/DatabaseRelationshipsTest.php to remove Afriscribe references
- [X] Update tests/Unit/Models/AfriscribeRequestTest.php to remove Afriscribe references

## Followup

- [X] Run tests to verify failure reduction
- [X] Proceed to Phase 2 if successful
