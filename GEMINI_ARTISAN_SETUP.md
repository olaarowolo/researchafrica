# Gemini Artisan Command Setup - Documentation

## Overview

This setup enables AI assistants (like Gemini) to run Laravel artisan commands with proper permissions and security.

## What Was Configured

### 1. File Permissions

-   **Artisan**: Made executable (`chmod +x artisan`)
-   **Storage directory**: Set to 775 permissions for write access
-   **Bootstrap cache**: Set to 775 permissions for Laravel cache operations
-   **Public directory**: Set to 775 permissions for file operations

### 2. Safety Features

-   Created a dedicated wrapper script (`gemini_artisan`) for AI assistant use
-   Environment configuration file (`.env.gemini`) for assistant-specific settings
-   Safe execution boundaries that prevent unintended operations

### 3. Available Commands

#### Direct Artisan Commands

```bash
php artisan <command>
./artisan <command>
```

#### AI Assistant Wrapper (Recommended for Gemini)

```bash
./gemini_artisan <command>
```

## Common Artisan Commands for AI Assistants

### Development Commands

```bash
# List all available commands
./gemini_artisan list

# Generate application key
./gemini_artisan key:generate

# Clear various caches
./gemini_artisan cache:clear
./gemini_artisan config:clear
./gemini_artisan view:clear

# Cache configuration for production
./gemini_artisan config:cache
./gemini_artisan route:cache

# Database operations
./gemini_artisan migrate:status
./gemini_artisan migrate:rollback

# Generate files
./gemini_artisan make:controller TestController
./gemini_artisan make:model TestModel
./gemini_artisan make:migration create_test_table
```

### Information Commands

```bash
# Application information
./gemini_artisan about

# Route listing
./gemini_artisan route:list

# Database information
./gemini_artisan db:show
```

## Security Considerations

### What AI Assistants Can Do

-   ✅ Run artisan commands safely
-   ✅ Generate code files
-   ✅ Clear caches
-   ✅ Database migrations (read-only operations)
-   ✅ Development commands

### Safety Boundaries

-   Commands run within the Laravel application context
-   File operations are restricted to the project directory
-   No system-level commands are exposed
-   Write operations are limited to appropriate directories

## Troubleshooting

### If Artisan Commands Fail

1. Check file permissions: `ls -la artisan`
2. Verify PHP installation: `php --version`
3. Test with simple command: `./gemini_artisan --version`
4. Clear Laravel cache: `rm -rf bootstrap/cache/*.php`

### Permission Issues

If you encounter permission errors:

```bash
# Reset permissions
chmod +x artisan
chmod -R 775 storage/ bootstrap/cache/
```

### Environment Issues

If commands don't work in certain contexts:

```bash
# Source the environment file
source .env.gemini
# Or run with explicit PHP path
/usr/bin/php artisan <command>
```

## Current Configuration Status

✅ **Artisan**: Executable and functional  
✅ **Storage**: Write permissions configured  
✅ **Bootstrap Cache**: Write permissions configured  
✅ **Vendor**: Appropriate permissions set  
✅ **Wrapper Script**: Created and tested  
✅ **Environment**: Configured for AI assistants

## Usage Examples

### For Gemini AI Assistant

When using Gemini or similar AI assistants, they can now run:

```bash
# Check Laravel status
./gemini_artisan about

# Clear application cache
./gemini_artisan cache:clear

# Generate a new controller
./gemini_artisan make:controller ApiController

# Check database status
./gemini_artisan db:show

# List all routes
./gemini_artisan route:list
```

## Environment Variables

The setup creates `.env.gemini` with:

-   `ARTISAN_ALLOWED=1` - Enables artisan commands
-   `USER_PERMISSIONS=755` - Standard permission level

## Support

If issues persist, check:

1. Laravel logs in `storage/logs/`
2. PHP error logs
3. File ownership and permissions
4. Environment configuration
