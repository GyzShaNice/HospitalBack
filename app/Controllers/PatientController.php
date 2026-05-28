<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\API\ResponseTrait;
use App\Models\PatientModel;
use App\Models\UserModel;

class PatientController extends BaseController
{
    use ResponseTrait;

    protected $patient_model;
    protected $user_model;

    public function __construct(){
        $this->patient_model = new PatientModel();
        $this->user_model = new UserModel();
    }

    public function indexPatient()
    {
        $patients = $this->patient_model
                         ->select('patient.id_patient,patient.emergency_number,users.id_user,users.name_user,users.surname_user,
                                    users.email,users.password,users.telephone,users.quarter')
                         ->join('users','patient.id_user = users.id_user')
                         ->findAll();
                    $response = [
                        "message"=>count($patients)>0? "patient found":"patient not found",
                        "success"=>count($patients)>0,
                        "data"=>$patients
                    ];
                    
                    return $this->respond($response);
    }

    public function showPatient($idpatient= null){
        $Specificpatient = $this->patient_model
                                ->select('patient.id_patient,patient.emergency_number,users.id_user,users.name_user,users.surname_user
                                        ,users.password,users.telephone,users.quarter,users.email')
                                ->join('users','patient.id_user = users.id_user')
                                ->find();
                                
        $response = [
            "message"=>$Specificpatient ? "patient found":"patient not found",
            "success"=>(bool)$Specificpatient,
            "data"=>$Specificpatient
        ];
        
        return $this->respond($response);
    }

    public function createPatient(){
        helper(['form']);

        $rules = [
            'name_user' => 'required|max_length[254]|min_length[2]',
            'emergency_number'=>'required|max_length[254]|min_length[9]',
            'surname_user'=>'required|max_length[254]|min_length[2]',
            'password'=>'required|max_length[254]|min_length[7]',
            'email'=> 'required|max_length[254]|valid_email|is_unique[users.email]',
            'quarter'=>'required|max_length[254]|min_length[2]',
            'telephone'=>'required|max_length[15]|is_unique[users.telephone]',

        ];

        // get the values entered by the user
        $name = $this->request->getVar('name_user');
        $surname=$this->request->getVar('surname_user');
        $email=$this->request->getVar('email');
        $password=$this->request->getVar('password');
        $telephone=$this->request->getVar('telephone');
        $quarter=$this->request->getVar('quarter');
        $emergency=$this->request->getVar('emergency_number');
    

        $db = \Config\Database::connect();
        $db->transStart();

        // now verify if a user exist
        $user = $this->user_model->where('email',$email)->first();

            if(!$user){
                $UserID = $this->user_model->insert([
                    'name_user'=>$name,
                    'surname_user'=>$surname,
                    'email'=>$email,
                    'password'=>password_hash($password,PASSWORD_DEFAULT),
                    'telephone'=>$telephone,
                    'quarter'=>$quarter,
                ]);

            }else{
                $UserID = $user['id_user'];
            }

            if(empty($emergency)){
                return $this->fail([
                    'message'=>'emergency number not found',
                    'success'=>false
                ]);
            }

            $patientID = $this->patient_model->insert([
                'id_user'=>$UserID,
                'emergency_number'=>$emergency,
            ]);

            $db->transComplete();

            if($db->transStatus()==false){
                return $this->fail([
                    'message'=>'patient creation failed',
                    'success'=>false
                ]);
            }

            return $this->respondCreated([
                'message'=>'Patient created',
                'success'=>true,
                'data'=>[
                    'id_patient'=>$patientID,
                    'id_user'=>$UserID,
                    'name_user'=>$name,
                    'surname_user'=>$surname,
                    'email'=>$email,
                    'quarter'=>$quarter,
                    'emergency_number'=>$emergency
                ]
            ]);
    }

    public function updatePatient(){

    }

    public function deletePatient(){
        
    }
}
