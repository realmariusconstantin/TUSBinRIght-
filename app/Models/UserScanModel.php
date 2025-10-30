<?php

namespace App\Models;

use CodeIgniter\Model;

class UserScanModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    // Create user scan
    public function createUserScan($user_id, $item_type_id)
    {
        $query = $this->db->query('CALL CreateUserScan(?, ?)', [$user_id, $item_type_id]);
        $result = $query->getResultArray();
        $query->freeResult();
        return $result;
    }

    // Get user scans with filters and pagination
    public function getUserScans($user_id = null, $start_date = null, $end_date = null, $limit = 10, $offset = 0)
    {
        $query = $this->db->query('CALL GetUserScans(?, ?, ?, ?, ?)', [
            $user_id,
            $start_date,
            $end_date,
            $limit,
            $offset
        ]);
        $result = $query->getResultArray();
        $query->freeResult();
        return $result;
    }

    // Delete multiple user scans by IDs
    public function deleteUserScans($scan_ids)
    {
        // Convert array to comma-separated string if necessary
        if (is_array($scan_ids)) {
            $scan_ids = implode(',', $scan_ids);
        }

        $query = $this->db->query('CALL DeleteUserScans(?)', [$scan_ids]);
        $result = $query->getResultArray();
        $query->freeResult();
        return $result;
    }

    // Get a count of the amount of scans using stored procedure
    public function getTotalScans()
    {
        $query = $this->db->query('CALL GetTotalScans()');
        $result = $query->getRowArray();
        $query->freeResult();
        return $result['total_scans'] ?? 0;
    }
}
