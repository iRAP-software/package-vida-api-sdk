<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace iRAP\VidaSDK\Models;

class authentication
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
    
    private function generateSignature($parameters, $secretKey)
    {
        array_change_key_case($parameters, CASE_LOWER); # just in case user forgets
        ksort($parameters); # order matters when producing a hash signature.
        $jsonString = json_encode($parameters);
        $signature = hash_hmac('sha256', $jsonString, $secretKey);
        return $signature;
    }
    
    private function getParameters()
    {
        return array(
            'user_auth_id' 	=> $this->m_user_auth_id,
            'system_auth_id'    => $this->m_client_auth_id,
            'user_public_key'   => $this->m_user_api_key,
            'system_public_key' => $this->m_client_api_key,
            'nonce'             => rand(999999, 99999999),
            'timestamp'         => time()
        );
    }
    
    private function getSignatures()
    {
        return array(
            'system_signature'  => $this->generateSignature($this->m_parameters, $this->m_client_private_key),
            'user_signature'  => $this->generateSignature($this->m_parameters, $this->m_user_private_key)
        );
    }
}
