<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\API\ResponseTrait;
use App\Models\PatientModel;
use App\Models\UserModel;
use App\Models\MedicalActModel;
use App\Models\VitalModel;
use App\Models\ConsultationModel;
use App\Models\Personnel;





class PatientController extends BaseController
{
    use ResponseTrait;

    protected $patient_model;
    protected $user_model;
    protected $mediAct_model;
    protected $vi_model;
    protected $consult_model;
    protected $perso_model;





    public function __construct(){
        $this->patient_model = new PatientModel();
        $this->user_model = new UserModel();
        $this->mediAct_model = new MedicalActModel();
        $this->vi_model = new VitalModel();
        $this->consult_model = new ConsultationModel();
        $this->perso_model = new Personnel();
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

    public function updatePatient($idpatient = null){
        $patient = $this->patient_model->find($idpatient);
        if(!$patient){
            return $this->failNotFound('Patient not found');
        }
        // cherche le patient. si il n'existe pas,retourne l'erreur

        $id_user = $patient['id_user'];
        // recupere l'id de la table users lie a ce patient

        // ont demarre la transaction, si une des requete echoue,les 2 sont annuler
        $db = \Config\Database::connect();
        $db->transStart();

        $this->user_model->update($id_user,[
            'name_user'=>$this->request->getVar('name_user'),
            'surname_user'=>$this->request->getVar('surname_user'),
            'email'=>$this->request->getVar('email'),
            'telephone'=>$this->request->getVar('telephone'),
            'quarter'=>$this->request->getVar('quarter'),
        ]);
        // mets a jour la table users avec les nouvelles donnee

        // ont mets a jour la table patient(seulement emergency_number est dans cette table)

        $this->patient_model->update($idpatient,[
            'emergency_number'=>$this->request->getVar('emergency_number'),
        ]);

        $db->transComplete();

        if($db->transStatus() === false){
            return $this->fail('Update failed');
        }

        return $this->respond(['message'=> 'Patient updated','success'=>true]);

    }

    public function deletePatient($idpatient = null){
        $patient  = $this->patient_model->find($idpatient);

        if(!$patient){
            return $this->failNotFound('Patient not found');
        }

        $id_user = $patient['id_user'];

        // demarre une transaction. si une 2 suppressions echoues,les 2 sont annulees

        $db = \Config\Database::connect();
        $db->transStart();

        $this->patient_model->delete($idpatient);
        // supprimer le patient dans la table patient.ont supprimer en premier parceque patient depend de users

        $this->user_model->delete($id_user);
        // ont supprime l'utilisateur dans la table users. ont supprimer en deuxieme

        $db->transComplete();

        if($db->transStatus() === false){
            return $this->fail('Delete failed');
        }

        return $this->respond([
            'message'=>'Patient deleted',
            'success'=>true
        ]);

    }

    // pour avoir les past information complet d'un patient
    public function getPatientHistory($idPatient){
        $medicalActs = $this->mediAct_model
                            ->where('id_patient',$idPatient)
                            ->findAll();

                //    par dans la table act medical
                // regarde les records ou le patient = this patient 


                $history = [];
                // ont va stocker l'historique du patient ici

                foreach($medicalActs as $act){
                    $vitals = $this->vi_model
                                    ->where('id_medicalAct',$act['id_medicalAct'])
                                    ->first();

                    $consult = $this->consult_model
                                    ->where('id_medicalAct',$act['id_medicalAct'])
                                    ->first(); 
                                    
                    $patient = $this->patient_model
                                    ->select('users.name_user, users.surname_user')
                                    ->join('users', 'patient.id_user = users.id_user')
                                    ->where('patient.id_patient', $act['id_patient'])
                                    ->first();
                                    
                    $personnel = $this->perso_model
                                    ->select('users.name_user, users.surname_user')
                                    ->join('users', 'personel.id_user = users.id_user')
                                    ->where('personel.id_personel', $act['id_personel'])
                                    ->first();                
                
                        $history[] = [
                            "patient"=>$patient,
                            "personnel" =>$personnel,
                            "medicalAct" => $act,
                            "vitals" => $vitals,
                            "consultation" => $consult
                        ];

                
                }


                
                return $this->respond([
                    "success" => true,
                    "data"=> $history
                ]);
    }
}
