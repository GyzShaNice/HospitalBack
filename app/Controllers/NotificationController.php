<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\API\ResponseTrait;
use App\Models\NotificationModel;
use App\Models\Personnel;
use App\Models\FunctionnModel;
use App\Models\NotiPerso;
use Firebase\JWT\JWT;
use Firebase\JWT\key;

class NotificationController extends BaseController
{
    use ResponseTrait;
    protected $Notif;
    protected $Notiperso;
    protected $perso_model;
    protected $funct_model;

    private const ROLES_CONCERNES = ['Major','HeadNurse','doctor','nurse'];
    private const ROLES_AUTORISES_A_ENVOYER = ['Major','HeadNurse'];

    public function __construct(){
        $this->Notif = new NotificationModel();
        $this->perso_model = new Personnel();
        $this->funct_model = new FunctionnModel();
        $this->Notiperso = new  NotiPerso();
    }

    private function getAuthenticatedPersonnelId():?int{
        $header = $this->request->getHeaderLine('Authorization');

            if(empty($header)||!preg_match('/Bearer\s(\S+)/',$header,$matches)){
                return null;
            }

            try{
                $decoded = JWT::decode($matches[1],new key(getenv('JWT_SECRET'),'HS256'));
                    return isset($decoded->data->id_personel)?(int) $decoded->data->id_personel : null;

            }catch(\Exception $e){
                return null;
            }
    }

    public function send()
    {
        // ont recupere les donnee json envoyee par le frontend
        $data = $this->request->getJSON(true);

        // ont recupere l'id du personnel connecte(injecte par ton AuthFilter)
        $idExpediteur = $this->getAuthenticatedPersonnelId();

        // va chercher le personnel connecter

        if(!$idExpediteur){
            // si aucun personnel avec cet id n'existe on envoie une erreru
            return $this->failUnauthorized('Unauthorized');
        }

        $expediteur = $this->perso_model->find($idExpediteur);


        if(!$expediteur){
            return $this->failNotFound("Expediteur introuvable");
        }

        // $expediteur['id_function'] est un numero
        $fonctionExpediteur = $this->funct_model->find($expediteur['id_function']);

        if(!$fonctionExpediteur){
            return $this->failNotFound("fonction de l'expediteur introuvablee");

        }
        $nomFonctionExpediteur = $fonctionExpediteur['name'];

        // ont verifie que le nom fait partie des roles autorise
        if(!in_array($nomFonctionExpediteur,self::ROLES_AUTORISES_A_ENVOYER)){
            return $this->failForbidden("vous n'avez pas le droit d'envoyer");
        }

        // on enleve le role de l'expediteur de la liste : il ne s'envoie pas a lui meme
        // si nomFonctionExpediteur = "HeadNurse"-> resultat = ['Major','doctor','nurse']

        $fonctionsDestinataires = array_filter(
            self::ROLES_CONCERNES,
            fn($nom)=>$nom !== $nomFonctionExpediteur
        );

        // convertit les NOMS et roles restants('Major','doctor','nurse') en leurs NUMEROS id_function

        $idsFunctionDestinataires = $this->funct_model->whereIn('name',$fonctionsDestinataires)
                                          ->findColumn('id_function');

        if(empty($idsFunctionDestinataires)){
            // securite : si aucun id_function trouve
            return $this->failNotFound("aucune fonction");
        }   
        
        $idsPersonnel = $this->perso_model->whereIn('id_function',$idsFunctionDestinataires)
                                    ->findColumn('id_personel');
        
       if(empty($idsPersonnel)){
        // securite:si personne ne correspond -> on arrete avant de creer un notif inutile
            return $this->failNotFound("aucun personnel destinataire trouve");
       }  
       
    //    on cree une seul ligne dans la table notification
    // ont ecrit une seul fois,peu importe le nombre de destinataire
       $idNotif = $this->Notif->insert([
            'id_expediteur'=>$idExpediteur,
            // ?? veut dire que, utilise data['titre'] si le front la envoyer, sinon utilise ce texte par defaut
            'titre'=>$data['titre']??'Nouvel emploie du temps publie',
            'message'=>$data['message']??"lemploie du temps a ete mis a jour"

       ],true);
    //    le "true" dit a insert() de nous renvoyer l'id notif qui vient d'etre generer automatiquement


    // on boucle sur chaque personnel destinataire trouve plus haut
    // pour chacun,on cree une ligne dans notifperso qui dit:
    // cet notification (id_notif) est destinee a cette personne (id_personnel),pas envore lue
       foreach($idsPersonnel as $idPersonnel){
        $this->Notiperso->insert([
            'id_notif'=>$idNotif,
            'id_personel'=>$idPersonnel,
            'is_read'=>0,
        ]);
       }

       return $this->respond([
            'status'=>'success',
            'destinataires'=>count($idsPersonnel),
       ],200);
    }


    // quel notification dois-je afficher pour la personne actuellement connectee
    public function notifRecu(){
        // id du personnel connecter(le nurse/doctor qui ouvre l'app)
        $idPersonnel = $this->getAuthenticatedPersonnelId();

        if (!$idPersonnel) {
            return $this->failUnauthorized('Unauthorized');
        }

        $notifs = $this->Notiperso
            // on veux voir toutes les colonnes de notification(titre,message,date)
            // plus deux colonnes specifique de notiperso : id_notiperso et is_read

                        ->select('notification.*,notiperso.id_notiPerso,notiperso.is_read')
                        ->join('notification','notification.id_notif = notiperso.id_notif')
                        ->where('notiperso.id_personel',$idPersonnel)
                        ->orderBy('notification.created_at','DESC')
                        ->findAll();

                        return $this->respond($notifs,200);
    }
}
