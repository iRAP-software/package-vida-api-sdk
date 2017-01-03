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
    
    public function registerApp($email, $password)
    {
        $appToken = Controllers\AuthController::getAppToken($name, $owner);
        $token = new \stdClass();
        $token->appAuthId = $appToken->auth_id;
        $token->appApiKey = $appToken->api_key;
        $token->appPrivateKey = $appToken->api_secret;
        return $token;
    }
}