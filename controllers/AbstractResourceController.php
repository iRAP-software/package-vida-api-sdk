<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace iRAP\VidaSDK\Controllers;

abstract class AbstractResourceController
{
    
    public function getResource($resource, $id = null)
    {
        
        $request = new \iRAP\VidaSDK\Models\APIRequest();
        $request->setUrl($resource, $id);
        $request->send();
        return $this->response($request);
    }
    
    public function postResource($resource, $data)
    {
        
        $request = new \iRAP\VidaSDK\Models\APIRequest();
        $request->setUrl($resource);
        $request->setPostData($data);
        $request->send();
        return $this->response($request);
    }
    
    public function putResource($resource, $id, $data)
    {
        $request = new \iRAP\VidaSDK\Models\APIRequest();
        $request->setUrl($resource, $id);
        $request->setPutData($data);
        $request->send();
        return $this->response($request);
    }
    
    public function deleteResource($resource, $id)
    {
        $request = new \iRAP\VidaSDK\Models\APIRequest();
        $request->setUrl($resource, $id);
        $request->setDeleteRequest();
        $request->send();
        return $this->response($request);
    }
    
    public function getResourceUserAccess($resource, $id)
    {
        $request = new \iRAP\VidaSDK\Models\APIRequest();
        $request->setUrl($resource, $id, "user-access");
        $request->send();
        return $this->response($request);
    }
    
    private function response($request)
    {
        $response = new \stdClass();
        if ($request->m_result)
        {
            $response->response = json_decode($request->m_result);
        }
        $response->status = $request->m_status;
        $response->code = $request->m_httpCode;
        $response->error = $request->m_error;
        return $response;
    }
}

