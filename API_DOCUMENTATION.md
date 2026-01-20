# Google Map Demo - API Documentation

This document provides detailed information about the API endpoints available in the Google Map Demo application.

## Base URL
```
http://localhost:8000/api
```

## Authentication
Currently, the API does not require authentication. All endpoints are publicly accessible for demo purposes.

## Endpoints

### 1. Get All Locations
Retrieve all saved locations from the database.

**Endpoint:** `GET /api/locations`

**Response:**
```json
[
    {
        "id": 1,
        "name": "Dhaka University",
        "latitude": "23.72790000",
        "longitude": "90.39810000",
        "address": "Dhaka University, Dhaka 1000, Bangladesh",
        "type": "saved",
        "user_id": null,
        "created_at": "2026-01-20T03:52:28.000000Z",
        "updated_at": "2026-01-20T03:52:28.000000Z"
    }
]
```

**cURL Example:**
```bash
curl -X GET http://localhost:8000/api/locations \
  -H "Accept: application/json"
```

### 2. Save New Location
Store a new location in the database.

**Endpoint:** `POST /api/locations`

**Request Body:**
```json
{
    "name": "My Favorite Place",
    "latitude": 23.8103,
    "longitude": 90.4125,
    "address": "Some address here",
    "type": "saved"
}
```

**Validation Rules:**
- `name`: required, string, max 255 characters
- `latitude`: required, numeric, between -90 and 90
- `longitude`: required, numeric, between -180 and 180
- `address`: optional, string, max 500 characters
- `type`: optional, string, max 50 characters

**Response:**
```json
{
    "success": true,
    "location": {
        "id": 11,
        "name": "My Favorite Place",
        "latitude": "23.81030000",
        "longitude": "90.41250000",
        "address": "Some address here",
        "type": "saved",
        "user_id": null,
        "created_at": "2026-01-20T04:00:00.000000Z",
        "updated_at": "2026-01-20T04:00:00.000000Z"
    }
}
```

**cURL Example:**
```bash
curl -X POST http://localhost:8000/api/locations \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "X-CSRF-TOKEN: your_csrf_token" \
  -d '{
    "name": "My Favorite Place",
    "latitude": 23.8103,
    "longitude": 90.4125,
    "address": "Some address here",
    "type": "saved"
  }'
```

### 3. Update Live Location
Update or create a user's live location.

**Endpoint:** `POST /api/live-location`

**Request Body:**
```json
{
    "latitude": 23.8103,
    "longitude": 90.4125,
    "user_id": "user_123"
}
```

**Validation Rules:**
- `latitude`: required, numeric, between -90 and 90
- `longitude`: required, numeric, between -180 and 180
- `user_id`: optional, string, max 100 characters (defaults to session ID)

**Response:**
```json
{
    "success": true,
    "location": {
        "id": 12,
        "name": "Live Location",
        "latitude": "23.81030000",
        "longitude": "90.41250000",
        "address": "Current Position",
        "type": "live_location",
        "user_id": "user_123",
        "created_at": "2026-01-20T04:00:00.000000Z",
        "updated_at": "2026-01-20T04:00:00.000000Z"
    }
}
```

**cURL Example:**
```bash
curl -X POST http://localhost:8000/api/live-location \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "X-CSRF-TOKEN: your_csrf_token" \
  -d '{
    "latitude": 23.8103,
    "longitude": 90.4125,
    "user_id": "user_123"
  }'
```

### 4. Get Live Locations
Retrieve all active live locations (updated within the last 5 minutes).

**Endpoint:** `GET /api/live-locations`

**Response:**
```json
[
    {
        "id": 12,
        "name": "Live Location",
        "latitude": "23.81030000",
        "longitude": "90.41250000",
        "address": "Current Position",
        "type": "live_location",
        "user_id": "user_123",
        "created_at": "2026-01-20T04:00:00.000000Z",
        "updated_at": "2026-01-20T04:00:00.000000Z"
    }
]
```

**cURL Example:**
```bash
curl -X GET http://localhost:8000/api/live-locations \
  -H "Accept: application/json"
```

### 5. Delete Location
Delete a specific location by ID.

**Endpoint:** `DELETE /api/locations/{id}`

**Parameters:**
- `id`: Location ID (integer)

**Response:**
```json
{
    "success": true,
    "message": "Location deleted successfully"
}
```

**cURL Example:**
```bash
curl -X DELETE http://localhost:8000/api/locations/1 \
  -H "Accept: application/json" \
  -H "X-CSRF-TOKEN: your_csrf_token"
```

## Error Responses

### Validation Error (422)
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "latitude": [
            "The latitude field is required."
        ],
        "longitude": [
            "The longitude field is required."
        ]
    }
}
```

### Not Found Error (404)
```json
{
    "message": "No query results for model [App\\Models\\Location] 999"
}
```

### Server Error (500)
```json
{
    "message": "Server Error"
}
```

## JavaScript Integration Examples

### Fetch API Examples

#### Get All Locations
```javascript
async function getLocations() {
    try {
        const response = await fetch('/api/locations');
        const locations = await response.json();
        console.log(locations);
    } catch (error) {
        console.error('Error:', error);
    }
}
```

#### Save Location
```javascript
async function saveLocation(name, lat, lng, address = '') {
    try {
        const response = await fetch('/api/locations', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                name: name,
                latitude: lat,
                longitude: lng,
                address: address,
                type: 'saved'
            })
        });
        
        const result = await response.json();
        if (result.success) {
            console.log('Location saved:', result.location);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}
```

#### Update Live Location
```javascript
async function updateLiveLocation(lat, lng, userId = null) {
    try {
        const response = await fetch('/api/live-location', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                latitude: lat,
                longitude: lng,
                user_id: userId
            })
        });
        
        const result = await response.json();
        if (result.success) {
            console.log('Live location updated:', result.location);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}
```

## Rate Limiting
Currently, there are no rate limits implemented. In a production environment, consider implementing rate limiting to prevent abuse.

## CORS
Cross-Origin Resource Sharing (CORS) is not configured by default. If you need to access the API from a different domain, configure CORS in your Laravel application.

## Security Considerations

1. **CSRF Protection**: All POST, PUT, DELETE requests require a valid CSRF token
2. **Input Validation**: All inputs are validated according to the rules specified
3. **SQL Injection**: Laravel's Eloquent ORM provides protection against SQL injection
4. **XSS Protection**: Always sanitize output when displaying user-generated content

## Testing the API

You can test the API using tools like:
- **Postman**: Import the endpoints and test them interactively
- **cURL**: Use the provided cURL examples
- **Browser DevTools**: Test directly from the map interface
- **Insomnia**: Another great API testing tool

## Future Enhancements

Potential improvements for the API:
- Authentication and authorization
- Rate limiting
- API versioning
- Pagination for large datasets
- WebSocket support for real-time updates
- Geofencing capabilities
- Location history tracking