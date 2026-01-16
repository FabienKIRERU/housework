<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\ServiceRepository;
use App\Http\Requests\Admin\StoreServiceRequest;
use App\Http\Requests\Admin\UpdateServiceRequest;

class ServiceController extends Controller
{
    //
    private ServiceRepository $serviceRepository;

    public function __construct(ServiceRepository $serviceRepository)
    {
        $this->serviceRepository = $serviceRepository;
    }

    public function index()
    {
        return response()->json($this->serviceRepository->getAllServices());
    }

    public function store(StoreServiceRequest $request)
    {
        $service = $this->serviceRepository->createService($request->validated());
            return response()->json([
                'message' => 'SService créé avec succès',
                'service' => $service,
        ], 201);
    }

    public function update(UpdateServiceRequest $request, $id)
    {
        $service = $this->serviceRepository->updateService($id, $request->validated());

        return response()->json([
            'message' => 'Service mis à jour avec succès',
            'service' => $service
        ]);
    }

    public function show($id)
    {
        return response()->json($this->serviceRepository->getServiceById($id));
    }

    public function destroy($id)
    {
        $this->serviceRepository->deleteService($id);
        return response()->json(['message' => 'Service supprimé avec succès']);
    }
}
