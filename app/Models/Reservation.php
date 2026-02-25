<?php

namespace App\Models;

use App\Models\User;
use App\Models\Client;
use App\Models\Service;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reservation extends Model
{
    use HasFactory;
    protected $fillable = [
        'client_id', 'code', 'houseworker_id', 
        'intervention_date', 'address', 'status', 'total_price'
    ];

    // Le client qui a commandé
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Relation Many-to-Many
    public function services()
    {
        return $this->belongsToMany(Service::class, 'reservation_service')
                    ->withPivot('price_at_booking') // On veut accéder au prix figé
                    ->withTimestamps();
    }

    // La ménagère assignée
    public function houseworker()
    {
        return $this->belongsTo(User::class, 'houseworker_id');
    }
}