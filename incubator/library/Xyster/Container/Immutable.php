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
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * @see Xyster_Container_Delegating_Abstract
 */
require_once 'Xyster/Container/Delegating/Abstract.php';
/**
 * Used to wrap an existing container and make it immutable
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Container_Immutable extends Xyster_Container_Delegating_Abstract
{
    /**
     * Tests if an object is equal to this one
     *
     * @param mixed $obj
     * @return boolean
     */
    public function equals( $obj )
    {
        return $this === $obj || 
            $obj === $this->getDelegate() || 
            $obj instanceof self && $this->getDelegate() === $obj->getDelegate();
    }
    
    /**
     * Gets a hash code for the object
     *
     * @return int
     */
    public function hashCode()
    {
        require_once 'Xyster/Type.php';
        return Xyster_Type::hash($this->getDelegate());
    }    
}