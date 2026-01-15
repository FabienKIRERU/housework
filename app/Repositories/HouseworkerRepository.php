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
}
