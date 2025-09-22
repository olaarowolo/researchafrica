ri# DevOps Guide - Research Africa Platform

## 📋 Overview

This guide provides comprehensive DevOps practices, deployment strategies, and infrastructure management for the Research Africa platform, including the AfriScribe proofreading service module.

## 🏗️ Architecture Overview

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Load Balancer │    │   Web Servers   │    │   Database      │
│   (Nginx/HAProxy)│    │   (PHP-FPM)     │    │   (MySQL)       │
└─────────────────┘    └─────────────────┘    └─────────────────┘
         │                       │                       │
         └───────────────────────┼───────────────────────┘
                                 │
                    ┌─────────────────┐
                    │   File Storage  │
                    │   (Local/S3)    │
                    └─────────────────┘
```

## 🚀 Development Environment

### Prerequisites

- **Docker & Docker Compose** (recommended)
- **PHP 8.1+** (for local development)
- **Composer**
- **Node.js & npm** (for frontend assets)
- **MySQL 8.0+**
- **Git**

### Option 1: Laravel Sail (Recommended)

Laravel Sail provides a complete Docker development environment:

```bash
# Install Laravel Sail
composer require laravel/sail --dev

# Publish Sail files
php artisan sail:install

# Start the environment
./vendor/bin/sail up -d

# Install PHP dependencies
./vendor/bin/sail composer install

# Install Node.js dependencies
./vendor/bin/sail npm install

# Generate application key
./vendor/bin/sail artisan key:generate

# Run migrations
./vendor/bin/sail artisan migrate

# Build frontend assets
./vendor/bin/sail npm run build
```

### Option 2: Local Development Setup

```bash
# Install PHP dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database in .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=research_africa
# DB_USERNAME=your_username
# DB_PASSWORD=your_password

# Run migrations
php artisan migrate

# Install Node.js dependencies
npm install

# Build assets
npm run build

# Start development server
php artisan serve
```

## 🏭 Production Deployment

### Deployment Strategies

#### 1. Traditional Server Deployment

**Server Requirements:**
- Ubuntu 20.04 LTS or CentOS 8+
- PHP 8.1+ with FPM
- MySQL 8.0+
- Nginx or Apache
- Redis (optional, for caching and queues)
- Supervisor (for queue workers)

**Deployment Script:**

```bash
#!/bin/bash
# deploy.sh

set -e

echo "🚀 Starting deployment..."

# Variables
APP_DIR="/var/www/research-africa"
BACKUP_DIR="/var/backups/research-africa"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)

# Create backup
echo "📦 Creating backup..."
mkdir -p $BACKUP_DIR
tar -czf $BACKUP_DIR/backup_$TIMESTAMP.tar.gz -C $APP_DIR .

# Pull latest changes
echo "📥 Pulling latest changes..."
cd $APP_DIR
git pull origin main

# Install dependencies
echo "📦 Installing dependencies..."
composer install --no-dev --optimize-autoloader

# Run database migrations
echo "🗄️ Running migrations..."
php artisan migrate --force

# Clear caches
echo "🧹 Clearing caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Build frontend assets
echo "🔨 Building assets..."
npm ci
npm run build

# Set permissions
echo "🔐 Setting permissions..."
chown -R www-data:www-data storage bootstrap/cache
chmod -R 755 storage bootstrap/cache

# Restart services
echo "🔄 Restarting services..."
sudo systemctl reload php8.1-fpm
sudo systemctl reload nginx

echo "✅ Deployment completed successfully!"
```

#### 2. Docker Production Deployment

**Docker Compose Production:**

```yaml
# docker-compose.prod.yml
version: '3.8'

services:
  app:
    image: research-africa:latest
    build:
      context: .
      dockerfile: Dockerfile.prod
    environment:
      - APP_ENV=production
      - APP_DEBUG=false
      - DB_HOST=db
      - DB_DATABASE=research_africa
      - DB_USERNAME=laravel
      - DB_PASSWORD=${DB_PASSWORD}
    volumes:
      - app-storage:/var/www/storage
    depends_on:
      - db
    networks:
      - app-network

  nginx:
    image: nginx:alpine
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./nginx.conf:/etc/nginx/nginx.conf
      - app-storage:/var/www/storage
    depends_on:
      - app
    networks:
      - app-network

  db:
    image: mysql:8.0
    environment:
      - MYSQL_ROOT_PASSWORD=${MYSQL_ROOT_PASSWORD}
      - MYSQL_DATABASE=research_africa
      - MYSQL_USER=laravel
      - MYSQL_PASSWORD=${DB_PASSWORD}
    volumes:
      - db-data:/var/lib/mysql
    networks:
      - app-network

  redis:
    image: redis:alpine
    networks:
      - app-network

volumes:
  app-storage:
  db-data:

networks:
  app-network:
    driver: bridge
```

**Production Dockerfile:**

```dockerfile
# Dockerfile.prod
FROM php:8.1-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    mysql-client

# Install PHP extensions
RUN docker-php-ext-install \
    pdo_mysql \
    gd \
    xml \
    zip \
    bcmath \
    intl \
    opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data storage bootstrap/cache

# Expose port
EXPOSE 9000

CMD ["php-fpm"]
```

### 3. Cloud Deployment (AWS/GCP/Azure)

#### AWS Deployment with Elastic Beanstalk

```yaml
# .ebextensions/environment.config
option_settings:
  aws:elasticbeanstalk:application:environment:
    APP_ENV: production
    APP_DEBUG: false
    LOG_CHANNEL: errorlog
  aws:elasticbeanstalk:container:php:phpini:
    document_root: /public
    memory_limit: 512M
  aws:elasticbeanstalk:environment:proxy:staticfiles:
    /public/css: public/css
    /public/js: public/js
    /public/images: public/images
```

## 🔧 Infrastructure as Code

### Ansible Playbook Example

```yaml
# deploy.yml
---
- name: Deploy Research Africa Platform
  hosts: web
  become: yes

  vars:
    app_dir: "/var/www/research-africa"
    php_version: "8.1"

  tasks:
    - name: Pull latest code
      git:
        repo: 'https://github.com/your-repo/research-africa.git'
        dest: "{{ app_dir }}"
        version: main
        force_reset: yes

    - name: Install PHP dependencies
      composer:
        command: install
        working_dir: "{{ app_dir }}"
        no_dev: yes
        optimize_autoloader: yes

    - name: Run migrations
      command: php artisan migrate --force
      args:
        chdir: "{{ app_dir }}"

    - name: Clear caches
      command: php artisan {{ item }}:cache
      args:
        chdir: "{{ app_dir }}"
      with_items:
        - config
        - route
        - view

    - name: Build frontend assets
      npm:
        path: "{{ app_dir }}"
        ci: yes
        production: yes

    - name: Set permissions
      file:
        path: "{{ app_dir }}/{{ item }}"
        state: directory
        owner: www-data
        group: www-data
        mode: '0755'
        recurse: yes
      with_items:
        - storage
        - bootstrap/cache

    - name: Restart PHP-FPM
      service:
        name: php{{ php_version }}-fpm
        state: restarted

    - name: Restart Nginx
      service:
        name: nginx
        state: restarted
```

## 📊 Monitoring & Logging

### Application Monitoring

**Recommended Tools:**
- **Laravel Telescope** (development)
- **New Relic** (production)
- **DataDog** (comprehensive monitoring)
- **Sentry** (error tracking)

**Installation:**

```bash
# Laravel Telescope (development)
composer require laravel/telescope --dev

# Sentry (production)
composer require sentry/sentry-laravel

# New Relic PHP Agent
# Install via package manager or New Relic installer
```

### Log Management

**Centralized Logging with ELK Stack:**

```yaml
# docker-compose.logging.yml
version: '3.8'

services:
  elasticsearch:
    image: elasticsearch:7.15.0
    environment:
      - discovery.type=single-node
    volumes:
      - es-data:/usr/share/elasticsearch/data

  logstash:
    image: logstash:7.15.0
    volumes:
      - ./logstash.conf:/usr/share/logstash/pipeline/logstash.conf
    depends_on:
      - elasticsearch

  kibana:
    image: kibana:7.15.0
    ports:
      - "5601:5601"
    depends_on:
      - elasticsearch

volumes:
  es-data:
```

**Logstash Configuration:**

```conf
# logstash.conf
input {
  file {
    path => "/var/log/research-africa/*.log"
    start_position => "beginning"
  }
}

filter {
  grok {
    match => {
      "message" => "%{TIMESTAMP_ISO8601:timestamp} %{LOGLEVEL:level} %{GREEDYDATA:message}"
    }
  }
}

output {
  elasticsearch {
    hosts => ["elasticsearch:9200"]
    index => "research-africa-%{+YYYY.MM.dd}"
  }
}
```

## 🔒 DevSecOps Security Framework

### Security-First Development

#### 1. Secure Coding Practices
```php
// Use Laravel's built-in security features
Route::middleware(['auth', 'verified', 'throttle:60,1'])->group(function () {
    // Protected routes
});

// Parameter binding to prevent injection
Route::get('/articles/{article}', function (Article $article) {
    return view('articles.show', compact('article'));
})->name('articles.show');

// CSRF protection (enabled by default)
<form method="POST" action="/articles">
    @csrf
    <!-- form fields -->
</form>
```

#### 2. Input Validation & Sanitization
```php
// app/Http/Requests/StoreArticleRequest.php
public function rules()
{
    return [
        'title' => 'required|string|max:255|regex:/^[\pL\s\-\.\,\!\?\:\;]+$/u',
        'content' => 'required|string|min:100',
        'file' => 'nullable|file|mimes:pdf,doc,docx|max:10240', // 10MB max
        'email' => 'required|email:rfc,dns|unique:users',
        'password' => [
            'required',
            'string',
            'min:12',
            'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/'
        ]
    ];
}
```

### Automated Security Testing

#### 1. SAST (Static Application Security Testing)
```yaml
# .github/workflows/security.yml
name: Security Scan

on:
  push:
    branches: [ main, develop ]
  pull_request:
    branches: [ main ]

jobs:
  security:
    runs-on: ubuntu-latest
    steps:
    - uses: actions/checkout@v3

    - name: Run PHP Security Checker
      uses: symfonycorp/security-checker-action@v4

    - name: Run PHPStan
      run: ./vendor/bin/phpstan analyse --level=8

    - name: Run Psalm
      run: ./vendor/bin/psalm --taint-analysis

    - name: Run SonarQube
      uses: sonarsource/sonarqube-scan-action@master
      env:
        SONAR_TOKEN: ${{ secrets.SONAR_TOKEN }}
        SONAR_HOST_URL: ${{ secrets.SONAR_HOST_URL }}

    - name: Dependency Check
      uses: dependency-check/Dependency-Check_Action@main
      with:
        project: 'Research Africa'
        path: '.'
        format: 'ALL'
```

#### 2. DAST (Dynamic Application Security Testing)
```yaml
# docker-compose.security.yml
version: '3.8'

services:
  owasp-zap:
    image: owasp/zap2docker-stable
    command: zap.sh -daemon -host 0.0.0.0 -port 8080
    ports:
      - "8080:8080"
    volumes:
      - zap-data:/zap

  nikto:
    image: sullo/nikto
    command: -h http://app:80
    depends_on:
      - app

  sqlmap:
    image: sqlmap
    command: -u "http://app/search?q=test" --batch --crawl=1
    depends_on:
      - app

volumes:
  zap-data:
```

### Infrastructure Security

#### 1. Container Security
```dockerfile
# Dockerfile.secure
FROM php:8.1-fpm-alpine

### SSL Configuration (Nginx)

```nginx
# /etc/nginx/sites-available/research-africa
server {
    listen 80;
    server_name yourdomain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name yourdomain.com;

    ssl_certificate /etc/ssl/certs/yourdomain.com.crt;
    ssl_certificate_key /etc/ssl/private/yourdomain.com.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES128-GCM-SHA256:ECDHE-RSA-AES256-GCM-SHA384;

    root /var/www/research-africa/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;
}
```

## ⚡ Performance Optimization

### Caching Strategy

```php
// config/cache.php - Add Redis configuration
'redis' => [
    'client' => env('REDIS_CLIENT', 'phpredis'),
    'options' => [
        'cluster' => env('REDIS_CLUSTER', 'redis'),
        'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
    ],
    'default' => [
        'url' => env('REDIS_URL'),
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD'),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_DB', '0'),
    ],
],
```

### Queue Configuration

**Supervisor Configuration:**

```ini
# /etc/supervisor/conf.d/research-africa-worker.conf
[program:research-africa-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/research-africa/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
numprocs=4
user=www-data
redirect_stderr=true
stdout_logfile=/var/log/supervisor/research-africa-worker.log
```

### Database Optimization

**MySQL Configuration:**

```ini
# /etc/mysql/mysql.conf.d/mysqld.cnf
[mysqld]
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
max_connections = 100
query_cache_size = 0
query_cache_type = 0
innodb_flush_method = O_DIRECT
innodb_flush_log_at_trx_commit = 2
```

## 🔄 CI/CD Pipeline

### GitHub Actions Example

```yaml
# .github/workflows/deploy.yml
name: Deploy to Production

on:
  push:
    branches: [ main ]
  pull_request:
    branches: [ main ]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
    - uses: actions/checkout@v3
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.1'
    - name: Install dependencies
      run: composer install --no-progress
    - name: Run tests
      run: php artisan test
    - name: Build assets
      run: npm ci && npm run build

  deploy:
    needs: test
    runs-on: ubuntu-latest
    if: github.ref == 'refs/heads/main'
    steps:
    - uses: actions/checkout@v3
    - name: Deploy to server
      uses: appleboy/ssh-action@master
      with:
        host: ${{ secrets.SERVER_HOST }}
        username: ${{ secrets.SERVER_USER }}
        key: ${{ secrets.SERVER_SSH_KEY }}
        script: |
          cd /var/www/research-africa
          ./deploy.sh
```

## 📁 Backup Strategy

### Automated Backup Script

```bash
#!/bin/bash
# backup.sh

BACKUP_DIR="/var/backups/research-africa"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
RETENTION_DAYS=30

# Create backup directory
mkdir -p $BACKUP_DIR

# Database backup
echo "📦 Creating database backup..."
mysqldump -h localhost -u research_africa -p'password' research_africa > $BACKUP_DIR/db_backup_$TIMESTAMP.sql

# Files backup
echo "📁 Creating files backup..."
tar -czf $BACKUP_DIR/files_backup_$TIMESTAMP.tar.gz \
    --exclude='/var/www/research-africa/storage/app/livewire-tmp' \
    --exclude='/var/www/research-africa/storage/logs' \
    /var/www/research-africa

# Upload to cloud storage (optional)
echo "☁️ Uploading to cloud storage..."
aws s3 sync $BACKUP_DIR s3://your-bucket/backups/$TIMESTAMP/

# Cleanup old backups
echo "🧹 Cleaning up old backups..."
find $BACKUP_DIR -type f -mtime +$RETENTION_DAYS -delete

echo "✅ Backup completed successfully!"
```

### Cron Schedule

```bash
# /etc/crontab
# Daily backup at 2 AM
0 2 * * * root /var/www/research-africa/backup.sh
```

## 🗄️ Database Management

### Migration Strategy

**Production Migration Commands:**

```bash
# Create backup before migration
php artisan backup:run --only-db

# Run migrations with confirmation
php artisan migrate --force

# Verify migration status
php artisan migrate:status

# Rollback if needed
php artisan migrate:rollback --step=1
```

### Database Maintenance

```sql
-- Optimize database performance
OPTIMIZE TABLE articles;
OPTIMIZE TABLE members;
OPTIMIZE TABLE comments;

-- Check for unused indexes
SELECT * FROM sys.schema_unused_indexes WHERE object_schema = 'research_africa';

-- Analyze slow queries
SELECT sql_text, exec_count, avg_timer_wait/1000000 as avg_time_ms
FROM performance_schema.events_statements_summary_by_digest
ORDER BY avg_timer_wait DESC
LIMIT 10;
```

## 🔍 Troubleshooting

### Common Issues

#### 1. File Upload Issues

```bash
# Check permissions
ls -la storage/

# Fix permissions
sudo chown -R www-data:www-data storage/
sudo chmod -R 755 storage/

# Check disk space
df -h
```

#### 2. Email Not Sending

```bash
# Check mail logs
tail -f storage/logs/laravel.log | grep mail

# Test mail configuration
php artisan tinker
Mail::raw('Test email', function($message) { $message->to('test@example.com')->subject('Test'); });
```

#### 3. Performance Issues

```bash
# Check PHP-FPM status
sudo systemctl status php8.1-fpm

# Monitor MySQL performance
mysqladmin -u root -p processlist

# Check disk I/O
iostat -x 1
```

### Debug Mode

**Enable Debug Mode Temporarily:**

```php
// In AppServiceProvider or specific controller
\Log::info('Debug info', [
    'user_id' => auth()->id(),
    'request' => request()->all(),
    'session' => session()->all()
]);
```

## 📈 Scaling

### Horizontal Scaling

**Load Balancer Configuration:**

```nginx
# /etc/nginx/nginx.conf
upstream research_africa_backend {
    least_conn;
    server app1.research-africa.internal:9000 weight=100;
    server app2.research-africa.internal:9000 weight=100;
    server app3.research-africa.internal:9000 weight=100;
}

server {
    listen 80;
    server_name research-africa.com;

    location / {
        proxy_pass http://research_africa_backend;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

### Database Scaling

**Read Replicas:**

```yaml
# docker-compose.scale.yml
services:
  db-replica:
    image: mysql:8.0
    environment:
      - MYSQL_ROOT_PASSWORD=${MYSQL_ROOT_PASSWORD}
      - MYSQL_DATABASE=research_africa
    command: --server-id=2 --log-bin=mysql-bin --read-only=1
    depends_on:
      - db
```

## 📞 Support & Maintenance

### Regular Maintenance Tasks

1. **Daily:**
   - Monitor error logs
   - Check disk space
   - Verify backup completion

2. **Weekly:**
   - Review slow query logs
   - Update security patches
   - Optimize database tables

3. **Monthly:**
   - Performance analysis
   - Security audit
   - Dependency updates

### Emergency Procedures

**Database Recovery:**

```bash
# Stop application
sudo systemctl stop nginx php8.1-fpm

# Restore database
mysql -u root -p research_africa < /var/backups/research-africa/db_backup_latest.sql

# Start application
sudo systemctl start php8.1-fpm nginx
```

**File System Recovery:**

```bash
# Restore from backup
cd /var/www
sudo rm -rf research-africa
sudo tar -xzf /var/backups/research-africa/files_backup_latest.tar.gz
sudo chown -R www-data:www-data research-africa
```

## 📚 Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Laravel Sail Documentation](https://laravel.com/docs/sail)
- [PHP-FPM Tuning Guide](https://www.php.net/manual/en/install.fpm.configuration.php)
- [MySQL Performance Tuning](https://dev.mysql.com/doc/refman/8.0/en/optimization.html)
- [Nginx Configuration Guide](https://nginx.org/en/docs/)

## 🤝 Contributing

When contributing to DevOps improvements:

1. Test all changes in staging environment first
2. Document any configuration changes
3. Update this README with new procedures
4. Include rollback procedures for critical changes
5. Test backup and recovery procedures

---

**Research Africa DevOps Team**

*Building robust infrastructure for African scholarly communication*
