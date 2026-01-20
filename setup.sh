#!/bin/bash

echo "🗺️  Google Map Demo - Setup Script"
echo "=================================="

# Check if .env exists
if [ ! -f .env ]; then
    echo "📋 Creating .env file from .env.example..."
    cp .env.example .env
else
    echo "✅ .env file already exists"
fi

# Generate application key
echo "🔑 Generating application key..."
php artisan key:generate

# Install PHP dependencies
echo "📦 Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader

# Install Node.js dependencies
echo "📦 Installing Node.js dependencies..."
npm install

# Run database migrations
echo "🗄️  Running database migrations..."
php artisan migrate --force

# Build assets
echo "🏗️  Building assets..."
npm run build

# Clear and cache config
echo "⚡ Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ""
echo "✅ Setup completed successfully!"
echo ""
echo "📝 Next steps:"
echo "1. Add your Google Maps API key to .env file:"
echo "   GOOGLE_MAPS_API_KEY=your_actual_api_key_here"
echo ""
echo "2. Start the development server:"
echo "   php artisan serve"
echo ""
echo "3. Visit http://localhost:8000/map to see your application"
echo ""
echo "🔗 Get your Google Maps API key from:"
echo "   https://console.cloud.google.com/"
echo ""
echo "📚 Enable these APIs in Google Cloud Console:"
echo "   - Maps JavaScript API"
echo "   - Places API"
echo "   - Geolocation API"
echo ""