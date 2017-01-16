<?php

/* 
 * This file contains some additional functionality. The methods here are not intended for use by
 * regular users and calling them will result in failed permissions checks.
 */

namespace iRAP\VidaSDK;

class Utils extends Client
{
    
    private $m_auth;
    public function __construct($appAuthID, $appAPIKey, $appPrivateKey)
    {
        parent::__construct($appAuthID, $appAPIKey, $appPrivateKey);
        $this->auth = new Models\Authentication($appAuthID, $appAPIKey, $appPrivateKey);
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
    
}