<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace iRAP\VidaSDK;

class Client implements apiInterface
{
    
    public function __construct($appAuthID, $appAPIKey, $appPrivateKey)
    {
        define(__NAMESPACE__.'\APP_AUTH_ID', $appAuthID);
        define(__NAMESPACE__.'\APP_API_KEY', $appAPIKey);
        define(__NAMESPACE__.'\APP_PRIVATE_KEY', $appPrivateKey);
        if (defined('IRAPDEV'))
        {
            define(__NAMESPACE__.'\API_URL', 'http://api.irap-dev.org');
        }
        else
        {
            define(__NAMESPACE__.'\API_URL', 'http://api.release.vida.irap.org');
        }
    }
    
    public function getUserToken($email, $password)
    {
        $userToken = Controllers\AuthController::getUserToken($email, $password);
        $token = new \stdClass();
        if ($userToken->code == 200)
        {
            $token->userAuthId = $userToken->response->auth_id;
            $token->userApiKey = $userToken->response->api_key;
            $token->userPrivateKey = $userToken->response->api_secret;
        }
        else
        {
            $token->error = $userToken->error;
        }
        return $token;
    }
    
    public function setUserToken($userAuthID, $userAPIKey, $userPrivateKey)
    {
        Controllers\AuthController::setUserToken($userAuthID, $userAPIKey, $userPrivateKey);
    }
    
    public function getUsers($id = null)
    {
        $userController = new Controllers\UsersController();
        return $userController->getResource('users', $id);
    }
    
    public function addUser($name, $email, $password)
    {
        $userController = new Controllers\UsersController();
        return $userController->postResource('users', array("name"=>$name,"email"=>$email,"password"=>$password));
    }
    
    public function replaceUser($id, $name, $email, $password)
    {
        $userController = new Controllers\UsersController();
        return $userController->putResource('users', $id, array("name"=>$name,"email"=>$email,"password"=>$password));
    }
    
    public function deleteUser($id)
    {
        $userController = new Controllers\UsersController();
        return $userController->deleteResource('users', $id);
    }
    
    public function getDatasets($id = null)
    {
        $datasetController = new Controllers\DatasetsController();
        return $datasetController->getResource('datasets', $id);
    }
    
    public function addDataset($name, $road_data)
    {
        $datasetController = new Controllers\DatasetsController();
        return $datasetController->postResource('datasets', array("name"=>$name, "road-data"=>$road_data));
    }
    
    public function replaceDataset($id, $name, $road_data)
    {
        $datasetController = new Controllers\DatasetsController();
        return $datasetController->putResource('datasets', $id, array("name"=>$name, "road-data"=>$road_data));
    }
    
    public function deleteDataset($id)
    {
        $datasetController = new Controllers\DatasetsController();
        return $datasetController->deleteResource('datasets', $id);
    }
    
    public function getProgrammes($id = null)
    {
        $programmeController = new Controllers\ProgrammesController();
        return $programmeController->getResource('programmes', $id);
    }
    
    public function addProgramme($name, $manager_id)
    {
        $programmeController = new Controllers\ProgrammesController();
        return $programmeController->postResource('programmes', array("name"=>$name, "manager_id"=>$manager_id));
    }
    
    public function replaceProgramme($id, $name, $manager_id)
    {
        $programmeController = new Controllers\ProgrammesController();
        return $programmeController->putResource('programmes', $id, array("name"=>$name, "manager_id"=>$manager_id));
    }
    
    public function deleteProgramme($id)
    {
        $programmeController = new Controllers\ProgrammesController();
        return $programmeController->deleteResource('programmes', $id);
    }
    
    public function getProgrammeUsers($id)
    {
        $programmeController = new Controllers\ProgrammesController();
        return $programmeController->getResourceUserAccess('programmes', $id);
    }
    
    public function getRegions($id = null)
    {
        $regionController = new Controllers\RegionsController();
        return $regionController->getResource('regions', $id);
    }
    
    public function addRegion($name, $programme_id, $manager_id)
    {
        $regionController = new Controllers\RegionsController();
        return $regionController->postResource('regions', array("name"=>$name, "programme_id"=>$programme_id, "manager_id"=>$manager_id));
    }
    
    public function replaceRegion($id, $name, $programme_id, $manager_id)
    {
        $regionController = new Controllers\RegionsController();
        return $regionController->putResource('regions', $id, array("name"=>$name, "programme_id"=>$programme_id, "manager_id"=>$manager_id));
    }
    
    public function deleteRegion($id)
    {
        $regionController = new Controllers\RegionsController();
        return $regionController->deleteResource('regions', $id);
    }
    
    public function getRegionUsers($id)
    {
        $regionController = new Controllers\RegionsController();
        return $regionController->getResourceUserAccess('regions', $id);
    }
    
    public function getProjects($id = null)
    {
        $projectController = new Controllers\ProjectsController();
        return $projectController->getResource('projects', $id);
    }
    
    public function addProject($name, $region_id, $manager_id, $model_id)
    {
        $projectController = new Controllers\ProjectsController();
        return $projectController->postResource('projects', array("name"=>$name, "region_id"=>$region_id, "manager_id"=>$manager_id, "model_id"=>$model_id));
    }
    
    public function replaceProject($id, $name, $region_id, $manager_id, $model_id)
    {
        $projectController = new Controllers\ProjectsController();
        return $projectController->putResource('projects', $id, array("name"=>$name, "region_id"=>$region_id, "manager_id"=>$manager_id, "model_id"=>$model_id));
    }
    
    public function deleteProject($id)
    {
        $projectController = new Controllers\ProjectsController();
        return $projectController->deleteResource('projects', $id);
    }
    
    public function getProjectUsers($id)
    {
        $projectController = new Controllers\ProjectsController();
        return $projectController->getResourceUserAccess('projects', $id);
    }
}