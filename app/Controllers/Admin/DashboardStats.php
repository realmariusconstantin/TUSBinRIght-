<?php

namespace App\Controllers\Admin;

use CodeIgniter\RESTful\ResourceController;

class DashboardStats extends ResourceController
{
    protected $format = 'json';

    // GET /admin/dashboard/stats
    public function getStats()
    {
        $db = \Config\Database::connect();

        // ✅ 1. Total items scanned
        // Assuming you have a `scans` or `scanned_items` table
        // If the table name is different, just change it here
        $scannedQuery = $db->query('SELECT COUNT(*) AS total_scanned FROM scanned_items');
        $totalScanned = $scannedQuery->getRow()->total_scanned ?? 0;

        // ✅ 2. Active users
        // Assuming you have a `users` table with `is_active` field
        // or else it will just count all users
        $usersQuery = $db->query('SELECT COUNT(*) AS active_users FROM users WHERE is_active = 1');
        $activeUsers = $usersQuery->getRow()->active_users ?? 0;

        // ✅ 3. Accuracy rate
        // Assuming you have a field like `is_correct` in your scans table
        // If not, we’ll just hardcode or calculate differently
        $accuracyQuery = $db->query('SELECT 
                                        (CASE WHEN COUNT(*) = 0 THEN 0 
                                              ELSE ROUND(SUM(CASE WHEN is_correct = 1 THEN 1 ELSE 0 END) / COUNT(*) * 100, 2) 
                                         END) AS accuracy_rate 
                                     FROM scanned_items');
        $accuracyRate = $accuracyQuery->getRow()->accuracy_rate ?? 0;

        return $this->response->setJSON([
            'status' => 'success',
            'stats' => [
                'items_scanned' => (int) $totalScanned,
                'active_users' => (int) $activeUsers,
                'accuracy_rate' => (float) $accuracyRate
            ]
        ]);
    }
}
