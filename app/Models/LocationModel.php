<?php

namespace App\Models;

use CodeIgniter\Model;

class LocationModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    // Get all locations using stored procedure
    public function getLocations()
    {
        $query = $this->db->query('CALL GetLocations()');
        return $query->getResultArray();
    }

    // Get a specific location by ID using stored procedure
    public function getLocationById($location_id)
    {
        $query = $this->db->query('CALL GetLocationById(?)', [$location_id]);
        return $query->getRowArray();
    }
}
