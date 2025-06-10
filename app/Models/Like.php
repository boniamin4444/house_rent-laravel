<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Like extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'house_rent_id',
    ];

    /**
     * Get the user that owns the like.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the house rent post that the like belongs to.
     */
    public function houseRent(): BelongsTo
    {
        // Using 'houserent_id' explicitly as the foreign key for clarity,
        // although Laravel would infer it correctly from the method name 'houseRent'
        return $this->belongsTo(HouseRent::class, 'house_rent_id');
    }
}