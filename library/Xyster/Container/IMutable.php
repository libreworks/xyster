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
 * @copyright Copyright (c) Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * Mutable container interface
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface Xyster_Container_IMutable
{
    /**
	 * Adds a definition to the container and autowires its dependencies based on the constructor.
	 * 
	 * @param mixed $type A Xyster_Type or the name of a class
	 * @param string $name Optional. The component name.
     */
    function autowire($type, $name = null);
    
    /**
	 * Adds a definition to the container and autowires its dependencies.
	 *
	 * @param mixed $type A Xyster_Type or the name of a class
	 * @param string $name Optional. The component name.
	 * @param array $except Optional.  An array of property names to ignore.
     */
    function autowireByName($type, $name = null, array $except = array());   
    
    /**
	 * Adds a definition to the container and autowires its dependencies.
	 *
	 * @param mixed $type A Xyster_Type or the name of a class
	 * @param string $name Optional. The component name.
	 * @param array $except Optional.  An array of property names to ignore.
     */
    function autowireByType($type, $name = null, array $except = array());
    
    /**
     * Adds a definition to the container.
     * 
     * @param Xyster_Container_Definition $definition The component definition
     */
    function add(Xyster_Container_Definition $definition);
}