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
}