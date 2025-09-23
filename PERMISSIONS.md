# Laravel Application File Permissions

Correct file and folder permissions are crucial for both the security and functionality of a Laravel application. Incorrect permissions can lead to application errors or expose your application to serious security vulnerabilities, especially in a production environment.

This guide follows the **principle of least privilege**, ensuring that users and processes only have the permissions essential to perform their intended functions.

## 1. File Ownership

For optimal security and manageability, your project files should be owned by your deployment user, and the group should be set to the web server's group.

-   **Owner**: Your deployment user (e.g., `olasunkanmiarowolo`). This user will own all the files and have full control.
-   **Group**: The web server's group (e.g., `www-data`, `nginx`, `apache`). The web server process runs as a member of this group.

You can set the ownership from your project root with the `chown` command. Replace `www-data` with your web server's group if it's different.

```bash
# Example: Set ownership for the entire project directory
sudo chown -R $USER:www-data .
```

## 2. Standard Permissions

The general permission scheme for a Laravel project should be:

-   **Directories**: `755` (rwxr-xr-x)
-   **Files**: `644` (rw-r--r--)

These settings mean:
-   The **owner** (`$USER`) can read, write, and traverse the directories.
-   The **group** (`www-data`) can read and traverse the directories.
-   **Everyone else** can read and traverse the directories.

## 3. Laravel Specific Writable Directories

Laravel requires certain directories to be writable by the web server for tasks like logging, caching, and file uploads. We grant the `www-data` group write access to these specific directories.

-   `storage` directory and all its subdirectories.
-   `bootstrap/cache` directory.

The permissions for these should be:
-   **Directories**: `775` (rwxrwxr-x)
-   **Files**: `664` (rw-rw-r--)

This allows both your deployment user (the owner) and the web server (the group) to write files, which is essential for running `artisan` commands and for the web application to function correctly.

## 4. Applying Permissions (The Right Way)

You can use the following script from your project's root directory to correctly set all permissions. It's idempotent, meaning you can run it multiple times without adverse effects.

```bash
#!/bin/bash
# This script sets the recommended permissions for a Laravel project in production.

# 1. Set general directory permissions to 755
echo "Setting directory permissions to 755..."
find . -type d -exec chmod 755 {} \;

# 2. Set general file permissions to 644
echo "Setting file permissions to 644..."
find . -type f -exec chmod 644 {} \;

# 3. Grant the web server write access to storage and cache
echo "Setting writable permissions for storage/ and bootstrap/cache/..."
chmod -R g+w storage bootstrap/cache

# 4. Make the artisan command executable
echo "Making 'artisan' executable..."
chmod +x artisan

echo "✅ Permissions have been set correctly."
```

**Note:** The `chmod -R g+w` command is precise. It adds group write permissions to the `storage` and `bootstrap/cache` directories and their contents, changing `755` directories to `775` and `644` files to `664`, without being overly permissive.

## 5. Production Hardening & Security Notes

### `.env` File
Your `.env` file contains highly sensitive credentials. It should **never** be world-readable.

-   **Recommended Permission**: `600` (rw-------)
-   **Command**: `chmod 600 .env`

This ensures only the file owner (your deployment user) can read or write to it. The web server process does not need to read this file directly in production if you are using configuration caching (`php artisan config:cache`).

### Executable Files
Only files that are meant to be run as scripts should have the execute bit. In a typical Laravel project, this is mainly the `artisan` file. The script in section 4 handles this.

### SELinux / AppArmor
If your server uses security modules like SELinux (common on RHEL/CentOS) or AppArmor (common on Ubuntu/Debian), you may need to set the correct security context for the `storage` and `bootstrap/cache` directories to allow the web server to write to them.

For example, on an SELinux-enabled system:
```bash
# This allows Apache to write to the storage directory
chcon -R -t httpd_sys_rw_content_t storage
chcon -R -t httpd_sys_rw_content_t bootstrap/cache
```
Consult your server's documentation for specific commands.

## Summary of Permissions

```bash
| Path                  | Type      | Recommended Permission | Reason                                       |
| --------------------- | --------- | ---------------------- | -------------------------------------------- |
| `.` (Project Root)    | Directory | `755`                  | General directory access.                    |
| `artisan`             | File      | `755`                  | Needs to be executable by the user.          |
| `app/`                | Directory | `755`                  | Contains application logic.                  |
| `bootstrap/`          | Directory | `755`                  | Contains app bootstrapping scripts.          |
| `bootstrap/cache/`    | Directory | `775`                  | Needs to be writable by app and web server.  |
| `config/`             | Directory | `755`                  | Contains configuration files.                |
| `database/`           | Directory | `755`                  | Contains database migrations and seeds.      |
| `public/`             | Directory | `755`                  | Web server document root.                    |
| `public/index.php`    | File      | `644`                  | Entry point of the application.              |
| `resources/`          | Directory | `755`                  | Contains views, raw assets, and language files. |
| `routes/`             | Directory | `755`                  | Contains route definitions.                  |
| `storage/`            | Directory | `775`                  | Needs to be writable by app and web server.  |
| `storage/app/`        | Directory | `775`                  | For file uploads, etc.                       |
| `storage/framework/`  | Directory | `775`                  | For sessions, views, cache.                  |
| `storage/logs/`       | Directory | `775`                  | For application logs.                        |
| `storage/logs/*.log`  | File      | `664`                  | Log files need to be writable.               |
| `vendor/`             | Directory | `755`                  | Composer dependencies.                       |
| `composer.json`       | File      | `644`                  | Project dependencies definition.             |
| `.env`                | File      | `600`                  | Contains sensitive credentials. Should not be world-readable. |