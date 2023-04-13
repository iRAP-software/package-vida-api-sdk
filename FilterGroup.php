<?php

/*
 * This class creates an object to group multiple FilterInterface objects together.
 */

namespace iRAP\VidaSDK;


class FilterGroup implements FilterInterface
{
    private $m_conjunction; 
    private $m_filtersArray = array();
    
    
    /**
     * Builds the group using an array of filter objects and a comparison string to explain how
     * to compare the filters. Acceptable comparisons are "AND" and "OR".
     * 
     * @param array $filters
     * @param string $comparison
     */
    public function __construct(Conjunction $conjunction, FilterInterface ...$filters)
    {
        $this->m_filtersArray = $filters;
        $this->m_conjunction = $conjunction;
    }
    
    
    /**
     * Method for JsonSerializable interface. Converts this object into a form that is json
     * serializable.
     * @return type
     */
    public function jsonSerialize(): mixed 
    {
        return array(
            'comparison' => (string)$this->m_conjunction,
            'filtersArray' => $this->m_filtersArray
        );
    }
    
    
    /**
     * A copy of the same method on the filter object, to ensure that it is still available to 
     * the APIRequest if the filter group is passed in.
     * 
     * @return string
     */
    public function buildFilter()
    {
        return urlencode(json_encode($this));
    }
    
    
    /**
     * A copy of the same method on the filter object, to ensure that it is still available to 
     * the APIRequest if the filter group is passed in.
     * 
     * @return json
     */
    public function getFilter()
    {
        return json_encode($this);
    }
}