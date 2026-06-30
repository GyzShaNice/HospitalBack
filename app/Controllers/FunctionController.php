<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\API\ResponseTrait;
use App\Models\FunctionnModel;


class FunctionController extends BaseController
{
    use ResponseTrait;
    protected $funct_model;

    public function __construct(){
        $this->funct_model = new FunctionnModel();
    }
    
    public function indexFunction()
    {
        $functs = $this->funct_model->findAll();

            $response = [
                "message"=>count($functs)>0? "functs found":"functs not found",
                "success"=>count($functs)>0,
                "data"=>$functs
            ];

            return $this->respond($response);
    }

    public function showFuncts($idfunct=null){
        $Specificfunct = $this->funct_model->find($idfunct);
                $response = [
                    "message"=>$Specificfunct? "funct done":"funct not done",
                    "success"=>(bool)$Specificfunct,
                    "data" => $Specificfunct
                ];
                
                return $this->respond($response);
    }

    public function createFunction(){
        helper(['form']);

        $rules = [
            'name'=> 'required|max_length[254]|min_length[3]',
            'description'=>'required|max_length[254]|min_length[2]',
            'status'=>'required|max_length[254]|min_length[2]'
        ];

        if(!$this->validate($rules)){
            return $this->fail([
                "error"=>$this->validator->getErrors(),
                "message"=>"could not create function",
                "success"=>false
            ],ResponseInterface::HTTP_BAD_REQUEST);
        }

        $dataFunct = [
            'name'=>$this->request->getVar('name'),
            'description'=>$this->request->getVar('description'),
            'status'=>$this->request->getVar('status')
        ];

        $FunctID = $this->funct_model->insert($dataFunct);
            $response = [
                "message"=>$FunctID? "function created":"function not created",
                "success"=>(bool)$FunctID,
                "data"=>$FunctID
            ];

            if(!$FunctID){
                return $this->response->setJSON($this->funct_model->errors());
            }

            return $this->respondCreated($response);
    }

    public function updateFunct($idFunct=null){
        helper(['form']);

        $rules = [
            'name' => 'required|max_length[254]|min_length[4]',
            'description' => 'required|max_length[254]|min_length[4]',
            'status' => 'required|max_length[255]|min_length[2]'
        ];

        if(!$this->validate($rules)){
            $response = [
                'errors'=>$this->validator->getErrors(),
                'message'=>'invalid information',
                'success'=>false
            ];

            return $this->fail($response);
        }

        $name_func = $this->request->getVar('name');

        $dataFunct = [
                'name' => $name_func,
                'description' => $this->request->getVar('description'),
                'status' => $this->request->getVar('status')
        ];

        $funct = $this->funct_model->update($idFunct,$dataFunct);

        $response = [
            "message"=>$funct? "functin updated":"function not updated",
            "success"=>(bool)$funct,
            "data"=>array_merge(['id_function'=>$idFunct],$dataFunct)
        ];

        return $this->respond($response);
    }

    public function deleteFunct($idfunct=null){
        $function_exist = $this->funct_model->find($idfunct);

        if(!$function_exist){
            return $this->failNotFound('delete impossible');
        }

        $delete = $this->funct_model->delete($idfunct);

        $response = [
            "message" => $delete ? "delete ok" : "delete fail",
                "success" => (bool) $delete,
                "data" => ['id_function'=>$idfunct]
        ];

        return $this->respond($response);
    }
}
