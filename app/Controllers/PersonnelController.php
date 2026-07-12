<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use Codeigniter\API\ResponseTrait;
use App\Models\Personnel;
use App\Models\UserModel;
use App\Models\FunctionnModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class PersonnelController extends BaseController
{
    use ResponseTrait;
    protected $perso_model;
    protected $user_model;
    protected $funct_model;

    public function __construct(){
        $this->perso_model = new Personnel();
        $this->user_model = new UserModel();
        $this->funct_model = new FunctionnModel();
    }

    private function getAuthenticatedPersonnelId(): ?int
    {
        $header = $this->request->getHeaderLine('Authorization');

        if (empty($header) || !preg_match('/Bearer\s(\S+)/', $header, $matches)) {
            return null;
        }

        try {
            $decoded = JWT::decode($matches[1], new Key(getenv('JWT_SECRET'), 'HS256'));
            return isset($decoded->data->id_personel) ? (int) $decoded->data->id_personel : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function me()
    {
        $idPersonnel = $this->getAuthenticatedPersonnelId();

        if (!$idPersonnel) {
            return $this->failUnauthorized('Unauthorized');
        }

        $profile = $this->perso_model
            ->select('personel.id_personel, personel.staff_code, personel.id_user, users.name_user, users.surname_user, users.email, users.telephone, users.quarter, function.name AS function_name')
            ->join('users', 'personel.id_user = users.id_user')
            ->join('function', 'personel.id_function = function.id_function', 'left')
            ->where('personel.id_personel', $idPersonnel)
            ->first();

        if (!$profile) {
            return $this->failNotFound('Personnel not found');
        }

        return $this->respond([
            'message' => 'profile found',
            'success' => true,
            'data' => $profile,
        ]);
    }

    public function updateMe()
    {
        $idPersonnel = $this->getAuthenticatedPersonnelId();

        if (!$idPersonnel) {
            return $this->failUnauthorized('Unauthorized');
        }

        $currentPersonnel = $this->perso_model->find($idPersonnel);

        if (!$currentPersonnel) {
            return $this->failNotFound('Personnel not found');
        }

        $currentUser = $this->user_model->find($currentPersonnel['id_user']);

        if (!$currentUser) {
            return $this->failNotFound('User not found');
        }

        $body = $this->request->getJSON(true) ?? [];

        $nameUser = trim((string) ($body['name_user'] ?? $this->request->getVar('name_user') ?? $currentUser['name_user']));
        $surnameUser = trim((string) ($body['surname_user'] ?? $this->request->getVar('surname_user') ?? $currentUser['surname_user']));
        $email = trim((string) ($body['email'] ?? $this->request->getVar('email') ?? $currentUser['email']));
        $telephone = trim((string) ($body['telephone'] ?? $this->request->getVar('telephone') ?? $currentUser['telephone']));
        $quarter = trim((string) ($body['quarter'] ?? $this->request->getVar('quarter') ?? $currentUser['quarter']));
        $staffCode = trim((string) ($body['staff_code'] ?? $this->request->getVar('staff_code') ?? $currentPersonnel['staff_code']));

        if ($nameUser === '' || $surnameUser === '' || $email === '' || $telephone === '' || $quarter === '') {
            return $this->fail([
                'message' => 'All required profile fields must be filled',
                'success' => false,
            ], ResponseInterface::HTTP_BAD_REQUEST);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->fail([
                'message' => 'Invalid email format',
                'success' => false,
            ], ResponseInterface::HTTP_BAD_REQUEST);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $this->user_model->update($currentPersonnel['id_user'], [
            'name_user' => $nameUser,
            'surname_user' => $surnameUser,
            'email' => $email,
            'telephone' => $telephone,
            'quarter' => $quarter,
        ]);

        $this->perso_model->update($idPersonnel, [
            'staff_code' => $staffCode,
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->fail([
                'message' => 'Update failed',
                'success' => false,
            ]);
        }

        $updatedProfile = $this->perso_model
            ->select('personel.id_personel, personel.staff_code, personel.id_user, users.name_user, users.surname_user, users.email, users.telephone, users.quarter, function.name AS function_name')
            ->join('users', 'personel.id_user = users.id_user')
            ->join('function', 'personel.id_function = function.id_function', 'left')
            ->where('personel.id_personel', $idPersonnel)
            ->first();

        return $this->respond([
            'message' => 'Profile updated',
            'success' => true,
            'data' => $updatedProfile,
        ]);
    }

    public function indexPerso()
    {
        $persos = $this->perso_model
                      ->select('personel.id_personel,personel.staff_code,users.id_user,users.name_user,
                                users.surname_user,users.quarter,users.telephone,users.email,function.name')
                       ->join('users','personel.id_user = users.id_user')
                       ->join('function','personel.id_function = function.id_function')  
                       ->findAll();

                    $response = [
                        "message"=>count($persos)>0? "personnel found":"personnel not found",
                        "success"=>count($persos)>0,
                        "data"=>$persos
                    ];
                    
                    return $this->respond($response);
    }

    public function showPerso($idperso = null){
        $perso = $this->perso_model
                      ->select('personnel.id_personel,users.id_user,users.name_user,users.surname_user
                                ,users.quarter,users.telephone,users.email')
                      ->join('users','personnel.id_user = users.id_user')
                      ->first();
                      
                    $response = [
                        "message"=>$perso ? "personnel found":"personnel not found",
                        "success"=>(bool)$perso,
                        "data"=>$perso
                    ];

            return $this->respond($response);
    }

    

    public function connexionPerso(){
        helper(['form']);

        $rules = [
            'staff_code'=>'required|max_length[100]|min_length[3]',
            'password'=>'required|max_length[100]|min_length[3]',
        ];

        if (!$this->validate($rules)) {
            return $this->fail([
                'error'   => $this->validator->getErrors(),
                'message' => 'invalid',
                'success' => false
            ], ResponseInterface::HTTP_BAD_REQUEST);
        }

        

        $staff = trim((string) $this->request->getVar('staff_code'));
        $password = $this->request->getVar('password');

        if ($staff === '') {
            return $this->fail([
                'error'   => 'staff_code is required',
                'message' => 'invalid',
                'success' => false
            ], ResponseInterface::HTTP_BAD_REQUEST);
        }

        // find personel by staff code
    // dd($this->request->getVar('staff_code'));
    // dd($staff);
       $perso = $this->perso_model
              ->where('staff_code',$staff)
              ->first();

// dd($perso);

        if (!$perso) {
            return $this->fail([
                'error'   => 'personnel not found',
                'message' => 'wrong credentials',
                'success' => false
            ], ResponseInterface::HTTP_BAD_REQUEST);
        }

        $user = $this->user_model->find($perso['id_user']);

        if (!$user) {
            return $this->fail([
                'error'   => 'user record not found',
                'message' => 'account incomplete',
                'success' => false
            ], ResponseInterface::HTTP_BAD_REQUEST);
        }

        if (!password_verify($password, $user['password'])) {
            return $this->fail([
                'error'   => 'wrong password',
                'message' => 'password not matching',
                'success' => false
            ], ResponseInterface::HTTP_BAD_REQUEST);
        }

        $function = $this->funct_model->where('id_function',$perso['id_function'])->first();

           if (!$function) {
                return $this->fail([
                    'error'   => 'no function assigned',
                    'message' => 'staff has no function',
                    'success' => false
                ], ResponseInterface::HTTP_BAD_REQUEST);
         } 

         $iat = time();
         $payload = [
            'iss'=>base_url(),
            'sub'=>$perso['id_personel'],
            'iat'=>$iat,
            'exp'=>$iat+3600,
            'data'=>[
                'id_personel'=>$perso['id_personel'],
                'id_user'=>$user['id_user'],
                'name_user'=>$user['name_user'],
                'surname_user'=>$user['surname_user'],
                'email'=>$user['email'],
                'staff_code'=>$perso['staff_code'],
                'function'=>$function['name'],
            ]
         ];

         $secretKey = getenv('JWT_SECRET');
         $jwt = \Firebase\JWT\JWT::encode($payload,$secretKey,'HS256');

        return $this->respond([
        'message' => 'login successful',
        'success' => true,
            'data'    => [
                'id_personel'   => $perso['id_personel'],
                'id_user'       => $user['id_user'],
                'name_user'     => $user['name_user'],
                'email'         => $user['email'],
                'telephone'     => $user['telephone'],
                'quarter'       => $user['quarter'],
                'staff_code'    => $perso['staff_code'],
                'function'      => $function['name'],
                'token'         =>$jwt,
               
            ]
        ], ResponseInterface::HTTP_OK); 
    }

    public function createPerso(){
        helper(['form']);

        $rules = [
            'name_user' => 'required|max_length[100]|min_length[5]',
            'staff_code' => 'required|max_length[100]|min_length[5]',
            'surname_user'=> 'required|max_length[10]|is_unique[users.surname_user]',
            'password'=> 'required|max_length[254]|min_length[7]',
            'email' => 'required|max_length[254]|valid_email|is_unique[users.email]',
            'quarter' => 'required|max_length[15]|min_length[2]',
            'telephone'=> 'required|max_length[15]|is_unique[users.telephone]',
            'id_function'=>'required|integer'
        ];

        if (!$this->validate($rules)) {
            return $this->fail([
                'error'   => $this->validator->getErrors(),
                'message' => 'Validation failed',
                'success' => false
            ], ResponseInterface::HTTP_BAD_REQUEST);
        }

        // get the values entered by user
        $name = $this->request->getVar('name_user');
        $surname = $this->request->getVar('surname_user');
        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');
        $telephone = $this->request->getVar('telephone');
        $quarter = $this->request->getVar('quarter');
        $staff = $this->request->getVar('staff_code');
        $function = $this->request->getVar('id_function');
     

        $functionExists = $this->funct_model->find($function);

        if(!$functionExists){
            return $this->fail([
                'message'=> 'Function not found ,please create it first',
                'success'=>false
            ],ResponseInterface::HTTP_BAD_REQUEST);
        }


        // transaction is a group of actions that must either succeed or fail together
        // we use transactions to
        // prevent partial data saving,database stays correct even if something fails
        // if one query fails,everything is undone

        // this done means connecting transaction to db
     $db = \Config\Database::connect();
            // this done means transStART(everthing must either fail or succeed together)
        $db->transStart();

            // now verify if a user exist

            $user = $this->user_model->where('email',$email)->first();

            // if not, create a new user
                if(!$user){
                    $UserID = $this->user_model->insert([
                        'name_user'=> $name,
                        'surname_user'=>$surname,
                        'email'=>$email,
                        'password'=>password_hash($password,PASSWORD_DEFAULT),
                        'telephone'=>$telephone,
                        'quarter'=>$quarter,
                    ]);

                }else{
                    $UserID = $user['id_user'];
                }

                // now we insert the user inside the perso table

                if (empty($staff)) {
                    return $this->fail([
                        'message' => 'staff_code is required',
                        'success' => false
                    ]);
                }

                // this done means create a personnel linked to a user
                $persoID = $this->perso_model->insert([
                    'id_user' => $UserID,
                    'staff_code' => $staff,
                    'id_function'=>$function
                ]);

                $db->transComplete();

                if($db->transStatus()== false){
                    return $this->fail([
                        'message'=>'personnel creation failed',
                        'success'=>false
                    ]);
                }

                // return this down if perso created
                    return $this->respondCreated([
                        'message'=>'Perso created',
                        'success'=>true,
                        'data'=>[
                            'id_personel'=>$persoID,
                            'id_user' => $UserID,
                            'name_user' => $name,
                            'surname_user'=>$surname,
                            'email'=>$email,
                            'quarter'=>$quarter,
                            'staff'=>$staff
                        ]
                    ]);
    } 
    
    public function updatePerso($idperso=null){
        $perso = $this->perso_model->find($idperso);

        if(!$perso){
            return $this->failNotFound('Personnel not found');
        }

        $id_user = $perso['id_user'];

        $db = \Config\Database::connect();
        $db->transStart();

        $this->user_model->update($id_user,[
            'name_user' => $this->request->getVar('name_user'),
            'surname_user'=>$this->request->getVar('surname_user'),
            'email'=>$this->request->getVar('email'),
            'telephone'=>$this->request->getVar('telephone'),
            'quarter'=>$this->request->getVar('quarter')
        ]);

        $this->perso_model->update($idperso,[
            'staff_code'=>$this->request->getVar('staff_code')
        ]);

        $db->transComplete();

        if($db->transStatus() === false){
            return $this->fail('Update failed');
        }

        return $this->respond(['message'=>'Personnel updatedd','success'=>true]);
    }

    public function deletePerso($idperso=null){
        $perso = $this->perso_model->find($idperso);

        if(!$perso){
            return $this->failNotFound('Personnel not found');
        }

        $id_user = $perso['id_user'];

        $db = \Config\Database::connect();
        $db->transStart();

        $this->perso_model->delete($idperso);

        $this->user_model->delete($id_user);

        $db->transComplete();

        if($db->transStatus() === false){
            return $this->fail('Delete failed');
        }

        return $this->respond([
            'message'=> 'Personnel deleted',
            'success'=>true
        ]);
    }
}
