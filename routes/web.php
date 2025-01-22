<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HouseRentController;
use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Route;

// Default welcome page route
Route::get('/', function () {
    return view('welcome');
})->name('welcome');


// GET request for dashboard
Route::get('/dashboard', function () {
    return view('welcome'); // Use the appropriate dashboard view here
})->middleware(['auth', 'verified'])->name('dashboard');

// Group routes with auth middleware
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('house_rents', HouseRentController::class);
    Route::get('/', [HouseRentController::class, 'index']);
    Route::post('/', function () {
        return view('welcome');
    })->name('welcome');
});
Route::get('/', [HouseRentController::class, 'index']);


Route::middleware('auth')->group(function() {
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::post('/messages/send', [MessageController::class, 'sendMessage'])->name('messages.send');;
});

// Include Laravel authentication routes
require __DIR__.'/auth.php';
