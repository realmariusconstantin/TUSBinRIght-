<?php

namespace App\Models;

use CodeIgniter\Model;

class ItemTypeModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    // Get all item types using stored procedure
    public function getItemTypes()
    {
        $query = $this->db->query('CALL GetItemTypes()');
        $result = $query->getResultArray();
        $query->freeResult();
        return $result;
    }

    // Get a specific item type by ID using stored procedure
    public function getItemTypeById($item_type_id)
    {
        $query = $this->db->query('CALL GetItemTypeById(?)', [$item_type_id]);
        $result = $query->getResultArray();
        $query->freeResult();
        return $result;
    }
}