<?php

namespace App\Models;

use App\Models\Reservation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name', 'firstname', 'email', 'phone', 'address'
    ];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
