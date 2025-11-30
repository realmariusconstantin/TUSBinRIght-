<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\UserScanModel;
use CodeIgniter\RESTful\ResourceController;

class StatsController extends ResourceController
{
    protected $userModel;
    protected $scanModel;
    protected $format = 'json';
    protected $db;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->scanModel = new UserScanModel();
        $this->db = \Config\Database::connect();
    }

    // GET /total-users
    public function totalUsers()
    {
        $totalUsers = $this->userModel->getTotalUsers();

        return $this->response->setJSON([
            'status' => 'success',
            'total_users' => $totalUsers
        ]);
    }

    // GET /total-scans
    public function totalScans()
    {
        $totalScans = $this->scanModel->getTotalScans();

        return $this->response->setJSON([
            'status' => 'success',
            'total_scans' => $totalScans
        ]);
    }

    // GET /community-stats
    // Returns community-wide recycling statistics for charts
    public function communityStats()
    {
        try {
            // Get total scans by material type
            $stats = $this->db->table('userscan')
                ->select('itemtype.description, COUNT(userscan.id) as count')
                ->join('itemtype', 'userscan.item_type_id = itemtype.id')
                ->groupBy('itemtype.description')
                ->get()
                ->getResultArray();

            // Get unique users per material
            $userStats = $this->db->table('userscan')
                ->select('itemtype.description, COUNT(DISTINCT userscan.user_id) as users')
                ->join('itemtype', 'userscan.item_type_id = itemtype.id')
                ->groupBy('itemtype.description')
                ->get()
                ->getResultArray();

            // Build response
            $communityData = [
                'plastic' => ['saved' => 0, 'users' => 0],
                'glass' => ['saved' => 0, 'users' => 0],
                'can' => ['saved' => 0, 'users' => 0],
                'paper' => ['saved' => 0, 'users' => 0],
                'total' => ['saved' => 0, 'users' => 0]
            ];

            // Carbon savings per item (kg CO2) - approximate values
            $carbonPerItem = [
                'plastic' => 0.08,  // ~80g CO2 per plastic bottle
                'glass' => 0.05,    // ~50g CO2 per glass item
                'can' => 0.06,      // ~60g CO2 per can
                'paper' => 0.03     // ~30g CO2 per paper item
            ];

            // Populate data
            foreach ($stats as $stat) {
                $type = strtolower($stat['description']);
                if (isset($communityData[$type])) {
                    $count = (int)$stat['count'];
                    $co2Saved = $count * ($carbonPerItem[$type] ?? 0.05);
                    $communityData[$type]['saved'] = round($co2Saved, 1);
                    $communityData[$type]['count'] = $count;
                    $communityData['total']['saved'] += $co2Saved;
                }
            }

            // Add user counts
            foreach ($userStats as $stat) {
                $type = strtolower($stat['description']);
                if (isset($communityData[$type])) {
                    $communityData[$type]['users'] = (int)$stat['users'];
                }
            }

            // Get total unique users
            $totalUsers = $this->db->table('userscan')
                ->selectCount('DISTINCT user_id', 'users')
                ->get()
                ->getRow();
            $communityData['total']['users'] = $totalUsers ? (int)$totalUsers->users : 0;

            $communityData['total']['saved'] = round($communityData['total']['saved'], 1);

            return $this->response->setJSON([
                'status' => 'success',
                'community' => $communityData
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Community stats failed: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to fetch community stats'
            ])->setStatusCode(500);
        }
    }
}
