<?php
// HouseRent Model
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HouseRent extends Model
{
    use HasFactory;

    protected $fillable = [
        'district',
        'police_station',
        'road',
        'description',
        'price',
        'square_feet',
        'bedrooms',
        'gallery',
        'user_id'
    ];

    protected $casts = [
        'gallery' => 'array',
    ];
}
