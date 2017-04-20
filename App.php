<?php

/* 
 * Welcome to the ViDA SDK. This is the primary class for users of the SDK and contains all of the
 * methods intended for use by developers. For help in understanding how to use the SDK, first 
 * look at the README.md file, then read the comments on each of the methods listed below.
 * 
 * If you require further help, or wish to report a bug fix, please email support@irap.org
 * 
 */

require_once __DIR__ . '/bootstrap.php';

namespace iRAP\VidaSDK;

class App extends Controllers\AbstractApiController
{
    private $m_auth;
    
    /**
     * Start here! The constructor takes the App's authentication credentials, which will be 
     * supplied to you by iRAP. An Authentication object
     * is created, ready to be passed to the API as required.
     * 
     * @param int $appAuthID
     * @param string $appAPIKey
     * @param string $appPrivateKey
     */
    public function __construct($appAuthID, $appAPIKey, $appPrivateKey)
    {
        $this->m_auth = new Models\AppAuthentication($appAuthID, $appAPIKey, $appPrivateKey);
    }
    
    /**
     * 
     * @return Models\AppAuthentication
     */
    protected function getAuth()
    {
        return $this->m_auth;
    }
}