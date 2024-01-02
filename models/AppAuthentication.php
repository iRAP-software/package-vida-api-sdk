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
    protected int $m_app_auth_id;
    protected string $m_app_api_key;
    protected string $m_app_private_key;


    /**
     * Takes the API token and sets up the authentication member variable
     *
     * @param int $app_auth_id
     * @param string $app_api_key
     * @param string $app_private_key
     */
    public function __construct(int $app_auth_id, string $app_api_key, string $app_private_key)
    {
        $this->m_app_auth_id = $app_auth_id;
        $this->m_app_api_key = $app_api_key;
        $this->m_app_private_key = $app_private_key;
    }
     
    /**
     * Encrypts a string
     * 
     * @param string $message
     * @return string
     */
    public function getEncryption(string $message): string
    {
        return $this->encrypt($message, $this->m_app_private_key);
    }
    
    /**
     * Builds an array of request parameters, for sending the API
     * 
     * @return array
     */
    public function getAuthHeaders(): array
    {
        return array(
            'auth_system_auth_id'    => $this->m_app_auth_id,
            'auth_system_public_key' => $this->m_app_api_key
        );
    }

    /**
     * Gets the signature for app and returns as an array.
     *
     * @param array $data
     * @return array
     */
    public function getSignatures(array $data): array
    {
        return array(
            'auth_system_signature'  => $this->generateSignature($data, $this->m_app_private_key)
        );
    }
}
