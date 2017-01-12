<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace iRAP\VidaSDK;

class Utils extends Client
{
    
    public function __construct($appAuthID, $appAPIKey, $appPrivateKey)
    {
        parent::__construct($appAuthID, $appAPIKey, $appPrivateKey);
    }
    
    public function registerApp($name, $owner)
    {
        $appToken = Controllers\AuthController::getAppToken($name, $owner);
        $token = new \stdClass();
        if (isset($appToken->response))
        {
            $token->appAuthId = $appToken->response->auth_id;
            $token->appApiKey = $appToken->response->api_key;
            $token->appPrivateKey = $appToken->response->api_secret;
        }
        $token->status = $appToken->status;
        $token->code = $appToken->ode;
        if (!empty($appToken->error))
        {
            $token->error = $appToken->error;
        }
        return $token;
    }
    
    public function getDatapoints($id, $dataset_id)
    {
        $datapointController = new Controllers\DatapointsController();
        return $datapointController->getResourceWithParent('datapoints', $id, $dataset_id);
    }
    
    public function addDatapoint($datapoints, $dataset_id)
    {
        $datapointController = new Controllers\DatapointsController();
        return $datapointController->postResourceWithParent('datapoints', $datapoints, $dataset_id);
    }
    
    public function replaceDatapoint($id, $datapoints, $dataset_id)
    {
        $datapointController = new Controllers\DatapointsController();
        return $datapointController->putResourceWithParent('datapoints', $id, $datapoints, $dataset_id);
    }
    
    public function deleteDatapoint($id, $dataset_id)
    {
        $datapointController = new Controllers\DatapointsController();
        return $datapointController->deleteResourceWithParent('datapoints', $id, $dataset_id);
    }
    
    public function getDatapointsForProgramme($id)
    {
        $datapointController = new Controllers\DatapointsController();
        return $datapointController->getResourceForAncestor('datapoints', 'programme', $id);
    }
    
    public function getDatapointsForRegion($id)
    {
        $datapointController = new Controllers\DatapointsController();
        return $datapointController->getResourceForAncestor('datapoints', 'region', $id);
    }
    
    public function getDatapointsForProject($id)
    {
        $datapointController = new Controllers\DatapointsController();
        return $datapointController->getResourceForAncestor('datapoints', 'project', $id);
    }
    
    public function getDatapointsForDataset($id)
    {
        $datapointController = new Controllers\DatapointsController();
        return $datapointController->getResourceForAncestor('datapoints', 'dataset', $id);
    }
    
}