<?php

/* 
 * Welcome to the ViDA SDK. This is the primary class for users of the SDK and contains all the
 * methods intended for use by developers. For help in understanding how to use the SDK, first 
 * look at the README.md file, then read the comments on each of the methods listed below.
 * 
 * If you require further help, or wish to report a bug fix, please email support@irap.org
 * 
 */

namespace iRAP\VidaSDK;


class App extends Controllers\AbstractApiController
{
    private Models\AppAuthentication $m_auth;
    const DATASET_TYPE_UNKNOWN  = 1;
    const DATASET_TYPE_EXISTING = 2;
    const DATASET_TYPE_DESIGN   = 3;
    const DATASET_TYPE_RESEARCH = 4;

    /**
     * Start here! The constructor takes the App's authentication credentials, which will be 
     * supplied to you by iRAP. An Authentication object
     * is created, ready to be passed to the API as required.
     * 
     * @param int $appAuthID
     * @param string $appAPIKey
     * @param string $appPrivateKey
     */
    public function __construct(int $appAuthID, string $appAPIKey, string $appPrivateKey)
    {
        $this->m_auth = new Models\AppAuthentication($appAuthID, $appAPIKey, $appPrivateKey);
    }
    
    /**
     * 
     * @return Models\AppAuthentication
     */
    protected function getAuth(): Models\AppAuthentication
    {
        return $this->m_auth;
    }
}
