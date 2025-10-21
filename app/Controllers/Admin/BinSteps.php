<?php

namespace App\Controllers\Admin;

use CodeIgniter\RESTful\ResourceController;

class BinSteps extends ResourceController
{
    protected $format = 'json';

    // GET /admin/bin-steps
    public function getSteps()
    {
        $db = \Config\Database::connect();
        $query = $db->query('CALL GetBinSteps()');
        $steps = $query->getResultArray();

        return $this->response->setJSON([
            'status' => 'success',
            'steps' => $steps
        ]);
    }

    // POST /admin/bin-steps/create
    public function createStep()
    {
        $data = $this->request->getJSON(true);
        $db = \Config\Database::connect();

        if (empty($data['description'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Missing description'
            ])->setStatusCode(400);
        }

        $res = $db->query('CALL CreateBinStep(?, ?)', [
            $data['description'],
            $data['bin_type_id'] ?? 1
        ])->getRowArray();

        return $this->response->setJSON($res);
    }

    // POST /admin/bin-steps/update
    public function updateStep()
    {
        $data = $this->request->getJSON(true);
        $db = \Config\Database::connect();

        if (empty($data['id'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Missing bin step ID'
            ])->setStatusCode(400);
        }

        $step = $db->table('binstep')->where('id', $data['id'])->get()->getRowArray();
        if (!$step) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Bin step not found'
            ])->setStatusCode(404);
        }

        $res = $db->query('CALL UpdateBinStep(?, ?, ?)', [
            $data['id'],
            $data['description'] ?? $step['description'],
            $data['bin_type_id'] ?? $step['bin_type_id']
        ])->getRowArray();

        return $this->response->setJSON($res);
    }

    // POST /admin/bin-steps/delete
    public function deleteStep()
    {
        $data = $this->request->getJSON(true);
        $db = \Config\Database::connect();

        if (empty($data['id'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Missing bin step ID'
            ])->setStatusCode(400);
        }

        $res = $db->query('CALL DeleteBinStep(?)', [$data['id']])->getRowArray();

        return $this->response->setJSON($res);
    }
}