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

abstract class AbstractAuthentication
{
    protected $m_authHeaders = array();
    
    
    /**
     * Fetches the headers for authentication such as the auth_id and API key.
     * Note: this does not return the signature, use the getSignature method for that.
     * @return array - name value pairs. e.g. (auth_id => 5)
     */
    public function getAuthHeaders()
    {
        return $this->m_authHeaders;
    }
    
    
    /**
     * Encrypt a String
     * @param String $message - the message to encrypt
     * @param String $key - the key to encrypt and then decrypt the message.
     * @return String - the encrypted form of the string
     */
    protected function encrypt($message, $key)
    {
        $md5Key = md5($key);
        
        $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, 
                                    $md5Key, 
                                    $message, 
                                    MCRYPT_MODE_CBC, 
                                    md5($md5Key));
        
        $encoded_encryption = base64_encode($encrypted);
        return $encoded_encryption;
    }
    
    
    /**
     * Takes the request parameters and the secret key and generates the request signature using
     * the hash_hmac() method and the sha256 algorithm.
     * 
     * @param array $parameters
     * @param string $secretKey
     * @return string
     */
    protected function generateSignature($parameters, $secretKey)
    {
        array_change_key_case($parameters, CASE_LOWER); # just in case user forgets
        ksort($parameters); # order matters when producing a hash signature.
        $jsonString = json_encode($parameters, JSON_NUMERIC_CHECK);
        $signature = hash_hmac('sha256', $jsonString, $secretKey);
        return $signature;
    }
    
    
    /**
     * Return an assosciative array of all necessary signatures for the given data.
     * @return array - array of signatures to include in the header of the request.
     */
    abstract public function getSignatures(array $data);
}