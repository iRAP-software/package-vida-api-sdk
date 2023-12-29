<?php

/*
 * An interface that all filter objects need to implement.
 */

namespace iRAP\VidaSDK;

Interface FilterInterface extends \JsonSerializable
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
    
    
    /**
     * All filters need to be serializable
     * @return mixed
     * @TODO Attribute ReturnTypeWillChange requires updating once PHP requirement for this package is at least 8
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize();
}
