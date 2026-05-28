<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\API\ResponseTrait;
use App\Models\VitalModel;
use App\Models\MedicalActModel;

class VitalController extends BaseController
{
    use ResponseTrait;
    protected $vi_model;
    protected $mediAct_model;

    function __construct(){
        $this->vi_model = new VitalModel();
        $this->mediAct_model = new MedicalActModel();
    }
    public function indexVi()
    {
        $vitals = $this->vi_model
                       ->select('vitalsigns.id_vi,vitalsigns.temperature,vitalsigns.weigth,vitalsigns.blood_pressure,vitalsigns.height,vitalsigns.heart_beat,
                                    medicalact.id_medicalAct')
                        ->join('medicalact','vitalsigns.id_medicalAct = medicalact.id_medicalAct')
                        ->join('patient','medicalact.id_patient = patient.id_patient')
                        ->join('personel','medicalact.id_personel = personel.id_personel')
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
}
