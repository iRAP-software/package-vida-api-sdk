<?php

/**************************************************************************************************
 * This file is for internal use by the ViDA SDK. It should not be altered by users
 **************************************************************************************************
 * 
 * This class deals with the authentication. It generates the signatures for app and user and makes
 * them available to the APIRequest object, for sending with all API requests. 
 * 
 */

namespace iRAP\VidaSDK\Models;

class AppAuthentication extends AbstractAuthentication
{
    protected $m_app_auth_id;
    protected $m_app_api_key;
    protected $m_app_private_key;
    protected $m_authentication;
    protected $m_parameters;
    protected $m_signatures;
    
    /**
     * Takes the API token and sets up the authentication member variable
     * 
     * @param int $app_auth_id
     * @param string $app_api_key
     * @param string $app_private_key
     * @param int $user_auth_id
     * @param string $user_api_key
     * @param string $user_private_key
     */
    public function __construct($app_auth_id, $app_api_key, $app_private_key)
    {
        $this->m_app_auth_id = $app_auth_id;
        $this->m_app_api_key = $app_api_key;
        $this->m_app_private_key = $app_private_key;
        $this->m_parameters = $this->getParameters();
        $this->m_signatures = $this->getSignatures();
        $this->m_authentication = array_merge($this->m_parameters, $this->m_signatures);
        parent::__construct();
    }
     
    /**
     * Encrypts a string
     * 
     * @param string $message
     * @return string
     */
    public static function getEncryption($message)
    {
        return self::encrypt($message, $this->m_app_private_key);
    }
    
    /**
     * Builds an array of request parameters, for sending the API
     * 
     * @return array
     */
    protected function getParameters()
    {
        $parameters = array(
            'system_auth_id'    => $this->m_app_auth_id,
            'system_public_key' => $this->m_app_api_key,
            'nonce'             => rand(999999, 99999999),
            'timestamp'         => time()
        );
        return $parameters;
    }
    
    /**
     * Gets the signature for app and returns as an array.
     * 
     * @return array
     */
    protected function getSignatures()
    {
        return array(
            'system_signature'  => $this->generateSignature($this->m_parameters, $this->m_app_private_key)
        );
    }
}
