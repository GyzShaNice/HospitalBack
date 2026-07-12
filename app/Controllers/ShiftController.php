<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\API\ResponseTrait;
use App\Models\ShiftModel;
use App\Models\GroupModel;

class ShiftController extends BaseController
{
    use ResponseTrait;

    protected $shift_model;
    protected $group_model;

    function __construct(){
        $this->shift_model = new ShiftModel();
        $this->group_model = new GroupModel();
    }

    public function indexShift()
    {
        $shifts = $this->shift_model->findAll();

        $response = [
            "message" => count($shifts) > 0 ? "shifts found" : "shifts not found",
            "success" => count($shifts) > 0,
            "data" => $shifts
        ];

        return $this->respond($response);
    }

    public function showShiftsByGroup($idGroup = null){
        $shifts = $this->shift_model
            ->where('id_group', $idGroup)
            ->findAll();

        $response = [
            "message" => count($shifts) > 0 ? "shifts found" : "shifts not found",
            "success" => count($shifts) > 0,
            "data" => $shifts
        ];

        return $this->respond($response);
    }

    public function createShift(){
        helper(['form']);

        $rules = [
            'id_group'   => 'required|integer',
            'shift_name' => 'required|max_length[50]|min_length[1]',
            'start_time' => 'required|valid_date[H:i]',
            'end_time'   => 'required|valid_date[H:i]',
        ];

        if(!$this->validate($rules)){
            return $this->fail([
                'errors'  => $this->validator->getErrors(),
                'message' => 'invalid information',
                'success' => false
            ], ResponseInterface::HTTP_BAD_REQUEST);
        }

        $idGroup = $this->request->getVar('id_group');

        $group = $this->group_model->find($idGroup);

        if(!$group){
            return $this->fail([
                'message' => 'group not found',
                'success' => false
            ]);
        }

        $dataShift = [
            'id_group'   => $idGroup,
            'shift_name' => $this->request->getVar('shift_name'),
            'start_time' => $this->request->getVar('start_time'),
            'end_time'   => $this->request->getVar('end_time'),
        ];

        $shiftID = $this->shift_model->insert($dataShift);

        $response = [
            "message" => $shiftID ? "shift created" : "shift not created",
            "success" => (bool) $shiftID,
            "data" => array_merge(['id_shift' => $shiftID], $dataShift)
        ];

        if(!$shiftID){
            return $this->response->setJSON($this->shift_model->errors());
        }

        return $this->respondCreated($response);
    }

    public function updateShift($idShift = null){
        helper(['form']);

        $rules = [
            'shift_name' => 'required|max_length[50]|min_length[1]',
            'start_time' => 'required|valid_date[H:i]',
            'end_time'   => 'required|valid_date[H:i]',
        ];

        if(!$this->validate($rules)){
            return $this->fail([
                'errors'  => $this->validator->getErrors(),
                'message' => 'invalid information',
                'success' => false
            ], ResponseInterface::HTTP_BAD_REQUEST);
        }

        $dataShift = [
            'shift_name' => $this->request->getVar('shift_name'),
            'start_time' => $this->request->getVar('start_time'),
            'end_time'   => $this->request->getVar('end_time'),
        ];

        $shift = $this->shift_model->update($idShift, $dataShift);

        $response = [
            "message" => $shift ? "shift updated" : "shift not updated",
            "success" => (bool) $shift,
            "data" => array_merge(['id_shift' => $idShift], $dataShift)
        ];

        return $this->respond($response);
    }

    public function deleteShift($idShift = null){
        $shift_exist = $this->shift_model->find($idShift);

        if(!$shift_exist){
            return $this->failNotFound('deletion impossible');
        }

        $delete = $this->shift_model->delete($idShift);

        $response = [
            "message" => $delete ? "deletion ok" : "deletion impossible",
            "success" => (bool) $delete,
            "data" => ['id_shift' => $idShift]
        ];

        return $this->respond($response);
    }
}
