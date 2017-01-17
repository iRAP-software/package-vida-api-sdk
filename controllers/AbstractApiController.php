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
    public function getUsers($id = null)
    {
        $userController = new UsersController($this->getAuth());
        return $userController->getResource('users', $id);
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
        return $userController->postResource('users', array("name"=>$name,"email"=>$email,"password"=>$password));
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
        return $userController->putResource('users', $id, array("name"=>$name,"email"=>$email,"password"=>$password));
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
        return $userController->deleteResource('users', $id);
    }
    
    /**
     * Fetches a list of all of the datasets in the system. If you specify an ID, that dataset will
     * be returned to you.
     * 
     * @param int $id
     * @return object
     */
    public function getDatasets($id = null)
    {
        $datasetController = new DatasetsController($this->getAuth());
        return $datasetController->getResource('datasets', $id);
    }
    
    /**
     * Creates a new dataset using the supplied data, which should be an array of field name as 
     * keys and the values you wish to insert, as name-value pairs
     * 
     * @param string $name
     * @param array $road_data
     * @return object
     */
    public function addDataset($name, $road_data)
    {
        $datasetController = new DatasetsController($this->getAuth());
        return $datasetController->postResource('datasets', array("name"=>$name, "road-data"=>$road_data));
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
    public function replaceDataset($id, $name, $road_data)
    {
        $datasetController = new DatasetsController($this->getAuth());
        return $datasetController->putResource('datasets', $id, array("name"=>$name, "road-data"=>$road_data));
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
        return $datasetController->deleteResource('datasets', $id);
    }
    
    /**
     * Get a list of the users who have access to a dataset, using the dataset's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getDatasetUsers($id)
    {
        $datasetController = new DatasetsController($this->getAuth());
        return $datasetController->getResource('datasets', $id, 'user-access');
    }
    
    /**
     * Get a list of datasets for a programme, using the programme's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getDatasetsForProgramme($id)
    {
        $datasetController = new DatasetsController($this->getAuth());
        return $datasetController->getResource('datasets', 'for', array('programme', $id));
    }
    
    /**
     * Get a list of datasets for a region, using the region's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getDatasetsForRegion($id)
    {
        $datasetController = new DatasetsController($this->getAuth());
        return $datasetController->getResource('datasets', 'for', array('region', $id));
    }
    
    /**
     * Get a list of datasets for a project, using the project's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getDatasetsForProject($id)
    {
        $datasetController = new DatasetsController($this->getAuth());
        return $datasetController->getResource('datasets', 'for', array('project', $id));
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
    public function processDataset($id)
    {
        $datasetController = new DatasetsController($this->getAuth());
        return $datasetController->getResource('datasets', $id, 'process');
    }
    
    /**
     * Fetches a list of all of the programmes in the system. If you specify an ID, that programme
     * will be returned to you.
     * 
     * @param int $id
     * @return object
     */
    public function getProgrammes($id = null)
    {
        $programmeController = new ProgrammesController($this->getAuth());
        return $programmeController->getResource('programmes', $id);
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
        return $programmeController->postResource('programmes', array("name"=>$name, "principal_users_id"=>$manager_id));
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
        return $programmeController->putResource('programmes', $id, array("name"=>$name, "manager_id"=>$manager_id));
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
        return $programmeController->deleteResource('programmes', $id);
    }
    
    /**
     * Get a list of the users who have access to a programme, using the programme's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getProgrammeUsers($id)
    {
        $programmeController = new ProgrammesController($this->getAuth());
        return $programmeController->getResource('programmes', $id, 'user-access');
    }
    
    /**
     * Fetches a list of all of the regions in the system. If you specify an ID, that region will be
     * returned to you.
     * 
     * @param int $id
     * @return object
     */
    public function getRegions($id = null)
    {
        $regionController = new RegionsController($this->getAuth());
        return $regionController->getResource('regions', $id);
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
        return $regionController->postResource('regions', array("name"=>$name, "programme_id"=>$programme_id, "manager_id"=>$manager_id));
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
        return $regionController->putResource('regions', $id, array("name"=>$name, "programme_id"=>$programme_id, "manager_id"=>$manager_id));
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
        return $regionController->deleteResource('regions', $id);
    }
    
    /**
     * Get a list of the users who have access to a region, using the region's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getRegionUsers($id)
    {
        $regionController = new RegionsController($this->getAuth());
        return $regionController->getResource('regions', $id, 'user-access');
    }
    
    /**
     * Get a list of regions for a programme, using the programme's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getRegionsForProgramme($id)
    {
        $regionController = new RegionsController($this->getAuth());
        return $regionController->getResource('regions', 'for', array('programme', $id));
    }
    
    /**
     * Fetches a list of all of the projects in the system. If you specify an ID, that project will
     * be returned to you.
     * 
     * @param int $id
     * @return object
     */
    public function getProjects($id = null)
    {
        $projectController = new ProjectsController($this->getAuth());
        return $projectController->getResource('projects', $id);
    }
    
    /**
     * Creates a new project, for which a name should be supplied, along with the id of the parent
     * region, the user id of the project's manager and the id of the model to be used.
     * 
     * @param string $name
     * @param int $region_id
     * @param int $manager_id
     * @param int $model_id
     * @return object
     */
    public function addProject($name, $region_id, $manager_id, $model_id)
    {
        $projectController = new ProjectsController($this->getAuth());
        return $projectController->postResource('projects', array("name"=>$name, "region_id"=>$region_id, "manager_id"=>$manager_id, "model_id"=>$model_id));
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
     * @return object
     */
    public function replaceProject($id, $name, $region_id, $manager_id, $model_id)
    {
        $projectController = new ProjectsController($this->getAuth());
        return $projectController->putResource('projects', $id, array("name"=>$name, "region_id"=>$region_id, "manager_id"=>$manager_id, "model_id"=>$model_id));
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
        return $projectController->deleteResource('projects', $id);
    }
    
    /**
     * Get a list of the users who have access to a project, using the project's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getProjectUsers($id)
    {
        $projectController = new ProjectsController($this->getAuth());
        return $projectController->getResource('projects', $id, 'user-access');
    }
    
    /**
     * Get a list of projects for a programme, using the programme's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getProjectsForProgramme($id)
    {
        $projectController = new ProjectsController($this->getAuth());
        return $projectController->getResource('projects', 'for', array('programme', $id));
    }
    
    /**
     * Get a list of projects for a regions, using the regions's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getProjectsForRegion($id)
    {
        $projectController = new ProjectsController($this->getAuth());
        return $projectController->getResource('projects', 'for', array('region', $id));
    }
    
    /**
     * Fetches a list of all of the variables in the system. If you specify an ID, that variable will be
     * returned to you.
     * 
     * @param int $id
     * @return object
     */
    public function getVariables($id = null)
    {
        $variableController = new VariablesController($this->getAuth());
        return $variableController->getResource('variables', $id);
    }
    
    /**
     * Creates a new set of variables, using the values supplied. $variables should be an array list
     * of name-value pairs, where the name matches the relevant field in the database.
     * 
     * @param array $variables
     * @return object
     */
    public function addVariable($variables)
    {
        $variableController = new VariablesController($this->getAuth());
        return $variableController->postResource('variables', $variables);
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
        return $variableController->putResource('variables', $id, $variables);
    }
    
    /**
     * Deletes a set of variables from the system, using the set of variables's ID.
     * 
     * @param int $id
     * @return object
     */
    public function deleteVariable($id)
    {
        $variableController = new VariablesController($this->getAuth());
        return $variableController->deleteResource('variables', $id);
    }
    
    /**
     * Get a list of variables for a dataset, using the dataset's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getVariablesForDataset($id)
    {
        $variableController = new VariablesController($this->getAuth());
        return $variableController->getResource('variables', 'for', array('dataset', $id));
    }
    
    /**
     * Fetches a road attributes for a specified location. You must specify the location ID,
     * and the ID of the dataset it belongs to.
     * 
     * @param int $id
     * @param int $dataset_id
     * @return object
     */
    public function getRoadAttributes($id, $dataset_id)
    {
        $roadAttributeController = new RoadAttributesController($this->getAuth());
        return $roadAttributeController->getResource('roadattributes', $id, $dataset_id);
    }
    
    /**
     * Adds a set of road attributes for the specified location. $roadAttributes should be an array 
     * list of name-value pairs, where the name matches the relevant field in the database.
     * $dataset_id should be the ID of the dataset the road attributes are associated with.
     * 
     * @param array $roadAttributes
     * @param int $dataset_id
     * @return object
     */
    public function addRoadAttribute($roadAttributes, $dataset_id)
    {
        $roadAttributeController = new RoadAttributesController($this->getAuth());
        return $roadAttributeController->postResource('roadattributes', $roadAttributes, $dataset_id);
    }
    
    /**
     * Replaces a set of road attributes for the specified location. $roadAttributes should be an 
     * array list of name-value pairs, where the name matches the relevant field in the database.
     * $dataset_id should be the ID of the dataset the road attributes are associated with. $id
     * should be the ID of the set of road attributes to replace.
     * 
     * @param int $id
     * @param array $roadAttributes
     * @param int $dataset_id
     * @return object
     */
    public function replaceRoadAttribute($id, $roadAttributes, $dataset_id)
    {
        $roadAttributeController = new RoadAttributesController($this->getAuth());
        return $roadAttributeController->putResource('roadattributes', $id, $roadAttributes, $dataset_id);
    }
    
    /**
     * Deletes a set of road attributes from the system, using the set of road attributes's ID.
     * 
     * @param int $id
     * @return object
     */
    public function deleteRoadAttribute($id, $dataset_id)
    {
        $roadAttributeController = new RoadAttributesController($this->getAuth());
        return $roadAttributeController->deleteResource('roadattributes', $id, $dataset_id);
    }
    
    /**
     * Get a list of road attributes for a programme, using the programme's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getRoadAttributesForProgramme($id)
    {
        $roadAttributeController = new RoadAttributesController($this->getAuth());
        return $roadAttributeController->getResource('roadattributes', 'for', array('programme', $id));
    }
    
    /**
     * Get a list of road attributes for a region, using the region's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getRoadAttributesForRegion($id)
    {
        $roadAttributeController = new RoadAttributesController($this->getAuth());
        return $roadAttributeController->getResource('roadattributes', 'for', array('region', $id));
    }
    
    /**
     * Get a list of road attributes for a project, using the project's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getRoadAttributesForProject($id)
    {
        $roadAttributeController = new RoadAttributesController($this->getAuth());
        return $roadAttributeController->getResource('roadattributes', 'for', array('project', $id));
    }
    
    /**
     * Get a list of road attributes for a dataset, using the dataset's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getRoadAttributesForDataset($id)
    {
        $roadAttributeController = new RoadAttributesController($this->getAuth());
        return $roadAttributeController->getResource('roadattributes', 'for', array('dataset', $id));
    }
    
    /**
     * Fetches fatalities for a specified location. You must specify the location ID,
     * and the ID of the dataset it belongs to.
     * 
     * @param int $id
     * @param int $dataset_id
     * @return object
     */
    public function getFatalities($id, $dataset_id)
    {
        $fatalitiesController = new FatalitiesController($this->getAuth());
        return $fatalitiesController->getResource('fatalities', $id, $dataset_id);
    }
    
    /**
     * Adds a set of fatalities for the specified location. $fatalities should be an array 
     * list of name-value pairs, where the name matches the relevant field in the database.
     * $dataset_id should be the ID of the dataset the road attributes are associated with.
     * 
     * @param array $fatalities
     * @param int $dataset_id
     * @return object
     */
    public function addFatalities($fatalities, $dataset_id)
    {
        $fatalitiesController = new FatalitiesController($this->getAuth());
        return $fatalitiesController->postResource('fatalities', $fatalities, $dataset_id);
    }
    
    /**
     * Replaces a set of fatalities for the specified location. $fatalities should be an array 
     * list of name-value pairs, where the name matches the relevant field in the database.
     * $dataset_id should be the ID of the dataset the road attributes are associated with. $id
     * should be the ID of the set of fatalities you wish to replace.
     * 
     * @param int $id
     * @param array $fatalities
     * @param int $dataset_id
     * @return object
     */
    public function replaceFatalities($id, $fatalities, $dataset_id)
    {
        $fatalitiesController = new FatalitiesController($this->getAuth());
        return $fatalitiesController->putResource('fatalities', $id, $fatalities, $dataset_id);
    }
    
    /**
     * Deletes a set of fatalities from the system, using the set of fatalities's ID.
     * 
     * @param int $id
     * @return object
     */
    public function deleteFatalities($id, $dataset_id)
    {
        $fatalitiesController = new FatalitiesController($this->getAuth());
        return $fatalitiesController->deleteResource('fatalities', $id, $dataset_id);
    }
    
    /**
     * Get a list of fatalities for a programme, using the programme's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getFatalitiesForProgramme($id)
    {
        $fatalitiesController = new FatalitiesController($this->getAuth());
        return $fatalitiesController->getResource('fatalities', 'for', array('programme', $id));
    }
    
    /**
     * Get a list of fatalities for a region, using the region's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getFatalitiesForRegion($id)
    {
        $fatalitiesController = new FatalitiesController($this->getAuth());
        return $fatalitiesController->getResource('fatalities', 'for', array('region', $id));
    }
    
    /**
     * Get a list of fatalities for a project, using the project's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getFatalitiesForProject($id)
    {
        $fatalitiesController = new FatalitiesController($this->getAuth());
        return $fatalitiesController->getResource('fatalities', 'for', array('project', $id));
    }
    
    /**
     * Get a list of fatalities for a dataset, using the dataset's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getFatalitiesForDataset($id)
    {
        $fatalitiesController = new FatalitiesController($this->getAuth());
        return $fatalitiesController->getResource('fatalities', 'for', array('dataset', $id));
    }
    
    /**
     * Fetches a before countermeasures star rating for a specified location. You must specify the 
     * location ID, and the ID of the dataset it belongs to.
     * 
     * @param int $id
     * @param int $dataset_id
     * @return object
     */
    public function getBeforeStarRatings($id, $dataset_id)
    {
        $starRatingController = new StarRatingsController($this->getAuth());
        return $starRatingController->getResource('starratings', $id, array('before', $dataset_id));
    }
    
    /**
     * Adds a set of star ratings for the specified location. $starratings should be an array 
     * list of name-value pairs, where the name matches the relevant field in the database.
     * $dataset_id should be the ID of the dataset the road attributes are associated with.
     * 
     * @param array $starratings
     * @param int $dataset_id
     * @return object
     */
    public function addBeforeStarRating($starratings, $dataset_id)
    {
        $starRatingController = new StarRatingsController($this->getAuth());
        return $starRatingController->postResource('starratings', $starratings, array('before', $dataset_id));
    }
    
    /**
     * Replaces a set of star ratings for the specified location. $starratings should be an array 
     * list of name-value pairs, where the name matches the relevant field in the database.
     * $dataset_id should be the ID of the dataset the road attributes are associated with.
     * $id should be the ID of the set of star ratings to be replaced.
     * 
     * @param int $id
     * @param array $starratings
     * @param int $dataset_id
     * @return object
     */
    public function replaceBeforeStarRating($id, $starratings, $dataset_id)
    {
        $starRatingController = new StarRatingsController($this->getAuth());
        return $starRatingController->putResource('starratings', $id, $starratings, array('before', $dataset_id));
    }
    
    /**
     * Deletes a set of star ratings from the system, using the set of star ratings's ID.
     * 
     * @param int $id
     * @return object
     */
    public function deleteBeforeStarRating($id, $dataset_id)
    {
        $starRatingController = new StarRatingsController($this->getAuth());
        return $starRatingController->deleteResource('starratings', $id, array('before', $dataset_id));
    }
    
    /**
     * Get a list of star ratings for a programme, using the programme's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getBeforeStarRatingsForProgramme($id)
    {
        $starRatingController = new StarRatingsController($this->getAuth());
        return $starRatingController->getResource('starratings', 'for', array('programme', $id, 'before'));
    }
    
    /**
     * Get a list of star ratings for a region, using the region's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getBeforeStarRatingsForRegion($id)
    {
        $starRatingController = new StarRatingsController($this->getAuth());
        return $starRatingController->getResource('starratings', 'for', array('region', $id, 'before'));
    }
    
    /**
     * Get a list of star ratings for a project, using the project's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getBeforeStarRatingsForProject($id)
    {
        $starRatingController = new StarRatingsController($this->getAuth());
        return $starRatingController->getResource('starratings', 'for', array('project', $id, 'before'));
    }
    
    /**
     * Get a list of star ratings for a dataset, using the dataset's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getBeforeStarRatingsForDataset($id)
    {
        $starRatingController = new StarRatingsController($this->getAuth());
        return $starRatingController->getResource('starratings', 'for', array('dataset', $id, 'before'));
    }
    
    /**
     * Fetches an after countermeasures star rating for a specified location. You must specify the 
     * location ID, and the ID of the dataset it belongs to.
     * 
     * @param int $id
     * @param int $dataset_id
     * @return object
     */
    public function getAfterStarRatings($id, $dataset_id)
    {
        $starRatingController = new StarRatingsController($this->getAuth());
        return $starRatingController->getResource('starratings', $id, array('after', $dataset_id));
    }
    
    /**
     * Adds a set of star ratings for the specified location. $starratings should be an array 
     * list of name-value pairs, where the name matches the relevant field in the database.
     * $dataset_id should be the ID of the dataset the road attributes are associated with.
     * 
     * @param array $starratings
     * @param int $dataset_id
     * @return object
     */
    public function addAfterStarRating($starratings, $dataset_id)
    {
        $starRatingController = new StarRatingsController($this->getAuth());
        return $starRatingController->postResource('starratings', $starratings, array('after', $dataset_id));
    }
    
    /**
     * Replaces a set of star ratings for the specified location. $starratings should be an array 
     * list of name-value pairs, where the name matches the relevant field in the database.
     * $dataset_id should be the ID of the dataset the road attributes are associated with.
     * $id should be the ID of the set of star ratings to be replaced.
     * 
     * @param int $id
     * @param array $starratings
     * @param int $dataset_id
     * @return object
     */
    public function replaceAfterStarRating($id, $starratings, $dataset_id)
    {
        $starRatingController = new StarRatingsController($this->getAuth());
        return $starRatingController->putResource('starratings', $id, $starratings, array('after', $dataset_id));
    }
    
    /**
     * Deletes a set of star ratings from the system, using the set of star ratings's ID.
     * 
     * @param int $id
     * @return object
     */
    public function deleteAfterStarRating($id, $dataset_id)
    {
        $starRatingController = new StarRatingsController($this->getAuth());
        return $starRatingController->deleteResource('starratings', $id, array('after', $dataset_id));
    }
    
    /**
     * Get a list of star ratings for a programme, using the programme's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getAfterStarRatingsForProgramme($id)
    {
        $starRatingController = new StarRatingsController($this->getAuth());
        return $starRatingController->getResource('starratings', 'for', array('programme', $id, 'after'));
    }
    
    /**
     * Get a list of star ratings for a region, using the region's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getAfterStarRatingsForRegion($id)
    {
        $starRatingController = new StarRatingsController($this->getAuth());
        return $starRatingController->getResource('starratings', 'for', array('region', $id, 'after'));
    }
    
    /**
     * Get a list of star ratings for a project, using the project's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getAfterStarRatingsForProject($id)
    {
        $starRatingController = new StarRatingsController($this->getAuth());
        return $starRatingController->getResource('starratings', 'for', array('project', $id, 'after'));
    }
    
    /**
     * Get a list of star ratings for a dataset, using the dataset's ID.
     * 
     * @param int $id
     * @return object
     */
    public function getAfterStarRatingsForDataset($id)
    {
        $starRatingController = new StarRatingsController($this->getAuth());
        return $starRatingController->getResource('starratings', 'for', array('dataset', $id, 'after'));
    }
}