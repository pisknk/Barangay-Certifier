# Barangay Certifier Software Update System

This document explains how the software update system for Barangay Certifier works.

## Overview

The software update system allows administrators to check for new versions of the Barangay Certifier application and perform updates when available. The system uses GitHub releases as the source for updates.

## Features

- Check for updates via the UI (Ultimate plan subscribers only)
- Check for updates via Artisan command
- Download and install updates via Artisan command
- Automatic backup before updating
- Rollback capability in case of update failure

## Components

1. **SystemVersion Model**
   - Tracks system version information
   - Stores version history in the database

2. **Artisan Commands**
   - `system:check-updates`: Checks for available updates
   - `system:update`: Performs the actual update process

3. **User Interface**
   - Settings page with version information and update button (for Ultimate plan subscribers)

## How It Works

### Checking for Updates

The system checks for updates by:
1. Getting the current version from the database
2. Querying the GitHub API for the latest release
3. Comparing versions to determine if an update is available
4. Storing update information in a temporary file if an update is available

You can check for updates with:

```bash
php artisan system:check-updates [--force]
```

The `--force` option bypasses the 24-hour cooldown between checks.

### Installing Updates

The update process follows these steps:
1. Create a backup of the current system
2. Download the update package from GitHub
3. Extract the update package
4. Apply the updates to the system
5. Run migrations and clear caches
6. Update the version record in the database
7. Clean up temporary files

You can perform an update with:

```bash
php artisan system:update [--force]
```

The `--force` option allows updating even if no update check has been performed.

### Subscription-Based Access

- Only Ultimate plan subscribers can access the update check feature in the UI
- Artisan commands can be used by administrators regardless of subscription plan

## Backup and Recovery

Before any update, the system automatically:
1. Creates a backup of essential files and directories
2. Stores the backup in `storage/app/system/backup_[timestamp]`

If an update fails, the system will attempt to restore from this backup automatically.

## Developer Notes

- The system avoids updating the `.env` file to prevent database credential issues
- Updates include `app`, `config`, `database`, `resources`, and `routes` directories
- The update process runs database migrations automatically
- Version numbers follow semantic versioning (e.g., 1.0.0)

## Troubleshooting

If an update fails:
1. Check the Laravel logs in `storage/logs`
2. Manually restore from the backup in `storage/app/system/backup_[timestamp]` if needed
3. Clear caches with `php artisan optimize:clear`

## Future Improvements

- Implement pre-update checks to ensure system compatibility
- Add support for incremental updates
- Implement email notifications for administrators when updates are available 