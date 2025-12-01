<?php

namespace App\Controllers\Admin;

use App\Models\UserScanModel;
use CodeIgniter\RESTful\ResourceController;

class UserScans extends ResourceController
{
    protected $model;
    protected $format = 'json';

    public function __construct()
    {
        $this->model = new UserScanModel();
    }

    // GET /admin/user-scans
    public function getScans()
    {
        $user_id = $this->request->getGet('user_id') ?: null;
        $start_date = $this->request->getGet('start_date') ?: null;
        $end_date = $this->request->getGet('end_date') ?: null;
        $limit = $this->request->getGet('limit') ?? 10;
        $offset = $this->request->getGet('offset') ?? 0;

        $scans = $this->model->getUserScans($user_id, $start_date, $end_date, $limit, $offset);

        return $this->response->setJSON([
            'status' => 'success',
            'scans' => $scans
        ]);
    }

    // DELETE /admin/user-scans
    public function deleteScans()
    {
        $input = $this->request->getJSON(true);
        $scan_ids = $input['scan_ids'] ?? [];

        if (empty($scan_ids)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'No scan IDs provided'
            ]);
        }

        $result = $this->model->deleteUserScans($scan_ids);

        return $this->response->setJSON([
            'status' => $result[0]['status'] ? 'success' : 'error',
            'message' => $result[0]['message']
        ]);
    }

    // POST /create-scan
    public function createScan()
    {
        $input = $this->request->getJSON(true);

        $user_id = $input['user_id'] ?? null;
        $item_type_id = $input['item_type_id'] ?? null;

        if (!$user_id || !$item_type_id) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Missing required fields: user_id and item_type_id'
            ]);
        }

        $result = $this->model->createUserScan($user_id, $item_type_id);

        if (!empty($result) && isset($result[0]['status']) && $result[0]['status']) {
            // Get the last inserted scan ID
            $db = \Config\Database::connect();
            $scanId = $db->insertID();

            // If insertID doesn't work with stored procedure, try to get the last scan for this user
            if (!$scanId) {
                $lastScan = $db->table('userscan')
                    ->where('user_id', $user_id)
                    ->orderBy('scanned_at', 'DESC')
                    ->limit(1)
                    ->get()
                    ->getRow();
                $scanId = $lastScan->id ?? null;
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => $result[0]['message'] ?? 'Scan created successfully',
                'data' => [
                    'scan_id' => $scanId,
                    'user_id' => $user_id,
                    'item_type_id' => $item_type_id
                ]
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => $result[0]['message'] ?? 'Failed to create scan'
        ]);
    }

    // POST /scan-accuracy
    // Submit accuracy feedback for a scan
    public function submitAccuracy()
    {
        $input = $this->request->getJSON(true);

        $scan_id = $input['scan_id'] ?? null;
        $is_accurate = isset($input['is_accurate']) ? (bool)$input['is_accurate'] : null;

        if (!$scan_id || $is_accurate === null) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Missing required fields: scan_id and is_accurate'
            ])->setStatusCode(400);
        }

        $db = \Config\Database::connect();

        try {
            // Update the scan with accuracy feedback
            $db->table('userscan')
                ->where('id', $scan_id)
                ->update(['is_accurate' => $is_accurate ? 1 : 0]);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Accuracy feedback recorded'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Failed to submit accuracy: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to record feedback'
            ])->setStatusCode(500);
        }
    }

    // GET /scan-accuracy-stats
    // Get overall accuracy percentage
    public function getAccuracyStats()
    {
        $db = \Config\Database::connect();

        try {
            // Get total rated scans and accurate count
            $stats = $db->table('userscan')
                ->select('COUNT(*) as total_rated, SUM(CASE WHEN is_accurate = 1 THEN 1 ELSE 0 END) as accurate_count')
                ->where('is_accurate IS NOT NULL')
                ->get()
                ->getRow();

            $totalRated = (int)($stats->total_rated ?? 0);
            $accurateCount = (int)($stats->accurate_count ?? 0);

            $accuracyPercentage = $totalRated > 0
                ? round(($accurateCount / $totalRated) * 100, 1)
                : 0;

            return $this->response->setJSON([
                'status' => 'success',
                'stats' => [
                    'total_rated' => $totalRated,
                    'accurate_count' => $accurateCount,
                    'accuracy_percentage' => $accuracyPercentage
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Failed to get accuracy stats: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to get accuracy stats'
            ])->setStatusCode(500);
        }
    }

    // GET /user-scans/userId
    public function getScansByUser($user_id)
    {
        if (!$user_id) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'User ID is required'
            ]);
        }

        $scans = $this->model->getUserScansByUserId($user_id);

        return $this->response->setJSON([
            'status' => 'success',
            'user_id' => $user_id,
            'total' => count($scans),
            'scans' => $scans
        ]);
    }
}
