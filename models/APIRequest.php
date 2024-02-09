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

use Exception;
use iRAP\VidaSDK\Defines;

class APIRequest
{
    public string $m_result;
    public int $m_httpCode;
    public ?string $m_status = null;
    public $m_error;
    
    private static string $s_version = Defines::IRAP_API_VERSION;
    private string $m_urlWithoutFilter; // url that will never have any ?filter within it
    private string $m_baseUrl; // the url of where the API is. e.g. https://api.vida.irap.org (no end slash)
    /**
     * @var \CurlHandle|resource|false
     */
    private $m_ch;
    private array $m_headers;
    private AbstractAuthentication $m_auth;  /* @var $m_auth AbstractAuthentication */
    private $m_data; /* array of data to send off in the body of the request */
    
    
    /**
     * The constructor feeds the authentication information into the request headers.
     * @param AbstractAuthentication $auth
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
            $this->m_baseUrl = Defines::IRAP_API_LIVE_URL;
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
            echo 'Target: ' . $this->m_urlWithoutFilter . PHP_EOL;
            echo $response;
        }
    }
    
    
    /**
     * Prep the curl handle for sending by adding the last second headers that are used for 
     * authentication
     * This is public because it is needed by the AsyncRequester class for sending lots of requests
     * asynchronously
     * @return \CurlHandle|resource|false curl handle for sending a curl request.
     */
    public function getCurlHandleForSending()
    {
        $handle = $this->m_ch;
        
        $size = 20;
        $nonce = substr(base64_encode(openssl_random_pseudo_bytes($size)), 0, $size);
        
        # Headers that need to be renewed every time we hit send()
        $lastSecondHeaders = array(
            'auth_nonce' => $nonce,
            'auth_timestamp' => time()
        );
        
        $headers = array_merge($this->m_headers, $this->m_auth->getAuthHeaders(), $lastSecondHeaders);
        
        $allDataToSign = array_merge($headers, $this->m_data);
        $allDataToSign['auth_url'] = $this->m_urlWithoutFilter;
        
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
     * @param null $filter
     */
    public function setUrl(string $resource, $id = null, $args = null, $filter = null)
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
        
        $this->m_urlWithoutFilter = $url;
        
        if (!empty($filter))
        {
            $this->m_headers['filter'] = $filter->getFilter();
        }

        $this->m_ch = curl_init($url);
    }
    
    
    /**
     * Sets some custom headers to send along with the request.
     * Authentication headers will automatically be appended to these
     * @param array $headers
     */
    public function setHeaders(array $headers)
    {
        $this->m_headers = $headers;
    }
    
    
    /**
     * Loops through the m_headers member variable and formats each header for the request. Returns
     * an array of the headers.
     * 
     * @return array;
     */
    private function formatHeaders($inputHeaders): array
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
    public function setPostData(array $data)
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
    public function setPutData(array $data)
    {
        $this->m_data = $data;
        curl_setopt($this->m_ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($this->m_ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    
    /**
     * Adds the supplied array to the request as POST Fields and sets the request to a PATCH request.
     * @param array $data
     */
    public function setPatchData(array $data)
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
     * @param $response
     * @param $curlHandle
     */
    public function processResponse($response, $curlHandle)
    {
        $info = curl_getinfo($curlHandle);
        $header = substr($response, 0, ($info['header_size']-1));
        $this->m_result = substr($response, $info['header_size']-1);
        $this->m_httpCode = $info['http_code'];
        
        $headers = array();
        
        foreach (explode("\r\n", $header) as $line)
        {
            $line = explode(': ', $line);
            
            if (count($line) < 2)
            {
                continue;
            }
            
            $key = $line[0];
            $value = $line[1];
            
            $headers[$key] = $value;
        }
        
        // In the transition to NGINX we have to switch from Status to API_STATUS, but unsure
        // if we will get Status back from fpm or whether nginx will strip this out, so having
        // API_STATUS override Status, if it exists.
        if (isset($headers['API_STATUS']))
        {
            $this->m_status = $headers['API_STATUS'];
        }
        elseif (isset($headers['Status']))
        {
            $this->m_status = $headers['Status'];
        }
        
        if (isset($headers['Error']))
        {
            $this->m_error = $headers['Error'];
        }
    }


    /**
     * Get a response object for this request.
     * This relies on the request having already been sent.
     * @return Response
     * @throws Exception
     */
    public function getResponse(): Response
    {
        return new Response(
            $this->m_httpCode,
            $this->m_result,
            $this->m_status,
            (isset($this->m_error)) ? $this->m_error : null
        );
    }
    
    
    # Accessors
    # In future use these rather than accessing member vars directly.
    public function getUrl(): string { return $this->m_urlWithoutFilter; }
    public function getResult(): string { return $this->m_result; }
    public function getHttpCode(): int { return $this->m_httpCode; }
    public function getStatus(): ?string { return $this->m_status; }
    public function getError() { return $this->m_error; }
}