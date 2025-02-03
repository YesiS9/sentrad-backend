<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Map;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class MapController extends Controller
{


    public function index(Request $request)
    {
        try {
            $senimanId = $request->input('seniman_id');
            $location = Map::where('seniman_id', $senimanId)->get();
            if ($location->isEmpty()) {
                return response()->json(['message' => 'Location not found'], 404);
            }

            return response()->json(['data' => $location], 200);
        } catch (Exception $e) {
            Log::error('Error fetching locations: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while fetching locations'], 500);
        }
    }
    public function indexAll()
    {
        try {
            $locations = Map::with('seniman')->get();
            if ($locations->isEmpty()) {
                return response()->json(['message' => 'Locations not found'], 404);
            }

            return response()->json(['data' => $locations], 200);
        } catch (Exception $e) {
            Log::error('Error fetching locations: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while fetching locations'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'seniman_id' => 'required|exists:seniman,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
            ]);


            $location = Map::create([
                'seniman_id' => $request->seniman_id,
                'name' => $request->name,
                'description' => $request->description,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);

            return response()->json(['data' => $location], 201);
        } catch (Exception $e) {
            Log::error('Error storing location: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while creating the location'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {

            $request->validate([
                'seniman_id' => 'sometimes|required|exists:seniman,id',
                'name' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'latitude' => 'sometimes|required|numeric|between:-90,90',
                'longitude' => 'sometimes|required|numeric|between:-180,180',
            ]);


            $location = Map::findOrFail($id);
            $location->update($request->only(['name', 'description', 'latitude', 'longitude']));

            return response()->json(['data' => $location]);
        } catch (Exception $e) {
            Log::error('Error updating location: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while updating the location'], 500);
        }
    }

    public function show($id)
    {
        try {
            $location = Map::findOrFail($id);

            return response()->json(['data' => $location]);
        } catch (Exception $e) {
            Log::error('Error fetching location: ' . $e->getMessage());
            return response()->json(['message' => 'Location not found'], 404);
        }
    }

    public function destroy($id)
    {
        try {
            // Find the location by ID
            $location = Map::find($id);

            // Check if the location exists
            if (!$location) {
                Log::error('Data Lokasi Tidak Ditemukan');
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data Lokasi Tidak Ditemukan',
                ], 404);
            }

            // Delete the location
            if ($location->delete()) {
                Log::info('Data Lokasi Berhasil Dihapus');
                return response()->json([
                    'data' => $location,
                    'status' => 'success',
                    'message' => 'Data Lokasi Berhasil Dihapus',
                ], 200);
            }
        } catch (Exception $e) {
            Log::error('Exception Error while deleting location: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while deleting the location',
            ], 500);
        }
    }
}
