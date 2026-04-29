<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Home::index');

// routes pour le user
    $routes->group('userr', function($routes){
        $routes->get('','UserController::index');
        $routes->post('connect', 'UserController::connexion');
        $routes->get('(:num)', 'UserController::show/$1');
        $routes->post('create', 'UserController::createUser');
        $routes->put('update/(:num)','UserController::update/$1');
    });

    $routes->group('admin',function($routes){
        $routes->get('','AdminController::indexAdmin');
        $routes->get('(:num)','AdminController::showAdmin/$1');
        $routes->post('creerAdmin','AdminController::createAdmin');
        $routes->post('connectAdmin','AdminController::connexionAdmin');
    });

    $routes->group('agent',function($routes){
        $routes->get('','AgentController::indexAgent');
        $routes->get('(:num)','AgentController::showAgent/$1');
        $routes->post('creerAgent','AgentController::createAgent');
        $routes->post('connectAgent','AgentController::connexionAgent');
    });

    $routes->group('client',function($routes){
        $routes->get('','ClientController::indexClient');
        $routes->get('(:num)','ClientController::showClient/$1');
        $routes->post('creerClient','ClientController::createClient');
        $routes->post('connectClient','ClientController::connexionClient');
    });

    $routes->group('owner',function($routes){
        $routes->get('','OwnerController::indexOwner');
        $routes->get('(:num)','OwnerController::showOwner/$1');
        $routes->post('creerOwner','OwnerController::createOwner');
        $routes->post('connectOwner','OwnerController::connexionOwner');
    });

    $routes->group('estate',function($routes){
        $routes->get('','EstateeController::indexEstate');
        $routes->get('(:num)','EstateeController::showEstate/$1');
        $routes->post('creerEstate','EstateeController::createEstate');
    });
