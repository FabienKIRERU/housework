<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\HouseworkerRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class HouseworkerRepository implements HouseworkerRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function getAllHouseworkers(){
        // Implementation here
        return User::where('role', 'houseworker')
                        ->orderBy('created_at', 'DESC')
                        ->get();
    }
    public function getHouseworkerById($id){
        // Implementation here
        return User::where('role', 'houseworker')->find($id);
    }
    public function createHouseworker(array $details){
        // Implementation here
        $details['role'] = 'houseworker';
        return User::create($details);
    }
    public function updateHouseworker($id, array $newDetails){
        $houseworker = $this->getHouseworkerById($id);
        
        if(isset($newDetails['password']) && !empty($newDetails['password'])){
            // Hash the password before updating
            $newDetails['password'] = Hash::make($newDetails['password']);
        }else{
            // Remove password from details to avoid overwriting with null
            unset($newDetails['password']);
        }
        $houseworker->update($newDetails);
    }
    
    public function deleteHouseworker($id){
        // Implementation here
        $houseworker = $this->getHouseworkerById($id);
        if($houseworker){
            $houseworker->delete();
        }
    }

    /**
     * Récupérer les ménagères avec leur planning actif
     */
    public function getHouseworkersWithPlanning()
    {
        return User::where('role', 'houseworker')
            ->with(['tasks' => function ($query) {
                // On ne veut voir que les tâches "assignées" (pas celles terminées ou annulées)
                // Et on veut trier par date d'intervention
                $query->wherePivot('status', 'assigned')
                      ->orderBy('intervention_date', 'asc');
            }])
            ->get()
            ->map(function ($user) {
                // Petite transformation pour faciliter la vie du Frontend
                // On va chercher le nom du service manuellement car il est stocké sous forme d'ID dans le pivot
                $user->tasks->transform(function ($task) {
                    $service = \App\Models\Service::find($task->pivot->service_id);
                    $task->service_name = $service ? $service->name : 'Service inconnu';
                    return $task;
                });
                
                return $user;
            });
    }
}
