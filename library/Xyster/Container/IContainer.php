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
 * Main container interface
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface Xyster_Container_IContainer
{
    /**
	 * Whether this container contains a component with the given name.
	 * 
	 * @param string $name The component name
	 * @return boolean
     */
    function contains($name);
    
    /**
     * Whether this container contains a component with the given type.
     * 
     * @param mixed $type A Xyster_Type or the name of a class
     * @return boolean
     */
    function containsType($type);
        
    /**
     * Gets the component by name.
     * 
     * @param string $name The component name
     * @param Xyster_Type $into Optional. The type into which the component is being injected
     * @return object The component
     */
    function get($name, Xyster_Type $into = null);
        
    /**
	 * Gets the components in the contanier for the given type.
	 * 
	 * @param mixed $type A Xyster_Type or string class name
	 * @return array Keys are component names, values are components themselves
     */
    function getForType($type);
    
    /**
	 * Gets the component names given a type.
	 * 
	 * If the type argument is omitted, this will return all component names.
	 * 
	 * @param mixed $type Optional. A Xyster_Type or string class name
	 * @return array of strings
     */
    function getNames($type = null);
    
    /**
     * Gets the parent container.
     * 
     * @return Xyster_Container_IContainer
     */
    function getParent();
    
    /**
     * Gets the type of component with the given name.
     * 
     * @param string $name The component name
     * @return Xyster_Type The component type
     */
    function getType($name);
}