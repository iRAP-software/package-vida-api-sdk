<?php

/* 
 * This class builds the filter object, should you wish to filter the returned results from an API 
 * get request. Filters can be built on multiple criteria, and when ready should be passed to the 
 * get method, in order to be included with the API request. For information on how to use the
 * filters, see the README.md file.
 */

namespace iRAP\VidaSDK;

class Filter
{
    
    private $m_filter = array();
    
    /**
     * Your initial filter criteria goes into the constructor. $field is the name of the field 
     * that appears in the return data. This is the field that will be tested by the filter. $value 
     * is the value you wish to test for and $operator is the test to apply.
     * 
     * @param string $field
     * @param string $value
     * @param string $operator
     */
    public function __construct($field, $value, $operator = '=')
    {
        $this->m_filter[] = $this->filterItem($field, $value, $operator);
    }
    
    /**
     * If you need to filter on more than one field, this is the method for you! As many filters
     * can be added as you like, and each one will be run in turn.
     * 
     * @param string $field
     * @param string $value
     * @param string $operator
     */
    public function addFilter($field, $value, $operator = '=')
    {
        $this->m_filter[] = $this->filterItem($field, $value, $operator);
    }
    
    /**
     * Internal method, called by the APIRequest() object to turn the filters into a URL friendly
     * string
     * 
     * @return string
     */
    public function buildFilter()
    {
        return urlencode(json_encode($this->m_filter));
    }
    
    /**
     * Internal method that json encodes the filter.
     * 
     * @return string
     */
    public function getFilter()
    {
        return json_encode($this->m_filter);
    }
    
    /**
     * Helper method that creates a filter object from the StdClass.
     * 
     * @param string $field
     * @param string $value
     * @param string $operator
     * @return \stdClass
     */
    private function filterItem($field, $value, $operator)
    {
        $filter = new \stdClass();
        $filter->field = $field;
        $filter->value = $value;
        $filter->operator = $operator;
        return $filter;
    }
}

/*
 * This class creates an object to group multiple filters together.
 */
class FilterGroup
{
    private $filterGroup;
    
    /**
     * Builds the group using an array of filter objects and a comparison string to explain how
     * to compare the filters. Acceptable comparisons are "AND" and "OR".
     * 
     * @param array $filtersArray
     * @param string $comparison
     */
    public function __construct($filtersArray, $comparison)
    {
        $this->filterGroup = new \stdClass();
        foreach ($filtersArray as $filter)
        {
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