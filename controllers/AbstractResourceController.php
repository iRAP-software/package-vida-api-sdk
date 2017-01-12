<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace iRAP\VidaSDK\Controllers;

abstract class AbstractResourceController
{
    
    public function getResource($resource, $id = null, $args = null)
    {
        
        $request = new \iRAP\VidaSDK\Models\APIRequest();
        $request->setUrl($resource, $id, $args);
        $request->send();
        return $this->response($request);
    }
    
    public function postResource($resource, $data, $id = null, $args = null)
    {
        
        $request = new \iRAP\VidaSDK\Models\APIRequest();
        $request->setUrl($resource, $id, $args);
        $request->setPostData($data);
        $request->send();
        return $this->response($request);
    }
    
    public function putResource($resource, $id, $data, $args = null)
    {
        $request = new \iRAP\VidaSDK\Models\APIRequest();
        $request->setUrl($resource, $id, $args);
        $request->setPutData($data);
        $request->send();
        return $this->response($request);
    }
    
    public function deleteResource($resource, $id, $args = null)
    {
        $request = new \iRAP\VidaSDK\Models\APIRequest();
        $request->setUrl($resource, $id, $args);
        $request->setDeleteRequest();
        $request->send();
        return $this->response($request);
    }
    
    public static function response($request)
    {
        $response = new \stdClass();
        if (!empty(json_decode($request->m_result, true)))
        {
            $response->response = json_decode($request->m_result);
        }
        $response->status = $request->m_status;
        $response->code = $request->m_httpCode;
        if (!empty($request->m_error))
        {
            $response->error = $request->m_error;
        }
        return $response;
    }
}

