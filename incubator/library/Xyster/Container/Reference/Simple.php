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
 * @see Xyster_Container_Reference
 */
require_once 'Xyster/Container/Reference.php';
/**
 * Simple instance implementation of Xyster_Container_Reference 
 * 
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Container_Reference_Simple implements Xyster_Container_Reference
{
    /**
     * @var mixed
     */
    private $_instance;

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
}