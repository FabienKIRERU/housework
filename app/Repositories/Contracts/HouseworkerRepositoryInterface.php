<?php

namespace App\Repositories\Contracts;

interface HouseworkerRepositoryInterface
{
    public function getAllHouseworkers();
    public function getHouseworkerById($id);
    public function createHouseworker(array $details);
    public function updateHouseworker($id, array $newDetails);
    public function deleteHouseworker($id);
}