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
        'client_id', 'code', 'service_id', 'houseworker_id', 
        'intervention_date', 'address', 'status'
    ];

    // Le client qui a commandé
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Le service commandé
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    // La ménagère assignée
    public function houseworker()
    {
        return $this->belongsTo(User::class, 'houseworker_id');
    }
}