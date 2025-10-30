<?php

namespace App\Controllers\Admin;

use CodeIgniter\RESTful\ResourceController;
use App\Models\DisposalRuleModel;

class DisposalRules extends ResourceController
{
    protected $model;
    protected $format = 'json';

    public function __construct()
    {
        $this->model = new DisposalRuleModel();
    }

    // GET /admin/disposal-rules
    public function getDisposalRules()
    {
        $rules = $this->model->getDisposalRules();

        return $this->response->setJSON([
            'status' => 'success',
            'rules' => $rules
        ]);
    }

    // POST /admin/disposal-rules/create
    public function createDisposalRule()
    {
        $data = $this->request->getJSON(true);

        if (empty($data['item_type_id']) || empty($data['location_id']) || empty($data['bin_type_id'])) {
            return $this->fail('Missing required fields', 400);
        }

        $res = $this->model->createDisposalRule($data);
        return $this->respond($res);
    }

    // POST /admin/disposal-rules/update
    public function updateDisposalRule()
    {
        $data = $this->request->getJSON(true);

        if (empty($data['id'])) {
            return $this->fail('Missing rule ID', 400);
        }

        $res = $this->model->updateDisposalRule($data);
        return $this->respond($res);
    }

    // POST /admin/disposal-rules/delete
    public function deleteDisposalRule()
    {
        $data = $this->request->getJSON(true);

        if (empty($data['id'])) {
            return $this->fail('Missing rule ID', 400);
        }

        $res = $this->model->deleteDisposalRule($data['id']);
        return $this->respond($res);
    }

    // GET /disposal-rules/filter?id_item_type=1&id_location=2
    public function getDisposalRulesByItemAndLocationId()
    {
        $itemTypeId = $this->request->getGet('item_type_id');
        $locationId = $this->request->getGet('location_id');

        if (empty($itemTypeId) || empty($locationId)) {
            return $this->fail('Missing required parameters: item_type_id and location_id', 400);
        }

        $rules = $this->model->getDisposalRulesByItemAndLocationId($itemTypeId, $locationId);

        return $this->response->setJSON([
            'status' => 'success',
            'rules' => $rules
        ]);
    }
}
