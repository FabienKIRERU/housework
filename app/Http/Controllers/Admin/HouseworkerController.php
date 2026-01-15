<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreHousewokerRequest;
use App\Http\Requests\Admin\UpdateHousewokerRequest;
use App\Repositories\Contracts\HouseworkerRepositoryInterface;
use Illuminate\Http\Request;

class HouseworkerController extends Controller
{
    private HouseworkerRepositoryInterface $houseworkerRepository;
    public function __construct(HouseworkerRepositoryInterface $houseworkerRepository)
    {
        $this->houseworkerRepository = $houseworkerRepository;
    }

    public function index(){
        return response()->json($this->houseworkerRepository->getAllHouseworkers());
    }

    public function store(StoreHousewokerRequest $request){

        $details = $request->validated();
        $houseworker = $this->houseworkerRepository->createHouseworker($details);
        return response()->json([
            'message' => 'Menager(ère) créé(e) avec succes',
            'houseworker' => $houseworker]
            , 201);
    }

    public function show($id){
        $houseworker = $this->houseworkerRepository->getHouseworkerById($id);
        if(!$houseworker){
            return response()->json(['message' => 'Menager(ère) non trouvé(e)'], 404);
        }
        return response()->json($houseworker);
    }

    public function update(UpdateHousewokerRequest $request, $id){
        $houseworker = $this->houseworkerRepository->getHouseworkerById($id);
        if(!$houseworker){
            return response()->json(['message' => 'Menager(ère) non trouvé(e)'], 404);
        }
        $data = $request->validated();
        $houseworker =$this->houseworkerRepository->updateHouseworker($id, $data);
        return response()->json(['message' => 'Menager(ère) mis(e) à jour avec succes']);
    }

    public function destroy($id){
        $houseworker = $this->houseworkerRepository->getHouseworkerById($id);
        if(!$houseworker){
            return response()->json(['message' => 'Menager(ère) non trouvé(e)'], 404);
        }
        $this->houseworkerRepository->deleteHouseworker($id);
        return response()->json(['message' => 'Menager(ère) supprimé(e) avec succes']);
    }
}
