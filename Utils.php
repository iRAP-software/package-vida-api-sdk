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
        $token->status = $appToken->m_status;
        $token->code = $appToken->m_httpCode;
        if (!empty($appToken->m_error))
        {
            $token->error = $appToken->m_error;
        }
        return $token;
    }
}