<?php

namespace App\Repositories\Contracts;

interface ServiceRepositoryInterface
{
    public function getAllServices();
    public function getServiceById($id);
    public function createService(array $details);
    public function updateService($id, array $newDetails);
    public function deleteService($id);
}