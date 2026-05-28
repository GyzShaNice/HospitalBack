<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\API\ResponseTrait;
use App\Models\GroupModel;
use App\Models\Personnel;

class GroupController extends BaseController
{
    use ResponseTrait;

    protected $group_model;
    protected $perso_model;

    function __construct(){
        $this->group_model = new GroupModel();
        $this->perso_model = new Personnel();
    }
    public function indexGroup()
    {
        $groups = $this->group_model->findAll();

        $response = [
            "message"=>count($groups)>0 ? "groups found":"groups not found",
            "success"=>count($groups)>0,
            "data" => $groups
        ];

        return $this->respond($response);
    }

    public function showGroup($idGroup = null){
        $Specificgroups = $this->group_model->findAll();

        $response = [
            "message"=>$Specificgroups? "groups found":"groups not found",
            "success"=>(bool)$Specificgroups,
            "data" => $Specificgroups
        ];

        return $this->respond($response);
    }

    public function createGroup(){
        helper(['form']);

        $rules = [
            'name_group'=> 'required|max_length[255]|min_length[1]',
            'type_group'=> 'required|max_length[255]|min_length[3]',
            'working_days'=> 'required|max_length[255]|min_length[3]',
            'start_time'=> 'required|valid_date[H:i]',
            'end_time' =>   'required|valid_date[H:i]',
            // hour and time
        ];

        if(!$this->validate($rules)){
            $response = [
                'errors'=>$this->validator->getErrors(),
                'message'=>'invalid information',
                'success'=>false
            ];

            return $this->fail($response);
        }

        $data_Group = [
            'name_group'=>$this->request->getVar('name_group'),
            'type_group'=>$this->request->getVar('type_group'),
            'working_days'=>$this->request->getVar('working_days'),
            'start_time'=>$this->request->getVar('start_time'),
            'end_time'=>$this->request->getVar('end_time')
        ];

        $groupID = $this->group_model->insert($data_Group);
            $response = [
                "message"=>$groupID? "group created":"group not created",
                "success"=>(bool)$groupID,
                "data"=>$groupID
            ];

            if(!$groupID){
                return $this->response->setJSON($this->group_model->errors());
            }

            return $this->respondCreated($response);
    }

    public function updateGroup($idGroup = null){
        helper(['form']);

        $rules = [
            'name_group'=>'required|max_length[254]|min_length[1]',
            'type_group'=>'required|max_length[254]|min_length[1]',
            'working_days'=>'required|max_length[254]|min_length[1]',
            'start_time'=>'required|valid_date[H:i]',
            'end_time'=>'required|valid_date[H:i]'
        ];

        if(!$this->validate($rules)){
            $response = [
                'errors'=>$this->validator->getErrors(),
                'message'=>'invalid information',
                'success'=>false
            ];

            return $this->fail($response);
        }

        $dataGroup = [
            'name_group'=>$this->request->getVar('name_group'),
            'type_group'=>$this->request->getVar('type_group'),
            'working_days'=>$this->request->getVar('working_days'),
            'start_time'=>$this->request->getVar('start_time'),
            'end_time'=>$this->request->getVar('end_time'),
        ];

        $group = $this->group_model->update($dataGroup);

        $response = [
            "message"=>$group? "group updated":"group not updated",
            "success"=>(bool)$group,
            "data"=>array_merge(['id_group'=>$idGroup],$dataGroup)
        ];

        return $this->respond($response);
    }

    public function deleteGroup($idGroup = null){
        $group_exist = $this->group_model->find($idGroup);

        if(!$group_exist){
            return $this->failNotFound('deletion impossible');
        }

        $delete = $this->group_model->delete($idGroup);

        $response = [
            "message"=>$delete ? "deletion ok":"deletion impossible",
            "success"=>(bool)$delete,
            "data"=>['id_group'=>$idGroup]
        ];

        return $this->respond($response);
    }
}
