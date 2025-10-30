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

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->scanModel = new UserScanModel();
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
}
