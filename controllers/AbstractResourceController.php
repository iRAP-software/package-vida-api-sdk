<?php

/**************************************************************************************************
 * This file is for internal use by the ViDA SDK. It should not be altered by users
 **************************************************************************************************
 * 
 * Contains the primary methods for interacting with the API and are available to all resource
 * controllers. These methods can be overridden in the individual resoruce controllers.
 * 
 */

namespace iRAP\VidaSDK\Controllers;

abstract class AbstractResourceController
{
    private $m_auth;
    public function __construct($auth)
    {
        $this->m_auth = $auth;
    }
    /**
     * Send a GET request to the API. The $resource and $id make up the first two parts of the 
     * URL, are $args can either be a third element, or an array of elements, each of which will
     * be separated with a '/'
     * 
     * @param string $resource
     * @param mixed $id
     * @param mixed $args
     * @return object
     */
    public function getResource($resource, $id = null, $args = null)
    {
        
        $request = new \iRAP\VidaSDK\Models\APIRequest($this->m_auth);
        $request->setUrl($resource, $id, $args);
        $request->send();
        return $this->response($request);
    }
    
    /**
     * Send a POST request to the API. The $resource and $id make up the first two parts of the 
     * URL, are $args can either be a third element, or an array of elements, each of which will
     * be separated with a '/'. $data should be an array of name-value pairs, representing the 
     * names and values of the POST fields.
     * 
     * @param string $resource
     * $param array $data
     * @param mixed $id
     * @param mixed $args
     * @return object
     */
    public function postResource($resource, $data, $id = null, $args = null)
    {
        
        $request = new \iRAP\VidaSDK\Models\APIRequest();
        $request->setUrl($resource, $id, $args);
        $request->setPostData($data);
        $request->send();
        return $this->response($request);
    }
    
    /**
     * Send a PUT request to the API. The $resource and $id make up the first two parts of the 
     * URL, are $args can either be a third element, or an array of elements, each of which will
     * be separated with a '/'. $data should be an array of name-value pairs, representing the 
     * names and values of the POST fields.
     * 
     * @param string $resource
     * $param array $data
     * @param mixed $id
     * @param mixed $args
     * @return object
     */
    public function putResource($resource, $id, $data, $args = null)
    {
        $request = new \iRAP\VidaSDK\Models\APIRequest();
        $request->setUrl($resource, $id, $args);
        $request->setPutData($data);
        $request->send();
        return $this->response($request);
    }
    
    /**
     * Send a DELETE request to the API. The $resource and $id make up the first two parts of the 
     * URL, are $args can either be a third element, or an array of elements, each of which will
     * be separated with a '/'.
     * 
     * @param string $resource
     * $param array $data
     * @param mixed $id
     * @param mixed $args
     * @return object
     */
    public function deleteResource($resource, $id, $args = null)
    {
        $request = new \iRAP\VidaSDK\Models\APIRequest();
        $request->setUrl($resource, $id, $args);
        $request->setDeleteRequest();
        $request->send();
        return $this->response($request);
    }
    
    /**
     * Takes the response properties from the APIRequest() object and formats them for use by 
     * the developer.
     * 
     * @param object $request
     * @return \stdClass
     */
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

