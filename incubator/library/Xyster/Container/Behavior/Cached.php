<?php
/**
 * Xyster Framework
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.opensource.org/licenses/bsd-license.php
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * @see Xyster_Container_Behavior_Stored
 */
require_once 'Xyster/Container/Behavior/Stored.php';
/**
 * Cached behavior
 * 
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Container_Behavior_Cached extends Xyster_Container_Behavior_Stored implements Xyster_Container_Reference
{
    /**
     * @var mixed
     */
    private $_instance;
    
    /**
     * Creates a new stored behavior
     * 
     */
    public function __construct( Xyster_Container_Component_Adapter $delegate, Xyster_Container_Reference $reference = null )
    {
        if ( $reference == null ) {
            $reference = $this; 
        }
        parent::__construct($delegate, $reference);
    }
    
    /**
     * Gets the value 
     *
     * @return mixed
     */
    public function get()
    {
        return $this->_instance;
    }
    
    /**
     * Sets the value
     *
     * @param mixed $value
     */
    public function set( $value )
    {
        $this->_instance = $value;
    }
    
    /**
     * Gets the string representation of the object
     *
     * @return string
     */
    public function __toString()
    {
        return "Cached:" . parent::__toString();
    }
}