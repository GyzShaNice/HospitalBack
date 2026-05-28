<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\API\ResponseTrait;
use App\Models\SubstitutionModel;
use App\Models\AbsenceModel;
use App\Models\Personnel;

class SubstitutionController extends BaseController
{
    use ResponseTrait;

   protected $absence_model;
   protected $subs_model;
   protected $perso_model;

    public function __construct(){
        $this->subs_model = new SubstitutionModel();
        $this->absence_model = new AbsenceModel();
        $this->perso_model = new Personnel();
    }
    public function indexsubs()
    {
        $subs = $this->subs_model
                     ->select('substitution.id_subs,substitution.date,
                            substitution.statut,personel.id_personel
                                ,absence.id_absence')
                     ->join('absence','substitution.id_absence = absence.id_absence')
                     ->join('personel','substitution.id_personel = personel.id_personel')
                     ->findAll();
                     
        $response = [
            "message"=>count($subs)>0? "subs found":"subs not found",
            "success"=>count($subs),
            "data"=>$subs
        ];
        
        return $this->respond($response);
    }

    public function showsubs($idSubs = null){
        $subs = $this->subs_model
                     ->select('substitution.id_subs,substitution.date,
                            substitution.statut,personel.id_personel
                                ,absence.id_absence')
                     ->join('absence','substitution.id_absence = absence.id_absence')
                     ->join('personel','substitution.id_personel = personel.id_personel')
                     ->find($idSubs);
                     
        $response = [
            "message"=>count($subs)>0? "subs found":"subs not found",
            "success"=>count($subs),
            "data"=>$subs
        ];
        
        return $this->respond($response);
    }

    public function createSubs(){
        helper(['form']);

        $rules = [
            'date'=>'required|valid_date[Y-m-d]',
            'statut'=>'required|in_list[accepted,modified,cancel]',
            'id_personel'=>'required|integer',
            'id_absence'=>'required|integer'
        ];

        if(!$this->validate($rules)){
            $response = [
                'error'=>$this->validator->getErrors(),
                'message'=>'invalid information',
                'success'=>false
            ];

            return $this->Fail($response);
        }

        $idPerso = $this->request->getVar('id_personel');

            $perso = $this->perso_model->find($idPerso);

            if(!$perso){
                return $this->fail([
                    'message'=>'personnel not found'
                ]);
            }
        $idAbs = $this->request->getVar('id_absence');

        $abse = $this->absence_model->find($idAbs);
            if(!$abse){
                return $this->fail([
                    'message'=>'absence not found'
                ]);
            }

            $dataSubs = [
                'date'=>$this->request->getVar('date'),
                'statut'=>$this->request->getVar('statut'),
                'id_personel'=>$idPerso,
                'id_absence'=>$idAbs
            ];

            $Subst = $this->subs_model->insert($dataSubs);

            $response = [
                "message"=>$Subst? "subs found":"subs not found",
                "success"=>(bool) $Subst,
                "data"=>array_merge(['id_subs'=>$Subst],$dataSubs)
            ];

            return $this->respondCreated($response);
    } 
     
     public function updateSubs($idSubs = null){

            helper(['form']);

            $rules = [
                'date'=>'required|valid_date[Y-m-d]',
                'statut'=>'required|in_list[accepted,modified,cancel]',
                'id_personel'=>'required|integer',
                'id_absence'=>'required|integer'
            ];

            if(!$this->validate($rules)){
                $response = [
                    'error'=>$this->validator->getErrors(),
                    'message'=>'invalid information',
                    'success'=>false

                ];

                return $this->fail($response);
            }

            $dataSubs = [
                'date'=>$this->request->getVar('date'),
                'statut'=>$this->request->getVar('statut'),
                'id_personel'=>$this->request->getVar('id_personel'),
                'id_absence'=>$this->request->getVar('id_absence')
            ];

            $SubsID = $this->subs_model->updaate($idSubs,$dataSubs);

            $response = [
                "message"=>$SubsID? "substitution updated":"substitution not updated",
                "success"=>(bool)$SubsID,
                "data"=>array_merge(['id_subs'=>$idSubs],$dataSubs)
            ];

            return $this->respond($response);
     }

     public function deleteSubs($idSubs = null){
        $subsDelete = $this->subs_model->find($idSubs);

        if(!$subsDelete){
            return $this->failNotFound('substitution not found');
        }

        $deleted = $this->subs_model->delete($idSubs);

        $response = [
            "message"=>$deleted? "substitution deleted":"substitution not deleted",
            "success"=>(bool)$deleted,
            "data"=>['id_subs'=>$idSubs]
        ];

        return $this->respond($response);
     }
}
