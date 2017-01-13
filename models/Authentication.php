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

class Authentication
{
    private $m_client_auth_id;
    private $m_client_api_key;
    private $m_client_private_key;
    private $m_user_auth_id;
    private $m_user_api_key;
    private $m_user_private_key;
    private $m_parameters;
    private $m_signatures;
    public $m_authentication;
    
    /**
     * Takes the API token and user token if available and sets up the authentication member variable
     * 
     * @param int $app_auth_id
     * @param string $app_api_key
     * @param string $app_private_key
     * @param int $user_auth_id
     * @param string $user_api_key
     * @param string $user_private_key
     */
    public function __construct($app_auth_id, $app_api_key, $app_private_key, $user_auth_id = '', $user_api_key = '', $user_private_key = '')
    {
        $this->m_client_auth_id = $app_auth_id;
        $this->m_client_api_key = $app_api_key;
        $this->m_client_private_key = $app_private_key;
        $this->m_user_auth_id = $user_auth_id;
        $this->m_user_api_key = $user_api_key;
        $this->m_user_private_key = $user_private_key;
        $this->m_parameters = $this->getParameters();
        $this->m_signatures = $this->getSignatures();
        $this->m_authentication = array_merge($this->m_parameters, $this->m_signatures);
    }
    
    /**
     * Takes the request parameters and the secret key and generates the request signature using
     * the hash_hmac() method and the sha256 algorithm.
     * 
     * @param array $parameters
     * @param string $secretKey
     * @return string
     */
    private function generateSignature($parameters, $secretKey)
    {
        array_change_key_case($parameters, CASE_LOWER); # just in case user forgets
        ksort($parameters); # order matters when producing a hash signature.
        $jsonString = json_encode($parameters, JSON_NUMERIC_CHECK);
        $signature = hash_hmac('sha256', $jsonString, $secretKey);
        return $signature;
    }
    
    /**
     * Builds an array of request parameters, for sending the API
     * 
     * @return array
     */
    private function getParameters()
    {
        $parameters = array(
            'system_auth_id'    => $this->m_client_auth_id,
            'system_public_key' => $this->m_client_api_key,
            'nonce'             => rand(999999, 99999999),
            'timestamp'         => time()
        );
        if (!empty($this->m_user_auth_id))
        {
            $parameters['user_auth_id'] = $this->m_user_auth_id;
        }
        if (!empty($this->m_user_api_key))
        {
            $parameters['user_public_key'] = $this->m_user_api_key;
        }
        return $parameters;
    }
    
    /**
     * Gets the signatures for app and user and returns them as an array.
     * 
     * @return array
     */
    private function getSignatures()
    {
        return array(
            'system_signature'  => $this->generateSignature($this->m_parameters, $this->m_client_private_key),
            'user_signature'  => $this->generateSignature($this->m_parameters, $this->m_user_private_key)
        );
    }
}
