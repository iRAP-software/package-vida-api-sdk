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
    
    private static $s_version = \iRAP\VidaSDK\Defines::IRAP_API_VERSION;
    private $m_urlWithouFilter; // url that will never have any ?filter within it
    private $m_url; // url request will be sent to
    private $m_baseUrl; // the url of where the API is. e.g. https://api.vida.irap.org (no end slash)
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
        
        if (defined('\iRAP\VidaSDK\IRAP_API_URL'))
        {
            $this->m_baseUrl = \iRAP\VidaSDK\IRAP_API_URL;
        }
        else
        {
            $this->m_baseUrl = \iRAP\VidaSDK\Defines::IRAP_API_LIVE_URL;
        }
        
        $this->m_headers = array();
        $this->m_data = array();
    }
    
    
    /**
     * Send the request to the API and receive the response.
     */
    public function send()
    {
        $curlHandle = $this->getCurlHandleForSending();
        $response = curl_exec($curlHandle);
        $this->processResponse($response, $curlHandle);
        curl_close($curlHandle);
        
        if (defined('\iRAP\VidaSDK\IRAP_DIAGNOSTICS'))
        {
            echo 'Target: ' . $this->m_urlWithouFilter . PHP_EOL;
            echo $response;
        }
    }
    
    
    /**
     * Prep the curl handle for sending by adding the last second headers that are used for 
     * authentication
     * This is public because it is needed by the AsyncRequester class for sending lots of requests
     * asynchronously
     * @return the curl handle for sending a curl request.
     */
    public function getCurlHandleForSending()
    {
        $handle = $this->m_ch;
        
        # Headers that need to be renewed every time we hit send()
        $lastSecondHeaders = array(
            'auth_nonce' => rand(1,99999),
            'auth_timestamp' => time()
        );
        
        $headers = array_merge($this->m_headers, $this->m_auth->getAuthHeaders(), $lastSecondHeaders);
        
        $allDataToSign = array_merge($headers, $this->m_data);
        $allDataToSign['auth_url'] = $this->m_urlWithouFilter;
        $signatures = $this->m_auth->getSignatures($allDataToSign);
        // Add signatures to the headers
        $headers = array_merge($headers, $signatures); 
        
        curl_setopt($handle, CURLOPT_HEADER, true);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $formattedHeaders = $this->formatHeaders($headers);
        curl_setopt($handle, CURLOPT_HTTPHEADER, $formattedHeaders);
        return $handle;
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
        
        $this->m_urlWithouFilter = $url;
        
        if (!empty($filter))
        {
            $url .= '?filter=' . $filter->buildFilter();
            $this->m_headers['filter'] = $filter->getFilter();
        }
        
        $this->m_url = $url;
        $this->m_ch = curl_init($this->m_url);
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
        curl_setopt($this->m_ch, CURLOPT_POSTFIELDS, json_encode($data));
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
    public function setDeleteRequest()
    {
        curl_setopt($this->m_ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    }
    
    
    /**
     * Takes the response from CURL and splits the header from the body. Splits out the header into
     * HTTP Code, Status and Error message, for return to the developer.
     * This is public so that the AsyncRequester class can use it
     * @param object $response
     */
    public function processResponse($response, $curlHandle)
    {
        $info = curl_getinfo($curlHandle);
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
    
    
    /**
     * Get a response object for this request.
     * This relies on the request having already been sent.
     * @return \iRAP\VidaSDK\Models\Response
     */
    public function getResponse()
    {
        return new \iRAP\VidaSDK\Models\Response(
            $this->m_httpCode,
            $this->m_status,
            $this->m_result,
            (isset($this->m_error)) ? $this->m_error : null
        );
    }
    
    
    # Accessors
    # In future use these rather than accesssing member vars directly.
    public function getUrl() { return $this->m_urlWithouFilter; }
    public function getResult() { return $this->m_result; }
    public function getHttpCode() { return $this->m_httpCode; }
    public function getStatus() { return $this->m_status; }
    public function getError() { return $this->m_error; }
}