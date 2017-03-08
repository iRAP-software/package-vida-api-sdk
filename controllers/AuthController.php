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
    protected function getResourceName()
    {
        return 'auth';
    }
    
    /**
     * Sends the user's email and password to the API and gets a user token back. The user token 
     * should be stored locally and used for all future requests. Email and password should NOT
     * be stored locally. The password is encrypted using the APP_PRIVATE KEY before transmission.
     * 
     * @param string $email
     * @param string $password
     * @return object
     */
    public static function getUserToken(\iRAP\VidaSDK\Models\AbstractAuthentication $auth, $email, $password)
    {
        $encrypted_password = $auth->getEncryption($password);
        $request = new \iRAP\VidaSDK\Models\APIRequest($auth);
        $request->setUrl("auth/register");
        $request->setPostData(array("email"=>$email,"password"=>$encrypted_password));
        $request->send();
        return parent::response($request);
    }
}
