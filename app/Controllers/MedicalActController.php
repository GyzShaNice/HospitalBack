<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\API\ResponseTrait;
use App\Models\MedicalActModel;
use App\Models\PatientModel;
use App\Models\Personnel;
use App\Models\UserModel;


class MedicalActController extends BaseController
{
    use ResponseTrait;
    protected $mediAct_model;
    protected $patient_model;
    protected $perso_model;
    protected $user_model;


    function __construct(){
        $this->mediAct_model = new MedicalActModel();
        $this->patient_model = new PatientModel();
        $this->perso_model = new Personnel();
        $this->user_model = new UserModel();

    }
    public function indexMedicalAct()
    {
        $mediActs =$this->mediAct_model
                        ->select('MedicalAct.id_medicalAct,MedicalAct.type_act,MedicalAct.date_Act,

                                    patient.id_patient,
                                    patientUser.name_user AS patient_name,
                                    patientUser.surname_user AS patient_surname,

                                    personel.id_personel,
                                    personelUser.name_user AS personel_name,
                                    personelUser.surname_user AS personel_surname
                                    ')
                        ->join('patient','MedicalAct.id_patient = patient.id_patient')

                        ->join('users AS patientUser','patient.id_user = patientUser.id_user')

                        
                        ->join('personel','MedicalAct.id_personel = personel.id_personel')

                        ->join('users AS personelUser','personel.id_user = personelUser.id_user')

                        ->findAll();
                $response = [
                    "message"=> count($mediActs)>0? "medical act found":"medical act not found",
                    "success"=> count($mediActs),
                    "data"=> $mediActs
                ];
               
                return $this->respond($response);
    }

    public function showMediActs($mediActs = null){
        $SpecificMedicalAct = $this->showMediActs
                                    ->select('MedicalAct.id_medicalAct,MedicalAct.type_act,MedicalAct.date_Act,patient.id_patient,patient.emergency_number,
                                        personel.id_personel,personel.staff_code')
                                    ->join('patient','MedicalAct.id_patient = patient.id_patient')
                                     ->join('personel','MedicalAct.id_personel = personel.id_personel')
                                     ->find();
                     $response = [
                        "message" => $SpecificMedicalAct ? "agent found" : "agent not found",
                        "success" => (bool)$SpecificMedicalAct,
                        "data" => $SpecificMedicalAct
                       ];

                   return $this->respond($response);    

    }

    public function createMediActs(){
        helper(['form']);

        $rules=[
            'id_patient'=>'required|integer',
            'id_personel'=>'required|integer',
            'type_act'=>'required|max_length[254]|min_length[9]',
            'date_act'=>'required|valid_date',
           
        ];

        // get the values entered by the user
        $idPatient = $this->request->getVar('id_patient');
        $idPersonel=$this->request->getVar('id_personel');
        $typeAct=$this->request->getVar('type_act');
        $dateAct=$this->request->getVar('date_act');

        $db = \Config\Database::connect();
        $db->transStart();

        $patient = $this->patient_model->where('id_patient',$idPatient)->first();
        $personel = $this->perso_model->where('id_personel',$idPersonel)->first();

            if(!$patient){
                return $this->fail([
                    'message'=>'patient not found',
                    'success'=>false
                ]);

            }

            if(!$personel){
                return $this->fail([
                    'message'=>'personel not found',
                    'success'=>false
                ]);

            }

            if(empty($typeAct)){
                return $this->fail([
                    'message'=>'typeAct and Date number not found',
                    'success'=>false
                ]);
            }

            if(empty($dateAct)){
                return $this->fail([
                    'message'=>'Date number not found',
                    'success'=>false
                ]);
            }

            // this down creates a medical act
            $medicID = $this->mediAct_model->insert([
                'id_patient'=>$idPatient,
                'id_personel'=>$idPersonel,
                'type_act'=>$typeAct,
                'date_act'=>$dateAct
            ]);

            $db->transComplete();

            if($db->transStatus()==false){
                return $this->fail([
                    'message'=>'medical Act creation failed',
                    'success'=>false
                ]);
            }

            return $this->respondCreated([
                'message'=>'Patient created',
                'success'=>true,
                'data'=>[
                    'id_medicalAct'=>$medicID,
                    'id_personel'=>$idPersonel,
                    'id_patient'=>$idPatient,
                    'type_Act'=>$typeAct,
                    'date_Act'=>$dateAct
                ]
            ]);
    }

    public function updateMediActs(){

    }

    public function deleteMediActs(){

    }
}
