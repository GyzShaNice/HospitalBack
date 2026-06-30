<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\API\ResponseTrait;
use App\Models\ConsultationModel;
use App\Models\MedicalActModel;
use App\Models\Personnel;
use App\Models\PatientModel;
use App\Models\UserModel;

class ConsultationController extends BaseController
{
    use ResponseTrait;
    protected $consult_model;
     protected $mediAct_model;
     protected $patient_model;
    protected $perso_model;
    protected $user_model;

     function __construct(){
        $this->consult_model = new ConsultationModel();
        $this->mediAct_model = new MedicalActModel();
        $this->patient_model = new PatientModel();
        $this->perso_model = new Personnel();
        $this->user_model = new UserModel();
     }
    public function indexConsult()
    {
        $consults = $this->consult_model
                         ->select('consultation.id_consult,consultation.motif,consultation.symptoms
                                  ,consultation.diagnois,consultation.observation,consultation.recommendation,
                                  medicalact.id_medicalAct,
                                  patient.id_patient,
                                  patientUser.name_user AS patient_name,
                                  patientUser.surname_user AS patient_surname,
                                  
                                  personel.id_personel,
                                  personelUser.name_user AS personel_name,
                                  personelUser.surname_user AS personel_surname')
                          ->join('medicalact','consultation.id_medicalAct = medicalact.id_medicalAct')

                          ->join('patient','medicalact.id_patient = patient.id_patient')  
                          ->join('users AS patientUser','patient.id_user = patientUser.id_user')

                          ->join('personel','medicalact.id_personel = personel.id_personel')
                          ->join('users AS personelUser','personel.id_user = personelUser.id_user')
                        

                      
                          ->findAll();
            $response = [
                "message" => count($consults)> 0? "consultation found":"consultation not found",
                "success"=>count($consults),
                "data"=>$consults
            ];
            
            return $this->respond($response);
    }

    public function showConsult($idConsult = null){
            $consults = $this->consult_model
                         ->select('consultation.id_consult,consultation.motif,consultation.symptoms
                                  ,consultation.diagnois,consultation.observation,consultation.recommendation,
                                  medicalact.id_medicalAct')
                          ->join('medicalact','consultation.id_medicalAct = medicalact.id_medicalAct')  
                          ->find($idConsult);
            $response = [
                "message" => count($consults)> 0? "consultation found":"consultation not found",
                "success"=>count($consults),
                "data"=>$consults
            ];
            
            return $this->respond($response);
    }

    public function createConsult(){
        helper(['form']);

        $rules = [
            'motif'=>'required|max_length[255]|min_length[5]',
            'symptoms'=>'required|max_length[255]|min_length[5]',
            'diagnois'=>'required|max_length[255]|min_length[5]',
            'observation'=>'required|max_length[255]|min_length[5]',
            'recommendation'=>'required|max_length[255]|min_length[5]'
        ];

        if(!$this->validate($rules)){
            $response = [
                'error'=>$this->validator->getErrors(),
                'message'=>'invalid information',
                'success'=>false
            ];

            return $this->fail($response);
        }

        $idMedicalAct = $this->request->getVar('id_medicalAct');

        $medicalAct = $this->mediAct_model->find($idMedicalAct);

        if(!$medicalAct){
            return $this->fail([
                'message'=>'Medical act not found'
            ]);
        }

        $dataConsult = [
            'motif'=>$this->request->getVar('motif'),
            'symptoms'=>$this->request->getVar('symptoms'),
            'diagnois'=>$this->request->getVar('diagnois'),
            'observation'=>$this->request->getVar('observation'),
            'recommendation'=>$this->request->getVar('recommendation'),
            'id_medicalAct'=>$idMedicalAct,
        ];

        $ConsultID = $this->consult_model->insert($dataConsult);

            $response = [
                "message"=>$ConsultID ? "consultation recorded":"consultation not recorded",
                "success"=>(bool) $ConsultID,
                "data"=>array_merge(['id_consult'=>$ConsultID],$dataConsult)
            ];

            return $this->respondCreated($response);
    }

    public function updateConsult($idConsult = null){
        helper(['form']);

        $rules = [
            'motif'=>'required|max_length[255]|min_length[5]',
            'symptoms'=>'required|max_length[255]|min_length[5]',
            'diagnois'=>'required|max_length[255]|min_length[5]',
            'observation'=>'required|max_length[255]|min_length[5]',
            'recommendation'=>'required|max_length[255]|min_length[5]'
        ];

        if(!$this->validate($rules)){
            $response = [
                'error'=>$this->validator->getErrors(),
                'message'=>'invalid information',
                'success'=>false
            ];

            return $this->fail($response);
        }

        $dataConsult = [
                'motif'=>$this->request->getVar('motif'),
                'symptoms'=>$this->request->getVar('symptoms'),
                'diagnois'=>$this->request->getVar('diagnois'),
                'observation'=>$this->request->getVar('observation'),
                'recommendation'=>$this->request->getVar('recommendation'),
           ];

           $ConsultID = $this->consult_model->update($idConsult,$dataConsult);

            $response = [
                "message"=>$ConsultID? "consultation updated":"consultation not updated",
                "success"=>(bool)$ConsultID,
                "data"=>array_merge(['id_consult'=>$idConsult],$dataConsult)
            ];

            return $this->respond($response);

    }

    public function deleteConsult($idConsult = null){
        $consult_exist = $this->consult_model->find($idConsult);

            if(!$consult_exist){
                return $this->failNotFound('consultation not found');
            }

            $deleted = $this->consult_model->delete($idConsult);

            $response = [
                "message"=>$deleted? "consultation deleted":"consultation not deleted",
                "success"=>(bool)$deleted,
                "data" =>['id_consult'=>$idConsult]
            ];

            return $this->respond($response);
    }
}
