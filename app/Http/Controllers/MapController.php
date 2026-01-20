<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Location;

class MapController extends Controller
{
    /**
     * Display the map view
     */
    public function index()
    {
        return view('map');
    }

    /**
     * Get all locations
     */
    public function getLocations(): JsonResponse
    {
        $locations = Location::latest()->take(100)->get();
        return response()->json($locations);
    }

    /**
     * Store a new location
     */
    public function storeLocation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'address' => 'nullable|string|max:500',
            'type' => 'nullable|string|max:50',
        ]);

        $location = Location::create($validated);

        return response()->json([
            'success' => true,
            'location' => $location
        ], 201);
    }

    /**
     * Update user's live location
     */
    public function updateLiveLocation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'user_id' => 'nullable|string|max:100',
        ]);

        // For demo purposes, we'll use session ID as user identifier
        $userId = $validated['user_id'] ?? session()->getId();

        $location = Location::updateOrCreate(
            [
                'user_id' => $userId,
                'type' => 'live_location'
            ],
            [
                'name' => 'Live Location',
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'address' => 'Current Position',
                'updated_at' => now()
            ]
        );

        return response()->json([
            'success' => true,
            'location' => $location
        ]);
    }

    /**
     * Get live locations of all users
     */
    public function getLiveLocations(): JsonResponse
    {
        $liveLocations = Location::where('type', 'live_location')
            ->where('updated_at', '>=', now()->subMinutes(5)) // Only recent locations
            ->get();

        return response()->json($liveLocations);
    }

    /**
     * Delete a location
     */
    public function deleteLocation($id): JsonResponse
    {
        $location = Location::findOrFail($id);
        $location->delete();

        return response()->json([
            'success' => true,
            'message' => 'Location deleted successfully'
        ]);
    }
}