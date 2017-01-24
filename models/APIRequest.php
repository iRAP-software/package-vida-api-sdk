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
    private static $s_version;
    private $s_url;
    private $m_ch;
    private $m_headers;
    public $m_result;
    public $m_httpCode;
    public $m_status;
    public $m_error;
    
    /**
     * The contructor feeds the authentication information into the request headers.
     */
    
    /**
     * 
     * @param \iRAP\VidaSDK\Models\Authentication $auth
     */
    public function __construct(AbstractAuthentication $auth)
    {
        $this->m_headers = $auth->getAuthentication();
        if (defined('IRAPDEV'))
        {
            $this->s_url = 'http://api.irap-dev.org';
        }
        else
        {
            $this->s_url = 'http://api.release.vida.irap.org';
        }
    }

    /**
     * This method sends the request to the API and receives the response.
     */
    public function send()
    {
        curl_setopt($this->m_ch, CURLOPT_HEADER, true);
        curl_setopt($this->m_ch, CURLOPT_RETURNTRANSFER, true);
        $this->m_headers = $this->formatHeaders();
        curl_setopt($this->m_ch, CURLOPT_HTTPHEADER, $this->m_headers);
        $response = curl_exec($this->m_ch);
        $this->processResponse($response);
        curl_close($this->m_ch);
        if (defined('IRAPDIAGNOSTICS'))
        {
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
    public function setUrl($resource, $id = null, $args = null)
    {
        $url = $this->s_url;
        if (!empty(self::$s_version))
        {
            $url .= '/'.self::$s_version;
        }
        $url .= '/'.$resource;
        if (!empty($id))
        {
            $url .= '/'.$id;
        }
        if (!empty($args))
        {
            if (is_array($args))
            {
                $url .= '/'.implode('/', $args);
            }
            else
            {
                $url .= '/'.$args;
            }
        }
        $this->m_ch = curl_init($url);
    }
    
    /**
     * Adds an array of headers to the existing headers, which are usually the authentication ones.
     * 
     * @param array $headers
     */
    public function setHeaders($headers)
    {
        $this->m_headers = array_merge($this->m_headers, $headers);
    }
    
    /**
     * Loops through the m_headers member variable and formats each header for the request. Returns
     * an array of the headers.
     * 
     * @return array;
     */
    private function formatHeaders() {
        $headers = array();
        foreach ($this->m_headers as $key=>$value)
        {
            $headers[] = $key.': '.$value;
        }
        return $headers;
    }
    
    /**
     * Adds the supplied array to the request as POST Fields and sets the request to a POST request.
     * 
     * @param array $data
     */
    public function setPostData($data)
    {
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
        curl_setopt($this->m_ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($this->m_ch, CURLOPT_POSTFIELDS, $data);
    }
    
    /**
     * Adds the supplied array to the request as POST Fields and sets the request to a PATCH request.
     * 
     * @param array $data
     */
    public function setPatchData($data)
    {
        curl_setopt($this->m_ch, CURLOPT_CUSTOMREQUEST, "PATCH");
        curl_setopt($this->m_ch, CURLOPT_POSTFIELDS, $data);
    }
    
    /**
     * Sets the request to a DELETE request
     */
    public function setDeleteRequest()
    {
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

