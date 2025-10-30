<?php

namespace App\Controllers\Admin;

use App\Models\ItemTypeModel;
use CodeIgniter\RESTful\ResourceController;

class ItemTypes extends ResourceController
{
    protected $model;
    protected $format = 'json';

    public function __construct()
    {
        $this->model = new ItemTypeModel();
    }

    // GET /admin/item-types
    public function getTypes()
    {
        $types = $this->model->getItemTypes();

        return $this->response->setJSON([
            'status' => 'success',
            'item_types' => $types
        ]);
    }
}
