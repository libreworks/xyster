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
 * @see Xyster_Container_IContainer
 */
require_once 'Xyster/Container/IContainer.php';
/**
 * Mutable container interface
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface Xyster_Container_IMutable extends Xyster_Container_IContainer
{
    /**
	 * Adds a definition to the container and autowires its dependencies based on the constructor.
	 * 
	 * @param mixed $type A Xyster_Type or the name of a class
	 * @param string $name Optional. The component name.
     * @return Xyster_Container_IMutable provides a fluent interface
     */
    function autowire($type, $name = null);
    
    /**
	 * Adds a definition to the container and autowires its dependencies.
	 *
	 * @param mixed $type A Xyster_Type or the name of a class
	 * @param string $name Optional. The component name.
	 * @param array $except Optional.  An array of property names to ignore.
     * @return Xyster_Container_IMutable provides a fluent interface
     */
    function autowireByName($type, $name = null, array $except = array());   
    
    /**
	 * Adds a definition to the container and autowires its dependencies.
	 *
	 * @param mixed $type A Xyster_Type or the name of a class
	 * @param string $name Optional. The component name.
	 * @param array $except Optional.  An array of property names to ignore.
     * @return Xyster_Container_IMutable provides a fluent interface
     */
    function autowireByType($type, $name = null, array $except = array());
    
    /**
     * Adds a definition to the container.
     * 
     * @param Xyster_Container_Definition $definition The component definition
     * @return Xyster_Container_IMutable provides a fluent interface
     */
    function add(Xyster_Container_Definition $definition);
    
    /**
     * Adds a provider to the container.
     * 
     * @param Xyster_Container_IProvider $provider The provider
     * @return Xyster_Container_IMutable provides a fluent interface
     */
    function addProvider(Xyster_Container_IProvider $provider);
}