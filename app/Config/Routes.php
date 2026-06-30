<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Home::index');

// routes pour le user
    // $routes->group('userr', function($routes){
    //     $routes->get('','UserController::index');
    //     $routes->post('connect', 'UserController::connexion');
    //     $routes->get('(:num)', 'UserController::show/$1');
    //     $routes->post('create', 'UserController::createUser');
    //     $routes->put('update/(:num)','UserController::update/$1');
    // });

    // $routes->group('admin',function($routes){
    //     $routes->get('','AdminController::indexAdmin');
    //     $routes->get('(:num)','AdminController::showAdmin/$1');
    //     $routes->post('creerAdmin','AdminController::createAdmin');
    //     $routes->post('connectAdmin','AdminController::connexionAdmin');
    // });

    // $routes->group('agent',function($routes){
    //     $routes->get('','AgentController::indexAgent');
    //     $routes->get('(:num)','AgentController::showAgent/$1');
    //     $routes->post('creerAgent','AgentController::createAgent');
    //     $routes->post('connectAgent','AgentController::connexionAgent');
    // });

    // $routes->group('client',function($routes){
    //     $routes->get('','ClientController::indexClient');
    //     $routes->get('(:num)','ClientController::showClient/$1');
    //     $routes->post('creerClient','ClientController::createClient');
    //     $routes->post('connectClient','ClientController::connexionClient');
    // });

    // $routes->group('owner',function($routes){
    //     $routes->get('','OwnerController::indexOwner');
    //     $routes->get('(:num)','OwnerController::showOwner/$1');
    //     $routes->post('creerOwner','OwnerController::createOwner');
    //     $routes->post('connectOwner','OwnerController::connexionOwner');
    // });

    // $routes->group('estate',function($routes){
    //     $routes->get('','EstateeController::indexEstate');
    //     $routes->get('(:num)','EstateeController::showEstate/$1');
    //     $routes->post('creerEstate','EstateeController::createEstate');
    // });

    // groupe api ca veut dire que, toutes les routes api seront ici
   // Gère TOUTES les requêtes OPTIONS pour toutes les routes api/*
// $routes->options('api/ping', 'TestController::ping');
// $routes->options('api/(:any)', 'TestController::ping');
//     $routes->group('api',function($routes){
//         $routes->get('ping','TestController::ping');
//     });

    $routes->group('user',function($routes){
        $routes->get('','UserController::index');
        $routes->get('(:num)','UserController::show\$1');
        $routes->post('connect','UserController::connexion');
        $routes->post('create','UserController::createUser');
        $routes->put('update/(:num)','UserController::update/$1');
        $routes->delete('delete/(:num)','UserController::delete/$1');
    });

    $routes->group('perso',function($routes){
        $routes->get('','PersonnelController::indexPerso',
        ['filter'=>'auth']);
        $routes->get('(:num)','PersonnelController::showPerso');
        $routes->post('connect','PersonnelController::connexionPerso');
        $routes->post('create','PersonnelController::createPerso');
        $routes->put('updatePerso/(:num)','PersonnelController::updatePerso/$1');
        $routes->delete('deletePerso/(:num)','PersonnelController::deletePerso/$1');
    });

    $routes->group('funct',function($routes){
        $routes->get('','FunctionController::indexFunction');
        $routes->get('(:num)','FunctionController::showFuncts');
        $routes->post('create','FunctionController::createFunction');
        $routes->post('update/(:num)','FunctionController::updateFunct/$1');
        $routes->post('delete/(:num)','FunctionController::deleteFunct/$1');
    });

    $routes->group('patient',function($routes){
        $routes->get('','PatientController::indexPatient');
        $routes->get('(:num)','PatientController::showPatient/$1');
        $routes->post('create','PatientController::createPatient');
        $routes->put('update/(:num)','PatientController::updatePatient/$1');
        $routes->delete('delete/(:num)','PatientController::deletePatient/$1');
        $routes->get('patientHistory/(:num)','PatientController::getPatientHistory/$1');
    });

    $routes->group('medic',function($routes){
        $routes->get('','MedicalActController::indexMedicalAct');
        $routes->get('(:num)','MedicalActController::showMediActs');
        $routes->post('create','MedicalActController::createMediActs');
        $routes->post('update/(:num)','PatientController::updateMediActs/$1');
        $routes->post('delete/(:num)','PatientController::deleteMediActs/$1');
    });

    $routes->group('vital',function($routes){
        $routes->get('','VitalController::indexVi');
        $routes->get('(:num)','VitalController ::showVi/$1');
        $routes->post('createVitals','VitalController::createVi');
        $routes->post('updateVitals/(:num)','VitalController::updateVi/$1');
        $routes->post('deleteVitals/(:num)','VitalController::deleteVi/$1');
        $routes->get('medicalAct/(:num)','VitalController::getVitalsByMedicalAct/$1');
    });

    $routes->group('consult',function($routes){
        $routes->get('','ConsultationController::indexConsult');
        $routes->get('showConsult/(:num)','ConsultationController::showConsult/$1');
        $routes->post('createConsult','ConsultationController::createConsult');
        $routes->put('updateConsult/(:num)','ConsultationController::updateConsult/$1');
        $routes->delete('deleteConsult/(:num)','ConsultationController::deleteConsult/$1');
    });

    $routes->group('presc',function($routes){
        $routes->get('','PrescribController :: indexPresc');
        $routes->get('showPresc/(:num)','PrescribController :: showPresc/$1');
        $routes->post('createPresc','PrescribController::createPresc');
        $routes->put('updatePresc/(:num)','PrescribController :: updatePresc/$1');
        $routes->delete('deletePresc/(:num)','PrescribController :: deletePresc/$1');
    });

    $routes->group('group',function($routes){
        $routes->get('','GroupController :: indexGroup');
        $routes->get('showGroup/(:num)','GroupController :: showGroup/$1');
        $routes->post('createGroup','GroupController::createGroup');
        $routes->put('updateGroup/(:num)','GroupController :: updateGroup/$1');
        $routes->delete('deleteGroup/(:num)','GroupController :: deleteGroup/$1');
    });

     $routes->group('groupA',function($routes){
        $routes->get('','GroupAffecterController :: indexGroupA');
        $routes->get('showGroupAff/(:num)','GroupAffecterController :: showGroupA/$1');
        $routes->post('createGroupAff','GroupAffecterController::createGroupA');
        $routes->put('updateGroupAff/(:num)','GroupAffecterController::updateGroupA/$1');
        $routes->delete('deleteGroupAff/(:num)','GroupAffecterController::deleteGroupA/$1');
    });

     $routes->group('absence',function($routes){
        $routes->get('','AbsenceController::indexAbs');
        $routes->get('showAbsence/(:num)','AbsenceController::showAbs/$1');
        $routes->post('createAbsence','AbsenceController::createAbs');
        $routes->put('updateAbsence/(:num)','AbsenceController::updateAbs/$1');
        $routes->delete('deleteAbsence/(:num)','AbsenceController::deleteAbs/$1');
    });

    $routes->group('subs',function($routes){
        $routes->get('','SubstitutionController::indexsubs');
        $routes->get('showSubstitution/(:num)','SubstitutionController::showSubs/$1');
        $routes->post('createSubstitution','SubstitutionController::createSubs');
        $routes->put('updateSubstitution/(:num)','SubstitutionController::updateSubs/$1');
        $routes->delete('deleteSubstitution/(:num)','SubstitutionController::deleteSubs/$1');
    });
