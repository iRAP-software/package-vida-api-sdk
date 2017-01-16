<?php

/**************************************************************************************************
 * This file is for internal use by the ViDA SDK. It should not be altered by users
 **************************************************************************************************
 * 
 * Contains the authentication methods used to request and set tokens. Also handles encryption
 * of the user password, before it is transmitted to the API
 * 
 */

namespace iRAP\VidaSDK\Controllers;

class AuthController extends AbstractResourceController
{
    /**
     * Sends the user's email and password to the API and gets a user token back. The user token 
     * should be stored locally and used for all future requests. Email and password should NOT
     * be stored locally. The password is encrypted using the APP_PRIVATE KEY before transmission.
     * 
     * @param string $email
     * @param string $password
     * @return object
     */
    public static function getUserToken($auth, $email, $password)
    {
        $encrypted_password = self::encrypt($password, $auth->getAppPrivateKey());
        $request = new \iRAP\VidaSDK\Models\APIRequest($auth);
        $request->setUrl("auth/register");
        $request->setPostData(array("email"=>$email,"password"=>$encrypted_password));
        $request->send();
        return parent::response($request);
    }
    
    /**
     * Requests an APP token for a new app. This request will normally be rejected, but exists
     * for use the API administration system, which uses the SDK
     * 
     * @param string $name
     * @param string $owner
     * @return object
     */
    public static function getAppToken($auth, $name, $owner)
    {
        $request = new \iRAP\VidaSDK\Models\APIRequest($auth);
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
