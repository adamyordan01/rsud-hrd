#!/bin/bash

echo "🚀 Setting up HRD Roles & Permissions with Prefix..."

# 1. Dump autoload untuk load helper baru
echo "📦 Running composer dump-autoload..."
composer dump-autoload

# 2. Clear cache
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# 3. Run dry-run migration untuk melihat status current
echo "🔍 Analyzing current state (dry-run)..."
php artisan hrd:migrate-prefix --dry-run

echo ""
echo "✅ Setup completed!"
echo ""
echo "Next steps:"
echo "1. Review the dry-run analysis above"
echo "2. Run: php artisan db:seed --class=RolesAndPermissionsSeeder (untuk create prefix roles/permissions)"
echo "3. Run: php artisan hrd:migrate-prefix (untuk migrate existing data)"
echo "4. Test the application"
