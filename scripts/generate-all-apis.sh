#!/bin/bash

# Generate All API Scaffolds
# This script generates the scaffolding for all remaining API controllers

cd "$(dirname "$0")/.."

echo "🚀 Generating API Scaffolds..."
echo ""

# Subscriptions API
echo "📦 Generating Subscriptions API..."
php artisan api:scaffold Subscription --endpoints=cancel,renew

# Posts API
echo "📝 Generating Posts API..."
php artisan api:scaffold Post --endpoints=like,unlike,bookmark,report

# Comments API
echo "💬 Generating Comments API..."
php artisan api:scaffold Comment

# Payments API
echo "💳 Generating Payments API..."
php artisan api:scaffold Payment --endpoints=tip,ppv,withdraw,transactions

# Media API
echo "🖼️  Generating Media API..."
php artisan api:scaffold Media --endpoints=upload,download,encode

# Live API
echo "📹 Generating Live API..."
php artisan api:scaffold Live --endpoints=start,stop,viewers,join

# Shop API
echo "🛍️  Generating Shop API..."
php artisan api:scaffold Shop --endpoints=products,orders

# Notifications API
echo "🔔 Generating Notifications API..."
php artisan api:scaffold Notification --endpoints=markRead,readAll,preferences

# Stories API
echo "📸 Generating Stories API..."
php artisan api:scaffold Story --endpoints=view,viewers

# Admin API
echo "⚙️  Generating Admin API..."
php artisan api:scaffold Admin --endpoints=dashboard,users,reports,analytics

# Odeva API
echo "🤖 Generating Odeva API..."
php artisan api:scaffold Odeva --endpoints=chat,functions,execute,context,automation,subscribe

echo ""
echo "✅ All API scaffolds generated successfully!"
echo ""
echo "📝 Next steps:"
echo "1. Review and customize generated controllers in app/Http/Controllers/Api/V1/"
echo "2. Add proper validation rules in app/Http/Requests/Api/"
echo "3. Complete resource transformations in app/Http/Resources/"
echo "4. Update routes if needed in routes/api/v1/"
echo ""
echo "🎯 Generated APIs:"
echo "  - Subscription API (subscriptions endpoint)"
echo "  - Post API (posts endpoint)"
echo "  - Comment API (comments endpoint)"
echo "  - Payment API (payments endpoint)"
echo "  - Media API (media endpoint)"
echo "  - Live API (live endpoint)"
echo "  - Shop API (shop endpoint)"
echo "  - Notification API (notifications endpoint)"
echo "  - Story API (stories endpoint)"
echo "  - Admin API (admin endpoint)"
echo "  - Odeva API (odeva endpoint)"

