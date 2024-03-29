<?php

/* 
 * A value object to ensure that we have AND or OR string.
 * Similar to an Enum.
 */

namespace iRAP\VidaSDK;

class Conjunction
{
    private $m_conjunction;
    
    private function __construct($conjunction)
    {
        $this->m_conjunction = $conjunction;
    }
    
    public static function createAnd(): Conjunction
    { return new Conjunction('AND'); }
    public static function createOr(): Conjunction
    { return new Conjunction('OR'); }
    
    public function __toString() { return $this->m_conjunction; }
}
