<?php

/* 
 * This is exactly like the response object, except that it has an additional accessor
 * that returns any validation error objects that may have been returned (only if validation step
 * of importation failed).
 */


namespace iRAP\VidaSDK\Models;

class ImportResponse extends Response
{
    private $m_validationErrors;
    
    
    public function __construct(Response $response)
    {
        $this->status = $response->status;
        $this->code = $response->code;
        $this->response = $response->response;
        $this->rawResponse = $response->rawResponse;
        $this->m_validationErrors = array();
        
        if (isset($this->response->errors))
        {
            $this->m_validationErrors = $this->response->errors;
        }
    }
    
    
    public function getValidationErrors() { return $this->m_validationErrors; }
}