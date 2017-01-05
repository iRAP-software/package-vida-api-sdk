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
        $encrypted_password = self::encrypt($password, \iRAP\VidaSDK\APP_PRIVATE_KEY);
        $request = new \iRAP\VidaSDK\Models\APIRequest();
        $request->setUrl("auth/register");
        $request->setPostData(array("email"=>$email,"password"=>$encrypted_password));
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
    
    /**
     * Encrypt a String
     * @param String $message - the message to encrypt
     * @param String $key - the key to encrypt and then decrypt the message.
     * @return String - the encryptd form of the string
     */
    private static function encrypt($message, $key)
    {
        $md5Key = md5($key);
        
        $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, 
                                    $md5Key, 
                                    $message, 
                                    MCRYPT_MODE_CBC, 
                                    md5($md5Key));
        
        $encoded_encryption = base64_encode($encrypted);
        return $encoded_encryption;
    }
}
