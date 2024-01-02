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

class UserAuthentication extends AbstractAuthentication
{
    protected int $m_app_auth_id;
    protected string $m_app_api_key;
    protected $m_app_protected_key;
    protected int $m_user_auth_id;
    protected string $m_user_api_key;
    protected $m_user_protected_key;
    protected string $m_app_private_key;
    protected string $m_user_private_key;
    
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
    public function __construct(int $app_auth_id, string $app_api_key, string $app_private_key, int $user_auth_id, string $user_api_key, string $user_private_key)
    {
        $this->m_app_auth_id = $app_auth_id;
        $this->m_app_api_key = $app_api_key;
        $this->m_app_private_key = $app_private_key;
        $this->m_user_auth_id = $user_auth_id;
        $this->m_user_api_key = $user_api_key;
        $this->m_user_private_key = $user_private_key;
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
            'auth_system_public_key' => $this->m_app_api_key,
            'auth_user_auth_id'      => $this->m_user_auth_id,
            'auth_user_public_key'   => $this->m_user_api_key,
        );
    }
    
    
    /**
     * Gets the user and app signatures for the provided data 
     * before returning them as an associative array.
     * @return array - name/value pairs of the signatures.
     */
    public function getSignatures(array $data): array
    {
        return array(
            'auth_system_signature'  => $this->generateSignature($data, $this->m_app_private_key),
            'auth_user_signature'  => $this->generateSignature($data, $this->m_user_private_key)
        );
    }
}
