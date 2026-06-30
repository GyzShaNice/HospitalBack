<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\API\ResponseTrait;
use App\Models\VitalModel;
use App\Models\MedicalActModel;
use App\Models\Personnel;
use App\Models\PatientModel;
use App\Models\UserModel;

class VitalController extends BaseController
{
    use ResponseTrait;
    protected $vi_model;
    protected $mediAct_model;
    protected $patient_model;
    protected $perso_model;
    protected $user_model;


    function __construct(){
        $this->vi_model = new VitalModel();
        $this->mediAct_model = new MedicalActModel();
        $this->patient_model = new PatientModel();
        $this->perso_model = new Personnel();
        $this->user_model = new UserModel();
    }
    public function indexVi()
    {
        $vitals = $this->vi_model
                       ->select('vitalsigns.id_vi,vitalsigns.temperature,vitalsigns.weight,vitalsigns.blood_pressure,vitalsigns.height,vitalsigns.heart_beat,
                                    medicalact.id_medicalAct,
                                    
                                    patient.id_patient,
                                    patientUser.name_user AS patient_name,
                                    patientUser.surname_user AS patient_surname,
                                    
                                    personel.id_personel,
                                    personelUser.name_user AS personel_name,
                                    personelUser.surname_user AS personel_surname'
                                    
                                    )
                        ->join('medicalact','vitalsigns.id_medicalAct = medicalact.id_medicalAct')
                        ->join('patient','medicalact.id_patient = patient.id_patient')

                        ->join('users AS patientUser','patient.id_user = patientUser.id_user')

                        ->join('personel','medicalact.id_personel = personel.id_personel')

                        ->join('users AS personelUser','personel.id_user = personelUser.id_user')

                        ->findAll();
                        
        $response = [
            "message"=>count($vitals)> 0? "vital signs found":"vitals signs not found",
            "success"=>count($vitals),
            "data"=>$vitals
        ];
        
        return $this->respond($response);
    }

    public function showVi($idVi = null){
        $specificVitals = $this->vi_model
                                ->select('vitalsigns.id_vi,vitalsigns.temperature,vitalsigns.weigth,vitalsigns.blood_pressure,vitalsigns.height,vitalsigns.heart_beat,
                                    medicalact.id_medicalAct')
                                ->join('medicalact','vitalsigns.id_medicalact = medicalact.id_medicalact')
                                ->join('patient','medicalact.id_patient = patient.id_patient')
                                ->join('personel','medicalact.id_personel = personel.id_personel')
                                ->find();
            $response = [
                "message" => $specificVitals ? "vitals found":"vitals not found",
                "success" => (bool)$specificVitals,
                "data"    => $specificVitals
            ];
            
            return $this->respond($response);
    }

    public function createVi(){
        helper(['form']);

        $rules = [
            'temperature' => 'required',
            'weight'      => 'required',
            'blood_pressure' => 'required',
            'height' => 'required',
            'heart_beat' => 'required',
        ];

        if(!$this->validate($rules)){
            $response = [
                'error'=>$this->validator->getErrors(),
                'message'=>'invalid information',
                'success'=>false
            ];

            return $this->fail($response);
        }

        // reads data sent from postman or frontend
        // recupere l'id de l'acte medical envoyer 
        $idMedicalAct = $this->request->getVar('id_medicalAct');

        // regarde dans la bd si l'id existe
        $medicalAct = $this->mediAct_model->find($idMedicalAct);

        if(!$medicalAct){
            return $this->fail([
                'message'=>'Medical not found'
            ]);
        }

        $dataVitals = [
            'temperature'=>$this->request->getVar('temperature'),
            'weight' =>$this->request->getVar('weight'),
            'blood_pressure'=>$this->request->getVar('blood_pressure'),
            'height' => $this->request->getVar('height'),
            'heart_beat' => $this->request->getVar('heart_beat'),
            'id_medicalAct'=>$idMedicalAct,
            // relier vitalsigns a medical act
        ];

        $VitalsID = $this->vi_model->insert($dataVitals);
            $response = [
                "message"=>$VitalsID ? "vital sign recorded" : "vital sign not created",
                "success"=>(bool) $VitalsID,
                "data"=>array_merge(['id_vi'=>$VitalsID],$dataVitals)
            ];

            return $this->respondCreated($response);
    }

    public function updateVi($idVi = null){
        helper(['form']);

        $rules = [
            'temperature' => 'required',
            'weight'      => 'required',
            'blood_pressure' => 'required',
            'height' => 'required',
            'heart_beat' => 'required',
        ];

        if(!$this->validate($rules)){
            $response = [
                'error'=>$this->validator->getErrors(),
                'message'=>'invalid information',
                'success'=>false
            ];

            return $this->fail($response);
        }

        $dataVitals = [
            'temperature' => $this->request->getVar('temperature'),
            'weight' =>$this->request->getVar('weight'),
            'blood_pressure'=>$this->request->getVar('blood_pressure'),
            'height' => $this->request->getVar('height'),
            'heart_beat'=>$this->request->getVar('heart_beat')
        ];

        $VitalsID = $this->vi_model->update($idVi,$dataVitals);

        $response = [
            "message"=>$VitalsID? "vitals signs updated":"vital signs not updated",
            "success"=>(bool) $VitalsID,
            "data"=>array_merge(['id_vi'=>$idVi],$dataVitals)
        ];

        return $this->respond($response);
    }

    public function deleteVi($idVi = null){
        $vital_exist = $this->vi_model->find($idVi);

        if(!$vital_exist){
            return $this->failNotFound('vitals not found');
        }

        $deleted = $this->vi_model->delete($idVi);

        $response = [
            "message"=>$deleted? "vitals deleted":"vitals not deleted",
            "success"=>(bool)$deleted,
            "data"=>['id_vi'=>$idVi]
        ];

        return $this->respond($response);
    }

    public function getVitalsByMedicalAct($idMedicalAct){
        $vital = $this->vi_model
                    ->select('
                        vitalsigns.id_vi,
                        vitalsigns.temperature,
                        vitalsigns.weight,
                        vitalsigns.blood_pressure,
                        vitalsigns.height,
                        vitalsigns.heart_beat,

                        medicalact.id_medicalAct,

                        patientUser.name_user AS patient_name,
                        patientUser.surname_user AS patient_surname,

                        personelUser.name_user AS personel_name,
                        personelUser.surname_user AS personel_surname
                    ')
                    ->join('medicalact', 'vitalsigns.id_medicalAct = medicalact.id_medicalAct')
                    ->join('patient', 'medicalact.id_patient = patient.id_patient')
                    ->join('users AS patientUser', 'patient.id_user = patientUser.id_user')
                    ->join('personel', 'medicalact.id_personel = personel.id_personel')
                    ->join('users AS personelUser', 'personel.id_user = personelUser.id_user')
                    ->where('vitalsigns.id_medicalAct', $idMedicalAct)
                    ->first();


                    if (!$vital){
                        return $this->failNotFound("vital record not found");
                    }

                    return $this->respond([
                        "success"=>true,
                        "message"=>"vital found",
                        "data"=>$vital
                    ]);
    }

}
