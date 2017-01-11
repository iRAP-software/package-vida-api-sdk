<?php

/* 
 * This interface specifies the methods available to users of the ViDA SDK;
 */

namespace iRAP\VidaSDK;

interface apiInterface
{
    public function getUserToken($email, $password);
    
    public function setUserToken($userAuthID, $userAPIKey, $userPrivateKey);
    
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
}