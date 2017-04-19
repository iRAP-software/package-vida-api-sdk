<?php

/**************************************************************************************************
 * This file is for internal use by the ViDA SDK. It should not be altered by users
 **************************************************************************************************
 * 
 * This is the class that actually makes the requests to the API. It is essentially a wrapper
 * for CURL, which handles inclusion of the authentication tokens in the header, assembles the
 * URL, sets the different request types and handles the response from the API.
 * 
 */
namespace iRAP\VidaSDK\Models;

class APIRequest
{
    public $m_result;
    public $m_httpCode;
    public $m_status;
    public $m_error;
    
    private static $s_version = IRAP_API_VERSION;
    private $m_url;
    private $m_baseUrl;
    private $m_ch;
    private $m_headers;
    private $m_auth;  /* @var $m_auth AbstractAuthentication */
    private $m_data; /* array of data to send off in the body of the request */
    
    
    /**
     * The constructor feeds the authentication information into the request headers.
     * @param \iRAP\VidaSDK\Models\Authentication $auth
     */
    public function __construct(AbstractAuthentication $auth)
    {
        $this->m_auth = $auth;
        
        if (defined('IRAP_API_URL'))
        {
            $this->m_baseUrl = IRAP_API_URL;
        }
        else
        {
            $this->m_baseUrl = IRAP_API_LIVE_URL;
        }
        
        $this->m_headers = array();
        $this->m_data = array();
    }
    
    
    /**
     * Send the request to the API and receive the response.
     */
    public function send()
    {
//        # Headers that need to be renewed every time we hit send()
//        $lastSecondHeaders = array(
//            'auth_nonce' => rand(1,99999),
//            'auth_timestamp' => time()
//        );
//        
//        $headers = array_merge($this->m_headers, $this->m_auth->getAuthHeaders(), $lastSecondHeaders);
//        
//        $allDataToSign = array_merge($headers, $this->m_data);
//        $allDataToSign['auth_url'] = $this->m_url;
//        $signatures = $this->m_auth->getSignatures($allDataToSign);
//        
//        // Add signatures to the headers
//        $headers = array_merge($headers, $signatures); 
//        
//        curl_setopt($this->m_ch, CURLOPT_HEADER, true);
//        curl_setopt($this->m_ch, CURLOPT_RETURNTRANSFER, true);
//        $formattedHeaders = $this->formatHeaders($headers);
//        curl_setopt($this->m_ch, CURLOPT_HTTPHEADER, $formattedHeaders);
//        $response = curl_exec($this->m_ch);
//        $this->processResponse($response);
//        curl_close($this->m_ch);
        
        if (defined('IRAP_DIAGNOSTICS'))
        {
            echo 'Target: ' . $this->m_url . PHP_EOL;
            echo $response;
        }
    }
    
    
    /**
     * Builds the URL and uses it to initiate CURL. The $resource and $id make up the first two 
     * parts of the URL, are $args can either be a third element, or an array of elements, each of which will
     * be separated with a '/'
     * 
     * @param string $resource
     * @param mixed $id
     * @param mixed $args
     */
    public function setUrl($resource, $id = null, $args = null, $filter = null)
    {
        $url = $this->m_baseUrl;
        
        if (!empty(self::$s_version))
        {
            $url .= '/' . self::$s_version;
        }
        
        $url .= '/' . $resource;
        
        if (!empty($id))
        {
            $url .= '/'.$id;
        }
        
        if (!empty($args))
        {
            if (is_array($args))
            {
                $url .= '/' . implode('/', $args);
            }
            else
            {
                $url .= '/' . $args;
            }
        }
        
        $this->m_url = $url;
        
        if (!empty($filter))
        {
            $url .= '?filter=' . $filter->buildFilter();
            $this->m_headers['filter'] = $filter->getFilter();
        }
        
        $this->m_ch = curl_init($url);
    }
    
    
    /**
     * Sets some custom headers to send along with the request.
     * Authentication headers will automatically be appended to these
     * @param array $headers
     */
    public function setHeaders($headers)
    {
        $this->m_headers = $headers;
    }
    
    
    /**
     * Loops through the m_headers member variable and formats each header for the request. Returns
     * an array of the headers.
     * 
     * @return array;
     */
    private function formatHeaders($inputHeaders) 
    {
        $outputHeaders = array();
        
        foreach ($inputHeaders as $key => $value)
        {
            $outputHeaders[] = $key.': '.$value;
        }
        
        return $outputHeaders;
    }
    
    
    /**
     * Adds the supplied array to the request as POST Fields and sets the request to a POST request.
     * 
     * @param array $data
     */
    public function setPostData($data)
    {
        $this->m_data = $data;
        curl_setopt($this->m_ch, CURLOPT_POST, true);
        curl_setopt($this->m_ch, CURLOPT_POSTFIELDS, $data);
    }
    
    
    /**
     * Adds the supplied array to the request as POST Fields and sets the request to a PUT request.
     * 
     * @param array $data
     */
    public function setPutData($data)
    {
        $this->m_data = $data;
        curl_setopt($this->m_ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($this->m_ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    
    /**
     * Adds the supplied array to the request as POST Fields and sets the request to a PATCH request.
     * @param array $data
     */
    public function setPatchData($data)
    {
        $this->m_data = $data;
        curl_setopt($this->m_ch, CURLOPT_CUSTOMREQUEST, "PATCH");
        curl_setopt($this->m_ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    
    /**
     * Sets the request to a DELETE request
     */
    public function setDeleteRequest($data)
    {
        $this->m_data = $data;
        curl_setopt($this->m_ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    }
    
    
    /**
     * Takes the response from CURL and splits the header from the body. Splits out the header into
     * HTTP Code, Status and Error message, for return to the developer.
     * 
     * @param object $response
     */
    private function processResponse($response)
    {
        $info = curl_getinfo($this->m_ch);
        $header = substr($response, 0, ($info['header_size']-1));
        $this->m_result = substr($response, $info['header_size']-1);
        $this->m_httpCode = $info['http_code'];
        
        foreach (explode("\r\n", $header) as $line)
        {
            $line = explode(': ', $line);
            
            if (count($line) < 2)
            {
                continue;
            }
            
            $key = $line[0];
            $value = $line[1];
            
            if ($key == 'Status')
            {
                $this->m_status = $value;
            }
            elseif ($key == 'Error')
            {
                $this->m_error = $value;
            }
        }
    }
}