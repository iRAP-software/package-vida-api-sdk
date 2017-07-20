<?php

/*
 * An interface that all filter objects need to implement.
 */

namespace iRAP\VidaSDK;

Interface FilterInterface
{
    /**
     * The same as getFilter, but performs a urlencode on the result.
     * @return string
     */
    public function buildFilter();
    
    
    /**
     * Convert this object into json form for the API
     * @return string - json
     */
    public function getFilter();
}