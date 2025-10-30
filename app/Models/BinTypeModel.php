<?php

namespace App\Models;

use CodeIgniter\Model;

class BinTypeModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    // Get all bin types using stored procedure
    public function getBinTypes()
    {
        $query = $this->db->query('CALL GetBinTypes()');
        $result = $query->getResultArray();
        $query->freeResult();
        return $result;
    }

    // Get a specific bin type by ID using stored procedure
    public function getBinTypeById($bin_type_id)
    {
        $query = $this->db->query('CALL GetBinTypeById(?)', [$bin_type_id]);
        $result = $query->getResultArray();
        $query->freeResult();
        return $result;
    }
}
