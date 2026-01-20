# Google Map Demo - Live Location Tracking

A Laravel-based web application that provides live Google Maps functionality with real-time location tracking, search capabilities, and location management.

## Features

🗺️ **Interactive Google Maps Integration**
- Real-time map display with Google Maps API
- Responsive design with modern UI

📍 **Live Location Tracking**
- Get current user location using browser geolocation
- Real-time location updates every 5 seconds
- Live location sharing between multiple users
- Visual indicators for online/offline status

🔍 **Advanced Search Functionality**
- Google Places API integration
- Autocomplete search suggestions
- Search for any location worldwide
- Place markers with detailed information

💾 **Location Management**
- Save favorite locations to database
- View saved locations with coordinates
- Delete unwanted locations
- Persistent storage using SQLite

🎨 **Modern User Interface**
- Clean, responsive design using Tailwind CSS
- Real-time status indicators
- Interactive controls and buttons
- Mobile-friendly interface

## Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js and npm
- Google Maps API key

### Setup Steps

1. **Clone the repository**
   ```bash
   git clone <your-repo-url>
   cd google-map-demo
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment Configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure Google Maps API**
   - Get your Google Maps API key from [Google Cloud Console](https://console.cloud.google.com/)
   - Enable the following APIs:
     - Maps JavaScript API
     - Places API
     - Geolocation API
   - Add your API key to `.env`:
     ```
     GOOGLE_MAPS_API_KEY=your_actual_api_key_here
     ```

6. **Database Setup**
   ```bash
   php artisan migrate
   ```

7. **Build Assets**
   ```bash
   npm run build
   ```

8. **Start the Development Server**
   ```bash
   php artisan serve
   ```

   Visit `http://localhost:8000/map` to see the application.

## Usage

### Getting Started
1. Open the application at `/map`
2. Click "Get My Location" to find your current position
3. Allow location permissions when prompted

### Live Location Tracking
1. Click "Start Live Tracking" to begin real-time location updates
2. Your location will be updated every 5 seconds
3. Other users' live locations will appear as green markers
4. Click "Stop Live Tracking" to disable real-time updates

### Searching for Places
1. Use the search box to find any location
2. Select from autocomplete suggestions
3. Click on markers to see place details
4. Save interesting locations for later

### Managing Saved Locations
1. Click "Save Location" on any marker to store it
2. View all saved locations in the bottom panel
3. Click "View" to navigate to a saved location
4. Click "Delete" to remove unwanted locations

## API Endpoints

The application provides RESTful API endpoints:

- `GET /api/locations` - Get all saved locations
- `POST /api/locations` - Save a new location
- `POST /api/live-location` - Update live location
- `GET /api/live-locations` - Get all live user locations
- `DELETE /api/locations/{id}` - Delete a location

## Technical Details

### Backend
- **Framework**: Laravel 12
- **Database**: SQLite (easily configurable to MySQL/PostgreSQL)
- **API**: RESTful endpoints with JSON responses
- **Models**: Location model with proper relationships and scopes

### Frontend
- **Styling**: Tailwind CSS v4
- **Build Tool**: Vite
- **JavaScript**: Vanilla JS with Google Maps API
- **Real-time Updates**: Polling-based location updates

### Database Schema
```sql
locations table:
- id (primary key)
- name (string)
- latitude (decimal 10,8)
- longitude (decimal 11,8)
- address (text, nullable)
- type (string: 'saved' or 'live_location')
- user_id (string, nullable)
- timestamps
```

## Configuration

### Google Maps API Setup
1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select existing one
3. Enable the following APIs:
   - Maps JavaScript API
   - Places API
   - Geolocation API (optional)
4. Create credentials (API Key)
5. Restrict the API key to your domain for security
6. Add the key to your `.env` file

### Environment Variables
```env
GOOGLE_MAPS_API_KEY=your_api_key_here
DB_CONNECTION=sqlite
SESSION_DRIVER=database
```

## Development

### Running in Development Mode
```bash
# Start Laravel development server
php artisan serve

# Start Vite development server (in another terminal)
npm run dev
```

### Building for Production
```bash
npm run build
```

## Troubleshooting

### Common Issues

1. **"This page can't load Google Maps correctly"**
   - Check if your API key is valid
   - Ensure required APIs are enabled
   - Verify API key restrictions

2. **Location not working**
   - Ensure HTTPS is used (required for geolocation)
   - Check browser location permissions
   - Verify geolocation is supported

3. **Database errors**
   - Run `php artisan migrate` to create tables
   - Check database file permissions
   - Verify SQLite is installed

4. **Assets not loading**
   - Run `npm run build` to compile assets
   - Check Vite configuration
   - Verify file permissions

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

For support and questions, please open an issue in the repository or contact the development team.