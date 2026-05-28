<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use Codeigniter\API\ResponseTrait;
use App\Models\Personnel;
use App\Models\AbsenceModel;
use App\Models\UserModel;

class AbsenceController extends BaseController
{
    use ResponseTrait;

    protected $perso_model;
    protected $absence_model;
    protected $user_model;

     public function __construct(){
        $this->perso_model = new Personnel();
        $this->absence_model = new AbsenceModel();
        $this->user_model = new UserModel();
       
    }

    public function indexAbs()
    {
        $absences = $this->absence_model
                         ->select('absence.id_absence,
                                    absence.motif,
                                    absence.date,
                                    absence.statut,
                                    users.name_user')
                          ->join('personel','absence.id_personel = personel.id_personel')
                          ->join('users','personel.id_user = users.id_user')
                          ->findAll();
            $response = [
                "message"=>count($absences)>0? "absence found":"absences not found",
                "success"=>count($absences),
                "data"=>$absences
            ];
            
            return $this->respond($response);
    }

    public function showAbs($idAbs = null){

        if(!$idAbs){
            return $this->fail([
                'message'=>'Absence id is required',
                'success'=>false
            ]);
        }
        $absence = $this->absence_model
                         ->select('absence.id_absence,
                                    absence.motif,
                                    absence.date,
                                    absence.statut,
                                    users.name_user')
                          ->join('personel','absence.id_personel = personel.id_personel')
                          ->join('users','personel.id_user = users.id_user')
                          ->where('absence.id_absence',$idAbs)
                          ->first();
            $response = [
                "message"=>count($absence)>0? "absence found":"absences not found",
                "success"=>count($absence),
                "data"=>$absence
            ];
            
            return $this->respond($response);
    }

    public function createAbs(){
        helper(['form']);

        $rules = [
            'motif'=>'required|max_length[254]|min_length[2]',
            'date'=>'required|valid_date[Y-m-d]',
            'statut'=>'required|in_list[accepted,modified,cancel]',
            'id_personel'=>'required|integer'
        ];

        if(!$this->validate($rules)){
            $response=[
                'error'=>$this->validator->getErrors(),
                'message'=>'invalid information',
                'success'=>false
            ];

            return $this->fail($response);

        }

        $idPerso = $this->request->getVar('id_personel');

        $perso = $this->perso_model->find($idPerso);

        if(!$perso){
            return $this->fail([
                'message'=>'personnel not found'
            ]);
        }

        $dataAbsence = [
            'motif'=>$this->request->getVar('motif'),
            'date'=>$this->request->getVar('date'),
            'statut'=>$this->request->getVar('statut'),
            'id_personel'=>$idPerso
        ];

        $AbsenceID = $this->absence_model->insert($dataAbsence);

            $response = [
                "message"=>$AbsenceID? "absence inserted":"absence not inserted",
                "success"=>(bool) $AbsenceID,
                "data"=>array_merge(['id_absence'=>$AbsenceID],$dataAbsence)
            ];

            return $this->respondCreated($response);

    }

    public function updateAbs($idAbs = null){
        helper(['form']);

        $rules = [
            'motif'=>'required|max_length[254]|min_length[2]',
            'date'=>'required|valid_date[Y-m-d]',
            'statut'=>'required|in_list[accepted,modified,cancel]',
            'id_personel'=>'required|integer'
        ];

        if(!$this->validate($rules)){
            $response = [
                'error'=>$this->validator->getErrors(),
                'message'=>'invalid information',
                'success'=>false
            ];

            return $this->fail($response);
        }
        $dataAbsence = [
            'motif'=>$this->request->getVar('motif'),
            'date'=>$this->request->getVar('date'),
            'statut'=>$this->request->getVar('statut'),
            'id_personel'=>$this->request->getVar('id_personel')
        ];

        $AbsenceID = $this->absence_model->update($idAbs,$dataAbsence);

        $response = [
            "message"=>$AbsenceID? "absence updated":"absence not updated",
            "success"=>(bool)$AbsenceID,
            "data"=>array_merge(['id_absence'=>$idAbs],$dataAbsence)
        ];

        return $this->Respond($response);
    }

    public function deleteAbs($idAbs = null){
        $absenceDelete = $this->absence_model->find($idAbs);

        if(!$absenceDelete){
            return $this->failNotFound('absence not found');
        }

        $deleted = $this->absence_model->delete($idAbs);

            $response = [
                "message"=>$deleted? "absence deleted":"absence not deleted",
                "success"=>(bool)$deleted,
                "data" =>['id_absence'=>$idAbs]
            ];

            return $this->respond($response);

    }
}
