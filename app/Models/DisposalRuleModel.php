<?php

namespace App\Models;

use CodeIgniter\Model;

class DisposalRuleModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    // Create a new disposal rule using stored procedure
    public function createDisposalRule($data)
    {
        $query = $this->db->query(
            'CALL CreateDisposalRule(?, ?, ?, ?)',
            [
                $data['item_type_id'],
                $data['location_id'],
                $data['bin_type_id'],
                $data['description']
            ]
        );
        return $query->getRowArray();
    }

    // Get all disposal rules using stored procedure
    public function getDisposalRules()
    {
        $query = $this->db->query('CALL GetDisposalRules()');
        return $query->getResultArray();
    }

    // Get a specific disposal rule by ID using stored procedure
    public function getDisposalRuleById($disposal_rule_id)
    {
        $query = $this->db->query('CALL GetDisposalRuleById(?)', [$disposal_rule_id]);
        return $query->getRowArray();
    }

    // Update a disposal rule using stored procedure
    public function updateDisposalRule($data)
    {
        $query = $this->db->query(
            'CALL UpdateDisposalRule(?, ?, ?, ?, ?)',
            [
                $data['id'],
                $data['item_type_id'],
                $data['location_id'],
                $data['bin_type_id'],
                $data['description']
            ]
        );
        return $query->getRowArray();
    }

    // Delete a disposal rule using stored procedure
    public function deleteDisposalRule($disposal_rule_id)
    {
        $query = $this->db->query('CALL DeleteDisposalRule(?)', [$disposal_rule_id]);
        return $query->getRowArray();
    }
}