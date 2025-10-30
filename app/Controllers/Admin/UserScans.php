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
            return $this->response->setJSON([
                'status' => 'success',
                'message' => $result[0]['message'] ?? 'Scan created successfully',
                'data' => $result[0]
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => $result[0]['message'] ?? 'Failed to create scan'
        ]);
    }
}
