<?php

namespace App\Http\Controllers;

use App\Models\HouseRent;
use Illuminate\Http\Request;

class HouseRentController extends Controller
{
    public function index()
    {
        $houseRents = HouseRent::all();
        return view('welcome', compact('houseRents'));
    }

   
    public function store(Request $request)
{
    // Validation
    $validated = $request->validate([
        'district' => 'required|string|max:255',
        'police_station' => 'required|string|max:255',
        'road' => 'required|string|max:255',
        'description' => 'required|string',
        'price' => 'required|numeric',
        'square_feet' => 'required|integer',
        'bedrooms' => 'required|integer',
        'gallery' => 'required|array',
        'gallery.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
    ]);

    // Handle the image upload for gallery
    $galleryPaths = [];
    if ($request->hasFile('gallery')) {
        foreach ($request->file('gallery') as $file) {
            $galleryPaths[] = $file->store('uploads/house_gallery', 'public');
        }
    }

    // Add the gallery paths to validated data
    $validated['gallery'] = $galleryPaths;

    // Add the authenticated user's ID to the data
    $validated['user_id'] = auth()->id(); // Get the currently authenticated user's ID

    // Store the data in the database
    HouseRent::create($validated);

    // Redirect back with a success message
    return redirect()->route('welcome')->with('success', 'House rent added successfully.');
}

}
