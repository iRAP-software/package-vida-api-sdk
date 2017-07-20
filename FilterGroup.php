<?php

/*
 * This class creates an object to group multiple FilterInterface objects together.
 */

namespace iRAP\VidaSDK;

class FilterGroup implements FilterInterface
{
    private $filterGroup;
    
    
    /**
     * Builds the group using an array of filter objects and a comparison string to explain how
     * to compare the filters. Acceptable comparisons are "AND" and "OR".
     * 
     * @param array $filters
     * @param string $comparison
     */
    public function __construct($comparison, FilterInterface ...$filters)
    {
        $this->filterGroup = new \stdClass();
        
        foreach ($filters as $filter)
        {
            /* @var $filter FilterInterface */
            $this->filterGroup->filtersArray[] = json_decode($filter->getFilter());
        }
        
        $this->filterGroup->comparison = $comparison;
    }
    
    
    /**
     * A copy of the same method on the filter object, to ensure that it is still available to 
     * the APIRequest if the filter group is passed in.
     * 
     * @return string
     */
    public function buildFilter()
    {
        return urlencode(json_encode($this->filterGroup));
    }
    
    
    /**
     * A copy of the same method on the filter object, to ensure that it is still available to 
     * the APIRequest if the filter group is passed in.
     * 
     * @return json
     */
    public function getFilter()
    {
        return json_encode($this->filterGroup);
    }
}