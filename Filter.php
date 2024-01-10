<?php

/* 
 * This class builds the filter object, should you wish to filter the returned results from an API 
 * get request. Filters can be built on multiple criteria, and when ready should be passed to the 
 * get method, in order to be included with the API request. For information on how to use the
 * filters, see the README.md file.
 */

namespace iRAP\VidaSDK;

use stdClass;

class Filter implements FilterInterface
{
    private array $m_filter = array();
    
    /**
     * Your initial filter criteria goes into the constructor. $field is the name of the field 
     * that appears in the return data. This is the field that will be tested by the filter. $value 
     * is the value you wish to test for and $operator is the test to apply.
     * 
     * @param string $field
     * @param string $value
     * @param string $operator
     */
    public function __construct(string $field, string $value, string $operator = '=')
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
    public function addFilter(string $field, string $value, string $operator = '=')
    {
        $this->m_filter[] = $this->filterItem($field, $value, $operator);
    }
    
    /**
     * Internal method, called by the APIRequest() object to turn the filters into a URL friendly
     * string
     * 
     * @return string
     */
    public function buildFilter(): string
    {
        return urlencode(json_encode($this->m_filter));
    }
    
    /**
     * Internal method that json encodes the filter.
     * 
     * @return string
     */
    public function getFilter(): string
    {
        return json_encode($this->m_filter);
    }
    
    
    /**
     * Implementing jsonSerializable method so that
     * if this is a subpart of another object being 
     * serialized, it will come through correctly.
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->m_filter;
    }
    
    
    /**
     * Helper method that creates a filter object from the StdClass.
     * 
     * @param string $field
     * @param string $value
     * @param string $operator
     * @return stdClass
     */
    private function filterItem(string $field, string $value, string $operator): stdClass
    {
        $filter = new stdClass();
        $filter->field = $field;
        $filter->value = $value;
        $filter->operator = $operator;
        return $filter;
    }
}