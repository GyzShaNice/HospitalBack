<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\API\ResponseTrait;
use App\Models\PrescribtionModel;
use App\Models\ConsultationModel;

class PrescribController extends BaseController
{
    use ResponseTrait;

    protected $pres_model;
    protected $consult_model;

    function __construct(){
        $this->presc_model = new PrescribtionModel();
        $this->consult_model = new ConsultationModel();
    }
    public function indexPresc()
    {
        $prescs = $this->presc_model
                       ->select('prescrib.id_presc,prescrib.date_presc,prescrib.instructions,consultation.id_consult')
                       ->join('consultation','prescrib.id_consult = consultation.id_consult')
                       ->findAll();
           $response=[
            "message" => count($prescs) >0? "prescribtion found":"prescribtion not found",
            "success" => count($prescs),
            "data" =>$prescs
           ];
           
           return $this->respond($response);
    }

    public function showPres($idPresc = null){
        $prescs = $this->presc_model
                       ->select('prescrib.id_presc,prescrib.date_presc,prescrib.instructions,consultation.id_consult')
                       ->join('consultation','prescrib.id_consult = consultation.id_consult')
                       ->find($idPresc);
           $response=[
            "message" => count($prescs) >0? "prescribtion found":"prescribtion not found",
            "success" => count($prescs),
            "data" =>$prescs
           ];
           
           return $this->respond($response);
    }

    public function createPresc(){
        helper(['form']);

        $rules = [
            'date_presc' => 'required|valid_date',
            'instructions' => 'required|max_length[255]|min_length[3]',

        ];

        if(!$this->validate($rules)){
            $response=[
                'error'=>$this->validator->getErrors(),
                'message'=>'invalid information',
                'success'=>false
            ];

            return $this->fail($response);
        }

        $idConsultID = $this->request->getVar('id_consult');

        $consultation = $this->consult_model->find($idConsultID);

        if(!$consultation){
            return $this->fail([
                'message'=>'Consultation not found'
            ]);
        }

        $dataPresc = [
            'date_presc'=>$this->request->getVar('date_presc'),
            'instructions' =>$this->request->getVar('instructions'),

            'id_consult'=>$idConsultID,

        ];

        $PrescID = $this->presc_model->insert($dataPresc);
            $response = [
                "message"=>$PrescID? "presc inserted":"presc not inserted",
                "success"=>(bool) $PrescID,
                "data"=>array_merge(['id_presc'=>$PrescID],$dataPresc)
            ];

            return $this->respondCreated($response);
    }

    public function updatePresc($idPresc = null){
        helper(['form']);

        $rules = [
            'date_presc' => 'required|valid_date',
            'instructions' => 'required|max_length[255]|min_length[3]',
        ];
    }

    public function deletePresc($idPresc = null){
        
    }
}
