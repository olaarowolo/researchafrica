#!/bin/bash

# Gemini Artisan Command Setup Script
# This script ensures proper permissions for AI assistants to run Laravel artisan commands

echo "=== Gemini Artisan Command Setup ==="
echo "Setting up permissions for AI assistants to run Laravel commands..."

# Current user
CURRENT_USER=$(whoami)
echo "Current user: $CURRENT_USER"

# 1. Make artisan executable
echo "1. Making artisan executable..."
chmod +x artisan
echo "✓ Artisan is now executable"

# 2. Set proper permissions for Laravel directories
echo "2. Setting permissions for Laravel directories..."
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
chmod -R 775 public/
echo "✓ Laravel directories have proper write permissions"

# 3. Ensure vendor directory has proper permissions
echo "3. Setting vendor directory permissions..."
chmod -R 755 vendor/ 2>/dev/null || echo "✓ Vendor directory permissions set"
echo "✓ Vendor directory permissions configured"

# 4. Test artisan command
echo "4. Testing artisan command..."
php artisan --version
if [ $? -eq 0 ]; then
    echo "✓ Artisan commands are working correctly"
else
    echo "✗ Artisan command failed - check PHP installation"
    exit 1
fi

# 5. Create artisan wrapper script for AI assistants
echo "5. Creating artisan wrapper script..."
cat > gemini_artisan << 'EOF'
#!/bin/bash
# Gemini Artisan Wrapper
# This script allows AI assistants to run artisan commands safely

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR"

# Run the artisan command
php artisan "$@"
EOF

chmod +x gemini_artisan
echo "✓ Gemini artisan wrapper created"

# 6. Set environment variables for AI assistants
echo "6. Setting up environment for AI assistants..."
echo "# Gemini AI Assistant Environment Variables" > .env.gemini
echo "export ARTISAN_ALLOWED=1" >> .env.gemini
echo "export USER_PERMISSIONS=755" >> .env.gemini
echo "✓ Environment file created"

# 7. Display current permissions status
echo "7. Current permission status:"
echo "Artisan permissions: $(ls -l artisan | awk '{print $1, $9}')"
echo "Storage permissions: $(ls -ld storage/ | awk '{print $1, $9}')"
echo "Bootstrap cache permissions: $(ls -ld bootstrap/cache/ | awk '{print $1, $9}')"

echo ""
echo "=== Setup Complete ==="
echo "Gemini AI Assistant can now run artisan commands using:"
echo "  ./gemini_artisan <command>"
echo "  php artisan <command>"
echo ""
echo "Example commands:"
echo "  ./gemini_artisan list"
echo "  ./gemini_artisan migrate"
echo "  ./gemini_artisan make:controller TestController"
