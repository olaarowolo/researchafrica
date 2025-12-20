#!/bin/bash

# Research Africa Multi-Journal Transformation
# Database Backup Script for Sprint 1 Implementation
# Created: $(date)
# Purpose: Safe backup before semantic clarity migration

set -e  # Exit on any error

# Configuration
DB_NAME="researchafrica"
DB_USER="root"
DB_HOST="localhost"
BACKUP_DIR="./backups"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_FILE="${BACKUP_DIR}/researchafrica_backup_${TIMESTAMP}.sql"
LOG_FILE="${BACKUP_DIR}/backup_log_${TIMESTAMP}.txt"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to log messages
log() {
    echo -e "${GREEN}[$(date +'%Y-%m-%d %H:%M:%S')] $1${NC}" | tee -a "$LOG_FILE"
}

error() {
    echo -e "${RED}[ERROR] $1${NC}" | tee -a "$LOG_FILE"
    exit 1
}

warning() {
    echo -e "${YELLOW}[WARNING] $1${NC}" | tee -a "$LOG_FILE"
}

# Create backup directory
mkdir -p "$BACKUP_DIR"

log "Starting database backup process..."
log "Backup will be saved to: $BACKUP_FILE"

# Check if MySQL client is available
if ! command -v mysql &> /dev/null; then
    error "MySQL client not found. Please install MySQL client."
fi

# Check if database connection works
if ! mysql -h "$DB_HOST" -u "$DB_USER" -e "USE $DB_NAME;" 2>/dev/null; then
    error "Cannot connect to database '$DB_NAME'. Please check connection settings."
fi

# Perform the backup
log "Creating database backup..."
mysqldump -h "$DB_HOST" -u "$DB_USER" \
    --single-transaction \
    --routines \
    --triggers \
    --events \
    --add-drop-table \
    --add-drop-trigger \
    --databases "$DB_NAME" > "$BACKUP_FILE" 2>> "$LOG_FILE"

if [ $? -eq 0 ]; then
    log "✅ Database backup completed successfully!"

    # Get backup file size
    BACKUP_SIZE=$(du -h "$BACKUP_FILE" | cut -f1)
    log "Backup file size: $BACKUP_SIZE"

    # Verify backup integrity
    log "Verifying backup integrity..."
    if mysql -h "$DB_HOST" -u "$DB_USER" "$DB_NAME" < /dev/null 2>/dev/null; then
        if grep -q "CREATE TABLE" "$BACKUP_FILE"; then
            log "✅ Backup integrity verified - contains table structures"
        else
            error "❌ Backup verification failed - incomplete backup file"
        fi
    else
        warning "⚠️ Could not fully verify backup integrity"
    fi

    # Create compressed backup
    log "Creating compressed backup..."
    gzip "$BACKUP_FILE"
    log "✅ Compressed backup created: ${BACKUP_FILE}.gz"

    # Clean up old backups (keep last 10)
    log "Cleaning up old backups..."
    ls -t "${BACKUP_DIR}"/researchafrica_backup_*.sql.gz 2>/dev/null | tail -n +11 | xargs rm -f 2>/dev/null || true

    log "🎉 Backup process completed successfully!"
    log "📁 Backup location: ${BACKUP_FILE}.gz"
    log "📝 Log file: $LOG_FILE"

else
    error "❌ Database backup failed!"
fi

# Verify backup can be restored (optional, can be disabled for large databases)
verify_restore() {
    log "Verifying backup restore capability..."

    # Create temporary database for testing
    TEST_DB="${DB_NAME}_test_${TIMESTAMP}"

    if mysql -h "$DB_HOST" -u "$DB_USER" -e "CREATE DATABASE $TEST_DB;" 2>/dev/null; then
        if gunzip -c "${BACKUP_FILE}.gz" | mysql -h "$DB_HOST" -u "$DB_USER" "$TEST_DB" 2>> "$LOG_FILE"; then
            log "✅ Backup restore verification successful!"
            mysql -h "$DB_HOST" -u "$DB_USER" -e "DROP DATABASE $TEST_DB;" 2>/dev/null
        else
            warning "⚠️ Backup restore verification failed - backup may not be restorable"
            mysql -h "$DB_HOST" -u "$DB_USER" -e "DROP DATABASE $TEST_DB;" 2>/dev/null
        fi
    else
        warning "⚠️ Could not create test database for restore verification"
    fi
}

# Uncomment the next line to enable restore verification
# verify_restore

exit 0
