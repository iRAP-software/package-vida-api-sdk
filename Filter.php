<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace iRAP\VidaSDK;

class Filter
{
    
    private $m_filter = array();
    
    public function __construct($field, $value, $operator = '=')
    {
        $this->m_filter[] = $this->filterItem($field, $value, $operator);
    }
    
    public function addAnd($field, $value, $operator = '=')
    {
        $this->m_filter[]['AND'] = $this->filterItem($field, $value, $operator);
    }
    
    public function addOr($field, $value, $operator = '=')
    {
        $this->m_filter[]['OR'] = $this->filterItem($field, $value, $operator);
    }
    
    public function buildFilter()
    {
        return urlencode(json_encode($this->m_filter));
    }
    
    public function getFilter()
    {
        return json_encode($this->m_filter);
    }
    
    private function filterItem($field, $value, $operator)
    {
        $filter = new \stdClass();
        $filter->field = $field;
        $filter->value = $value;
        $filter->operator = $operator;
        return $filter;
    }
}