<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Like; // Make sure this is correct
use App\Models\HouseRent; // Ensure this model exists and is used if necessary for relationships
use App\Models\User; // Ensure this model exists and is used if necessary for relationships
use Illuminate\Support\Facades\Auth; // Ensure Auth facade is imported

class LikeController extends Controller
{
    /**
     * Toggle a like for a house rent post.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggle(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'house_rent_id' => 'required|exists:house_rents,id', // Ensures the ID is present and exists in houserents table
        ]);

        $userId = Auth::id(); // Get the ID of the currently authenticated user

        // If user is not logged in, return 401 Unauthorized
        if (!$userId) {
            return response()->json(['message' => 'Unauthorized. Please log in.'], 401);
        }

        $houseRentId = $request->house_rent_id;

        // Check if a like already exists for this user and house rent
        $like = Like::where('user_id', $userId)
                    ->where('house_rent_id', $houseRentId)
                    ->first();

        if ($like) {
            // If a like exists, delete it (unlike)
            try {
                $like->delete();
                return response()->json(['message' => 'Unliked']);
            } catch (\Exception $e) {
                // Log the error for debugging purposes
                \Log::error('Error unliking post: ' . $e->getMessage(), ['user_id' => $userId, 'house_rent_id' => $houseRentId]);
                return response()->json(['message' => 'Failed to unlike post. Please try again.'], 500);
            }
        } else {
            // If no like exists, create a new one (like)
            try {
                Like::create([
                    'user_id' => $userId,
                    'house_rent_id' => $houseRentId,
                ]);
                return response()->json(['message' => 'Liked']);
            } catch (\Exception $e) {
                // Log the error for debugging purposes
                \Log::error('Error liking post: ' . $e->getMessage(), ['user_id' => $userId, 'house_rent_id' => $houseRentId]);
                return response()->json(['message' => 'Failed to like post. Please try again.'], 500);
            }
        }
    }
}