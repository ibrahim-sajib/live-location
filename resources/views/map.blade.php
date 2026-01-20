<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Live Google Map Demo</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        #map {
            height: 70vh;
            width: 100%;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .controls {
            margin-top: 10px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            box-sizing: border-box;
            height: 40px;
            outline: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.2s ease;
        }

        #pac-input {
            background-color: #fff;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            font-weight: 400;
            padding: 0 16px;
            text-overflow: ellipsis;
            width: 100%;
            max-width: 400px;
        }

        #pac-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .pac-container {
            font-family: 'Inter', sans-serif;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .location-info {
            background: white;
            border-radius: 8px;
            padding: 16px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 16px;
        }

        .btn {
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.2s ease;
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background-color: #3b82f6;
            color: white;
        }

        .btn-primary:hover {
            background-color: #2563eb;
        }

        .btn-success {
            background-color: #10b981;
            color: white;
        }

        .btn-success:hover {
            background-color: #059669;
        }

        .btn-danger {
            background-color: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background-color: #dc2626;
        }

        .status-indicator {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 8px;
        }

        .status-online {
            background-color: #10b981;
        }

        .status-offline {
            background-color: #6b7280;
        }

        .loading {
            opacity: 0.6;
            pointer-events: none;
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-6">
        <!-- Header -->
        <div class="text-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Live Google Map Demo</h1>
            <p class="text-gray-600">Track live locations and search for places in real-time</p>
        </div>

        <!-- Controls Panel -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                <button id="getCurrentLocation" class="btn btn-primary">
                    📍 Get My Location
                </button>
                <button id="toggleLiveTracking" class="btn btn-success">
                    🔴 Start Live Tracking
                </button>
                <button id="saveCurrentLocation" class="btn btn-primary" disabled>
                    💾 Save Location
                </button>
                <button id="clearMarkers" class="btn btn-danger">
                    🗑️ Clear Markers
                </button>
            </div>
            
            <!-- Search Box -->
            <div class="mb-4">
                <input id="pac-input" class="controls w-full" type="text" placeholder="🔍 Search for places...">
            </div>

            <!-- Status Info -->
            <div id="statusInfo" class="location-info hidden">
                <div class="flex items-center justify-between">
                    <div>
                        <span id="locationStatus" class="status-indicator status-offline"></span>
                        <span id="statusText">Location tracking disabled</span>
                    </div>
                    <div id="coordinates" class="text-sm text-gray-500"></div>
                </div>
            </div>
        </div>

        <!-- Map Container -->
        <div class="bg-white rounded-lg shadow-sm p-4">
            <div id="map"></div>
        </div>

        <!-- Saved Locations -->
        <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
            <h3 class="text-lg font-semibold mb-4">Saved Locations</h3>
            <div id="savedLocations" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Saved locations will be loaded here -->
            </div>
        </div>
    </div>

    <script>
        let map;
        let currentLocationMarker;
        let liveTrackingEnabled = false;
        let liveTrackingInterval;
        let markers = [];
        let userMarkers = new Map();

        // Initialize map
        function initMap() {
            // Default location (Dhaka)
            const defaultLocation = { lat: 23.8103, lng: 90.4125 };

            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 13,
                center: defaultLocation,
                mapTypeControl: true,
                streetViewControl: true,
                fullscreenControl: true,
                styles: [
                    {
                        featureType: "poi",
                        elementType: "labels",
                        stylers: [{ visibility: "on" }]
                    }
                ]
            });

            // Initialize search box
            initSearchBox();
            
            // Load saved locations
            loadSavedLocations();
            
            // Load live locations
            loadLiveLocations();
            
            // Set up periodic live location updates
            setInterval(loadLiveLocations, 10000); // Update every 10 seconds

            // Try to get user's current location on load
            getCurrentLocation();
        }

        function initSearchBox() {
            const input = document.getElementById("pac-input");
            const searchBox = new google.maps.places.SearchBox(input);

            map.addListener("bounds_changed", () => {
                searchBox.setBounds(map.getBounds());
            });

            searchBox.addListener("places_changed", () => {
                const places = searchBox.getPlaces();
                if (places.length == 0) return;

                // Clear existing search markers
                markers.forEach(marker => marker.setMap(null));
                markers = [];

                const bounds = new google.maps.LatLngBounds();

                places.forEach(place => {
                    if (!place.geometry || !place.geometry.location) return;

                    const marker = new google.maps.Marker({
                        map,
                        title: place.name,
                        position: place.geometry.location,
                        icon: {
                            url: place.icon,
                            size: new google.maps.Size(71, 71),
                            origin: new google.maps.Point(0, 0),
                            anchor: new google.maps.Point(17, 34),
                            scaledSize: new google.maps.Size(25, 25),
                        }
                    });

                    // Add info window
                    const infoWindow = new google.maps.InfoWindow({
                        content: `
                            <div>
                                <h3>${place.name}</h3>
                                <p>${place.formatted_address || 'Address not available'}</p>
                                <button onclick="saveLocation('${place.name}', ${place.geometry.location.lat()}, ${place.geometry.location.lng()}, '${place.formatted_address || ''}')" class="btn btn-primary btn-sm mt-2">Save Location</button>
                            </div>
                        `
                    });

                    marker.addListener('click', () => {
                        infoWindow.open(map, marker);
                    });

                    markers.push(marker);

                    if (place.geometry.viewport) {
                        bounds.union(place.geometry.viewport);
                    } else {
                        bounds.extend(place.geometry.location);
                    }
                });

                map.fitBounds(bounds);
            });
        }

        // Get current location
        function getCurrentLocation() {
            if (navigator.geolocation) {
                document.getElementById('getCurrentLocation').classList.add('loading');
                
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const pos = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude,
                        };

                        updateCurrentLocationMarker(pos);
                        map.setCenter(pos);
                        map.setZoom(15);
                        
                        updateLocationStatus(true, pos);
                        document.getElementById('saveCurrentLocation').disabled = false;
                        document.getElementById('getCurrentLocation').classList.remove('loading');
                    },
                    (error) => {
                        console.error('Error getting location:', error);
                        updateLocationStatus(false);
                        document.getElementById('getCurrentLocation').classList.remove('loading');
                        alert('Error getting your location. Please check your browser permissions.');
                    }
                );
            } else {
                alert('Geolocation is not supported by this browser.');
            }
        }

        // Update current location marker
        function updateCurrentLocationMarker(position) {
            if (currentLocationMarker) {
                currentLocationMarker.setMap(null);
            }

            currentLocationMarker = new google.maps.Marker({
                position: position,
                map: map,
                title: 'Your Current Location',
                icon: {
                    url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="12" r="8" fill="#3b82f6" stroke="#ffffff" stroke-width="2"/>
                            <circle cx="12" cy="12" r="3" fill="#ffffff"/>
                        </svg>
                    `),
                    scaledSize: new google.maps.Size(24, 24),
                    anchor: new google.maps.Point(12, 12)
                }
            });

            const infoWindow = new google.maps.InfoWindow({
                content: `
                    <div>
                        <h3>Your Current Location</h3>
                        <p>Lat: ${position.lat.toFixed(6)}, Lng: ${position.lng.toFixed(6)}</p>
                        <button onclick="saveCurrentLocation()" class="btn btn-primary btn-sm mt-2">Save This Location</button>
                    </div>
                `
            });

            currentLocationMarker.addListener('click', () => {
                infoWindow.open(map, currentLocationMarker);
            });
        }

        // Toggle live tracking
        function toggleLiveTracking() {
            const button = document.getElementById('toggleLiveTracking');
            
            if (!liveTrackingEnabled) {
                startLiveTracking();
                button.textContent = '⏹️ Stop Live Tracking';
                button.classList.remove('btn-success');
                button.classList.add('btn-danger');
            } else {
                stopLiveTracking();
                button.textContent = '🔴 Start Live Tracking';
                button.classList.remove('btn-danger');
                button.classList.add('btn-success');
            }
        }

        function startLiveTracking() {
            if (!navigator.geolocation) {
                alert('Geolocation is not supported by this browser.');
                return;
            }

            liveTrackingEnabled = true;
            
            liveTrackingInterval = setInterval(() => {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const pos = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude,
                        };

                        updateCurrentLocationMarker(pos);
                        updateLocationStatus(true, pos);
                        
                        // Send to server
                        updateLiveLocationOnServer(pos);
                    },
                    (error) => {
                        console.error('Error getting location:', error);
                        updateLocationStatus(false);
                    }
                );
            }, 5000); // Update every 5 seconds
        }

        function stopLiveTracking() {
            liveTrackingEnabled = false;
            if (liveTrackingInterval) {
                clearInterval(liveTrackingInterval);
            }
            updateLocationStatus(false);
        }

        // Update location status display
        function updateLocationStatus(isActive, position = null) {
            const statusInfo = document.getElementById('statusInfo');
            const locationStatus = document.getElementById('locationStatus');
            const statusText = document.getElementById('statusText');
            const coordinates = document.getElementById('coordinates');

            statusInfo.classList.remove('hidden');
            
            if (isActive && position) {
                locationStatus.classList.remove('status-offline');
                locationStatus.classList.add('status-online');
                statusText.textContent = liveTrackingEnabled ? 'Live tracking active' : 'Location found';
                coordinates.textContent = `${position.lat.toFixed(6)}, ${position.lng.toFixed(6)}`;
            } else {
                locationStatus.classList.remove('status-online');
                locationStatus.classList.add('status-offline');
                statusText.textContent = 'Location tracking disabled';
                coordinates.textContent = '';
            }
        }

        // API Functions
        async function updateLiveLocationOnServer(position) {
            try {
                const response = await fetch('/api/live-location', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        latitude: position.lat,
                        longitude: position.lng,
                        user_id: 'user_' + Math.random().toString(36).substr(2, 9) // Demo user ID
                    })
                });
                
                if (!response.ok) {
                    throw new Error('Failed to update live location');
                }
            } catch (error) {
                console.error('Error updating live location:', error);
            }
        }

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

                if (response.ok) {
                    alert('Location saved successfully!');
                    loadSavedLocations();
                } else {
                    throw new Error('Failed to save location');
                }
            } catch (error) {
                console.error('Error saving location:', error);
                alert('Error saving location. Please try again.');
            }
        }

        async function saveCurrentLocation() {
            if (!currentLocationMarker) {
                alert('Please get your current location first.');
                return;
            }

            const position = currentLocationMarker.getPosition();
            const name = prompt('Enter a name for this location:', 'My Location');
            
            if (name) {
                await saveLocation(name, position.lat(), position.lng(), 'Current Location');
            }
        }

        async function loadSavedLocations() {
            try {
                const response = await fetch('/api/locations');
                const locations = await response.json();
                
                displaySavedLocations(locations);
            } catch (error) {
                console.error('Error loading saved locations:', error);
            }
        }

        async function loadLiveLocations() {
            try {
                const response = await fetch('/api/live-locations');
                const liveLocations = await response.json();
                
                displayLiveLocations(liveLocations);
            } catch (error) {
                console.error('Error loading live locations:', error);
            }
        }

        function displaySavedLocations(locations) {
            const container = document.getElementById('savedLocations');
            container.innerHTML = '';

            locations.filter(loc => loc.type !== 'live_location').forEach(location => {
                const locationCard = document.createElement('div');
                locationCard.className = 'bg-gray-50 p-4 rounded-lg';
                locationCard.innerHTML = `
                    <h4 class="font-semibold">${location.name}</h4>
                    <p class="text-sm text-gray-600">${location.address || 'No address'}</p>
                    <p class="text-xs text-gray-500">${location.latitude}, ${location.longitude}</p>
                    <div class="mt-2 space-x-2">
                        <button onclick="goToLocation(${location.latitude}, ${location.longitude})" class="btn btn-primary btn-sm">View</button>
                        <button onclick="deleteLocation(${location.id})" class="btn btn-danger btn-sm">Delete</button>
                    </div>
                `;
                container.appendChild(locationCard);
            });
        }

        function displayLiveLocations(liveLocations) {
            // Clear existing user markers
            userMarkers.forEach(marker => marker.setMap(null));
            userMarkers.clear();

            liveLocations.forEach(location => {
                const marker = new google.maps.Marker({
                    position: { lat: parseFloat(location.latitude), lng: parseFloat(location.longitude) },
                    map: map,
                    title: `Live User: ${location.user_id}`,
                    icon: {
                        url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="10" cy="10" r="8" fill="#10b981" stroke="#ffffff" stroke-width="2"/>
                                <circle cx="10" cy="10" r="3" fill="#ffffff"/>
                            </svg>
                        `),
                        scaledSize: new google.maps.Size(20, 20),
                        anchor: new google.maps.Point(10, 10)
                    }
                });

                const infoWindow = new google.maps.InfoWindow({
                    content: `
                        <div>
                            <h3>Live User</h3>
                            <p>User ID: ${location.user_id}</p>
                            <p>Last updated: ${new Date(location.updated_at).toLocaleTimeString()}</p>
                        </div>
                    `
                });

                marker.addListener('click', () => {
                    infoWindow.open(map, marker);
                });

                userMarkers.set(location.user_id, marker);
            });
        }

        function goToLocation(lat, lng) {
            map.setCenter({ lat: parseFloat(lat), lng: parseFloat(lng) });
            map.setZoom(15);
        }

        async function deleteLocation(id) {
            if (!confirm('Are you sure you want to delete this location?')) return;

            try {
                const response = await fetch(`/api/locations/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.ok) {
                    loadSavedLocations();
                } else {
                    throw new Error('Failed to delete location');
                }
            } catch (error) {
                console.error('Error deleting location:', error);
                alert('Error deleting location. Please try again.');
            }
        }

        function clearMarkers() {
            markers.forEach(marker => marker.setMap(null));
            markers = [];
        }

        // Event listeners
        document.getElementById('getCurrentLocation').addEventListener('click', getCurrentLocation);
        document.getElementById('toggleLiveTracking').addEventListener('click', toggleLiveTracking);
        document.getElementById('saveCurrentLocation').addEventListener('click', saveCurrentLocation);
        document.getElementById('clearMarkers').addEventListener('click', clearMarkers);
    </script>

    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&callback=initMap&libraries=places&v=weekly"
        async
        defer>
    </script>
</body>

</html>