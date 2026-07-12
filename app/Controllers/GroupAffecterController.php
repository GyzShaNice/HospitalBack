<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\API\ResponseTrait;
use App\Models\GroupAffecterModel;
use App\Models\Personnel;
use App\Models\GroupModel;
use App\Models\ShiftModel;

class GroupAffecterController extends BaseController
{
    use ResponseTrait;

    protected $group_M;
    protected $group_model;
    protected $perso_model;
    protected $shift_model;

    function __construct(){
       $this->group_model = new GroupModel();
        $this->perso_model = new Personnel();
        $this->group_M = new GroupAffecterModel();
        $this->shift_model = new ShiftModel();
    }
    public function indexGroupA()
    {
        $groupsA = $this->group_M
                        ->select('groupaffecter.id_groupAff,groupaffecter.month,groupaffecter.year,
                                    groupaffecter.week_number,personel.id_personel,group.id_group,
                                    group.name_group,group.type_group,
                                    shift.id_shift,shift.shift_name,shift.start_time,shift.end_time')
                        ->join('personel','groupaffecter.id_personel =  personel.id_personel')
                        ->join('group','groupaffecter.id_group =  group.id_group')
                        ->join('shift','groupaffecter.id_shift = shift.id_shift','left')
                        ->findAll();
                
            $response = [
                    "message"=> count($groupsA)>0? "groupAffecter found":"groupAffecter not found",
                    "success"=> count($groupsA),
                    "data"=> $groupsA
                ];
               
                return $this->respond($response);
            
    }

    public function showGroupA($idgroupA){

        $groupA = $this->group_M
                        ->select('groupaffecter.id_groupAff,groupaffecter.month,groupaffecter.year,
                                    groupaffecter.week_number,personel.id_personel,group.id_group,
                                    group.name_group,group.type_group,
                                    shift.id_shift,shift.shift_name,shift.start_time,shift.end_time')
                        ->join('personel','groupaffecter.id_personel =  personel.id_personel')
                        ->join('group','groupaffecter.id_group =  group.id_group')
                        ->join('shift','groupaffecter.id_shift = shift.id_shift','left')
                        ->find($idgroupA);

            $response = [
                    "message"=> $groupA? "groupAffecter found":"groupAffecter not found",
                    "success"=> (bool) $groupA,
                    "data"=> $groupA
                ];

                return $this->respond($response);
    }

    public function createGroupA(){
        helper(['form']);

        $rules = [
                'id_group'=>'required|integer',
            'id_personel'=>'required|integer',
            'month'=>'required|max_length[20]|min_length[1]',
            'year'=>'required|max_length[20]|min_length[1]',
            'id_shift'=>'permit_empty|integer',
            'week_number'=>'permit_empty|integer',

        ];

        // get the values entered by the user
        $idGroup = $this->request->getVar('id_group');
        $idPersonel=$this->request->getVar('id_personel');
        $Month=$this->request->getVar('month');
        $Year=$this->request->getVar('year');
        $idShift=$this->request->getVar('id_shift');
        $weekNumber=$this->request->getVar('week_number');

        $db = \Config\Database::connect();
        $db->transStart();

        $group = $this->group_model->where('id_group',$idGroup)->first();
        $personel = $this->perso_model->where('id_personel',$idPersonel)->first();

            if(!$group){
                return $this->fail([
                    'message'=>'group not found',
                    'success'=>false
                ]);

            }

            if(!$personel){
                return $this->fail([
                    'message'=>'personel not found',
                    'success'=>false
                ]);

            }

            if(empty($Year)){
                return $this->fail([
                    'message'=>'year number not found',
                    'success'=>false
                ]);
            }

            if(empty($Month)){
                return $this->fail([
                    'message'=>'month not found',
                    'success'=>false
                ]);
            }

            if(!empty($idShift)){
                $shift = $this->shift_model->find($idShift);

                if(!$shift){
                    return $this->fail([
                        'message'=>'shift not found',
                        'success'=>false
                    ]);
                }

                if((int) $shift['id_group'] !== (int) $idGroup){
                    return $this->fail([
                        'message'=>'this shift does not belong to the selected group',
                        'success'=>false
                    ]);
                }
            }

            // this down creates a medical act
            $groupA = $this->group_M->insert([
                'id_group'=>$idGroup,
                'id_personel'=>$idPersonel,
                'id_shift'=>$idShift ?: null,
                'week_number'=>$weekNumber ?: null,
                'year'=>$Year,
                'month'=>$Month
            ]);

            $db->transComplete();

            if($db->transStatus()==false){
                return $this->fail([
                    'message'=>'group affecter creation failed',
                    'success'=>false
                ]);
            }

            return $this->respondCreated([
                'message'=>'group created',
                'success'=>true,
                'data'=>[
                    'id_groupaffecter'=>$groupA,
                    'id_personel'=>$idPersonel,
                    'id_group'=>$idGroup,
                    'id_shift'=>$idShift ?: null,
                    'week_number'=>$weekNumber ?: null,
                    'year'=>$Year,
                    'month'=>$Month
                ]
            ]);

    }
}
