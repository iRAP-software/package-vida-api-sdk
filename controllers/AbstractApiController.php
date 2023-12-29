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

use iRAP\VidaSDK\Models\Response;

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
        if ($userToken->code == 200)
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
     * Checks whether the GET contains the user key. If so, it creates a user token object and
     * returns it. If not, the requestUserPermissions method is called, which redirects the user
     * to ViDA.
     *
     * @param string $returnUrl
     */
    public function requestUserPermissions($returnUrl)
    {
        $get = filter_input_array(INPUT_GET);
        if (
            isset($get['userAuthId']) &&
            isset($get['userApiKey']) &&
            isset($get['userPrivateKey']) &&
            isset($get['userID'])
        )
        {
            $token = new \stdClass();
            $token->userAuthId = urldecode($get['userAuthId']);
            $token->userApiKey = urldecode($get['userApiKey']);
            $token->userPrivateKey = urldecode($get['userPrivateKey']);
            $token->userID = $get['userID'];
            $response = $token;
        }
        elseif(isset($get['status']) && $get['status'] == 'rejected')
        {
            $token = new \stdClass();
            $token->status = 'Rejected';
            $token->code = 401;
            $response = $token;
        }
        else
        {
            AuthController::requestUserPermissions($this->getAuth(), $returnUrl);
            $response = new \stdClass();
        }
        return $response;
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
     * Fetches a list of all of the users in the system. If you specify an ID, that user will be
     * returned to you.
     *
     * @param int $id
     * @return object
     */
    public function getUserAccess($userID, $filter = null)
    {
        $userController = new UsersController($this->getAuth(), $filter);
        return $userController->getResource($userID, 'user-access');
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
     * Creates a new dataset
     * @param string $name
     * @param $project_id
     * @param $manager_id
     * @param int $type Could be any one of \iRAP\VidaSDK\App::DATASET_TYPE_EXISTING,
     * \iRAP\VidaSDK\App::DATASET_TYPE_DESIGN, \iRAP\VidaSDK\App::DATASET_TYPE_RESEARCH.
     * <i>Defaults to \iRAP\VidaSDK\App::DATASET_TYPE_UNKNOWN</i>
     * @param string $assessment_date Date format, 'Y-m-d' e.g. 2020-10-22
     * @param string $description
     * @return object
     */
    public function addDataset(string $name, $project_id, $manager_id,
                               int $type = \iRAP\VidaSDK\App::DATASET_TYPE_UNKNOWN,
                               string $assessment_date = null,
                               string $description = '')
    {
        $datasetController = new DatasetsController($this->getAuth());
        return $datasetController->postResource(array(
            'name' => $name,
            'project_id' => $project_id,
            'manager_id' => $manager_id,
            'type_id' => $type,
            'description' => $description,
            'assessment_date' => $assessment_date
        ));
    }

    /**
     * Updates a dataset
     * @param $id
     * @param string $name
     * @param $project_id
     * @param $manager_id
     * @param int $type Could be any one of \iRAP\VidaSDK\App::DATASET_TYPE_EXISTING,
     * \iRAP\VidaSDK\App::DATASET_TYPE_DESIGN, \iRAP\VidaSDK\App::DATASET_TYPE_RESEARCH.
     * <i>Defaults to \iRAP\VidaSDK\App::DATASET_TYPE_UNKNOWN</i>
     * @param string $assessment_date Date format, 'Y-m-d' e.g. 2020-10-22
     * @param string $description
     * @return object
     */
    public function updateDataset($id, string $name, $project_id, $manager_id,
                                  int $type = \iRAP\VidaSDK\App::DATASET_TYPE_UNKNOWN,
                                  string $assessment_date = null,
                                  string $description = '')
    {
        $datasetController = new DatasetsController($this->getAuth());
        return $datasetController->patchResource($id,
            array(
                'name' => $name,
                'project_id' => $project_id,
                'manager_id' => $manager_id,
                'type_id' => $type,
                'description' => $description,
                'assessment_date' => $assessment_date
            ));
    }

    /**
     * Updates the status of a dataset, using the following status codes:
     *
     * - 1 - Draft
     * - 2 - Working
     * - 3 - Final Hidden
     * - 4 - Final Unpublished
     * - 5 - Final Published
     *
     * @param int $id
     * @param int $status_id
     * @return object
     */
    public function updateDatasetStatus($id, $status_id)
    {
        $datasetController = new DatasetsController($this->getAuth());
        return $datasetController->patchResource($id, array("status_id"=>$status_id));
    }

    /**
     * Replaces a dataset
     * @param $id
     * @param string $name
     * @param $project_id
     * @param $manager_id
     * @param int $type Could be any one of \iRAP\VidaSDK\App::DATASET_TYPE_EXISTING,
     * \iRAP\VidaSDK\App::DATASET_TYPE_DESIGN, \iRAP\VidaSDK\App::DATASET_TYPE_RESEARCH.
     * <i>Defaults to \iRAP\VidaSDK\App::DATASET_TYPE_UNKNOWN</i>
     * @param string $assessment_date Date format, 'Y-m-d' e.g. 2020-10-22
     * @param string $description
     * @return object
     */
    public function replaceDataset($id, string $name, $project_id, $manager_id,
                                   int $type = \iRAP\VidaSDK\App::DATASET_TYPE_UNKNOWN,
                                   string $assessment_date = null,
                                   string $description = '')
    {
        $datasetController = new DatasetsController($this->getAuth());
        return $datasetController->putResource($id,
            array(
                'name' => $name,
                'project_id' => $project_id,
                'manager_id' => $manager_id,
                'type_id' => $type,
                'description' => $description,
                'assessment_date' => $assessment_date
            ));
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
     * Reprocess a dataset using the data already in ViDA.
     * @param int $datasetID
     * @return object
     */
    public function reprocessDataset($datasetID)
    {
        $datasetController = new DatasetsController($this->getAuth());
        return $datasetController->getResource($datasetID, 'reprocess');
    }


    /**
     * Validates the specified dataset and begins processing. Processing data is added to a queue
     * and a successful response to this request means that the dataset has been added to the queue,
     * not that processing is complete. To check whether it has finished, call getDataset($id) and
     * examine the returned is_data_processing property.
     *
     * If the data fails to validate, a 400 will be returned. Check the errors property for the
     * errors encountered
     *
     * @param int $id
     * @return object
     */
    public function validateAndProcessDataset($id, $filter = null)
    {
        $datasetController = new DatasetsController($this->getAuth(), $filter);
        return $datasetController->getResource($id, 'validateandprocess');
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
     * project, the id of the parent region, the user id of the project's manager.
     *
     * @param int $id
     * @param string $name
     * @param int $region_id
     * @param int $manager_id
     * @return object
     */
    public function updateProject($id, $name, $region_id, $manager_id)
    {
        $projectController = new ProjectsController($this->getAuth());
        return $projectController->patchResource($id, array("name"=>$name, "region_id"=>$region_id, "manager_id"=>$manager_id));
    }

    /**
     * Replaces a project, for which a name should be supplied, along with the id of the
     * project, the id of the parent region, the user id of the project's manager.
     *
     * @param int $id
     * @param string $name
     * @param int $region_id
     * @param int $manager_id
     * @return object
     */
    public function replaceProject($id, $name, $region_id, $manager_id)
    {
        $projectController = new ProjectsController($this->getAuth());
        return $projectController->putResource($id, array("name"=>$name, "region_id"=>$region_id, "manager_id"=>$manager_id));
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
     * @param int $id - the location ID of the road attributes in the dataset.
     * @param int $dataset_id - the ID of the dataset.
     * @return object
     */
    public function getRoadAttributes($id, $dataset_id, $filter = null)
    {
        $msg = 'getRoadAttributes is deprecated. ' .
            'Please use getBeforeRoadAttributes instead.';
        trigger_error($msg, E_USER_NOTICE);
        return $this->getBeforeRoadAttributes($id, $dataset_id, $filter);
    }


    /**
     * Fetches a road attributes for a specified location. You must specify the location ID,
     * and the ID of the dataset it belongs to.
     * @param int $id - the location ID of the road attributes in the dataset.
     * @param int $dataset_id - the ID of the dataset.
     * @return object
     */
    public function getBeforeRoadAttributes($id, $dataset_id, $filter = null)
    {
        $roadAttributeController = new RoadAttributesController($this->getAuth(), $filter);
        return $roadAttributeController->getResource($id, array('before', $dataset_id));
    }


    /**
     * Fetches a road attributes for a specified location. You must specify the location ID,
     * and the ID of the dataset it belongs to.
     * @param int $id - the location ID of the road attributes in the dataset.
     * @param int $dataset_id - the ID of the dataset.
     * @return object
     */
    public function getAfterRoadAttributes($id, $dataset_id, $filter = null)
    {
        $roadAttributeController = new RoadAttributesController($this->getAuth(), $filter);
        return $roadAttributeController->getResource($id, array('after', $dataset_id));
    }


    /**
     * Get a list of road attributes for a programme, using the programme's ID.
     * This method is deprecated. Please use getBeforeRoadAttributesForProgramme instead.
     * @param int $id - the ID of the programme.
     * @return object
     */
    public function getRoadAttributesForProgramme($id, $filter = null)
    {
        $msg = 'getRoadAttributesForProgramme is deprecated. ' .
            'Please use getBeforeRoadAttributesForProgramme instead.';
        trigger_error($msg, E_USER_NOTICE);
        return $this->getBeforeRoadAttributesForProgramme($id, $filter);
    }


    /**
     * Get a list of road attributes for a programme, using the programme's ID.
     * @param int $id - the ID of the programme.
     * @return object
     */
    public function getBeforeRoadAttributesForProgramme($id, $filter = null)
    {
        $roadAttributeController = new RoadAttributesController($this->getAuth(), $filter);
        return $roadAttributeController->getResource('for', array('programme', $id, 'before'));
    }


    /**
     * Get a list of road attributes for a programme, using the programme's ID.
     * @param int $id - the ID of the programme.
     * @return object
     */
    public function getAfterRoadAttributesForProgramme($id, $filter = null)
    {
        $roadAttributeController = new RoadAttributesController($this->getAuth(), $filter);
        return $roadAttributeController->getResource('for', array('programme', $id, 'after'));
    }


    /**
     * Get a list of road attributes for a region, using the region's ID.
     * @param int $id - the ID of the region.
     * @return object
     */
    public function getRoadAttributesForRegion($id, $filter = null)
    {
        $msg = 'getRoadAttributesForRegion is deprecated. ' .
            'Please use getBeforeRoadAttributesForRegion instead.';
        trigger_error($msg, E_USER_NOTICE);
        return $this->getBeforeRoadAttributesForRegion($id, $filter);
    }


    /**
     * Get a list of road attributes for a region, using the region's ID.
     * @param int $id - the ID of the region.
     * @return object
     */
    public function getBeforeRoadAttributesForRegion($id, $filter = null)
    {
        $roadAttributeController = new RoadAttributesController($this->getAuth(), $filter);
        return $roadAttributeController->getResource('for', array('region', $id, 'before'));
    }


    /**
     * Get a list of road attributes for a region, using the region's ID.
     * @param int $id - the ID of the region.
     * @return object
     */
    public function getAfterRoadAttributesForRegion($id, $filter = null)
    {
        $roadAttributeController = new RoadAttributesController($this->getAuth(), $filter);
        return $roadAttributeController->getResource('for', array('region', $id, 'after'));
    }


    /**
     * Get a list of road attributes for a project.
     * This is deprecated, please use getBeforeRoadAttributesForProject instead.
     * @param int $id - the ID of the project to get road attributes for.
     * @return object
     */
    public function getRoadAttributesForProject($id, $filter = null)
    {
        $msg = 'getRoadAttributesForProject is deprecated. ' .
            'Please use getBeforeRoadAttributesForProject instead.';
        trigger_error($msg, E_USER_NOTICE);
        return $this->getBeforeRoadAttributesForProject($id);
    }


    /**
     * Get a list of road attributes for a project.
     * @param int $id - the ID of the project to get road attributes for.
     * @return object
     */
    public function getBeforeRoadAttributesForProject($id, $filter = null)
    {
        $roadAttributeController = new RoadAttributesController($this->getAuth(), $filter);
        return $roadAttributeController->getResource('for', array('project', $id, 'before'));
    }


    /**
     * Get a list of road attributes for a project.
     * @param int $id - the ID of the project to get road attributes for.
     * @return object
     */
    public function getAfterRoadAttributesForProject($id, $filter = null)
    {
        $roadAttributeController = new RoadAttributesController($this->getAuth(), $filter);
        return $roadAttributeController->getResource('for', array('project', $id, 'after'));
    }


    /**
     * Alias for getBeforeRoadAttributesForDataset.
     * This is deprecated, please use getBeforeRoadAttributesForDataset instead.
     * @param int $id - the ID of the dataset
     * @return object
     */
    public function getRoadAttributesForDataset($id, $filter = null)
    {
        $msg = 'getBeforeRoadAttributesForDataset is deprecated. ' .
            'Please use getBeforeRoadAttributesForDataset instead.';
        trigger_error($msg, E_USER_NOTICE);
        return $this->getBeforeRoadAttributesForDataset($id, $filter);
    }


    /**
     * Get a list of road attributes for a dataset, using the dataset's ID.
     * @param int $id - the ID of the dataset
     * @return object
     */
    public function getBeforeRoadAttributesForDataset($id, $filter = null)
    {
        $roadAttributeController = new RoadAttributesController($this->getAuth(), $filter);
        return $roadAttributeController->getResource('for', array('dataset', $id, 'before'));
    }


    /**
     * Get a list of road attributes for a dataset, using the dataset's ID.
     * @param int $id - the ID of the dataset
     * @return object
     */
    public function getAfterRoadAttributesForDataset($id, $filter = null)
    {
        $roadAttributeController = new RoadAttributesController($this->getAuth(), $filter);
        return $roadAttributeController->getResource('for', array('dataset', $id, 'after'));
    }

    /**
     * Fetches a locations for a specified location. You must specify the location ID,
     * and the ID of the dataset it belongs to.
     * @param int $id - the location ID of the locations in the dataset.
     * @param int $dataset_id - the ID of the dataset.
     * @return object
     */
    public function getBeforeLocations($id, $dataset_id, $filter = null)
    {
        $locationController = new LocationsController($this->getAuth(), $filter);
        return $locationController->getResource($id, array('before', $dataset_id));
    }


    /**
     * Get a list of locations for a programme, using the programme's ID.
     * @param int $id - the ID of the programme.
     * @return object
     */
    public function getBeforeLocationsForProgramme($id, $filter = null)
    {
        $locationController = new LocationsController($this->getAuth(), $filter);
        return $locationController->getResource('for', array('programme', $id, 'before'));
    }


    /**
     * Get a list of locations for a region, using the region's ID.
     * @param int $id - the ID of the region.
     * @return object
     */
    public function getBeforeLocationsForRegion($id, $filter = null)
    {
        $locationController = new LocationsController($this->getAuth(), $filter);
        return $locationController->getResource('for', array('region', $id, 'before'));
    }

    /**
     * Get a list of locations for a project.
     * @param int $id - the ID of the project to get locations for.
     * @return object
     */
    public function getBeforeLocationsForProject($id, $filter = null)
    {
        $locationController = new LocationsController($this->getAuth(), $filter);
        return $locationController->getResource('for', array('project', $id, 'before'));
    }

    /**
     * Get a list of locations for a dataset, using the dataset's ID.
     * @param int $id - the ID of the dataset
     * @return object
     */
    public function getBeforeLocationsForDataset($id, $filter = null)
    {
        $locationController = new LocationsController($this->getAuth(), $filter);
        return $locationController->getResource('for', array('dataset', $id, 'before'));
    }

    /**
     * Fetches a locations for a specified location. You must specify the location ID,
     * and the ID of the dataset it belongs to.
     * @param int $id - the location ID of the locations in the dataset.
     * @param int $dataset_id - the ID of the dataset.
     * @return object
     */
    public function getAfterLocations($id, $dataset_id, $filter = null)
    {
        $locationController = new LocationsController($this->getAuth(), $filter);
        return $locationController->getResource($id, array('after', $dataset_id));
    }


    /**
     * Get a list of locations for a programme, using the programme's ID.
     * @param int $id - the ID of the programme.
     * @return object
     */
    public function getAfterLocationsForProgramme($id, $filter = null)
    {
        $locationController = new LocationsController($this->getAuth(), $filter);
        return $locationController->getResource('for', array('programme', $id, 'after'));
    }


    /**
     * Get a list of locations for a region, using the region's ID.
     * @param int $id - the ID of the region.
     * @return object
     */
    public function getAfterLocationsForRegion($id, $filter = null)
    {
        $locationController = new LocationsController($this->getAuth(), $filter);
        return $locationController->getResource('for', array('region', $id, 'after'));
    }

    /**
     * Get a list of locations for a project.
     * @param int $id - the ID of the project to get locations for.
     * @return object
     */
    public function getAfterLocationsForProject($id, $filter = null)
    {
        $locationController = new LocationsController($this->getAuth(), $filter);
        return $locationController->getResource('for', array('project', $id, 'after'));
    }

    /**
     * Get a list of locations for a dataset, using the dataset's ID.
     * @param int $id - the ID of the dataset
     * @return object
     */
    public function getAfterLocationsForDataset($id, $filter = null)
    {
        $locationController = new LocationsController($this->getAuth(), $filter);
        return $locationController->getResource('for', array('dataset', $id, 'after'));
    }

    /**
     * Get a list of bounds for a programme, using the programme's ID.
     * @param int $id - the ID of the programme.
     * @return object
     */
    public function getBoundsForProgramme($id, $filter = null)
    {
        $boundController = new BoundsController($this->getAuth(), $filter);
        return $boundController->getResource('for', array('programme', $id));
    }


    /**
     * Get a list of bounds for a region, using the region's ID.
     * @param int $id - the ID of the region.
     * @return object
     */
    public function getBoundsForRegion($id, $filter = null)
    {
        $boundController = new BoundsController($this->getAuth(), $filter);
        return $boundController->getResource('for', array('region', $id));
    }

    /**
     * Get a list of bounds for a project.
     * @param int $id - the ID of the project to get bounds for.
     * @return object
     */
    public function getBoundsForProject($id, $filter = null)
    {
        $boundController = new BoundsController($this->getAuth(), $filter);
        return $boundController->getResource('for', array('project', $id));
    }

    /**
     * Get a list of bounds for a dataset, using the dataset's ID.
     * @param int $id - the ID of the dataset
     * @return object
     */
    public function getBoundsForDataset($id, $filter = null)
    {
        $boundController = new BoundsController($this->getAuth(), $filter);
        return $boundController->getResource('for', array('dataset', $id));
    }

    /**
     * Alias for getBeforeFatalities.
     * This is deprecated, please use getBeforeFatalities instead.
     * @param int $id - the location ID of the fatalities
     * @param int $dataset_id - the ID of the dataset the fatality row is in.
     * @return object
     */
    public function getFatalities($id, $dataset_id, $filter = null)
    {
        $msg = 'getFatalities is deprecated. ' .
            'Please use getBeforeFatalities instead.';
        trigger_error($msg, E_USER_NOTICE);
        return $this->getBeforeFatalities($id, $dataset_id, $filter);
    }


    /**
     * Fetches fatalities for a specified location. You must specify the location ID,
     * and the ID of the dataset it belongs to.
     * @param int $id - the location ID of the fatalities
     * @param int $dataset_id - the ID of the dataset the fatality row is in.
     * @return object
     */
    public function getBeforeFatalities($id, $dataset_id, $filter = null)
    {
        $fatalitiesController = new FatalitiesController($this->getAuth(), $filter);
        return $fatalitiesController->getResource($id, array('before', $dataset_id));
    }


    /**
     * Fetches fatalities for a specified location. You must specify the location ID,
     * and the ID of the dataset it belongs to.
     * @param int $id - the location ID of the fatalities
     * @param int $dataset_id - the ID of the dataset the fatality row is in.
     * @return object
     */
    public function getAfterFatalities($id, $dataset_id, $filter = null)
    {
        $fatalitiesController = new FatalitiesController($this->getAuth(), $filter);
        return $fatalitiesController->getResource($id, array('after', $dataset_id));
    }


    /**
     * Get a list of fatalities for a programme, using the programme's ID.
     * @param int $id - the ID of the programme.
     * @return object
     */
    public function getFatalitiesForProgramme($id, $filter = null)
    {
        $msg = 'getFatalitiesForProgramme is deprecated. ' .
            'Please use getBeforeFatalitiesForProgramme instead.';
        trigger_error($msg, E_USER_NOTICE);
        return $this->getBeforeFatalitiesForProgramme($id, $filter);
    }


    /**
     * Get a list of "before" fatalities for a programme, using the programme's ID.
     * @param int $id - the ID of the programme.
     * @return object
     */
    public function getBeforeFatalitiesForProgramme($id, $filter = null)
    {
        $fatalitiesController = new FatalitiesController($this->getAuth(), $filter);
        return $fatalitiesController->getResource('for', array('programme', $id, 'before'));
    }


    /**
     * Get a list of "after" fatalities for a programme, using the programme's ID.
     * @param int $id - the ID of the programme.
     * @return object
     */
    public function getAfterFatalitiesForProgramme($id, $filter = null)
    {
        $fatalitiesController = new FatalitiesController($this->getAuth(), $filter);
        return $fatalitiesController->getResource('for', array('programme', $id, 'after'));
    }


    /**
     * Get a list of fatalities for a region, using the region's ID.
     * @param int $id - the ID of the region.
     * @return object
     */
    public function getFatalitiesForRegion($id, $filter = null)
    {
        $msg = 'getFatalitiesForRegion is deprecated. ' .
            'Please use getBeforeFatalitiesForRegion instead.';
        trigger_error($msg, E_USER_NOTICE);
        return $this->getBeforeFatalitiesForRegion($id, $filter);
    }


    /**
     * Get a list of "before" fatalities for a region, using the region's ID.
     * @param int $id - the ID of the region.
     * @return object
     */
    public function getBeforeFatalitiesForRegion($id, $filter = null)
    {
        $fatalitiesController = new FatalitiesController($this->getAuth(), $filter);
        return $fatalitiesController->getResource('for', array('region', $id, 'before'));
    }


    /**
     * Get a list of "after" fatalities for a region, using the region's ID.
     * @param int $id - the ID of the region.
     * @return object
     */
    public function getAfterFatalitiesForRegion($id, $filter = null)
    {
        $fatalitiesController = new FatalitiesController($this->getAuth(), $filter);
        return $fatalitiesController->getResource('for', array('region', $id, 'after'));
    }


    /**
     * Get a list of fatalities for a project, using the project's ID.
     * @param int $id - the ID of the project.
     * @return object
     */
    public function getFatalitiesForProject($id, $filter = null)
    {
        $msg = 'getFatalitiesForProject is deprecated. ' .
            'Please use getBeforeFatalitiesForProject instead.';
        trigger_error($msg, E_USER_NOTICE);
        return $this->getBeforeFatalitiesForProject($id, $filter);
    }


    /**
     * Get a list of "before" fatalities for a project, using the project's ID.
     * @param int $id - the ID of the project we are getting fatalities for.
     * @return object
     */
    public function getBeforeFatalitiesForProject($id, $filter = null)
    {
        $fatalitiesController = new FatalitiesController($this->getAuth(), $filter);
        return $fatalitiesController->getResource('for', array('project', $id, 'before'));
    }


    /**
     * Get a list of "after" fatalities for a project, using the project's ID.
     * @param int $id - the ID of the project we are getting fatalities for.
     * @return object
     */
    public function getAfterFatalitiesForProject($id, $filter = null)
    {
        $fatalitiesController = new FatalitiesController($this->getAuth(), $filter);
        return $fatalitiesController->getResource('for', array('project', $id, 'after'));
    }


    /**
     * Get a list of the before fatalities for a dataset, using the dataset's ID.
     * This is deprecated, please use getBeforeFatalitiesForDataset instead.
     *
     * @param int $id
     * @return object
     */
    public function getFatalitiesForDataset($id, $filter = null)
    {
        $msg = 'getFatalitiesForDataset is deprecated. ' .
            'Please use getBeforeFatalitiesForDataset instead.';
        trigger_error($msg, E_USER_NOTICE);
        return $this->getBeforeFatalitiesForDataset($id, $filter);
    }


    /**
     * Get a list of the before fatalities for a dataset, using the dataset's ID.
     * @param int $id - the ID of the dataset we are getting fatalities for.
     * @return object
     */
    public function getBeforeFatalitiesForDataset($id, $filter = null)
    {
        $fatalitiesController = new FatalitiesController($this->getAuth(), $filter);
        return $fatalitiesController->getResource('for', array('dataset', $id, 'before'));
    }


    /**
     * Get a list of the after fatalities for a dataset, using the dataset's ID.
     * @param int $id - the ID of the dataset we are getting fatalities for.
     * @return object
     */
    public function getAfterFatalitiesForDataset($id, $filter = null)
    {
        $fatalitiesController = new FatalitiesController($this->getAuth(), $filter);
        return $fatalitiesController->getResource('for', array('dataset', $id, 'after'));
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
        $starRatingController = new StarRatingsController($this->getAuth());
        $request = $starRatingController->getBeforeStarRatingsRequest($id, $dataset_id, $filter);
        $request->send();
        return $request->getResponse();
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
        $request = $starRatingController->getBeforeStarRatingsForDatasetRequest($id, $filter);
        $request->send();
        return $request->getResponse();
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
        $request = $starRatingController->getAfterStarRatingsForDatasetRequest($id, $filter);
        $request->send();
        return $request->getResponse();
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
            $dataArray = array('data' => $data);
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
     * Imports a CSV file from the specified url. The CSV file is expected to have a header
     * row that will be ignored.
     * @param $dataset_id - the ID of the dataset we wish to import for.
     * @param string $url - the url to the CSV file we wish to import. Temporary pre-signed s3 urls
     *                      recommended.
     * @return \iRAP\VidaSDK\Models\ImportResponse
     */
    public function importData($dataset_id, $url)
    {
        $dataController = new DataController($this->getAuth());
        return $dataController->importData($dataset_id, $url);
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

    /**
     * Get a list of star rating results summary for a programme, using the programme's ID.
     *
     * @param int $id
     * @return object
     */
    public function getStarRatingResultsSummaryForProgramme($id, $filter = null)
    {
        $starratingresultssummaryController = new StarRatingResultsSummaryController($this->getAuth(), $filter);
        return $starratingresultssummaryController->getResource('for', array('programme', $id));
    }

    /**
     * Get a list of star rating results summary for a region, using the region's ID.
     *
     * @param int $id
     * @return object
     */
    public function getStarRatingResultsSummaryForRegion($id, $filter = null)
    {
        $starratingresultssummaryController = new StarRatingResultsSummaryController($this->getAuth(), $filter);
        return $starratingresultssummaryController->getResource('for', array('region', $id));
    }

    /**
     * Get a list of star rating results summary for a project, using the project's ID.
     *
     * @param int $id
     * @return object
     */
    public function getStarRatingResultsSummaryForProject($id, $filter = null)
    {
        $starratingresultssummaryController = new StarRatingResultsSummaryController($this->getAuth(), $filter);
        return $starratingresultssummaryController->getResource('for', array('project', $id));
    }

    /**
     * Get a list of star rating results summary for a dataset, using the dataset's ID.
     *
     * @param int $id
     * @return object
     */
    public function getStarRatingResultsSummaryForDataset($id, $filter = null)
    {
        $starratingresultssummaryController = new StarRatingResultsSummaryController($this->getAuth(), $filter);
        return $starratingresultssummaryController->getResource('for', array('dataset', $id));
    }

    /**
     * Get a report filter by ID.
     *
     * @param int $id
     * @return object
     */
    public function getReportFilter($id, $filter = null)
    {
        $reportFiltersController = new ReportFiltersController($this->getAuth(), $filter);
        return $reportFiltersController->getResource($id);
    }

    /**
     * Creates a new report filter using the supplied filter json
     *
     * @param $filter_json
     * @return object
     */
    public function addReportFilter($filter_json)
    {
        $reportFiltersController = new ReportFiltersController($this->getAuth());
        return $reportFiltersController->postResource(array("filter_json"=>$filter_json));
    }

    /**
     * Invite a user
     * @param string $email
     * @param string $first_name
     * @param string $last_name
     * @param array $permissions List of datasets (IDs) to which the user will be
     * added as a 'Reader'
     * @return object A token will also be returned which will be required when
     * accepting an invitation
     */
    public function inviteUser(string $email, string $first_name = null,
                               string $last_name = null, array $permissions = [])
    {
        $inviteCtrl = new InviteController($this->getAuth());
        return $inviteCtrl->invite($email, $first_name, $last_name, $permissions);
    }

    /**
     * Get invitation details
     * @param mixed $value Invitation ID or email address of the invited user or
     * token if the user was previously invited
     * @return object
     */
    public function getInviteDetails($value)
    {
        $inviteCtrl = new InviteController($this->getAuth());
        return $inviteCtrl->details($value);
    }

    /**
     * Accept an invitation
     * @param string $token
     * @return object
     */
    public function acceptInvitation(string $token)
    {
        $inviteCtrl = new InviteController($this->getAuth());
        return $inviteCtrl->accept($token);
    }

    /**
     * Gets data for SRIP (id will always be ignored)
     * @param int $id
     * @return object
     */
    public function getSRIP($modelId = 2, $filter = null)
    {
        $appliedCountermeasuresController = new AppliedCountermeasuresController($this->getAuth(), $filter);
        return $appliedCountermeasuresController->getResource('get_srip', $modelId);
    }

    /**
     * Add new access type
     * @param string $identifier A new unique access identifier
     * @param string $name A human friendly name for this access, will be helpful for managers
     * @return Response
     */
    public function addAccess(string $identifier, string $name): Response
    {
        $accessController = new AccessController($this->getAuth());
        return $accessController->postResource([
            'identifier' => $identifier,
            'name' => $name
        ]);
    }

    /**
     * Deletes existing access type from the system, using the unique identifier. All related permission will also be deleted
     * @param string $identifier
     * @return Response
     */
    public function deleteAccess(string $identifier): Response
    {
        $accessController = new AccessController($this->getAuth());
        return $accessController->deleteResource($identifier);
    }

    /**
     * Check if a user has permission for an access, using the unique identifier and user id
     * @param string $identifier
     * @param int $userId
     * @return Response
     */
    public function hasPermission(string $identifier, int $userId): Response
    {
        $permissionController = new PermissionController($this->getAuth());
        return $permissionController->hasPermission($userId, $identifier);
    }

    /**
     * Check if a user has permission for any access, using user's id
     * @param int $userId
     * @return Response
     */
    public function hasAnyPermission(int $userId): Response
    {
        $permissionController = new PermissionController($this->getAuth());
        return $permissionController->hasPermission($userId);
    }

    /**
     * Set/Unset permission of a user for an access
     * @param string $identifier
     * @param int $userId
     * @param bool $permission Set this as false to remove permission
     * @return Response
     */
    public function setPermission(string $identifier, int $userId, bool $permission): Response
    {
        $permissionController = new PermissionController($this->getAuth());
        return $permissionController->setPermission(...[
            'identifier' => $identifier,
            'userId' => $userId,
            'hasPermission' => $permission
        ]);
    }

    /**
     * Add permission for an access to a user
     * @param string $identifier
     * @param int $userId
     * @return Response
     */
    public function addPermission(string $identifier, int $userId): Response
    {
        return $this->setPermission($identifier, $userId, true);
    }

    /**
     * Delete permission for an access of a user
     * @param string $identifier
     * @param int $userId
     * @return Response
     */
    public function deletePermission(string $identifier, int $userId): Response
    {
        return $this->setPermission($identifier, $userId, false);
    }

    /**
     * Check if a user is manager for an access, using the unique identifier and user id
     * @param string $identifier
     * @param int $userId
     * @return Response
     */
    public function isManager(string $identifier, int $userId): Response
    {
        $permissionController = new PermissionController($this->getAuth());
        return $permissionController->isManager($userId, $identifier);
    }

    /**
     * Check if a user is manager for any access, using user's id
     * @param int $userId
     * @return Response
     */
    public function isAnyManager(int $userId): Response
    {
        $permissionController = new PermissionController($this->getAuth());
        return $permissionController->isManager($userId);
    }

    /**
     * Set/Unset a user as manager for an access
     * @param string $identifier
     * @param int $userId
     * @param bool $manager Set this as false to remove user as manager
     * @return Response
     */
    public function setManager(string $identifier, int $userId, bool $manager): Response
    {
        $permissionController = new PermissionController($this->getAuth());
        return $permissionController->setPermission(...[
            'identifier' => $identifier,
            'userId' => $userId,
            'isManager' => $manager
        ]);
    }

    /**
     * Add a user as manager for an access
     * @param string $identifier
     * @param int $userId
     * @return Response
     */
    public function addManager(string $identifier, int $userId): Response
    {
        return $this->setManager($identifier, $userId, true);
    }

    /**
     * Remove a user as manager for an access
     * @param string $identifier
     * @param int $userId
     * @return Response
     */
    public function deleteManager(string $identifier, int $userId): Response
    {
        return $this->setManager($identifier, $userId, false);
    }
}