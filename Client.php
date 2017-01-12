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
        if (isset($userToken->response))
        {
            $token->userAuthId = $userToken->response->auth_id;
            $token->userApiKey = $userToken->response->api_key;
            $token->userPrivateKey = $userToken->response->api_secret;
        }
        $token->status = $userToken->status;
        $token->code = $userToken->code;
        if (!empty($userToken->error))
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
    
    public function getDatasetUsers($id)
    {
        $datasetController = new Controllers\DatasetsController();
        return $datasetController->getResource('datasets', $id, 'user-access');
    }
    
    public function getDatasetsForProgramme($id)
    {
        $datasetController = new Controllers\DatasetsController();
        return $datasetController->getResource('datasets', 'for', array('programme', $id));
    }
    
    public function getDatasetsForRegion($id)
    {
        $datasetController = new Controllers\DatasetsController();
        return $datasetController->getResource('datasets', 'for', array('region', $id));
    }
    
    public function getDatasetsForProject($id)
    {
        $datasetController = new Controllers\DatasetsController();
        return $datasetController->getResource('datasets', 'for', array('project', $id));
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
        return $programmeController->getResource('programmes', $id, 'user-access');
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
        return $regionController->getResource('regions', $id, 'user-access');
    }
    
    public function getRegionsForProgramme($id)
    {
        $regionController = new Controllers\RegionsController();
        return $regionController->getResource('regions', 'for', array('programme', $id));
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
        return $projectController->getResource('projects', $id, 'user-access');
    }
    
    public function getProjectsForProgramme($id)
    {
        $projectController = new Controllers\ProjectsController();
        return $projectController->getResource('projects', 'for', array('programme', $id));
    }
    
    public function getProjectsForRegion($id)
    {
        $projectController = new Controllers\ProjectsController();
        return $projectController->getResource('projects', 'for', array('region', $id));
    }
    
    public function getVariables($id = null)
    {
        $variableController = new Controllers\VariablesController();
        return $variableController->getResource('variables', $id);
    }
    
    public function addVariable($variables)
    {
        $variableController = new Controllers\VariablesController();
        return $variableController->postResource('variables', $variables);
    }
    
    public function replaceVariable($id, $variables)
    {
        $variableController = new Controllers\VariablesController();
        return $variableController->putResource('variables', $id, $variables);
    }
    
    public function deleteVariable($id)
    {
        $variableController = new Controllers\VariablesController();
        return $variableController->deleteResource('variables', $id);
    }
    
    public function getVariablesForDataset($id)
    {
        $variableController = new Controllers\VariablesController();
        return $variableController->getResource('variables', 'for', array('dataset', $id));
    }
    
    public function getRoadAttributes($id, $dataset_id)
    {
        $roadAttributeController = new Controllers\RoadAttributesController();
        return $roadAttributeController->getResource('roadAttributes', $id, $dataset_id);
    }
    
    public function addRoadAttribute($roadAttributes, $dataset_id)
    {
        $roadAttributeController = new Controllers\RoadAttributesController();
        return $roadAttributeController->postResource('roadAttributes', $roadAttributes, $dataset_id);
    }
    
    public function replaceRoadAttribute($id, $roadAttributes, $dataset_id)
    {
        $roadAttributeController = new Controllers\RoadAttributesController();
        return $roadAttributeController->putResource('roadAttributes', $id, $roadAttributes, $dataset_id);
    }
    
    public function deleteRoadAttribute($id, $dataset_id)
    {
        $roadAttributeController = new Controllers\RoadAttributesController();
        return $roadAttributeController->deleteResource('roadAttributes', $id, $dataset_id);
    }
    
    public function getRoadAttributesForProgramme($id)
    {
        $roadAttributeController = new Controllers\RoadAttributesController();
        return $roadAttributeController->getResource('roadAttributes', 'for', array('programme', $id));
    }
    
    public function getRoadAttributesForRegion($id)
    {
        $roadAttributeController = new Controllers\RoadAttributesController();
        return $roadAttributeController->getResource('roadAttributes', 'for', array('region', $id));
    }
    
    public function getRoadAttributesForProject($id)
    {
        $roadAttributeController = new Controllers\RoadAttributesController();
        return $roadAttributeController->getResource('roadAttributes', 'for', array('project', $id));
    }
    
    public function getRoadAttributesForDataset($id)
    {
        $roadAttributeController = new Controllers\RoadAttributesController();
        return $roadAttributeController->getResource('roadAttributes', 'for', array('dataset', $id));
    }
    
    public function getFatalities($id, $dataset_id)
    {
        $fatalitiesController = new Controllers\FatalitiesController();
        return $fatalitiesController->getResource('fatalities', $id, $dataset_id);
    }
    
    public function addFatalities($fatalities, $dataset_id)
    {
        $fatalitiesController = new Controllers\FatalitiesController();
        return $fatalitiesController->postResource('fatalities', $fatalities, $dataset_id);
    }
    
    public function replaceFatalities($id, $fatalities, $dataset_id)
    {
        $fatalitiesController = new Controllers\FatalitiesController();
        return $fatalitiesController->putResource('fatalities', $id, $fatalities, $dataset_id);
    }
    
    public function deleteFatalities($id, $dataset_id)
    {
        $fatalitiesController = new Controllers\FatalitiesController();
        return $fatalitiesController->deleteResource('fatalities', $id, $dataset_id);
    }
    
    public function getFatalitiesForProgramme($id)
    {
        $fatalitiesController = new Controllers\FatalitiesController();
        return $fatalitiesController->getResource('fatalities', 'for', array('programme', $id));
    }
    
    public function getFatalitiesForRegion($id)
    {
        $fatalitiesController = new Controllers\FatalitiesController();
        return $fatalitiesController->getResource('fatalities', 'for', array('region', $id));
    }
    
    public function getFatalitiesForProject($id)
    {
        $fatalitiesController = new Controllers\FatalitiesController();
        return $fatalitiesController->getResource('fatalities', 'for', array('project', $id));
    }
    
    public function getFatalitiesForDataset($id)
    {
        $fatalitiesController = new Controllers\FatalitiesController();
        return $fatalitiesController->getResource('fatalities', 'for', array('dataset', $id));
    }
    
    public function getBeforeStarRatings($id, $dataset_id)
    {
        $starRatingController = new Controllers\StarRatingsController();
        return $starRatingController->getResource('starratings', $id, array('before', $dataset_id));
    }
    
    public function addBeforeStarRating($starratings, $dataset_id)
    {
        $starRatingController = new Controllers\StarRatingsController();
        return $starRatingController->postResource('starratings', $starratings, array('before', $dataset_id));
    }
    
    public function replaceBeforeStarRating($id, $starratings, $dataset_id)
    {
        $starRatingController = new Controllers\StarRatingsController();
        return $starRatingController->putResource('starratings', $id, $starratings, array('before', $dataset_id));
    }
    
    public function deleteBeforeStarRating($id, $dataset_id)
    {
        $starRatingController = new Controllers\StarRatingsController();
        return $starRatingController->deleteResource('starratings', $id, array('before', $dataset_id));
    }
    
    public function getBeforeStarRatingsForProgramme($id)
    {
        $starRatingController = new Controllers\StarRatingsController();
        return $starRatingController->getResource('starratings', 'for', array('programme', $id, 'before'));
    }
    
    public function getBeforeStarRatingsForRegion($id)
    {
        $starRatingController = new Controllers\StarRatingsController();
        return $starRatingController->getResource('starratings', 'for', array('region', $id, 'before'));
    }
    
    public function getBeforeStarRatingsForProject($id)
    {
        $starRatingController = new Controllers\StarRatingsController();
        return $starRatingController->getResource('starratings', 'for', array('project', $id, 'before'));
    }
    
    public function getBeforeStarRatingsForDataset($id)
    {
        $starRatingController = new Controllers\StarRatingsController();
        return $starRatingController->getResource('starratings', 'for', array('dataset', $id, 'before'));
    }
    
    public function getAfterStarRatings($id, $dataset_id)
    {
        $starRatingController = new Controllers\StarRatingsController();
        return $starRatingController->getResource('starratings', $id, array('after', $dataset_id));
    }
    
    public function addAfterStarRating($starratings, $dataset_id)
    {
        $starRatingController = new Controllers\StarRatingsController();
        return $starRatingController->postResource('starratings', $starratings, array('after', $dataset_id));
    }
    
    public function replaceAfterStarRating($id, $starratings, $dataset_id)
    {
        $starRatingController = new Controllers\StarRatingsController();
        return $starRatingController->putResource('starratings', $id, $starratings, array('after', $dataset_id));
    }
    
    public function deleteAfterStarRating($id, $dataset_id)
    {
        $starRatingController = new Controllers\StarRatingsController();
        return $starRatingController->deleteResource('starratings', $id, array('after', $dataset_id));
    }
    
    public function getAfterStarRatingsForProgramme($id)
    {
        $starRatingController = new Controllers\StarRatingsController();
        return $starRatingController->getResource('starratings', 'for', array('programme', $id, 'after'));
    }
    
    public function getAfterStarRatingsForRegion($id)
    {
        $starRatingController = new Controllers\StarRatingsController();
        return $starRatingController->getResource('starratings', 'for', array('region', $id, 'after'));
    }
    
    public function getAfterStarRatingsForProject($id)
    {
        $starRatingController = new Controllers\StarRatingsController();
        return $starRatingController->getResource('starratings', 'for', array('project', $id, 'after'));
    }
    
    public function getAfterStarRatingsForDataset($id)
    {
        $starRatingController = new Controllers\StarRatingsController();
        return $starRatingController->getResource('starratings', 'for', array('dataset', $id, 'after'));
    }
}