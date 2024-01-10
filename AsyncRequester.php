<?php

/*
 * A class to make it easy to send lots of API requests together asynchronously
 */

namespace iRAP\VidaSDK;

use iRAP\VidaSDK\Models\APIRequest;

class AsyncRequester
{
    private array $m_requests;
    
    
    function __construct(APIRequest ...$requests)
    {
        $this->m_requests = $requests;   
        $this->run();
    }
    
    
    private function run()
    {
        // Create get requests for each URL
        $curlMultiHandle = curl_multi_init();
        
        foreach ($this->m_requests as $i => $request)
        {
            $curlHandles[$i] = $request->getCurlHandleForSending();
            curl_multi_add_handle($curlMultiHandle, $curlHandles[$i]);
        }
        
        // Start performing the request
        do {
            $execReturnValue = curl_multi_exec($curlMultiHandle, $runningHandles);
        } while ($execReturnValue == CURLM_CALL_MULTI_PERFORM);
        
        // Loop and continue processing the request
        while ($runningHandles && $execReturnValue == CURLM_OK) 
        {
            if (curl_multi_select($curlMultiHandle) != -1) 
            {
                usleep(100);
            }
                
            do {
                $execReturnValue = curl_multi_exec($curlMultiHandle, $runningHandles);
            } while ($execReturnValue == CURLM_CALL_MULTI_PERFORM);
        }
        
        // Check for any errors
        if ($execReturnValue != CURLM_OK) 
        {
            trigger_error("Curl multi read error $execReturnValue\n", E_USER_WARNING);
        }
        
        // Extract the content
        foreach ($this->m_requests as $i => $request)
        {
            // Check for errors
            $curlError = curl_error($curlHandles[$i]);
            
            if ($curlError == "") 
            {
                $responseContent = curl_multi_getcontent($curlHandles[$i]);
                $this->m_requests[$i]->processResponse($responseContent, $curlHandles[$i]);
            } 
            else 
            {
                print "Curl error on handle $i: $curlError\n";
            }
            
            // Remove and close the handle
            curl_multi_remove_handle($curlMultiHandle, $curlHandles[$i]);
            curl_close($curlHandles[$i]);
        }
        
        // Clean up the curl_multi handle
        curl_multi_close($curlMultiHandle);
    }
}