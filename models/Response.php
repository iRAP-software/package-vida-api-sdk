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

use Exception;

class Response
{
    public string $status;
    public int $code;
    public $response;
    public string $rawResponse;
    public $error;
    
    
    /**
     * Create the response object from an API response.
     * @param int $code - the HTTP response code.
     * @param string $rawResponseBody - the string response body.
     * @param ?string $status - the status message from the response header.
     * @param mixed $error - the error message from the API response header (if there was one)
     * @throws Exception
     */
    public function __construct(int $code, string $rawResponseBody, ?string $status = null, $error = null)
    {
        if (!$status)
        {
            trigger_error("API response: $code - " . htmlentities($rawResponseBody), E_USER_WARNING);
            // if status is not set, something went wrong.
            $status = "Error";
            
            if ($error === null || $error === "")
            {
                $error = "Server did not respond as expected (server may be down)";
            }
        }
        
        if (!in_array($status, array("Success", "Error")))
        {
            throw new Exception("Unrecognized status: " . $status);
        }
        
        $this->status = $status;
        $this->rawResponse = $rawResponseBody;
        $this->code = $code;
        $this->error = $error;
        
        // Using null instead of empty() so response can be set to an empty array for "[]"
        if (json_decode($rawResponseBody, true) !== null)
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
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }
    
    
    /**
     * Get the HTTP response code from the API response. This is 200 for a successful request
     * or could be 404 for a not found etc.
     * @return int - the HTTP response code.
     */
    public function getCode(): int
    {
        return $this->code;
    }
    
    
    /**
     * Fetch the response object from the API. This is the response after it has
     * been json decoded. If json decode failed, then this will be NULL.
     * @return ?mixed
     * @TODO Attribute ReturnTypeWillChange requires updating once PHP requirement for this package is at least 8
     */
    #[\ReturnTypeWillChange]
    public function getResponse()
    {
        return $this->response;
    }
    
    
    /**
     * Fetch the raw response from the API. This should be a JSON string, but this
     * method can be useful if something went wrong, and we didn't receive a JSON response
     * @return string
     */
    public function getRawResponse(): string
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
