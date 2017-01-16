<?php

/* 
 * This interface specifies the methods available to users of the ViDA SDK. It is implemented by
 * Client.php
 */

namespace iRAP\VidaSDK;

interface ApiInterface
{
    public function getUserToken($email, $password);
    
    public function getUsers();
    
    public function addUser($name, $email, $password);
    
    public function replaceUser($id, $name, $email, $password);
    
    public function deleteUser($id);
    
    public function getDatasets();
    
    public function addDataset($name, $road_data);
    
    public function replaceDataset($id, $name, $road_data);
    
    public function deleteDataset($id);
    
    public function getDatasetsForProgramme($id);
    
    public function getDatasetsForRegion($id);
    
    public function getDatasetsForProject($id);
    
    public function getProgrammes();
    
    public function addProgramme($name, $manager_id);
    
    public function replaceProgramme($id, $name, $manager_id);
    
    public function deleteProgramme($id);
    
    public function getProgrammeUsers($id);
    
    public function getRegions();
    
    public function addRegion($name, $programme_id, $manager_id);
    
    public function replaceRegion($id, $name, $programme_id, $manager_id);
    
    public function deleteRegion($id);
    
    public function getRegionUsers($id);
    
    public function getRegionsForProgramme($id);
    
    public function getProjects();
    
    public function addProject($name, $region_id, $manager_id, $model_id);
    
    public function replaceProject($id, $name, $region_id, $manager_id, $model_id);
    
    public function deleteProject($id);
    
    public function getProjectUsers($id);
    
    public function getProjectsForProgramme($id);
    
    public function getProjectsForRegion($id);
    
    public function getVariables();
    
    public function addVariable($variables);
    
    public function replaceVariable($id, $variables);
    
    public function deleteVariable($id);
    
    public function getVariablesForDataset($id);
    
    public function getRoadAttributes($id, $dataset_id);
    
    public function addRoadAttribute($roadAttributes, $dataset_id);
    
    public function replaceRoadAttribute($id, $roadAttributes, $dataset_id);
    
    public function deleteRoadAttribute($id, $dataset_id);
    
    public function getRoadAttributesForProgramme($id);
    
    public function getRoadAttributesForRegion($id);
    
    public function getRoadAttributesForProject($id);
    
    public function getRoadAttributesForDataset($id);
    
    public function getFatalities($id, $dataset_id);
    
    public function addFatalities($fatalities, $dataset_id);
    
    public function replaceFatalities($id, $fatalities, $dataset_id);
    
    public function deleteFatalities($id, $dataset_id);
    
    public function getFatalitiesForProgramme($id);
    
    public function getFatalitiesForRegion($id);
    
    public function getFatalitiesForProject($id);
    
    public function getFatalitiesForDataset($id);
    
    public function getBeforeStarRatings($id, $dataset_id);
    
    public function addBeforeStarRating($starratings, $dataset_id);
    
    public function replaceBeforeStarRating($id, $starratings, $dataset_id);
    
    public function deleteBeforeStarRating($id, $dataset_id);
    
    public function getBeforeStarRatingsForProgramme($id);
    
    public function getBeforeStarRatingsForRegion($id);
    
    public function getBeforeStarRatingsForProject($id);
    
    public function getBeforeStarRatingsForDataset($id);
    
    public function getAfterStarRatings($id, $dataset_id);
    
    public function addAfterStarRating($starratings, $dataset_id);
    
    public function replaceAfterStarRating($id, $starratings, $dataset_id);
    
    public function deleteAfterStarRating($id, $dataset_id);
    
    public function getAfterStarRatingsForProgramme($id);
    
    public function getAfterStarRatingsForRegion($id);
    
    public function getAfterStarRatingsForProject($id);
    
    public function getAfterStarRatingsForDataset($id);
}