<?php

/* 
 * This interface specifies the methods available to users of the ViDA SDK. It is implemented by
 * App.php and User.php
 */

namespace iRAP\VidaSDK\Controllers;

interface ApiInterface
{
    public function getUserToken($email, $password);
    
    public function requestUserPermissions($returnUrl);
    
    public function getUsers(int $id = null, array|string $filter = null);
    
    public function addUser($name, $email, $password);
    
    public function updateUser($id, $name, $email, $password);
    
    public function replaceUser($id, $name, $email, $password);
    
    public function deleteUser($id);
    
    public function getUserAccess($userID, array|string $filter = null);
    
    public function getDatasets(int $id = null, array|string $filter = null);
    
    public function addDataset(string $name, $project_id, $manager_id, int $type, string $assessment_date = null, string $description = '');
    
    public function updateDataset($id, string $name, $project_id, $manager_id, int $type, string $assessment_date = null, string $description = '');
    
    public function updateDatasetStatus($id, $status_id);
    
    public function replaceDataset($id, string $name, $project_id, $manager_id, int $type, string $assessment_date = null, string $description = '');
    
    public function deleteDataset($id);
    
    public function getDatasetUsers($id, array|string $filter = null);
    
    public function addDatasetUser($dataset_id, $user_id, int $access_level = 1, int $user_manager = 0);
    
    public function deleteDatasetUser($dataset_id, $user_id);
    
    public function getDatasetsForProgramme($id, array|string $filter = null);
    
    public function getDatasetsForRegion($id, array|string $filter = null);
    
    public function getDatasetsForProject($id, array|string $filter = null);
    
    public function processDataset($id, array|string $filter = null);
    
    public function reprocessDataset($datasetID);
    
    public function validateAndProcessDataset($id, array|string $filter = null);
    
    public function getProgrammes(int $id = null, array|string $filter = null);
    
    public function addProgramme($name, $manager_id);
    
    public function updateProgramme($id, $name, $manager_id);
    
    public function replaceProgramme($id, $name, $manager_id);
    
    public function deleteProgramme($id);
    
    public function getProgrammeUsers($id, array|string $filter = null);
    
    public function addProgrammeUser($programme_id, $user_id, int $access_level = 1, int $user_manager = 0);
    
    public function deleteProgrammeUser($programme_id, $user_id);
    
    public function getRegions(int $id = null, array|string $filter = null);
    
    public function addRegion($name, $programme_id, $manager_id);
    
    public function updateRegion($id, $name, $programme_id, $manager_id);
    
    public function replaceRegion($id, $name, $programme_id, $manager_id);
    
    public function deleteRegion($id);
    
    public function getRegionUsers($id, array|string $filter = null);
    
    public function addRegionUser($region_id, $user_id, int $access_level = 1, int $user_manager = 0);
    
    public function deleteRegionUser($region_id, $user_id);
    
    public function getRegionsForProgramme($id, array|string $filter = null);
    
    public function getProjects(int $id = null, array|string $filter = null);
    
    public function addProject($name, $region_id, $manager_id, $model_id, $country_id);
        
    public function updateProject($id, $name, $region_id, $manager_id);
    
    public function replaceProject($id, $name, $region_id, $manager_id);
    
    public function deleteProject($id);
    
    public function getProjectUsers($id, array|string $filter = null);
    
    public function addProjectUser($project_id, $user_id, int $access_level = 1, int $user_manager = 0);
    
    public function deleteProjectUser($project_id, $user_id);
    
    public function getProjectsForProgramme($id, array|string $filter = null);
    
    public function getProjectsForRegion($id, array|string $filter = null);
    
    public function getVariables(int $id = null, array|string $filter = null);
    
    public function updateVariable($id, $variables);
    
    public function replaceVariable($id, $variables);
    
    public function getVariablesForDataset($id, array|string $filter = null);
    
    public function getRoadAttributes($id, $dataset_id, array|string $filter = null);
    
    public function getRoadAttributesForProgramme($id, array|string $filter = null);
    
    public function getRoadAttributesForRegion($id, array|string $filter = null);
    
    public function getRoadAttributesForProject($id, array|string $filter = null);
    
    public function getRoadAttributesForDataset($id, array|string $filter = null);
    
    public function getBeforeLocations($id, $dataset_id, array|string $filter = null);
    
    public function getBeforeLocationsForProgramme($id, array|string $filter = null);
    
    public function getBeforeLocationsForRegion($id, array|string $filter = null);
    
    public function getBeforeLocationsForProject($id, array|string $filter = null);
    
    public function getBeforeLocationsForDataset($id, array|string $filter = null);
            
    public function getAfterLocations($id, $dataset_id, array|string $filter = null);
    
    public function getAfterLocationsForProgramme($id, array|string $filter = null);
    
    public function getAfterLocationsForRegion($id, array|string $filter = null);
    
    public function getAfterLocationsForProject($id, array|string $filter = null);
    
    public function getAfterLocationsForDataset($id, array|string $filter = null);
    
    public function getBoundsForProgramme($id, array|string $filter = null);
    
    public function getBoundsForRegion($id, array|string $filter = null);
    
    public function getBoundsForProject($id, array|string $filter = null);
    
    public function getBoundsForDataset($id, array|string $filter = null);
    
    public function getFatalities($id, $dataset_id, array|string $filter = null);
    
    public function getFatalitiesForProgramme($id, array|string $filter = null);
    
    public function getFatalitiesForRegion($id, array|string $filter = null);
    
    public function getFatalitiesForProject($id, array|string $filter = null);
    
    public function getFatalitiesForDataset($id, array|string $filter = null);
    
    public function getBeforeStarRatings($id, $dataset_id, array|string $filter = null);
    
    public function getBeforeStarRatingsForProgramme($id, array|string $filter = null);
    
    public function getBeforeStarRatingsForRegion($id, array|string $filter = null);
    
    public function getBeforeStarRatingsForProject($id, array|string $filter = null);
    
    public function getBeforeStarRatingsForDataset($id, array|string $filter = null);
    
    public function getAfterStarRatings($id, $dataset_id, array|string $filter = null);
    
    public function getAfterStarRatingsForProgramme($id, array|string $filter = null);
    
    public function getAfterStarRatingsForRegion($id, array|string $filter = null);
    
    public function getAfterStarRatingsForProject($id, array|string $filter = null);
    
    public function getAfterStarRatingsForDataset($id, array|string $filter = null);
    
    public function getData($id, $dataset_id, array|string $filter = null);
    
    public function addData($data, $dataset_id);
    
    public function updateData($id, $data, $dataset_id);
    
    public function replaceData($id, $data, $dataset_id);
    
    public function deleteData($id, $dataset_id);
    
    public function getDataForProgramme($id, array|string $filter = null);
    
    public function getDataForRegion($id, array|string $filter = null);
    
    public function getDataForProject($id, array|string $filter = null);
    
    public function getDataForDataset($id, array|string $filter = null);
    
    public function getCountries(int $id = null, array|string $filter = null);
    
    public function getPermissions(array|string $filter = null);
    
    public function getStarRatingResultsSummaryForProgramme($id, array|string $filter = null);
    
    public function getStarRatingResultsSummaryForRegion($id, array|string $filter = null);
    
    public function getStarRatingResultsSummaryForProject($id, array|string $filter = null);
    
    public function getStarRatingResultsSummaryForDataset($id, array|string $filter = null);
    
    public function getReportFilter($id, array|string $filter = null);
    
    public function addReportFilter($filter_json);

    public function inviteUser(string $email, string $first_name = null, string $last_name = null, array $permissions = []);

    public function getInviteDetails($value);

    public function acceptInvitation(string $token);

    public function getSRIP($modelId = 2, array|string $filter = null);

    public function addAccess(string $identifier, string $name);

    public function deleteAccess(string $identifier);
}