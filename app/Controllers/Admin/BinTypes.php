<?php

namespace App\Controllers\Admin;

use CodeIgniter\RESTful\ResourceController;

class BinTypes extends ResourceController
{
    protected $format = 'json';

    // GET /admin/bin-types
    public function getTypes()
    {
        $db = \Config\Database::connect();
        $query = $db->query('CALL GetBinTypes()');
        $types = $query->getResultArray();

        return $this->response->setJSON([
            'status' => 'success',
            'bin_types' => $types
        ]);
    }
}