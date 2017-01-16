<?php

/* 
 * Welcome to the ViDA SDK. This is the primary class for users of the SDK and contains all of the
 * methods intended for use by developers. For help in understanding how to use the SDK, first 
 * look at the README.md file, then read the comments on each of the methods listed below.
 * 
 * If you require further help, or wish to report a bug fix, please email support@irap.org
 * 
 */

namespace iRAP\VidaSDK;

class Client extends ApiController
{
    
    /**
     * Start here! The constructor takes the App's authentication credentials, which will be 
     * supplied to you by iRAP. By placing them here, all of the authentication work is done for
     * you, which saves a lot of hassle further down the line.
     * 
     * The IRAPDEV constant is used internally during development of the API, and is no use to you.
     * 
     * @param int $appAuthID
     * @param string $appAPIKey
     * @param string $appPrivateKey
     */
    public function __construct($appAuthID, $appAPIKey, $appPrivateKey, $userAuthID, $userAPIKey, $userPrivateKey)
    {
        $this->m_auth = new Models\Authentication($appAuthID, $appAPIKey, $appPrivateKey, $userAuthID, $userAPIKey, $userPrivateKey);
    }
}