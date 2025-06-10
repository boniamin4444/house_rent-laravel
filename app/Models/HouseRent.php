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

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function likes()
    {
        return $this->hasMany(Like::class);
    }
    // public function comments()
    // {
    //     return $this->hasMany(Comment::class);
    // }
}
