<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use Codeigniter\API\ResponseTrait;

use App\Models\UserModel;

class UserController extends BaseController
{
    // ResponseTrait enables us to use API methods instead of raw json
    use ResponseTrait;
    protected $user_model;

    public function __construct(){
        $this->user_model = new UserModel();
    }
    // having instances of model helps you
    // to communicate with the database but 
    // by passing through the controller
    public function index()
    {
        $user = $this->user_model->findAll();
        $response = [
            "message" => count($user) > 0? "user found":"user not found",
            "success" => count($user) > 0,
            "data" => $user
        ];

        return $this->respond($response);
    }

    public function show($iduser = null){
        $SpecificUser = $this->user_model->find($iduser);

        $response = [
            "message" => $SpecificUser ? "user found":"user not found",
            "success" => (bool) $SpecificUser,
            "data" => $SpecificUser
        ];

        return $this->respond($response);
    }

    public function connexion(){
        helper(['form']);

        $rules = [
            'password' => 'required|max_length[254]|min_length[6]',
            'staff_code' => 'required|max_length[100]|min_length[5]',
        ];

        if(!$this->validate($rules)){
            return $this->fail([
                'error' => $this->validator->getErrors(),
                "message" => 'valid input',
                "success" => false
            ],ResponseInterface::HTTP_BAD_REQUEST);
        }

        $staffCode = $this->request->getVar('staff_code');
        $password = $this->request->getVar('password');

        $user = $this->user_model->where('staff_code',$staffCode)->first();

        if(!$user){
            return $this->fail([
                'error' => 'wrong informations',
                "message"=> 'wrong credentials',
                "success"=>false
            ],ResponseInterface::HTTP_UNAUTHORIZED);
        }

        if(!password_verify($password,$user['password'])){
                return $this->fail([
                    'error' => 'wrong password',
                    "messaage"=> "wrong password",
                    "success"=>false
                ],ResponseInterface::HTTP_UNAUTHORIZED);
        }

        $response = [
            "message"=>"Login successful",
            "success"=>true,
            "data"=>$user
        ];

        return $this->respond($response);
    }

    public function createUser(){
        helper(['form']);

        $rules = [
            'name_user' => 'required|max_length[100]|min_length[5]',
            'surname_user'=> 'required|max_length[30]|min_length[3]',
            'password'=> 'required|max_length[254]|min_length[7]',
            'email' => 'required|max_length[254]|valid_email|is_unique[users.email]',
            'quarter' => 'required|max_length[15]|min_length[2]',
            'telephone'=> 'required|max_length[15]|is_unique[users.telephone]'
        ];
        
        if(!$this->validate($rules)){
            return $this->fail([
                'error'=>$this->validator->getErrors(),
                "message"=>'input invalid',
                "success"=> false
            ], ResponseInterface::HTTP_BAD_REQUEST);
        }

        $password = $this->request->getVar('password');

        $dataUser = [
            'name_user' => $this->request->getVar('name_user'),
            'surname_user' => $this->request->getVar('surname_user'),
            'email' => $this->request->getVar('email'),
            'quarter'=>$this->request->getVar('quarter'),
            'telephone'=>$this->request->getVar('telephone'),
            'password'=>password_hash($password,PASSWORD_DEFAULT)
        ];

        $UserID = $this->user_model->insert($dataUser);
            $response = [
                "message" => $UserID ? "user created" : "user not created",
                "success"=>(bool) $UserID,
                "data"=>array_merge(['id_user'=>$UserID],$dataUser)
            ];

            return $this->respondCreated($response);
    }

    public function update($iduser=null){
        helper(['form']);

        $rules = [
             'name_user' => 'required|max_length[100]|min_length[5]',
            'surname_user'=> 'required|max_length[10]|is_unique[users.surname_user]',
            'password'=> 'required|max_length[254]|min_length[7]',
            'email' => 'required|max_length[254]|valid_email|is_unique[users.email]',
            'quarter' => 'required|max_length[15]|min_length[2]',
            'telephone'=> 'required|max_length[15]|is_unique[users.telephone]'
        ];

        if(!$this->validate($rules)){
            $response = [
                'error'=>$this->validator->getErrors(),
                'message'=>'invalid information',
                'success'=>false
            ];

            return $this->fail($response);
        }

        $name_user = $this->request->getVar('name_user');
        $dataUser = [
            'name_user' => $this->request->getVar('name_user'),
            'surname_user' => $this->request->getVar('surname_user'),
            'email' => $this->request->getVar('email'),
            'quarter'=>$this->request->getVar('quarter'),
            'telephone'=>$this->request->getVar('telephone'),
            
        ];

        $UserID = $this->user_model->update($iduser,$dataUser);

        $response = [
            "message" => $UserID? "user updated successfully":"update fail",
            "success"=>(bool) $UserID,
            "data" => array_merge(['id_user'=> $iduser],$dataUser)
        ];

        return $this->respond($response);
    }

    public function delete($iduser=null){
        $user_exist = $this->user_model->find($iduser);

        if(!$user_exist){
            return $this->failNotFound('user not found');
        }

        $deleted=$this->user_model->delete($iduser);

        $response = [
            "message"=>$deleted? "delete ok":"deletion fail",
            "success"=>(bool)$deleted,
            "data"=>['id_user'=>$iduser]
        ];

        return $this->respond($response);
    }
}
