<?php

/* 
 * Welcome to the ViDA SDK. This is the primary class for users of the SDK and contains all of the
 * methods intended for use by developers. For help in understanding how to use the SDK, first 
 * look at the README.md file, then read the comments on each of the methods listed below.
 * 
 * If you require further help, or wish to report a bug fix, please email support@irap.org
 * 
 */

namespace iRAP\VidaSDK\Controllers;

abstract class AbstractApiController implements ApiInterface
{
     
    abstract protected function getAuth();
    
    /**
     ******* This method requires special permission from iRAP and is not available to all ********
     * 
     * Takes the user's email address and password and returns the user authentication token needed
     * to complete all future requests made on behalf of that user. The email address and password
     * should not be stored in your app as they are no longer needed. The returned token can be used
     * by calling the setUserToken() method below, and should be stored for future use, to avoid 
     * having to ask the user to sign in again.
     * 
     * @param string $email
     * @param string $password
     * @return \stdClass
     */
    public function getUserToken($email, $password)
    {
        $userToken = AuthController::getUserToken($this->getAuth(), $email, $password);
        $token = new \stdClass();
        if (isset($userToken->response))
        {
            $token->userAuthId = $userToken->response->auth_id;
            $token->userApiKey = $userToken->response->api_key;
            $token->userPrivateKey = $userToken->response->api_secret;
            $token->userID = $userToken->response->user_id;
        }
        $token->status = $userToken->status;
        $token->code = $userToken->code;
        if (!empty($userToken->error))
        {
            $token->error = $userToken->error;
        }
        return $token;
    }
    
    /**
     * Fetches a list of all of the users in the system. If you specify an ID, that user will be
     * returned to you.
     * 
     * @param int $id
     * @return object
     */
    public function getUsers($id = null, $filter = null)
    {
        $userController = new UsersController($this->getAuth(), $filter);
        return $userController->getResource($id);
    }
    
    /**
     * Add a new user to the system by supplying their name, email address and a password.
     * 
     * @param string $name
     * @param string $email
     * @param string $password
     * @return object
     */
    public function addUser($name, $email, $password)
    {
        $userController = new UsersController($this->getAuth());
        return $userController->postResource(array("name"=>$name,"email"=>$email,"password"=>$password));
    }
    
    /**
     * Update a user in the system by supplying their user id, along with a new name, email address
     * and password.
     * 
     * @param int $id
     * @param string $name
     * @param string $email
     * @param string $password
     * @return object
     */
    public function updateUser($id, $name, $email, $password)
    {
        $userController = new UsersController($this->getAuth());
        return $userController->patchResource($id, array("name"=>$name,"email"=>$email,"password"=>$password));
    }
    
    /**
     * Replace a user in the system by supplying their user id, along with a new name, email address
     * and password.
     * 
     * @param int $id
     * @param string $name
     * @param string $email
     * @param string $password
     * @return object
     */
    public function replaceUser($id, $name, $email, $password)
    {
        $userController = new UsersController($this->getAuth());
        return $userController->putResource($id, array("name"=>$name,"email"=>$email,"password"=>$password));
    }
    
    /**
     * Delete a user from the system, using their user id.
     * 
     * @param int $id
     * @return object
     */
    public function deleteUser($id)
    {
        $userController = new UsersController($this->getAuth());
        return $userController->deleteResource($id);
    }
    
    /**
     * Fetches a list of all of the datasets in the system. If you specify an ID, that dataset will
     * be returned to you.
     * 
     * @param int $id
     * @return object
     */
    public function getDatasets($id = null, $filter = null)
    {
        $datasetController = new DatasetsController($this->getAuth(), $filter);
        return $datasetController->getResource($id);
    }
    
    /**
     * Creates a new dataset using the supplied data, which should be an array of field name as 
     * keys and the values you wish to insert, as name-value pairs
     * 
     * @param string $name
     * @param array $road_data
     * @return object
     */
    public function addDataset($name, $project_id, $manager_id)
    {
        $datasetController = new DatasetsController($this->getAuth());
        return $datasetController->postResource(array("name"=>$name, "project_id"=>$project_id, "manager_id"=>$manager_id));
    }
    
    /**
     * Updates a dataset using the supplied data, which should be an array of field name as 
     * keys and the values you wish to insert, as name-value pairs. The ID of the dataset to update
     * and a new name should also be supplied
     * 
     * @param int $id
     * @param string $name
     * @param array $road_data
     * @return object
     */
    public function updateDataset($id, $name, $project_id, $manager_id)
    {
        $datasetController = new DatasetsController($this->getAuth());
        return $datasetController->patchResource($id, array("name"=>$name, "project_id"=>$project_id, "manager_id"=>$manager_id));
    }
    
    /**
     * Replaces a dataset using the supplied data, which should be an array of field name as 
     * keys and the values you wish to insert, as name-value pairs. The ID of the dataset to replace
     * and a new name should also be supplied
     * 
     * @param int $id
     * @param string $name
     * @param array $road_data
     * @return object
     */
    public function replaceDataset($id, $name, $project_id, $manager_id)
    {
        $datasetController = new DatasetsController($this->getAuth());
        return $datasetController->putResource($id, array("name"=>$name, "project_id"=>$project_id, "manager_id"=>$manager_id));
    }
    
    /**
     * Deletes a dataset from the system, using the dataset's ID.
     * 
     * @param int $id
     * @return object
     */
    public function deleteDataset($id)
    {
        $datasetController = new DatasetsController($this->getAuth());
        return $datasetController->deleteResource($id);
    }
    
    /**
     * Get a list of the users who have access to a dataset, using the dataset's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getDatasetUsers($id, $filter = null)
    {
        $datasetController = new DatasetsController($this->getAuth(), $filter);
        return $datasetController->getResource($id, 'user-access');
    }
    
    /**
     * Grant access to the specified user for the specified dataset
     * 
     * @param int $dataset_id
     * @param int $user_id
     * @param int $access_level
     * @param int $user_manager
     * @return object
     */
    public function addDatasetUser($dataset_id, $user_id, $access_level = 1, $user_manager = 0)
    {
        $datasetController = new DatasetsController($this->getAuth());
        return $datasetController->postResource(array('user_id'=>$user_id, 'access_level'=>$access_level, 'user_manager'=>$user_manager), $dataset_id, 'user-access');
    }
    
    /**
     * Revokes access for the specified user for the specified dataset
     * 
     * @param int $dataset_id
     * @param int $user_id
     * @return object
     */
    public function deleteDatasetUser($dataset_id, $user_id)
    {
        $datasetController = new DatasetsController($this->getAuth());
        return $datasetController->deleteResource($dataset_id, array('user-access',$user_id));
    }
    
    /**
     * Get a list of datasets for a programme, using the programme's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getDatasetsForProgramme($id, $filter = null)
    {
        $datasetController = new DatasetsController($this->getAuth(), $filter);
        return $datasetController->getResource('for', array('programme', $id));
    }
    
    /**
     * Get a list of datasets for a region, using the region's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getDatasetsForRegion($id, $filter = null)
    {
        $datasetController = new DatasetsController($this->getAuth(), $filter);
        return $datasetController->getResource('for', array('region', $id));
    }
    
    /**
     * Get a list of datasets for a project, using the project's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getDatasetsForProject($id, $filter = null)
    {
        $datasetController = new DatasetsController($this->getAuth(), $filter);
        return $datasetController->getResource('for', array('project', $id));
    }
    
    /**
     * Start processing the specified dataset. Processing data is added to a queue and a successful
     * response to this request means that the dataset has been added to the queue, not that
     * processing is complete. To check whether it has finished, call getDataset($id) and examine
     * the returned is_data_processing property.
     * 
     * @param int $id
     * @return object
     */
    public function processDataset($id, $filter = null)
    {
        $datasetController = new DatasetsController($this->getAuth(), $filter);
        return $datasetController->getResource($id, 'process');
    }
    
    /**
     * Fetches a list of all of the programmes in the system. If you specify an ID, that programme
     * will be returned to you.
     * 
     * @param int $id
     * @return object
     */
    public function getProgrammes($id = null, $filter = null)
    {
        $programmeController = new ProgrammesController($this->getAuth(), $filter);
        return $programmeController->getResource($id);
    }
    
    /**
     * Creates a new programme, for which a name should be supplied, along with the user id of 
     * the programme's manager.
     * 
     * @param string $name
     * @param int $manager_id
     * @return object
     */
    public function addProgramme($name, $manager_id)
    {
        $programmeController = new ProgrammesController($this->getAuth());
        return $programmeController->postResource(array("name"=>$name, "manager_id"=>$manager_id));
    }
    
    /**
     * Updates a programme, for which a new name should be supplied, along with the id of
     * the programme, and the user id of the programme's manager.
     * 
     * @param int $id
     * @param string $name
     * @param int $manager_id
     * @return object
     */
    public function updateProgramme($id, $name, $manager_id)
    {
        $programmeController = new ProgrammesController($this->getAuth());
        return $programmeController->patchResource($id, array("name"=>$name, "manager_id"=>$manager_id));
    }
    
    /**
     * Replaces a programme, for which a new name should be supplied, along with the id of
     * the programme, and the user id of the programme's manager.
     * 
     * @param int $id
     * @param string $name
     * @param int $manager_id
     * @return object
     */
    public function replaceProgramme($id, $name, $manager_id)
    {
        $programmeController = new ProgrammesController($this->getAuth());
        return $programmeController->putResource($id, array("name"=>$name, "manager_id"=>$manager_id));
    }
    
    /**
     * Deletes a programme from the system, using the programme's ID.
     * 
     * @param int $id
     * @return object
     */
    public function deleteProgramme($id)
    {
        $programmeController = new ProgrammesController($this->getAuth());
        return $programmeController->deleteResource($id);
    }
    
    /**
     * Get a list of the users who have access to a programme, using the programme's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getProgrammeUsers($id, $filter = null)
    {
        $programmeController = new ProgrammesController($this->getAuth(), $filter);
        return $programmeController->getResource($id, 'user-access');
    }
    
    /**
     * Grant access to the specified user for the specified programme
     * 
     * @param int $programme_id
     * @param int $user_id
     * @param int $access_level
     * @param int $user_manager
     * @return object
     */
    public function addProgrammeUser($programme_id, $user_id, $access_level = 1, $user_manager = 0)
    {
        $programmeController = new ProgrammesController($this->getAuth());
        return $programmeController->postResource(array('user_id'=>$user_id, 'access_level'=>$access_level, 'user_manager'=>$user_manager), $programme_id, 'user-access');
    }
    
    /**
     * Revokes access for the specified user for the specified programme
     * 
     * @param int $programme_id
     * @param int $user_id
     * @return object
     */
    public function deleteProgrammeUser($programme_id, $user_id)
    {
        $programmeController = new ProgrammesController($this->getAuth());
        return $programmeController->deleteResource($programme_id, array('user-access',$user_id));
    }
    
    /**
     * Fetches a list of all of the regions in the system. If you specify an ID, that region will be
     * returned to you.
     * 
     * @param int $id
     * @return object
     */
    public function getRegions($id = null, $filter = null)
    {
        $regionController = new RegionsController($this->getAuth(), $filter);
        return $regionController->getResource($id);
    }
    
    /**
     * Creates a new region, for which a name should be supplied, along with the id of the parent
     * programme and the user id of the region's manager.
     * 
     * @param string $name
     * @param int $programme_id
     * @param int $manager_id
     * @return object
     */
    public function addRegion($name, $programme_id, $manager_id)
    {
        $regionController = new RegionsController($this->getAuth());
        return $regionController->postResource(array("name"=>$name, "programme_id"=>$programme_id, "manager_id"=>$manager_id));
    }
    
    /**
     * Updates a region, for which a new name should be supplied, along with the id of the 
     * region, the id of the parent programme and the user id of the region's manager.
     * 
     * @param int $id
     * @param string $name
     * @param int $programme_id
     * @param int $manager_id
     * @return object
     */
    public function updateRegion($id, $name, $programme_id, $manager_id)
    {
        $regionController = new RegionsController($this->getAuth());
        return $regionController->patchResource($id, array("name"=>$name, "programme_id"=>$programme_id, "manager_id"=>$manager_id));
    }
    
    /**
     * Replaces a region, for which a new name should be supplied, along with the id of the 
     * region, the id of the parent programme and the user id of the region's manager.
     * 
     * @param int $id
     * @param string $name
     * @param int $programme_id
     * @param int $manager_id
     * @return object
     */
    public function replaceRegion($id, $name, $programme_id, $manager_id)
    {
        $regionController = new RegionsController($this->getAuth());
        return $regionController->putResource($id, array("name"=>$name, "programme_id"=>$programme_id, "manager_id"=>$manager_id));
    }
    
    /**
     * Deletes a region from the system, using the region's ID.
     * 
     * @param int $id
     * @return object
     */
    public function deleteRegion($id)
    {
        $regionController = new RegionsController($this->getAuth());
        return $regionController->deleteResource($id);
    }
    
    /**
     * Get a list of the users who have access to a region, using the region's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getRegionUsers($id, $filter = null)
    {
        $regionController = new RegionsController($this->getAuth(), $filter);
        return $regionController->getResource($id, 'user-access');
    }
    
    /**
     * Grant access to the specified user for the specified region
     * 
     * @param int $region_id
     * @param int $user_id
     * @param int $access_level
     * @param int $user_manager
     * @return object
     */
    public function addRegionUser($region_id, $user_id, $access_level = 1, $user_manager = 0)
    {
        $regionController = new RegionsController($this->getAuth());
        return $regionController->postResource(array('user_id'=>$user_id, 'access_level'=>$access_level, 'user_manager'=>$user_manager), $region_id, 'user-access');
    }
    
    /**
     * Revokes access for the specified user for the specified region
     * 
     * @param int $region_id
     * @param int $user_id
     * @return object
     */
    public function deleteRegionUser($region_id, $user_id)
    {
        $regionController = new RegionsController($this->getAuth());
        return $regionController->deleteResource($region_id, array('user-access',$user_id));
    }
    
    /**
     * Get a list of regions for a programme, using the programme's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getRegionsForProgramme($id, $filter = null)
    {
        $regionController = new RegionsController($this->getAuth(), $filter);
        return $regionController->getResource('for', array('programme', $id));
    }
    
    /**
     * Fetches a list of all of the projects in the system. If you specify an ID, that project will
     * be returned to you.
     * 
     * @param int $id
     * @return object
     */
    public function getProjects($id = null, $filter = null)
    {
        $projectController = new ProjectsController($this->getAuth(), $filter);
        return $projectController->getResource($id);
    }
    
    /**
     * Creates a new project, for which a name should be supplied, along with the id of the parent
     * region, the user id of the project's manager and the id of the model to be used.
     * 
     * @param string $name
     * @param int $region_id
     * @param int $manager_id
     * @param int $model_id
     * @param int $country_id
     * @return object
     */
    public function addProject($name, $region_id, $manager_id, $model_id, $country_id)
    {
        $projectController = new ProjectsController($this->getAuth());
        return $projectController->postResource(array("name"=>$name, "region_id"=>$region_id, "manager_id"=>$manager_id, "model_id"=>$model_id, "country_id"=>$country_id));
    }
    
    /**
     * Updates a project, for which a name should be supplied, along with the id of the 
     * project, the id of the parent region, the user id of the project's manager and the id
     * of the model to be used.
     * 
     * @param int $id
     * @param string $name
     * @param int $region_id
     * @param int $manager_id
     * @param int $model_id
     * @param int $country_id
     * @return object
     */
    public function updateProject($id, $name, $region_id, $manager_id, $model_id, $country_id)
    {
        $projectController = new ProjectsController($this->getAuth());
        return $projectController->patchResource($id, array("name"=>$name, "region_id"=>$region_id, "manager_id"=>$manager_id, "model_id"=>$model_id, "country_id"=>$country_id));
    }
    
    /**
     * Replaces a project, for which a name should be supplied, along with the id of the 
     * project, the id of the parent region, the user id of the project's manager and the id
     * of the model to be used.
     * 
     * @param int $id
     * @param string $name
     * @param int $region_id
     * @param int $manager_id
     * @param int $model_id
     * @param int $country_id
     * @return object
     */
    public function replaceProject($id, $name, $region_id, $manager_id, $model_id, $country_id)
    {
        $projectController = new ProjectsController($this->getAuth());
        return $projectController->putResource($id, array("name"=>$name, "region_id"=>$region_id, "manager_id"=>$manager_id, "model_id"=>$model_id, "country_id"=>$country_id));
    }
    
    /**
     * Deletes a project from the system, using the project's ID.
     * 
     * @param int $id
     * @return object
     */
    public function deleteProject($id)
    {
        $projectController = new ProjectsController($this->getAuth());
        return $projectController->deleteResource($id);
    }
    
    /**
     * Grant access to the specified user for the specified project
     * 
     * @param int $project_id
     * @param int $user_id
     * @param int $access_level
     * @param int $user_manager
     * @return object
     */
    public function addProjectUser($project_id, $user_id, $access_level = 1, $user_manager = 0)
    {
        $projectController = new ProjectsController($this->getAuth());
        return $projectController->postResource(array('user_id'=>$user_id, 'access_level'=>$access_level, 'user_manager'=>$user_manager), $project_id, 'user-access');
    }
    
    /**
     * Revokes access for the specified user for the specified project
     * 
     * @param int $project_id
     * @param int $user_id
     * @return object
     */
    public function deleteProjectUser($project_id, $user_id)
    {
        $projectController = new ProjectsController($this->getAuth());
        return $projectController->deleteResource($project_id, array('user-access',$user_id));
    }
    
    /**
     * Get a list of the users who have access to a project, using the project's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getProjectUsers($id, $filter = null)
    {
        $projectController = new ProjectsController($this->getAuth(), $filter);
        return $projectController->getResource($id, 'user-access');
    }
    
    /**
     * Get a list of projects for a programme, using the programme's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getProjectsForProgramme($id, $filter = null)
    {
        $projectController = new ProjectsController($this->getAuth(), $filter);
        return $projectController->getResource('for', array('programme', $id));
    }
    
    /**
     * Get a list of projects for a regions, using the regions's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getProjectsForRegion($id, $filter = null)
    {
        $projectController = new ProjectsController($this->getAuth(), $filter);
        return $projectController->getResource('for', array('region', $id));
    }
    
    /**
     * Fetches a list of all of the variables in the system. If you specify an ID, that variable will be
     * returned to you.
     * 
     * @param int $id
     * @return object
     */
    public function getVariables($id = null, $filter = null)
    {
        $variableController = new VariablesController($this->getAuth(), $filter);
        return $variableController->getResource($id);
    }
    
    /**
     * Updates a set of variables, using the values supplied. $variables should be an array list
     * of name-value pairs, where the name matches the relevant field in the database. $id should
     * be the ID of the set of variables to be updated
     * 
     * @param int $id
     * @param array $variables
     * @return object
     */
    public function updateVariable($id, $variables)
    {
        $variableController = new VariablesController($this->getAuth());
        return $variableController->patchResource($id, $variables);
    }
    
    /**
     * Replaces a set of variables, using the values supplied. $variables should be an array list
     * of name-value pairs, where the name matches the relevant field in the database. $id should
     * be the ID of the set of variables to be replaced
     * 
     * @param int $id
     * @param array $variables
     * @return object
     */
    public function replaceVariable($id, $variables)
    {
        $variableController = new VariablesController($this->getAuth());
        return $variableController->putResource($id, $variables);
    }
    
    /**
     * Get a list of variables for a dataset, using the dataset's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getVariablesForDataset($id, $filter = null)
    {
        $variableController = new VariablesController($this->getAuth(), $filter);
        return $variableController->getResource('for', array('dataset', $id));
    }
    
    /**
     * Fetches a road attributes for a specified location. You must specify the location ID,
     * and the ID of the dataset it belongs to.
     * 
     * @param int $id
     * @param int $dataset_id
     * @return object
     */
    public function getRoadAttributes($id, $dataset_id, $filter = null)
    {
        $roadAttributeController = new RoadAttributesController($this->getAuth(), $filter);
        return $roadAttributeController->getResource($id, $dataset_id);
    }
    
    /**
     * Get a list of road attributes for a programme, using the programme's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getRoadAttributesForProgramme($id, $filter = null)
    {
        $roadAttributeController = new RoadAttributesController($this->getAuth(), $filter);
        return $roadAttributeController->getResource('for', array('programme', $id));
    }
    
    /**
     * Get a list of road attributes for a region, using the region's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getRoadAttributesForRegion($id, $filter = null)
    {
        $roadAttributeController = new RoadAttributesController($this->getAuth(), $filter);
        return $roadAttributeController->getResource('for', array('region', $id));
    }
    
    /**
     * Get a list of road attributes for a project, using the project's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getRoadAttributesForProject($id, $filter = null)
    {
        $roadAttributeController = new RoadAttributesController($this->getAuth(), $filter);
        return $roadAttributeController->getResource('for', array('project', $id));
    }
    
    /**
     * Get a list of road attributes for a dataset, using the dataset's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getRoadAttributesForDataset($id, $filter = null)
    {
        $roadAttributeController = new RoadAttributesController($this->getAuth(), $filter);
        return $roadAttributeController->getResource('for', array('dataset', $id));
    }
    
    /**
     * Fetches fatalities for a specified location. You must specify the location ID,
     * and the ID of the dataset it belongs to.
     * 
     * @param int $id
     * @param int $dataset_id
     * @return object
     */
    public function getFatalities($id, $dataset_id, $filter = null)
    {
        $fatalitiesController = new FatalitiesController($this->getAuth(), $filter);
        return $fatalitiesController->getResource($id, $dataset_id);
    }
    
    /**
     * Get a list of fatalities for a programme, using the programme's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getFatalitiesForProgramme($id, $filter = null)
    {
        $fatalitiesController = new FatalitiesController($this->getAuth(), $filter);
        return $fatalitiesController->getResource('for', array('programme', $id));
    }
    
    /**
     * Get a list of fatalities for a region, using the region's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getFatalitiesForRegion($id, $filter = null)
    {
        $fatalitiesController = new FatalitiesController($this->getAuth(), $filter);
        return $fatalitiesController->getResource('for', array('region', $id));
    }
    
    /**
     * Get a list of fatalities for a project, using the project's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getFatalitiesForProject($id, $filter = null)
    {
        $fatalitiesController = new FatalitiesController($this->getAuth(), $filter);
        return $fatalitiesController->getResource('for', array('project', $id));
    }
    
    /**
     * Get a list of fatalities for a dataset, using the dataset's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getFatalitiesForDataset($id, $filter = null)
    {
        $fatalitiesController = new FatalitiesController($this->getAuth(), $filter);
        return $fatalitiesController->getResource('for', array('dataset', $id));
    }
    
    /**
     * Fetches a before countermeasures star rating for a specified location. You must specify the 
     * location ID, and the ID of the dataset it belongs to.
     * 
     * @param int $id
     * @param int $dataset_id
     * @return object
     */
    public function getBeforeStarRatings($id, $dataset_id, $filter = null)
    {
        $starRatingController = new StarRatingsController($this->getAuth(), $filter);
        return $starRatingController->getResource($id, array('before', $dataset_id));
    }
    
    /**
     * Get a list of star ratings for a programme, using the programme's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getBeforeStarRatingsForProgramme($id, $filter = null)
    {
        $starRatingController = new StarRatingsController($this->getAuth(), $filter);
        return $starRatingController->getResource('for', array('programme', $id, 'before'));
    }
    
    /**
     * Get a list of star ratings for a region, using the region's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getBeforeStarRatingsForRegion($id, $filter = null)
    {
        $starRatingController = new StarRatingsController($this->getAuth(), $filter);
        return $starRatingController->getResource('for', array('region', $id, 'before'));
    }
    
    /**
     * Get a list of star ratings for a project, using the project's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getBeforeStarRatingsForProject($id, $filter = null)
    {
        $starRatingController = new StarRatingsController($this->getAuth(), $filter);
        return $starRatingController->getResource('for', array('project', $id, 'before'));
    }
    
    /**
     * Get a list of star ratings for a dataset, using the dataset's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getBeforeStarRatingsForDataset($id, $filter = null)
    {
        $starRatingController = new StarRatingsController($this->getAuth(), $filter);
        return $starRatingController->getResource('for', array('dataset', $id, 'before'));
    }
    
    /**
     * Fetches an after countermeasures star rating for a specified location. You must specify the 
     * location ID, and the ID of the dataset it belongs to.
     * 
     * @param int $id
     * @param int $dataset_id
     * @return object
     */
    public function getAfterStarRatings($id, $dataset_id, $filter = null)
    {
        $starRatingController = new StarRatingsController($this->getAuth(), $filter);
        return $starRatingController->getResource($id, array('after', $dataset_id));
    }
    
    /**
     * Get a list of star ratings for a programme, using the programme's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getAfterStarRatingsForProgramme($id, $filter = null)
    {
        $starRatingController = new StarRatingsController($this->getAuth(), $filter);
        return $starRatingController->getResource('for', array('programme', $id, 'after'));
    }
    
    /**
     * Get a list of star ratings for a region, using the region's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getAfterStarRatingsForRegion($id, $filter = null)
    {
        $starRatingController = new StarRatingsController($this->getAuth(), $filter);
        return $starRatingController->getResource('for', array('region', $id, 'after'));
    }
    
    /**
     * Get a list of star ratings for a project, using the project's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getAfterStarRatingsForProject($id, $filter = null)
    {
        $starRatingController = new StarRatingsController($this->getAuth(), $filter);
        return $starRatingController->getResource('for', array('project', $id, 'after'));
    }
    
    /**
     * Get a list of star ratings for a dataset, using the dataset's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getAfterStarRatingsForDataset($id, $filter = null)
    {
        $starRatingController = new StarRatingsController($this->getAuth(), $filter);
        return $starRatingController->getResource('for', array('dataset', $id, 'after'));
    }
    
    /**
     * Fetches a data for a specified location. You must specify the location ID,
     * and the ID of the dataset it belongs to.
     * 
     * @param int $id
     * @param int $dataset_id
     * @return object
     */
    public function getData($id, $dataset_id, $filter = null)
    {
        $dataController = new DataController($this->getAuth(), $filter);
        return $dataController->getResource($id, $dataset_id);
    }
    
    /**
     * Adds a set of data for the specified location. $data should be an array 
     * list of name-value pairs, where the name matches the relevant field in the database.
     * $dataset_id should be the ID of the dataset the data are associated with.
     * 
     * @param array $data
     * @param int $dataset_id
     * @return object
     */
    public function addData($data, $dataset_id)
    {
        if (
                $data !== array_values($data) || 
                (
                    isset($data[0]) && 
                    is_array($data[0]) && 
                    $data[0] !== array_values($data[0])
                )
            )
        {
            // $data has keys, encode as JSON
            $dataArray = array('data' => json_encode($data));
        }
        else
        {
            $dataArray = array('data' => $data);
        }
        $dataController = new DataController($this->getAuth());
        return $dataController->postResource($dataArray, $dataset_id);
    }
    
    /**
     * Updates a set of data for the specified location. $data should be an 
     * array list of name-value pairs, where the name matches the relevant field in the database.
     * $dataset_id should be the ID of the dataset the data are associated with. $id
     * should be the ID of the set of data to update.
     * 
     * @param int $id
     * @param array $data
     * @param int $dataset_id
     * @return object
     */
    public function updateData($id, $data, $dataset_id)
    {
        $dataArray = array('data' => $data);
        $dataController = new DataController($this->getAuth());
        return $dataController->patchResource($id, $dataArray, $dataset_id);
    }
    
    /**
     * Replaces a set of data for the specified location. $data should be an 
     * array list of name-value pairs, where the name matches the relevant field in the database.
     * $dataset_id should be the ID of the dataset the data are associated with. $id
     * should be the ID of the set of data to replace.
     * 
     * @param int $id
     * @param array $data
     * @param int $dataset_id
     * @return object
     */
    public function replaceData($id, $data, $dataset_id)
    {
        $dataArray = array('data' => $data);
        $dataController = new DataController($this->getAuth());
        return $dataController->putResource($id, $dataArray, $dataset_id);
    }
    
    /**
     * Deletes a set of data from the system, using the set of data's ID.
     * 
     * @param int $id
     * @return object
     */
    public function deleteData($id, $dataset_id)
    {
        $dataController = new DataController($this->getAuth());
        return $dataController->deleteResource($id, $dataset_id);
    }
    
    /**
     * Get a list of data for a programme, using the programme's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getDataForProgramme($id, $filter = null)
    {
        $dataController = new DataController($this->getAuth(), $filter);
        return $dataController->getResource('for', array('programme', $id));
    }
    
    /**
     * Get a list of data for a region, using the region's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getDataForRegion($id, $filter = null)
    {
        $dataController = new DataController($this->getAuth(), $filter);
        return $dataController->getResource('for', array('region', $id));
    }
    
    /**
     * Get a list of data for a project, using the project's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getDataForProject($id, $filter = null)
    {
        $dataController = new DataController($this->getAuth(), $filter);
        return $dataController->getResource('for', array('project', $id));
    }
    
    /**
     * Get a list of data for a dataset, using the dataset's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getDataForDataset($id, $filter = null)
    {
        $dataController = new DataController($this->getAuth(), $filter);
        return $dataController->getResource('for', array('dataset', $id));
    }
    
    /**
     * Fetches a list of all of the countries in the system. If you specify an ID, that country
     * will be returned to you.
     * 
     * @param int $id
     * @return object
     */
    public function getCountries($id = null, $filter = null)
    {
        $countriesController = new CountriesController($this->getAuth(), $filter);
        return $countriesController->getResource($id);
    }
    
    /**
     * Fetches the permissions for the user. If this is called on an app object, it fetches
     * the permissions for the app.
     * 
     * @return object
     */
    public function getPermissions($filter = null)
    {
        $permissionsController = new PermissionsController($this->getAuth(), $filter);
        return $permissionsController->getResource();
    }
}