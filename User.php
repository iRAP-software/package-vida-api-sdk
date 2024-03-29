<?php

/* 
 * Welcome to the ViDA SDK. This is the primary class for accessing the API as a User and contains 
 * all the methods intended for use by developers. For help in understanding how to use the SDK,
 * first look at the README.md file, then read the comments on each of the methods listed below.
 * 
 * If you require further help, or wish to report a bug fix, please email support@irap.org
 * 
 */

namespace iRAP\VidaSDK;


class User extends Controllers\AbstractApiController
{
    
    protected Models\UserAuthentication $m_auth;
    /**
     * Start here! The constructor takes the App's authentication credentials, which will be 
     * supplied to you by iRAP and the user's authentication details. An Authentication object
     * is created, ready to be passed to the API as required.
     * 
     * @param int $appAuthID
     * @param string $appAPIKey
     * @param string $appPrivateKey
     * @param int $userAuthID
     * @param string $userAPIKey
     * @param string $userPrivateKey
     */
    public function __construct(int $appAuthID, string $appAPIKey, string $appPrivateKey, int $userAuthID, string $userAPIKey, string $userPrivateKey)
    {
        $this->m_auth = new Models\UserAuthentication($appAuthID, $appAPIKey, $appPrivateKey, $userAuthID, $userAPIKey, $userPrivateKey);
    }
    
    protected function getAuth(): Models\UserAuthentication
    {
        return $this->m_auth;
    }
}