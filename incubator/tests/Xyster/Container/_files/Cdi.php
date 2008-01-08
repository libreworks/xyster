<?php
/**
 * This file contains test classes for unit testing setter injection 
 */

class Submarine
{
    protected $_capn;
    protected $_fuel;
    protected $_crew;
    protected $_name;
    protected $_location;
    
    public function __construct( Sailor $capn, SubFuel $fuel, array $crew = array() )
    {
        $this->_capn = $capn;
        $this->_fuel = $fuel;
        $this->_crew = $crew;
    }
    
    public function getCaptain()
    {
        return $this->_capn;
    }
    
    public function setLocation( $location )
    {
        $this->_location = $location;
    }
    
    public function setName( $name )
    {
        $this->_name = $name;
    }
    
    public function setX( $x )
    {
        // nothing
    }
    
    protected function setError( $error )
    {
        // do nothing
    }
}

interface Sailor
{
    function navigate();
}

class SubmarineCaptain implements Sailor
{
    protected $_suit;
    protected $_name;
    
    public function __construct( ScubaGear $suit, $name )
    {
        $this->_suit = $suit;
    }
    
    public function navigate()
    {
    }
}

class SubFuel
{
}

class ScubaGear
{
}

class SeaUrchin
{
    protected function __construct()
    {
    }
}