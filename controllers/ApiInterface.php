<?php

/* 
 * This interface specifies the methods available to users of the ViDA SDK. It is implemented by
 * App.php and User.php
 */

namespace iRAP\VidaSDK\Controllers;

interface ApiInterface
{
    public function requestUserPermissions($returnUrl);

    public function getUsers();

    public function getUserAccess($userID);

    public function getDatasets();

    public function addDataset(string $name, $project_id, $manager_id, int $country_id, int $type, string $assessment_date, string $description);

    public function updateDataset($id, string $name, $project_id, $manager_id, int $country_id, int $type, string $assessment_date, string $description);

    public function updateDatasetStatus($id, $status_id);

    public function replaceDataset($id, string $name, $project_id, $manager_id, int $type, string $assessment_date, string $description);

    public function deleteDataset($id);

    public function getDatasetUsers($id);

    public function addDatasetUser($dataset_id, $user_id);

    public function deleteDatasetUser($dataset_id, $user_id);

    public function getDatasetsForProgramme($id);

    public function getDatasetsForRegion($id);

    public function getDatasetsForProject($id);

    public function processDataset($id, $filter = null);

    public function reprocessDataset($datasetID);

    public function validateAndProcessDataset($id, $filter = null);

    public function getProgrammes();

    public function addProgramme($name, $manager_id);

    public function updateProgramme($id, $name, $manager_id);

    public function replaceProgramme($id, $name, $manager_id);

    public function deleteProgramme($id);

    public function getProgrammeUsers($id);

    public function addProgrammeUser($programme_id, $user_id);

    public function deleteProgrammeUser($programme_id, $user_id);

    public function getRegions();

    public function addRegion($name, $programme_id, $manager_id);

    public function updateRegion($id, $name, $programme_id, $manager_id);

    public function replaceRegion($id, $name, $programme_id, $manager_id);

    public function deleteRegion($id);

    public function getRegionUsers($id);

    public function addRegionUser($region_id, $user_id);

    public function deleteRegionUser($region_id, $user_id);

    public function getRegionsForProgramme($id);

    public function getProjects();

    public function addProject($name, $region_id, $manager_id, $model_id);

    public function updateProject($id, $name, $region_id, $manager_id);

    public function replaceProject($id, $name, $region_id, $manager_id);

    public function deleteProject($id);

    public function getProjectUsers($id);

    public function addProjectUser($project_id, $user_id);

    public function deleteProjectUser($project_id, $user_id);

    public function getProjectsForProgramme($id);

    public function getProjectsForRegion($id);

    public function getVariables();

    public function updateVariable($id, $variables);

    public function replaceVariable($id, $variables);

    public function getVariablesForDataset($id);

    public function getRoadAttributes($id, $dataset_id);

    public function getRoadAttributesForProgramme($id);

    public function getRoadAttributesForRegion($id);

    public function getRoadAttributesForProject($id);

    public function getRoadAttributesForDataset($id);

    public function getBeforeLocations($id, $dataset_id, $filter = null);

    public function getBeforeLocationsForProgramme($id, $filter = null);

    public function getBeforeLocationsForRegion($id, $filter = null);

    public function getBeforeLocationsForProject($id, $filter = null);

    public function getBeforeLocationsForDataset($id, $filter = null);

    public function getAfterLocations($id, $dataset_id, $filter = null);

    public function getAfterLocationsForProgramme($id, $filter = null);

    public function getAfterLocationsForRegion($id, $filter = null);

    public function getAfterLocationsForProject($id, $filter = null);

    public function getAfterLocationsForDataset($id, $filter = null);

    public function getBoundsForProgramme($id, $filter = null);

    public function getBoundsForRegion($id, $filter = null);

    public function getBoundsForProject($id, $filter = null);

    public function getBoundsForDataset($id, $filter = null);

    public function getFatalities($id, $dataset_id);

    public function getFatalitiesForProgramme($id);

    public function getFatalitiesForRegion($id);

    public function getFatalitiesForProject($id);

    public function getFatalitiesForDataset($id);

    public function getBeforeStarRatings($id, $dataset_id);

    public function getBeforeStarRatingsForProgramme($id);

    public function getBeforeStarRatingsForRegion($id);

    public function getBeforeStarRatingsForProject($id);

    public function getBeforeStarRatingsForDataset($id);

    public function getBeforeDecimalStarRatingsForDataset(int $id, $filter = null);

    public function getAfterStarRatings($id, $dataset_id);

    public function getAfterStarRatingsForProgramme($id);

    public function getAfterStarRatingsForRegion($id);

    public function getAfterStarRatingsForProject($id);

    public function getAfterStarRatingsForDataset($id);

    public function getAfterDecimalStarRatingsForDataset(int $id, $filter = null);

    public function getData($id, $dataset_id);

    public function addData($data, $dataset_id);

    public function updateData($id, $data, $dataset_id);

    public function replaceData($id, $data, $dataset_id);

    public function deleteData($id, $dataset_id);

    public function getDataForProgramme($id);

    public function getDataForRegion($id);

    public function getDataForProject($id);

    public function getDataForDataset($id);

    public function getCountries();

    public function getPermissions();

    public function getStarRatingResultsSummaryForProgramme($id, $filter = null);

    public function getStarRatingResultsSummaryForRegion($id, $filter = null);

    public function getStarRatingResultsSummaryForProject($id, $filter = null);

    public function getStarRatingResultsSummaryForDataset($id, $filter = null);

    public function getReportFilter($id, $filter = null);

    public function addReportFilter($filter_json);

    public function inviteUser(string $email, string $first_name = null, string $last_name = null, array $permissions = []);

    public function getInviteDetails($value);

    public function acceptInvitation(string $token);

    public function getSRIP($modelId = 2, $filter = null);

    public function addAccess(string $identifier, string $name);

    public function deleteAccess(string $identifier);

    public function hasPermission(string $identifier, int $userId);

    public function hasAnyPermission(int $userId);

    public function setPermission(string $identifier, $userId, bool $permission);

    public function addPermission(string $identifier, int ...$userId);

    public function deletePermission(string $identifier, int ...$userId);

    public function isManager(string $identifier, int $userId);

    public function isAnyManager(int $userId);

    public function setManager(string $identifier, $userId, bool $manager);

    public function addManager(string $identifier, int ...$userId);

    public function deleteManager(string $identifier, int ...$userId);

    public function requestDownloadFileExternal(string $type, int $datasetId, string $filename, string $language = 'en-gb');

    public function getRequiredSpeedsForDataset(int $datasetId, ?array $userGroup = null, $filter = null);

    public function calculateRoadClassificationsForDataset(int $datasetId, int $rulesetId, $filter = null);

    public function getRoadClassificationsForDataset(int $datasetId, $filter = null);

    public function getRoadClassificationsRulesets($filter = null);

    public function getRoadClassificationsRulesetFunctions($rulesetId, $filter = null);
}