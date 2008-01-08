<?php
/**
 * This file contains test classes for unit testing setter injection 
 */

class RocketShip
{
    protected $_astronaut;
    protected $_fuel;
    
    public function getPilot()
    {
        return $this->_astronaut;
    }
    
    public function setPilot( Astronaut $astronaut )
    {
        $this->_astronaut = $astronaut;
    }
    
    public function setFuel( RocketFuel $fuel )
    {
        $this->_fuel = $fuel;
    }
}

interface Astronaut
{
    function pilot();
}

class RocketPilot implements Astronaut
{
    protected $_suit;
    
    public function pilot()
    {
    }
    
    public function setSuit( SpaceSuit $suit )
    {
        $this->_suit = $suit;
    }
}

class RocketFuel
{
}

class SpaceSuit
{
}

class ExtraTerrestrial
{
    protected function setColor( $color )
    {
        // nothing
    }
}