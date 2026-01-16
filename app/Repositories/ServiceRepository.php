<?php

namespace App\Repositories;

use App\Repositories\Contracts\ServiceRepositoryInterface;
use App\Models\Service;

class ServiceRepository implements ServiceRepositoryInterface
{
    public function getAllServices()
    {
        return Service::orderBy('created_at', 'desc')->get();
    }

    public function getServiceById($id)
    {
        return Service::findOrFail($id);
    }

    public function createService(array $details)
    {
        return Service::create($details);
    }

    public function updateService($id, array $newDetails)
    {
        $service = Service::findOrFail($id);
        $service->update($newDetails);
        return $service;
    }

    public function deleteService($id)
    {
        $service = Service::findOrFail($id);
        return $service->delete();
    }
}