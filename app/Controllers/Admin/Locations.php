<?php

namespace App\Controllers\Admin;

use App\Models\LocationModel;
use CodeIgniter\RESTful\ResourceController;

class Locations extends ResourceController
{
    protected $model;
    protected $format = 'json';

    public function __construct()
    {
        $this->model = new LocationModel();
    }

    // GET /admin/locations
    public function getLocations()
    {
        $locations = $this->model->getLocations();

        return $this->response->setJSON([
            'status' => 'success',
            'locations' => $locations
        ]);
    }
}