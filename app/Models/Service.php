<?php

namespace App\Models;

use App\Models\ServiceCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name', 'description', 'price', 'is_active'
    ];

    public function reservations(){
        return $this->belongsToMany(Reservation::class, 'reservation_service')
                    ->withPivot('price_at_booking')
                    ->withTimestamps();
    }

}
