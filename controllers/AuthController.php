<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace iRAP\VidaSDK\Controllers;

class AuthController extends AbstractResourceController
{
    public static function getUserToken($email, $password)
    {
        $request = new \iRAP\VidaSDK\Models\APIRequest();
        $request->setUrl("auth/register");
        $request->setPostData(array("email"=>$email,"password"=>$password));
        $request->send();
        return parent::response($request);
    }
    
    public static function setUserToken($userAuthID, $userAPIKey, $userPrivateKey)
    {
        define('iRAP\VidaSDK\USER_AUTH_ID', $userAuthID);
        define('iRAP\VidaSDK\USER_API_KEY', $userAPIKey);
        define('iRAP\VidaSDK\USER_PRIVATE_KEY', $userPrivateKey);
    }
    
    public static function getAppToken($name, $owner)
    {
        $request = new \iRAP\VidaSDK\Models\APIRequest();
        $request->setUrl("auth/register_app");
        $request->setPostData(array("name"=>$name,"owner"=>$owner));
        $request->send();
        return parent::response($request);
    }
}
