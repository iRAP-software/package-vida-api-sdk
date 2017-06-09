<?php

/* 
 * A response object to represent the response that came back from the API.
 * This object has been limited by a need to not break an existing interface, but when possible
 * it will be good to make the following changes
 *  - change member variables to be private
 *  - only one response member variable, which is the raw response from the API.
 *  - getResponse() should become getJsonResponse() which performs last minute json_decode.
 *  - status should possibly become a boolean because values 
 */

namespace iRAP\VidaSDK\Models;

class Response
{
    public $status;
    public $code;
    public $response;
    public $rawResponse;
    public $error;
    
    
    /**
     * Create the response object from an API response.
     * @param int $code - the HTTP response code.
     * @param string $status - the status message from the response header.
     * @param string $rawResponseBody - the string response body.
     * @param mixed $error - the error message from the API respnose header (if there was one)
     * @throws Exception
     */
    public function __construct($code, $status, $rawResponseBody, $error = null)
    {
        if ($status === null || $status === "")
        {
            // if status is not set, something went wrong.
            $status = "error";
        }
        
        $status = strtolower($status);
        
        if (!in_array($status, array("success", "error")))
        {
            throw new \Exception("Unrecognized status: " . $status);
        }
        
        $this->status = $status;
        $this->rawResponse = $rawResponseBody;
        $this->code = $code;
        $this->error = $error;
               
        if (!empty(json_decode($rawResponseBody, true)))
        {
            $this->response = json_decode($rawResponseBody);
        }
        else
        {
            $this->response = null;
        }
    }
    
    
    /**
     * Get the status of the response. Every response from the API has a status message in the h
     * header. This always "success" or "error".
     * @return type
     */
    public function getStatus()
    {
        return $this->status;
    }
    
    
    /**
     * Get the HTTP response code from the API response. This is 200 for a successful request
     * or could be 404 for a not found etc.
     * @return int - the HTTP response code.
     */
    public function getCode()
    {
        return $this->code();
    }
    
    
    /**
     * Fetch the response object from the API. This is the response after it has
     * been json decoded. If json decode failed, then this will be NULL.
     * @return \stdClass
     */
    public function getResponse()
    {
        return $this->response;
    }
    
    
    /**
     * Fetch the raw response from the API. This should be a JSON string, but this
     * method can be useful if something wen't wrong and we didn't recieve a JSON response
     * @return string
     */
    public function getRawResponse()
    {
        return $this->rawResponse;
    }
    
    
    /**
     * Get any error message that came back from the API. This will be null if everything 
     * was ok, but will be a string message if there was an issue.
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }
}
